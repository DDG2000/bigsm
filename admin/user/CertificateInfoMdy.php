<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2014-6-8
**本页： 会员认证 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER');

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$cId = $FLib->RequestInt('cId',0,9,'认证ID');
$sqlw = 'from p2p_user_certificate a left join p2p_certificate b on a.I_certificateID=b.ID left join user_base u on a.I_userID=u.ID where a.Status=1 and b.Status=1 and a.ID='.$cId;
$sql = 'select a.*,b.Vc_name,b.I_type,u.Vc_nickname '.$sqlw.'';
$vv = $DataBase->GetResultOne($sql);
if(!$vv){ echo showErr('记录未找到'); exit; }

$t = $vv['Vc_nickname'].'['.$vv['Vc_name'].']的认证资料';
$points = array('会员管理','会员认证');//, $t.'管理'
$action = 'CertificateInfoProcess.php';
$hides  = array('Work'=>$Work, 'type'=>$type);
$params = array();
$helps  = array();
$extend = array();

$filetype = '*.'.str_replace(',',';*.',$g_conf['cfg_yunxutupianleixing']);
$extend['js'] = '
<script type="text/javascript" src="../include/upload/js/jquery.upload.js"></script>
<script type="text/javascript">	
$(function(){
	new upload().init("#swfup1","fpath",{file_size_limit:"100 MB", file_types:"'.$filetype.'",file_upload_limit:1,PHPSESSID:"'.session_id().'",ty:1});
	$(".upfile .change").hide();
	$(".upfile input:hidden").change(function(){$(this).parent().parent().parent(".upfile").find(".imgMdy").hide();});
});
</script>
';

switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		break;
	case 'AddReco':
		$Rs = array();
		$title = '添加'.$t;
		break;
	default:
		die('没有该操作类型!');
}
$params[] = array('val'=>$vv['Vc_nickname'],'name'=>'认证会员','tip'=>'');
$params[] = array('val'=>$vv['Vc_name'],'name'=>'认证项','tip'=>'');
if($Work=='AddReco'){
	
}else{
	
}
$imgda = jsonstr_to_array($vv['Vc_image']);
$surl = '';
if($Work=='MdyReco' && is_array($imgda)){
	if(!isset($imgda[$Id])){ echo showErr('图片记录未找到'); exit; }
	$surl = '<div class="imgMdy">';
	foreach($imgda as $k=>$v){
		if($k==$Id){
			$surl .='<a class="mdyimg" href="'.$v.'" target="_blank"><img src="'.$v.'" style="width:300px"></a>';
		}
	}
	$surl .='<a class="change">更换</a></div>';
}
$params[] = array('val'=>'<div class="upfile">'.$surl.'<div id="swfup1" class="swfup"></div>上传单张图片</div>','name'=>'认证资料','tip'=>'','attrs'=>'isc=""');
$extend['btn_reset']=false;

$hides['cId'] = $cId;
$points[] = $title;

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'action', $action );
$tpl->assign( 'hides', $hides );
$tpl->assign( 'params', $params );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('mdy'.$raintpl_ver);
exit;
}
?>
