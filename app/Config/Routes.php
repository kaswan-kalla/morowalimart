<?php

namespace Config;

use CodeIgniter\Config\Routes as BaseRoutes;

$routes = Services::routes();

if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * ============================================================
 * ROUTE DEFINITIONS
 * ============================================================
 */

// === SURVEY (dinonaktifkan karena sudah selesai) ===
// $routes->get('survey', 'Survey::index');
// $routes->post('survey/submit', 'Survey::submit');
$routes->get('home', 'Home::index');

// === PUBLIC ROUTES ===
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::loginProcess');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::registerProcess');
$routes->get('logout', 'Auth::logout');
$routes->get('forgot-password', 'Auth::forgotPassword');
$routes->post('forgot-password', 'Auth::forgotPasswordProcess');
$routes->get('reset-password/(:segment)', 'Auth::resetPassword/$1');
$routes->post('reset-password', 'Auth::resetPasswordProcess');

// === PRODUCT PUBLIC ===
$routes->get('produk', 'Product::index');
$routes->get('produk/(:segment)', 'Product::detail/$1');
$routes->get('search', 'Product::search');
$routes->post('search/ajax', 'Product::searchAjax');
$routes->get('kategori/(:segment)', 'Product::byCategory/$1');
$routes->get('toko/(:segment)', 'Store::publicProfile/$1');

// === WISHLIST (toggle via AJAX, perlu login) ===
$routes->group('wishlist', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Wishlist::index');
    $routes->post('toggle', 'Wishlist::toggle');
    $routes->get('list', 'Wishlist::getList');
});

// === CART (perlu login) ===
$routes->group('cart', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Cart::index');
    $routes->post('add', 'Cart::add');
    $routes->post('update', 'Cart::updateQty');
    $routes->post('remove', 'Cart::remove');
    $routes->get('summary', 'Cart::summary');
});

// === CHECKOUT (perlu login) ===
$routes->group('checkout', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Checkout::index');
    $routes->post('process', 'Checkout::process');
    $routes->post('apply-voucher', 'Checkout::applyVoucher');
});

// === ORDER (perlu login) ===
$routes->group('order', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Order::index');
    $routes->get('(:num)', 'Order::detail/$1');
    $routes->post('cancel', 'Order::cancel');
    $routes->post('complete', 'Order::complete');
    $routes->post('reorder', 'Order::reorder');
});

// === PAYMENT (perlu login) ===
$routes->group('payment', ['filter' => 'auth'], function ($routes) {
    $routes->get('(:num)', 'Payment::index/$1');
    $routes->post('upload', 'Payment::upload');
});

// === MIDTRANS WEBHOOK (tanpa auth filter, tanpa CSRF) ===
$routes->post('payment/notification', 'Payment::notification');

// === REVIEW (perlu login) ===
$routes->group('review', ['filter' => 'auth'], function ($routes) {
    $routes->post('submit', 'Review::submit');
    $routes->get('product/(:num)', 'Review::getByProduct/$1');
});

// === ADDRESS (perlu login) ===
$routes->group('address', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Address::index');
    $routes->get('get/(:num)', 'Address::get/$1');
    $routes->post('save', 'Address::save');
    $routes->post('update/(:num)', 'Address::update/$1');
    $routes->post('delete/(:num)', 'Address::delete/$1');
    $routes->post('set-default/(:num)', 'Address::setDefault/$1');
});

// === PROFILE (perlu login) ===
$routes->group('profile', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Profile::index');
    $routes->post('update', 'Profile::update');
    $routes->post('upload-photo', 'Profile::updatePhoto');
    $routes->post('change-password', 'Profile::changePassword');
});

// === STORE / SELLER DASHBOARD (perlu login + role seller) ===
$routes->group('seller', ['filter' => 'seller'], function ($routes) {
    $routes->get('dashboard', 'Seller\Dashboard::index');
    $routes->get('sales-chart', 'Seller\Dashboard::salesChart');

    // Produk seller (English + Indonesian aliases)
    $routes->get('products', 'Seller\Product::index');
    $routes->get('produk', 'Seller\Product::index');
    $routes->get('products/create', 'Seller\Product::create');
    $routes->get('produk/tambah', 'Seller\Product::create');
    $routes->post('products/store', 'Seller\Product::save');
    $routes->post('produk/save', 'Seller\Product::save');
    $routes->get('products/edit/(:num)', 'Seller\Product::edit/$1');
    $routes->get('produk/edit/(:num)', 'Seller\Product::edit/$1');
    $routes->post('products/update', 'Seller\Product::update');
    $routes->post('produk/update/(:num)', 'Seller\Product::update/$1');
    $routes->post('products/delete', 'Seller\Product::delete');
    $routes->post('produk/delete/(:num)', 'Seller\Product::delete/$1');
    $routes->post('products/set-main-image', 'Seller\Product::setMainImage');
    $routes->post('produk/set-main-image/(:num)', 'Seller\Product::setMainImage/$1');
    $routes->post('products/delete-image', 'Seller\Product::deleteImage');
    $routes->post('produk/delete-image/(:num)', 'Seller\Product::deleteImage/$1');

    // Pesanan seller (English + Indonesian aliases)
    $routes->get('orders', 'Seller\Order::index');
    $routes->get('pesanan', 'Seller\Order::index');
    $routes->get('orders/(:num)', 'Seller\Order::detail/$1');
    $routes->get('pesanan/detail/(:num)', 'Seller\Order::detail/$1');
    $routes->post('orders/process', 'Seller\Order::process');
    $routes->post('pesanan/process/(:num)', 'Seller\Order::process/$1');
    $routes->post('orders/ship', 'Seller\Order::ship');
    $routes->post('pesanan/ship/(:num)', 'Seller\Order::ship/$1');
    $routes->get('orders/get-courier-option', 'Seller\Order::getCourierOption');

    // Toko
    $routes->get('store', 'Seller\Store::index');
    $routes->get('toko', 'Seller\Store::index');
    $routes->post('store/create', 'Seller\Store::create');
    $routes->post('store/update', 'Seller\Store::update');
    $routes->post('toko/update', 'Seller\Store::update');
});

// === ADMIN DASHBOARD ===
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');

    // Users
    $routes->get('users', 'Admin\User::index');
    $routes->get('users/data', 'Admin\User::data');
    $routes->post('users/update-role', 'Admin\User::updateRole');
    $routes->post('users/toggle-status', 'Admin\User::toggleStatus');

    // Stores
    $routes->get('stores', 'Admin\Store::index');
    $routes->get('stores/data', 'Admin\Store::data');
    $routes->get('stores/get/(:num)', 'Admin\Store::get/$1');
    $routes->get('stores/get-users', 'Admin\Store::getUserOption');
    $routes->post('stores/store', 'Admin\Store::store');
    $routes->post('stores/update/(:num)', 'Admin\Store::update/$1');
    $routes->post('stores/toggle', 'Admin\Store::toggle');
    $routes->post('stores/delete', 'Admin\Store::delete');

    // Products
    $routes->get('products', 'Admin\Product::index');
    $routes->get('products/data', 'Admin\Product::data');
    $routes->get('products/get/(:num)', 'Admin\Product::get/$1');
    $routes->get('products/get-categories', 'Admin\Product::getCategoryOption');
    $routes->get('products/get-stores', 'Admin\Product::getStoreOption');
    $routes->post('products/store', 'Admin\Product::store');
    $routes->post('products/update/(:num)', 'Admin\Product::update/$1');
    $routes->post('products/toggle', 'Admin\Product::toggle');
    $routes->post('products/delete', 'Admin\Product::delete');

    // Categories
    $routes->get('categories', 'Admin\Category::index');
    $routes->get('categories/data', 'Admin\Category::data');
    $routes->get('categories/get/(:num)', 'Admin\Category::get/$1');
    $routes->post('categories/store', 'Admin\Category::save');
    $routes->post('categories/update/(:num)', 'Admin\Category::update/$1');
    $routes->post('categories/delete/(:num)', 'Admin\Category::delete/$1');

    // Payments verification
    $routes->get('payments', 'Admin\Payment::index');
    $routes->get('payments/data', 'Admin\Payment::data');
    $routes->post('payments/verify', 'Admin\Payment::verify');

    // Reports
    $routes->get('reports/sales', 'Admin\Report::sales');
    $routes->get('reports/sales-data', 'Admin\Report::salesData');
    $routes->get('reports/transactions', 'Admin\Report::transactions');
    $routes->get('reports/transaction-data', 'Admin\Report::transactionData');

    // Vouchers
    $routes->get('vouchers', 'Admin\Voucher::index');
    $routes->get('vouchers/data', 'Admin\Voucher::data');
    $routes->get('vouchers/get/(:num)', 'Admin\Voucher::get/$1');
    $routes->post('vouchers/store', 'Admin\Voucher::save');
    $routes->post('vouchers/update/(:num)', 'Admin\Voucher::update/$1');
    $routes->post('vouchers/delete/(:num)', 'Admin\Voucher::delete/$1');

    // Survey
    $routes->get('survey', 'Admin\Survey::index');
    $routes->get('survey/data', 'Admin\Survey::data');
});

// === COURIER DASHBOARD ===
$routes->group('courier', ['filter' => 'courier'], function ($routes) {
    $routes->get('/', 'Courier::index');
    $routes->get('(:num)', 'Courier::detail/$1');
    $routes->post('complete', 'Courier::complete');
});

/*
 * ============================================================
 * ADDITIONAL ROUTING
 * ============================================================
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
