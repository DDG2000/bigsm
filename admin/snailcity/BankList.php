<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 用户 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SYS_SET_USER');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$userID   = $FLib->RequestInt('userID',0,10,'参数');
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType;

$title = '银行卡';
$points = array('系统管理', $title.'管理');

$hides  = array();
$extend = array();

if($userID!=0){ $mWhere = " and I_userID={$userID}"; }
//20140811 edit chenjx
$tables=" p2p_user_bankcard where Status=1 and I_signed=0 {$mWhere}";
$order="order by id desc";
$sql = "SELECT * FROM {$tables} {$order}";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;
$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'', 'wid'=>'');
$ths[]=array('val'=>'银行卡号', 'wid'=>'');
$ths[]=array('val'=>'银行名称', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	
	for($i=1;$i<count($Rs) ;$i++){
		$j=$Rs[$i]['Vc_bankcode'];
		$Rs[$i]['bankcode'] = $DDIC['p2p_user_bankcard.Vc_bankcode'][$j]['name'];		
		$_td  = '<td></td>';
		$_td .= '<td>'.$Rs[$i]['Vc_code'].'</td>';
		$_td .= '<td>'.$Rs[$i]['bankcode'].'</td>';
		$_td .= '<td><a href="BankMdy.php?Work=Withdraw&Id='.$Rs[$i]['ID'].'&bankcode='.$Rs[$i]['bankcode'].'&Vc_code='.$Rs[$i]['Vc_code'].'" class="hs" h="360" title="提现">提现</a></td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array('<a href="BankProcess.php?Work=DeleteReco&userID='.$userID.'&IdList=" class="del" rel="IdList">解绑</a>',);
$extend['gbtns'] = array('<a href="BankMdy.php?Work=AddReco&userID='.$userID.' " class="hs" h="530" title="添加'.$title.'"><span>添加'.$title.'</span></a>',);
$helps  = array();
$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);


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
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('list'.$raintpl_ver);
exit;
}
?>
