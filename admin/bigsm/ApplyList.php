<?php
require_once('../include/TopFile.php');
require_once(WEBROOTINCCLASS.'AuditManager.php');
require_once(WEBROOTINC.'ApplyCommon.php');
//审核状态status： 
// 1、审核中；
// 2、退回；
// 3、完成；
// 4、终止
$Admin->CheckPopedoms('SC_AUDIT');
$isMdy = $Admin->CheckPopedom('SC_AUDIT_AUDIT');
$sKey = $FLib->RequestChar ( 'sKey', 1, 50, '查询关键字', 1 );
$sType = $FLib->RequestInt ( 'sType', 0, 9, '查询类型' );
$CurrPage = $FLib->RequestInt ( 'currpage', 1, 9, '当前页数' );
$UrlInfo = "&sKey=" . urlencode ( $sKey ) . "&sType=" . $sType;
$I_entity = $FLib->RequestInt ( 'I_entity', 1, 9, '申请分类' );
$status = $FLib->RequestInt ( 'status', 0, 9, '申请状态' );
$UrlInfo .= '&I_entity='.$I_entity;
$auditManager = new AuditManager($Admin,$entityarr,$I_entity,$Admin->AuditRule) ;
if(!array_key_exists($I_entity, $entityarr)){ echo showErr('分类参数有误未找到'); exit; }
$table = $entityarr[$I_entity][1];
$column = 'I_flowID';
$column_v = $FLib->requestint($column,0,9,'分栏');//---需传值
$last_flow=0;$first_flow=0;
$flows = $auditManager->getEntityFlows() ;
$types = $auditManager->getTypeArr();
//流程中 status==0 必须获取当前审核流程的第几步 ----
if(is_array($types) && !array_key_exists($column_v, $types) && $status==0){$column_v = array_shift(array_keys($types));}
if($column_v>0){$status=0;}//只存在一个状态
//申请流程中
$marks = '';
foreach($types as $k=>$v){
	$marks.='<a href="?'.$column.'='.$k.$UrlInfo.'" '.($k==$column_v?'class="cur"':'').'>'.$v.'</a>';
}

$marks.='<a href="?status=3'.$UrlInfo.'" '.(3==$status?'class="cur"':'').'>已完成</a>';
$marks.='<a href="?status=4'.$UrlInfo.'" '.(4==$status?'class="cur"':'').'>已终止</a>';
$UrlInfo .= '&status='.$status.'&'.$column.'=' . $column_v;

$title = $entityarr[$I_entity][0].'审核';
$points = array('审核管理', $title.'管理' );
$sTypes = array('', '任务标题');
$hides  = array('I_entity'=>$I_entity, 'status'=>$status, $column=>$column_v);
$extend = array('marks'=>$marks);
$result = $auditManager->getApplyPage($status,$column_v, $CurrPage);
$ths = array();
$ths[]=array('val'=>'任务标题', 'wid'=>'');
$ths[]=array('val'=>'任务说明', 'wid'=>'');
$ths[]=array('val'=>'任务状态', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
if($isMdy){
	$ths[]=array('val'=>'操作', 'wid'=>'');
}
$tds = array() ;
if (is_array($result)) {
	$pagecount = $result[0]['pagecount'];
	$rscount = $result[0]['rscount'];
	$extend['rscount']=$rscount;
	for($i=1;$i<count($result) ;$i++){
		$r = $result[$i] ;
		$_td  = '<td title="'.$r['Vc_name'].'"><a href="ApplyInfo.php?Id='.$r['id'].'&I_entity='.$I_entity.'" class="hs" h="800" title="'.$title.'详情">'.$FLib->CutStr($r['Vc_name'],80).'</a></td>';
		$content = strip_tags(htmlspecialchars_decode($r['Vc_content'])) ;
		$_td .= '<td>'.$FLib->CutStr($content,180).'</td>';
		$_td .= '<td>'.getApplyStatus($r['I_status']).'</td>';
		$_td .= '<td title="'. $r['Createtime'] .'">'. $FLib->fromatdate($r['Createtime'],'Y-m-d') .'</td>';
		if(!in_array($status,$auditManager->getEndStatus()) && $isMdy){
			$abtns='';
			$abtns.='<a href="ApplyMdy.php?Work=MdyReco&id='.$r['id'].'&I_entity='.$I_entity.'" class="hs" h="600" title="处理'.$title.'">查看处理</a>';
			$_td .= '<td>'.$abtns.'</td>';
		}else{
			$_td .= '<td><a href="ApplyInfo.php?id='.$r['id'].'&I_entity='.$I_entity.'" class="hs" title="'.$title.'详情">查看</a></td>';
		}
		$tds[$r['id']]=$_td;
	}
}

$helps  = array();
$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);

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
