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
        $cart = $this->cartClient->getCart($userId);
        
        $items = $cart['items'] ?? [];
        if (empty($items)) {
            throw new BusinessException("Keranjang belanja kosong");
        }

        $orderItemsData = [];
        $totalAmount = 0;

        foreach ($items as $cartItem) {
            $productId = $cartItem['product_id'];
            $quantity = $cartItem['quantity'];

            $product = $this->productClient->getProduct($productId);

            $stock = $product['stock'] ?? 0;
            $unitPrice = (float) ($product['price'] ?? 0); 
            $productName = $product['name'] ?? 'Unknown Product';

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

        $cleared = $this->cartClient->clearCart($userId);
        if (!$cleared) {
            Log::warning("Pesanan {$order->id} berhasil dibuat, tetapi keranjang user {$userId} gagal dikosongkan.");
        }

        return $order->load('items');
    }

    /**
     * Commit 8: Get order detail by ID.
     */
    public function getOrderById(int $id)
    {
        return Order::with('items')->find($id);
    }

    /**
     * Commit 9: Get orders list by user ID with optional status filter.
     */
    public function getOrdersByUser(int $userId, ?string $status = null)
    {
        $query = Order::with('items')->where('user_id', $userId);

        if ($status) {
            $query->where('status', strtoupper($status));
        }

        return $query->get();
    }

    /**
     * Commit 10: Simulate order payment and update status.
     */
    public function payOrder(int $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return null;
        }

        if ($order->status === 'PAID') {
            throw new BusinessException("Pesanan sudah pernah dibayar sebelumnya.");
        }

        $order->update(['status' => 'PAID']);

        Log::info("Simulasi Pembayaran Berhasil: Pesanan ID {$id} telah lunas.");

        return $order->load('items');
    }
}