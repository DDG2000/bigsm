<?php 
/*
函数名:makethumb
创建人:于立超
函数功能说明:将图片放缩；
生成缩略图,参数说明: 
$srcFile-图片文件名, 
$dstFile-另存文件名, 
$dstW-图片保存宽度, 
$dstH-图片保存高度, 
$rate-图片保存品质,
$percent-等比缩放比例;
提示:当缩放比例存在时,添了固定尺寸也不会起作用；
*/ 


/*
例子:

*/
	//makethumb("2.bmp","lajie.bmp","100","200",""); 

function makethumb($srcFile,$dstFile,$dstW,$dstH,$percent,$rate=100) 
{ 
   if(empty($percent))
     {
		$data = GetImageSize($srcFile); 
		switch($data[2]) 
		{ 
		case 1: 
		$im=@ImageCreateFromGIF($srcFile); 
		break; 
		case 2: 
		$im=@ImageCreateFromJPEG($srcFile); 
		break; 
		case 3: 
		$im=@ImageCreateFromPNG($srcFile); 
		break; 
		case 4: 
		$im=@ImageCreateFromWBMP($srcFile); 
		break;
		} 
		if(!$im) return False; 
		$srcW=ImageSX($im); 
		$srcH=ImageSY($im); 
		$MAX = max($dstH,$dstW);
		if($srcW>$MAX or $srcH>$MAX)
		  {
			if($srcW>$srcH)
			   {
				 $NdstW = $MAX;
				 $NdstH = $srcH/($srcW/$MAX);
			   }
			else
			   {
				 $NdstH = $MAX;
				 $NdstW = $srcW/($srcH/$MAX);
			   }   
		  }
		else
		  {
			$NdstW = $srcW;
			$NdstH = $srcH;
		  }
		$ni = ImageCreateTrueColor($NdstW,$NdstH); 
		$white = ImageColorAllocate($ni,255,255,255); 
		$black = ImageColorAllocate($ni,0,0,0); 
		// 填充背景色 
		imagefilledrectangle($ni,0,0,$NdstW,$NdstH,$white);
		imagecopyresampled($ni,$im,0,0,0,0,$NdstW,$NdstH,$srcW,$srcH); 
		ImageJpeg($ni,$dstFile,$rate);  
        imagedestroy($im); 
        imagedestroy($ni); 
   }
   else
    {
        
	  /*打开原图*/
			//print "123";
			//exit;
		
			$filename = $srcFile;
	
		  /*缩放比例   既:新图/原图*/
				$percent = $percent;
				list($width,$heigth) = getimagesize($filename);
				$newwidth = $width * $percent;
				$newheigth = $heigth * $percent;
				
		 /*存储路径  新建一个真彩色图像   imagecreatetruecolor不能用于 GIF 文件格式*/
				$thumb = imagecreatetruecolor($newwidth,$newheigth);
					$data = GetImageSize($srcFile); 
					switch($data[2]) 
					{ 
					case 1: 
					$source=@ImageCreateFromGIF($filename); 
					break; 
					case 2: 
					$source=@ImageCreateFromJPEG($filename); 
					break; 
					case 3: 
					$source=@ImageCreateFromPNG($filename); 
					break; 
					} 
				if(!$source)
				{
				  unlink($filename);
				  return false;
				}
				else
				{
				imagecopyresampled($thumb,$source,0,0,0,0,$newwidth,$newheigth,$width,$heigth);
		 /*打开*/
		        $data = GetImageSize($srcFile); 
					switch($data[2]) 
					{ 
					case 1: 
					$source=@ImageCreateFromGIF($filename); 
					imagegif($thumb,$dstFile);
					break; 
					case 2: 
					$source=@ImageCreateFromJPEG($filename); 
					imagejpeg($thumb,$dstFile);
					break; 
					case 3: 
					$source=@ImageCreateFromPNG($filename); 
					imagepng($thumb,$dstFile);
					break; 
					} 
				
                return true;
				}
	 }

} 
?>
