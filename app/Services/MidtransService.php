<?php

namespace App\Services;

/**
 * Service layer untuk integrasi Midtrans Snap API.
 * Menggunakan cURL native (tanpa library eksternal).
 */
class MidtransService
{
    protected $serverKey;
    protected $clientKey;
    protected $isProduction;
    protected $snapBaseUrl;

    public function __construct()
    {
        $this->serverKey    = env('MIDTRANS_SERVER_KEY', '');
        $this->clientKey    = env('MIDTRANS_CLIENT_KEY', '');
        $this->isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);

        $this->snapBaseUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Generate Snap Token dari Midtrans API.
     *
     * @param array $params Parameter transaksi (transaction_details, customer_details, enabled_payments, item_details, dll)
     * @return array|null ['token' => string, 'redirect_url' => string] atau null jika gagal
     */
    public function createSnapToken(array $params): ?array
    {
        $authString = base64_encode($this->serverKey . ':');

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . $authString,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $this->snapBaseUrl,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($params),
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            log_message('error', 'Midtrans cURL error: ' . $error);
            return null;
        }

        $result = json_decode($response, true);

        if ($httpCode !== 201 || !isset($result['token'])) {
            $errorMsg = $result['error_messages'] ?? ($result['status_message'] ?? 'Unknown error');
            log_message('error', 'Midtrans Snap API error [' . $httpCode . ']: ' . json_encode($errorMsg));
            return null;
        }

        return [
            'token'        => $result['token'],
            'redirect_url' => $result['redirect_url'],
        ];
    }

    /**
     * Validasi signature key dari notifikasi Midtrans.
     * Signature = SHA512(order_id + status_code + gross_amount + server_key)
     *
     * @param string $orderId
     * @param string $statusCode
     * @param string $grossAmount
     * @param string $signatureKey
     * @return bool
     */
    public function validateSignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
        return hash_equals($expected, $signatureKey);
    }

    /**
     * Ambil URL snap.js berdasarkan environment.
     */
    public function getSnapUrl(): string
    {
        return $this->isProduction
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /**
     * Ambil Client Key (untuk frontend).
     */
    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    /**
     * Cek transaksi status langsung ke Midtrans API.
     *
     * @param string $orderId
     * @return array|null
     */
    public function getTransactionStatus(string $orderId): ?array
    {
        $url = $this->isProduction
            ? 'https://api.midtrans.com/v2/' . urlencode($orderId) . '/status'
            : 'https://api.sandbox.midtrans.com/v2/' . urlencode($orderId) . '/status';

        $authString = base64_encode($this->serverKey . ':');

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
                'Authorization: Basic ' . $authString,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            log_message('error', 'Midtrans status check error: ' . $error);
            return null;
        }

        return json_decode($response, true);
    }
}
