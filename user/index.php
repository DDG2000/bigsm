<?php
if(!defined('WEBROOT'))exit;
if(!isset($m))exit;

/*
fund:资金管理
invest:投资管理
loan:借贷管理
account:账户管理
message:消息管理
*/

if($m==''){$m='index';}
//目录访问限制
if(!in_array($m,array('index','public','userprocess', 'fund','invest','loan','account','banking','require',
		'message','stat', 'wizard','address','shopcar','order','concentrated','requirecommodity','project','concentrateindex')))exit;

//需要登录的页面
if(!$lg){if(!in_array($m,array('public','userprocess','concentrateindex'))){if($uid==0){loginouttime();}}}

//企业验证
$user = new User();
$re=$user->isAuthCompany($uid);
if($uid){
	if(!$re && !in_array($m,array('account'))){
		//企业没有验证有又是子账号,返回字符串
		if($lg['I_is_children']==1){returnjson(array('err'=>'-1','msg'=>'请用主账号完成企业验证'));exit;}
		header("location:http:/index.php?act=user&m=account&w=authcompany");
	}
	//子账号验证 子账号只能看到项目信息
	if($lg['I_is_children']==1 && !in_array($m,array('project')) ){
		header("location:/index.php?act=user&m=project&w=list");
	}
}

if($m!='index'){
	if(file_exists("{$act}/{$m}.php")){
		require("{$act}/{$m}.php");
	}
	//首页内容
}else{
	/**
	 * @author zy
	 * 交易管理>我的订单接口
	 * 展示我的订单
	 * url地址：
	 * http://www.bigsm.com/index.php?act=user&m=index
	 * 输入：需登录后访问
	 * 输出:err:int 结果状态 -1失败 0成功
	 * u_status:array 状态
	 * safelv:int 安全等级0 差, 1 中, 2 优
	 * Vc_truename:str 真实姓名
	 * Vc_photo:str 头像
	 * status:array 状态数量 '10'=>'等待审核','20'=>'已完成','70'=>'未评价'
	 * 10：等待审核；20：已完成；30：待发货 40：待提货; 50：商家取消；60：用户取消
	 * da:array 商品信息
	 *   page:int 当前页
	 *   count:str 总条数
	 *   pcount:float 页数
	 *   data:array() 订单
	 *     0=>id:int 订单号(隐藏域)
	 *       Vc_orderNO:str 订单编号
	 *       shopname:str 卖家公司
	 *       Vc_phone:str 公司电话
	 *       Createtime:str 创建时间
	 *       Vc_consignee:str 收件人
	 *       N_amount_price:str 订单总价
	 *       I_status:str 订单状态
	 *       goods=>array()商品
	 *          0=>I_commodityID:str 商品ID
	 *              itemname:str 商品名
	 *              stuffname:str 材质
	 *              specificationname:str 规格名
	 *              factoryname:str 钢厂名
	 *              warehouse:str 仓库名
	 *              N_amount:str 数量
	 *              N_weight:str 重量
	 *              N_price:str 单价
	 *              N_amount_price:str 总价
	 * */
	 //1、会员中心首页显示用户真实名字、头像、账户安全等级。
	$da = getUserStatus($lg);
	$safelv=0;
	if($lg['I_mobileauthenticate']==2){$safelv++;}
	if($lg['I_Emailauthenticate']==2){$safelv++;}
	$p['u_status'] = $da;
	$p['safelv'] = $safelv;
	$re=$user->getDetail($uid);
	$p['Vc_truename'] = $re['Vc_truename'];
	$p['Vc_photo'] = $re['Vc_photo'];
	// 2、显示订单在不同状态下的数量。
	require(WEBROOTINCCLASS . 'Order.php');
	$order=new Order();
	$re1=$order->getstatusnum($uid);
	if($re1){
		$re2=$order->getNoApp($uid);
		if($re2){
			$re1['70']=(string)count($re2);
		}
	}
	for($i=10;$i<=70;$i+=10){
		$re1[$i]=isset($re1[$i])?$re1[$i]:'0';
	}
	$p['status']=$re1;
	// 3、显示最近四条订单信息。
	require_once(WEBROOTINCCLASS . 'OrderCommodity.php');
	$ordercommodity=new OrderCommodity();
	$re=$order->getPages($uid,1,4,array(),1);
	if($re) {
		//获取每单商品信息
		foreach ($re['data'] as $k => $v) {
			$oid = $v['id'];
			//获取当前单号所有商品
			$r = $ordercommodity->get($oid);
			//添加字段goods在每一订单中,包含所有商品
			$v['goods'] = $r;
			$re['data'][$k] = $v;
		}
	}
	$p['da'] =$re;
}
$p['m_cur'] = isset($m_cur) ? $m_cur:$m;

?>
