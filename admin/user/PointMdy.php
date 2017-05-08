<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 用户积分 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$UserID = $FLib->RequestInt('UserID',0,9,'ID');

$title = '用户积分';
$points = array('会员管理', $title.'管理');
$action = 'PointProcess.php';
$hides  = array('Work'=>$Work,'UserID'=>$UserID);
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_MEMBER_POINT');

$Rs0 = $DataBase->GetResultOne('select * from user_base where ID='.$UserID.' limit 0,1');
if(!$Rs0){ echo showErr('记录未找到'); exit; }

$params[] = array('val'=>iset($Rs0['Vc_name']),'name'=>'用户名','tip'=>'');
$params[] = array('val'=>'<input type="radio" name="ctype" checked="checked" value="+" >增加 <input type="radio" name="ctype" value="-" >减少','name'=>'积分变更','tip'=>'选择积分增加还是减少');
$params['cpoint'] = array('name'=>'变更数值','val'=>'0','tip'=>'','ty'=>'text','attrs'=>'isc="nums",maxlength="8"');
$params['reason'] = array('name'=>'变更原因','val'=>'','tip'=>'填写原因，有助于事后查看','ty'=>'textarea','attrs'=>'isc="",maxlength="200"');

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
