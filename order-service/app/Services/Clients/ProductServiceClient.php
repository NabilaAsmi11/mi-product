<?php

namespace App\Services\Clients;

use Illuminate\Support\Facades\Http;
use App\Exceptions\ServiceUnavailableException;
use App\Exceptions\BusinessException;

class ProductServiceClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            config('services.product.url', 'http://product-service:3001'),
            '/'
        );
    }

    public function getProduct(int $productId): array
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/products/{$productId}");

            if ($response->status() === 404) {
                throw new BusinessException(
                    "Produk dengan ID {$productId} tidak ditemukan"
                );
            }

            if ($response->failed()) {
                throw new ServiceUnavailableException(
                    'Gagal mengambil data produk dari Product Service'
                );
            }

            $product = $response->json();

            if (!is_array($product) || !isset($product['id'])) {
                throw new ServiceUnavailableException(
                    'Format respons Product Service tidak sesuai'
                );
            }

            return $product;
        } catch (BusinessException $e) {
            throw $e;
        } catch (ServiceUnavailableException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new ServiceUnavailableException(
                'Product Service tidak dapat diakses: ' . $e->getMessage()
            );
        }
    }
}
