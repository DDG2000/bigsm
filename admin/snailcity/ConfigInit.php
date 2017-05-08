<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-4-10
**本页： 初始化参数 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
require_once('ConfigCommon.php');
$Admin->CheckPopedoms('SC_SITE_CONFIG_INIT');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$gp = $FLib->RequestInt('gp',0,9,'状态');//default 1
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType .'&gp=' . $gp ;

$title = '初始化参数';
$points = array('网站管理', $title.'管理');
$sTypes = array('', '参数标识');
$sTypes = array('', '分组名称');
$hides  = array('gp'=>$gp);
$extend = array();

switch ($sType){
	case 1:
		$mWhere = "Vc_name like '%$sKey%'";
		break;
	default:
		$mWhere = '1=1';
		break;
}
if($gp>0){
	$mWhere .= ' and I_group='.$gp;
}
$tables = 'site_parameter where Status=1 and '.$mWhere.'';
$sql = "SELECT * FROM {$tables} order by Vc_name";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = 100;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$groups = array();
foreach($grouparr as $k=>$v){$groups[$k]=$v[1];}
$marks = '<a href="?gp=0" class="'.(0==$gp?'cur':'').'">全部</a>';
foreach($groups as $k=>$v){
	$marks .= '<a href="?gp='.$k.'" class="'.($k==$gp?'cur':'').'">'.$v.'</a>';
}
$extend['marks']=$marks;
$ths = array();
$ths[]=array('val'=>'参数名', 'wid'=>'');
$ths[]=array('val'=>'参数值', 'wid'=>'');
//$ths[]=array('val'=>'类型', 'wid'=>'');
$ths[]=array('val'=>'参数组', 'wid'=>'');
$ths[]=array('val'=>'参数说明', 'wid'=>'');
$ths[]=array('val'=>'参数Tip', 'wid'=>'');
$ths[]=array('val'=>'可见', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$_td  = '<td>'.$Rs[$i]['Vc_name'].'</td>';
		$_td .= '<td title="'.$Rs[$i]['Vc_value'].'">'.$FLib->CutString($Rs[$i]['Vc_value'],10).'</td>';
		//$_td .= '<td>'.$Rs[$i]['Vc_type'].'</td>';
		$_td .= '<td>'.iset($groups[$Rs[$i]['I_group']]).'</td>';
		$_td .= '<td title="'.$Rs[$i]['Vc_intro'].'">'.$FLib->CutString($Rs[$i]['Vc_intro'],10).'</td>';
		$_td .= '<td title="'.$Rs[$i]['Vc_tip'].'">'.$FLib->CutString($Rs[$i]['Vc_tip'],10).'</td>';
		$_td .= '<td>'.($Rs[$i]['I_show']==1?'是':'否').'</td>';
		$_td .= '<td><a href="ConfigInitMdy.php?Work=MdyReco&Id='.$Rs[$i]['ID'].'" class="hs" h="530" title="编辑'.$title.'">编辑</a></td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array('<a href="ConfigInitProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删 除</a>',);
$extend['gbtns'] = array('<a href="ConfigInitMdy.php?Work=AddReco&gp='.$gp.'" class="hs" h="530" title="添加'.$title.'"><span>添加'.$title.'</span></a>','<a href="ConfigInitProcess.php?Work=CreateReco" class="goto" title="生成后参数将生效，是否生成？">生成缓存文件</a>');
$helps  = array('参数的添加,编辑和删除需要同时在ConfigManager.php的模板处理','生成缓存文件，即将参数值写入文件中，可不通过查询数据库直接调用参数');
$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
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
$tpl->assign( 'pagelistparam', $pagelistparam );
$tpl->assign( 'ths', $ths );
$tpl->assign( 'tds', $tds );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('list'.$raintpl_ver);
exit;
}
?>
