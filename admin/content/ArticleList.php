<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 网站介绍 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('ES_ARTICLE');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$cid = $FLib->RequestInt('cid',0,9,'分类id');
$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType .'&cid=' . $cid ;

$title = '文章';
$isadd = 1;
if($cid>0){
	$Rs0 = $DataBase->GetResultOne("SELECT * FROM es_article_class where status=1 and ID={$cid}");
	if(!$Rs0){ echo showErr('记录未找到'); exit; }
	$isadd = $Rs0['I_add'];
	$title = $Rs0['Vc_name'];
}

$points = array('网站管理', '内容管理', $title.'管理' );
$sTypes = array('', '文章名称');//, '分类名称'
$hides  = array('cid'=>$cid);
$extend = array();

switch ($sType){
	case 1:
		$mWhere = "ea.Vc_name like '%$sKey%'";
		break;
	//case 2:
		//$mWhere = "eac.Vc_name like '%$sKey%'";
	//	break;
	default:
		$mWhere = '1=1';
		break;
}
if($cid>0){
	$mWhere .= ' and ea.I_classID='.$cid;
}
$tables = 'es_article ea left join es_article_class eac on ea.I_classID=eac.ID where ea.Status=1 and '.$mWhere.'';
$sql = "SELECT ea.*,eac.Vc_name eacname FROM {$tables} order by I_order desc,ea.ID desc";
$sqlcount = "SELECT count(*) FROM {$tables} ";
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);

$ths = array();
$ths[]=array('val'=>'文章名称', 'wid'=>'');
$ths[]=array('val'=>'分类名称', 'wid'=>'');
$ths[]=array('val'=>'审核', 'wid'=>'');
$ths[]=array('val'=>'发布日期', 'wid'=>'');
$ths[]=array('val'=>'排序号', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');

$tds = array();
if(is_array($Rs)){
	$idnotmodify=$GLOBALS['Config']->admin_content_idnomodify;//此id组中的不允许修改
	$idnotdel = $GLOBALS['Config']->admin_content_idnodel;
	
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
		$showurl = '<a href="/index.php?act=help&m=prew&id='.$Rs[$i]['ID'].'" target="_blank">预览</a> | ';
		if($Rs[$i]['I_audit']==1){
			$showurl = '<a href="/index.php?act=help&m=prew&id='.$Rs[$i]['ID'].'" target="_blank">查看</a> | ';
		}
		$_td  = '<td>'.$Rs[$i]['Vc_name'].'</td>';
		$_td .= '<td>'.$Rs[$i]['eacname'].'</td>';
		
		if(in_array($Rs[$i]['ID'],$idnotdel)){
			$_td .= '<td><img src="../image/checked.gif"></td>';
		}else{
			$_td .= '<td><img src="../image/'. ($Rs[$i]['I_audit']==1? 'checked':'check').'.gif"></td>';
		}
		
		$_td .= '<td>'.$FLib->FromatDate($Rs[$i]['Dt_release'],'Y-m-d').'</td>';
		$_td .= '<td>'.$Rs[$i]['I_order'].'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d') .'</td>';
		
		if(in_array($Rs[$i]['ID'],$idnotmodify)){
			$_td .= '<td>'.$showurl.'编辑</td>';
		}else{
			$_td .= '<td>'.$showurl.'<a href="ArticleMdy.php?Work=MdyReco&Id='.$Rs[$i]['ID'].'" class="" h="500" title="编辑内容分类">编辑</a></td>';
		}
		$tds[$Rs[$i]['ID']]=$_td;
    }
}
$DataBase->CloseDataBase();

$btns = array('<a href="ArticleProcess.php?Work=AuditReco&tname='.urlencode($title).'&IdList=" class="del" rel="IdList">审 核</a>');
$extend['gbtns'] = array();
if($isadd==1){
	$btns[] = '<a href="ArticleProcess.php?Work=DeleteReco&tname='.urlencode($title).'&IdList=" class="del" rel="IdList">删 除</a>';
	$extend['gbtns'][] = '<a href="ArticleMdy.php?Work=AddReco&cid='.$cid.'" class="" h="500" title="添加'.$title.'"><span>添加'.$title.'</span></a>';
}
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

$tpl->assign( 'idnotdel', $idnotdel);

$tpl->draw('listdel'.$raintpl_ver);
exit;
}
?>
