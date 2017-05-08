<?php
if(1){
/****************************************************************** 
**创建者：sakura
**创建时间：2014-11-04
**本页： 商品类型 管理
**说明：
******************************************************************/

require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SM_ITEM_CLASS');
require(WEBROOTINCCLASS.'ItemClass.php');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$sKey   = $FLib->RequestChar('sKey',1,50,'参数',1);
$sType  = $FLib->RequestInt('sType',0,9,'类型');
$CurrPage = $FLib->RequestInt('currpage',1,9,'页数');
$UrlInfo = "&sKey=" . urlencode($sKey) ."&sType=" . $sType;
$outexcel = $FLib->RequestChar('outexcel',1,9,'outexcel');

$I_mall_classID=1;//钢材市场
$form='ItemClass';
$title = '分类';
$points = array('商品管理', $title.'列表' );

$extend['js']='

<script type="text/javascript">
$(function(){

	$(".buta_excel").live("click",function(){
		window.location.href="./'.$form.'List.php?outexcel=true'.$UrlInfo.'";
	});

})
</script>
';
// $tables = 'sm_item_class where Status=1 and I_mall_classID=1';
// $sql = "SELECT * FROM {$tables} order by I_order asc";
// $sqlcount = "SELECT count(*) FROM {$tables} ";
// if($inexcel){
    
//     include_once WEBROOTINC.'ImportExcel.php';
    
    
// }
$pagesize = $Config->AdminPageSize;
$pagecount = 1;$rscount=0;
if($outexcel){
    /*
     * 导出excel
     */
   
    $tables = 'sm_item_class uu where Status=1 and I_mall_classID='.$I_mall_classID;
    $sql = "SELECT * FROM {$tables} ";
    $sqlcount = "SELECT count(*) FROM {$tables} ";
    $sqlorder = " order by I_order asc";
    $sqlwmtime = $sql." and left(uu.Createtime,7)='explodeExcelTy2Mtime' ".$sqlorder;//按月导出使用$sqlwmtime，$sqlmtimecount
    $sqlmtimecount = "select left(uu.Createtime,7) as mtime from {$tables} group by left(uu.Createtime,7)";
    $sql .= $sqlorder;
    $dat=array();
    $dat['sql']=$sql;
    $dat['sqlcount']=$sqlcount;
    $dat['sqlwmtime']=$sqlwmtime;
    $dat['sqlmtimecount']=$sqlmtimecount;
    $dat['rs']=0;
    $dat['rscount']=iset($DataBase->fetch_val($sqlcount),0);
    $dat['filename']=$title;
    $dat['fields']=array();
    $dat['fields'][]=array('分类id','id');
    $dat['fields'][]=array('名称','Vc_name');

    $dat['fields'][]=array('排序号','I_order');

    include_once WEBROOTINC.'ExplodeExcel.php';


    exit;
     

    // include_once WEBROOTINC.'ExcelOut.php';
}



//$Rs  = $DataBase->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);
$itemclass=new ItemClass();

$Rs=$itemclass->getItemClassListPage($CurrPage, $pagesize, $I_mall_classID);
//var_dump($Rs);
// exit();
$ths = array();
$ths[]=array('val'=>'类别id', 'wid'=>'');
$ths[]=array('val'=>'名称', 'wid'=>'');
$ths[]=array('val'=>'排序号', 'wid'=>'');
$ths[]=array('val'=>'创建时间', 'wid'=>'');
$ths[]=array('val'=>'操作', 'wid'=>'');
$tds = array();
if(is_array($Rs)){
	$pagecount = $Rs[0]['pagecount'];
	$rscount = $Rs[0]['rscount'];$extend['rscount']=$rscount;
	for($i=1;$i<count($Rs) ;$i++){
	    
		$_td = '<td>'. $Rs[$i]['id'].'</td>';
		$_td .= '<td>'. $Rs[$i]['Vc_name'].'</td>';
		$_td .= '<td>'. $Rs[$i]['I_order'] .'</td>';
		$_td .= '<td title="'. $Rs[$i]['Createtime'] .'">'. $FLib->fromatdate($Rs[$i]['Createtime'],'Y-m-d H:i:s') .'</td>';
		$_td .= '<td><a href="'.$form.'Mdy.php?Work=MdyReco&Id='.$Rs[$i]['id'].'" class="hs" h="450" title="编辑'.$title.'">编辑</a></td>';
		$tds[$Rs[$i]['id']]=$_td;
    }
}
//var_dump($tds);
// exit();
$DataBase->CloseDataBase();
$extend['gbtns'][] = '<a href="'.$form.'Mdy.php?Work=AddReco" class="hs" h="450" title="添加'.$title.'"><span>添加'.$title.'</span></a>';
$btns = array('<a href="'.$form.'Process.php?Work=DeleteReco&tname='.urlencode($title).'&IdList=" class="del" rel="IdList" title="删除'.$title.'"><span>删除</span></a>');

//array_push($btns,'<a name="submit" class="butr_excel" style="display:inline-block;float:none;"/>导入excel</a>');
array_push($btns,'<a href="'.$form.'Mdy.php?Work=ImportReco" class="hs" h="450" title="导入'.$title.'"><span>导入'.$title.'</span></a>');
array_push($btns,'<a name="submit" class="buta_excel" style="display:inline-block;float:none;"/>导出excel</a>');


$helps  = array();
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
}
?>