<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 合作站点 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$t = '合作站点';
$points = array('商城管理', $t.'管理');
$action = 'CooperatProcess.php';
$hides  = array('Work'=>$Work, 'type'=>$type);
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_SITE_PARTNER');
switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from site_partner where Status=1 and ID='.$Id.' limit 0,1');
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		break;
	case 'AddReco':
		$Rs = array();
		$title = '添加'.$t;
		$Rs['I_picture']=$Rs['I_add']=1;
		break;
	default:
		die('没有该操作类型!');	
}

$params['title'] = array('val'=>iset($Rs['Vc_name']),'name'=>'站点名称','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="50"');
$params['site'] = array('val'=>iset($Rs['Vc_site']),'name'=>'站点地址','tip'=>'如:http://**','ty'=>'text','attrs'=>'isc="" maxlength="100"');

$points[] = $title;

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'action', $action );
$tpl->assign( 'hides', $hides );
$tpl->assign( 'params', $params );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('mdy'.$raintpl_ver);
exit;
}
?>
