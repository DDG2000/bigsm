<?php  
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER_GRADE');

$Work   = $FLib->requestchar('Work',1,50,'参数',1);
if(!$Config->Link)
{
	$DataBase->OpenDataBase();
}

$tt = '会员等级';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
{
	case 'MdyReco': /**修改记录**/
		$Id   = $GLOBALS['FLib']->requestint('Id',0,9,'用户Id');
		getvalue($FLib);
		$DataBase->querySql("update user_grade set N_pointdiscount='$N_pointdiscount' ,N_pricediscount='$N_pricediscount' ,Vc_name='$Vc_name' ,I_point='$I_point'  Where ID=$Id");
		echo showSuc($tt.'修改成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'AddReco': /**增加记录**/
		getvalue($FLib);
		$DataBase->querySql( "insert into user_grade (N_pointdiscount,N_pricediscount,I_point,Vc_name,Createtime) values ('$N_pointdiscount','$N_pricediscount','$I_point','$Vc_name',now())");
		echo showSuc($tt.'添加成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'DeleteReco': /**删除记录**/
		$IdList = $FLib->requestchar('IdList',0,100,'IdList',1);
		if(!$FLib->isidlist($IdList)) { echo showErr('参数错误'); exit; }
		$DataBase->querySql('UPDATE user_grade SET Status=0 WHERE ID IN('.$IdList.')');
		$Admin ->AddLog('会员管理','删除等级：其ID为：'.$IdList);
		echo showSuc($tt.'删除成功',$FLib->IsRequest('backurl'),$obj);
		break;
} 

function getvalue($FLib)
{
	global $N_pointdiscount,$N_pricediscount,$Vc_name,$I_point;

	$Vc_name      = $FLib->RequestChar('Vc_name',0,50,'身份',1);
	$I_point         = $FLib->RequestInt('I_point',0,50,'积分');
	$N_pointdiscount = $FLib->RequestChar('N_pointdiscount',0,50,'积分折扣',0);
	$N_pricediscount = $FLib->RequestChar('N_pricediscount',0,50,'价格折扣',0);
}

$DataBase->CloseDataBase();    
?>
