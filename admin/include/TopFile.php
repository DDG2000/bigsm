<?php
/**
* 描述：引入系统的配置和函数库类形成全局通用的文件
* 作者：kign
* 书写日期：2011-03-16
*/

//报告运行时错误
error_reporting(E_ERROR | E_PARSE);
// error_reporting(E_ALL);
//header('Cache-control: private, must-revalidate');   
//开启缓存，
ob_start();
@header("Content-Type: text/html; charset=utf-8");
//定义根目录
if(!defined('L')){define('L',DIRECTORY_SEPARATOR);}
if(!defined('WEBROOT')){define('WEBROOT',dirname(dirname(dirname(__FILE__))).L);}
define('WEBROOTINC', WEBROOT.'include'.L );
define('WEBROOTDATA', WEBROOT.'data'.L );
define('WEBROOTINCCLASS', WEBROOT.'include'.L.'class'.L );
define('ADMININC', WEBROOT.'admin'.L.'include'.L );
$isDebug = false ;
if (file_exists(WEBROOTINC .'DataConfig_dev.php')){
	require(WEBROOTINC.'DataConfig_dev.php');
	$isDebug = true ;
} else {
	if(!file_exists(WEBROOTINC .'DataConfig.php')){
		header('Location: ../../install/php/InstallStepFirst.php');
		exit();
	}
	require(WEBROOTINC.'DataConfig.php');
}
define('IS_DEBUG',$isDebug) ;
require(WEBROOTINC . 'Constant.php');
require(WEBROOTINC . 'UserDBControl.class.php');
require(WEBROOTINC . 'UserFunction.class.php');
require(ADMININC . 'Manager.class.php');

//add log4php
require_once(WEBROOT . 'include' . L .'log4php'.L.'Logger.php') ;
Logger::configure(WEBROOT . 'include' . L .'log4php'.L.'config.xml') ;

$Config   = new DataConfig;
$DataBase = new UserDBControl($Config);
$FLib  = new UserFunction($Config);
$FLib->StartSession();
if (!$Config->Link) { $DataBase->OpenDataBase();}
$Admin = new Manager($Config, $DataBase, $FLib);

$Cfg = $Config;
$Db = $DataBase;
$FL = $FLib;


//调用网站公共函数文件
require(WEBROOTINC . 'PublicFun.php');
require(WEBROOTINC . 'common.php');

//调用网站参数文件 
if(file_exists(WEBROOTDATA.'config.cache.inc.php')){
	require_once(WEBROOTDATA.'config.cache.inc.php');
}
//调用数据字典
require(WEBROOTINC . 'data.dictionary.php');

//调用常用类文件
require_once(WEBROOTINCCLASS . 'User.php');
require_once(WEBROOTINCCLASS . 'Message.php');
require_once(WEBROOTINCCLASS . 'Loan.php');
require_once(WEBROOTINCCLASS . 'CacheManager.php');
//调用模板引擎
require(WEBROOTINC . 'rain.tpl.class.php');
//调用模板引擎后台配置
require(ADMININC . 'rain.tpl.config.php');
require(WEBROOTINC . 'config.p2p.php');
$p2pConfig= new ConfigP2p();
?>
