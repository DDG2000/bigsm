<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 广告位 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$t = '广告位';
$points = array('网站管理', $t.'管理');
$action = 'AdvertPlaceProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_SITE_ADPLACE_MDY');
switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from site_advertise_place where Status=1 and ID='.$Id.' limit 0,1');
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
$params['title'] = array('val'=>iset($Rs['Vc_name']),'name'=>'位置名称','tip'=>'广告显示位置的名称','ty'=>'text','attrs'=>'isc="" maxlength="20"');
//$params['file_name'] = array('val'=>iset($Rs['Vc_file']),'name'=>'应用文件','tip'=>'调用广告的文档','ty'=>'text','attrs'=>'isc="filenamepatFun" maxlength="100"');
//$params['start_flag'] = array('val'=>iset($Rs['Vc_start']),'name'=>'起始标记','tip'=>'用于文档中调取广告','ty'=>'text','attrs'=>'isc="" maxlength="50"');
//$params['end_flag'] = array('val'=>iset($Rs['Vc_end']),'name'=>'结束标记','tip'=>'用于文档中调取广告','ty'=>'text','attrs'=>'isc="" maxlength="50"');
$params[] = array('val'=>'<input name="width" type="text" class="txt_put2" style="width:30px;" maxlength="5" value ="'.iset($Rs['I_width']).'" /> * <input name="height" type="text" class="txt_put2" style="width:30px;" maxlength="5" value ="'.iset($Rs['I_height']).'" />','name'=>'宽 * 高','tip'=>'单位：像素');

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
