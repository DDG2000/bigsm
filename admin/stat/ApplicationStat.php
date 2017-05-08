<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2014-06-08
**本页： 项目状态 统计 饼图
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_APPLICATION_1');
$dkmxbool=false;
if($Admin->CheckPopedom('SC_LOAN_APP')){
	$dkmxbool=true;
}

$stime = $FLib->RequestChar('stime',1,100,'参数',1);
$etime = $FLib->RequestChar('etime',1,100,'参数',1);
$ty = $FLib->RequestInt('ty',0,9,'分类');
$UrlInfo = "&etime=" .$etime ."&stime=" . $stime ."&ty=" . $ty;

$title = '项目状态';
$points = array('统计管理', ($ty==1?'合作机构':'').'项目统计', $title );
$extend = array();
$extend['hides'] = array();

$sqlw = '';
if($stime != ''){
	$sqlw .= " and a.Createtime >= '$stime'";
}
if($etime != ''){
	$sqlw .= " and a.Createtime <= '$etime'";
}
if($ty==1){
	$sqlw .= " and a.I_bondingcompanyID>0";//and a.I_classID=3
}

$sql = "select count(*) as nums, I_status from p2p_application a where Status>0 $sqlw group by I_status";
$Rs = $DataBase->GettArrayResult($sql);
/*w,h宽高; title,subtitle:标题副标题; ytitle,yt1,yt2:数据项提示及分割提示; 
pie:图表数据
	datas:统计项及其对应百分比 ['name1',45.0],{name:'name2',y:12.8,sliced:true,selected:true}
*/
$stat = array('w'=>'90%','h'=>500,'title'=>$title,'subtitle'=>'','ytitle'=>'数量(百分比)');
$stat['items'] = '';
$stat['datas'] = '';
$ths = array();
$ths[]=array('val'=>'分类', 'wid'=>'');
$ths[]=array('val'=>'数量', 'wid'=>'');
if($dkmxbool){
$ths[]=array('val'=>'操作', 'wid'=>'');
}
$tds = $itmes = $datas = array();
$Total = 0;

$status=$DDIC['p2p_application.I_status'];
foreach ($status as $k=>$vb){
	$nums = 0;
	$_td  = '<td>'.$vb.'</td>';
	foreach($Rs as $v){
		if($k==$v['I_status']){
			$Total += $v['nums'];
			$nums = $v['nums'];
			break;
		}
	}
	$_td .= '<td>'. $nums .'</td>';
	if($dkmxbool){
		$_td .= '<td><a href="../p2p/ApplicationList.php?status='.$k.'">查看明细</a></td>';
	}
	$tds[]=$_td;
}
$rep = 100;
if($Rs){
	foreach($Rs as $v){
		$vp = number_format((100*$v['nums']/$Total), 2, '.', '');
		$rep -= $vp;
		$datas[] = '["'.getApplicationStatus($v['I_status']).'",'.$vp.']';
	}
}else{
	$datas[] = '{name:"无数据",y:100,sliced:true,selected:true}';
}
/* if($rep>0){
	$datas[] = '{name:"其它",y:'.$rep.',sliced:true,selected:true}';//'["其它",'.$rep.']';
} */
$stat['datas'] = implode(',', $datas);

$list=array('ths'=>$ths,'tds'=>$tds);
$DataBase->CloseDataBase();

$helps  = array('显示时间段内会员来源统计');
$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?s=1'.$UrlInfo);

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'extend', $extend );
$tpl->assign( 'stat', $stat );
$tpl->assign( 'list', $list );
$tpl->assign( 'helps', $helps );

$tpl->draw('stat_pie'.$raintpl_ver);
exit;
}
?>
