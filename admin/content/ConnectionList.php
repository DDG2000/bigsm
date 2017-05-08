<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-03-16
**本页： 友情链接 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_LINK');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->requestchar('sKey',1,50,'参数',1);
$sType  = $FLib->requestint('sType',0,9,'类型');
$CurrPage = $FLib->requestint('currpage',1,9,'页数');
$status = $FLib->requestint('status',2,9,'状态');
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType  .'&status=' . $status ;

//$typearr = array('t_2'=>'文字连接','t_1'=>'图片连接');//key 需要根据status改变
//$marks = '';
//foreach($typearr as $k=>$v){$marks.='<a href="?status='.substr($k,2).'" '.(substr($k,2)==$status?'class="cur"':'').'>'.$v.'</a>';}

$title = '友情链接';
$points = array('网站管理', $title.'管理' );
$sTypes = array('', '链接名称');
$hides  = array('status'=>$status);
$extend = array();//'marks'=>$marks

switch ($sType){
	case 1:
		$mWhere = "a.Vc_name like '%$sKey%'";
		break;
	default:
		$mWhere = '1=1';
		break;
}
$mWhere .=' and a.I_type='.$status;
$tables = 'es_link a where a.Status=1 and '.$mWhere.'';
$sql = "SELECT a.* FROM {$tables} order by a.I_order desc,a.ID desc";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'名称或图片', 'wid'=>'');
$ths[]=array('val'=>'链接地址', 'wid'=>'');
$ths[]=array('val'=>'排序号', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$ttt = $Rs[$i]['I_type']==1?'<img style="max_width:50px;max_height:50px;_height:50px;_width:50px;" src="'.$Rs[$i]['Vc_image'].'" />':$FLib->CutStr($Rs[$i]['Vc_name'],40);
		$_td  = '<td>'.$ttt.'</td>';
		$_td .= '<td>'.$Rs[$i]['Vc_link'].'</td>';
		$_td .= '<td>'.$Rs[$i]['I_order'].'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		$_td .= '<td><a href="ConnectionMdy.php?Work=MdyReco&Id='.$Rs[$i]['ID'].'&type='.$Rs[$i]['I_type'].'" class="hs" h="350" title="编辑'.$title.'">编辑</a></td>';
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns   = array();
$btns[] = '<a href="ConnectionProcess.php?Work=DeleteReco&IdList=" class="del" rel="IdList">删 除</a>';

$extend['gbtns'] = array('<a href="ConnectionMdy.php?Work=AddReco" class="hs" h="350" title="添加'.$title.'"><span>添加'.$title.'</span></a>');

$helps  = array();
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
