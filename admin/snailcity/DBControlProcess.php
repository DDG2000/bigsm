<?php
error_reporting(0);
require_once"../include/TopFile.php";
$Admin->CheckPopedoms('SC_SYS_TOOL_DB_BACKUP_MDY'); 


$dbfolder = str_replace('\\','/',substr(WEBROOT,0,-1).$Config->DataFileDIRName);

$Work=$FLib->IsRequest('Work');
$Name=$FLib->IsRequest('name');
if ($Work ==''){   
	include_once('../include/DataBaseBackup.class.php') ;
	$mysqlbackup = new MySQL_BackUp();
	$mysqlbackup->server = $Config->DataBaseHost ;
	$mysqlbackup->username = $Config->DataBaseUser;
	$mysqlbackup->password = $Config->DataBasePassword;
	$mysqlbackup->database = $Config->DataBaseName;
	$mysqlbackup->Read_Char= $Config->ReadChar;
    $mysqlbackup->backup_dir=$dbfolder;

	echo '数据库备份中111......';
	if( $mysqlbackup->Execute(2) )
	{
		 $Admin ->AddLog('系统工具','数据库备份成功');
		 echo showSuc('数据库备份成功',$FLib->IsRequest('backurl'),'parent');
	}
	else
	{
		echo showErr('数据库备份失败'); exit;
	}

}
else if ($Work=='download')
{	
	@set_time_limit( 0 );
	if (!file_exists($dbfolder.$Name))  //检查文件是否存在
	{
		echo showErr('文件不存在'); exit;
	} 
	else
	{
          $fullname="../../export/".$Name;
          header("Content-Type:application/octet-stream");
          $filesize = filesize($fullname);
          header("Content-Disposition: attachment; filename=".$Name."; charset=utf-8");
          header("Content-Length: ".$filesize."");
          readfile($fullname);
          exit;
	}

}
else if ($Work=='Del')
{
    $Name=$dbfolder.$_GET['name'];
	if(file_exists($Name))
	{
		if(@unlink($Name))
		{
			echo showSuc('文件删除完毕',$FLib->IsRequest('backurl'),'self');
		}
		else
		{
			echo showErr('文件删除失败'); exit;
		}
	}
}
else if ($Work=='Restore')
{		
	@set_time_limit( 0 );
	$Admin->CheckPopedoms('SC_SYS_TOOL_DB_RESTORE'); 
    $Name=$_GET['name'];
	if (!file_exists($dbfolder.$Name))  //检查文件是否存在
	{
		echo showErr('文件不存在'); exit;
	}
	$sqlfile =$dbfolder.$Name;
	$fp = fopen($sqlfile, 'rb');
	$sql = fread($fp, filesize($sqlfile));
	fclose($fp);
	if(itenable_run_query($sql))
	{  
		$Admin ->AddLog('系统工具',"数据库".$Name."恢复");
	    echo '数据库恢复成功';
		exit;
	}
	else
	{
		echo showErr('数据库恢复失败'); exit;
	}
}
function itenable_run_query($sql) 
{	global $Config;
	mysql_connect($Config->DataBaseHost,$Config->DataBaseUser,$Config->DataBasePassword) or die('s');
    mysql_select_db($Config->DataBaseName)or die('d');
	mysql_query("set names ".$Config->ReadChar)or die('sf');
	$ret = array();
	$num = 0;
	$sql = str_replace("\r\n", "\n",$sql);
	foreach (explode(";\n", trim($sql)) as $query) 
	{
		$queries = explode("\n", trim($query));
		foreach($queries as $query) 
		{
			$ret[$num] .=$query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
		}
		$num++;
	}
	unset($sql);
	ob_end_clean();
	$style = "<style>\n";
	$style .= ".tipsinfo { font-size: 12px; font-family: verdana;line-height: 10px;margin:0px;padding:0px; height:300px width:600px;OVERFLOW-y:auto;OVERFLOW:scroll}\n";
	$style .= ".red{ color: #cf1100;font-weight: bold;}\n";
	$style .= ".green{ color: green;font-weight: bold;}\n";
	$style .= "</style>\n";
	echo $style;
	flush();
	$scroll = "<SCRIPT type=text/javascript>window.scrollTo(0,document.body.scrollHeight);</SCRIPT>";
	$prefix = "";
	for ( $i=0; $i<300; $i++ ) $prefix .= " \n";
   $tablenum = 0;
	foreach ($ret as $query) 
	{
		$query = trim($query);
		if($query)
		{	
			if(substr($query, 0, 12) == 'CREATE TABLE')
			{ 	
				$name = preg_replace("/CREATE TABLE `([a-z0-9_]+)` .*/is", "\\1", $query);
				$str = "<div class='tipsinfo' id='a'>正在恢复表".$name." ...已恢复".$tablenum."个表 </div>";
				ob_end_clean();
				echo $prefix.$str.$scroll;
				flush();
				ob_flush();
		        mysql_query($query);
				$tablenum++;
		    }
			else
			{
				mysql_query($query);
			}
	    }
	}
	return true;
}

?>

