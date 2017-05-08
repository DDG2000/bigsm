<?php
/****************************************************************** 
**创建者：sakura
**创建时间：2014-10-08
**本页： 会员详细明细
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms_keys(array('SC_STAT_USER_3','SC_STAT_USER_4','SC_STAT_USER_5'));
$isUserSC=$Admin->CheckPopedom('SC_MEMBER');

if(!$outexcel){
	
$Code  = $FLib->RequestChar('Code',1,50,'参数',1);
$Name  = $FLib->RequestChar('Name',1,50,'参数',1);
$Type  = $FLib->RequestChar('Type',1,50,'参数',1);
$sKey3  = $FLib->RequestChar('Type_',1,50,'参数',1);
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$outexcel = $FLib->RequestChar('outexcel',1,9,'outexcel');
$UrlInfo = '&Code=' . $Code.'&Name='.$Name.'&Type='.$Type.'&Type_='.$sKey3;

require('statComment.php');
$dat = statData($Type,$Code,$Name);
require(WEBROOTDATA.'appclass.cache.inc.php');

$title = '会员详情-'.$dat['title'];
$points = array('统计管理', '会员统计', $title );
$sTypes = array('', '名称');
$extend = array();
$extend['hides'] = array();
$extend['js'] = '
<script type="text/javascript">
$(document).ready(function(){
	$(".buta_excel").live("click",function(){
		window.location.href="/admin/stat/MembersUserStatInfo.php?'.$UrlInfo.'&outexcel=true";
	});
})
</script>
';//<a name="submit" class="buta_excel" style="display:inline-block;float:none;"/>导出excel</a>
$btns = array('
<a name="submit" class="history_back" style="display:inline-block;float:none;" href="'.$FLib->IsRequest('backurl').'"/>返回</a>
');

$tables = $dat['tables'];
$sql = "select * FROM {$tables}";
$sqlcount ="select COUNT(*) from {$tables}";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$thsl=$ths = array();
$tdsl=$tds = array();
switch ($dat['type']){
case 'friend':
$ths[]=array('val'=>'用户名', 'wid'=>'');
$ths[]=array('val'=>'真实名称', 'wid'=>'');
$ths[]=array('val'=>'邮箱账号', 'wid'=>'','sty'=>'width:160px');
$ths[]=array('val'=>'手机号', 'wid'=>'');
$ths[]=array('val'=>'注册时间', 'wid'=>'');
$ths[]=array('val'=>'充值总金额', 'wid'=>'');
$ths[]=array('val'=>'投资总金额', 'wid'=>'');
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){	
		$_td='';	
		if($isUserSC)$_td .= '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['I_userID'].'" class="hs" h="" title="【'.$Rs[$i]['Vc_nickname'].'】会员详细页">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</a></td>';
		else $_td .= '<td title="'.$Rs[$i]['Vc_nickname'].'">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</td>';
		$_td .= '<td title="'. $Rs[$i]['Vc_truename'] .'">'. $Rs[$i]['Vc_truename'] .'</td>';
		$_td .= '<td style="width:160px" title="'. $Rs[$i]['Vc_Email'] .'">'. $Rs[$i]['Vc_Email'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Vc_mobile'] .'">'. $Rs[$i]['Vc_mobile'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d H:i:s') .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount_recharge'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount_invest'] .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}	
}
	break;
case 'invest':
$ths[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'借款金额', 'wid'=>'');
$ths[]=array('val'=>'投资金额', 'wid'=>'');
$ths[]=array('val'=>'期数', 'wid'=>'');
$ths[]=array('val'=>'应收金额', 'wid'=>'');
$ths[]=array('val'=>'应收本金', 'wid'=>'');
$ths[]=array('val'=>'实收本金', 'wid'=>'');
$ths[]=array('val'=>'应收利息', 'wid'=>'');
//$ths[]=array('val'=>'应收罚息', 'wid'=>'');
$ths[]=array('val'=>'应收日期', 'wid'=>'');
$ths[]=array('val'=>'实收日期', 'wid'=>'');
$ths[]=array('val'=>'状态', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){		
		$_td = '<td>'. $Rs[$i]['Vc_title'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount_loan'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount_bid'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['I_sort'].'/'.$Rs[$i]['I_sorts'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_capital'] .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_repay']==0?'0.00':$Rs[$i]['N_capital']).'</td>';
		$_td .= '<td>'. $Rs[$i]['N_interest'] .'</td>';
		//$_td .= '<td>'. $Rs[$i]['N_fee'] .'</td>';
		$_td .= '<td>'. $FLib->fromatdate($Rs[$i]['Dt_repay_b'],'Y-m-d') .'</td>';
		$_td .= '<td>'. ($Rs[$i]['Dt_repay']?$FLib->fromatdate($Rs[$i]['Dt_repay'],'Y-m-d'):"") .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_repay']==0?($Rs[$i]['I_status']==2?'逾期':'未还'):($Rs[$i]['I_repay']==2?'已代偿':getInfoByDDIC('p2p_repayment_record.I_repay',$Rs[$i]['I_repay']))).'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}	
}
	break;
case 'invest_interest':
$ths[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'借款金额', 'wid'=>'');
$ths[]=array('val'=>'期数', 'wid'=>'');
$ths[]=array('val'=>'应收金额', 'wid'=>'');
$ths[]=array('val'=>'应收本金', 'wid'=>'');
$ths[]=array('val'=>'应收利息', 'wid'=>'');
$ths[]=array('val'=>'实收利息', 'wid'=>'');
//$ths[]=array('val'=>'应收罚息', 'wid'=>'');
$ths[]=array('val'=>'应收日期', 'wid'=>'');
$ths[]=array('val'=>'实收日期', 'wid'=>'');
$ths[]=array('val'=>'状态', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){		
		$_td = '<td>'. $Rs[$i]['Vc_title'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount_loan'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['I_sort'].'/'.$Rs[$i]['I_sorts'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_capital'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_interest'] .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_repay']==0?'0.00':$Rs[$i]['N_interest']).'</td>';
		//$_td .= '<td>'. $Rs[$i]['N_fee'] .'</td>';
		$_td .= '<td>'. $FLib->fromatdate($Rs[$i]['Dt_repay_b'],'Y-m-d') .'</td>';
		$_td .= '<td>'. ($Rs[$i]['Dt_repay']?$FLib->fromatdate($Rs[$i]['Dt_repay'],'Y-m-d'):"") .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_repay']==0?($Rs[$i]['I_status']==2?'逾期':'未还'):getInfoByDDIC('p2p_repayment_record.I_repay',$Rs[$i]['I_repay'])).'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}	
}
	break;
case 'fee':
$ths[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'借款金额', 'wid'=>'');
$ths[]=array('val'=>'应收罚息', 'wid'=>'');
$ths[]=array('val'=>'实收罚息', 'wid'=>'');
$ths[]=array('val'=>'应收日期', 'wid'=>'');
$ths[]=array('val'=>'实收日期', 'wid'=>'');
//$ths[]=array('val'=>'状态', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){		
		$_td = '<td>'. $Rs[$i]['Vc_title'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount_loan'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_fee'] .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_repay']==0?'0.00':$Rs[$i]['N_fee']).'</td>';
		$_td .= '<td>'. $FLib->fromatdate($Rs[$i]['Dt_repay_b'],'Y-m-d') .'</td>';
		$_td .= '<td>'. ($Rs[$i]['Dt_repay']?$FLib->fromatdate($Rs[$i]['Dt_repay'],'Y-m-d'):"") .'</td>';
		//$_td .= '<td>'. ($Rs[$i]['I_repay']==0?($Rs[$i]['I_status']==2?'逾期':'待收'):($Rs[$i]['I_operation']==1?'已收':'已代偿')).'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}	
}
	break;
case 'loan':
$ths[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'借款金额', 'wid'=>'');
$ths[]=array('val'=>'期数', 'wid'=>'');
$ths[]=array('val'=>'应还金额', 'wid'=>'');
$ths[]=array('val'=>'应还本金', 'wid'=>'');
$ths[]=array('val'=>'实还本金', 'wid'=>'');
$ths[]=array('val'=>'应还利息', 'wid'=>'');
$ths[]=array('val'=>'应还罚息', 'wid'=>'');
$ths[]=array('val'=>'还款日期', 'wid'=>'');
$ths[]=array('val'=>'实还日期', 'wid'=>'');
$ths[]=array('val'=>'状态', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){		
		$_td = '<td>'. $Rs[$i]['Vc_title'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount_loan'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['I_sort'].'/'.$Rs[$i]['I_sorts'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_capital'] .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_operation']==0?'0.00':$Rs[$i]['N_capital']).'</td>';
		$_td .= '<td>'. $Rs[$i]['N_interest'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_fee'] .'</td>';
		$_td .= '<td>'. $FLib->fromatdate($Rs[$i]['Dt_repay'],'Y-m-d') .'</td>';
		$_td .= '<td>'. ($Rs[$i]['Dt_operation']?$FLib->fromatdate($Rs[$i]['Dt_operation'],'Y-m-d'):"") .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_operation']==0?($Rs[$i]['I_status']==2?'逾期':'未还'):getInfoByDDIC('p2p_repayment.I_operation',$Rs[$i]['I_operation'])) .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}	
}
	break;
case 'loan_interest':
$ths[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'借款金额', 'wid'=>'');
$ths[]=array('val'=>'期数', 'wid'=>'');
$ths[]=array('val'=>'应还金额', 'wid'=>'');
$ths[]=array('val'=>'应还本金', 'wid'=>'');
$ths[]=array('val'=>'应还利息', 'wid'=>'');
$ths[]=array('val'=>'实还利息', 'wid'=>'');
$ths[]=array('val'=>'应还罚息', 'wid'=>'');
$ths[]=array('val'=>'应还日期', 'wid'=>'');
$ths[]=array('val'=>'实还日期', 'wid'=>'');
$ths[]=array('val'=>'状态', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){		
		$_td = '<td>'. $Rs[$i]['Vc_title'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount_loan'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['I_sort'].'/'.$Rs[$i]['I_sorts'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_capital'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_interest'] .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_operation']==0?'0.00':$Rs[$i]['N_interest']).'</td>';
		$_td .= '<td>'. $Rs[$i]['N_fee'] .'</td>';
		$_td .= '<td>'. $FLib->fromatdate($Rs[$i]['Dt_repay'],'Y-m-d') .'</td>';
		$_td .= '<td>'. ($Rs[$i]['Dt_operation']?$FLib->fromatdate($Rs[$i]['Dt_operation'],'Y-m-d'):"") .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_operation']==0?($Rs[$i]['I_status']==2?'逾期':'未还'):getInfoByDDIC('p2p_repayment.I_operation',$Rs[$i]['I_operation'])) .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}	
}
	break;
case 'efine':
$ths[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'借款金额', 'wid'=>'');
$ths[]=array('val'=>'逾期期数', 'wid'=>'');
$ths[]=array('val'=>'应还本金', 'wid'=>'');
$ths[]=array('val'=>'应还利息', 'wid'=>'');
$ths[]=array('val'=>'逾期罚息', 'wid'=>'');
$ths[]=array('val'=>'应还日期', 'wid'=>'');
$ths[]=array('val'=>'还款状态', 'wid'=>'');
$ths[]=array('val'=>'实还日期', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){
		$_td = '<td>'. $Rs[$i]['Vc_title'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount_loan'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['I_sort'].'/'.$Rs[$i]['I_sorts'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_capital'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_interest'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_fee'] .'</td>';
		$_td .= '<td>'.$FLib->fromatdate($Rs[$i]['Dt_repay'],'Y-m-d') .'</td>';
		$_td .= '<td>'. getInfoByDDIC('p2p_repayment.I_operation',$Rs[$i]['I_operation']) .'</td>';
		$_td .= '<td>'. $FLib->fromatdate($Rs[$i]['Dt_operation'],'Y-m-d').'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}
}
	break;
case 'efee':
$ths[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'借款金额', 'wid'=>'');
$ths[]=array('val'=>'期数', 'wid'=>'');
$ths[]=array('val'=>'应还金额', 'wid'=>'');
$ths[]=array('val'=>'应还本金', 'wid'=>'');
$ths[]=array('val'=>'应还利息', 'wid'=>'');
$ths[]=array('val'=>'应还罚息', 'wid'=>'');
$ths[]=array('val'=>'罚息天数', 'wid'=>'');
$ths[]=array('val'=>'实还罚息', 'wid'=>'');
$ths[]=array('val'=>'应还日期', 'wid'=>'');
$ths[]=array('val'=>'实还日期', 'wid'=>'');
//$ths[]=array('val'=>'状态', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){
		$_td = '<td>'. $Rs[$i]['Vc_title'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount_loan'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['I_sort'].'/'.$Rs[$i]['I_sorts'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_capital'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_interest'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_fee'] .'</td>';
		$_td .= '<td>'. ($Rs[$i]['Dt_check']? intval((strtotime($Rs[$i]['Dt_check'])-strtotime($Rs[$i]['Dt_repay']))/3600/24):'0') .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_operation']==1?$Rs[$i]['N_fee']:'0.00').'</td>';
		$_td .= '<td>'. $FLib->fromatdate($Rs[$i]['Dt_repay'],'Y-m-d') .'</td>';
		$_td .= '<td>'. $FLib->fromatdate($Rs[$i]['Dt_operation'],'Y-m-d') .'</td>';
		//$_td .= '<td>'.  ($Rs[$i]['I_operation']==0?"逾期":getInfoByDDIC('p2p_repayment.I_operation',$Rs[$i]['I_operation'])).'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}
}
	break;
case 'income':
$ths[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'投资金额', 'wid'=>'');
$ths[]=array('val'=>'期数', 'wid'=>'');
$ths[]=array('val'=>'待收金额', 'wid'=>'');
$ths[]=array('val'=>'待收本金', 'wid'=>'');
$ths[]=array('val'=>'待收利息', 'wid'=>'');
/* $ths[]=array('val'=>'待收罚息', 'wid'=>''); */
$ths[]=array('val'=>'状态', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){
		$_td = '<td>'. $Rs[$i]['Vc_title'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount_loan'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['I_sort'].'/'.$Rs[$i]['I_sorts'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_capital'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_interest'] .'</td>';
		/* $_td .= '<td>'. $Rs[$i]['N_fee'] .'</td>'; */
		$_td .= '<td>'. ($Rs[$i]['I_status']==2?'逾期':'未收') .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}
}
	break;
case 'expend':
$ths[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'借款金额', 'wid'=>'');
$ths[]=array('val'=>'期数', 'wid'=>'');
$ths[]=array('val'=>'待还金额', 'wid'=>'');
$ths[]=array('val'=>'待还本金', 'wid'=>'');
$ths[]=array('val'=>'待还利息', 'wid'=>'');
$ths[]=array('val'=>'待还罚息', 'wid'=>'');
$ths[]=array('val'=>'应还日期', 'wid'=>'');
$ths[]=array('val'=>'状态', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){
		$_td = '<td>'. $Rs[$i]['Vc_title'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount_loan'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['I_sort'].'/'.$Rs[$i]['I_sorts'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_capital'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_interest'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_fee'] .'</td>';
		$_td .= '<td>'. $FLib->fromatdate($Rs[$i]['Dt_repay'],'Y-m-d') .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_status']==2?'逾期':'待还') .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}
}
	break;
case 'repayment':
$ths[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'借款金额', 'wid'=>'');
$ths[]=array('val'=>'标类型', 'wid'=>'');
$ths[]=array('val'=>'子分类', 'wid'=>'');
$ths[]=array('val'=>'借款期限', 'wid'=>'');
$ths[]=array('val'=>'年利率', 'wid'=>'');
$ths[]=array('val'=>'还款方式', 'wid'=>'');
$ths[]=array('val'=>'期数', 'wid'=>'');
$ths[]=array('val'=>'应还金额', 'wid'=>'');
$ths[]=array('val'=>'应还本金', 'wid'=>'');
$ths[]=array('val'=>'应还利息', 'wid'=>'');
$ths[]=array('val'=>'应还罚息', 'wid'=>'');
$ths[]=array('val'=>'应还日期', 'wid'=>'');
$ths[]=array('val'=>'状态', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){		
		$_td = '<td>'. $Rs[$i]['Vc_title'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount_loan'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['cname'].'</td>';
		$_td .= '<td>'. $Rs[$i]['sbname'].'</td>';
		$_td .= '<td>'. $Rs[$i]['Vc_life'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_yearannualrate'] .'</td>';
		$_td .= '<td>'. getInfoByDDIC('p2p_application.I_repayment',$Rs[$i]['I_repayment']) .'</td>';
		$_td .= '<td>'. $Rs[$i]['I_sort'].'/'.$Rs[$i]['I_sorts'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_capital'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_interest'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_fee'] .'</td>';
		$_td .= '<td>'. $FLib->fromatdate($Rs[$i]['Dt_repay'],'Y-m-d') .'</td>';
		$_td .= '<td>'. getInfoByDDIC('p2p_repayment.I_status',$Rs[$i]['I_status']).'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}	
}
	break;
case 'repaid':
$ths[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'借款金额', 'wid'=>'');
$ths[]=array('val'=>'标类型', 'wid'=>'');
$ths[]=array('val'=>'子分类', 'wid'=>'');
$ths[]=array('val'=>'借款期限', 'wid'=>'');
$ths[]=array('val'=>'年利率', 'wid'=>'');
$ths[]=array('val'=>'还款方式', 'wid'=>'');
$ths[]=array('val'=>'期数', 'wid'=>'');
$ths[]=array('val'=>'已还金额', 'wid'=>'');
$ths[]=array('val'=>'已还本金', 'wid'=>'');
$ths[]=array('val'=>'已还利息', 'wid'=>'');
$ths[]=array('val'=>'已还罚息', 'wid'=>'');
$ths[]=array('val'=>'应还日期', 'wid'=>'');
$ths[]=array('val'=>'还清日期', 'wid'=>'');
$ths[]=array('val'=>'状态', 'wid'=>'');
$ths[]=array('val'=>'垫付人', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){		
		$_td = '<td>'. $Rs[$i]['Vc_title'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount_loan'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['cname'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['sbname'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['Vc_life'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_yearannualrate'] .'</td>';
		$_td .= '<td>'. getInfoByDDIC('p2p_application.I_repayment',$Rs[$i]['I_repayment']) .'</td>';
		$_td .= '<td>'. $Rs[$i]['I_sort'].'/'.$Rs[$i]['I_sorts'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_capital'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_interest'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_fee'] .'</td>';
		$_td .= '<td>'. $FLib->fromatdate($Rs[$i]['Dt_repay'],'Y-m-d') .'</td>';
		$_td .= '<td>'. $FLib->fromatdate($Rs[$i]['Dt_operation'],'Y-m-d') .'</td>';
		$_td .= '<td>'.($Rs[$i]['I_type']==4?'垫付':getInfoByDDIC('p2p_repayment.I_operation',$Rs[$i]['I_operation'])) .'</td>';
		$_td .= '<td>'. $Rs[$i]['Vc_substitute'] .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}	
}
	break;

}

$DataBase->CloseDataBase();
$extend['fan'] = false;
$helps  = array();
$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';

$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'sKey', $sKey );
$tpl->assign( 'btns', $btns );
$tpl->assign( 'pagelistparam', $pagelistparam );
$tpl->assign( 'thsl', $thsl );
$tpl->assign( 'tdsl', $tdsl );
$tpl->assign( 'ths', $ths );
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
	
	//include_once WEBROOTINC.'ExplodeExcel.php';
}
?>