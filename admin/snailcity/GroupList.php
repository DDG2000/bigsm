<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 分组 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SYS_SET_GROUP');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$Parent = $FLib->RequestInt('Parent',0,9,'父ID');
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType .'&Parent=' . $Parent;

$title = '分组';
$points = array('系统管理', $title.'管理', array('innerHtml'=>'根组', 'href'=>'?Parent=0'));
$pid_tem = $Parent;$npoints = array();
while($pid_tem>0){
	$Rs = $DataBase->GetResultOne('SELECT I_parentID,Vc_name FROM sc_group WHERE Status=1 And id=' . $pid_tem .' limit 0,1');
	if(!$Rs){break;}
	$npoints[] = array('innerHtml'=>$Rs[1], 'href'=>'?Parent='.$pid_tem);
	$pid_tem = $Rs[0];
}
$points = array_merge($points, array_reverse($npoints));
$sTypes = array('', '权限标识', '权限名称');
$sTypes = array('', '分组名称');
$hides  = array('Parent'=>$Parent);
$extend = array();

switch ($sType){
	case 1:
		$mWhere = "Vc_name like '%$sKey%'";
		break;
	default:
		$mWhere = '1=1';
		break;
}
$mWhere .= ' and I_parentID=' . $Parent;
$tables = 'sc_group where Status=1 and I_show=1 and '.$mWhere.'';
$sql = "SELECT * FROM {$tables} order by ID";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'分组名称', 'wid'=>'');
$ths[]=array('val'=>'上级组', 'wid'=>'');
$ths[]=array('val'=>'排序号', 'wid'=>'');
$ths[]=array('val'=>'创建者', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$pname='根组';
		if($Rs[$i]['I_parentID']>0){
			$Rs1 = $DataBase->GetResultOne("select Vc_name from sc_group where status<>0 and I_show=1 and ID='".$Rs[$i]['I_parentID']."'");
			$pname=isset($Rs1[$i]['Vc_name']);
		}
		$_td  = '<td><a href="?Parent='. $Rs[$i]['ID'] .'">'.$Rs[$i]['Vc_name'].'</a></td>';
		$_td .= '<td>'.$pname.'</td>';
		$_td .= '<td>'.$Rs[$i]['I_order'].'</td>';
		$_td .= '<td>'.$Admin->GetAdminInfo($Rs[$i]['I_operatorID'],1).'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$_td .= '<td><a href="GroupMdy.php?Work=MdyReco&Id='.$Rs[$i]['ID'].'" class="hs" h="530" title="编辑'.$title.'">编辑</a></td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array('<a href="GroupProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删 除</a>',);
$extend['gbtns'] = array('<a href="GroupMdy.php?Work=AddReco&Parent='.$Parent.'" class="hs" h="530" title="添加'.$title.'"><span>添加'.$title.'</span></a>',);
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
