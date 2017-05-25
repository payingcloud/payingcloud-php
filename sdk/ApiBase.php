<?php
/**
 *  ��������
 */
require('ApiConst.php');

/**
 * ��������
 * @access public
 * @param string $resource ����ƴ�Ӵ�
 * @param string $Authorization ǩ��
 * @param string $CA HTTPSʱ�Ƿ�����ϸ���֤
 * @return string ������������.
 */
function Request($resource, $body, $way = 'POST')
{
    $url = ApiConst::DOMAIN_URL . $resource;    //�����ַ
    $timestamp = gmdate('D, d M Y H:i:s T');    //��ʾʱ�����ʾ�˴β�����ʱ�䣬�ұ���ΪGMT��ʽ

    if (!empty($body)) {
        //php json_encode��Ժ����Զ�ת�� �账���
        $content = DecodeUnicode(str_replace("\\/", "/", json_encode($body)));
    } else {
        $content = $body;
    }

    //��ȡǩ��
    $Authorization = Sign($resource, $content, $timestamp, $way);

    $header = array('Content-Type:application/json; charset=UTF-8',
        'Authorization:' . $Authorization,
        'Date:' . $timestamp);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ����֤����
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
// ����
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
 * ��ȡǩ����
 * @access public
 * @param string $resource ����ƴ�Ӵ�
 * @param string $body ��ʾ�������json����json����
 * @param string $timestamp ��ʾʱ�����ʾ�˴β�����ʱ�䣬�ұ���ΪGMT��ʽ
 * @param string $verb ��ʾHTTP �����Method����Ҫ��PUT��GET��POST��HEAD��DELETE��
 * @return string ����ǩ��.
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
 * ����32λΨһ����.
 * @return string 32λΨһ����.
 */
function GetUniqueId()
{
    return (md5(uniqid(rand())));
}

////json ��ת��
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