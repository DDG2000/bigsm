<?php
/**
 * 用户信息管理
 */
if(!defined('WEBROOT'))exit;
if(empty($uid)){returnjson(array('err'=>-1,'msg'=>'获取用户参数失败'));}
//require_once(WEBROOT.'pay'.L.'moneymm'.L.'api'.L.'api.class.php');
//$Api = getApiClass();
$sitename = $g_conf['cfg_web_name'];
$user = new User();
require_once( WEBROOTINCCLASS.'Company.php');
require_once( WEBROOTINC.'UploadFile.php');
$c=new Company();
$w=$FL->requeststr('w',1,0,'w',1,1);
$m.='_'.$w;
switch($w) {
	//>>>>>>>>>>>>>>>>>>>>>>>>账户管理>账户信息<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
	//账户信息展示
	case 'info':
		/**
		 * @author zy
		 * 账户管理>账户信息接口
		 * 展示用户信息,ID,姓名手机号,邮箱,个人地址,公司信息,公司id,公司名称,公司地址,公司性质
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&w=info
		 * 输入：需登录后访问
		 *
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * 以下仅在err为0时会返回
		 * ID:用户id
		 * Vc_truename:用户真实姓名
		 * Vc_mobile:手机号
		 * Vc_Email:邮箱
		 * Vc_address_user:用户地址
		 * id:公司地址
		 * Vc_name:公司名
		 * Vc_address_company:公司地址
		 * propname:公司性质
		 * */

		$da=$user->getUserInfo($uid);
		$p['user'] =$lg;
		$p['data']=$da;
		break;
	//修改用户头像
	case 'mdyphoto':
		/**
		 * @author zy
		 * 账户管理>账户信息>修改姓名接口
		 * 获取用户id,姓名,然后修改
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&m=mdyphoto
		 * 输入：需登录后访问
		 *Vc_photo:file 用户头像
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		$upload=new UploadFile();
		$re=$upload->Upload($_FILES['Vc_photo'],'face');
		$da['Vc_photo']=$re['picpath'];
		$re=$user->mdy($da, $uid);
		if(!$re){ returnjson(array('err'=>-1,'msg'=>'修改失败'));}
		returnjson(array('err'=>0,'msg'=>'ok'));
		break;
	//修改真实姓名
	case 'mdytruename':
		/**
		 * @author zy
		 * 账户管理>账户信息>修改姓名接口
		 * 获取用户id,姓名,然后修改
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&m=mdytruename
		 * 输入：需登录后访问
		 *Vc_truename:用户的真实姓名
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		$da['Vc_truename']=$FLib->requeststr('Vc_truename',0,50,'姓名',1);
		//保护名检验
//		if(!protectnamecheck($uname)){ returnjson(array('err'=>-1,'msg'=>'该姓名不可使用'));}
		$re=$user->mdy($da, $uid);
		if(!$re){ returnjson(array('err'=>-1,'msg'=>'修改失败'));}
		$lg['Vc_truename']=$da['Vc_truename'];
		setuser($lg);
		returnjson(array('err'=>0,'msg'=>'ok'));
		break;
	//修改用户地址
	case 'mdyuseradd':
		/**
		 * @author zy
		 * 账户管理>账户信息>修改个人地址接口
		 * 获取地址信息修改
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&w=mdyuseradd
		 * 输入：需登录后访问
		 * I_provinceID:省份ID
		 * I_cityID:城市ID
		 * I_districtID:区县ID
		 * VC_address:详细地址
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		$da['I_provinceID']=$FLib->requestint('I_provinceID',0,50,'省份ID',1) ;
		$da['I_cityID']=$FLib->requestint('I_cityID',0,50,'城市ID',1) ;
		$da['I_districtID']=$FLib->requestint('I_districtID',0,50,'区县ID',1) ;
		$da['VC_address']=$FLib->requeststr('VC_address',0,50,'详细地址',1) ;
		$re=$user->mdy($da, $uid);
		if(!$re){ returnjson(array('err'=>-1,'msg'=>'修改失败'));}
		//重新设置登录信息
		$lg['I_provinceID']=$da['I_provinceID'];
		$lg['I_cityID']=$da['I_cityID'];
		$lg['I_districtID']=$da['I_districtID'];
		$lg['VC_address']=$da['VC_address'];
		setuser($lg);
		returnjson(array('err'=>0,'msg'=>'ok'));
		break;
	//修改公司名
	case 'mdycompanyname':
		/**
		 * @author zy
		 * 账户管理>账户信息>修改公司名接口
		 * 获取公司id,新公司名修改
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&w=mdycompanyname
		 * 输入：需登录后访问
		 * Vc_name:公司名
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		$cid=$c->getCid($uid);
		$da['Vc_name']=$FLib->requeststr('Vc_name',0,50,'姓名',1);
		$re=$c->mdy($da, $cid);
		if(!$re){ returnjson(array('err'=>-1,'msg'=>'修改失败')); }
		returnjson(array('err'=>0,'msg'=>'ok'));
		break;
	//修改公司地址
	case 'mdycompanyadd':
		/**
		 * @author zy
		 * 账户管理>账户信息>修改公司地址接口
		 * 获取地址信息修改
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&w=mdycompanyadd
		 * 输入：需登录后访问
		 * I_provinceID:省份ID
		 * I_cityID:城市ID
		 * I_districtID:区县ID
		 * VC_address:详细地址
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		$cid=$c->getCid($uid);
		$da['I_provinceID']=$FLib->requestint('I_provinceID',0,11,'省份ID',1) ;
		$da['I_cityID']=$FLib->requestint('I_cityID',0,11,'城市ID',1) ;
		$da['I_districtID']=$FLib->requestint('I_districtID',0,11,'区县ID',1) ;
		$da['VC_address']=$FLib->requeststr('VC_address',0,50,'详细地址',1) ;
		$re=$c->mdy($da, $cid);
		if(!$re){ returnjson(array('err'=>-1,'msg'=>'修改失败'));}
		returnjson(array('err'=>0,'msg'=>'ok'));
		break;
	//>>>>>>>>>>>>>>>>>>>>>>>>账户管理>账户安全<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
	//账户安全展示页面
	case 'safe':
		/**
		 * @author zy
		 * 账户管理>账户安全接口
		 * 获取手机号,邮箱
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&w=safe
		 * 输入:需要登录访问
		 * 输出:
		 * Vc_mobile:手机号
		 * Vc_Email:邮箱
		 * I_mobileauthenticate:手机是否被验证
		 * I_Emailauthenticate:邮箱是否被验证
		 * safe:安全级别
		 * */
		$userinfo=$user->getInfo($uid);
		$da['safe']=0;
		$da['Vc_mobile']=isset($userinfo['Vc_mobile'])?hideStar($userinfo['Vc_mobile']):'';
		$da['Vc_Email']=isset($userinfo['Vc_Email'])?hideStar($userinfo['Vc_Email']):'';
		$da['Vc_mobile']=hideStar($userinfo['Vc_mobile']);
		$da['Vc_Email']=hideStar($userinfo['Vc_Email']);
		$da['I_mobileauthenticate']=$userinfo['I_mobileauthenticate'];
		if(intval($da['I_mobileauthenticate'])==2){
			$da['safe']++;
		}
		$da['I_Emailauthenticate']=$userinfo['I_Emailauthenticate'];
		if(intval($da['I_Emailauthenticate'])==2){
			$da['safe']++;
		}
		$p['err']=0;
		$p['msg']='ok';
		$p['data']=$da;
		break;
	//修改密码
	case 'mdypass':
		/**
		 * @author zy
		 * 账户管理>账户安全>修改密码接口
		 * 获取新旧密码验证
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&w=mdypass
		 * 输入：需登录后访问
		 * Vc_mobile:新手机号
		 *activeCode:手机验证码
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		$oldpass=$FLib->requeststr('oldpass',0,50,'旧密码',1);
		$newpass=$FLib->requeststr('newpass',0,50,'新密码',1);

		//获取保存的用户密码,判断
		$pass=$user->getInfo($uid, 'Vc_password', false);
		if($pass!=$user->pwd($oldpass)){returnjson(array('err'=>-401,'msg'=>'原密码不正确'));}
		//修改密码
		$re=$user->mdy(array('Vc_password'=>$newpass), $uid);
		if(!$re){ returnjson(array('err'=>-1,'msg'=>'修改失败'));}

		returnjson(array('err'=>0,'msg'=>'ok'));
		break;
	//>>>>>>>>>>>>修改手机号<<<<<<<<<<<
	//验证原有手机号
	case 'mdymobile_step1':
		/**
		 * @author zy
		 * 修改手机号,先验证原有手机号接口
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&w=mdymobile_step1
		 * 输入：需登录后访问
		 * oldmobile:str 原有手机号
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		$Vc_mobile = $lg['Vc_mobile'];
		$oldmobile= $FL->requeststr('oldmobile',0,50,'原手机号');
		$re=$FL->checkMobile($oldmobile);
		if(!$re){returnjson(array('err'=>-1, 'msg'=>'请输入正确的手机号'));}
		if($Vc_mobile!=$oldmobile){returnjson(array('err'=>-2, 'msg'=>'原有手机号不正确'));}
		//原手机已验证标志
		$_SESSION['pass_mobile']=1;
		returnjson(array('err'=>0, 'msg'=>'ok'));
		break;
	//修改手机号,并修改
	case 'mdymobile_step2':
		/**
		 * @author zy
		 * 修改手机号接口,验证修改后的手机号接口
		 * 获取手机验证码验证
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&w=mdymobile_step2
		 * 输入：需登录后访问
		 * Vc_mobile:str 新手机号
		 * activeCode:str 手机验证码
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		$Vc_mobile=$FLib->requeststr('Vc_mobile',0,50,'新手机号',1);
		$activeCode = $FL->requeststr('activeCode',0,50,'验证码');
		$mobilecode = iset($_SESSION['mobilecode'],array('',0));
		if(strtoupper($activeCode)!=$mobilecode[0]){ returnjson(array('err'=>-401,'msg'=>'短信验证码有误')); }
		if(time() > $mobilecode[1]){ returnjson(array('err'=>-402,'msg'=>'短信验证码已无效，请重新发送')); }
		unset($_SESSION['mobilecode']);
		unset($_SESSION['mobilecode_time']);
		//验证手机号唯一
		$re = $user->checkuser(array('Vc_mobile'=>$Vc_mobile));
		if($re){returnjson(array('err'=>-1,'msg'=>'手机号已存在'));}
		if ($lg['I_mobileauthenticate'] > 0) {
			if (!isset($_SESSION['pass_mobile']) || $_SESSION['pass_mobile']!=1) {
				returnjson(array('err'=>403,'msg'=>'请先验证原手机号'));
			}
		}
		$da=array();
		$da['Vc_mobile'] = $Vc_mobile;
		$da['I_mobileauthenticate'] = 2;
		$re=$user->mdy($da, $uid);
		if(!$re){ returnjson(array('err'=>-1,'msg'=>'修改失败'));}
		$lg['Vc_mobile']=$da['Vc_mobile'];
		setuser($lg);
		returnjson(array('err'=>0,'msg'=>'ok'));
		break;
	//>>>>>>>>>>>>修改邮箱<<<<<<<<<<<
	//验证邮箱是否存在ajax
	case 'emailexists':
		/**
		 * @author zy
		 * ajax
		 * 账户管理>账户信息>修改邮箱,验证邮箱是否被注册
		 * 获取邮箱号,验证邮箱是否被注册
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&w=emailexists
		 * 输入：需登录后访问
		 * Vc_email:新邮箱
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		$email = $FLib->requeststr('Vc_email',0,50,'邮箱',1);
		//检测邮箱是否已经注册
		$re = $user->checkEmailOne($email);
		if ($re) {returnjson(array('err'=>-1, 'msg'=>'该邮箱地址已被使用，请使用其他邮箱地址认证。'));
		} else {returnjson(array('err'=>0, 'msg'=>'ok'));}
		break;
	//修改邮箱,先发送邮件,token
	case 'authemail_step1':
		/**
		 * @author zy
		 * ajax
		 * 账户管理>账户信息>修改邮箱,先发送验证码
		 * 获取邮箱号,验证邮箱是否被注册
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&w=authemail_step1
		 * 输入：需登录后访问
		 * Vc_email:str 邮箱
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		$email = $FLib->requeststr('Vc_email',0,50,'邮箱',1);
		//设置检验码
		$re = $user->addCertified($email,$uid);
		if(!$re){ returnjson(array('err'=>-101,'msg'=>'1分钟后再发送吧！')); }
		$certification = $re;
		$ctime=date('Y-m-d H:i:s');
		$url = $Cfg->WebRoot.'/index.php?act=user&m=userprocess&w=authemail_step2&token='.$certification;

		//-发送邮件
		$name = $lg['Vc_nickname'];
		$host = $webroot = $Config->WebRoot;
		$subject = $sitename.'邮箱验证f';
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
		returnjson(array('err'=>0));
		break;
	//认证邮箱第二步,检验token并跳转
	case 'authemail_step2':
		/**
		 * @author zy
		 * 认证邮箱第二步,检验token并跳转
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&w=authemail_step2
		 * 输入：需登录后访问
		 * token:str token 安全码
		 * 输出：
		 * Location: /index.php?act=user&m=wizard
		 * */
		$token=$FLib->requeststr('token',0,50,'',1,0);

		$r = $user->checkCertified(trim($token));
		if($r['err']<0){ errorPage($r['msg'],'确定','/');}
		if($r['err']==0){
			$uid = $r['I_userID'];
			$r=$user->setEmailValid($uid);
			setuser($user->usero($user->getInfo($uid)));
		}
		header('Location: /index.php?act=user&m=wizard');
		break;

	//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>账户管理>企业认证<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
	//企业认证
	case 'authcompany':
		/**
		 * @author zy
		 * 账户管理>企业认证页面接口
		 * 展示用户信息,ID,姓名手机号,公司信息,公司id,公司名称,公司地址
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&w=authcompany
		 * 页面信息
		 * 输入：需登录后访问
		 *
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * 以下仅在err为0时会返回
		 *ID:用户id
		 * Vc_truename:用户真实姓名
		 * Vc_mobile:手机号
		 * id:公司地址
		 * Vc_name:公司名
		 * Vc_address_company:公司地址
		 *
		 * 提交页面
		 * 输入：需登录后访问
		 * Vc_lawname:法人姓名
		 * Vc_cityrenID:身份证号
		 * Vc_imageID:身份证照片
		 * Vc_imageLicense:营业执照照片
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		if(isset($_REQUEST['submit'])){
			$upload=new UploadFile();
			$re=$upload->Upload($_FILES['Vc_imageID'],'IDimage');
			$co['Vc_imageID']=$re['picpath'];
			$re=$upload->Upload($_FILES['Vc_imageLicense'],'Licenseimage');
			$co['Vc_imageLicense']=$re['picpath'];
			$cid=$FLib->requestint('id',0,11,'公司id',1) ;
			$companyname=$FLib->requeststr('Vc_name',0,50,'公司名',1);
			$co['Vc_lawname']=$FLib->requeststr('Vc_lawname',0,50,'法人姓名',1);
			$co['Vc_cityrenID']=$FLib->requeststr('Vc_cityrenID',0,50,'法人身份证',1);
			//添加认证信息
			$re=$c->create($co,$cid,$companyname);
			if(!$re){ returnjson(array('err'=>-1,'msg'=>'提交认证失败'));}
//			header('location:/index.php?act=user&m=public&w=login');
			echo "<script>alert('提交认证成功,请刷新页面')</script>";
		}else{
			$da=$user->getUserInfo($uid);
			$p['I_status']=$da['I_status'];
			$p['err']=0;
			$p['msg']='ok';
			$p['user']=$lg;
			$p['data']=$da;
		}
	break;
	
	case 'test1':
		/**
		 * @author zy
		 * 账户管理>企业认证接口
		 * 保存公司认证信息
		 * url地址：
		 * http://www.bigsm.com/index.php?act=user&m=account&w=test1
		 * 输入：需登录后访问
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		//获取获取上传图片
		$upload=new UploadFile();
		dump($_FILES);
		$re=$upload->Upload($_FILES['Vc_imageID'],'IDimage');
		$co['Vc_imageID']=$re['picname'];
		dump($re);
		break;
}
?>