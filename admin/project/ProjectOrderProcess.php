<?php
require_once('../include/TopFile.php');
require_once(WEBROOTINCCLASS.'Project.php');
require_once(WEBROOTINCCLASS.'ProjectOrder.php');
require_once(WEBROOTINCCLASS.'ProjectConsumptions.php');
require_once(WEBROOTINCCLASS.'OrderUtils.php');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$tname   = $FLib->RequestChar('tname',1,50,'参数',1);
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
$Admin->CheckPopedoms('SM_PROJECT_ORDER');
$project = new Project() ;
$projectOrder = new ProjectOrder($project) ;
$id = $FLib->RequestInt('id', 0, 10) ;
$pid = $FLib->RequestInt('pid', 0, 10) ;
$erpOdn = $FLib->RequestChar('erpOdn', 0, 30, 'ERP订单号', 1) ;
$projectData = $project->get($pid) ;
$data['Vc_erp_orderNO'] = $erpOdn ;
if (!$projectData) {
	echo showErr('参数错误'); exit;
}
switch ($Work) {
	case 'MdyReco':
		$projectOrder->update($data, "id=$id");
		echo showSuc('修改成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'AddReco':
		$data['Vc_orderNO'] = OrderUtils::getOrderNo() ;
		$data['I_projectID'] = $pid ;
		$projectOrder->saveData($data) ;
		echo showSuc('添加成功',$FLib->IsRequest('backurl'),$obj);
		break;
}
$DataBase->CloseDataBase();