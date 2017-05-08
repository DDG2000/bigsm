<?php
if(1){
require_once('../include/TopFile.php');
require(WEBROOTINCCLASS.'Concentrated.php');
$Admin->CheckPopedoms('SM_CONCENTRATED_LIST');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$ID = $FLib->RequestInt('Id', 0, 10,'ID') ;
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType."&Id=$ID";

$I_mall_classID=1;//钢材市场
$form='ProjectOrder';
$title = '报名商家';
$points = array('集采管理', $title.'列表' );
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$objConcentrated = new Concentrated();

$Rs = $objConcentrated->getListPage($CurrPage, $pagesize,$ID) ;

$ths = array();

$ths[]=array('val'=>'公司名称', 'wid'=>'');
$ths[]=array('val'=>'联系人', 'wid'=>'');
$ths[]=array('val'=>'联系电话', 'wid'=>'');
$ths[]=array('val'=>'报名时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td>'. $Rs[$i]['company'].'</td>';
		$_td .= '<td>'. $Rs[$i]['contact'].'</td>';
		$_td .= '<td>'. $Rs[$i]['phone'].'</td>';
		$_td .= '<td>'. $Rs[$i]['Createtime'] .'</td>';
		$_td .= '<td>-</td>';
// 		$_td .= '<td><a href="'.$form.'Mdy.php?Work=MdyReco&Id='.$Rs[$i]['id'].'" class="hs" h="450" title="编辑'.$title.'">编辑</a></td>';
		$tds[$Rs[$i]['id']]=$_td;
    }
}
$DataBase->CloseDataBase();

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