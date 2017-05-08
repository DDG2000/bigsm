<?php
require_once"../include/TopFile.php";
require_once(WEBROOT.'pay'.L.'moneymm'.L.'api'.L.'api.class.php');
require_once(WEBROOTINCCLASS . 'Pay.php');
$Api = getApiClass();
$Pay = new Pay();
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
$RechargeType  = $FLib->RequestInt('RechargeType',0,9,'充值类型');
$RechargeType  = $RechargeType?$RechargeType:'';
switch($Work)
{

	case 'ReleaseB': 
		$u = $Db->fetch_one("select ID from user_base where Vc_openid='".$Api->api_config['PlatformMoneymoremore']."'");
		if(!$u){echo showErr('用户不存在！'); exit;}
		$amount   	   = $FLib->RequestChar('Amount',1,12,'参数',1);		
		$amount 	   = round(floatval($amount),2);
		if($amount<=1){
			echo showErr('金额必须大于1') ;
			return ;
		}		
		$orderNo = $Api->getOrderNo();
		$da = array();
		$da['I_userID'] = $u['ID'];
		$da['Vc_code'] = $orderNo;
		$da['N_price'] = $amount;
		$da['I_type'] = 1;//参考 p2p_record_cash.I_type
		
		$r = $Pay->saveOrder($da);
		
		$da = array(
				'RechargeMoneymoremore' => $Api->api_config['PlatformMoneymoremore'],
				'OrderNo' => $orderNo,
				'Amount' => $amount,
				'FeeType'=>1,
				'RechargeType' => $RechargeType,
				'ReturnURL' => "http://".$_SERVER["HTTP_HOST"]."/pay/moneymm/return/bk_adminpay.php",
				'NotifyURL' => "http://".$_SERVER["HTTP_HOST"]."/pay/moneymm/notify/bk_adminpay_notify.php"
		);	
		
		$r['htmls'] =  $Api->requestFormopen($Api->pay($da));
		$r['msg'] = '跳转新页面充值，是否已完成？';
		echo showTip($r['msg'],"../snailcity/Daccount.php?Work=ReleaseB&I_entity=3",'self','pause');
		echo $r['htmls'];
		break;

	case 'ReleaseC': 
		$u = $Db->fetch_one("select ID from user_base where Vc_openid='".$Api->api_config['PlatPay']."'");
		if(!$u){echo showErr('用户不存在！'); exit;}
		$amount   = $FLib->RequestChar('Amount',1,12,'参数',1);		
		$amount = round(floatval($amount),2);
		if($amount<=1){
			echo showErr('金额必须大于1') ;
			return ;
		}		
		$orderNo = $Api->getOrderNo();
		$da = array();
		$da['I_userID'] = $u['ID'];
		$da['Vc_code'] = $orderNo;
		$da['N_price'] = $amount;
		$da['I_type'] = 1;//参考 p2p_record_cash.I_type
		
		$r = $Pay->saveOrder($da);
		
		$da = array(
				'RechargeMoneymoremore' => $Api->api_config['PlatPay'],
				'OrderNo' => $orderNo,
				'Amount' => $amount,
				'RechargeType' => $RechargeType,
				'FeeType'=>1,
				'ReturnURL' => "http://".$_SERVER["HTTP_HOST"]."/pay/moneymm/return/bk_adminpay.php",
				'NotifyURL' => "http://".$_SERVER["HTTP_HOST"]."/pay/moneymm/notify/bk_adminpay_notify.php"
		);	
		
		$r['htmls'] =  $Api->requestFormopen($Api->pay($da));
		$r['msg'] = '跳转新页面充值，是否已完成？';
		echo showTip($r['msg'],"../snailcity/Daccount.php?Work=ReleaseC&I_entity=1",'self','pause');
		echo $r['htmls'];
		break;
	
	case 'ReleaseD': 	
		$u = $Db->fetch_one("select ID from user_base where Vc_openid='".$Api->api_config['PlatPay2']."'");
		if(!$u){echo showErr('用户不存在！'); exit;}	
		$amount   = $FLib->RequestChar('Amount',1,12,'参数',1);
		$amount = round(floatval($amount),2);
		if($amount<=1){
			echo showErr('金额必须大于1') ;
			return ;
		}
		$orderNo = $Api->getOrderNo();
		$da = array();
		$da['I_userID'] = $u['ID'];
		$da['Vc_code'] = $orderNo;
		$da['N_price'] = $amount;
		$da['I_type'] = 1;//参考 p2p_record_cash.I_type		
		$r = $Pay->saveOrder($da);		
		$da = array(
				'RechargeMoneymoremore' => $Api->api_config['PlatPay2'],
				'OrderNo' => $orderNo,
				'Amount' => $amount,
				'ReturnURL' => "http://".$_SERVER["HTTP_HOST"]."/pay/moneymm/return/bk_adminpay.php",
				'NotifyURL' => "http://".$_SERVER["HTTP_HOST"]."/pay/moneymm/notify/bk_adminpay_notify.php"
		);		
		if($RechargeType)$da['RechargeType']=$RechargeType;
		$r['htmls'] =  $Api->requestFormopen($Api->pay($da));
		$r['msg'] = '跳转新页面充值，是否已完成？';	
			
		echo showTip($r['msg'],"../snailcity/Daccount.php?Work=ReleaseD&I_entity=2",'self','pause');
		echo $r['htmls'];
		break;	
}
?>