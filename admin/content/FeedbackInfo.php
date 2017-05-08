<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 意见反馈详细页 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}


$title = '意见反馈详细页';
$points = array('网站管理', $title);
$hides  = array();
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_SITE_OPINION');

$Id = $FLib->RequestInt('Id',0,9,'ID');
$Rs = $DataBase->GetResultOne('select c.Vc_nickname uname,b.* from fp_feedback b left join user_base c on c.ID=b.I_userID where b.ID='.$Id.' and b.Status=1   limit 0,1');
if(!$Rs){ echo showErr('记录未找到'); exit; }
$params[] = array('name'=>'用户ID','val'=>iset($Rs['I_userID']),'tip'=>'');
$params[] = array('name'=>'审核状态','val'=>($Rs['I_audit']?'已审核':'未审核'),'tip'=>'');
$params[] = array('name'=>'创建时间','val'=>iset($Rs['Createtime']),'tip'=>'');
$params[] = array('name'=>'内容','val'=>iset($Rs['T_content']),'tip'=>'');
$params[] = array('name'=>'回复','val'=>iset($Rs['Vc_title']),'tip'=>'');

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
