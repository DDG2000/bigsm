<?php
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 会员 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER');
require(WEBROOTDATA.'userclass.cache.inc.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'搜索关键字',1);
$sType  = $FLib->RequestInt('sType',0,9,'搜索关键字类型');
$type     = $FLib->RequestInt('type',1,9,'查询类型');
$nulltrue  = $FLib->RequestInt('nulltrue',0,9,'为空搜索');
$starttime  = $FLib->RequestChar('starttime',1,19,'开始时间');
$endtime  = $FLib->RequestChar('endtime',1,19,'结束时间');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType ."&type=" . $type .
	($startnum?"&startnum=".$startnum:'').($endnum?"&endnum=".$endnum:'').
	($starttime?"&starttime=".$starttime:'').($endtime?"&endtime=".$endtime:'').
	($nulltrue?"&nulltrue=".$nulltrue:'').($freelimitstatus?"&freelimitstatus=".$freelimitstatus:'').
	($nulltrue?"&nulltrue=".$nulltrue:'');
$outexcel = $FLib->RequestChar('outexcel',1,9,'outexcel');

$title ='会员';

$sqlw = ' and uu.ID>999';
//搜索关键字类型
switch ($sType){
	case 1:
		if($nulltrue)
			$sqlw .= " and uu.Vc_truename is null";
		else if($sKey!='')
			$sqlw .= " and uu.Vc_truename like '%" . $sKey . "%'";
		break;
	case 2:
		if($nulltrue)
			$sqlw .= " and uu.Vc_mobile is null";
		else if($sKey!='')
			$sqlw .= " and uu.Vc_mobile like '%" . $sKey . "%'";
		break;
	case 3:
		if($sKey!='')
			$sqlw .= " and uu.Vc_Email like '%" . $sKey . "%'";
		break;
	case 4:
		if($sKey!='')
			$sqlw .= " and sc.Vc_name  like '%" . $sKey . "%'";
		break;
	case 4:
		if($sKey!='')
			$sqlw .= " and ss.Vc_name  like '%" . $sKey . "%'";
		break;
}
if($audit>-1){
	$sqlw .= ' and uu.I_audit='.$audit;
}
if($freelimitstatus==1){
	$sqlw .= ' and uu.I_allowance_free=1';
}
if($starttime)$sqlw .= " and uu.Createtime >= '".$starttime."'";
if($endtime)$sqlw .= " and uu.Createtime <= '".$endtime."'";
$tables = 'user_base uu left join sm_company sc on uu.I_companyID=sc.id left join sm_shop ss on uu.ID=ss.I_userID where uu.Status>0 '.$sqlw.'';
$sql = "SELECT uu.*,sc.Vc_name as companyname,ss.Vc_name as shopname FROM {$tables}";
$sqlorder = " order by uu.ID desc";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$sqlwmtime = $sql." and left(uu.Createtime,7)='explodeExcelTy2Mtime' ".$sqlorder;
$sqlmtimecount = "select left(uu.Createtime,7) as mtime from {$tables} group by left(uu.Createtime,7)";
$sql .= $sqlorder;
$btime = time()-86400;
if(!$outexcel){
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);
$points = array('会员管理', $title.'列表' );
$sTypes = array('','真实姓名','手机号码','邮箱地址','企业名','商铺名');
$hides  = array('type'=>$type);
$extend = array();
$extend['js']='
<link type="text/css" rel="stylesheet" href="../include/timepicker/css/jquery-ui.css">
<script src="../js/form.checkfun.js" type="text/javascript"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-slide.min.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">
$(function(){'.($nulltrue?'$("input[name=\'sKey\']").attr("disabled","disabled");':'').
($sType<5&&$sType>2?'':'$(".nullcheck").hide();').
'  $("select[name=\'sType\']").live(\'change\',function(){
		$(".spancheck input").val("");
		if($(this).val()>1){
			$(".nullcheck").show();
		}else{
			$(".nullcheck").hide();
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
	$("input[name=\'starttime\']").datetimepicker({
		changeYear:true,
		yearRange:"1900:'.date('Y').'",
		showSecond: true,
 		timeFormat: "hh:mm:ss",
		maxDate:0,
		onSelect : function(selectedDate) {
			$("input[name=\'endtime\']").datetimepicker("option", "minDate", selectedDate);
		}
	});
	$("input[name=\'endtime\']").datetimepicker({
		changeYear:true,
		yearRange:"1900:'.date('Y').'",
		showSecond: true,
 		timeFormat: "hh:mm:ss",
		maxDate:1,
		hour: 23,
		minute: 59,
		second: 59,
		onSelect : function(selectedDate) {
			$("input[name=\'starttime\']").datetimepicker("option", "maxDate", selectedDate);
		}
	});
	'.($starttime?'$("input[name=\'endtime\']").datetimepicker("option", "minDate", "'.$starttime.'");':'').'
	$(".buta_excel").live("click",function(){
		window.location.href="./MemberList.php?outexcel=true'.$UrlInfo.'";
	});
	$("input[name=\'starttime\']").val("'.$starttime.'");
	$("input[name=\'endtime\']").val("'.$endtime.'");
})
</script>
';
//$classStr='<option value="">所有</option>';
//foreach ($da_userclass as $k=>$v){
//	if($typeclass==$k){
//		$classStr.='<option selected="selected" value="'.$k.'">'.$v['Vc_name'].'</option>';
//	}else{
//		$classStr.='<option value="'.$k.'">'.$v['Vc_name'].'</option>';
//	}
//}
//$auditStr='<option value="">所有</option>'.
//'<option value="0" '.($audit==0?'selected="selected"':'').'>审核中</option>'.
//'<option value="1" '.($audit==1?'selected="selected"':'').'>已通过</option>'.
//'<option value="2" '.($audit==2?'selected="selected"':'').'>未通过</option>';
$extend['sTypes'] = array('<span class="spancheck nullcheck"><input type="checkbox" id="nulltruecheckbox" value="1" '.($nulltrue?'checked="checked"':'').'>为空搜索
		<input type="hidden" name="nulltrue" id="nulltruehidden" value="0"</span>'
		, '<span class=" timecheck">注册时间：<input class="txt_put_datetime" name="starttime" value="'.$starttime.'" data-time="" onclick="nodatepatFun" tp="开始时间" maxlength="19"/>
		- <input class="txt_put_datetime" name="endtime" value="'.$endtime.'" data-time="" onclick="nodatepatFun" tp="结束时间" maxlength="19"/></span>');

	$thsl=$ths=$tdsl=$tds = array();	
	$thsl[]=array('val'=>'会员ID', 'wid'=>'');
	$ths[]=array('val'=>'真实姓名', 'wid'=>'');
	$ths[]=array('val'=>'手机', 'wid'=>'');
	$ths[]=array('val'=>'邮箱', 'wid'=>'');
	$ths[]=array('val'=>'企业名', 'wid'=>'');
	$ths[]=array('val'=>'商铺名', 'wid'=>'');
	$ths[]=array('val'=>'账户锁定状态', 'wid'=>'');
	$ths[]=array('val'=>'登陆锁定状态', 'wid'=>'');
	$ths[]=array('val'=>'注册时间', 'wid'=>'');
	$ths[]=array('val'=>'操作', 'wid'=>'');
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$td_btn = array();
		$_td = '<td>'.$Rs[$i]['ID'].'</td>';
		$tdsl[$Rs[$i]['ID']]=$_td;
		$_td = '';
		$_td .= '<td><a href="MemberInfo.php?Id='.$Rs[$i]['ID'].'" class="hs" h="" title="【'.$Rs[$i]['Vc_nickname'].'】会员详细页">'.$Rs[$i]['Vc_truename'].'</a></td>';
		$_td .= '<td>'. ($Rs[$i]['I_mobileauthenticate']==2? $Rs[$i]['Vc_mobile']:'未认证') .'</td>';
		$_td .= '<td title="'. ($Rs[$i]['I_Emailauthenticate']==2?$Rs[$i]['Vc_Email']:'未认证') .'">'.$Rs[$i]['Vc_Email'].'</td>';
		$_td .= '<td >'.$Rs[$i]['companyname'].'</td>';
		$_td .= '<td >'.$Rs[$i]['shopname'].'</td>';
		$_td .= '<td>'. ($Rs[$i]['Status']==1?'未锁定':($Rs[$i]['Status']==2?'<span style="color:red;">锁定</span>':'未知')) .'</td>';
		$_td .= '<td>'. ($Rs[$i]['errLoginLockTime']>$btime?'<span style="color:red;">锁定</span>':'未锁定') .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'" >'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d H:i:s') .'</td>';
//		$td_btn[] = '<a href="MemberMdy.php?Work=MdyFriend&Id='.$Rs[$i]['ID'].'" class="hs" w="900" title="通知">通知</a>';
		$td_btn[] = '<a href="MemberMessage.php?Work=SendMessage&&IdList='.$Rs[$i]['ID'].'" class="hs" h="340" title="'.$title.'通知管理">通知</a>';
//		$td_btn[] = '<a href="MemberMdyPwd.php?Work=MdyPwd&Id='.$Rs[$i]['ID'].'" class="hs" h="340" title="修改'.$title.'密码">修改密码</a>';
		$_td .= '<td style="">'.join(' | ',$td_btn).'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array();
$gbtns = array();
	$btns[] = '<a href="MemberMessage.php?Work=SendMessage&IdList=" class="del hs" rel="IdList">勾选通知/全部通知</a>';
	$btns[] = '<a href="MemberProcess.php?Work=LockReco&IdList=" class="del" rel="IdList">账户锁定</a>';
	$btns[] = '<a href="MemberProcess.php?Work=UnLockReco&IdList=" class="del" rel="IdList">账户解锁</a>';
	$btns[] = '<a href="MemberProcess.php?Work=UnLoginLockReco&IdList=" class="del" rel="IdList">解除登陆锁定</a>';
//	$btns[] = '<a href="MemberProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删除</a>';
//	$btns[] = '<a href="MemberProcess.php?Work=AuditReco&IdList=" class="del" rel="IdList">审核</a>';
//	$btns[] = '<a href="MemberProcess.php?Work=DelBadReco&IdList=" class="del" rel="IdList">移除</a>';
$extend['gbtns'] = $gbtns;
$helps  = array();
if($ty==2){$helps[] = '审核：必须会员自己通过邮箱和手机验证, 审核才可成功！';}
$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);

array_push($btns,'<a name="submit" class="buta_excel"  style="display:inline-block;float:none;"/>导出excel</a>');

$helps=array('为空搜索：手机号码、真实姓名 选中 为空搜索 即可查询此项为空的信息');
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
$tpl->assign( 'thsl', $thsl );
$tpl->assign( 'tdsl', $tdsl );
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
	$dat['rs']=0;
	$dat['rscount']=iset($DataBase->fetch_val($sqlcount),0);
	$dat['filename']=$title;
	$dat['fields']=array();
	$dat['fields'][]=array('会员ID','ID');
	$dat['fields'][]=array('真实姓名','Vc_truename');
	$dat['fields'][]=array('联系电话','Vc_mobile');
	$dat['fields'][]=array('邮箱','Vc_Email');
	$dat['fields'][]=array('身份证号','Vc_identity');
	$dat['fields'][]=array('注册时间','Createtime','datetime');
	$dat['fields'][]=array('账户锁定状态', '($Rs[$i][\'Status\']==1?\'未锁定\':($Rs[$i][\'Status\']==2?\'锁定\':\'未知\'))','other');
	$dat['fields'][]=array('登陆锁定状态', '($Rs[$i][\'errLoginLockTime\']>$btime?\'锁定\':\'未锁定\')','other');

	include_once WEBROOTINC.'ExplodeExcel.php';
}
?>