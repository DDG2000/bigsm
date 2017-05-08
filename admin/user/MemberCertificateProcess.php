<?php
require_once('../include/TopFile.php');
require_once('../include/File.class.php');
$Admin->CheckPopedoms('SC_MEMBER');
$Work   = $FLib->requestchar('Work',0,50,'参数',0);

$tt = '会员认证';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
{
case 'AddReco': /***添加**/
	$uid = $FLib->RequestInt('uid',0,9,'会员ID');
    	$certid = $FLib->RequestInt('certid',0,9,'认证项ID');
	$uc = $Db->fetch_one("select * from p2p_user_certificate where Status=1 and I_userID=$uid and I_certificateID=$certid");
	$imgda = array();
	if(!!$uc){
		$imgda = jsonstr_to_array($uc['Vc_image']);
	}
	
	$imgMn = iset($g_conf['cfg_auth_imgnum'],5);
	if(count($imgda)>=$imgMn){ echo showErr('该认证项图片到达上限【'.$imgMn.'】！'); exit; }

	$fnames = $FLib->RequestChar('fpath',1,1000,'上传文件',1);
	$isPass = $FLib->RequestChar('pass',1,1,'通过',1) ;
	if($fnames=='' && $GLOBALS['Work']=='AddReco') {
		// user_certificate_null.png
		echo $isPass;
		if ('1'==$isPass) {
			$fnames = '/admin/image/user_certificate_null.png' ;
		} else {
			echo showErr('请上传认证资料！'); 
			exit; 
		}
	}
	$farr = explode(',',$fnames);
	
	$Files = new FileClass;
	$yspath = $Files->CreateFolderNew(WEBROOT, $Cfg->File_Upload_TempPath);
	$updir  = $Cfg->Upcert . date('Ym').'/';
	$save_path = $Files->CreateFolderNew(WEBROOT, $updir);
	foreach($farr as $fname){
		if($fname=='') continue;
		if(!is_file($yspath.$fname)) continue;
		if(count($imgda)+1>$imgMn) break;
		
		$f_ext = array_pop(explode('.', $fname));
		$n_fname = date('dHis').mt_rand(1000,9999). '.' . $f_ext;
		$Files->makeimage($yspath.$fname, $save_path.$n_fname.'_s.jpg', 200, 200);
		rename($yspath.$fname, $save_path.$n_fname);
		$imgda[] = $updir.$n_fname;
	}
	$da=array();
	$da['Vc_image'] = addslashes(json_encode($imgda));
    	$da['I_userID'] = $uid;
    	$da['I_certificateID'] = $certid;	
	$da['Createtime@'] = 'now()';
	if(!$uc){
		$da['I_operatorID'] = $Admin->Uid;
	$Db->autoExecute('p2p_user_certificate', $da);
	}else{
		$Db->autoExecute('p2p_user_certificate', $da, 'update', "I_userID=$uid and I_certificateID=$certid");
	}
	$Admin ->AddLog('会员管理','增加'.$tt.'：会员ID:'.$uid.'认证项ID:'.$certid.'图片' );
	echo showSuc($tt.'添加成功',$FLib->IsRequest('backurl3'),$obj);
	break;
case 'MdyReco':  /***修改**/
	break;
case 'DelReco':  /***删除**/
	$IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
	if(!$FLib->isidlist($IdList)) { echo showErr('参数错误'); exit; }
	
	$Db->autoExecute('p2p_user_certificate', array('Status'=>0), 'update', "ID in ($IdList)");
	$Admin ->AddLog('会员管理','删除'.$tt.'：其ID为：'.$IdList );
	echo showSuc($tt.'删除成功',$FLib->IsRequest('backurl3'),$obj);
	break;
case 'Del2Reco':  /***删除认证项中图片**/
	$IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
	if(!$FLib->isidlist($IdList)) { echo showErr('参数错误'); exit; }
    $uid = $FLib->RequestInt('uid',0,9,'会员ID');
    $certid = $FLib->RequestInt('certid',0,9,'认证项ID');
	$uc = $Db->fetch_one("select * from p2p_user_certificate where Status=1 and I_userID='$uid' and I_certificateID='$certid'");
	if(!!$uc){
		$o = jsonstr_to_array($uc['Vc_image']);
		$Idarr = explode(',', $IdList);
		foreach($Idarr as $v){
			if(isset($o[$v])) unset($o[$v]);
}
		$o = array_values($o);
		$da=array();
		$da['Vc_image'] = addslashes(json_encode($o));
		$da['Createtime@'] = 'now()';
		$Db->autoExecute('p2p_user_certificate', $da, 'update', "ID=".$uc['ID']);
	}
	$Admin ->AddLog('会员管理','删除'.$tt.'：会员ID:'.$uid.'认证项ID:'.$certid.'中的图片'.$IdList );
	echo showSuc($tt.'资料删除成功',$FLib->IsRequest('backurl1'),$obj);
	break;
}


$DataBase->CloseDataBase();  
?>