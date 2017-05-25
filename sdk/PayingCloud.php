<?php
/**
 *  sdk
 */
require('ApiBase.php');

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

    $result = Request($resource, $body);

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

    $result = Request($resource, $body);

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

    $body = '';

    $result = Request($resource, $body, 'GET');

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

    $body = '';

    $result = Request($resource, $body, 'GET');

    return ($result);
}

?>