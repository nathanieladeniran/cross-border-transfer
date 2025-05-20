<?php

namespace Payins\WalletPayins\Poli;

class BasePoli
{
    private $config;
    public $country;
    public $currency;

    public function __construct()
    {
        $this->config = config('poli');
    }

    public function initiateTransaction($amount, $transactionRef)
    {
        $payloadToPoliArr = [
            "Amount" => $amount,
            "CurrencyCode" => $this->currency,
            "MerchantReference" => $transactionRef,
            "MerchantHomepageURL" => $this->config['MerchantHomepageURL'],
            "SuccessURL" => $this->config['SuccessURL'],
            "FailureURL" => $this->config['FailureURL'],
            "CancellationURL" => $this->config['CancellationURL'],
            "NotificationURL" => route('api.nudge.checkout.webhook', $this->country), //Later call
        ];

        $json_builder = json_encode($payloadToPoliArr);
        $url = "v2/Transaction/Initiate";
        return $this->callPoliTerminal($json_builder, $url, true);

    }

    public function getTransaction($value)
    {
        $json_builder = json_encode([]);

        $url = 'v2/Transaction/GetTransaction?token=' . $value;

        return $this->callPoliTerminal($json_builder, $url);
    }

    private function callPoliTerminal($json_builder, $url, $post = false)
    {
        $mercode = $this->config[$this->country]['merchant_code'];
        $authcode = $this->config[$this->country]['authentication_code'];
        $auth = base64_encode("{$mercode}:{$authcode}");
        $header = array();
        $header[] = 'Content-Type: application/json';
        $header[] = 'Authorization: Basic ' . $auth;
        $ch = curl_init("https://poliapi.apac.paywithpoli.com/api/" . $url);
//See the cURL documentation for more information: http://curl.haxx.se/docs/sslcerts.html
//We recommend using this bundle: https://raw.githubusercontent.com/bagder/ca-bundle/master/ca-bundle.crt
        curl_setopt($ch, CURLOPT_CAINFO, app_path("ca-bundle.crt"));
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_builder);
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $data;
    }

}
