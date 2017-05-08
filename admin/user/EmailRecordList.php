<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 邮件 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER_EMAIL');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType ;

$title = '邮件记录';
$points = array('会员管理', '邮件管理', $title.'列表');
$sTypes = array('','邮件ID','邮件标题');
$hides  = array();
$extend = array();
switch ($sType)
{
	case 1:
		$mWhere = "a.I_emailID = '" . $sKey . "'";
		break;
	case 2:
		$mWhere = "b.Vc_title like '%" . $sKey . "%'";
		break;
	default:
		$mWhere = '1=1';
		break;
}
$tables = 'site_email_record a left join site_email b on a.I_emailID=b.ID where a.Status>0 and '.$mWhere.'';
$sql = "SELECT a.*,b.Vc_title FROM {$tables} order by a.ID desc";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'邮件ID', 'wid'=>'');
$ths[]=array('val'=>'邮件标题', 'wid'=>'');
$ths[]=array('val'=>'操作人员', 'wid'=>'');
$ths[]=array('val'=>'建立时间', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$content = strip_tags($Rs[$i]['T_content']);
		$_td  = '<td>'. $Rs[$i]['I_emailID'] .'</td>';
		$_td .= '<td>'. iset($Rs[$i]['Vc_title']) .'</td>';
		$_td .= '<td>'. $Admin->GetAdminInfo($Rs[$i]['I_operatorID'],1) .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array();
$btns[] = '<a href="EmailRecordProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删除</a>';
$extend['gbtns'] = array('<a href="EmailRecordProcess.php?Work=ClearReco" class="delall" title="清空所有记录,该操作不可恢复，请慎重！">清空所有</a>');
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
* 描述：邮件记录页
* 版本：管理系统 V0.1系统
* 作者：张绍海
* 书写日期：2011-03-16
* 修改日期：
*/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER_EMAIL_MDY');
$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>邮件发送记录</title>
<link href="../style/back_1.css" rel="stylesheet" type="text/css" />
<script src="../js/ie6.js.php" type="text/javascript"></script>
<script src="../js/FunctionLib.js.php" language="javascript"></script>
<script src="../js/PageList.js.php"></script>
</head>
<body>
<table border="0" cellspacing="0" cellpadding="0" id="main" align="center">
	<tr><td class="td_ct1"></td></tr>
	<tr>
		<td align="center">
			<table class="table_address" cellpadding="0" cellspacing="0"><tr><td class="td_address">当前位置：<a class="aaddress">会员管理</a> > <a class="aaddress">邮件记录</a></td>
			</tr></table>
		</td>
	</tr>
	<tr><td class="td_ct1"></td></tr>
	<tr>
		<td align="center">
		<form name="form1" method="post" action="EmailRecordList.php">
			<table class="table_search" cellpadding="0" cellspacing="0">
				<tr><td class="td_search">查询内容：
        <input name="sKey" type="text" class="put1" value="<?php echo $sKey;?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;查询类型：
		<select name="sType" class="put2">
          <option value="1" <?php if($sType == 1) echo 'selected="selected"';?>>邮件ID</option>
		  <option value="2" <?php if($sType == 2) echo 'selected="selected"';?>>邮件标题</option>
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
$sql = "select * from site_email_record where  Status<>0 and {WHERE} order by Createtime desc";
$sqlcount = "select count(*) from site_email_record where Status<>0 and  {WHERE}";
switch ($sType)
{
        case 1:
            $mWhere = "I_emailID like '%" . $sKey . "%'";
			break;
        case 2:
           $mWhere = "I_emailID in(select ID from site_email where status<>0 and Vc_title like '%" . $sKey . "%')" ;
			break;
        default:
            $mWhere = "'1'='1'";
			break;
}
$sql = str_replace('{WHERE}',$mWhere, $sql);
$sqlcount = str_replace('{WHERE}',$mWhere, $sqlcount);
$pagecount =1;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);
$UrlInfo = "&sKey=" . $sKey ."&sType=" . $sType;
if(is_array($Rs))
{
	$pagecount =$Rs[0]['pagecount'];
}		
?>
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" class="table_page1">
				<tr>
					
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
					<td class="td_content1">&nbsp;</td>
					<td class="td_content2">邮件ID</td>
					<td class="td_content2">邮件标题</td>
					<td class="td_content2">操作人员</td>
					<td class="td_content2">创建时间</td>
				</tr>
<?php
if(is_array($Rs))
{
	$pagecount =$Rs[0]['pagecount'];
	for($i=1;$i<count($Rs) ;$i++)
	{
?>				
				<tr>
				    <td class="td_content1"><?php echo $i ;?></td>
					<td><?php echo $Rs[$i]['I_emailID'];?></td>
					<td><?php
						$Rs1 = $DataBase->GettArrayResult("select Vc_title from site_email where status<>0 and ID='".$Rs[$i]['I_emailID']."'");
						if(is_array($Rs1))
						{
						   echo $Rs1[0][0];
						}
						else
						{
						   echo "----";
						}
					?></td>
					<td><?php echo $Admin->GetAdminInfo($Rs[$i]['I_operatorID'],1);?></td>
					<td style=" cursor:pointer;" title="<?php echo $Rs[$i]['Createtime'] ;?>"><?php echo $FLib->FromatDate($Rs[$i]['Createtime'],'Y-m-d') ;?></td> 
				</tr>
<?php
    }
}
else
{
     echo '<tr><td colspan="11" align="center">暂无相关数据</td></tr>';
}	
$FLib->AdminSetcookie('backurl','EmailRecordList.php?currpage='.$CurrPage.$UrlInfo);
$DataBase->CloseDataBase();
?>
			</table>
		</td>
	</td>
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" class="table_page2">
				<tr>
					
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
<p><span style='font-family:宋体;mso-ascii-font-family:"Times New Roman";mso-hansi-font-family:"Times New Roman";'>1.点击"编辑"可重新编辑邮件内容</span></p>
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
