<?php
namespace app\common\model ;
use \think\db\Query;
use think\Model;
use app\admin\model\AdminModel;

class CommentModel extends AdminModel {
	
	protected $table = 'sm_comment' ;
	protected $pk = 'id' ;
	protected $createTime = 'Createtime';
	protected $updateTime = false ;
	protected $insert = ['state'=>1];
	protected $status = 'state' ;
	
	const COMMENT_WAITING  			    = 1 ;
	const COMMENT_DONE  			    = 2 ;
	const COMMENT_CANCEL  		    	= 3 ;


	public $statusArray = [
		self::COMMENT_WAITING		=>'待处理',
		self::COMMENT_DONE		    =>'已处理',
		self::COMMENT_CANCEL		=>'已取消',
	] ;
	// status查询
	public function scopeStatus($query)
	{
	    $query->where('state', 1);
	}
	
	/**
	 * 获取十条数据
	 */
	public function getPage($currentPage){
	    $query=new Query();
	    $query->table($this->table)
	    ->where('state',1);
	    return $this->getPaginationByQuery($query, $currentPage) ;
	}
	
	public function getPageList ($currentPage = 1,$param) {
		$query=new Query();
		$where=[];
		if(array_key_exists($param['applystatus'],$this->statusArray)){
			$where['a.I_status'] = $param['applystatus'];
		}
		if(iseta($param,'keywords')){
			//用户名/内容/手机号
			$where['su.Vc_name|a.Vc_content|a.Vc_phone'] =['like','%'.trim($param['keywords']).'%'];
		}
		$query = $this->getMQuery($where);
		return $this->getPaginationByQuery($query, $currentPage) ;
	}
	
	public function getById ($id) {
	    return $this->getMQuery(['a.id'=>$id])->find() ;
	}
	public function getMQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table($this->table)->alias('a')
	    ->field('a.*,su.Vc_name as username')
	    ->where('a.state',1)
	    ->where($where)
	    ->join('sm_user  su',' su.id=a.I_userID ','left')
	    ->order('a.Createtime desc');
	    return $query ;
	}
	
	public function getAllComment(){
		return $this->where('state',1)->select();
	}
}