<?php
if(1){
	require_once('../include/TopFile.php');
	require_once(WEBROOTINCCLASS.'Project.php');
	$Admin->CheckPopedoms('SM_PROJECT');
	//use cache
	if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

	$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
	$sType  = $FLib->RequestInt('sType',0,9,'类型');
	$sTypes = array('关键词') ;
	$status = $FLib->requestint('status',1,9,'状态');
	$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
	$UrlInfo = '&sKey=' . urlencode($sKey) .'&sType=' . $sType  .'&status=' . $status ;
	$outexcel = $FLib->RequestChar('outexcel',1,9,'outexcel');

	$typearr = array('t_1'=>'活动中成交项目','t_0'=>'禁用中成交项目');//key 需要根据status改变
	$marks = '';
	foreach($typearr as $k=>$v){$marks.='<a href="?status='.substr($k,2).'" '.(substr($k,2)==$status?'class="cur"':'').'>'.$v.'</a>';}

	$I_mall_classID=1;//钢材市场
	$form='Project';
	$title = '项目';
	$sTypes = array('请选择', '项目单位','项目名称','回报率','成交金额');
	$points = array('项目管理', $title.'列表' );
	$hides  = array('status'=>$status);
	$extend = array('marks'=>$marks);

	switch ($sType){
		case 1:
			$mWhere = "and Vc_company like '%$sKey%'";
			break;
		case 2:
			$mWhere = "and Vc_name like '%$sKey%' ";
			break;
		case 3:
			$mWhere = "and N_reward like '%$sKey%' ";
			break;
		case 4:
			$mWhere = "and N_amount like '%$sKey%'";
			break;
		default:
			$mWhere = "and (Vc_company like '%$sKey%' or Vc_name like '%$sKey%' or N_reward like '%$sKey%' or N_amount like '%$sKey%')";
			break;
	}
	$mWhere .= ' and I_active='.$status;
	$tables = 'sm_project_deal  where Status=1 '.$mWhere;
	$sql = "SELECT * FROM {$tables} order by id desc";
	$sqlcount = "SELECT count(*) FROM {$tables} ";
	$pagesize = $Config->AdminPageSize;
	$pagecount = 1;$rscount=0;
	$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);
	$ths = array();
	$ths[]=array('val'=>'ID', 'wid'=>'');
	$ths[]=array('val'=>'项目单位', 'wid'=>'');
	$ths[]=array('val'=>'项目名称', 'wid'=>'');
	$ths[]=array('val'=>'年化回报', 'wid'=>'');
	$ths[]=array('val'=>'成交金额', 'wid'=>'');
	$ths[]=array('val'=>'活动', 'wid'=>'');
	$ths[]=array('val'=>'操作', 'wid'=>'150');
	$tds = array();
	if(is_array($Rs)){
		$pagecount = $Rs[0]['pagecount'];
		$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
		for($i=1;$i<count($Rs) ;$i++){
			$opts = "<a href='{$form}DealMdy.php?Work=MdyReco&Id={$Rs[$i]['id']}' class='hs' h='450' title='编辑$title'>编辑</a>" ;
			$_td  = '<td>'. $Rs[$i]['id'].'</td>';
			$_td .= '<td>'. $Rs[$i]['Vc_company'].'</td>';
			$_td .= '<td>'. $Rs[$i]['Vc_name'].'</td>';
			$_td .= '<td>'. $Rs[$i]['N_reward'] .'%</td>';
			$_td .= '<td>'. $Rs[$i]['N_amount'] .'万</td>';
			$_td .= '<td>'.($Rs[$i]['I_active']==1?'活动':'禁用').'</td>';
			$_td .= '<td>'.$opts.'</td>';
			$tds[$Rs[$i]['id']]=$_td;
		}
	}
	$DataBase->CloseDataBase();

	$helps  = array();
	$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
	$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);
		$extend['gbtns'] = array('<a href="ProjectDealMdy.php?Work=AddReco" class="hs" h="450" title="添加'.$title.'"><span>添加'.$title.'</span></a>');
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