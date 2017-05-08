<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2014-5-24
**本页： 审核角色 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_AUDIT_ROLE_MDY');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$t = '审核角色';
$points = array('网站管理', '审核管理', $t.'管理');
$action = 'AuditRoleProcess.php';
$hides  = array('Work'=>$Work, 'type'=>$type);
$params = array();
$helps  = array();
$extend = array();

switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from p2p_role where Status=1 and ID='.$Id.' limit 0,1');
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		break;
	case 'AddReco':
		$Rs = array();
		$title = '添加'.$t;
		break;
	default:
		die('没有该操作类型!');	
}

$params['Vc_name'] = array('val'=>iset($Rs['Vc_name']),'name'=>'角色名称','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="50"');
$params['Vc_intro'] = array('val'=>iset($Rs['Vc_intro']),'name'=>'备注','tip'=>'','ty'=>'textarea','attrs'=>'isc="MaxLen500" ennull=""');

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
