<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 权限 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SYS_SET_POPEDOM');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$pid  = $FLib->RequestInt('pid',0,9,'父类ID');
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType .'&pid=' . $pid;

$title = '权限';
$points = array('系统管理', $title.'管理');
$sTypes = array();
$hides  = array();
$extend = array('js'=>'
<script>
$(function(){
});
</script>
');

$sqlw = '';
if($pid>0){
	$sqlw .= " and I_parentID=$pid";
}
$tables = 'sc_popedom where Status=1 '.$sqlw.'';
$sql = "SELECT * FROM {$tables} order by I_parentID asc,I_order desc,ID";
$Rs  = $DataBase->GettArrayResult($sql);
$extend['rscount'] = count($Rs);

//格式化目录
//$fa 按I_parentID 排序
$sids = '';
function _formatlistlevel($fa, $pid=0){
	global $sids;
	$ta=array();
	$falen = count($fa);
	foreach($fa as $k=>$vv){
		$v = array('ID'=>$vv['ID'],'Vc_name'=>$vv['Vc_name'],'Vc_key'=>$vv['Vc_key'],'Vc_intro'=>$vv['Vc_intro'],'I_parentID'=>$vv['I_parentID'],'I_order'=>$vv['I_order']);
		if($v['I_parentID']==$pid){
			$ta[$v['ID']] = $v;
			//$sids .= $v['ID'].',';
			unset($fa[$k]);
		}
	}
	if($falen != count($fa)){
		foreach($ta as $v){
			if(empty($fa)){break;}
			$ra = _formatlistlevel($fa, $v['ID']);
			$fa = $ra['fa'];
			$ta[$v['ID']]['child'] = $ra['ta'];
			unset($ra);
		}
	}
	return array('fa'=>$fa, 'ta'=>$ta);
}
function _showitem($v, $prev=''){
	global $title;
	$_td  = '<td>'.$prev.'<a href="?Parent='. $v['ID'] .'&PKey='.$v['Vc_key'].'">'.$v['Vc_name'].'</a></td>';
	$_td .= '<td>'.$v['Vc_key'].'</td>';
	$_td .= '<td>'.$v['I_order'].'</td>';
	$_td .= '<td>
		<a href="PopedomMdy.php?Work=MdyReco&Id='.$v['ID'].'" class="hs" h="500" title="编辑'.$title.'">编辑</a> |<a href="PopedomMdy.php?Work=AddReco&Parent='.$v['ID'].'&PKey='.$v['Vc_key'].'" class="hs" h="500" title="添加'.$title.'">添加子项</a> | <a href="PopedomProcess.php?Work=DeleteReco&IdList='.$v['ID'].'" class="goto" title="是否确认删除该项？">删除</a>
		</td>';
	return $_td;
}
function _showlistlevel($fa, $prev=''){
	$tds = array();
	foreach($fa as $v){
		$tds[$v['ID']] = _showitem($v, $prev);
		if(!empty($v['child'])){
			$prevs = $prev.'|==';
			$ntds = _showlistlevel($v['child'], $prevs);
			$tds = $tds+$ntds;
		}
	}
	return $tds;
}
$ths = array();
$ths[]=array('val'=>'权限名称', 'wid'=>'');
$ths[]=array('val'=>'权限标识', 'wid'=>'');
$ths[]=array('val'=>'排序号', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');

$ra = _formatlistlevel($Rs, $pid);
//echo $sids;
$tds = _showlistlevel($ra['ta']);

$DataBase->CloseDataBase();

$btns   = array('<a href="PopedomProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删 除</a>',);
$extend['gbtns'] = array();
$extend['gbtns'][] = '<a href="PopedomMdy.php?Work=AddReco&Parent='.$pid.'&PKey='.$PKey.'" class="hs" h="500" title="添加'.$title.'"><span>添加'.$title.'</span></a>';
$helps  = array();

$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'sKey', $sKey );
$tpl->assign( 'sType', $sType );
$tpl->assign( 'sTypes', $sTypes );
$tpl->assign( 'hides', $hides );
$tpl->assign( 'btns', $btns );
$tpl->assign( 'ths', $ths );
$tpl->assign( 'tds', $tds );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('listlevel'.$raintpl_ver);
exit;
}
?>
