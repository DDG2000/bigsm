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

$title = '用户密码';
$points = array('会员管理', $title);
$action = 'MemberProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_MEMBER_BASE');

$Id = $FLib->RequestInt('Id',0,9,'ID');
$Rs = $DataBase->GetResultOne('select * from user_base where ID='.$Id.' limit 0,1');
if(!$Rs){ echo showErr('记录未找到'); exit; }
$hides['Id'] = $Id;

//$params[] = array('val'=>iset($Rs['Vc_name']),'name'=>'用户名','tip'=>'');
$params[] = array('val'=>iset($Id),'name'=>'用户ID','tip'=>'');
$params['pwd'] = array('val'=>'<input type="password" name="pwd" value="" class="txt_put2" isc="pwdlenFun" maxlength="20" autocomplete="off">','name'=>'新密码','attrs'=>'isc','tip'=>'以非空字符的6-20位组成');
$params['pwd1'] = array('val'=>'<input type="password" name="pwd1" value="" class="txt_put2" isc="pwd2chkFun" maxlength="20" autocomplete="off">','name'=>'确认新密码','attrs'=>'isc','tip'=>'请重复输入新密码');

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
