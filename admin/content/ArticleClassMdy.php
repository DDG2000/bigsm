<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 内容分类 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$t = '内容分类';
$points = array('网站管理', $t.'管理');
$action = 'ArticleClassProcess.php';
$hides  = array('Work'=>$Work, 'type'=>$type);
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_SITE_ARTICLE_CLASS_MDY');
switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from es_article_class where Status=1 and ID='.$Id.' limit 0,1');
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		break;
	case 'AddReco':
		$Rs = array();
		$title = '添加'.$t;
		$Rs['I_picture']=$Rs['I_add']=1;
		$Rs['I_order']=100;
		break;
	default:
		die('没有该操作类型!');	
}

$params['name'] = array('val'=>iset($Rs['Vc_name']),'name'=>'分类名称','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="50"');
$params['order'] = array('val'=>iset($Rs['I_order']),'name'=>'排序号','tip'=>'','ty'=>'text','attrs'=>'isc="num" maxlength="8"');
$params[] = array('val'=>'<input name="ispicture" type="radio" value="1" '.(iset($Rs['I_picture'])==1?'checked':'').'/>显示 <input name="ispicture" value="2" type="radio" '.(iset($Rs['I_picture'])==2?'checked':'').'/>隐藏','name'=>'分类图片');
$params[] = array('val'=>'<input name="isadd" type="radio" value="1" '.(iset($Rs['I_add'])==1?'checked':'').'/>是 <input name="isadd" type="radio" value="2" '.(iset($Rs['I_add'])==2?'checked':'').'/>否','name'=>'增减文章');
$params['intro'] = array('val'=>iset($Rs['Vc_content']),'name'=>'备注','tip'=>'广告所显示的内容','ty'=>'textarea','attrs'=>'');


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
