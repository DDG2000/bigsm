<?php
/****************************************************************** 
**创建者：sakura
**创建时间：2014-10-08
**本页：自动投标明细
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_OTHER_3');;
require(WEBROOTDATA.'appclass.cache.inc.php');
$isUserSC=$Admin->CheckPopedom('SC_MEMBER');
$isP2PSC=$Admin->CheckPopedom('SC_LOAN_APP');

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
if(!$sKey){
	$id = $FLib->RequestInt('id',0,9,'参数',1);
	if($id){
		$sKey=$FLib->RequestChar('name',1,50,'参数',1);
	}
}
$starttime = $FLib->RequestChar('starttime','',10,'时间',1);
$endtime = $FLib->RequestChar('endtime','',10,'时间',1);
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType.
	"&id=".$id."&starttime=" . $starttime."&endtime=".$endtime;
$outexcel = $FLib->RequestChar('outexcel',1,9,'outexcel');

$title = '自动投标统计明细';
$mWhere='';
if($id) $mWhere.=" and a.I_userID={$id}";
if ($sKey)$mWhere .= " and u.Vc_nickname like '%{$sKey}%'";
if($starttime)$mWhere .= " and  a.Createtime >= '".$starttime." 00:00:00'";
if($endtime)$mWhere .= " and  a.Createtime <= '".$endtime." 23:23:59'";

$tables = "p2p_bid a left join p2p_application b on a.I_applicationID=b.ID
left join p2p_application_class c on c.ID=b.I_classID
left join p2p_application_subclass sb on sb.ID=b.I_subclassID
left join user_base u on u.ID=a.I_userID 
where a.Status=1 and a.I_type=2 {$mWhere} and u.ID>999";
$sql = "select a.*,b.Vc_title,c.Vc_name cname,sb.Vc_name sbname,u.Vc_Email,u.Vc_nickname,u.Vc_truename,u.Vc_mobile FROM {$tables}";
$sqlcount ="select COUNT(*) from {$tables}";
$sqlwmtime = $sql." and left(a.Createtime,7)='explodeExcelTy2Mtime' ";
$sqlmtimecount = "select left(a.Createtime,7) as mtime from {$tables} group by left(a.Createtime,7)";

if(!$outexcel){
	
	$pagesize = $Config->AdminPageSize;
	$pagecount = 1;$rscount=0;
	$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$points = array('统计管理', '其他', $title );
$extend = array();
$extend['hide'] = array('id'=>$id);
$extend['js'] = '
	<link type="text/css" rel="stylesheet" href="../include/timepicker/css/jquery-ui.css" >
	<script type="text/javascript" src="../js/form.checkfun.js"></script>
	<script type="text/javascript" src="../include/timepicker/js/jquery-ui.js"></script>
	<script type="text/javascript" src="../include/timepicker/js/jquery-ui-slide.min.js"></script>
	<script type="text/javascript" src="../include/timepicker/js/jquery-ui-timepicker-addon.js"></script>
	<script type="text/javascript">
	$(function(){
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
		$(".buta_excel").live("click",function(){
			window.location.href="/admin/stat/AutobidStatInfo.php?outexcel=true'.$UrlInfo.'";
		});
	})
	</script>
	';
$extend['sTypes'] = array('<span class="spancheck defaultcheck">用户名：<input name="sKey" type="text" class="txt_put1" value="'.$sKey.'" maxlength="50"></span>'
		,'<span class="spancheck timecheck">投资时间：<input class="txt_put_date" name="starttime" value="'.$starttime.'" data-time="" onclick="nodatepatFun" tp="开始时间" maxlength="10"/>
			- <input class="txt_put_date" name="endtime" value="'.$endtime.'" data-time="" onclick="nodatepatFun" tp="结束时间" maxlength="10"/></span>');

$btns = array('<a href="AutobidStat.php" name="change" class="buta" style="display:inline-block;float:right;"/>按人统计显示</a>',
	'<a name="submit" class="buta_excel" style="display:inline-block;float:none;"/>导出excel</a>'
	//'<a name="submit" class="history_back" href="'.$FLib->IsRequest('backurl').'"/>返回</a>',
	);

$ths=$tds = array();
$ths[]=array('val'=>'用户名', 'wid'=>'');
$ths[]=array('val'=>'真实名称','wid'=>'') ;
$ths[]=array('val'=>'手机号码','wid'=>'') ;
$ths[]=array('val'=>'邮箱','wid'=>'') ;
$ths[]=array('val'=>'投资项目', 'wid'=>'');
$ths[]=array('val'=>'标类型', 'wid'=>'');
$ths[]=array('val'=>'子分类', 'wid'=>'');
$ths[]=array('val'=>'投资金额', 'wid'=>'');
$ths[]=array('val'=>'投资时间', 'wid'=>'');
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
		$_td .= '<td>'. $Rs[$i]['Vc_mobile'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['Vc_Email'] .'</td>';
		if($isP2PSC)$_td .= '<td><a href="../p2p/ApplicationInfo.php?Id='.$Rs[$i]['I_applicationID'].'" class="hs" w="750" h="700" title="【'.$Rs[$i]['Vc_title'].'】详细页">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</a></td>';
		else $_td .= '<td title="'.$Rs[$i]['Vc_title'].'">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</td>';
		$_td .= '<td>'. $Rs[$i]['cname'].'</td>';
		$_td .= '<td>'. $Rs[$i]['sbname'].'</td>';
		$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d H:i:s') .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_deal']==1?"已处理":"未处理").'</td>';
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
			array('用户名','Vc_nickname'),
			array('真实姓名','Vc_truename'),
			array('手机号码','Vc_mobile'),
			array('邮箱','Vc_Email'),
			array('投资项目','Vc_title'),
			array('标类型','cname'),
			array('子分类','sbname'),
			array('投资金额','N_amount'),
			array('投资时间','Createtime','datetime'),
			array('状态','$Rs[$i][\'I_deal\']==1?"已处理":"未处理"','other'),
	);

	include_once WEBROOTINC.'ExplodeExcel.php';
}
?>