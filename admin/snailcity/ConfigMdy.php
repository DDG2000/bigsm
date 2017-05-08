<?php
/**
* 作者：张绍海
* 书写日期：2011-03-16
* 描述：配置处理页
*/
require_once('../include/TopFile.php');
$DataBase->CloseDataBase();
require_once(WEBROOT.'admin' . L . 'include' . L . 'File.class.php');
$FileClass = new FileClass;
$Admin->CheckPopedoms('SC_SYS_SET_CONFIG');
$File = '../../include/Config.class.php';
$rt = $FileTemplate = $FileClass->ReadFile($File);
if(isset($rt['err'])){echo showErr($rt['err']);exit;}
$Filemtime = $FileClass->ReadFile($File,2);
$FLib->AdminSetcookie('backurl','ConfigMdy.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>配置文件编辑</title>
<link href="../style/back_1.css" rel="stylesheet" type="text/css" />
<script src="../js/ie6.js.php" type="text/javascript"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function chkForm_MdyFile(obj)
{
    if(obj.Content.value == "")
    {
		Alert("配置内容有误",'',obj.Content);
        return false;
    }
}
//-->
</SCRIPT>
</head>
<body>
<table border="0" cellspacing="0" cellpadding="0" id="main" align="center">
	<tr><td class="td_ct1"></td></tr>
	<tr>
		<td align="center">
			<table class="table_address" cellpadding="0" cellspacing="0"><tr><td class="td_address">当前位置：<a class="aaddress">系统工具</a> > <a class="aaddress">系统配置文件</a></td>
			</tr></table>
		</td>
	</tr>
	<tr><td class="td_ct2"></td></tr>
	<tr>
		<td align="center">
<form name="form1" method="post" action="ConfigProcess.php" onSubmit="return chkForm_MdyFile(this)">					
			<table class="table_add2" cellpadding="0" cellspacing="0" style="height:300px;">
			
			<tr><td class="td_add2_1" colspan="2"></td></tr>
			
			<tr>
				<td class="td_add2_2_1" valign="top">配置文件：</td>
				<td class="td_add2_3_1" ><textarea name="Content" cols="90" rows="40" wrap="OFF" style="width:98%;" class="input_text"><?php echo $FileTemplate ;?></textarea></td>
			</tr>
			<tr>
				<td class="td_add2_2_1" width="9%">文件属性：</td>
				<td class="td_add2_3_1" width="91%">上次修改于:<b><?php echo $Filemtime ;?></b>；当前文件大小为<b><?php echo filesize($File); ?>/B</b></td>
			</tr>
			<tr><td class="td_add2_1" colspan="2"></td></tr>
			<tr>
				<td class="td_add2_6" colspan="2"><input name="submit1" type="submit" class="button1" value="确 认" />&nbsp;&nbsp;&nbsp;&nbsp;<input name="submit12" type="button" class="button1" value="返 回" onClick="window.history.back()"/></td>
			</tr>
			<tr><td class="td_add2_1" colspan="2"></td></tr>
			</table>
</form>			
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
<p><span style='font-family:宋体;mso-ascii-font-family:"Times New Roman";mso-hansi-font-family:"Times New Roman";'>1.角色指明该用户是属于哪个角色，如果为空，则该用户不属于任何角色</span></p>
<p><span style='font-family:宋体;mso-ascii-font-family:"Times New Roman";mso-hansi-font-family:"Times New Roman";'>2.编辑：可对信息进行修改</span></p>
<p><span style='font-family:宋体;mso-ascii-font-family:"Times New Roman";mso-hansi-font-family:"Times New Roman";'>3.禁用：禁止此用户使其不能登录，解禁后恢复正常</span></p>
<p><span style='font-family:宋体;mso-ascii-font-family:"Times New Roman";mso-hansi-font-family:"Times New Roman";'>4.搜索：可根据设置的条件进行搜索查询，任一条件为空默认搜索所有记录</span></p>
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

