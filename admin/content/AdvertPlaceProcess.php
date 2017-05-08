<?php
/**
* 模块：基础模块
* 描述：广告位列表页
* 作者：张绍海
*/ 
//引入根目录下include里面的TopFile.php文件
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_ADPLACE_MDY');
$Work   = $FLib->RequestChar('Work',1,50,'参数1',1);
$title   = $FLib->RequestChar('title',1,50,'参数2',1);
//$file_name   = $FLib->RequestChar('file_name',1,50,'参数3',1);
//$Start_flag   = $FLib->RequestChar('start_flag',1,50,'参数4',1,3);
//$End_flag   = $FLib->RequestChar('end_flag',1,50,'参数5',1,3);
$width = $FLib->RequestInt('width',50,9,'宽度');
$height = $FLib->RequestInt('height',50,9,'长度');

$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
{
	case 'MdyReco': /**修改记录**/
		$Id   = $FLib->RequestInt('Id',0,9,'ID');
		$DataBase->QuerySql("Update site_advertise_place set Vc_name='$title',I_width='$width',I_height='$height' Where id=$Id");
		$Admin ->AddLog('内容管理','修改广告位：其Id为：'.$Id );
		echo showSuc('广告位编辑完毕',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'AddReco': /**增加记录**/
		$DataBase->QuerySql("insert into site_advertise_place(Vc_name,I_width,I_height,Createtime) values('$title', '$width' , '$height', now())");
		$Admin ->AddLog('内容管理','增加广告位：其名称为：'.$title);
		echo showSuc('广告位添加完毕',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'DeleteReco': /**删除记录**/
		$IdList = $FLib->RequestChar('IdList',1,10000,'IdList',1);
		if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		$DataBase->QuerySql('UPDATE site_advertise_place SET Status=0 WHERE ID IN('.$IdList.')');

		$Admin ->AddLog('内容管理','删除广告位：其ID为：'.$IdList);
		echo showSuc('广告位删除完毕',$FLib->IsRequest('backurl'),$obj);
		break;
} 
$DataBase->CloseDataBase();
?>










