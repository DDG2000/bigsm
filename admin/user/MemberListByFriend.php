<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 会员 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_MEMBER');
require(WEBROOTDATA.'userclass.cache.inc.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$type   = $FLib->RequestInt('type',0,9,'参数',1);
$notId   = $FLib->RequestInt('notId',0,9,'不包含此ID',1);
$IdList = $FLib->RequestChar('IdList',1,0,'IdList',0);
$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$typeclass  = $FLib->RequestInt('typeclass',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&type=".$type ."&IdList=".$IdList."&notId=".$notId.
	"&sKey=" . urlencode($sKey) ."&sType=" . $sType."&typeclass=".$typeclass;

$title = '会员列表选择器';
$points = array('会员管理','绑定邀请关系', $title);
$sTypes = array('','会员昵称','真实姓名','邮箱地址','手机号码','黑名单','会员类型');
$hides=array('type'=>$type,'notId'=>$notId,'IdList'=>$IdList);
$extend = array();
$extend['js']='
<style type="text/css">
.spancheck{display: none;}
</style>
<script type="text/javascript">
$(function(){
'.($sType<5?'$(".defaultcheck").show();':'$(".typecheck").show();').'
	$("select[name=\'sType\']").live(\'change\',function(){
		$(".spancheck input").val("");
		$(".spancheck").hide();
		
		if($(this).val()<5){
			$(".defaultcheck").show();
		}else{
			$(".typecheck").show();
		}
	});
	'.($type==2?'
	$("input[name=\'IdList\']").each(function(){
		if("'.$IdList.'".indexOf($(this).val()) >= 0){
		 	$(this).attr("checked","checked");
		}
	});
	$("input[name=\'IdList\']").live("change",function(){
		if($(this).attr("checked")){
			$("#CheckedList").append("<span id=\'Checked"+$(this).val()+"\'><input name=\'CheckedListCkx\' type=\'checkbox\' value=\'"+$(this).val()+"\' checked />"+$(this).val()+"&nbsp;</span>");
		}else{
			$("#CheckedList #Checked"+$(this).val()).remove();
		}
	});
})
function clickIdList(){
	var ids = "" ;
	$("input[name=\'CheckedListCkx\']:checked").each(function(){
		ids += $(this).val()+",";
	});
	ids = ids.substr(0,(ids.length-1));
	parent.$("textarea[name=\'ID2\']").text(ids);
	parent.hidePopWin();
}':'});').'
</script>
';

$classStr='<option value="">所有</option>';
foreach ($da_userclass as $k=>$v){
	if($typeclass==$k){
		$classStr.='<option selected="selected" value="'.$k.'">'.$v['Vc_name'].'</option>';
	}else{
		$classStr.='<option value="'.$k.'">'.$v['Vc_name'].'</option>';
	}
}
$extend['sTypes'] = array('<span class="spancheck typecheck"><select name="typeclass" class="sel_put1 chzn-select-no-single">'.
		$classStr.'</select></span>');

$sqlw = '';
if($sKey!=''){
switch ($sType){
	case 1:
		$sqlw .= " and Vc_nickname like '%" . $sKey . "%'";
		break;
	case 2:
		$sqlw .= " and Vc_truename like '%" . $sKey . "%'";
		break;
	case 3:
		$sqlw .= " and Vc_Email like '%" . $sKey . "%'";
		break;
	case 4:
		$sqlw .= " and Vc_mobile like '%" . $sKey . "%'";
	break;
}
}
if($sType==5){
	$sqlw .= ' and I_bad=1';
}
if($sType==6 && $typeclass)
	$sqlw .= " and I_userclass=".$typeclass;

$tables = 'user_base where ID>999 and Status>0 '.$sqlw.' and ID<>'.$notId;
$sql = "SELECT * FROM {$tables} order by ID desc";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = 5;//$Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths=$thsl = array();
$thsl[]=array('val'=>'ID', 'wid'=>'');
$ths[]=array('val'=>'昵称', 'wid'=>'');
$ths[]=array('val'=>'邮箱', 'wid'=>'');
$ths[]=array('val'=>'手机', 'wid'=>'');
$ths[]=array('val'=>'真实姓名', 'wid'=>'');
$ths[]=array('val'=>'会员类型', 'wid'=>'');
$ths[]=array('val'=>'账户类型', 'wid'=>'');
$ths[]=array('val'=>'锁定状态', 'wid'=>'');
$ths[]=array('val'=>'注册时间', 'wid'=>'');
$ths[]=array('val'=>'黑名单', 'wid'=>'');
$tds=$tdsl = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$td_btn = array();
		$_td  = '<td><a href="MemberInfo.php?Id='.$Rs[$i]['ID'].'" class="hs" h="" title="会员详细页">'.$Rs[$i]['ID'].'</a></td>';
		$tdsl[$Rs[$i]['ID']]=$_td;
		$_td = '<td><a href="MemberInfo.php?Id='.$Rs[$i]['ID'].'" class="hs" h="" title="会员详细页">'.$Rs[$i]['Vc_nickname'].'</a></td>';
		$_td .= '<td title="'. ($Rs[$i]['I_Emailauthenticate']==2?'已认证':'未认证') .'">'.$Rs[$i]['Vc_Email'].'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_mobileauthenticate']==2? $Rs[$i]['Vc_mobile']:'未认证') .'</td>';
		$_td .= '<td>'.$Rs[$i]['Vc_truename'].'</td>';
		$_td .= '<td>'.$da_userclass[$Rs[$i]['I_userclass']]['Vc_name'].'</td>';
		$_td .= '<td>'.($Rs[$i]['I_company']>0?'企业':'个人').'</td>';
		$_td .= '<td>'. ($Rs[$i]['Status']==1?'未锁定':($Rs[$i]['Status']==2?'<span style="color:red;">锁定</span>':'未知')) .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$_td .= '<td>'. ($Rs[$i]['I_bad']==1?"<span style='color:red;'>是</span>":"否") .'</td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

if($IdList) $CheckedListArr = explode(',', $IdList);
$CheckedListStr = '';
foreach ($CheckedListArr as $v){
	$CheckedListStr.="<span id='Checked{$v}'><input name='CheckedListCkx' type='checkbox' value='{$v}' checked onclick='return false' />{$v}&nbsp;</span>";
}
if($type==2){
$btns   = array('<span id="CheckedList">已选：'.$CheckedListStr.'</span>'
		,'<a href="javascript:clickIdList()">确定</a>');
}
$helps  = array('只能单页选择要保存的项');
$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'sKey', $sKey );
$tpl->assign( 'sType', $sType );
$tpl->assign( 'sTypes', $sTypes );
$tpl->assign( 'hides', $hides );
$tpl->assign( 'btns', $btns );
$tpl->assign( 'pagelistparam', $pagelistparam );
$tpl->assign( 'ths', $ths );
$tpl->assign( 'tds', $tds );
$tpl->assign( 'thsl', $thsl );
$tpl->assign( 'tdsl', $tdsl );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );
if($type==1){
	$tpl->assign( 'checked', $IdList );
	$tpl->assign( 'returnInput', 'ID1' );
	
	$tpl->draw('radio_div'.$raintpl_ver);
}elseif($type==2){
	$tpl->assign( 'checked', $IdList );
	
	$tpl->draw('list_div'.$raintpl_ver);
}

exit;
}
?>