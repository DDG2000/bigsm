<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 会员 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');
require(WEBROOTDATA.'userclass.cache.inc.php');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$title = '会员';
$points = array('会员管理');
$action = 'MemberProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();
$extend['js'] = '
<link type="text/css" rel="stylesheet" href="../include/timepicker/css/jquery-ui.css" >
<script type="text/javascript" src="../include/timepicker/js/jquery-ui.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-slide.min.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">
$(function(){
//var p1=$("#adAction").children("option:selected").val();
		
})
function clickFriend(val){
	//showF
}
</script>
';


$pcfg = new ConfigP2p();//默认参数
unset($pcfg->fp_institution[0]);
$pcfg->fp_institution;

switch($Work){
	case 'MdyExpire':
		$Admin->CheckPopedoms('SC_MEMBER');
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from user_base where ID='.$Id.' limit 0,1');
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$hides['Id'] = $Id;
		
		
		$classStr='<select name="I_userclass" ><option value="">请选择</option>';
		foreach ($da_userclass as $k=>$v){
			if($Rs['I_userclass']==$k){
				$classStr.='<option selected="selected" value="'.$k.'">'.$v['Vc_name'].'</option>';
			}else{
				$classStr.='<option value="'.$k.'">'.$v['Vc_name'].'</option>';
			}
		}
		$classStr.='</select>';
		

		$params[] = array('val'=>iset($Id),'name'=>'用户ID','tip'=>'');
		$params[] = array('val'=>'<input name="Dt_expire" type="text" class="txt_put2" isc="" id="Dt_expire" value="'.$FLib->FromatDate($Rs['Dt_expire'],'Y-m-d').'"><script>$(function(){$(\'#Dt_expire\').datepicker();});</script>','name'=>'会员有效期','tip'=>'会员有效期截止时间');
		$params['I_userclass'] = array('name'=>'用户类型','val'=>$classStr,'tip'=>'');
		
		$title = '会员期限和类型';
		break;
	case 'MdyPay':
		$Admin->CheckPopedoms('SC_MEMBER');
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select * from user_base where ID='.$Id.' limit 0,1');
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$hides['Id'] = $Id;

		$params[] = array('val'=>$Rs['Vc_Email'],'name'=>'用户邮箱','tip'=>'');
		//$params['Vc_name'] = array('val'=>$Id.'代充值','name'=>'任务标题','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="50"');
		$params['I_userID'] = array('val'=>$Id,'name'=>'用户ID','tip'=>'充值对象','ty'=>'text','attrs'=>'isc="nums" maxlength="9" readonly');
		$params['N_amount'] = array('val'=>'','name'=>'金额','tip'=>'','ty'=>'text','attrs'=>'isc="float2poiFun" maxlength="9"');
		$params['Vc_content'] = array('val'=>'','name'=>'备注','tip'=>'','ty'=>'textarea','attrs'=>'isc="MaxLen500" ennull=""');
		
		$title = '代充值';
		break;
	
	case 'MdyFriend':
		$Admin->CheckPopedoms('SC_MEMBER');
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne('select ID,myIntroCode,IntroForm from user_base where ID='.$Id.' and Status>0 limit 0,1');
		if($Rs['myIntroCode']=='' || strlen($Rs['myIntroCode'])<6){
			$User = new User();
			$da=array();
			//保证推荐码的唯一性		
			while(1){
				$da['myIntroCode']=generateNo(6);
				$temp=$User->checkUserCode($da,0);
				if($temp['flag']=='1') break; 
			}
			$User->Db->autoExecute('user_base',$da,'update',"ID={$Rs['ID']}");
			$Rs['myIntroCode']=$da['myIntroCode'];
		}else{
			if($Rs['IntroForm']){
				$Rs1 = $DataBase->GetResultOne('select ID from user_base where myIntroCode="'.$Rs['IntroForm'].'" and Status>0 limit 0,1');
				$Rs['ID1']=$Rs1['ID'];
			}
			$Rs1 = $DataBase->GetResultAll('select ID from user_base where IntroForm="'.$Rs['myIntroCode'].'" and Status>0');
			for ($i=1;$i<count($Rs1);$i++){
				$Rs['ID2'].=$Rs1[$i]['ID'].',';
			}
			if($Rs['ID2']){
				$Rs['ID2']=substr($Rs['ID2'], 0,mb_strlen($Rs['ID2'])-1);
			}
		}
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$hides['Id'] = $Id;
	
		$params['myIntroCode'] = array('val'=>$Rs['myIntroCode'],'name'=>'我的邀请码','tip'=>'','ty'=>'text','attrs'=>'isc="" maxlength="9" readonly');
		$params['ID1'] = array('val'=>$Rs['ID1'],'name'=>'邀请我的人','tip'=>'','ty'=>'text',
				'attrs'=>'data-url="MemberListByFriend.php?type=1&notId='.$Id.'&IdList=" w="800" h="400" title="邀请我的人选择器"');
		$params['ID2'] = array('val'=>$Rs['ID2'],'name'=>'我邀请的人','tip'=>'','ty'=>'textarea',
				'attrs'=>'data-url="MemberListByFriend.php?type=2&notId='.$Id.'&IdList=" w="800" h="400" title="我邀请的人选择器"');
		
		$title = '绑定邀请关系';
		$extend['btn_reset']=false;
		break;
		
	default:
		die('没有该操作类型!');	
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
