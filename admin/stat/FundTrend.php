<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2014-06-08
**本页： 资金走势：成交金额、综合年化收益、投资人数；折线 统计
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_APPLICATION_4');

$stime = $FLib->RequestChar('stime',1,100,'参数',1);
$etime = $FLib->RequestChar('etime',1,100,'参数',1);
$ty = $FLib->RequestInt('ty',0,9,'分类');
$UrlInfo = "&etime=" .$etime ."&stime=" . $stime ."&ty=" . $ty;

$title = '资金走势';
$points = array('统计管理', ($ty==1?'合作机构':'').'项目统计', $title );
$extend = array();

$isneedCreate = true; 
$nowdate = date('Y-m-d');
if( ($statdata = F('stat_fund'.$ty)) ){
	if(isset($statdata['time']) && $statdata['time']==$nowdate){
		$isneedCreate = false;
		$datas = $statdata['data'];
	}
}
//$isneedCreate = true;//-每次是否更新文件
//正式删除------begin
$isneedCreate = true;//-每次是否更新文件
if(file_exists(WEBROOTDATA."tmp_appstat.php")){require_once(WEBROOTDATA."tmp_appstat.php");}
//-----end

if($isneedCreate){
	$psize = 14;
	$beginday = date('Y-m-d',strtotime('-'.$psize.' day'));
	$sel = "N_invest amount,N_yearannualrate,I_investor,I_investnum,Vc_accounttime";
	$tables = 'p2p_day_stat';
	if($ty==1){ $tables = 'p2p_bc_day_stat'; }
	$sqlw = "from $tables where Status>0 and Vc_accounttime>='{$beginday}' group by Vc_accounttime";
	$orders = 'order by Vc_accounttime asc';
	$pda = $Db->getTableByPage(1,$psize,$sel,$sqlw,$orders);
	for($i=$psize;$i>0;$i--){
		$items[] = date('m月d日',strtotime('-'.$i.' day'));
		$moneylist[] = 0;
		$ratelist[] = 0;
		$lenderlist[] = 0;
		$lendnumlist[] = 0;
	}
	if($pda['count']>0){
		foreach($pda['data'] as $o){
			if(($k=array_search(date('m月d日',strtotime($o['Vc_accounttime'])), $items)) !== false){
				$moneylist[$k] = round($o[0]/10000, 4);
				$ratelist[$k] = floatval($o[1]);
				$lenderlist[$k] = floatval($o[2]);
				$lendnumlist[$k] = floatval($o[3]);
			}
		}
	}
	$data=array();
	$data['datalist']=$items;
	$data['moneylist']=$moneylist;
	$data['ratelist']=$ratelist;
	$data['lenderlist']=$lenderlist;
	$data['lendnumlist']=$lendnumlist;
	$datas = json_encode($data);
	
	$statdata=array();
	$statdata['data'] = $datas;
	$statdata['time'] = $nowdate;
	F('stat_fund'.$ty,$statdata);
}
$stat = $datas;
$DataBase->CloseDataBase();

$helps  = array('成交金额、综合年化收益、投资人数、投资成功笔数的最近14天的日统计');
$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?s=1'.$UrlInfo);

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'extend', $extend );
$tpl->assign( 'stat', $stat );
$tpl->assign( 'list', $list );
$tpl->assign( 'helps', $helps );

$tpl->draw('stat_lines_new'.$raintpl_ver);
exit;
}
?>
