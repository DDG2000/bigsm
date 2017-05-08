<?php
/****************************************************************** 
**创建者：sakura
**创建时间：2014-10-08
**本页：自动投标明细
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_OTHER_3');
$isUserSC=$Admin->CheckPopedom('SC_MEMBER');
	
$files = WEBROOTDATA.'appclass.cache.inc.php';
if(file_exists($files)){require($files);}else{errorPage('借款分类未生成');}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType;
$outexcel = $FLib->RequestChar('outexcel',1,9,'outexcel');

$title = '自动投标统计明细';

$mWhere='';
if ($sKey)$mWhere .= " and c.Vc_nickname like '%" . $sKey . "%'";

$table = 'p2p_autobid a
			left join user_base c on c.ID=a.I_userID
		where c.Status=1 '.$mWhere;
$sql = 'select a.*,
			(select sum(b.N_amount) from p2p_bid b where b.I_deal=1 and b.Status=1 and b.I_type=2 and a.I_userID=b.I_userID) N_amount_bid,
			(select count(*) from p2p_bid b where b.I_deal=1 and b.Status=1 and b.I_type=2 and a.I_userID=b.I_userID) bid_nums,
			c.Vc_nickname,c.Vc_mobile,c.Vc_truename,c.Vc_Email,c.N_amount N_amount_totel,c.N_amount_freeze
		FROM '.$table;
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
			window.location.href="./AutobidStat.php?outexcel=true'.$UrlInfo.'";
		});
	})
	</script>
	';
	$extend['sTypes'] = array('<span class="spancheck defaultcheck">用户名：<input name="sKey" type="text" class="txt_put1" value="'.$sKey.'" maxlength="50"></span>'
			);
	
	$thsl=$ths=$tdsl=$tds = array();
	$thsl[]=array('val'=>'用户名', 'wid'=>'');
	$ths[]=array('val'=>'真实姓名', 'wid'=>'');
	$ths[]=array('val'=>'联系电话', 'wid'=>'');
	$ths[]=array('val'=>'电子邮件', 'wid'=>'');
	$ths[]=array('val'=>'账户余额', 'wid'=>'');
	$ths[]=array('val'=>'可用金额', 'wid'=>'');
	$ths[]=array('val'=>'冻结金额', 'wid'=>'');
	$ths[]=array('val'=>'已投项目', 'wid'=>'');
	$ths[]=array('val'=>'已投金额', 'wid'=>'');
	$ths[]=array('val'=>'设置金额', 'wid'=>'');
	$ths[]=array('val'=>'保留金额', 'wid'=>'');
	$ths[]=array('val'=>'当前状态', 'wid'=>'');
	//$ths[]=array('val'=>'贷款类型', 'wid'=>'');
	//$ths[]=array('val'=>'贷款金额下限', 'wid'=>'');
	//$ths[]=array('val'=>'贷款金额上限', 'wid'=>'');
	$ths[]=array('val'=>'借款期限下限', 'wid'=>'');
	$ths[]=array('val'=>'借款期限上限', 'wid'=>'');
	$ths[]=array('val'=>'年化利率下限', 'wid'=>'');
	$ths[]=array('val'=>'年化利率上限', 'wid'=>'');
	$ths[]=array('val'=>'标类型', 'wid'=>'');
	//$ths[]=array('val'=>'还款方式', 'wid'=>'');已去掉此功能
	$ths[]=array('val'=>'操作', 'wid'=>'');
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
			$_td .= '<td>'. $Rs[$i]['Vc_Email'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['N_amount_totel'] .'</td>';
			$_td .= '<td>'. ($Rs[$i]['N_amount_totel']-$Rs[$i]['N_amount_freeze']).'</td>';
			$_td .= '<td>'. $Rs[$i]['N_amount_freeze'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['bid_nums'] .'个</td>';
			$_td .= '<td>'. $Rs[$i]['N_amount_bid'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['N_amount'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['N_base'] .'</td>';
			$_td .= '<td>'. ($Rs[$i]['I_deal']==1&&$Rs[$i]['Status']==1?"开启中":"已关闭").'</td>';
			/* $typeStr='';
			if($Rs[$i]['Vc_type']){
				$typeids=explode(",", $Rs[$i]['Vc_type']);
				foreach ($typeids as $typeid)
					$typeStr .= $da_appclass[$typeid]['Vc_name'].' ';
			}
			$_td .= '<td>'. $typeStr .'</td>';
			$_td .= '<td>'. $Rs[$i]['I_minamount'] .'</td>';
			$_td .= '<td>'. $Rs[$i]['I_maxamount'] .'</td>'; */
			$_td .= '<td>'. $Rs[$i]['I_minlife'] .'个月</td>';
			$_td .= '<td>'. $Rs[$i]['I_maxlife'] .'个月</td>';
			$_td .= '<td>'. $Rs[$i]['I_minannualrate'] .'%</td>';
			$_td .= '<td>'. $Rs[$i]['I_maxannualrate'] .'%</td>';
			$_td .= '<td>'. $da_appclass[$Rs[$i]['I_appClass']]['Vc_name'] .'</td>';
			$_td .= '<td><a title="'.$Rs[$i]['Vc_nickname'].'自动投标明细" href="AutobidStatInfo.php?id='.$Rs[$i]['I_userID'].'&name='.$Rs[$i]['Vc_nickname'].'">查看明细</a></td>';
			$tds[$Rs[$i]['ID']]=$_td;
		}
	}
	
	$DataBase->CloseDataBase();
	$extend['fan'] = false;
	$helps  = array();
	$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
	$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);

	$btns=array('<a href="AutobidStatInfo.php" name="change" class="buta" style="display:inline-block;float:right;"/>按投资时间显示</a>',
			'<a name="submit" class="buta_excel" style="display:inline-block;float:none;"/>导出excel</a>'
					);
	
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
			array('联系电话','Vc_Email'),
			array('电子邮件','Vc_mobile'),
			array('账户余额','N_amount_totel'),
			array('可用金额','$Rs[$i][\'N_amount_totel\']-$Rs[$i][\'N_amount_freeze\']','other'),
			array('冻结金额','N_amount_freeze'),
			array('已投项目','bid_nums'),
			array('已投金额','N_amount_bid'),
			array('设置金额','N_amount'),
			array('保留金额','N_base'),
			array('当前状态','$Rs[$i][\'I_deal\']==1&&$Rs[$i][\'Status\']==1?"开启中":"已关闭"','other'),
			//$ths[]=array('val'=>'贷款类型', 'wid'=>''),
			//$ths[]=array('val'=>'贷款金额下限', 'wid'=>''),
			//$ths[]=array('val'=>'贷款金额上限', 'wid'=>''),
			array('借款期限下限','I_minlife','','个月'),
			array('借款期限上限','I_maxlife','','个月'),
			array('年化利率下限','I_minannualrate','','%'),
			array('年化利率上限','I_maxannualrate','','%'),
			//array('还款方式','$Rs[$i][\'I_repayment\']?getInfoByDDIC(\'p2p_application.I_repayment\',$Rs[$i][\'I_repayment\']):"不限"','other')
			array('标类型','$da_appclass[$Rs[$i][\'I_appClass\']][\'Vc_name\']','other'),
	);
	
	include_once WEBROOTINC.'ExplodeExcel.php';
}

?>