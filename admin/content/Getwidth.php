<?php
require_once('../include/TopFile.php');
$id     = $FLib->requestint('id',0,9,'广告位ID');
$width = 935;
$height = 115;
$Rs = $DataBase->GettArrayResult('select* from site_advertise_place where Status <> 0 and ID='.$id);
if(is_array($Rs)){
	$width= $Rs[0]['I_width'];
	$height = $Rs[0]['I_height'];
}
$Work     = $FLib->requestchar('Work',1,50,'参数',1);
switch($Work){
	case 'PROD':
		echo $width,'|||||',$height;	
		break;
	default:
		echo 'no';
		exit;
}

?>