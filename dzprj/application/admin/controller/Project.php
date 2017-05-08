<?php
namespace app\admin\controller ;
use think\paginator\driver\Bootstrap;
use app\common\model\message\MessageService;
use app\common\model\ProjectModel;
use think\Validate;
use app\common\model\BillModel;
use app\common\model\OrderModel;
use app\common\model\page\Mypage;

class Project extends AdminController {
	private $projectModel,$messageService;
	
	public function _initialize() {
		$this->projectModel = new ProjectModel();
		$this->orderModel = new OrderModel();
		$this->messageService = new MessageService();
		$this->billModel = new BillModel();
		parent::_initialize();
	}
	
    public function index($page=1) {
       
        $param['uname'] = input('uname/s','');
        $param['projStatus'] = input('projStatus/d',-1);
        $where = [];
        if(iseta($param,'uname')){
             
            $where['a.Vc_name|ea.Vc_name|h.Vc_applicantName|h.Vc_erp_name'] =['like','%'.trim($param['uname']).'%'];
             
        }
         
        if(array_key_exists($param['projStatus'],$this->projectModel->statusArray)){
        
            $where['a.I_status'] = $param['projStatus'];
        }
        
        
        $pageSize=10;
    	$pages = $this->projectModel->getMQuery($where)->page($page, $pageSize )->select();
    	$data = $this->projectModel->getMQuery($where)->select();
    	$total = count($data);
    	
//     	$pages = new Bootstrap($pages, $pageSize,$page,false,$total) ;
    	$pages = new Mypage($pages, $pageSize,$page,false,$total) ;
    	
    	 $this->assign([
	       
	        'proj' =>$this->projectModel,
	        'page' =>$pages,
	        'param'=>$param,
    	     
	    ]) ;
    	return $this->fetch("index") ;

    }

  
    /**
     * 获取项目对应公司在平台的所有认证用户
     * @return \think\response\Json
     */
    public function getUserList ($cname=0) {
        
        $where['Vc_code']=trim($cname);
        $rs = db('erp_project')->where($where)->find();
        if($rs){
           
           $data = db('sm_user_company')->where('Vc_companycode',$rs['Vc_companycode'])->field('I_userID id,Vc_applicantName name')->select();
            
           if($data){
             return getJsonStrSucNoMsg($data);
           }else{
               return getJsonStrError('项目签约公司尚未在平台注册认证通过！');
           }
           
        }else{
            return getJsonStrError('项目编号有误,或暂未同步到该erp数据！');
        }
        
    }
   
    public function  add(){
        
        if($this->request->isPost()){
            $I_userID = input('post.uid/d',0);
            $Vc_contractSn = input('post.Vc_contractSn/s','');
            $Vc_contractfile = input('post.Vc_contractfile/s','');
            
            if(!$I_userID){
                return getJsonStrError('未选择对应平台认证用户！');
            }
            if(!$Vc_contractSn){
                return getJsonStrError('未填写项目编号！');
            }
           
            
            $data = array();
            $data['I_status'] = 2;
            $data['D_checktime'] = date("Y-m-d H:i:s");
            $data['Createtime'] = date("Y-m-d H:i:s");
            $data['I_userID'] = $I_userID;
            $data['I_src'] = 2;
            $data['Vc_Sn'] = date('YmdHis').generateCode(4);
           
            if ($Vc_contractSn){
                
                $cda = db('erp_project')->where('Vc_code',trim($Vc_contractSn))->find();
                if(!$cda){
                    return getJsonStrError('该项目编号不存在，或暂未同步到该erp数据！');
                }
                //判断该合同号是否已绑定其他用户
                 $find = db('se_project')->where('Vc_code',trim($Vc_contractSn))->find();
               // $unfind = $this->projectModel->checkParamUpdate(['Vc_contractSn'=>trim($Vc_contractSn),'id'=>$id]);
                if($find){
                    return getJsonStrError('该项目编号已绑定其他项目！');
                }

                //用户不能重复绑定同一合同号
                $find = db('se_project')->where('Vc_code',trim($Vc_contractSn))->column('I_userID');//
                
                if(in_array($I_userID, $find)){
                    return getJsonStrError('该项目编号已绑定该用户！');
                }
                
                
                
                $data['Vc_code'] = $Vc_contractSn;
                
                
            }
            
            
            if($Vc_contractfile){
                
                $data['Vc_contractfile'] = $Vc_contractfile;
                
            }
            
            
//             $uprow = db('se_project')->where('id',$id)->setField(array('I_status'=>$I_status));
            $uprow = db('se_project')->insertGetId($data);
            if($uprow > 0){
                $this->addManageLog('项目审核', '新增了ID为'.$uprow.'的项目');
                
                $this->messageService->sendAuditSuccess($I_userID, $cda['Vc_name'], date("Y-m-d H:i"),['projId'=>$uprow]);
                
                
                return getJsonStrSuc('新增成功');
            }else{
                
                return getJsonStrError('新增失败');
            }
            
        }else{
        
           
            return $this->fetch();
            
            
        }
        
    }
    public function  addorder(){
        
        if($this->request->isPost()){
            $rules = [
                ['I_projectID','require','参数非法！'],
                ['I_userID','gt:0','未选择用户！'],
                ['I_provinceID','gt:0','未选择省！'],
                ['I_cityID','gt:0','未选择市！'],
                ['I_districtID','gt:0','未选择区！'],
                ['Vc_address','require','未填写详细地址！'],
                ['Vc_contact','require','未填写收货人！'],
                ['D_transport_end','require','未选择运输时间！'],
                ['Vc_phone','require|length:11','手机号未填写|手机号是11位'],
                ['I_industryID','gt:0','未选择行业！']
            
            ];
            //项目信息
            $da=array();
            $da['I_userID']=input('post.I_userID/d',0);
            $da['I_projectID']=input('post.I_projectID/d',0);
            $da['I_industryID']=input('post.I_industryID/d',0);
            $da['I_provinceID']=input('post.I_provinceID/d',0);
            $da['I_cityID']=input('post.I_cityID/d',0);
            $da['I_districtID']=input('post.I_districtID/d',0);
            $da['Vc_address']=input('post.Vc_address/s','');
            $da['Vc_contact']=input('post.Vc_contact/s','');
            $da['Vc_phone']=input('post.Vc_phone/s','');
            $da['D_transport_end']=input('post.D_transport_end/s','');
            //$da['D_transport_end'] = date('Y-m-d H:i:s',strtotime($da['D_transport_end']));
            $da['T_note']=input('post.T_note','');
            $da['Vc_orderSn']=input('post.Vc_orderSn','');
            $projname=input('post.projname','');
            $validate = new Validate($rules);
            $res   = $validate->check($da);
            if(!$res){
                return getJsonStr(500,$validate->getError());
            }
            if(!funcphone($da['Vc_phone'])){
                return getJsonStr(500,'手机号码不正确！');
            }
            //----校验订单号是否属于该项目
            
            $cda = db('erp_systd')->where('Vc_orderSn',trim($da['Vc_orderSn']))->find();
            if(!$cda){
                return getJsonStrError('该订单号不存在，或erp暂未同步到该数据！');
            }
            //比较合同项目名称，不相等则不能提交
            $Vc_projNo = input('post.Vc_projNo/s','');
            if($cda['Vc_projNo']!=$Vc_projNo){
                return getJsonStrError('该订单号不属于该项目！');
            }
            //该订单是否已绑定平台其他订单
            $find = db('se_project_order')->where(['Vc_orderSn'=>$da['Vc_orderSn'],'state'=>1])->find();
            if($find){
                return getJsonStrError('该订单号已绑定平台其他项目！');
            }
           
            
            $da['I_status'] = 2;
            $da['Createtime'] = date('Y-m-d H:i:s');
            $insrow = db('se_project_order')->insertGetId($da);
            if($insrow){
                $this->addManageLog('项目审核', 'ID为'.$da['I_projectID'].'的项目'.$projname.'新增了erp订单号为'.$da['Vc_orderSn'].'的订单');
                
                return getJsonStrSuc('新增成功');
            }else{
                
                return getJsonStrError('新增失败');
            }
            
        }else{
            
            $id = input('id/d',0);
            if(!$id){
                $this->error('不合法的请求');
            }
            $proj = $this->projectModel->getMQuery(['a.id'=> $id])->find();
            $users = db('sm_user_company')->field('I_userID,Vc_applicantName')->where(['Vc_companycode'=>$proj['Vc_companycode']])->select();
            $malls = get_malls();
            $this->assign([
                'malls'=>$malls,
                'data'=>$proj,
                'users'=>$users,
            ]) ;
//             dump($proj);
           
            return $this->fetch();
            
            
        }
        
    }
   
    public function  edit(){
        
        if($this->request->isPost()){
            $id = input('post.id/d',0);
            $I_userID = input('post.uid/d',0);
            $projstatus = input('post.projstatus/s','');
            $projname= input('post.projname/s','');
            $I_status = input('post.approved/d',0);
            $Vc_contractSn = input('post.Vc_code/s','');
            $Vc_contractfile = input('post.Vc_contractfile/s','');
            
            if(!$id){
                return getJsonStrError('不合法的请求');
            }
            
            if(!$I_status){
                return getJsonStrError('未选择审核状态！');
            }
            if($I_status==1){
                
                $findorder = db('se_project_order')->where(['I_projectID'=>$id])->find();
                if($findorder){
                
                return getJsonStrError('该项目已创建订单，不能审核为不通过！');
                }
                
            }
            if($I_status==5){
                if(!$Vc_contractSn){
                return getJsonStrError('未填写项目编号，无法审核为已完成！');
                }
            }
            $data = array();
            $data['I_status'] = $I_status;
            $data['D_checktime'] = date("Y-m-d H:i:s");
            //$proj = db('se_project')->where('id',$id)->find();
             
            
            if($I_status==4){
             $data['D_closetime'] = date("Y-m-d H:i:s");
            }
           
            if ($Vc_contractSn){
                
                $cda = db('erp_project')->where('Vc_code',trim($Vc_contractSn))->find();
                if(!$cda){
                    return getJsonStrError('该项目编号不存在，或erp暂未同步到该数据！');
                }
                //判断该项目编号是否已绑定其他用户
             //  // $find = db('se_project')->where('Vc_contractSn',trim($Vc_contractSn))->find();
                $unfind = $this->projectModel->checkParamUpdate(['Vc_code'=>trim($Vc_contractSn),'id'=>$id]);
                
                if(!$unfind){
                    return getJsonStrError('该项目编号已绑定其他项目！');
                }
 
                //用户不能重复绑定同一合同号
                if(empty($projstatus)){
                    
                    $find = db('se_project')->where('Vc_code',trim($Vc_contractSn))->column('I_userID');//
                    if(in_array($I_userID, $find)){
                        return getJsonStrError('该项目编号已绑定该用户！');
                    }
                    
                }
                
                
                //该合同号对应公司是否和项目用户认证公司对应
                $map['a.id'] = $id;
                $Vc_companycode=$this->projectModel->getProjComQuery($map,'b.Vc_companycode')->value('Vc_companycode');
                if($cda['Vc_companycode']!=$Vc_companycode){
                    return getJsonStrError('用户认证公司未签约此项目！');
                }
                
                $data['Vc_code'] = $Vc_contractSn;

            }
            
            
            if($Vc_contractfile){
                if($Vc_contractfile==-1){
                    
                $data['Vc_contractfile'] = '';
                }else{
                $data['Vc_contractfile'] = $Vc_contractfile;
                    
                }
                
            }
            
//             $uprow = db('se_project')->where('id',$id)->setField(array('I_status'=>$I_status));
            $prestatus = db('se_project')->where('id',$id)->value('I_status');
            $uprow = db('se_project')->where('id',$id)->update($data);
            if($uprow > 0){
                $this->addManageLog('项目审核', '认证审核了ID为'.$id.'的项目');
                $proj =  $this->projectModel->getById($id);
                if($I_status==1){
                    if($prestatus!=1){
                	$this->messageService->sendAuditFail($proj['I_userID'], $proj['Vc_ct_name']==null?$proj['projname']:$proj['Vc_ct_name'],['projId'=>$id]);
                    }
                }
                if($I_status==2){
                    if($prestatus!=2){
                        
                	$this->messageService->sendAuditSuccess($proj['I_userID'], $proj['Vc_ct_name']==null?$proj['projname']:$proj['Vc_ct_name'], date("Y-m-d H:i:s"),['projId'=>$id]);
                    }
                }
                return getJsonStrSuc('审核成功');
            }else{
                
                return getJsonStrError('审核失败');
            }
            
        }else{
        
            $id = $this->request->get('id',0);
            if(!$id){
                $this->error('信息未选择');
            }
            $data =  $this->projectModel->getById($id);
            $contacts = $this->projectModel->getContactById($id);
            $this->assign([
                'model' =>$this->projectModel,
                'data'=>$data,
                'contacts'=>$contacts,
            ]) ;
            return $this->fetch();
        }
        
    }
   
    public  function vieworder(){
        
        /**
         *思路,先筛选出符合条件（主表条件和关联表条件）的se_project_order表的id号,再用分页实现,关联表按二次条件查询
         *1.array_merge;$idstr=implode(',',$brr);array_unique()
         */
        //$uid = $this->getSessionUid();
        $where = $map = [];
//         $where['a.I_userID']=$uid;
        $projId = input('id/d',0);
        $page = input('page/d',1);
        if(!$projId){
            $this->error('参数不合法！');
        }
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
        
            $where['a.Vc_Sn|a.ERP_Sn|a.Vc_goods_class|a.Vc_goods_breed|a.Vc_goods_material|a.Vc_goods_spec|a.Vc_goods_factory'] =['like','%'.trim($keyword).'%'];
            $map['a.Vc_orderSn|pol.Vc_goods_class|pol.Vc_goods_breed|pol.Vc_goods_material|pol.Vc_goods_spec|pol.Vc_goods_factory'] = ['like','%'.trim($keyword).'%'];
            $map3['a.Vc_orderSn|a.Vc_goods_class|a.Vc_goods_breed|a.Vc_goods_material|a.Vc_goods_spec|a.Vc_goods_factory'] = ['like','%'.trim($keyword).'%'];
        
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
            //$map2['e.I_userID']=$uid;
            $rs = $this->orderModel->getOrderlistQuery($map2,'a.id orderid')->group('a.id')->select();
            if($rs){
                foreach($rs as $v){
                    $brr[]=$v['orderid'];
                }
            }
        }else{
             
             
          
            $brr = $this->orderModel->getHomeOrderQuery($where,'a.*')->order(['a.Createtime'=>'desc'])->column('a.id');
        
          
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
//         // 	    dump($sqlwhere);
//         $GoodsJudgePrice = get_judgeprice();
//         $list = $this->orderModel->getOrderProjQuery($sqlwhere,'a.*,b.projname,b.Vc_ct_name')->order('a.Createtime desc')->paginate(2,false,['query'=>$param]);
//         $listdata = [];
     
        $pageSize=10;
        $GoodsJudgePrice = get_judgeprice();
        //         $list = $this->orderModel->getOrderProjQuery($sqlwhere,'a.*,b.projname,b.Vc_ct_name')->order('a.Createtime desc')->paginate(2,false,['query'=>$param]);
        $list = $this->orderModel->getOrderProjQuery($sqlwhere,'a.*,b.projname,b.Vc_ct_name')->order('a.Createtime desc')->page($page, $pageSize )->select();
        
        $total = count($this->orderModel->getOrderProjQuery($sqlwhere,'a.*,b.projname,b.Vc_ct_name')->order('a.Createtime desc')->select());
        
//         $list = new Bootstrap($list, $pageSize,$page,false,$total) ;
        $list = new Mypage($list, $pageSize,$page,false,$total) ;
        $listdata=[];
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
                      //  $v['N_judge_totalprice'] = $v['N_judge_price']*$v['N_plan_weight'];
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
                     //   $v['N_judge_totalprice'] = $v['N_judge_price']*$v['N_plan_weight'];
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
        //$malls = get_malls();
        //货物大类
      //  $goodclass = db('erp_goods_tree')->cache('erp_goods_tree_data',60)->where("state=1 ")->field('Vc_goods_class')->group('Vc_goods_class')->select();
        //所属项目
       // $projs = $this->orderModel->getXLMQuery(['tmp.I_userID'=>$uid])->group('tmp.I_projectID')->select();
        
        $this->assign([
           
            'model'=>$this->orderModel,
            'type'=>$type,
            'pages'=>$list,
            'listdata'=>$listdata,
            'param'=>$param,
            'isSearch'=>$isSearch,
        ]) ;
        return $this->fetch('order') ;
        
        
    }
   

    public function viewbill() {
        $map = [];
        //  $map['e.I_userID']=$uid;
        $where['Dt_arrived'] = ['exp','is not null'];
         
        $type = input('type/d',4);
        $page = input('page/d',1);
        $projId = input('id/d',0);
        if(!$projId){
            $this->error('参数不合法！');
        }
        
        $orderStatus = input('orderStatus/s','');
        $industryId = input('industryId/s','');
        $keyword = input('keyword/s','');
         
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
    
            $param['id'] = $projId;
        }
    
        //关键词搜索
         
        if($keyword){
    
            $map['a.Vc_orderSn|ezd.Vc_goods_breed|ezd.Vc_goods_material|ezd.Vc_goods_spec|ezd.Vc_goods_factory|ep.Vc_name '] = ['like','%'.trim($keyword).'%'];
            $where['Vc_orderSn|Vc_goods_breed|Vc_goods_material|Vc_goods_spec|Vc_goods_factory|Vc_proj'] = ['like','%'.trim($keyword).'%'];
    
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
                $sqlwhere['a.id']=['exp','in ('.$idstr.')'];
            }else{
                $sqlwhere['a.id']=$brr['0'];
            }
        }else{
            $sqlwhere['a.id']=0;
        }
    
         
         
        $param['type']=$type;
        $pageSize=10;
        //         $list = $this->billModel->getMQuery($sqlwhere)->paginate(2,false,['query'=>$param]);
        $list = $this->billModel->getMQuery($sqlwhere)->page($page, $pageSize )->select();
        $total = count($this->billModel->getMQuery($sqlwhere)->select());
        //         dump($sqlwhere);
        //         dump($list);
        //         dump($total);
        $list = new Bootstrap($list, $pageSize,$page,false,$total) ;
        $listdata = [];
        foreach ($list->items() as $n=>$v ){
            $listdata[$n] = $v;
            $where['Vc_orderSn'] = $v['Vc_orderSn'];
             
            $billlist = db('erp_syszd')->where($where)->select();
            //             echo db('erp_syszd')->getLastSql();die;
            //             dump($billlist);
            if($billlist){
                foreach ($billlist as &$val){
                    $find = db('sm_syszd')
                    ->where(['Vc_orderSn'=>$val['Vc_orderSn'],'Vc_goods_breed'=>$val['Vc_goods_breed'],'Vc_goods_material'=>$val['Vc_goods_material']
                        ,'Vc_goods_spec'=>$val['Vc_goods_spec'],'Vc_goods_factory'=>$val['Vc_goods_factory']])
                        ->find();
                     
                    if($find){
                        $val['I_isconfirm'] =1;
                    }else{
                        $val['I_isconfirm'] =0;
                    }
                }
            }
             
            $listdata[$n]['billlist'] = $billlist;
        }
         
         
        //         	    dump($listdata);die;
         
        //行业
        $malls = $this->billModel->getBAllQuery([],'ezd.Vc_industry')->group('ezd.Vc_industry')->select();
    
        //所属项目
        // $projs = $this->billModel->getBAllQuery([],'a.I_projectID,e.Vc_name projname,e.Vc_ct_name ct_projname')->group('a.I_projectID')->select();
    
         
        $this->assign([
            'type'=>$type,
            'model'=>$this->billModel,
            'pages'=>$list,
            'listdata'=>$listdata,
            'malls'=>$malls,
            //             'projs'=>$projs,
            'param'=>$param,
        ]) ;
        return $this->fetch('bill') ;
    
    }
    
    
    
    
}
