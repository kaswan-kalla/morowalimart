<?php

namespace App\Services;

use App\Models\NotificationModel;
use App\Models\UserModel;

/**
 * Manajemen notifikasi user dan admin
 */
class NotificationService
{
    protected $notificationModel;
    protected $userModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->userModel         = new UserModel();
    }

    /**
     * Notifikasi ke customer bahwa pembayaran berhasil
     */
    public function notifyCustomer(int $userId, string $orderNumber, int $orderId): void
    {
        $this->notificationModel->insert([
            'user_id' => $userId,
            'type'    => 'payment_success',
            'title'   => 'Pembayaran Berhasil',
            'message' => "Pembayaran untuk Order {$orderNumber} berhasil diterima. Pesanan Anda sedang diproses oleh tim MorowaliMart.",
            'link'    => site_url("order/{$orderId}"),
        ]);
    }

    /**
     * Notifikasi ke semua admin bahwa ada pesanan baru dibayar
     */
    public function notifyAdmins(string $orderNumber, float $amount, int $orderId): void
    {
        $admins = $this->userModel->where('role', 'admin')
            ->where('is_active', 1)
            ->findAll();

        foreach ($admins as $admin) {
            $this->notificationModel->insert([
                'user_id' => $admin['id'],
                'type'    => 'new_order',
                'title'   => 'Pesanan Baru Dibayar',
                'message' => "Pesanan {$orderNumber} telah dibayar dan siap diproses. Total: Rp " . number_format($amount, 0, ',', '.'),
                'link'    => site_url("admin/payments"),
            ]);
        }
    }

    /**
     * Kirim notifikasi ke user tertentu
     */
    public function send(int $userId, string $type, string $title, string $message, ?string $link = null): void
    {
        $this->notificationModel->insert([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
        ]);
    }
}
