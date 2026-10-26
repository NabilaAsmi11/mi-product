namespace App\Services\Clients;

use App\Exceptions\ServiceUnavailableException;
use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductServiceClient
{
    protected string $baseUrl;

    public function __construct()
    {
        // Membaca URL product-service dari environment variable
        $this->baseUrl = config('services.product.url', env('PRODUCT_SERVICE_URL', 'http://product-service:3001'));
    }

    public function getProduct($productId)
    {
        try {
            // Melakukan HTTP GET dengan timeout 5 detik
            $response = Http::timeout(5)->get("{$this->baseUrl}/products/{$productId}");

            if ($response->status() === 404) {
                throw new BusinessException("Produk dengan id {$productId} tidak ditemukan");
            }

            if ($response->failed()) {
                Log::error("Product service error: " . $response->body());
                throw new ServiceUnavailableException("Product service tidak dapat dihubungi, coba lagi nanti");
            }

            return $response->json('data');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Product service connection failed: " . $e->getMessage());
            throw new ServiceUnavailableException("Product service tidak dapat dihubungi, coba lagi nanti");
        }
    }
}