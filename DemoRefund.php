<?php
/**
 *  退款演示
 */
require('sdk/PayingCloud.php');
require('DemoConfig.php');

header("Content-type: text/html; charset=utf-8");

$payingcloud = new PayingCloud(DemoConfig::ACCESS_KEY_ID, DemoConfig::ACCESS_KEY_SECRET);

$chargeNo = '6d6bc55a3b850adaa67595ee9c50d4a5';
$refundNo = DemoConfig::GetUniqueId();
$amount = '1';
$remark = '备注';
$metadata = '元数据';
$notifyUrl = 'http://localhost:8080/order/charge/success';

$r = $payingcloud->Refund($chargeNo, $refundNo, $amount, $notifyUrl, $remark, $metadata);

print_r($r);
?>          