<?php
require_once('../include/TopFile.php');
require_once(WEBROOTINCCLASS.'AuditManager.php');
require_once(WEBROOTINC.'ApplyCommon.php');
$Admin->CheckPopedoms('SC_AUDIT_AUDIT');
$batch = $Cfg->allowBatchAudit ;
$id = $FLib->RequestInt('Id',0,9,'ID');
$I_entity = $FLib->RequestInt ( 'I_entity', 1, 9, '申请分类' );
$auditManager = new AuditManager($Admin,$entityarr,$I_entity,$Admin->AuditRule) ;
$applyInfo = $auditManager->getApplyById($id) ;
$auditRecords = $auditManager->getAuditRecords($id) ;
$fullFlow = $auditManager->getFullFlow();
$logsa = array();
foreach($auditRecords as $v){
	$logsa[] = $v['Createtime'].' '.$v['uname'].' '.iset($statusarr[$v['I_status']],'--').' '
			.iset($fullFlow[$v['I_flowID']],'--').($v['Vc_intro']!=''?'<br/>备注意见:'.$v['Vc_intro']:'');
}

$title = $entityarr[$I_entity][0].'详细页';
$points = array('审核管理', $title);
$hides  = array();
$params = array();
$helps  = array();
$extend = array();

if(!$applyInfo){ echo showErr('记录未找到'); exit; }
$params[] = array('name'=>'任务标题','val'=>$applyInfo['Vc_name'],'tip'=>'');
$params[] = array('name'=>'任务说明','val'=>$applyInfo['Vc_content'],'tip'=>'');
$params[] = array('name'=>'审核记录','val'=>join('<br/>',$logsa),'tip'=>'');
$params[] = array('name'=>'当前审核','val'=>$fullFlow[$applyInfo['I_flowID']],'tip'=>'');
$params[] = array('name'=>'任务状态','val'=>getApplyStatus($applyInfo['I_status']),'tip'=>'');
$params[] = array('name'=>'创建时间','val'=>$applyInfo['Createtime'],'tip'=>'');

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
