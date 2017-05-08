<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 用户组编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$t='用户组';
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$Parent = $FLib->RequestInt('Parent',0,10,'父ID');
$points = array('系统管理', $t.'管理');
$action = 'GroupProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_SYS_SET_GROUP_MDY');
switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne("SELECT * FROM sc_group WHERE id=" . $Id);
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		$Rs1 = $DataBase->GetResultOne("select T_rule from sc_rule_group where Status<>0 and I_type=1 And I_groupID=".$Id);
		$rolelist = !$Rs1 ? '':$Rs1['T_rule'];
		$Rs1 = $DataBase->GetResultOne("select T_rule from sc_rule_group where Status<>0 and I_type=2 And I_groupID=".$Id);
		$rulelist = !$Rs1 ? '':$Rs1['T_rule'];
		break;
	case 'AddReco':
		$Rs = array();
		$title = '添加'.$t;
		$Rs['I_order']=100;
		break;
	default:
		die('没有该操作类型!');	
}
$Rs1 = $DataBase->GettArrayResult("select Vc_name from sc_group where status<>0 and I_show=1 and ID='".$Parent."'");
$pname = !$Rs1 ? '根组':$Rs1[0];
$params[] = array('val'=> $pname.'<input name="parent" value="'.$Parent.'" type="hidden" />','name'=>'上级组','tip'=>'');
$params['title'] = array('val'=>iset($Rs['Vc_name']),'name'=>'用户组名称','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="50"');
$params['order'] = array('val'=>iset($Rs['I_order']),'name'=>'排序号','tip'=>'','ty'=>'text','attrs'=>'isc="orderpatFun" maxlength="6"');
$params['intro'] = array('val'=>iset($Rs['Vc_intro']),'name'=>'备注','ty'=>'textarea','attrs'=>'');
$params['rolelist'] = array('val'=>iset($rolelist),'name'=>'角色分配','tip'=>'用户属于角色,则有该角色的权限','ty'=>'text','attrs'=>'data-url="RoleSelect.php?Type=2&IdList=" w="500" h="400" title="角色选择器"');
if($Config->GroupTure == 1){
	$params['rulelist'] = array('val'=>iset($rulelist),'name'=>'独立权限','tip'=>'用于分配其所在组或角色中不存在的权限','ty'=>'text','attrs'=>'data-url="PopedomSelect.php?Type=2&IdList=" w="500" h="400" title="权限选择器"');
}

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