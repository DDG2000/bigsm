<?php
namespace app\admin\controller ;
use think\paginator\driver\Bootstrap;
use app\common\model\UserModel;
use app\common\model\FindCarsModel;

class FindCars extends AdminController {
	private $findCarsModel;
	
	public function _initialize() {
		$this->findCarsModel = new FindCarsModel();
		parent::_initialize();
	}
	
    public function index($page=1) {
       
        $param['keywords'] = input('keywords/s','');
        $param['applystatus'] = input('applystatus/d',-1);
    	$pages = $this->findCarsModel->getPage($page,$param) ;
    	 $this->assign([
	       
	        'findcars' =>$this->findCarsModel,
	        'page' =>$pages,
	        'param'=>$param,
    	     
	    ]) ;
    	return $this->fetch("index") ;

    }

  

    public function  edit(){
//
//        if($this->request->isPost()){
//            $id = input('post.id/d',0);
//            $data['I_industryID'] = input('post.I_industryID/d',0);
//            $data['I_status'] = input('post.I_status/d',0);
//            $uprow = db('sm_findcars')->where('id',$id)->update($data);
//            if($uprow > 0){
//                $this->addManageLog('找车申请审核', '认证审核了id为'.$id.'的找车申请');
//                return getJsonStrSuc('审核成功');
//            }else{
//                return getJsonStrError('审核失败');
//            }
//
//        }else{
        
            $id = $this->request->get('id',0);
            if(!$id){
                $this->error('信息未选择');
            }
            $data =  $this->findCarsModel->getById($id);
            $goodsList = $this->findCarsModel->getCarsListById($id);
            $this->assign([
                'findCars' =>$this->findCarsModel,
                'data'=>$data,
                'goodsList'=>$goodsList,
            ]) ;
            return $this->fetch();
        }
        
//    }


    /**
     * 快速审核
     * @return mixed
     */
    public function  check(){
        $id = input('get.id/d',0);
        if(!$id){return getJsonStrSuc('参数1获取失败');}
        $check = input('get.check/d',0);
        if(!$check){return getJsonStrSuc('参数2获取失败');}
        if($check==1){
            $data['I_status']=FindCarsModel::STATUS_PASS;
        }elseif($check==2){
            $data['I_status']=FindCarsModel::STATUS_REJECT;
        }
        $uprow = db('sm_findcars')->where('id',$id)->update($data);
        if($uprow > 0){
            $this->addManageLog('找车申请审核', '认证审核了id为'.$id.'的找车申请');
            return getJsonStrSuc('审核完成');
        }else{
            return getJsonStrError('审核失败');
        }
    }

    
    
    
}
