<?php
/****************************************************************** 
**创建者：张杰
**创建时间：2008-08-27  
**本页：
**   管理系统 V0.1坏词处理页
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_BDWORD');

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
{
	case 'AddReco': /***添加坏词**/
		$lname   = $FLib->RequestChar('Vc_badword',0,500,'坏词名称',1);
		$na=preg_split('/[\s,]+/', $lname, 0, PREG_SPLIT_NO_EMPTY);
		$err = 0;
		for($i=0; $i<count($na); $i++){
			$nai=$na[$i];
			$r = $DataBase->fetch_val("select count(*) from site_badword where Status=1 And Vc_badword='$nai'");
			if($r > 0){
				$err++;
			}else{
				$DataBase->querySql("insert into site_badword (Vc_badword,I_operatorID,Createtime) values('$nai',$Admin->Uid,now())");
			}
		}
		if($err==count($na)){
			echo showErr('这'.($err==1?'个':'些').'坏词已经添加过了');
			exit;
		}

		$Admin ->AddLog('站点管理','增加坏词：其名称为：'.$lname);
		$r = createbadcatch();
		$msg = '';
		if($r['flag']<1){
			$msg .= ',生成失败!'.$r['err'];
		}
		echo showSuc('坏词添加成功'.$msg,$FLib->IsRequest('backurl'),$obj);
		break;
	case 'MdyReco':  /***修改坏词**/
		$lname   = $FLib->RequestChar('Vc_badword',0,500,'坏词名称',1);
		$Id   = $FLib->RequestInt('Id',0,9,'ID');
		$r = $DataBase->fetch_val("select count(*) from site_badword where Status=1 And Vc_badword='$lname' And ID<>$Id");
		if($r > 0){ echo showErr('坏词名称重复'); exit; }
		$DataBase->QuerySql("update site_badword set Vc_badword='$lname' where ID=$Id");

		$r = createbadcatch();
		$msg = '';
		if($r['flag']<1){
			$msg .= ',生成失败!'.$r['err'];
		}

		$Admin ->AddLog('站点管理','修改坏词：其ID为：'.$Id );
		echo showSuc('坏词修改成功'.$msg,$FLib->IsRequest('backurl'),$obj);
		break;
	case 'DeleteReco':  /***删除坏词**/
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		//$DataBase->QuerySql("update site_badword set Status=0 where ID in ($IdList)");
		$DataBase->QuerySql("delete from site_badword where ID in ($IdList)");

		$r = createbadcatch();
		$msg = '';
		if($r['flag']<1){
			$msg .= ',生成失败!'.$r['err'];
		}

		$Admin ->AddLog('站点管理','删除坏词：其ID为：'.$IdList );
		echo showSuc('坏词删除完毕'.$msg,$FLib->IsRequest('backurl'),$obj);
		break;
	case 'CreateReco':  /***更新缓存坏词**/
		$r = createbadcatch();
		if($r['flag']<1){
			echo showErr('生成失败!'.$r['err']); exit;
		}
		echo showSuc('坏词更新缓存成功',$FLib->IsRequest('backurl'),$obj);
		break;
}
$DataBase->CloseDataBase();

function createbadcatch(){
	global $DataBase;
	$fname = WEBROOTDATA.'badword.cache.inc.php';
	$rn = "\r\n";
	$str = '<?php'.$rn;
	$str .= '$da_badword=array(';//.$rn
	$da = $DataBase->fetch_all("select Vc_badword from site_badword where status>0");
	foreach($da as $k=>$v){
		$str .= ($k>0?',':'').'\''.str_replace("'",'',$v['Vc_badword']).'\'';//.$rn
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