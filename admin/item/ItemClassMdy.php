<?php
if(1){
/****************************************************************** 
**创建者：sakura
**创建时间：2014-11-04
**本页：商品类型 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');
require(WEBROOTINCCLASS.'ItemClass.php');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$title = '商品分类';
$points = array('商品管理', $title.'管理');
$action = 'ItemClassProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SM_ITEM_CLASS_MDY');
switch($Work){
	case 'MdyReco':/**编辑**/
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		
// 		$Rs = $DataBase->GetResultOne('select * from sm_item_class where id='.$Id.' limit 0,1');
		$ItemClass = new ItemClass();
		$Rs = $ItemClass->getItemClassOne($Id);
		if(!$Rs){ echo showErr('记录未找到'); exit; }
		$title = '编辑'.$title;
		$hides['Id'] = $Id;
		break;
	case 'AddReco': /**增加**/
		$Rs = array();
		$title = '添加'.$title;
		break;
	case 'ImportReco': /**导入**/
		$Rs = array();
		$points = array('Excel', '数据导入');
		$title = '导入'.$title;
		$extend['multipart']='multipart';
		break;
	default:
		die('没有该操作类型!');	
}
if($Work=='ImportReco'){
    
    $params[] = array('val'=>'<a href="/data/resource/examples/classtemp.xls" class="but"><span style="margin:0">点击下载导入例子文件</span></a>','name'=>'模板文件','tip'=>'仅支持excel2007及以下版本');
    $params[] = array('val'=>'<input type="file"  name="file_stu">','name'=>'选择Excel文件','tip'=>'如果导入速度较慢，建议您把文件拆分为几个小文件，然后分别导入');

}else {
$params['name'] = array('name'=>'名称','val'=>iset($Rs['Vc_name']),'ty'=>'text','attrs'=>'isc="" maxlength="100"','tip'=>'');
$params['order'] = array('name'=>'序号','val'=>iset($Rs['I_order']),'ty'=>'text','attrs'=>'isc="nums" maxlength="10"','tip'=>'数字大的排在前');
}
$points[] = $title;

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'action', $action );
$tpl->assign( 'hides', $hides );
$tpl->assign( 'params', $params );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('mdy'.$raintpl_ver);

exit;
}
?>
