<?php
if(1){
/****************************************************************** 
**创建者：sakura
**创建时间：2014-10-08
**本页： 资金统计 饼图
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_STAT_APPLICATION_3');

if($raintpl_cache && $cache = $tpl->cache('stat_fund', 60, 1) ){echo $cache;	exit;}

$UrlInfo = "";

$title = '资金统计';
$points = array('统计管理', '项目统计', $title );
$extend = array();
$extend['hides'] = array();

/* require_once(WEBROOT.'pay'.L.'api.class.php');
$Api = getApiClass(); */
require_once(WEBROOT.'pay'.L.'moneymm'.L.'api'.L.'api.class.php');
$Api = getApiClass();
$datas = array();
$datas1 = array();
$notinfo = array();

$subSql1 = "SELECT *, 0 isAdmin FROM p2p_repayment WHERE I_operation<>2 ";
$subSql2 = "SELECT *, 1 isAdmin FROM p2p_repayment_admin ";
$unionSql = "(".$subSql1." UNION ".$subSql2.")";

//账户总余额
$sql = "select sum(N_amount) N_amount,sum(N_amount_freeze) N_amount_freeze from user_base where Status=1 and ID>999";
$baseamount=$DataBase->fetch_one($sql);
$datas['a1']=iset($baseamount['N_amount'],0.00);
//$datas1[] = array(0=>'账户总余额',1=>$datas['a1'],2=>'a1');
//待收总金额
$sql = "select sum(N_capital+N_interest) from p2p_repayment where Status=1 and I_operation=0 and I_type=1";
$datas['a2']=iset($DataBase->fetch_val($sql),0.00);
//$datas1[] = array(0=>'待收总金额',1=>$datas['a2'],2=>'a2');
//托管资金余额
$datas['a3']=$datas['a1']+$datas['a2'];
$datas1[] = array(0=>'托管资金余额',1=>$datas['a3'],2=>'a3');
//充值总额
$sql = "select sum(N_income) from p2p_record_cash where I_type = 1 and Status=1 and I_userID>999";
$datas['a4']=iset($DataBase->fetch_val($sql),0.00);
$datas1[] = array(0=>'充值总额（不包含平台账户）',1=>$datas['a4'],2=>'a4');
//提现总额
$sql = "select sum(N_expend-N_income) from p2p_record_cash where I_type = 2 and Status=1 and I_userID>999";
$datas['a5']=iset($DataBase->fetch_val($sql),0.00);
$datas1[] = array(0=>'提现总额（不包含平台账户）',1=>$datas['a5'],2=>'a5');
//借款总金额
$sql = "select sum(N_capital) from p2p_repayment where Status=1 and I_type=1";
$datas['a6']=iset($DataBase->fetch_val($sql),0.00);
$datas1[] = array(0=>'借款总金额',1=>$datas['a6'],2=>'a6');
//利息总金额
$sql = "select sum(N_interest) N_interest from p2p_repayment where Status=1 and I_type=1";
$datas['a7']=iset($DataBase->fetch_val($sql),0.00);
$datas1[] = array(0=>'利息总金额',1=>$datas['a7'],2=>'a7');
//风险备付金收取总额+明细    ,N_reserve,N_reserverate,N_amount借款本金、风险备付金费率和金额
$sql = "select sum(N_reserve) from p2p_application where I_status>=50 and Status=1";
$datas['a8']=iset($DataBase->fetch_val($sql),0.00);
$datas1[] = array(0=>'风险备付金收取总额',1=>$datas['a8'],2=>'a8');
$notinfo[] = 'a8';
//当前备付金账户余额
$sql = "select Vc_openid from user_base where ID=".$g_conf['cfg_loan_reserve_account'];
$Vc_openid=$DataBase->fetch_val($sql);
/* $r = $Api->getuser($Vc_openid);
if($r['isSign']&&iset($r['resultCode'])=='success'){
	$N_amount_new = $r['balance'];
	$N_amount_freeze_new = $r['freezeBalance'];
} */
$r = $Api->getuser($Vc_openid);
$datas['a9']=$r[0]-$r[2];
$datas1[] = array(0=>'当前备付金账户余额',1=>$datas['a9'],2=>'a9');
//借款管理费收取总额+明细,N_borrow,N_borrowrate,N_amount借款本金、管理费费率和管理费金额
$sql="select sum(N_borrow) from p2p_application where I_status>=50 and Status=1";
$datas['a10']=iset($DataBase->fetch_val($sql),0.00);
$datas1[] = array(0=>'借款管理费收取总额',1=>$datas['a10'],2=>'a10');
$notinfo[] = 'a10';
//当前管理费账户余额
$sql = "select Vc_openid from user_base where ID={$g_conf['cfg_loan_management_account']}";
$Vc_openid=$DataBase->fetch_val($sql);
$r = $Api->balanceQuery($Vc_openid,2);
$datas['a11']=$r[1];
$datas1[] = array(0=>'当前管理费账户可用余额',1=>$datas['a11'],2=>'a11');
//利息管理费
$sql="Select sum(N_ifee) from p2p_record_cash where I_type=52 and I_userID={$g_conf['cfg_loan_management_account']}";
$datas['a12']=iset($DataBase->fetch_val($sql),0.00);
$datas1[] = array(0=>'利息管理费',1=>$datas['a12'],2=>'a12');
//逾期总金额+明细
$sql="Select sum(N_capital+N_interest) from {$unionSql} a where Status=1 and I_status =2 and I_type=1";
$datas['a13']=iset($DataBase->fetch_val($sql),0.00);
$datas1[] = array(0=>'逾期总金额',1=>$datas['a13'],2=>'a13');
$notinfo[] = 'a13';
//平台代偿总金额+明细
$sql="Select sum(N_amount) from p2p_apply_substitute where Status=1 and I_repayID={$g_conf['cfg_loan_reserve_account']} and (I_Status=3)";
$datas['a14']=iset($DataBase->fetch_val($sql),0.00);
$datas1[] = array(0=>'平台代偿总金额',1=>$datas['a14'],2=>'a14');
$notinfo[] = 'a14';
//担保机构代偿总金额+明细
$sql="Select sum(N_amount) from p2p_apply_substitute where Status=1 and I_repayID<>{$g_conf['cfg_loan_reserve_account']} and (I_Status=3)";
$datas['a15']=iset($DataBase->fetch_val($sql),0.00);
$datas1[] = array(0=>'担保机构代偿总金额',1=>$datas['a15'],2=>'a15');
$notinfo[] = 'a15';
//罚息总金额+明细
$sql="Select sum(N_fee) from {$unionSql} a where Status=1 and I_status=2 and I_type=1";
$datas['a16']=iset($DataBase->fetch_val($sql),0.00);
$datas1[] = array(0=>'罚息总金额',1=>$datas['a16'],2=>'a16');
$notinfo[] = 'a16';
/*含代偿部分需扣除
from p2p_apply_substitute where status=1 and I_status<4 
I_repaymentID=$repayid */
//闲置资金余额
$datas['a17']=($baseamount['N_amount']-$baseamount['N_amount_freeze']);
$datas1[] = array(0=>'闲置资金余额',1=>$datas['a17'],2=>'a17');
//逾期金额
/* $sql="Select sum(N_capital+N_interest) from p2p_repayment where Status=1 and I_status =2 and I_type=1";
$datas['a18']=iset($DataBase->fetch_val($sql),0.00); */
//$datas1[] = array(0=>'逾期金额',1=>$datas['a18'],2=>'a18');
//成交金额
$sql="Select sum(N_capital+N_interest) from p2p_repayment where Status=1 and I_type=1";
$datas['a19']=iset($DataBase->fetch_val($sql),0.00);
//$datas1[] = array(0=>'成交金额',1=>$datas['a19'],2=>'a19');
//坏账率  坏账率=逾期金额/成交金额
$datas['a20']=$datas['a19']>0?sprintf("%.2f",($datas['a13']/$datas['a19']*100)):0.00;
$datas1[] = array(0=>'坏账率',1=>$datas['a20'].'%',2=>'a20');
//逾期期数
$sql="Select count(*) from {$unionSql} a where Status=1 and I_type=1 and I_status =2";
$datas['a21']=iset($DataBase->fetch_val($sql),0.00);
//$datas1[] = array(0=>'逾期期数',1=>$datas['a21'],2=>'a21');
//总还款期数
$sql="Select count(*) from p2p_repayment where Status=1 and I_type=1";
$datas['a22']=iset($DataBase->fetch_val($sql),0.00);
//$datas1[] = array(0=>'总还款期数',1=>$datas['a22'],2=>'a22');
//逾期率     逾期期数/总还款期数
$datas['a23']=$datas['a22']>0?sprintf("%.2f",($datas['a21']/$datas['a22']*100)):0.00;
$datas1[] = array(0=>'逾期率',1=>$datas['a23'].'%',2=>'a23');

$stat = array('w'=>'90%','h'=>500,'title'=>$title,'subtitle'=>'','ytitle'=>'资金统计','yt2'=>'');
$stat['items'] = '';
$stat['datas'] = '';

$ths = array();
$ths[]=array('val'=>'名称', 'wid'=>'');
$ths[]=array('val'=>'金额', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');
$tds = $itmes = $datas2 = array();
foreach ($datas1 as $k=>$v){
	$itmes[] = $v[0];
	$datas2[] = $v[1];
	
	$_td  = '<td>'.$v[0].'</td>';
	$_td .= '<td>'. $v[1].'</td>';
	if(in_array($v[2],$notinfo)){
		$_td .= '<td><a title="'.$v[0].'明细" href="FundStatInfo.php?type='.$v[2].'">查看明细</a></td>';
	}else{
		$_td .= '<td></td>';
	}
	$tds[]=$_td;
}
$stat['items'] = '"'.implode('","', $itmes).'"';
$stat['datas'] = '{name:"资金统计",data:['.(implode(',', $datas2)).']'.'}';

$stat['xlens'] = count($itmes);

$list=array('ths'=>$ths,'tds'=>$tds);

$DataBase->CloseDataBase();

$helps  = array('显示资金统计');
$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?s=1'.$UrlInfo);

$stat=null;//去掉tubiao
//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'extend', $extend );
$tpl->assign( 'stat', $stat );
$tpl->assign( 'list', $list );
$tpl->assign( 'helps', $helps );

$tpl->draw('stat_fund'.$raintpl_ver);
exit;
}
?>
