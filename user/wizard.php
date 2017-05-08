<?php

if(!defined('WEBROOT')) exit;

$u_status = getUserStatus($lg);

$p['icy']=$u_status['icy'];
if ($u_status['authEmail'] != 1) {
	$p['step'] = 1;
} else if ($u_status['authMobile'] != 1) {
	$p['step'] = 2;
} else if ($u_status['isopen'] != 1) {
	$p['step'] = 3;
	//addbyさくら20141208获取双乾注册跳转连接begin
	if($p['icy']==1){
		require_once(WEBROOT.'pay'.L.'moneymm'.L.'api'.L.'api.class.php');
		$Api = getApiClass();
		$p['mmm_reg']=$Api->api_config['goto_url']['regist'];
	}
	//さくらend
} else if ($u_status['allocation_type'] != 1) {
	$p['step'] = 4;
} else {
	$p['step'] = 5;
	header('location: /index.php?act=user');
}

switch ($p['step']) {
	case 1:
		$m .= '_step1';
		break;
	
	case 2:
		$m .= '_step2';
		break;

	case 3:
		$m .= '_step3';
		if($p['icy']==1){
			$m .= 'company';
		}
		break;

	case 4:
		$m .= '_step4';
		break;

	default:
		# code...
		break;
}

