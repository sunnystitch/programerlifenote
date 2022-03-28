<?php
//Example:
//GET请求
// $url = 'http://149.129.215.154:8084/api/v1/internal/account/billday';
// $ak = 'order_main';
// $request_data = ['uid' => 1920910];
// $data = CommonSignService::curlData($url, $request_data, $ak, 'GET');
// var_dump($data);
// exit;


//POST示例
// $url = 'http://149.129.215.154:8084/api/v1/internal/account/amount';
// $ak = 'order_main';
// $request_data = [
//     'amount' => 800000,
//     'op' => 'op',
//     'product_id' => 202,
//     'uid' => 1920910,
// ];
// $data = CommonSignService::curlData($url, $request_data, $ak, 'POST', 'json');
// var_dump($data);
//exit;


/**
 * 统一签名校验类
 * Created by PhpStorm.
 * User: liuanyuan
 * Date: 2019-08-16
 * Time: 17:19
 * Version: 0.0.1
 */
class CommonSignService
{
    //请求返回的curl_no
    public static $last_curl_no;

    //请求返回的http_code
    public static $last_curl_http_code;

    //秘钥配置-根据调用方配置 ak => sk_env
    protected static $AK_SK_LIST = [
        'order' => 'INTERNAL_ADMIN_ORDER_SK', //后台调用订单
        'admin_order' => 'INTERNAL_ADMIN_MAIN_SK',   //后台调用主干
        'crm' => 'INTERNAL_ORDER_MAIN_SK',   //订单调用主干
        '' => 'INTERNAL_MAIN_ORDER_SK',   //主干调用订单
    ];

    protected static $CONTENT_TYPE = [
        'json' => 'Content-Type:application/json',
        'urlencode' => 'Content-Type:application/x-www-form-urlencoded'
    ];

    public static function init()
    {
        self::$last_curl_no = 0;
        self::$last_curl_http_code = 0;
    }

    /**
     * 签名加密
     * @param $req_data array 请求数据
     * @param $timestamp int  时间戳
     * @param string $ak string 对应ak值
     * @return string
     */
    public static function encryptSign($req_data, $timestamp, $ak = 'admin_order')
    {
        $sign_data = $req_data;
        if (is_array($req_data)) {
            ksort($req_data);
            $sign_data = http_build_query($req_data);
        }
        //$sk = '0l21&QMBPNs3xrLmArr@M6tD18SZ7topVFgBiL@0';
        //在配置文件里配置秘钥
        $sk = env(self::$AK_SK_LIST[$ak]);
        $sign = base64_encode(hash_hmac('sha256', $sign_data . $timestamp, $sk, true));
        return $sign;
    }

    /**
     * 统一内部curl请求
     * 带签名校验
     * @param $url string   请求地址
     * @param $data array   请求数据（传入数组）
     * @param $ak string    请求的ak
     * @param string $method 请求方法：POST,GET,DELETE,PUT 其他的再扩展
     * @param string $content_type 请求方法目前支持：urlencode 和 json
     * @param array $headers 请求头设置，如有需要设置传字符串数组
     * @param int $timeout_ms 请求超时时间：默认5000毫秒 = 5秒
     * @return bool|string
     */
    public static function curlData($url, $data, $ak, $method = 'GET', $content_type = 'urlencode', $headers = [], $timeout_ms = 5000)
    {
        if (!is_array($data)) {
            return false;
        }
        if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'])) {
            return false;
        }
        self::init();
        $ch = curl_init();
        $timestamp = time();
        $headers[] = 'ak:' . $ak;
        $headers[] = 'timestamp:' . $timestamp;
        empty($content_type) && $content_type = 'urlencode';
        if ($method == 'POST' || $method == 'DELETE' || $method == 'PUT') {
            switch ($content_type) {
                case 'json':
                    $sign_data = json_encode($data);
                    break;
                case 'urlencode':
                default:
                    ksort($data);
                    $sign_data = http_build_query($data);
                    break;
            }
            $headers[] = self::$CONTENT_TYPE[$content_type];
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $sign_data);
        } else if ($method == 'GET') {
            ksort($data);
            $sign_data = http_build_query($data);
            $url = $url . '?' . $sign_data;
        } else {
            return false;
        }
        $sign = self::encryptSign($sign_data, $timestamp, $ak);
        $headers[] = 'sign:' . $sign;
        if (!empty($timeout_ms)) {
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout_ms);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        self::$last_curl_no = curl_errno($ch);
        self::$last_curl_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $output;
    }
}

?>