<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Checkout;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function index(): JsonResponse
    {
        $checkouts = $this->checkoutService->getHistory(Auth::id());

        return response()->json([
            'status' => 'success',
            'data'   => $checkouts,
        ]);
    }

    public function store(CheckoutRequest $request): JsonResponse
    {
        $checkout = $this->checkoutService->checkout($request->validated(), Auth::id());

        return response()->json([
            'status'  => 'success',
            'message' => 'Checkout berhasil',
            'data'    => $checkout,
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $checkout = $this->checkoutService->getDetail($id, Auth::id());

        return response()->json([
            'status' => 'success',
            'data'   => $checkout,
        ]);
    }

    public function cancel($id): JsonResponse
    {
        $checkout = $this->checkoutService->cancelCheckout($id, Auth::id());

        return response()->json([
            'status'  => 'success',
            'message' => 'Checkout berhasil dibatalkan',
            'data'    => $checkout,
        ]);
    }
}
