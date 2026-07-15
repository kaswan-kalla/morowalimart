<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Controller Autentikasi: Login, Register, Logout, Forgot/Reset Password
 */
class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Halaman Login
     */
    public function login()
    {
        if (is_logged_in()) return redirect()->to('home');
        return view('layout/marketplace_content', ['content' => 'auth', 'subview' => 'login', 'meta_title' => 'Login']);
    }

    /**
     * Proses Login (AJAX)
     */
    public function loginProcess()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/login');
        }

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => implode('<br>', $this->validator->getErrors())
            ]);
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Email atau password salah'
            ]);
        }

        if (!$user['is_active']) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Akun Anda telah dinonaktifkan'
            ]);
        }

        // Set session
        $sessionData = [
            'user_id'    => $user['id'],
            'user_name'  => $user['name'],
            'user_email' => $user['email'],
            'user_photo' => $user['photo'],
            'role'       => $user['role'],
        ];
        $this->session->set($sessionData);

        // Regenerate session ID untuk keamanan
        $this->session->regenerate();

        $redirect = $this->session->get('redirect_url') ?: base_url();
        $this->session->remove('redirect_url');

        return $this->response->setJSON([
            'status'   => true,
            'message'  => 'Login berhasil',
            'redirect' => $redirect
        ]);
    }

    /**
     * Halaman Register
     */
    public function register()
    {
        if (is_logged_in()) return redirect()->to('home');
        return view('layout/marketplace_content', ['content' => 'auth', 'subview' => 'register', 'meta_title' => 'Daftar Akun']);
    }

    /**
     * Proses Register (AJAX)
     */
    public function registerProcess()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/register');
        }

        $rules = [
            'name'             => 'required|min_length[3]|max_length[100]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'email' => ['is_unique' => 'Email sudah terdaftar'],
            'password_confirm' => ['matches' => 'Konfirmasi password tidak cocok'],
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => implode('<br>', $this->validator->getErrors())
            ]);
        }

        $this->userModel->insert([
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'     => 'buyer',
        ]);

        return $this->response->setJSON([
            'status'   => true,
            'message'  => 'Registrasi berhasil! Silakan login.',
            'redirect' => base_url('login')
        ]);
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login');
    }

    /**
     * Halaman Lupa Password
     */
    public function forgotPassword()
    {
        return view('layout/marketplace_content', ['content' => 'auth', 'subview' => 'forgot_password', 'meta_title' => 'Lupa Password']);
    }

    /**
     * Proses Lupa Password (AJAX) - generate token
     */
    public function forgotPasswordProcess()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/forgot-password');
        }

        $email = $this->request->getPost('email');
        $user  = $this->userModel->findByEmail($email);

        // Selalu return success untuk keamanan (jangan bocorkan info email)
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $this->userModel->update($user['id'], [
                'reset_token'   => $token,
                'reset_expires' => date('Y-m-d H:i:s', strtotime('+1 hour')),
            ]);

            // Di production: kirim email dengan link reset
            // Link: base_url('reset-password/' . $token)
            log_message('info', "Reset token for {$email}: {$token}");
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Jika email terdaftar, link reset password telah dikirim'
        ]);
    }

    /**
     * Halaman Reset Password
     */
    public function resetPassword($token)
    {
        $user = $this->userModel->findByResetToken($token);
        if (!$user) {
            return redirect()->to('/forgot-password')->with('error', 'Token tidak valid atau sudah kadaluarsa');
        }

        return view('layout/marketplace_content', [
            'content' => 'auth',
            'subview'    => 'reset_password',
            'meta_title' => 'Reset Password',
            'token'      => $token
        ]);
    }

    /**
     * Proses Reset Password (AJAX)
     */
    public function resetPasswordProcess()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/forgot-password');
        }

        $token    = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByResetToken($token);
        if (!$user) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Token tidak valid'
            ]);
        }

        $this->userModel->update($user['id'], [
            'password'     => password_hash($password, PASSWORD_BCRYPT),
            'reset_token'   => null,
            'reset_expires' => null,
        ]);

        return $this->response->setJSON([
            'status'   => true,
            'message'  => 'Password berhasil direset! Silakan login.',
            'redirect' => base_url('login')
        ]);
    }
}
