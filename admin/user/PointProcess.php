<?php
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 用户积分 处理页
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER_POINT');

$tt = '用户积分';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);

$UserID  = $FLib->RequestInt('UserID',0,9,'UserID');
$cpoint  = $FLib->RequestInt('cpoint',0,9,'币值改变数');
$ctype   = $FLib->RequestChar('ctype',0,1,'改变意向',0);
$reason  = $FLib->RequestChar('reason',0,200,'原因',1);
$reason  = "管理员".$Admin->Uname."修改：".$reason;
if($cpoint<0){$cpoint=-$cpoint;}
$cpoint = $ctype.$cpoint;
if ($ctype == '-') {
	$tt .= '减少';
}else{
	$tt .= '增加';
}

$sql = "insert into user_point_record (I_userID,I_number,Vc_reason,Createtime) values($UserID,$cpoint,'$reason',now())";
$DataBase->QuerySql($sql);
$sql = "update user_base set I_point = I_point $cpoint where ID=$UserID";
$DataBase->QuerySql($sql);

$Admin ->AddLog('会员管理','积分管理：'.$UserID.'改变：'.$cpoint );
echo showSuc($tt.'成功',$FLib->IsRequest('backurl'),$obj);

$DataBase->CloseDataBase();
?>