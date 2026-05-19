<?php
// src/Services/WechatPayService.php — WeChat Pay V3 integration

class WechatPayService {
    private $appid;
    private $mchid;
    private $apiv3Key;
    private $serialNo;
    private $privateKeyPath;
    private $notifyUrl;

    public function __construct() {
        $settings = Settings::getAll();
        $this->appid = $settings['wx_appid'] ?? '';
        $this->mchid = $settings['wx_mchid'] ?? '';
        $this->apiv3Key = $settings['wx_apiv3_key'] ?? '';
        $this->serialNo = $settings['wx_serial_no'] ?? '';
        $this->privateKeyPath = $settings['wx_key_path'] ?? '';
        $this->notifyUrl = ($settings['siteUrl'] ?? 'https://xaicms.com') . '/api/payment/wechat-notify';
    }

    public function isConfigured(): bool {
        return !empty($this->appid) && !empty($this->mchid) && !empty($this->apiv3Key);
    }

    /**
     * Create Native payment QR code order
     */
    public function createNativeOrder(string $outTradeNo, string $description, int $amountCents): ?array {
        if (!$this->isConfigured()) return null;

        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/native';
        $body = json_encode([
            'appid' => $this->appid,
            'mchid' => $this->mchid,
            'description' => $description,
            'out_trade_no' => $outTradeNo,
            'notify_url' => $this->notifyUrl,
            'amount' => [
                'total' => $amountCents,
                'currency' => 'CNY',
            ],
        ]);

        $result = $this->request('POST', $url, $body);
        return $result ? json_decode($result, true) : null;
    }

    /**
     * Verify webhook signature
     */
    public function verifySignature(string $body, string $signature, string $timestamp, string $nonce): bool {
        if (empty($this->apiv3Key)) return false;
        $message = "$timestamp\n$nonce\n$body\n";
        $expected = base64_encode(hash_hmac('sha256', $message, $this->apiv3Key, true));
        return hash_equals($expected, $signature);
    }

    /**
     * Decrypt notify resource
     */
    public function decryptResource(string $associatedData, string $nonce, string $ciphertext): ?string {
        if (empty($this->apiv3Key)) return null;
        $decrypted = openssl_decrypt(
            base64_decode($ciphertext),
            'aes-256-gcm',
            $this->apiv3Key,
            OPENSSL_RAW_DATA,
            base64_decode($nonce),
            base64_decode($associatedData)
        );
        return $decrypted ?: null;
    }

    private function request(string $method, string $url, string $body = ''): ?string {
        $ch = curl_init($url);
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: xAI-CMS/1.0',
        ];

        // Add WxPay authorization header
        $authHeader = $this->buildAuthHeader($method, $url, $body);
        if ($authHeader) $headers[] = $authHeader;

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return $response;
        }
        return null;
    }

    /**
     * Build WeChat Pay V3 Authorization header
     */
    private function buildAuthHeader(string $method, string $url, string $body): string {
        if (empty($this->mchid) || empty($this->serialNo) || empty($this->privateKeyPath)) {
            return '';
        }

        $parsed = parse_url($url);
        $path = $parsed['path'] . (isset($parsed['query']) ? '?' . $parsed['query'] : '');
        $timestamp = (string)time();
        $nonce = bin2hex(random_bytes(16));
        $message = "$method\n$path\n$timestamp\n$nonce\n$body\n";

        $privateKey = file_get_contents($this->privateKeyPath);
        if (!$privateKey) return '';

        openssl_sign($message, $signature, $privateKey, 'sha256WithRSAEncryption');
        $sign = base64_encode($signature);

        $token = sprintf(
            'mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $this->mchid, $nonce, $timestamp, $this->serialNo, $sign
        );

        return "Authorization: WECHATPAY2-SHA256-RSA2048 $token";
    }
}
