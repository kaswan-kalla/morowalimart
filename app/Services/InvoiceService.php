<?php

namespace App\Services;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\AddressModel;

/**
 * Generate invoice PDF (HTML-based, disimpan ke file)
 */
class InvoiceService
{
    protected $orderModel;
    protected $orderItemModel;
    protected $addressModel;

    public function __construct()
    {
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->addressModel   = new AddressModel();
    }

    /**
     * Generate invoice dan simpan ke uploads/invoices/
     * @return string Path file invoice
     */
    public function generate(int $orderId, array $orderData = [], array $items = []): string
    {
        if (empty($orderData)) {
            $orderData = $this->orderModel->getWithAddress($orderId);
        }
        if (empty($items)) {
            $items = $this->orderItemModel->getByOrder($orderId);
        }

        $orderNumber = $orderData['order_number'];
        $invoiceNo   = $orderData['invoice_no'] ?? 'INV-' . $orderNumber;

        // Pastikan direktori ada
        $dir = FCPATH . 'uploads/invoices';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $invoiceNo . '.html';
        $filepath = $dir . '/' . $filename;

        // Cari alamat
        $address = [];
        if (!empty($orderData['address_id'])) {
            $address = $this->addressModel->find($orderData['address_id']);
        }

        // Build HTML
        $html = $this->buildHtml($orderData, $items, $address);

        file_put_contents($filepath, $html);

        return 'uploads/invoices/' . $filename;
    }

    protected function buildHtml(array $order, array $items, array $address): string
    {
        $orderNumber = esc($order['order_number']);
        $invoiceNo   = esc($order['invoice_no'] ?? 'INV-' . $orderNumber);
        $date        = date('d/m/Y H:i', strtotime($order['paid_at'] ?? $order['created_at']));
        $total       = number_format($order['total_amount'], 0, ',', '.');
        $subtotal    = number_format($order['subtotal'], 0, ',', '.');
        $shipping    = number_format($order['shipping_cost'], 0, ',', '.');
        $discount    = number_format($order['discount_amount'], 0, ',', '.');
        $method      = esc(strtoupper(str_replace('_', ' ', $order['payment_method'] ?? '-')));

        $recipient   = esc($address['recipient_name'] ?? $order['recipient_name'] ?? '-');
        $addrText    = esc($address['address'] ?? $order['shipping_address'] ?? '-');
        $city        = esc($address['city'] ?? '-');
        $phone       = esc($address['phone'] ?? $order['phone'] ?? '-');

        $rows = '';
        $no = 1;
        foreach ($items as $item) {
            $price = number_format($item['price'], 0, ',', '.');
            $sub  = number_format($item['price'] * $item['qty'], 0, ',', '.');
            $rows .= <<<ROW
            <tr>
                <td>{$no}</td>
                <td>{$item['product_name']}</td>
                <td>{$item['qty']}</td>
                <td>Rp {$price}</td>
                <td>Rp {$sub}</td>
            </tr>
ROW;
            $no++;
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice {$invoiceNo}</title>
<style>
    body { font-family: 'Segoe UI', Arial, sans-serif; margin:0; padding:20px; color:#333; }
    .header { text-align:center; margin-bottom:30px; border-bottom:2px solid #0d6efd; padding-bottom:15px; }
    .header h1 { color:#0d6efd; margin:0; font-size:24px; }
    .header p { margin:3px 0; color:#666; }
    .info { margin-bottom:20px; }
    .info table { width:100%; }
    .info td { vertical-align:top; width:50%; }
    table.items { width:100%; border-collapse:collapse; margin-bottom:20px; }
    table.items th { background:#0d6efd; color:white; padding:8px; text-align:left; }
    table.items td { padding:8px; border-bottom:1px solid #ddd; }
    table.items tr:nth-child(even) { background:#f8f9fa; }
    .total { text-align:right; font-size:16px; margin-top:10px; }
    .total strong { color:#dc3545; font-size:20px; }
    .footer { text-align:center; margin-top:30px; padding-top:15px; border-top:1px solid #ddd; color:#999; font-size:12px; }
</style>
</head>
<body>
<div class="header">
    <h1>MorowaliMart</h1>
    <p>Invoice: {$invoiceNo}</p>
    <p>Tanggal: {$date}</p>
</div>
<div class="info">
    <table>
        <tr>
            <td>
                <strong>Kepada:</strong><br>
                {$recipient}<br>
                {$addrText}<br>
                {$city}<br>
                Telp: {$phone}
            </td>
            <td style="text-align:right">
                <strong>No. Pesanan:</strong> {$orderNumber}<br>
                <strong>Invoice:</strong> {$invoiceNo}<br>
                <strong>Pembayaran:</strong> {$method}
            </td>
        </tr>
    </table>
</div>
<table class="items">
    <tr><th>#</th><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr>
    {$rows}
</table>
<div class="total">
    <p>Subtotal: Rp {$subtotal}</p>
    <p>Ongkos Kirim: Rp {$shipping}</p>
    <p>Diskon: Rp {$discount}</p>
    <p><strong>Total: Rp {$total}</strong></p>
</div>
<div class="footer">
    Terima kasih telah berbelanja di MorowaliMart.<br>
    Invoice ini sah dan diproses secara otomatis oleh sistem.
</div>
</body>
</html>
HTML;
    }
}
