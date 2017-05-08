<?php
require_once('../include/TopFile.php');
require_once('../include/makeimage/makethumb.php');
require_once('../include/File.class.php');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$tname   = $FLib->RequestChar('tname',1,50,'参数',1);
$pt=$tname?$tname:'网站介绍';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work){
	case 'MdyReco': /**修改记录**/
		getvalue($Config->AdminPopedKeypattern,$FLib);
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		
		$idnotdel = $GLOBALS['Config']->admin_content_idnodel;
		if(!in_array($Id,$idnotdel)){
			$da['I_audit'] = 0;
		}
		
		$DataBase->autoExecute('es_article', $da, 'update', "id=$Id");

		$Admin ->AddLog('网站管理','修改'.$pt.'：其ID为：'.$Id );

		echo showSuc($pt.'修改成功',$FLib->IsRequest('backurl'),'self');
		break;
	case 'MdyReco2': /**修改记录**/
		getvalue($Config->AdminPopedKeypattern,$FLib);
		$Id = $FLib->RequestInt('Id',0,9,'ID');

		$idnotdel = $GLOBALS['Config']->admin_content_idnodel;
		if(!in_array($Id,$idnotdel)){
			$da['I_audit'] = 0;
		}

		$DataBase->autoExecute('es_article', $da, 'update', "id=$Id");

		$Admin ->AddLog('网站管理','修改'.$pt.'：其ID为：'.$Id );

		echo showSuc($pt.'修改成功','','self');
		break;
	case 'AddReco': /**增加记录**/
		getvalue($Config->AdminPopedKeypattern,$FLib);
		
		$da['I_operatorID'] = $Admin->Uid;
		$da['Createtime@'] = 'now()';
		$DataBase->autoExecute('es_article', $da);

		$Admin ->AddLog('网站管理','增加'.$pt.'：其名：'.$da['Vc_name']);
		echo showSuc($pt.'增加成功',$FLib->IsRequest('backurl'),'self');
		break;
	case 'DeleteReco': /**删除记录**/
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		$DataBase->QuerySql('update es_article set Status=0 where ID in('. $IdList.')');

		$Admin ->AddLog('网站管理','删除'.$pt.'：其ID为：'.$IdList);
		echo showSuc($pt.'删除完毕',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'AuditReco': /**审核记录**/
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		$DataBase->QuerySql('UPDATE es_article SET I_audit=1 WHERE ID IN('.$IdList.')');

		$Admin ->AddLog('网站管理','审核'.$pt.'：其ID为：'.$IdList);
		echo showSuc($pt.'审核完毕',$FLib->IsRequest('backurl'),$obj);
		break;
}
function getvalue($p,$FLib)
{
	$Files = new FileClass;
	global $da;
    $title          = $FLib->RequestChar('title',0,150,'标题',1);
    $order          = $FLib->RequestInt('order',0,9,'排序号');
	$classid        = $FLib->RequestInt('classID',0,9,'分类ID');
	$content        = $FLib->RequestChar('content',1,0,'文本内容',1,3); 
	$time           = $_POST['time']; 
    $fpath          = '';//$FLib->RequestChar('fpath',1,200,'图片路径',1);

    $Vc_source        = $FLib->RequestChar('Vc_source',1,50,'文章来源',1);
    $Vc_author      = $FLib->RequestChar('Vc_author',1,50,'作者',1);
    $Vc_tags        = $FLib->RequestChar('Vc_tags',1,100,'标签',1);
	
	$uploadfile = '';
	if($fpath!=''){
		$file_ext = array_pop(explode('.', $fpath));
		$new_file_name = date('His'). '.' . $file_ext;
		$uploaddir  = $GLOBALS['Config']->Articlepicture . date('Ymd').'/';
		$uploadfile = $uploaddir.$new_file_name;
		$save_path = $Files->CreateFolderNew(WEBROOT, $uploaddir);
		$file_path = $save_path.$new_file_name;
		copy($fpath, $file_path);
		unlink($fpath);
	}
	$da = array();
	$da['I_classID'] = $classid;
	$da['Vc_name'] = $title;
	$da['T_content'] = $content;
	$da['Dt_release'] = $time;
	$da['I_order'] = $order;
	$da['Vc_source'] = $Vc_source;
	//$da['Vc_author'] = $Vc_author;
	//$da['Vc_tags'] = $Vc_tags;
	if($uploadfile!=''){
		//$da['Vc_picture'] = $uploadfile;
	}

}

$DataBase->CloseDataBase();
?>