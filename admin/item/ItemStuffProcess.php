<?php
/****************************************************************** 
**创建者：sakura
**创建时间：2014-11-04
**本页： 用户类型 管理
**说明：
******************************************************************/
error_reporting(0);
set_time_limit(0);
require_once('../include/TopFile.php'); 
require(WEBROOTINC.'ExcelImport.php');

require(WEBROOTINCCLASS.'ItemStuff.php');
$Admin->CheckPopedoms('SM_ITEM_STUFF');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
if(!$Config->Link)
{
	$DataBase->OpenDataBase();
}
$mallclass='钢材市场';
$I_mall_classID=1;//钢材市场id
$tt = '材质管理';
$t2 = '材质';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
$ItemOb = new ItemStuff();
switch($Work)
 {
 	case 'MdyReco': /**编辑**/
		 $Admin->CheckPopedoms('SM_ITEM_STUFF_MDY');
	     GetValue($FLib);
		 $Id = $FLib->RequestInt('Id',0,9,'ID');
		
		 //$DataBase->QuerySql("update sm_item_class set Vc_name='$name',I_order='$order' where id='$Id'");
		 $ItemOb->edit($name, $order, $Id,$itemclassid);
		 $r = createitemclasscache();
// 		 $msg = $r['flag']<1 ? ',生成失败!'.$r['err']:'';
		 $msg = $r['flag']<1 ? ',生成失败!':'';
		 $Admin ->AddLog($mallclass.$tt,'修改'.$t2.'：其Id为：'.$Id );
		 echo showSuc($tt.'编辑成功'.$msg,$FLib->IsRequest('backurl'),$obj);
         break;
	case 'AddReco': /**增加**/
		 $Admin->CheckPopedoms('SM_ITEM_STUFF_MDY');
		 GetValue($FLib);
		 
		 $ItemOb->add($I_mall_classID, $name, $order,$itemclassid);
		 
		 $r = createitemclasscache();
		 
		 $msg = $r['flag']<1 ? ',生成失败!':'';
		 $Admin ->AddLog($mallclass.$tt,'增加'.$t2.'：其名称为：'.$name);
		 
		 echo showSuc($tt.'添加成功'.$msg,$FLib->IsRequest('backurl'),$obj);
         break;
    case 'ImportReco': /**导入增加**/
              
             $Admin->CheckPopedoms('SM_ITEM_STUFF_MDY');
         
             if (! empty ( $_FILES ['file_stu'] ['name'] ))
             {
                 $tmp_file = $_FILES ['file_stu'] ['tmp_name'];
                 $file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
                 $file_type = $file_types [count ( $file_types ) - 1];
                 /*判别是不是.xls文件，判别是不是excel文件*/
                 if (strtolower ( $file_type ) != "xls")
                 {
                     $msg = '不是后缀为xls的Excel文件，请重新上传';
                     echo showErr($msg);
                     exit;
                 }
         
                 /*设置上传路径*/
                 $savePath = WEBROOT . 'data/upfile/excel/';
                 /*以时间来命名上传的文件*/
                 $str = date ( 'Ymdhis' );
                 $file_name = $str . "." . $file_type;
                 /*是否上传成功*/
                 if (! copy ( $tmp_file, $savePath . $file_name ))
                 {
         
                     $msg = '上传失败';
                     echo showErr($msg);
                     exit;
                 }
                 /*
                  *对上传的Excel数据进行处理生成编程数据,ExcelToArray类中
                  *这里调用执行了类里面的read函数，把Excel转化为数组并返回给$res,再进行数据库写入
                  */
                 $excel = new ExcelImport();
                 $res = $excel->read( $savePath . $file_name );
                 //   array_shift($res);//去除首行标题栏
                 //                  		     var_dump($res);
                 //                  		     exit();S
         
                 /*对生成的数组进行数据库的写入*/
                 foreach ( $res as $k => $v )
                 {
                     if ($k != 0)
                     {
                         if($k>1){
                              
                             $itemclassid = intval($v[0]);
                             $name = trim($v[1]);
                             $order = intval($v[2]);
                             // $result=$ItemClass->addItemClass($I_mall_classID, $name, $order);
                             $result=$ItemOb->add($I_mall_classID, $name, $order,$itemclassid);
                             $Admin ->AddLog($mallclass.$tt,'增加'.$t2.'：其名称为：'.$name);
                             if (! $result)
                             {
                                 $msg = '导入数据库失败';
                                 echo showErr($msg);
                                 exit;
                             }
                         }
         
                     }
                 }
                  
                 if(is_file($savePath.$file_name)){
                     chmod($savePath.$file_name,0777);
                     // echo "文件存在！已经删除!--您可以重新上传文件";
                     if(!unlink($savePath.$file_name)){
                         $msg = '删除上传excel失败,文件权限问题';
                         echo showErr($msg);
                         exit;
                     }
                      
                 }
         
                 $r = createitemclasscache();
                 $msg = $r['flag']<1 ? ',生成失败!':'';
                 echo showSuc($tt.'添加成功'.$msg,$FLib->IsRequest('backurl'),$obj);
                 break;
                  
             }else {
                  
                 $msg = '未选择任何文件';
                 echo showErr($msg);
                 break;
                 exit;
                  
             }    
         
         
         
         
         
    case 'DeleteReco': /**删除**/
		 $Admin->CheckPopedoms('SM_ITEM_STUFF_MDY');
		 $IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		 if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		 //$DataBase->QuerySql('update sm_item_class set Status=0 where ID in('.$IdList.')');
	     $ItemOb->deleteItem($IdList);
	
		 $r = createitemclasscache();
// 		 $msg = $r['flag']<1 ? ',生成失败!'.$r['err']:'';
		 $msg = $r['flag']<1 ? ',生成失败!':'';
		 $Admin ->AddLog($mallclass.$tt,'删除'.$t2.'：其ID为：'.$IdList);
		 echo showSuc($tt.'删除成功'.$msg,$FLib->IsRequest('backurl'),$obj);
		
         break;
    default:
	echo $FLib ->Alert('参数错误!','self','BACK');
	exit;
 } 

function GetValue($FLib)
{
	global $name,$order,$itemclassid;
    $name = trim($FLib->requestchar('name',0,100,'标题',1));
	$order = $FLib->requestint('order',0,10,'序号',1);
	$itemclassid = $FLib->requestint('itemclasslist',0,10,'所属分类id',1);
}

function createitemclasscache(){
    
    global $DataBase;
    $itemStuffArray = $DataBase->fetch_all_assoc("select * from sm_item_stuff where status=1 and I_mall_classID=1  order by I_order asc") ;
    $data = CacheManager::saveCache(CACHE_ITEM_STUFF, $itemStuffArray) ;
    if($data==false){
        return array('flag'=>0);
    }
    return array('flag'=>1);
}




$DataBase->CloseDataBase();   
?>
