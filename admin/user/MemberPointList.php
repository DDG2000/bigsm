<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页：全部 积分记录 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER_POINT');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType ;
$DateTime1   = $FLib->RequestChar('DateTime1',1,100,'起始时间',1);
$DateTime2  = $FLib->RequestChar('DateTime2',1,100,'结束时间',1);

$title = '积分记录';
$points = array('会员管理', $title.'管理' );
$sTypes = array('','用户名','原因');
$hides  = array();
$extend = array('js'=>'<script src="../include/calendar/Calendar.js" type="text/javascript"></script><link type="text/css" rel="stylesheet" href="../include/calendar/calendar.css" >');
$extend['sTypes'] = array('注册时间：<input name="DateTime1" type="text" class="txt_put1" id="ftime" value="'.$FLib->FromatDate(iset($DateTime1),'Y-m-d').'" isc="datepatFun" onclick="return showCalendar(\'ftime\', \'y-mm-dd\');"> 至 <input name="DateTime2" type="text" class="txt_put1" id="etime" value="'.$FLib->FromatDate(iset($DateTime2),'Y-m-d').'" isc="datepatFun" onclick="return showCalendar(\'etime\', \'y-mm-dd\');">');
$mWhere = '';
switch ($sType)
{
	case 1:
		$mWhere = "ub.Vc_name like '%" . $sKey . "%'";
		break;
	case 2:
		$mWhere = "a.Vc_reason like '%" . $sKey . "%'";
		break;
	default:
		$mWhere = '1=1';
		break;
}
if(strtotime($DateTime1)>0){
	$mWhere .= " and a.Createtime>='$DateTime1'";
	$UrlInfo .= "&DateTime1=" . $DateTime1 ;
}
if(strtotime($DateTime2)>0){
	$mWhere .= " and a.Createtime<'$DateTime2'";
	$UrlInfo .= "&DateTime2=" . $DateTime2 ;
}
$tables = 'user_point_record a inner join user_base ub on a.I_userID=ub.ID where a.Status>0 and ub.Status>0 and '.$mWhere.'';
$sql = "SELECT a.*,ub.Vc_name FROM {$tables} order by a.ID desc";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'用户名', 'wid'=>'');
$ths[]=array('val'=>'积分变更', 'wid'=>'');
$ths[]=array('val'=>'变更原因', 'wid'=>'');
$ths[]=array('val'=>'操作时间', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td>'. $Rs[$i]['Vc_name'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['I_number'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['Vc_reason'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array();
$btns[] = '<a href="MemberPointProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删除</a>';
$extend['gbtns'] = array();//array('<a href="MemberPointProcess.php?Work=ClearReco" class="delall" title="清空所有记录,该操作不可恢复，请慎重！">清空所有</a>');
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
