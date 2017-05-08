<?php
namespace app\home\controller ;

use app\common\model\OrderModel;
use app\common\model\ProjectModel;
use app\common\controller\MyValidate;
use app\common\model\IndustryModel;
use GuzzleHttp\json_encode;
use think\Validate;
class Order extends WorkbenchController {

	private $orderModel,$projectModel,$industryModel;
	public function __construct() {

		parent::_initialize() ;
		$this->orderModel = new OrderModel();
		$this->industryModel = new IndustryModel();
		$this->projectModel = new ProjectModel();
		parent::__construct();
		$this->check_certify();

	}


	/**
	 * 获取缓存的货物树形结构
	 * @return \think\response\Json
	 */

	public  function getGoodsTree(){

	    $arr = get_goodstree();
	    return json($arr);

	}



	
	/**
	 * 工作台-采购下单-项目列表形式
	 * @return html
	 */
	public function createAll () {


	    $uid = $this->getSessionUid();
	    //
	    $companycode=db('sm_user_company')->where(['I_userID'=>$uid])->value('Vc_companycode');
	    $where['h.Vc_companycode'] = $companycode;
	    
	    
	    $projs = $this->projectModel->getMQuery(['h.Vc_companycode'=>$companycode,'a.I_status'=>['exp','in (2,3)'],'ea.N_usable_loan'=>['exp','>0']])->select();
	    $isedit = input('isedit/d',0);//是否修改订单
	    $form =$goods= [];
	    if($isedit){
	        if($_COOKIE['orderform']){
	    
	            $form = (array)json_decode($_COOKIE['orderform']);
	            $goods= json_decode($_COOKIE['ordergoods'],true) ;
	        }
	    }
	    $GoodsJudgePrice = get_judgeprice();
	    $malls = get_malls();
	    $this->assign([
	        'uid'=>$uid,
			'GoodsJudgePrice'=>$GoodsJudgePrice,
	        'projs'=>$projs,
	        'malls'=>$malls,
	        'industryModel'=>$this->industryModel,
			'title'=>'采购下单',
	        'form'=>$form,
	        'goods'=>$goods,
	    ]) ;
		return $this->fetch('creates') ;
	}
	
	
	
	
// 	public function createAll () {


// 	    $uid = $this->getSessionUid();

// 	    $projs = $this->projectModel->getMQuery(['a.I_userID'=>$uid,'a.I_status'=>['exp','in (2,3)']])->select();

// 	   $GoodsJudgePrice = get_judgeprice();
// 	    $malls = get_malls();
// 	    $this->assign([
// 	        'uid'=>$uid,
// 			'GoodsJudgePrice'=>$GoodsJudgePrice,
// 	        'projs'=>$projs,
// 	        'malls'=>$malls,
// 			'title'=>'采购下单',
// 	    ]) ;
// 		return $this->fetch('creates') ;
// 	}
	/**
	 * 创建订单-项目下单
	 * @return html
	 */
	public function create ($id) {
	
	
	    $uid = $this->getSessionUid();
// 	    $proj = $this->projectModel->getUserProjectById($uid,$id);
	    $proj = $this->projectModel->getById($id);
	    $contacts = $this->projectModel->getContactById($id);
	    $isedit = input('isedit/d',0);//是否修改订单
	    if(!$proj){
	        $this->error("不合法的请求");
	    }
	    if(in_array($proj['I_status'], [0,1,4,5])){
	        $this->error("项目审核通过后，方可下单！");
	    }
// 	    dump($proj);
	    if(($proj['N_usable_loan'])<=0){
	        $this->error("项目可用余额不足！");
	    }
	    
	    
	    $form =$goods= [];
	    if($isedit){
	        if($_COOKIE['orderform']){
	
	            $form = (array)json_decode($_COOKIE['orderform']);
	            $goods= (array)json_decode($_COOKIE['ordergoods'],true) ;
	        }
	    }
	
	    $GoodsJudgePrice = get_judgeprice();
	    $malls = get_malls();
	    $this->assign([
	        'uid'=>$uid,
	        'data'=>$proj,
	        'form'=>$form,
	        'goods'=>$goods,
	        'GoodsJudgePrice'=>$GoodsJudgePrice,
	        'industryModel'=>$this->industryModel,
	        //     'projs'=>$projs,
	        'malls'=>$malls,
	        'title'=>'采购下单',
	    ]) ;
	    return $this->fetch() ;
	}

	/**
	 * 提交项目下单订单
	 * @return Json
	 */
	public  function beforesave(){
	    // 	    dump(get_ordernum());
	    $rules = [
	        ['I_projectID','require','参数非法！'],
	        ['I_provinceID','gt:0','未选择省！'],
	        ['I_cityID','gt:0','未选择市！'],
	        ['I_districtID','gt:0','未选择区！'],
	        ['Vc_address','require','未填写详细地址！'],
	        ['Vc_contact','require','未填写收货人！'],
	        ['Vc_phone','require|phone:1','手机号未填写|手机号格式不正确'],
	        ['I_industryID','gt:0','未选择行业！'],
	        ['D_transport_end','require','未选择期望到货时间！']

	    ];
	    //项目信息
	    $da=array();
	    $da['I_projectID']=input('post.I_projectID/d',0);
	    $da['projname']=input('post.projname/s','');
	    $da['pagesrc']=input('post.pagesrc/s','');
	    $da['I_industryID']=input('post.I_industryID/d',0);
	    $da['I_provinceID']=input('post.I_provinceID/d',0);
	    $da['I_cityID']=input('post.I_cityID/d',0);
	    $da['I_districtID']=input('post.I_districtID/d',0);
	    $da['province']=input('post.province/s','');
	    $da['city']=input('post.city/s','');
	    $da['district']=input('post.district/s','');
	    $da['Vc_address']=input('post.Vc_address/s','');
	    $da['Vc_contact']=input('post.Vc_contact/s','');
	    $da['Vc_phone']=input('post.Vc_phone/s','');
// 	    $da['D_transport_start']=input('post.D_transport_start/s','');
	    $da['D_transport_end']=input('post.D_transport_end/s','');
	   
	    $da['T_note']=input('post.T_note',0);
	    $da['N_judge_totalprice']=input('post.N_judge_totalprice');
	    $da['T_judge_info']=input('post.T_judge_info','');
	    $da['Vc_Sn'] = get_ordernum();
	    //采购信息
	    $data=array();
	    $data['Vc_goods_code']=input('post.Vc_goods_code/a','');
	    $data['Vc_goods_name']=input('post.Vc_goods_name/a','');
	    $data['N_plan_weight']=input('post.N_plan_weight/a','');
	    $data['Vc_goods_uint']=input('post.Vc_unit/a','');

	    $validate = new MyValidate($rules);
	    $res   = $validate->check($da);
	    if(!$res){
	        return getJsonStr(500,$validate->getError());
	    }
	    $da['D_transport_end'] = date('Y-m-d H:i:s',strtotime($da['D_transport_end']));
	    if(!funcphone($da['Vc_phone'])){
	        return getJsonStr(500,'手机号码不正确！');
	    }

		$cda = array();
		//总价 总量 吨总价 件总价 吨总量 件总量
		$totaolPrice=$totalNum=$totalTun=$totalPice=$numTun=$numPice=0;
		$judgePrice = db('configure')->where('code','GoodsJudgePrice')->value('value');
		for ($i=0,$j=1;$i<count($data['Vc_goods_code']);$i++,$j++){
			if(!$data['Vc_goods_code'][$i]){
				return getJsonStr(500,"第".$j."个货物未选择");
			}
			if(!$data['N_plan_weight'][$i]){
				return getJsonStr(500,"第".$j."个货物未填写数量");
			}
			if(!$data['Vc_goods_uint'][$i]){
				return getJsonStr(500,"第".$j."个货物未选择单位");
			}
			$temp['Vc_goods_code'] = $data['Vc_goods_code'][$i];
			$temp['Vc_goods_name'] = $data['Vc_goods_name'][$i];
			$temp['N_plan_weight'] = $data['N_plan_weight'][$i];
			$temp['Vc_goods_uint'] = $data['Vc_goods_uint'][$i];
			$temp['N_judge_totalprice'] = $judgePrice*$temp['N_plan_weight'];
			$totaolPrice+=$temp['N_judge_totalprice'];
			$totalNum+=$temp['N_plan_weight'];
			if($temp['Vc_goods_uint']=='吨'){
				$totalPice+=$temp['N_judge_totalprice'];
				$numTun+=$temp['N_plan_weight'];
			}
			if($temp['Vc_goods_uint']=='件'){
				$totalPice+=$temp['N_judge_totalprice'];
				$numPice+=$temp['N_plan_weight'];
			}
			$cda[] = $temp;
		}
		$da['totalNum']=$totalNum;
		$da['totalTun']=$totalTun;
		$da['totalPice']=$totalPice;
		$da['numTun']=$numTun;
		$da['numPice']=$numPice;
	    $s1 = setcookie("orderform",json_encode($da));
		// var_dump($);
	    $s2 =  setcookie("ordergoods",json_encode($cda));
	    // $_COOKIE['']
	    if($s1&&$s2){

	        return getJsonStrSuc('保存成功！',[],url('order/save'));

	    }else{

	        return getJsonStr(500,"保存失败！浏览器禁用了cookie，会影响修改订单的功能~");

	    }


	}
	/**
	 * 保存项目下单订单
	 * @return Json
	 */
	public  function save(){
	    // 	    dump(get_ordernum());
	    if($this->request->isPost()){
	    $rules = [
	        ['I_projectID','require','参数非法！'],
	        ['I_provinceID','gt:0','未选择省！'],
	        ['I_cityID','gt:0','未选择市！'],
	        ['I_districtID','gt:0','未选择区！'],
	        ['Vc_address','require','未填写详细地址！'],
	        ['Vc_contact','require','未填写收货人！'],
	        ['Vc_phone','require|length:11','手机号未填写|手机号是11位'],
	        ['I_industryID','gt:0','未选择行业！'],
	        ['Vc_Sn','require','不存在订单号，请重新下单！']

	    ];
	    //项目信息
	    $da=array();
	    $da['I_projectID']=input('post.I_projectID/d',0);
	    $da['I_industryID']=input('post.I_industryID/d',0);
	    $da['I_provinceID']=input('post.I_provinceID/d',0);
	    $da['I_cityID']=input('post.I_cityID/d',0);
	    $da['I_districtID']=input('post.I_districtID/d',0);
	    $da['Vc_address']=input('post.Vc_address/s','');
	    $da['Vc_contact']=input('post.Vc_contact/s','');
	    $da['Vc_phone']=input('post.Vc_phone/s','');
// 	    $da['D_transport_start']=input('post.D_transport_start/s','');
	    $da['D_transport_end']=input('post.D_transport_end/s','');
	    $da['D_transport_end'] = date('Y-m-d H:i:s',strtotime($da['D_transport_end']));
	    $da['T_note']=input('post.T_note',0);
	    $da['N_judge_totalprice']=input('post.N_judge_totalprice');
	    $da['T_judge_info']=input('post.T_judge_info','');
	    $da['Vc_Sn'] = input('post.Vc_Sn','');
	    
	    $da['I_userID']= $this->getSessionUid();
	    //采购信息
	    $data=array();
	    $data['Vc_goods_code']=input('post.Vc_goods_code/a','');
	    $data['N_plan_weight']=input('post.N_plan_weight/a','');
	    $data['Vc_goods_uint']=input('post.Vc_goods_uint/a','');

	    $validate = new Validate($rules);
	    $res   = $validate->check($da);
	    if(!$res){
	        return getJsonStr(500,$validate->getError());
	    }
	    if(!funcphone($da['Vc_phone'])){
	        return getJsonStr(500,'手机号码不正确！');
	    }

	    foreach ($data['Vc_goods_code'] as $val){
	        if(!$val){
	            return getJsonStr(500,'货物未选择');
	        }
	    }
	    foreach ($data['N_plan_weight'] as $val){
	        if(!$val){
	            return getJsonStr(500,'有数量未填写');
	        }
	    }
	    foreach ($data['Vc_goods_uint'] as $val){
	        if(!$val){
	            return getJsonStr(500,'未选择单位');
	        }
	    }
	    
        //校验订单号是否已提交
        $find = db('se_project_order')->where(['Vc_Sn'=>trim($da['Vc_Sn']),'state'=>1])->find();
	    if($find){
	        return getJsonStr(500,'该订单已提交！');
	    }

	    $insrow = $this->orderModel->save($da);

	    $cda = array();
	    for ($i=0;$i<count($data['Vc_goods_code']);$i++){
	        $temp['I_orderID'] = $insrow;
	        $temp['Vc_goods_code'] = $data['Vc_goods_code'][$i];

	        $goodsArr = db('erp_goods_tree')->where('Vc_goods_code',$temp['Vc_goods_code'])->find();
	        $temp['Vc_goods_type'] = $goodsArr['Vc_goods_type'];
	        $temp['Vc_goods_class'] = $goodsArr['Vc_goods_class'];
	        $temp['Vc_goods_breed'] = $goodsArr['Vc_goods_breed'];
	        $temp['Vc_goods_material'] = $goodsArr['Vc_goods_material'];
	        $temp['Vc_goods_spec'] = $goodsArr['Vc_goods_spec'];

	        $temp['N_plan_weight'] = $data['N_plan_weight'][$i];

	        $temp['N_judge_price'] = db('configure')->where('code','GoodsJudgePrice')->value('value');
	        $temp['N_judge_totalprice'] = $temp['N_judge_price']*$temp['N_plan_weight'];

	        $temp['Vc_goods_uint'] = $data['Vc_goods_uint'][$i];
	        $temp['Createtime'] = date("Y-m-d H:i:s");
	        $cda[] = $temp;
	    }
	      if($insrow){
	          
	          $pcrows = db('se_project_orderlist')-> insertAll($cda);

	          //添加项目首次下单时间
	          $ordertime=db('se_project')->where('id',$da['I_projectID'])->value('D_firstorder_time');
	          if(!$ordertime){
	              $t['D_firstorder_time'] = date("Y-m-d H:i:s");
	              db('se_project')->where('id',$da['I_projectID'])->setField($t);
	          }
// 	          setcookie("orderform", "", time() - 3600);
// 	          setcookie("ordergoods", "", time() - 3600);
	          cookie('orderform', null);
	          cookie('ordergoods', null);
	      
	          return getJsonStrSuc('保存成功！');

	      }else{

	          return getJsonStr(500,"保存失败！");

	      }

	    }else{

// 	        $s1 = setcookie("orderform",json_encode($da));
// 	        $s2 =  setcookie("ordergoods",json_encode($data));
            if(isset($_COOKIE['orderform'])){
                $form = json_decode($_COOKIE['orderform']) ;
                $goods= json_decode($_COOKIE['ordergoods'],true) ;
                $malls = db('sm_industry')->where('state',1)->select();
                $this->assign([
                    'malls'=>$malls,
                    'form'=>get_object_vars($form),
                    'goods'=>$goods,
                    'title'=>'采购下单',
                ]) ;
                return $this->fetch() ;
                
            }else{
                $this->redirect(url('order/listpage'));
            }
	        
	       


	    }

	}
	/**
	 * 工作台订单列表-综合搜索
	 * @param number $type
	 * @return html
	 */
	public function listpage ($type=4) {

	    /**
         *思路,先筛选出符合条件（主表条件和关联表条件）的se_project_order表的id号,再用分页实现,关联表按二次条件查询
         *1.array_merge;$idstr=implode(',',$brr);array_unique()
	     */
	    $uid = $this->getSessionUid();
	    $where = $map = [];
	    $where['a.I_userID']=$uid;
// 	    $map['e.I_userID']=$uid;

	    $orderStatus = input('orderStatus/d',-2);
	    $industryId = input('industryId/d',-1);
	    $className = input('className/s','');
	    $projId = input('projId/d',-1);
	    $keyword = input('keyword/s','');

// 	    dump($this->orderModel->statusArray);
		$isSearch = 0;
        //订单状态搜索
	    if(array_key_exists($orderStatus,$this->orderModel->statusArray)){
			$isSearch = 1;

	        switch ($orderStatus){
	            case 0:
	                $where['a.StatusIndex'] = $orderStatus;//待审核
	                break;
	            case 1:
	                $map['pol.Vc_orderstatus'] ='待发货';
	                $where['a.StatusIndex'] = $orderStatus;
	                break;
	            case 2:
	                $map['pol.Vc_orderstatus'] ='已发货';
	                $where['a.StatusIndex'] = $orderStatus;
	                break;
	            case 3:
	                $map['pol.Vc_orderstatus'] ='已到货';
	                $where['a.StatusIndex'] = $orderStatus;
	                break;
	                 
	            case 4:
	                $map2['g.Vc_orderstatus'] ='已确认';
	                break;
	            case -1:
	                //$where['a.I_status'] = $orderStatus;//审核未通过
	                $where['a.StatusIndex'] = $orderStatus;//审核未通过
	                break;
	            case 5:
	               // $where['a.I_status'] = $orderStatus;//已冻结
	                $where['a.StatusIndex'] = $orderStatus;
	                break;
	            case 6:
	               // $where['a.I_status'] = $orderStatus;//已关闭
	                $where['a.StatusIndex'] = $orderStatus;
	                break;
	        
	        }

	        $param['orderStatus'] = $orderStatus;
	    }
	    //行业搜索
	    if($industryId>0){

	        $where['a.I_industryID'] = $industryId;
// 	        $map['a.I_industryID'] = $industryId;

	        $param['industryId'] = $industryId;
			$isSearch = 1;
			
	    }
	    //货物大类搜索

	    if($className){

	        $where['a.Vc_goods_class'] = $className;
	        $map['pol.Vc_goods_class'] = $className;

	        $param['className'] = $className;
			$isSearch = 1;
			
	    }

	    //项目搜索
	    if($projId>0){

	        $where['a.I_projectID'] = $projId;
// 	        $map['a.I_projectID'] = $projId;
	        $map2['a.I_projectID'] = $projId;
	        $param['projId'] = $projId;
			$isSearch = 1;
			
	    }

	    //关键词搜索

	    if($keyword){
	        
            $find = $this->orderModel->getHomeOrderQuery(['a.Vc_Sn'=>['like','%'.trim($keyword).'%'],'a.ERP_Sn'=>['exp','is not null']],'a.Vc_Sn')->find();
	        if(!$find){
	            
	            $where['a.Vc_Sn|a.ERP_Sn|a.Vc_goods_class|a.Vc_goods_breed|a.Vc_goods_material|a.Vc_goods_spec|a.Vc_goods_factory'] =['like','%'.trim($keyword).'%'];
	            $map['a.Vc_orderSn|pol.Vc_goods_class|pol.Vc_goods_breed|pol.Vc_goods_material|pol.Vc_goods_spec|pol.Vc_goods_factory'] = ['like','%'.trim($keyword).'%'];
	            $map3['b.Vc_Sn|a.Vc_orderSn|a.Vc_goods_class|a.Vc_goods_breed|a.Vc_goods_material|a.Vc_goods_spec|a.Vc_goods_factory'] = ['like','%'.trim($keyword).'%'];
	             
	        }else{
	            $where['a.ERP_Sn|a.Vc_goods_class|a.Vc_goods_breed|a.Vc_goods_material|a.Vc_goods_spec|a.Vc_goods_factory'] =['like','%'.trim($keyword).'%'];
	            $map['a.Vc_orderSn|pol.Vc_goods_class|pol.Vc_goods_breed|pol.Vc_goods_material|pol.Vc_goods_spec|pol.Vc_goods_factory'] = ['like','%'.trim($keyword).'%'];
	            $map3['a.Vc_orderSn|a.Vc_goods_class|a.Vc_goods_breed|a.Vc_goods_material|a.Vc_goods_spec|a.Vc_goods_factory'] = ['like','%'.trim($keyword).'%'];
	            
	        }
	        
	        $param['keyword'] = $keyword;
			$isSearch = 1;
			
	    }


	    switch ($type){
	        case 1:
// 	            $where['a.I_status'] = 1;//待发货
	            $map['pol.Vc_orderstatus'] ='待发货';
// 	            $where['pol.Vc_orderstatus'] ='待发货';
	            $where['a.StatusIndex'] = 1;
	            break;
	        case 2:
// 	            $where['a.I_status'] = 2;//已发货
	            $map['pol.Vc_orderstatus'] ='已发货';
	            $where['a.StatusIndex'] =2;
	            break;
	        case 3:
// 	            $where['a.I_status'] = 3;//已送达
	            $map['pol.Vc_orderstatus'] ='已到货';
	            $where['a.StatusIndex'] =3;
	            break;
// 	        case 5:
// 	            $map2['g.Vc_orderstatus'] ='已确认';

// 	            break;

	    }
	    
	    //    数量统计
	    $count['wait'] = $this->orderModel->getHomeOrderQuery(['a.StatusIndex'=>1,'a.I_userID'=>$uid],'count(*) ')->value('count(*)');
	    $count['inpro'] = $this->orderModel->getHomeOrderQuery(['a.StatusIndex'=>2,'a.I_userID'=>$uid],'count(*) ')->value('count(*)');
	    $count['finish'] = $this->orderModel->getHomeOrderQuery(['a.StatusIndex'=>3,'a.I_userID'=>$uid],'count(*) ')->value('count(*)');
	    
	    
	    //数量统计
// 	    $count['wait'] = $this->orderModel->getWQuery(['pol.Vc_orderstatus'=>'待发货','e.I_userID'=>$uid],'count(*) ')->value('count(*)');
// 	    $count['inpro'] = $this->orderModel->getWQuery(['pol.Vc_orderstatus'=>'已发货','e.I_userID'=>$uid],'count(*) ')->value('count(*)');
// 	    $count['finish'] = $this->orderModel->getWQuery(['pol.Vc_orderstatus'=>'已到货','e.I_userID'=>$uid],'count(*) ')->value('count(*)');
	    //$count['other'] =$this->orderModel->getWQuery(['pol.Vc_orderstatus'=>'已确认','e.I_userID'=>$uid],'count(*) ')->value('count(*)');
        //统计平台待发货的订单
//         $waitorders = $this->orderModel->getWQuery(['a.I_status'=>1,'e.I_userID'=>$uid],'a.id')->column('a.id');
//         $erp_waitorders = $this->orderModel->getWQuery(['pol.Vc_orderstatus'=>'待发货','e.I_userID'=>$uid],'a.id')->column('a.id');
// 	    if(is_array($waitorders)){
// 	        foreach ($waitorders as $vid){
// 	            if(in_array($vid, $erp_waitorders))
// 	                continue;
// 	            $count['wait']+=$this->orderModel->getGoodsQuery(['a.I_orderID'=>$vid,'a.I_goods_src'=>1])->count();
// 	        }
	        
// 	    }
	    
	    
	    $brr = [];
	    if(isset($map2['g.Vc_orderstatus'])){//获取已确认的订单id
	        //$rs = db('se_project_orderlist')->where($map2)->field('I_orderID')->group('I_orderID')->select();
	         $map2['a.I_userID']=$uid;
	        $rs = $this->orderModel->getOrderlistQuery($map2,'a.id orderid')->group('a.id')->select();
	        if($rs){
	            foreach($rs as $v){
	                $brr[]=$v['orderid'];
	            }
	        }
	    }else{
	        
	        
// 	    $idarr1=$idarr2=array();
// 	    $rs1 = $this->orderModel->getWQuery($map,'a.id')->group('a.id')->select();
// 	    $rs2 = $this->orderModel->getWQuery($where,'a.id')->group('a.id')->select();
//        // echo $this->orderModel->getLastSql();
      
// 	    if($rs1){
// 	        foreach($rs1 as $v){
// 	            $idarr1[]=$v['id'];
// 	        }
// 	    }
// 	    if($rs2){
// 	        foreach($rs2 as $v){
// 	            $idarr2[]=$v['id'];
// 	        }
// 	    }
	     $brr = $this->orderModel->getHomeOrderQuery($where,'a.*')->order(['a.Createtime'=>'desc'])->column('a.id');
	     
	   //  dump($brr);die;
	    //$brr=array_merge($idarr1,$idarr2);
	    $brr = array_unique($brr);//
	    }
	  //  dump($brr);

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


	    $param['type'] = $type;
// 	    dump($sqlwhere);
	    $GoodsJudgePrice = get_judgeprice();
// 	    $list = $this->orderModel->where($sqlwhere)->paginate(2,false,['query'=>$param]);
	    $list = $this->orderModel->getOrderProjQuery($sqlwhere,'a.*,b.projname,b.Vc_ct_name')->order('a.Createtime desc')->paginate(2,false,['query'=>$param]);
	    $listdata = [];
// 	    dump($list);
	    foreach ($list->items() as $n=>$vo ){


	        $listdata[$n] = $vo;
			$goods=[];
			if(	empty($vo['Vc_orderSn'])){
	        //先拿到平台创建订单货物
	           $map3['a.I_orderID'] = $vo['id'];
	           $map3['a.I_goods_src'] = 1;
            	$goods = $this->orderModel->getGoodsQuery($map3)->select();
			}
            //erp订单货物列表
            if($vo['Vc_orderSn']){
                $goods_erp=array();
                if(isset($map2['g.Vc_orderstatus'])){//已确认

                    $map2['pol.Vc_orderSn'] = $vo['Vc_orderSn'];
                    $goods_erp = $this->orderModel->getOrderlistQuery($map2,'pol.*,IFNULL(g.state,0) I_isconfirm')->select();

                    foreach ($goods_erp as &$v){
                        $v['I_goods_src'] = 2;
                        $v['N_judge_price'] = $GoodsJudgePrice;
                        if($v['Vc_orderstatus']=='已到货'){
                            
                        $v['N_judge_totalprice'] = $v['N_ac_price']*$v['N_plan_weight'];
                        }else{
                            
                        $v['N_judge_totalprice'] = $v['N_judge_price']*$v['N_plan_weight'];
                        }

                    }


                }else{

                $map['pol.Vc_orderSn'] = $vo['Vc_orderSn'];
                $goods_erp =$this->orderModel->getGQuery($map,'pol.*,IFNULL(g.state,0) I_isconfirm')->select();
                foreach ($goods_erp as &$v){
                    $v['I_goods_src'] = 2;
                    $v['N_judge_price'] = $GoodsJudgePrice;
//                     $v['N_judge_totalprice'] = $v['N_ac_price']*$v['N_plan_weight'];
                    if($v['Vc_orderstatus']=='已到货'){
                    
                        $v['N_judge_totalprice'] = $v['N_ac_price']*$v['N_plan_weight'];
                    }else{
                    
                        $v['N_judge_totalprice'] = $v['N_judge_price']*$v['N_plan_weight'];
                    }
                }

                }
                $goods = array_merge($goods,$goods_erp);


            }

            $listdata[$n]['orderlist'] = $goods;
	       //$map['a.id'] = $v['id'];
	       //$listdata[$n]['orderlist'] = $this->orderModel->getWQuery($map)->select();
	    }
	  //  dump($listdata);

	    //行业
	    $malls = get_malls();
	    //货物大类
	    $goodclass = db('erp_goods_tree')->cache('erp_goods_tree_data',60)->where("state=1 ")->field('Vc_goods_class')->group('Vc_goods_class')->select();
	    //所属项目
	   $projs = $this->orderModel->getXLMQuery(['tmp.I_userID'=>$uid])->group('tmp.I_projectID')->select();
	    $this->assign([
	        'uid'=>$uid,
	        'count'=>$count,
	        'model'=>$this->orderModel,
	        'type'=>$type,
	        'list'=>$list,
	        'listdata'=>$listdata,
	        'malls'=>$malls,
	        'goodclass'=>$goodclass,
	        'projs'=>$projs,
	        'param'=>$param,
			'isSearch'=>$isSearch,
	    ]) ;
	    return $this->fetch('list') ;
	}




	/**
	 * 查看物流信息
	 */
	public function expressInfo ($id=0) {

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
	        'model' =>$this->orderModel,
	        'data'=>$data,
	    ]) ;
	    return $this->fetch('express');

	}
	/**
	 * 订单详情
	 */
	public function orderInfo () {

	    $id = $this->request->get('id',0);
	    $odn = $this->request->get('odn',0);
	    $uid = $this->getSessionUid();
	   
	    if(!$id && !$odn){
	        $this->error('不合法的请求');
	    }
	    $goods=[];
	    if ($id) {
	    	$data = $this->orderModel->getMQuery(['a.id'=>$id])->find();
	    	if(!$data['Vc_orderSn']){
	    	    
	       $goods = $this->orderModel->getGoodsQuery(['a.I_orderID'=>$id,'a.I_goods_src'=>1])->select();
	    	}
	       
	    }else {
	    	$data = $this->orderModel->getMQuery(['a.Vc_orderSn'=>$odn,'a.I_userID'=>$uid])->find();
	    }
	    $data['projinfo'] =$this->projectModel->getMQuery(['a.id'=>$data['I_projectID']])->find();
// 	    $data['orderlist'] = $this->orderModel->getWQuery(['pol.Vc_orderSn'=>$data['Vc_orderSn']])->select();
	    $GoodsJudgePrice = get_judgeprice();
	    $goods_erp =[];
	    if($data['Vc_orderSn']){
	        
                $goods_erp =$this->orderModel->getGQuery(['pol.Vc_orderSn'=>$data['Vc_orderSn']],'pol.*,IFNULL(g.state,0) I_isconfirm')->select();
                foreach ($goods_erp as &$v){
                    $v['I_goods_src'] = 2;
                    $v['N_judge_price'] = $GoodsJudgePrice;
//                     $v['N_judge_totalprice'] = $v['N_ac_price']*$v['N_plan_weight'];
                    if($v['Vc_orderstatus']=='已到货'){
                    
                        $v['N_judge_totalprice'] = $v['N_ac_price']*$v['N_plan_weight'];
                    }else{
                    
                        $v['N_judge_totalprice'] = $v['N_judge_price']*$v['N_plan_weight'];
                    }
             }
	    
	    }
	    
	    $expressinfo = $this->orderModel->getExpressLastInfo(['b.Vc_orderSn'=>$data['Vc_orderSn']],'b.*')->limit(1)->find();
	    $goods = array_merge($goods,$goods_erp);
	    $data['orderlist'] =$goods;
// 	    dump($expressinfo);
// 	    dump($data);die;

	    $this->assign([
	        'model' =>$this->orderModel,
	        'data'=>$data,
	        'vo'=>$data,
	        'expressinfo'=>$expressinfo,
	    ]) ;
	    return $this->fetch('info');

	}
	/**
	 * 取消订单
	 * return json
	 */
	public function cancel ($id=0) {

	    $id = $this->request->get('id',0);
	    if(!$id){
	        return getJsonStrError('不合法的请求');
	    }
	    $data['I_status'] = 6;
	    $uprow = $this->orderModel->update($data,['id'=>$id]);
	    if($uprow){

	        return getJsonStrSuc('取消成功');
	    }else{

	        return getJsonStrError('取消失败');
	    }


	}
	/**
	 * 确认订单
	 * return json
	 */
	public function confirm () {


	    $oid = input('oid',0);
	    $id = input('id',0);
	    $orderSn = input('orderSn','');
	    if(!$id){
	        return getJsonStrError('不合法的请求');
	    }
	    if(!$oid){
	        return getJsonStrError('不合法的请求');
	    }
	    //
	    $rs = db('erp_systd')->where(['id'=>$id,'Vc_orderSn'=>$orderSn])->find();

	    if(!$rs){
	        $this->error('数据更新中，请刷新页面后，重新操作！');
	    }

	    $data['I_orderID'] = $oid;
	    $data['Vc_orderSn'] = $rs['Vc_orderSn'];
	    $data['Vc_goods_breed'] = $rs['Vc_goods_breed'];
	    $data['Vc_goods_material'] = $rs['Vc_goods_material'];
	    $data['Vc_goods_spec'] = $rs['Vc_goods_spec'];
	    $data['Vc_goods_factory'] = $rs['Vc_goods_factory'];
	    $data['I_goods_src'] = 2;
	    $data['Vc_orderstatus'] = '已确认';//
	    $data['Createtime'] = date("Y-m-d H:i:s");//
	    $insrow = db('se_project_orderlist')->insertGetId($data);
	    if($insrow){

	        return getJsonStrSuc('确认成功');
	    }else{

	        return getJsonStrError('确认失败');
	    }

	}
	/**
	 * 异常申诉
	 * return json
	 */
	public function appeal () {

	    $map['Vc_orderSn'] = input('Vc_orderSn','');
	    $map['I_type'] = input('I_type',1);
	    $map['Vc_goods_breed'] = input('Vc_goods_breed','');
	    $map['Vc_goods_material'] = input('Vc_goods_material','');
	    $map['Vc_goods_spec'] = input('Vc_goods_spec','');
	    $map['Vc_goods_factory'] = input('Vc_goods_factory','');
	    $map['T_appeal']= input('T_appeal/s','');
	    $map['Createtime']= date("Y-m-d H:i:s");

	    $map['I_userID'] = $this->getSessionUid();
	    if(!$map['Vc_orderSn']){
	        return getJsonStrError('不合法的请求');
	    }
	   
	    if(empty(trim($map['T_appeal']))){
	        return getJsonStrError('未填写申诉内容！');
	    }

	    $insrow = db('sm_order_appeal')->insertGetId($map);
	    if($insrow){

	        return getJsonStrSuc('提交成功');
	    }else{

	        return getJsonStrError('提交失败');
	    }

	}



	/**
	 * 修改订单
	 *
	 */
	public function edit() {

	    $uid = $this->getSessionUid();
	    $GoodsJudgePrice = get_judgeprice();
	    if($this->request->isPost()){
	        $rules = [
	            ['id','gt:0','参数非法！'],
	            ['I_projectID','require','参数非法！'],
	            ['I_provinceID','gt:0','未选择省！'],
	            ['I_cityID','gt:0','未选择市！'],
	            ['I_districtID','gt:0','未选择区！'],
	            ['Vc_address','require','未填写详细地址！'],
	            ['Vc_contact','require','未填写收货人！'],
	            ['Vc_phone','require|length:11','手机号未填写|手机号是11位'],
	            ['I_industryID','gt:0','未选择行业！']

	        ];
	        //项目信息
	        $da=array();
	        $da['id']=input('post.oid/d',0);
	        $da['I_projectID']=input('post.I_projectID/d',0);
	        $da['I_industryID']=input('post.I_industryID/d',0);
	        $da['I_provinceID']=input('post.I_provinceID/d',0);
	        $da['I_cityID']=input('post.I_cityID/d',0);
	        $da['I_districtID']=input('post.I_districtID/d',0);
	        $da['Vc_address']=input('post.Vc_address/s','');
	        $da['Vc_contact']=input('post.Vc_contact/s','');
	        $da['Vc_phone']=input('post.Vc_phone/s','');
// 	        $da['D_transport_start']=input('post.D_transport_start/s','');
	        $da['D_transport_end']=input('post.D_transport_end/s','');
	        $da['D_transport_end'] = date('Y-m-d H:i:s',strtotime($da['D_transport_end']));
	        $da['T_note']=input('post.T_note',0);
	        $da['N_judge_totalprice']=input('post.N_judge_totalprice');
	        $da['T_judge_info']=input('post.T_judge_info','');
// 	       dump($da);die;
	        //采购信息
	        $data=array();
	        $data['Vc_goods_code']=input('post.Vc_goods_code/a','');
	        $data['N_plan_weight']=input('post.N_plan_weight/a','');
	        $data['Vc_goods_uint']=input('post.Vc_goods_unit/a','');

	        $validate = new Validate($rules);
	        $res   = $validate->check($da);
	        if(!$res){
	            return getJsonStr(500,$validate->getError());
	        }
           // dump($data);die;
	        
	        foreach ($data['Vc_goods_code'] as $val){
	            if(!$val){
	                return getJsonStr(500,'未选择货物');
	            }
	        }
	        foreach ($data['N_plan_weight'] as $val){
	            if(!$val){
	                return getJsonStr(500,'有数量未填写');
	            }
	        }
	        foreach ($data['Vc_goods_uint'] as $val){
	            if(!$val){
	                return getJsonStr(500,'未选择单位');
	            }
	        }

	        $uprow = $this->orderModel->update($da);
	        if($uprow){
	        //删除se_project_orderlist以前的数据

	        db('se_project_orderlist')->where(['I_orderID'=>$da['id']])->update(['state'=>0]);


	        $cda = array();
	        for ($i=0;$i<count($data['Vc_goods_code']);$i++){
	            $temp['I_orderID'] = $da['id'];
	            $temp['Vc_goods_code'] = $data['Vc_goods_code'][$i];

	            $goodsArr = db('erp_goods_tree')->where('Vc_goods_code',$temp['Vc_goods_code'])->find();
	            $temp['Vc_goods_type'] = $goodsArr['Vc_goods_type'];
	            $temp['Vc_goods_class'] = $goodsArr['Vc_goods_class'];
	            $temp['Vc_goods_breed'] = $goodsArr['Vc_goods_breed'];
	            $temp['Vc_goods_material'] = $goodsArr['Vc_goods_material'];
	            $temp['Vc_goods_spec'] = $goodsArr['Vc_goods_spec'];

	            $temp['N_plan_weight'] = $data['N_plan_weight'][$i];

	            $temp['N_judge_price'] = $GoodsJudgePrice;
	            $temp['N_judge_totalprice'] = $temp['N_judge_price']*$temp['N_plan_weight'];

	            $temp['Vc_goods_uint'] = $data['Vc_goods_uint'][$i];
	            $temp['Createtime'] = date("Y-m-d H:i:s");
	            $cda[] = $temp;
	        }


	            $pcrows = db('se_project_orderlist')-> insertAll($cda);

	            return getJsonStrSuc('修改成功！',[],url('order/listpage'));

	        }else{

	            return getJsonStr(500,"修改失败！");

	        }


	    }else {

	        $id = $this->request->get('id',0);

	        if(!$id){
	            $this->error('不合法的请求');
	        }
	        $data = $this->orderModel->getMQuery(['a.id'=>$id,'a.I_userID'=>$uid,'a.I_status'=>0])->find();
	        if(!$data){
	            $this->error('未获取到数据');
	        }
	        $goods =db('se_project_orderlist')->where(['I_orderID'=>$data['id'],'state'=>1])->select();
	        	//  dump($data);   dump($goods);die;
	        $projs = $this->projectModel->getMQuery(['a.I_userID'=>$uid,'a.I_status'=>['exp','in (2,3)']])->select();
	        $malls = get_malls();
	        $this->assign([
	            'uid'=>$uid,
	            'data'=>$data,
	            'projs'=>$projs,
	            'goods'=>$goods,
	            'malls'=>$malls,
	            'GoodsJudgePrice'=>$GoodsJudgePrice,
	            'title'=>'修改订单',
	        ]) ;
	        return $this->fetch() ;
	        exit;
	        
	        
	      

	    }






	}
	
	
	/**
	 * ajax是否能再次购买
	 */
     public function  iscanbuy($id){
         
  
             $proj = $this->projectModel->getById($id);
            
             if(!$proj){
              return   getJsonStr("不合法的请求");
             }
             if(in_array($proj['I_status'], [0,1,4,5])){
              return   getJsonStr("项目审核通过后，方可下单！");
             }
             // 	    dump($proj);
             if(($proj['N_usable_loan'])<=0){
              return   getJsonStr("项目可用余额不足！");
             }
         
             return getJsonStrSuc('可以再次购买！',[],url('/order/create',['id'=>$id]));
            
         
     }

}
