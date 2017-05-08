<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 权限编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$t='权限';
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$Parent = $FLib->RequestInt('Parent',0,10,'父ID');
$PKey   = $FLib->RequestChar('PKey',1,50,'标识',0);
$points = array('系统管理', $t.'管理');
$action = 'PopedomProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();

switch($Work){
	case 'MdyReco':
		$Admin->CheckPopedoms('SC_SYS_SET_POPEDOM_MDY');
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne("SELECT * FROM sc_popedom WHERE id=" . $Id);
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$Parent=$Rs['I_parentID'];
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		$mWhere = 'And id<>'.$Id.'';
		break;
	case 'AddReco':
		$Admin->CheckPopedoms('SC_SYS_SET_POPEDOM');
		$Rs = array();
		$Rs['Vc_key'] = ($PKey != '')?$PKey . "_":'SC_';
		$title = '添加'.$t;
		$mWhere = '';
		$Rs['I_order']=100;
		break;
	default:
		die('没有该操作类型!');	
}
$options='<option value="0" '.($v[0] == $Parent?'selected="selected"':'').'>0&nbsp;根</option>';
$Rs1 = $DataBase->GettArrayResult('select ID,Vc_name from sc_popedom where Status<>0 '.$mWhere.' order by id');
foreach($Rs1 as $v){
	$options .= '<option value="'.$v[0].'" '.($v[0] == $Parent?'selected="selected"':'').'>'.$v[0].'&nbsp;'.$v[1].'</option>';
}
$params['parent'] = array('val'=>'<select name="parent" class="sel_put2 chzn-select-no-single" isc="">'.iset($options).'</select>','name'=>'上级权限','tip'=>'');
$params['Pkey'] = array('val'=>iset($Rs['Vc_key']),'name'=>'权限标识','tip'=>'格式：[A-Z、0-9和下划线]','ty'=>'text','attrs'=>'isc="popedompatFun" def="'.iset($Rs['Vc_key']).'" maxlength="50"');
$params['title'] = array('val'=>iset($Rs['Vc_name']),'name'=>'权限名称','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="30"');
$params['order'] = array('val'=>iset($Rs['I_order']),'name'=>'排序号','tip'=>'','ty'=>'text','attrs'=>'isc="orderpatFun" maxlength="6"');
$params['intro'] = array('val'=>iset($Rs['Vc_intro']),'name'=>'备注','ty'=>'textarea','attrs'=>'');
	
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