<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 服务器信息 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

$Admin->CheckPopedoms('SC_SYS_TOOL_DETECT');

//use cache
if($raintpl_cache && $cache = $tpl->cache('server', 60, 1) ){echo $cache;	exit;}

$title = '服务器信息';
$points = array('系统管理', $title);
$params = array();
$helps  = array();
$extend = array();


$Y = '<font color="green">支持<b>√</b></font>';$N = '<font color="red">不支持<b>×</b></font>';
$params[] = array('name'=>'服务器基本信息','val'=>'','tip'=>'_Title_' );
$params[] = array('name'=>'服务器域名','val'=>$_SERVER['SERVER_NAME'] );
$params[] = array('name'=>'服务器IP','val'=>$_SERVER['REMOTE_HOST'] );
$params[] = array('name'=>'web server','val'=>$_SERVER['SERVER_SOFTWARE'] );
$params[] = array('name'=>'php版本','val'=>PHP_VERSION );
$params[] = array('name'=>'服务器路径','val'=>$_SERVER['DOCUMENT_ROOT'] );
$params[] = array('name'=>'剩余空间','val'=>intval(diskfreespace(".") / (1024 * 1024))."M" );
$params[] = array('name'=>'服务器操作系统','val'=>PHP_OS );
$params[] = array('name'=>'脚本超时时间','val'=>get_cfg_var("max_execution_time") );


$params[] = array('name'=>'服务器数据库信息','val'=>'','tip'=>'_Title_' );
$params[] = array('name'=>'mysql数据库','val'=>function_exists( 'mysql_connect' ) ?$Y:$N );
$params[] = array('name'=>'mysql版本','val'=>mysql_get_server_info() );


$params[] = array('name'=>'服务器常用组件','val'=>'','tip'=>'_Title_' );
$params[] = array('name'=>'GD库函数','val'=>extension_loaded('gd') ?$Y:$N );
$params[] = array('name'=>'mbstring函数','val'=>function_exists( 'mb_strlen' ) ?$Y:$N );
//$params[] = array('name'=>'服务器SMTP组件支持','val'=>function_exists( 'mail' ) ?$Y:$N );
$params[] = array('name'=>'xml','val'=>function_exists( 'xml_set_object' ) ?$Y:$N );
$params[] = array('name'=>'fsockopen函数支持','val'=>function_exists( 'fsockopen' ) ?$Y:$N );
$params[] = array('name'=>'Session支持','val'=>function_exists( 'session_start' ) ?$Y:$N );


//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'params', $params );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('server'.$raintpl_ver);
exit;
}
?>
