<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2014-5-24
**本页： 审核流程 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_AUDIT_ROLE');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$title='角色选择器';
$Type = $FLib->RequestInt('Type',1,50,'类型');
$IdList = $FLib->RequestChar('IdList',1,0,'IdList',0);
$returnInput = $FLib->RequestChar('returnInput',0,50,'returnInput',0);

$points = array('网站管理', '审核管理', $title);
$params = array();
$helps  = array();

$Rs = $DataBase->GettArrayResult('select * from p2p_role where Status=1 order by id ');
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
