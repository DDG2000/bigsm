<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 支付记录 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER_COIN');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UserID = $FLib->RequestInt('UserID',0,9,'ID');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType ."&UserID=" . $UserID;

$title = '支付记录';
$points = array('会员管理', $title.'列表' );
$sTypes = array('','会员ID');
$hides  = array('UserID'=>$UserID);
$extend = array();

//$ismbsee = $Admin->CheckPopedom('SC_EXTRA_MEMBERSEE');
//if($ismbsee){
//	$sTypes[] = '会员邮箱';
//	$sTypes[] = '会员手机';
//}
$mWhere = '';
switch ($sType){
	case 1:
		$mWhere = " and I_userID='" . $sKey . "'";
		break;
	case 2:
		$mWhere = " and I_userID=(select ID from user_base where Vc_email='" . $sKey . "' limit 0,1)";
		break;
	case 3:
		$mWhere = " and I_userID=(select ID from user_base where Vc_mobile='" . $sKey . "' limit 0,1)";
		break;
}
if($UserID>0){
	$mWhere .= " and I_userID='{$UserID}'";
}
$tables = 'user_pay_record where Status>0 '.$mWhere.'';
$sql = "SELECT * FROM {$tables} order by ID desc";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);


$pays = array(1=>'充值会员',2=>'充值金币');
$payobj = new Pay();

function getPayment($k, $ks){
	$s = '----';
	foreach($ks as $v){
		if($v['k']==$k){
			$s = '<img src="/tpl/image/pay/'.$v['logo'].'" style="max-height:25px;_height:25px;">';
			break;
		}
	}
	return $s;
}

$ths = array();
$ths[]=array('val'=>'用户', 'wid'=>'10%');
$ths[]=array('val'=>'金额', 'wid'=>'10%');
$ths[]=array('val'=>'充值种类', 'wid'=>'');
$ths[]=array('val'=>'充值途经', 'wid'=>'');
$ths[]=array('val'=>'时间', 'wid'=>'10%');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td><a href="MemberInfo.php?Id='.$Rs[$i]['I_userID'].'" class="hs" h="" title="会员详细页">'.$Rs[$i]['I_userID'].'</a></td>';
		$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
		$_td .= '<td>'. $pays[$Rs[$i]['I_pay']] .'</td>';
		$_td .= '<td>'. getPayment($Rs[$i]['Vc_payment'], $payobj->payment) .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array();
$extend['gbtns'] = array();
$extend['fan'] = 0;
$extend['nohelps'] = 1;
$helps  = array();
$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
//$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'sKey', $sKey );
$tpl->assign( 'sType', $sType );
$tpl->assign( 'sTypes', $sTypes );
$tpl->assign( 'hides', $hides );
$tpl->assign( 'btns', $btns );
$tpl->assign( 'pagelistparam', $pagelistparam );
$tpl->assign( 'ths', $ths );
$tpl->assign( 'tds', $tds );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('list'.$raintpl_ver);
exit;
}
?>
