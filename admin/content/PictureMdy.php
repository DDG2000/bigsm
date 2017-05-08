<?php
if(1){
/****************************************************************** 
**创建者：zy
**创建时间：2016-06-20
**本页： 图片 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

//$type = $FLib->requestint('type',1,9,'类别');
//$typearr = array('t_1'=>'图片图片','t_2'=>'flash图片','t_3'=>'文字图片');//key 需要根据类别值改变

$t = '图片';
$points = array('网站管理', $t.'管理');
$action = 'PictureProcess.php';
$hides  = array('Work'=>$Work, 'type'=>$type);
$params = array();
$helps  = array();
//$extend = array();
if($type<3){
$extend['multipart'] = 1;
$filetype = '*.'.str_replace(',',';*.',$g_conf['cfg_yunxutupianleixing']);
if($type==2){$filetype='*.swf';}
$extend['js'] = '
<script type="text/javascript" src="../include/upload/js/jquery.upload.js"></script>
<script src="../include/kindeditor/kindeditor-min.js" type="text/javascript"></script>
<script src="../include/kindeditor/lang/zh_CN.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="../include/timepicker/css/jquery-ui.css" >
<script type="text/javascript" src="../include/timepicker/js/jquery-ui.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-slide.min.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">$(function(){$(".ftime").datetimepicker();});</script>
<script type="text/javascript">	
$(function(){
	new upload().init("#swfup1","fpath",{file_size_limit:"100 MB", file_types:"'.$filetype.'",file_upload_limit:1,PHPSESSID:"'.session_id().'"});
	$(".upfile .change").hide();
	$(".upfile input:hidden").change(function(){$(this).parent().parent().parent(".upfile").find(".imgMdy").hide();});
});
</script>
';
}

$Admin->CheckPopedoms('SC_SITE_AD_MDY');
switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from site_advertise where Status=1 and ID='.$Id.' limit 0,1');
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		$Parent= $Rs['I_placeID'];
		//$type = $Rs['I_type'];
		break;
	case 'AddReco':
		$Rs = array();
		$title = '添加'.$t;
		break;
	default:
		die('没有该操作类型!');	
}
//$marks = '';
//foreach($typearr as $k=>$v){$marks.='<a href="?Work='.$Work.'&type='.substr($k,2).'&Id='.iset($Id).'" '.(substr($k,2)==$type?'class="cur"':'').'>'.$v.'</a>';}
//$extend['marks'] = $marks;

$wh='';
$wharr = array('id_0'=>'');
$options='<option value="0" '.($v[0] == $Parent?'selected="selected"':'').'>请选择 </option>';
$Rs1 = $DataBase->GettArrayResult('select ID,Vc_name,I_width,I_height from site_advertise_place where Status=1 order by id');
foreach($Rs1 as $v){
	$options .= '<option value="'.$v[0].'" '.($v[0] == $Parent?'selected="selected"':'').'>'.$v[1].'</option>';
	$wharr['id_'.$v[0]]=$v[2].'*'.$v[3];
	if($v[0]==$Parent) {$wh=$v[2].'*'.$v[3];}
}

$params['title'] = array('val'=>iset($Rs['Vc_name']),'name'=>'图片名称','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="20"');
$params['link'] = array('val'=>iset($Rs['Vc_link']),'name'=>'链接地址','tip'=>'点击图片链接到的页面http://**','ty'=>'text','attrs'=>'isc=""  maxlength="200"');
	if($Work=='MdyReco'){$surl = empty($Rs['Vc_original'])?'':'<div class="imgMdy">
		<a class="mdyimg" href="'.$Config->WebRoot.$Rs['Vc_original'].'" title="点击查看原图" target="_blank"><img src="'.$Config->WebRoot.$Rs['Vc_original'].'"></a>
		<a class="change">更换</a></div>';}
$params[] = array('val'=>'<div class="upfile">'.$surl.'<div id="swfup1" class="swfup"></div></div>尺寸为<span id="f_wh">'.$wh.'</span>','name'=>'代表图片','tip'=>'图片所显示的内容','attrs'=>'isc=""');
$params['intro'] = array('val'=>iset($Rs['Vc_name']),'name'=>'图片简介','tip'=>'','ty'=>'textarea','attrs'=>'isc="" maxlength="20"');
	if(isset($Rs['I_active']) && $Rs['I_active']==0){
		$status_var = '<input type="radio" name="show" value="1" >活动 <input type="radio" name="show" value="0" checked>禁用 ';
	}else{
		$status_var = '<input type="radio" name="show" value="1" checked>活动 <input type="radio" name="show" value="0" >禁用 ';
	}
$params['show'] = array('val'=>$status_var,'name'=>'状态','tip'=>'','ty'=>'radio','attrs'=>'');
$params['order'] = array('val'=>iset($Rs['I_order']),'name'=>'图片排序','tip'=>'默认100','ty'=>'text','attrs'=>'isc="" maxlength="20"');

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
