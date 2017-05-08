<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 首页浏览量 统计
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$UrlInfo = "&etime=" .$etime ."&stime=" . $stime;

$title = '首页浏览量';
$points = array('统计管理', '浏览量统计', $title );
$extend = array();


$mWhere = '';
$Rs = $DataBase->GetResultOne('select count(*) as pcount from stat_log_index where status=1');
$Total = $Rs[0];
$Rs = $DataBase->GetResultOne('select count(*) as pcount from stat_log_index where status=1 and Createtime>"'.date('Y-m-d').'"');
$Today = $Rs[0];

$sql = "SELECT count(*),left(Createtime,7) as lefttime from stat_log_index WHERE status=1 group by lefttime order by lefttime asc";
$Rs = $DataBase->GettArrayResult($sql);
$da = array();
$mons = array('一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月');
$_tmp = array();
foreach($mons as $v){$_tmp[]=0;}
foreach($Rs as $v){
	$year = substr($v['lefttime'],0,4);
	$mon = intval(substr($v['lefttime'],5,2));
	if (!array_key_exists($year, $da)) {
		$da[$year] = $_tmp;
	}
	$da[$year][$mon-1] = $v[0];
}

/*w,h宽高; title,subtitle:标题副标题; ytitle,yt1,yt2:数据项提示及分割提示; 
pie:图表数据
	itmes:统计项说明
	datas:统计项及其对应值 {name: '2013年',data: [7.0, 6.9,...]},{..}
*/
$stat = array('w'=>'90%','h'=>400,'title'=>$title,'subtitle'=>'首页总浏览量:'.$Total.',今日浏览量:'.$Today,'ytitle'=>'浏览量','yt2'=>'');
$stat['items'] = '';
$stat['datas'] = '';
$ths = array(array('val'=>'年份', 'wid'=>''));
foreach($mons as $v){
	$ths[]=array('val'=>$v, 'wid'=>'');
}
$tds = $itmes = $datas = array();
foreach($da as $k=>$v){
	$_td = '<td>'. $k .'年</td>';
	foreach($v as $vv){
		$_td .= '<td>'. $vv .'</td>';
	}
	$tds[]=$_td;
	$datas[]='{name:"'.$k.'年",data:['.(implode(',', $v)).']'.'}';
}
$list=array('ths'=>$ths,'tds'=>$tds);

$itmes = $mons;
$stat['items'] = '"'.implode('","', $itmes).'"';
$stat['datas'] = implode(',', $datas);

$DataBase->CloseDataBase();

$helps  = array('按月份显示每年的首页浏览量情况');
$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?s=1'.$UrlInfo);

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'extend', $extend );
$tpl->assign( 'stat', $stat );
$tpl->assign( 'list', $list );
$tpl->assign( 'helps', $helps );

$tpl->draw('stat_line'.$raintpl_ver);
exit;
}
?>
