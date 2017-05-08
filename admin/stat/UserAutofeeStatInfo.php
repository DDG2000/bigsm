<?php
/****************************************************************** 
**创建者：sakura
**创建时间：2014-10-08
**本页：实名认证明细
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_OTHER_4');
$isUserSC=$Admin->CheckPopedom('SC_MEMBER');

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$type  = $FLib->RequestInt('type',1,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = '';
$outexcel = $FLib->RequestChar('outexcel',1,9,'outexcel');

$title = '实名认证明细';

$mWhere='';
$table = 'user_autofee a left join user_base b on a.userid=b.ID where b.status=1 '.$mWhere;
$sql = 'select a.*,b.Vc_nickname,b.Vc_mobile,b.Vc_Email from '.$table;
$sqlcount ="select COUNT(*) from {$table}";
$sqlwmtime = $sql." and left(a.Createtime,7)='explodeExcelTy2Mtime' ";
$sqlmtimecount = "select left(a.Createtime,7) as mtime from {$table} group by left(a.Createtime,7)";

if(!$outexcel){
	
	$pagesize = $Config->AdminPageSize;
	$pagecount = 1;$rscount=0;
	$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);
	
	$points = array('统计管理', '其他', $title );
	$extend = array();
	$extend['js'] = '
	<script type="text/javascript">
	$(function(){
		$(".buta_excel").live("click",function(){
			window.location.href="/admin/stat/UserAutofeeStatInfo.php?outexcel=true";
		});
	})
	</script>
	';
	$btns = array('
	<a name="submit" class="buta_excel" style="display:inline-block;float:none;"/>导出excel</a>
	');
	
	$ths =$tds = array();
	$ths[]=array('val'=>'用户名', 'wid'=>'');
	$ths[]=array('val'=>'联系电话', 'wid'=>'');
	$ths[]=array('val'=>'邮箱', 'wid'=>'');
	$ths[]=array('val'=>'认证名称', 'wid'=>'');
	$ths[]=array('val'=>'认证号码', 'wid'=>'');
	$ths[]=array('val'=>'认证金额', 'wid'=>'');
	$ths[]=array('val'=>'认证时间', 'wid'=>'');
	$ths[]=array('val'=>'状态', 'wid'=>'');
	if(is_array($Rs)){
		$pagecount = $Rs[0]['pagecount'];
		$rscount = $Rs[0]['rscount'];
		$extend['rscount']=$rscount;
		for($i=1;$i<count($Rs);$i++){
			$_td='';
			if($isUserSC)$_td .= '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['userid'].'" class="hs" h="" title="【'.$Rs[$i]['Vc_nickname'].'】会员详细页">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</a></td>';
			else $_td .= '<td title="'.$Rs[$i]['Vc_nickname'].'">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</td>';
			$_td .= '<td>'. $Rs[$i]['Vc_mobile'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['Vc_Email'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['Vc_truename'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['Vc_identity'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['AuthFee'] .'</td>';
			$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d H:i:s') .'</td>';
			$_td .= '<td>'. ($Rs[$i]['AuthState']==1?"认证通过":($Rs[$i]['AuthState']==2?"已认证":"认证失败")) .'</td>';
			$tds[$Rs[$i]['id']]=$_td;
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
	$dat['fields']=array(
			array('用户名','Vc_nickname'),
			array('联系电话','Vc_mobile'),
			array('邮箱','Vc_Email'),
			array('认证名称','Vc_truename'),
			array('认证号码','Vc_identity'),
			array('认证金额','AuthFee'),
			array('认证时间','Createtime','datetime'),
			array('状态','$Rs[$i][\'AuthState\']==1?"认证通过":($Rs[$i][\'AuthState\']==2?"已认证":"认证失败")','other')
	);
	
	include WEBROOTINC.'ExplodeExcel.php';
}

?>