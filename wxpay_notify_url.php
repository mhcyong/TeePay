<?php
/*
 * 付费阅读异步回调
 */
include '../../../config.inc.php';
require_once 'libs/payjs.php';
date_default_timezone_set('Asia/Shanghai');

$db = Typecho_Db::get();
$options = Typecho_Widget::widget('Widget_Options');
$option=$options->plugin('TeePay');

$data = $_POST;
if($data['return_code'] == 1){
	$payjs = new Payjs("","",$option->payjs_wxpay_key,"");
	$sign_verify = $data['sign'];
	unset($data['sign']);
	if($payjs->sign($data) == $sign_verify&&$data['total_fee']==$data['attach']*100){
		$updateItem = $db->update('table.teepay_fees')->rows(array('feestatus'=>1))->where('feeid=?',$data["out_trade_no"]);
		$updateItemRows= $db->query($updateItem);		
		echo 'success';exit();	
	}else{		
		$db = Typecho_Db::get();
		$updateItem = $db->update('table.teepay_fees')->rows(array('feestatus'=>2))->where('feeid=?',$_POST['out_trade_no']);
		$updateItemRows= $db->query($updateItem);
		echo 'fail';exit();
	}
}

?>