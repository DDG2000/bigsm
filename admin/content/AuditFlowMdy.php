<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2014-5-24
**本页： 审核流程 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_AUDIT_FLOW_MDY');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$t = '审核流程';
$points = array('网站管理', '审核管理', $t.'管理');
$action = 'AuditFlowProcess.php';
$hides  = array('Work'=>$Work, 'type'=>$type);
$params = array();
$helps  = array();
$extend = array();

switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from sm_apply_flow where Status=1 and ID='.$Id.' limit 0,1');
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		$hides['I_entity'] = $Rs['I_entity'];
		break;
	case 'AddReco':
		$I_entity = $FLib->RequestInt('column',1,9,'分类');
		$Rs = array();
		$title = '添加'.$t;
		$hides['I_entity'] = $I_entity;
		break;
	default:
		die('没有该操作类型!');	
}

require(WEBROOTINC.'ApplyCommon.php');
foreach($entityarr as $k=>$v) $typearr[$k]=$v[0];
$params[] = array('val'=>iset($typearr[$hides['I_entity']]),'name'=>'类别','tip'=>'','ty'=>'','');

$params['Vc_name'] = array('val'=>iset($Rs['Vc_name']),'name'=>'流程名称','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="50"');
$params['I_level'] = array('val'=>iset($Rs['I_level']),'name'=>'审核级别','tip'=>'','ty'=>'text','attrs'=>'isc="nums" maxlength="2"');
$params['Vc_role'] = array('val'=>iset($Rs['Vc_role']),'name'=>'角色','tip'=>'用户属于角色,则有该角色的对应的流程','ty'=>'text','attrs'=>'data-url="AuditRoleSelect.php?Type=2&IdList=" w="500" h="400" title="角色选择器"');
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
