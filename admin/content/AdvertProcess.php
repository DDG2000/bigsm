<?php
/**
* 模块：基础模块
* 描述：广告修改页
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
	$DataBase->QuerySql("UPDATE site_advertise SET Vc_name='$title', Vc_link='$link',I_type='$type',Vc_start='$starttime',Vc_end='$endtime',I_placeID='$place',I_active=$show $sqlp WHERE id=$Id");
	$Admin ->AddLog('网站管理','修改广告：其Id为：'.$Id );
	echo showSuc("广告修改成功",'AdvertList.php?status=',$obj);
	break;

case 'AddReco': /**添加记录**/   
	getvalue($FLib);
	$DataBase->QuerySql("INSERT INTO site_advertise	(Vc_name,I_type,Vc_link,I_placeID,I_active,Vc_content,Vc_image,Vc_flash,Createtime)	VALUES	('$title', '$type', '$link','$place','$show', '$remark', '$uploadfile','$uploadfile1',now())");
	$Admin ->AddLog('网站管理','增加广告：其名称为：'.$title);
	echo showSuc('广告添加成功','AdvertList.php?status=',$obj);
	break;

case 'DeleteReco': /**删除记录**/
	$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
	if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	$DataBase->QuerySql('UPDATE site_advertise SET Status=0 WHERE ID IN('.$IdList.')');

	$Admin ->AddLog('网站管理','删除广告：其ID为：'.$IdList);
	echo showSuc('广告删除完毕',$FLib->IsRequest('backurl'),$obj);
	break;
} 

function getvalue($FLib){
	$Files = new FileClass;
	global $title,$type,$link,$place,$show,$remark,$starttime,$endtime,$file1,$flash,$save,$uploadfile,$uploadfile1,$Work;
	$title                 = $FLib->RequestChar('title',0,50,'广告名称',1);
	$type                  = $FLib->RequestInt('type',1,9,'广告分类');
	$place                 = $FLib->RequestInt('Parent',0,9,'广告位置',1);
	if($place == 0) { echo showErr('请选择广告位置'); exit; }
	$link                  = $FLib->RequestChar('link',1,200,'链接地址',1);
	$starttime                  = $FLib->RequestChar('starttime',1,50,'开始时间',1);
	$endtime                   = $FLib->RequestChar('endtime',1,50,'结束时间',1);
	$show 				   = $FLib->RequestInt('show',1,9,'状态');
    $fpath          = $FLib->RequestChar('fpath',1,200,'上传文件路径',1);

	switch($type){
	case '1':
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
			if($Work=='AddReco') { echo showErr('您选择了图片型广告,请上传广告图片！'); exit; }
		}
		break;
	case '2':
		if($fpath!=''){
			$file_ext = array_pop(explode('.', $fpath));
			if($file_ext !== 'swf'){echo showErr('请上传flash类型的文件');exit;}
			$new_file_name = date('His'). '.' . $file_ext;
			$uploaddir  = $GLOBALS['Config']->Upadvert . date('Ymd').'/';
			$uploadfile1 = $uploaddir.$new_file_name;
			$save_path = $Files->CreateFolderNew(WEBROOT, $uploaddir);
			$file_path = $save_path.$new_file_name;
			copy($fpath, $file_path);
			unlink($fpath);
		}else{
			if($Work=='AddReco') { echo showErr('您选择了flash型广告,请上传flash广告！'); exit; }
		}
		break;
	case '3': //文字
		$remark = $FLib->RequestChar('remark',1,200,'广告内容',1);
		if($remark == "") { echo showErr('您选择了文字型广告,请填写广告内容！'); exit; }
		break;
}

}


$DataBase->CloseDataBase();   
 
?>       
    