<?php
if(1){
/****************************************************************** 
**创建者：sakura
**创建时间：2014-10-08
**本页： 逾期
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_APPLICATION_5');

//use cache
if($raintpl_cache && $cache = $tpl->cache('stat_ymd', 60, 1) ){echo $cache;	exit;}

$UrlInfo = '';

$starttime = $FLib->RequestChar('starttime','',10,'参数',1);
$endtime = $FLib->RequestChar('endtime','',10,'参数',1);

$type = $FLib->RequestInt('type',2,9,'类型');

$title = '逾期统计';
$points = array('统计管理', '项目统计', $title );
$extend = array();
$extend['hides'] = array('type'=>$type);

$extend['js'] = '
<link type="text/css" rel="stylesheet" href="../include/timepicker/css/jquery-ui.css" >
<script src="../js/form.checkfun.js" type="text/javascript"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-slide.min.js"></script>
<script type="text/javascript" src="../include/timepicker/js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">
$(function(){
	$("input[name=\'starttime\']").datepicker({
		changeYear:true,
		onSelect : function(selectedDate) {
			$("input[name=\'endtime\']").datepicker("option", "minDate", selectedDate); 
		}
	});
	$("input[name=\'endtime\']").datepicker({
		changeYear:true,
		onSelect : function(selectedDate) {
			$("input[name=\'endtime\']").datepicker("option", "maxDate", selectedDate); 
		}
	});  
	'.($starttime?'$("input[name=\'endtime\']").datepicker("option", "minDate", "'.$starttime.'");':'').'
})
</script>
';
$extend['sTypes'] = array('初始时间：<input class="txt_put_date" name="starttime" value="'.$starttime.'" data-time="" onclick="nodatepatFun" tp="初始时间" maxlength="10"/>'
		,'结束时间：<input class="txt_put_date" name="endtime" value="'.$endtime.'" data-time="" onclick="nodatepatFun" tp="结束时间" maxlength="10"/>');
$extend['ymd'] = '
<a href="OverdueStat.php?type=1" style="'.($type==1?'color: red;font-weight: bold;':'').'">年</a>
<a href="OverdueStat.php?type=2" style="'.($type==2?'color: red;font-weight: bold;':'').'">月</a>
<a href="OverdueStat.php?type=3" style="'.($type==3?'color: red;font-weight: bold;':'').'">日</a>
';

$mTimeWhere = '';
$tTimeWhere = '';
if($starttime){
	$mTimeWhere .= " and Dt_repay >= '{$starttime} 00:00:00' ";
	$tTimeWhere .= " and b.Dt_repay >= '{$starttime} 00:00:00' ";
}
if($endtime){
	$mTimeWhere .= " and Dt_repay <= '{$endtime} 23:59:59' ";
	$tTimeWhere .= " and b.Dt_repay <= '{$endtime} 23:59:59' ";
}
$Total = array();

$subSql1 = "SELECT *, 0 isAdmin FROM p2p_repayment WHERE I_operation<>2 ";
$subSql2 = "SELECT *, 1 isAdmin FROM p2p_repayment_admin ";
$unionSql = "(".$subSql1." UNION ".$subSql2.")";

$Rs = $DataBase->GetResultOne('SELECT COUNT(*) appnums from
				(Select count(*) from '.$unionSql.' a
				where Status=1 and I_type=1 and I_status=2 '.$mTimeWhere.'
				group by I_applicationID) a');

$Total['appnums'] = $Rs[0];
$Rs = $DataBase->GetResultOne('Select sum(N_capital+N_interest) amount from '.$unionSql.' a
				where Status=1 and I_type=1 and I_status=2 '.$mTimeWhere);
$Total['amount'] = iset($Rs[0],'0.00');

$num = 7;
if($type == 1){
	$num = 4;
}else if($type == 3){
	$num = 10;
}

$sql="
SELECT count(*) nums,sum(N_capital+N_interest) amount,left(Dt_repay,".$num.") lefttime,
	sum(N_fee) fee,
	sum(if(I_operation=0,0,(N_capital+N_interest))) repaid,
	sum(if(I_operation=0,(N_capital+N_interest),0)) repayment,
	(SELECT sum(I_sorts) FROM
		(select I_sorts,left(b.Dt_repay,".$num.") lefttime
			from {$unionSql} b
			where b.Status=1 and b.I_type=1 and b.I_status=2 ".$tTimeWhere."
			group by left(b.Dt_repay,".$num."),b.I_applicationID
		) z
		where left(a.Dt_repay,".$num.")=z.lefttime group by z.lefttime
	)sortsnums,
	(select count(*) from
		(Select count(*) counts,left(b.Dt_repay,".$num.") lefttime from {$unionSql} b
			where b.Status=1 and b.I_type=1 and b.I_status=2 ".$tTimeWhere."
			group by left(b.Dt_repay,".$num."), b.I_applicationID
		) s
		where left(a.Dt_repay,".$num.")=s.lefttime group by s.lefttime
	) appnums
from {$unionSql} a
where a.Status=1 and a.I_type=1 and a.I_status=2 ".$mTimeWhere."
group by lefttime order by lefttime asc";
$Rs = $DataBase->GettArrayResult($sql);

$stat = array('w'=>'90%','h'=>400,'title'=>$title,
		'subtitle'=>'逾期项目数:'.iset($Total['appnums'],0).',逾期金额:'.iset($Total['amount'],'0.00'),
		'ytitle'=>'逾期金额','yt2'=>'元');

$ths = $tds = $itmes = $datas = array();
$ths[] = array('val'=>'时间', 'wid'=>'');
$ths[] = array('val'=>'逾期个数', 'wid'=>'');
$ths[] = array('val'=>'逾期项目数', 'wid'=>'');
$ths[] = array('val'=>'逾期金额', 'wid'=>'');
$ths[] = array('val'=>'已还部分', 'wid'=>'');
$ths[] = array('val'=>'未还部分', 'wid'=>'');
$ths[] = array('val'=>'罚息总额', 'wid'=>'');
$ths[] = array('val'=>'逾期率', 'wid'=>'');
$stat['items'] = '';
$stat['datas'] = '';
if($type == 1){
	$da = array();
	$years = array();
	if($Rs){
		foreach($Rs as $v){
			$year = $v['lefttime'].'年';
			if (!array_key_exists($year, $years)) {
				$years[] = $year;
			}
			$da[$year] = $v['amount'];
			$_td = '<td><a href="OverdueStatInfo.php?starttime='.$v['lefttime'].'-01-01&endtime='.$v['lefttime'].'-12-31">'. $v['lefttime'] .'</a></td>';
			$_td .= '<td>'. $v['nums'] .'</td>';
			$_td .= '<td>'. $v['appnums'] .'</td>';
			$_td .= '<td>'. $v['amount'] .'</td>';
			$_td .= '<td>'. $v['repaid'] .'</td>';
			$_td .= '<td>'. $v['repayment'] .'</td>';
			$_td .= '<td>'. $v['fee'] .'</td>';
			$_td .= '<td>'. sprintf("%.2f",$v['nums']/$v['sortsnums']).'</td>';
			$tds[]=$_td;
		}
	}else{
		$year = date("Y年");
		$years[] = $year;
		$da[$year] = 0;
	}
	$stat['items'] = '"'.implode('","', $years).'"';
	$stat['datas'] = '{name:"年统计",data:['.(implode(',', $da)).']'.'}';
	$stat['legend']='false';
}else if($type == 2){
	$da = array();
	$mons = array('一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月');
	$_tmp = array();
	foreach($mons as $v){$_tmp[]=0;}
	if($Rs){
		foreach($Rs as $v){
			$year = substr($v['lefttime'],0,4);
			$mon = intval(substr($v['lefttime'],5,2));
			if (!array_key_exists($year, $da)) {
				$da[$year] = $_tmp;
			}
			$da[$year][$mon-1] = $v['amount'];
			$_td = '<td><a href="OverdueStatInfo.php?starttime='.$v['lefttime'].'-01&endtime='.date('Y-m-t',strtotime($v['lefttime'])).'">'. $v['lefttime'] .'</a></td>';
			$_td .= '<td>'. $v['nums'] .'</td>';
			$_td .= '<td>'. $v['appnums'] .'</td>';
			$_td .= '<td>'. $v['amount'] .'</td>';
			$_td .= '<td>'. $v['repaid'] .'</td>';
			$_td .= '<td>'. $v['repayment'] .'</td>';
			$_td .= '<td>'. $v['fee'] .'</td>';
			$_td .= '<td>'. sprintf("%.2f",$v['nums']/$v['sortsnums']).'</td>';
			$tds[]=$_td;
		}
		foreach($da as $k=>$v){
			$datas[]='{name:"'.$k.'年",data:['.(implode(',', $v)).']'.'}';
		}
	}else{
		$nowyear=date('Y');
		if($endtime)$nowyear=substr($endtime,0,4);
		$datas[]='{name:"'.$nowyear.'年",data:['.(implode(',', $_tmp)).']'.'}';
	}
	$itmes = $mons;
	$stat['items'] = '"'.implode('","', $itmes).'"';
	$stat['datas'] = implode(',', $datas);
}else if ($type == 3){
	$da = array();
	$_tmp = array();
	$days = array();//以1周为调动
	for($i=0;$i<366;$i++){
		$days[]=date('m月d日',strtotime('+ '.$i.' days',strtotime('2008-01-01')));
	}
	foreach($days as $v){$_tmp[]=0;}
	if($Rs){
		foreach($Rs as $v){
			$year = substr($v['lefttime'],0,4);
			$day =  floor((strtotime($v['lefttime'])-strtotime(date('Y-01-01',strtotime($v['lefttime']))))/(24*60*60));
			if(!(($year%4==0&&$year%100!=0)||$year%400==0)&&$day>59){
				$day++;
			}
			if (!array_key_exists($year, $da)) {
				$da[$year] = $_tmp;
			}
			$da[$year][$day] = $v['amount'];
			$_td = '<td><a href="OverdueStatInfo.php?starttime='.$v['lefttime'].'&endtime='.$v['lefttime'].'">'. $v['lefttime'] .'</a></td>';
			$_td .= '<td>'. $v['nums'] .'</td>';
			$_td .= '<td>'. $v['appnums'] .'</td>';
			$_td .= '<td>'. $v['amount'] .'</td>';
			$_td .= '<td>'. $v['repaid'] .'</td>';
			$_td .= '<td>'. $v['repayment'] .'</td>';
			$_td .= '<td>'. $v['fee'] .'</td>';
			$_td .= '<td>'. sprintf("%.2f",$v['nums']/$v['sortsnums']) .'</td>';
			$tds[]=$_td;
		}
		foreach($da as $k=>$v){
			$datas[]='{name:"'.$k.'",data:['.(implode(',', $v)).']'.'}';
		}
	}else{
		$nowyear=date('Y');
		if($endtime)$nowyear=substr($endtime,0,4);
		$datas[]='{name:"'.$nowyear.'年",data:['.(implode(',', $_tmp)).']'.'}';
	}
	$itmes = $days;
	$stat['items'] = '"'.implode('","', $itmes).'"';
	$stat['datas'] = implode(',', $datas);
	$stat['xlens'] = count($days);
	$stat['xnum'] = 14;
}
$list=array('ths'=>$ths,'tds'=>$tds);

$DataBase->CloseDataBase();

$helps  = array('按年/月/日显示逾期情况',
		($type==1?'明细：点击 2014 则搜索 2014-01-01 00:00:00 至 2014-12-31 23:59:59 的明细':'').
		($type==2?'明细：点击 2014-11 则搜索 2014-11-01 00:00:00 至 2014-11-30 23:59:59 的明细':'').
		($type==3?'明细：点击 2014-11-12 则搜索 2014-11-12 00:00:00 至 2014-11-12 23:59:59 的明细':'')
	);
$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?s=1'.$UrlInfo);

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'extend', $extend );
$tpl->assign( 'stat', $stat );
$tpl->assign( 'list', $list );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'type', $type );


$tpl->draw('stat_ymd'.$raintpl_ver);
exit;
}
?>
