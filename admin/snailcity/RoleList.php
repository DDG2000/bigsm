<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 权限 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SYS_SET_ROLE');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType;

$title = '角色';
$points = array('系统管理', $title.'管理');
$sTypes = array('', '角色名称');
$hides  = array();//不传parent 查询全部
$extend = array();

switch ($sType){
	case 1:
		$mWhere = "Vc_name like '%$sKey%'";
		break;
	default:
		$mWhere = '1=1';
		break;
}
$tables = 'sc_role where Status=1 and '.$mWhere.'';
$sql = "SELECT * FROM {$tables} order by ID";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'角色名称', 'wid'=>'');
$ths[]=array('val'=>'创建者', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td>'.$Rs[$i]['Vc_name'].'</td>';
		$_td .= '<td>'.$Admin->GetAdminInfo($Rs[$i]['I_operatorID'],1).'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$_td .= '<td><a href="RoleMdy.php?Work=MdyReco&Id='.$Rs[$i]['ID'].'" class="hs" h="450" title="编辑'.$title.'">编辑</a></td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array('<a href="RoleProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删 除</a>',);
$extend['gbtns'] = array('<a href="RoleMdy.php?Work=AddReco" class="hs" h="450" title="添加'.$title.'"><span>添加'.$title.'</span></a>',);
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
