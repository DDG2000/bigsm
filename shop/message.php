<?php
if(!defined('WEBROOT'))exit;
$w=$FL->requeststr('w',1,0,'w',1,1);
$m.='_'.$w;

$Message = new Message();
switch($w){
//消息展示
case 'list':
	/**
	 * @author zy
	 * 消息展示
	 * url地址：
	 * http://www.bigsm.com/index.php?act=shop&m=message&w=list
	 * 输入：需登录后访问
	 * endtime:str 结束时间
	 * page:int 当前页
	 * 输出:err:int 结果状态 -1失败 0成功
	 * message:array 分页消息
	 * pagestr:str 分页页码
	 * */
	$page = $FL->requestint('page',1,'页数',1);
	$page = $page<1?1:$page;
	$psize = 10;
	$da = $Message->getListPage($page,$psize,"and I_userID=$uid");
	dump($da);
	$page = $da['page'];
	$count = $da['count'];
	$pcount = $da['pcount'];
	$p['message'] = $da;
	$p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=$act&m=message&w=list");
	break;
	
case 'read':
	/**
	 * @author zy
	 * 消息已读标记
	 * url地址：
	 * http://www.bigsm.com/index.php?act=shop&m=message&w=list
	 * 输入：需登录后访问
	 * endtime:str 结束时间
	 * page:int 当前页
	 * 输出:err:int 结果状态 -1失败 0成功
	 * message:array 分页消息
	 * pagestr:str 分页页码
	 * */
	$IdList=$FL->requestidlist('IdList',0,1000,'',1,1);
	if(strpos($IdList,',')===false){
		$sqlw = "and  ID = $IdList";
	}else{
		$sqlw = "and  ID in $IdList";
	}
		$r = $Message->saveMess(array('I_read'=>1), "I_userID=$uid $sqlw");
	$r['mess_unreadnum'] = $Message->getCount($uid,1);
	returnjson($r);
	break;
	
/**消息删除  **/
case 'del':
	/**
	 * @author zy
	 * 消息删除
	 * url地址：
	 * http://www.bigsm.com/index.php?act=shop&m=message&w=del
	 * 输入：需登录后访问
	 * endtime:str 结束时间
	 * page:int 当前页
	 * 输出:err:int 结果状态 -1失败 0成功
	 * message:array 分页消息
	 * pagestr:str 分页页码
	 * */
	$IdList=$FL->requestidlist('IdList',0,1000,'',1,1);
	if(strpos($IdList,',')===false){
		$sqlw = "and  ID = $IdList";
	}else{
		$sqlw = "and  ID in $IdList";
	}
	$IdList=$FL->requestidlist('IdList',0,1000,'',1,1);
	$r = $Message->saveMess(array('Status'=>0), "I_userID={$uid} $sqlw");
	$r['mess_num'] = $Message->getCount($uid,0);
	$r['mess_unreadnum'] = $Message->getCount($uid,1);
	returnjson($r);
	break;

/**消息清空  **/
case 'clear':
	/**
	 * @author zy
	 * 消息删除
	 * url地址：
	 * http://www.bigsm.com/index.php?act=shop&m=message&w=clear
	 * 输入：需登录后访问
	 * endtime:str 结束时间
	 * page:int 当前页
	 * 输出:err:int 结果状态 -1失败 0成功
	 * message:array 分页消息
	 * pagestr:str 分页页码
	 * */
	$r = $Message->saveMess(array('I_read'=>1), "I_userID={$uid}");
	$r['mess_num'] = 0;
	$r['mess_unreadnum'] = 0;
	returnjson($r);
	break;

}

?>