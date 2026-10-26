namespace App\Services\Clients;

use App\Exceptions\ServiceUnavailableException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CartServiceClient
{
    protected string $baseUrl;

    public function __construct()
    {
        // Membaca URL cart-service dari environment variable
        $this->baseUrl = config('services.cart.url', env('CART_SERVICE_URL', 'http://cart-service:3000'));
    }

    public function getCart($userId)
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/carts/{$userId}");

            if ($response->failed()) {
                Log::error("Cart service error: " . $response->body());
                throw new ServiceUnavailableException("Cart service tidak dapat dihubungi, coba lagi nanti");
            }

            return $response->json('data');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Cart service connection failed: " . $e->getMessage());
            throw new ServiceUnavailableException("Cart service tidak dapat dihubungi, coba lagi nanti");
        }
    }

    public function clearCart($userId)
    {
        try {
            $response = Http::timeout(5)->delete("{$this->baseUrl}/carts/{$userId}");

            if ($response->failed()) {
                Log::warning("Gagal mengosongkan keranjang untuk user {$userId}: " . $response->body());
                return false;
            }

            return true;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning("Koneksi gagal saat mengosongkan keranjang user {$userId}: " . $e->getMessage());
            return false;
        }
    }
}