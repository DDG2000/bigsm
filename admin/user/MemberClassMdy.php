<?php
if(1){
/****************************************************************** 
**创建者：sakura
**创建时间：2014-11-04
**本页：会员类型 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$title = '会员类型';
$points = array('会员管理', $title.'管理');
$action = 'MemberClassProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_MEMBER_CLASS_MDY');
switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from user_class where ID='.$Id.' limit 0,1');
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑'.$title;
		$hides['Id'] = $Id;
		break;
	case 'AddReco':
		$Rs = array();
		$title = '添加'.$title;
		break;
	default:
		die('没有该操作类型!');	
}
$params['name'] = array('name'=>'名称','val'=>iset($Rs['Vc_name']),'ty'=>'text','attrs'=>'isc="" maxlength="100"','tip'=>'');
$params['order'] = array('name'=>'序号','val'=>iset($Rs['I_order']),'ty'=>'text','attrs'=>'isc="nums" maxlength="10"','tip'=>'数字大的排在前');

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
