<?php
require_once('../include/TopFile.php');
require_once('../include/File.class.php');
$Admin->CheckPopedoms('SC_MEMBER');
$Work   = $FLib->requestchar('Work',0,50,'参数',0);

$cId = $FLib->RequestInt('cId',0,9,'ID');
$Rs = $Db->fetch_one("select * from p2p_user_certificate where ID={$cId}");
if(!$Rs) {echo showErr('参数错误'); exit; }
$tt = '会员:'.$Rs['I_userID'].'[认证项:'.$Rs['I_certificateID'].']的认证资料';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
{
case 'AddReco': /***添加**/
    $fpath = $FLib->RequestChar('fpath',1,2000,'上传文件路径',1);
	if($fpath==''){ echo showErr('请上传认证资料！'); exit; }
	$Files = new FileClass;
	$yspath = $Files->CreateFolderNew(WEBROOT, $GLOBALS['Config']->File_Upload_TempPath);
	$updir  = $GLOBALS['Config']->Articlepicture . date('Ym').'/';
	$save_path = $Files->CreateFolderNew(WEBROOT, $updir);
	$farr = explode(',',$fpath);
	foreach($farr as $fname){
		if($fname=='') continue;
		if(!is_file($yspath.$fname)) continue;
		$f_ext = array_pop(explode('.', $fname));
		$n_fname = date('dHis').mt_rand(1000,9999). '.' . $f_ext;
		$Files->makeimage($yspath.$fname, $save_path.$n_fname.'_s.jpg', 200, 200);
		rename($yspath.$fname, $save_path.$n_fname);
		$imgda[]  = $updir.$n_fname;
	}
	if(empty($imgda)){ echo showErr('上传认证资料异常！'); exit; }
	
	$o = jsonstr_to_array($Rs['Vc_image']);
	foreach($imgda as $v){ $o[] = $v;}
	$nda['Vc_image'] = addslashes(json_encode($o));
	$Db->autoExecute('p2p_user_certificate', $nda, 'update', "ID=$cId");
	$Admin ->AddLog('会员管理','增加'.$tt.'：'.join(',',$imgda) );
	echo showSuc($tt.'添加成功',$FLib->IsRequest('backurl'),$obj);
	break;
case 'MdyReco': /***编辑**/
	/*$Id = $FLib->RequestInt('Id',0,9,'ID');
    $fpath = $FLib->RequestChar('fpath',1,2000,'上传文件路径',1);
	$Files = new FileClass;
	$yspath = $Files->CreateFolderNew(WEBROOT, $GLOBALS['Config']->File_Upload_TempPath);
	$updir  = $GLOBALS['Config']->Articlepicture . date('Ym').'/';
	$save_path = $Files->CreateFolderNew(WEBROOT, $updir);
	$farr = explode(',',$fpath);
	foreach($farr as $fname){
		if($fname=='') continue;
		if(!is_file($yspath.$fname)) continue;
		$f_ext = array_pop(explode('.', $fname));
		$n_fname = date('dHis').mt_rand(1000,9999). '.' . $f_ext;
		$Files->makeimage($yspath.$fname, $save_path.$n_fname.'_s.jpg', 200, 200);
		rename($yspath.$fname, $save_path.$n_fname);
		$imgda[]  = $updir.$n_fname;
	}
	if(!empty($imgda)){
		$o = jsonstr_to_array($Rs['Vc_image']);
		if(!isset($o[$Id])){ echo showErr('修改认证资料异常！'); exit; }
		$o[$Id] = $imgda[0];
		$nda['Vc_image'] = addslashes(json_encode($o));
		$Db->autoExecute('p2p_user_certificate', $nda, 'update', "ID=$cId");
		$Admin ->AddLog('会员管理','编辑'.$tt.'：其序号:'.$Id );
	}
	echo showSuc($tt.'编辑成功',$FLib->IsRequest('backurl'),$obj);*/
	break;
case 'DelReco':  /***删除单个图片**/
	$IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
	if(!$FLib->isidlist($IdList)) { echo showErr('参数错误'); exit; }
	$cId = $FLib->RequestInt('cId',0,9,'ID');
	$Rs = $Db->fetch_one("select Vc_image from p2p_user_certificate where ID={$cId}");
	if(!$Rs) {echo showErr('参数错误'); exit; }
	$o = jsonstr_to_array($Rs['Vc_image']);
	$Idarr = explode(',', $IdList);
	foreach($Idarr as $v){
		if(isset($o[$v])) unset($o[$v]);
	}
	$nda['Vc_image'] = addslashes(json_encode($o));
	$Db->autoExecute('p2p_user_certificate', $nda, 'update', "ID=$cId");
	$Admin ->AddLog('会员管理','删除'.$tt.'：其序号:'.$IdList );
	echo showSuc($tt.'删除成功',$FLib->IsRequest('backurl'),$obj);
	break;
}


$DataBase->CloseDataBase();  
?>