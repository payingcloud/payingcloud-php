<?php
/**
 *  基础方法
 */
require('ApiConst.php');

/**
 * 网络请求
 * @access public
 * @param string $resource 参数拼接串
 * @param string $Authorization 签名
 * @param string $CA HTTPS时是否进行严格认证
 * @return string 返回请求数据.
 */
function Request($resource, $body, $way = 'POST')
{
    $url = ApiConst::DOMAIN_URL . $resource;    //请求地址
    $timestamp = gmdate('D, d M Y H:i:s T');    //表示时间戳表示此次操作的时间，且必须为GMT格式

    if (!empty($body)) {
        //php json_encode会对汉字自动转码 需处理掉
        $content = DecodeUnicode(str_replace("\\/", "/", json_encode($body)));
    } else {
        $content = $body;
    }

    //获取签名
    $Authorization = Sign($resource, $content, $timestamp, $way);

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

/////////////
// 加密
/////////////
function HmacSha1Hex($str, $key)
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

/**
 * 获取签名。
 * @access public
 * @param string $resource 参数拼接串
 * @param string $body 表示请求体的json完整json内容
 * @param string $timestamp 表示时间戳表示此次操作的时间，且必须为GMT格式
 * @param string $verb 表示HTTP 请求的Method，主要有PUT，GET，POST，HEAD，DELETE等
 * @return string 返回签名.
 */
function Sign($resource, $body, $timestamp, $verb = 'POST')
{
    $str = $verb . "\n" . $resource . "\n" . $body . "\n" . $timestamp . "\n";

    $sign = HmacSha1Hex($str, ApiConst::ACCESS_KEY_SECRET);

    $base64 = ApiConst::ACCESS_KEY_ID . ':' . $sign;

    $Authorization = "Basic " . base64_encode($base64);

    return ($Authorization);
}

/**
 * 生成32位唯一编码.
 * @return string 32位唯一编码.
 */
function GetUniqueId()
{
    return (md5(uniqid(rand())));
}

////json 不转码
function DecodeUnicode($str)
{
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
        create_function(
            '$matches',
            'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
        ),
        $str);
}

?>