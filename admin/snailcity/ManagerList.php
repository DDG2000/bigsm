<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 用户 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SYS_SET_USER');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType;

$title = '用户';
$points = array('系统管理', $title.'管理');
$sTypes = array('', '用 户 名');
$hides  = array();
$extend = array();

switch ($sType){
	case 1:
		$mWhere = "Vc_name like '%$sKey%'";
		break;
	default:
		$mWhere = '1=1';
		break;
}
//var_dump($Admin->Rule);
//系统管理员(起始用户system) 角色ID=2 
if(!(strpos(','.$Admin->Rule.',', ',2,')!==false || $Admin->Uname=='system')){
  $mWhere .= ' and ID not in (select I_userID from sc_rule_user where status=1 and I_type=1 and T_rule=2 )';
}
$tables = 'sc_user where Status>0 and '.$mWhere.'';
$sql = "SELECT * FROM {$tables} order by ID";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'', 'wid'=>'');
$ths[]=array('val'=>'用户名', 'wid'=>'');
$ths[]=array('val'=>'个人资料', 'wid'=>'');
$ths[]=array('val'=>'创建者', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td><img src="../image/'. ($Rs[$i]['Status']==2?'AdminLock.gif':'AdminUnLock.gif') .'"></td>';
		$_td .= '<td>'.$Rs[$i]['Vc_name'].'</td>';
		$_td .= '<td><a href="UserRuleLook.php?Id='.$Rs[$i]['ID'].'" class="hs" h="500" title="个人资料">查看</a></td>';
		$_td .= '<td>'.$Admin->GetAdminInfo($Rs[$i]['I_operatorID'],1).'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$_td .= '<td><a class="h_s" href="ManagerProcess.php?Work=LockReco&IdList='.$Rs[$i]['ID'].'&Flag='. ($Rs[$i]['Status']==2?'1">解禁':'2">禁用') .'</a> | <a href="ManagerMdy.php?Work=MdyReco&Id='.$Rs[$i]['ID'].'" class="hs" h="530" title="编辑'.$title.'">编辑</a> | <a href="ManagerMdy.php?Work=MdyPwd&Id='.$Rs[$i]['ID'].'" class="hs" h="360" title="修改密码">修改密码</a></td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array('<a href="ManagerProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删 除</a>',);
$extend['gbtns'] = array('<a href="ManagerMdy.php?Work=AddReco" class="hs" h="530" title="添加'.$title.'"><span>添加'.$title.'</span></a>',);
$helps  = array();
$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);


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
