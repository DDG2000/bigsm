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
	
$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$starttime = $FLib->RequestChar('starttime',1,10,'时间',1);
$endtime = $FLib->RequestChar('endtime',1,10,'时间',1);
$type = $FLib->RequestInt('type',0,10,'方式',1);
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$outexcel = $FLib->RequestChar('outexcel',1,9,'outexcel');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType.
	"&starttime=" . $starttime."&endtime=".$endtime.'&type='.$type ;

$title = '投资统计';

$mWhere ='';
if($starttime)
	$mWhere .= " and a.Createtime >= '{$starttime} 00:00:00' ";
if($endtime)
	$mWhere .= " and a.Createtime <= '{$endtime} 23:59:59' ";

if($mWhere){
	$table = '(select a.I_userID,a.I_deal,sum(a.N_amount) N_amount_bid
				FROM p2p_bid a left join p2p_application z on z.ID=a.I_applicationID
				where '.($type==1?'z.I_status>=50':'z.I_status in (20,40,50,60)').' and a.I_deal=1 and a.Status=1 '.$mWhere.' group by a.I_userID
			) b left join user_base j on b.I_userID=j.ID
			where j.Status=1 and j.ID >999';
	$sql = 'select j.*,b.* from '.$table;
	$sqlcount ="select COUNT(*) from {$table}";
	$sqlwmtime = $sql." and left(j.Createtime,7)='explodeExcelTy2Mtime' ";
	$sqlmtimecount = "select left(j.Createtime,7) as mtime from {$table} group by left(j.Createtime,7)";
	
}else{
	$pagecount = 0;
	$rscount = 0;
}

if(!$outexcel){
	if($mWhere){
		$pagesize = $Config->AdminPageSize;
		$pagecount = 1;$rscount=0;
		$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);
	}
	
	$points = array('统计管理', '会员统计', $title);
	$sTypes = array('', '投资时间');
	$hides = array('type'=>$type);
	$extend = array();
	$extend['js'] = '
	<link type="text/css" rel="stylesheet" href="../include/timepicker/css/jquery-ui.css" >
	<script src="../js/form.checkfun.js" type="text/javascript"></script>
	<script type="text/javascript" src="../include/timepicker/js/jquery-ui.js"></script>
	<script type="text/javascript" src="../include/timepicker/js/jquery-ui-slide.min.js"></script>
	<script type="text/javascript" src="../include/timepicker/js/jquery-ui-timepicker-addon.js"></script>
	<script type="text/javascript">
	$(function(){
		$(".buta_excel").live("click",function(){
			window.location.href="/admin/stat/InvestStat.php?outexcel=true'.$UrlInfo.'";
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
	});
	</script>
	';
	
	$extend['sTypes'] = array('<span class="spancheck timecheck">投资时间：<input class="txt_put_date" name="starttime" value="'.$starttime.'" data-time="" onclick="nodatepatFun" tp="开始时间" maxlength="10"/>
	 - <input class="txt_put_date" name="endtime" value="'.$endtime.'" data-time="" onclick="nodatepatFun" tp="结束时间" maxlength="10"/></span>');
	
	$ths =$tds = array();
	$ths[]=array('val'=>'用户名', 'wid'=>'');
	$ths[]=array('val'=>'真实姓名', 'wid'=>'');
	$ths[]=array('val'=>'电话号码', 'wid'=>'');
	$ths[]=array('val'=>'电子邮箱', 'wid'=>'');
	$ths[]=array('val'=>'账户余额', 'wid'=>'');
	$ths[]=array('val'=>'冻结金额', 'wid'=>'');
	$ths[]=array('val'=>'注册时间', 'wid'=>'');
	$ths[]=array('val'=>'投资金额', 'wid'=>'');
	if(is_array($Rs)){
		$pagecount = $Rs[0]['pagecount'];
		$rscount = $Rs[0]['rscount'];
		$extend['rscount']=$rscount;
		for($i=1;$i<count($Rs);$i++){
			$_td='';
			if($isUserSC)$_td .= '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['ID'].'" class="hs" h="" title="【'.$Rs[$i]['Vc_nickname'].'】会员详细页">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</a></td>';
			else $_td .= '<td title="'.$Rs[$i]['Vc_nickname'].'">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</td>';
			$_td .= '<td>'. $Rs[$i]['Vc_truename'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['Vc_mobile'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['Vc_Email'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['N_amount_freeze'] .'</td>';
			$_td .= '<td>'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d h:i:s') .'</td>';
			$_td .= '<td><a title="'.$starttime.'-'.$endtime.'投资统计明细" href="./InvestStatInfo.php?sKey='.$Rs[$i]['ID'].'&starttime='.$starttime.'&endtime='.$endtime.'&type='.$type.'">'. $Rs[$i]['N_amount_bid'] .'</a></td>';
			$tds[$Rs[$i]['ID']]=$_td;
		}
	}
	$DataBase->CloseDataBase();
	$extend['fan'] = false;
	$helps  = array('默认不显示信息','显示某一时间段的投资信息，请先选择投资时间后再进行搜索');
	$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
	$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);
	$btns = array(
			($type==1?'<a name="submit" style="display:inline-block;float:right;" href="./InvestStat.php"/>按实际投资时间统计</a>'
					:'<a name="submit" style="display:inline-block;float:right;" href="./InvestStat.php?type=1"/>按满标审核通过后统计</a>')
			,($rscount>0?'<a name="submit" class="buta_excel" style="display:inline-block;float:none;"/>导出excel</a>':'')
			);
	
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
	
	$tpl->draw('list_invest'.$raintpl_ver);
	
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
			array('用户名','Vc_nickname'),
			array('真实姓名','Vc_truename'),
			array('电话号码','Vc_mobile'),
			array('电子邮箱','Vc_Email'),
			array('账户余额','N_amount'),
			array('冻结金额','N_amount_freeze'),
			array('注册时间','Createtime','datetime'),
			array('投资金额','N_amount_bid')
	);
	
	include_once WEBROOTINC.'ExplodeExcel.php';
}

?>
