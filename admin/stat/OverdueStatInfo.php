<?php
if(1){
/****************************************************************** 
**创建者：sakura
**创建时间：2014-10-08
**本页： 逾期明细
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_APPLICATION_5');
$isUserSC=$Admin->CheckPopedom('SC_MEMBER');
$isP2PSC=$Admin->CheckPopedom('SC_LOAN_APP');

//use cache
if($raintpl_cache && $cache = $tpl->cache('stat_menberuser_ymd', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$starttime = $FLib->RequestChar('starttime',0,10,'参数',1);
$endtime = $FLib->RequestChar('endtime',0,10,'参数',1);
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType.
	"&starttime=" . $starttime."&endtime=".$endtime;

$title = '逾期统计';
$points = array('统计管理', '项目统计', $title );
$sTypes = array('','借款人');
$extend = array();
$extend['js'] = '
<link type="text/css" rel="stylesheet" href="../include/timepicker/css/jquery-ui.css" >
<script src="../js/form.checkfun.js" type="text/javascript"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-slide.min.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">
$(function(){
	$("input[name=\'starttime\']").datepicker({
		changeYear:true,
		onSelect : function(selectedDate) {
			$("input[name=\'endtime\']").datepicker("option", "minDate", selectedDate); 
		}
	});
	$("input[name=\'endtime\']").datepicker({
		changeYear:true,
		onSelect : function(selectedDate) {
			$("input[name=\'starttime\']").datepicker("option", "maxDate", selectedDate); 
		}
	});
	'.($starttime?'$("input[name=\'endtime\']").datepicker("option", "minDate", "'.$starttime.'");':'').'
})
</script>
';
$extend['sTypes'] = array('初始时间：<input class="txt_put_date" name="starttime" value="'.$starttime.'" data-time="" onclick="nodatepatFun" tp="初始时间" maxlength="10"/>'
		,'结束时间：<input class="txt_put_date" name="endtime" value="'.$endtime.'" data-time="" onclick="nodatepatFun" tp="结束时间" maxlength="10"/>');
$btns = array('
<a name="submit" class="history_back" href="'.$FLib->IsRequest('backurl').'"/>返回</a>
');

$mWhere = '';
if($sKey!=''){
	switch ($sType){
		case 1:
			$mWhere .= " and b.Vc_name like '%" . $sKey . "%'";
			break;
	}
}
if($starttime){
	$mWhere .= " and a.Dt_repay >= '{$starttime} 00:00:00' ";
}
if($endtime){
	$mWhere .= " and a.Dt_repay <= '{$endtime} 23:59:59' ";
}

$subSql1 = "SELECT *, 0 isAdmin FROM p2p_repayment WHERE I_operation<>2 ";
$subSql2 = "SELECT *, 1 isAdmin FROM p2p_repayment_admin ";
$unionSql = "(".$subSql1." UNION ".$subSql2.")";

$tables = ''.$unionSql.' a left join user_base b on a.I_repayID=b.ID
			left join p2p_application c on a.I_applicationID=c.ID
			left join user_base d on c.I_applicantID=d.ID
		where a.Status=1 and a.I_type=1 and a.I_status=2 '.$mWhere.'
			and b.Status=1 and c.Status=1';
$sql="SELECT a.*,
c.Vc_title ,
if(a.I_substituteID is null,'借款人个人',if(a.I_substituteID > 999,if(a.I_operation=2,'担保公司代偿','借款人个人'),'平台垫付')) repay_name,
d.Vc_nickname loan_name
FROM {$tables}";
$sqlcount ="select COUNT(*) from {$tables}";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);


$ths = $tds = array();
$ths[] = array('val'=>'项目名称', 'wid'=>'');
$ths[] = array('val'=>'借款人', 'wid'=>'');
$ths[] = array('val'=>'期数', 'wid'=>'');
$ths[] = array('val'=>'金额', 'wid'=>'');
$ths[] = array('val'=>'本金', 'wid'=>'');
$ths[] = array('val'=>'利息', 'wid'=>'');
$ths[] = array('val'=>'罚息', 'wid'=>'');
$ths[] = array('val'=>'逾期时间', 'wid'=>'');
$ths[] = array('val'=>'还款人', 'wid'=>'');
$ths[] = array('val'=>'还款金额', 'wid'=>'');
$ths[] = array('val'=>'还款时间', 'wid'=>'');
$ths[] = array('val'=>'还款状态', 'wid'=>'');
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){
		$_td='';
		if($isP2PSC)$_td .= '<td><a href="../p2p/ApplicationInfo.php?Id='.$Rs[$i]['I_applicationID'].'" class="hs" w="750" h="700" title="【'.$Rs[$i]['Vc_title'].'】详细页">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</a></td>';
		else $_td .= '<td title="'.$Rs[$i]['Vc_title'].'">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</td>';
		if($isUserSC)$_td .= '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['I_repayID'].'" class="hs" h="" title="【'.$Rs[$i]['loan_name'].'】会员详细页">'.$FLib->cutstr($Rs[$i]['loan_name'],40).'</a></td>';
		else $_td .= '<td title="'.$Rs[$i]['loan_name'].'">'.$FLib->cutstr($Rs[$i]['loan_name'],40).'</td>';
		$_td .= '<td>'. $Rs[$i]['I_sort'].'/'.$Rs[$i]['I_sorts'] .'</td>';
		$_td .= '<td>'. ($Rs[$i]['N_capital']+$Rs[$i]['N_interest']) .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_capital'].'</td>';
		$_td .= '<td>'. $Rs[$i]['N_interest'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_fee'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Dt_repay'] .'">'. $FLib->fromatdate($Rs[$i]['Dt_repay'],'Y-m-d') .'</td>';
		$_td .= '<td>'. $Rs[$i]['repay_name'] .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_operation'] == 0?'0.00':($Rs[$i]['I_operation'] == 1?$Rs[$i]['N_amount']: ($Rs[$i]['N_capital']+$Rs[$i]['N_interest']))) .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Dt_operation'] .'">'. $FLib->fromatdate($Rs[$i]['Dt_operation'],'Y-m-d') .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_operation'] == 2?'已代偿':getInfoByDDIC("p2p_repayment.I_operation",$Rs[$i]['I_operation'])) .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}
}

$DataBase->CloseDataBase();
$extend['fan'] = false;
$helps  = array();
$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';

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
