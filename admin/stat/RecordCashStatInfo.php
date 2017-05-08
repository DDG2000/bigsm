<?php
/****************************************************************** 
**创建者：sakura
**创建时间：2014-10-08
**本页：资金记录明细
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_RECORDCASH');
$isUserSC=$Admin->CheckPopedom('SC_MEMBER');
$isP2PSC=$Admin->CheckPopedom('SC_LOAN_APP');

$files = WEBROOTDATA.'appclass.cache.inc.php';
if(file_exists($files)){require($files);}else{errorPage('借款分类未生成');}
	
$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$type  = $FLib->RequestChar('type','',9,'类型');
$reason  = $FLib->RequestInt('reason',0,9,'类型');
$starttime = $FLib->RequestChar('starttime','',19,'时间',1);
$endtime = $FLib->RequestChar('endtime','',19,'时间',1);
$class= $FLib->RequestInt('class',0,9,'类型');
$subClass= $FLib->RequestInt('subClass',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType."&reason=" . $reason ."&type=" . $type 
	.($class?"&class=".$class:'').($subClass?"&subClass=".$subClass:'')
	.($starttime?"&starttime=".$starttime:'').($endtime?"&endtime=".$endtime:'') ;;
$outexcel = $FLib->RequestChar('outexcel',1,9,'outexcel');

$title='';
$reasonsArr=getInfoByDDIC('p2p_record_cash.Vc_reason','all');
if($type==1){
	$title='投资人';
	$reasons=getInfoByDDIC('p2p_record_cash.Vc_reason','invest');
}elseif($type==2){
	$title='借款人';
	$reasons=getInfoByDDIC('p2p_record_cash.Vc_reason','loan');
}else{
	$reasons=$reasonsArr;
}

$title = $title.'资金使用记录明细';
if($sKey)$mWhere .= " and c.Vc_nickname like '%" . $sKey . "%'";

if($reason){
	$reasonStr = $reasonsArr[$reason];
	$mWhere .= " and a.Vc_reason like '{$reasonStr}'";
}
if($class)$mWhere .= " and d.I_classId = $class " ;
if($subClass)$mWhere .= " and d.I_subclassId = $subClass " ;
if($starttime)$mWhere .= " and a.Createtime >= '{$starttime}' ";
if($endtime)$mWhere .= " and a.Createtime <= '{$endtime}' ";
$reasonsAll = "'".implode("','",$reasons)."'";
if($type>0){
	$mWhere.= ' and a.Vc_reason in('.$reasonsAll.')';
}
$tables = 'p2p_record_cash a 
			left join user_base c on c.ID=a.I_userID
			left join p2p_application d on d.ID=a.I_applicationID
			left join p2p_application_class cc on d.I_classID=cc.ID
			left join p2p_application_subclass sb on d.I_subclassId=sb.ID
		where a.Status=1 and c.ID>999'.$mWhere;
$sql = "select a.*,c.Vc_nickname ,c.Vc_truename,c.Vc_mobile,c.Vc_Email,
d.Vc_title,cc.Vc_name cname,sb.Vc_name sbname FROM {$tables} ";
$sqlorder = " order by a.Createtime desc,a.ID desc";
$sqlcount ="select COUNT(*) from {$tables}";
$sqlwmtime = $sql." and left(a.Createtime,7)='explodeExcelTy2Mtime' ".$sqlorder;
$sqlmtimecount = "select left(a.Createtime,7) as mtime from {$tables} group by left(a.Createtime,7)";
$sql.=$sqlorder;

if(!$outexcel){
	$pagesize = $Config->AdminPageSize;
	$pagecount = 1;$rscount=0;
	$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);
	
	$points = array('统计管理', '用户资金使用记录', $title );
	$extend = array();
	$extend['js'] = '
	<link type="text/css" rel="stylesheet" href="../include/timepicker/css/jquery-ui.css" >
	<script type="text/javascript" src="../js/form.checkfun.js"></script>
	<script type="text/javascript" src="../include/timepicker/js/jquery-ui.js"></script>
	<script type="text/javascript" src="../include/timepicker/js/jquery-ui-slide.min.js"></script>
	<script type="text/javascript" src="../include/timepicker/js/jquery-ui-timepicker-addon.js"></script>
	<script type="text/javascript">
	$(function(){'.($class==1?'$(".subClasscheck").show();':'$(".subClasscheck").hide();').'
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
			window.location.href="/admin/stat/RecordCashStatInfo.php?outexcel=true'.$UrlInfo.'";
		});
		$("input[name=\'starttime\']").val("'.$starttime.'");
		$("input[name=\'endtime\']").val("'.$endtime.'");
			
		$("select[name=class]").change(function(){
			if($(this).val()==1){
				$(".subClasscheck").show();
			}else{
				$(".subClasscheck").hide();
				$("select[name=subClass]").val(0);
			}
		});
	})
	</script>';
	$classsStr = '<select name="class" class="sel_put1 chzn-select-no-single"><option value="">所有</option>';
	foreach ($da_appclass as $k=>$v){
		if($class==$k){
			$classsStr.='<option selected="selected" value="'.$k.'">'.$v['Vc_name'].'</option>';
		}else{
			$classsStr.='<option value="'.$k.'">'.$v['Vc_name'].'</option>';
		}
	}
	$classsStr .= '</select>';
	$subClasss = $Db->fetch_all("select * from p2p_application_subclass where Status=1");
	$subClasssStr = '<select name="subClass" class="sel_put1 chzn-select-no-single"><option value="">所有</option>';
	foreach ($subClasss as $v){
		if($subClass==$v['ID']){
			$subClasssStr.='<option selected="selected" value="'.$v['ID'].'">'.$v['Vc_name'].'</option>';
		}else{
			$subClasssStr.='<option value="'.$v['ID'].'">'.$v['Vc_name'].'</option>';
		}
	}
	$subClasssStr .= '</select>';
	$typesStr='<option value="all">所有</option>
			<option value="1" '.($type==1?'selected="selected"':'').'>投资人</option>
			<option value="2" '.($type==2?'selected="selected"':'').'>借款人</option>';
	$reasonsStr='<option value="">所有</option>';
	foreach ($reasonsArr as $k=>$v){
		if($reason==$k){
			$reasonsStr.='<option selected="selected" value="'.$k.'">'.$v.'</option>';
		}else{
			$reasonsStr.='<option value="'.$k.'">'.$v.'</option>';
		}
	}
	$extend['sTypes'] = array('<span class="spancheck reasoncheck">用户类型：<select name="type" class="sel_put1 chzn-select-no-single">'.
			$typesStr.'</select></span>'
			,'<span class="spancheck defaultcheck">用户名：<input name="sKey" type="text" class="txt_put1" value="'.$sKey.'" maxlength="50"></span>'
			,'<span class="spancheck classcheck">标类型：'.$classsStr.'</span>'
			,'<span class="spancheck subClasscheck">子分类：'.$subClasssStr.'</span>'
			,'<br><br><span class="spancheck reasoncheck">资金类型：<select name="reason" class="sel_put1 chzn-select-no-single" style="width:130px">'.
			$reasonsStr.'</select></span>'
			,'<span class="spancheck timecheck">使用时间：<input class="txt_put_datetime" name="starttime" value="'.$starttime.'" data-time="" onclick="nodatepatFun" tp="开始时间" maxlength="19"/>
			- <input class="txt_put_datetime" name="endtime" value="'.$endtime.'" data-time="" onclick="nodatepatFun" tp="结束时间" maxlength="19"/></span>');
	$btns = array('
	<a name="submit" class="buta_excel" style="display:inline-block;float:none;"/>导出excel</a>
	');
	
	$thsl=$tdsl=$ths =$tds = array();
	$thsl[]=array('val'=>'ID', 'wid'=>'');
	$thsl[]=array('val'=>'用户名', 'wid'=>'');
	$ths[]=array('val'=>'真实姓名', 'wid'=>'');
	//$ths[]=array('val'=>'联系电话', 'wid'=>'');
	//$ths[]=array('val'=>'邮箱', 'wid'=>'');
	$ths[]=array('val'=>'项目ID', 'wid'=>'');
	$ths[]=array('val'=>'项目名称', 'wid'=>'');
	$ths[]=array('val'=>'标类型', 'wid'=>'');
	$ths[]=array('val'=>'子分类', 'wid'=>'');
	$ths[]=array('val'=>'交易类型', 'wid'=>'');
	$ths[]=array('val'=>'操作金额', 'wid'=>'');
	$ths[]=array('val'=>'账户余额', 'wid'=>'');
	$ths[]=array('val'=>'可用金额', 'wid'=>'');
	$ths[]=array('val'=>'冻结金额', 'wid'=>'');
	$ths[]=array('val'=>'待收金额', 'wid'=>'');
	$ths[]=array('val'=>'待还金额', 'wid'=>'');
	$ths[]=array('val'=>'账户总额', 'wid'=>'');
	$ths[]=array('val'=>'备注', 'wid'=>'');
	$ths[]=array('val'=>'时间', 'wid'=>'');
	if(is_array($Rs)){
		$pagecount = $Rs[0]['pagecount'];
		$rscount = $Rs[0]['rscount'];
		$extend['rscount']=$rscount;
		for($i=1;$i<count($Rs);$i++){
			$_td = '<td>'. $Rs[$i]['ID'] .'</td>';
			if($isUserSC)$_td .= '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['I_userID'].'" class="hs" h="" title="【'.$Rs[$i]['Vc_nickname'].'】会员详细页">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</a></td>';
			else $_td .= '<td title="'.$Rs[$i]['Vc_nickname'].'">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</td>';
			$tdsl[$Rs[$i]['ID']]=$_td;
			$_td = '<td>'. $Rs[$i]['Vc_truename'] .'</td>';
			//$_td .= '<td>'. $Rs[$i]['Vc_mobile'] .'</td>';
			//$_td .= '<td>'. $Rs[$i]['Vc_Email'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['I_applicationID'] .'</td>';
			if($isP2PSC)$_td .= '<td><a href="../p2p/ApplicationInfo.php?Id='.$Rs[$i]['I_applicationID'].'" class="hs" w="750" h="700" title="【'.$Rs[$i]['Vc_title'].'】详细页">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</a></td>';
			else $_td .= '<td title="'.$Rs[$i]['Vc_title'].'">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</td>';
			$_td .= '<td>'. $Rs[$i]['cname'].'</td>';
			$_td .= '<td>'. $Rs[$i]['sbname'].'</td>';
			$_td .= '<td>'. getInfoByDDIC('p2p_record_cash.I_type',$Rs[$i]['I_type']) .'</td>';
			$_td .= '<td>'. ($Rs[$i]['N_income']>0?$Rs[$i]['N_income']:$Rs[$i]['N_expend']) .'</td>';
			$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
			$_td .= '<td>'. number_format($Rs[$i]['N_amount']-$Rs[$i]['N_amount_freeze'],2,'.','').'</td>';
			$_td .= '<td>'. $Rs[$i]['N_amount_freeze'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['N_amount_repossessed'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['N_amount_revert'] .'</td>';
			$_td .= '<td>'. number_format($Rs[$i]['N_amount']+$Rs[$i]['N_amount_repossessed'],2,'.','').'</td>';
			$_td .= '<td>'. $Rs[$i]['Vc_reason'] .'</td>';
			$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d H:i:s') .'</td>';
			$tds[$Rs[$i]['ID']]=$_td;
		}
	}
	
	$DataBase->CloseDataBase();
	$extend['fan'] = false;
	$helps  = array("可用余额=账户余额-冻结金额","账户总额=可用余额+冻结金额+待收总金额   ");
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
			//array('联系电话','Vc_mobile'),
			//array('邮箱','Vc_Email'),
			array('项目ID','I_applicationID'),
			array('项目名称','Vc_title'),
			array('标类型', 'cname'),
			array('子分类', 'sbname'),
			array('交易类型','getInfoByDDIC(\'p2p_record_cash.I_type\',$Rs[$i][\'I_type\'])','other'),
			array('操作金额','$Rs[$i][\'N_income\']>0?$Rs[$i][\'N_income\']:($Rs[$i][\'N_expend\'])','other'),
			array('账户余额','N_amount'),
			array('可用金额','$Rs[$i][\'N_amount\']-$Rs[$i][\'N_amount_freeze\']','other'),
			array('冻结金额','N_amount_freeze'),
			array('待收金额','N_amount_repossessed'),
			array('待还金额','N_amount_revert'),
			array('账户总额','$Rs[$i][\'N_amount\']+$Rs[$i][\'N_amount_repossessed\']','other'),
			array('备注','Vc_reason'),
			array('时间','Createtime','datetime')
	);
	
	include_once WEBROOTINC.'ExplodeExcel.php';
}

?>