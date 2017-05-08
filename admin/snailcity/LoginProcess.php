<?php
/**
* 模块：基础模块
* 描述：退出程序处理页和登录处理页
*/

//引入根目录下include里面的TopFile.php文件
require_once"../include/TopFile.php";
//接收登录页面隐藏域提交过的的值
$Work  =   $FLib->IsRequest('Work');
//根据接收到的work值分别执行不同的程序
switch ($Work)
{
	//如果work是LoginCheck ， 就进行登录处理
    case    'LoginCheck':
        $UserName   =    $FLib->RequestChar('UserName',0,50,'用户名',1);
        $UserPwd    =    $FLib->RequestChar('UserPwd',0,50,'密码',1);
        $UserIp     =    $FLib->GetClientIp();
        if ($UserName    ==    ''    ||    $UserPwd    ==    ''    ||    $UserIp    ==    '')
		{
            echo showErr('对不起，登录失败');
	        exit;
        }

        $DataBase    ->OpenDataBase();
        // $Admin->Logoff();
        $LoginResult    =    $Admin->Login($UserName, $UserPwd, $UserIp);
		//根据登录处理返回的结果做处理
	    switch ($LoginResult)
        {
            case    0    :
                $Admin -> AddLog('系统安全','用户登录：登录失败，原因：用户名不存在！');
                echo showErr('对不起，用户不存在');
			    break;	
            case    1    :
            	/*用户信息写入cookie zys add*/
            	//$skey = $UserName.','.$UserPwd;
            	//$Admin->FLib->AdminSetcookie2('adminlgstaus', ase_encode($skey));
            	//$Admin->FLib->AdminSetcookie2('adminlgtime', time());
            	$_SESSION['adminlgtime']=time();
            	
			    $Admin ->AddLog('系统安全','用户登录：登录成功！');
                echo showTip('登录成功！','IndexPopWin.php','self','tourl');//直接跳转
				break;	
            case    2    :
			    $Admin ->AddLog('系统安全','用户登录：登录失败，原因：此用户被禁用！');
			     echo showErr('对不起，['.$UserName. ']用户被禁止登录');
			     break;	
            case    3    :
			    $Admin ->AddLog('系统安全','用户登录：登录失败，原因：密码错误！');
			    echo showErr('对不起，登录密码不正确！');
			    break;	
		    case    4    :
			    $Admin ->AddLog('系统安全','用户登录：登录失败，原因：IP被锁！');
			    echo showErr('对不起，为了安全此IP暂时被锁定！');
			    break;	
			case    5    :
			    $Admin ->AddLog('系统安全','用户登录：登录失败，原因：IP黑名单！');
			    echo showErr('对不起，此IP已列入黑名单！');
			    break;	
            default    :
			    $Admin ->AddLog('系统安全','用户登录：登录失败，原因：不明！');
			    echo showErr('对不起，登录失败');
			    break;	
        }
        exit;
		//如果work是LoginOff ， 就退出程序
	    case    'LoginOff':
		    if (!$Config->Link)
			{
			    $DataBase->OpenDataBase();
			}
			$Admin ->AddLog('系统安全','用户退出：安全退出系统！',$Admin->Uid);
			$Admin-> Logoff();
			echo $FLib->SelfUrl('../index.php');
			exit;
}

?>
