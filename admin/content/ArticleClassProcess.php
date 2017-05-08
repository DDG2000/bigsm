<?php
/**
* 作者：
* 书写日期：
*/ 

//引入根目录下include里面的TopFile.php文件
require_once('../include/TopFile.php');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);

$Admin->CheckPopedoms('SC_SITE_ARTICLE_CLASS_MDY');
switch($Work)
{
	case 'MdyReco': /**修改记录**/
		 $Id = $FLib->RequestInt('Id',0,9,'ID');
		 $classID = $FLib->RequestInt('classID',0,9,'父ID');
		 $name=$FLib->RequestChar('name',1,50,'分类',1);
		 $ispicture=$FLib->RequestInt('ispicture',0,9,'显示图片');
		 $isadd=$FLib->RequestInt('isadd',0,9,'增减文章');
		 $order=$FLib->RequestInt('order',100,9,'排序号');
		 $intro=$FLib->RequestChar('intro',1,50,'备注信息',1);

		 $Re = $DataBase->SelectSql('select  ID FROM es_article_class where Vc_name=\''. $name . '\' And Status<>0 And I_partentID= 3 and ID<>'.$Id);
		 if($DataBase->GetResultRows($Re) > 0) { echo showErr('分类名称已经存在'); exit; }
		 $DataBase->QuerySql("update es_article_class set I_partentID='$classID',Vc_name='$name',I_picture='$ispicture',I_add='$isadd',Vc_intro='$intro',I_order=$order where ID=$Id");
		 $Admin ->AddLog('内容管理','修改分类名称：其Id为：'.$Id );
		echo showSuc('分类修改成功',$FLib->IsRequest('backurl'),$obj);
         break;
	case 'AddReco': /**增加记录**/
		 $classID = $FLib->RequestInt('classID',0,9,'父ID');
		 $name=$FLib->RequestChar('name',1,50,'分类',1);
		 $ispicture=$FLib->RequestInt('ispicture',0,9,'显示图片');
		 $isadd=$FLib->RequestInt('isadd',0,9,'增减文章');
		 $order=$FLib->RequestInt('order',100,9,'排序号');
		 $intro=$FLib->RequestChar('intro',1,50,'备注信息',1);
		 $Re = $DataBase->SelectSql('select  ID FROM es_article_class where Vc_name=\''. $name . '\' And I_partentID= 3 And Status<>0');
		 if($DataBase->GetResultRows($Re) > 0) { echo showErr('分类名称已经存在'); exit; }
		 $DataBase->QuerySql("insert into es_article_class(Vc_name,I_partentID,Vc_intro,I_operatorID,I_picture,I_add,I_order,Createtime) values('$name','$classID','$intro',$Admin->Uid,'$ispicture','$isadd',$order,now())");
		 $Admin ->AddLog('内容管理','增加分类名称：其名称为：'.$name);
		echo showSuc('分类增加成功',$FLib->IsRequest('backurl'),$obj);
         break;
    case 'DeleteReco': /**删除记录**/
		 $IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		 if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }

		 $DataBase->QuerySql('update es_article_class set Status=0 where ID in ('. $IdList.')');

		 $Admin ->AddLog('内容管理','删除分类名称：其ID为：'.$IdList);
		echo showSuc('分类删除成功',$FLib->IsRequest('backurl'),$obj);
         break;

}
$DataBase->CloseDataBase();
?>