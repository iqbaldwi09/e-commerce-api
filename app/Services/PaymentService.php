<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Checkout;

class PaymentService
{
    protected string $baseUrl;
    protected string $secretKey;
    protected string $webhookToken;

    public function __construct()
    {
        $this->baseUrl = config('services.xendit.base_url', env('XENDIT_BASE_URL'));
        $this->secretKey = env('XENDIT_SECRET_KEY');
        $this->webhookToken = env('XENDIT_WEBHOOK_TOKEN');
    }

    public function createInvoice(Checkout $checkout)
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/v2/invoices", [
                'external_id' => 'checkout_' . $checkout->id,
                'amount'      => $checkout->total,
                'payer_email' => $checkout->user->email,
                'description' => 'Payment for checkout #' . $checkout->id,
            ]);

        if ($response->failed()) {
            throw new \Exception('Failed to create invoice: ' . $response->body());
        }

        $invoice = $response->json();

        $checkout->update([
            'payment_invoice_id' => $invoice['id'],
        ]);

        return $invoice;
    }

    public function validateWebhook($headerToken): bool
    {
        return $headerToken === $this->webhookToken;
    }

    public function handleWebhook(array $payload)
    {
        $checkoutId = str_replace('checkout_', '', $payload['external_id'] ?? null);

        $checkout = Checkout::find($checkoutId);

        if (!$checkout) {
            return null;
        }

        if (($payload['status'] ?? '') === 'PAID') {
            $checkout->update(['status' => 'paid']);
        } elseif (($payload['status'] ?? '') === 'EXPIRED') {
            $checkout->update(['status' => 'cancelled']);
        }

        return $checkout;
    }

}
