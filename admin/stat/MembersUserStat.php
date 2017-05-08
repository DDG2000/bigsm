<?php
/****************************************************************** 
**创建者：sakura
**创建时间：2014-10-04
**本页： 会员详情统计
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_USER');
$isUserSC=$Admin->CheckPopedom('SC_MEMBER');

$outexcel = $FLib->RequestChar('outexcel',1,9,'outexcel');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list_div', 60, 1) ){echo $cache;	exit;}

//正式删除---begin
if(file_exists(WEBROOTDATA."tmp_memberstat.php")){require_once(WEBROOTDATA."tmp_memberstat.php");}
//-----end

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$type = $FLib->RequestChar('type','',9,'类型');
$nulltrue  = $FLib->RequestInt('nulltrue',0,9,'为空搜索');
$startnum = $FLib->RequestChar('startnum',1,10,'开始金额',1);
$endnum = $FLib->RequestChar('endnum',1,10,'结束金额',1);
$starttime  = $FLib->RequestChar('starttime',1,10,'开始时间');
$endtime  = $FLib->RequestChar('endtime',1,10,'结束时间');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType ."&type=".$type.
	($startnum?"&startnum=".$startnum:'').($endnum?"&endnum=".$endnum:'').
	($starttime?"&starttime=".$starttime:'').($endtime?"&endtime=".$endtime:'').
	($nulltrue?"&nulltrue=".$nulltrue:'');

$title='';
if($type=='invest'){
	$Admin->CheckPopedoms('SC_STAT_USER_4');
	$title='投资人详情';
}elseif($type=='loan'){
	$Admin->CheckPopedoms('SC_STAT_USER_5');
	$title='借款人详情';
}else{
	$Admin->CheckPopedoms('SC_STAT_USER_3');
	$title='会员详情';
}

$mWhere = '';
switch ($sType){
	case 1:
		if($sKey!='')$mWhere .= " and t.Vc_nickname like '%" . $sKey . "%'";
		break;
	case 2:
		if($sKey!='')$mWhere .= " and t.Vc_Email like '%" . $sKey . "%'";
		break;
	case 3:
		if($nulltrue)$mWhere .= " and t.Vc_mobile is null";
		else if($sKey!='')$mWhere .= " and t.Vc_mobile like '%" . $sKey . "%'";
		break;
	case 4:
		if($nulltrue)$mWhere .= " and t.Vc_truename is null";
		else if($sKey!='')$mWhere .= " and t.Vc_truename like '%" . $sKey . "%'";
			break;
	case 5:
		if($startnum)$mWhere .= " and t.N_amount_unused >= ".$startnum;
		if($endnum)$mWhere .= " and t.N_amount_unused <= ".$endnum;
		break;
	case 6:
		if($startnum)$mWhere .= " and t.N_amount_all >= ".$startnum;
		if($endnum)$mWhere .= " and t.N_amount_all <= ".$endnum;
		break;
}
if($starttime)$mWhere .= " and t.Createtime >= '".$starttime." 00:00:00'";
if($endtime)$mWhere .= " and t.Createtime <= '".$endtime." 23:23:59'";

$tables="tmp_memberuser_stat_totel t where 1=1".$mWhere;
if($type=='invest'){
	$tables="tmp_memberuser_stat_totel t
				where EXISTS (select * from (
					select a.I_userID
					from p2p_bid a left join p2p_application b on a.I_applicationID=b.ID
					where a.status=1 and a.I_deal=1 and b.I_status>=50
					group by a.I_userID
					) s where t.ID=s.I_userID
				)".$mWhere;
}elseif($type=='loan'){
	$tables="tmp_memberuser_stat_totel t
				where EXISTS (SELECT b.I_applicantID
					FROM p2p_application b
					WHERE b.I_status>=50 and t.ID=b.I_applicantID
				)".$mWhere;
}
$sql = "select t.* FROM {$tables}";
$sqlorder = " order by t.Createtime desc";
$sqlcount ="select COUNT(*) from {$tables}";
$sqlwmtime = $sql." and left(t.Createtime,7)='explodeExcelTy2Mtime' ".$sqlorder;
$sqlmtimecount = "select left(t.Createtime,7) as mtime from {$tables} group by left(t.Createtime,7)";
$sql .= $sqlorder;

if(!$outexcel){

	$pagesize = $Config->AdminPageSize;
	$pagecount = 1;$rscount=0;
	$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);
	
	$points = array('统计管理', '会员统计', $title );
	$sTypes = array('', '用户名','邮箱地址','手机号码','真实姓名','闲置资金','净资产');
	$extend = array();
	$hides = array('type'=>$type);
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
	$(function(){$(".timecheck").show();'.($nulltrue?'$("input[name=\'sKey\']").attr("disabled","disabled");$("#nulltruehidden").val("1");':'').
	($sType<5&&$sType>2?'$(".nullcheck").show();':'').
	($sType<5?'$(".defaultcheck").show();':'').
	($sType==5||$sType==6?'$(".numcheck").show();':'').
	'   $("select[name=\'sType\']").live(\'change\',function(){
			$(".spancheck input").val("");
			$(".spancheck").hide();
			
			if($(this).val()==5||$(this).val()==6){
				$(".numcheck").show();
			}else{
				$(".defaultcheck").show();
				if($(this).val()>2){
					$(".nullcheck").show();
					if($("#nulltruecheckbox").is(\':checked\')){
						$("input[name=\'sKey\']").attr("disabled","disabled");
						$("#nulltruehidden").val("1");
					}else{
						$("input[name=\'sKey\']").removeAttr("disabled");
						$("#nulltruehidden").val("0");
					}
				}
			}
		});
		$("#nulltruecheckbox").live(\'change\',function(){
			if($(this).is(\':checked\')){
				$("input[name=\'sKey\']").attr("disabled","disabled");
				$("#nulltruehidden").val("1");
			}else{
				$("input[name=\'sKey\']").removeAttr("disabled");
				$("#nulltruehidden").val("0");
			}
		});  
		$(".buta_excel").live("click",function(){
			window.location.href="/admin/stat/MembersUserStat.php?outexcel=true'.$UrlInfo.'";
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
	function nonumchangeFun(){
		var startnum = $("input[name=\'startnum\']"),endnum = $("input[name=\'endnum\']");
		if(startnum.val()>0 && endnum.val()>0 && startnum.val()>endnum.val()){
			return {"flag":false,"err":"开始金额不能大于结束金额"};
		}
		return {"flag":true};
	}
	</script>	
	';
	$extend['sTypes'] = array('<span class="spancheck nullcheck"><input type="checkbox" id="nulltruecheckbox" value="1" '.($nulltrue?'checked="checked"':'').'>为空搜索
			<input type="hidden" name="nulltrue" id="nulltruehidden" value="0"</span>'
			,'<span class="spancheck numcheck">金额：<input class="txt_put1" name="startnum" value="'.$startnum.'" tp="开始金额" maxlength="10"/>
			- <input class="txt_put1" name="endnum" value="'.$endnum.'" tp="结束金额" maxlength="10"/></span>'
			,'<span class=" timecheck">注册时间：<input class="txt_put_date" name="starttime" value="'.$starttime.'" data-time="" onclick="nodatepatFun" tp="开始时间" maxlength="10"/>
			- <input class="txt_put_date" name="endtime" value="'.$endtime.'" data-time="" onclick="nodatepatFun" tp="结束时间" maxlength="10"/></span>');
	
	$btns = array('
	<a name="submit" class="buta_excel" style="display:inline-block;float:none;"/>导出excel</a>
	');
	
	$thsl = array();
	$thsl[]=array('val'=>'用户名', 'wid'=>''); 
	$ths = array();
	$ths[]=array('val'=>'真实名称', 'wid'=>'');
	$ths[]=array('val'=>'邮箱账号', 'wid'=>'','sty'=>'width:160px');
	$ths[]=array('val'=>'手机号', 'wid'=>'');
	$ths[]=array('val'=>'注册时间', 'wid'=>'');
	$ths[]=array('val'=>'推荐好友数', 'wid'=>'');
	$ths[]=array('val'=>'充值总金额', 'wid'=>'');
	$ths[]=array('val'=>'提现总金额', 'wid'=>'');
	$ths[]=array('val'=>'闲置资金', 'wid'=>'');
	$ths[]=array('val'=>'净资产', 'wid'=>'');
	$ths[]=array('val'=>'投资总金额', 'wid'=>'');
	$ths[]=array('val'=>'投资利息', 'wid'=>'');
	$ths[]=array('val'=>'罚息收入', 'wid'=>'');
	$ths[]=array('val'=>'借款总金额', 'wid'=>'');
	$ths[]=array('val'=>'借款利息', 'wid'=>'');
	$ths[]=array('val'=>'逾期次数', 'wid'=>'');
	$ths[]=array('val'=>'逾期金额', 'wid'=>'');
	$ths[]=array('val'=>'罚息支出', 'wid'=>'');
	$ths[]=array('val'=>'待收总金额', 'wid'=>'');
	$ths[]=array('val'=>'待还总金额', 'wid'=>'');
	$ths[]=array('val'=>'还款中项目数量', 'wid'=>'');
	$ths[]=array('val'=>'还款中项目金额', 'wid'=>'');
	$ths[]=array('val'=>'已还清项目数量', 'wid'=>'');
	$ths[]=array('val'=>'已还清项目金额', 'wid'=>'');
	$tdsl = array();
	$tds = array();
	if(is_array($Rs)){
		$pagecount = $Rs[0]['pagecount'];
		$rscount = $Rs[0]['rscount'];
		$extend['rscount']=$rscount;
		if($rscount>0){
		for($i=1;$i<count($Rs);$i++){	
			$_td='';
			if($isUserSC)$_td .= '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['ID'].'" class="hs" h="" title="【'.$Rs[$i]['Vc_nickname'].'】会员详细页">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</a></td>';
			else $_td .= '<td title="'.$Rs[$i]['Vc_nickname'].'">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</td>';
			$tdsl[$Rs[$i]['ID']]=$_td;
			$_td = '<td title="'. $Rs[$i]['Vc_truename'] .'">'. $Rs[$i]['Vc_truename'] .'</td>';
			$_td .= '<td style="width:160px" title="'. $Rs[$i]['Vc_Email'] .'">'. $Rs[$i]['Vc_Email'] .'</td>';
			$_td .= '<td title="'. $Rs[$i]['Vc_mobile'] .'">'. $Rs[$i]['Vc_mobile'] .'</td>';
			$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d H:i:s') .'</td>';
			
			$_td .= '<td><a title="点击查看明细" href="MembersUserStatInfo.php?Code='.$Rs[$i]['ID'].'&Name='.$Rs[$i]['Vc_nickname'].'&Type=friend&Type_='.$type.'">'.$Rs[$i]['I_friend_num'].' 个</a></td>';
			//$ths[]=array('val'=>'推荐好友数（明细）', 'wid'=>'');
			$_td .= '<td>'. $Rs[$i]['N_amount_recharge'] .'</td>';
			//$ths[]=array('val'=>'充值总金额', 'wid'=>'');
			$_td .= '<td>'. $Rs[$i]['N_amount_withdraw'] .'</td>';
			//$ths[]=array('val'=>'提现总金额', 'wid'=>'');
			$_td .= '<td>'. $Rs[$i]['N_amount_unused'] .'</td>';
			//$ths[]=array('val'=>'闲置资金', 'wid'=>'');
			$_td .= '<td>'. $Rs[$i]['N_amount_all'] .'</td>';
			//$ths[]=array('val'=>'净资产', 'wid'=>'');
			$_td .= '<td><a title="点击查看明细" href="MembersUserStatInfo.php?Code='.$Rs[$i]['ID'].'&Name='.$Rs[$i]['Vc_nickname'].'&Type=invest&Type_='.$type.'">'. $Rs[$i]['N_amount_invest'] .'</a></td>';
			//$ths[]=array('val'=>'投资总金额（累计和明细）', 'wid'=>'');
			$_td .= '<td><a title="点击查看明细" href="MembersUserStatInfo.php?Code='.$Rs[$i]['ID'].'&Name='.$Rs[$i]['Vc_nickname'].'&Type=invest_interest&Type_='.$type.'">'. $Rs[$i]['N_interest'] .'</a></td>';
			//$ths[]=array('val'=>'投资利息（累计和明细）', 'wid'=>'');
			$_td .= '<td><a title="点击查看明细" href="MembersUserStatInfo.php?Code='.$Rs[$i]['ID'].'&Name='.$Rs[$i]['Vc_nickname'].'&Type=fee&Type_='.$type.'">'. $Rs[$i]['N_fee'].'</a></td>';
			//$ths[]=array('val'=>'罚息收入（累计和明细）', 'wid'=>'');
			$_td .= '<td><a title="点击查看明细" href="MembersUserStatInfo.php?Code='.$Rs[$i]['ID'].'&Name='.$Rs[$i]['Vc_nickname'].'&Type=loan&Type_='.$type.'">'. $Rs[$i]['N_amount_loan'].'</a></td>';
			//$ths[]=array('val'=>'借款总金额（累计和明细）', 'wid'=>'');
			$_td .= '<td><a title="点击查看明细" href="MembersUserStatInfo.php?Code='.$Rs[$i]['ID'].'&Name='.$Rs[$i]['Vc_nickname'].'&Type=loan_interest&Type_='.$type.'">'. $Rs[$i]['N_einterest'].'</a></td>';
			//$ths[]=array('val'=>'借款利息（累计和明细）', 'wid'=>'');
			$_td .= '<td><a title="点击查看明细" href="MembersUserStatInfo.php?Code='.$Rs[$i]['ID'].'&Name='.$Rs[$i]['Vc_nickname'].'&Type=efine&Type_='.$type.'">'. $Rs[$i]['N_efine_num'].' 次</a></td>';
			//$ths[]=array('val'=>'逾期次数（累计和明细）', 'wid'=>'');
			$_td .= '<td><a title="点击查看明细" href="MembersUserStatInfo.php?Code='.$Rs[$i]['ID'].'&Name='.$Rs[$i]['Vc_nickname'].'&Type=efine&Type_='.$type.'">'. $Rs[$i]['N_efine'].'</a></td>';
			//$ths[]=array('val'=>'逾期金额（累计和明细）', 'wid'=>'');
			$_td .= '<td><a title="点击查看明细" href="MembersUserStatInfo.php?Code='.$Rs[$i]['ID'].'&Name='.$Rs[$i]['Vc_nickname'].'&Type=efee&Type_='.$type.'">'. $Rs[$i]['N_efee'].'</a>'.'</td>';
			//$ths[]=array('val'=>'罚息支出（累计和明细）', 'wid'=>'');
			$_td .= '<td><a title="点击查看明细" href="MembersUserStatInfo.php?Code='.$Rs[$i]['ID'].'&Name='.$Rs[$i]['Vc_nickname'].'&Type=income&Type_='.$type.'">'. $Rs[$i]['N_income'].'</a></td>';
			//$ths[]=array('val'=>'待收总金额（累计和明细）', 'wid'=>'');
			$_td .= '<td><a title="点击查看明细" href="MembersUserStatInfo.php?Code='.$Rs[$i]['ID'].'&Name='.$Rs[$i]['Vc_nickname'].'&Type=expend&Type_='.$type.'">'. $Rs[$i]['N_expend'].'</a></td>';
			//$ths[]=array('val'=>'待还总金额（累计和明细）', 'wid'=>'');
			$_td .= '<td><a title="点击查看明细" href="MembersUserStatInfo.php?Code='.$Rs[$i]['ID'].'&Name='.$Rs[$i]['Vc_nickname'].'&Type=repayment&Type_='.$type.'">'. $Rs[$i]['N_repayment_num'].' 个</a></td>';
			//$ths[]=array('val'=>'还款中项目数量和金额（明细）', 'wid'=>'');
			$_td .= '<td><a title="点击查看明细" href="MembersUserStatInfo.php?Code='.$Rs[$i]['ID'].'&Name='.$Rs[$i]['Vc_nickname'].'&Type=repayment&Type_='.$type.'">'. $Rs[$i]['N_repayment_amount'].'</a></td>';
			//$ths[]=array('val'=>'还款中项目数量和金额（明细）', 'wid'=>'');
			$_td .= '<td><a title="点击查看明细" href="MembersUserStatInfo.php?Code='.$Rs[$i]['ID'].'&Name='.$Rs[$i]['Vc_nickname'].'&Type=repaid&Type_='.$type.'">'. $Rs[$i]['N_repaid_num'].' 个</a></td>';
			//$ths[]=array('val'=>'已还清项目数量和金额（明细）', 'wid'=>'');
			$_td .= '<td><a title="点击查看明细" href="MembersUserStatInfo.php?Code='.$Rs[$i]['ID'].'&Name='.$Rs[$i]['Vc_nickname'].'&Type=repaid&Type_='.$type.'">'. $Rs[$i]['N_repaid_amount'].'</a></td>';
			//$ths[]=array('val'=>'已还清项目数量和金额（明细）', 'wid'=>'');
			
			$tds[$Rs[$i]['ID']]=$_td;
		}
		}
	}
	$DataBase->CloseDataBase();
	$extend['fan'] = false;
	$helps  = array();
	$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
	$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);
	
	$helps=array('为空搜索：手机号码、真实姓名 选中 为空搜索 即可查询此项为空的信息','数据总数说明：只统计到前一天23:59产生的数据','会员总数说明：只统计未被锁定的会员的数据','明细：绿色数字点击后可查看明细！');
	$tpl = new RainTPL;
	$tpl->assign( 'title', $title );
	$tpl->assign( 'points', $points );
	$tpl->assign( 'sKey', $sKey );
	$tpl->assign( 'sType', $sType );
	$tpl->assign( 'sTypes', $sTypes );
	$tpl->assign( 'hides', $hides );
	$tpl->assign( 'btns', $btns );
	$tpl->assign( 'pagelistparam', $pagelistparam );
	$tpl->assign( 'thsl', $thsl );
	$tpl->assign( 'ths', $ths );
	$tpl->assign( 'tdsl', $tdsl );
	$tpl->assign( 'tds', $tds );
	$tpl->assign( 'helps', $helps );
	$tpl->assign( 'extend', $extend );
	
	$tpl->draw('list_div'.$raintpl_ver);
	
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
		array('真实名称','Vc_truename'),
		array('邮箱账号','Vc_Email'),
		array('手机号','Vc_mobile'),
		array('注册时间','Createtime','datetime'),
		array('推荐好友数','I_friend_num','','个'),
		array('充值总金额','N_amount_recharge'),
		array('提现总金额','N_amount_withdraw'),
		array('闲置资金','N_amount_unused'),
		array('净资产','N_amount_all'),
		array('投资总金额','N_amount_invest'),
		array('投资利息','N_interest'),
		array('罚息收入','N_fee'),
		array('借款总金额','N_amount_loan'),
		array('借款利息','N_einterest'),
		array('逾期次数','N_efine_num'),
		array('逾期金额','N_efine'),
		array('罚息支出','N_efee'),
		array('待收总金额','N_income'),
		array('待还总金额','N_expend'),
		array('还款中项目数量','N_repayment_num'),
		array('还款中项目金额','N_repayment_amount'),
		array('已还清项目数量','N_repaid_num'),
		array('已还清项目金额','N_repaid_amount')
	);
	
	include_once WEBROOTINC.'ExplodeExcel.php';
}
?>
