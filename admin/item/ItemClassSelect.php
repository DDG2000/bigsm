<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 角色编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');
require(WEBROOTINCCLASS.'ItemClass.php');
//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}
$I_mall_classID=1;
$title='分类选择器';
$Type   = $FLib->RequestChar('Type',0,50,'类型',0);
$IdList   = $FLib->RequestChar('IdList',1,0,'IdList',0);
$returnInput   = $FLib->RequestChar('returnInput',0,50,'returnInput',0);

$points = array('商品管理', $title);
$params = array();
$helps  = array();

$itemclass=new ItemClass();

// $Rs = $DataBase->GettArrayResult('select * from sm_item_class where Status<>0  order by I_order asc ');
$Rs=$itemclass->getArrayById($I_mall_classID);
if(is_array($Rs)){
	$IdList = ','.$IdList.',';
	for($i=0;$i<count($Rs);$i++){
		if(strchr($IdList, ','.$Rs[$i]['id'].',')){
			$ischeck = 'true';
		}else{
			$ischeck = 'false';
		}
		$params[] = 'd.add('.$Rs[$i]['id'].',0,"'.htmlspecialchars($Rs[$i]['Vc_name']).'","", "", "", "", "", "",'.$Rs[$i]['id'].','.$ischeck.');';
	}
}
unset($Rs);

//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'params', $params );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'Type', $Type );
$tpl->assign( 'returnInput', $returnInput );

$tpl->draw('tree'.$raintpl_ver);
exit;
}
?>
