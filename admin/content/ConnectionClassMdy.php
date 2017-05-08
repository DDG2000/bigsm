<?php
/**
* 模块：基础模块
* 描述：用户修改密码和添加用户和 修改用户登，具体实现是根据接收到的$Work值来判断的
* 作者：张绍海
*/

//引入根目录下include里面的TopFile.php文件
require_once"../include/TopFile.php";
//$Admin->CheckPopedoms('SC_SITE_CONTENTCLASS_MDY');
$Work   = $FLib->RequestChar('Work',0,50,'参数',1);
if($Work=='AddReco')
{ 
	$pt='添加';
}
else if($Work=='MdyReco')
{
	$pt='修改';
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $pt; ?>分类</title>
<link href="../style/back_1.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../../style/Dialog.css" />
<script type="text/javascript" src="../../js/Dialog.js.php"></script>
<SCRIPT LANGUAGE="JavaScript" src="../js/FunctionLib.js.php"></script>
<script src="../js/ie6.js.php" type="text/javascript"></script>
<script type="text/javascript">
<!--
function sbc(obj)
{
	var d1 = obj.name.value;
	var d2 = obj.order.value;
	if(d1 == "")
	{
        Alert("类别名称不能为空！",'',obj.name);
		return false;
	}
	if (d1.length>7)
	{
        Alert("类别名称过长！",'',obj.name);
		return false;
	}
	if(d2 == "")
	{
        Alert("排序号不能为空！",'',obj.order);
		return false;
	}
	if(isNaN(d2))
	{
        Alert("排序号格式不正确！",'',obj.order);
		return false;
	}
	return true;
}
//-->
</script>
</head>

<body>
<table border="0" cellspacing="0" cellpadding="0" id="main" align="center">
	<tr><td class="td_ct1"></td></tr>
<?php
    if($Work=='AddReco')
	{ 
		    AddReco();
	}
	else if($Work=='MdyReco')
	{
	   MdyReco();
	}
	else
	{
		echo $FLib->Alert('参数错误','','');
	}
?>	
	
<?php
function AddReco()
{        
	    $Parent =$GLOBALS['FLib']->RequestInt('Parent',0,10,'父ID');
        $GLOBALS['Admin']->CheckPopedoms('SC_SYS_SET_USER_EDIT');
?>
<tr>
		<td align="center">
			<table class="table_address" cellpadding="0" cellspacing="0"><tr><td class="td_address">当前位置：<a  class="aaddress">友情链接管理</a> > <a  class="aaddress">添加类别</a></td>
			</tr></table>
		</td>
	</tr>
	<tr><td class="td_ct1"></td></tr>

	
	<tr>
		<td align="center">
			<table class="table_add2" cellpadding="0" cellspacing="0">
			<form name="form2" method="post" action="ConnectionClassProcess.php" onSubmit=" return sbc(this)">
			<tr><td class="td_add2_1" colspan="2"></td></tr>
			<tr>
				<td class="td_add2_2"><font class="red2">*</font> 分类名称：</td>
				<td class="td_add2_3"><input name="name" type="text" class="put1" style="width:200px;" > </td>
			</tr>	
			<tr>
				<td class="td_add2_2"><font class="red2">*</font> 排序号：</td>
				<td class="td_add2_3"><input name="order" type="text" class="put1" style="width:200px;" value="100" > </td>
			</tr>
			<tr>
				<td class="td_add2_2">备注：</td>
				<td class="td_add2_3"><textarea rows="" cols="" name="intro" id="intro" class="put3" style="width:300px;"></textarea></td>
			</tr>
			<tr><td class="td_add2_1" colspan="2"></td></tr>
			<tr>
				<td class="td_add2_4">&nbsp;</td>
				<td class="td_add2_5"><input name="butto1" type="submit" class="button1" value="确 认"/>&nbsp;&nbsp;&nbsp;&nbsp;<input name="butto2" type="reset" class="button1" value="重 置"/></td>
			</tr>
			<input type="hidden" name="Work" value="AddReco">
		    <input type="hidden" name="Parent" value="<?php echo $Parent ;?>">
			<tr><td class="td_add2_1" colspan="2"></td></tr>
			</form>
			</table>
		</td>
	</tr>
	<tr><td class="td_ct3"></td></tr>
	<tr>
		<td align="center">
		
			<table class="table_help" cellpadding="0" cellspacing="0">
				<tr>
				   <td class="td_help" 
style="CURSOR: hand"  onClick="if(document.getElementById('HelpTab').style.display=='none'){document.getElementById('HelpTab').style.display='';window.scrollTo(window.pageXOffset,2000);}else{document.getElementById('HelpTab').style.display='none'}">::Help::</td>
				</tr>
				
				 <tr Id="HelpTab" style="DISPLAY: none"> 
      <td  ><a name="Help"></a> <table width="90%" border="0" cellspacing="1" cellpadding="2">
          <tr> 
            <td>
<p><span style='font-family:宋体;mso-ascii-font-family:"Times New Roman";mso-hansi-font-family:"Times New Roman"; float:left;height:25px;'>1.*号为必填项！</span></p>
</br>
</td>
          </tr>
        </table></td>
    </tr>
				
			</table>
			
			
			
		</td>
	</tr>

	<?php
}
function MdyReco()
{  
	$Parent =$GLOBALS['FLib']->RequestInt('Parent',0,10,'父ID');
    $Id = $GLOBALS['FLib']->RequestInt('Id',0,50,'Id');
	$Re = $GLOBALS['DataBase']->SelectSql("select * from es_link_class where Status<>0 And ID=$Id limit 0,1");
	if($GLOBALS['DataBase']->GetResultRows($Re) == 0)
	{
		echo $GLOBALS['FLib']->Alert('记录未找到','','');
	    exit;
	}
    $Result = $GLOBALS['DataBase']->GetResultArray($Re);
?>
<tr>
		<td align="center">
			<table class="table_address" cellpadding="0" cellspacing="0"><tr><td class="td_address">当前位置：<a  class="aaddress">咨询类别</a> > <a  class="aaddress">修改咨询类别</a></td>
			</tr></table>
		</td>
	</tr>
	<tr><td class="td_ct1"></td></tr>

	
	<tr>
		<td align="center">
			<table class="table_add2" cellpadding="0" cellspacing="0">
			<form name="form2" method="post" action="ConnectionClassProcess.php" onSubmit="return sbc(this)">
			<tr><td class="td_add2_1" colspan="2"></td></tr>
			<tr>
				<td class="td_add2_2"><font class="red2">*</font> 分类名称：</td>
				<td class="td_add2_3"><input name="name" type="text" class="put1" style="width:200px;" 
				value="<?php echo $Result ['Vc_name']; ?>"> </td>
			</tr>
			
			<tr>
				<td class="td_add2_2"><font class="red2">*</font> 排序号：</td>
				<td class="td_add2_3"><input name="order" type="text" class="put1" style="width:200px;" value="<?php echo $Result ['I_order'] ;?>">
				</td>
			</tr>
			<tr>
				<td class="td_add2_2">备注：</td>
				<td class="td_add2_3"><textarea rows="" cols="" name="intro" id="intro" class="put3" style="width:300px;"><?php echo $Result['Vc_intro'];?></textarea></td>
			</tr>
			<tr><td class="td_add2_1" colspan="2"></td></tr>
			<tr>
				<td class="td_add2_4">&nbsp;</td>
				<td class="td_add2_5"><input name="butto1" type="submit" class="button1" value="确 认"/>&nbsp;&nbsp;&nbsp;&nbsp;<input name="butto2" type="reset" class="button1" value="重 置"/></td>
				<input name="Id" id="Id" value="<?php echo $Id;?>" type="hidden"/>
			</tr>
		    <input type="hidden" name="Parent" value="<?php echo $Parent ;?>">
			<input type="hidden" name="Work" value="MdyReco">
			<tr><td class="td_add2_1" colspan="2"></td></tr>
			</form>
			</table>
		</td>
	</tr>
	<tr><td class="td_ct3"></td></tr>
	<tr>
		<td align="center">
		
			<table class="table_help" cellpadding="0" cellspacing="0">
				<tr>
				   <td class="td_help" 
style="CURSOR: hand"  onClick="if(document.getElementById('HelpTab').style.display=='none'){document.getElementById('HelpTab').style.display='';window.scrollTo(window.pageXOffset,2000);}else{document.getElementById('HelpTab').style.display='none'}">::Help::</td>
				</tr>
				
				 <tr Id="HelpTab" style="DISPLAY: none"> 
      <td  ><a name="Help"></a> <table width="90%" border="0" cellspacing="1" cellpadding="2">
          <tr> 
            <td>
<p><span style='font-family:宋体;mso-ascii-font-family:"Times New Roman";mso-hansi-font-family:"Times New Roman"; float:left;'>1.*号为必填项！</span></p>
</br>
</br>
</td>
          </tr>
        </table></td>
    </tr>
				
			</table>
			
			
			
		</td>
	</tr>
<?php
}
?>	
	<tr><td class="td_ct1"></td></tr>
</table>
</body>
</html>

