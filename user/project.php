<?php
if(!defined('WEBROOT'))exit;
require_once(WEBROOTINCCLASS.'Project.php');
$project = new Project() ;
require_once(WEBROOTINCCLASS.'ProjectOrder.php');
$projectOrder = new ProjectOrder($project) ;
require_once(WEBROOTINCCLASS.'ProjectConsumptions.php');
$projectConsumptions = new ProjectConsumptions($project,$projectOrder) ;
$user = new User();
require_once(WEBROOTINCCLASS.'CacheManager.php');
$userinfo=$user->getUserInfo($uid);
$cid=$userinfo['cid'];

$re=$user->getCompanyStatus($uid);
if($re['I_status']!=30){returnjson(array('err'=>-5,'msg'=>'请先完成公司认证'));}

$w=$FL->requeststr('w',1,0,'w',1,1);
$m.='_'.$w;

switch ($w) {
	//展示项目申请
	case 'list':
		/**
		 * @author zy
		 * 展示项目申请
		 * http://www.bigsm.com/index.php?act=user&m=project&w=list
		 * 添加招标需求页面
		 * 输入:
		 * page:int 当前页码
		 * psize:int 每页条数
		 * I_status:str 状态 10,20进行中 30已完结 传入字符串
		 * keywords:str 关键字
		 * 输出:
		 * I_status:int 状态
		 * keywords:str 关键字
		 * pagestr:str 页码信息
		 * data:array 分页信息
		 *    page:int  当前页
		 *    count:int  统计条数
		 *    pcount:int  总页数
		 *    data:array  项目信息
		 * 			Vc_name:str 项目名
		 * 			Vc_address:str 项目地点
		 * 			Vc_admin:str 项目业主
		 * 			N_loan_amount:int 申请垫资金额，单位：万元
		 * 			Vc_contact:str 联系人
		 * */
		//获取页码
		$page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
		$psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):10;

		$status=$FL->requeststr('I_status',1,50,'状态');
		$keywords=$FL->requeststr('keywords',1,50,'关键字');

		if(!$status) $status='10,20';
		$re = $project->getProjectPage($uid, $keywords, $status, $page,$psize) ;
		$page = $re['page'];
		$pcount = $re['pcount'];
		$p['I_status']=$status;
		$p['keywords']=$keywords;
		$p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=user&m=banking&w=list&I_status=$status&keywords=$keywords");
		$p['data'] = $re;
		break;
	//新加项目
	case 'add':
		/**
		 * @author zy
		 * 新加项目
		 * http://www.bigsm.com/index.php?act=user&m=project&m=add
		 * 添加招标需求页面
		 * 输入:
		 * type:int
		 * 输出:
		 *
		 * 提交请求
		 * 输入：
		 * I_classID:int 类型ID
		 * Vc_name:str 项目名
		 * Vc_address:str 项目地点
		 * Vc_admin:str 项目业主
		 * D_start:str 项目开始日期
		 * D_end:str 结束日期
		 * I_need_loans:int 是否需要垫资：0不需要；1需要
		 * N_loan_amount:int 申请垫资金额，单位：万元
		 * I_life:int 垫资期限，单位：天
		 * N_loan_cost:str 垫资成本，单位：万元
		 * Vc_contact:str 联系人
		 * Vc_contact_phone:str 联系人电话
		 * I_project_org_classID:int 垫资企业类型,1国有企业 2私营企业 3有限责任公司 4股份有限公司 5其他
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		if(isset($_REQUEST['submit'])){
			//项目表中数据
			$da['I_companyID']=$cid;
			$da['I_classID']=$FL->requestint('I_classID',0,'类型ID',1);
			$da['Vc_name']=$FL->requeststr('Vc_name',0,50,'项目名');
			$da['Vc_address']=$FL->requeststr('Vc_address',0,255,'项目地点');
			$da['Vc_admin']=$FL->requeststr('Vc_admin',0,20,'项目业主');
			$da['D_start']=$FL->requeststr('D_start',0,50,'项目开始日期');
			$da['D_end']=$FL->requeststr('D_end',0,50,'结束日期');
			$da['I_need_loans']=$FL->requestint('I_need_loans',0,'是否需要垫资：0不需要；1需要',1);
			if($da['I_need_loans']){
				$da['N_loan_amount']=$FL->requeststr('N_loan_amount',0,50,'申请垫资金额，单位：万元');
				$da['I_life']=$FLib->requestint('I_life',0,'垫资期限，单位：天',1);
			}
			$da['N_loan_cost']=$FL->requeststr('N_loan_cost',0,50,'垫资成本，单位：万元');
			$da['Vc_contact']=$FL->requeststr('Vc_contact',0,20,'联系人');
			$da['Vc_contact_phone']=$FL->requeststr('Vc_contact_phone',0,11,'联系人电话');
			$da['I_project_org_classID']=$FL->requestint('I_project_org_classID',0,'垫资企业类型，见sm_project_org_class',1);

			$re=$project->create($da);
			returnjson($re);

		}else{
			//项目类型
			$p['type']=$project->getProjectClass();
		}
		break;
	//修改项目
	case 'mdy':
		/**
		 * @author zy
		 * 修改项目
		 * 用户权限
		 * 10  => '修改项目信',
		 * 20  => '提交项目用量申请',
		 * 30  => '查询项目概况',
		 * 40  => '查询项目用量申请记录',
		 * 50  => '询项目订单',
		 * 60  => '询项目付款记录',
		 * http://www.bigsm.com/index.php?act=user&m=project&w=mdy
		 * 添加招标需求页面
		 * 输入:
		 * id:int 项目id
		 * 输出:
		 * data:array 项目信息
		 * 		I_classID:int 类型ID
		 * 		Vc_name:str 项目名
		 * 		Vc_address:str 项目地点
		 * 		Vc_admin:str 项目业主
		 * 		D_start:str 项目开始日期
		 * 		D_end:str 结束日期
		 * 		I_need_loans:int 是否需要垫资：0不需要；1需要
		 * 		N_loan_amount:int 申请垫资金额，单位：万元
		 * 		I_life:int 垫资期限，单位：天
		 * 		N_loan_cost:str 垫资成本，单位：万元
		 * 		Vc_contact:str 联系人
		 * 		Vc_contact_phone:str 联系人电话
		 * 		I_project_org_classID:int 垫资企业类型,1国有企业 2私营企业 3有限责任公司 4股份有限公司 5其他
		 * 		I_status:int  10：申请中,能修改所有信息   20：进行中,审核通过,只能修改电话联系人
		 *  					30：已完成   50：审核不通过
		 *
		 * 提交请求
		 * 输入：
		 * submit:(隐藏域,所有提交的标志,无值)
		 * pid:int 项目id(隐藏域)
		 *
		 * 当状态I_status为10时
		 * Vc_contact:str 联系人
		 * Vc_contact_phone:str 联系人电话
		 *
		 * 当状态I_status为20时
		 * I_classID:int 类型ID
		 * Vc_name:str 项目名
		 * Vc_address:str 项目地点
		 * Vc_admin:str 项目业主
		 * D_start:str 项目开始日期
		 * D_end:str 结束日期
		 * I_need_loans:int 是否需要垫资：0不需要；1需要
		 * N_loan_amount:int 申请垫资金额，单位：万元
		 * I_life:int 垫资期限，单位：天
		 * N_loan_cost:str 垫资成本，单位：万元
		 * Vc_contact:str 联系人
		 * Vc_contact_phone:str 联系人电话
		 * I_project_org_classID:int 垫资企业类型,1国有企业 2私营企业 3有限责任公司 4股份有限公司 5其他
		 * 输出：
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		//查询用户权限,10用户修改权限
		$pid=$FLib->requestint('id',0,'项目id',1);
		$re=$project->getUserPermission($lg,$pid,10);
		if(!$re){returnjson(array('err'=>-1,'msg'=>'没有操作权限!'));}
		if(isset($_REQUEST['submit'])){
			//获取项目的申请状态
			$I_status=$project->getStatus($pid);
			//当状态为10审核中  可以全部修改
			if($I_status==10){
				$da['I_classID']=$FLib->requestint('I_classID',0,'类型ID',1);
				$da['I_companyID']=$FLib->requestint('I_companyID',0,'申请企业ID',1);
				$da['Vc_name']=$FL->requeststr('Vc_name',0,50,'项目名');
				$da['Vc_address']=$FL->requeststr('Vc_address',0,50,'项目地点');
				$da['Vc_admin']=$FL->requeststr('Vc_admin',0,50,'项目业主');
				$da['D_start']=$FL->requeststr('D_start',0,50,'项目开始日期');
				$da['D_end']=$FL->requeststr('Vc_admin',0,50,'结束日期');
				$da['I_need_loans']=$FLib->requestint('I_need_loans',0,'是否需要垫资：0不需要；1需要\'',1);
				$da['N_loan_amount']=$FL->requeststr('N_loan_amount',0,50,'申请垫资金额，单位：万元');
				$da['I_life']=$FL->requeststr('I_life',0,50,'垫资期限，单位：天');
				$da['N_loan_cost']=$FL->requeststr('N_loan_cost',0,50,'垫资期限，单位：万元');
				$da['Vc_contact']=$FL->requeststr('Vc_contact',0,50,'联系人');
				$da['Vc_contact_phone']=$FL->requeststr('Vc_contact_phone',0,50,'联系人电话');
				$da['I_project_org_classID']=$FLib->requestint('I_project_org_classID',0,'垫资企业类型，见sm_project_org_class\'',1);
			}
			//当状态为20 审核通过 可以修改电话联系人
			elseif($I_status==20){
				$da['Vc_contact']=$FL->requeststr('Vc_contact',0,50,'联系人');
				$da['Vc_contact_phone']=$FL->requeststr('Vc_contact_phone',0,50,'联系人电话');
			}else{
				returnjson(array('err'=>-2,'msg'=>'项目不可修改'));
			}
			$re=$project->mdyProject($pid,$da);
			//添加项目信息
			if(!$re){ returnjson(array('err'=>-3,'msg'=>'修改项目失败'));}
			returnjson(array('err'=>0,'msg'=>'ok'));
		}else{
			$p['data']=$project->getProjectInfo($pid);
		}
		break;
	//项目概况
	case 'detail':
		/**
		 * @author zy
		 * 项目概况
		 * http://www.bigsm.com/index.php?act=user&m=project&w=detail
		 * 添加招标需求页面
		 * 输入:
		 * id:int 项目id
		 * 输出:
		 * data:array 项目信息
		 * 		I_classID:int 类型ID
		 * 		Vc_name:str 项目名
		 * 		Vc_address:str 项目地点
		 * 		Vc_admin:str 项目业主
		 * 		D_start:str 项目开始日期
		 * 		D_end:str 结束日期
		 * 		I_need_loans:int 是否需要垫资：0不需要；1需要
		 * 		N_loan_amount:int 申请垫资金额，单位：万元
		 * 		I_life:int 垫资期限，单位：天
		 * 		N_loan_cost:str 垫资成本，单位：万元
		 * 		Vc_contact:str 联系人
		 * 		Vc_contact_phone:str 联系人电话
		 * 		I_project_org_classID:int 垫资企业类型,1国有企业 2私营企业 3有限责任公司 4股份有限公司 5其他
		 * 		I_status:int  10：申请中,能修改所有信息   20：进行中,审核通过,只能修改电话联系人
		 *  					30：已完成   50：审核不通过
		 *
		 * */
		//查询用户权限,30时用户有查询项目概况权限
		$pid=$FLib->requestint('id',0,'项目id',1);
		$re=$project->getUserPermission($lg,$pid,30);
		if(!$re){returnjson(array('err'=>-1,'msg'=>'没有操作权限!'));}
		//申请概况
		$re=$project->getProjectInfo($pid);
		$p['data']=$re;
		break;
	//项目用量申请
	case 'addConsumptionsApply':
		/**
		 * @author zy
		 * 项目用量申请
		 * http://www.bigsm.com/index.php?act=user&m=project&w=addConsumptionsApply
		 * 添加招标需求页面
		 * 输入:
		 * id:int 项目id
		 * 输出：
		 * id:int 项目id(放在隐藏域)
		 * Vc_name:str 项目名
		 *
		 * 提交请求
		 * 输入：
		 * submit:(隐藏域,所有提交的标志,无值)
		 * id:int 项目id
		 * Vc_info:str 用量说明
		 * 输出:
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
 		 * */
		//查询项目申请的状态,30完成时才能提交用量申请
		$pid=$FLib->requestint('id',0,'项目id',1);
		$I_status=$project->getStatus($pid);
		if($I_status!=20){returnjson(array('err'=>-1,'msg'=>'项目用量不可申请'));}
		//查询用户的权限,20时才能申请
		$re=$project->getUserPermission($lg,$pid,20);
		if(!$re){returnjson(array('err'=>-1,'msg'=>'没有操作权限!'));}
		if(isset($_REQUEST['submit'])){
			$da['I_userID']=$uid;
			$da['I_projectID']=$FLib->requestint('id',0,'项目id',1);
			$da['Vc_info']=$FL->requeststr('Vc_info',0,50,'用量说明');
			//添加项目用量申请
			$re=$projectConsumptions->create($da,$project,$projectOrder);
			returnjson($re);
		}else{
			$re=$project->getProjectInfo($pid);
			$p['id']=$re['id'];
			$p['Vc_name']=$re['Vc_name'];
		}
		break;
	//展示项目用量申请记录,先判断用户有没有查看权限
	case 'listConsumptionsApply':
		/**
		 * @author zy
		 * 展示项目用量申请记录,先判断用户有没有查看权限
		 * http://www.bigsm.com/index.php?act=user&m=project&w=listConsumptionsApply
		 * 输入:
		 * id:int 项目id
		 * ajax:int 是否ajax请求(在前台有ajax请求需传入参数,在用户中心为模板输出可省略)
		 * 输出：
		 * data:array 用量申请记录
		 * projectname:str 项目名称
		 * Vc_info:str 用量说明
		 * Createtime:str 提交时间
		 * username:str 操作用户
		 *
		 * 当ajax输出时
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * data:array 用量信息
		 * 		data:array 用量申请记录
		 * 		projectname:str 项目名称
		 *   	Vc_info:str 用量说明
		 * 		Createtime:str 提交时间
		 * 		username:str 操作用户
		 * */
		//查询用户权限,40时用户有查询项目用量申请记录权限
		$pid=$FLib->requestint('id',0,'项目id',1);
		if(!$pid){returnjson(array('err'=>-1,'msg'=>'参数错误!'));}
		$re=$project->getUserPermission($lg,$pid,40);
		if(!$re){returnjson(array('err'=>-2,'msg'=>'没有操作权限!'));}
		$ajax=$FLib->requestint('ajax',0,'是否是ajax请求',1);

		//展示项目用量申请记录,获取所有项目用量申请记录
		$re=$project->getConsumptionsApply($pid);
		if(isset($ajax)&&$ajax==1){
			returnjson(array('err'=>0,'msg'=>'ok','data'=>$re));
		}else{
			$p['data']=$project->getConsumptionsApply($pid);
		}
		break;
	//项目订单TODO
	case 'orderlist':
		$uid = 1 ; $pid = 4 ;
		$result = $project->getProjectOrders($uid, $pid) ;
	//展示所有子用户
	case 'listusers':
		/**
		 * @author zy
		 * 展示所有子用户
		 * http://www.bigsm.com/index.php?act=user&m=project&w=listusers
		 * 输入:
		 * starttime:str 开始时间
		 * endtime:str 结束时间
		 * keywords:str 关键字
		 * 输出：
		 * starttime:str 开始时间
		 * endtime:str 结束时间
		 * keywords:str 关键字
		 * pagestr:str 页码信息
		 * data:str 用户信息
		 * 		ID:int 用户id
		 * 		Vc_name:str 用户名
		 * 		Vc_truename:str 真实姓名
		 * 		Vc_mobile:str 电话号
		 * 		Vc_Email:str 邮箱
		 * 		Createtime:str 创建时间
		 * */
		//子账号不可执行
		if ($lg['I_is_children'] == 1) {returnjson(array('err'=>-1,'msg'=>'子帐号不能执行此操作!')) ;}
		//获取页码
		$page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
		$psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):20;

		$da['starttime']=$FLib->requeststr('starttime',1,50,'开始时间',1);
		$da['endtime']=$FLib->requeststr('endtime',1,50,'结束时间',1);
		$da['keywords']=$FL->requeststr('keywords',1,50,'关键字');

		$re = $project->getusers($lg['I_companyID'],$da,$page,$psize) ;
		$page = $re['page'];
		$pcount = $re['pcount'];
		$p['starttime']=$da['starttime'];
		$p['endtime']=$da['endtime'];
		$p['keywords']=$da['keywords'];
		$p['pagestr'] = getPageStrFunSd($pcount, $page, "?act=user&m=order&w=list&starttime={$da['starttime']}&endtime={$da['endtime']}&keywords={$da['keywords']}");
		$p['data'] = $re;

		break;
	//添加子账号
	case 'adduser':
		/**
		 * @author zy
		 * 添加子账号
		 * 手机号重复检验 http://www.bigsm.com/index.php?act=user&m=userprocess&w=checkmobile
		 * http://www.bigsm.com/index.php?act=user&m=project&w=adduser
		 * 提交页面:
		 * 输入:
		 * Vc_name:str 用户名
		 * Vc_password:str 密码
		 * repassword:str 密码重复
		 * Vc_truename:str 真实姓名
		 * Vc_mobile:str 手机号
		 * Vc_Email:str 邮箱
		 * */
		//子账号不可注册
		if ($lg['I_is_children'] == 1) {returnjson(array('err'=>-1,'msg'=>'子帐号不能执行此操作!')) ;}
		if(isset($_REQUEST['submit'])){
			$da['Vc_name']=$FLib->requeststr('Vc_name',0,50,'用户名',1) ;
			$da['Vc_password']=$FLib->requeststr('Vc_password',0,50,'密码',1);
			$repassword=$FLib->requeststr('repassword',0,50,'密码重复',1);
			if($repassword!=$da['Vc_password']){returnjson(array('err'=>-1,'msg'=>'两次输入密码不一致'));}
			$da['Vc_truename']=$FLib->requeststr('Vc_truename',0,50,'真实姓名',1) ;
			$da['Vc_mobile']=$FLib->requeststr('Vc_mobile',0,50,'手机号',1);
			$da['Vc_Email']=$FLib->requeststr('Vc_Email',0,50,'邮箱',1);
			//获取公司id
			$da['I_companyID']=$lg['I_companyID'];
			$da['I_is_children']=1;
			$user=new User();
			//0代表子账号注册
			$r=$user->add($da,0);
			if(!$r){ returnjson(array('err'=>-1,'msg'=>'user add false')); }
			returnjson(array('err'=>0, 'msg'=>'ok'));
		}else{
			//子账号输入页面
		}
		break;
	//设置权限
	case 'setpms':
		/**
		 * @author zy
		 * 设置权限
		 * http://www.bigsm.com/index.php?act=user&m=project&w=setpms
		 * 设置展示页面
		 * 输入:
		 * ID:int 子用户ID
		 * 输出：
		 * projects:array 项目id,项目名
		 * 		id:int 项目id
		 * 		Vc_name:str 项目名
		 * userrole:array 用户权限
		 *   	项目id=>array(用户权限)
		 *
		 * 设置权限页面
		 * 输入:
		 * I_userID:int 子用户ID
		 * pamarr:array 权限(二维数组)
		 * 输出:
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		//展示用户已有的权限,首先获取所有项目,同时返回用户已有权限,提交权限设置,删除已有权限,再添加
		//子账号不可分配权限
		if ($lg['I_is_children'] == 1) {returnjson(array('err'=>-1,'msg'=>'子帐号不能执行此操作!')) ;}
		//获取子用户id
		$cid=$FL->requestint('ID',0,'子用户ID',1);
		if(isset($_REQUEST['submit'])){
			//获取权限二维数组
			$pmarr =$_POST['pamarr'];
			if(count($pmarr)>0){
				$result = $project->setProjectPermission($cid, $pmarr) ;
				returnjson($result) ;
			}else{
				returnjson(array('err'=>-2,'msg'=>'权限为空'));
			}
		}else{
			//获取项目id,项目名
			$p['projects']=$project->getProNameId($lg['I_companyID']);
			//获取子用户已有的权限
			$p['userrole']=$project->getProjectRoles($cid);
		}
		break;
	//重置子用户密码
	case 'resetpass':
		/**
		 * @author zy
		 * 重置子用户密码
		 * http://www.bigsm.com/index.php?act=user&m=project&w=resetpass
		 * 提交页面:
		 * 输入:
		 * ID:str 子用户ID
		 * Vc_password:str 密码
		 * 输出:
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		//子账号不可分配权限
		if ($lg['I_is_children'] == 1) {returnjson(array('err'=>-1,'msg'=>'子帐号不能执行此操作!')) ;}
		$cid=$FL->requestint('ID',0,'子用户ID',1);
		$pass=$FLib->requeststr('Vc_password',0,50,'密码',1);
		$pass=$user->pwd($pass);
		$re=$project->setcolume(array('Vc_password'=>$pass)," ID=$cid and Status=1 ");
		if(!$re){ returnjson(array('err'=>-1,'msg'=>'重置密码失败')); }
		returnjson(array('err'=>0, 'msg'=>'ok'));
		break;
	//启用禁用子用户
	case 'onoff':
		/**
		 * @author zy
		 * 启用禁用子用户
		 * http://www.bigsm.com/index.php?act=user&m=project&w=onoff
		 * 提交页面:
		 * 输入:
		 * ID:str 子用户ID
		 * I_children_status:int 1正常使用的子帐号；2被禁用的子帐号'
		 * 输出:
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		//子账号不可分配权限
		if ($lg['I_is_children'] == 1) {returnjson(array('err'=>-1,'msg'=>'子帐号不能执行此操作!')) ;}
		$cid=$FL->requestint('ID',0,'子用户ID',1);
		$status=$FL->requestint('I_children_status',0,'子用户状态',1);
		$status=3-(int)$status;
		$re=$project->setcolume(array('I_children_status'=>$status)," ID=$cid and Status=1 ");
		if(!$re){ returnjson(array('err'=>-1,'msg'=>'切换失败')); }
		returnjson(array('err'=>0, 'msg'=>'ok'));
		break;
	//删除子用户
	case 'deluser':
		/**
		 * @author zy
		 * 删除子用户
		 * http://www.bigsm.com/index.php?act=user&m=project&w=deluser
		 * 提交页面:
		 * 输入:
		 * ID:str 子用户ID
		 * 输出:
		 * err:int 结果状态 -1失败 0成功
		 * msg: 提示信息
		 * */
		//子账号不可分配权限
		if ($lg['I_is_children'] == 1) {returnjson(array('err'=>-1,'msg'=>'子帐号不能执行此操作!')) ;}
		$cid=$FL->requestint('ID',0,'子用户ID',1);
		$re=$project->setcolume(array('Status'=>0)," ID=$cid and Status=1" );
		if(!$re){ returnjson(array('err'=>-1,'msg'=>'删除失败')); }
		returnjson(array('err'=>0, 'msg'=>'ok'));
		break;
	default:
		echo 123 ;
}
?>