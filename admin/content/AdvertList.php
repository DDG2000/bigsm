<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 广告 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_AD');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->requestchar('sKey',1,50,'参数',1);
$sType  = $FLib->requestint('sType',0,9,'类型');
$CurrPage = $FLib->requestint('currpage',1,9,'页数');
$status = $FLib->requestint('status',1,9,'状态');
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType  .'&status=' . $status ;

$typearr = array('t_1'=>'活动中广告','t_0'=>'禁用广告');//key 需要根据status改变
$marks = '';
foreach($typearr as $k=>$v){$marks.='<a href="?status='.substr($k,2).'" '.(substr($k,2)==$status?'class="cur"':'').'>'.$v.'</a>';}

$title = '广告';
$points = array('网站管理', $title.'管理' );
$sTypes = array('', '广告名称');
$hides  = array('status'=>$status);
$extend = array('marks'=>$marks);

switch ($sType){
	case 1:
		$mWhere = "sa.Vc_name like '%$sKey%'";
		break;
	default:
		$mWhere = '1=1';
		break;
}
$mWhere .= ' and sa.I_active='.$status;
$tables = 'site_advertise sa left join site_advertise_place sap on sa.I_placeID=sap.ID where sa.Status=1 and '.$mWhere.'';
$sql = "SELECT sa.*,sap.Vc_name sapname FROM {$tables} order by sa.ID desc";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'广告名称', 'wid'=>'');
$ths[]=array('val'=>'广告位置', 'wid'=>'');
$ths[]=array('val'=>'广告类型', 'wid'=>'');
$ths[]=array('val'=>'开始时间', 'wid'=>'');
$ths[]=array('val'=>'结束时间', 'wid'=>'');
$ths[]=array('val'=>'查看', 'wid'=>'');
$ths[]=array('val'=>'状态', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td>'.$FLib->CutStr($Rs[$i]['Vc_name'],40).'</td>';
		$_td .= '<td>'.$Rs[$i]['sapname'].'</td>';
		$_td .= '<td>'.($Rs[$i]['I_type']==1?'图片':($Rs[$i]['I_type']==2?'flash':($Rs[$i]['I_type']==3?'文字':'不详'))).'</td>';
		$_td .= '<td>'.$Rs[$i]['Vc_start'].'</td>';
		$_td .= '<td>'.$Rs[$i]['Vc_end'].'</td>';
		$_td .= '<td><a href='.$Rs[$i]["Vc_image"].' target="_black"><img src='.$Rs[$i]["Vc_image"].' height="50px"></a></td></td>';
		$_td .= '<td>'.($Rs[$i]['I_active']==1?'活动':'禁用').'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$_td .= '<td><a href="AdvertMdy.php?Work=MdyReco&Id='.$Rs[$i]['ID'].'&type='.$Rs[$i]['I_type'].'" class="hs" h="450" title="编辑'.$title.'">编辑</a></td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array();
if($status==0){$btns[] = '<a href="AdvertUpdateProcess.php?IdList=" class="del" rel="IdList" title="同一广告位上活动的广告,将被置成禁用!" h="200">启 用</a>';}
$btns[] = '<a href="AdvertProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删 除</a>';

$extend['gbtns'] = array('<a href="AdvertMdy.php?Work=AddReco" class="hs" h="450" title="添加'.$title.'"><span>添加'.$title.'</span></a>');

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
