<?php
/**
*参数分组：key=>数据库中I_group字段
*/
$grouparr = array(
	7=>array('SC_SITE_CONFIG_WEB','网站参数'),
	99=>array('SC_SITE_CONFIG_SYS','运行环境参数'),
	11=>array('SM_SHOP_CONFIG','商铺参数')
);
/*
*全局网站参数写入缓存文件
*/
function writeConfigValue(){
	global $DataBase;
	$configfile = WEBROOT.'data'.L.'config.cache.inc.php';
	$rn = "\r\n";
	$str = '<?php'.$rn;
	$str .= '$g_conf=array('.$rn;
	$Rs = $DataBase->GettArrayResult("select Vc_name,Vc_value from site_parameter where status=1 order by Vc_name");
	if($Rs){
		$L = count($Rs);
		for($i=0;$i<$L;$i++){
			$str .= "'".$Rs[$i][0]."'=>";
			if(is_numeric($Rs[$i][1])){
				$str .= $Rs[$i][1];
			}else{
				$str .= "'".str_replace("'",'',$Rs[$i][1])."'";
			}
			$str .= ($i<$L?',':'').$rn;
		}
	}
	$str .= ')'.$rn;
	$str .= '?>';
	return writeincdata($str, $configfile);
}

?>
