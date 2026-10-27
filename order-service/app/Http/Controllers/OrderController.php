<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    protected OrderService $orderService;

    /**
     * Inject OrderService dependency.
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Handle order creation request.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder(
            $request->validated('user_id')
        );

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);
    }

    /**
     * Health check endpoint for monitoring service status.
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
     * Get order detail by ID.
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Order retrieved successfully',
            'data' => $order
        ], 200);
    }
}