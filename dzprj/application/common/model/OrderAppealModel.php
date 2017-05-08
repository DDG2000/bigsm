<?php
namespace app\common\model ;
use \think\db\Query;
use app\admin\model\AdminModel;
class OrderAppealModel extends AdminModel {
	
	protected $table = "sm_order_appeal" ;
	protected $pk = 'id' ;
	protected $createTime = 'Createtime' ;
	protected $updateTime = 'Createtime' ;
	protected $status = 'state' ;
	
	const STATUS_WAIT  			        = 1 ;
	const STATUS_REPLIED  		    	= 2 ;
	const STATUS_CANCEL  		    	= 3 ;
	
	public $statusArray = [
			self::STATUS_WAIT		=>'待处理',
			self::STATUS_REPLIED		    =>'已处理',
			self::STATUS_CANCEL		    =>'已取消',
	] ;
	
	public function getPage ($currentPage,$param=[]) {
	    $where = [] ;
	     
	    if(iseta($param,'uname')){
	       
	           $where['a.Vc_orderSn|b.Vc_applicantName|etd.Vc_proj'] =['like','%'.trim($param['uname']).'%'];
	        
	    }
	    
        if(iseta($param,'replyStatus')){
            if(array_key_exists($param['replyStatus'], $this->statusArray)){
                
                $where['a.I_status'] = $param['replyStatus'];
            }
            
        }
        if(iseta($param,'replyType')){
                
                $where['a.I_type'] = $param['replyType'];
            
        }
	     
	    $query = $this->getMQuery($where) ;
	    return $this->getPaginationByQuery($query, $currentPage) ;
	}
	
	
	public function getById ($id) {
	    return $this->getMQuery(['a.id'=>$id])->find() ;
	}
	
	private function getMQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table($this->table)->alias('a')
	    ->field('a.*,b.Vc_applicantName,etd.Vc_proj')
	    ->join('sm_user_company  b','b.I_userID = a.I_userID','left')
	    ->join('erp_systd etd','etd.Vc_orderSn = a.Vc_orderSn','left')
	    ->where($where)
	    ->group('a.id')
	    ->order(['a.Createtime'=>'desc']);
	    return $query ;
	}
	
	
	
	
	
	
	
	
	
}