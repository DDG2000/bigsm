<?php
namespace app\admin\controller ;
use app\common\model\OrderModel;
use app\common\model\ProjectModel;

class Order extends AdminController {
	private $orderModel,$projectModel;
	
	public function _initialize() {
		$this->orderModel = new OrderModel();
		$this->projectModel = new ProjectModel();
		parent::_initialize();
	}
	
    public function index($page=1) {
       
//      echo  $this->orderModel->getXLMQuery()->fetchSql(true)->select();
//        echo $this->orderModel->getLastSql();
//         die;
        
        $param['uname'] = input('uname/s','');
        $param['projStatus'] = input('projStatus/d',-2);
    	$pages = $this->orderModel->getPage($page,$param) ;
    	 $this->assign([
	       
	        'model' =>$this->orderModel,
	        'page' =>$pages,
	        'param'=>$param,
    	     
	    ]) ;
    	return $this->fetch("index") ;

    }

  
    /**
     * 查看物流信息
     */
    public function view ($id=0) {
        
        $id = $this->request->get('id',0);
        $orderSn = $this->request->get('orderSn','');
        if(!$id){
            $this->error('不合法的请求');
        }
        $data = db('erp_systd')->where(['id'=>$id,'Vc_orderSn'=>$orderSn])->find();
        if(!$data){
            $this->error('数据更新中，请刷新页面后，重新操作！');
        }
        $this->assign([
            'data'=>$data,
        ]) ;
        return $this->fetch();
        
    }
    
    
   /**
    * 审核编辑
    */
    public function  edit(){
        //更新货物表和物流表
        if($this->request->isPost()){
            $id = input('post.id/d',0);
            $I_status = input('post.approved/d',-2);
            $Vc_orderSn = input('post.Vc_orderSn/s','');
            
            if(!$id){
                return getJsonStrError('不合法的请求');
            }
            
            if($I_status==-2){
                return getJsonStrError('未选择审核状态！');
            }
            $data = array();
            $data['I_status'] = $I_status;
            $data['D_checktime'] = date("Y-m-d H:i:s");
            
            if($I_status==2&&empty($Vc_orderSn)){
                return getJsonStrError('未填写订单号！');
            }
            if($I_status==5){
             $data['D_freezetime'] = date("Y-m-d H:i:s");
            }
            if($I_status==6){
             $data['D_closetime'] = date("Y-m-d H:i:s");
            }
    
            if ($Vc_orderSn&&in_array($I_status, [1,2])){
                
                $cda = db('erp_systd')->where('Vc_orderSn',trim($Vc_orderSn))->find();
                if(!$cda){
                    return getJsonStrError('该订单号不存在，或erp暂未同步到该数据！');
                }
                //比较合同项目名称，不相等则不能提交
                 $Vc_projNo = input('post.Vc_projNo/s','');
                if($cda['Vc_projNo']!=$Vc_projNo){
                    return getJsonStrError('该订单号不属于该项目！');
                }
                //该订单是否已绑定平台其他订单
//                 $find = db('se_project_order')->where(['Vc_orderSn'=>$Vc_orderSn,'state'=>1])->find();
//                 if($find){
//                     return getJsonStrError('该订单号已绑定平台其他订单！');
//                 }
                
                $unfind = $this->orderModel->checkParamUpdate(['Vc_orderSn'=>trim($Vc_orderSn),'id'=>$id]);
                
                if(!$unfind){
                    return getJsonStrError('该订单号已绑定平台其他订单！');
                }
                
                
                $data['Vc_orderSn'] = $Vc_orderSn;
               
            }
           
            $uprow = db('se_project_order')->where('id',$id)->update($data);
            if($uprow > 0){
                $this->addManageLog('订单审核', '审核了ID为'.$id.'的订单');
                return getJsonStrSuc('审核成功');
            }else{
                
                return getJsonStrError('审核失败');
            }
            
        }else{
        
            $id = $this->request->get('id',0);
            if(!$id){
                $this->error('不合法的请求');
            }
            $GoodsJudgePrice = get_judgeprice();
            $data =  $this->orderModel->getById($id);
            $datanew =  $this->orderModel->getXLMById($id);
            $proj = $this->projectModel->getMQuery(['a.id'=> $data['I_projectID']])->find();
            $goods = $this->orderModel->getGoodsQuery(['a.I_orderID'=>$id,'a.I_goods_src'=>1])->select();
            $totalGoodsprice = $this->orderModel->getGoodsQuery(['a.I_orderID'=>$id,'a.I_goods_src'=>1])->sum('a.N_judge_totalprice');
            $totalGoodsprice = formatAmountSimply($totalGoodsprice/10000);
            $actotalGoodsprice = 0;
            $acjudgeGoodsprice = 0;
            if($data['Vc_orderSn']){
                $goods_erp = db('erp_systd')->where(['Vc_orderSn'=>$data['Vc_orderSn']])->select();
                $actotalGoodsprice = db('erp_systd')->where(['Vc_orderSn'=>$data['Vc_orderSn']])->sum('N_ac_settlement');
                $actotalGoodsprice = formatAmountSimply($actotalGoodsprice/10000);
                foreach ($goods_erp as &$v){
                    $v['I_goods_src'] = 2;
                    $v['N_judge_price'] = $GoodsJudgePrice;
                    $v['N_judge_totalprice'] = $v['N_judge_price']*$v['N_plan_weight'];
                    $acjudgeGoodsprice +=$v['N_judge_totalprice'];
                    //是否确认
                    $find = db('se_project_orderlist')
                    ->where(['Vc_orderSn'=>$v['Vc_orderSn'],'Vc_goods_breed'=>$v['Vc_goods_breed'],'Vc_goods_material'=>$v['Vc_goods_material']
                        ,'Vc_goods_spec'=>$v['Vc_goods_spec'],'Vc_goods_factory'=>$v['Vc_goods_factory']])
                        ->find();
                     
                    if($find){
                        $v['I_isconfirm'] =1;
                    }else{
                        $v['I_isconfirm'] =0;
                    }
                    
                    
                }
                $goods = array_merge($goods,$goods_erp);
            }
            $acjudgeGoodsprice = formatAmountSimply($acjudgeGoodsprice/10000);
            $actotalGoodsprice = formatAmountSimply($actotalGoodsprice);
            //拿到物流信息
           // $expressinfo = $this->orderModel->getExpressById($id);
//             dump($goods);die;
            $this->assign([
                'model' =>$this->orderModel,
                'data'=>$data,
                'datanew'=>$datanew,
                'goods'=>$goods,
                'proj'=>$proj,
                'totalGoodsprice'=>$totalGoodsprice,
                'actotalGoodsprice'=>$actotalGoodsprice,
                'acjudgeGoodsprice'=>$acjudgeGoodsprice,
            ]) ;
            return $this->fetch();
        }
        
    }
   

   

    
    
    
}
