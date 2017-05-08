<?php
/**
* 模块：基础模块
* 描述：用户各种操作的处理页面包括锁定，禁用，修改密码 删除记录等 具体实现是根据接收到的$Work值来判断的
* 作者：张绍海
*/


//引入根目录下include里面的TopFile.php文件
require_once"../include/TopFile.php";
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);

switch ($Work)
{  
case 'Withdraw':
		require_once(WEBROOT.'pay'.L.'moneymm'.L.'api'.L.'api.class.php');
		require_once(WEBROOTINCCLASS . 'Pay.php');
		$Api = getApiClass();
		$Admin->CheckPopedoms('SC_SYS_SET_USER_EDIT');
		$ID   = $FLib->requestInt('ID',0,10,'参数',1);
		
		$bank=$DataBase->fetch_one("select * from p2p_user_bankcard where status=1 and ID=".$ID);
		if(!$bank){echo showErr('银行卡信息异常'.$ID); exit;}
		$Pay = new Pay();
		$orderNo = $Api->getOrderNo();
		$da=array();
		$da['I_userID'] = $bank['I_userID'];
		$da['Vc_code'] = $orderNo;		
		$da['N_price'] = $FLib->RequestChar('N_amount',0,10,'金额',1);
		$da['N_price'] =$N_price= round(floatval($da['N_price']),2);
		if($da['N_price']==0){ echo showErr('请填写有效的金额！'); exit; }

		if ($bank['I_userID'] == 903) {
			$amount = $Api->balanceQuery($Api->api_config['PlatPay']);
			$WithdrawMoneymoremore=$Api->api_config['PlatPay'];
			if ($da['N_price'] > $amount[0]) {
				echo showErr('备付金账号余额不足'); exit;
			}
			$FreeLimitStatus=getFreeLimitStatus(903);
		} else if ($bank['I_userID'] == 904) {
			$amount = $Api->balanceQuery($Api->api_config['PlatPay2']);
			$WithdrawMoneymoremore=$Api->api_config['PlatPay2'];
			if ($da['N_price'] > $amount[0]) {
				echo showErr('余额不足'); exit;
			}
			$FreeLimitStatus=getFreeLimitStatus(904);
		} else if ($bank['I_userID'] == 901) {
			$amount = $Api->balanceQuery($Api->api_config['PlatformMoneymoremore'],2);
			$WithdrawMoneymoremore=$Api->api_config['PlatformMoneymoremore'];
			if ($da['N_price'] > $amount[1]) {
				echo showErr('余额不足'); exit;
			}
			$FreeLimitStatus=getFreeLimitStatus(901);
		} else {
			echo showErr('参数错误'); exit;
		}

		$da['I_type'] = 2;//参考 p2p_record_cash.I_type
		$da['Vc_payment'] = $bank['Vc_bankcode'];
		$da['Vc_bankcard'] = $bank['Vc_code'];
		$r = $Pay->saveOrder($da);
		$da=array(
				'OrderNo'=>$orderNo,
				'WithdrawMoneymoremore'=>$WithdrawMoneymoremore,
				'Amount'=>$da['N_price'],
				'CardNo'=>$bank['Vc_code'],
		
				'CardType'=>0,
				'BankCode'=>$bank['Vc_bankcode'],
				'Province'=>$bank['Vc_province'],
				'City'=>$bank['Vc_city'],
		
				'$Remark1'=>$bank['I_userID'],
				//'delay' => $delay ,
				'FeePercent'=>0,
				'ReturnURL'=>"http://".$_SERVER["HTTP_HOST"]."/pay/moneymm/return/bk_admin_withdraw.php",
				'NotifyURL'=>"http://".$_SERVER["HTTP_HOST"]."/pay/moneymm/notify/bk_admin_withdraw_notify.php"
		
		);
		//账号能使用平台免提现手续费额度
		if($FreeLimitStatus){
			$freelimit=$Db->fetch_val('select Vc_value from site_parameter where Vc_name="cfg_withdraw_free"');
			$freelimit=round(floatval($freelimit),2);
			if($freelimit>0){
				if($freelimit>$N_price){
					$da['FeePercent']=100;
				}else{
					$da['FeePercent']=floor(($freelimit)/$N_price*100);
				}
			}else{
				$da['FeePercent']=0;
			}
		}
		
		$r=array('flag'=>1);
		$r['htmls'] = $Api->requestFormopen($Api->withdraw($da));
		$r['msg'] = '跳转新页面提现，是否已完成？';
		echo showTip($r['msg'],'/admin/snailcity/BankList.php?userID='.$bank['I_userID'],$obj,'pause');
		echo $r['htmls'];
		//echo $Api->withdraw($da);
		//echo '跳转中...';
		break;
	
    case 'AddReco':
		/***函数:添加记录***/
		$Admin->CheckPopedoms('SC_SYS_SET_USER_EDIT');
		
		$Vc_code = $FLib->requestchar('Vc_code',0,50,'银行卡号',1);
		$Vc_bankcode = $FLib->requestInt('Vc_bankcode',0,3,'银行编码',1);
		$Vc_province = $FLib->requestInt('Vc_province',0,4,'所在省',1);
		$Vc_city = $FLib->requestInt('Vc_city',0,4,'所在市',1);
		$I_userID = $FLib->RequestInt('userID',0,12,'用户id',1);	

		$Vc_code=str_replace(' ','',$Vc_code);
		
		if($I_userID==''){ echo showErr('用户id错误'); exit;}
		if($Vc_code==''){ echo showErr('银行卡号错误'); exit;}
		if($Vc_bankcode==''){ echo showErr('银行编码错误'); exit;}
		if($Vc_province==''){ echo showErr('省份错误'); exit;}
		if($Vc_city==''){ echo showErr('城市错误'); exit;}	
		$r = $DataBase->fetch_val("select count(*) from p2p_user_bankcard where status=1 and I_signed=0 and I_userID={$da['I_userID']} and Vc_code='{$da['Vc_code']}'");
		if($r>0) { echo showErr('该银行卡已经添加过了'); exit;}
		$DataBase->QuerySql("insert into p2p_user_bankcard(I_userID,Vc_code,Vc_bankcode,Vc_province,Vc_city) values($I_userID,'$Vc_code',$Vc_bankcode,$Vc_province,$Vc_city)");
	
		echo showSuc('银行卡添加完毕','/admin/snailcity/BankList.php?userID='.$I_userID,$obj); 
		break;
    case 'DeleteReco':		
		$Admin->CheckPopedoms('SC_SYS_SET_USER_MDY');
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		$I_userID = $FLib->RequestInt('userID',0,12,'用户id',1);

		if(!$FLib->IsIdlist($IdList))
		{
			echo showErr('参数错误');
			exit;
		}
		$DataBase->QuerySql("update p2p_user_bankcard set Status= 0 where ID in( $IdList )");
		
		$msg = '解绑银行卡：其ID为：';
		$Admin->AddLog('系统管理',$msg.$IdList);
		echo showSuc('解绑银行卡完毕','/admin/snailcity/BankList.php?userID='.$I_userID,$obj);
		
		break;   
}

function getFreeLimitStatus($uid){
	global $Db;
 	return $Db->fetch_val('select I_allowance_free from user_base where Id='.$uid);
}
?>
