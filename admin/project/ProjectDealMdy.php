<?php
if(1){
/****************************************************************** 
**创建者：zy
**创建时间：2016-06-20
**本页： 成交项目 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');
require_once(WEBROOTINCCLASS.'Project.php');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$t = '成交项目';
$points = array('项目管理', '管理');
$action = 'ProjectDealProcess.php';
$hides  = array('Work'=>$Work, 'type'=>$type);
$params = array();
$helps  = array();

$project = new Project() ;

$Admin->CheckPopedoms('SM_PROJECT_DEAL');
switch($Work){
	case 'MdyReco':
		$id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $project->getDealProjectInfo($id) ;
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑'.$t;
		$hides['Id'] = $id;
		break;
	case 'AddReco':
		$Rs = array();
		$title = '添加';
		break;
	default:
		die('没有该操作类型!');	
}
$hides['pid'] = $pid;
$params['Vc_company'] = array('val'=>iset($Rs['Vc_company']),'name'=>'项目单位','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="20"');
$params['Vc_name'] = array('val'=>iset($Rs['Vc_name']),'name'=>'项目名称','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="20"');
$params['N_reward'] = array('val'=>iset($Rs['N_reward']),'name'=>'年化回报','tip'=>'单位 %','ty'=>'text','attrs'=>'isc="" maxlength="20"');
$params['N_amount'] = array('val'=>iset($Rs['N_amount']),'name'=>'成交金额','tip'=>'单位 万','ty'=>'text','attrs'=>'isc="" maxlength="20"');
if(isset($Rs['I_active']) && $Rs['I_active']==0){
	$status_var = '<input type="radio" name="I_active" value="1" >活动 <input type="radio" name="I_active" value="0" checked>禁用 ';
}else{
	$status_var = '<input type="radio" name="I_active" value="1" checked>活动 <input type="radio" name="I_active" value="0" >禁用 ';
}
$params['I_active'] = array('val'=>$status_var,'name'=>'状态','tip'=>'','ty'=>'radio','attrs'=>'');
if($t==''){$t='项目订单';}
$title.=$t;
$points[1]=$t.$points[1];

$points[] = $title;

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'action', $action );
$tpl->assign( 'hides', $hides );
	$tpl->assign( 'sType', $sType );
	$tpl->assign( 'sTypes', $sTypes );
$tpl->assign( 'params', $params );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('mdy'.$raintpl_ver);
exit;
}
?>
