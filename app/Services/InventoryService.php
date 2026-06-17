<?php

namespace App\Services;

use App\Models\ProductModel;
use App\Models\StockMovementModel;

/**
 * Manajemen stok dan mutasi stok
 */
class InventoryService
{
    protected $productModel;
    protected $stockMovementModel;

    public function __construct()
    {
        $this->productModel       = new ProductModel();
        $this->stockMovementModel = new StockMovementModel();
    }

    /**
     * Kurangi stok untuk setiap item order
     * @throws \RuntimeException jika stok tidak cukup
     */
    public function deductStock(array $orderItems, string $referenceNo): void
    {
        foreach ($orderItems as $item) {
            $productId = (int) $item['product_id'];
            $qty       = (int) $item['qty'];

            $affected = $this->productModel
                ->set('stock', "stock - {$qty}", false)
                ->set('sold', "sold + {$qty}", false)
                ->where('id', $productId)
                ->where('stock >=', $qty)
                ->update();

            if (!$affected) {
                throw new \RuntimeException(
                    "Stok tidak cukup untuk produk: {$item['product_name']}"
                );
            }

            // Catat mutasi stok
            $this->stockMovementModel->insert([
                'product_id'   => $productId,
                'qty'          => $qty,
                'type'         => 'OUT',
                'reference_no' => $referenceNo,
                'notes'        => "Penjualan Order {$referenceNo}",
            ]);
        }
    }

    /**
     * Kembalikan stok (saat pembayaran dibatalkan/ditolak setelah stok dikurangi)
     */
    public function restoreStock(array $orderItems, string $referenceNo): void
    {
        foreach ($orderItems as $item) {
            $productId = (int) $item['product_id'];
            $qty       = (int) $item['qty'];

            $this->productModel
                ->set('stock', "stock + {$qty}", false)
                ->set('sold', "sold - {$qty}", false)
                ->where('id', $productId)
                ->update();

            $this->stockMovementModel->insert([
                'product_id'   => $productId,
                'qty'          => $qty,
                'type'         => 'IN',
                'reference_no' => $referenceNo,
                'notes'        => "Retur Order {$referenceNo}",
            ]);
        }
    }
}
