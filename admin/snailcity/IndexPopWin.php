<?php
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 主框架 管理
**说明：
******************************************************************/
require_once('../../include/UserConfig.class.php');
$Config = new UserConfig;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $Config->SysName ;?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="xww,kign" />
<meta name="Version" content="Snail Up V3.0" />
</head>
<script type="text/javascript">
<!--
function hideMenu(){document.getElementById("fram1").cols="0,*";}
function showMenu(){document.getElementById("fram1").cols="186,*";}
function toMenu(url){window.Menu.location.href=url;}
function flushCon(){window.Cont.location.reload();}
function toCon(url){window.Cont.location.href=url;}
//退出系统
function exitSys(){if(confirm('你确定退出系统？')){window.location.href='LoginProcess.php?Work=LoginOff';}}
//超时处理
function overTime(){window.location.href='../index.php';}
//-->
</script>
<frameset rows="75,*" cols="*" framespacing="0" frameborder="yes" border="0">
	<frame src="FrameTop.php" name="frameTop" frameborder="no" scrolling="no" noresize />
	<frameset rows="*" cols="186,*" framespacing="no"  frameborder="yes" border="0" id="fram1">
		<frame src="FrameMenu.php" name="Menu" frameborder="no" scrolling="no" />
		<frame src="#" name="Cont" frameborder="no" scrolling="auto"/>
	</frameset>
</frameset>
<noframes> 
<body>
</body>
</noframes> 
</html>