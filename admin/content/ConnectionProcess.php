<?php
require_once('../include/TopFile.php');
require_once('../include/makeimage/makethumb.php');
require_once('../include/File.class.php');
$Admin->CheckPopedoms('SC_SITE_LINK');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work){
case 'MdyReco': /**修改记录**/
	getvalue($FLib);
	$Id = $FLib->RequestInt('Id',0,9,'ID');
	$sqlp = '';
	switch ($type){
		case '1':
			if(!empty($uploadfile)){ $sqlp .= ", Vc_image='$uploadfile'";}
			break;
	}
	$DataBase->QuerySql("UPDATE es_link SET Vc_name='$title', I_order='$order', Vc_link='$link',I_type='$type' $sqlp WHERE id=$Id"); 
	$r = createeslinks();
	if($r['flag']<1){
		echo showErr('生成失败!'.$r['err']); exit;
	}
	$Admin ->AddLog('网站管理','修改链接：其Id为：'.$Id );
	echo showSuc('链接修改成功','ConnectionList.php',$obj);
	break;

case 'AddReco': /**添加记录**/   
	getvalue($FLib);
	$DataBase->QuerySql("INSERT INTO es_link (Vc_name,I_type,Vc_link,Vc_image,I_operatorID,Createtime,I_order) VALUES ('$title', '$type', '$link','$uploadfile',$Admin->Uid,now(),'{$order}')");
	
	$r = createeslinks();
	if($r['flag']<1){
		echo showErr('生成失败!'.$r['err']); exit;
	}
	$Admin ->AddLog('网站管理','增加链接：其名称为：'.$title);
	echo showSuc('链接添加成功','ConnectionList.php',$obj);
	break;

case 'DeleteReco': /**删除记录**/
	$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
	if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
	$DataBase->QuerySql('UPDATE es_link SET Status=0 WHERE ID IN('.$IdList.')');

	$r = createeslinks();
	if($r['flag']<1){
		echo showErr('生成失败!'.$r['err']); exit;
	}
	$Admin ->AddLog('网站管理','删除链接：其ID为：'.$IdList);
	echo showSuc('链接删除完毕',$FLib->IsRequest('backurl'),$obj);
	break;
}

function getvalue($FLib){
	$Files = new FileClass;
	global $title,$type,$link,$flash,$uploadfile,$Work,$order;
	$title                 = $FLib->RequestChar('title',0,50,'链接名称',1);
	$type                  = $FLib->RequestInt('type',1,9,'链接分类');
	$link                  = $FLib->RequestChar('link',1,200,'链接地址',1);
    $fpath                 = $FLib->RequestChar('fpath',1,200,'上传文件路径',1);
    $order                 = $FLib->RequestInt('order',1,9,'排序号');
	switch($type){
	case '1':
		if($fpath!=''){
			$file_ext = array_pop(explode('.', $fpath));
			$new_file_name = date('His'). '.' . $file_ext;
			$uploaddir  = $GLOBALS['Config']->Upadvert . date('Ymd').'/';
			$uploadfile = $uploaddir.$new_file_name;
			$save_path = $Files->CreateFolderNew(WEBROOT, $uploaddir);
			$file_path = $save_path.$new_file_name;
			copy($fpath, $file_path);
			unlink($fpath);
		}else{
			if($Work=='AddReco') { echo showErr('您选择了图片型链接,请上传链接图片！'); exit; }
		}
		break;
	}
	
}
//生成连接地址文件,用于显示
function createeslinks(){
	global $DataBase;
	$fname = WEBROOTDATA.'eslinks.html';
	$rn = "\r\n";
	$str = '';
	$da = $DataBase->fetch_all("select Vc_name,I_type,Vc_link,Vc_image from es_link where status>0 order by I_order desc,id desc");
	foreach($da as $k=>$v){
		if($v['I_type']==2){
			$str .= '<a href="'.$v['Vc_link'].'" target="_blank">'.$v['Vc_name'].'</a>'.$rn;//
		}elseif($v['I_type']==1){
			$str .= '<a href="'.$v['Vc_link'].'" target="_blank" title="'.$v['Vc_name'].'"><img src="'.$v['Vc_image'].'" /></a>'.$rn;//
		}
	}
	$r = writeincdata($str, $fname);
	if($r[0]<1){
		return array('flag'=>0,'err'=>$r[1]);
	}
	return array('flag'=>1);
}

$DataBase->CloseDataBase();   
 
?>       
    