
<?php
/**
* 模块：基础模块
* 描述：配置处理页
* 作者：张绍海
*/ 

require_once('../include/TopFile.php');
require_once(WEBROOT.'admin' . L . 'include' . L . 'File.class.php');
$FileClass = new FileClass;
$Admin->CheckPopedoms('SC_SYS_SET_CONFIG_MDY');

$Content   = $FLib->IsRequest('Content');
if ($Content =='')
{
	echo    $FLib->Alert('内容不能为空','','');
	exit;
}
$rt = $FileClass->WriteFile('../../include/Config.class.php',$Content);
if(isset($rt['err'])){echo showErr($rt['err']);exit;}

$Admin ->AddLog('系统管理','修改系统：配置文件！');
echo $FLib ->Alert('配置文件修改完毕','self',$FLib->IsRequest('backurl'));
$DataBase->CloseDataBase();
?>
