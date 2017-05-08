<?php
require_once('../include/TopFile.php'); 
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
if(!$Config->Link)
{
	$DataBase->OpenDataBase();
}

$tt = '邮件发送记录';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
{
	case 'DeleteReco': /**删除**/
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		$DataBase->QuerySql('update site_email_record set Status=0 where ID in('.$IdList.')');
		$Admin ->AddLog('会员管理','删除邮件：其ID为：'.$IdList);
		echo showSuc($tt.'删除成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'ClearReco': /**清空**/
		//$DataBase->QuerySql('update site_email_record set Status=0 ');
		$DataBase->querySql('delete from site_email_record ');
		$Admin ->AddLog('会员管理','清空邮件');
		echo showSuc($tt.'清空成功',$FLib->IsRequest('backurl'),$obj);
		break;
}
$DataBase->CloseDataBase();   
?>
