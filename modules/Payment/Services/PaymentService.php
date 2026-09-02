<?php

namespace Modules\Payment\Services;

use Modules\Order\Models\Order;
use Modules\Payment\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Process payment for an order
     */
    public function processPayment(Order $order, array $paymentData): Payment
    {
        return DB::transaction(function () use ($order, $paymentData) {
            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'provider' => $paymentData['provider'],
                'transaction_id' => $paymentData['transaction_id'] ?? $this->generateTransactionId(),
                'amount' => $order->total,
                'currency' => $order->currency,
                'payment_status' => 'pending',
                'payment_method' => $paymentData['payment_method'] ?? null,
                'card_last_four' => $paymentData['card_last_four'] ?? null,
                'card_brand' => $paymentData['card_brand'] ?? null,
                'mpesa_receipt' => $paymentData['mpesa_receipt'] ?? null,
                'mpesa_phone' => $paymentData['mpesa_phone'] ?? null,
                'provider_response' => $paymentData['provider_response'] ?? null,
            ]);

            return $payment;
        });
    }

    /**
     * Confirm a successful payment
     */
    public function confirmPayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $payment->markAsCompleted();
            
            // Update order status
            $order = $payment->order;
            $order->markAsPaid();
        });
    }

    /**
     * Fail a payment
     */
    public function failPayment(Payment $payment, string $reason): void
    {
        DB::transaction(function () use ($payment, $reason) {
            $payment->markAsFailed($reason);
            
            // Update order status
            $order = $payment->order;
            $order->update(['status' => 'failed']);
            $order->addStatus('failed', "Payment failed: {$reason}");
        });
    }

    /**
     * Refund a payment
     */
    public function refundPayment(Payment $payment, float $amount): void
    {
        DB::transaction(function () use ($payment, $amount) {
            $payment->refund($amount);
            
            // Update order status
            $order = $payment->order;
            $order->update(['status' => 'refunded']);
            $order->addStatus('refunded', "Refunded: {$amount}");
        });
    }

    /**
     * Generate transaction ID
     */
    protected function generateTransactionId(): string
    {
        return 'TXN' . Str::upper(Str::random(12));
    }

    /**
     * Validate payment webhook
     */
    public function validateWebhook(string $provider, array $payload): bool
    {
        // This would contain provider-specific validation logic
        // For example, verifying Stripe signatures or M-Pesa security
        
        return true;
    }

    /**
     * Handle payment webhook
     */
    public function handleWebhook(string $provider, array $payload): void
    {
        // Process webhook from payment provider
        // This is where you'd update payment status based on provider callbacks
    }
}