<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 修改黑/白名单 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$Status  = $FLib->RequestInt('Status',0,9,'类型');

$title = '注册用户信息';
$points = array('会员管理', $title);
$params = array();
$helps  = array();
$extend = array();

$Rs = $DataBase->GetResultOne("SELECT count(*) FROM user_base WHERE Status>0");
$Rs1 = $DataBase->GetResultOne("SELECT count(*) FROM user_base WHERE Status>0 and DATE_SUB(CURDATE(),INTERVAL 7 DAY) <= date(Createtime)");
$Rs2 = $DataBase->GetResultOne("SELECT count(*) FROM user_base WHERE Status>0 and to_days(Createtime) = to_days(now())");
$params[] = array('name'=>'注册会员总数','val'=>$Rs[0]);
$params[] = array('name'=>'最近一周注册会员总数','val'=>$Rs1[0]);
$params[] = array('name'=>'今天注册会员数','val'=>$Rs2[0]);
$extend['jpg'] = '../image/title/4.png';

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'params', $params );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('logininfo'.$raintpl_ver);
exit;
}
?>
<?php
/**
* 模块：基础模块
* 描述：会员信息页
* 版本：SnailCity内容管理系统 V0.1系统
* 作者：张绍海
* 书写日期：2011-03-16
* 修改日期：
*/
require_once('../include/TopFile.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $Config->SysName ;?></title>
<link href="../style/back_1.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table border="0" cellspacing="0" cellpadding="0" id="main" align="center">
	<tr><td class="td_ct1"></td></tr>
	<tr>
		<td align="center">
			<table class="table_address" cellpadding="0" cellspacing="0"><tr><td class="td_address">当前位置：<a  class="aaddress">会员管理</a> > <a  class="aaddress">会员信息</a> </td>
			</tr></table>
		</td>
	</tr>
	<tr><td class="td_ct1"></td></tr>
	<!--
	<tr>
		<td align="center">
			<table class="table_add1" cellpadding="0" cellspacing="0"><tr><td class="td_add1">管理员信息</td>
			</tr></table>
		</td>
	</tr>
	-->
	<tr><td class="td_ct3"> </td></tr>
	<tr><td class="td_ct3"> </td></tr>
	<tr><td class="td_ct3"> </td></tr>
	<tr align="center">
		<td align="center">
		<?php
		$Re = $DataBase->SelectSql("SELECT  *  FROM user_base WHERE Status<>0 ");
		if($DataBase->GetResultRows($Re) == 0)
		{

	      $pt1 = '0'; 
		}
		else
		{
		 $pt1 =  $DataBase->GetResultRows($Re);
		}
		$Re = $DataBase->SelectSql("SELECT * FROM user_base WHERE Status<>0 and DATE_SUB(CURDATE(),INTERVAL 7 DAY) <= date(Createtime)");
		if($DataBase->GetResultRows($Re) == 0)
		{

	      $pt2 = '0'; 
		}
		else
		{
		 $pt2 =  $DataBase->GetResultRows($Re);
		}
		$Re = $DataBase->SelectSql("SELECT  *  FROM  user_base WHERE Status<>0 and to_days(Createtime) = to_days(now())");
		if($DataBase->GetResultRows($Re) == 0)
		{

	      $pt3 = '0'; 
		}
		else
		{
		 $pt3 =  $DataBase->GetResultRows($Re); 
		}
	    ?>	
			<table class="table_system" cellpadding="0" cellspacing="0px">
				<tr>
					<td rowspan="6" align="center" valign="top" width="30%"><img src="../image/title/4.png"></td>
				    <td class="td_system2_2">注册会员总数：</td>
					<td class="td_system3_2"><?php echo $pt1;?></td>
				</tr>
				<tr>
				    <td class="td_system2_2">最近一周注册会员总数：</td>
					<td class="td_system3_2"><?php echo $pt2;?></td>
				</tr>
				<tr>
				    <td class="td_system2_2">今天注册会员数：</td>
					<td class="td_system3_2"><?php echo $pt3;?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td class="td_ct1"></td></tr>
	<tr><td class="td_ct2"></td></tr>
</table>
</body>
</html>


