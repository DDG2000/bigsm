<?php
/**
* 模块：基础模块
* 描述：图片修改页
* 作者：kign
*/
require_once('../include/TopFile.php');
require_once('../include/makeimage/makethumb.php');
require_once('../include/File.class.php');
$Admin->CheckPopedoms('SC_SITE_AD_MDY');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work){

case 'MdyReco': /**修改记录**/
	getvalue($FLib);
	$Id = $FLib->RequestInt('Id',0,9,'ID');
	$sqlp = '';
	switch ($type){
		case '1':
			if(!empty($uploadfile)){ $sqlp .= ", Vc_image='$uploadfile'";}
			break;
		case '2':
			if(!empty($uploadfile1)){ $sqlp .= ", Vc_flash='$uploadfile1'";}
			break;
		case '3':
			$sqlp .= ", Vc_content='$remark'";
			break;
	}
	$DataBase->QuerySql("UPDATE site_image SET Vc_name='$title', Vc_link='$link',Vc_original='$uploadfile',Vc_intro='$intro',I_active='$show',I_order='$order' $sqlp WHERE id=$Id");
	$Admin ->AddLog('网站管理','修改图片：其Id为：'.$Id );
	echo showSuc("图片修改成功",'PictureList.php?status=',$obj);
	break;

case 'AddReco': /**添加记录**/   
	getvalue($FLib);
	$DataBase->QuerySql("INSERT INTO site_image	(Vc_name,Vc_link,Vc_original,Vc_intro,I_active,I_order,Createtime)	VALUES	('$title', '$link','$uploadfile','$intro', '$show', '$order',now())");
	$Admin ->AddLog('网站管理','增加图片：其名称为：'.$title);
	echo showSuc("图片添加成功",'PictureList.php?status=',$obj);
	break;

case 'DeleteReco': /**删除记录**/
	$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
	if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	$DataBase->QuerySql('UPDATE site_advertise SET Status=0 WHERE ID IN('.$IdList.')');

	$Admin ->AddLog('网站管理','删除图片：其ID为：'.$IdList);
	echo showSuc('图片删除完毕',$FLib->IsRequest('backurl'),$obj);
	break;
} 

function getvalue($FLib){
	$Files = new FileClass;
	global $title,$link,$intro,$show,$uploadfile,$Work,$order;
	$title                 = $FLib->RequestChar('title',0,50,'图片名称',1);
	$link                  = $FLib->RequestChar('link',1,200,'链接地址',1);
	$intro                 = $FLib->RequestChar('intro',1,200,'图片简介',1);
	$show 				   = $FLib->RequestInt('show',1,9,'状态');
    $order          	   = $FLib->RequestChar('order',1,200,'排序',1);
    $fpath          	   = $FLib->RequestChar('fpath',1,200,'上传文件路径',1);

	if($fpath!=''){
		$file_ext = array_pop(explode('.', $fpath));
		$new_file_name = date('His'). '.' . $file_ext;
		$uploaddir  = $GLOBALS['Config']->Upadvert . date('Ymd').'/';
		$uploadfile = $uploaddir.$new_file_name;
		$save_path = $Files->CreateFolderNew(WEBROOT, $uploaddir);
		$file_path = $save_path.$new_file_name;
		copy($fpath, $file_path);
		unlink($fpath);
	}else{
		if($Work=='AddReco') { echo showErr('您选择了图片型图片,请上传图片！'); exit; }
	}

}


$DataBase->CloseDataBase();   
 
?>       
    