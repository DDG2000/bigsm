<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 网站介绍 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$t = '';
$points = array('网站管理', '管理');
$action = 'ArticleProcess.php';
$hides  = array('Work'=>$Work, 'type'=>$type);
$params = array();
$helps  = array();
$extend = array('multipart'=>1,'js'=>'
<script src="../include/kindeditor/kindeditor-min.js" type="text/javascript"></script>
<script src="../include/kindeditor/lang/zh_CN.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="../include/timepicker/css/jquery-ui.css" >
<script type="text/javascript" src="../include/timepicker/js/jquery-ui.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-slide.min.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">$(function(){$(\'#ftime\').datetimepicker();});</script>
');
/*
<script src="../include/calendar/Calendar.js" type="text/javascript"></script>

<link type="text/css" rel="stylesheet" href="../include/calendar/calendar.css" >
<script type="text/javascript" src="../include/upload/js/jquery.upload.js"></script>
<script type="text/javascript">	
$(function(){
	new upload().init("#swfup1","fpath",{file_size_limit:"'. ($g_conf['cfg_yunxutupiandaxiao']/1024) .' MB", file_types:"*.'.str_replace(',',';*.',$g_conf['cfg_yunxutupianleixing']).'",file_upload_limit:1,PHPSESSID:"'.session_id().'"});
	$(".upfile .change").hide();
	$(".upfile input:hidden").change(function(){$(this).parent().parent().parent(".upfile").find(".imgMdy").hide();});
});
</script>
*/
$Admin->CheckPopedoms('ES_ARTICLE_MDY');
switch($Work){
	case 'MdyReco':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from es_article where Status=1 and ID='.$Id.' limit 0,1');
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑';
		$hides['Id'] = $Id;
		$Parent = $Rs['I_classID'];
		break;
	case 'MdyReco2':
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from es_article where Status=1 and ID='.$Id.' limit 0,1');
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑';
		$hides['Id'] = $Id;
		$Parent = $Rs['I_classID'];
		break;
	case 'AddReco':
		$Parent = $FLib->RequestInt('cid',0,9,'classID');
		$Rs = array();
		$title = '添加';
		$classID=0;
		$Rs['I_order']=100;
		$Rs['Dt_release']=date('Y-m-d H:i');
		break;
	default:
		die('没有该操作类型!');	
}
if($Work=='AddReco'){
	$options='<option value="0" '.($v[0] == $Parent?'selected="selected"':'').'>请选择</option>';
	$Rs1 = $DataBase->GettArrayResult('select ID,Vc_name from es_article_class where Status=1 order by id');
	foreach($Rs1 as $v){
		if($v[0] == $Parent){
			$t=$v[1];
			$options .= '<option value="'.$v[0].'" selected="selected">'.$v[1].'</option>';
		}else{
			$options .= '<option value="'.$v[0].'" >'.$v[1].'</option>';
		}
		
	}
	//$params[] = array('val'=>'<select name="classID" class="sel_put2 chzn-select" isc="select0Fun" onChange="articleClassChangeFun(this.value)" >'.iset($options).'</select>','name'=>'所属分类','tip'=>'');
	$params[] = array('val'=>$t.'<input type="hidden" name="classID" value="'.$Parent.'">','name'=>'所属分类');
}else{
	$Rs1 = $DataBase->GetResultOne("SELECT ID,Vc_name FROM es_article_class where Status=1 and ID=".$Parent);
	$t=$Rs1['Vc_name'];
	$params[] = array('val'=>iset($Rs1['Vc_name']).'<input type="hidden" name="classID" value="'.$Parent.'">','name'=>'所属分类');
}
$params['title'] = array('val'=>iset($Rs['Vc_name']),'name'=>'文章标题','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="50"');
//$params['Vc_author'] = array('val'=>iset($Rs['Vc_author']),'name'=>'作者','tip'=>'','ty'=>'text','attrs'=>'maxlength="50"');
$params['Vc_source'] = array('val'=>iset($Rs['Vc_source']),'name'=>'文章来源','tip'=>'','ty'=>'text','attrs'=>'maxlength="100"');
//$params['Vc_tags'] = array('val'=>iset($Rs['Vc_tags']),'name'=>'标签','tip'=>'','ty'=>'text','attrs'=>'maxlength="50"');
if($t==''){$t='网站介绍';}
$hides['tname']=$t;
$title.=$t;
$points[1]=$t.$points[1];
//$surl='';
//if($Work=='MdyReco'){$surl = empty($Rs['Vc_picture'])?'':'<div class="imgMdy"><a class="mdyimg" href="'.$Config->WebRoot.$Rs['Vc_picture'].'" title="点击查看原图" target="_blank"><img src="'.$Config->WebRoot.$Rs['Vc_picture'].'"></a><a class="change">更换</a></div>';}
//$params[] = array('val'=>'<div class="upfile">'.$surl.'<div id="swfup1" class="swfup"></div></div>','name'=>'代表图片','tip'=>'图片限定'.$g_conf['cfg_yunxutupianleixing']);

$params[] = array('val'=>'<input name="time" type="text" class="txt_put2" id="ftime" value="'.$FLib->FromatDate(iset($Rs['Dt_release']),'Y-m-d H:i').'" isc="datepatFun" nofocus="">','name'=>'发布日期','tip'=>'前台显示该文章发布时间');
$params['order'] = array('val'=>iset($Rs['I_order']),'name'=>'排序号','tip'=>'前台显示按降序排列','ty'=>'text','attrs'=>'isc="nums" maxlength="8" ');
$params[] = array('val'=>'','name'=>'文章内容','tip'=>'');
$params[] = array('val'=>'<textarea id="KE_content" name="content" style="width:680px;height:400px">'.iset($Rs['T_content']).'</textarea><script type="text/javascript">KindEditor.ready(function(K) { K.create(\'#KE_content\', {});}); $(function(){$(\'a[name=reset]\').click(function(){self.location.reload(); });})</script>','name'=>'','tip'=>'');


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
