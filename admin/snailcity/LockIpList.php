<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 登录记录 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SYS_TOOL_ISLOCKIP');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->requestchar('sKey',1,50,'参数',1);
$sType  = $FLib->requestint('sType',0,9,'类型');
$CurrPage = $FLib->requestint('currpage',1,9,'页数');
$status = $FLib->requestint('Status',1,9,'状态');
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType .'&Status=' . $status ;

$title = $status==1?'IP黑名单':'IP白名单';
$points = array('系统管理', $title );
$sTypes = array('', 'IP地址');
$hides  = array('status'=>$status);
$extend = array();

switch ($sType){
	case 1:
		$mWhere = "Vc_startIP like '%$sKey%'";
		break;
	default:
		$mWhere = '1=1';
		break;
}
if($status==1){
	$tables = 'sc_lockip where Status=1 and '.$mWhere.'';
}else{
	$tables = 'sc_allowip where Status=1 and '.$mWhere.'';
}
$sql = "SELECT * FROM {$tables} order by ID desc";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'IP开始地址', 'wid'=>'');
$ths[]=array('val'=>'IP结束地址', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td>'.$FLib->IPDecode($Rs[$i]['Vc_startIP'] ).'</td>';
		$_td .= '<td>'.$FLib->IPDecode($Rs[$i]['Vc_endIP'] ).'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$_td .= '<td><a href="LockIpMdy.php?Work=MdyReco&Status='.$status.'&Id='.$Rs[$i]['ID'].'" class="hs" h="300" title="编辑'.$title.'">编辑</a></td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array('<a href="LockIpProcess.php?Work=DeleteReco&Status='.$status.'&IdList=" class="del" rel="IdList">删 除</a>',);
$extend['gbtns'] = array('<a href="LockIpMdy.php?Work=AddReco&Status='.$status.'" class="hs" h="300" title="添加'.$title.'"><span>添加'.$title.'</span></a>',);
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
