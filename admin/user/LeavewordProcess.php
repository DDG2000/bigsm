<?php
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER_LEAVEWORD_MDY');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$pt='留言';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work){
	
	case 'DeleteReco': /**删除记录**/
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		$DataBase->QuerySql('update user_leaveword set Status=0 where ID in('. $IdList.')');

		$Admin ->AddLog('会员管理','删除'.$pt.'：其ID为：'.$IdList);
		echo showSuc($pt.'删除完毕',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'ShowReco': 
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		$DataBase->QuerySql("UPDATE user_leaveword SET I_display=1 WHERE ID IN({$IdList})");

		$Admin ->AddLog('会员管理','显示'.$pt.'：其ID为：'.$IdList);
		echo showSuc($pt.'执行完毕',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'reply': 
		$Id        = $FLib->RequestInt('Id',0,9,'');
		$bid       = $FLib->RequestInt('bid',0,9,'');
		$rcontent  = $FLib->RequestChar('rcontent',0,0,'回复内容',1);
		if(!$Id) { echo showErr('参数错误'); exit; }
		$uid=$Admin->Uid;
		$DataBase->QuerySql("UPDATE user_leaveword SET I_operatorID={$uid},I_deal=1 WHERE ID=$Id");
        if($bid){
		$DataBase->QuerySql("UPDATE user_leaveword_reply SET Vc_content='{$rcontent}',I_operatorID={$uid} WHERE ID={$bid}");
		}else{
	    $DataBase->QuerySql("insert into user_leaveword_reply (Vc_content,I_operatorID,I_leavewordID,Createtime) values('{$rcontent}',{$uid},{$Id},now())");
		}
		$Admin ->AddLog('会员管理','回复留言：其ID为：'.$Id);
		echo showSuc($pt.'回复完毕',$FLib->IsRequest('backurl'),$obj);
		break;
}
$DataBase->CloseDataBase();
?>