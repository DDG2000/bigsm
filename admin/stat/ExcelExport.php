<?php
if(1){
require_once('../include/TopFile.php');

$returnurl = $FLib->RequestChar('returnurl',1,500,'参数',1);

$points = array('Excel', '数据导出');

//删除24小时以前的导出包
$btime = time()-86400;
$all_dir = WEBROOTDATA.'excel_out'.L;
$eot_del = $all_dir.'excelouttime_del.php';
if(file_exists($eot_del)) require $eot_del;
else excelouttime_fw($eot_del);
if($excelouttime_del<$btime){
	//获取小于24小时的文件
	if(is_dir($all_dir)){
		if ($dh = opendir($all_dir)){
			$patten = "/^\d{4}-\d{2}-\d{2}_\d{2}_\d{2}$/";
			while (($file = readdir($dh)) !== false){
				$filetime = substr($file, (strlen($file)-20),16);
				if (preg_match_all($patten, $filetime)){
					$filetime = str_replace("_", ":", $filetime);
					$filetime = $filetime.':00';
					$filetime = substr_replace($filetime,' ','10','1');
					$filetime = strtotime($filetime);
					if($filetime<$btime)@unlink($all_dir.$file);
				}
			}
			closedir($dh);
		}
	}else{
	mkdir($all_dir);
	}
	excelouttime_fw($eot_del);
} 


$tpl = new RainTPL;
$tpl->assign( 'returnurl', $returnurl );
$tpl->assign( 'points', $points );

$tpl->draw('excelExport'.$raintpl_ver);

exit;
}

function excelouttime_fw($fname){
	//更新时间
	$str = '<?php $excelouttime_del='.time().'; ?>';
	$r = writeincdata($str, $fname);
	if($r[0]<1){
		return array('flag'=>0,'err'=>$r[1]);
	}
	return array('flag'=>1);
}
	?>