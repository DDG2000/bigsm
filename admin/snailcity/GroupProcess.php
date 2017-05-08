<?php
/**
* 模块：基础模块
* 描述：用户组列表处理页
* 作者：张绍海
*/

require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SYS_SET_GROUP_MDY');

$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);

switch($Work)
{
	case 'MdyReco': /**修改记录**/
		GetValue($Config->AdminPopedKeypattern,$FLib);
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		if($Id == 0){ echo showErr('参数错误');exit;}
		$DataBase->QuerySql("update sc_group set Vc_name='$title',Vc_intro='$intro',I_order=$order where ID=$Id");
		$DataBase->QuerySql("update sc_rule_group set T_rule='$rolelist' where I_type=1 and I_groupID=$Id");
		$DataBase->QuerySql("update sc_rule_group set T_rule='$rulelist' where I_type=2 and I_groupID=$Id");
		 $DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		$Admin ->AddLog('系统管理','修改组：其Id为：'.$Id );
		echo showSuc('组记录修改完毕',$FLib->IsRequest('backurl'),$obj);
		break;
	case 'AddReco': /**增加记录**/
		GetValue($Config->AdminPopedKeypattern,$FLib);
		$DataBase->QuerySql("insert into sc_group(Vc_name,Vc_intro,I_parentID,I_operatorID,I_order,Createtime) values('$title','$intro',$parent,$Admin->Uid,$order,now())");
		$groupid = $DataBase->GetlastID();

		$jcrole = ''; //继承的角色
		$jcrule = ''; //继承的权限
		if ($parent != 0){
			$Rs = $DataBase->GetResultOne('select T_inheritrule,T_rule from sc_rule_group where status<>0 and I_type=1 and I_groupID='.$parent.' order by createtime desc limit 0,1');
			if (is_array($Rs)){
				$jcrole = $Rs[0].($Rs[1]==''?'':','.$Rs[1]);
			}
			$Rs1 = $DataBase->GetResultOne('select T_inheritrule,T_rule from sc_rule_group where status<>0 and I_type=2 and I_groupID='.$parent.' order by createtime desc limit 0,1');
			if (is_array($Rs1)){
				$jcrule = $Rs[0].($Rs[1]==''?'':','.$Rs[1]);
			}
		}

		$DataBase->QuerySql("insert into sc_rule_group(I_groupID,I_type,T_inheritrule,I_operatorID,T_rule,Createtime) values('$groupid','1','$jcrole',$Admin->Uid,'$rolelist',now())");
		$DataBase->QuerySql("insert into sc_rule_group(I_groupID,I_type,T_inheritrule,I_operatorID,T_rule,Createtime) values('$groupid','2','$jcrule',$Admin->Uid,'$rulelist',now())");

		 $DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		$Admin ->AddLog('系统管理','增加组：其名称为：'.$title);
		echo showSuc('组记录增加完毕',$FLib->IsRequest('backurl'),$obj);
		break;
		 
    case 'DeleteReco': /**删除记录**/
		$IdList = $FLib->RequestChar('IdList',0,10000,'IdList',1);
		if (!$FLib->IsIdList($IdList)){ echo showErr('参数错误');exit;}
		$Idarray = explode(',',$IdList);
		for($i=0;$i<count($Idarray);$i++){
			$Result = $DataBase->SelectSql('select * from sc_group where Status<>0 And I_parentID ='. $Idarray[$i]);
			if($DataBase->GetResultRows($Result) == 0){
				$DataBase->QuerySql('update sc_group set Status=0 where ID='. $Idarray[$i]);
				$DataBase->QuerySql('update sc_rule_group set Status=0 where I_groupID ='. $Idarray[$i]);
			}
		}
		 $DataBase->autoExecute('sc_user',array('Dt_update@'=>'now()'),'update','1');
		$Admin ->AddLog('系统管理','删除组：其ID为：'.$IdList);
		echo showSuc('组记录删除完毕',$FLib->IsRequest('backurl'),$obj);
		break;
}

function GetValue($P,$FLib)
{
	global $parent,$title,$rolelist,$intro,$order,$rulelist;
	$parent         = $FLib->RequestInt('parent',0,9,'父ID');
    $title          = $FLib->RequestChar('title',0,50,'标题',1);
    $intro          = $FLib->RequestChar('intro',1,500,'备注',1);
    $order          = $FLib->RequestInt('order',0,9,'排列顺序');
	$rolelist       = $FLib->RequestChar('rolelist',1,0,'组的独立角色',1);
    $rulelist       = $FLib->RequestChar('rulelist',1,0,'组的独立权限',1);
}
$DataBase->CloseDataBase();
?>