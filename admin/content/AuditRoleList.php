<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2014-5-24
**本页： 审核角色 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_AUDIT_ROLE');
$isMdy = $Admin->CheckPopedom('SC_SITE_AUDIT_FLOW_MDY');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType ;

$title = '审核角色';
$points = array('网站管理', '审核管理', $title.'管理' );
$sTypes = array('', '角色名称');
$hides  = array();
$extend = array();

$mWhere = '';
switch ($sType){
	case 1:
		$mWhere = " and Vc_name like '%" . $sKey . "%'";
		break;
}
$sqlw = 'from p2p_role where Status=1'.$mWhere;
$sql = 'select * '.$sqlw.' order by id desc';
$sqlcount = 'select count(*) '.$sqlw;
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'角色名称', 'wid'=>'');
$ths[]=array('val'=>'备注', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
if($isMdy){
$ths[]=array('val'=>'操作', 'wid'=>'');
}
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td title="'.$Rs[$i]['Vc_name'].'">'.$FLib->CutStr($Rs[$i]['Vc_name'],80).'</td>';
		$_td .= '<td title="'.$Rs[$i]['Vc_intro'].'">'.$FLib->CutStr($Rs[$i]['Vc_intro'],80).'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		if($isMdy){
		$_td .= '<td><a href="AuditRoleMdy.php?Work=MdyReco&Id='.$Rs[$i]['ID'].'" class="hs" h="340" title="编辑'.$title.'">编辑</a>';
		}
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();
$extend['gbtns']=$btns=array();
if($isMdy){
$btns[] = '<a href="AuditRoleProcess.php?Work=DelReco&IdList=" class="del" rel="IdList">删 除</a>';
$extend['gbtns'][] = '<a href="AuditRoleMdy.php?Work=AddReco" class="hs" h="340" title="添加'.$title.'"><span>添加'.$title.'</span></a>';
}
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
