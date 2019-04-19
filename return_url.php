<?php
/*
 * 付费阅读是否成功的通知
 */
include '../../../config.inc.php';
$db = Typecho_Db::get();
$prefix = $db->getPrefix();
date_default_timezone_set('Asia/Shanghai');

$options = Typecho_Widget::widget('Widget_Options');
$option=$options->plugin('TeePay');
$plug_url = $options->pluginUrl;

$feeid = isset($_POST['feeid']) ? addslashes($_POST['feeid']) : '';
$queryContent= $db->select()->from('table.teepay_fees')->where('feeid = ?', $feeid); 
$rowContent = $db->fetchRow($queryContent);
	
$json=json_encode(array("feestatus"=>$rowContent['feestatus'],"feeid"=>$feeid));
echo $json;
exit;
?>