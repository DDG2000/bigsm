<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 用户 管理
**说明：
******************************************************************/

require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER_LEAVEWORD_MDY');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sdate   = $FLib->RequestChar('sdate',1,50,'',1);
$edate   = $FLib->RequestChar('edate',1,50,'',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$type     = $FLib->RequestInt('type',1,9,'查询类型');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType ."&type=" . $type."&sdate=" . $sdate."&edate=" . $edate;

$title = '留言';
$points = array('会员管理', $title.'列表' );
$sTypes = array('','昵称');
$hides  = array('type'=>$type);
$extend = array('sTypes'=>array('留言时间:<input type="text" size="10" name="sdate" value="'.$sdate .'" id="sdate" isc="datepatFun" onclick="return showCalendar(\'sdate\', \'y-mm-dd\');" ennull="1" class="txt_put1"/>-<input type="text" size="10" name="edate"  value="'.$edate .'"  id="edate" isc="datepatFun" onclick="return showCalendar(\'edate\', \'y-mm-dd\');"  ennull="1" class="txt_put1"/>'),

'js'=>'<script src="../include/calendar/Calendar.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="../include/calendar/calendar.css" >');
$mWhere='';
if($sKey){
switch ($sType){
	case 1:
		$mWhere = "Vc_username like '%{$sKey}%' and ";
		
		break;
	
	
}
}

if(preg_match('/^\d{4}\-\d{1,2}\-\d{1,2}$/',$sdate)){
	$mWhere .= "Createtime >= '{$sdate}' and ";
}
if(preg_match('/^\d{4}\-\d{1,2}\-\d{1,2}$/',$edate)){
	$mWhere .= "Createtime <= '{$edate} 23:59:59' and ";
}
$tables = 'user_leaveword where '.$mWhere.'Status=1';
$sql = "SELECT * FROM {$tables} order by ID desc";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'昵称', 'wid'=>'');
//$ths[]=array('val'=>'标题', 'wid'=>'');
$ths[]=array('val'=>'内容', 'wid'=>'');
$ths[]=array('val'=>'是否显示', 'wid'=>'');
//$ths[]=array('val'=>'是否处理', 'wid'=>'');
$ths[]=array('val'=>'留言时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		if($Rs[$i]['I_userID']>0){
			$_td  = '<td><a href="MemberInfo.php?Id='.$Rs[$i]['I_userID'].'" class="hs" h="" title="用户详细页">'.$Rs[$i]['Vc_username'].'</a></td>';
		}else{
			$_td  = '<td>'.$Rs[$i]['Vc_username'].'</td>';
		}
		//$_td .= '<td>'. $Rs[$i]['Vc_name'] .'</td>';
		$_td .= '<td title="'.str_replace('"','&quot;',strip_tags($Rs[$i]['Vc_content'])).'">'. $FLib->CutStr($Rs[$i]['Vc_content'],50) .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_display']?'显示':'未显示') .'</td>';
		//$_td .= '<td>'. ($Rs[$i]['I_deal']?'已处理':'未处理') .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$_td .= '<td><a href="LeavewordInfo.php?lid='.$Rs[$i]['ID'].'" class="hs" h="450">查看</a></td>';//<a href="LeavewordReply.php?lid='.$Rs[$i]['ID'].'" class="hs" h="500">回复</a>
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array();
$btns[] = '<a href="LeavewordProcess.php?Work=ShowReco&IdList=" class="del" rel="IdList">显示</a>';
$btns[] = '<a href="LeavewordProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删除</a>';

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