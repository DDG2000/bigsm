<?php 
require_once('UserConfig.class.php');
class    DataConfig   extends    UserConfig {
	var $DataBaseHost                  =     '192.168.11.20';
	var $DataBaseUser                  =     'root';
	var $DataBasePassword              =     'sysroot';
	var $DataBaseName                  =     'bigsm';
	var $SuperAdminEmail               =     'admin@nimda.com';
	var $WebRoot                       =     'http://www.bigsm.com/';
	var $SessionHeadName               =     'bigsm';
	var $ErrorLogListWrite             =     TRUE;
	//前段cookie过期时间，建议与session过期时间保持一致
	var $expirationTime                =     1440;
	//网站用户每日推荐邮件发送上限
	var $emailCount                    =     10;
}