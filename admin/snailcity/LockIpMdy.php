<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 修改黑/白名单 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$Status  = $FLib->RequestInt('Status',0,9,'类型');

$table = $Status==1 ?'sc_lockip':'sc_allowip';
$t = $Status==1?'IP黑名单':'IP白名单';
$points = array('系统管理', '系统工具', $t.'管理');
$action = 'LockIpProcess.php';
$hides  = array('Work'=>$Work, 'Status'=>$Status);
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_SYS_TOOL_ISLOCKIP_MDY');
switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne("select ID,Vc_startIP,Vc_endIP from {$table} where Status=1 And ID=$Id limit 0,1");
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		$sIP = $FLib->IPDecode($Rs[1]);
		$eIP = $FLib->IPDecode($Rs[2]);
		break;
	case 'AddReco':
		$Rs = array();
		$title = '添加'.$t;
		break;
	default:
		die('没有该操作类型!');	
}
$params['start'] = array('val'=>iset($sIP),'name'=>'IP开始地址','tip'=>'格式:192.168.1.0','ty'=>'text','attrs'=>'isc="ippatFun" autocomplete="off" maxlength="20"');
$params['end'] = array('val'=>iset($eIP),'name'=>'IP结束地址','tip'=>'格式:192.168.1.1','ty'=>'text','attrs'=>'isc="ippatFun" autocomplete="off" maxlength="20"');

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
