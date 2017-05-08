<?php
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_AUDIT_FLOW_MDY');
$Work   = $FLib->requestchar('Work',0,50,'参数',0);

$tt = '审核流程';$table='sm_apply_flow';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
{
	case 'AddReco': /***添加记录**/
		getParam();
		$sql = "select count(*) from {$table} where Status=1 and I_level='{$data['I_level']}' and I_entity={$data['I_entity']}";
		if($Db->fetch_val($sql) > 0) { echo showErr('审核级别已存在'); exit; }
		
		$data['Createtime@'] = 'now()';
		$Db->autoExecute($table,$data);
		 $DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		
		$Admin ->AddLog('网站管理','增加'.$tt.'：其名称为：'.$data['Vc_name'] );
		echo $FLib ->Alert('执行完毕','self','hidden');
		echo showSuc($tt.'添加成功',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'MdyReco':  /***修改记录**/
		$Id   = $FLib->RequestInt('Id',0,9,'ID');
		getParam();
		$sql = "select count(*) from {$table} where Status=1 and I_level='{$data['I_level']}' and I_entity={$data['I_entity']} and ID<>{$Id}";
		if($Db->fetch_val($sql) > 0) { echo showErr('审核级别已存在'); exit; }
		
		$Db->autoExecute($table,$data, 'update', "ID='{$Id}'");
		 $DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		
		$Admin ->AddLog('网站管理','修改'.$tt.'：其ID为：'.$Id );
		echo showSuc($tt.'修改成功',$FLib->IsRequest('backurl'),$obj);
		break;
    case 'DelReco':  /***删除记录**/
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
		if(!$FLib->isidlist($IdList)) { echo showErr('参数错误'); exit; }
		$data['Status'] = 0;
		$Db->autoExecute($table,$data, 'update', "ID in ($IdList)");
		 $DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		$Admin ->AddLog('网站管理','删除'.$tt.'：其ID为：'.$IdList );
		echo showSuc($tt.'删除成功',$FLib->IsRequest('backurl'),$obj);
		break;
    case 'LockReco':
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		$Flag = $FLib->RequestInt('Flag',1,9,'Flag');
		if(!$FLib->isidlist($IdList)) { echo showErr('参数错误'); exit; }
		
		$data['I_enable'] = $Flag;
		$Db->autoExecute($table,$data, 'update', "ID in ($IdList)");
		if($Flag == 1){ 
			$msg = '启用'.$tt.'：其ID为：';
			$Admin->AddLog('网站管理',$msg.$IdList);
			echo showSuc($tt.'启用完毕',$FLib->IsRequest('backurl'),$obj);
		}else{
			$msg = '禁用'.$tt.'：其ID为：';
			$Admin->AddLog('网站管理',$msg.$IdList);
			echo showSuc($tt.'禁用完毕',$FLib->IsRequest('backurl'),$obj);
		}
		 $DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		break;
}
function getParam(){
	global $FLib,$data;
	$Vc_name = $FLib->RequestChar('Vc_name',0,50,'流程名称',1);
	$Vc_role = $FLib->RequestChar('Vc_role',1,2000,'流程名称',1);
	$Vc_intro = $FLib->RequestChar('Vc_intro',1,500,'备注',1);
	$I_entity = $FLib->RequestInt('I_entity',1,9,'分类');
	$I_level = $FLib->RequestInt('I_level',1,9,'审核级别');
	
	$data['Vc_name'] = $Vc_name;
	$data['Vc_role'] = $Vc_role;
	$data['Vc_intro'] = $Vc_intro;
	$data['I_entity'] = $I_entity;
	$data['I_level'] = $I_level;
	
}
$DataBase->CloseDataBase();  
?>