<?php
/****************************************************************** 
**创建者：sakura
**创建时间：2014-10-08
**本页：坏账明细
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_APPLICATION_6');
$isUserSC=$Admin->CheckPopedom('SC_MEMBER');
$isP2PSC=$Admin->CheckPopedom('SC_LOAN_APP');

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$type  = $FLib->RequestInt('type',1,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = '';
$outexcel = $FLib->RequestChar('outexcel',1,9,'outexcel');

$title = '坏账明细';

$subSql1 = "SELECT *, 0 isAdmin FROM p2p_repayment WHERE I_operation<>2 and I_bad_status=1 and status=1";
$subSql2 = "SELECT *, 1 isAdmin FROM p2p_repayment_admin where I_bad_status=1 and status=1";
$unionSql = "(".$subSql1." UNION ".$subSql2.")";

$mWhere='';
$table = "{$unionSql} a left join p2p_application b on b.ID=a.I_applicationID 
left join user_base u on u.ID=a.I_repayID
left join p2p_application c on c.ID=b.I_classId
left join p2p_application_subclass sb on sb.ID=b.I_subclassId
WHERE 1=1 {$mWhere}";
$sql = 'select a.*,b.Vc_title, c.Vc_name cname, sb.Vc_name sbname , u.Vc_nickname,u.Vc_truename,u.Vc_mobile ,u.ID as I_userID
		from '.$table;
$sqlcount ="select COUNT(*) from {$table}";
$sqlwmtime = $sql." and left(a.Createtime,7)='explodeExcelTy2Mtime' ";
$sqlmtimecount = "select left(a.Createtime,7) as mtime from {$table} group by left(a.Createtime,7)";

if(!$outexcel){
	
	$pagesize = $Config->AdminPageSize;
	$pagecount = 1;$rscount=0;
	$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);
	
	$points = array('统计管理', '项目统计', $title );
	$extend = array();
	$extend['js'] = '
	<script type="text/javascript">
	$(function(){
		$(".buta_excel").live("click",function(){
			window.location.href="./BadStatInfo.php?outexcel=true";
		});
	})
	</script>
	';
	$btns = array('
	<a name="submit" class="buta_excel" style="display:inline-block;float:none;"/>导出excel</a>
	');
	
	$ths =$tds=$thsl =$tdsl = array();
	$thsl[]=array('val'=>'借款用户名','wid'=>'') ;
	$ths[]=array('val'=>'真实名称','wid'=>'') ;
	$ths[]=array('val'=>'手机号码','wid'=>'') ;
	$ths[]=array('val'=>'项目名称','wid'=>'') ;
	$ths[]=array('val'=>'标类型','wid'=>'') ;
	$ths[]=array('val'=>'子分类','wid'=>'') ;
	$ths[]=array('val'=>'期数','wid'=>'') ;
	$ths[]=array('val'=>'应还时间','wid'=>'100px') ;
	$ths[]=array('val'=>'应还金额','wid'=>'') ;
	$ths[]=array('val'=>'应还本金','wid'=>'') ;
	$ths[]=array('val'=>'应还利息','wid'=>'') ;
	$ths[]=array('val'=>'逾期罚息','wid'=>'') ;
	$ths[]=array('val'=>'代偿状态','wid'=>'') ;
	$ths[]=array('val'=>'用户还款状态','wid'=>'') ;
	if(is_array($Rs)){
		$pagecount = $Rs[0]['pagecount'];
		$rscount = $Rs[0]['rscount'];
		$extend['rscount']=$rscount;
		for($i=1;$i<count($Rs);$i++){
			$_td='';
			if($isUserSC)$_td .= '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['I_userID'].'" class="hs" h="" title="【'.$Rs[$i]['Vc_nickname'].'】会员详细页">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</a></td>';
			else $_td .= '<td title="'.$Rs[$i]['Vc_nickname'].'">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</td>';
			$tdsl[$Rs[$i]['ID']]=$_td;
			$_td = '<td>'. $Rs[$i]['Vc_truename'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['Vc_mobile'] .'</td>';
			if($isP2PSC)$_td .= '<td><a href="../p2p/ApplicationInfo.php?Id='.$Rs[$i]['I_applicationID'].'" class="hs" w="750" h="700" title="【'.$Rs[$i]['Vc_title'].'】详细页">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</a></td>';
			else $_td .= '<td title="'.$Rs[$i]['Vc_title'].'">'. $FLib->cutstr($Rs[$i]['Vc_title'],40) .'</td>';
			$_td .= '<td>'.$Rs[$i]['cname'].'</td>' ;
			$_td .= '<td>'.$Rs[$i]['sbname'].'</td>' ;
			$_td .= '<td>'.$Rs[$i]['I_sort'].'/'.$Rs[$i]['I_sorts'].'</td>' ;
			$_td .= '<td>'.$FLib->fromatdate($Rs[$i]['Dt_repay'],'Y-m-d').'</td>' ;
			$_td .= '<td>'.$Rs[$i]['N_amount'].'</td>' ;
			$_td .= '<td>'.$Rs[$i]['N_capital'].'</td>' ;
			$_td .= '<td>'.$Rs[$i]['N_interest'].'</td>' ;
			$_td .= '<td>'.$Rs[$i]['N_fee'].'</td>' ;
			$_td .= '<td>'.($Rs[$i]['isAdmin']==1?'已代偿':'未代偿').'</td>' ;
			$_td .= '<td>'.getInfoByDDIC('p2p_repayment.I_operation',$Rs[$i]['I_operation']).'</td>' ;
			$tds[$Rs[$i]['ID']]=$_td;
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
	$dat['rs']=0;//$Rs = $DataBase->fetch_all($sql);
	$dat['rscount']=iset($DataBase->fetch_val($sqlcount),0);
	$dat['filename']=$title;
	$dat['fields']=array(
			array('借款用户名','Vc_nickname'),
			array('真实名称','Vc_truename'),
			array('手机号码','Vc_mobile'),
			array('项目名称','Vc_title'),
			array('标类型','cname'),
			array('子分类','sbname'),
			array('期数','$Rs[$i][\'I_sort\'].\'/\'.$Rs[$i][\'I_sorts\']','other'),
			array('应还时间','Dt_repay','date'),
			array('应还金额','N_amount'),
			array('应还本金','N_capital'),
			array('应还利息','N_interest') ,
			array('逾期罚息','N_fee') ,
			array('代偿状态','$Rs[$i][\'isAdmin\']==1?"已代偿":"未代偿"','other'),
			array('用户还款状态','getInfoByDDIC(\'p2p_repayment.I_operation\',$Rs[$i][\'I_operation\'])','other'),
	);
	
	include WEBROOTINC.'ExplodeExcel.php';
}

?>