<?php
/****************************************************************** 
**创建者：zhi
**创建时间：2013-10-18
**本页： 意见反馈信息编辑
**说明：
******************************************************************/

require('./../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_OPINION');

$userobj= new User();

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);

switch($Work)
{
	 case 'ShowReco': /**删除记录**/
		 $IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		 if(!$FLib->IsIdList($IdList))
		 {
		 	echo showErr('参数错误');
			exit;
		 }
		 $DataBase->QuerySql('update fp_feedback set I_show=1,I_showID='.$Admin->Uid.",Dt_show=now() where ID IN({$IdList})");
		 $Admin ->AddLog('网站管理','显示意见反馈：其ID为：'.$IdList);
		 echo showSuc('显示执行完毕',$FLib->IsRequest('backurl'),$obj);
         break;
	
    case 'DeleteReco': /**删除记录**/
		 $IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		 if(!$FLib->IsIdList($IdList))
		 {
		 	echo showErr('参数错误');
			exit;
		 }
		 $DataBase->QuerySql("update fp_feedback set Status=0 where ID IN({$IdList})");
		 $Admin ->AddLog('网站管理','删除意见反馈：其ID为：'.$IdList);
		 echo showSuc('删除完毕',$FLib->IsRequest('backurl'),$obj);
         break;
	 case 'AuditReco': /**删除记录**/
		 $IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		 if(!$FLib->IsIdList($IdList))
		 {
		 	echo showErr('参数错误');
			exit;
		 }
		 $DataBase->QuerySql('update fp_feedback set I_audit=1,I_auditID='.$Admin->Uid.",Dt_audit=now() where ID IN({$IdList})");
		 $Admin ->AddLog('网站管理','审核意见反馈：其ID为：'.$IdList);
		 echo showSuc('审核完毕',$FLib->IsRequest('backurl'),$obj);
         break;
	case 'reply': /**回复**/
		$Id        = $FLib->RequestInt('Id',0,9,'');
		$rcontent  = $FLib->RequestChar('rcontent',0,500,'回复内容',1);
		if(!$Id) { echo showErr('参数错误'); exit; }
		if(trim($rcontent)==''){ echo showErr('回复内容不能为空！'); exit;}

		$r = $DataBase->fetch_one("select * from fp_feedback where id={$Id}");
		if($r['Vc_title']!=''){ echo showErr('已回复不可再回复！'); exit;}

		
		$r = $userobj->replyFeedback($rcontent, $Id);
		if($r['flag']<1){ echo showErr('错误：'.$r['msg']); exit; }

		$Admin ->AddLog('网站管理','回复意见反馈：其ID为：'.$Id);
		echo showSuc('回复完毕',$FLib->IsRequest('backurl'),$obj);
		break;
}



$DataBase->CloseDataBase();
?>