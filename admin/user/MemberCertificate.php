<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2014-6-8
**本页： 会员自定义认证 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER');

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$uid = $FLib->RequestInt('uid',0,9,'会员ID');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType."&uid=" . $uid ;

$u = $Db->fetch_one("select * from user_base where status>0 and ID=$uid");
if(!$u){echo showErr('用户未找到'); exit; }

$title = '会员认证';//['.$u['ID'].':'.$u['Vc_nickname'].']自定义
$points = array('会员管理', $title.'管理' );
$sTypes = array('', '认证类型');
$hides  = array();
$extend = array();
$hides['uid'] = $uid;
$extend['js']='
<style>
.tdd{width:;}
.tdd img{max-width:80px;max-height:80px;_width:80px;_height:80px;}
</style>
';

$mWhere = '';
switch ($sType){
	case 1:
		$mWhere .= " and b.Vc_name like '%" . $sKey . "%'";
		break;
}
$mWhere .= " and a.I_userID=$uid";
$sqlw = 'from p2p_user_certificate a left join p2p_certificate b on a.I_certificateID=b.ID where a.Status=1 and b.Status=1'.$mWhere;
$sql = 'select a.*,b.Vc_name,b.I_type '.$sqlw.' order by a.id desc';
$sqlcount = 'select count(*) '.$sqlw;
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'认证项', 'wid'=>'10%');
$ths[]=array('val'=>'类别', 'wid'=>'80');
$ths[]=array('val'=>'认证图片', 'wid'=>'');
$ths[]=array('val'=>'认证时间', 'wid'=>'100');
$ths[]=array('val'=>'操作', 'wid'=>'80');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		//$_td = '<td><a href="MemberCertificateInfo.php?Id='.$Rs[$i]['ID'].'" class="hs" title="会员认证详细页">'.$Rs[$i]['Vc_name'].'</a></td>';
		$_td = '<td>'.$Rs[$i]['Vc_name'].'</td>';
		$_td .= '<td>'.($Rs[$i]['I_type']==1?'必要':'可选').'</td>';
		$imgs = jsonstr_to_array($Rs[$i]['Vc_image']);
		$img=array();
		foreach($imgs as $o){
			$img[] = '<a href="'.$o.'" target="_blank"><img src="'.$o.'_s.jpg" style=""></a>';
		}
		$_td .= '<td><div class="tdd">'.join('',$img).'</div></td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$_td .= '<td><a href="CertificateInfoList.php?cId='.$Rs[$i]['ID'].'" class="hs" w="750" h="450" title="'.$Rs[$i]['Vc_name'].'管理页">管理</a></td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns[] = '<a href="MemberCertificateProcess.php?Work=DelReco&IdList=" class="del" rel="IdList">删 除</a>';
$extend['gbtns'][] = '<a href="MemberCertificateMdy.php?Work=AddReco&uid='.$uid.'" class="hs" w="750" h="450" title="添加'.$title.'"><span>添加'.$title.'</span></a>';
$extend['gbtns'][] = '<a href="javascript:;" onclick="location.reload();">刷新</a>';

$helps  = array();
$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
$FLib->AdminSetcookie('backurl3',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);

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
