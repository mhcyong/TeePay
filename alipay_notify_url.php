<?php
/*
 * 付费阅读异步回调
 */
include '../../../config.inc.php';
require_once 'libs/alipaynotify.php';
date_default_timezone_set('Asia/Shanghai');

$options = Typecho_Widget::widget('Widget_Options');
$option=$options->plugin('TePay');
	
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
		$updateItem = $db->update('table.tepay_fees')->rows(array('feestatus'=>1))->where('feeid=?',$_POST['out_trade_no']);
		$updateItemRows= $db->query($updateItem);
		
		$queryItem= $db->select()->from('table.tepay_fees')->where('feeid = ?', $_POST['out_trade_no']); 
		$rowItem = $db->fetchRow($queryItem);
		if($rowItem['feestatus']==1){
			$queryContents= $db->select()->from('table.contents')->where('cid = ?', $rowItem['feecid']); 
			$rowContents = $db->fetchRow($queryContents);
			$queryUser= $db->select()->from('table.users')->where('uid = ?', $rowContents['authorId']); 
			$rowUser = $db->fetchRow($queryUser);
			$updateUser = $db->update('table.users')->rows(array('tepay_money'=>$rowUser['tepay_money']+$rowItem['feeprice']))->where('uid=?',$rowContents['authorId']);
			$updateUserRows= $db->query($updateUser);
		}    //程序执行完后必须打印输出“success”（不包含引号）。如果商户反馈给支付宝的字符不是success这7个字符，支付宝服务器会不断重发通知，直到超过24小时22分钟。一般情况下，25小时以内完成8次通知（通知的间隔频率一般是：4m,10m,10m,1h,2h,6h,15h）；
		echo 'success';exit();		
	}elseif($_POST['trade_status'] == 'TRADE_CLOSED'){		
		$db = Typecho_Db::get();
		$updateItem = $db->update('table.tepay_fees')->rows(array('feestatus'=>2))->where('feeid=?',$_POST['out_trade_no']);
		$updateItemRows= $db->query($updateItem);
		echo 'fail';exit();
	}
}
echo 'error';exit();

?>

