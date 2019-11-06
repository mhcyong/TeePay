<?php
/*
 * 付费阅读异步回调
 */
include '../../../config.inc.php';
require_once 'libs/payjs.php';
date_default_timezone_set('Asia/Shanghai');

$db = Typecho_Db::get();
$options = Typecho_Widget::widget('Widget_Options');
$option=$options->plugin('TePay');

$data = $_POST;
if($data['return_code'] == 1){
	$payjs = new Payjs("","",$option->payjs_wxpay_key,"");
	$sign_verify = $data['sign'];
	unset($data['sign']);
	if($payjs->sign($data) == $sign_verify&&$data['total_fee']==$data['attach']*100){
		$updateItem = $db->update('table.tepay_fees')->rows(array('feestatus'=>1))->where('feeid=?',$data["out_trade_no"]);
		$updateItemRows= $db->query($updateItem);
		
		$queryItem= $db->select()->from('table.tepay_fees')->where('feeid = ?', $data["out_trade_no"]); 
		$rowItem = $db->fetchRow($queryItem);
		if($rowItem['feestatus']==1){
			$queryContents= $db->select()->from('table.contents')->where('cid = ?', $rowItem['feecid']); 
			$rowContents = $db->fetchRow($queryContents);
			$queryUser= $db->select()->from('table.users')->where('uid = ?', $rowContents['authorId']); 
			$rowUser = $db->fetchRow($queryUser);
			$updateUser = $db->update('table.users')->rows(array('tepay_money'=>$rowUser['tepay_money']+$rowItem['feeprice']))->where('uid=?',$rowContents['authorId']);
			$updateUserRows= $db->query($updateUser);
		}
		echo 'success';
	}else{		
		$db = Typecho_Db::get();
		$updateItem = $db->update('table.tepay_fees')->rows(array('feestatus'=>2))->where('feeid=?',$_POST['out_trade_no']);
		$updateItemRows= $db->query($updateItem);
		echo 'fail';exit();
	}
}

?>