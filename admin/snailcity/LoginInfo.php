<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 登录信息 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$Status  = $FLib->RequestInt('Status',0,9,'类型');

$title = '用户登录信息';
$points = array('系统管理', '当前用户', $title);
$params = array();
$helps  = array();
$extend = array();

$Id = $Admin->Uid;
$Rs = $DataBase->GetResultOne("select * from sc_user where Status=1 And ID=$Id limit 0,1");
if(!$Rs){ echo showErr('记录未找到'); exit; }
$params[] = array('name'=>'用户名','val'=>iset($Admin->Uname));
$params[] = array('name'=>'创建时间','val'=>iset($Rs['Createtime']));
$params[] = array('name'=>'本次登录','val'=>iset($Rs['Dt_logintime']));
$params[] = array('name'=>'上次登录','val'=>iset($Rs['Dt_lasttime']));
$params[] = array('name'=>'登录次数','val'=>iset($Rs['I_number']));
$extend['jpg'] = '../image/title/6.png';

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'params', $params );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('logininfo'.$raintpl_ver);
exit;
}
?>