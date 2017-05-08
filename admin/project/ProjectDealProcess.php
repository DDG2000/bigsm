<?php
/**
* 模块：基础模块
* 描述：图片修改页
* 作者：kign
*/
require_once('../include/TopFile.php');
require_once(WEBROOTINCCLASS.'Project.php');
$Admin->CheckPopedoms('SM_PROJECT_DEAL_MDY');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work){

case 'MdyReco': /**修改记录**/
	getvalue($FLib);
	$Id = $FLib->RequestInt('Id',0,9,'ID');
	$DataBase->QuerySql("UPDATE sm_project_deal SET Vc_company='$Vc_company', Vc_name='$Vc_name',N_reward='$N_reward',N_amount='$N_amount',I_active='$I_active' WHERE id=$Id");
	$Admin ->AddLog('成交项目管理','修改成交项目：其Id为：'.$Id );
	echo showSuc("成交项目修改成功",'ProjectDealList.php?status=',$obj);
	break;

case 'AddReco': /**添加记录**/   
	getvalue($FLib);
	$DataBase->QuerySql("INSERT INTO sm_project_deal (Vc_company,Vc_name,N_reward,N_amount,Createtime,I_active)	VALUES	('$Vc_company', '$Vc_name','$N_reward','$N_amount',$I_active,now())");
	$Admin ->AddLog('成交项目管理','增加成交项目：其名称为：'.$Vc_name);
	echo showSuc("成交项目添加成功",'ProjectDealList.php?status=',$obj);
	break;

case 'DeleteReco': /**删除记录**/
	$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
	if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	$DataBase->QuerySql('UPDATE sm_project_deal SET Status=0 WHERE ID IN('.$IdList.')');

	$Admin ->AddLog('成交项目管理','删除成交项目：其ID为：'.$IdList);
	echo showSuc('成交项目删除完毕',$FLib->IsRequest('backurl'),$obj);
	break;
} 

function getvalue($FLib){
	global $Vc_company,$Vc_name,$N_reward,$N_amount,$I_active;
	$Vc_company          = $FLib->RequestChar('Vc_company',0,50,'Vc_company',1);
	$Vc_name             = $FLib->RequestChar('Vc_name',1,50,'项目名称',1);
	$N_reward            = $FLib->RequestChar('N_reward',1,20,'年化回报',1);
	$N_amount            = $FLib->RequestChar('N_amount',1,20,'成交金额',1);
	$I_active      	   = $FLib->RequestInt('I_active',1,9,'活动');
}


$DataBase->CloseDataBase();   
 
?>       
    