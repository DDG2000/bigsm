<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-4-10
**本页： 初始化参数 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_MODELEMAIL');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$type = $FLib->RequestInt('type',0,9,'类型');//default 1
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType .'&type=' . $type ;

$ts = ($type==1?'手机':'邮箱');
$title = $ts.'白名单';
$points = array('网站管理', $title.'管理');
$sTypes = array('', $ts);
$hides  = array('type'=>$type);
$extend = array();

switch ($sType){
	case 1:
		$mWhere = "Vc_value like '%$sKey%'";
		break;
	default:
		$mWhere = '1=1';
		break;
}
if($type>0){
	$mWhere .= ' and I_type='.$type;
}
$tables = 'site_test_phone_email where Status=1 and '.$mWhere.'';
$sql = "SELECT * FROM {$tables}";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = 100;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>$ts, 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td>'.$Rs[$i]['Vc_value'].'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d H:i:s') .'</td>';
		$_td .= '<td><a href="TestPhoneEmailMdy.php?Work=MdyReco&type='.$type.'&Id='.$Rs[$i]['ID'].'" class="hs" h="530" title="编辑'.$title.'">编辑</a></td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();
$btns   = array('<a href="TestPhoneEmailProcess.php?Work=DeleteReco&type='.$type.'&IdList=" class="del" rel="IdList">删 除</a>',);
$extend['gbtns'] = array('<a href="TestPhoneEmailMdy.php?Work=AddReco&type='.$type.'" class="hs" h="530" title="添加'.$title.'"><span>添加'.$title.'</span></a>');
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
