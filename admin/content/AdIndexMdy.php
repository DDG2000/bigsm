<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 首页轮播 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$t = '首页轮播';
$points = array('网站管理', $t.'管理');
$action = 'AdIndexProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();

//$extend['multipart'] = 1;
$filetype = '*.'.str_replace(',',';*.',$g_conf['cfg_yunxutupianleixing']);
$extend['js'] = '
<script type="text/javascript" src="../include/upload/js/jquery.upload.js"></script>
<script type="text/javascript">	
$(function(){
	new upload().init("#swfup1","fpath",{file_size_limit:"100 MB", file_types:"'.$filetype.'",file_upload_limit:1,PHPSESSID:"'.session_id().'"});
	$(".upfile .change").hide();
	$(".upfile input:hidden").change(function(){$(this).parent().parent().parent(".upfile").find(".imgMdy").hide();});
});
</script>
';


$Admin->CheckPopedoms('SC_SITE_ADROLL');
switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from es_roll where ID='.$Id.' limit 0,1');
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		break;
	case 'AddReco':
		$Rs = array();
		$Rs['I_order'] = 100;
		$title = '添加'.$t;
		break;
	default:
		die('没有该操作类型!');	
}
$params['title'] = array('val'=>iset($Rs['Vc_name']),'name'=>'名称','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="20"');
$params['link'] = array('val'=>iset($Rs['Vc_url']),'name'=>'链接地址','tip'=>'点击链接到的页面http://**','ty'=>'text','attrs'=>' maxlength="200"');
if($Work=='MdyReco'){
	$surl = empty($Rs['Vc_image'])?'':'<div class="imgMdy"><a class="mdyimg" href="'.$Config->WebRoot.$Rs['Vc_image'].'" title="点击查看原图" target="_blank"><img src="'.$Config->WebRoot.$Rs['Vc_image'].'"></a><a class="change">更换</a></div>';}
$params[] = array('val'=>'<div class="upfile">'.$surl.'<div id="swfup1" class="swfup"></div></div>最佳尺寸为<span id="f_wh">1920X330</span>','name'=>'代表图片','tip'=>'广告所显示的内容','attrs'=>'isc=""');
$params['order'] = array('val'=>iset($Rs['I_order']),'name'=>'排序号','tip'=>'前台显示按降序排列','ty'=>'text','attrs'=>'isc="nums" maxlength="8" ');
if($Work=='MdyReco'&&$Rs['I_type']==2){
	$type='<input type="radio" name="type" value="1" /> 大图&nbsp;<input type="radio" name="type" value="2" checked/>小图';
}else{
	$type='<input type="radio" name="type"  value="1" checked> 大图&nbsp;<input type="radio" name="type" value="2"/>小图';
}
$params['type'] = array('val'=>$type,'name'=>'图片位置','tip'=>'广告在页面的位置','ty'=>'radio','attrs'=>'isc="nums" maxlength="8" ');
if($Work=='MdyReco'&&$Rs['Status']==0){
	$status='<input type="radio" name="status" value="1" /> 显示&nbsp;<input type="radio" name="status" value="0" checked="checked"/>不显示';
}else{
	$status='<input type="radio" name="status"  value="1" checked="checked"/> 显示&nbsp;<input type="radio" name="status" value="0"/>不显示';
}
$params['status'] = array('val'=>$status,'name'=>'图片状态','tip'=>'是否显示','ty'=>'radio','attrs'=>'isc="nums" maxlength="8" ');



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
