<?php
if(!defined('WEBROOT'))exit;
include_once(WEBROOTINC.'VerifyCode'.L.'CreateVerifyCode.class.php');
$w=$FL->requeststr('w',1,0,'w',1,1);
$sitename = $g_conf['cfg_web_name'];
$user=new User();
$m=$w;
switch($w){
	//获取省
	case 'province':
		/**
		 * @author zy
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=public&w=province
		 * 输入：需登录后访问
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * array(0=>array(ID:省份ID,Vc_province:省份名))
		 * */
		$provinces=$Db->fetch_all_assoc('select ID ,Vc_province from site_province');
		returnjson($provinces);
		break;
	//获取市
	case 'city':
		/**
		 * @author zy
		 * 获取市信息
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=public&w=city
		 * 输入：需登录后访问
		 * I_provinceID:省ID
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * array(0=>array(ID:市ID,Vc_city:市名))
		 * */
		$pid=$FLib->requestint('I_provinceID',0,'省ID',1) ;
		$cities=$Db->fetch_all_assoc("select ID,Vc_city from site_city where I_provinceID=$pid");
		returnjson($cities);
		break;
	//获取区
	case 'district':
		/**
		 * @author zy
		 * 获取市信息
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=public&w=district
		 * 输入：需登录后访问
		 * I_cityID:市ID
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * array(0=>array(ID:区ID,Vc_city:区名))
		 * */
		$cid=$FLib->requestint('I_cityID',0,'城市ID',1) ;
		$districts=$Db->fetch_all_assoc("select ID,Vc_district from site_district where I_cityID=$cid");
		returnjson($districts);
		break;
	//注册页面,提供注册信息
	case 'reg':
		/**
		 * @author zy
		 * 注册页面接口
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=public&w=reg
		 * 
		 * 输入：
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * provinces:array 省
		 * prop:array 公司属性
		 * */
		$p['provinces']=$Db->fetch_all_assoc('select ID,Vc_province from site_province');//省份
		$p['prop'] = $Db->fetch_all_assoc("select id,Vc_name from sm_company_prop ");//公司属性,一维数组
		returnjson(array('err'=>0,'msg'=>'我宣你','data'=>$p));
		break;
	//登录获取验证码
	case 'yzm':
		/**
		 * @author zy
		 * 获取验证码接口
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=public&w=yzm
		 * 输入：
		 * 输出：
		 * */
		$yzmimg = new Securimage();
		$yzmimg->show();
		break;

	case 'test':
		/**
		 * @author zy
		 * 获取验证码接口
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=public&w=test
		 * 输入：
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		setcookie('refererUrl','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		dump($_COOKIE['refererUrl']);
		break;
	
		//登录页面
	case 'login':
		/**
		 * @author zy
		 * 登录页面接口
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=public&w=login
		 * 输入：
		 * 输出：
		 * */
		//登录先删除登录数据
		unsetuser();
		setcookie ('refererUrl','');
		$flag=1;
		if($flag==1){
			setcookie('refererUrl','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			$flag=0;
		}
//		dump($_COOKIE['refererUrl']);
		if(!isset($_COOKIE['refererUrl']) || (strpos($_COOKIE['refererUrl'],'login')==true) || (strpos($_COOKIE['refererUrl'],'lgout')==true)){
			$_COOKIE['refererUrl']='';
		}
//		dump($lg);
//		dump($_COOKIE['lglocation']);
//		dump($_COOKIE['refererUrl']!='');
		break;
	//检验用户名是否存在ajax
	case 'isuser':
		/**
		 * @author zy
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=public&w=isuser
		 * 输入：
		 * username:str 用户名
		 * 输出：
		 * type:int 账号类型 1邮箱 2手机号 (放在请求地址中)
		 * err:int 结果状态 -1不可用 0可用
		 * msg: 提示信息
		 *
		 * */
		//检验输入账号的类型
		$username=$FLib->requeststr('username',0,50,'输入账号',1);
		$type=0;
		//判断是邮箱
		if($FL->checkEmail($username)){$type=1;}
		//判断是电话
		if($FL->checkMobile($username)){$type=2;}
		if($type==0){returnjson(array('err'=>-1,'msg'=>'请输入正确的用户账号'));}

		//检验用户是否存在
		$sql="select ID from user_base where (Vc_mobile=$username or Vc_email=$username) and Status=1 and I_Emailauthenticate=2 and I_mobileauthenticate=2 ";
		$re = $Db->fetch_val($sql);
		if(!$re){returnjson(array('err'=>-2,'msg'=>'该用户不存在'));}
		returnjson(array('err'=>0,'msg'=>'ok','type'=>$type));
		break;
	//找回密码第一步
	case 'foegetpass_step1':
		/**
		 * @author zy
		 * 第一步,输入账号,验证码提交
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=public&w=foegetpass_step1
		 * 提交页面
		 * 输入：
		 * username:str 用户名
		 * type:int 账号类型 1邮箱 2手机号 (放在请求地址中
		 * code:str 验证码
		 * 输出：
		 * type:int 账号类型 1邮箱 2手机号 (放在请求地址中)
		 * username:str 用户名 (放在请求地址中)
		 * err:int 结果状态 -1不可用 0可用
		 * msg: 提示信息
		 *
		 * 展示页面
		 * */

		//提交后检验验证码
		if(isset($_REQUEST['submit'])){
			$username=$FLib->requeststr('username',0,50,'输入账号',1);
			$type=$FLib->requestint('type',0,'类型',1) ;
			//检验验证码
			$code=$FLib->requeststr('code',0,50,'',1,1);
			$yzmimg = new Securimage();
			$temp=strtolower($yzmimg->getCode());
			if (empty($temp)) {
				returnjson(array('err'=>-2,'msg'=>'验证码已过期'));
			} else if ($temp != strtolower($code)){
				returnjson(array('err'=>-1,'msg'=>'您输入的验证码有误'));
			}

//			header("Location: /index.php?act=user&m=public&w=foegetpass_step2&type=$type&username=$username");
			returnjson(array('err'=>0,'msg'=>'ok','type'=>$type,'username'=>$username));
		}else{
			//展示第一步

		}
		break;
	//找回密码第二步
	case 'foegetpass_step2':
		/**
		 * @author zy
		 * 获取手机或邮箱验证码,提交
		 * url地址:
		 * http://www.bigsm.com/index.php?act=user&m=public&w=foegetpass_step2
		 * 提交页面
		 * 输入：
		 * type:int 账号类型 1邮箱 2手机号 (放在请求地址中)
		 * kcode:str 验证码
		 * Vc_mobile:str 手机号
		 * 输出：
		 * err:int 结果状态 -1不可用 0可用
		 * msg: 提示信息
		 *
		 * 展示页面
		 * 输入:
		 * type:int 账号类型
		 * username:str 账号
		 * */

		//提交后检验手机验证码
		if(isset($_REQUEST['submit'])){
			//检验用户提交的验证码是否正确
			$type=$FLib->requestint('type',0,'类型',1) ;
			$inputcode = $FL->requeststr('code',0,50,'标识号',1,1);
			$Vc_mobile=$FLib->requeststr('Vc_mobile',0,50,'手机号',1,1);
			$code = iset($_SESSION['code'],array('',0));
			if(strtoupper($inputcode)!=$code[0]){ returnjson(array('err'=>-401,'msg'=>'验证码有误')); }
			if(time() > $code[1]){ returnjson(array('err'=>-402,'msg'=>'验证码已无效，请重新发送')); }
			unset($_SESSION['code']);
			unset($_SESSION['code_time']);
			$r = $Db->fetch_one("select ID from user_base where Vc_mobile=$Vc_mobile and Status=1");
			$_SESSION['setpass'] = $r;
//			header("Location: /index.php?act=user&m=public&w=foegetpass_step3&type=$type");
			returnjson(array('err'=>0,'msg'=>'ok'));
		}else{
			//展示第二步
			$p['type']=$FLib->requestint('type',0,'账号类型',1) ;
			$p['username']=$FLib->requeststr('username',0,50,'账号',1,1);
		}
		break;
	//检查短信验证码，并添加用户修改密码权限
	case 'foegetpass_step3':
		/**
		 * @author zy
		 * 输入新密码,提交
		 * url地址:
		 * http://www.bigsm.com/index.php?act=user&m=public&w=foegetpass_step3
		 * 提交页面
		 * 输入：
		 * pwd:str 新密码
		 * 输出：
		 * err:int 结果状态 -1不可用 0可用
		 * msg: 提示信息
		 *
		 * 展示页面
		 * 
		 * */
		if(isset($_REQUEST['submit'])){
			$pwd=$FLib->requeststr('pwd',0,50,'新密码',1);
			if(strlen($pwd)<6||strlen($pwd)>20)returnjson(array('err'=>-1,'msg'=>'需要6-20位的非空密码！'));
			if(!isset($_SESSION['setpass']))returnjson(array('err'=>-2,'msg'=>'修改密码权限异常，请检查！'));
			$uid=$_SESSION['setpass'];
			unset($_SESSION['setpass']);
			$r=$user->setPassByFind($uid, $pwd);
			returnjson($r);
		}else{
			//展示页面
		}
		break;
	//验证邮箱修改密码
	case 'emailmdypass':
		/**
		 * @author zy
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&w=emailmdypass
		 * 输入：需登录后访问
		 * token:str token 安全码
		 * 输出：
		 * Location: /index.php?act=user&m=public&w=foegetpass_step3&type=1
		 * */
		$token=$FLib->requeststr('token',0,50,'',1,0);
		$r = $user->checkCertified(trim($token));
		if($r['err']<0){ errorPage($r['msg'],'确定','/');}
		header('Location: /index.php?act=user&m=public&w=foegetpass_step3&type=1');

		break;
	//修改邮箱,先发送验证码ajax
	case 'sendemail':
		/**
		 * @author zy
		 * 输入新密码,提交
		 * url地址:
		 * http://www.bigsm.com/index.php?act=user&m=public&w=sendemail
		 * 提交页面
		 * 输入：
		 * Vc_email:str 邮箱
		 * 输出：
		 * err:int 结果状态 -1不可用 0可用
		 * msg: 提示信息
		 *
		 * 展示页面
		 *
		 * */
		$email = $FLib->requeststr('Vc_email',0,50,'邮箱',1);
		//设置检验码
		$re = $user->addCertified($email,$uid);
		if(!$re){ returnjson(array('err'=>-101,'msg'=>'1分钟后再发送吧！')); }
		$certification = $re;
		$ctime=date('Y-m-d H:i:s');
		$url = $Cfg->WebRoot.'/index.php?act=user&m=account&w=emailmdypass&s='.$certification;

		//-发送邮件
		$name = $lg['Vc_nickname'];
		$host = $webroot = $Config->WebRoot;
		$subject = $sitename.'邮箱验证';
		$search = $replace = array();

		$body = '<p>您于'.$ctime.'申请验证邮箱，点击以下连接即可完成验证:<br/><a href="'.$url.'" target="_blank">'.$url.'</p><p>为保障您的帐户安全，请在24小时内点击该链接，您也可以将链接复制到浏览器地址栏访问'.$url.'<br/>若您没有申请过验证邮箱 ，请及时联系我们！ </p>';

		$content = file_get_contents(WEBROOT.'tpl'.L.'mail'.L.'mail.html');

		$search[]='{uname}'; $replace[]=$name;
		$search[]='{p2pname}'; $replace[]=$sitename;
		$search[]='{host}'; $replace[]=$host;
		$search[]='{webroot}'; $replace[]=$webroot;
		$search[]='{Vc_content}'; $replace[]=$body;
		$content = str_replace($search,$replace,$content);
		$sendres = think_send_mail($email,$name, $subject, $content);
		//$sendres = think_send_mail($email, $name, $subject , $body);
		$content = 'result:'.$sendres.',uid:'.$uid.',email:'.$email;
		w_email_msg_log("绑定邮箱", $content);
		if($sendres!='ok'){returnjson(array('err'=>-201,'msg'=>'邮件发送失败'));}
		returnjson(array('err'=>0,'msg'=>'ok'));
		break;
}


?>
