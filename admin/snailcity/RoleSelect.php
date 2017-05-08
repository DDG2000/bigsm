<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 角色编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$title='角色选择器';
$Type   = $FLib->RequestChar('Type',0,50,'类型',0);
$IdList   = $FLib->RequestChar('IdList',1,0,'IdList',0);
$returnInput   = $FLib->RequestChar('returnInput',0,50,'returnInput',0);

$points = array('系统管理', '系统安全', $title);
$params = array();
$helps  = array();

$mWhere = '';
//系统管理员(起始用户system) 角色ID=2 
if(!(strpos(','.$Admin->Rule.',', ',2,')!==false || $Admin->Uname=='system')){
  $mWhere .= ' and ID<>2';
}
$Rs = $DataBase->GettArrayResult('select * from sc_role where Status<>0 '.$mWhere.' order by id ');
if(is_array($Rs)){
	$IdList = ','.$IdList.',';
	for($i=0;$i<count($Rs);$i++){
		if(strchr($IdList, ','.$Rs[$i]['ID'].',')){
			$ischeck = 'true';
		}else{
			$ischeck = 'false';
		}
		$params[] = 'd.add('.$Rs[$i]['ID'].',0,"'.htmlspecialchars($Rs[$i]['Vc_name']).'","", "", "", "", "", "",'.$Rs[$i]['ID'].','.$ischeck.');';
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
