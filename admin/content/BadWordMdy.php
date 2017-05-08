<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 坏词 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$t = '坏词';
$points = array('网站管理', $t.'管理');
$action = 'BadWordProcess.php';
$hides  = array('Work'=>$Work, 'type'=>$type);
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_SITE_BDWORD');
switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from site_badword where Status=1 and ID='.$Id.' limit 0,1');
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$params['Vc_badword'] = array('val'=>iset($Rs['Vc_badword']),'name'=>'坏词','tip'=>'坏词不能重复','ty'=>'text','attrs'=>'isc="" maxlength="50"');
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		break;
	case 'AddReco':
		$Rs = array();
		$params['Vc_badword'] = array('val'=>'','name'=>'坏词','tip'=>'添加多个请以英文逗号或者空格分割<br/>坏词不能重复','ty'=>'textarea','attrs'=>'isc="" maxlength="200"');
		$title = '添加'.$t;
		break;
	default:
		die('没有该操作类型!');	
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

