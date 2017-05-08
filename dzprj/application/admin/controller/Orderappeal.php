<?php
namespace app\admin\controller ;
use app\common\model\OrderAppealModel;


class Orderappeal extends AdminController {
	private $orderAppealModel;
	
	public function _initialize() {
		$this->orderAppealModel = new OrderAppealModel();
		parent::_initialize();
	}
	
	
	
    public function index($page=1) {
       
        $param['uname'] = input('uname/s','');
        $param['replyStatus'] = input('replyStatus/d',0);
        $param['replyType'] = input('replyType/d',0);
    	$pages = $this->orderAppealModel->getPage($page,$param) ;
    	 $this->assign([
	       
	        'model' =>$this->orderAppealModel,
	        'page' =>$pages,
	        'param'=>$param,
    	     
	    ]) ;
    	return $this->fetch("index") ;

    }
    
    public function edit(){
        
        if($this->request->isPost()){
            
            $id = input('post.id/d',0);
            $T_admin_note= input('post.adminnote/s','');
            $I_status = input('post.approved/d',0);
            if(!$id){
                return getJsonStrError('不合法的请求');
            }
            
            if(!$I_status){
                return getJsonStrError('未选择审核状态！');
            }
            $data['I_status'] = $I_status;
            $data['T_admin_note'] = $T_admin_note;
            
            $uprow = db('sm_order_appeal')->where('id',$id)->update($data);
            if($uprow > 0){
                $this->addManageLog('订单申诉', '查看审核处理了ID为'.$id.'的订单申诉');
                return getJsonStrSuc('审核成功');
            }else{
            
                return getJsonStrError('审核失败');
            }
            
            
        }else{
            
            $id = input('id/d',0);
            
            $data = $this->orderAppealModel->getById($id);
            
            $this->assign([
                'model' =>$this->orderAppealModel,
                'data'=>$data,
            ]) ;
            return $this->fetch() ;
            
        }
        
        
        
        
        
    }
  
   
  
    
    
    
}
