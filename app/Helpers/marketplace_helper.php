<?php

/**
 * Helper Marketplace
 * Fungsi-fungsi umum yang digunakan di seluruh aplikasi
 */

if (!function_exists('asset_url')) {
    /**
     * Generate URL untuk asset (CSS/JS) dengan cache-buster otomatis
     * berdasarkan file modification time, sehingga browser auto reload
     * saat file diubah tanpa perlu hard refresh.
     *
     * @param string $path Path relatif dari folder public/, e.g. "asset/css/style.css"
     * @return string Full URL dengan ?v=timestamp
     */
    function asset_url($path)
    {
        $fullPath = FCPATH . $path;
        $version = file_exists($fullPath) ? filemtime($fullPath) : time();
        return base_url($path) . '?v=' . $version;
    }
}

if (!function_exists('format_rupiah')) {
    /**
     * Format angka ke format Rupiah
     */
    function format_rupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('generate_slug')) {
    /**
     * Generate slug dari string
     */
    function generate_slug($string)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
        return $slug;
    }
}

if (!function_exists('get_user')) {
    /**
     * Ambil data user yang sedang login dari session
     */
    function get_user()
    {
        $session = session();
        if (!$session->get('user_id')) {
            return null;
        }
        return [
            'id'    => $session->get('user_id'),
            'name'  => $session->get('user_name'),
            'email' => $session->get('user_email'),
            'role'  => $session->get('role'),
            'photo' => $session->get('user_photo'),
        ];
    }
}

if (!function_exists('is_logged_in')) {
    /**
     * Cek apakah user sudah login
     */
    function is_logged_in()
    {
        return (bool) session()->get('user_id');
    }
}

if (!function_exists('user_role')) {
    /**
     * Ambil role user yang sedang login
     */
    function user_role()
    {
        return session()->get('role') ?? 'guest';
    }
}

if (!function_exists('is_seller')) {
    /**
     * Cek apakah user adalah seller atau admin
     */
    function is_seller()
    {
        return in_array(user_role(), ['seller', 'admin']);
    }
}

if (!function_exists('is_admin')) {
    /**
     * Cek apakah user adalah admin
     */
    function is_admin()
    {
        return user_role() === 'admin';
    }
}

if (!function_exists('upload_image')) {
    /**
     * Upload gambar dengan validasi
     * @return string|null path file atau null jika gagal
     */
    function upload_image($file, string $folder = 'uploads')
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        // Validasi tipe dan ukuran
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize = 2048; // 2MB

        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return null;
        }

        if ($file->getSize('kb') > $maxSize) {
            return null;
        }

        $newName = $file->getRandomName();
        $file->move(FCPATH . $folder, $newName);

        return $folder . '/' . $newName;
    }
}

if (!function_exists('delete_image')) {
    /**
     * Hapus file gambar
     */
    function delete_image(string $path)
    {
        $fullPath = FCPATH . $path;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}

if (!function_exists('json_response')) {
    /**
     * Return JSON response standar
     */
    function json_response(bool $status, string $message = '', $data = null, int $code = 200)
    {
        $response = [
            'status'  => $status,
            'message' => $message,
        ];
        if ($data !== null) {
            $response['data'] = $data;
        }
        return json_encode($response);
    }
}

if (!function_exists('get_cart_count')) {
    /**
     * Ambil jumlah item di cart user
     */
    function get_cart_count()
    {
        if (!is_logged_in()) return 0;

        $cartModel = model('CartModel');
        $cartItemModel = model('CartItemModel');

        $cart = $cartModel->where('user_id', session()->get('user_id'))->first();
        if (!$cart) return 0;

        return (int) $cartItemModel->countItems($cart['id']);
    }
}
