<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2014-6-8
**本页： 会员认证详细页 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}


$title = '会员认证详情';
$points = array('会员管理', $title);
$hides  = array();
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_MEMBER');

$Id = $FLib->RequestInt('Id',0,9,'ID');
$sqlw = 'from p2p_user_certificate a left join p2p_certificate b on a.I_certificateID=b.ID left join user_base u on a.I_userID=u.ID where a.Status=1 and b.Status=1 and a.ID='.$Id;
$sql = 'select a.*,b.Vc_name,b.I_type,u.Vc_nickname '.$sqlw.'';
$Rs = $DataBase->GetResultOne($sql);
if(!$Rs){ echo showErr('记录未找到'); exit; }

$params[] = array('name'=>'会员','val'=>'ID:'.$Rs['I_userID'].' 昵称：'.$Rs['Vc_nickname'],'tip'=>'');
$params[] = array('name'=>'认证项','val'=>$Rs['Vc_name'],'tip'=>'');
$imgs = '';
$o = jsonstr_to_array($Rs['Vc_image']);
foreach($o as $v){
	$imgs .= '<a href="'.$v.'" target="_blank"><img src="'.$v.'" style="max-width:300px;_width:300px;"/></a><br/>';
}
$params[] = array('name'=>'认证资料','val'=>$imgs,'tip'=>'');
$params[] = array('name'=>'创建时间','val'=>$FLib->fromatdate($Rs['Createtime'],'Y-m-d'),'tip'=>'');


//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'hides', $hides );
$tpl->assign( 'params', $params );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('info'.$raintpl_ver);
exit;
}
?>
