<?php
namespace app\common\model ;
use \think\db\Query;
use app\admin\model\AdminModel;
use think\paginator\driver\Bootstrap;
use app\common\model\page\Mypage;
class OrderModel extends AdminModel {
	
	protected $table = "se_project_order" ;
	protected $pk = 'id' ;
	protected $createTime = 'Createtime' ;
	protected $status = 'state' ;
	
// 	const STATUS_CHECKING  			    = 0 ;
// 	const STATUS_WAITSEND  			    = 1 ;
// 	const STATUS_SENDET  			    = 2 ;
// 	const STATUS_ARRIVED  			    = 3 ;
// 	const STATUS_CONFIRM  		    	= 4 ;
// 	const STATUS_REJECT  		    	= 5 ;
// 	const STATUS_FREEZED  		    	= 6 ;
// 	const STATUS_CLOSED  		    	= 7 ;
	
	const STATUS_CHECKING  			    = 0 ;
	const STATUS_WAITSEND  			    = 1 ;
	const STATUS_SENDET  			    = 2 ;
	const STATUS_ARRIVED  			    = 3 ;
	const STATUS_CONFIRM  		    	= 4 ;
	const STATUS_REJECT  		    	= -1 ;
	const STATUS_FREEZED  		    	= 5 ;
	const STATUS_CLOSED  		    	= 6 ;
	
	//-1 审核未通过 0=待审核=未审核 1=已审核=待发货 2=已发货 3=已到货=未确认  4=已确认 5=已冻结 6=已关闭=已取消
	public $statusArray = [
	    self::STATUS_CHECKING		    =>'待审核',
	    self::STATUS_WAITSEND		    =>'待发货',
	    self::STATUS_SENDET		        =>'已发货',
	    self::STATUS_ARRIVED		    =>'已到货',
	    self::STATUS_CONFIRM		    =>'已确认',
	    self::STATUS_REJECT		        =>'审核未通过',
	    self::STATUS_FREEZED		    =>'已冻结',
	    self::STATUS_CLOSED		        =>'已关闭',
	] ;
	
	public function getPage ($currentPage,$param=[]) {
	    $where = [] ;
	
	    if(iseta($param,'uname')){
	         
	        $where['tmp.projname|tmp.ct_projname|tmp.Vc_orderSn|tmp.Vc_Sn|tmp.Vc_erp_name'] =['like','%'.trim($param['uname']).'%'];
	         
	    }
	     
	    if(array_key_exists($param['projStatus'],$this->statusArray)){
	
	        $where['tmp.StatusIndex'] = $param['projStatus'];
	
	    }
	
	    //$query = $this->getMQuery($where) ;
	    
	    $pageSize=10;
	    $pages = $this->getXLMQuery($where)->page($currentPage, $pageSize )->select();
	    $data = $this->getXLMQuery($where)->select();
	    $total = count($data);
// 	    $pages = new Bootstrap($pages, $pageSize,$currentPage,false,$total) ;
	    $pages = new Mypage($pages, $pageSize,$currentPage,false,$total) ;
	    return $pages ;
// 	    return $this->getPaginationByQuery($query, $currentPage) ;
	}
	
	
	public function getById ($id) {
	    return $this->getMQuery(['a.id'=>$id])->find() ;
	}
	public function getXLMById ($id) {
	    return $this->getXLMQuery(['tmp.id'=>$id])->find() ;
	}
	
	public function getGoodsById ($oid) {
	    return $this->getGoodsQuery(['a.I_orderID'=>$oid])->select() ;
	}
	
	public function getGoodsByPageId ($oid) {
	    return $this->getGoodsQuery(['a.I_orderID'=>$oid])->paginate(5,true) ;
	}
	
	
	public function getGoodsBygId ($id) {
	    return $this->getGoodsQuery(['a.id'=>$id])->find() ;
	}

	//查询平台货物信息
	public function getGoodsQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table('se_project_orderlist')->alias('a')
	    ->field('a.*')
	    ->join('se_project_order b ','b.id = a.I_orderID','left')
	    ->where($where)
	    ->order(['a.Createtime'=>'asc']);
	    return $query ;
	}
	
	
	public function getMQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table($this->table)->alias('a')
	    ->field('a.*,f.Vc_name industry,e.Vc_name projname,ep.Vc_name ct_projname,ep.Vc_code,b.name proname,c.name cityname,d.name areaname
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
	
	
	
	//项目订单进行视图查询
	public function getXLMQuery ($where = []) {
	    $query = new Query() ;
	   // $query2 = clone $query;
	    $where2['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $subQuery = $query->table($this->table)->alias('a')
	    ->field('a.*,f.Vc_name industry,e.Vc_name projname,ep.Vc_name ct_projname,ep.Vc_code,b.name proname,c.name cityname,d.name areaname
            ,h.Vc_applicantName,h.Vc_erp_name,ec.Vc_name company,(select max(StatusIndex) from V_OrderDetailes vst where vst.id=a.id ) as StatusIndex')
	                ->join('s_province b','b.id=a.I_provinceID','left')
	                ->join('s_city c','c.id=a.I_cityID','left')
	                ->join('s_district d','d.id=a.I_districtID','left')
	                ->join('se_project e','e.id = a.I_projectID','left')
	                ->join('erp_project ep','ep.Vc_code = e.Vc_code','left')
	                ->join('sm_industry f','f.id = a.I_industryID','left')
	                ->join('sm_user_company h','h.I_userID = a.I_userID','left')
	                ->join('erp_company ec','ec.Vc_companycode = ep.Vc_companycode','left')
	                ->where($where2)
	                ->group('a.id')
	                ->order(['a.Createtime'=>'desc'])->buildSql();
	  
	    $query->table($subQuery.' tmp')
	    ->field('tmp.* , vod.Status StatusName')
	    ->join('V_OrderStatus vod','tmp.StatusIndex=vod.id','left')
	    ->where($where)
	    ->order(['tmp.Createtime'=>'desc'])
	    ;
// 	    $query->query("select tmp.*
// 	    , vod.Status StatusName
//         from (
//         SELECT    a.*,f.Vc_name industry,e.Vc_name projname,ep.Vc_name ct_projname,b.name proname,c.name cityname,d.name areaname
//                     ,h.Vc_applicantName,h.Vc_erp_name,ec.Vc_name company
//         			,(select max(StatusIndex) from V_OrderDetailes vst where vst.id=a.id ) as StatusIndex
//         FROM se_project_order a 
//         LEFT JOIN s_province b on b.id = a.I_provinceID
//         LEFT JOIN s_city c on c.id = a.I_cityID
//         LEFT JOIN s_district d on d.id = a.I_districtID
//         LEFT JOIN se_project e on e.id = a.I_projectID
//         LEFT JOIN erp_project ep on ep.Vc_code = e.Vc_code
//         LEFT JOIN sm_industry f on f.id = a.I_industryID
//         LEFT JOIN sm_user_company h on h.I_userID = e.I_userID
//         LEFT JOIN erp_company ec on ec.Vc_companycode = ep.Vc_companycode
//         WHERE a.state = 1 and ".$where." GROUP  BY a.id ORDER BY a.Createtime  ) tmp left join V_OrderStatus vod on tmp.StatusIndex=vod.id");
	    
	    return $query ;
	}


	//交易记录复杂查询使用
	public function getWQuery ($where = [],$field='*') {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table($this->table)->alias('a')
// 	    ->field('pol.*,a.*,f.Vc_name industry,e.Vc_name projname,e.Vc_ct_name ct_projname,b.name proname,c.name cityname,d.name areaname
//             ,h.Vc_applicantName,h.Vc_erp_name,ec.Vc_name company'.$field)
	    ->field($field)
		        ->join('erp_systd pol','pol.Vc_orderSn = a.Vc_orderSn','left')
		        ->join('s_province b','b.id=a.I_provinceID','left')
		        ->join('s_city c','c.id=a.I_cityID','left')
		        ->join('s_district d','d.id=a.I_districtID','left')
		        ->join('se_project e','e.id = a.I_projectID','left')
		        ->join('sm_industry f','f.id = a.I_industryID','left')
		        ->join('sm_user_company h','h.I_userID = a.I_userID','left')
		        ->where($where)
		        ->order(['a.Createtime'=>'desc']);
	    return $query ;
	}
	//交易记录复杂查询使用2--主要判断订单是否已确认
	public function getGQuery ($where = [],$field='*') {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $where['pol.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table($this->table)->alias('a')
	    ->field($field)
		        ->join('erp_systd pol','pol.Vc_orderSn = a.Vc_orderSn','left')
		        ->join('s_province b','b.id=a.I_provinceID','left')
		        ->join('s_city c','c.id=a.I_cityID','left')
		        ->join('s_district d','d.id=a.I_districtID','left')
		        ->join('se_project e','e.id = a.I_projectID','left')
		        ->join('sm_industry f','f.id = a.I_industryID','left')
		        ->join('sm_user_company h','h.I_userID = a.I_userID','left')
		        ->join('se_project_orderlist g','pol.Vc_orderSn = g.Vc_orderSn 
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
	    
	//交易记录优化查询使用4--用于获取已确认订单
	
	public function getOrderlistQuery ($where = [],$field='*') {
	     $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $where['g.state'] = self::DEFAULT_STATUS_NORMAL;
	    $where['pol.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table('se_project_orderlist')->alias('g')
// 	    ->field('pol.*,IFNULL(g.state,0)  I_isconfirm,a.id orderid')
	    ->field($field)
        ->join('se_project_order a','g.Vc_orderSn = a.Vc_orderSn','left')
        ->join('se_project e','e.id = a.I_projectID','left')
        ->join('erp_systd pol','pol.Vc_orderSn = g.Vc_orderSn 
            and pol.Vc_goods_breed = g.Vc_goods_breed 
            and pol.Vc_goods_material = g.Vc_goods_material 
            and pol.Vc_goods_spec = g.Vc_goods_spec
            and pol.Vc_goods_factory = g.Vc_goods_factory','left')
        ->where($where);
	    
	    return $query ;
	    }
	    
	    
	    //订单提醒-用于查询未发送消息的订单
	    public function getOrderNoticeQuery ($wheresql = '1=1',$where = '1=1',$field='*') {
	        $query = new Query() ;
	        
// 	        $rs = $query->query("SELECT o.uid,o.Vc_orderSn from (SELECT pol.*,e.I_userID uid,IFNULL(s.state,0) issend
//              FROM se_project_order a
//             LEFT JOIN  erp_systd pol on pol.Vc_orderSn = a.Vc_orderSn 
//             LEFT JOIN se_project e on e.id = a.I_projectID
//             LEFT JOIN sm_order_msg s on s.Vc_orderSn = a.Vc_orderSn and s.I_userID = e.I_userID
// 	        and s.Vc_orderstatus = pol.Vc_orderstatus
//             WHERE a.state = 1   
//             and pol.state=1 and pol.Vc_orderstatus='已到货'
//             GROUP BY pol.Vc_orderSn ) o where o.issend=0");

	        $rs = $query->query("SELECT ".$field." from (SELECT pol.*,a.I_userID ,IFNULL(s.state,0) issend
             FROM se_project_order a
            LEFT JOIN  erp_systd pol on pol.Vc_orderSn = a.Vc_orderSn 
            LEFT JOIN se_project e on e.id = a.I_projectID
            LEFT JOIN sm_order_msg s on s.Vc_orderSn = pol.Vc_orderSn and s.I_userID = a.I_userID and s.Vc_orderstatus = pol.Vc_orderstatus
	            
            WHERE a.state = 1   
            and pol.state=1 and {$wheresql}
            GROUP BY pol.Vc_orderSn ) o where {$where}");
	        
	        return $rs ;
	    }
	    //账单对账提醒
	    public function getbillNoticeQuery ($wheresql = '1=1',$where = '1=1',$field='*') {
	        $query = new Query() ;
	        
            // 	        $rs = $query->query("SELECT * from (SELECT pol.*,e.I_userID ,IFNULL(s.state,0) issend
            //  FROM se_project_order a
            // LEFT JOIN  erp_syszd pol on pol.Vc_orderSn = a.Vc_orderSn 
            // LEFT JOIN se_project e on e.id = a.I_projectID
            // LEFT JOIN sm_bill_msg s on s.Vc_orderSn = pol.Vc_orderSn and s.I_userID = e.I_userID and s.Vc_billstatus = pol.Vc_billstatus
            // WHERE a.state = 1   
            
            // and pol.state=1 and pol.Dt_arrived is not null and pol.Vc_billstatus='待还款'
            // GROUP BY pol.Vc_orderSn
            // ) o  where o.issend=0");

	        $rs = $query->query("SELECT ".$field." from (SELECT pol.*,a.I_userID ,IFNULL(s.state,0) issend
             FROM se_project_order a
            LEFT JOIN  erp_syszd pol on pol.Vc_orderSn = a.Vc_orderSn 
            LEFT JOIN se_project e on e.id = a.I_projectID
            LEFT JOIN sm_bill_msg s on s.Vc_orderSn = pol.Vc_orderSn and s.I_userID = a.I_userID 
	        and s.Vc_billstatus = pol.Vc_billstatus
            WHERE a.state = 1   
            and pol.state=1 and pol.Dt_arrived is not null and {$wheresql}
            GROUP BY pol.Vc_orderSn ) o where {$where}");
	        
	        return $rs ;
	    }
	    //账单还款提醒
	    public function getbillRepayQuery ($wheresql = '1=1',$where = '1=1',$field='*') {
	        $query = new Query() ;
	        
//             	        $rs = $query->query("SELECT * from (SELECT pol.*,e.I_userID ,IFNULL(s.state,0) issend,ep.I_ct_loan_life
//                      FROM se_project_order a
//                     LEFT JOIN  erp_syszd pol on pol.Vc_orderSn = a.Vc_orderSn 
//                     LEFT JOIN erp_project ep on ep.Vc_code = pol.Vc_projNo
//                     LEFT JOIN se_project e on e.id = a.I_projectID
//                     LEFT JOIN sm_bill_msg_repay s on s.Vc_orderSn = pol.Vc_orderSn and s.I_userID = e.I_userID and s.Vc_billstatus = pol.Vc_billstatus
//                     WHERE a.state = 1   
                    
//                     and pol.state=1 and pol.Dt_arrived is not null and pol.Vc_billstatus='待还款'
//                     GROUP BY pol.Vc_orderSn
//                     ) o ");

	        $rs = $query->query("SELECT ".$field." from (SELECT pol.*,a.I_userID ,IFNULL(s.state,0) issend,ep.I_ct_loan_life
             FROM se_project_order a
            LEFT JOIN  erp_syszd pol on pol.Vc_orderSn = a.Vc_orderSn 
	        LEFT JOIN erp_project ep on ep.Vc_code = pol.Vc_projNo
            LEFT JOIN se_project e on e.id = a.I_projectID
            LEFT JOIN sm_bill_msg_repay s on s.Vc_orderSn = pol.Vc_orderSn and s.I_userID = a.I_userID 
	        and s.Vc_billstatus = pol.Vc_billstatus
            WHERE a.state = 1   
            and pol.state=1 and pol.Dt_arrived is not null and {$wheresql}
            GROUP BY pol.Vc_orderSn ) o where {$where}");
	        
	        return $rs ;
	    }
	    
	    //用视图方式进行前台交易订单查询
	    public function getHomeOrderQuery($where = [],$field='*'){
	        
	        $query = new Query() ;
	        //$where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	        $query->table('V_OrderDetailes')->alias('a')
	        // 	    ->field('a.*')
	        ->field($field)
	        ->where($where);
// 	        ->order(['a.Createtime'=>'desc']);
	        return $query ;
	        
	        
	    }
	    //订单详情中的最新物流信息
	    public function  getExpressLastInfo($where = [],$field='*'){
	        
	        $query = new Query() ;
	        //$where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	        $query->table('V_OrderDetailes')->alias('a')
	        // 	    ->field('a.*,b.*')
	        ->field($field)
	        ->join('erp_systd b','b.Vc_orderSn=a.ERP_Sn 
                    and b.Vc_goods_breed = a.Vc_goods_breed 
                    and b.Vc_goods_material = a.Vc_goods_material
                    and b.Vc_goods_spec = a.Vc_goods_spec
                    and b.Vc_goods_factory = a.Vc_goods_factory','left')
	        ->where($where)
	        ->order('a.StatusIndex desc,b.Dt_arrived desc,b.Dt_senddate desc');
	        return $query ;
	        
	        
	    }
	    
	
}