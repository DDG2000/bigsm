<?php
namespace app\console\controller ;

use app\common\model\OrderModel;
use app\common\model\message\MessageService;
use app\admin\model\ConfigureModel;
/**
 * 命令行
 * @author Chenjx
 *
 */
class Sendmsg extends ConsoleController {
    protected $orderModel,$messageService,$configModel ;
	
	public function __construct() {
	    $this->orderModel = new OrderModel();
	    $this->messageService = new MessageService();
	    $this->configModel = new ConfigureModel() ;
	}
	
	public function sendAll() {
		$this->sendRemindArrived() ;
		$this->sendRemindCheck() ;
		$this->sendRemindDispatch() ;
		$this->sendRemindRepay() ;
	}
	
	/**
	 * 发货提醒
	 */
	public function sendRemindDispatch(){

	    $wheresql = "pol.Vc_orderstatus='已发货'";
	    $where = "o.issend=0  " ;
	    $rs = $this->orderModel->getOrderNoticeQuery($wheresql,$where,'o.I_userID,o.Vc_orderSn,o.Vc_orderstatus');
	    if($rs){
    	    foreach ($rs as &$v){
    	        $v['Createtime'] = date('Y-m-d H:i:s');
    	        
    	        $this->messageService->sendRemindDispatch($v['I_userID'], $v['Vc_orderSn'],['odn'=>$v['Vc_orderSn']]);
    	        
    	    }
    	    
    	    db('sm_order_msg')->insertAll($rs);
	    
	    }
	    
	}
	/**
	 * 到货提醒
	 */
	public function sendRemindArrived(){

	    $wheresql = "pol.Vc_orderstatus='已到货'";
	    $where = "o.issend=0  " ;
	    $rs = $this->orderModel->getOrderNoticeQuery($wheresql,$where,"o.I_userID,o.Vc_orderSn,o.Vc_orderstatus,o.Dt_arrived ");
	    if($rs){
	    $da = [];
	    foreach ($rs as $key=>$v){
	        
	        $temp['I_userID'] = $v['I_userID'];
	        $temp['Vc_orderSn'] = $v['Vc_orderSn'];
	        $temp['Vc_orderstatus'] = $v['Vc_orderstatus'];
	        $temp['Createtime'] = date('Y-m-d H:i:s');
	        $this->messageService->sendRemindArrived($v['I_userID'], $v['Vc_orderSn'], $v['Dt_arrived'],['odn'=>$v['Vc_orderSn']]);
	        $da[] = $temp;
	    }
	    
	    db('sm_order_msg')->insertAll($da);
	    }
	}
	/**
	 * 对账提醒
	 */
	public function sendRemindCheck(){
	    
		$date_set = $this->configModel->getValue('RemindCheck') ;
	    $date_now=date('j');
        if($date_set==$date_now){
    	    $wheresql = "pol.Vc_billstatus='待还款'";
    	    $where = "o.issend=0  " ;
    	    $rs = $this->orderModel->getbillNoticeQuery($wheresql,$where,"o.I_userID,o.Vc_orderSn,o.Vc_billstatus");
    	    if($rs){
        	    $da = [];
        	    foreach ($rs as $key=>$v){
        	        
        	        $temp['I_userID'] = $v['I_userID'];
        	        $temp['Vc_orderSn'] = $v['Vc_orderSn'];
        	        $temp['Vc_billstatus'] = $v['Vc_billstatus'];
        	        $temp['Createtime'] = date('Y-m-d H:i:s');
        	        
        	        $this->messageService->sendRemindCheck($v['I_userID'], $v['Vc_orderSn'],['type'=>4]);
        	        $da[] = $temp;
        	    }
        	    
        	    db('sm_bill_msg')->insertAll($da);
    	    }
        }
	}
	
	/**
	 * 还款提醒
	 */
	public function sendRemindRepay(){
        
		$date_conf = $this->configModel->getValue('RemindRepay') ;
	    
	    $wheresql = "pol.Vc_billstatus='待还款'";
	    $where = "o.issend=0  " ;
	    $rs = $this->orderModel->getbillRepayQuery($wheresql,$where,"o.I_userID,o.Vc_orderSn,o.Vc_billstatus,o.I_loan_days,o.Dt_arrived,o.I_ct_loan_life");
	    if($rs){
	    $da = [];
	    foreach ($rs as $key=>$v){
	        $day = $v['I_ct_loan_life']-$v['I_loan_days'];
	        if($day<=$date_conf){
	            
    	        $temp['I_userID'] = $v['I_userID'];
    	        $temp['Vc_orderSn'] = $v['Vc_orderSn'];
    	        $temp['Vc_billstatus'] = $v['Vc_billstatus'];
    	        $temp['Createtime'] = date('Y-m-d H:i:s');
    	        $overdate = date('Y-m-d',strtotime('+'.$day.'day',strtotime($v['Dt_arrived'])));
    	        $this->messageService->sendRemindRepay($v['I_userID'], $v['Vc_orderSn'],$overdate,['type'=>2]);
    	        $da[] = $temp; 

	        }
	        
	    }
	    
	    db('sm_bill_msg_repay')->insertAll($da);
	    }
	}
	
	
	
	
	
	
	
}