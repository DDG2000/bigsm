<?php
namespace app\home\controller ;

use app\common\model\BillModel;
use app\common\model\ProjectModel;


class Bill extends WorkbenchController {

	private $billModel;
	public function __construct() {

		parent::_initialize() ;
		parent::__construct();
		$this->check_certify();
		$this->projectModel = new ProjectModel;
		$this->billModel = new BillModel;

	}

	public function listpage ($type=4) {
	    $uid = $this->getSessionUid();
	    $map = [];
	    $map['a.I_userID']=$uid;
	    $where['Dt_arrived'] = ['exp','is not null'];

	    $orderStatus = input('orderStatus/s','');
	    $industryId = input('industryId/s','');
	    $projId = input('projId/d',-1);
	    $keyword = input('keyword/s','');
	    $isSearch = 0;
	    switch ($type){
	        case 1:
	            $where['Vc_billstatus'] = '已还款';//已还款
	            $map['ezd.Vc_billstatus'] ='已还款';
	            break;
	        case 2:
	            $where['Vc_billstatus'] = '待还款';//待还款
	            $map['ezd.Vc_billstatus'] ='待还款';
	            break;
	        case 3:
	            $where['Vc_billstatus'] = '已逾期';//已逾期
	            $map['ezd.Vc_billstatus'] ='已逾期';
	            break;

	    }

	    //订单状态搜索
	    if(array_key_exists($orderStatus,$this->billModel->statusArray)){

	        $map['ezd.Vc_billstatus'] = $orderStatus;

	        $where['Vc_billstatus'] = $orderStatus;

	        $param['orderStatus'] = $orderStatus;
	        $isSearch = 1;
	    }
	    //行业搜索
	    if($industryId){

	        $where['Vc_industry'] = $industryId;
	        $map['ezd.Vc_industry'] = $industryId;
	        $param['industryId'] = $industryId;
	        $isSearch = 1;
	    }


	    //项目搜索
	    if($projId>0){

	        $map['a.I_projectID'] = $projId;

	        $param['projId'] = $projId;
	        $isSearch = 1;
	    }

	    //关键词搜索

	    if($keyword){

	        $map['a.Vc_orderSn|ezd.Vc_goods_breed|ezd.Vc_goods_material|ezd.Vc_goods_spec|ezd.Vc_goods_factory'] = ['like','%'.trim($keyword).'%'];
	        $where['pol.Vc_orderSn|pol.Vc_goods_breed|pol.Vc_goods_material|pol.Vc_goods_spec|pol.Vc_goods_factory'] = ['like','%'.trim($keyword).'%'];

	        $param['keyword'] = $keyword;
	        $isSearch = 1;
	    }

	    $idarr1=[];
	    $rs1 = $this->billModel->getBAllQuery($map,'a.id')->group('a.Vc_orderSn')->select();

	    if($rs1){
	        foreach($rs1 as $v){
	            $idarr1[]=$v['id'];
	        }
	    }
	    $brr = $idarr1;

	    if($brr){
	        if(count($brr)>1){
	            $idstr=implode(',',$brr);
	            $sqlwhere['a.id']=['exp','in ('.$idstr.')'];
	        }else{
	            $sqlwhere['a.id']=$brr['0'];
	        }
	    }else{
	        $sqlwhere['a.id']=0;
	    }

// 	    dump($sqlwhere);

	    $param['type']=$type;
// 	    $list = $this->billModel->where($sqlwhere)->paginate(2,false,['query'=>$param]);
	    $list = $this->billModel->getOrderProjQuery($sqlwhere,'a.*,b.projname,b.Vc_ct_name')->paginate(2,false,['query'=>$param]);
	    $listdata = [];
	    foreach ($list->items() as $n=>$v ){
	        
	        $listdata[$n] = $v;
	        $where['pol.Vc_orderSn'] = $v['Vc_orderSn'];
	     
	        $billlist =$this->billModel->getGQuery($where,'pol.*,IFNULL(g.I_isconfirm,0) I_isconfirm')->select();


	        $listdata[$n]['billlist'] = $billlist;
	    }



	    //数量统计
	    $count['finish'] =count($this->billModel->getBillQuery(['ezd.Vc_billstatus'=>'已还款','a.I_userID'=>$uid])->select());
	    $count['wait'] =count($this->billModel->getBillQuery(['ezd.Vc_billstatus'=>'待还款','a.I_userID'=>$uid])->select());
	    $count['overdate'] =count($this->billModel->getBillQuery(['ezd.Vc_billstatus'=>'已逾期','a.I_userID'=>$uid])->select());
	    
	    $count['overdatemoney'] =$this->billModel->getBillQuery(['ezd.Vc_billstatus'=>'已逾期','a.I_userID'=>$uid])->sum('ezd.N_settlement');
	    //$count['other'] =$this->billModel->getBAllQuery(['ezd.Vc_billstatus'=>'已确认','e.I_userID'=>$uid],'count(*) ')->value('count(*)');


	    //行业
	    $malls = $this->billModel->getBAllQuery(['a.I_userID'=>$uid],'ezd.Vc_industry')->group('ezd.Vc_industry')->select();

	    //所属项目
	    $projs = $this->billModel->getBAllQuery(['a.I_userID'=>$uid],'a.I_projectID,e.Vc_name projname,ep.Vc_name ct_projname')->group('a.I_projectID')->select();

// 	    dump($listdata);die;
	    $this->assign([
	        'count'=>$count,
	        'type'=>$type,
	        'model'=>$this->billModel,
	        'list'=>$list,
	        'listdata'=>$listdata,
	        'malls'=>$malls,
	        'projs'=>$projs,
	        'param'=>$param,
	        'isSearch'=>$isSearch,
	    ]) ;
	    return $this->fetch('list') ;
	}


	/**
	 * 确认账单
	 * return json
	 */
	public function confirm () {


	    $ids = input('id',0);
	    if(!$ids){
	        return getJsonStrError('不合法的请求');
	    }


	    $data = db('erp_syszd')->where('id','exp','in ('.$ids.')')->field('Vc_orderSn,Vc_goods_breed,Vc_goods_material,Vc_goods_spec,Vc_goods_factory')->select();
		
	    if(!$data){
	        return getJsonStrError('请重新确认');
	    }

	    $insrow = db('sm_syszd')->insertAll($data);
	    if($insrow){

	        return getJsonStrSuc('确认成功');
	    }else{

	        return getJsonStrError('确认失败');
	    }

	}
    /**
     * 导出项目账单excel
     */
	public function  exportexl(){
	    
	    
	    
	    
	    $uid = $this->getSessionUid();
	    $map = [];
	    $map['a.I_userID']=$uid;
	    $where['Dt_arrived'] = ['exp','is not null'];
	    
	    $type = input('type/d',4);
	    $exportType = input('exportType/d',4);
	    $orderStatus = input('orderStatus/s','');
	    $industryId = input('industryId/s','');
	    $projId = input('projId/d',-1);
	    $keyword = input('keyword/s','');
	    
	    switch ($exportType){
	        case 1:
	            $where['Vc_billstatus'] = '已还款';//已还款
	            $map['ezd.Vc_billstatus'] ='已还款';
	            break;
	        case 2:
	            $where['Vc_billstatus'] = '待还款';//待还款
	            $map['ezd.Vc_billstatus'] ='待还款';
	            break;
	        case 3:
	            $where['Vc_billstatus'] = '已逾期';//已逾期
	            $map['ezd.Vc_billstatus'] ='已逾期';
	            break;
	    
	    }
	    switch ($type){
	        case 1:
	            $where['Vc_billstatus'] = '已还款';//已还款
	            $map['ezd.Vc_billstatus'] ='已还款';
	            break;
	        case 2:
	            $where['Vc_billstatus'] = '待还款';//待还款
	            $map['ezd.Vc_billstatus'] ='待还款';
	            break;
	        case 3:
	            $where['Vc_billstatus'] = '已逾期';//已逾期
	            $map['ezd.Vc_billstatus'] ='已逾期';
	            break;
	    
	    }
	    
	    //订单状态搜索
	    if(array_key_exists($orderStatus,$this->billModel->statusArray)){
	    
	        $map['ezd.Vc_billstatus'] = $orderStatus;
	    
	        $where['Vc_billstatus'] = $orderStatus;
	    
	        $param['orderStatus'] = $orderStatus;
	    }
	    //行业搜索
	    if($industryId){
	    
	        $where['Vc_industry'] = $industryId;
	        $map['ezd.Vc_industry'] = $industryId;
	        $param['industryId'] = $industryId;
	    }
	    
	    
	    //项目搜索
	    if($projId>0){
	    
	        $map['a.I_projectID'] = $projId;
	    
	        $param['projId'] = $projId;
	    }
	    
	    //关键词搜索
	    
	    if($keyword){
	    
	        $map['a.Vc_orderSn|ezd.Vc_goods_breed|ezd.Vc_goods_material|ezd.Vc_goods_spec|ezd.Vc_goods_factory'] = ['like','%'.trim($keyword).'%'];
	        $where['pol.Vc_orderSn|pol.Vc_goods_breed|pol.Vc_goods_material|pol.Vc_goods_spec|pol.Vc_goods_factory'] = ['like','%'.trim($keyword).'%'];
	    
	        $param['keyword'] = $keyword;
	    }
	    
	    $idarr1=[];
	    $rs1 = $this->billModel->getBAllQuery($map,'a.id')->group('a.Vc_orderSn')->select();
	    
	    if($rs1){
	        foreach($rs1 as $v){
	            $idarr1[]=$v['id'];
	        }
	    }
	    $brr = $idarr1;
	    
	    if($brr){
	        if(count($brr)>1){
	            $idstr=implode(',',$brr);
	            $sqlwhere['id']=['exp','in ('.$idstr.')'];
	        }else{
	            $sqlwhere['id']=$brr['0'];
	        }
	    }else{
	        $sqlwhere['id']=0;
	    }
	    
	    // 	    dump($sqlwhere);
	    
	    $param['type']=$type;
	    // 	    $list = $this->billModel->getMQuery($sqlwhere)->paginate(2,false,['query'=>$param]);
	    $list = $this->billModel->where($sqlwhere)->order('Createtime desc')->paginate(15,false,['query'=>$param]);
	    $listdata = [];
	    foreach ($list->items() as $n=>$v ){
	     
	        $where['pol.Vc_orderSn'] = $v['Vc_orderSn'];
	    

	        $billlist =$this->billModel->getGQuery($where,'pol.*,IFNULL(g.I_isconfirm,0) I_isconfirm')->select();
	        if(is_array($billlist)){
	            foreach ($billlist as &$val){
	                $val['D_ordertime'] = $v['Createtime'];
	            }
	            
	        }
	        $listdata = array_merge($listdata,$billlist);
	    }
	    
	    
	    
// 	    dump($listdata);die;
	    
	    
	    import('ExcelService', EXTEND_PATH, '.php');
	    $excelobj = new \ExcelService();
	    
	    $dat['rs']=$listdata;
	    $dat['rscount']=count($listdata);
	    $dat['fields']=array(); 
	    $dat['fields'][]=array('订单号','Vc_orderSn');
	    $dat['fields'][]=array('下单时间','D_ordertime','date');
	    $dat['fields'][]=array('所属项目','Vc_proj');
	    $dat['fields'][]=array('品名','Vc_goods_breed');
	    $dat['fields'][]=array('材质','Vc_goods_material');
	    $dat['fields'][]=array('规格','Vc_goods_spec');
	    $dat['fields'][]=array('产地','Vc_goods_factory');
// 	    $dat['fields'][]=array('到货重量','N_arrived_weight');
	    $dat['fields'][]=array('到货日期','Dt_arrived','date');
	    $dat['fields'][]=array('还款日期','Dt_repayment','date');
	    $dat['fields'][]=array('垫资金额(元)','N_loan_amount');
	    $dat['fields'][]=array('垫资天数','I_loan_days');
	    $dat['fields'][]=array('垫资利息(元)','N_loan_interest');
	    $dat['fields'][]=array('结算金额(元)','N_settlement');
	    $dat['fields'][]=array('账单状态','Vc_billstatus');
	    $dat['fields'][]=array('确认状态','($Rs[$i][\'I_isconfirm\']==1?\'已确认\':\'未确认\')','other');
	    
	    $filename='账单'.date('YmdHis',time());    //生成的Excel文件文件名
	    $res=$excelobj->export($dat,$filename);
	    
	    
	    
	    
	}


}
