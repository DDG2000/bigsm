<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 登录记录 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SYS_TOOL_TRYLOGIN');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->requestchar('sKey',1,50,'参数',1);
$sType  = $FLib->requestint('sType',0,9,'类型');
$CurrPage = $FLib->requestint('currpage',1,9,'页数');
$status = $FLib->requestint('status',0,9,'状态');
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType ;

$typearr = array('t_1'=>'成功登录','t_0'=>'失败登录');//key 需要根据status改变
$marks = '';
foreach($typearr as $k=>$v){$marks.='<a href="?status='.substr($k,2).$UrlInfo.'" '.(substr($k,2)==$status?'class="cur"':'').'>'.$v.'</a>';}
$UrlInfo .= '&status=' . $status ;

$title = '登录记录';
$points = array('系统管理', $title );
$sTypes = array('', '用户名', '登录IP');
$hides  = array('status'=>$status);
$extend = array('marks'=>$marks);

switch ($sType){
	case 1:
		$mWhere = "Vc_name like '%$sKey%'";
		break;
	case 2:
		$mWhere = "Vc_IP like '%$sKey%'";
		break;
	default:
		$mWhere = '1=1';
		break;
}
$mWhere .= ' and I_type='.$status;
$tables = 'sc_login_record where Status=1 and '.$mWhere.'';
$sql = "SELECT * FROM {$tables} order by ID desc";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'用户名', 'wid'=>'');
if($status==0){$ths[]=array('val'=>'密码', 'wid'=>'');}
$ths[]=array('val'=>'IP', 'wid'=>'');
$ths[]=array('val'=>'类别', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td>'.$Rs[$i]['Vc_name'].'</td>';
		if($status==0){$_td .= '<td>'.$Rs[$i]['Vc_password'].'</td>';}
		$_td .= '<td>'.$Rs[$i]['Vc_IP'].'</td>';
		$_td .= '<td>'.($Rs[$i]['I_type']==1?'成功':'失败').'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array('<a href="TryLoginProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删 除</a>',);
$extend['gbtns'] = array('<a href="TryLoginProcess.php?Work=ClearReco" class="delall" title="是否清空所有记录">清空记录</a>');
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
