<?php

class TencentSmsService {
    private $appId;
    private $appKey;
    private $sign;
    private $templateId;

    public function __construct($settings) {
        // 旧版接口只需要 SDKAppID 和 AppKey
        $this->appId = $settings['smsSdkAppId'] ?? '';
        $this->appKey = $settings['smsSecretKey'] ?? ''; // 复用数据库字段，这里存的是 AppKey
        $this->sign = $settings['smsSignName'] ?? '';
        $this->templateId = $settings['smsTemplateRegister'] ?? '';
    }

    public function sendVerificationCode($phoneNumber, $code, $templateId = null) {
        if (empty($this->appId) || empty($this->appKey)) {
            error_log("Tencent SMS (Legacy): Missing AppID or AppKey");
            return ['status' => false, 'message' => 'SMS configuration missing (AppID/AppKey)'];
        }

        $useTemplateId = $templateId ?: $this->templateId;
        if (empty($useTemplateId)) {
             return ['status' => false, 'message' => 'SMS template ID not configured'];
        }

        // 处理手机号，提取国家码
        $nationCode = '86';
        $mobile = $phoneNumber;
        if (strpos($phoneNumber, '+') === 0) {
            // 简单处理 +86138... 格式
            $nationCode = substr($phoneNumber, 1, 2);
            $mobile = substr($phoneNumber, 3);
        } else if (strlen($phoneNumber) === 11 && $phoneNumber[0] === '1') {
            // 默认为中国手机号
            $nationCode = '86';
            $mobile = $phoneNumber;
        }

        // 准备参数
        $random = rand(100000, 999999);
        $time = time();
        
        // 计算签名 (Legacy: sha256("appkey={appkey}&random={random}&time={time}&mobile={mobile}"))
        $sigString = "appkey={$this->appKey}&random={$random}&time={$time}&mobile={$mobile}";
        $sig = hash("sha256", $sigString);

        // 构造请求包体
        $data = [
            "ext" => "",
            "extend" => "",
            "params" => [(string)$code], // 假设模板只有一个参数 {1}
            "sig" => $sig,
            "sign" => $this->sign,
            "tel" => [
                "mobile" => $mobile,
                "nationcode" => $nationCode
            ],
            "time" => $time,
            "tpl_id" => (int)$useTemplateId
        ];

        // 发送请求
        $url = "https://yun.tim.qq.com/v5/tlssmssvr/sendsms?sdkappid={$this->appId}&random={$random}";
        
        return $this->sendRequest($url, $data);
    }

    private function sendRequest($url, $data) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        
        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return ['status' => false, 'message' => 'CURL Error: ' . $error];
        }

        $result = json_decode($response, true);
        
        if (isset($result['result']) && $result['result'] === 0) {
            return ['status' => true, 'message' => 'Success'];
        } else {
            $errMsg = $result['errmsg'] ?? 'Unknown error';
            return ['status' => false, 'message' => "Tencent API Error ({$result['result']}): $errMsg"];
        }
    }
}
