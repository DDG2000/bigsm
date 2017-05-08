<?php
/****************************************************************** 
**本页： 数据库优化调用
**说明： 
******************************************************************/

require_once"../include/TopFile.php";
$Admin->CheckPopedoms('SC_SYS_TOOL_DB_OPTIMIZE'); 

$prefix = "\n";
set_time_limit(300);
ob_end_clean();
$style = '<style>';
$style .= '.tipsinfo { font-size: 12px; font-family: verdana;line-height: 20px;margin:0px;padding:0px;}';
$style .= '.red{ color: #cf1100;font-weight: bold;}';
$style .= '.green{ color: green;font-weight: bold;}';
$style .= '</style>';
echo $style;
flush();

$scroll = '<SCRIPT type=text/javascript>window.scrollTo(0,document.body.scrollHeight);</SCRIPT>';
//优化基本表
$SQL = 'SHOW TABLES FROM '.$Config->DataBaseName.' ';
$Result = $DataBase->SelectSql($SQL);
$tablenum = 0;
While( $Row = $DataBase->GetResultArray($Result) )
{
	$tablename = $Row['Tables_in_'.$Config->DataBaseName];
	$SQL = ' OPTIMIZE TABLE '. $tablename .' ';
	$DataBase->querySql($SQL);
	$str = '<div class="tipsinfo">优化表：'.$tablename.' ... <b class=green>Succ</b></div>';
	echo $prefix.$str.$scroll;
	flush();
	$tablenum++;
}
echo '<div class="tipsinfo"><b>优化表：'.$tablenum.'</b></div>'.$prefix;
flush();

?>