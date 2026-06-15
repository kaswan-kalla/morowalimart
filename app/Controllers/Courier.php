<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;

/**
 * Controller Dashboard Kurir
 * Menampilkan delivery yang di-assign ke kurir + navigasi lokasi
 */
class Courier extends BaseController
{
    protected $orderModel, $orderItemModel;

    public function __construct()
    {
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
    }

    /**
     * Daftar delivery milik kurir yang login
     */
    public function index()
    {
        $userId  = $this->session->get('user_id');
        $page    = max(1, (int) $this->request->getGet('page'));
        $limit   = 10;
        $offset  = ($page - 1) * $limit;

        $builder = $this->orderModel
            ->where('courier_id', $userId)
            ->whereIn('status', ['shipped', 'completed']);

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        $orders = $builder->orderBy('shipped_at', 'DESC')
            ->findAll($limit, $offset);

        $data = [
            'content'    => 'courier',
            'meta_title' => 'Pengiriman Saya',
            'orders'     => $orders,
            'total'      => $total,
            'page'       => $page,
            'limit'      => $limit,
        ];

        return view('layout/marketplace_content', $data);
    }

    /**
     * Detail delivery
     */
    public function detail($id)
    {
        $userId = $this->session->get('user_id');
        $order  = $this->orderModel->find($id);

        if (!$order || $order['courier_id'] != $userId) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'content'    => 'courier',
            'subview'    => 'detail',
            'meta_title' => 'Pengiriman #' . $order['order_number'],
            'order'      => $order,
            'items'      => $this->orderItemModel->getByOrder($id),
        ];

        return view('layout/marketplace_content', $data);
    }

    /**
     * Tandai pesanan sebagai terkirim (completed)
     */
    public function complete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }

        $id     = $this->request->getPost('id');
        $userId = $this->session->get('user_id');
        $order  = $this->orderModel->find($id);

        if (!$order || $order['courier_id'] != $userId) {
            return $this->response->setJSON(['status' => false, 'message' => 'Pesanan tidak ditemukan']);
        }

        if ($order['status'] !== 'shipped') {
            return $this->response->setJSON(['status' => false, 'message' => 'Status tidak valid']);
        }

        $this->orderModel->update($id, [
            'status'       => 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Pengiriman selesai',
        ]);
    }
}
