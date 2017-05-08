<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 网站介绍 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');
require_once(WEBROOTINCCLASS.'Project.php');
require_once(WEBROOTINCCLASS.'ProjectOrder.php');
require_once(WEBROOTINCCLASS.'ProjectConsumptions.php');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$t = '';
$points = array('项目订单', '管理');
$action = 'ProjectOrderProcess.php';
$hides  = array('Work'=>$Work, 'type'=>$type);
$params = array();
$helps  = array();

$project = new Project() ;

$Admin->CheckPopedoms('SM_PROJECT_ORDER');
$pid = $FLib->RequestInt('pid',0,9,'projectID');
$pj = $project->get($pid) ;
if(!$pj){ echo showErr('项目未找到'); exit; }
$rs = array();
$params[] = array('val'=>$pj['Vc_name'],'name'=>'项目名称') ; 
switch($Work){
	case 'MdyReco':
		$id = $FLib->RequestInt('Id',0,9,'ID');
		$rs = $projectOrder->get($id) ;
		if(!$rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑';
		$params[] = array('val'=>isset($rs['Vc_orderNO'])?$rs['Vc_orderNO']:'','name'=>'项目订单号') ; 
		$hides['id'] = $id;
		break;
	case 'AddReco':
		$title = '添加';
		break;
	default:
		die('没有该操作类型!');	
}
$hides['pid'] = $pid;
if($Work=='AddReco'){
}else{
}
$params['erpOdn'] = array('val'=>isset($rs['Vc_erp_orderNO']) ? $rs['Vc_erp_orderNO'] : '','name'=>'ERP订单号','ty'=>'text','tip'=>'ERP系统订单号','attrs'=>'isc="" maxlength="50"') ; 

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
$tpl->assign( 'params', $params );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('mdy'.$raintpl_ver);
exit;
}
?>
