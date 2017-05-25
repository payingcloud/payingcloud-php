<?php

class PayingCloud
{
    const BASE_URL = 'https://api.payingcloud.cn';

    private $mAccessKeyId;
    private $mAccessKeySecret;

    /**
     * 构造函数.
     * @param string $accessKeyId accessKeyId
     * @param string $accessKeySecret 密钥
     * @access public
     */
    public function __construct($accessKeyId, $accessKeySecret)
    {
        $this->mAccessKeyId = $accessKeyId;
        $this->mAccessKeySecret = $accessKeySecret;
    }

    /**
     * 付款接口
     * @access public
     * @param string $chargeNo 商户收款单号
     * @param string $subject 商品名
     * @param int $amount 收款金额
     * @param string $channel 收款渠道
     * @param string $remark 备注
     * @param string $extra 渠道额外参数
     * @param string $metadata 元数据
     * @param string $notifyUrl 异步通知地址
     * @return string 返回请求数据.
     */
    function Charge($chargeNo, $subject, $amount, $channel, $remark = '', $extra = '', $metadata = '', $notifyUrl = '')
    {
        $resource = '/charges';

        $body = array(
            'chargeNo' => $chargeNo,
            'subject' => $subject,
            'amount' => $amount,
            'channel' => $channel,
            'remark' => $remark,
            'extra' => $extra,
            'metadata' => $metadata,
            'notifyUrl' => $notifyUrl
        );

        $result = self::Request($resource, $body);

        return ($result);
    }

    /**
     * 退款接口
     * @access public
     * @param string $chargeNo 商户收款单号
     * @param string $refundNo 退款单号
     * @param int $amount 收款金额
     * @param string $notifyUrl 异步通知地址
     * @param string $remark 备注
     * @param string $metadata 元数据
     * @return string 返回请求数据.
     */
    function Refund($chargeNo, $refundNo, $amount, $notifyUrl, $remark = '', $metadata = '')
    {
        $resource = '/refunds';

        $body = array(
            'chargeNo' => $chargeNo,
            'refundNo' => $refundNo,
            'amount' => $amount,    //退款金额
            'remark' => $remark,
            'metadata' => $metadata,
            'notifyUrl' => $notifyUrl,    //异步通知地址
        );

        $result = self::Request($resource, $body);

        return ($result);
    }

    /**
     * 订单查询
     * @access public
     * @param string $chargeNo 商户订单号
     * @return string 返回请求数据.
     */
    function ShowCharge($chargeNo)
    {
        $resource = '/charges/' . $chargeNo;

        $result = self::Request($resource, '', 'GET');

        return ($result);
    }

    /**
     * 退款查询
     * @access public
     * @param string $refundNo 商户退款号
     * @return string 返回请求数据.
     */
    function ShowRefund($refundNo)
    {
        $resource = '/refunds/' . $refundNo;

        $result = self::Request($resource, '', 'GET');

        return ($result);
    }

    /**
     * 网络请求
     * @access public
     * @param string $resource 参数拼接串
     * @param $body
     * @param string $way
     * @return string 返回请求数据.
     */
    protected function Request($resource, $body, $way = 'POST')
    {
        $url = self::BASE_URL . $resource;    //请求地址
        $timestamp = gmdate('D, d M Y H:i:s T');  //表示时间戳表示此次操作的时间，且必须为GMT格式

        if (!empty($body)) {
            //php json_encode会对汉字自动转码 需处理掉
            $content = self::DecodeUnicode(str_replace("\\/", "/", json_encode($body)));
        } else {
            $content = $body;
        }

        //获取签名
        $Authorization = self::Sign($resource, $content, $timestamp, $way);

        $header = array('Content-Type:application/json; charset=UTF-8',
            'Authorization:' . $Authorization,
            'Date:' . $timestamp);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if ($way == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        }

        $response = curl_exec($ch);
        if ($error = curl_error($ch)) {
            die($error);
        }

        curl_close($ch);
        return json_decode($response, true);
    }

    /**
     * 获取签名。
     * @access public
     * @param string $resource 参数拼接串
     * @param string $body 表示请求体的json完整json内容
     * @param string $timestamp 表示时间戳表示此次操作的时间，且必须为GMT格式
     * @param string $verb 表示HTTP 请求的Method，主要有PUT，GET，POST，HEAD，DELETE等
     * @return string 返回签名.
     */
    private function Sign($resource, $body, $timestamp, $verb = 'POST')
    {
        $str = $verb . "\n" . $resource . "\n" . $body . "\n" . $timestamp . "\n";

        $sign = self::HmacSha1Hex($str, $this->mAccessKeySecret);

        $base64 = $this->mAccessKeyId . ':' . $sign;

        $Authorization = "Basic " . base64_encode($base64);

        return ($Authorization);
    }

    private function HmacSha1Hex($str, $key)
    {
        $signature = "";
        if (function_exists('hash_hmac')) {
            $signature = hash_hmac("sha1", $str, $key, false);
        } else {
            $blocksize = 64;
            $hashfunc = 'sha1';
            if (strlen($key) > $blocksize) {
                $key = pack('H*', $hashfunc($key));
            }
            $key = str_pad($key, $blocksize, chr(0x00));
            $ipad = str_repeat(chr(0x36), $blocksize);
            $opad = str_repeat(chr(0x5c), $blocksize);
            $hmac = pack(
                'H*', $hashfunc(
                    ($key ^ $opad) . pack(
                        'H*', $hashfunc(
                            ($key ^ $ipad) . $str
                        )
                    )
                )
            );
            $signature = $hmac;
        }

        return ($signature);
    }

    ////json 不转码
    private function DecodeUnicode($str)
    {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
            create_function(
                '$matches',
                'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
            ),
            $str);
    }
}

?>