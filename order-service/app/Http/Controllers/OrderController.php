<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessException;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Menampilkan semua pesanan.
     */
    public function index(): JsonResponse
    {
        $orders = Order::with('items')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'message' => 'Orders retrieved successfully',
            'data' => $orders,
        ], 200);
    }

    /**
     * Membuat pesanan baru.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder(
            $request->validated('user_id')
        );

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order,
        ], 201);
    }

    /**
     * Health check.
     */
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'healthy',
            'service' => 'order-service',
            'timestamp' => now()->toIso8601String(),
        ], 200);
    }

    /**
     * Menampilkan detail pesanan berdasarkan ID.
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Order retrieved successfully',
            'data' => $order,
        ], 200);
    }

    /**
     * Menampilkan pesanan milik pengguna.
     */
    public function getByUser(
        int $userId,
        Request $request
    ): JsonResponse {
       
        $request->validate([
            'status' => 'sometimes|in:PENDING,PAID,CANCELLED',
        ]);

        $status = $request->query('status');

        $orders = $this->orderService->getOrdersByUser(
            $userId,
            $status
        );

        return response()->json([
            'message' => 'User orders retrieved successfully',
            'data' => $orders,
        ], 200);
    }

    /**
     * Simulasi pembayaran pesanan.
     */
    public function pay(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->payOrder($id);

            if (!$order) {
                return response()->json([
                    'message' => 'Order not found',
                ], 404);
            }

            return response()->json([
                'message' => 'Payment simulation successful',
                'data' => $order,
            ], 200);
        } catch (BusinessException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        }
    }
}