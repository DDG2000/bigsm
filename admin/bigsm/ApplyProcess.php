<?php
require_once('../include/TopFile.php');
require_once(WEBROOTINCCLASS.'AuditManager.php');
require_once(WEBROOTINC.'ApplyCommon.php');

$Admin->CheckPopedoms('SC_AUDIT_AUDIT');
$work   = $FLib->requestchar('Work',0,50,'参数',0);

$I_entity = $FLib->RequestInt('I_entity',1,9,'分类');//都提交
$flowID = $FLib->RequestInt('flowID',0,9,'当前审核步骤');//都提交
$I_status = $FLib->RequestInt('I_status',1,9,'审核意见');//单个任务是提交
$Vc_intro = $FLib->RequestChar('Vc_intro',1,500,'备注',1);//单个任务是提交
$I_applyID = $FLib->RequestInt('I_applyID',0,9,'任务ID');//单个任务是提交
$IdList = $FLib->RequestChar('IdList',1,0,'IdList',0);//多个任务提交
if(!$FLib->isidlist($IdList)) { $IdList=$I_applyID; }
$appida = explode(',', $IdList);
if(!array_key_exists($I_entity, $entityarr)){ echo showErr('分类参数有误未找到'); exit; }

$auditManager = new AuditManager($Admin,$entityarr,$I_entity,$Admin->AuditRule) ;
$typearr = $auditManager->getTypeArr();

$tt = $entityarr[$I_entity][0].'申请';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
$appCountAll = count($appida) ; $appCountSucc = 0 ; $appCountErr = 0 ;

$msg = $tt ;

//=======当前用户审核流程集合================
$AuditRule = $Admin->AuditRule;
$data['I_entity'] = $I_entity;
$data['I_status'] = $I_status;
$data['Vc_intro'] = $Vc_intro;
$data['I_operatorID'] = $Admin->Uid;
$data['Createtime@'] = 'now()';

//=======单个任务提交时根据选择转成其他处理====================
if($work=='MdyReco'){
	if($I_status==1){
		$work='PassReco';
	}elseif($I_status==2){
		$work='StopReco';
	}elseif($I_status==3){
		$work='ReturnReco';
	}
}
switch ($work) {
	case 'FinishReco':  /***完成**/
	case 'PassReco':  /***通过**/
		foreach ($appida as $appid) {
			countResult($auditManager->next($appid,$Vc_intro)) ;
		}
		$msg .= ($work == 'PassReco' ? '通过' : '完成') ;
		break;
	case 'StopReco':  /***终止**/
		foreach ($appida as $appid) {
			countResult($auditManager->reject($appid,$Vc_intro)) ;
		}
		$msg .= '终止' ;
		break;
	case 'ReturnReco':  /***退回**/
		foreach ($appida as $appid) {
			countResult($auditManager->goback($appid,$Vc_intro)) ;
		}
		$msg .= '退回' ;
		break;
}

$msg .=  "审核，$appCountSucc 个成功，$appCountErr 个失败" ;
$Admin ->AddLog('审核管理',$msg.'：其ID为：'.join(',',$appida) ) ;
echo showSuc($msg,$FLib->IsRequest('backurl'),$obj);

function countResult ($result) {
	global $appCountErr,$appCountSucc ;
	$result['code'] == AuditManager::$AUDIT_CODE_SUCCESS ? $appCountSucc ++ : $appCountErr ++ ;
}