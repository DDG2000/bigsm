<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 用户密码 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$title = '用户等级';
$points = array('会员管理', $title);
$action = 'MemberGradeProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_MEMBER_GRADE');
switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from user_grade where ID='.$Id.' limit 0,1');
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
$params['Vc_name'] = array('name'=>'用户身份','val'=>iset($Rs['Vc_name']),'ty'=>'text','attrs'=>'isc="" maxlength="50"','tip'=>'等级，用户的称呼');
$params['I_point'] = array('name'=>'所需积分','val'=>iset($Rs['I_point']),'ty'=>'text','attrs'=>'isc="nums" maxlength="9"','tip'=>'必需为整数');
$params['N_pointdiscount'] = array('name'=>'积分折扣','val'=>number_format(iset($Rs['N_pointdiscount']),2,'.',''),'ty'=>'text','attrs'=>'isc="" maxlength="10"','tip'=>'该等级，用户消耗积分折扣');
$params['N_pricediscount'] = array('name'=>'价格折扣','val'=>number_format(iset($Rs['N_pricediscount']),2,'.',''),'ty'=>'text','attrs'=>'isc="" maxlength="10"','tip'=>'该等级，用户买东西折扣');

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
