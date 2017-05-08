<?php
/****************************************************************** 
**创建者：sakura
**创建时间：2014-10-08
**本页：  帮助类
**说明：
******************************************************************/
function statData($type,$code='',$name=''){
	if(!$type)
		return;
	$dat = array();
	$dat['type']=$type;
	$dat['code']=$code;
	$dat['name']=$name;
	switch ($type){
		case 'friend':
			$dat['title']='【<font color="red">'.$name.'</font>】好友推荐明细';
			$dat['filename']='好友推荐明细';
			$dat['tables']="tmp_memberuser_stat_friend t where t.I_userbaseID='{$code}'";
			break;
		case 'invest':
			$dat['title']='【<font color="red">'.$name.'</font>】投资总金额明细';
			$dat['filename']='投资总金额明细';
			$dat['tables']="tmp_memberuser_stat_invest t where t.I_userID='{$code}'";
			break;
		case 'invest_interest':
			$dat['title']='【<font color="red">'.$name.'</font>】投资利息明细';
			$dat['filename']='投资利息明细';
			$dat['tables']="tmp_memberuser_stat_invest t where t.I_userID='{$code}'";
			break;
		case 'fee':
			$dat['title']='【<font color="red">'.$name.'</font>】罚息收入明细';
			$dat['filename']='罚息收入明细';
			$dat['tables']="tmp_memberuser_stat_fee t where t.I_userID='{$code}'";
			break;
		case 'loan':
			$dat['title']='【<font color="red">'.$name.'</font>】借款总金额明细';
			$dat['filename']='借款总金额明细';
			$dat['tables']="tmp_memberuser_stat_loan t where t.I_userID='{$code}'";
			break;
		case 'loan_interest':
			$dat['title']='【<font color="red">'.$name.'</font>】借款利息明细';
			$dat['filename']='借款利息明细';
			$dat['tables']="tmp_memberuser_stat_loan t where t.I_userID='{$code}'";
			break;
		case 'efine':
			$dat['title']='【<font color="red">'.$name.'</font>】逾期明细';
			$dat['filename']='逾期明细';
			$dat['tables']="tmp_memberuser_stat_efine t where t.I_userID='{$code}'";
			break;
		case 'efee':
			$dat['title']='【<font color="red">'.$name.'</font>】罚息支出明细';
			$dat['filename']='罚息支出明细';
			$dat['tables']="tmp_memberuser_stat_efee t where t.I_userID='{$code}'";
			break;
		case 'income':
			$dat['title']='【<font color="red">'.$name.'</font>】待收总金额明细';
			$dat['filename']='待收总金额明细';
			$dat['tables']="tmp_memberuser_stat_income t where t.I_userID='{$code}'";
			break;
		case 'expend':
			$dat['title']='【<font color="red">'.$name.'</font>】待还总金额明细';
			$dat['filename']='待还总金额明细';
			$dat['tables']="tmp_memberuser_stat_expend t where t.I_userID='{$code}'";
			break;
		case 'repayment':
			$dat['title']='【<font color="red">'.$name.'</font>】还款中项目明细';
			$dat['filename']='还款中项目明细';
			$dat['tables']="tmp_memberuser_stat_repayment t where t.I_userID='{$code}'";
			break;
		case 'repaid':
			$dat['title']='【<font color="red">'.$name.'</font>】已还清项目明细';
			$dat['filename']='已还清项目明细';
			$dat['tables']="tmp_memberuser_stat_repaid t where t.I_userID='{$code}'";
			break;
		case 'sysget':
			$dat['title']='【<font color="red">'.$name.'</font>】系统奖励金额明细';
			$dat['filename']='系统奖励金额明细';
			$dat['tables']="tmp_memberuser_stat_sysget t where t.I_userID='{$code}'";
			break;
	}
	return $dat;
}


?>