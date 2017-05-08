<?php
if(!defined('WEBROOT'))exit;
$User = new User();

function str2miriad($s,$fix=4){
	return round($s,2);//round($s/10000, $fix);
}

$w=$FL->requeststr('w',1,0,'w',1,1);
$m.='_'.$w;
$p['ty'] = $FL->requeststr('ty',1,0,'ty',1);
if(!in_array($p['ty'], array('month','year'))){$p['ty']='month';}
$stat='';
$psizea = array(8,5);
switch($w){
case 'in1':
	if($p['ty']=='year'){
	}else{
		$psize = $psizea[0]-1;
		for($i=$psize;$i>=0;$i--){
			$items[] = date('Y/m',mktime(0,0,0,date('n')-$i,1,date('Y')));
			$da1[] = 0;
			$da2[] = 0;
			$da3[] = 0;
		}
		$beginday = date('Y-m-01',mktime(0,0,0,date('n')-$psize,1,date('Y')) );
		$sel = "sum(N_iinterest) N_iinterest,sum(N_ifine) N_ifine,sum(N_ipenalty) N_ipenalty,left(Createtime,7) as lefttime,sum(if(I_type=52,N_efee,0)) N_efee0";
		$sqlw = "from p2p_record_cash where Status=1 and I_userID=$uid and Createtime>='{$beginday}' group by lefttime";
		$orders = 'order by lefttime';
		$pda = $Db->getTableByPage(1,$psize+1,$sel,$sqlw,$orders);
		if($pda['count']>0){
			foreach($pda['data'] as $o){
				if(($k=array_search(str_replace('-','/',$o['lefttime']), $items)) !== false){
					$da1[$k] = str2miriad($o['N_iinterest']-$o['N_efee0']);//去除投资利息管理费
					$da2[$k] = str2miriad($o['N_ifine']);
					$da3[$k] = str2miriad($o['N_ipenalty']);
				}
			}
		}
	}
	$data=array();
	$data['datalist']=$items;
	$data['da1list']=$da1;
	$data['da2list']=$da2;
	$data['da3list']=$da3;
	$stat = json_encode($data);
	break;
case 'in2':
	if($p['ty']=='year'){
	}else{
		$psize = $psizea[0]-1;
		for($i=$psize;$i>=0;$i--){
			$items[] = date('Y/m',mktime(0,0,0,date('n')-$i,1,date('Y')));
			$da1[] = 0;
			$da2[] = 0;
		}
		$beginday = date('Y-m-01',mktime(0,0,0,date('n')-$psize,1,date('Y')) );
		$sel = "sum(b.N_amount) amount,a.I_classID,left(b.Createtime,7) as lefttime";
		$sqlw = "from p2p_application a left join p2p_bid b on a.ID=b.I_applicationID where a.Status=1 and b.Status=1 and b.I_deal=1 and b.I_userID=$uid and a.I_status>=50 and b.Createtime>='{$beginday}' group by lefttime,a.I_classID";
		$orders = 'order by lefttime';
		$pda = $Db->getTableByPage(1,($psize+1)*3,$sel,$sqlw,$orders);//3种标分类
		if($pda['count']>0){
			foreach($pda['data'] as $o){
				if(($k=array_search(str_replace('-','/',$o['lefttime']), $items)) !== false){
					$amount = str2miriad($o['amount']);
					if($o['I_classID']==1){$da1[$k] = $amount;}//信用标
					if($o['I_classID']==2){$da2[$k] = $amount;}//保理标
				}
			}
		}
	}
	$data=array();
	$data['datalist']=$items;
	$data['da1list']=$da1;
	$data['da2list']=$da2;
	$stat = json_encode($data);
	break;
case 'in3':
	break;
case 'lo1':
	if($p['ty']=='year'){
	}else{
		$psize = $psizea[0]-1;
		for($i=$psize;$i>=0;$i--){
			$j = 4-$i;//4-
			$items[] = date('Y/m', mktime(0,0,0,date('n')+$j,1,date('Y')) );
			$da1[] = 0;
			$da2[] = 0;
			$da3[] = 0;
			$da4[] = 0;
		}
		$beginday = date('Y-m-01',mktime(0,0,0,date('n')-$psize+4,1,date('Y')) );
		//N_efee 只计算 51借款管理费+92备付金  去除了 81投资奖励 52-投资管理费
		$sel = "sum(if(I_type in (51,92),N_efee,0)) N_efee,left(Createtime,7) as lefttime";//sum(N_einterest) N_einterest,sum(N_efine) N_efine,sum(N_epenalty) N_epenalty,
		$sqlw = "from p2p_record_cash where Status=1 and I_userID=$uid and Createtime>='{$beginday}' group by lefttime";
		$orders = 'order by lefttime';
		$pda = $Db->getTableByPage(1,$psize+1,$sel,$sqlw,$orders);
		if($pda['count']>0){
			foreach($pda['data'] as $o){
				if(($k=array_search(str_replace('-','/',$o['lefttime']), $items)) !== false){
					//$da1[$k] = str2miriad($o['N_einterest']);
					//$da2[$k] = str2miriad($o['N_efine']);
					//$da3[$k] = str2miriad($o['N_epenalty']);
					$da4[$k] = str2miriad($o['N_efee']);
				}
			}
		}
		//去除已付未付 不含代付
		$sel = "sum(N_capital) N_capital,sum(N_interest) N_interest,sum(if(I_type=1,N_fee,0)) N_efine,sum(if(I_type=3,N_fee,0)) N_epenalty,left(Dt_repay,7) as lefttime";
		$sqlw = "from p2p_repayment where Status=1 and I_operation<2 and I_repayID=$uid and Dt_repay>='{$beginday}' group by lefttime";
		$orders = 'order by lefttime';
		$pda = $Db->getTableByPage(1,$psize+1,$sel,$sqlw,$orders);
		if($pda['count']>0){
			foreach($pda['data'] as $o){
				if(($k=array_search(str_replace('-','/',$o['lefttime']), $items)) !== false){
					$da1[$k] = str2miriad($o['N_interest']);
					$da2[$k] = str2miriad($o['N_efine']);
					$da3[$k] = str2miriad($o['N_epenalty']);
				}
			}
		}
	}
	$data=array();
	$data['datalist']=$items;
	$data['da1list']=$da1;
	$data['da2list']=$da2;
	$data['da3list']=$da3;
	$data['da4list']=$da4;
	$stat = json_encode($data);
	break;
case 'lo2'://未来一段时间--和其他不同
	if($p['ty']=='year'){
	}else{
		$psize = $psizea[0]-1;
		for($i=0;$i<=$psize;$i++){
			$items[] = date('Y/m',mktime(0,0,0,date('n')+$i,1,date('Y')));
			$da1[] = 0;
			$da2[] = 0;
			$da3[] = 0;
		}
		$beginday = date('Y-m-01');
		$sel = "sum(N_capital) N_capital,sum(N_interest) N_interest,sum(N_fee) N_fee,left(Dt_repay,7) as lefttime";
		$sqlw = "from p2p_repayment where Status=1 and I_operation=0 and I_repayID=$uid and Dt_repay>='{$beginday}' group by lefttime";
		$orders = 'order by lefttime';
		$pda = $Db->getTableByPage(1,$psize+1,$sel,$sqlw,$orders);
		if($pda['count']>0){
			foreach($pda['data'] as $o){
				if(($k=array_search(str_replace('-','/',$o['lefttime']), $items)) !== false){
					$da1[$k] = str2miriad($o['N_capital']);
					$da2[$k] = str2miriad($o['N_interest']);
					$da3[$k] = str2miriad($o['N_fee']);
				}
			}
		}
	}
	$data=array();
	$data['datalist']=$items;
	$data['da1list']=$da1;
	$data['da2list']=$da2;
	$data['da3list']=$da3;
	$stat = json_encode($data);
	break;
case 'lo3':
	if($p['ty']=='year'){
	}else{
		$psize = $psizea[0]-1;
		for($i=$psize;$i>=0;$i--){
			$items[] = date('Y/m',mktime(0,0,0,date('n')-$i,1,date('Y')) );
			$da1[] = 0;
			$da2[] = 0;
			$da3[] = 0;
			$da4[] = 0;
			$da5[] = 0;
		}
		$beginday = date('Y-m-01',mktime(0,0,0,date('n')-$psize,1,date('Y')) );
		//N_efee 只计算 51借款管理费+92备付金  去除了 81投资奖励 52-投资管理费
		$sel = "sum(N_einterest) N_einterest,sum(N_efine) N_efine,sum(N_epenalty) N_epenalty,sum(if(I_type in (51,92),N_efee,0)) N_efee,sum(N_ecapital) N_ecapital,left(Createtime,7) as lefttime";
		$sqlw = "from p2p_record_cash where Status=1 and I_userID=$uid and Createtime>='{$beginday}' group by lefttime";
		$orders = 'order by lefttime';
		$pda = $Db->getTableByPage(1,$psize+1,$sel,$sqlw,$orders);//3种标分类
		if($pda['count']>0){
			foreach($pda['data'] as $o){
				if(($k=array_search(str_replace('-','/',$o['lefttime']), $items)) !== false){
					$da1[$k] = str2miriad($o['N_ecapital']);
					$da2[$k] = str2miriad($o['N_einterest']);
					$da3[$k] = str2miriad($o['N_efine']);
					$da4[$k] = str2miriad($o['N_epenalty']);
					$da5[$k] = str2miriad($o['N_efee']);
				}
			}
		}
	}
	$data=array();
	$data['datalist']=$items;
	$data['da1list']=$da1;
	$data['da2list']=$da2;
	$data['da3list']=$da3;
	$data['da4list']=$da4;
	$data['da5list']=$da5;
	$stat = json_encode($data);
	break;
case 'lo4':
	if($p['ty']=='year'){
	}else{
		$psize = $psizea[0]-1;
		for($i=$psize;$i>=0;$i--){
			$items[] = date('Y/m',mktime(0,0,0,date('n')-$i,1,date('Y')) );
			$da1[] = 0;
		}
		$beginday = date('Y-m-01',mktime(0,0,0,date('n')-$psize,1,date('Y')) );
		$sel = "sum(N_amount) amount,left(Createtime,7) as lefttime";
		$sqlw = "from p2p_application where Status=1 and I_applicantID=$uid and I_status>=50 and Createtime>='{$beginday}' group by lefttime";
		$orders = 'order by lefttime';
		$pda = $Db->getTableByPage(1,($psize+1),$sel,$sqlw,$orders);
		if($pda['count']>0){
			foreach($pda['data'] as $o){
				if(($k=array_search(str_replace('-','/',$o['lefttime']), $items)) !== false){
					$da1[$k] = str2miriad($o['amount']);
				}
			}
		}
	}
	$data=array();
	$data['datalist']=$items;
	$data['da1list']=$da1;
	$stat = json_encode($data);
	break;
}
$p['stat'] = $stat;
?>
