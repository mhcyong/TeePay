<?php
include '../../../config.inc.php';
require_once 'libs/payjs.php';
require_once 'libs/alipay.php';
$db = Typecho_Db::get();
$prefix = $db->getPrefix();
date_default_timezone_set('Asia/Shanghai');

$action = isset($_POST['action']) ? addslashes($_POST['action']) : '';
if($action=='paysubmit'){
	$feetype = isset($_POST['feetype']) ? addslashes($_POST['feetype']) : '';
	$feecookie = isset($_COOKIE["TeePayCookie"]) ? addslashes($_COOKIE["TeePayCookie"]) : '';
	$feecid = isset($_POST['feecid']) ? intval(urldecode($_POST['feecid'])) : '';
	$feeuid = isset($_POST['feeuid']) ? intval(urldecode($_POST['feeuid'])) : 0;
	
	$options = Typecho_Widget::widget('Widget_Options');
	$option=$options->plugin('TeePay');
	$plug_url = $options->pluginUrl;
	
	$queryContent= $db->select()->from('table.contents')->where('cid = ?', $feecid); 
	$rowContent = $db->fetchRow($queryContent);
	

	switch($feetype){
		case "alipay":
			$time=time();
			$feeid = date("YmdHis",$time) . rand(100000, 999999);
			header('Content-type:text/html; Charset=utf-8');
			/*** 请填写以下配置信息 ***/
			$appid = $option->alipay_appid;  //https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID
			$notifyUrl = $option->alipay_notify_url;     //付款成功后的异步回调地址
			$outTradeNo = $feeid;     //你自己的商品订单号，不能重复
			$payAmount = $rowContent['teepay_price'];          //付款金额，单位:元
			$orderName = $rowContent['title'];    //订单标题
			$signType = 'RSA2';			//签名算法类型，支持RSA2和RSA，推荐使用RSA2
			$rsaPrivateKey=$option->app_private_key;;		
			//商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310
			/*** 配置结束 ***/
			$aliPay = new AlipayService();
			$aliPay->setAppid($appid);
			$aliPay->setNotifyUrl($notifyUrl);
			$aliPay->setRsaPrivateKey($rsaPrivateKey);
			$aliPay->setTotalFee($payAmount);
			$aliPay->setOutTradeNo($outTradeNo);
			$aliPay->setOrderName($orderName);

			$result = $aliPay->doPay();
			$result = $result['alipay_trade_precreate_response'];
			if($result['code'] && $result['code']=='10000'){
				//生成二维码
				$url = $result['qr_code'];
				$data = array(
				'feeid'   =>  $feeid,
				'feecid'   =>  $feecid,
				'feeuid'     =>  $feeuid,
				'feeprice'=> $rowContent['teepay_price'],
				'feetype'     =>  $feetype,
				'feestatus'=>0,
				'feeinstime'=>date('Y-m-d H:i:s',$time),
				'feecookie'=>$feecookie
				);
				$insert = $db->insert('table.teepay_fees')->rows($data);
				$insertId = $db->query($insert);
				$json=json_encode(array("status"=>"ok","type"=>"alipay","qrcode"=>$url,"feeid"=>$feeid));
				echo $json;
				exit;
			}
			break;
				
		case "wxpay":
			$time=time();
			$feeid = date("YmdHis",$time) . rand(100000, 999999);
			$arr = [
				'body' => $rowContent['title'],               // 订单标题
				'out_trade_no' => $feeid,       // 订单号
				'total_fee' => $rowContent["teepay_price"]*100,             // 金额,单位:分
				'attach'=>$rowContent["teepay_price"]// 自定义数据
			];
			$payjs = new Payjs($arr,$option->payjs_wxpay_mchid,$option->payjs_wxpay_key,$option->payjs_wxpay_notify_url);
			$res = $payjs->pay();
			$rst=json_decode($res,true);
			if($rst["return_code"]==1){
				$data = array(
					'feeid'   =>  $arr['out_trade_no'],
					'feecid'   =>  $feecid,
					'feeuid'     =>  $feeuid,
					'feeprice'=> $rowContent['teepay_price'],
					'feetype'     =>  $feetype,
					'feestatus'=>0,
					'feeinstime'=>date('Y-m-d H:i:s',$time),
					'feecookie'=>$feecookie
				);
				$insert = $db->insert('table.teepay_fees')->rows($data);
				$insertId = $db->query($insert);
				$json=json_encode(array("status"=>"ok","type"=>"payjs","qrcode"=>$rst["qrcode"],"feeid"=>$feeid));
				echo $json;
				exit;
				
			}
			break;
	}
	$json=json_encode(array("status"=>"fail"));
	echo $json;
	exit;
}
?>