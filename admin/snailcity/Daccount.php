<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 登录信息 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');
require_once(WEBROOT.'pay'.L.'moneymm'.L.'api'.L.'api.class.php');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}
$Api = getApiClass();

//print_r($dinfo);

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$Status  = $FLib->RequestInt('Status',0,9,'类型');

$I_entity = $FLib->RequestInt('I_entity',1,9,'分类');
$title = $I_entity == 1 ? 'C' : 'D' ;
$title .= '账户资金';
$points = array('系统管理', '当前用户', $title);
$action = 'DaccountProcess.php';
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$hides  = array('Work'=>$Work);
$hides['I_entity'] = $I_entity;
$params = array();
$extend = array();
$extend['js'] = '
<link type="text/css" rel="stylesheet" href="../include/timepicker/css/jquery-ui.css" >
<script type="text/javascript" src="../include/timepicker/js/jquery-ui.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-slide.min.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-timepicker-addon.js"></script>
';
$Id = $Admin->Uid;
$Rs = $DataBase->GetResultOne("select * from sc_user where Status=1 And ID=$Id limit 0,1");
if(!$Rs){ echo showErr('记录未找到'); exit; }
if($I_entity=='1'){
	$dinfo=$Api->balanceQuery($Api->api_config['PlatPay']);
	$params[] = array('name'=>'C账户平台账号余额','val'=>iset($dinfo[0]));
	//$params[] = array('name'=>'C账户能转入平台余额','val'=>$dinfo[1]-$dinfo[0]);
	$params[] = array('name'=>'充值方式','val'=>'<input type="radio" name="RechargeType" checked value="4">企业网银充值<input type="radio" name="RechargeType" value="3">汇款充值');
	
}
if($I_entity=='2'){
	$dinfo=$Api->balanceQuery($Api->api_config['PlatPay2']);
	$params[] = array('name'=>'D账户平台账号余额','val'=>iset($dinfo[0]));
	//$params[] = array('name'=>'D账户能转入平台余额','val'=>$dinfo[1]-$dinfo[0]);
	$params[] = array('name'=>'充值方式','val'=>'<input type="radio" name="RechargeType" checked value="">网银充值<input type="radio" name="RechargeType" value="3">汇款充值');
}
if($I_entity=='3'){
	$dinfo=$Api->balanceQuery($Api->api_config['PlatformMoneymoremore'],2);
	//var_dump($dinfo);
	$params[] = array('name'=>'管理费账号余额','val'=>iset($dinfo[1]));
	//$params[] = array('name'=>'D账户能转入平台余额','val'=>$dinfo[1]-$dinfo[0]);
	$params[] = array('name'=>'充值方式','val'=>'<input type="radio" name="RechargeType" checked value="4">企业网银充值<input type="radio" name="RechargeType" value="3">汇款充值');
	
}
$params[] = array('name'=>'充值','val'=>"<input type='txt' name='Amount'>");


//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'action', $action );
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'params', $params );
$tpl->assign( 'hides', $hides );
$tpl->assign( 'extend', $extend );
$tpl->draw('daccount'.$raintpl_ver);
exit;
}
?>