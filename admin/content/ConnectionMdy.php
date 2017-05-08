<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页：友情链接 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

//$type = $FLib->requestint('type',2,9,'类别');
$type =2;
//$typearr = array('t_2'=>'文字连接','t_1'=>'图片连接');//key 需要根据类别值改变

$t = '友情链接';
$points = array('网站管理', $t.'管理');
$action = 'ConnectionProcess.php';
$hides  = array('Work'=>$Work, 'type'=>$type);
$params = array();
$helps  = array();
$extend = array();
if($type==1){
$extend['multipart'] = 1;
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
}

$Admin->CheckPopedoms('SC_SITE_LINK');
switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from es_link where Status=1 and ID='.$Id.' limit 0,1');
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		//$type = $Rs['I_type'];
		break;
	case 'AddReco':
		$Rs = array('I_order'=>100);
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

$params['title'] = array('val'=>iset($Rs['Vc_name']),'name'=>'网站名称','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="20"');
$params['link'] = array('val'=>iset($Rs['Vc_link']),'name'=>'链接地址','tip'=>'示例:http://**','ty'=>'text','attrs'=>'isc="" maxlength="200"');
$params['order'] = array('val'=>iset($Rs['I_order']),'name'=>'排序号','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="20"');
if($type==1){
	if($Work=='MdyReco'){$surl = empty($Rs['Vc_image'])?'':'<div class="imgMdy"><a class="mdyimg" href="'.$Config->WebRoot.$Rs['Vc_image'].'" title="点击查看原图" target="_blank"><img src="'.$Config->WebRoot.$Rs['Vc_image'].'"></a><a class="change">更换</a></div>';}
	$params[] = array('val'=>'<div class="upfile">'.$surl.'<div id="swfup1" class="swfup"></div></div>尺寸为<span id="f_wh">'.$wh.'</span>','name'=>'网站LOGO','tip'=>'网站代表图','attrs'=>'isc=""');
}
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
