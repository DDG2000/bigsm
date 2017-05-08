<?php
/**
* 模块：基础模块
* 描述：权限处理页
* 作者：张绍海
*/ 

//引入根目录下include里面的TopFile.php文件
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SYS_SET_POPEDOM');

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);

switch($Work)
{
	case 'MdyReco': /**修改记录**/
		$Admin->CheckPopedoms('SC_SYS_SET_POPEDOM_MDY');
	     GetValue($Config->AdminPopedKeypattern,$FLib);
		 $Id = $FLib->RequestInt('Id',0,9,'ID');
		 if($Id == 0)
		 {
		 	echo showErr('参数错误');
			exit;
		 }
		 $Re = $DataBase->SelectSql('select  ID FROM sc_popedom where Vc_key=\''. $Pkey . '\' And Status<>0 and ID<>'.$Id);
		 if($DataBase->GetResultRows($Re) > 0)
		 {
		 	echo showErr('权限标识已经存在!');
			exit;
		 }
		 $DataBase->QuerySql("update sc_popedom set Vc_name='$title',Vc_key='$Pkey',Vc_intro='$intro',I_parentID=$parent,I_order=$order where ID=$Id");
		 $DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		 $Admin ->AddLog('系统管理','修改权限：其Id为：'.$Id );
		 echo showSuc('权限记录修改完毕',$FLib->IsRequest('backurl'),$obj);
         break;
	case 'AddReco': /**增加记录**/
		$Admin->CheckPopedoms('SC_SYS_SET_POPEDOM_MDY');
		 GetValue($Config->AdminPopedKeypattern,$FLib);
		 $Re = $DataBase->SelectSql('select  ID FROM sc_popedom where Vc_key=\''. $Pkey . '\' And Status<>0');
		 if($DataBase->GetResultRows($Re) > 0)
		 {
		 	echo showErr('权限标识已经存在!');
			exit;
		 }
		 $DataBase->QuerySql("insert into sc_popedom(Vc_name,Vc_key,Vc_intro,I_parentID,I_operatorID,I_order,Createtime) values('$title','$Pkey','$intro',$parent,$Admin->Uid,$order,now())");
		 $DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		 $Admin ->AddLog('系统管理','增加权限：其名称为：'.$title .'，标识为：'. $Pkey);
		 echo showSuc('权限记录增加完毕',$FLib->IsRequest('backurl'),$obj);
         break;
    case 'DeleteReco': /**删除记录**/
		 $Admin->CheckPopedoms('SC_SYS_SET_POPEDOM_MDY');
		 $IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		 if(!$FLib->IsIdList($IdList))
		 {
		 	echo showErr('参数错误');
			exit;
		 }
		 $Idarray = explode(',',$IdList);
		 for($i=0;$i<count($Idarray);$i++)
		 {
		 	
		 	$Re = $DataBase->SelectSql('select * from sc_popedom where Status<>0 And I_parentID ='. $Idarray[$i]);
			$cc = $DataBase->GetResultRows($Re);
			if($cc == 0)
			{
				$DataBase->QuerySql('update sc_popedom set Status=0 where ID='. $Idarray[$i]);
			}
		 }
		 $DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		 $Admin ->AddLog('系统管理','删除权限：其ID为：'.$IdList);
		 echo showSuc('权限记录删除完毕'.$cc,$FLib->IsRequest('backurl'),$obj);
         break;
}

function GetValue($P,$FLib)
{
	global $parent,$title,$Pkey,$intro,$order;
	$parent         = $FLib->RequestInt('parent',0,9,'父ID');
    $title          = $FLib->RequestChar('title',0,50,'标题',1);
    $Pkey           = strtoupper($FLib->RequestChar('Pkey',0,50,'权限标识',1));
    $intro          = $FLib->RequestChar('intro',1,50,'备注',1);
    $order          = $FLib->RequestInt('order',0,9,'排列顺序');
	if(!preg_match('/'.$P.'/',$Pkey))
	{
		echo $FLib ->Alert('权限标识格式有误!','self','hidden');
		exit;
	}
}
$DataBase->CloseDataBase();
?>