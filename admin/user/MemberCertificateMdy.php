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
$uid = $FLib->RequestInt('uid',0,9,'会员ID');
$certid = $FLib->RequestInt('certid',0,9,'认证项ID');
$u = $Db->fetch_one("select * from user_base where status>0 and ID=$uid");
if(!$u){echo showErr('用户未找到'); exit; }

$t = '会员认证';//['.$u['ID'].':'.$u['Vc_nickname'].']
$points = array('会员管理', $t.'管理');// 
$action = 'MemberCertificateProcess.php';
$hides  = array('Work'=>$Work, 'type'=>$type);
$params = array();
$helps  = array();
$extend = array();
$hides['uid'] = $uid;

$imgMn = iset($g_conf['cfg_auth_imgnum'],5);
$filetype = '*.'.str_replace(',',';*.',$g_conf['cfg_yunxutupianleixing']);
$extend['js'] = '
<script type="text/javascript" src="../include/upload/js/jquery.upload.js"></script>
<script type="text/javascript">	
$(function(){
	new upload().init("#swfup1","fpath",{file_size_limit:"100 MB", file_types:"'.$filetype.'",file_upload_limit:'.$imgMn.',PHPSESSID:"'.session_id().'",ty:1});
	$(".upfile .change").hide();
	$(".upfile input:hidden").change(function(){$(this).parent().parent().parent(".upfile").find(".imgMdy").hide();});
});
</script>
';

switch($Work){
	case 'AddReco':
		$Rs = array();
		$title = '添加'.$t;
		break;
	default:
		die('没有该操作类型!');
}
$points[] = $title;

$options='<option value="0" '.($v[0] == $certid?'selected="selected"':'').'>请选择 </option>';
$Rs1 = $DataBase->GettArrayResult('select ID,Vc_name from p2p_certificate where Status=1 order by I_order desc,id');
foreach($Rs1 as $v){
	$options .= '<option value="'.$v[0].'" '.($v[0] == $certid?'selected="selected"':'').'>'.$v[1].'</option>';
}

$params[] = array('val'=>$u['Vc_nickname'],'name'=>'会员昵称','tip'=>'');
$params[] = array('val'=>'<select name="certid" class="sel_put2 chzn-select" isc="select0Fun">'.$options.'</select>','name'=>'认证项','tip'=>'');
$params[] = array('val'=>'<label><input type="checkbox" name="pass" checked="checked" value="1" >勾选后直接通过，可以不用上传图片。</label>','name'=>'通过','tip'=>'');
$params[] = array('val'=>'<div class="upfile"><div id="swfup1" class="swfup"></div>可上传多张图片,最多'.$imgMn.'张</div>','name'=>'认证资料','tip'=>'','attrs'=>'');
$hides['I_userID'] = $uid;
$extend['btn_reset']=false;

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
