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
            $response = Http::baseUri(config('services.product.url'))
                ->get("/api/products/{$productId}");

            if ($response->successful && $response->json('data')) {
                return $response->json('data');
            }

            return null;
        } catch (Throwable $e) {
            Log::error("Gagal mengambil data produk dengan id {$productId}", [
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}