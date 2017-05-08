<?php
/****************************************************************** 
**创建者：sakura
**创建时间：2014-10-08
**本页：投资明细
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_USER_6');
$isUserSC=$Admin->CheckPopedom('SC_MEMBER');
$isP2PSC=$Admin->CheckPopedom('SC_LOAN_APP');

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$starttime = $FLib->RequestChar('starttime',1,10,'时间',1);
$endtime = $FLib->RequestChar('endtime',1,10,'时间',1);
$type = $FLib->RequestChar('type',1,10,'方式',1);
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$outexcel = $FLib->RequestChar('outexcel',1,9,'outexcel');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType.
	"&starttime=" . $starttime."&endtime=".$endtime.'&type='.$type ;
$title = '投资统计明细';

if($sKey)$mWhere = ' and j.ID='.$sKey;
if($starttime)
	$mWhere .= " and a.Createtime >= '{$starttime} 00:00:00' ";
if($endtime)
	$mWhere .= " and a.Createtime <= '{$endtime} 23:59:59' ";

$table = 'p2p_bid a left join p2p_application b on b.ID=a.I_applicationID
		left join user_base j on a.I_userID=j.ID
		left join p2p_application_class cc on b.I_classID=cc.ID
		left join p2p_application_subclass sb on b.I_subclassID=sb.ID
			where '.($type==1?'b.I_status>=50':'b.I_status in (20,40,50,60)').' and a.I_deal=1 and a.Status=1 '.$mWhere;
$sql = 'select a.*,b.Vc_title,cc.Vc_name cname,sb.Vc_name sbname,b.N_amount N_amount_loan,b.N_yearannualrate,b.Vc_life,b.I_classID,b.I_repayment,b.I_status,j.Vc_nickname from '.$table;
$sqlcount ="select COUNT(*) from {$table}";
$sqlwmtime = $sql." and left(a.Createtime,7)='explodeExcelTy2Mtime' ";
$sqlmtimecount = "select left(a.Createtime,7) as mtime from {$table} group by left(a.Createtime,7)";

if(!$outexcel){
	
	$pagesize = $Config->AdminPageSize;
	$pagecount = 1;$rscount=0;
	$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);
	
	$points = array('统计管理', '会员统计', $title);
	$sTypes = array('','投资时间');
	$hides=array('sKey'=>$sKey);
	$extend = array();
	$extend['js'] = '
	<link type="text/css" rel="stylesheet" href="../include/timepicker/css/jquery-ui.css" >
	<style type="text/css">
	.spancheck{display: none;}
	</style>
	<script src="../js/form.checkfun.js" type="text/javascript"></script>
	<script type="text/javascript" src="../include/timepicker/js/jquery-ui.js"></script>
	<script type="text/javascript" src="../include/timepicker/js/jquery-ui-slide.min.js"></script>
	<script type="text/javascript" src="../include/timepicker/js/jquery-ui-timepicker-addon.js"></script>
	<script type="text/javascript">
	$(function(){$(".timecheck").show();
		$(".buta_excel").live("click",function(){
			window.location.href="./InvestStatInfo.php?outexcel=true'.$UrlInfo.'";
		});
		$("input[name=\'starttime\']").datepicker({
			changeYear:true,
			yearRange:"1900:'.date('Y').'",
			maxDate:0,
			onSelect : function(selectedDate) {
				$("input[name=\'endtime\']").datepicker("option", "minDate", selectedDate); 
			}
		});
		$("input[name=\'endtime\']").datepicker({
			changeYear:true,
			yearRange:"1900:'.date('Y').'",
			maxDate:0,
			onSelect : function(selectedDate) {
				$("input[name=\'starttime\']").datepicker("option", "maxDate", selectedDate); 
			}
		});
		'.($starttime?'$("input[name=\'endtime\']").datepicker("option", "minDate", "'.$starttime.'");':'').'
	})
	</script>
	';
	$btns = array('<a name="submit" class="history_back" style="display:inline-block;float:none;" href="'.$FLib->IsRequest('backurl').'"/>返回</a>');
	
	$extend['sTypes'] = array('<span class="spancheck timecheck">时间：<input class="txt_put_date" name="starttime" value="'.$starttime.'" data-time="" onclick="nodatepatFun" tp="开始时间" maxlength="10"/>
	 - <input class="txt_put_date" name="endtime" value="'.$endtime.'" data-time="" onclick="nodatepatFun" tp="结束时间" maxlength="10"/></span>');
	
	$ths =$tds = array();
	$ths[]=array('val'=>'用户名', 'wid'=>'');
	$ths[]=array('val'=>'项目名称', 'wid'=>'');
	$ths[]=array('val'=>'借款金额', 'wid'=>'');
	$ths[]=array('val'=>'标类型', 'wid'=>'');
	$ths[]=array('val'=>'子分类', 'wid'=>'');
	$ths[]=array('val'=>'借款期限', 'wid'=>'');
	$ths[]=array('val'=>'年利率', 'wid'=>'');
	$ths[]=array('val'=>'还款方式', 'wid'=>'');
	$ths[]=array('val'=>'借款状态', 'wid'=>'');
	$ths[]=array('val'=>'投资金额', 'wid'=>'');
	$ths[]=array('val'=>'投资时间', 'wid'=>'');
	$ths[]=array('val'=>'投标方式', 'wid'=>'');
	if(is_array($Rs)){
		$pagecount = $Rs[0]['pagecount'];
		$rscount = $Rs[0]['rscount'];
		$extend['rscount']=$rscount;
		for($i=1;$i<count($Rs);$i++){
			$_td='';
			if($isUserSC)$_td .= '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['I_userID'].'" class="hs" h="" title="【'.$Rs[$i]['Vc_nickname'].'】会员详细页">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</a></td>';
			else $_td .= '<td title="'.$Rs[$i]['Vc_nickname'].'">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</td>';
			if($isP2PSC)$_td .= '<td><a href="../p2p/ApplicationInfo.php?Id='.$Rs[$i]['I_applicationID'].'" class="hs" w="750" h="700" title="【'.$Rs[$i]['Vc_title'].'】详细页">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</a></td>';
			else $_td .= '<td title="'.$Rs[$i]['Vc_title'].'">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</td>';
			$_td .= '<td>'. $Rs[$i]['N_amount_loan'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['cname'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['sbname'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['Vc_life'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['N_yearannualrate'] .'</td>';
			$_td .= '<td>'. getInfoByDDIC('p2p_application.I_repayment',$Rs[$i]['I_repayment']) .'</td>';
			$_td .= '<td>'. getInfoByDDIC('p2p_application.I_status',$Rs[$i]['I_status']) .'</td>';
			$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
			$_td .= '<td>'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d h:i:s') .'</td>';
			$_td .= '<td>'. ($Rs[$i]['I_type']==2?"自动投标":"手动投标") .'</td>';
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

/*
 * 导出excel
*/
else{
	$dat=array();
	$dat['sql']=$sql;
	$dat['sqlcount']=$sqlcount;
	$dat['sqlwmtime']=$sqlwmtime;
	$dat['sqlmtimecount']=$sqlmtimecount;
	$dat['rs']=0;//$Rs = $DataBase->fetch_all($sql);
	$dat['rscount']=iset($DataBase->fetch_val($sqlcount),0);
	$dat['filename']=$title;
	$dat['fields']=array(
			array('用户名','Vc_name'),
			array('项目名称','Vc_title'),
			array('借款金额','N_amount_loan'),
			array('标类型','cname'),
			array('子分类','sbname'),
			array('借款期限','Vc_life','','%'),
			array('年利率','N_yearannualrate'),
			array('还款方式','getInfoByDDIC(\'p2p_application.I_repayment\',$Rs[$i][\'I_repayment\'])','other'),
			array('投资金额','N_amount'),
			array('投资时间','$FLib->fromatdate($Rs[$i][\'Createtime\'],\'Y-m-d h:i:s\')','other'),
			array('投标方式','$Rs[$i][\'I_type\']==2?"自动投标":"手动投标"','other')
	);
	
	include WEBROOTINC.'ExplodeExcel.php';
}

?>
