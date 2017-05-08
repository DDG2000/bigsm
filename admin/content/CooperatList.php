<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 合作站点 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_PARTNER');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType ;

$title = '合作站点';
$points = array('商城管理', $title.'管理' );
$sTypes = array('', '站点名称', '站点地址');
$hides  = array();
$extend = array();

switch ($sType){
	case 1:
		$mWhere = "p.Vc_name like '%" . $sKey . "%'";
		break;
	case 2:
		$mWhere = "p.Vc_site like '%" . $sKey . "%'";
		break;
	default:
		$mWhere = '1=1';
		break;
}
$sql = 'select p.*,q.count from site_partner as p left join (select Vc_introducer,count(*) as count from user_base where status=1 group by Vc_introducer) as q on p.Vc_identity=q.Vc_introducer where p.Status=1 and '.$mWhere.' order by q.count desc';
$sqlcount = 'select count(*) from site_partner as p where p.Status=1 and '.$mWhere.'';
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'站点名称', 'wid'=>'');
$ths[]=array('val'=>'站点地址', 'wid'=>'');
//$ths[]=array('val'=>'用户记录', 'wid'=>'');
//$ths[]=array('val'=>'消费记录', 'wid'=>'');
$ths[]=array('val'=>'站点标记', 'wid'=>'');
$ths[]=array('val'=>'用户数', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td title="'.$Rs[$i]['Vc_name'].'">'.$FLib->CutStr($Rs[$i]['Vc_name'],80).'</td>';
		$_td .= '<td>'.$Rs[$i]['Vc_site'].'</td>';
		//$_td .= '<td><a href="UserRecordSearch.php?id='.$Rs[$i]['ID'].'" class="hs" title="查看用户记录">查看</a></td>';
		//$_td .= '<td><a href="ExpenseRecordSearch.php?id='.$Rs[$i]['ID'].'" class="hs" title="查看消费记录">查看</a></td>';
		$_td .= '<td>'.$Rs[$i]['Vc_identity'].'</td>';
		$_td .= '<td>'.isetn($Rs[$i]['count']).'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$_td .= '<td><a href="CooperatMdy.php?Work=MdyReco&Id='.$Rs[$i]['ID'].'" class="hs" h="340" title="编辑'.$title.'">编辑</a>|<a href="javascript:copyToClipBoard(\''.$Config->WebRoot.'user/reg.php?N='.$Rs[$i]['Vc_identity'].'\');void(0);">复制</a></td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array('<a href="CooperatProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删 除</a>',);
$extend['gbtns'] = array('<a href="CooperatMdy.php?Work=AddReco&Aid='.$Aid.'" class="hs" h="340" title="添加'.$title.'"><span>添加'.$title.'</span></a>',);
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
