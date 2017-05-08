<?php 
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER_POINT');
$Work   = $FLib->requestchar('Work',1,50,'参数',1);

$tt = '积分修改记录';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
{
	case 'DeleteReco': /**删除记录**/
		$IdList = $FLib->requestchar('IdList',0,100,'IdList',1);
		if(!$FLib->isidlist($IdList)) { echo showErr('参数错误'); exit; }
		$DataBase->querySql('UPDATE user_point_record SET Status=0 WHERE ID IN('.$IdList.')');
		$Admin ->AddLog('积分记录管理','删除积分记录：其ID为：'.$IdList);
		echo showSuc($tt.'删除成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'ClearReco': /**清空记录**/
		$DataBase->querySql('delete from user_point_record ');
		$Admin ->AddLog('积分记录管理','清空积分记录');
		echo showSuc($tt.'清空成功',$FLib->IsRequest('backurl'),$obj);
		break;
} 
$DataBase->CloseDataBase();    
?>
