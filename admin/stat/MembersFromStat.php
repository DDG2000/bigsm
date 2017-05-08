<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-12-14
**本页： 会员来源 统计
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$stime = $FLib->RequestChar('stime',1,100,'参数',1);
$etime = $FLib->RequestChar('etime',1,100,'参数',1);
$t = $FLib->RequestChar('t',1,100,'分类',1);
$UrlInfo = "&etime=" .$etime ."&stime=" . $stime;

$title = '会员来源';
$points = array('统计管理', '会员统计', $title );
$extend = array();
$extend['hides'] = array();

$mWhere = '';
if($stime != ''){
	$mWhere .= " and a.Createtime >= '$stime'";
}
if($etime != ''){
	$mWhere .= " and a.Createtime <= '$etime'";
}
if($t=='partner'){//本站用户 统计分类， 注册和微博登录
	$mWhere .= " and a.Vc_introducer=''";
	$sql = 'select count(*) as sumcount, a.Vc_introducer,a.I_partnertype,b.Vc_name from user_base as a left join site_partner as b on a.Vc_introducer = b.Vc_identity where a.status>0 '.$mWhere.' group by a.Vc_introducer,a.I_partnertype order by sumcount desc limit 0,10';
}elseif($t=='all'){//将微博用户视为特殊合作站点
	$sql = 'select count(*) as sumcount, a.Vc_introducer,a.I_partnertype,b.Vc_name from user_base as a left join site_partner as b on a.Vc_introducer = b.Vc_identity where a.status>0 '.$mWhere.' group by a.Vc_introducer,a.I_partnertype order by sumcount desc limit 0,10';
}else{//来源统计 微博视为本站用户
	$sql = 'select count(*) as sumcount, a.Vc_introducer,0 as I_partnertype,b.Vc_name from user_base as a left join site_partner as b on a.Vc_introducer = b.Vc_identity where a.status>0 '.$mWhere.' group by a.Vc_introducer order by sumcount desc limit 0,10';
}
$sql0 = 'select count(*) sumcount from user_base a where a.status>0 '.$mWhere.'';
$Rs = $DataBase->GetResultOne($sql0);
$Total = isset($Rs[0]) ? $Rs[0]:0;
$Rs = $DataBase->GettArrayResult($sql);
/*w,h宽高; title,subtitle:标题副标题; ytitle,yt1,yt2:数据项提示及分割提示; 
pie:图表数据
	datas:统计项及其对应百分比 ['name1',45.0],{name:'name2',y:12.8,sliced:true,selected:true}
*/
$stat = array('w'=>'90%','h'=>500,'title'=>$title,'subtitle'=>'','ytitle'=>'数量(百分比)');
$stat['items'] = '';
$stat['datas'] = '';
$ths = array();
$ths[]=array('val'=>'来源名', 'wid'=>'');
$ths[]=array('val'=>'数量', 'wid'=>'');
$tds = $itmes = $datas = array();
foreach($Rs as $v){
	$name = $v['Vc_name'];
	if($v['Vc_introducer']==''){
		$name='本站';
		if($v['I_partnertype']==1) $name='新浪';
		if($v['I_partnertype']==2) $name='QQ';
	}
	$_td  = '<td title="'.$v['Vc_introducer'].'">'.$name.'</td>';
	$_td .= '<td>'. $v['sumcount'] .'</td>';
	$tds[]=$_td;
}
//按单元字母排序
function cmp($a, $b){
	$a = iconv('UTF-8', 'GBK//IGNORE',$a['Vc_name']);
	$b = iconv('UTF-8', 'GBK//IGNORE',$b['Vc_name']);
    if ($a == $b) {return 0;}
    return ($a < $b) ? -1 : 1;
}
//usort($Rs, "cmp");
$rep = 100;
foreach($Rs as $v){
	$vp = number_format((100*$v['sumcount']/$Total), 2, '.', '');
	$rep -= $vp;
	$name = $v['Vc_name'];
	if($v['Vc_introducer']==''){
		$name='本站';
		if($v['I_partnertype']==1) $name='新浪';
		if($v['I_partnertype']==2) $name='QQ';
	}
	$datas[] = '["'.$name.'",'.$vp.']';
}
if($rep>0){
	$datas[] = '{name:"其它",y:'.$rep.',sliced:true,selected:true}';//'["其它",'.$rep.']';
}
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
