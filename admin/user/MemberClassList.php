<?php
if(1){
/****************************************************************** 
**创建者：sakura
**创建时间：2014-11-04
**本页： 用户类型 管理
**说明：
******************************************************************/

require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER_CLASS');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType;

$title = '会员类型';
$points = array('会员管理', $title.'列表' );

$tables = 'user_class where Status=1';
$sql = "SELECT * FROM {$tables} order by ID";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'名称', 'wid'=>'');
$ths[]=array('val'=>'排序号', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td = '<td>'. $Rs[$i]['Vc_name'].'</td>';
		$_td .= '<td>'. $Rs[$i]['I_order'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d H:i:s') .'</td>';
		$_td .= '<td><a href="MemberClassMdy.php?Work=MdyReco&Id='.$Rs[$i]['ID'].'" class="hs" h="450" title="编辑'.$title.'">编辑</a></td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();
$extend['gbtns'][] = '<a href="MemberClassMdy.php?Work=AddReco" class="hs" h="450" title="添加'.$title.'"><span>添加'.$title.'</span></a>';
$btns = array('<a href="MemberClassProcess.php?Work=DeleteReco&tname='.urlencode($title).'&IdList=" class="del" rel="IdList" title="删除'.$title.'"><span>删除</span></a>');

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