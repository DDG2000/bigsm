<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 用户权限查看 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}


$title = '用户权限查看';
$points = array('用户管理', $title);
$hides  = array();
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_SYS_SET_USER');

$Id = $FLib->RequestInt('Id',0,9,'ID');
$Rs = $DataBase->GetResultOne("select Vc_name,Vc_intro from sc_user where Status>0 And ID=$Id limit 0,1");
if(!$Rs){ echo showErr('记录未找到'); exit; }
$username = $Rs['Vc_name'];
$userintro= $Rs['Vc_intro'];

$params[] = array('name'=>'用户名','val'=>iset($username),'tip'=>'');
$params[] = array('name'=>'备注','val'=>iset($userintro),'tip'=>'');
if($Config->GroupTure==1 && $Admin->CheckPopedom('SC_SYS_SET_GROUP')){
	$params[] = array('name'=>'<b>所在组</b>','val'=>'','tip'=>'_Title_');
	$Rs = $DataBase->GettArrayResult("select ID,Vc_name,Vc_intro from sc_group where Status=1 And ID in(select I_groupID from sc_group_user where Status=1 And I_userID=$Id)");
	if($Rs){
		foreach($Rs as $k=>$v){
			$params[] = array('name'=>$v[1],'val'=>$v[2],'tip'=>'');
		}
	}else{
		$params[] = array('name'=>'此用户暂时不属于任何组','val'=>'','tip'=>'_Tip_');
	}
}

$rolelist = '';
$Rs = $DataBase->GetResultOne("select T_rule from sc_rule_user where Status=1 and I_type=1 And I_userID=$Id limit 0,1");
if($Rs){ $rolelist =  $Rs[0]; }
$params[] = array('name'=>'拥有角色','val'=>'','tip'=>'_Title_');
if($rolelist!=''){
	$Rs = $DataBase->GettArrayResult("select ID,Vc_name,Vc_intro from sc_role where Status=1 And ID in(".$rolelist.")");
	if($Rs){
		foreach($Rs as $k=>$v){
			$params[] = array('name'=>$v[1],'val'=>$v[2],'tip'=>'');
		}
	}
}
if($rolelist==''||!$Rs){
	$params[] = array('name'=>'此用户暂时不属于任何角色','val'=>'','tip'=>'_Tip_');
}

if($Config->UserRule==1 && $Admin->CheckPopedom('SC_SYS_SET_POPEDOM')){
	$rulelist = '';
	$Rs = $DataBase->GetResultOne("select T_rule from sc_rule_user where Status=1 and I_type=2 And I_userID=$Id limit 0,1");
	if($Rs){ $rulelist =  $Rs[0]; }
	$params[] = array('name'=>'拥有特定权限','val'=>'','tip'=>'_Title_');
	if($rulelist!=''){
		$Rs = $DataBase->GettArrayResult("select ID,Vc_name,Vc_key from sc_popedom where Status=1 And ID in(".$rulelist.")");
		if($Rs){
			foreach($Rs as $k=>$v){
				$params[] = array('name'=>$v[1],'val'=>$v[2],'tip'=>'');
			}
		}
	}
	if($rulelist==''||!$Rs){
		$params[] = array('name'=>'此用户暂时不拥有任何特定权限','val'=>'','tip'=>'_Tip_');
	}
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
