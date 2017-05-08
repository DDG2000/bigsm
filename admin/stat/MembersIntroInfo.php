<?php
if(1){
/****************************************************************** 
**创建者：spoon
**创建时间：2014-08-04
**本页： 会员推荐情况 统计
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT');
$isUserSC=$Admin->CheckPopedom('SC_MEMBER');

//use cache
//if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('Id',1,50,'参数',1);
$mWhere = '';
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = '&Id=' . $sKey ;

$title = '会员推荐详情';
$points = array('统计管理', '会员统计', $title );
$sTypes = array('', '名称');
$extend = array();
$extend['hides'] = array();

$tables = " user_base u where IntroForm='{$sKey}'";
$sql = "select u.ID ,u.Vc_nickname,u.Vc_truename,u.Vc_Email,u.Vc_mobile,u.Createtime,u.myIntroCode,
if((select count(1) from p2p_repayment_record c where u.id=c.I_payeeID)<=0,'已注册','已投资') strutsStr,
(select sum(x.N_income) 
	from p2p_record_cash x 
	where x.I_userID=u.ID and x.Status=1 and x.I_type=1) N_amount_recharge,
(select sum(a.N_amount) 
	from p2p_bid a left join p2p_application b on b.ID=a.I_applicationID
	where b.I_status>=50 and a.I_userID=u.ID and a.I_deal=1 and a.Status=1) N_amount_invest
FROM {$tables}";

$sqlcount ="select COUNT(*) from {$tables}";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'用户名', 'wid'=>'');
$ths[]=array('val'=>'真实名称', 'wid'=>'');
$ths[]=array('val'=>'邮箱账号', 'wid'=>'','sty'=>'width:160px');
//$ths[]=array('val'=>'手机号', 'wid'=>'');
$ths[]=array('val'=>'注册时间', 'wid'=>'');
$ths[]=array('val'=>'充值总金额', 'wid'=>'');
$ths[]=array('val'=>'投资总金额', 'wid'=>'');
$ths[]=array('val'=>'绑定状态', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){		
		$_td='';	
		if($isUserSC)$_td .= '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['ID'].'" class="hs" h="" title="【'.$Rs[$i]['Vc_nickname'].'】会员详细页">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</a></td>';
		else $_td .= '<td title="'.$Rs[$i]['Vc_nickname'].'">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</td>';
		$_td .= '<td title="'. $Rs[$i]['Vc_truename'] .'">'. $Rs[$i]['Vc_truename'] .'</td>';
		$_td .= '<td style="width:160px" title="'. $Rs[$i]['Vc_Email'] .'">'. $Rs[$i]['Vc_Email'] .'</td>';
		//$_td .= '<td title="'. $Rs[$i]['Vc_mobile'] .'">'. $Rs[$i]['Vc_mobile'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d H:i:s') .'</td>';
		$_td .= '<td>'. iset($Rs[$i]['N_amount_recharge'],'0.00') .'</td>';
		$_td .= '<td>'. iset($Rs[$i]['N_amount_invest'],'0.00') .'</td>';
		$_td .= '<td>'. $Rs[$i]['strutsStr'] .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}	
}

$DataBase->CloseDataBase();
$extend['fan'] = false;
$helps  = array();
$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);

$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'sKey', $sKey );
$tpl->assign( 'btns', $btns );
$tpl->assign( 'pagelistparam', $pagelistparam );
$tpl->assign( 'ths', $ths );
$tpl->assign( 'tds', $tds );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('introinfo'.$raintpl_ver);

exit;
}
?>
