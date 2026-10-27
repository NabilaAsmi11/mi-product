<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use App\Exceptions\BusinessException;
use App\Exceptions\ServiceUnavailableException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $userId = $request->input('user_id');
            $order = $this->orderService->createOrder($userId);

            return response()->json([
                'message' => 'Pesanan berhasil dibuat',
                'data' => $order
            ], 201);

        } catch (BusinessException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 409);

        } catch (ServiceUnavailableException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 503);

        } catch (\Throwable $e) {
            Log::error("Unexpected error in checkout: " . $e->getMessage(), [
                'exception' => $e
            ]);

            $response = [
                'message' => 'Terjadi kesalahan pada server'
            ];

            // Tampilkan detail teknis jika mode debug aktif
            if (config('app.debug')) {
                $response['error'] = $e->getMessage();
                $response['trace'] = $e->getTrace();
            }

            return response()->json($response, 500);
        }
    }
}