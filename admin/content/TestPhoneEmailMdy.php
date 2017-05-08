<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 初始化参数
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_MODELEMAIL');

//use cache
if($raintpl_cache && $cache = $tpl->cache('mdy', 60, 1) ){echo $cache;	exit;}

$t='手机邮箱白名单';
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$type = $FLib->RequestInt('type',1,9,'类型');
$t=($type==1?'手机':'邮箱').'白名单';
$points = array('网站管理', $t.'管理');
$action = 'TestPhoneEmailProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();

switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne("SELECT * FROM site_test_phone_email WHERE id=" . $Id);
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$type = iset($Rs['I_type'],1);
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		$hides['I_type'] = $type;
		break;
	case 'AddReco':
		$title = '添加'.$t;
		$hides['I_type'] = $type;
		break;
	default:
		die('没有该操作类型!');	
}
$hides['type'] = $type;
$params['Vc_type'] = array('val'=>$type==1?'手机号':'邮箱号','name'=>'类型');
$params['Vc_value'] = array('val'=>iset($Rs['Vc_value']),'name'=>'值','tip'=>'','ty'=>'text','attrs'=>'isc="'.($type==1?'mobile':'email').'" maxlength="50"');
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