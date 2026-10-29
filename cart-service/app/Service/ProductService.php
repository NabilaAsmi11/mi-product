<?php

namespace App\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductService
{
    public function getProduct(int $productId): ?array
    {
        try {
            $response = Http::baseUrl(
                config('services.product.url')
           )->timeout(5)->get("/products/{$productId}");

            if (!$response->successful()) {
                Log::warning(
                    "Produk dengan id {$productId} gagal diambil",
                    [
                        'status' => $response->status(),
                        'response' => $response->body(),
                    ]
                );

                return null;
            }

            $product = $response->json();

            // Mendukung respons berbentuk objek langsung atau objek di dalam data.
            if (isset($product['data']) && is_array($product['data'])) {
                $product = $product['data'];
            }

            if (!is_array($product) || !isset($product['id'])) {
                Log::warning(
                    "Format respons produk tidak sesuai",
                    ['product_id' => $productId]
                );

                return null;
            }

            return $product;
        } catch (Throwable $e) {
            Log::error(
                "Gagal mengambil data produk dengan id {$productId}",
                [
                    'product_id' => $productId,
                    'error' => $e->getMessage(),
                ]
            );

            return null;
        }
    }
}
