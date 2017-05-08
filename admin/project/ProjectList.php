<?php
if(1){
require_once('../include/TopFile.php');
require_once(WEBROOTINCCLASS.'Project.php');
$Admin->CheckPopedoms('SM_PROJECT');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$sTypes = array('关键词') ;
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType;
$outexcel = $FLib->RequestChar('outexcel',1,9,'outexcel');

$I_mall_classID=1;//钢材市场
$form='Project';
$title = '项目';
$points = array('项目管理', $title.'列表' );
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;

$project = new Project() ;
$Rs = $project->getListPage($CurrPage, $pagesize, $sType, $sKey) ;

$ths = array();
$ths[]=array('val'=>'ID', 'wid'=>'');
$ths[]=array('val'=>'项目名称', 'wid'=>'');
$ths[]=array('val'=>'地址', 'wid'=>'');
$ths[]=array('val'=>'负责人', 'wid'=>'');
$ths[]=array('val'=>'起始时间', 'wid'=>'');
$ths[]=array('val'=>'是否融资', 'wid'=>'');
$ths[]=array('val'=>'联系人', 'wid'=>'');
$ths[]=array('val'=>'公司名称', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'150');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
	    $opts = "<a href='{$form}Mdy.php?Work=MdyReco&Id={$Rs[$i]['id']}' class='hs' h='450' title='编辑$title'>编辑</a>" ;
	    if ($Admin->CheckPopedom("SM_PROJECT_ORDER")) {
		    $opts.= " | <a href='ProjectOrderList.php?Id={$Rs[$i]['id']}' class='hs' w='1000' h='700' title='查看项目订单'>查看订单</a> " ;
	    }
	    if ($Admin->CheckPopedom("SM_PROJECT_CONSUMPTIONS")) {
		    $opts.= " | <a href='ProjectConsumptionsList.php?Id={$Rs[$i]['id']}' class='hs' w='1000' h='700' title='查看项目订单'>查看用量</a> " ;
	    }
		$_td  = '<td>'. $Rs[$i]['id'].'</td>';
		$_td .= '<td>'. $Rs[$i]['Vc_name'].'</td>';
		$_td .= '<td>'. $Rs[$i]['Vc_address'].'</td>';
		$_td .= '<td>'. $Rs[$i]['Vc_admin'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['D_start'] . '至' . $Rs[$i]['D_end'] .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_need_loans'] == 1 ? '是':'否') .'</td>';
		$_td .= '<td>'. $Rs[$i]['Vc_contact'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['cname'] .'</td>';
		$_td .= '<td>'.$opts.'</td>';
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