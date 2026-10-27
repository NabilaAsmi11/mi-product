<?php

namespace App\Services;

use App\Models\Order;
use App\Services\Clients\CartServiceClient;
use App\Services\Clients\ProductServiceClient;
use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected CartServiceClient $cartClient;
    protected ProductServiceClient $productClient;

    public function __construct(CartServiceClient $cartClient, ProductServiceClient $productClient)
    {
        $this->cartClient = $cartClient;
        $this->productClient = $productClient;
    }

    public function createOrder(int $userId): Order
    {
        // 1. Ambil isi keranjang dari cart-service
        $cart = $this->cartClient->getCart($userId);
        
        $items = $cart['items'] ?? [];
        if (empty($items)) {
            throw new BusinessException("Keranjang belanja kosong");
        }

        $orderItemsData = [];
        $totalAmount = 0;

        // 2. Validasi setiap produk & stok dari product-service serta lakukan snapshot harga
        foreach ($items as $cartItem) {
            $productId = $cartItem['product_id'];
            $quantity = $cartItem['quantity'];

            $product = $this->productClient->getProduct($productId);

            $stock = $product['stock'] ?? 0;
            $unitPrice = (float) ($product['price'] ?? 0); 
            $productName = $product['name'] ?? 'Unknown Product';

            // Cek aturan bisnis: apakah stok cukup
            if ($stock < $quantity) {
                throw new BusinessException("Stok {$productName} tidak mencukupi: diminta {$quantity}, tersedia {$stock}");
            }

            $subtotal = $unitPrice * $quantity;
            $totalAmount += $subtotal;

            $orderItemsData[] = [
                'product_id' => $productId,
                'product_name' => $productName,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        }

        // 3. Simpan pesanan ke database menggunakan transaksi database (DB::transaction)
        $order = DB::transaction(function () use ($userId, $totalAmount, $orderItemsData) {
            $order = Order::create([
                'user_id' => $userId,
                'status' => 'PENDING',
                'total_amount' => $totalAmount,
            ]);

            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            return $order;
        });

        // 4. Minta cart-service mengosongkan keranjang
        $cleared = $this->cartClient->clearCart($userId);
        if (!$cleared) {
            Log::warning("Pesanan {$order->id} berhasil dibuat, tetapi keranjang user {$userId} gagal dikosongkan.");
        }

        // Kembalikan pesanan beserta relasi items-nya
        return $order->load('items');
    }
}