<?php
/**
* 模块：基础模块
* 描述：用户各种操作的处理页面包括锁定，禁用，修改密码 删除记录等 具体实现是根据接收到的$Work值来判断的
* 作者：张绍海
*/


//引入根目录下include里面的TopFile.php文件
require_once"../include/TopFile.php";
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);

switch ($Work)
{
    case 'MdyPwd':
		/****函数:修改密码
		'说明:
		'   以下分两类情况说明:
		'       1.具有[系统管理]权限的操作者
		'           a.允许修改所有人的密码
		'           b.修改密码时无需输入原密码
		'       2.不具有[系统管理]权限的操作者
		'           a.只允许修改自己的密码
		'           b.修改密码前必须给出原密码
		****/
		$Id     = $FLib->RequestInt('Id',0,50,'ID');
		if($Id != $Admin->Uid){	$Admin->CheckPopedoms('SC_SYS_SET_USER_EDIT'); }

		$pwd    = $FLib->RequestChar('pwd',1,50,'密码',1);
		$pwd1   = $FLib->RequestChar('pwd1',1,50,'密码',1);
		if(strlen($pwd)<6||strlen($pwd)>20) { echo showErr('密码最小长度6，最大长度20!'); exit; }
		if($pwd != $pwd1) { echo showErr('两次密码输入不一致!'); exit; }
		$pwd = md5($GLOBALS['Config']->PasswordEncodeKey .md5($pwd));
		$DataBase->QuerySql("update sc_user set Vc_password='$pwd'  Where ID=$Id");
		$Admin->AddLog('系统管理','修改用户密码：其ID为：'.$Id);
		echo showSuc('用户密码修改成功',$FLib->IsRequest('backurl'),$obj); 
		break;
    case 'MdyReco':
		/***函数:修改记录***/
		$Admin->CheckPopedoms('SC_SYS_SET_USER_MDY');
		$intro   = $FLib->RequestChar('intro',1,500,'备注',1);
		$title = $FLib->RequestChar('title',0,50,'用户名',1);
		$grouplist   = $FLib->RequestChar('grouplist',1,500,'组',1);
		$rolelist   = $FLib->RequestChar('rolelist',1,500,'角色',1);
		$rulelist   = $FLib->RequestChar('rulelist',1,500,'权限',1);
		$audit_role   = $FLib->RequestChar('audit_role',1,500,'审核角色',1);
		//$pope_role   = $FLib->RequestInt('pope_role',-1,9,'角色');
		$Id   = $FLib->RequestInt('Id',0,9,'用户Id');
		if($grouplist!='' && !$FLib->IsIdlist($grouplist)){ echo showErr('用户组参数错误'); exit;}
		if($rolelist!='' && !$FLib->IsIdlist($rolelist)){ echo showErr('独立角色参数错误'); exit;}
		if($rulelist!='' && !$FLib->IsIdlist($rulelist)){ echo showErr('独立权限参数错误'); exit;}
		if($audit_role!='' && !$FLib->IsIdlist($audit_role)){ echo showErr('审核角色参数错误'); exit;}
		
		//用户组修改
		$DataBase->QuerySql("update sc_group_user set status =0 Where I_userID=$Id");
		$Idarray = explode(',',$grouplist);
		for($i=0;$i<count($Idarray);$i++)
		{
			if($Idarray[$i] != '')
			{
			$DataBase->QuerySql("insert into sc_group_user(I_userID,I_groupID,I_operatorID,Createtime) values($Id,$Idarray[$i],".$Admin->Uid.",now())");
			}
		}
		//独立角色修改
		$DataBase->QuerySql("update sc_rule_user set T_rule='$rolelist' Where status<>0 and I_type=1 and  I_userID=$Id");
		//独立权限修改
		$DataBase->QuerySql("update sc_rule_user set T_rule='$rulelist' Where status<>0 and I_type=2 and  I_userID=$Id");
		//审核角色修改
		$DataBase->QuerySql("update sc_rule_user set T_rule='$audit_role' Where status<>0 and I_type=3 and  I_userID=$Id");
		
		//用户本身修改
		$scda = array();
		$scda['Vc_intro'] = $intro;
		$scda['Vc_name'] = $title;
		$scda['Dt_update@'] = 'now()';//设置权限更新时间，需要更新权限
		$DataBase->autoExecute('sc_user',$scda,'update',"ID=$Id");
		
		$Admin->AddLog('系统管理','修改用户：其ID为：'.$Id);
		echo showSuc('用户修改完毕',$FLib->IsRequest('backurl'),$obj); 
		break;
    case 'AddReco':
		/***函数:添加记录***/
		$Admin->CheckPopedoms('SC_SYS_SET_USER_EDIT');
		$title = $FLib->RequestChar('title',0,50,'用户名',1);
		$pwd   = $FLib->RequestChar('pwd',0,50,'密码',1);
		$intro   = $FLib->RequestChar('intro',1,500,'备注',1);
		$grouplist   = $FLib->RequestChar('grouplist',1,500,'用户组',1);
		$rolelist   = $FLib->RequestChar('rolelist',1,500,'独立角色',1);
		$rulelist   = $FLib->RequestChar('rulelist',1,500,'独立权限',1);
		$audit_role   = $FLib->RequestChar('audit_role',1,500,'审核角色',1);
		//$rolelist = $rolelist!=''?$rolelist:1;
		if($grouplist!='' && !$FLib->IsIdlist($grouplist)){ echo showErr('用户组参数错误'); exit;}
		if($rolelist!='' && !$FLib->IsIdlist($rolelist)){ echo showErr('独立角色参数错误'); exit;}
		if($rulelist!='' && !$FLib->IsIdlist($rulelist)){ echo showErr('独立权限参数错误'); exit;}
		if($audit_role!='' && !$FLib->IsIdlist($audit_role)){ echo showErr('审核角色参数错误'); exit;}

		if(strlen($pwd)<6||strlen($pwd)>20) { echo showErr('密码最小长度6，最大长度20!'); exit; }

		$pwd = md5($GLOBALS['Config']->PasswordEncodeKey .md5($pwd));
		$ip=$_SERVER['REMOTE_ADDR'];
		if(!$FLib->CheckUserName($title))
		{
			echo showErr('用户名填写不正确!');
			exit;
		}	
		$Re = $DataBase->SelectSql("select id from sc_user where Status<>0 And Vc_name='$title'");
		if($DataBase->GetResultRows($Re) > 0)
		{
			echo showErr('用户已存在!');
			exit;
		}
		$DataBase->QuerySql("insert into sc_user(Vc_name,Vc_password,Vc_intro,I_operatorID,I_number,Dt_logintime,Vc_loginIP,Status,CreateTime) values('$title','$pwd','$intro',".$Admin->Uid.",0,now(),'$ip',2,now())");

		$Re = $DataBase->SelectSql("select ID from sc_user where Status<>0 And Vc_name='$title' limit 0,1");
		if($DataBase->GetResultRows($Re) > 0)
		{
				$Rs = $DataBase->GetResultArray($Re);
				$userp = $Rs[0];
				//加入组信息
				$Idarray = explode(',',$grouplist);
				for($i=0;$i<count($Idarray);$i++)
				{
					if($Idarray[$i] != '')
					{
					$DataBase->QuerySql("insert into sc_group_user(I_userID,I_groupID,I_operatorID,Createtime) values($userp,$Idarray[$i],".$Admin->Uid.",now())");
					}
				}
				$DataBase->QuerySql("insert into sc_rule_user(I_userID,I_type,T_rule,I_operatorID,Createtime) values
					 ($userp,1,'".$rolelist."',".$Admin->Uid.",now())
					,($userp,2,'".$rulelist."',".$Admin->Uid.",now())
					,($userp,3,'".$audit_role."',".$Admin->Uid.",now())
				");
		}
		$Admin->AddLog('系统管理','添加用户： '.$title);
		echo showSuc('用户添加完毕',$FLib->IsRequest('backurl'),$obj); 
		break;
    case 'LockReco':
		/**
		* 模块：基础模块
		* 描述：锁定记录
		* 版本：SnailCity内容管理系统 V0.1系统
		* 作者：张绍海
		* 书写日期：2011-03-16
		* 修改日期：
		* 版权所有：北京元诚正信信息技术有限公司 www.snailcity.com
		* Copyright Yuanchengzhengxin Co.Ltd. All rights reserved.
		*/
		$Admin->CheckPopedoms('SC_SYS_SET_USER_MDY');
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		$Flag = $FLib->RequestInt('Flag',1,9,'Flag');
		if(!$FLib->IsIdlist($IdList))
		{
			echo showErr('参数错误');
			exit;
		}
		$DataBase->QuerySql("update sc_user set Status= $Flag where ID in( $IdList )");
		if($Flag == 1)
		{ 
			$msg = '解禁用户：其ID为：';
			$Admin->AddLog('系统管理',$msg.$IdList);
			echo showSuc('用户解锁完毕',$FLib->IsRequest('backurl'),$obj);
		}
		else
		{
			$msg = '禁用用户：其ID为：';
			$Admin->AddLog('系统管理',$msg.$IdList);
			echo showSuc('用户锁定完毕',$FLib->IsRequest('backurl'),$obj);
		}
		break;
    case 'DeleteReco':
		/**函数:删除记录**/
		$Admin->CheckPopedoms('SC_SYS_SET_USER_EDIT');
		$IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		if(!$FLib->IsIdlist($IdList))
		{
			echo showErr('参数错误');
			exit;
		}
		$DataBase->QuerySql("update sc_user set Status= 0 where ID in( $IdList )");
		$DataBase->QuerySql("update sc_rule_user set Status= 0 where I_userID in( $IdList )");
		$Admin->AddLog('系统管理','删除用户：其ID为： '.$IdList);
		echo showSuc('用户删除完毕',$FLib->IsRequest('backurl'),$obj);
		break;
}

?>
