<?php

namespace App\Services\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ServiceUnavailableException;

class CartServiceClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            config('services.cart.url', 'http://cart-service:3001'),
            '/'
        );
    }

    public function getCart(int $userId): array
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/api/carts/{$userId}");

            if ($response->failed()) {
                throw new ServiceUnavailableException(
                    'Gagal mengambil data keranjang dari Cart Service'
                );
            }

            $cart = $response->json('data');

            if (
                !is_array($cart) ||
                !isset($cart['user_id']) ||
                !isset($cart['items']) ||
                !is_array($cart['items'])
            ) {
                throw new ServiceUnavailableException(
                    'Format respons Cart Service tidak sesuai'
                );
            }

            return $cart;
        } catch (ServiceUnavailableException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new ServiceUnavailableException(
                'Cart Service tidak dapat diakses: ' . $e->getMessage()
            );
        }
    }

    public function clearCart(int $userId): bool
    {
        try {
            $response = Http::timeout(5)
                ->delete("{$this->baseUrl}/api/carts/{$userId}");

            if ($response->failed()) {
                Log::warning(
                    "Gagal mengosongkan keranjang user {$userId}",
                    ['status' => $response->status()]
                );

                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::warning(
                "Cart Service tidak dapat diakses saat mengosongkan keranjang user {$userId}",
                ['error' => $e->getMessage()]
            );

            return false;
        }
    }
}
