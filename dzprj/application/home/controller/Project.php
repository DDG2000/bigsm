<?php
namespace app\home\controller ;

use app\common\model\ProjectModel;

use think\Validate;
use GuzzleHttp\json_encode;
use GuzzleHttp\json_decode;
use app\common\model\OrderModel;
use app\common\model\BillModel;
class Project extends WorkbenchController {

	private $projectModel,$orderModel,$billModel;
	public function __construct() {

		parent::_initialize() ;
		$this->projectModel = new ProjectModel;
		$this->orderModel = new OrderModel();
		$this->billModel = new BillModel();
		parent::__construct();
		$this->check_certify();
	}
	/**
	 * 创建项目
	 * @return html
	 */
	public function create($type=0) {
		//获取cookie数据
		if($type==2 && isset($_COOKIE['projform'])){
			$form=unserialize($_COOKIE['projform']);
			$projcontact=unserialize($_COOKIE['projcontact']);
		}
		if(isset($form)){
			$this->assign([
				'form'=>$form,
				'projcontact'=>$projcontact,
			]);
		}
		$orgclass = db('sm_project_org_class')->where("state=1")->select();
		$uid = $this->getSessionUid();
		$Vc_applicantName =db('sm_user_company')->where(['I_userID'=>$uid])->value('Vc_applicantName');
		$this->assign([
			'uid'=>$uid,
			'orgclass'=>$orgclass,
			'title'=>'创建项目',
			'Vc_applicantName'=>$Vc_applicantName,
		]) ;
		return $this->fetch() ;
	}

	/**
	 * 提交项目
	 * @return \think\response\Json,
	 */
	public  function beforesave(){

		$rules = [
			['I_userID','require','参数非法！'],
			['Vc_name','require','未填写项目名称！'],
			['I_project_org_classID','gt:0','未选择企业类型！'],
			['I_provinceID','gt:0','未选择省！'],
			['I_cityID','gt:0','未选择市！'],
			['I_districtID','gt:0','未选择区！'],
			['Vc_address','require','未填写地址！'],
			['D_start','require','项目周期未选择开始时间！'],
			['D_end','require','项目周期未选择结束时间！'],
			['N_loan_amount','require','未填写垫资额度！'],
			['I_loan_life','gt:0','未填写垫资天数！'],
			['N_usearea','require|gt:0','未填写开发面积！|开发面积不能为0！'],
			['N_weight','require|gt:0','未填写预计用量！|预计用量不能为0！']
// 	        ['I_loantypeID','gt:0','未选择垫资类型！'],
// 	        ['N_loan_rate','require','未填写年化率！']
		];
		//项目
		$da=array();
		$da['I_userID']=input('post.uid/d',0);
		$da['Vc_name']=input('post.Vc_name/s','');
		$da['I_project_org_classID']=input('post.I_project_org_classID/d',0);
		$da['I_provinceID']=input('post.I_provinceID/d',0);
		$da['I_cityID']=input('post.I_cityID/d',0);
		$da['I_districtID']=input('post.I_districtID/d',0);
		$da['province']=input('post.province/s','');
		$da['city']=input('post.city/s','');
		$da['district']=input('post.district/s','');
		$da['Vc_address']=input('post.Vc_address/s','');
		$da['D_start']=input('post.D_start/s','');
		$da['D_end']=input('post.D_end/s','');
		$da['N_loan_amount']=input('post.N_loan_amount','');
		$da['I_loan_life']=input('post.I_loan_life/d',0);
		$da['N_usearea']=input('post.N_usearea','');
		$da['N_weight']=input('post.N_weight','');
		//项目联系人
		$data=array();
		$data['Vc_contactName']=input('post.Vc_contactName/a','');
		$data['Vc_phone']=input('post.Vc_phone/a','');

		$validate = new Validate($rules);
		$res   = $validate->check($da);
		if(!$res){
			return getJsonStr(500,$validate->getError());
		}
		if(strtotime($da['D_start'])>strtotime($da['D_end'])){
			return getJsonStrError('亲,项目结束时间要大些哦!');
		}
		for ($i=0,$j=1;$i<count($data['Vc_contactName']);$i++,$j++){
			if(!$data['Vc_contactName'][$i]){
				return getJsonStr(500,"第".$j."个联系人未输入姓名");
			}
			if(!$data['Vc_phone'][$i] || !funcphone($data['Vc_phone'][$i])){
				return getJsonStr(500,"第".$j."个联系人未输入手机号或手机格式错误");
			}
			$temp['Vc_contactName'] = $data['Vc_contactName'][$i];
			$temp['Vc_phone'] = $data['Vc_phone'][$i];
			$cda[] = (array)$temp;
		}

		$da['Vc_Sn'] = date('YmdHis').generateCode(4);

		$s1 = setcookie("projform",serialize($da), time()+3600);
		$s2 =  setcookie("projcontact",serialize($cda), time()+3600);
		if($s1&&$s2){
			return getJsonStrSuc('保存成功！',[],url('project/save'));
		}else{
			return getJsonStr(500,"保存失败！浏览器禁用了cookie，会影响修改申请的功能~");
		}
	}
	/**
	 * 保存项目
	 * @return \think\response\Json,
	 */
	public  function save(){
		if($this->request->isPost()){
			$rules = [
				['Vc_name','require','未填写项目名称！'],
				['I_project_org_classID','gt:0','未选择企业类型！'],
				['I_provinceID','gt:0','未选择省！'],
				['I_cityID','gt:0','未选择市！'],
				['I_districtID','gt:0','未选择区！'],
				['Vc_address','require','未填写地址！'],
				['D_start','require','项目周期未选择开始时间！'],
				['D_end','require','项目周期未选择结束时间！'],
				['N_loan_amount','require','未填写垫资额度！'],
				['I_loan_life','gt:0','未填写垫资天数！'],
				['N_usearea','require','未填写开发面积！'],
				['N_weight','require','未填写预计用量！'],
				['Vc_Sn','require','不存在项目编号，请重新创建项目！']

			];
			//项目
			$da=array();
			$da['I_userID']=$this->getSessionUid();
			$da['Vc_name']=input('post.Vc_name/s','');
			$da['I_project_org_classID']=input('post.I_project_org_classID/d',0);
			$da['I_provinceID']=input('post.I_provinceID/d',0);
			$da['I_cityID']=input('post.I_cityID/d',0);
			$da['I_districtID']=input('post.I_districtID/d',0);
			$da['Vc_address']=input('post.Vc_address/s','');
			$da['D_start']=input('post.D_start/s','');
			$da['D_end']=input('post.D_end/s','');
			$da['N_loan_amount']=input('post.N_loan_amount','');
			$da['I_loan_life']=input('post.I_loan_life/d',0);
			$da['N_usearea']=input('post.N_usearea','');
			$da['N_weight']=input('post.N_weight','');

	        $da['Vc_Sn'] = input('post.Vc_Sn','');
			
			//项目联系人
			$data=array();
			$data['Vc_contactName']=input('post.Vc_contactName/a','');
			$data['Vc_phone']=input('post.Vc_phone/a','');

			$validate = new Validate($rules);
			$res   = $validate->check($da);
			if(!$res){
				return getJsonStr(500,$validate->getError());
			}
			foreach ($data['Vc_contactName'] as $k=>$val){
			    if(!$val){
			        return getJsonStr(500,"第".($k+1)."个联系人未输入姓名");
			    }
			}
			foreach ($data['Vc_phone'] as $k=>$val){
			    if(!$val|| !funcphone($val)){
			        return getJsonStr(500,"第".($k+1)."个联系人未输入手机号或手机号输入错误");
			    }
			}
			
			//校验项目是否已提交
			$find = db('se_project')->where(['Vc_Sn'=>trim($da['Vc_Sn']),'state'=>1])->find();
			if($find){
			    return getJsonStr(500,'该项目已提交！');
			}
			

			$insrow = $this->projectModel->save($da);
			//echo $this->projectModel->getLastSql();die;
			for ($i=0,$j=1;$i<count($data['Vc_contactName']);$i++,$j++){
				
				$temp['Vc_contactName'] = $data['Vc_contactName'][$i];
				$temp['Vc_phone'] = $data['Vc_phone'][$i];
				$temp['Createtime'] = date("Y-m-d H:i:s");
				$temp['I_projectID'] = $insrow;

				$cda[] = (array)$temp;
			}
			if($insrow){
				$pcrows = db('sm_project_contact')-> insertAll($cda);
				return getJsonStrSuc('保存成功！');
			}else{
				return getJsonStr(500,"保存失败！");
			}
		}else{
		    
		    if(isset($_COOKIE['projform'])){
		        $projform = unserialize($_COOKIE['projform']) ;
		        $projcontact= unserialize($_COOKIE['projcontact']) ;
		        $orgclass = db('sm_project_org_class')->where('state=1')->select();
		        $this->assign([
		            'orgclass'=>$orgclass,
		            'form'=>$projform,
		            'formcontact'=>$projcontact,
		            'title'=>'项目确认',
		        ]) ;
		        return $this->fetch() ;
		    }else{
		        $this->redirect(url('project/listPage'));
		    }
			

		}


	}
	/**
	 * 工作台项目列表
	 * @param number $type
	 */
	public function listpage ($type=4) {


		$uid = $this->getSessionUid();
		$companycode=db('sm_user_company')->where(['I_userID'=>$uid])->value('Vc_companycode');
		$where['h.Vc_companycode'] =$companycode;
		switch ($type){
			case 1:
				$where['a.I_status'] = ['in',[2,3]];//进行中的项目
				break;
			case 2:
				$where['a.I_status'] = 0;//待审核
				break;
			case 3:
				$where['a.I_status'] = ['in',[4,5]];//已完成
				break;
		

		}


// 		$count['inpro'] =db('se_project')->where(" state=1 and I_userID={$uid} and I_status in (2,3) ")->count();
// 		$count['wait'] =db('se_project')->where(" state=1 and I_userID={$uid} and I_status=0 ")->count();
// 		$count['finish'] =db('se_project')->where(" state=1 and I_userID={$uid} and I_status in (1,4,5) ")->count();
		$count['inpro'] = $this->projectModel->getHomeMQuery(['a.I_status'=>['in',[2,3]],'h.Vc_companycode'=>$companycode],'count(*)')->value('count(*)');
		$count['wait'] = $this->projectModel->getHomeMQuery(['a.I_status'=>0,'h.Vc_companycode'=>$companycode],'count(*)')->value('count(*)');
		$count['finish'] = $this->projectModel->getHomeMQuery(['a.I_status'=>['in',[4,5]],'h.Vc_companycode'=>$companycode],'count(*)')->value('count(*)');
// 		$count['finish'] = $this->projectModel->getHomeMQuery(['a.I_status'=>5,'h.Vc_companycode'=>$companycode],'count(*)')->value('count(*)');
		$count['all'] = $this->projectModel->getHomeMQuery(['h.Vc_companycode'=>$companycode],'count(*)')->value('count(*)');
		$param['type'] = $type;
		// $list = $this->projectModel->where($where)->paginate(5,false,['query'=>$param]);
		$list = $this->projectModel->getMQuery($where)->paginate(5,false,['query'=>$param]);

		$this->assign([
			'uid'=>$uid,
			'count'=>$count,
			'type'=>$type,
			'list'=>$list,
		]) ;
		return $this->fetch('list') ;
	}


	public  function detail(){
		$uid = $this->getSessionUid();
		$projId = input('projId/d',-1);
		
		
		
		$companycode=db('sm_user_company')->where(['I_userID'=>$uid])->value('Vc_companycode');
		$companyproj = $this->projectModel->getMQuery(['a.id'=>$projId,'h.Vc_companycode'=>$companycode])->find();
		
		$proj = $this->projectModel->getMQuery(['a.id'=>$projId])->find();
		if(!$proj){
		$this->error('不合法的请求');
		}

		//该项目下所有erp订单号
		$Vc_orderSnArr = db('se_project_order')->where(['I_projectID'=>$proj['aid'],'state'=>1])->column('Vc_orderSn');
        //该项目下当前用户所有erp订单号
		$userOrders = $this->orderModel->getHomeOrderQuery(['a.I_projectID'=>$projId,'a.I_userID'=>$uid,'a.ERP_Sn'=>['exp','is not null']])->group('a.ERP_Sn')->column('a.ERP_Sn');
		//dump($userOrders);
		$count=array();
		$count['repay'] =0;
		$count['overdate'] =0;
// 		$count['ordernum'] =db('se_project_order')->where(['I_projectID'=>$proj['aid'],'state'=>1])->count();//针对项目所有订单
		$count['ordernum'] =$this->orderModel->getHomeOrderQuery(['a.I_projectID'=>$projId,'a.I_userID'=>$uid])->count();//
		$count['billnum'] =0;
		$count['confirm'] = 0;
		//已逾期
		$count['overdate']= db('erp_syszd')->where(['Vc_projNo'=>$proj['Vc_code'],'Vc_billstatus'=>'已逾期'])->sum('N_settlement');
		if(is_array($userOrders)){
    		foreach ($Vc_orderSnArr as $val){
    			//待还款
    			$count['repay']+= db('erp_syszd')
    				->where(" Vc_projNo=:projno and Vc_billstatus='待还款' and Vc_orderSn=:orderSn  ")
    				->bind(['projno'=>[$proj['Vc_code'],\PDO::PARAM_STR],'orderSn'=>[$val,\PDO::PARAM_STR]])
    				->sum('N_settlement');
    
    			//已逾期
    		//	$count['overdate']+= db('erp_syszd')->where(['Vc_projNo'=>$proj['Vc_code'],'Vc_billstatus'=>'已逾期','Vc_orderSn'=>$val])->sum('N_settlement');
    
    			//订单数
    			//$count['ordernum']+= db('erp_systd')->where(['Vc_projNo'=>$proj['Vc_code'],'Vc_orderSn'=>$val])->count();
    
    			//账单数
    			//$count['billnum'] += db('erp_syszd')->where(['Vc_projNo'=>$proj['Vc_code'],'Vc_orderSn'=>$val])->count();
    			//待确认：n笔
    
    			//$count['confirm'] += $this->orderModel->getGQuery(['pol.Vc_projNo'=>$proj['Vc_code'],'pol.Vc_orderSn'=>$val,'pol.Vc_orderstatus'=>'已到货'],'count(*)')->value('count(*)')-$this->orderModel->getGQuery(['pol.Vc_projNo'=>$proj['Vc_code'],'pol.Vc_orderSn'=>$val,'g.state'=>1],'count(*)')->value('count(*)');
    
    
    		  }
		}
		if(is_array($userOrders)){
		    
    		foreach ($userOrders as $val){
    		    //账单数
    		    $count['billnum'] += db('erp_syszd')->where(['Vc_projNo'=>$proj['Vc_code'],'Vc_orderSn'=>$val,'Dt_arrived'=>['exp','is not null']])->count();
    		    //待确认订单：n笔
    		    $count['confirm'] += $this->orderModel->getGQuery(['pol.Vc_projNo'=>$proj['Vc_code'],'pol.Vc_orderSn'=>$val,'pol.Vc_orderstatus'=>'已到货'],'count(*)')->value('count(*)')-$this->orderModel->getGQuery(['pol.Vc_projNo'=>$proj['Vc_code'],'pol.Vc_orderSn'=>$val,'g.state'=>1],'count(*)')->value('count(*)');
    		    
    		}
    		
		}
		
		$count['repay']= $count['repay']+$count['overdate'];
		$count['repay'] = formatAmountSimply($count['repay']/10000);
		$count['overdate'] = formatAmountSimply($count['overdate']/10000);
		
		
		
		
		$where = $map = [];
		$where['a.I_userID']=$uid;
		
		
		$type = input('type/d',4);
		$orderStatus = input('orderStatus/d',-2);
		$industryId = input('industryId/d',-1);
		$className = input('className/s','');
// 		$projId = input('projId/d',-1);
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
		   // $isSearch = 1;
		    	
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
		
// 		    $where['a.Vc_Sn|a.ERP_Sn|a.Vc_goods_class|a.Vc_goods_breed|a.Vc_goods_material|a.Vc_goods_spec|a.Vc_goods_factory'] =['like','%'.trim($keyword).'%'];
// 		    $map['a.Vc_orderSn|pol.Vc_goods_class|pol.Vc_goods_breed|pol.Vc_goods_material|pol.Vc_goods_spec|pol.Vc_goods_factory'] = ['like','%'.trim($keyword).'%'];
// 		    $map3['a.Vc_orderSn|a.Vc_goods_class|a.Vc_goods_breed|a.Vc_goods_material|a.Vc_goods_spec|a.Vc_goods_factory'] = ['like','%'.trim($keyword).'%'];
		
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
		     
		
		}
		 
	
		 
		 
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
// 		                $v['N_judge_totalprice'] = $v['N_ac_price']*$v['N_plan_weight'];
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
// 		                $v['N_judge_totalprice'] = $v['N_ac_price']*$v['N_plan_weight'];
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
		    'proj'=>$companyproj,
		    'param'=>$param,
		    'isSearch'=>$isSearch,
		]) ;
		return $this->fetch() ;

	}

	public  function billdetail(){
		$uid = $this->getSessionUid();
		$projId = input('projId/d',-1);
		
		
		$companycode=db('sm_user_company')->where(['I_userID'=>$uid])->value('Vc_companycode');
		$companyproj = $this->projectModel->getMQuery(['a.id'=>$projId,'h.Vc_companycode'=>$companycode])->find();
		
		$proj = $this->projectModel->getMQuery(['a.id'=>$projId])->find();
		if(!$proj){
		    $this->error('不合法的请求');
		}
		
		//该项目下所有erp订单号
		$Vc_orderSnArr = db('se_project_order')->where(['I_projectID'=>$proj['aid'],'state'=>1])->column('Vc_orderSn');
		//该项目下当前用户所有erp订单号
		$userOrders = $this->orderModel->getHomeOrderQuery(['a.I_projectID'=>$projId,'a.I_userID'=>$uid,'a.ERP_Sn'=>['exp','is not null']])->group('a.ERP_Sn')->column('a.ERP_Sn');
		//dump($userOrders);
		$count=array();
		$count['repay'] =0;
		$count['overdate'] =0;
		// 		$count['ordernum'] =db('se_project_order')->where(['I_projectID'=>$proj['aid'],'state'=>1])->count();//针对项目所有订单
		$count['ordernum'] =$this->orderModel->getHomeOrderQuery(['a.I_projectID'=>$projId,'a.I_userID'=>$uid])->count();//
		$count['billnum'] =0;
		$count['confirm'] = 0;
		//已逾期
		$count['overdate']= db('erp_syszd')->where(['Vc_projNo'=>$proj['Vc_code'],'Vc_billstatus'=>'已逾期'])->sum('N_settlement');
		if(is_array($userOrders)){
		    foreach ($Vc_orderSnArr as $val){
		        //待还款
		        $count['repay']+= db('erp_syszd')
		    				->where(" Vc_projNo=:projno and Vc_billstatus='待还款' and Vc_orderSn=:orderSn  ")
		    				->bind(['projno'=>[$proj['Vc_code'],\PDO::PARAM_STR],'orderSn'=>[$val,\PDO::PARAM_STR]])
		    				->sum('N_settlement');
		
		    }
		}
		if(is_array($userOrders)){
		
		    foreach ($userOrders as $v){
		        //账单数
// 		        $where['Dt_arrived'] = ['exp','is not null'];
		        $count['billnum'] += db('erp_syszd')->where(['Vc_projNo'=>$proj['Vc_code'],'Vc_orderSn'=>$v,'Dt_arrived'=>['exp','is not null']])->count();
		
		        //待确认账单：n笔
		        $count['confirm'] += $this->billModel->getGQuery(['pol.Vc_projNo'=>$proj['Vc_code'],'pol.Vc_orderSn'=>$v,'pol.Dt_arrived'=>['exp','is not null']],'count(*)')->value('count(*)')-$this->billModel->getGQuery(['pol.Vc_projNo'=>$proj['Vc_code'],'pol.Vc_orderSn'=>$v,'g.I_isconfirm'=>1],'count(*)')->value('count(*)');
		        
		    }
		
		}
	

		$count['repay']= $count['repay']+$count['overdate'];
		$count['repay'] = formatAmountSimply($count['repay']/10000);
		$count['overdate'] = formatAmountSimply($count['overdate']/10000);



		$map = [];
		$map['a.I_userID']=$uid;
		$where['Dt_arrived'] = ['exp','is not null'];

		$type = 4;
		$orderStatus = input('orderStatus/s','');
		$industryId = input('industryId/s','');
		$keyword = input('keyword/s','');
        
		
		$isSearch = 0;
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
		$list = $this->billModel->getOrderProjQuery($sqlwhere,'a.*,b.projname,b.Vc_ct_name')->paginate(2,false,['query'=>$param]);
		$listdata = [];
		foreach ($list->items() as $n=>$v ){

			$listdata[$n] = $v;
			$where['pol.Vc_orderSn'] = $v['Vc_orderSn'];

			$billlist =$this->billModel->getGQuery($where,'pol.*,IFNULL(g.I_isconfirm,0) I_isconfirm')->select();


			$listdata[$n]['billlist'] = $billlist;

		}


		//行业
		$malls = $this->billModel->getBAllQuery(['a.I_userID'=>$uid],'ezd.Vc_industry')->group('ezd.Vc_industry')->select();

		//所属项目
		$projs = $this->billModel->getBAllQuery(['a.I_userID'=>$uid],'a.I_projectID,e.Vc_name projname,ep.Vc_name ct_projname')->group('a.I_projectID')->select();



		$this->assign([
			'count'=>$count,
// 	        'type'=>$type,
			'model'=>$this->billModel,
			'list'=>$list,
			'listdata'=>$listdata,
			'malls'=>$malls,
			'projs'=>$projs,
			'proj'=>$companyproj,
			'param'=>$param,
		    'isSearch'=>$isSearch,
		]) ;
		return $this->fetch() ;




	}






}
