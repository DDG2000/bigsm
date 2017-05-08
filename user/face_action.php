<?php
require_once('../include/TopFile.php');
$lg = getuser();
if(!$lg){die('您未登录！');}
$uid = $lg['uid'];

require(WEBROOT . 'include/File.class.php');
$Fc = new FileClass();

$path = '/upload/face/';
$tempath = $path.'tmp/';
$savepath = $Fc->CreateFolder($tempath);
$action = $FL->mr('act');
if($action=='delimg'){
	$filename = $_POST['imagename'];
	if(!empty($filename)){
		@unlink($savepath.$filename);
		echo '1';
	}else{
		echo '删除失败.';
	}
}
elseif($action=='subface'){
	$x=$FL->requestint('x',0,'x',1);
	$y=$FL->requestint('y',0,'y',1);
	$w=$FL->requestint('w',96,'w',1);
	$h=$FL->requestint('h',96,'h',1);
	$pic=$FL->requeststr('src',0,50,'图片地址',1);
	$bi=$FL->requeststr('bi',0,50,'比例',1);
	$bi = floatval($bi);
	$x/=$bi;$y/=$bi;
	$fw=$w/$bi;$fh=$h/$bi;
	
	$tw=96;$th=96;
	//剪切后小图片的名字
	$str = explode(".",$pic);//图片的格式
	$type = array_pop($str); //图片的格式
	$filename = $uid .'_'.time().'_96x96'.'.'. $type; //重新生成图片的名字
	$uploadBanner = $pic;
	$pathnew     = $path.date('Ym').'/';
	$savepathnew = $Fc->CreateFolder($pathnew);
	$sliceBanner = $pathnew.$filename;//剪切后的图片存放的位置
	$savepathBanner = $savepathnew.$filename;
	$src_picfname = WEBROOT.$uploadBanner;

	//创建图片
	$src_pic = getImageHander($src_picfname);
	$dst_pic = imagecreatetruecolor($tw, $th);
	imagecopyresampled($dst_pic,$src_pic,0,0,$x,$y,$tw,$th,$fw,$fh);
	imagejpeg($dst_pic, $savepathBanner);
	imagedestroy($src_pic);
	imagedestroy($dst_pic);
	
	//删除已上传未裁切的图片
	if(file_exists($src_picfname)) {
		@unlink($src_picfname);
	}

	//新图片的位置 处理入库 及返回
	$newphoto = $sliceBanner;
	$u=new User();
	$r=$u->mdy(array('Vc_photo'=>$newphoto), $uid);
	if($r['err']<1){ returnjson($r); }
	$lg['Vc_photo']=$newphoto;
	$r['photo']=$newphoto;
	returnjson($r);

}
else{
	if(!isset($_FILES['mypic'])){
		echo '请选择图片';
		exit;		
	}
	$picname = $_FILES['mypic']['name'];
	$picsize = $_FILES['mypic']['size'];
	if ($picname == "") {
		echo '请选择图片';
		exit;
	}
	if ($picsize > 2048000) {
		echo '图片大小不能超过2M';
		exit;
	}
	$type = strtolower(strstr($picname, '.'));
	if (!in_array($type, array('.gif','.jpg','.jpeg','.png'))) {
		echo '图片格式不对！';
		exit;
	}
	$rand = mt_rand(100, 999);
	$pics_0 = date("YmdHis") . $rand;
	$pics = $pics_0 . $type;
	$pics_n = $pics_0 . '_n.jpg';
	//上传路径
	$pic_path = $savepath . $pics;
	move_uploaded_file($_FILES['mypic']['tmp_name'], $pic_path);
	$pic_webpath = $tempath . $pics;
	
	//$image_size = getimagesize($pic_path);
	//生成缩略图
	$Fc->makeimage($pic_path, $savepath.$pics_n, 300,300);
	$pic_webpath = $tempath.$pics_n;
	$image_size = getimagesize($savepath.$pics_n);
	$arr = array(
		'name'=>$picname,
		'pic'=>$pic_webpath,
		'width'=>$image_size[0],
		'height'=>$image_size[1]
	);
	echo json_encode($arr);

	
	//临时文件夹 内过期文件删除
	$abspath = $savepath;
	$time = time() - 86400;
	if (is_dir($abspath)){
		if ($tmpFilePath = opendir($abspath)) {
			while (($tmpFile = readdir($tmpFilePath)) !== false) {
				$theFileTime = filectime($abspath.$tmpFile);
				if( $theFileTime <= $time){
					@unlink($abspath.$tmpFile);
				}
			}
			closedir($tmpFilePath);
		}
	}
}



//初始化图片
function getImageHander ($url) {
	$size=@getimagesize($url);
	switch($size['mime']){
		case 'image/jpeg': $im = imagecreatefromjpeg($url);break;
		case 'image/gif' : $im = imagecreatefromgif($url);break;
		case 'image/png' : $im = imagecreatefrompng($url);break;
		//case 'image/bmp' : $im = imagecreatefrombmp($url);break;
		default: $im=false;break;
	}
	return $im;
}

?>