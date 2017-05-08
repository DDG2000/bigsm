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
$Admin->CheckPopedoms('SC_MEMBER_CLASS');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
if(!$Config->Link)
{
	$DataBase->OpenDataBase();
}

$tt = '会员类型';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
 {
 	case 'MdyReco': /**编辑邮件**/
		 $Admin->CheckPopedoms('SC_MEMBER_CLASS_MDY');
	     GetValue($FLib);
		 $Id = $FLib->RequestInt('Id',0,9,'ID');
		 $DataBase->QuerySql("update user_class set Vc_name='$name',I_order='$order' where id='$Id'");
		 
		 $r = createuserclasscache();
		 $msg = $r['flag']<1 ? ',生成失败!'.$r['err']:'';
		 
		 $Admin ->AddLog('会员类型管理','修改类型：其Id为：'.$Id );
		 echo showSuc($tt.'编辑成功'.$msg,$FLib->IsRequest('backurl'),$obj);
         break;
	case 'AddReco': /**增加邮件**/
		 $Admin->CheckPopedoms('SC_MEMBER_CLASS_MDY');
		 GetValue($FLib);
		 $DataBase->QuerySql("insert into user_class(Vc_name,I_order,Createtime,Status) values
		 ('$name','$order',now(),1)");
		 
		 $r = createuserclasscache();
		 $msg = $r['flag']<1 ? ',生成失败!'.$r['err']:'';
		 
		 $Admin ->AddLog('会员类型管理','增加类型：其名称为：'.$name);
		 echo showSuc($tt.'添加成功'.$msg,$FLib->IsRequest('backurl'),$obj);
         break;
    case 'DeleteReco': /**删除邮件**/
		 $Admin->CheckPopedoms('SC_MEMBER_CLASS_MDY');
		 $IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		 if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		 $DataBase->QuerySql('update user_class set Status=0 where ID in('.$IdList.')');
		 
		 $r = createuserclasscache();
		 $msg = $r['flag']<1 ? ',生成失败!'.$r['err']:'';
		 
		 $Admin ->AddLog('会员类型管理','删除类型：其ID为：'.$IdList);
		 echo showSuc($tt.'删除成功'.$msg,$FLib->IsRequest('backurl'),$obj);
		
         break;
    default:
	echo $FLib ->Alert('参数错误!','self','BACK');
	exit;
 } 

function GetValue($FLib)
{
	global $name,$order;
    $name = $FLib->requestchar('name',0,100,'标题',1);
	$order = $FLib->requestint('order',0,10,'序号',1);
}

function createuserclasscache(){
	global $DataBase;
	$fname = WEBROOTDATA.'userclass.cache.inc.php';
	$rn = "\r\n";
	$str = '<?php'.$rn;
	$str .= '$da_userclass=array(';//.$rn
	$da = $DataBase->fetch_all("select * from user_class where status>0 order by I_order desc");
	foreach($da as $k=>$v){
		$str .= $rn.($k>0? ',':'');
		$str .= $v['ID'].'=>array(\'Vc_name\'=>\''.str_replace("'",'',$v['Vc_name']).'\')';
	}
	$str .= $rn.');';
	$r = writeincdata($str, $fname);
	if($r[0]<1){
		return array('flag'=>0,'err'=>$r[1]);
	}
	return array('flag'=>1);
}
$DataBase->CloseDataBase();   
?>
