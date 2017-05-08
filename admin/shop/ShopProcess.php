<?php

error_reporting(0);
set_time_limit(0);
require_once('../include/TopFile.php'); 
require(WEBROOTINCCLASS.'Shop.php');
$Admin->CheckPopedoms('SM_SHOP_LIST');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
if(!$Config->Link)
{
	$DataBase->OpenDataBase();
}

$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
$Shop = new Shop();
switch($Work)
 {
 	case 'MdyReco': /**编辑**/
		 $Admin->CheckPopedoms('SM_SHOP_MDY');
	    
		 $id = $FLib->RequestInt('Id',0,9,'ID');
		 $daArr['I_type'] = $FLib->RequestInt('I_type',0,9,'店铺类型');
// 		if($daArr['I_cert_status']==3){//如果认证通过，则更新开店时间 
// 		    $daArr['Dt_open@']='now()';
// 		}
		 //$DataBase->QuerySql("update sm_item_class set Vc_name='$name',I_order='$order' where id='$Id'");
		 $Shop->update($daArr, "id=$id");
		 echo showSuc('审核成功',$FLib->IsRequest('backurl'),$obj);
         break;
   
    default:
	echo $FLib ->Alert('参数错误!','self','BACK');
	exit;
 } 

// function GetValue($FLib)
// {
// 	global $name,$order,$itemclassid;
//     $name = trim($FLib->requestchar('name',0,100,'标题',1));
// 	$order = $FLib->requestint('order',0,10,'序号',1);
// 	$itemclassid = $FLib->requestint('itemclasslist',0,10,'所属分类id',1);
// }




$DataBase->CloseDataBase();   
?>
