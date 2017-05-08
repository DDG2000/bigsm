<?php
namespace app\admin\controller ;
use think\paginator\driver\Bootstrap;
use app\common\model\UserModel;
use app\common\model\FindMoneyModel;

class FindMoney extends AdminController {
	private $findMoneyModel;
	
	public function _initialize() {
		$this->findMoneyModel = new FindMoneyModel();
		parent::_initialize();
	}
	
    public function index($page=1) {
        $page = $this->request->get('page',1) ;
        $param['keywords'] = input('keywords/s','');
        $param['applystatus'] = input('applystatus/d',-1);
    	$pages = $this->findMoneyModel->getPage($page,$param) ;
        $this->assign([
	        'findmoney' =>$this->findMoneyModel,
	        'page' =>$pages,
	        'param'=>$param,
	    ]) ;
    	return $this->fetch("index") ;
    }
    public function getTels($id=0) {
        $id= input('id/d',0);
        if($id==0){return getJsonStrError('参数错误');}
        $tels=db('sm_findmoney_tel')->where(" state=1 and I_findmoneyID={$id}")->select();
        if($tels){
            return getJsonStrSucNoMsg($tels);
        }else{
            return getJsonStrError('读取数据失败');
        }
    }
  
    /**
     * 获取合同对应公司在平台的所有认证用户
     * @return \think\response\Json
     */
    public function getUserList ($cname=0) {
        
        $where['Vc_contractSn']=trim($cname);
        $rs = db('erp_project')->where($where)->find();
        if($rs){
           
           $data = db('sm_user_company')->where('Vc_companycode',$rs['Vc_companycode'])->field('I_userID id,Vc_applicantName name')->select();
            
           if($data){
             return getJsonStrSucNoMsg($data);
           }else{
               return getJsonStrError('合同签约公司尚未在平台注册认证通过！');
           }
           
        }else{
            return getJsonStrError('合同号有误,或暂未同步到该erp数据！');
        }
        
    }
   

    public function  check(){
            $id = input('get.id/d',0);
            if(!$id){return getJsonStrSuc('参数1获取失败');}
            $check = input('get.check/d',0);
            if(!$check){return getJsonStrSuc('参数2获取失败');}
            if($check==1){
                $data['I_status']=FindMoneyModel::STATUS_PASS;
            }elseif($check==2){
                $data['I_status']=FindMoneyModel::STATUS_REJECT;
            }
            $uprow = db('sm_findmoney')->where('id',$id)->update($data);
            if($uprow > 0){
                $this->addManageLog('存货质押申请审核', '认证审核了id为'.$id.'的存货质押申请');
                return getJsonStrSuc('审核成功');
            }else{
                return getJsonStrError('审核失败');
            }
    }

    public function  edit(){

        if($this->request->isPost()){
            $id = input('post.id/d',0);
            $data['I_status'] = input('post.I_status/d',0);
            $uprow = db('sm_findgoods')->where('id',$id)->update($data);
            if($uprow > 0){
                $this->addManageLog('存货质押申请审核', '认证审核了id为'.$id.'的存货质押申请');
                return getJsonStrSuc('审核成功');
            }else{
                return getJsonStrError('审核失败');
            }

        }else{

            $id = $this->request->get('id',0);
            if(!$id){
                $this->error('信息未选择');
            }
            $data =  $this->findMoneyModel->getById($id);
            $tels = $this->findMoneyModel->getTelsById($id);
            $count=count($tels);
            $this->assign([
                'findgoods' =>$this->findMoneyModel,
                'tels' =>$tels,
                'count' =>$count,
                'data'=>$data,
            ]) ;
            return $this->fetch();
        }

    }
   

    
    
    
}
