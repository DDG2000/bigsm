<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 备份,恢复和优化数据库 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SYS_TOOL_DB_BACKUP');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');

$title = '备份,恢复和优化数据库';
$points = array('系统管理', '系统安全', $title);
$sTypes = array();
$hides  = array();
$extend = array();

$ths = array();
$ths[]=array('val'=>'备份时间', 'wid'=>'');
$ths[]=array('val'=>'文件名称', 'wid'=>'');
$ths[]=array('val'=>'文件大小', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');


$log=array();
$Dir = str_replace('\\','/',substr(WEBROOT,0,-1).$Config->DataFileDIRName);
$Dbfir = substr($Config->WebRoot,0,-1).$Config->DataFileDIRName;
if(is_dir($Dir)){
	if($dh = opendir($Dir)){
		while (($filename = readdir($dh)) !== false) {
			if($filename == '.' || $filename == '..') continue;
			if(substr($filename,strrpos($filename,'.')) == '.sql'){
				$file = $Dir.$filename;
				$filemtime = date('Y-m-d H:i:s',filemtime($file));
				$log[] = array(
					'filename' => $filename,
					'filesize' =>filesize($file),
					'addtime' => $filemtime,
					'filepath' =>$Dbfir.$file
				);
			}
		}
	}
}
$Rs=array_reverse($log);

$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$tds = array();
if(is_array($Rs)){
	$rscount = count($Rs);
	$pagecount = ceil($rscount/$pagesize);
	$extend['rscount']=$rscount;
	$stard=$pagesize*($CurrPage-1);$end=$CurrPage*$pagesize;
	for($i=$stard;$i<$end ;$i++){
		if($i>=$rscount) break;
		$fname = $Rs[$i]['filename'];
		$_td  = '<td>'.$Rs[$i]['addtime'].'</td>';
		$_td .= '<td>'.$Rs[$i]['filename'].'</td>';
		$_td .= '<td>'.round($Rs[$i]['filesize']/1024).'</td>';
		$_td .= '<td><a href="DBControlProcess.php?Work=Del&name='.$fname.'" class="goto" title="是否确认删除该文件">删除</a> | <a href="DBControlProcess.php?Work=Restore&name='.$fname.'" class="goto" title="是否确认恢复该文件">恢复</a> | <a href="DBControlProcess.php?Work=download&name='.$fname.'">下载</a></td>';
		$tds[]=$_td;
    }
}

$btns   = array();
$extend['gbtns'] = array();
$extend['gbtns'][] = '<a href="DBControlProcess.php?Work=" class="hs" w="500" h="300" olt="after" title="添加新备份"><span>添加新备份</span></a>';
$extend['gbtns'][] = '<a href="DBOptimize.php" class="hs" w="500" h="300" olt="after" title="优化数据库">优化数据库</a>';
$helps  = array();
$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'sKey', $sKey );
$tpl->assign( 'sType', $sType );
$tpl->assign( 'sTypes', $sTypes );
$tpl->assign( 'hides', $hides );
$tpl->assign( 'btns', $btns );
$tpl->assign( 'pagelistparam', $pagelistparam );
$tpl->assign( 'ths', $ths );
$tpl->assign( 'tds', $tds );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('list'.$raintpl_ver);
exit;
}
?>
<?php
/**
* 模块：基础模块
* 描述：用户列表页
* 作者：张绍海
* 书写日期：2011-03-16
* 修改日期：
*/ 

require_once"../include/TopFile.php";
$Admin->CheckPopedoms('SC_SYS_TOOL_DB'); 
$Admin->CheckPopedoms('SC_SYS_TOOL_DB_BACKUP'); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>备份,恢复和优化数据库</title>
<link href="../style/back_1.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../../style/Dialog.css" />
<script type="text/javascript" src="../../js/Dialog.js.php"></script>
<script src="../js/ie6.js.php" type="text/javascript"></script>
<SCRIPT LANGUAGE="JavaScript" src="../js/FunctionLib.js.php"></script>
<script src="../js/PageList.js.php"></script>
<script>
function refreshParent() {
   window.location.replace(window.location.href);
}
function AddReco()
{
        url= "DBBackup.php";
	    showPopWin(url, 500, 300, null);

}
function Add()
{
        url= "SelectBackUp.php";
   showPopWin(url, 300, 150, refreshParent, null);

}
function Alt()
{
   Alert("数据库恢复完毕",'');
}
function Restore(name)
{
   url= "SelectBackUpProcess.php?Work=Restore&name="+name;
   showPopWin(url, 700, 400, Alt, null);
}
</script>
</head>
<body>
<table border="0" cellspacing="0" cellpadding="0" id="main" align="center">
	<tr><td class="td_ct1"></td></tr>
	<tr>
		<td align="center">
			<table class="table_address" cellpadding="0" cellspacing="0"><tr><td class="td_address">当前位置：<a class="aaddress">系统工具</a> > <a class="aaddress">备份数据库</a></td>
			</tr></table>
		</td>
	</tr>
	<tr><td class="td_ct2"></td></tr>
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" class="table_page1">
				<tr>
					<td class="td_page1_1"><input name="fan" type="checkbox" value="" onclick="SelectAllCheckBox('IdList');"/>
				    反选&nbsp;&nbsp;&nbsp;&nbsp;<input name="hp" type="button" class="button2" value="添加新备份" onclick="Add()"/>&nbsp;&nbsp;<input name="hp2" type="button" class="button2" value="优化数据库" onclick="AddReco()"/></td>
			<td class="td_page1_2">
			</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center">
			<table class="table_content" cellpadding="0" cellspacing="1px">
				<tr>
					<td class="td_content2">&nbsp;</td>
					<td class="td_content2">备份时间</td>
					<td class="td_content2">文件名称</td>
					<td class="td_content2">文件大小</td>
					<td width="32%" class="td_content2">操作</td>
				</tr>
	<?php
	$log=array();
$Dir = str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']).$Config->DataFileDIRName;
if(is_dir($Dir))
{
		if($dh = opendir($Dir)){
			while (($filename = readdir($dh)) !== false) 
			{
					if($filename != '.' && $filename != '..')
					{
							if(substr($filename,strrpos($filename,'.')) == '.sql')
							{
								$file = $Dir.$filename;
								$filemtime = date('Y-m-d H:i:s',filemtime($file));
								$log[] = array(
											 'filename' => $filename,
											 'filesize' =>filesize($file),
											 'addtime' => $filemtime,
											 'filepath' =>$Config->WebRoot."export".$file);
							}
					}
			}
		}
}
?>
<?php
$log=array_reverse($log);
if(isset($log))
{
	if(is_array($log))
	{
		foreach($log as $value)
		{
	?>
				<tr>
				    <td class='td_content1'><input name='IdList' type='checkbox'class='input_checkbox' value=''></td>
					<td class=''><?php echo $value['addtime']; ?></td>
					<td class=''><?php echo $value ['filename'];?></td>
					<td class=''><?php echo round($value['filesize']/1024);?>kb</td>
					<td class=''>
					<a href="SelectBackUpProcess.php?Work=Del&name=<?php echo $value ['filename']; ?>">删除</a>
					<a href="javascript:Restore('<?php echo $value['filename']; ?>')">|恢复|</a>
					<a href="SelectBackUpProcess.php?Work=download&name=<?php echo $value ['filename'];?>">下载</a>
					</td>
				</tr>
	<?php
		}
	}
}else{					
	?>
     <tr><td colspan="8" align="center">暂无相关数据</td></tr>
	<?php
		}	
    $FLib->AdminSetcookie('backurl','DataBaseBackUp.php');
	?>
			</table>
		</td>
	</td>
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" class="table_page2">
				<tr>
					<td class="td_page1_1"><input name="fan" type="checkbox" value="" onclick="SelectAllCheckBox('IdList');"/>
				    反选&nbsp;&nbsp;&nbsp;&nbsp;<input name="hp" type="button" class="button2" value="添加新备份" onclick="Add()"/>&nbsp;&nbsp;<input name="hp2" type="button" class="button2" value="优化数据库" onclick="AddReco()"/></td>
			<td class="td_page1_2">
			</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td class="td_ct3"></td></tr>
	<tr>
		<td align="center">
			<table class="table_help" cellpadding="0" cellspacing="0">
			<tr>
				   <td class="td_help" style="CURSOR: hand"  onClick="if(document.getElementById('HelpTab').style.display=='none'){document.getElementById('HelpTab').style.display='';window.scrollTo(window.pageXOffset,2000);}else{document.getElementById('HelpTab').style.display='none'}">::Help::</td>
				</tr>
				
				 <tr Id="HelpTab" style="DISPLAY: none"> 
      <td  ><a name="Help"></a> <table width="100%" border="0" cellspacing="1" cellpadding="2">
          <tr> 
            <td align="left">
<p><span style='font-family:宋体;mso-ascii-font-family:"Times New Roman";mso-hansi-font-family:"Times New Roman";'>1.数据库优化需要一定的时间， 需耐心等待，</span></p>
<p><span style='font-family:宋体;mso-ascii-font-family:"Times New Roman";mso-hansi-font-family:"Times New Roman";'>2.可备份，数据库恢复后不能还原</span></p>

           </td>
         </tr>
			</table>
		</td>
	</tr>
	</table>
		</td>
	</tr>
	<tr><td class="td_ct1"></td></tr>
</table>
</body>
</html>

