<?php
/****************************************************************** 
**创建者：sakura
**创建时间：2014-10-08
**本页：充值提现明细
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_OTHER');
$isUserSC=$Admin->CheckPopedom('SC_MEMBER');
$banks = $DDIC['p2p_user_bankcard.Vc_bankcode'];
	
$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$type  = $FLib->RequestInt('type',1,9,'类型');
$sDeal  = $FLib->RequestInt('sDeal',0,9,'类型');
$starttime = $FLib->RequestChar('starttime',1,19,'参数',1);
$endtime = $FLib->RequestChar('endtime',1,19,'参数',1);
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType ."&type=" . $type .
	"&starttime=" . $starttime."&endtime=".$endtime."&sDeal=".$sDeal;
$outexcel = $FLib->RequestChar('outexcel',1,9,'outexcel');

$title='';
if($sKey)$mWhere .= " and b.Vc_nickname like '%" . $sKey . "%'";
if($sDeal == 1)$mWhere .= " and a.I_deal=1";
if($sDeal == 2)$mWhere .= " and a.I_deal<>1 and left(a.Createtime,10)='".date('Y-m-d')."'";
if($sDeal == 3)$mWhere .= " and a.I_deal<>1 and left(a.Createtime,10)<>'".date('Y-m-d')."'";

if($starttime)$mWhere .= " and a.Createtime >= '{$starttime}' ";
if($endtime)$mWhere .= " and a.Createtime <= '{$endtime}' ";
if($type==1){
	$Admin->CheckPopedoms('SC_STAT_OTHER_1');
	$title='充值';
}elseif($type==2){
	$Admin->CheckPopedoms('SC_STAT_OTHER_2');
	$title='提现';
}else{
	exit;
}

$title = $title.'明细';

$table = 'p2p_pay_order a 
			left join user_base b on b.ID=a.I_userID 
		where a.Status=1 and a.I_type='.$type.$mWhere;
$sql = 'select a.*,b.Vc_nickname,b.Vc_truename,b.Vc_mobile,b.Vc_Email FROM '.$table;
$sqlorder = ' order by a.Createtime desc';
$sqlcount ="select COUNT(*) from {$table}";
$sqlwmtime = $sql." and left(a.Createtime,7)='explodeExcelTy2Mtime' ".$sqlorder;
$sqlmtimecount = "select left(a.Createtime,7) as mtime from {$table} group by left(a.Createtime,7)";
$sql .= $sqlorder;

if(!$outexcel){
	
	$pagesize = $Config->AdminPageSize;
	$pagecount = 1;$rscount=0;
	$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);
	
	$points = array('统计管理', '其他', $title );
	$extend = array();
	$hides = array('type'=>$type);
	$extend['js'] = '
	<link type="text/css" rel="stylesheet" href="../include/timepicker/css/jquery-ui.css" >
	<script type="text/javascript" src="../js/form.checkfun.js"></script>
	<script type="text/javascript" src="../include/timepicker/js/jquery-ui.js"></script>
	<script type="text/javascript" src="../include/timepicker/js/jquery-ui-slide.min.js"></script>
	<script type="text/javascript" src="../include/timepicker/js/jquery-ui-timepicker-addon.js"></script>
	<script type="text/javascript">
	$(function(){
		$("input[name=\'starttime\']").datetimepicker({
			changeYear:true,
			yearRange:"1900:'.date('Y').'",
			maxDate:0,
			showSecond: true,
	 		timeFormat: "hh:mm:ss",
			onSelect : function(selectedDate) {
				$("input[name=\'endtime\']").datetimepicker("option", "minDate", selectedDate);
			}
		});
		$("input[name=\'endtime\']").datetimepicker({
			changeYear:true,
			yearRange:"1900:'.date('Y').'",
			maxDate:1,
			showSecond: true,
	 		timeFormat: "hh:mm:ss",
			hour: 23,
			minute: 59,
			second: 59,
			onSelect : function(selectedDate) {
				$("input[name=\'starttime\']").datetimepicker("option", "maxDate", selectedDate);
			}
		});
		'.($starttime?'$("input[name=\'endtime\']").datetimepicker("option", "minDate", "'.$starttime.'");':'').'
		$(".buta_excel").live("click",function(){
			window.location.href="/admin/stat/PayStatInfo.php?outexcel=true'.$UrlInfo.'";
		});
		$("input[name=\'starttime\']").val("'.$starttime.'");
		$("input[name=\'endtime\']").val("'.$endtime.'");
	})
	</script>';
	$extend['sTypes'] = array('<span class="spancheck defaultcheck">用户名：<input name="sKey" type="text" class="txt_put1" value="'.$sKey.'" maxlength="50"></span>',
			'<span class="spancheck dealcheck">状态：<select name="sDeal" class="sel_put1 chzn-select-no-single"><option value="">所有</option>'
			.($type==1?'<option '.($sDeal==1?'selected="selected"':'').' value="1">成功</option>
					<option '.($sDeal==2?'selected="selected"':'').' value="2">处理中</option>
					<option '.($sDeal==3?'selected="selected"':'').' value="3">失败</option>':'')
			.($type==2?'<option '.($sDeal==1?'selected="selected"':'').' value="1">成功</option>
					<option '.($sDeal==2?'selected="selected"':'').' value="2">处理中</option>
					<option '.($sDeal==3?'selected="selected"':'').' value="3">退回</option>':'').
			'</select></span>'
			,'<span class=" timecheck">操作时间：<input class="txt_put_datetime" name="starttime" value="'.$starttime.'" data-time="" onclick="nodatepatFun" tp="开始时间" maxlength="19"/>
			- <input class="txt_put_datetime" name="endtime" value="'.$endtime.'" data-time="" onclick="nodatepatFun" tp="结束时间" maxlength="19"/></span>');
	$btns = array('<a name="submit" class="buta_excel" style="display:inline-block;float:none;"/>导出excel</a>');
	
	$ths =$tds = array();
	$ths[]=array('val'=>'用户名', 'wid'=>'');
	$ths[]=array('val'=>'真实姓名', 'wid'=>'');
	//$ths[]=array('val'=>'联系电话', 'wid'=>'');
	//$ths[]=array('val'=>'邮箱', 'wid'=>'');
	if($type==1){
		$ths[]=array('val'=>'订单号', 'wid'=>'');
		$ths[]=array('val'=>'流水号', 'wid'=>'');
		$ths[]=array('val'=>'卡类型', 'wid'=>'');
		$ths[]=array('val'=>'充值类型', 'wid'=>'');
		$ths[]=array('val'=>'充值金额', 'wid'=>'');
		$ths[]=array('val'=>'手续费', 'wid'=>'');
		$ths[]=array('val'=>'充值时间', 'wid'=>'');
		$ths[]=array('val'=>'备注', 'wid'=>'');
		$ths[]=array('val'=>'状态', 'wid'=>'');
		if(is_array($Rs)){
			$pagecount = $Rs[0]['pagecount'];
			$rscount = $Rs[0]['rscount'];
			$extend['rscount']=$rscount;
			for($i=1;$i<count($Rs);$i++){
				$_td='';
				if($isUserSC)$_td .= '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['I_userID'].'" class="hs" h="" title="【'.$Rs[$i]['Vc_nickname'].'】会员详细页">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</a></td>';
				else $_td .= '<td title="'.$Rs[$i]['Vc_nickname'].'">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</td>';
				$_td .= '<td>'. $Rs[$i]['Vc_truename'] .'</td>';
				//$_td .= '<td>'. $Rs[$i]['Vc_mobile'] .'</td>';
				//$_td .= '<td>'. $Rs[$i]['Vc_Email'] .'</td>';
				$_td .= '<td>'. $Rs[$i]['Vc_code'] .'</td>';
				$_td .= '<td>'. $Rs[$i]['Vc_paycode'] .'</td>';
				$_td .= '<td>'. $Rs[$i]['Vc_payment'] .'</td>';
				$_td .= '<td>'. getInfoByDDIC('p2p_pay_order.I_recharge_type',$Rs[$i]['I_recharge_type']) .'</td>';
				$_td .= '<td>'. $Rs[$i]['N_price'] .'</td>';
				$_td .= '<td>'. $Rs[$i]['N_price_fee'] .'</td>';
				$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d H:i:s') .'</td>';
				$_td .= '<td>'. $Rs[$i]['Vc_intro'] .'</td>';
				$_td .= '<td>'. ($Rs[$i]['I_deal']==1?"成功":(($FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d')==date('Y-m-d'))?"处理中":"失败")) .'</td>';
				$tds[$Rs[$i]['ID']]=$_td;
			}
		}
	}elseif ($type==2){
		$ths[]=array('val'=>'提现账号', 'wid'=>'');
		$ths[]=array('val'=>'提现银行', 'wid'=>'');
		$ths[]=array('val'=>'提现金额', 'wid'=>'');
		$ths[]=array('val'=>'提现时间', 'wid'=>'');
		$ths[]=array('val'=>'状态', 'wid'=>'');
		if(is_array($Rs)){
			$pagecount = $Rs[0]['pagecount'];
			$rscount = $Rs[0]['rscount'];
			$extend['rscount']=$rscount;
			for($i=1;$i<count($Rs);$i++){		
				$_td = '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['I_userID'].'" class="hs" h="" title="【'.$Rs[$i]['Vc_nickname'].'】会员详细页">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</a></td>';
				$_td .= '<td>'. $Rs[$i]['Vc_truename'] .'</td>';
				//$_td .= '<td>'. $Rs[$i]['Vc_mobile'] .'</td>';
				//$_td .= '<td>'. $Rs[$i]['Vc_Email'] .'</td>';
				$_td .= '<td width="130">'. $Rs[$i]['Vc_bankcard'] .'</td>';
				$_td .= '<td>'. $banks[$Rs[$i]['Vc_payment']]['name'].'</td>';
				$_td .= '<td>'. $Rs[$i]['N_price'] .'</td>';
				$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d H:i:s') .'</td>';
				$_td .= '<td>'. ($Rs[$i]['I_deal']==1?"成功":(($FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d')==date('Y-m-d'))?"处理中":"退回")) .'</td>';
				$tds[$Rs[$i]['ID']]=$_td;
			}	
		}
	}
	$DataBase->CloseDataBase();
	$extend['fan'] = false;
	$helps  = array();
	$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
	$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);
	
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
	$dat['fields']=array();
	$dat['fields'][]=array('用户名','Vc_nickname');
	$dat['fields'][]=array('真实姓名','Vc_truename');
	//$dat['fields'][]=array('联系电话','Vc_mobile');
	//$dat['fields'][]=array('邮箱','Vc_Email');
	if($type==1){
		array_push($dat['fields'],
				array('订单号','Vc_code'),
				array('流水号','Vc_paycode'),
				array('卡类型','Vc_payment'),
				array('充值类型','getInfoByDDIC(\'p2p_pay_order.I_recharge_type\',$Rs[$i][\'I_recharge_type\'])','other'),
				array('充值金额','N_price'),
				array('手续费','N_price_fee'),
				array('充值时间','Createtime','datetime'),
				array('备注','Vc_intro'),
				array('状态','$Rs[$i][\'I_deal\']==1?"成功":(($FLib->fromatdate($Rs[$i][\'Createtime\'],\'Y-m-d\')==date(\'Y-m-d\'))?"处理中":"失败")','other')
		);
	}elseif ($type==2){
		array_push($dat['fields'],
				array('提现账号','Vc_bankcard'),
				array('提现银行','$banks[$Rs[$i][\'Vc_payment\']][\'name\']','other'),
				array('提现金额','N_price'),
				array('提现时间','Createtime','datetime'),
				array('状态','$Rs[$i][\'I_deal\']==1?"成功":(($FLib->fromatdate($Rs[$i][\'Createtime\'],\'Y-m-d\')==date(\'Y-m-d\'))?"处理中":"退回")','other')
		);
	}
		
	include_once WEBROOTINC.'ExplodeExcel.php';
}

?>