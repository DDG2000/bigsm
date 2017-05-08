<?php
/**
* 模块：基础模块
* 描述：系统日志处理页
*/ 
require_once('../include/TopFile.php');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
$Admin->CheckPopedoms('SC_SYS_TOOL_TRYLOGIN_MDY');

switch($Work )
{
	case 'DeleteReco':
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		if(!$FLib->IsIdList($IdList)){ echo showErr('参数错误');exit;}
		$DataBase->QuerySql('delete from sc_login_record where id in(' . $IdList . ')');
		$Admin->AddLog('系统管理','删除尝试登录记录：其ID为： '.$IdList);
		echo showSuc('尝试登录记录删除完毕',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'ClearReco':
		$DataBase->QuerySql('delete from sc_login_record ');
		$Admin->AddLog('系统管理','清空尝试登录记录!');
		echo showSuc('清空尝试登录记录完毕',$FLib->IsRequest('backurl'),$obj);
		break;

}

?>

