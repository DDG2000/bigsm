<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 留言 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_OPINION');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}


$t = '意见反馈回复';
$points = array('网站管理', $t);
$action = 'FeedbackProcess.php';
$hides  = array('Work'=>'reply', 'type'=>$type);
$params = array();
$helps  = array();
$extend = array();

$Id = $FLib->RequestInt('id',0,9,'ID');
$Rs = $DataBase->GetResultOne('select a.*, a.Vc_title as rcontent from fp_feedback a where a.Status=1 and a.ID='.$Id.' limit 0,1');
if(!$Rs){ echo showErr('记录未找到'); exit; }
$title = $t;
$hides['Id'] = $Id;

$params[] = array('val'=>$Rs['I_userID'],'name'=>'用户ID','tip'=>'','ty'=>'');
$params[] = array('val'=>$Rs['Createtime'],'name'=>'时间','tip'=>'','ty'=>'');
$params[] = array('val'=>$Rs['T_content'],'name'=>'内容','tip'=>'','ty'=>'');
$params['rcontent'] = array('val'=>$Rs['rcontent'],'name'=>'回复内容','tip'=>'回复内容500字以内！','ty'=>'textarea','attrs'=>'isc="MaxLen500"');


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
