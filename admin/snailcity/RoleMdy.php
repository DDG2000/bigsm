<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 角色编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$t='角色';
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$points = array('系统管理', $t.'管理');
$action = 'RoleProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();

switch($Work){
	case 'MdyReco':
		$Admin->CheckPopedoms('SC_SYS_SET_ROLE_MDY');
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne("SELECT * FROM sc_role WHERE id=" . $Id);
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		$Rs1 = $DataBase->GetResultOne('select * from  sc_rule_role where I_roleID = ' . $Id . ' And Status<>0 and I_type=0 limit 0,1');
		$rulelist = !$Rs1 ? '':$Rs1['T_rule'];
		break;
	case 'AddReco':
		$Admin->CheckPopedoms('SC_SYS_SET_ROLE_MDY');
		$Rs = array();
		$title = '添加'.$t;
		break;
	default:
		die('没有该操作类型!');	
}
$params['title'] = array('val'=>iset($Rs['Vc_name']),'name'=>'角色名称','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="30"');
$params['intro'] = array('val'=>iset($Rs['Vc_intro']),'name'=>'备注','ty'=>'textarea','attrs'=>'');
$params['rulelist'] = array('val'=>iset($rulelist),'name'=>'权限分配','tip'=>'用于分配该角色拥有的权限','ty'=>'text','attrs'=>'data-url="PopedomSelect.php?Type=2&IdList=" w="500" h="400" title="权限选择器"');

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
