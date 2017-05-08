<?php
/****************************************************************** 
**创建者：于立超
**创建时间：2009-10-16
**本页：
**  系统用户登录页
**说明：
******************************************************************/
error_reporting(0);
require_once(WEBROOT.'admin' . L . 'include' . L . 'makeimage'. L .'makethumb.php'); /*函数操作基类*/
class FileClass
{
	var $l;
	function FileClass()
	{
		$this->l =DIRECTORY_SEPARATOR;
		
	}
	/***
     参数：$c 要建立的文件夹
     ***/
    function CreateFolder($c)
    {	
	  if($c =='') return;
	  $carr = explode('/',$c);
	  $Root = $_SERVER['DOCUMENT_ROOT'].$this->l;
	  for($i=0;$i<count($carr);$i++)
	  {
		  if(!empty($carr[$i]))
		  {
		     if(!is_dir($Root.$carr[$i]))
			 {
                    mkdir($Root.$carr[$i],0777);
					chmod($Root.$carr[$i],0777);			 
			 }
		     $Root  = $Root.$carr[$i].$this->l;
		  }
	   }
	   unset($carr);
	   return $Root;
     }
	/***
     参数：$c 要建立的文件夹 全路径
     ***/
    function CreateFolderNew($root, $c)
    {	
	  if($c =='') return;
	  $carr = explode('/',$c);
	  $Root = $root;
	  for($i=0;$i<count($carr);$i++)
	  {
		  if(!empty($carr[$i]))
		  {
		     if(!is_dir($Root.$carr[$i]))
			 {
                    mkdir($Root.$carr[$i],0777);
					chmod($Root.$carr[$i],0777);			 
			 }
		     $Root  = $Root.$carr[$i].$this->l;
		  }
	   }
	   unset($carr);
	   return $Root;
     }
   
   /***
   转换文件大小
   参数：文件大小
   ***/
   function rfilesze($n)
   {
   	  if(!preg_match('/^[0-9]+$/',$n))
	  {
	  	return $n;
	  }
	  if($n < 1024)
	  {
	  	return $n.' B';
	  }
	  $n = $n/1024;
	  if($n < 1000)
	  {
	  	return number_format($n,2).' KB';
	  }
	  $n = $n/1024;
	  if($n < 1000)
	  {
	  	return number_format($n,2).' MB';
	  }
	  $n = $n/1024;
	  if($n < 1000)
	  {
	  	return number_format($n,2).' GB';
	  }
	  $n = $n/1024;
	  return number_format($n,2).' UB';
	  
   
   }
/***
转入文件类型
返回扩展名
**/
function getFileType($T)
{
    
    if($T =='') return '';
	switch(strtolower($T))
	{
	      case 'application/internet-property-stream':
		     return 'acx';
		     break;
		  case 'video/x-ms-asf':
		     return 'asx|asr|asf';
		     break;
		  case 'video/x-msvideo':
		     return 'avi';
		     break;
		  case 'video/avi':
		     return 'avi';
		     break;
		  case 'image/bmp':
		     return 'bmp';
		     break;
		  case 'text/plain':
		     return 'c|h|txt';
		     break;
		  case 'text/css':
		     return 'css';
		     break;
		  case 'application/octet-stream':
		     return 'exe|rar|lzh|lha';
		     break;
		  case 'image/gif':
		     return 'gif';
		     break;
		  case 'text/html':
		     return 'htm|html|stm';
		     break;
		  case 'image/pipeg':
		     return 'jfif';
		     break;
		  case 'image/jpeg':
		    // return 'jpe|jpeg|jpg';
		     return 'jpg';
		     break;
		  case 'image/pjpeg':
		     return 'jpg';
		     break;
		  case 'application/x-javascript':
		     return 'js';
		     break;
		  case 'video/x-sgi-movie':
		     return 'movie';
		     break;
		  case 'video/mpeg':
		     return 'mp2|mpa|mpe|mpeg|mpg|mpv2';
		     break;
		  case 'audio/mpeg':
		     return 'mp3';
		     break;
		  case 'application/oda':
		     return 'oda'; 
		     break;
		  case 'image/x-png':
		     return 'png';
		     break;
		  case 'image/x-portable-anymap':
		     return 'pnm';
		     break;
		  case 'application/vnd.ms-powerpoint':
		     return 'pot|pps|ppt';
		     break;
		  case 'audio/x-pn-realaudio':
		     return 'ram|rm';
		     break;
		  case 'audio/x-wav':
		     return 'wav'; 
		     break;
		  case 'video/x-ms-wmv':
		     return 'wmv';
		     break;
		  case 'application/zip':
		     return 'zip';
		     break;
		  case 'application/x-zip-compressed':
		     return 'zip';
		     break;
		  case 'application/x-shockwave-flash':
		     return 'swf';
			 break;
		  default:
		     return '';
	         break;
	  }

  }
  	/***
	写文件文件
	参数：$F:文件地址;$C:文件内容
	**/	
	function WriteFile($F,$C)
	{     
		if($F==''){
			return array('err'=>'路径不能为空');
		}
		if(!@file_put_contents($F,$C)){
			return array('err'=>'文件['.$F.']没有写权限');
		}
	}
	/***
	读取文件
	参数：$p：文件名;$T:类型
	$T: 1-内容;2-修改时间
	**/	
	function ReadFile($p='',$T = 1)
	{
		if($p==''){
			return array('err'=>'文件路径不能为空');
		}
		if(!file_exists($p)){
			return array('err'=>'文件['.$p.']未找到');
		}
		if(!is_readable($p)){
			return array('err'=>'文件['.$p.']不能读取');
		}
		switch($T)
		{
			case 1:
				return file_get_contents($p);
				break;
			case 2:
				return date('Y-m-d H:i:s',filemtime($p));
				break;
			default:
				return array('err'=>'类型错误');
				break;
		}
		
	}
	/***
	删除文件
	****/
	function delfile($p)
	{
		if($p =='') return;
		if(!eregi('^[a-z]{1}\:',$p))
		{
			if(!eregi('^\/',$p))
			{
				echo $GLOBALS['FLib']->Alert('路径不正确','','BACK');
				exit;
			}
			$p = WEBROOT . str_replace('/',$this->l,substr($p,1,strlen($p)));
	
		}
		if(file_exists($p))
		{
			@unlink($p);
		}
	}
	
	/***
	接受上传文件
	$p:文件
	$path : 文件存储路径
	$T：文件要求类型
	$Z: 文件大小
	$isnull:是否允许为空
	$info:相关信息
	$i:防止重复
	****/
function uplodefile($p,$path,$T,$Z,$isnull,$info,$i)
	{
		 if($p == ''  || $path == '' || $T == '' || $Z == '' || $isnull == '' ) return '';
		 if(!isset($_FILES[$p])) return '';
		 if($_FILES[$p]['size'] == 0 && $isnull ==0)
		 { 
		 	  if( $info==''  ) return '';
		 	  echo $GLOBALS['FLib']->Alert($info.'不能为空!','','BACK');
				exit;
		 }
		 if($_FILES[$p]['size'] == 0)
		 {
		 	 return '';
		 }
		 if($_FILES[$p]['size'] > $Z)
		 {
		 	   if( $info==''  ) return '';
		 	   echo $GLOBALS['FLib']->Alert($info.'超过规定大小!','','BACK');
				 exit;
		 }
		 if(!strstr(strtolower($T),$this->getFileType($_FILES[$p]['type'])))
		 {
		 	   if( $info==''  ) return '';
		 	   echo $GLOBALS['FLib']->Alert($info.'类型不匹配!','','BACK');
				 exit;
		 }
		$path .= date('Ymd'). '/';
		 $date = date('YmdHis');
		 $datefile = $date.$i;
		 $type1 = $_FILES[$p]['type'];
		 $save = $this->CreateFolder($path).$datefile .'.'.$this->getFileType($type1);
		 // $save;
		 $root = $path.$datefile .'.'.$this->getFileType($type1);
		 
		// echo $p;
		 // echo $save;
		 if (!move_uploaded_file($_FILES[$p]['tmp_name'],$save))
             {
                  echo "<SCRIPT language=JavaScript>alert('图片储存失败!');this.location.href='vbscript:history.back()';</SCRIPT>";
                  exit;
             }
		 return $root;	 
		if(!strstr($type1,'image'))
		{
			 return $root;
        }
		else
		{
			 if(strstr($type1,'image/bmp') != '')
				{
					$uploadnews1    =  $path.$datefile.'.jpg';
					$saveroot       =  WEBROOT.str_replace('/',L,substr($uploadnews1,1));
					//$saveroot      =  WEBROOT.str_replace('/',L,substr($saveserver,1));
					$im = $this->ImageCreateFromBMP($save);
					imagejpeg($im,$saveroot); 
					imagedestroy($im); 
					unlink($save);
					 if(!makethumb($saveroot,$saveroot,"100","200","1"))
						 {
						   echo "<SCRIPT language=JavaScript>alert('图片储存失败!');this.location.href='vbscript:history.back()';</SCRIPT>";
						   exit; 
						 }
						 else
						 {
						 return $uploadnews1;	
						 }
				}
			else
			{	
				 //global makethumb;
				 if(!makethumb($save,$save,"100","200","1"))
				 {
				   echo "<SCRIPT language=JavaScript>alert('图片储存失败!');this.location.href='vbscript:history.back()';</SCRIPT>";
				   exit; 
				 }
				 else
				 {
				 return $root;	
				 }
			}	 
		}

	}
	/**
	获取bmp数据
	**/
	function  ImageCreateFromBMP ($filename)
	{
		if  (! $f1  = fopen ($filename ,"rb")) return  FALSE ;
		$FILE  = unpack ("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread ($f1 ,14 ));
		if  ($FILE ['file_type'] != 19778 ) return  FALSE ;
		$BMP  = unpack ('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.  '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.  '/Vvert_resolution/Vcolors_used/Vcolors_important', fread ($f1 ,40 ));
		$BMP ['colors'] = pow (2 ,$BMP ['bits_per_pixel']);
		if  ($BMP ['size_bitmap'] == 0 ) $BMP ['size_bitmap'] = $FILE ['file_size'] - $FILE ['bitmap_offset'];
	$BMP ['bytes_per_pixel'] = $BMP ['bits_per_pixel']/8 ;
	$BMP ['bytes_per_pixel2'] = ceil ($BMP ['bytes_per_pixel']);
	$BMP ['decal'] = ($BMP ['width']*$BMP ['bytes_per_pixel']/4 );
	$BMP ['decal'] -= floor ($BMP ['width']*$BMP ['bytes_per_pixel']/4 );
	$BMP ['decal'] = 4 -(4 *$BMP ['decal']);
	if  ($BMP ['decal'] == 4 ) $BMP ['decal'] = 0 ;
	$PALETTE  = array ();
	if  ($BMP ['colors'] < 16777216 ) 
	{
	 $PALETTE  = unpack ('V'. $BMP ['colors'], fread ($f1 ,$BMP ['colors']*4 ));
		
	} $IMG= fread ($f1 ,$BMP ['size_bitmap']);
	$VIDE  = chr (0 );
	$res  = imagecreatetruecolor ($BMP ['width'],$BMP ['height']);
	$P  = 0 ;
	$Y  = $BMP ['height']-1 ;
	while  ($Y  >= 0 ) 
	{
		$X =0 ;
		while  ($X  < $BMP ['width']) 
		{
			if  ($BMP ['bits_per_pixel'] == 24 ) $COLOR  = unpack ("V",substr ($IMG ,$P ,3 ). $VIDE );
			elseif  ($BMP ['bits_per_pixel'] == 16 ) 
			{
			$COLOR  = unpack ("n",substr ($IMG ,$P ,2 ));
			$COLOR [1] = $PALETTE [$COLOR [1 ]+1 ];
			} elseif  ($BMP ['bits_per_pixel'] == 8 ) 
			{
			$COLOR  = unpack ("n",$VIDE.substr ($IMG ,$P ,1 ));
			$COLOR [1 ] = $PALETTE [$COLOR [1 ]+1 ];
		   } elseif  ($BMP ['bits_per_pixel'] == 4 ) 
		   {
		   	$COLOR  = unpack ("n",$VIDE.substr ($IMG ,floor ($P ),1 ));
			if  (($P *2 )%2  == 0 ) $COLOR [1 ] = ($COLOR [1 ] >> 4 ) ;
			else  $COLOR [1 ] = ($COLOR [1 ] &  0x0F );
			$COLOR [1 ] = $PALETTE [$COLOR [1 ]+1 ];

			} elseif  ($BMP ['bits_per_pixel'] == 1 ) 
			{
			$COLOR  = unpack ("n",$VIDE.substr ($IMG ,floor ($P ),1 ));
			if  (($P *8 )%8  == 0 ) $COLOR [1 ] = $COLOR [1 ] >>7 ;
			elseif  (($P *8 )%8  == 1 ) $COLOR [1 ] = ($COLOR [1 ] &  0x40 )>>6 ;
			elseif  (($P *8 )%8  == 2 ) $COLOR [1 ] = ($COLOR [1 ] &  0x20 )>>5 ;
			elseif  (($P *8 )%8  == 3 ) $COLOR [1 ] = ($COLOR [1 ] &  0x10 )>>4 ;
			elseif  (($P *8 )%8  == 4 ) $COLOR [1 ] = ($COLOR [1 ] &  0x8 )>>3 ;
			elseif  (($P *8 )%8  == 5 ) $COLOR [1 ] = ($COLOR [1 ] &  0x4 )>>2 ;
			elseif  (($P *8 )%8  == 6 ) $COLOR [1 ] = ($COLOR [1 ] &  0x2 )>>1 ;
			elseif  (($P *8 )%8  == 7 ) $COLOR [1 ] = ($COLOR [1 ] &  0x1 );
			$COLOR [1 ] = $PALETTE [$COLOR [1 ]+1 ];
			} else  return  FALSE ;
			imagesetpixel ($res ,$X ,$Y ,$COLOR [1 ]);
			$X ++;
			$P  += $BMP ['bytes_per_pixel'];			
	   } $Y --;
		$P +=$BMP ['decal'];
		
		} fclose ($f1 );
	return  $res ;
	}
	
		//ok
	//替换正则表大达式函数
	function FilterStr($str)
	{
        $FilterStr=$str;
        if ($str=="" or empty($FilterStr)) 
		{
            return "";
		}
        else
		{
            $FilterStr=str_replace("\\","\\\\",$FilterStr);
			//php中一个\表示正则。
            $FilterStr=str_replace("(","\\(",$FilterStr);
			//这也要进行双\\替换。
            $FilterStr=str_replace(")","\\)",$FilterStr);
            $FilterStr=str_replace("*","\\*",$FilterStr);
            $FilterStr=str_replace("?","\\?",$FilterStr);
            $FilterStr=str_replace("{","\\{",$FilterStr);
            $FilterStr=str_replace("}","\\}",$FilterStr);
            $FilterStr=str_replace(".","\\.",$FilterStr);
            $FilterStr=str_replace("+","\\+",$FilterStr);
            $FilterStr=str_replace("[","\\[",$FilterStr);
            $FilterStr=str_replace("]","\\]",$FilterStr);
			$FilterStr=str_replace("$","\\$",$FilterStr);
			return $FilterStr;
       }
    }
	//替换文件
		function Re_C($content,$pattern,$replacement)
	{
		   $Re_C = preg_replace($pattern,$replacement,$content); 
		   return $Re_C;

	}
	
	/*2009-08-10添加*/
	
	/***
	*函数名: 上传文件
	*参数：
	* $p: 上传文件变量
	* $path: 文件存储路径
	* $T：文件限制类型，以‘|’分隔不同类型
	* $Z: 文件限制大小
	*返回: 成功-文件上传地址,失败false
	****/
	function uplodefile_1($p,$path,$T='',$Z=0,$isnull=true)
	{
		if($p == '' || $path == '') return false;
		if(!isset($_FILES[$p])) return false;
		if($_FILES[$p]['size'] == 0 ){
			if($isnull)return '';
			echo '<script>alert("不能为空!");history.back();</script>';
			exit;
		}
		if($T != ''){
			$uptype = explode('|',$this->getFileType($_FILES[$p]['type']));
			$stype  = explode('|',$T);
			if(!array_intersect($stype,$uptype)){
				echo '<script>alert("文件类型不正确!");history.back();</script>';
				exit;
			}
		}
		if($Z != 0){
			if($_FILES[$p]['size'] > $Z){
				echo '<script>alert("文件过大!");history.back();</script>';
				exit;
			}
		}
		$path .= date('Ymd').'/';
		$f_new = date('YmdHis').substr(microtime(),2,4);
		$save = $this->CreateFolder($path).$f_new .strrchr($_FILES[$p]['name'],'.');
		$root = $path.$f_new.strrchr($_FILES[$p]['name'],'.');
		if(!move_uploaded_file($_FILES[$p]['tmp_name'],$save))
		{
			echo "<SCRIPT>alert('文件上传失败!');history.back();</SCRIPT>";
			exit;
		}
		return $root;
	}
	
	/*****
	*函数功能说明:将图片生成指定区域内的JPG新图；
	*参数：
	* $srcFile-原图片地址(物理),
	* $dstFile-新图片地址(物理)-新图必须为jpg后缀,
	* $w-等比缩的宽度 -0代表不限;
	* $h-等比缩的高度 -0代表不限;
	*返回：成功true,失败false
	*/
	function makeimage_1($srcFile,$dstFile,$w=0,$h=0)
	{
		if($srcFile == ''||$dstFile == '') return false;
		if(stripos($dstFile,'jpg') === false) return false;
		if(!file_exists($srcFile)) return false;
		$pd = getimagesize($srcFile);
		if(!$pd) return false;
		$w_init = $pd[0];$h_init = $pd[1];
		if($w != 0 && $h != 0){
			if($pd[0] > $w or $pd[1]> $h){
				if (($pd[0]/$w) > ($pd[1]/$h)){
					$pd[1] = ($pd[1]/$pd[0])*$w;
					$pd[0] = $w;
				}else{
					$pd[0] = ($pd[0]/$pd[1])*$h;
					$pd[1] = $h;
				}
			}
		}elseif($w != 0 && $h == 0){
			if($pd[0] > $w){
				$pd[1] = ($pd[1]/$pd[0])*$w;
				$pd[0] = $w;
			}
		}elseif($w == 0 && $h != 0){
			if($pd[1] > $h){
				$pd[0] = ($pd[0]/$pd[1])*$h;
				$pd[1] = $h;
			}
		}
		$w_new = intval($pd[0]);$h_new = intval($pd[1]);
		$t = $pd[2];
		$image_p = imagecreatetruecolor($w_new, $h_new);
		switch($t)
		{
			case 1:
				$image = imagecreatefromgif($srcFile);
				break;
			case 2:
				$image = imagecreatefromjpeg($srcFile);
				break;
			case 3:
				$image = imagecreatefrompng($srcFile);
				break;
			case 6:
				$image = $this->ImageCreateFromBMP($srcFile);
				break;
			default:
				return false;
		}
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $w_new, $h_new, $w_init, $h_init);
		imagejpeg($image_p,$dstFile,100);
		imagedestroy($image_p);
		imagedestroy($image);
		return true;
	}
	
	/*****
	*函数名:safeimage
	*函数功能说明:重画图片-生成JPG格式的新图；
	*参数：
	* $srcFile-图片地址(物理),
	* $percent-等比缩放比例;
	*返回：成功新名称,失败false
	*/
	function safeimage($srcFile,$percent=1)
	{
		if(!file_exists($srcFile))return false;
		list($w,$h,$t) = getimagesize($srcFile);
		$w_new = $w * $percent;
		$h_new = $h * $percent;
		
		$dstFile = str_replace(strrchr($srcFile,'.'),'_new',$srcFile).'.jpg';
		$image_p = imagecreatetruecolor($w_new, $h_new);
		switch($t)
		{
			case 1:
				$image = imagecreatefromgif($srcFile);
				break;
			case 2:
				$image = imagecreatefromjpeg($srcFile);
				break;
			case 3:
				$image = imagecreatefrompng($srcFile);
				break;
			case 6:
				$image = $this->ImageCreateFromBMP($srcFile);
				break;
			default:
				return false;
		}
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $w_new, $h_new, $w, $h);
		imagejpeg($image_p,$dstFile,100);
		imagedestroy($image_p);
		imagedestroy($image);
		unlink($srcFile);
		$dstFile_new = str_replace(strrchr($srcFile,'.'),'',$srcFile).'.jpg';
		rename($dstFile,$dstFile_new);
		return $dstFile_new;
	}
	
	
	/*****
	*函数名:makeimage
	*函数功能说明:将图片生成指定区域内的JPG新图；
	*参数：
	* $srcFile-原图片地址(物理),
	* $dstFile-新图片地址(物理)-新图必须为jpg后缀,
	* $w-等比缩的宽度 -0代表不限;
	* $h-等比缩的高度 -0代表不限;
	*返回：成功true,失败false
	*/
	function makeimage($srcFile,$dstFile,$w=0,$h=0)
	{
		if($srcFile == ''||$dstFile == '') return false;
		if(stripos($dstFile,'jpg') === false) return false;
		if(!file_exists($srcFile)) return false;
		$pd = getimagesize($srcFile);
		if(!$pd) return false;
		$w_init = $pd[0];$h_init = $pd[1];
		if($w != 0 && $h != 0){
			if($pd[0] > $w or $pd[1]> $h){
				if (($pd[0]/$w) > ($pd[1]/$h)){
					$pd[1] = ($pd[1]/$pd[0])*$w;
					$pd[0] = $w;
				}else{
					$pd[0] = ($pd[0]/$pd[1])*$h;
					$pd[1] = $h;
				}
			}
		}elseif($w != 0 && $h == 0){
			if($pd[0] > $w){
				$pd[1] = ($pd[1]/$pd[0])*$w;
				$pd[0] = $w;
			}
		}elseif($w == 0 && $h != 0){
			if($pd[1] > $h){
				$pd[0] = ($pd[0]/$pd[1])*$h;
				$pd[1] = $h;
			}
		}
		$w_new = intval($pd[0]);$h_new = intval($pd[1]);
		$t = $pd[2];
		$image_p = imagecreatetruecolor($w_new, $h_new);
		switch($t)
		{
			case 1:
				$image = imagecreatefromgif($srcFile);
				break;
			case 2:
				$image = imagecreatefromjpeg($srcFile);
				break;
			case 3:
				$image = imagecreatefrompng($srcFile);
				break;
			case 6:
				$image = $this->ImageCreateFromBMP($srcFile);
				break;
			default:
				return false;
		}
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $w_new, $h_new, $w_init, $h_init);
		imagejpeg($image_p,$dstFile,100);
		imagedestroy($image_p);
		imagedestroy($image);
		return true;
	}
	
	/***
	 压缩一个文件至压缩包
	$url     文件夹路径
	$tofile  压缩文件名
	$infile  要加入压缩包的文件
	***/
	public function ZipFile($url,$tofile,$infile,$def='')
	{
		//w_log($url.$tofile.$url.$infile);
		$zip=new ZipArchive;
		if ($zip->open($url.$tofile,ZIPARCHIVE::CREATE) === TRUE) {
			if (is_file($url.$infile)) {
				$zip->addFile($url.$infile, ($def?$def:$infile));
			}
			$zip->close();
			return true;
		} else {
			return false;
		}
	}
}
?>