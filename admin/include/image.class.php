<?php
class image
{
	var $w_pct = 20; //透明度
	var $w_quality = 80; //质量
	var $w_minwidth = 100; //最小宽
	var $w_minheight = 100; //最小高
	var $interlace = 0;  //图像是否为隔行扫描的
	var $fontfile ;  //字体文件
	var $w_img ; //默认水印图

	function __construct()
	{
		$this->fontfile = XINCHENG_ROOT.'include/fonts/simhei.ttf';
		$this->w_img = XINCHENG_ROOT.'images/watermark/logo.gif';
	}
	function image()
	{
		$this->__construct();
	}

	function info($img)
	{
		$imageinfo = getimagesize($img); //返回图像信息数组 0=>宽的像素 1=>高的像素 2=>是图像类型的标记 3 =>是文本字符串，内容为“height="yyy" width="xxx"”，
		if($imageinfo === false) return false;
		$imagetype = strtolower(substr(image_type_to_extension($imageinfo[2]),1)); //获取图像文件类型 $imageinfo[2]是图像类型的标记
		$imagesize = filesize($img); //图像大小
		$info = array(
		'width'=>$imageinfo[0],
		'height'=>$imageinfo[1],
		'type'=>$imagetype,
		'size'=>$imagesize,
		'mime'=>$imageinfo['mime']
		);
		return $info;
	}

	/**
	 * 缩略图
	 *
	 * @param string $image
	 * @param string $filename
	 * @param int $maxwidth
	 * @param int $maxheight
	 * @param string $suffix
	 * @param int $autocut
	 * @return string
	 */
	function thumb($image, $filename = '', $maxwidth = 50, $maxheight = 50, $suffix='_thumb', $autocut = 0)
	{
		if( !$this->check($image)) return false;
		$info  = $this->info($image); //获取图片信息
		if($info === false) return false;
		$srcwidth  = $info['width']; //源图宽
		$srcheight = $info['height']; //源图高
		$pathinfo = pathinfo($image);
		$type =  $pathinfo['extension']; //取得扩展名
		if(!$type) $type = $info['type']; //如果没有取到，用$info['type']
		$type = strtolower($type);
		unset($info);
		$scale = min($maxwidth/$srcwidth, $maxheight/$srcheight); //获取缩略比例
		//获取按照源图的比列
		$createwidth = $width  = (int)($srcwidth*$scale); //取得缩略宽
		$createheight = $height = (int)($srcheight*$scale); //取得缩略高
		$psrc_x = $psrc_y = 0;
		if($autocut) //按照缩略图的比例来获取
		{
			if($maxwidth/$maxheight<$srcwidth/$srcheight && $maxheight>=$height) //如果缩略图按比列比源图窄的话
			{
				$width = $maxheight/$height*$width; //宽按照相应比例做处理
				$height = $maxheight; //高不变
			}
			elseif($maxwidth/$maxheight>$srcwidth/$srcheight && $maxwidth>=$width)//如果缩略图按比列比源图宽的话
			{
				$height = $maxwidth/$width*$height;
				$width = $maxwidth;
			}
			$createwidth = $maxwidth;
			$createheight = $maxheight;
		}
		$createfun = 'imagecreatefrom'.($type=='jpg' ? 'jpeg' : $type); //找到不同的图像处理函数
		$srcimg = $createfun($image); //返回图像标识符
		if($type != 'gif' && function_exists('imagecreatetruecolor'))
		$thumbimg = imagecreatetruecolor($createwidth, $createheight); //新建一个真彩色图像
		else
		$thumbimg = imagecreate($width, $height); //新建一个基于调色板的图像

		if(function_exists('imagecopyresampled')) //重采样拷贝部分图像并调整大小，真彩
		//imagecopyresampled(新图，源图,新图左上角x距离,新图左上角y距离,源图左上角x距离,源图左上角y距离,新图宽,新图高,源图宽,源图高)
		imagecopyresampled($thumbimg, $srcimg, 0, 0, $psrc_x, $psrc_y, $width, $height, $srcwidth, $srcheight);
		else //拷贝部分图像并调整大小，调色板
		imagecopyresized($thumbimg, $srcimg, 0, 0, $psrc_x, $psrc_y, $width, $height,  $srcwidth, $srcheight);
		if($type=='gif' || $type=='png')
		{
			//imagecolorallocate 为一幅图像分配颜色
			$background_color  =  imagecolorallocate($thumbimg,  0, 255, 0);  // 给基于调色板的图像填充背景色， 指派一个绿色
			// imagecolortransparent 将某个颜色定义为透明色
			imagecolortransparent($thumbimg, $background_color);  //  设置为透明色，若注释掉该行则输出绿色的图
		}
		// imageinterlace 激活或禁止隔行扫描
		if($type=='jpg' || $type=='jpeg') imageinterlace($thumbimg, $this->interlace);
		$imagefun = 'image'.($type=='jpg' ? 'jpeg' : $type);
		//imagejpeg imagegif imagepng
		if(empty($filename)) $filename  = substr($image, 0, strrpos($image, '.')).$suffix.'.'.$type; //获取文件名
		//aaa.gif aaa_thumb.gif
		$imagefun($thumbimg, $filename); //新建图像
		imagedestroy($thumbimg); //销毁缩略图
		imagedestroy($srcimg); //销毁源图
		return $filename;
	}
	//watermark(源图,生成文件,生成位置,水印文件,水印文本,背景色)
	function watermark($source, $target = '', $w_pos = 9, $w_img = '', $w_text = '', $w_font = 12, $w_color = '#cccccc')
	{
		if( !$this->check($source)) return false;
		if(!$target) $target = $source;
		if ($w_img == '' && $w_text == '')
		$w_img = $this->w_img;
		$source_info = getimagesize($source);
		$source_w    = $source_info[0]; //获取宽
		$source_h    = $source_info[1]; //获取高
		if($source_w < $this->w_minwidth || $source_h < $this->w_minheight) return false; //宽和高达不到要求直接返回
		switch($source_info[2]) //新建图片
		{
			case 1 :
				$source_img = imagecreatefromgif($source);
				break;
			case 2 :
				$source_img = imagecreatefromjpeg($source);
				break;
			case 3 :
				$source_img = imagecreatefrompng($source);
				break;
			default :
				return false;
		}
		if(!empty($w_img) && file_exists($w_img)) //水印文件
		{
			$ifwaterimage = 1; //是否水印图
			$water_info   = getimagesize($w_img); //水印信息
			$width        = $water_info[0];
			$height       = $water_info[1];
			switch($water_info[2])
			{
				case 1 :
					$water_img = imagecreatefromgif($w_img);
					break;
				case 2 :
					$water_img = imagecreatefromjpeg($w_img);
					break;
				case 3 :
					$water_img = imagecreatefrompng($w_img);
					break;
				default :
					return;
			}
		}
		else
		{
			$ifwaterimage = 0;
			//imagettfbbox 本函数计算并返回一个包围着 TrueType 文本范围的虚拟方框的像素大小。
			//imagettfbbox ( 字体大小, 字体角度, 字体文件,文件 )
			$temp = imagettfbbox(ceil($w_font*1.6), 0, $this->fontfile, $w_text);//取得使用 truetype 字体的文本的范围
			$width = $temp[4] - $temp[6]; //右上角 X 位置 - 左上角 X 位置
			$height = $temp[3] - $temp[5]; //右下角 Y 位置- 右上角 Y 位置
			unset($temp);
		}
		switch($w_pos)
		{
			case 0: //随机位置
			$wx = rand(0,($source_w - $width));
			$wy = rand(0,($source_h - $height));
			break;
			case 1: //左上角
			$wx = 25;
			$wy = 25;
			break;
			case 2: //上面中间位置
			$wx = ($source_w - $width) / 2;
			$wy = 0;
			break;
			case 3: //右上角
			$wx = $source_w - $width;
			$wy = 0;
			break;
			case 4: //左面中间位置
			$wx = 0;
			$wy = ($source_h - $height) / 2;
			break;
			case 5: //中间位置
			$wx = ($source_w - $width) / 2;
			$wy = ($source_h - $height) / 2;
			break;
			case 6: //底部中间位置
			$wx = ($source_w - $width) / 2;
			$wy = $source_h - $height;
			break;
			case 7: //左下角
			$wx = 0;
			$wy = $source_h - $height;
			break;
			case 8: //右面中间位置
			$wx = $source_w - $width;
			$wy = ($source_h - $height) /2;
			break;
			case 9: //右下角
			$wx = $source_w - $width;
			$wy = $source_h - $height ;
			break;
			default: //随机
			$wx = rand(0,($source_w - $width));
			$wy = rand(0,($source_h - $height));
			break;
		}
		if($ifwaterimage) //如果有水印图
		{
			//imagecopymerge 拷贝并合并图像的一部分
			//参数(源图,水印图,拷贝到源图x位置,拷贝到源图y位置,从水印图x位置,从水印图y位置,高,宽,透明度)
			imagecopymerge($source_img, $water_img, $wx, $wy, 0, 0, $width, $height, $this->w_pct);
		}
		else
		{
			if(!empty($w_color) && (strlen($w_color)==7))
			{
				$r = hexdec(substr($w_color,1,2)); //获取红色
				$g = hexdec(substr($w_color,3,2)); //获取绿色
				$b = hexdec(substr($w_color,5)); //获取蓝色
			}
			else
			{
				return;
			}
			//imagecolorallocate 基于调色板的图像填充背景色
			//imagestring 水平地画一行字符串
			//imagestring(源图,字体大小,位置X,位置Y,文字,颜色)
			//参数($image, float $size, float $angle, int $x, int $y, int $color, string $fontfile, string $text)
			imagettftext($source_img,$w_font,0,$wx,$wy,imagecolorallocate($source_img,$r,$g,$b),$this->fontfile,$w_text);
			//imagestring($source_img,$w_font,$wx,$wy,$w_text,imagecolorallocate($source_img,$r,$g,$b));
		}
		//输出到文件或者浏览器
		switch($source_info[2])
		{
			case 1 :
				imagegif($source_img, $target); //以 GIF 格式将图像输出到浏览器或文件
				break;
			case 2 :
				imagejpeg($source_img, $target, $this->w_quality); //以 JPEG 格式将图像输出到浏览器或文件
				break;
			case 3 :
				imagepng($source_img, $target); //以 PNG 格式将图像输出到浏览器或文件
				break;
			default :
				return;
		}
		if(isset($water_info))
		{
			unset($water_info); //销毁
		}
		if(isset($water_img))
		{
			imagedestroy($water_img); //销毁
		}
		unset($source_info);
		imagedestroy($source_img);
		return true;
	}
	//gd库必须存在，后缀为jpg|jpeg|gif|png，文件存在，imagecreatefromjpeg或者imagecreatefromgif存在
	function check($image)
	{
		return extension_loaded('gd') &&
		preg_match("/\.(jpg|jpeg|gif|png)/i", $image, $m) &&
		file_exists($image) &&
		function_exists('imagecreatefrom'.($m[1] == 'jpg' ? 'jpeg' : $m[1]));
		//imagecreatefromjpeg
		//imagecreatefromgif
		//imagecreatefrompng
	}
}

/**
 缩略图
1.新建一个图像资源 通过 imagecreatefromgif imagecreatefromjpeg imagecreatefrompng
2.imagecopyresampled 拷贝图像，并调整大小

水印：图片水印，文字水印
1. 创建图像
2.加水印
图片水印：imagecopymerge 把2张图合并在一起
文字水印：imagettftext  向图像写入文字


*/
?>