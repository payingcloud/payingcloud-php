<?php
/**
 *  付款演示
 */
require('sdk/PayingCloud.php');
require('DemoConfig.php');

header("Content-type: text/html; charset=utf-8");

$payingcloud = new PayingCloud(DemoConfig::ACCESS_KEY_ID, DemoConfig::ACCESS_KEY_SECRET);

$amount = 1;
$metadata = '元数据';
$subject = '金牛座软件开发';
$extra = array('returnUrl' => 'http://localhost:8080/order/charge/return');
$channel = 'WXPAY_NATIVE';
$notifyUrl = 'http://localhost:8080/order/charge/success';
$remark = '备注';

$r = $payingcloud->Charge(DemoConfig::GetUniqueId(), $subject, $amount, $channel, $remark, $extra, $metadata, $notifyUrl);

print_r($r);

$error = $r['error'];
$errorDescription = json_decode($r['errorDescription'], true);
$errorInfo = $errorDescription['error'];
$errorDescriptionInfo = $errorDescription['errorDescription'];

echo $error;
echo '<br>';
echo $errorDescription;
echo '<br>';
echo $errorInfo;
echo '<br>';
echo $errorDescriptionInfo;
?>          