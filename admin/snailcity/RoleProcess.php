<?php
/**
* 模块：基础模块
* 描述：角色处理页
* 作者：张绍海
*/ 

//引入根目录下include里面的TopFile.php文件
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SYS_SET_ROLE_MDY');
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
$Work   = $FLib->RequestChar('Work',0,50,'参数',0);

switch ($Work)
{
	case 'AddReco': /***添加角色**/
		$title   = $FLib->RequestChar('title',0,30,'角色名称',1);
		$intro   = $FLib->RequestChar('intro',1,50,'备注',1);
		$rulelist = $FLib->RequestChar('rulelist',0,0,'权限分配 ',1);
		$Re = $DataBase->SelectSql("select * from sc_role where Status<>0 And Vc_name='$title' limit 0,1");
		if($DataBase->GetResultRows($Re) > 0){
		   echo showErr('角色名称重复');
		   exit;
		}
		$DataBase->QuerySql("insert into sc_role(Vc_name,Vc_intro,I_operatorID,Createtime) values('$title','$intro',$Admin->Uid,now())");
		$roleID = $DataBase->GetlastID();
		$DataBase->QuerySql("insert into sc_rule_role(I_roleID,T_rule,I_operatorID,Createtime) values('$roleID','$rulelist',$Admin->Uid,now())");
		 $DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		$Admin ->AddLog('系统管理','增加角色：其名称为：'.$title );
		echo showSuc('角色添加完毕',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'MdyReco':  /***修改角色**/	   	
		$Admin->CheckPopedoms('SC_SYS_SET_ROLE_MDY'); 
		$rulelist = $FLib->RequestChar('rulelist',0,0,'权限分配 ',1);
		$title   = $FLib->RequestChar('title',0,30,'角色名称',1);
		$intro   = $FLib->RequestChar('intro',1,50,'备注',1);
		$Id   = $FLib->RequestInt('Id',0,9,'ID');
		$Re = $DataBase->SelectSql("select * from sc_role where Status<>0 And Vc_name='$title' And ID<>$Id limit 0,1");
		if($DataBase->GetResultRows($Re) > 0){
		   echo showErr('角色名称重复');
		   exit;
		}
		$DataBase->QuerySql("update sc_role set Vc_name='$title',Vc_intro='$intro' where ID=$Id");
		$Sql = "update sc_rule_role set T_rule='$rulelist' where I_roleID=$Id";
		$DataBase->QuerySql($Sql);
		$DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		$Admin ->AddLog('系统管理','修改角色：其ID为：'.$Id );
		echo showSuc('角色修改完毕',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'DeleteReco':  /***删除角色**/
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',0);
		if(!$FLib->IsIdList($IdList)){ echo showErr('参数错误');exit;}
		$DataBase->QuerySql("update sc_role set Status=0 where ID in ($IdList)");
		$DataBase->QuerySql("update sc_rule_role set Status = 0 where  I_roleID in ($IdList)");
		 $DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		$Admin ->AddLog('系统管理','删除角色：其ID为：'.$IdList );
		echo showSuc('角色删除完毕',$FLib->IsRequest('backurl'),$obj);
		break;
}
$DataBase->CloseDataBase();
?>
