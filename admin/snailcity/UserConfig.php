<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页：系统参数设置  编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$title = '参数设置';
$points = array('系统管理', '系统安全', '参数设置');
$action = 'UserConfigProcess.php';
$hides  = array();
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_SYS_SET_USERCONFIG');

$params['adminuser'] = array('name'=>'管理员邮箱','val'=>iset($Config->SuperAdminEmail),'tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="50"');
$params['website'] = array('name'=>'网站网址','val'=>iset($Config->WebRoot),'tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="200"');
$params['sessionhead'] = array('name'=>'session前缀','val'=>iset($Config->SessionHeadName),'tip'=>'','ty'=>'text','attrs'=>'maxlength="50"');
$params[] = array('name'=>'错误日志记录','val'=>'<input type="radio" name="islog" value="TRUE" '.($Config->ErrorLogListWrite==TRUE?'checked':'').'/>YES <input type="radio" name="islog" value="FALSE" '.($Config->ErrorLogListWrite==FALSE?'checked':'').'/>NO','tip'=>'');

$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF']);

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
