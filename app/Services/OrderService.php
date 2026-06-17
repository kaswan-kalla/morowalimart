<?php

namespace App\Services;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\OrderHistoryModel;
use App\Models\OrderFulfillmentModel;
use App\Models\AuditLogModel;

/**
 * Manajemen state pesanan, histori, fulfillment, dan audit
 */
class OrderService
{
    protected $orderModel;
    protected $orderItemModel;
    protected $orderHistoryModel;
    protected $fulfillmentModel;
    protected $auditLogModel;

    public function __construct()
    {
        $this->orderModel       = new OrderModel();
        $this->orderItemModel   = new OrderItemModel();
        $this->orderHistoryModel = new OrderHistoryModel();
        $this->fulfillmentModel = new OrderFulfillmentModel();
        $this->auditLogModel    = new AuditLogModel();
    }

    /**
     * Update order ke status processing (pembayaran berhasil)
     */
    public function markAsProcessing(int $orderId, string $paymentMethod): array
    {
        $order = $this->orderModel->find($orderId);
        if (!$order) {
            throw new \RuntimeException('Order tidak ditemukan');
        }

        $now = date('Y-m-d H:i:s');
        $orderNumber = $order['order_number'];

        // Generate invoice number
        $invoiceNo = 'INV-' . $orderNumber;

        $this->orderModel->update($orderId, [
            'status'         => 'processing',
            'payment_status' => 'paid',
            'paid_at'        => $now,
            'invoice_no'     => $invoiceNo,
            'payment_method' => $paymentMethod,
        ]);

        // Simpan histori
        $paymentLabel = strtoupper(str_replace('_', ' ', $paymentMethod));
        $this->orderHistoryModel->insert([
            'order_id'    => $orderId,
            'message'     => "Order {$orderNumber} berhasil dibayar melalui {$paymentLabel}.",
            'created_by'  => $order['user_id'],
        ]);

        // Buat fulfillment
        $this->fulfillmentModel->insert([
            'order_id' => $orderId,
            'status'   => 'waiting_pick',
        ]);

        return [
            'order'       => $order,
            'orderNumber' => $orderNumber,
            'invoiceNo'   => $invoiceNo,
        ];
    }

    /**
     * Ambil semua item order
     */
    public function getOrderItems(int $orderId): array
    {
        return $this->orderItemModel->getByOrder($orderId);
    }

    /**
     * Catat audit trail
     */
    public function audit(string $action, string $entityType, int $entityId, ?int $userId, array $details = []): void
    {
        $this->auditLogModel->insert([
            'user_id'     => $userId,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'details'     => json_encode($details),
            'ip_address'  => service('request')->getIPAddress(),
        ]);
    }
}
