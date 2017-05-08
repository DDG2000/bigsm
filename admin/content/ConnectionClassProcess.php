<?php
/**
* 模块：基础模块
* 描述：权限处理页
* 作者：张绍海
*/ 

//引入根目录下include里面的TopFile.php文件
require_once('../include/TopFile.php');
//$Admin->CheckPopedoms('SC_SITE_CONTENTCLASS_MDY');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
if(!$Config->Link)
{
	$DataBase->OpenDataBase();
}
switch($Work)
{
	case 'MdyReco': /**修改记录**/
		 $Id = $FLib->RequestInt('Id',0,9,'ID');
		 $parent = $FLib->RequestInt('Parent',0,9,'父ID');
		 $name=$FLib->RequestChar('name',1,50,'参数',1);
		 //$ispicture=$FLib->RequestInt('ispicture',0,9,'显示图片');
		 //$isadd=$FLib->RequestInt('isadd',0,9,'增减文章');
		 $order=$FLib->RequestInt('order',100,9,'排序号');
		 $intro=$FLib->RequestChar('intro',1,50,'备注信息',1);
		 if($Id == 0)
		 {
		 	echo $FLib ->Alert('参数错误','self','BACK');
			exit;
		 }
		 //echo $name; echo $Id; echo $parent;die();
		 $Re = $DataBase->SelectSql('select  ID FROM es_link_class where Vc_name=\''. $name . '\' And Status<>0 and ID<>'.$Id.' and I_partentID='.$parent);
		 if($DataBase->GetResultRows($Re) > 0)
		 {
		 	echo $FLib ->Alert('类别名已存在!','self','BACK');
			exit;
		 }
		 $creator=$Admin->GetAdminInfo($Admin->Uid,1) ;
		 $DataBase->QuerySql("update es_link_class set I_partentID='$parent',Vc_name='$name',I_operatorID='$Admin->Uid',Vc_intro='$intro',I_order=$order where ID=$Id");
		 $Admin ->AddLog('网站管理','修改友情链接分类：其Id为：'.$Id );
    	 echo $FLib ->Alert('修改友情链接分类修改完毕','self','hidden');
         break;
	case 'AddReco': /**增加记录**/
		 $parent = $FLib->RequestInt('Parent',0,9,'父ID');
		 $name=$FLib->RequestChar('name',1,50,'参数',1);
		// $ispicture=$FLib->RequestInt('ispicture',0,9,'显示图片');
		 //$isadd=$FLib->RequestInt('isadd',0,9,'增减文章');
		 $order=$FLib->RequestInt('order',100,9,'排序号');
		 $intro=$FLib->RequestChar('intro',1,50,'备注信息',1);
		 $Re = $DataBase->SelectSql('select  ID FROM es_link_class where Vc_name=\''. $name . '\' And Status<>0');
		 if($DataBase->GetResultRows($Re) > 0)
		 {
		 	echo $FLib ->Alert('类别名称已经存在!','self','BACK');
			exit;
		 }
		 $creator=$Admin->GetAdminInfo($Admin->Uid,1) ;
		 $DataBase->QuerySql("insert into es_link_class(Vc_name,I_partentID,Vc_intro,I_operatorID,I_order,Createtime) values('$name','$parent','$intro','$Admin->Uid',$order,now())");
		 $Admin ->AddLog('网站管理','增加友情链接分类：其名称为：'.$name);
    	 echo $FLib ->Alert('友情链接分类增加完毕','self','hidden');
         break;
    case 'DeleteReco': /**删除记录**/
		 $IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		 if(!$FLib->IsIdList($IdList))
		 {
		 	echo $FLib ->Alert('参数错误','self','BACK');
			exit;
		 }
		 $Idarray = explode(',',$IdList);
		 for($i=0;$i<count($Idarray);$i++)
		 {
		 	
		 	$Re = $DataBase->SelectSql('select * from es_link_class where Status<>0 And ID ='. $Idarray[$i]);
			if($DataBase->GetResultRows($Re) != 0)
			{
				$DataBase->QuerySql('update es_link_class set Status=0 where ID='. $Idarray[$i]);
			}
			else
			{
			    echo $FLib ->Alert('参数错误','self',$FLib->IsRequest('backurl'));
                exit;
			}
		 }
		 $Admin ->AddLog('网站管理','删除友情链接分类：其ID为：'.$IdList);
    	 echo $FLib ->Alert('友情链接分类删除完毕','self',$FLib->IsRequest('backurl'));
         break;
    default:
	echo $FLib ->Alert('参数错误!','self','BACK');
	exit;

}
$DataBase->CloseDataBase();
?>