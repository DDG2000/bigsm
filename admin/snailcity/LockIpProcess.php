<?php
/**
* 模块：基础模块
* 描述：用户修改密码和添加用户和 修改用户登，具体实现是根据接收到的$Work值来判断的
* 作者：张绍海
*/

//引入根目录下include里面的TopFile.php文件
require_once"../include/TopFile.php";
$Admin->CheckPopedoms('SC_SYS_TOOL_ISLOCKIP_MDY');
$Work   = $FLib->RequestChar('Work',0,50,'参数',1);
$Status  = $FLib->RequestInt('Status',0,9,'类型');
$startIP = $FLib->RequestChar('start',0,15,'起始IP',1);
$endIP   = $FLib->RequestChar('end',0,15,'截止IP',1);

if (!$FLib->CheckIP($startIP)) { echo showErr('起始IP格式有误'); exit; }
if (!$FLib->CheckIP($endIP)) { echo showErr('截止IP格式有误'); exit; }
$startIP = $FLib->IPEncode($startIP);
$endIP = $FLib->IPEncode($endIP);
if ($startIP>$endIP) { echo showErr('截止IP小于开始ip'); exit; }

$table = $Status==1 ?'sc_lockip':'sc_allowip';
$tip = $Status==1 ?'IP黑名单':'IP白名单';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
{
	case 'AddReco': /***添加IP**/
		$Re = $DataBase->SelectSql("select * from {$table} where Status<>0 And Vc_startIP='$startIP' limit 0,1");
		if($DataBase->GetResultRows($Re) > 0) { echo showErr('起始IP重复'); exit; }
		$Re = $DataBase->SelectSql("select * from {$table} where Status<>0 And Vc_endIP='$endIP' limit 0,1");
		if($DataBase->GetResultRows($Re) > 0) { echo showErr('截止IP重复'); exit; }
		$DataBase->QuerySql("insert into {$table} (Vc_startIP,Vc_endIP,I_operatorID,Createtime) values('$startIP','$endIP',$Admin->Uid,now())");
		$Admin ->AddLog('系统工具','增加'.$tip.'：其名称为：'.$startIP );
	    echo showSuc($tip.'增加完毕',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'MdyReco':  /***修改IP**/
	   $Id   = $FLib->RequestInt('Id',0,9,'ID');
	   $sql  = "select * from {$table} where Status<>0 And Vc_startIP='$startIP' And ID<>$Id limit 0,1";
	   $Re = $DataBase->SelectSql($sql);
	   if($DataBase->GetResultRows($Re) > 0)  { echo showErr('起始IP重复'); exit; }
	   $Re = $DataBase->SelectSql("select * from {$table} where Status<>0 And Vc_endIP='$endIP' And ID<>$Id limit 0,1");
	   if($DataBase->GetResultRows($Re) > 0)  { echo showErr('截止IP重复'); exit; }
	   $DataBase->QuerySql("update {$table} set Vc_startIP='$startIP',Vc_endIP='$endIP' where ID=$Id");
	   $Admin ->AddLog('系统工具','修改'.$tip.'：其名称为：'.$Id );
	   echo showSuc($tip.'修改完毕',$FLib->IsRequest('backurl'),$obj);

	   break;
    case 'DelReco':  /***删除IP**/
	   $IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
	   if(!$FLib->IsIdlist($IdList)) { echo showErr('参数错误'); exit; }
	   $DataBase->QuerySql("update {$table} set Status=0 where ID in ($IdList)");
	   $Admin ->AddLog('系统工具','删除'.$tip.'：其名称为：'.$Id );
	   echo $FLib ->Alert('执行完毕','self',$FLib->IsRequest('backurl'));
	   echo showSuc($tip.'删除完毕',$FLib->IsRequest('backurl'),$obj);
	   break;

}
$DataBase->CloseDataBase();
?>