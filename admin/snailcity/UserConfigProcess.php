<?php
/**
* 模块：基础模块
* 描述：用户配置处理页
*/ 
require_once('../include/TopFile.php');
require_once(WEBROOT.'admin' . L . 'include' . L . 'File.class.php');
$FC = new FileClass;
$Admin->CheckPopedoms('SC_SYS_SET_USERCONFIG_MDY');
$file = $FLib->RequestChar('file',0,50,'文件',0);
$var  = $FLib->RequestChar('var',0,20,'变量名',0);

$adminuseremail=$FLib->RequestChar('adminuser',0,50,'管理员邮箱',0);
$website=$FLib->RequestChar('website',0,200,'网站网址',0);
$sessionhead=$FLib->RequestChar('sessionhead',0,50,'session前缀名',0);
$islog=$FLib->RequestChar('islog',0,20,'错误日志记录',0);
$configStr1="<?php \n\r";
$configStr1.="require_once('UserConfig.class.php');\n";
$configStr1.="class    DataConfig   extends    UserConfig {\n\r";
$configStr1 .= 'var $DataBaseHost                  =     \''.$Config->DataBaseHost.'\';'."\n".' ';
$configStr1 .= 'var $DataBaseUser                  =     \''.$Config->DataBaseUser.'\';'."\n".'';
$configStr1 .= 'var $DataBasePassword              =     \''.$Config->DataBasePassword.'\'; '."\n".' ';
$configStr1 .= 'var $DataBaseName                  =     \''.$Config->DataBaseName.'\'; '."\n".' ';
$configStr1 .= 'var $SuperAdminEmail               =     \''.$adminuseremail.'\'; '."\n".' ';
$configStr1 .= 'var $WebRoot                       =     \''.$website.'\'; '."\n".' ';
$configStr1 .= 'var $SessionHeadName               =     \''.$sessionhead.'\'; '."\n".' ';
$configStr1 .= 'var $PasswordEncodeKey             =     \''.$Config->PasswordEncodeKey.'\';'."\n".'';
$configStr1 .= 'var $ErrorLogListWrite             =     '.$islog.'; '."\n".'';
$configStr1 .="}\n\r?>\n";

@chmod(WEBROOT.'/include',0777);

if(!($fp = @fopen("../../include/DataConfig.php","w"))){ echo showErr('写入配置失败，请检查../include/DataConfig.php目录是否可写入'); exit; }
fwrite($fp,$configStr1);
fclose($fp);

$Admin ->AddLog('系统管理','修改系统参数');
echo showSuc('参数修改成功',$FLib->IsRequest('backurl'),'self');
exit;
?>