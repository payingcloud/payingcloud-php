<?php
/**
 *  退款演示
 */
require('sdk/PayingCloud.php');

$chargeNo = '6d6bc55a3b850adaa67595ee9c50d4a5';
$refundNo = GetUniqueId();
$amount = '1';
$remark = '备注';
$metadata = '元数据';
$notifyUrl = 'http://localhost:8080/order/charge/success';

$r = Refund($chargeNo, $refundNo, $amount, $notifyUrl, $remark, $metadata);
print_r($r);
?>          