<?php
/**
 *  订单查询演示
 */
require('sdk/PayingCloud.php');
require('DemoConfig.php');

header("Content-type: text/html; charset=utf-8");

$payingcloud = new PayingCloud(DemoConfig::ACCESS_KEY_ID, DemoConfig::ACCESS_KEY_SECRET);

$chargeNo = '6d6bc55a3b850adaa67595ee9c50d4a5';  //商户订单号

$r = $payingcloud->ShowCharge($chargeNo);

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