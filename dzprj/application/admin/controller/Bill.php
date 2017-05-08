<?php
namespace app\admin\controller ;
use app\common\model\BillModel;
use think\paginator\driver\Bootstrap;
use app\common\model\page\Mypage;

class Bill extends AdminController {
	private $billModel;
	
	public function _initialize() {
		$this->billModel = new BillModel();
		parent::_initialize();
	}
	
  
    public function index($type=4,$page=1) {
        $map = [];
      //  $map['e.I_userID']=$uid;
        $where['Dt_arrived'] = ['exp','is not null'];
         
        $orderStatus = input('orderStatus/s','');
        $industryId = input('industryId/s','');
        $projId = input('projId/d',-1);
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
        
            $param['projId'] = $projId;
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
//         $list = new Bootstrap($list, $pageSize,$page,false,$total) ;
        $list = new Mypage($list, $pageSize,$page,false,$total) ;
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
        return $this->fetch('list') ;

    }

  
   
    
 

    
    
    
}
