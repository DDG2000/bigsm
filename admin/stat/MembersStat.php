<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 会员数量情况 统计
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_USER_1');

//use cache
//if($raintpl_cache && $cache = $tpl->cache('stat_ymd', 60, 1) ){echo $cache;	exit;}

$UrlInfo = '';

$starttime = $FLib->RequestChar('starttime',1,10,'参数',1);
$endtime = $FLib->RequestChar('endtime',1,10,'参数',1);

$type = $FLib->RequestInt('type',2,9,'类型');

$title = '会员数量情况';
$points = array('统计管理', '会员统计', $title );
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
		yearRange:"1900:'.date('Y').'",
		maxDate:0,
		onSelect : function(selectedDate) {
			$("input[name=\'endtime\']").datepicker("option", "minDate", selectedDate); 
		}
	});
	$("input[name=\'endtime\']").datepicker({
		changeYear:true,
		yearRange:"1900:'.date('Y').'",
		maxDate:0,
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
<a href="MembersStat.php?type=1" style="'.($type==1?'color: red;font-weight: bold;':'').'">年</a>
<a href="MembersStat.php?type=2" style="'.($type==2?'color: red;font-weight: bold;':'').'">月</a>
<a href="MembersStat.php?type=3" style="'.($type==3?'color: red;font-weight: bold;':'').'">日</a>
';

$mTimeWhere = ' and ID > 999 ';
if($starttime){
	$mTimeWhere .= " and Createtime >= '{$starttime} 00:00:00' ";
}
if($endtime){
	$mTimeWhere .= " and Createtime <= '{$endtime} 23:59:59' ";
}
$Rs = $DataBase->GetResultOne('select count(*) as pcount from user_base where ID>999 and status>0'.$mTimeWhere);
$Total = $Rs[0];
$Rs = $DataBase->GetResultOne('select count(*) as pcount from user_base where ID>999 and status>0 
		and Createtime >="'.date('Y-m-d').' 00:00:00" and Createtime <="'.date('Y-m-d').' 23:59:59"');
$Today = $Rs[0];

$num = 7;
if($type == 1){
	$num = 4;
}else if($type == 3){
	$num = 10;
}
$sql = "SELECT count(*),left(Createtime,".$num.") as lefttime from user_base WHERE ID>999 and status>0 ".$mTimeWhere." group by lefttime order by lefttime asc";
$Rs = $DataBase->GettArrayResult($sql);

$stat = array('w'=>'90%','h'=>400,'title'=>$title,'subtitle'=>'会员总数:'.$Total.'人'.($endtime == date('Y-m-d')?',当天注册数:'.$Today.'人':''),'ytitle'=>'注册人数','yt2'=>'人');

$startyear = $starttime?intval(substr($starttime,0,4)):null;
$endyear = $endtime?intval(substr($endtime,0,4)):intval(date('Y'));
if($type == 1){
	$da = array();
	$years = array();
	if($Rs){
		foreach($Rs as $v){
			$year = $v['lefttime'].'年';
			if (!array_key_exists($year, $years)) {
				$years[] = $year;
			}
			$da[$year] = $v[0];
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
	$stat['items'] = '';
	$stat['datas'] = '';
	$itmes = $datas = array();
	if($Rs){
		foreach($Rs as $v){
			$year = substr($v['lefttime'],0,4);
			$mon = intval(substr($v['lefttime'],5,2));
			if (!array_key_exists($year, $da)) {
				$da[$year] = $_tmp;
			}
			$da[$year][$mon-1] = $v[0];
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
			$da[$year][$day] = $v[0];
		}
		foreach($da as $k=>$v){
			$datas[]='{name:"'.$k.'年",data:['.(implode(',', $v)).']'.'}';
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
$stat['ymin'] = '1';

$DataBase->CloseDataBase();

$helps  = array('按年/月/日显示会员注册情况');
$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?s=1'.$UrlInfo);

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'btns', $btns );
$tpl->assign( 'extend', $extend );
$tpl->assign( 'stat', $stat );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'type', $type );

$tpl->draw('stat_ymd'.$raintpl_ver);
exit;
}
?>
