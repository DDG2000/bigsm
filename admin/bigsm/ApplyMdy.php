<?php
require_once('../include/TopFile.php');
require_once(WEBROOTINCCLASS.'AuditManager.php');
require_once(WEBROOTINC.'ApplyCommon.php');
$Admin->CheckPopedoms('SC_AUDIT_AUDIT');
$batch = $Cfg->allowBatchAudit ;

$work   = $FLib->RequestChar('Work',1,50,'参数',1);
$batch = $FLib->RequestChar('batch',0,50,'批处理',0);

$I_entity = $FLib->RequestInt('I_entity',1,9,'分类');
if(!array_key_exists($I_entity, $entityarr)){ echo showErr('分类参数有误未找到'); exit; }

$t = $entityarr[$I_entity][0].'申请';
$points = array('审核管理', $t.'管理');
$action = 'ApplyProcess.php';
$hides  = array('Work'=>$work,'batch'=>$batch);
$params = array();
$helps  = array();
$extend = array();

$auditManager = new AuditManager($Admin,$entityarr,$I_entity,$Admin->AuditRule) ;
$typearr = $auditManager->getTypeArr();
$fullFlow = $auditManager->getFullFlow();
$id = 0 ;
switch ($work) {
	case 'MdyReco' :
		if ($batch) {
		} else {
			$id = $FLib->RequestInt('id',0,9,'id');
			$appda = $auditManager->getApplyById($id) ;
			if (!$appda) {
				echo showErr('记录未找到'); exit;
			}
			if(!array_key_exists($appda['I_flowID'], $typearr)){
				echo showErr(empty($typearr)?'还没有定义审核流程，请定义审核流程！':'您没有该审核权限'); exit;
			}
			$title = '处理'.$t;
			$hides['I_applyID'] = $id;
			$hides['I_entity'] = $I_entity;
			$hides['flowID'] = $appda['I_flowID'];
		}
		break;
	default:
		die('没有该操作类型!');
}

$logsa = array();
$logda = $auditManager->getAuditRecords($id) ;

foreach($logda as $v){
	$logsa[] = $v['Createtime'].' '.$v['uname'].' '.iset($statusarr[$v['I_status']],'--').' '
			.iset($fullFlow[$v['I_flowID']],'--').($v['Vc_intro']!=''?'<br/>备注意见:'.$v['Vc_intro']:'');
}

if ($batch) {
} else {
	$params[] = array('name'=>'任务标题','val'=>iset($appda['Vc_name']),'tip'=>'');
	$params[] = array('name'=>'任务说明','val'=>iset($appda['Vc_content']),'tip'=>'');
	$params[] = array('name'=>'审核记录','val'=>join('<br/>',$logsa),'tip'=>'');
}
if($auditManager->getLastFlow()==$appda['I_flowID']){
	$status_var = '<input type="radio" name="I_status" value="1" checked="checked">完成 ';
}else{
	$status_var = '<input type="radio" name="I_status" value="1" checked="checked">通过 ';
}
$status_var .= '<input type="radio" name="I_status" value="2">终止';
if($auditManager->getFirstFlow()!=$appda['I_flowID']) {
	$status_var .=' <input type="radio" name="I_status" value="3">退回' ;
}

$params['I_status'] = array('name'=>'审核意见','val'=>$status_var,'tip'=>'');
$params['Vc_intro'] = array('val'=>'','name'=>'备注','tip'=>'最多150字','ty'=>'textarea','attrs'=>'isc="MaxLen150" ennull=""');

$points[] = $title;

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