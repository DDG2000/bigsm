<?php
require_once('../include/TopFile.php');
$lg = getuser();
if(!$lg){  loginouttime();}
$uid = $lg['uid'];
require_once(WEBROOT . 'include/File.class.php');
$Fc = new FileClass();

$path = '/upload/shop/certificate/';
$tempath = $path.'tmp/';
$savepath = $Fc->CreateFolder($tempath);

$action = $FL->mr('act');//http://www.bigsm.com/shop/certify_action.php?&act=subface&src=
if($action=='delimg'){
    /**
     * @author wh
     * 删除图片接口
     * url地址：
     * http://www.bigsm.com/shop/certify_action.php?&act=delimg
     * 输入：需登录后访问
     *
     * imagename：   string 图片名字 xxx.jpg
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     *
     * */
//     http://www.bigsm.com/shop/certify_action.php?&act=delimg
    if(!isset($_POST['imagename'])){
      returnjson(array('err'=>-1,'msg'=>'参数不合法'));
    }
	$filename = $_POST['imagename'];
	if(!empty($filename)){
		@unlink($savepath.$filename);
		returnjson(array('err'=>0,'msg'=>'删除成功'));
// 		echo '1';
	}else{
	    returnjson(array('err'=>-1,'msg'=>'删除失败'));
// 		echo '删除失败.';
	}
}
elseif($action=='subface'){
   
    
    /**
     * @author wh
     * 保存图片接口
     * url地址：
     * http://www.bigsm.com/shop/certify_action.php?&act=subface
     * 输入：需登录后访问
     *
     * x: int  裁剪的原点x坐标
     * y: int  裁剪的原点y坐标
     * w: int  裁剪的宽度
     * h: int  裁剪的高度
     * src: string 返回的临时图片URL地址
     * bi: string  比例
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     *以下仅在err为0时会返回
     * photo: string  新图片的位置url
     *
     * */
    
    
	$x=$FL->requestint('x',0,'x',1);
	$y=$FL->requestint('y',0,'y',1);
	$w=$FL->requestint('w',96,'w',1);
	$h=$FL->requestint('h',96,'h',1);
	$pic=$FL->requeststr('src',0,100,'图片地址',1);
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
// 	require(WEBROOTINCCLASS.'Shop.php');
// 	$shop=new Shop();
// 	$r=$shop->mdy(array('Vc_logo_pic'=>$newphoto), $uid);
// 	if($r['flag']<1){ returnjson($r); }
// 	$lg['Vc_photo']=$newphoto;//做登录的处理
	// $r=array();
	$r['imagename']=$filename;
	$r['photo']=$newphoto;
	$r['err']=0;
	$r['msg']='success';
	returnjson($r);

}
else{

    /**
     * @author wh
     * 上传认证图片接口
     * url地址：
     * http://www.bigsm.com/shop/certify_action.php
     * 输入：需登录后访问
     * （输入以下任一参数）
     * Vc_identity_pic1：file  身份证正面照片
     * Vc_identity_pic2：file  身份证背面照片
     * Vc_licence_pic：file  营业执照照片
     * Vc_tax_pic：file  税务登记证图片
     * Vc_org_pic：file  组织机构代码证照片
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     * 以下仅在err为0时会返回
     * name: string  上传图片的名称
     * pic: string  临时图片的位置url
     * width: string  压缩后图片宽度
     * height: string  压缩后图片高度
     * 
     * 
     * */
    
  /*  
    * Vc_identity_pic1：string  身份证正面照片
    * Vc_identity_pic2：string  身份证背面照片
    * Vc_licence_pic：string  营业执照照片
    * Vc_tax_pic：string  税务登记证图片
    * Vc_org_pic：string  组织机构代码证照片
    *  */
//     var_dump($_FILES);
//     exit;
	
	if(isset($_FILES['Vc_identity_pic1'])){
	    
	    $picname = $_FILES['Vc_identity_pic1']['name'];
	    $picsize = $_FILES['Vc_identity_pic1']['size'];
	    $pictempname=$_FILES['Vc_identity_pic1']['tmp_name'];
	    
	}else if(isset($_FILES['Vc_identity_pic2'])){
	    
	    $picname = $_FILES['Vc_identity_pic2']['name'];
	    $picsize = $_FILES['Vc_identity_pic2']['size'];
	    $pictempname=$_FILES['Vc_identity_pic2']['tmp_name'];
	    
	}else if(isset($_FILES['Vc_licence_pic'])){
	    
	    $picname = $_FILES['Vc_licence_pic']['name'];
	    $picsize = $_FILES['Vc_licence_pic']['size'];
	    $pictempname=$_FILES['Vc_licence_pic']['tmp_name'];
	    
	}else if(isset($_FILES['Vc_tax_pic'])){
	    
	    $picname = $_FILES['Vc_tax_pic']['name'];
	    $picsize = $_FILES['Vc_tax_pic']['size'];
	    $pictempname=$_FILES['Vc_tax_pic']['tmp_name'];

	}else if(isset($_FILES['Vc_org_pic'])){
	    
	    $picname = $_FILES['Vc_org_pic']['name'];
	    $picsize = $_FILES['Vc_org_pic']['size'];
	    $pictempname=$_FILES['Vc_org_pic']['tmp_name'];
	    
	}else {
	    // 		returnjson($array);
	    returnjson(array('err'=>-1,'msg'=>'请选择图片'));
	}
	
	if ($picname == "") {
	    returnjson(array('err'=>-1,'msg'=>'请选择图片'));

	}
	if ($picsize > 2048000) {
	    returnjson(array('err'=>-1,'msg'=>'图片大小不能超过2M'));

	}
	$type = strtolower(strstr($picname, '.'));
	if (!in_array($type, array('.gif','.jpg','.jpeg','.png'))) {
	    returnjson(array('err'=>-1,'msg'=>'图片格式不对！'));

	}
	$rand = mt_rand(100, 999);
	$pics_0 = date("YmdHis") . $rand;
	$pics = $pics_0 . $type;
	$pics_n = $pics_0 . '_n.jpg';
	//上传路径
	$pic_path = $savepath . $pics;
	move_uploaded_file($pictempname, $pic_path);
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
	$arr['err']=0;
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