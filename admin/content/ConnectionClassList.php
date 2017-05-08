<?php
/**
* 模块：基础模块
* 描述：咨询类别列表页
* 作者：张绍海
*/ 
//引入根目录下include里面的TopFile.php文件
require_once('../include/TopFile.php');
//$Admin->CheckPopedoms('SC_SITE_CONTENTCLASS');
$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$Parent = $FLib->RequestInt('Parent',0,10,'父ID');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>分类列表</title>
<link href="../style/back_1.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../../style/Dialog.css" />
<script type="text/javascript" src="../../js/Dialog.js.php"></script>
<script src="../js/ie6.js.php" type="text/javascript"></script>
<script src="../js/FunctionLib.js.php" language="javascript"></script>
<script src="../js/PageList.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function AddReco(Parent)
{
   var url = "ConnectionClassMdy.php?Work=AddReco&Parent=" + Parent;
   showPopWin(url, 700, 400, refreshParent, null);
}

function MdyReco(mId,Parent)
{
      var url = "ConnectionClassMdy.php?Work=MdyReco&Id=" + mId+"&Parent=" + Parent;
      showPopWin(url, 700, 400, refreshParent, null);
}

function DeleteReco(IdList)
{
    if(IdList == "") return false;
	Alert('是否确定要删除选中项？','1','',"ConnectionClassProcess.php?Work=DeleteReco&IdList=" + IdList);
}
//-->
</SCRIPT>
</head>
<body>
<table border="0" cellspacing="0" cellpadding="0" id="main" align="center">
	<tr><td class="td_ct1"></td></tr>
	<tr>
		<td align="center">
			<table class="table_address" cellpadding="0" cellspacing="0"><tr><td class="td_address">当前位置：<a class="aaddress">网站管理</a> > <a class="aaddress">友情链接管理</a> </td>
			</tr></table>
		</td>
	</tr>
	<tr><td class="td_ct1"></td></tr>
	<tr>
		<td align="center">
		<form name="form1" method="post" action="ConnectionClassList.php">
			<table class="table_search" cellpadding="0" cellspacing="0">
				<tr><td class="td_search">
				<input type="hidden" name="Parent" value="<?php echo $Parent ;?>">
				查询内容：
        <input name="sKey" type="text" class="put1" value="<?php echo $sKey;?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;查询类型：
		<select name="sType" class="put2">
          <option value="0" <?php if($sType == 0) echo 'selected="selected"';?>>　　　　</option>
          <option value="2" selected="selected" <?php if($sType == 2) echo 'selected="selected"';?>>类别名称</option>
        </select>&nbsp;&nbsp;&nbsp;&nbsp;<input name="Submit" type="submit" class="button1" value="搜 索"/>
		</td>
				
				  </tr>
			</table>
		</form>	
		</td>
	</tr>
	<tr><td class="td_ct2"></td></tr>
	<?php
$pagesize = $Config->AdminPageSize;
$sql = 'select * from es_link_class  where {WHERE}  And Status<>0 order by I_order desc,id desc ';
$sqlcount = 'select count(*) from es_link_class where {WHERE}  And Status<>0 ';
switch ($sType)
{
        case 2:
            $mWhere = "Vc_name like '%" . $sKey . "%'";
			break;
        default:
            $mWhere = "'1'='1'";
			break;
}
$mWhere = $mWhere . " AND I_partentID =" .$Parent;
$sql = str_replace('{WHERE}',$mWhere, $sql);
$sqlcount = str_replace('{WHERE}',$mWhere, $sqlcount);
$pagecount =1;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$UrlInfo = "&Parent=" . $Parent ."&sKey=" .urlencode($sKey) ."&sType=" . $sType ;
if(is_array($Rs))
{
	$pagecount =$Rs[0]['pagecount'];
}	
	?>
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" class="table_page1">
				<tr>
					<td class="td_page1_1"><input name="fan" type="checkbox" value="" onclick="SelectAllCheckBox('IdList');"/>
				    反选&nbsp;&nbsp;&nbsp;&nbsp;<input name="hp" type="button" class="button1" value="添 加" onclick="AddReco('<?php echo $Parent ;?>')"/>&nbsp;&nbsp;<input name="hp2" type="button" class="button1" value="删除" onclick="DeleteReco(GetCheckBoxList('IdList'))"/></td>
			<td class="td_page1_2">
			共<font class="red2" style="font-weight:bold;"><?php echo $Rs[0]['rscount']?$Rs[0]['rscount']:0;?></font>条记录&nbsp;
			<script language="JavaScript">
				<!--
				var plb = new PageListBar('plb',<?php echo $pagecount;?>, <?php echo $CurrPage;?>, '<?php echo $UrlInfo;?>', <?php echo $Config->AdminPageSum;?>);
				   document.write(plb);
				//-->
	        </script>
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
					<td class="td_content2">类别名称</td>
					<td class="td_content2">排序号</td>
				
					<td class="td_content2">创建时间</td>
					<td class="td_content2">操作</td>
				</tr>
<?php
if(is_array($Rs))
{
	$pagecount =$Rs[0]['pagecount'];
	for($i=1;$i<count($Rs) ;$i++)
	{
?>				
				<tr>
				    <td class="td_content1"><input name="IdList" type="checkbox" value="<?php echo $Rs[$i]['ID'] ;?>"/></td>
					<td class=""><!--<A HREF="?Parent=<?php echo $Rs[$i]['ID'] ;?>">--><?php echo  $FLib->CutStr($Rs[$i]['Vc_name'],50) ;?><!--</a>--></td>
					<td class=""><?php echo $Rs[$i]['I_order'] ;?></td>
					
					<td class="" style=" cursor:pointer;" title="<?php echo $Rs[$i]['Createtime'] ;?>"><?php echo $FLib->FromatDate($Rs[$i]['Createtime'],'Y-m-d') ;?></td>             <td> <a href="javascript:MdyReco('<?php echo $Rs[$i]['ID'] ;?>','<?php echo $Parent ;?>');void(0);">编辑</a></td>
				</tr>
<?php
    }
}

else
{
     echo '<tr><td colspan="8" align="center">暂无相关数据</td></tr>';
}	
$FLib->AdminSetcookie('backurl','ConnectionClassList.php?currpage='.$CurrPage.$UrlInfo);
 $DataBase->CloseDataBase();
?>
			</table>
		</td>
	</td>
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" class="table_page2">
				<tr>
					<td class="td_page2_1"><input name="fan" type="checkbox" value="" onclick="SelectAllCheckBox('IdList');"/>
				    反选&nbsp;&nbsp;&nbsp;&nbsp;<input name="hp" type="button" class="button1" value="添 加" onclick="AddReco('<?php echo $Parent ;?>')"/>&nbsp;&nbsp;<input name="hp2" type="button" class="button1" value="删除" onclick="DeleteReco(GetCheckBoxList('IdList'))"/></td>
					<td class="td_page2_2">
					共<font class="red2" style="font-weight:bold;"><?php echo $Rs[0]['rscount']?$Rs[0]['rscount']:0;?></font>条记录&nbsp;
			<script language="JavaScript">
				<!--
                    var plb = new PageListBar('plb',<?php echo $pagecount;?>, <?php echo $CurrPage;?>, '<?php echo $UrlInfo;?>', <?php echo $Config->AdminPageSum;?>);
				   document.write(plb);
				//-->
	        </script></td>
				</tr>
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
            <td align="left">
<p><span style='font-family:宋体;mso-ascii-font-family:"Times New Roman";mso-hansi-font-family:"Times New Roman";'>1.根据查询条件进行查询</span></p>
</td>
          </tr>
		  
        </table></td>
    </tr>
			
			</table>
		</td>
	</tr>
	<tr><td class="td_ct1"></td></tr>
</table>
</body>
</html>

