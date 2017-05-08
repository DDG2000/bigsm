<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 系统配置参数管理 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');
// require_once('ShopConfigCommon.php');
require_once('../snailcity/ConfigCommon.php');
//use cache
if($raintpl_cache && $cache = $tpl->cache('configmdy', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$gp = $FLib->RequestInt('gp',11,9,'状态');//default 11

$amk =  $grouparr;
$title = '商铺参数';
$marks = '';
//必须有一个默认的
foreach($amk as $k=>$v){
	$m=$k;$cn='';
	if($gp==$m){$cn = 'cur';$title=$v[1];}
	if($v[0]=='' || $Admin->CheckPopedom($v[0])){
		$marks .= '<a href="?gp='.$m.'" class="'.$cn.'">'.$v[1].'</a>';
	}
}

$points = array('商家管理','商铺参数设置');
$action = 'ShopConfig.php';
$hides  = array('Work'=>'save','gp'=>$gp);
$params = array();
$helps  = array();
$extend = array('marks'=>$marks);//

//save begin
if($Work=="save"){
	$configarr = array();
	foreach($_POST as $k=>$v){
		if(preg_match("/^edit___/",$k)){
			$v = $FLib->IsRequest($k);
			if(is_array($v)){
				$v = $FLib->RequestCheck($k,1,0,'参数'.$k,1);
			}else{
				$v = $FLib->RequestChar($k,1,0,'参数'.$k,1);
			}
		}else{
			continue;
		}
		$k = preg_replace("/^edit___/","",$k);
		$configarr[$k]=$v;
	}

	//写入数据库
	foreach($configarr as $k=>$v){
		$sql = "update site_parameter set Vc_value='$v',Createtime=now() where Vc_name='$k'";
		$DataBase->QuerySql($sql);
	}
	//write begin
	$r = writeConfigValue();
	if($r[0]<1){echo showErr($r[1]);exit;}
	//write end

	$Admin ->AddLog('参数管理','成功更改参数配置！并生成文件' );
	echo showSuc('参数修改成功！',$FLib->IsRequest('backurl'), 'self');
	exit;
}
//save end

$tips = array(
	'yn'=>array(1=>'是',0=>'否')
	,'kg'=>array(1=>'开启',0=>'关闭')
	,'zc'=>array(1=>'支持',0=>'不支持')
	,'ordertype'=>array(0=>'未确认',1=>'已确认',2=>'已收款',3=>'已发货',4=>'已完成')
	,'ordertype1'=>array(0=>'未确认',1=>'已确认',2=>'已收款')
	,'img'=>array('jpg'=>'jpg','jpeg'=>'jpeg','png'=>'png','gif'=>'gif','bmp'=>'bmp')
);

$Rs = $DataBase->GettArrayResult('select Vc_name,Vc_value,Vc_intro,Vc_tip from site_parameter where Status=1 and I_show=1 and I_group='.$gp);//


foreach($Rs as $v){
	$params[$v[0]] = array('name'=>$v[2],'tip'=>$v[3],'val'=>$v[1],'key'=>'edit___'.$v[0]);
}
$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?gp='.$gp);


//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'action', $action );
$tpl->assign( 'hides', $hides );
$tpl->assign( 'params', $params );
$tpl->assign( 'tips', $tips );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('configmanager'.$raintpl_ver);
exit;
}
?>
