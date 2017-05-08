<?php
require_once('../include/TopFile.php');

include_once(WEBROOTINCCLASS . 'Sns.php');
$Sns = new Sns();

$mobile = $FLib->requeststr('Vc_mobile',0,50,'手机号',1,1);

$action = $FLib->requeststr('action',1,50,'操作类型',1,1);

if ($action == 'chkExistsMobile') {$t = "绑定手机";}
/**
 * @author zy
 * 发送手机验证码
 * url地址：
 * http://www.bigsm.com/index.php?act=user&m=sendmobilecode
 * 输入：
 * Vc_mobile;str 手机号
 *
 * 找回密码时:
 * err:int 结果状态 -1失败 0成功
 * msg: 提示信息
 * */
if ($action == 'chkExistsMobile') {
	//add 20140822 验证手机是否已注册
	$hasReged = $Db->fetch_one("select id from user_base where Vc_mobile={$mobile} ") ;
	if($hasReged) {
		w_email_msg_log($t, 'result:该手机号码已注册,mobile:'.$mobile);
		returnjson(array('err'=>-250,'msg'=>'该手机号码已被使用，请使用其他号码'));
	} else {
		returnjson(array('err'=>0,'msg'=>'ok'));
	}
}

if(isset($_SESSION['mobilecode_time'])){
	if($_SESSION['mobilecode_time'] > time()){
		returnjson(array('err'=>-102,'msg'=>'发送短信间隔中..'));
	}else{
		unset($_SESSION['mobilecode_time']);
	}
}

$code = generateCode(6);
$miute = 30;//时效
$r = $Sns->send_yzm($mobile, array('param1'=>$code,'param2'=>$miute));
w_email_msg_log($t, 'result:'.$r['msg'].',mobile:'.$mobile);
if($r['err']==1){
	$_SESSION['code'] = array($code,time()+$miute*60);
	$_SESSION['code_time'] = time()+60;
	$r['msg'] = '短信发送成功！请填写收到的验证码。';
}else{
	$r['err'] = $r['msg'];
	$r['msg'] = '短信发送失败！'.($r['err']==-503?'短信接口应用未提交审核！':'');
}
w_log('验证码:'.$code.' '.$r['msg']);
returnjson($r);
?>