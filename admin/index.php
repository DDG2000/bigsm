<?php
/**
* 模块：基础模块
* 描述：系统默认登录页面
*/
//zys add
if(isset($_COOKIE['adminlgstaus'])){
	header('Location:snailcity/IndexPopWin.php');
	exit();
}
//定义根目录
if(!defined('L')){define('L',DIRECTORY_SEPARATOR);}
if(!defined('WEBROOT')){define('WEBROOT',dirname(dirname(__FILE__)).L);}
if(!file_exists(WEBROOT . 'include' . L . 'DataConfig.php')){
    header('Location:../install/php/InstallStepFirst.php');
    exit();
}
if(file_exists(WEBROOT . 'install' . L . 'index.php' )){
    die('请先删除install文件夹刷新浏览器继续！');
}

//引入根目录下include里面的UserConfig.class.php文件
require_once(WEBROOT . 'include' . L . 'UserConfig.class.php');
//实例化对象
$Config = new UserConfig;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtmlcommon/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title><?php echo $Config ->SysName?></title>

<link href="style/link.css" rel="stylesheet" type="text/css" />
<link href="style/alert.css" rel="stylesheet" type="text/css" />
<link href="style/highslide.css" rel="stylesheet" type="text/css" />
<script src="js/highslide-with-html.js" type="text/javascript"></script>
<script src="js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="js/jquery.humanmsg.js" type="text/javascript"></script>
<script src="js/jquery.base.js" type="text/javascript"></script>
<style type="text/css">
body {background-color: #FFFFFF;font-size:12px;}
.td {font-size:12px;color: #000000;text-decoration: none;}
.bd {height: 15px;*height:20px;width: 108px;margin:0;}
.button01-out{PADDING:1px;font-size:12px;WIDTH: 60px;CURSOR: pointer; }
</style>
</head>

<body >

<table width="99%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td height="36"></td>
    </tr>
</table>

<table width="99%" border="0" cellpadding="0" cellspacing="0" background="image/bj.jpg">
    <tr>
        <td height="456" align="center" valign="middle"><table width="609" height="118" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center" valign="middle">

				    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="14%" align="left" valign="top" colspan="2"><img src="image/logo_p2p.jpg" />
						    </td>
                            <td width="86%" align="left" valign="bottom">&nbsp;</td>
                        </tr>
                    </table>

                    <table width="100%" border="0" cellpadding="0" cellspacing="0" background="image/admin_bj.jpg">
                        <tr>
                            <td width="39%" align="left" valign="top">
							    <img src="image/admin_02.jpg" width="239" height="216" />
							</td>
                            <td align="center" valign="middle" width="5%">&nbsp;
							
							</td>
                            <td width="51%" align="center" valign="middle">
							    <form id="form1" name="form1" method="post" action="snailcity/LoginProcess.php" check="1" target="hideframe">
                                    <table width="60%" border="0" cellspacing="0" cellpadding="0">
									    <tr>
										    <td height="22">
											    <img src="image/admin_03.gif" width="172" height="27" />
											</td>
										</tr>
									    <tr>
										    <td height="22">
											</td>
										</tr>
									    <tr>
										    <td height="22">
											    <table width="100%" height="22" border="0" cellpadding="0" cellspacing="0">
												    <tr>
													    <td width="34%" class="td">用户名:</td>
														<td width="66%"><input name="UserName" type="text" class="bd" isc="" maxlength="50" /></td>
												    </tr>
										        </table>
										   </td>
									 </tr>                  
									 <tr>
									     <td height="22">
										     <table width="100%" height="22" border="0" cellpadding="0" cellspacing="0">
											     <tr>
												     <td width="34%" class="td">密&nbsp;&nbsp;码:</td>
													 <td width="66%"><input name="UserPwd" type="password" autocomplete="off" class="bd" isc="" maxlength="50"/></td>
												 </tr>
											</table>
										</td>
									</tr>
									<tr>
									    <td height="22"></td>
									</tr>
									<tr>
									    <td height="22" align="center">
										    <table width="100" border="0" cellspacing="0" cellpadding="0">
											    <tr>
												    <td width="40" align="right">
													    <input type="submit" class="button01-out" value="提  交" name="submit1"><input type="hidden" name="Work" value="LoginCheck">
													</td>
													<td width="20">
													    &nbsp;&nbsp;&nbsp;&nbsp;
													</td>
													<td width="40" align="right">
													    <input type="reset" class="button01-out" value="取  消" name="reset1">
												    </td>
										        </tr>
									        </table>
									    </td>
									</tr>
						        </table>
                            </form>              
					    </td>
                        <td width="5%" align="right" valign="top"><img src="image/admin_04.jpg" width="2" height="215"/></td>
                     </tr>
                </table>
		    </td>
         </tr>
      </table>

      <table width="609" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td>
			      <table width="100%" border="0" cellpadding="0" cellspacing="0">
				      <tr>
                          <td height="20" colspan="2">
					      </td>
                      </tr>
					  <tr>
					      <td width="60" height="22" align="left" class="td"></td>
					      <td width="94%" align="left" class="td">注意：</td>
					  </tr>
					  <tr>
					      <td width="60" height="22" align="left" class="td"></td>
						  <td align="left" class="td">
						      1.退出本系统前请先注销用户,防止管理员权限驻留，产生安全隐患！
						  </td>
					 </tr>
					 <tr>
					     <td width="60" height="22" align="left" class="td">
						    </td>
					           <td align="left" class="td">
						          2.本系统要求服务器端及客户端均安装有IE5.5以上版本，否则某些功能将无法正常使用。
						       </td>
					       </tr>
                       </table>
		           </td>
               </tr>
           </table>
       </td>
    </tr>
</table>
</body>
</html>
<iframe id="hideframe" name="hideframe" style="display:none;">