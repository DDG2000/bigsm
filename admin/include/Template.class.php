<?php

class Template
{
	var $FilePath,$Template  ;                       //'模板文件
		
		
	//待测
	function Write_file($path,$Content)
	{
		$path = WEBROOT.str_replace('/','\\',substr($path,1));
		$file_pointer = fopen($path,"w"); 
		fwrite($file_pointer,$Content); 
        fclose($file_pointer); 
	}
	
	//待测
	function read_file($path)
	{
		$path = WEBROOT.str_replace('/','\\',substr($path,1));
		$file_pointer = fopen($path,"r"); 
		$read_file = fread($file_pointer, filesize($path)); 
        if ($read_file=="")
		{
	        echo "<script language='javascript'>alert('文件 [".$path."] 没有内容!');history.back();</script>";
			exit;
		}
		return $read_file;
		fclose($file_pointer); 
	}
		
		
		
		

	
	

	//ok
	function Cut_P($s,$m,$str)
	{
		if ($str=="" or empty($str))
		{
		return;
		}
		return str_replace("<!--$scp:{".$s."}{".$m."}-->", "", $str);
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
	
	//ok
	//判断是文字,图片，FLASH
	function name_type($type1,$Width,$height,$str,$img_c)
	{
	//echo $type1."-->".$Width."-->"."---->".$height."--->".$str."-->".$img_c;
	//exit;
		if ($type1 == "img" )
		{
			$flash_arr = "";
			$is_flash = false;
			$flash_arr = explode(".",$str);
			if (strtolower($flash_arr[(count($flash_arr)-1)]) == "swf")
			{
				$is_flash = true;
			}
		}
		if (empty($str) or empty($type1))
		{
			$name_type="";
			return;
		}
		switch ($type1)
		{
 	        case "txt":
				$name_type = $str;
				break;
			case "img":
				if ($is_flash != true)
				{
					if ($Width!="" and is_numeric($Width) and $height!="" and is_numeric($height))
					{
						$name_type = "<img src='".$str."' width='".$Width."' height='".$height."' border='0' {class} />";
					}	
					else
					{
						$name_type = "<img src='".$str."' border='0' {class} />";
					}
					
					$name_type = str_replace("{class}",$img_c,$name_type);
				}	
				else
				{
					$name_type = $this->view_flash($Width,$height,$str);
				}	
				break;
			case "swf":
				$name_type = $this->view_flash($Width,$height,$str);
				break;
		    default:
	            $name_type="";
				
		}	
		return $name_type;	
	}
	
	//ok
	function view_flash($Width,$height,$str)
	{
		if (empty($str))
		{
			$view_flash = "";
			return;
		}
		if ($Width!="" and is_numeric($Width) and $height!="" and is_numeric($height))
		{
			$view_flash = "<script language='javascript'>write_flash('".$Width."','".$height."','".$str."');</script>";
		}
		else
		{
			$view_flash = "<script language='javascript's >write_flash1('".$str."');</script>";
		}
	    return $view_flash;
	}	
	
		
	





	
	function Re_C($content,$pattern,$replacement)
	{
		   $Re_C = preg_replace($pattern,$replacement,$content); 
		   return $Re_C;

	}
	
	//正则表达式函数

	
function get_content($Content,$patt)
	{
		preg_match_all($patt,$Content,$aa);
        //print_r($aa); 
		//echo "<br>";
		//return $aa[1][0];
		$Content="";
		for ($i=0;$i<(count($aa)-1);$i++)
		{
			$Content=$Content.$aa[1][$i];
		}
		$get_content=$Content;
		return $get_content;
	}
	
	
			
		function get_content_P($Content,&$scb_p,&$scm_p)
		{
			$pattern = '|\<\!\-\-\$scp\:\{([\S\s]*?)\}\{([\S\s]*)\}\-\-\>|';
			preg_match_all($pattern,$Content,$aa);
			$scb_p = $aa[1][0];
			$scm_p = $aa[2][0];
		}
	
	
		//ok                          
	function change_P($str_arr)
	{
		$str_arr = explode("|",$str_arr);
		for ($i=1;$i<count($str_arr);$i++)
		{
			if ($str_arr[$i]!="")
			{
				$str_arr[$i] = explode(",",$str_arr[$i]);
				if (count($str_arr[$i])==8 and $str_arr[$i][0] == "img") 
				{
				}
				elseif (count($str_arr[$i])!=7)
				{
                    echo "<script language='javascript'>alert('标签格式不正确');history.back();</script>";
					exit;
				}
			}
		}
		$change_P = $str_arr;
		return $change_P;
	}
	

}
?>