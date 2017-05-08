<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 用户编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$t='用户';
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$points = array('系统管理', $t.'管理');
$action = 'ManagerProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();
$extend = array('js'=>'
<script type="text/javascript">
	$(function(){
		$(":input[name=rolelist]").blur(function(){
			var _tv=","+$(this).val()+",",_adrole=$(":input[name=audit_role]");
			if(_tv.indexOf(",4,")>-1){
				_adrole.attr({"isc":"","nofocus":""});
			}else{
				_adrole.removeAttr("isc");
			}
		}).blur();
	});
</script>
');


$def_rolelist = 5;//默认用户角色
switch($Work){
    case 'MdyPwd' :
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		if($Id != $Admin->Uid){	$Admin->CheckPopedoms('SC_SYS_SET_USER_EDIT'); }
		$burl = $FLib->RequestInt('burl',0,9,'burl');
		$Rs = $DataBase->GetResultOne("SELECT * FROM sc_user WHERE id=" . $Id);
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '修改'.$t.'密码';
		$hides['Id'] = $Id;
		$params[] = array('val'=>iset($Rs['Vc_name']),'name'=>'用户名');
		$params['pwd'] = array('val'=>'<input type="password" name="pwd" value="" class="txt_put2" isc="pwdlenFun" maxlength="20" autocomplete="off">','name'=>'新密码','tip'=>'以非空字符的6-20位组成','tipw'=>'60%','attrs'=>'isc');
		$params['pwd1'] = array('val'=>'<input type="password" name="pwd1" value="" class="txt_put2" isc="pwd2chkFun" maxlength="20" autocomplete="off">','name'=>'确认新密码','tip'=>'为了安全，请重新输入新密码','attrs'=>'isc');
		if($burl==1){
			$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?Work=MdyPwd&Id='.$Id.'&burl=1');
			$hides['obj'] = 'self';
			$extend['isb'] = 1;
		}
		break;
	case 'MdyReco':
		$Admin->CheckPopedoms('SC_SYS_SET_USER_EDIT');
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		//获取用户信息
		$Rs = $DataBase->GetResultOne("SELECT * FROM sc_user WHERE id=" . $Id);
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		//获取权限
		$Rs1 = $DataBase->GetResultOne("select T_rule from sc_rule_user where Status=1 and I_type=1 And I_userID=$Id ");
		$rolelist =  $Rs1 ? $Rs1[0] : '';
		$Rs1 = $DataBase->GetResultOne("select T_rule from sc_rule_user where Status=1 and I_type=2 And I_userID=$Id ");
		$rulelist =  $Rs1 ? $Rs1[0] : '';
		$grouplist = '';
		//获取用户组
		$Rs1 = $DataBase->GettArrayResult("select I_groupID from sc_group_user where Status=1 And I_userID=$Id");
		if($Rs1){
			foreach($Rs1 as $v){
				$grouplist .= ($grouplist==''?'':',').$v[0];
			}
		}
		//额外审核角色
		$Rs1 = $DataBase->GetResultOne("select T_rule from sc_rule_user where Status=1 and I_type=3 And I_userID=$Id ");
		$audit_role =  $Rs1 ? $Rs1[0] : '';
		
		$title = '编辑'.$t;
		$hides['Id'] = $Id;
		if($rolelist=='') $rolelist = $def_rolelist;
		break;
	case 'AddReco':
		$Admin->CheckPopedoms('SC_SYS_SET_USER_EDIT');
		$Rs = array();
		$title = '添加'.$t;
		$rolelist = $def_rolelist;
		break;
	default:
		die('没有该操作类型!');
}

if($Work != 'MdyPwd'){
	$params['title'] = array('val'=>iset($Rs['Vc_name']),'name'=>'用户名','tip'=>'只能使用字母,数字和下划线','ty'=>'text','attrs'=>'isc="namepatFun" maxlength="20"');
	if($Work == 'AddReco'){
		$params['pwd'] = array('val'=>'<input type="password" name="pwd" value="" class="txt_put2" isc="pwdlenFun" maxlength="20" autocomplete="off">','name'=>'新密码','tip'=>'以非空字符的6-20位组成','attrs'=>'isc');
	}
	$params['intro'] = array('val'=>iset($Rs['Vc_intro']),'name'=>'备注','ty'=>'textarea','attrs'=>'');
	if($Config->GroupTure == 1 && $Admin->CheckPopedom('SC_SYS_SET_GROUP_MDY')){
		$params['grouplist'] = array('val'=>iset($grouplist),'name'=>'用户组','tip'=>'用户属于组,则有该组的权限','ty'=>'text','attrs'=>'data-url="GroupSelect.php?Type=2&IdList=" w="500" h="400" title="用户组选择器"');
	}
	$params['rolelist'] = array('val'=>iset($rolelist),'name'=>'独立角色','tip'=>'用户属于角色,则有该角色的网站权限','ty'=>'text','attrs'=>'data-url="RoleSelect.php?Type=1&IdList=" w="500" h="400" title="角色选择器"');
	if($Config->GroupTure == 1 && $Admin->CheckPopedom('SC_SYS_SET_POPEDOM')){
		$params['rulelist'] = array('val'=>iset($rulelist),'name'=>'独立权限','tip'=>'用于分配其所在组或角色中不存在的权限','ty'=>'text','attrs'=>'data-url="PopedomSelect.php?Type=2&IdList=" w="500" h="400" title="权限选择器"');
	}
	//额外审核角色
	$params['audit_role'] = array('val'=>iset($audit_role),'name'=>'审核角色','tip'=>'用户属于角色,则有该角色的审核权限',
		'ty'=>'text','attrs'=>'data-url="../content/AuditRoleSelect.php?Type=1&IdList=" w="500" h="400" title="审核角色选择器"');
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
