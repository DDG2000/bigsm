<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2014-6-9
**本页： 会员查找返回IdList 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER');

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$isAuthenticated = $FLib->RequestInt('isAuthenticated',0,1,'是否认证') ;
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$Type     = $FLib->RequestInt('Type',1,9,'返回类型');
$IdList = $FLib->RequestChar('IdList',1,0,'IdList',0);
$returnInput = $FLib->RequestChar('returnInput',1,50,'returnInput',0);

$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType ."&Type=" . $Type."&IdList=" . $IdList."&returnInput=" . $returnInput . "&isAuthenticated=" . $isAuthenticated  ;

$title = '会员名单';
$points = array('会员管理', $title.'搜索列表' );
$sTypes = array('','昵称','姓名','邮箱','手机');
$hides  = array();
$extend = array();
$hides['Type'] = $Type;
$hides['IdList'] = $IdList;
$hides['returnInput'] = $returnInput;
$hides['isAuthenticated'] = $isAuthenticated ;
$extend['IdList'] = explode(',',$IdList);

$sqlw = '';
if($sKey!=''){
	switch ($sType){
		case 1:
			$sqlw .= " and Vc_nickname like '%" . $sKey . "%'";
			break;
		case 2:
			$sqlw .= " and Vc_truename like '%" . $sKey . "%'";
			break;
		case 3:
			$sqlw .= " and Vc_Email like '%" . $sKey . "%'";
			break;
		case 4:
			$sqlw .= " and Vc_mobile='" . $sKey . "'";
			break;
	}
}
if (1==$isAuthenticated) {
	$sqlw .= " and length(Vc_truename) > 0 and length(Vc_openid) > 0 " ;
}
$tables = 'user_base where Status>0 '.$sqlw.'';
$sql = "SELECT * FROM {$tables} order by ID desc";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'ID', 'wid'=>'');
$ths[]=array('val'=>'昵称', 'wid'=>'');
$ths[]=array('val'=>'姓名', 'wid'=>'');
$ths[]=array('val'=>'邮箱', 'wid'=>'');
$ths[]=array('val'=>'手机', 'wid'=>'');
$ths[]=array('val'=>'注册时间', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td>'. $Rs[$i]['ID'] .'</td>';
		$_td .= '<td><a href="MemberInfo.php?Id='.$Rs[$i]['ID'].'" class="hs" h="" title="会员详细页">'.$Rs[$i]['Vc_nickname'].'</a></td>';
		$_td .= '<td>'. $Rs[$i]['Vc_truename'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['Vc_Email'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['Vc_mobile'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns[] = '';
//$extend['gbtns'] = '';
$helps  = array();
$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
//$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);

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

$tpl->draw('listsearch'.$raintpl_ver);
exit;
}
?>