<?php
if(!defined('WEBROOT'))exit;
include_once(WEBROOTINC.'VerifyCode'.L.'CreateVerifyCode.class.php');
//include_once(WEBROOTINC.'ssl'.L.'RSA.php');

require_once(WEBROOTINCCLASS . 'Company.php');
$company=new Company();
$u=new User();
$sitename = $g_conf['cfg_web_name'];
$w=$FL->requeststr('w',1,0,'w',1,1);
if($w==''){$w='reg';}
switch($w){
/*
***注册登录账号*********************************************************
*/
	//*检验手机号是否已注册*//
	case 'checkmobile':
		/**
		 * @author zy
		 * ajax 检验手机号
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=userprocess&w=checkmobile
		 * 输入：
		 * Vc_mobile:str 手机号
		 * 输出：
		 * err:int 结果状态 -1不可用 0可用
		 * msg: 提示信息
		 * */
		//获取手机信息
		$da['Vc_mobile']=$FLib->requeststr('Vc_mobile',0,50,'手机号',1);
		$re=$u->checkuser($da);
		if($re){returnjson(array('err'=>-1,'msg'=>'手机已注册'));}
		returnjson(array('err'=>0,'msg'=>'ok'));
		break;
	//**登录验证码检验   **//
	case 'ckyzm':
		/**
		 * @author zy
		 * ajax 检验验证码
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=userprocess&w=ckyzm
		 * 输入：
		 * yzm:str 验证码
		 * 输出：
		 * err:int 结果状态 -1不可用 0可用
		 * msg: 提示信息
		 * */
		$skey=$FLib->requeststr('yzm',0,50,'',1,1);
		$yzmimg = new Securimage();
//		if(strtolower($yzmimg->getCode()) != strtolower($skey)){ returnjson(array('err'=>-1,'msg'=>'验证码有误')); }
		$temp=strtolower($yzmimg->getCode());
		if (empty($temp)) {
			returnjson(array('err'=>-2,'msg'=>'验证码已过期'));
		} else if ($temp != strtolower($skey)){
			returnjson(array('err'=>-1,'msg'=>'验证码有误'));
		}
		returnjson(array('err'=>0,'msg'=>'ok'));
		break;
	//验证手机验证码
	case 'checkcode':
		/**
		 * @author zy
		 * ajax 检验手机验证码
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=userprocess&w=checkcode
		 * 输入：
		 * code:str 验证码
		 * Vc_mobile:str 手机号
		 * 输出：
		 * err:int 结果状态 -1不可用 0可用
		 * msg: 提示信息
		 * */
		$code=$FLib->requeststr('code',0,50,'手机验证码',1);
		$mobile=$FLib->requeststr('Vc_mobile',0,50,'手机号',1);
		if($code!=$_SESSION['mobilecode'][$mobile]){
			returnjson(array('err'=>-1,'msg'=>'验证码错误'));
		}
		returnjson(array('err'=>0,'msg'=>'ok'));
		break;
	//验证公司名是否已注册
	case 'checkcompany':
		/**
		 * @author zy
		 * ajax 检验公司名是否已注册
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=userprocess&w=checkcompany
		 * 输入：
		 * code:str 验证码
		 * Vc_mobile:str 手机号
		 * 输出：
		 * err:int 结果状态 -1不可用 0可用
		 * msg: 提示信息
		 * */
		$Vc_company=$FLib->requeststr('Vc_company',0,50,'公司名',1);
		$re=$company->isLeged($Vc_company);
		if($re){
			returnjson(array('err'=>-1,'msg'=>'公司已经认证或正在认证中'));
		}
		returnjson(array('err'=>0,'msg'=>'ok'));
		break;
	case 'test':
		/**
		 * @author zy
		 * ajax 检验手机号
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=userprocess&w=test
		 * 输入：
		 * Vc_mobile:str 手机号
		 * 输出：
		 * err:int 结果状态 -1不可用 0可用
		 * msg: 提示信息
		 * */
		$uname=$FLib->requeststr('username',0,50,'用户名',1);
		$sql = "select * from user_base where Status>0";
		//验证邮箱,手机号 都必须是已验证的
		if (preg_match('/^[a-zA-Z0-9_\-]{1,}@[a-zA-Z0-9_\-]{1,}\.[a-zA-Z0-9_\-.]{1,}$/', $uname)) {
			$sql .= " and Vc_Email='{$uname}' and I_Emailauthenticate=2 ";
		} else if (preg_match('/^(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/', $uname)) {
			$sql .= " and Vc_mobile='{$uname}' and I_mobileauthenticate =2 ";
		}
		echo $sql;
		$rs=$this->Db->GetResultOne($sql);

		$pass='123456';
		$pass=$u->pwd($u->pwd($pass)) ;
		dump($pass);
		break;
	/*用户注册*/
	case 'reg':
		/**
		 * @author zy
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=userprocess&w=reg
		 * http://www.bigsm.com/index.php?act=user&m=userprocess&w=reg&Vc_mobile=17777777222&Vc_password=123456&repassword=123456&Vc_truename=张三&Vc_company=长城&I_provinceID=23&I_cityID=255&I_districtID=3333&Vc_address=大厦&Vc_propID=1
		 * 输入：
		 * Vc_mobile:str 手机号
		 * Vc_password:str 密码
		 * repassword:str 密码重复
		 * Vc_truename:str 真实姓名
		 * Vc_company:str  公司名
		 * I_provinceID:int 省份ID
		 * I_districtID;int 城市ID
		 * I_districtID:int 区县ID
		 * Vc_address:str 具体地址
		 * I_propID:str 公司性质
		 * yzm:str 验证码
		 * 输出：
		 * err:int 结果状态 -1不可用 0可用
		 * msg: 提示信息
		 * */
		$da=array();
		$co=array();
		//获取注册信息电话mobile,密码upass,密码重复urepass,正是姓名name,公司名字company,地址procince,city,address,公司性质propid
		$da['Vc_mobile']=$FLib->requeststr('Vc_mobile',0,50,'手机号',1);
		$da['Vc_password']=$FLib->requeststr('Vc_password',0,20,'密码',1);
		$repassword=$FLib->requeststr('repassword',0,20,'密码重复',1);
		if($repassword!=$da['Vc_password']){returnjson(array('err'=>-1,'msg'=>'两次输入密码不一致'));}
		$da['Vc_truename']=$FLib->requeststr('Vc_truename',0,50,'真实姓名',1) ;
		$co['Vc_name']=$FLib->requeststr('Vc_company',0,50,'公司名',1) ;
		$co['I_provinceID']=$FLib->requestint('I_provinceID',0,20,'省份ID',1) ;
		$co['I_cityID']=$FLib->requestint('I_cityID',0,20,'城市ID',1) ;
		$co['I_districtID']=$FLib->requestint('I_districtID',0,20,'区县ID',1) ;
		$co['Vc_address']=$FLib->requeststr('Vc_address',0,50,'具体地址',1) ;
		$props=$_POST['Vc_propID'] ;//没有防注入
		$co['Vc_propID']=implode(',',$props);
//		$yzm=$FLib->requeststr('yzm',0,50,'验证码',1);
//		$yzmimg = new Securimage();
//		if(!$yzmimg->check($yzm)){ returnjson(array('err'=>-401,'msg'=>'验证码有误')); }
		//保存公司信息

		//查询公司名,是否已认证或是正在认证中
		$re=$company->isLeged($co['Vc_name']);
		if(!$re){ returnjson(array('err'=>-2,'msg'=>'公司已经认证或正在认证中,若有疑问请联系客服')); }
		//返回公司id
		$r=$company->add($co);
		if(!$r){ returnjson(array('err'=>-3,'msg'=>'company add false')); }
		//获取新建公司id
		$da['I_companyID']=$r;
		//添加用户信息
		$r=$u->add($da);
		if(!$r){ returnjson(array('err'=>-4,'msg'=>'user add false')); }

		//设置登录seesion,必须
		setuser($r);

		//登录时间在获取用户信息中使用
		$_SESSION['logintime']=time();
		//保存登录状态24分钟
		$skey = $da['Vc_mobile'].','.$da['Vc_password'];
		$newtime=time()+$u->Cfg->expirationTime;
		setcookie('loginstaus', ase_encode($skey), $newtime,'/');
		setcookie('logintime',time(),$newtime,'/');
		//保存登录手机号30天
		setcookie('loginmobile',$da['Vc_mobile'],time()+30*24*3600,'/');

		returnjson(array('err'=>0, 'msg'=>'ok','url'=>'http://www.bigsm.com/index.php?act=user&m=account&w=authcompany'));
		break;
	//登录检验
	case 'lg':
		/**
		 * @author zy
		 * 用户登录,检验登录手机号,验证输入密码错误次数,5次自动锁定,24小时解锁
		 * ur
		 * l地址：
		 * http://www.bigsm.com/index.php?act=user&m=userprocess&w=lg
		 * 输入：
		 * username:str 账号 手机号/邮箱
		 * Vc_password:str 密码
		 * yzm:str 验证码
		 * 输出：
		 * url:str 跳转地址
		 * err:int 结果状态 -1不可用 0可用
		 * msg: 提示信息
		 * */
		$uname=$FLib->requeststr('username',0,50,'用户名',1);
		$upass=$FLib->requeststr('Vc_password',0,50000,'密码',1);

//		$yzm=$FLib->requeststr('yzm',0,50,'验证码',1);
//		$yzmimg = new Securimage();
//		if(!$yzmimg->check($yzm)){ returnjson(array('err'=>-401,'msg'=>'验证码有误！')); }
		//登录检测 用户名密码 账号是否被锁定 登录日志
		$r=$u->login($uname,$upass);
		if($r['err']<0){returnjson($r);}
//		if($r['user']['uid']<10000) returnjson(array('err'=>-1,'msg'=>'系统用户禁止登陆！'));

		//登录方式1 邮箱 2手机
//		if (preg_match('/^[a-zA-Z0-9_\-]{1,}@[a-zA-Z0-9_\-]{1,}\.[a-zA-Z0-9_\-.]{1,}$/', $uname)) {
//			$type=1;
//		} else if (preg_match('/^(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/', $uname)) {
//			$type=2;
//		}

		//设置登录session,必须的
		setuser($r['user']);
		//登录保持
		$isremember=$FLib->requestint('isremember',0,'登录保持',1);
		$skey = $uname.','.$upass;
		if($isremember==1){
			//保存登录10天
			setcookie('loginstaus', ase_encode($skey), 10*24*3600,'/');
			setcookie('logintime',time(),10*24*3600,'/');
		}else{
			//24分钟内免登录
			$newtime=time()+$u->Cfg->expirationTime;
			setcookie('loginstaus', ase_encode($skey), $newtime,'/');
			setcookie('logintime',time(),$newtime,'/');
		}
		//保存登录时间
		$_SESSION['logintime']=time();
		//保存用户的手机号30天
		setcookie('loginmobile',$uname,time()+30*3600*24,'/');

		$r['user'] = getuser(0);

		//是否跳转到登录之前页面
		if($_COOKIE['refererUrl']!=''){
			$refererUrl=$_COOKIE['refererUrl'];
			$refererUrl='http://www.bigsm.com/index.php?act=requirement&m=searchlist';
		}else{
			$refererUrl='http://www.bigsm.com/index.php?act=user&m=index';
		}
		setcookie('refererUrl',"",-1,'/');
		returnjson(array('err'=>0, 'msg'=>'ok','url'=>$refererUrl,'data'=>$r));
		break;
	/*登出*/
	case 'lgout':
		/**
		 * @author zy
		 * 登出,清除登录数据,跳转登录页面
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=userprocess&w=lgout
		 * 输入：
		 * 输出：
		 * err:int 结果状态 -1不可用 0可用
		 * msg: 提示信息
		 * */
		unsetuser();
		header('location: http://www.bigsm.com/index.php?act=user&m=public&w=login');
		break;
}
?>