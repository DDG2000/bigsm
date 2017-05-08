<?php
//if($_REQUEST['PHPSESSID']){session_id($_REQUEST['PHPSESSID']);}
require('../../TopFile.php');

set_time_limit(0);
function createFolder_t($Root, $c){if($c =='') return;$carr = explode('/',$c);for($i=0;$i<count($carr);$i++){if(!empty($carr[$i])){if(!is_dir($Root.$carr[$i])){mkdir($Root.$carr[$i],0777);chmod($Root.$carr[$i],0777);}$Root  = $Root.$carr[$i].'/';}}unset($carr);}

$temppath = $Config->File_Upload_TempPath;
//文件保存目录路径
$save_path =  WEBROOT . $temppath;
createFolder_t(WEBROOT, $temppath);
$save_path = realpath($save_path) . L;
//文件保存目录URL
$php_url = dirname(dirname(dirname(dirname(dirname($_SERVER['PHP_SELF'])))));
$save_url =  $php_url . $temppath;

//开始处理临时目录中超过24小时的文件
$time = time();
if (is_dir($save_path)){
	if ($tmpFilePath = opendir($save_path)) {
		while (($tmpFile = readdir($tmpFilePath)) !== false) {
			$theFileTime = filectime($save_path.$tmpFile);
			if( $theFileTime <= $time - 86400 && $tmpFile != 'index.html' ){
				@unlink($save_path.$tmpFile);
			}
		}
		closedir($tmpFilePath);
	}
}

$ty = intval($_GET['ty']);

$thisFiles = ( trim($_POST['uploadFileName']) != '') ? trim($_POST['uploadFileName']) : "Filedata" ;
$upfile = $_FILES[$thisFiles];
if (!isset($upfile) || !is_uploaded_file($upfile["tmp_name"]) || $upfile["error"] != 0){
	header("HTTP/1.1 500 File Upload Error");
	if (isset($upfile)) {echo $upfile["error"];}
	exit();
}else{
	$file_ext = trim(strtolower(array_pop(explode('.', $upfile['name']))));
	//$new_file_name = md5($upfile['name'].microtime(1)) . '_' . $_REQUEST["PHPSESSID"] . '.' . $file_ext;
	$new_file_name = md5($upfile['name'].microtime(1)) . '.' . $file_ext;
	$file_path = $save_path . $new_file_name;
	if (move_uploaded_file($upfile['tmp_name'], $file_path) === false) {
		die('上传失败|'.$file_path);
	}
	@chmod($file_path, 0777);
	$file_url = $save_url . $new_file_name;
	if($ty==1){
		$file_path = $file_url = $new_file_name;
	}
	if ($_POST['upClientStatus'] !== '1') {
		die('上传['.$upfile['name'].']文件成功 |'.$file_path.'|'.$file_url);
	} else {
		//上传客户端包，需要返回url及packsize
		$client_path = $Config->File_Upload_Client_Path . date('Ymd') . '/';
		//客户端包保存目录路径
		$save_path =  WEBROOT . $client_path;
		createFolder_t(WEBROOT, $client_path);
		$save_path = realpath($save_path) . L;
		//文件完整路径及文件名
		$new_file_name = date('YmdHis').mt_rand().'.'.$file_ext;
		$dest_path = $save_path . $new_file_name;
		//包大小
		$packsize = filesize($file_path);

		rename($file_path, $dest_path);

		$file_url = $save_url . $client_path . $new_file_name;
		$url = 'http://' . $_SERVER['SERVER_NAME'] . $client_path . $new_file_name;
		die('上传['.$upfile['name'].']文件成功 |'.$dest_path.'|'.$file_url.'|'.$url.'|'.$packsize);
	}

}

?>