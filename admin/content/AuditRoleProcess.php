<?php
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_AUDIT_ROLE_MDY');
$Work   = $FLib->requestchar('Work',0,50,'参数',0);

$tt = '审核角色';$table='p2p_role';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
{
	case 'AddReco': /***添加记录**/
		getParam();
		$sql = "select count(*) from {$table} where Status=1 and Vc_name='{$data['Vc_name']}'";
		if($Db->fetch_val($sql) > 0) { echo showErr('角色名称已存在'); exit; }
		
		$data['I_operatorID'] = $Admin->Uid;
		$data['Createtime@'] = 'now()';
		$Db->autoExecute($table,$data);
		 $DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		 
		$Admin ->AddLog('网站管理','增加审核角色：其名称为：'.$data['Vc_name'] );
		echo $FLib ->Alert('执行完毕','self','hidden');
		echo showSuc($tt.'添加成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'MdyReco':  /***修改记录**/
		$Id   = $FLib->RequestInt('Id',0,9,'ID');
		getParam();
		$sql = "select count(*) from {$table} where Status=1 and Vc_name='{$data['Vc_name']}' and ID<>{$Id}";
		if($Db->fetch_val($sql) > 0) { echo showErr('角色名称已存在'); exit; }
		
		$Db->autoExecute($table,$data, 'update', "ID='{$Id}'");
		 $DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		
		$Admin ->AddLog('网站管理','修改审核角色：其ID为：'.$Id );
		echo showSuc($tt.'修改成功',$FLib->IsRequest('backurl'),$obj);
		break;
    case 'DelReco':  /***删除记录**/
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
		if(!$FLib->isidlist($IdList)) { echo showErr('参数错误'); exit; }
		$data['Status'] = 0;
		$Db->autoExecute($table,$data, 'update', "ID in ($IdList)");
		 $DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		 
		$Admin ->AddLog('网站管理','删除审核角色：其ID为：'.$IdList );
		echo showSuc($tt.'删除成功',$FLib->IsRequest('backurl'),$obj);
		break;
}
function getParam(){
	global $FLib,$data;
	$Vc_name = $FLib->RequestChar('Vc_name',0,50,'角色名称',1);
	$Vc_intro = $FLib->RequestChar('Vc_intro',1,500,'备注',1);
	
	$data['Vc_name'] = $Vc_name;
	$data['Vc_intro'] = $Vc_intro;
	
}
$DataBase->CloseDataBase();  
?>