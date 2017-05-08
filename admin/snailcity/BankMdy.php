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
$userID = $FLib->RequestInt('userID',1,12,'用户id',1);
$points = array('系统管理', $t.'管理');
$action = 'BankProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();
$extend = array('js'=>'
<script type="text/javascript" src="../../tpl/js/apcd.js"></script>
<script type="text/javascript" src="../../tpl/js/jquery.select-1.3.6.js"></script>			
<script>			
$(function(){
	if(typeof(APCD)=="undefined"){alert("省市未加载！");}
	var provinceobj = APCD.province,districtobj=APCD.district;
	var _province=$("#province"),_city=$("#city"),_p_d=$("#province_v").val(),_c_d=$("#city_v").val();
	var cityname="";
	var o=provinceobj;
	for(var k=0;k<o.length;k++){
		var ats = {};
		if(o[k].id==_p_d){
			ats.selected="selected";
			for(var j=0;j<o[k].child.length;j++){
				if(o[k].child[j].id==_c_d){cityname=o[k].child[j].name;break;}
			}
		}
		$("<option>").appendTo(_province).val(o[k].id).text(o[k].name).attr(ats);
	}
	if(_c_d!=""){_city.html(\'<option value="\'+_c_d+\'">\'+cityname+\'</option>\');}
	_province.change(function(){
		_city.empty();
		var va = _province.find("option:selected").val();
		if(va==0){$("<option >请选择城市</option>").appendTo(_city);}
		for(var i in provinceobj){
			if(va==provinceobj[i].id){
				var o=provinceobj[i].child;
				for(var k=0;k<o.length;k++){
					$("<option>").appendTo(_city).val(o[k].id).text(o[k].name);
				}
				break;
			}
		}
		_city.sSelect();
	});
	
	$("#seldv select").sSelect();
	$("#Vc_code").bind("keydown",function(e){
		var _t=$(this),_v=_t.val().replace(/[^\d]/g,"");
		if(!isNaN(_v)){
			_t.val( _v.replace(/(\d{4})(?=\d)/g,"$1 ").substr(0,23) );//四位数字一组，以空格分割
		}else{
			if(e.keyCode==8){//当输入非法字符时，禁止除退格键以外的按键输入
				return true;
			}else{
				return false
			}
		}
	}).keyup();	
});
</script>
');


$def_rolelist = 5;//默认用户角色
switch($Work){   
	case 'Withdraw':
		//$Admin->CheckPopedoms('SC_SYS_SET_USER_EDIT');
		$freelimit=$Db->fetch_val('select Vc_value from site_parameter where Vc_name="cfg_withdraw_free"');
		$freelimit=round(floatval($freelimit),2);
		$Vc_bankcode   = $FLib->RequestChar('bankcode',1,50,'参数',1);
		$Vc_code   = $FLib->RequestChar('Vc_code',1,50,'参数',1);
		$Id   = $FLib->requestInt('Id',0,10,'参数',1);
		$params['Vc_code']     = array('val'=>$Vc_code,'name'=>'银行卡号');
		$params['Vc_bankcode']     = array('val'=>$Vc_bankcode,'name'=>'银行名称');
		$params['freelimit']   = array('val'=>$freelimit,'name'=>'免提现额度');
		$params['N_amount'] = array('val'=>'','name'=>'金额','tip'=>'','ty'=>'text','attrs'=>'isc="float2poiFun" maxlength="9"');
		$hides['ID'] = $Id;
			
		break;
	case 'AddReco':
		$Admin->CheckPopedoms('SC_SYS_SET_USER_EDIT');
		$Rs = array();
		$title = '添加'.$t;
		$rolelist = $def_rolelist;
		$Vc_bankcode='';
		foreach($DDIC['p2p_user_bankcard.Vc_bankcode'] as $k=>$v){
			$Vc_bankcode.= '<option value="'.$k.'" >'.$v['name'].'</option>';
		}
		if($Work == 'AddReco'){
			$params['Vc_bankcode'] = array('val'=>'<select name="Vc_bankcode" class="sel_put2 chzn-select-no-single">'.$Vc_bankcode.'</select>','name'=>'选择银行','tip'=>'');
			$params['Vc_code']     = array('val'=>'','name'=>'银行卡号','tip'=>'只能使用数字','ty'=>'text','attrs'=>' id="Vc_code" maxlength="23"');
			$params['Vc_province'] = array('val'=>'<select name="Vc_province" id="province" ><option value="">请选择省份</option></select>','name'=>'选择省份','tip'=>'');
			$params['Vc_city'] = array('val'=>'<select name="Vc_city" id="city" ><option value="">请选择城市</option></select><input type="hidden" id="province_v"  /><input type="hidden" id="city_v" />','name'=>'选择城市','tip'=>'');
			$hides['userID'] = $userID;
			
		}
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
