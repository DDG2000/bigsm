<?php
namespace app\common\model ;
use \think\db\Query;
use app\admin\model\AdminModel;
class ProjectModel extends AdminModel {
	
	protected $table = "se_project" ;
	protected $pk = 'id' ;
	protected $createTime = 'Createtime' ;
	protected $updateTime = 'D_updatetime' ;
	protected $status = 'state' ;
	
	const STATUS_CHECKING  			    = 0 ;
	const STATUS_REJECT  		    	= 1 ;
	const STATUS_PASS  			        = 2 ;
	const STATUS_INPROGRESS  			= 3 ;
	const STATUS_CLOSED  		    	= 4 ;
	const STATUS_FINISH  		    	= 5 ;
	
	
	public $statusArray = [
			self::STATUS_CHECKING		=>'待审核',
			self::STATUS_REJECT		    =>'审核未通过',
			self::STATUS_PASS		    =>'审核通过',
			self::STATUS_INPROGRESS		=>'进行中',
			self::STATUS_CLOSED		    =>'已关闭',
			self::STATUS_FINISH		    =>'已完成',
	] ;
	
	public function getPage ($currentPage,$param=[]) {
	    $where = [] ;
	     
	    if(iseta($param,'uname')){
	        
	           $where['a.Vc_name|ea.Vc_name|h.Vc_applicantName|h.Vc_erp_name'] =['like','%'.trim($param['uname']).'%'];
	        
	        
	    }
	    
        if(array_key_exists($param['projStatus'],$this->statusArray)){
            
              $where['a.I_status'] = $param['projStatus'];
            
        }
        
	    $query = $this->getMQuery($where) ;
	    return $query ;
	}
	
	
	public function getById ($id) {
	    return $this->getMQuery(['a.id'=>$id])->find() ;
	}
	
	public function getUserProjectById ($uid, $id) {
		return $this->getMQuery(['a.id'=>$id,'a.I_userID'=>$uid])->find() ;
	}
	
	public function getContactById ($pid) {
	    return $this->getContactQuery(['a.I_projectID'=>$pid])->select() ;
	}
	private function getContactQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table('sm_project_contact')->alias('a')
	    ->field('a.*')
	    ->where($where)
	    ->order(['a.Createtime'=>'asc']);
	    return $query ;
	}
	
// 	private function getMQuery2 ($where = []) {
// 	    $query = new Query() ;
// 	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
// // 	    $where['e.state'] = self::DEFAULT_STATUS_NORMAL;
// 	    $query->table($this->table)->alias('a')
// 	    ->field('a.*,f.Vc_name orgname,g.Vc_name loantype,b.name proname,c.name cityname,d.name areaname,
// 	        e.Vc_name uname,e.Vc_mobile umobile,h.Vc_applicantName,h.Vc_erp_name,ec.Vc_name company')
// 	    ->join('s_province b','b.id=a.I_provinceID','left')
// 	    ->join('s_city c','c.id=a.I_cityID','left')
// 	    ->join('s_district d','d.id=a.I_districtID','left')
// 	    ->join('sm_user e','e.id = a.I_userID','left')
// 	    ->join('sm_project_org_class f','f.id = a.I_project_org_classID','left')
// 	    ->join('sm_project_loantype g','g.id = a.I_loantypeID','left')
// 	    ->join('sm_user_company h','h.I_userID = a.I_userID','left')
// 	    ->join('erp_company ec','ec.Vc_companycode = a.Vc_ct_companycode','left')
// 	    ->where($where)
// 	    ->order(['a.Createtime'=>'desc']);
// 	    return $query ;
// 	}
	
	
	public function getMQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
// 	    $where['e.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table($this->table)->alias('a')
	    ->field('a.id aid,a.Createtime applytime,a.Vc_name projname,a.Vc_address projaddr,a.*,f.Vc_name orgname,g.Vc_name loantype,b.name proname,c.name cityname,d.name areaname,e.Vc_name uname,e.Vc_mobile umobile
            ,h.Vc_applicantName,h.Vc_erp_name,ec.Vc_name company,ea.*,ea.Vc_name Vc_ct_name')
// 	    ->field('f.Vc_name orgname,g.Vc_name loantype,b.name proname,c.name cityname,d.name areaname,e.Vc_name uname,e.Vc_mobile umobile
//              ,h.Vc_applicantName,h.Vc_erp_name,ec.Vc_name company'.$field)
	    ->join('s_province b','b.id=a.I_provinceID','left')
	    ->join('s_city c','c.id=a.I_cityID','left')
	    ->join('s_district d','d.id=a.I_districtID','left')
	    ->join('sm_user e','e.id = a.I_userID','left')
	    ->join('sm_project_org_class f','f.id = a.I_project_org_classID','left')
	    ->join('sm_project_loantype g','g.id = a.I_loantypeID','left')
	    ->join('sm_user_company h','h.I_userID = a.I_userID','left')
	    ->join('erp_project ea','ea.Vc_code = a.Vc_code','left')
	    ->join('erp_company ec','ec.Vc_companycode = ea.Vc_companycode','left')
	    ->where($where)
// 	    ->group('a.id')
	    ->order(['a.Createtime'=>'desc']);
	    return $query ;
	}
	//前台项目查询使用
	public function getHomeMQuery ($where = [],$field = '*') {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table($this->table)->alias('a')
// 	    ->field('a.id aid,a.Createtime applytime,a.Vc_name projname,a.Vc_address projaddr,a.*,f.Vc_name orgname,g.Vc_name loantype,b.name proname,c.name cityname,d.name areaname,e.Vc_name uname,e.Vc_mobile umobile
//             ,h.Vc_applicantName,h.Vc_erp_name,ec.Vc_name company,ea.*,ea.Vc_name Vc_ct_name')
        ->field($field)
	    ->join('s_province b','b.id=a.I_provinceID','left')
	    ->join('s_city c','c.id=a.I_cityID','left')
	    ->join('s_district d','d.id=a.I_districtID','left')
	    ->join('sm_user e','e.id = a.I_userID','left')
	    ->join('sm_project_org_class f','f.id = a.I_project_org_classID','left')
	    ->join('sm_project_loantype g','g.id = a.I_loantypeID','left')
	    ->join('sm_user_company h','h.I_userID = a.I_userID','left')
	    ->join('erp_project ea','ea.Vc_code = a.Vc_code','left')
	    ->join('erp_company ec','ec.Vc_companycode = ea.Vc_companycode','left')
	    ->where($where)
// 	    ->group('a.id')
	    ->order(['a.Createtime'=>'desc']);
	    return $query ;
	}
	
	
	//获取项目公司信息
	public function  getProjComQuery($where = [],$field="*"){
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table($this->table)->alias('a')
	    ->field($field)
	    ->join('sm_user_company b','b.I_userID = a.I_userID','left')
	    ->where($where);
	    return $query ;
	}
	
	
	
	
	
}