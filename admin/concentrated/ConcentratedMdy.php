<?php
if(1){

require_once('../include/TopFile.php');

require(WEBROOTINCCLASS.'Concentrated.php');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$title = '详情';
$points = array('集采管理', $title.'管理');
$action = 'ConcentratedProcess.php';
$hides  = array('Work'=>$Work);
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


$Admin->CheckPopedoms('SM_CONCENTRATED_MDY');//店铺审核
$def_itemclasslist = 1;//默认品名分类
$objConcentrated = new Concentrated();
switch($Work){
	case 'MdyReco':/**编辑**/
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$uid = $FLib->RequestInt('uid',0,9,'I_buyerID');
		
// 		$Rs = $DataBase->GetResultOne('select * from sm_item_class where id='.$Id.' limit 0,1');
		
		$Rs = $objConcentrated->getInfo($Id,$uid);
		if(!$Rs){ echo showErr('记录未找到'); exit; }
// 		$r1=$objConcentrated->getMallClassList($Rs['Vc_mall_classIds']);
		
		$r2=$objConcentrated->getItemList($Rs['Vc_itemIds']);

		
		$title = '查看'.$title;
		$hides['Id'] = $Id;
		
		break;
	case 'MdyReco2':/**报名商家**/
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		
// 		$Rs = $DataBase->GetResultOne('select * from sm_item_class where id='.$Id.' limit 0,1');
		
		$Rs = $objConcentrated->getRelatedInfo($Id);
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		
// 		$r1=$objShop->getMallClassList($Rs['Vc_mall_classIds']);

		
		$title = '查看报名商家'.$title;
		$hides['Id'] = $Id;
		
		break;
	
	default:
		die('没有该操作类型!');	
}

if($Work=='MdyReco'){
   

$params[] = array('val'=>iset($Rs['Vc_name']),'name'=>'集采名称','tip'=>'');

$params[] = array('val'=>iset($Rs['itemclassname']),'name'=>'类型','tip'=>'');

$Vc_itemIds=',';
$items='';
foreach ($r2 as $v){
    $items=$items.$v['Vc_name'].$Vc_itemIds;

}
$params[] = array('val'=>$items,'name'=>'集采品名','tip'=>'');

$params[] = array('val'=>iset($Rs['N_weight']),'name'=>'总重量/吨','tip'=>'');
$params[] = array('val'=>iset($Rs['proname']).iset($Rs['cityname']).iset($Rs['disname']).iset($Rs['Vc_address']),'name'=>'项目地址','tip'=>'');
$params[] = array('val'=>$Rs['D_start'].'至'.$Rs['D_end'],'name'=>'集采期限','tip'=>'');
$params[] = array('val'=>iset($Rs['Createtime']),'name'=>'发布时间','tip'=>'');

// $Vc_mall_classIds='<input type="checkbox" name="mall_class" checked  >';


$params[] = array('val'=>'','name'=>'中标公告','tip'=>'');
$params[] = array('val'=>'<textarea id="KE_content" name="content" style="width:680px;height:400px">'.iset($Rs['T_announcement']).'</textarea><script type="text/javascript">KindEditor.ready(function(K) { K.create(\'#KE_content\', {});}); $(function(){$(\'a[name=reset]\').click(function(){self.location.reload(); });})</script>','name'=>'','tip'=>'');


// $params[] = array('val'=>'','name'=>'认证状态','tip'=>'','attrs'=>'isc="nums"'); 
  
    $status='<input type="radio" name="I_status" value="0" '.$FLib->isChecked($Rs['I_status'],10).'/> 待审核&nbsp;
        <input type="radio" name="I_status" value="1" '.$FLib->isChecked($Rs['I_status'],20).'/> 招标中&nbsp;
        <input type="radio" name="I_status" value="2" '.$FLib->isChecked($Rs['I_status'],30).'/> 已截至&nbsp;
        <input type="radio" name="I_status" value="3" '.$FLib->isChecked($Rs['I_status'],40).'/> 已成交&nbsp;
        <input type="radio" name="I_status" value="4" '.$FLib->isChecked($Rs['I_status'],50).'/>审核不通过';

$params[''] = array('val'=>$status,'name'=>'状态','tip'=>'','ty'=>'radio', );
// $params[''] = array('val'=>$status,'name'=>'认证状态','tip'=>'','ty'=>'radio','attrs'=>'isc="nums" maxlength="8" ');

// $params[] = array('val'=>'<input name="Dt_open" type="text" class="txt_put2" id="ftime" value="'.$FLib->FromatDate(iset($Rs['Dt_open']),'Y-m-d H:i').'" isc="datepatFun" nofocus="">','name'=>'开店日期','tip'=>'即审核认证通过后的日期');


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
