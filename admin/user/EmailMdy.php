<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 邮件 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$title = '邮件';
$points = array('会员管理', $title.'管理');
$action = 'EmailProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();
$extend['js'] = '<script src="../include/editor2/kindeditor.js" type="text/javascript"></script>';

$Admin->CheckPopedoms('SC_MEMBER_EMAIL_MDY');
switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from site_email where ID='.$Id.' limit 0,1');
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		break;
	case 'AddReco':
		$Rs = array();
		$title = '添加'.$t;
		break;
	default:
		die('没有该操作类型!');	
}
$params['title'] = array('name'=>'标题','val'=>iset($Rs['Vc_title']),'ty'=>'text','attrs'=>'isc="" maxlength="100"','tip'=>'');
$params[] = array('val'=>'<textarea id="KE_content" name="contentvalue">'.iset($Rs['T_content']).'</textarea><script type="text/javascript">
if(KE)KE.show({id:\'KE_content\',width:\'570px\',height:\'250px\'});</script>','name'=>'商品介绍','tip'=>'');

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
