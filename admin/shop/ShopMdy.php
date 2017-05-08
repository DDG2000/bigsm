<?php
if(1){

require_once('../include/TopFile.php');

require(WEBROOTINCCLASS.'Shop.php');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$title = '店铺';
$points = array('商家管理', $title.'管理');
$action = 'ShopProcess.php';
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


$Admin->CheckPopedoms('SM_SHOP_MDY');//店铺审核
$def_itemclasslist = 1;//默认品名分类
switch($Work){
	case 'MdyReco':/**编辑**/
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		
// 		$Rs = $DataBase->GetResultOne('select * from sm_item_class where id='.$Id.' limit 0,1');
		$objShop = new Shop();
		$Rs = $objShop->getInfo($Id);
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		
		$r1=$objShop->getMallClassList($Rs['Vc_mall_classIds']);
		
		
		
		$r2=$objShop->getItemClassList($Rs['Vc_itemIds']);
// 		dump($r2);
// 		exit;
		
		$title = '审核'.$title;
		$hides['Id'] = $Id;
		
		break;
	
	default:
		die('没有该操作类型!');	
}

if($Work=='MdyReco'){
    $slogourl = empty($Rs['Vc_logo_pic'])?'':'<div class="imgMdy"><a class="mdyimg" href="'.$Config->WebRoot.$Rs['Vc_logo_pic'].'" title="点击查看原图" target="_blank"><img src="'.$Config->WebRoot.$Rs['Vc_logo_pic'].'"></a></div>';
    $Vc_identity_pic1 = empty($Rs['Vc_identity_pic1'])?'':'<div class="imgMdy"><a class="mdyimg" href="'.$Config->WebRoot.$Rs['Vc_identity_pic1'].'" title="点击查看原图" target="_blank"><img src="'.$Config->WebRoot.$Rs['Vc_identity_pic1'].'"></a></div>';
    $Vc_identity_pic2 = empty($Rs['Vc_identity_pic2'])?'':'<div class="imgMdy"><a class="mdyimg" href="'.$Config->WebRoot.$Rs['Vc_identity_pic2'].'" title="点击查看原图" target="_blank"><img src="'.$Config->WebRoot.$Rs['Vc_identity_pic2'].'"></a></div>';
    $Vc_licence_pic = empty($Rs['Vc_licence_pic'])?'':'<div class="imgMdy"><a class="mdyimg" href="'.$Config->WebRoot.$Rs['Vc_licence_pic'].'" title="点击查看原图" target="_blank"><img src="'.$Config->WebRoot.$Rs['Vc_licence_pic'].'"></a></div>';
    $Vc_tax_pic = empty($Rs['Vc_tax_pic'])?'':'<div class="imgMdy"><a class="mdyimg" href="'.$Config->WebRoot.$Rs['Vc_tax_pic'].'" title="点击查看原图" target="_blank"><img src="'.$Config->WebRoot.$Rs['Vc_tax_pic'].'"></a></div>';
    $Vc_org_pic = empty($Rs['Vc_org_pic'])?'':'<div class="imgMdy"><a class="mdyimg" href="'.$Config->WebRoot.$Rs['Vc_org_pic'].'" title="点击查看原图" target="_blank"><img src="'.$Config->WebRoot.$Rs['Vc_org_pic'].'"></a></div>';


$params[] = array('val'=> $slogourl,'name'=>'公司logo','tip'=>'');
$params[] = array('val'=>iset($Rs['Vc_name']),'name'=>'公司名称','tip'=>'');

$I_type='<input type="radio" name="I_type" value="1" '.$FLib->isChecked($Rs['I_type'],1).'/> 自营商铺&nbsp;
        <input type="radio" name="I_type" value="2" '.$FLib->isChecked($Rs['I_type'],2).'/> 撮合市场商铺&nbsp;';
     

$params['I_type'] = array('val'=>$I_type,'name'=>'商铺类型','tip'=>'','attrs'=>'isc="nums" maxlength="8" ');


$params[] = array('val'=>iset($Rs['proname']).iset($Rs['cityname']).iset($Rs['disname']).iset($Rs['Vc_address']),'name'=>'公司地址','tip'=>'');
$params[] = array('val'=>iset($Rs['Vc_phone']),'name'=>'公司电话','tip'=>'');
$params[] = array('val'=>iset($Rs['Vc_fax']),'name'=>'公司传真','tip'=>'');
$params[] = array('val'=>iset($Rs['Vc_contact']),'name'=>'联系人','tip'=>'');
$params[] = array('val'=>iset($Rs['Vc_contact_phone']),'name'=>'联系人电话','tip'=>'');

// $Vc_mall_classIds='<input type="checkbox" name="mall_class" checked  >';
$Vc_mall_classIds=',';
$mall_class='';
foreach ($r1 as $v){
    $mall_class=$mall_class.$v['Vc_name'].$Vc_mall_classIds;

}
$params[] = array('val'=>$mall_class,'name'=>'经营范围','tip'=>'');


// $Vc_itemIds='<input type="checkbox" name="items" checked  >';
$Vc_itemIds=',';
$items='';
foreach ($r2 as $v){
    $items=$items.$v['Vc_name'].$Vc_itemIds;
    
}
$params[] = array('val'=>$items,'name'=>'经营类别','tip'=>'');


$params[] = array('val'=>'','name'=>'公司简介','tip'=>'');
$params[] = array('val'=>'<textarea id="KE_content" name="content" style="width:680px;height:400px">'.iset($Rs['T_desc']).'</textarea><script type="text/javascript">KindEditor.ready(function(K) { K.create(\'#KE_content\', {});}); $(function(){$(\'a[name=reset]\').click(function(){self.location.reload(); });})</script>','name'=>'','tip'=>'');
$params[] = array('val'=>iset($Rs['Vc_corporation']),'name'=>'法人姓名','tip'=>'');
$params[] = array('val'=>iset($Rs['Vc_identity']),'name'=>'法人身份证号','tip'=>'');
$params[] = array('val'=>$Vc_identity_pic1,'name'=>'身份证正面','tip'=>'');
$params[] = array('val'=>$Vc_identity_pic2,'name'=>'身份证反面','tip'=>'');
$params[] = array('val'=>$Vc_licence_pic,'name'=>'营业执照','tip'=>'');
$params[] = array('val'=>$Vc_tax_pic,'name'=>'税务登记证','tip'=>'');
$params[] = array('val'=>$Vc_org_pic,'name'=>'组织机构代码证','tip'=>'');

// $params[] = array('val'=>'','name'=>'认证状态','tip'=>'','attrs'=>'isc="nums"');

    $status='<input type="radio" name="I_cert_status" value="0" '.$FLib->isChecked($Rs['I_cert_status'],0).'/> 没有申请认证&nbsp;
        <input type="radio" name="I_cert_status" value="1" '.$FLib->isChecked($Rs['I_cert_status'],1).'/> 在认证中&nbsp;
        <input type="radio" name="I_cert_status" value="2" '.$FLib->isChecked($Rs['I_cert_status'],2).'/> 认证退回&nbsp;
        <input type="radio" name="I_cert_status" value="3" '.$FLib->isChecked($Rs['I_cert_status'],3).'/> 认证通过&nbsp;
        <input type="radio" name="I_cert_status" value="4" '.$FLib->isChecked($Rs['I_cert_status'],4).'/>认证不通过';

$params[''] = array('val'=>$status,'name'=>'认证状态','tip'=>'','ty'=>'radio', );
// $params[''] = array('val'=>$status,'name'=>'认证状态','tip'=>'','ty'=>'radio','attrs'=>'isc="nums" maxlength="8" ');

$params[] = array('val'=>iset($Rs['Dt_open']),'name'=>'开店日期','tip'=>'');
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
