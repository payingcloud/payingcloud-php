# PayingCloud PHP SDK 使用说明

## 引入

在需要使用本SDK的页面引入：
  
  ```php
  require('sdk/PayingCloud.php');
  ```

## 付款

- 调用函数

  ```php
  Charge($chargeNo, $subject, $amount, $channel, $remark, $extra, $metadata, $notifyUrl)
  ```

- 请求参数

  * $chargeNo：[必填] 商户收款单号（商户系统内部订单号，要求8到32个字符、且在同一个应用下唯一，只能包含字母和数字）
  * $subject：[必填] 商品名
  * $amount：[必填] 收款金额（订单总金额，单位为分，不能小于1）
  * $channel：[必填] 收款渠道（具体渠道类型详见渠道类型表 https://payingcloud.github.io/payingcloud-api-doc/#渠道额外参数）
  * $remark：[选填] 备注
  * $extra：[选填] 渠道额外参数（用键值对的map存储不同渠道之间的渠道额外参数 https://payingcloud.github.io/payingcloud-api-doc/#渠道额外参数）
  * $metadata：[选填] 元数据（用于携带自定义数据，原样返回）
  * $notifyUrl：[选填] 异步通知地址（支付成功后返回支付结果地址，必须为公网地址，如不填将发送到在控制台配置的Webhooks地址，如也没配置Webhooks地址的话将不发送通知）

- 返回参数
  
  请参考 https://payingcloud.github.io/payingcloud-api-doc/#_同步返回参数

## 退款

- 调用函数

  ```php
  Refund($chargeNo, $refundNo, $amount, $notifyUrl, $remark, $metadata)
  ```

- 请求参数
  
  * $chargeNo：[必填] 商户收款单号（支付时订单号）
  * $refundNo：[必填] 退款单号（商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔）
  * $amount：[必填] 退款金额（退款总金额，订单总金额，单位为分，只能为整数）
  * $notifyUrl：[选填] 异步通知地址（支付成功后返回支付结果地址，必须为公网地址，如不填将发送到在控制台配置的Webhooks地址，如也没配置Webhooks地址的话将不发送通知） 非必填
  * $remark：[选填] 备注
  * $metadata：[选填] 元数据（用于携带自定义数据，原样返回）

- 返回参数
  
  请参考 https://payingcloud.github.io/payingcloud-api-doc/#_返回结果
	
## 订单查询

- 调用函数

  ```php
  ShowCharge($chargeNo)
  ```

- 请求参数
	
  * $chargeNo：[必填] 商户订单号

- 返回参数
  
  请参考 https://payingcloud.github.io/payingcloud-api-doc/#_返回参数
	
## 退款查询

- 调用函数

  ```php
  ShowRefund($refundNo)
  ```

- 请求参数

  * $refundNo：[必填] 商户退款号

- 返回参数
  
  请参考 https://payingcloud.github.io/payingcloud-api-doc/#_返回参数_2
	