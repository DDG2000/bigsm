<?php
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_PARTNER');
if(!$Config->Link){$DataBase->OpenDataBase();}
$Work   = $FLib->requestchar('Work',0,50,'参数',0);

$tt = '合作站点';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
{
	case 'AddReco': /***添加合作站点**/
	   $lname    = $FLib->requestchar('title',0,50,'合作站点名称',1);
	   $site     = $FLib->requestchar('site',0,100,'合作站点地址',1);
	   //$identity = $FLib->requestchar('identity',0,50,'合作站点标记',1);
	   $identity = 'e'.md5($lname);
	   $Re = $DataBase->SelectSql("select * from site_partner where Status<>0 And Vc_name='$lname' limit 0,1");
	   if($DataBase->GetResultRows($Re) > 0) { echo showErr('合作站点名称重复'); exit; }

	   $DataBase->querySql("insert into site_partner (Vc_name,Vc_site,Vc_identity,Createtime,I_operatorID) values('$lname','$site','$identity',now(),'$Admin->Uid')");
	   $Admin ->AddLog('站点管理','增加合作站点：其名称为：'.$lname );
       echo $FLib ->Alert('执行完毕','self','hidden');
	   echo showSuc($tt.'添加成功',$FLib->IsRequest('backurl'),$obj);
	   break;
	case 'MdyReco':  /***修改合作站点**/
	   $lname    = $FLib->requestchar('title',0,50,'合作站点名称',1);
	   $site     = $FLib->requestchar('site',0,100,'合作站点地址',1);
	   //$identity = $FLib->requestchar('identity',0,50,'合作站点标记',1);
	   $identity = 'e'.md5($lname);
	   $Id   = $FLib->requestint('Id',0,9,'ID');
	   $sql  = "select * from site_partner where Status<>0 And Vc_name='$lname' And ID<>$Id limit 0,1";
	   $Re = $DataBase->SelectSql($sql);
	   if($DataBase->GetResultRows($Re) > 0) { echo showErr('合作站点名称重复'); exit; }

	   $DataBase->querySql("update site_partner set Vc_name='$lname',Vc_site='$site',Vc_identity='$identity' where ID=$Id");
	   $Admin ->AddLog('站点管理','修改合作站点：其ID为：'.$Id );
	   echo showSuc($tt.'修改成功',$FLib->IsRequest('backurl'),$obj);
	   break;
    case 'DelReco':  /***删除合作站点**/
	   $IdList = $FLib->requestchar('IdList',0,100,'IdList',0);
	   if(!$FLib->isidlist($IdList)) { echo showErr('参数错误'); exit; }

	   $DataBase->querySql("update site_partner set Status=0 where ID in ($IdList)");
	   $Admin ->AddLog('站点管理','删除合作站点：其ID为：'.$IdList );
	   echo showSuc($tt.'删除成功',$FLib->IsRequest('backurl'),$obj);
	   break;

}
$DataBase->CloseDataBase();  
?>