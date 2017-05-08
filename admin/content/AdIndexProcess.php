<?php
/**
* 首页轮播 处理页
* 作者：kign
* 书写日期：2013-12-09
*/
require_once('../include/TopFile.php');
require_once('../include/File.class.php');
//require_once('../include/makeimage/makethumb.php');
$Admin->CheckPopedoms('SC_SITE_ADROLL');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work){
case 'MdyReco': /**修改记录**/
	getvalue($FLib);
	$Id = $FLib->RequestInt('Id',0,9,'ID');
	$sqlp = '';
//	$sql="UPDATE es_roll SET Vc_name='$title',Vc_url='$link',I_type='$type',Status='$status',I_order='$order' $sqlp WHERE id=$Id";
//	var_dump($_POST);
//	echo $sql;
//	exit;
	if(!empty($uploadfile)){ $sqlp .= ",Vc_image='$uploadfile'";}
	$DataBase->QuerySql("UPDATE es_roll SET Vc_name='$title',Vc_url='$link',Status='$status',I_type='$type',I_order='$order' $sqlp WHERE id=$Id");
	$Admin ->AddLog('网站管理','修改首页轮播：其Id为：'.$Id );
	echo showSuc('首页轮播修改成功','AdIndexList.php?status=0',$obj);
	break;

case 'AddReco': /**添加记录**/   
	getvalue($FLib);
	dump($_POST);
//	exit;
	$DataBase->QuerySql("INSERT INTO es_roll (Vc_name,Vc_url,Vc_image,I_order,I_type,Createtime) VALUES	('$title','$link','$uploadfile','$order','$type',now())");
	$Admin ->AddLog('网站管理','增加首页轮播：其名称为：'.$title);
	echo showSuc('首页轮播添加成功','AdIndexList.php?status=0',$obj);
	break;

case 'DeleteReco': /**删除记录**/
	$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
	if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	$DataBase->QuerySql('UPDATE es_roll SET Status=0 WHERE ID IN('.$IdList.')');

	$Admin ->AddLog('网站管理','删除首页轮播：其ID为：'.$IdList);
	echo showSuc('首页轮播删除完毕',$FLib->IsRequest('backurl'),$obj);
	break;
}

function getvalue($FLib){
	$Files = new FileClass;
	global $title,$link,$order,$type,$save,$uploadfile,$Work,$status;
	$title = $FLib->RequestChar('title',0,50,'名称',1);
	$link = $FLib->RequestChar('link',1,200,'链接地址',1);
	$order = $FLib->RequestInt('order',100,9,'排序号');
	$type = $FLib->RequestInt('type',1,9,'图片位置');
    $fpath = $FLib->RequestChar('fpath',1,200,'上传文件路径',1);
    $status = $FLib->RequestChar('status',1,200,'上传文件路径',1);
	if($fpath!='' && is_file($fpath)){
		$file_ext = array_pop(explode('.', $fpath));
		$new_file_name = date('His'). '.' . $file_ext;
		$uploaddir  = $GLOBALS['Config']->Upadvert . date('Ymd').'/';
		$uploadfile = $uploaddir.$new_file_name;
		$save_path = $Files->CreateFolderNew(WEBROOT, $uploaddir);
		$file_path = $save_path.$new_file_name;
		copy($fpath, $file_path);
		unlink($fpath);
	}else{
		if($Work=='AddReco') { echo showErr('请上传图片！'); exit; }
	}

}


$DataBase->CloseDataBase();   
 
?>       
    