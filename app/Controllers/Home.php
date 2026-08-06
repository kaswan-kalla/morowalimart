<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;

/**
 * Controller halaman utama (Beranda)
 */
class Home extends BaseController
{
    protected $productModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->productModel  = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        // Admin langsung ke panel admin
        if (is_admin()) {
            return redirect()->to('admin/dashboard');
        }

        $data = [
            'content'          => 'home',
            'meta_title'       => 'Marketplace - Belanja Online Terpercaya',
            'categories'       => $this->categoryModel->getParents(),
            'latest_products'  => $this->productModel->getWithRelations()->where('products.is_active', 1)->orderBy('products.created_at', 'DESC')->findAll(8),
            'popular_products' => $this->productModel->getWithRelations()->where('products.is_active', 1)->orderBy('products.sold', 'DESC')->findAll(8),
        ];

        return view('layout/marketplace_content', $data);
    }
}
