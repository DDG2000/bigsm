<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 留言 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER_LEAVEWORD_MDY');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}


$t = '留言回复';
$points = array('会员管理', $t);
$action = 'LeavewordProcess.php';
$hides  = array('Work'=>'reply', 'type'=>$type);
$params = array();
$helps  = array();
$extend = array();

$Id = $FLib->RequestInt('lid',0,9,'ID');
$Rs = $DataBase->GetResultOne('select a.*,b.Vc_content rcontent,b.ID bid from user_leaveword a left join user_leaveword_reply b on a.ID=b.I_leavewordID and b.Status=1 where   a.Status=1 and a.ID='.$Id.' limit 0,1');
if(!$Rs){ echo showErr('记录未找到'); exit; }
$title = $t;
$hides['Id'] = $Id;
$hides['bid'] = $Rs['bid'];

//$params['title'] = array('val'=>$Rs['Vc_name'],'name'=>'标题','tip'=>'','ty'=>'');
$params[] = array('val'=>$Rs['Vc_username'],'name'=>'用户','tip'=>'','ty'=>'');
$params[] = array('val'=>$Rs['Createtime'],'name'=>'时间','tip'=>'','ty'=>'');
$params[] = array('val'=>$Rs['Vc_IP'],'name'=>'IP','tip'=>'','ty'=>'');
$params[] = array('val'=>$Rs['Vc_content'],'name'=>'内容','tip'=>'','ty'=>'');
$params['rcontent'] = array('val'=>$Rs['rcontent'],'name'=>'回复内容','tip'=>'','ty'=>'textarea');


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
