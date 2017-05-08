<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 首页轮播 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_ADROLL');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}
//关键字,搜索开关
$sKey   = $FLib->requestchar('sKey',1,50,'参数',1);
$sType  = $FLib->requestint('sType',0,9,'类型');
$CurrPage = $FLib->requestint('currpage',1,9,'页数');
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType ;

$title = '首页轮播';
$points = array('网站管理', $title.'管理' );
$sTypes = array('', '名称');
$hides  = array();
$extend = array();

switch ($sType){
	case 1:
		$mWhere = "a.Vc_name like '%$sKey%'";
		break;
	default:
		$mWhere = '1=1';
		break;
}
$tables = 'es_roll a where  '.$mWhere.'';
$sql = "SELECT a.* FROM {$tables} order by a.Status desc ,a.I_order desc,a.Createtime desc";
	//分页代码
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'名称', 'wid'=>'');
$ths[]=array('val'=>'连接', 'wid'=>'');
$ths[]=array('val'=>'图片', 'wid'=>'');
$ths[]=array('val'=>'位置', 'wid'=>'');
$ths[]=array('val'=>'排序号', 'wid'=>'');
$ths[]=array('val'=>'状态', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');
$pos=array('大图','小');
$img=array('../image/pop_x.gif','../image/onCorrect.gif');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$type=$Rs[$i]['I_type']-1;
		$position=$pos[$type];
		$_td  = '<td title="'. $Rs[$i]['Vc_name'] .'">'. $FLib->CutStr($Rs[$i]['Vc_name'],40).'</td>';
		$_td .= '<td title="'. $Rs[$i]['Vc_url']  .'">'. $FLib->CutStr($Rs[$i]['Vc_url'],40).'</td>';
		$_td .= '<td><a href="'.$Config->WebRoot.$Rs[$i]['Vc_image'].'" title="点击查看" target="_blank">查看</a></td>';
		$_td .= '<td title="'. $Rs[$i]['I_type']  .'">'. $FLib->CutStr($position,40).'</td>';
		$_td .= '<td title="'. $Rs[$i]['I_order'] .'">'. $Rs[$i]['I_order'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Status'] .'"><img id="change" src="'.$img[$Rs[$i]['Status']].'"/></td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$_td .= '<td><a href="AdIndexMdy.php?Work=MdyReco&Id='.$Rs[$i]['ID'].'&type='.$Rs[$i]['I_type'].'" class="hs" h="450" title="编辑'.$title.'">编辑</a></td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array();
$btns[] = '<a href="AdIndexProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删 除</a>';

$extend['gbtns'] = array('<a href="AdIndexMdy.php?Work=AddReco" class="hs" h="450" title="添加'.$title.'"><span>添加'.$title.'</span></a>');

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
