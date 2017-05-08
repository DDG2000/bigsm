<?php
/****************************************************************** 
**创建者：kign
**创建时间：2014-02-07  
**本页：
**    保留名处理页
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_PTNAME');

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
{
	case 'AddReco': /***添加保留名**/
		$lname   = $FLib->RequestChar('Vc_protectname',0,500,'保留名',1);
		$na=preg_split('/[\s,]+/', $lname, 0, PREG_SPLIT_NO_EMPTY);
		$err = 0;
		for($i=0; $i<count($na); $i++){
			$nai=$na[$i];
			$r = $DataBase->fetch_val("select count(*) from site_protectname where Status=1 And Vc_protectname='$nai'");
			if($r > 0){
				$err++;
			}else{
				$DataBase->querySql("insert into site_protectname (Vc_protectname,I_operatorID,Createtime) values('$nai',$Admin->Uid,now())");
			}
		}
		if($err==count($na)){
			echo showErr('这'.($err==1?'个':'些').'坏词已经添加过了');
			exit;
		}
		
		$r = createProtectNameCache();
		$msg = '';
		if($r['flag']<1){
			$msg .= ',生成失败!'.$r['err'];
		}
		
		$Admin ->AddLog('站点管理','增加保留名：其名称为：'.$lname);
		echo showSuc('添加保留名成功'.$msg,$FLib->IsRequest('backurl'),$obj);
		break;
	case 'MdyReco':  /***修改保留名**/
		$lname   = $FLib->RequestChar('Vc_protectname',0,500,'保留名',1);
		$Id   = $FLib->RequestInt('Id',0,9,'ID');
		$r = $DataBase->fetch_val("select count(*) from site_protectname where Status=1 And Vc_protectname='$lname' And ID<>$Id");
		if($r > 0){ echo showErr('保留名重复'); exit; }
		$DataBase->QuerySql("update site_protectname set Vc_protectname='$lname' where ID=$Id");
		
		$r = createProtectNameCache();
		$msg = '';
		if($r['flag']<1){
			$msg .= ',生成失败!'.$r['err'];
		}
		
		$Admin ->AddLog('站点管理','修改保留名：其ID为：'.$Id );
		echo showSuc('修改保留名成功'.$msg,$FLib->IsRequest('backurl'),$obj);
		break;
	case 'DeleteReco':  /***删除保留名**/
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		$DataBase->QuerySql("delete from site_protectname where ID in ($IdList)");
		
		$r = createProtectNameCache();
		$msg = '';
		if($r['flag']<1){
			$msg .= ',生成失败!'.$r['err'];
		}
		
		$Admin ->AddLog('站点管理','删除保留名：其ID为：'.$IdList );
		echo showSuc('删除保留名成功'.$msg,$FLib->IsRequest('backurl'),$obj);
		break;
}
$DataBase->CloseDataBase();


function createProtectNameCache(){
	global $DataBase;
	$fname = WEBROOTDATA.'protectname.cache.inc.php';
	$rn = "\r\n";
	$str = '<?php'.$rn;
	$str .= '$da_protectname=array(';//.$rn
	$da = $DataBase->fetch_all("select Vc_protectname from site_protectname where status>0");
	foreach($da as $k=>$v){
		$str .= ($k>0?',':'').'\''.str_replace("'",'',$v['Vc_protectname']).'\'';//.$rn
	}
	$str .= ');'.$rn;
	$str .= '?>';
	$r = writeincdata($str, $fname);
	if($r[0]<1){
		return array('flag'=>0,'err'=>$r[1]);
	}
	return array('flag'=>1);
}

?>