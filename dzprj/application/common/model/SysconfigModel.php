<?php
namespace app\common\model ;

class SysconfigModel extends BaseModel {
	
	protected $table = "sm_sysconfig" ;
	protected $pk = 'id' ;
	protected $createTime = 'Createtime';
	protected $updateTime = 'Createtime';
	protected $status	  = 'state' ;
	
	public function getDataArr($where=[]){
	    
	    $where['state']=parent::DEFAULT_STATUS_NORMAL;
	    $da = $this->db($this->table)
	    ->field('Vc_showfields,Vc_requirefields')
	    ->where($where)
	    ->find();
	    
	    return $da;
	}
	
}