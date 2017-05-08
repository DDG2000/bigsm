<?php
/**
* 模块：基础模块
* 描述：用户列表页
* 版本：SnailCity内容管理系统 V0.1系统
* 作者：张绍海
* 书写日期：2011-03-16
* 修改日期：
*/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER');
$Work   = $FLib->RequestChar('Work',0,50,'参数',0);

$tt = '用户';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
{
	case 'SendMessage'://站内信
		require_once(WEBROOTINCCLASS . 'MessageService.php');
		$objMsg=new MessageService();
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
		dump($IdList);
		if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		$da['url'] = $FLib->RequestChar('url',0,50,'链接地址',1);
		$da['content'] = $FLib->RequestChar('note',0,50,'通知',1);
		$IdList=explode(',',$IdList);
		if($IdList['0']=='0'){
			$re=$objMsg->sendAllMsgSuccess($da);
			$n=$re['sum'];
			$r=$re['count'];
		}else{
			$r=0;$n=0;
			foreach ($IdList as $id){
				$da['userID']=$id;
				$objMsg->sendWebMsgSuccess($da);
				if($re){$r++;}
				$n++;
			}
		}
		$Admin ->AddLog('站内信','通知：其ID为：'.$IdList );
		echo showSuc($tt."通知完毕".($n>$r ? ('共'.$n.'条记录,'.$r.'条成功！,'.($n-$r).'条失败！'):''),$FLib->IsRequest('backurl'),$obj);
		break;
	case 'LockReco'://账户锁定
	    $IdList = $FLib->RequestChar('IdList',0,0,'IdList',0);
	    if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		echo "update user_base set Status=2 where status=1 and ID in ($IdList)";
	    $DataBase->QuerySql("update user_base set Status=2 where status=1 and ID in ($IdList)");
		$r = $DataBase->GetAffectedRows();
		$n = count(explode(',',$IdList));
	    $Admin ->AddLog('会员管理','锁定用户：其ID为：'.$IdList );
		echo showSuc($tt.'锁定完毕'.($n>$r ? ('共'.$n.'条记录,'.$r.'条成功！,'.($n-$r).'条失败！'):''),$FLib->IsRequest('backurl'),$obj);
		break;
	case 'UnLockReco'://账户解锁
	    $IdList = $FLib->RequestChar('IdList',0,0,'IdList',0);
	    if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	    $DataBase->QuerySql("update user_base set Status=1 where status=2 and ID in ($IdList)");
		$r = $DataBase->GetAffectedRows();
		$n = count(explode(',',$IdList));
	    $Admin ->AddLog('会员管理','解除锁定用户：其ID为：'.$IdList );
		echo showSuc($tt.'解除完毕'.($n>$r ? ('共'.$n.'条记录,'.$r.'条成功！,'.($n-$r).'条失败！'):''),$FLib->IsRequest('backurl'),$obj);
		break;
	case 'UnLoginLockReco'://解除登陆锁定
		$IdList = $FLib->RequestChar('IdList',0,0,'IdList',0);
		if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		$DataBase->QuerySql("update user_base set errLoginLockTime=0 where errLoginLockTime>0 and ID in ($IdList)");
		$DataBase->QuerySql("update user_login_record set Status=0 where I_type=0 and Vc_name in ($IdList)");
		$r = $DataBase->GetAffectedRows();
		$n = count(explode(',',$IdList));
		$Admin ->AddLog('会员管理','解除登陆锁定用户：其ID为：'.$IdList );
		echo showSuc($tt.'解除完毕'.($n>$r ? ('共'.$n.'条记录,'.$r.'条成功！,'.($n-$r).'条失败！'):''),$FLib->IsRequest('backurl'),$obj);
		break; 
	case 'DeleteReco':
	    $IdList = $FLib->RequestChar('IdList',0,0,'IdList',0);
	    if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	    $DataBase->QuerySql("update user_base set Status=0 where ID in ($IdList)");
	    $Admin ->AddLog('会员管理','删除用户：其ID为：'.$IdList );
		echo showSuc($tt.'删除成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'DelBadReco':
	    $IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
	    if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	    $DataBase->QuerySql("update user_base set I_bad=0 where ID in ($IdList)");
	    $Admin ->AddLog('会员管理','移除黑名单用户：其ID为：'.$IdList );
		echo showSuc('黑名单用户移除成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'MdyPwd':
	    $Id =  $FLib->RequestInt('Id',0,9,'参数');
		$pwd = $FLib->RequestChar('pwd',0,100,'新密码',0);
		$pwd1 = $FLib->RequestChar('pwd1',0,100,'确认新密码',0);
		if($pwd!=$pwd1){ echo showErr('密码不一致'); exit; }
	    $p = md5($Config->PasswordPre.$pwd);
		$DataBase->QuerySql(" update `user_base` set  `Vc_password`= '".$p."' where ID=".$Id." ");
	    $Admin ->AddLog('会员管理','修改用户密码：其ID为：'.$Id );
		echo showSuc($tt.'修改密码成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'UpUser1'://分类转为专家
	    $IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
	    if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	    $DataBase->QuerySql("update user_base set I_type=2 where ID in ($IdList)");
	    $Admin ->AddLog('会员管理','分类转为专家：其ID为：'.$IdList );
		echo showSuc($tt.'转为专家成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'UpUser2'://分类转为零售商
	    $IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
	    if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	    $DataBase->QuerySql("update user_base set I_type=3 where ID in ($IdList)");
	    $Admin ->AddLog('会员管理','分类转为零售商：其ID为：'.$IdList );
		echo showSuc($tt.'转为零售商成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'UpUser3'://分类转为普通用户
	    $IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
	    if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	    $DataBase->QuerySql("update user_base set I_type=1 where ID in ($IdList)");
	    $Admin ->AddLog('会员管理','分类转为普通用户：其ID为：'.$IdList );
		echo showSuc($tt.'转为普通用户成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'UpUser4'://身份转为普通用户
	    $IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
	    if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	    $DataBase->QuerySql("update user_base set I_degree=1 where ID in ($IdList)");
	    $Admin ->AddLog('会员管理','身份转为普通用户：其ID为：'.$IdList );
		echo showSuc($tt.'转为普通用户成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'UpVIP'://身份转为VIP
	    $IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
	    if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	    $DataBase->QuerySql("update user_base set I_degree=2 where ID in ($IdList)");
	    $Admin ->AddLog('会员管理','身份转为VIP：其ID为：'.$IdList );
		echo showSuc($tt.'转为VIP成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'Audit'://身份转为VIP
	    $IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
	    if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	    $DataBase->QuerySql("update user_base set I_degree=2 where ID in ($IdList)");
	    $Admin ->AddLog('会员管理','身份转为VIP：其ID为：'.$IdList );
		echo showSuc($tt.'转为VIP成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'AuditReco': /**审核记录**/
	    $IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
	    if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		$DataBase->QuerySql('UPDATE user_base SET I_audit=1,I_auditID='.$Admin->Uid.",Dt_audit=now() WHERE ID IN({$IdList}) and I_mobileauthenticate=2 and I_Emailauthenticate=2");//手机验证后才可审核
		$r = $DataBase->GetAffectedRows();
		$n = count(explode(',',$IdList));
		$Admin ->AddLog('会员管理','审核'.$tt.'：其ID为：'.$IdList);
		echo showSuc($tt.'审核完毕！'.($n>$r ? ('共'.$n.'条记录,'.$r.'条成功！,'.($n-$r).'条失败！'):''),$FLib->IsRequest('backurl'),$obj);
		break;
	case 'MdyExpire':
	    $Id =  $FLib->RequestInt('Id',0,9,'参数');
	    $Dt_expire = $FLib->RequestChar('Dt_expire',0,100,'过期时间',0);
	    $I_userclass = $FLib->RequestInt('I_userclass',0,10,'会员类型',0);
		if(!strtotime($Dt_expire)){ echo showErr('时间错误'); exit; }

		$DataBase->QuerySql(" update `user_base` set  Dt_expire= '".$Dt_expire."',I_userclass= '.$I_userclass.' where ID=".$Id." ");
	    $Admin ->AddLog('会员管理','修改用户过期时间：其ID为：'.$Id );
		echo showSuc($tt.'修改成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'blockedWithdwar' : 
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
		if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		$DataBase->QuerySql("UPDATE `user_base` SET I_withdraw_blocked=1 WHERE ID IN ({$IdList}) ") ;
		$r = $DataBase->GetAffectedRows();
		$n = count(explode(',',$IdList));
		$Admin ->AddLog('会员管理','冻结提现'.$tt.'：其ID为：'.$IdList);
		echo showSuc($tt.'冻结提现！'.($n>$r ? ('共'.$n.'条记录,'.$r.'条成功！,'.($n-$r).'条失败！'):''),$FLib->IsRequest('backurl'),$obj);
		break ;
	case 'unBlockedWithdwar' :
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
		if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		$DataBase->QuerySql("UPDATE `user_base` SET I_withdraw_blocked=0 WHERE ID IN ({$IdList}) ") ;
		$r = $DataBase->GetAffectedRows();
		$n = count(explode(',',$IdList));
		$Admin ->AddLog('会员管理','解冻提现'.$tt.'：其ID为：'.$IdList);
		echo showSuc($tt.'解冻提现！'.($n>$r ? ('共'.$n.'条记录,'.$r.'条成功！,'.($n-$r).'条失败！'):''),$FLib->IsRequest('backurl'),$obj);
		break;
	case 'unBlockedAuto' :
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
		if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		$DataBase->QuerySql("UPDATE `user_base` SET TrueNameCount=0 WHERE ID IN ({$IdList}) ") ;
		$r = $DataBase->GetAffectedRows();
		$n = count(explode(',',$IdList));
		$Admin ->AddLog('会员管理','解除实名认证限制'.$tt.'：其ID为：'.$IdList);
		echo showSuc($tt.'解除实名认证限制！'.($n>$r ? ('共'.$n.'条记录,'.$r.'条成功！,'.($n-$r).'条失败！'):''),$FLib->IsRequest('backurl'),$obj);
		break;
	case 'MdyPay' ://代充
		require_once(WEBROOT.'pay'.L.'moneymm'.L.'api'.L.'api.class.php');
		require_once(WEBROOTINCCLASS . 'Pay.php');
		$Api = getApiClass();
		$da['Vc_name'] = $FLib->RequestChar('Vc_name',0,50,'任务标题',1);
		$da['Vc_content'] = $FLib->RequestChar('Vc_content',0,500,'任务说明',1);
		$da['N_amount'] = $FLib->RequestChar('N_amount',0,14,'金额',1);
		$da['I_userID'] = $FLib->RequestInt('I_userID',0,9,'用户ID');
		//$da['I_applyID'] = $g_conf['cfg_loan_management_account'];//D账户
		$da['N_amount'] = round(floatval($da['N_amount']),2);
		if($da['N_amount']==0){ echo showErr('请填写有效的金额！'); exit; }
		$u = $Db->fetch_one("select Vc_nickname,Vc_truename,Vc_openid from user_base where ID=".$da['I_userID']);
		if(!$u){echo showErr('用户不存在！'); exit;}
		
		//写订单
		$orderNo = $Api->getOrderNo();		
		$Pay = new Pay();
		$daa = array();
		$daa['I_userID'] = $da['I_userID'];
		$daa['Vc_code'] = $orderNo;
		$daa['N_price'] = $da['N_amount'];
		$daa['I_type'] = 1;//20:代充；参考 p2p_record_cash.I_type
		$daa['Vc_intro'] = '代充值';
		$r = $Pay->saveOrder($daa);
		
		//$Admin ->AddLog('审核管理','增加'.$tt.'：其名称为：'.$da['Vc_name'] );
		//还款付款-跳转收银台
		$nda=array();
		$nda[] = array(
			'LoanOutMoneymoremore' => $Api->api_config['PlatPay2'],
			'LoanInMoneymoremore' => $u['Vc_openid'],
			'OrderNo' => $Api->getOrderNo(),
			'BatchNo' => $Api->getOrderNo(),
			'Amount' => $da['N_amount'],
			'TransferName'=> "代充值"
		);		
	
		$dab = array(
			'loanList' => $nda,
			'orderNo'=>$orderNo,
			'tradeNo'=>$da['N_amount'],
			'thesname' => "代充值",//交易类型：thesname
			'return' => 'http://'.$_SERVER['HTTP_HOST'].'/pay/moneymm/return/dc_return.php',
			'notify' => 'http://'.$_SERVER['HTTP_HOST'].'/pay/moneymm/notify/dc_notify.php'
		) ;
		$r=array('flag'=>1);
		$r['htmls'] = $Api->requestFormopen($Api->repay_pay_manage($dab));
		$r['msg'] = '跳转新页面支付，是否已完成？';
		echo showTip($r['msg'],$FLib->IsRequest('backurl'),$obj,'pause');
		echo $r['htmls'];
		break;	
	case 'MdyFriend':
		$Id =  $FLib->RequestInt('Id',0,9,'参数');
		$myIntroCode = $FLib->RequestChar('myIntroCode',0,100,'我的邀请码');
		$ID1 = $FLib->RequestChar('ID1',0,10,'邀请我的人');
		$ID2 = $FLib->RequestChar('ID2',0,1000,'我邀请的人');
		
		$Rs1 = $DataBase->GetResultOne("select ID,myIntroCode from user_base where ID={$ID1}");
		$User = new User();
		$Rs1['myIntroCode']=$User->returnUserCode($Rs1['ID'],$Rs1['myIntroCode']);
		$DataBase->QuerySql(" update `user_base` set IntroForm='{$Rs1['myIntroCode']}'  where ID={$Id}");
		$DataBase->QuerySql(" update `user_base` set IntroForm='' where IntroForm='{$myIntroCode}'");
		$DataBase->QuerySql(" update `user_base` set IntroForm='{$myIntroCode}' where ID in (".$ID2.") ");
		
		$Admin ->AddLog('会员管理','修改被邀请人：其ID为：'.$Id );
		echo showSuc($tt.'修改成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'MdyCompany':
		$Id =  $FLib->RequestInt('Id',0,9,'参数');
		$audit =  $FLib->RequestInt('audit',2,9,'参数');
		$da = array();
		$da['I_audit'] = $audit;
		$da['I_auditID'] = $Admin->Uid;
		$da['Dt_audit@'] = 'now()';
		$DataBase->autoExecute('user_base', $da, 'update', "ID=$Id");
		$Admin ->AddLog('会员管理','企业用户审核：其ID为：'.$Id );
		echo showSuc($tt.'审核修改成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'setFreeWidthdraw' :
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
	    if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	    $DataBase->QuerySql("update user_base set I_allowance_free=1 where status=1 and ID in ($IdList)");
		$r = $DataBase->GetAffectedRows();
		$n = count(explode(',',$IdList));
	    $Admin ->AddLog('会员管理','设置免费提现用户：其ID为：'.$IdList );
		echo showSuc($tt.'设置完毕'.($n>$r ? ('共'.$n.'条记录,'.$r.'条成功！,'.($n-$r).'条失败！'):''),$FLib->IsRequest('backurl'),$obj);
		break;
	case 'unSetFreeWidthdraw' :
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
	    if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	    $DataBase->QuerySql("update user_base set I_allowance_free=0 where status=1 and ID in ($IdList)");
		$r = $DataBase->GetAffectedRows();
		$n = count(explode(',',$IdList));
	    $Admin ->AddLog('会员管理','减少免提现手续费用户：其ID为：'.$IdList );
		echo showSuc($tt.'处理完毕'.($n>$r ? ('共'.$n.'条记录,'.$r.'条成功！,'.($n-$r).'条失败！'):''),$FLib->IsRequest('backurl'),$obj);
		break;		
	default:
	    $FLib->Alert('参数错误','','');
	    exit;
}
$DataBase->CloseDataBase();
?>