<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter untuk halaman kurir - cek role courier/admin
 */
class CourierFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $role = $session->get('role');
        if (!in_array($role, ['courier', 'admin'])) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses ke halaman kurir');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
