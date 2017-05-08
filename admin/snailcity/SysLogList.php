<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 系统日志 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SYS_SET_LOG');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->requestchar('sKey',1,50,'参数',1);
$sType  = $FLib->requestint('sType',0,9,'类型');
$CurrPage = $FLib->requestint('currpage',1,9,'页数');
$stime = $FLib->RequestChar('stime',1,100,'参数',1);
$etime = $FLib->RequestChar('etime',1,100,'参数',1);
$status = $FLib->requestint('status',0,9,'状态');
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType ."&etime=" .$etime ."&stime=" . $stime;
$typearr = array('t_1'=>'系统安全','t_2'=>'系统管理','t_3'=>'网站管理','t_4'=>'贷款管理','t_5'=>'审核管理','t_6'=>'论坛管理');//key 需要根据status改变
$marks = '';
foreach($typearr as $k=>$v){$marks.='<a href="?status='.substr($k,2).'" '.(substr($k,2)==$status?'class="cur"':'').'>'.$v.'</a>';}
$UrlInfo .= '&status=' . $status ;

$title = '系统日志';
$points = array('系统管理', $title );
$sTypes = array('', '操作者', '登录IP', '日志内容');
$hides  = array('status'=>$status);
$extend = array('marks'=>$marks);
//搜索时间
$extend['js'] = '
<script src="../js/form.checkfun.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="../include/timepicker/css/jquery-ui.css" >
<script type="text/javascript" src="../include/timepicker/js/jquery-ui.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-slide.min.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">$(function(){$(\'.datepat\').datetimepicker({hour: 0,minute: 0});});</script>';
$search_sTypes = array();
$search_sTypes[] = '创建时间：起 <input name="stime" type="text" class="txt_put1 datepat" value="'.$stime.'" isc="datepatFun" ennull="" maxlength="19">';
$search_sTypes[] = '至 <input name="etime" type="text" class="txt_put1 datepat" value="'.$etime.'" isc="datepatFun" ennull="" maxlength="19">';
$extend['sTypes'] = $search_sTypes;

switch ($sType){
	case 1:
		$mWhere = "su.Vc_name like '%$sKey%' and su.Status>0";
		break;
	case 2:
		$mWhere = "sl.Vc_IP like '%$sKey%'";
		break;
	case 3:
		$mWhere = "sl.Vc_operation like '%$sKey%'";
		break;
	default:
		$mWhere = '1=1';
		break;
}
if($stime != ''){
	$mWhere .= " and sl.Createtime >= '$stime'";
}
if($etime != ''){
	$mWhere .= " and sl.Createtime <= '$etime'";
}
if(isset($typearr['t_'.$status])){
	switch($status){
	  case 2:
	  $mWhere .= " and (sl.Vc_module like '%".$typearr['t_'.$status]."%' or sl.Vc_module like '%会员管理%')";
	  break;
	  case 3:
	  $mWhere .= " and (sl.Vc_module like '%".$typearr['t_'.$status]."%' or sl.Vc_module like '%内容管理%')";
	  break;
	  case 4:
	  $mWhere .= " and (sl.Vc_module like '%".$typearr['t_'.$status]."%')";
	  break;
	  default:
	  $mWhere .= " and sl.Vc_module like '%".$typearr['t_'.$status]."%'";
	}
	
}
$tables = 'sc_log sl left join sc_user su on sl.I_operatorID=su.ID where sl.Status=1 and '.$mWhere.'';
$sql = "SELECT sl.*,su.Vc_name suname FROM {$tables} order by sl.ID desc";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'日志内容', 'wid'=>'');
$ths[]=array('val'=>'所属模块', 'wid'=>'');
$ths[]=array('val'=>'操作者', 'wid'=>'');
$ths[]=array('val'=>'IP', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td>'.$Rs[$i]['Vc_operation'].'</td>';
		$_td .= '<td>'.$Rs[$i]['Vc_module'].'</td>';
		$_td .= '<td>'.$Rs[$i]['suname'].'</td>';
		$_td .= '<td>'.$Rs[$i]['Vc_IP'].'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array('<a href="SysLogProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删 除</a>',);
$extend['gbtns'] = array('<a href="SysLogProcess.php?Work=ClearReco" class="delall" title="是否清空所有记录">清空记录</a>');
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
