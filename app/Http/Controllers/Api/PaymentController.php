<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function create(Checkout $checkout)
    {
        $invoice = $this->paymentService->createInvoice($checkout);

        return response()->json([
            'status'  => 'success',
            'message' => 'Invoice created',
            'data'    => $invoice,
        ]);
    }

    public function webhook(Request $request)
    {
        $token = $request->header('X-CALLBACK-TOKEN');

        if (!$this->paymentService->validateWebhook($token)) {
            return response()->json(['message' => 'Unauthorized callback token'], 401);
        }

        $checkout = $this->paymentService->handleWebhook($request->all());

        if (!$checkout) {
            return response()->json(['message' => 'Checkout not found or invalid external_id'], 404);
        }

        return response()->json(['message' => 'Webhook processed successfully']);
    }
}
