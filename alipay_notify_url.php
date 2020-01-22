<?php
/*
 * 付费阅读异步回调
 */
include '../../../config.inc.php';
require_once 'libs/alipaynotify.php';
date_default_timezone_set('Asia/Shanghai');

$options = Typecho_Widget::widget('Widget_Options');
$option=$options->plugin('TeePay');
	
header('Content-type:text/html; Charset=utf-8');
//支付宝公钥，账户中心->密钥管理->开放平台密钥，找到添加了支付功能的应用，根据你的加密类型，查看支付宝公钥
$alipayPublicKey = $option->alipay_public_key;
$aliPay = new AlipayService($alipayPublicKey);
//验证签名
$result = $aliPay->rsaCheck($_POST,$_POST['sign_type']);
if($result===true){
	if($_POST['trade_status'] == 'TRADE_SUCCESS'){
		//处理你的逻辑，例如获取订单号$_POST['out_trade_no']，订单金额$_POST['total_amount']等
		$db = Typecho_Db::get();
		$updateItem = $db->update('table.teepay_fees')->rows(array('feestatus'=>1))->where('feeid=?',$_POST['out_trade_no']);
		$updateItemRows= $db->query($updateItem);		
		echo 'success';exit();		
	}elseif($_POST['trade_status'] == 'TRADE_CLOSED'){		
		$db = Typecho_Db::get();
		$updateItem = $db->update('table.teepay_fees')->rows(array('feestatus'=>2))->where('feeid=?',$_POST['out_trade_no']);
		$updateItemRows= $db->query($updateItem);
		echo 'fail';exit();
	}
}
echo 'error';exit();

?>

