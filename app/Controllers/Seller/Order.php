<?php

namespace App\Controllers\Seller;

use App\Controllers\BaseController;
use App\Models\StoreModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\UserModel;

/**
 * Pesanan Seller - proses & kirim
 */
class Order extends BaseController
{
    protected $store, $orderModel, $orderItemModel, $userModel;

    public function __construct()
    {
        $storeModel = new StoreModel();
        $this->store = $storeModel->findByUserId(session()->get('user_id'));
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->userModel      = new UserModel();
    }

    public function index()
    {
        if (!$this->store) return redirect()->to('/seller/toko');

        $status = $this->request->getGet('status');
        $result = $this->orderModel->getByStore($this->store['id'], $status);

        foreach ($result['orders'] as &$order) {
            $order['items'] = $this->orderItemModel->getByOrderAndStore($order['id'], $this->store['id']);
        }

        $data = [
            'meta_title' => 'Pesanan Masuk',
            'orders'     => $result['orders'],
            'total'      => $result['total'],
            'status'     => $status,
        ];
        return view('seller/order/index', $data);
    }

    public function detail($id)
    {
        if (!$this->store) return redirect()->to('/seller/toko');

        $order = $this->orderModel->getWithAddress($id);
        if (!$order) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $data = [
            'meta_title' => 'Detail Pesanan',
            'order'      => $order,
            'items'      => $this->orderItemModel->getByOrderAndStore($id, $this->store['id']),
        ];
        return view('seller/order/detail', $data);
    }

    /**
     * Proses pesanan (status: processing)
     */
    public function process($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $order = $this->orderModel->find($id);
        if (!$order || $order['status'] !== 'awaiting_payment') {
            return $this->response->setJSON(['status' => false, 'message' => 'Status tidak valid']);
        }

        $this->orderModel->update($id, ['status' => 'processing']);
        return $this->response->setJSON(['status' => true, 'message' => 'Pesanan diproses']);
    }

    /**
     * Kirim pesanan (status: shipped)
     */
    public function ship($id)
    {
        if (!$this->request->isAJAX()) return $this->response->setJSON(['status' => false]);

        $order = $this->orderModel->find($id);
        if (!$order || $order['status'] !== 'processing') {
            return $this->response->setJSON(['status' => false, 'message' => 'Status tidak valid']);
        }

        $trackingNumber = $this->request->getPost('tracking_number');
        $courierId      = (int) $this->request->getPost('courier_id');

        $this->orderModel->update($id, [
            'status'          => 'shipped',
            'tracking_number' => $trackingNumber,
            'courier_id'      => $courierId ?: null,
            'shipped_at'      => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Pesanan dikirim']);
    }

    /**
     * Daftar kurir untuk dropdown
     */
    public function getCourierOption()
    {
        $couriers = $this->userModel
            ->where('role', 'courier')
            ->where('is_active', 1)
            ->findAll();

        $options = [['DisplayText' => '-- Pilih Kurir --', 'Value' => 0]];
        foreach ($couriers as $c) {
            $options[] = [
                'DisplayText' => $c['name'] . ' (' . $c['email'] . ')',
                'Value'       => (int) $c['id'],
            ];
        }

        return $this->response->setJSON(['Result' => 'OK', 'Options' => $options]);
    }
}
