<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 用户密码 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

//use cache
if($raintpl_cache && $cache = $tpl->cache('list', 60, 1) ){echo $cache;	exit;}

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$title = '通知';
$points = array('会员管理', '会员通知管理');
$action = 'MemberProcess.php';
$hides  = array('Work'=>$Work);
$params = array();
$helps  = array();
$extend = array();

$Admin->CheckPopedoms('SC_MEMBER_BASE');

    $IdList = $FLib->RequestChar('IdList',0,0,'IdList',0);
    if($IdList){
        $Rs = $DataBase->fetch_all_assoc("select * from user_base where ID in ($IdList)");
        if(!$Rs){ echo showErr('记录未找到'); exit; }
        $hides['IdList'] = $IdList;
    }else{
        $hides['IdList'] = '0';
    }
    if($IdList){
        $params[] = array('val'=>iset($IdList),'name'=>'用户ID','tip'=>'');
    }else{
        $params[] = array('val'=>'全部用户','name'=>'用户ID','tip'=>'');
    }
    $params['title'] = array('val'=>'站内信','name'=>'标题','attrs'=>'','tip'=>'');
    $params['url'] = array('val'=>'<input type="text" name="url" value="" class="txt_put2" isc="" maxlength="" autocomplete="off">','name'=>'链接','attrs'=>'isc','tip'=>'链接地址');
    $params['note'] = array('val'=>'','name'=>'通知','tip'=>'','ty'=>'textarea','attrs'=>'isc');
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
