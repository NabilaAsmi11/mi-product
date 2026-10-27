<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use App\Exceptions\BusinessException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

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
     * Commit 8: Health check endpoint.
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
     * Commit 8: Get order detail by ID.
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

    /**
     * Commit 9: Get user orders list with status filter.
     */
    public function getByUser(int $userId, Request $request): JsonResponse
    {
        $status = $request->query('status');
        $orders = $this->orderService->getOrdersByUser($userId, $status);

        return response()->json([
            'message' => 'User orders retrieved successfully',
            'data' => $orders
        ], 200);
    }

    /**
     * Commit 10: Simulate order payment.
     */
    public function pay(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->payOrder($id);

            if (!$order) {
                return response()->json([
                    'message' => 'Order not found'
                ], 404);
            }

            return response()->json([
                'message' => 'Payment simulation successful',
                'data' => $order
            ], 200);
        } catch (BusinessException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 409);
        }
    }
}