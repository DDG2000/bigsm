<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2014-5-24
**本页： 审核流程 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_AUDIT_FLOW');
$isMdy = $Admin->CheckPopedom('SC_SITE_AUDIT_FLOW_MDY');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType ;

//分栏
$column = 'column';
$column_v = $FLib->requestint($column,1,9,'分类');

require(WEBROOTINC.'ApplyCommon.php');
foreach($entityarr as $k=>$v) $typearr[$k]=$v[0];
$marks = '';
foreach($typearr as $k=>$v){$marks.='<a href="?'.$column.'='.$k.'" '.($k==$column_v?'class="cur"':'').'>'.$v.'</a>';}
$UrlInfo .= '&'.$column.'=' . $column_v ;

$title = '审核流程';
$points = array('网站管理', '审核管理', $title.'管理' );
$sTypes = array('', '流程名称');
$hides  = array($column=>$column_v);
$extend = array('marks'=>$marks);

$mWhere = '';
switch ($sType){
	case 1:
		$mWhere .= " and Vc_name like '%" . $sKey . "%'";
		break;
}
if($column_v > 0){
	$mWhere .= " and I_entity=$column_v";
}
$sqlw = 'from sm_apply_flow where Status=1'.$mWhere;
$sql = 'select * '.$sqlw.' order by id desc';
$sqlcount = 'select count(*) '.$sqlw;
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'步骤名称', 'wid'=>'');
$ths[]=array('val'=>'审核级别', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
if($isMdy){
$ths[]=array('val'=>'操作', 'wid'=>'');
}
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td title="'.$Rs[$i]['Vc_name'].'">'.$FLib->CutStr($Rs[$i]['Vc_name'],80).'</td>';
		$_td .= '<td>'.$Rs[$i]['I_level'].'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		if($isMdy){
		$_td .= '<td><a href="AuditFlowMdy.php?Work=MdyReco&Id='.$Rs[$i]['id'].'" class="hs" h="460" title="编辑'.$title.'">编辑</a>';
		}
		$tds[$Rs[$i]['id']]=$_td;
    }
}
$DataBase->CloseDataBase();
$extend['gbtns']=$btns=array();
if($isMdy){
	$btns[] = '<a href="AuditFlowProcess.php?Work=DelReco&IdList=" class="del" rel="IdList">删 除</a>';
	$extend['gbtns'][] = '<a href="AuditFlowMdy.php?Work=AddReco&'.$column.'='.$column_v.'" class="hs" h="460" title="添加'.$title.'"><span>添加'.$title.'</span></a>';
}
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
