<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 会员详细页 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}


$title = '会员详细页';
$points = array('会员管理', $title);
$hides  = array();
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_MEMBER');

$Id = $FLib->RequestInt('Id',0,9,'ID');
$Rs = $DataBase->GetResultOne('select * from user_base where ID='.$Id.' limit 0,1');
if(!$Rs){ echo showErr('记录未找到'); exit; }

$btime = time()-86400;
$params[] = array('name'=>'会员ID','val'=>iset($Rs['ID']),'tip'=>'');

if($Rs['I_company']==0){
//$params[] = array('name'=>'会员名','val'=>iset($Rs['Vc_name']),'tip'=>'');
$params[] = array('name'=>'昵称','val'=>iset($Rs['Vc_nickname']),'tip'=>'');

//$params[] = array('name'=>'密码提示问题','val'=>iset($Rs['Vc_question']),'tip'=>'');
//$params[] = array('name'=>'提示问题答案','val'=>iset($Rs['Vc_answer']),'tip'=>'');
//getUserFrom 
//$ufrom='本站';$partty=$Rs['I_partnertype'];$ducer=$Rs['Vc_introducer'];
//if($partty==1){$ufrom='新浪';}
//elseif($partty==2){$ufrom='QQ';}
//elseif($ducer!=''){
//	$pner = $DataBase->GetResultOne('select Vc_name,Vc_identity from site_partner where status=1 and Vc_identity="'.$ducer.'"');
//	$ufrom = $pner ? $pner[0]:'<span title="'.$ducer.'">未知</span>';
//}
//$params[] = array('name'=>'来自','val'=>$ufrom,'tip'=>'');

$params[] = array('name'=>'真实姓名','val'=>iset($Rs['Vc_truename']),'tip'=>'');
$params[] = array('name'=>'黑名单','val'=>$Rs['I_bad']==1?'黑名':'--','tip'=>'');
$params[] = array('name'=>'性别','val'=>($Rs["I_sex"]==1?"男":"女"),'tip'=>'');
$params[] = array('name'=>'出生日期','val'=>$FLib->fromatdate($Rs['Dt_birthday'],'Y-m-d'),'tip'=>'');
$params[] = array('name'=>'职务','val'=>iset($Rs['Vc_position']),'tip'=>'');


$us = checkUserStatus($Rs);
$usa = getUserStatus($Rs);
$params[] = array('name'=>'会员状态','val'=>$us['msg'],'tip'=>'');
$params[] = array('name'=>'是否是VIP','val'=>(is_null($Rs['Dt_expire'])?'否':'是'),'tip'=>'');
$params[] = array('name'=>'VIP过期时间','val'=>$FLib->fromatdate($Rs['Dt_expire'],'Y-m-d'),'tip'=>'');
$params[] = array('name'=>'是否开通双乾','val'=>$usa['isopen']==1?'开通':'未开通','tip'=>'');

//if($Admin->CheckPopedom('SC_EXTRA_MEMBERSEE')){
	$params[] = array('name'=>'<b>联系方式</b>','val'=>'','tip'=>'_Title_');
	$params[] = array('name'=>'身份证','val'=>iset($Rs['Vc_identity']),'tip'=>'');
	//$params[] = array('name'=>'邮政编码','val'=>iset($Rs['Vc_postalcode']),'tip'=>'');
	//$params[] = array('name'=>'通信地址','val'=>iset($Rs['Vc_address']),'tip'=>'');
	$params[] = array('name'=>'手机号码','val'=>iset($Rs['Vc_mobile']),'tip'=>'');
	//$params[] = array('name'=>'办公电话','val'=>iset($Rs['Vc_officetel']),'tip'=>'');
	//$params[] = array('name'=>'固定电话','val'=>iset($Rs['Vc_hometel']),'tip'=>'');
	//$params[] = array('name'=>'QQ号','val'=>iset($Rs['Vc_QQ']),'tip'=>'');
	//$params[] = array('name'=>'MSN号','val'=>iset($Rs['Vc_MSN']),'tip'=>'');
	$params[] = array('name'=>'Email','val'=>iset($Rs['Vc_Email']),'tip'=>'');
//}

}else{
	$jsonobj=jsonstr_to_array($Rs['T_json']);
	$params[] = array('name'=>'企业昵称','val'=>iset($Rs['Vc_nickname']),'tip'=>'');
	$params[] = array('name'=>'企业全称','val'=>iset($Rs['Vc_truename']),'tip'=>'');
	$params[] = array('name'=>'营业执照流水号','val'=>iset($Rs['Vc_identity']),'tip'=>'');
	$params[] = array('name'=>'手机号','val'=>iset($Rs['Vc_mobile']),'tip'=>'');
	$params[] = array('name'=>'邮箱','val'=>iset($Rs['Vc_Email']),'tip'=>'');
	$params[] = array('name'=>'注册日期','val'=>iset($jsonobj['regtime']),'tip'=>'');
	$params[] = array('name'=>'所属行业','val'=>iset($jsonobj['trade']),'tip'=>'');
	$params[] = array('name'=>'企业所在地','val'=>'<span id="province_val">'.iset($jsonobj['province']).'</span>&nbsp;<span id="city_val">'.iset($jsonobj['city']).'</span>','tip'=>'');
	$params[] = array('name'=>'企业规模','val'=>iset($jsonobj['asset']),'tip'=>'');
	$params[] = array('name'=>'联系人','val'=>iset($jsonobj['contacts']),'tip'=>'');
	$params[] = array('name'=>'联系电话1','val'=>iset($jsonobj['tel1']),'tip'=>'');
	$params[] = array('name'=>'联系电话2','val'=>iset($jsonobj['tel2']),'tip'=>'');
	$params[] = array('name'=>'联系地址','val'=>iset($jsonobj['address']),'tip'=>'');
	
}

	$params[] = array('name'=>'用户余额','val'=>iset($Rs['N_amount']),'tip'=>'');
	$params[] = array('name'=>'冻结金额','val'=>iset($Rs['N_amount_freeze']),'tip'=>'');


$params[] = array('name'=>'<b>系统记录</b>','val'=>'','tip'=>'_Title_');
$params[] = array('name'=>'注册时IP','val'=>$Rs['Vc_IP'],'tip'=>'');
$params[] = array('name'=>'登录次数','val'=>$Rs['I_logins'],'tip'=>'');
$params[] = array('name'=>'最后登录IP','val'=>iset($Rs['Vc_lastloginIP']),'tip'=>'');
$params[] = array('name'=>'最后登录时间','val'=>iset($Rs['Dt_lastlogintime']),'tip'=>'');
$params[] = array('name'=>'<b>状态记录</b>','val'=>'','tip'=>'_Title_');
$params[] = array('name'=>'账户锁定状态','val'=>($Rs['Status']==1?'未锁定':($Rs['Status']==2?'锁定':'删除')),'tip'=>'');
$params[] = array('name'=>'登陆锁定状态','val'=>($Rs['errLoginLockTime']>$btime?'锁定':'未锁定'),'tip'=>'');

if($Rs['I_company']<>0){
	if($Rs['I_audit']==0) $auditS='<font color="red">审核中</font>';
	elseif($Rs['I_audit']==1) $auditS='已通过';
	elseif($Rs['I_audit']==2) $auditS='未通过';
	
	$params[] = array('name'=>'审核状态','val'=>$auditS,'tip'=>'');
	//$params[] = array('name'=>'审核时间','val'=>$FLib->fromatdate($Rs['Dt_audit'],'Y-m-d H:i:s'),'tip'=>'');
}

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'hides', $hides );
$tpl->assign( 'params', $params );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('info'.$raintpl_ver);
exit;
}
?>
