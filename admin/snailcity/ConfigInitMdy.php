<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 初始化参数
**说明：
******************************************************************/
require_once('../include/TopFile.php');
require_once('ConfigCommon.php');
$Admin->CheckPopedoms('SC_SITE_CONFIG_INIT');

//use cache
if($raintpl_cache && $cache = $tpl->cache('mdy', 60, 1) ){echo $cache;	exit;}

$t='参数';
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$points = array('网站管理', $t.'管理');
$action = 'ConfigInitProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();

switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne("SELECT * FROM site_parameter WHERE id=" . $Id);
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		break;
	case 'AddReco':
		$gp = $FLib->RequestInt('gp',0,9,'分组');
		$Rs = array();
		$Rs['Vc_name']='cfg_';
		$Rs['Vc_type']='string';
		$Rs['I_group']=$gp;
		$Rs['I_show']=1;
		$Rs['I_order']=100;
		$title = '添加'.$t;
		break;
	default:
		die('没有该操作类型!');	
}
$groups = array();
foreach($grouparr as $k=>$v){$groups[$k]=$v[1];}
$params[] = array('val'=>fn_select('I_group',iset($Rs['I_group']),$groups),'name'=>'参数组','tip'=>'','attrs'=>'');
$params['Vc_name'] = array('val'=>iset($Rs['Vc_name']),'name'=>'参数名','tip'=>'必须以cfg_开头','ty'=>'text','attrs'=>'isc="" maxlength="50"');
$types = array('number'=>'数字','bool'=>'布尔','string'=>'字符串','bstring'=>'html数据');
//$params[] = array('val'=>fn_radio('Vc_type',$Rs['Vc_type'],$types),'name'=>'参数类型','tip'=>'','ty'=>'','attrs'=>'isc="" maxlength="50"');
$shows = array(1=>'可见',0=>'不可见');
$params[] = array('val'=>fn_radio('I_show',$Rs['I_show'],$shows),'name'=>'是否显示','tip'=>'在用户参数设置总是否显示','ty'=>'','attrs'=>'isc=""');
$params['Vc_value'] = array('val'=>iset($Rs['Vc_value']),'name'=>'参数值','tip'=>'','ty'=>'textarea','attrs'=>'isc="" maxlength=""');
$params['Vc_intro'] = array('val'=>iset($Rs['Vc_intro']),'name'=>'参数说明','ty'=>'text','attrs'=>'maxlength="100"');
$params['Vc_tip'] = array('val'=>iset($Rs['Vc_tip']),'name'=>'参数Tip','tip'=>'支持HTML标签','ty'=>'textarea','attrs'=>'maxlength="500"');
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