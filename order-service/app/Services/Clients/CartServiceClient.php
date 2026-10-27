<?php

namespace App\Services\Clients;

use Illuminate\Support\Facades\Http;
use App\Exceptions\ServiceUnavailableException;

class CartServiceClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.cart.url', 'http://cart-service:3000');
    }

    public function getCart(int $userId): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/api/cart/{$userId}");

            if ($response->failed()) {
                throw new ServiceUnavailableException("Gagal mengambil data keranjang dari Cart Service");
            }

            return $response->json();
        } catch (\Exception $e) {
            throw new ServiceUnavailableException("Cart Service tidak dapat diakses: " . $e->getMessage());
        }
    }

    public function clearCart(int $userId): bool
    {
        try {
            $response = Http::timeout(5)->delete("{$this->baseUrl}/api/cart/{$userId}");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}