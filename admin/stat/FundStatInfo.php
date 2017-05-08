<?php
if(1){
/****************************************************************** 
**创建者：sakura
**创建时间：2014-10-08
**本页： 资金统计明细  统计
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_APPLICATION_3');
$isUserSC=$Admin->CheckPopedom('SC_MEMBER');
$isP2PSC=$Admin->CheckPopedom('SC_LOAN_APP');

$type  = $FLib->RequestChar('type',1,50,'参数',1);
$sKey1  = $FLib->RequestChar('Name',1,50,'参数',1);
$sKey2  = $FLib->RequestChar('Type',1,50,'参数',1);
$isA  = $FLib->RequestInt('isA',-1,50,'偿还状态');
$re  = $FLib->RequestInt('re',-1,50,'用户还款状态');

$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = '&type=' . $type.($isA>-1?'&isA=' . $isA:'').($re>-1?'&re=' . $re:'');

$dat = returnDat($type,($isA>-1?" and a.isAdmin={$isA}":'').($re>-1?" and a.I_operation={$re}":''));

$title = '资金统计-'.$dat['title'];
$points = array('统计管理', '项目统计', $title );
$sTypes = array('', '名称');
$extend = array();
$extend['hides'] = array();
$hides = array('type'=>$type);
$btns = array('
<a name="submit" class="history_back" style="display:inline-block;float:none;" href="'.$FLib->IsRequest('backurl').'"/>返回</a>
');

$sql = $dat['sql'];
$sqlcount =$dat['sqlcount'];
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths=$thsl=$tds=$tdsl = array();
switch ($dat['type']){
case 'a8':
$thsl[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'借款金额', 'wid'=>'');
$ths[]=array('val'=>'风险备付金金额', 'wid'=>'');
$ths[]=array('val'=>'风险备付金费率', 'wid'=>'');
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){
		$_td='';
		if($isP2PSC)$_td .= '<td><a href="../p2p/ApplicationInfo.php?Id='.$Rs[$i]['ID'].'" class="hs" w="750" h="700" title="【'.$Rs[$i]['Vc_title'].'】详细页">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</a></td>';
		else $_td .= '<td title="'.$Rs[$i]['Vc_title'].'">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</td>';
		$tdsl[$Rs[$i]['ID']]=$_td;
		$_td='';
		$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_reserve'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_reserverate'] .'%</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}	
}
	break;
case 'a10':
$thsl[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'借款金额', 'wid'=>'');
$ths[]=array('val'=>'借款期限', 'wid'=>'');
$ths[]=array('val'=>'管理费金额', 'wid'=>'');
$ths[]=array('val'=>'管理费费率', 'wid'=>'');
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){		
		$_td='';
		if($isP2PSC)$_td .= '<td><a href="../p2p/ApplicationInfo.php?Id='.$Rs[$i]['ID'].'" class="hs" w="750" h="700" title="【'.$Rs[$i]['Vc_title'].'】详细页">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</a></td>';
		else $_td .= '<td title="'.$Rs[$i]['Vc_title'].'">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</td>';
		$tdsl[$Rs[$i]['ID']]=$_td;
		$_td='';
		$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['Vc_life'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_borrow'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_borrowrate'] .'%</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}	
}
	break;
case 'a13':
$thsl[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'借款金额', 'wid'=>'');
$ths[]=array('val'=>'借款人', 'wid'=>'');
$ths[]=array('val'=>'逾期期数', 'wid'=>'');
$ths[]=array('val'=>'逾期本金', 'wid'=>'');
$ths[]=array('val'=>'逾期利息', 'wid'=>'');
$ths[]=array('val'=>'逾期罚息', 'wid'=>'');
$ths[]=array('val'=>'应还时间', 'wid'=>'');
$ths[]=array('val'=>'还款状态', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){	
		$_td='';
		if($isP2PSC)$_td .= '<td><a href="../p2p/ApplicationInfo.php?Id='.$Rs[$i]['I_applicationID'].'" class="hs" w="750" h="700" title="【'.$Rs[$i]['Vc_title'].'】详细页">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</a></td>';
		else $_td .= '<td title="'.$Rs[$i]['Vc_title'].'">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</td>';
		$tdsl[$Rs[$i]['ID']]=$_td;
		$_td='';
		$_td .= '<td>'. $Rs[$i]['N_amount_loan'] .'</td>';
		if($isUserSC)$_td .= '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['I_userID'].'" class="hs" h="" title="会员详细页">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</a></td>';
		else $_td .= '<td title="'.$Rs[$i]['Vc_nickname'].'">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</td>';
		$_td .= '<td>'. $Rs[$i]['I_sort'].'/'. $Rs[$i]['I_sorts'].'</td>';
		$_td .= '<td>'. $Rs[$i]['N_capital'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_interest'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_fee'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Dt_repay'] .'">'. $FLib->fromatdate($Rs[$i]['Dt_repay'],'Y-m-d') .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_operation']==0?"逾期":getInfoByDDIC("p2p_repayment.I_operation",$Rs[$i]['I_operation'])) .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}	
}
	break;
case 'a14':
case 'a15':
$thsl[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'借款金额', 'wid'=>'');
$ths[]=array('val'=>'借款人', 'wid'=>'');
$ths[]=array('val'=>'逾期期数', 'wid'=>'');
$ths[]=array('val'=>'逾期金额', 'wid'=>'');
$ths[]=array('val'=>'逾期时间', 'wid'=>'');
$ths[]=array('val'=>'代偿金额', 'wid'=>'');
$ths[]=array('val'=>'代偿时间', 'wid'=>'');
$ths[]=array('val'=>'代偿状态', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){		
		$_td='';
		if($isP2PSC)$_td .= '<td><a href="../p2p/ApplicationInfo.php?Id='.$Rs[$i]['I_applicationID'].'" class="hs" w="750" h="700" title="【'.$Rs[$i]['Vc_title'].'】详细页">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</a></td>';
		else $_td .= '<td title="'.$Rs[$i]['Vc_title'].'">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</td>';
		$tdsl[$Rs[$i]['ID']]=$_td;
		$_td='';
		$_td .= '<td>'. $Rs[$i]['N_amount_loan'] .'</td>';
		if($isUserSC)$_td .= '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['I_userID'].'" class="hs" h="" title="会员详细页">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</a></td>';
		else $_td .= '<td title="'.$Rs[$i]['Vc_nickname'].'">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</td>';
		$_td .= '<td>'. $Rs[$i]['I_sort'].'/'. $Rs[$i]['I_sorts'].'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount_repay'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Dt_repay'] .'">'. $FLib->fromatdate($Rs[$i]['Dt_repay'],'Y-m-d') .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$_td .= '<td>'. getInfoByDDIC("p2p_apply_*.I_status",$Rs[$i]['I_Status']) .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
	}	
}
	break;
case 'a16':
	$extend['sTypes'] = array('<span class="spancheck reasoncheck">偿还状态：<select name="type" class="sel_put1 chzn-select-no-single">
			<option value="all">所有</option>
			<option value="0" '.($isA==0?'selected="selected"':'').'>未偿还</option>
			<option value="1" '.($isA==1?'selected="selected"':'').'>已偿还</option>
			</select></span>'
			,'<span class="spancheck reasoncheck">用户还款状态：<select name="reason" class="sel_put1 chzn-select-no-single" style="width:130px">
			<option value="all">所有</option>
			<option value="0" '.($re==0?'selected="selected"':'').'>未还</option>
			<option value="1" '.($re==1?'selected="selected"':'').'>已还</option>
			<!-- <option value="2" '.($re==2?'selected="selected"':'').'>被偿还</option> -->
			</select></span>'
			);
	
$thsl[]=array('val'=>'借款标题', 'wid'=>'');
$ths[]=array('val'=>'借款金额', 'wid'=>'');
$ths[]=array('val'=>'借款人', 'wid'=>'');
$ths[]=array('val'=>'逾期期数', 'wid'=>'');
$ths[]=array('val'=>'逾期金额', 'wid'=>'');
$ths[]=array('val'=>'逾期时间', 'wid'=>'');
$ths[]=array('val'=>'罚息费费率', 'wid'=>'');
$ths[]=array('val'=>'逾期天数', 'wid'=>'');
$ths[]=array('val'=>'罚息金额', 'wid'=>'');
$ths[]=array('val'=>'用户罚息总额', 'wid'=>'');
$ths[]=array('val'=>'偿还状态', 'wid'=>'');
$ths[]=array('val'=>'用户还款状态', 'wid'=>'');
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs);$i++){		
		$_td='';
		if($isP2PSC)$_td .= '<td><a href="../p2p/ApplicationInfo.php?Id='.$Rs[$i]['I_applicationID'].'" class="hs" w="750" h="700" title="【'.$Rs[$i]['Vc_title'].'】详细页">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</a></td>';
		else $_td .= '<td title="'.$Rs[$i]['Vc_title'].'">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</td>';
		$tdsl[$Rs[$i]['ID']]=$_td;
		$_td='';
		$_td .= '<td>'. $Rs[$i]['N_amount_loan'] .'</td>';
		if($isUserSC)$_td .= '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['I_userID'].'" class="hs" h="" title="会员详细页">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</a></td>';
		else $_td .= '<td title="'.$Rs[$i]['Vc_nickname'].'">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</td>';
		$_td .= '<td>'. $Rs[$i]['I_sort'].'/'. $Rs[$i]['I_sorts'].'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Dt_repay'] .'">'. $FLib->fromatdate($Rs[$i]['Dt_repay'],'Y-m-d') .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_rate'] .'%</td>';
		$_td .= '<td>'. $Rs[$i]['N_day'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_fee'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['N_fee_touser'] .'</td>';
		$_td .= '<td>'.  ($Rs[$i]['isAdmin']==1?'已代偿':"未代偿") .'</td>';
		$_td .= '<td>'.  getInfoByDDIC("p2p_repayment.I_operation",$Rs[$i]['I_operation']) .'</td>';
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
$tpl->assign( 'hides', $hides );
$tpl->assign( 'pagelistparam', $pagelistparam );
$tpl->assign( 'ths', $ths );
$tpl->assign( 'tds', $tds );
$tpl->assign( 'thsl', $thsl );
$tpl->assign( 'tdsl', $tdsl );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );
$tpl->draw('list_div'.$raintpl_ver);
exit;
}

function returnDat($type='a8',$where){
	global $g_conf;
	$dat=array();
	$dat['type']=$type;
	$subSql1 = "SELECT *, 0 isAdmin FROM p2p_repayment WHERE I_operation<>2 ";
	$subSql2 = "SELECT *, 1 isAdmin FROM p2p_repayment_admin ";
	$unionSql = "(".$subSql1." UNION ".$subSql2.")";
	switch ($type) {
		case 'a8':
			$dat['title']='风险备付金收取明细';
			$tables = 'p2p_application where I_status>=50 and Status=1';
			//借款本金、风险备付金费率和金额
			$dat['sql'] = "select ID,Vc_title,N_amount,N_reserve,N_reserverate from {$tables}";
			$dat['sqlcount'] ="select COUNT(*) from {$tables}";
			break;
		case 'a10':
			$dat['title']='借款管理费收取明细';
			$tables = 'p2p_application where I_status>=50 and Status=1';
			//借款本金、管理费费率和管理费金额
			$dat['sql'] = "select ID,Vc_title,N_amount,Vc_life,N_borrow,N_borrowrate,N_amount from {$tables}";
			$dat['sqlcount'] ="select COUNT(*) from {$tables}";
			break;
		case 'a13':
			$dat['title']='逾期明细';
			$tables = $unionSql.' a 
					left join p2p_application b on b.ID=a.I_applicationID
					left join user_base c on a.I_repayID=c.ID 
				where a.Status=1 and a.I_status =2 and a.I_type=1';
			//逾期项目、借款人、逾期期数、逾期金额、应还时间、还款状态
			$dat['sql'] = "select a.ID,b.Vc_title,b.N_amount as N_amount_loan,c.Vc_nickname,
								a.I_sort ,I_sorts,
								a.N_amount,a.N_capital,a.N_interest,a.N_fee,
								a.Dt_repay,a.I_operation,a.I_applicationID,c.ID as I_userID
							from {$tables}";
			$dat['sqlcount'] ="select COUNT(*) from {$tables}";
			break;
		case 'a14':
			$dat['title']='平台代偿明细';
			$tables = "p2p_apply_substitute a 
						left join p2p_application b on b.ID=a.I_applicationID
						left join p2p_repayment c on a.I_repaymentID=c.ID
						left join user_base d on c.I_repayID=d.ID 
					where a.Status=1 and a.I_repayID={$g_conf['cfg_loan_reserve_account']} and (a.I_Status=3)";
			//项目名称、借款人、逾期期数、逾期金额、逾期时间、代偿时间和金额、代偿状态等
			$dat['sql'] = "select a.ID, b.Vc_title ,b.N_amount as N_amount_loan,d.Vc_nickname,
								c.I_sort,c.I_sorts,(c.N_capital+c.N_interest) as N_amount_repay,c.Dt_repay,
								a.N_amount,a.Createtime,a.I_Status,
								a.I_applicationID,d.ID as I_userID
							from {$tables}";
			$dat['sqlcount'] ="select COUNT(*) from {$tables}";
			break;
		case 'a15':
			$dat['title']='担保机构代偿明细';
			$tables = "p2p_apply_substitute a
						left join p2p_application b on b.ID=a.I_applicationID
						left join p2p_repayment c on a.I_repaymentID=c.ID
						left join user_base d on c.I_repayID=d.ID 
					where a.Status=1 and a.I_repayID<>{$g_conf['cfg_loan_reserve_account']} and (a.I_Status=3)";
			//项目名称、借款人、逾期期数、逾期金额、逾期时间、代偿时间和金额、代偿状态等
			$dat['sql'] = "select a.ID,b.Vc_title ,b.N_amount as N_amount_loan,d.Vc_nickname ,
								c.I_sort ,c.I_sorts,(c.N_capital+c.N_interest) as N_amount_repay,c.Dt_repay ,
								a.N_amount,a.Createtime ,a.I_Status,
								a.I_applicationID,d.ID as I_userID
							from {$tables}";
			$dat['sqlcount'] ="select COUNT(*) from {$tables}";
			break;
		case 'a16':
			$dat['title']='罚息明细';
			$tables = $unionSql.' a 
						left join p2p_application b on b.ID=a.I_applicationID
						left join user_base c on a.I_repayID=c.ID 
					where a.Status=1 and a.I_status=2 and a.I_type=1'.$where;
			//项目名称、借款人、逾期期数、逾期金额、逾期时间、罚息费率、逾期天数、罚息金额、偿还状态等
			$dat['sql'] = "select a.ID,b.Vc_title,b.N_amount as N_amount_loan,c.Vc_nickname,
								a.I_sort,a.I_sorts,(a.N_capital+a.N_interest) N_amount,a.Dt_repay,
								if(a.Dt_check is null,0,a.N_overdue_rate) as N_rate,
								if(a.Dt_check is null,0,DATEDIFF(a.Dt_check,a.Dt_repay)) as N_day,
								a.N_fee,a.N_fee_touser,
								a.I_operation ,a.isAdmin,
								a.I_applicationID,c.ID as I_userID
							from {$tables}";
			$dat['sqlcount'] ="select COUNT(*) from {$tables}";
			break;
	}
	return $dat;
}
?>