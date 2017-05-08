<?php 
/*
������:makethumb
������:������
��������˵��:��ͼƬ������
��������ͼ,����˵��: 
$srcFile-ͼƬ�ļ���, 
$dstFile-����ļ���, 
$dstW-ͼƬ������, 
$dstH-ͼƬ����߶�, 
$rate-ͼƬ����Ʒ��,
$percent-�ȱ����ű���;
��ʾ:�����ű�������ʱ,���˹̶��ߴ�Ҳ���������ã�
*/ 


/*
����:

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
		// ��䱳��ɫ 
		imagefilledrectangle($ni,0,0,$NdstW,$NdstH,$white);
		imagecopyresampled($ni,$im,0,0,0,0,$NdstW,$NdstH,$srcW,$srcH); 
		ImageJpeg($ni,$dstFile,$rate);  
        imagedestroy($im); 
        imagedestroy($ni); 
   }
   else
    {
        
	  /*��ԭͼ*/
			//print "123";
			//exit;
		
			$filename = $srcFile;
	
		  /*���ű���   ��:��ͼ/ԭͼ*/
				$percent = $percent;
				list($width,$heigth) = getimagesize($filename);
				$newwidth = $width * $percent;
				$newheigth = $heigth * $percent;
				
		 /*�洢·��  �½�һ�����ɫͼ��   imagecreatetruecolor�������� GIF �ļ���ʽ*/
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
		 /*��*/
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
