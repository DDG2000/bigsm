<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 用户等级 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER_GRADE');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType ;

$title = '用户等级';
$points = array('会员管理', $title.'管理' );
$sTypes = array();
$hides  = array();
$extend = array();

$tables = 'user_grade where Status=1 ';
$sql = "SELECT * FROM {$tables} order by ID desc";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'用户身份', 'wid'=>'');
$ths[]=array('val'=>'所需积分', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td>'. $Rs[$i]['Vc_name'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['I_point'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$_td .= '<td><a href="MemberGradeMdy.php?Work=MdyReco&Id='.$Rs[$i]['ID'].'" class="hs" h="370" title="编辑'.$title.'">编辑</a></td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array();
$btns[] = '<a href="MemberGradeProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删除</a>';
$extend['gbtns'] = array('<a href="MemberGradeMdy.php?Work=AddReco" class="hs" h="370" title="添加'.$title.'"><span>添加'.$title.'</span></a>');
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
