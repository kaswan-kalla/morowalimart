<?php

namespace App\Services;

use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\PaymentLogModel;

/**
 * Orchestrator proses bisnis setelah pembayaran berhasil.
 * Mengkoordinasikan InventoryService, OrderService, InvoiceService, NotificationService.
 */
class PaymentService
{
    protected $orderModel;
    protected $paymentModel;
    protected $paymentLogModel;
    protected $midtransService;
    protected $inventoryService;
    protected $orderService;
    protected $invoiceService;
    protected $notificationService;

    public function __construct()
    {
        $this->orderModel          = new OrderModel();
        $this->paymentModel        = new PaymentModel();
        $this->paymentLogModel     = new PaymentLogModel();
        $this->midtransService     = new MidtransService();
        $this->inventoryService    = new InventoryService();
        $this->orderService        = new OrderService();
        $this->invoiceService      = new InvoiceService();
        $this->notificationService = new NotificationService();
    }

    /**
     * Proses notifikasi webhook dari Midtrans.
     * Menentukan status pembayaran dan menjalankan pipeline bisnis.
     *
     * @return array ['status' => bool, 'message' => string, 'httpCode' => int]
     */
    public function processNotification(string $rawPayload): array
    {
        $notif = json_decode($rawPayload, true);

        if (!$notif || !isset($notif['order_id'])) {
            return ['status' => false, 'message' => 'Invalid payload', 'httpCode' => 400];
        }

        $orderIdFromMidtrans = $notif['order_id'] ?? '';
        $statusCode          = $notif['status_code'] ?? '';
        $grossAmount         = $notif['gross_amount'] ?? '';
        $signatureKey        = $notif['signature_key'] ?? '';
        $transactionId       = $notif['transaction_id'] ?? '';
        $transactionStatus   = $notif['transaction_status'] ?? '';
        $fraudStatus         = $notif['fraud_status'] ?? '';
        $paymentType         = $notif['payment_type'] ?? '';

        // Cari order by order_number
        $order = $this->orderModel->where('order_number', $orderIdFromMidtrans)->first();
        if (!$order) {
            return ['status' => false, 'message' => 'Order not found', 'httpCode' => 404];
        }

        // Simpan log sebelum validasi
        $logId = $this->paymentLogModel->insert([
            'order_id'           => $order['id'],
            'transaction_id'     => $transactionId,
            'transaction_status' => $transactionStatus,
            'fraud_status'       => $fraudStatus,
            'payment_type'       => $paymentType,
            'status_code'        => $statusCode,
            'signature_key'      => $signatureKey,
            'raw_payload'        => $rawPayload,
            'is_processed'       => 0,
        ]);

        // Validasi signature
        if (!$this->midtransService->validateSignature($orderIdFromMidtrans, $statusCode, $grossAmount, $signatureKey)) {
            $this->paymentLogModel->update($logId, [
                'is_processed'  => 1,
                'error_message' => 'Invalid signature',
            ]);
            return ['status' => false, 'message' => 'Invalid signature', 'httpCode' => 403];
        }

        // Ekstrak detail pembayaran
        $details = $this->extractPaymentDetails($notif);

        // Cek apakah ini transaksi sukses (settlement / capture accept)
        $isSuccess = (
            ($transactionStatus === 'settlement') ||
            ($transactionStatus === 'capture' && $fraudStatus === 'accept')
        );

        if ($isSuccess) {
            try {
                $this->processSuccessPayment($order, $paymentType, $transactionId, $details);
            } catch (\Throwable $e) {
                // Rollback transaction sudah terjadi di processSuccessPayment
                $this->paymentLogModel->update($logId, [
                    'is_processed'  => 1,
                    'error_message' => $e->getMessage(),
                ]);
                log_message('error', 'Payment pipeline error: ' . $e->getMessage());
                return ['status' => false, 'message' => 'Processing failed: ' . $e->getMessage(), 'httpCode' => 500];
            }

            $this->paymentLogModel->update($logId, ['is_processed' => 1]);
            return ['status' => true, 'message' => 'Payment processed successfully', 'httpCode' => 200];
        }

        // Handle non-success statuses
        $this->handleNonSuccessPayment($order, $transactionStatus, $fraudStatus, $paymentType, $details);

        $this->paymentLogModel->update($logId, ['is_processed' => 1]);
        return ['status' => true, 'message' => 'Notification processed', 'httpCode' => 200];
    }

    /**
     * Pipeline bisnis lengkap untuk pembayaran sukses.
     * Semua operasi dalam 1 database transaction.
     */
    protected function processSuccessPayment(array $order, string $paymentType, string $transactionId, array $details): void
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $orderId      = $order['id'];
            $userId       = $order['user_id'];
            $orderNumber  = $order['order_number'];
            $totalAmount  = (float) $order['total_amount'];

            // Step 1: Simpan detail pembayaran di order
            $updateData = ['payment_details' => json_encode($details)];
            $this->orderModel->update($orderId, $updateData);

            // Step 2: Update order status & simpan histori
            $result = $this->orderService->markAsProcessing($orderId, $paymentType);

            // Step 3: Insert/update record payment
            $existingPayment = $this->paymentModel->getByOrder($orderId);
            $paymentData = [
                'order_id'       => $orderId,
                'user_id'        => $userId,
                'payment_method' => $paymentType,
                'amount'         => $totalAmount,
                'status'         => 'verified',
            ];
            if ($existingPayment) {
                $this->paymentModel->update($existingPayment['id'], $paymentData);
            } else {
                $this->paymentModel->insert($paymentData);
            }

            // Step 4: Kurangi stok + catat mutasi stok
            $items = $this->orderService->getOrderItems($orderId);
            $this->inventoryService->deductStock($items, $orderNumber);

            // Step 5: Generate invoice
            $this->invoiceService->generate($orderId, $order, $items);

            // Step 6: Notifikasi customer
            $this->notificationService->notifyCustomer($userId, $orderNumber, $orderId);

            // Step 7: Notifikasi admin
            $this->notificationService->notifyAdmins($orderNumber, $totalAmount, $orderId);

            // Step 8: Audit trail
            $this->orderService->audit(
                'payment_success',
                'order',
                $orderId,
                $userId,
                [
                    'order_number'  => $orderNumber,
                    'amount'        => $totalAmount,
                    'payment_method' => $paymentType,
                    'transaction_id' => $transactionId,
                ]
            );

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed');
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Handle status non-success (pending, deny, expire, cancel)
     */
    protected function handleNonSuccessPayment(array $order, string $transactionStatus, string $fraudStatus, string $paymentType, array $details): void
    {
        $orderId = $order['id'];
        $updateData = [];
        $paymentData = [
            'order_id'       => $orderId,
            'user_id'        => $order['user_id'],
            'payment_method' => $paymentType,
            'amount'         => $order['total_amount'],
        ];

        switch ($transactionStatus) {
            case 'capture':
                if ($fraudStatus === 'challenge') {
                    $updateData['status']         = 'awaiting_payment';
                    $paymentData['status']        = 'pending';
                }
                break;

            case 'pending':
                $updateData['status']             = 'awaiting_payment';
                $paymentData['status']            = 'pending';
                break;

            case 'deny':
            case 'cancel':
            case 'expire':
                $updateData['status']             = 'cancelled';
                $updateData['cancel_reason']      = 'Pembayaran ' . $transactionStatus;
                $updateData['payment_status']     = $transactionStatus === 'deny' ? 'denied' : 'expired';
                $paymentData['status']            = 'rejected';
                break;

            default:
                $updateData['status']             = 'awaiting_payment';
                $paymentData['status']            = 'pending';
                break;
        }

        if (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            // Kembalikan stok jika sebelumnya sudah dikurangi
            $currentStatus = $order['status'] ?? '';
            if (in_array($currentStatus, ['processing', 'shipped', 'completed'])) {
                $items = $this->orderService->getOrderItems($orderId);
                $invService = new InventoryService();
                $invService->restoreStock($items, $order['order_number']);
            }
        }

        if (!empty($details)) {
            $updateData['payment_details'] = json_encode($details);
        }
        if (!empty($paymentType)) {
            $updateData['payment_method'] = $paymentType;
        }

        $this->orderModel->update($orderId, $updateData);

        $existingPayment = $this->paymentModel->getByOrder($orderId);
        if ($existingPayment) {
            $this->paymentModel->update($existingPayment['id'], $paymentData);
        } else {
            $this->paymentModel->insert($paymentData);
        }
    }

    /**
     * Ekstrak detail pembayaran dari payload notifikasi Midtrans.
     */
    protected function extractPaymentDetails(array $notif): array
    {
        $details = [];
        $paymentType = $notif['payment_type'] ?? '';

        if ($paymentType === 'bank_transfer' && isset($notif['va_numbers'])) {
            foreach ($notif['va_numbers'] as $va) {
                $details['bank']      = $va['bank'] ?? '';
                $details['va_number'] = $va['va_number'] ?? '';
                break;
            }
        } elseif ($paymentType === 'gopay') {
            $details['qr_string'] = $notif['actions'][0]['url'] ?? '';
        } elseif ($paymentType === 'shopeepay') {
            foreach ($notif['actions'] ?? [] as $action) {
                if (($action['name'] ?? '') === 'generate-qr-code') {
                    $details['qr_url'] = $action['url'] ?? '';
                    break;
                }
            }
        } elseif ($paymentType === 'qris') {
            foreach ($notif['actions'] ?? [] as $action) {
                if (isset($action['url'])) {
                    $details['qr_url'] = $action['url'];
                    break;
                }
            }
        }

        return $details;
    }
}
