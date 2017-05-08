<?php
/****************************************************************** 
**创建者：sakura
**创建时间：2014-10-08
**本页：实名认证明细
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_OTHER_5');
$isUserSC=$Admin->CheckPopedom('SC_MEMBER');

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$type  = $FLib->RequestInt('type',1,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = '';
$outexcel = $FLib->RequestChar('outexcel',1,9,'outexcel');

$title = '使用免提现额度明细';

$mWhere='';
$table = 'p2p_withdraw_log a left join user_base b on a.user_id=b.ID where b.status=1 and a.Status=1 '.$mWhere;
$sql = 'select a.*,b.Vc_nickname,b.Vc_mobile,b.Vc_Email
		from '.$table.' order by a.ID desc';

//(select sum(z.Free_limit) from p2p_withdraw_log z where a.Status=1 and a.user_id=z.user_id) Free_limits
if(!$outexcel){
	
	$sqlcount ="select COUNT(*) from {$table}";
	$pagesize = $Config->AdminPageSize;
	$pagecount = 1;$rscount=0;
	$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);
	
	$points = array('统计管理', '其他', $title );
	$extend = array();
	$extend['js'] = '
	<script type="text/javascript">
	$(function(){
		$(".buta_excel").live("click",function(){
			window.location.href="/admin/stat/UserWithdrawStatInfo.php?outexcel=true";
		});
	})
	</script>
	';
	$btns = array('
	<a name="submit" class="buta_excel" style="display:inline-block;float:none;"/>导出excel</a>
	');
	
	$ths =$tds = array();
	$ths[]=array('val'=>'用户名', 'wid'=>'');
	$ths[]=array('val'=>'邮箱', 'wid'=>'');
	$ths[]=array('val'=>'账户类型', 'wid'=>'');
	$ths[]=array('val'=>'提现金额', 'wid'=>'');
	$ths[]=array('val'=>'使用免提现额度', 'wid'=>'');
	//$ths[]=array('val'=>'共使用免提现额度', 'wid'=>'');
	$ths[]=array('val'=>'提现时间', 'wid'=>'');
	if(is_array($Rs)){
		$pagecount = $Rs[0]['pagecount'];
		$rscount = $Rs[0]['rscount'];
		$extend['rscount']=$rscount;
		$totalamount = 0;
		for($i=1;$i<count($Rs);$i++){
			$_td='';
			if($isUserSC)$_td .= '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['User_id'].'" class="hs" h="" title="【'.$Rs[$i]['Vc_nickname'].'】会员详细页">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</a></td>';
			else $_td .= '<td title="'.$Rs[$i]['Vc_nickname'].'">'.$FLib->cutstr($Rs[$i]['Vc_nickname'],40).'</td>';
			$_td .= '<td>'. $Rs[$i]['Vc_Email'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['Free_limit'] .'</td>';
			//$_td .= '<td>'. $Rs[$i]['Free_limits'] .'</td>';
			$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d H:i:s') .'</td>';
			$tds[$Rs[$i]['ID']]=$_td;
			$totalamount+=iset($Rs[$i]['Free_limit'],0);
		}
		$extend['sumtext']='，使用免提现额度共 [<font class="green">'.number_format($totalamount,2,'.','').'</font>] 元';
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
	$Rs  = $DataBase->fetch_all($sql);
	$dat['rs']=$Rs;
	$dat['rscount']=count($Rs);
	$dat['filename']=$title;
	$dat['fields']=array(
			array('用户名','Vc_nickname'),
			array('邮箱','Vc_Email'),
			array('账户类型','($Rs[$i][\'I_company\']>0?\'企业\':\'个人\')','other'),
			array('提现金额','N_amount'),
			array('使用免提现额度','Free_limit'),
			//array('共使用免提现额度','Free_limits'),
			array('提现时间','Createtime','datetime'),
	);
	
	include WEBROOTINC.'ExplodeExcel.php';
}

?>
