<?php
if(1){
/****************************************************************** 
**创建者：zhi
**创建时间：2013-10-18
**本页：意见反馈
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_OPINION');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$PKey   = $FLib->RequestChar('PKey',1,50,'标识',0);
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType .'&PKey=' . $PKey ;

$title = '意见反馈';
$points = array('网站管理', $title.'管理');
$sTypes = array('', '用户ID');
$hides  = array();//不传parent 查询全部
$extend = array();
$mWhere='1';
if($sKey!=''){
switch ($sType){
	case 1:
		$mWhere .= " and b.I_userID = '$sKey'";
		break;
}
}
$tables = "fp_feedback b left join user_base c on c.ID=b.I_userID where {$mWhere} and b.Status=1 ";
$sql = "SELECT b.ID,b.Vc_title,b.T_content,b.I_audit,b.Createtime,c.Vc_nickname uname,b.I_userID FROM {$tables} order by b.ID DESC";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'用户ID', 'wid'=>'');
$ths[]=array('val'=>'内容', 'wid'=>'');
$ths[]=array('val'=>'审核状态', 'wid'=>'');
$ths[]=array('val'=>'回复', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td = '<td><a href="../user/MemberInfo.php?Id='.$Rs[$i]['I_userID'].'" class="hs" h="" title="用户详细页">'.$Rs[$i]['I_userID'].'</a></td>';
		$_td .= '<td >'.$FLib->cutStrLower($Rs[$i]['T_content'],20).'</td>';
		$_td .= '<td>'.($Rs[$i]['I_audit']?'已审核':'未审核').'</td>';
		$_td .= '<td>'.($Rs[$i]['Vc_title']!=''?'已回复':'未回复').'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$_td .= '<td><a href="FeedbackInfo.php?Id='.$Rs[$i]['ID'].'" class="hs" h="500" >详情</a>'.($Rs[$i]['Vc_title']==''?' | <a href="FeedbackReply.php?id='.$Rs[$i]['ID'].'" class="hs" h="500">回复</a>':'').'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array('<a href="FeedbackProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删 除</a>','<a href="FeedbackProcess.php?Work=AuditReco&IdList=" class="del" rel="IdList">审 核</a>',);
$extend['gbtns'] = array();
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
