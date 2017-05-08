<?php
namespace app\home\controller ;

use app\common\model\UserModel;

use think\Validate;
use app\common\model\BillModel;
use app\common\model\ProjectModel;
use app\common\model\OrderModel;

class Workroom extends WorkbenchController {
	
	private $userModel,$billModel,$projModel,$orderModel;
	public function __construct() {

		parent::_initialize() ;
		$this->userModel = new UserModel;
		$this->billModel = new BillModel();
		$this->projectModel = new ProjectModel();
		$this->orderModel = new OrderModel();
		parent::__construct();
		
	}
	
	
	public function index () {
// 	    dump($_SESSION['user']);die;
	    $uid = $this->getSessionUid();
	    //个人信息
	    $user = $this->userModel->getCompanyById($uid);
	    
	    $projlist=$orderlist=[];
	    $overdate=$waitCount=0;
	    $iscertified = false;
	    $usableprojs =0; //可用项目数
	    
	    
//         $usableprojs = count($this->projectModel->getMQuery(['a.I_userID'=>$uid,'a.I_status'=>['exp','in (2,3)'],'ea.N_usable_loan'=>['exp','>0']])->select());
// 	    $projlist = $this->projectModel->getMQuery(['a.I_status'=>['in',[2,3]],'a.I_userID'=>$uid])
// 	    ->limit(2)
// 	    ->select();
        //根据公司来判断可用项目数及可用项目
	    $companycode=db('sm_user_company')->where(['I_userID'=>$uid])->value('Vc_companycode');
	    $where['h.Vc_companycode'] =$companycode;
	    
        $usableprojs = count($this->projectModel->getMQuery(['h.Vc_companycode'=>$companycode,'a.I_status'=>['exp','in (2,3)'],'ea.N_usable_loan'=>['exp','>0']])->select());
	    $projlist = $this->projectModel->getMQuery(['a.I_status'=>['in',[2,3]],'h.Vc_companycode'=>$companycode])
	    ->limit(2)
	    ->select();
	    //加判断是否认证
	    if($user['I_status']==3){
                	        $iscertified = true;
	     } 	   
	     
        //账单已逾期数量
        $overdate =count($this->billModel->getBillQuery(['ezd.Vc_billstatus'=>'已逾期','a.I_userID'=>$uid])->select());
	    //订单已到货数量
	    $goods_erp =$this->orderModel->getWQuery(['pol.Vc_orderstatus'=>'已到货','a.I_userID'=>$uid],'pol.* ')->select();
	    $finishCount = count($goods_erp);
	    $waitCount =  $finishCount;

	    //订单信息获取最新一条该用户的订单
	    $GoodsJudgePrice = get_judgeprice();
	    $list = $this->orderModel->getWQuery(['a.I_status'=>['in',[0,1,2,3]],'a.I_userID'=>$uid],'a.*')->limit(1)
        ->select();
	    $listdata = [];
                	    
	    foreach ($list as $n=>$vo ){
	    
	    
	        $listdata[$n] = $vo;
	        $listdata[$n]['projinfo'] = $this->projectModel->getMQuery(['a.id'=>$vo['I_projectID']])->find();
	        $goods=[];
	        if(!$vo['Vc_orderSn']){
	        //先拿到平台创建订单货物
	        $goods = $this->orderModel->getGoodsQuery(['a.I_orderID'=>$vo['id'],'a.I_goods_src'=>1])->select();
	        }
	    
	        //erp订单货物列表
	        if($vo['Vc_orderSn']){
	            $goods_erp=array();
	            $goods_erp =$this->orderModel->getGQuery(['pol.Vc_orderSn'=>$vo['Vc_orderSn']],'pol.*,IFNULL(g.state,0) I_isconfirm')->select();
	            foreach ($goods_erp as &$v){
	                $v['I_goods_src'] = 2;
	                $v['N_judge_price'] = $GoodsJudgePrice;
// 	                $v['N_judge_totalprice'] = $v['N_ac_price']*$v['N_plan_weight'];
	                if($v['Vc_orderstatus']=='已到货'){
	                
	                    $v['N_judge_totalprice'] = $v['N_ac_price']*$v['N_plan_weight'];
	                }else{
	                
	                    $v['N_judge_totalprice'] = $v['N_judge_price']*$v['N_plan_weight'];
	                }
	            }

	            
	            $goods = array_merge($goods,$goods_erp);
	    
	    
	        }
	    
	        $listdata[$n]['orderlist'] = $goods;
	       
	    }
	    $orderlist = $listdata;

	    $this->assign([
	        'user'=>$user,
	        'projlist'=>$projlist,
	        'orderlist'=>$orderlist,
	        'overdate'=>$overdate,
	        'model'=>$this->orderModel,
	        'waitCount'=>$waitCount,
	        'iscertified'=>$iscertified,
	        'usableprojs'=>$usableprojs,
	    ]) ;
		return $this->fetch() ;
	}
	
	
	
	
	
	
	
	
}