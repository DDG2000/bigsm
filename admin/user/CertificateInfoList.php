<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2014-6-8
**本页： 担保公司 图片json类管理 管理   //T_image 1-多图-每张图有大小两张图
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER');
$isMdy = $Admin->CheckPopedom('SC_MEMBER');


$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType ;
$cId = $FLib->RequestInt('cId',0,9,'ID');

$sqlw = 'from p2p_user_certificate a left join p2p_certificate b on a.I_certificateID=b.ID left join user_base u on a.I_userID=u.ID where a.Status=1 and b.Status=1 and a.ID='.$cId;
$sql = 'select a.*,b.Vc_name,b.I_type,u.Vc_nickname '.$sqlw.'';
$vv = $DataBase->GetResultOne($sql);
if(!$vv){ echo showErr('记录未找到'); exit; }
$certid = $vv['I_certificateID'];
$uid = $vv['I_userID'];

$title = $vv['Vc_nickname'].'['.$vv['Vc_name'].']的认证资料';
$points = array('会员管理','会员认证', $title.'列表' );
$sTypes = array();
$hides  = array();
$extend = array();

$hides['cId'] = $cId;
$UrlInfo .= '&cId='.$cId;

$ths = array();
$ths[]=array('val'=>'序号', 'wid'=>'');
$ths[]=array('val'=>'图片', 'wid'=>'');

$Rs = jsonstr_to_array($vv['Vc_image']);
$tds = array();
if(is_array($Rs)){
	$pagecount = 1;
	$rscount = count($Rs);$extend['rscount']=$rscount;
	foreach($Rs as $k=>$o){
		$_td = '<td>'.($k+1).'</td>';
		$_td .= '<td><a href="'.$o.'" target="_blank"><img src="'.$o.'_s.jpg" style=""></a></td>';
		$tds[$k]=$_td;
    }
}
$DataBase->CloseDataBase();
if($isMdy){
$btns[] = '<a href="MemberCertificateProcess.php?Work=Del2Reco&uid='.$uid.'&certid='.$certid.'&IdList=" class="del" rel="IdList">删 除</a>';
$extend['gbtns'][] = '<a href="MemberCertificateMdy.php?Work=AddReco&uid='.$uid.'&certid='.$certid.'"><span>添加'.$title.'</span></a>';
}
$helps  = array();
$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
$FLib->AdminSetcookie('backurl1',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);

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
