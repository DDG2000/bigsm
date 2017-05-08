<?php

/****************************************************************** 
**创建者：sakura
**创建时间：2014-11-04
**本页： 商品类型 管理
**说明：
******************************************************************/

require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SM_CONCENTRATED_LIST');
require(WEBROOTINCCLASS.'Concentrated.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
// $itemclassid = $FLib->RequestInt('itemclasslist',0,9,'所属分类');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType;



$form='Concentrated';
$title = '集采';
$points = array('集采管理', $title.'列表' );

//搜索框
$sTypes = array('', '关键字');
// $search_sTypes = array();
// $search_sTypes[] = '所属分类：<input type="text" name="itemclasslist" value="" class="txt_put2" data-url="ItemClassSelect.php?Type=1&IdList=" w="500" h="400" title="分类选择器">';
// $extend['sTypes'] = $search_sTypes;

// $where .= "AND (sp.Vc_name like '%$projectKeywords%' " ;
// $where .= "OR ub.Vc_truename like '%$projectKeywords%' " ;
// $where .= "OR sc.Vc_name like '%$projectKeywords%')  " ;
$mWhere = '1=1';
switch ($sType){
    case 1:
        $mWhere .= " and a.Vc_name like '%$sKey%'";
        $mWhere .= " or ub.Vc_truename like '%$sKey%'";
        $mWhere .= " or ub.Vc_mobile like '%$sKey%'";
        break;
       
}


$objConcentrated=new Concentrated();
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;

//$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);
$Rs=$objConcentrated->getConcentratedListPage($CurrPage, $pagesize, $mWhere);


$ths = array();
$ths[]=array('val'=>'集采名称', 'wid'=>'');
$ths[]=array('val'=>'负责人', 'wid'=>'');
$ths[]=array('val'=>'联系电话', 'wid'=>'');
$ths[]=array('val'=>'项目地址', 'wid'=>'');
$ths[]=array('val'=>'集采期限', 'wid'=>'');
$ths[]=array('val'=>'发布时间', 'wid'=>'');
$ths[]=array('val'=>'审核状态', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');
$tds = array();

$I_statusArr=array(
   '10'=>'待审核',
   '20'=>'招标中',
   '30'=>'已截至',
   '40'=>'已成交',
   '50'=>'审核不通过',
);
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
	    
		$_td = '<td>'. $Rs[$i]['Vc_name'].'</td>';
		$_td .= '<td>'.$Rs[$i]['Vc_truename'].'</td>';
		$_td .= '<td>'. $Rs[$i]['Vc_mobile'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['proname'].$Rs[$i]['cityname'].$Rs[$i]['disname'].$Rs[$i]['Vc_address'] .'</td>';
		$_td .= '<td>'. $Rs[$i]['D_start'].'至'.$Rs[$i]['D_end'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d H:i:s') .'</td>';
		$_td .= '<td>'. 
		  		$I_statusArr[$Rs[$i]['I_status']]
		.'</td>';
		$opts = '<a href="'.$form.'Mdy.php?Work=MdyReco&Id='.$Rs[$i]['id'].'&uid='.$Rs[$i]['I_buyerID'].'" class="hs" h="450" title="查看详情'.$title.'">查看详情</a> ';
		if ($Admin->CheckPopedom("SM_CONCENTRATED_RECORD")) {
		    $opts.=  ' | <a href="ConcentratedShopList.php?&Id='.$Rs[$i]['id'].'" class="hs" h="450" title="报名商家'.$title.'">报名商家</a>' ;
		}
		
		$_td .= '<td>'.$opts.'</td>';
// 		$_td .= '<td><a href="'.$form.'Mdy.php?Work=MdyReco&Id='.$Rs[$i]['id'].'" class="hs" h="450" title="查看详情'.$title.'">查看详情</a></td>';
		$tds[$Rs[$i]['id']]=$_td;
    }
}

$DataBase->CloseDataBase();
/* $extend['gbtns'][] = '<a href="'.$form.'Mdy.php?Work=AddReco" class="hs" h="450" title="添加'.$title.'"><span>添加'.$title.'</span></a>';
$btns = array('<a href="'.$form.'Process.php?Work=DeleteReco&tname='.urlencode($title).'&IdList=" class="del" rel="IdList" title="删除'.$title.'"><span>删除</span></a>'); */
/* $btns[] = '<a href="'.$form.'Process.php?Work=AuditReco&tname='.urlencode($title).'&IdList=" class="del" rel="IdList" title="审核'.$title.'"><span>审核</span></a>';
 */



$helps  = array('关键字:集采名称/负责人/联系电话');
$pagelistparam = '"plb", '.$pagecount.', '.$CurrPage.', "'.$UrlInfo.'", '.$Config->AdminPageSum.', '.$rscount.'';
$FLib->AdminSetcookie('backurl',$_SERVER['PHP_SELF'].'?currpage='.$CurrPage.$UrlInfo);

   

//initialize a Rain TPL object
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













?>