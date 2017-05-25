<?php
/**
 *  退款单查询演示
 */
require('sdk/PayingCloud.php');

$refundNo = '16a6ccba702d2aca9120a4686c5a6e1b';  //商户退款号

$r = ShowRefund($refundNo);
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