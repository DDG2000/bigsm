<?php
namespace app\common\model ;
use \think\db\Query;
use app\admin\model\AdminModel;
class BillModel extends AdminModel {
	
	protected $table = "se_project_order" ;
	protected $pk = 'id' ;
	protected $createTime = 'Createtime' ;
	protected $status = 'state' ;
	
	
	
	
	public $statusArray = [
	    '已还款'		    =>'已还款',
	    '已逾期'		    =>'已逾期',
	    '待还款'		    =>'待还款',
	] ;
	
	public function getPage ($currentPage,$param=[]) {
	    $where = [] ;
	
	    if(iseta($param,'uname')){
	         
	        $where['e.Vc_name|ep.Vc_name|a.Vc_orderSn|h.Vc_erp_name'] =['like','%'.trim($param['uname']).'%'];
	         
	    }
	     
	    if(array_key_exists($param['projStatus'],$this->statusArray)){
	
	        $where['a.I_status'] = $param['projStatus'];
	
	    }
	
	    $query = $this->getMQuery($where) ;
	    
	    return $this->getPaginationByQuery($query, $currentPage) ;
	}
	
	
	public function getById ($id) {
	    return $this->getMQuery(['a.id'=>$id])->find() ;
	}
	
	
	public function getMQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $where['a.Vc_orderSn'] = ['exp','is not null'];
	    $query->table($this->table)->alias('a')
	    ->field('a.*,f.Vc_name industry,e.Vc_name projname,ep.Vc_name ct_projname,b.name proname,c.name cityname,d.name areaname
            ,h.Vc_applicantName,h.Vc_erp_name,ec.Vc_name company')
		        ->join('s_province b','b.id=a.I_provinceID','left')
		        ->join('s_city c','c.id=a.I_cityID','left')
		        ->join('s_district d','d.id=a.I_districtID','left')
		        ->join('se_project e','e.id = a.I_projectID','left')
		        ->join('erp_project ep','ep.Vc_code = e.Vc_code','left')
		        ->join('sm_industry f','f.id = a.I_industryID','left')
		        ->join('sm_user_company h','h.I_userID = a.I_userID','left')
		        ->join('erp_company ec','ec.Vc_companycode = ep.Vc_companycode','left')
		        ->where($where)
		        ->group('a.id')
		        ->order(['a.Createtime'=>'desc']);
	    return $query ;
	}
	
	public function getBAllQuery ($where = [],$field='*') {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $where['a.Vc_orderSn'] = ['exp','is not null'];
	    $where['ezd.Dt_arrived'] = ['exp','is not null'];
	    $query->table($this->table)->alias('a')
// 	    ->field('a.*,f.Vc_name industry,e.Vc_name projname,ep.Vc_name ct_projname,b.name proname,c.name cityname,d.name areaname
//             ,h.Vc_applicantName,h.Vc_erp_name,ec.Vc_name company')
	     ->field($field)
		        ->join('s_province b','b.id=a.I_provinceID','left')
		        ->join('s_city c','c.id=a.I_cityID','left')
		        ->join('s_district d','d.id=a.I_districtID','left')
		        ->join('se_project e','e.id = a.I_projectID','left')
		        ->join('erp_project ep','ep.Vc_code = e.Vc_code','left')
		        ->join('sm_industry f','f.id = a.I_industryID','left')
		        ->join('sm_user_company h','h.I_userID = a.I_userID','left')
		        ->join('erp_company ec','ec.Vc_companycode = ep.Vc_companycode','left')
		        ->join('erp_syszd ezd','ezd.Vc_orderSn = a.Vc_orderSn','left')
		        ->where($where)
		        ->group('a.id')
		        ->order(['a.Createtime'=>'desc']);
	    return $query ;
	}
	public function getBillQuery ($where = [],$field='*') {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $where['a.Vc_orderSn'] = ['exp','is not null'];
	    $where['ezd.Dt_arrived'] = ['exp','is not null'];
	    $query->table($this->table)->alias('a')
// 	    ->field('a.*,f.Vc_name industry,e.Vc_name projname,ep.Vc_name ct_projname,b.name proname,c.name cityname,d.name areaname
//             ,h.Vc_applicantName,h.Vc_erp_name,ec.Vc_name company')
	     ->field($field)
		        ->join('s_province b','b.id=a.I_provinceID','left')
		        ->join('s_city c','c.id=a.I_cityID','left')
		        ->join('s_district d','d.id=a.I_districtID','left')
		        ->join('se_project e','e.id = a.I_projectID','left')
		        ->join('sm_industry f','f.id = a.I_industryID','left')
		        ->join('sm_user_company h','h.I_userID = a.I_userID','left')
		        ->join('erp_syszd ezd','ezd.Vc_orderSn = a.Vc_orderSn','left')
		        ->where($where)
		        ->order(['a.Createtime'=>'desc']);
	    return $query ;
	}
	
	//账单复杂查询使用2--主要判断账单是否已确认
	public function getGQuery ($where = [],$field='*') {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $where['pol.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table($this->table)->alias('a')
	    ->field($field)
	    ->join('erp_syszd pol','pol.Vc_orderSn = a.Vc_orderSn','left')
	    ->join('se_project e','e.id = a.I_projectID','left')
	    ->join('sm_syszd g','pol.Vc_orderSn = g.Vc_orderSn
                    and pol.Vc_goods_breed = g.Vc_goods_breed
                    and pol.Vc_goods_material = g.Vc_goods_material
                    and pol.Vc_goods_spec = g.Vc_goods_spec
                    and pol.Vc_goods_factory = g.Vc_goods_factory','left')
	                    ->where($where)
	                    ->order(['pol.id'=>'desc']);
	    return $query ;
	}
	
	//交易记录复杂查询使用3--用于同时获取项目和项目下订单信息-不影响分页
	
	public function getOrderProjQuery ($where = [],$field='*') {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table($this->table)->alias('a')
	    // 	    ->field('a.*,b.projname,b.Vc_ct_name')
	    ->field($field)
	    ->join('(SELECT sp.id,sp.Vc_name projname,ep.Vc_name Vc_ct_name from se_project sp
        LEFT JOIN erp_project ep  ON ep.Vc_code = sp.Vc_code
        WHERE  sp.state = 1
        GROUP BY sp.id ) b','b.id = a.I_projectID','left')
	        ->where($where);
	    return $query ;
	}
	
	
	
	
}