<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-5-8
**本页： 数据库操作
**说明：
******************************************************************/
require_once('../include/TopFile.php');

$t='数据库操作';
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$points = array('系统管理', $t.'管理');
$action = 'DataBaseProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();

//$Admin->CheckPopedoms('SC_SYS_SET_DB_OPERATE');

$params[] = array('name'=>'请输入sql语句','val'=>'专业人员使用，请慎用！','tip'=>'仅支持select,update,delete操作');
$params[] = array('name'=>'','val'=>'<textarea name="sql" class="txt_put3" style="width:600px;height:300px;" isc=""></textarea>','tip'=>'');

$points[] = $title;

//use cache
if($raintpl_cache && $cache = $tpl->cache('mdy', 60, 1) ){echo $cache;	exit;}

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
