<?php
if(1){
/****************************************************************** 
**创建者：spoon
**创建时间：2014-08-04
**本页： 会员推荐情况 统计
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_USER_2');
require(WEBROOTDATA.'userclass.cache.inc.php');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$mWhere = '';
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType;

$title = '会员推荐情况';
$points = array('统计管理', '会员统计', $title );
$sTypes = array('', '用户名');
$extend = array();
$extend['hides'] = array();

switch ($sType){
	case 1:
		$mWhere .= " and zz.Vc_nickname like '%{$sKey}%'  ";
		break;

}

$tables = "user_base z,user_base zz where zz.myIntroCode=z.introForm and z.introForm is not null and z.introForm !='' {$mWhere} group by z.introForm";
$sql = "select count(z.introForm) as con ,
zz.ID,zz.I_userclass,zz.Vc_nickname,zz.Vc_truename,zz.Vc_Email,zz.Vc_mobile,zz.Createtime,zz.myIntroCode,
(select sum(x.N_income) from p2p_record_cash x 
where x.I_userID=zz.ID and x.Status=1 and x.I_type=1) N_amount_recharge,
(select sum(a.N_amount) from p2p_bid a 
left join p2p_application b on b.ID=a.I_applicationID
left join user_base xx on a.I_userID=xx.ID
where b.I_status>=50 and a.I_deal=1 and a.Status=1
and xx.ID>999 and xx.introForm=zz.myIntroCode) N_amount_invest

FROM {$tables} order by con desc";

$sqlcount ="select count(*) from {$tables}";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'用户名', 'wid'=>'');
$ths[]=array('val'=>'真实名称', 'wid'=>'');
$ths[]=array('val'=>'邮箱账号', 'wid'=>'','sty'=>'width:160px');
//$ths[]=array('val'=>'手机号', 'wid'=>'');
$ths[]=array('val'=>'会员类型', 'wid'=>'');
$ths[]=array('val'=>'注册时间', 'wid'=>'');
$ths[]=array('val'=>'充值总金额', 'wid'=>'');
$ths[]=array('val'=>'投资总金额', 'wid'=>'');
$ths[]=array('val'=>'推荐人数', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');

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
		$_td .= '<td>'.$da_userclass[$Rs[$i]['I_userclass']]['Vc_name'].'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d H:i:s') .'</td>';
		$_td .= '<td>'. iset($Rs[$i]['N_amount_recharge'],'0.00') .'</td>';
		$_td .= '<td>'. iset($Rs[$i]['N_amount_invest'],'0.00') .'</td>';
		$_td .= '<td>'. $Rs[$i]['con'] .'</td>';
		$_td .= '<td><a href="MembersIntroInfo.php?Id='.$Rs[$i]['myIntroCode'].'" class="hs" w="750" h="700" title="【'.$Rs[$i]['Vc_nickname'].'】'.$title.'详细页">查看</a></td>';
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
$tpl->assign( 'sType', $sType );
$tpl->assign( 'sTypes', $sTypes );
$tpl->assign( 'btns', $btns );
$tpl->assign( 'pagelistparam', $pagelistparam );
$tpl->assign( 'ths', $ths );
$tpl->assign( 'tds', $tds );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('introcount'.$raintpl_ver);

exit;
}
?>
