<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\ProductImageModel;
use App\Models\CategoryModel;
use App\Models\StoreModel;

class Product extends BaseController
{
    protected $productModel, $productImageModel, $categoryModel, $storeModel;

    public function __construct()
    {
        $this->productModel      = new ProductModel();
        $this->productImageModel = new ProductImageModel();
        $this->categoryModel     = new CategoryModel();
        $this->storeModel        = new StoreModel();
    }

    public function index()
    {
        return view('admin/product/index', [
            'meta_title' => 'Input Data Barang',
            'categories' => $this->categoryModel->orderBy('name', 'ASC')->findAll(),
            'stores'     => $this->storeModel->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function data()
    {
        $search = $this->request->getGet('search')['value'] ?? '';
        $start  = (int) $this->request->getGet('start');
        $limit  = (int) $this->request->getGet('length');

        $builder = $this->productModel->select('products.*, stores.name as store_name, categories.name as category_name')
            ->join('stores', 'stores.id = products.store_id')
            ->join('categories', 'categories.id = products.category_id', 'left');
        if ($search) {
            $builder->groupStart()
                ->like('products.name', $search)
                ->orLike('products.sku', $search)
                ->orLike('stores.name', $search)
                ->groupEnd();
        }

        $total   = (clone $builder)->countAllResults(false);
        $products = $builder->orderBy('products.created_at', 'DESC')->get($limit, $start)->getResultArray();

        return $this->response->setJSON([
            'draw'            => (int) $this->request->getGet('draw'),
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $products,
        ]);
    }

    public function get($id)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->response->setJSON(['status' => false, 'message' => 'Produk tidak ditemukan']);
        }
        // Ambil gambar tambahan
        $images = $this->productImageModel->getByProduct($id);
        $product['additional_images'] = $images;

        return $this->response->setJSON(['status' => true, 'data' => $product]);
    }

    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $name  = $this->request->getPost('name');
        $sku   = $this->request->getPost('sku');
        $slug  = generate_slug($name);

        // Validasi slug ganda (termasuk yg soft-delete)
        $existing = $this->productModel->withDeleted()->where('slug', $slug)->first();
        if ($existing) {
            $slug = $slug . '-' . time();
        }

        $data = [
            'store_id'       => (int) $this->request->getPost('store_id'),
            'category_id'    => (int) $this->request->getPost('category_id') ?: null,
            'name'           => $name,
            'slug'           => $slug,
            'sku'            => $sku ?: null,
            'description'    => $this->request->getPost('description') ?: '',
            'price'          => (int) str_replace('.', '', $this->request->getPost('price')),
            'discount_price' => (int) str_replace('.', '', $this->request->getPost('discount_price')) ?: 0,
            'weight'         => (int) str_replace('.', '', $this->request->getPost('weight')),
            'stock'          => (int) $this->request->getPost('stock'),
            'is_active'      => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if (!$this->productModel->insert($data)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan: ' . implode(', ', $this->productModel->errors())]);
        }

        $productId = $this->productModel->getInsertID();

        // Upload gambar utama
        $file = $this->request->getFile('main_image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $path = upload_image($file, 'uploads/products');
            if ($path) {
                $this->productModel->update($productId, ['main_image' => $path]);
                // Simpan juga ke product_images
                $this->productImageModel->insert([
                    'product_id' => $productId,
                    'image'      => $path,
                    'is_main'    => 1,
                    'sort_order' => 0,
                ]);
            }
        }

        return $this->response->setJSON(['status' => true, 'message' => 'Produk berhasil ditambahkan']);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->response->setJSON(['status' => false, 'message' => 'Produk tidak ditemukan']);
        }

        $name = $this->request->getPost('name');
        $slug = generate_slug($name);

        // Cek slug ganda kecuali dirinya sendiri
        $existing = $this->productModel->where('slug', $slug)->where('id !=', $id)->first();
        if ($existing) {
            $slug = $slug . '-' . $id;
        }

        $data = [
            'store_id'       => (int) $this->request->getPost('store_id'),
            'category_id'    => (int) $this->request->getPost('category_id') ?: null,
            'name'           => $name,
            'slug'           => $slug,
            'sku'            => $this->request->getPost('sku') ?: null,
            'description'    => $this->request->getPost('description') ?: '',
            'price'          => (int) str_replace('.', '', $this->request->getPost('price')),
            'discount_price' => (int) str_replace('.', '', $this->request->getPost('discount_price')) ?: 0,
            'weight'         => (int) str_replace('.', '', $this->request->getPost('weight')),
            'stock'          => (int) $this->request->getPost('stock'),
            'is_active'      => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if (!$this->productModel->update($id, $data)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal mengupdate: ' . implode(', ', $this->productModel->errors())]);
        }

        // Upload gambar utama baru jika ada
        $file = $this->request->getFile('main_image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Hapus gambar lama
            if ($product['main_image']) {
                delete_image($product['main_image']);
            }
            $path = upload_image($file, 'uploads/products');
            if ($path) {
                $this->productModel->update($id, ['main_image' => $path]);
                // Update juga di product_images
                $existingMain = $this->productImageModel->where('product_id', $id)->where('is_main', 1)->first();
                if ($existingMain) {
                    $this->productImageModel->update($existingMain['id'], ['image' => $path]);
                } else {
                    $this->productImageModel->insert([
                        'product_id' => $id,
                        'image'      => $path,
                        'is_main'    => 1,
                        'sort_order' => 0,
                    ]);
                }
            }
        }

        return $this->response->setJSON(['status' => true, 'message' => 'Produk berhasil diperbarui']);
    }

    public function toggle()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false]);
        }
        $id = (int) $this->request->getPost('id');
        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->response->setJSON(['status' => false, 'message' => 'Produk tidak ditemukan']);
        }

        $this->productModel->update($id, ['is_active' => !$product['is_active']]);
        return $this->response->setJSON(['status' => true, 'message' => 'Status produk diperbarui']);
    }

    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid request']);
        }

        $id = (int) $this->request->getPost('id');
        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->response->setJSON(['status' => false, 'message' => 'Produk tidak ditemukan']);
        }

        // Hapus file gambar
        if ($product['main_image']) {
            delete_image($product['main_image']);
        }

        // Hapus gambar tambahan
        $images = $this->productImageModel->getByProduct($id);
        foreach ($images as $img) {
            delete_image($img['image']);
        }
        $this->productImageModel->where('product_id', $id)->delete();

        // Soft delete produk
        $this->productModel->delete($id);

        return $this->response->setJSON(['status' => true, 'message' => 'Produk berhasil dihapus']);
    }

    public function getCategoryOption()
    {
        $categories = $this->categoryModel->orderBy('name', 'ASC')->findAll();
        $options = [['DisplayText' => '-- Pilih Kategori --', 'Value' => '']];
        foreach ($categories as $c) {
            $options[] = ['DisplayText' => $c['name'], 'Value' => (int) $c['id']];
        }
        return $this->response->setJSON(['Result' => 'OK', 'Options' => $options]);
    }

    public function getStoreOption()
    {
        $stores = $this->storeModel->orderBy('name', 'ASC')->findAll();
        $options = [['DisplayText' => '-- Pilih Toko --', 'Value' => '']];
        foreach ($stores as $s) {
            $options[] = ['DisplayText' => $s['name'], 'Value' => (int) $s['id']];
        }
        return $this->response->setJSON(['Result' => 'OK', 'Options' => $options]);
    }
}
