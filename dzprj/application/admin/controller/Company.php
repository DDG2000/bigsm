<?php
namespace app\admin\controller ;
use think\paginator\driver\Bootstrap;
use app\common\model\UserModel;

use think\db\Query;
use \think\Request;
use GuzzleHttp\json_encode;
class Company extends AdminController {
	private $userModel;
	
	public function _initialize() {
		$this->userModel = new UserModel();
		parent::_initialize();
	}
	
	
	/**
	 * 获取erp中的公司信息
	 * @return \think\response\Json
	 */
	public function getCompanyList($keyword='') {
	    //模糊查询5条公司信息
	    $data=[];
	    $where['Vc_name'] =['like','%'.trim($keyword).'%'];
	    
	    $data= db('erp_company')->where($where)->limit(5)->column('Vc_name');
	     
	    return json($data);
	}
	
	
	
    public function index($page=1) {
       
        $param['uname'] = input('uname/s','');
        $param['certifyStatus'] = input('certifyStatus/d',0);
    	$pages = $this->userModel->getCompanyPage($page,$param) ;
    	 $this->assign([
	       
	        'user' =>$this->userModel,
	        'page' =>$pages,
	        'param'=>$param,
    	     
	    ]) ;
    	return $this->fetch("index") ;

    }

  
   
   
    public function  edit(){
        
        if($this->request->isPost()){
            $id = input('post.id/d',0);
            $company= input('post.company/s','');
            $I_status = input('post.approved/d',0);
            $Vc_erp_name = input('post.Vc_erp_name/s','');
            
            if(!$id){
                return getJsonStrError('信息未选择');
            }
            
            if(!$I_status){
                return getJsonStrError('未选择审核状态！');
            }
            if($I_status==2){//审核不通过
                
                $I_userID = db('sm_user_company')->where(['id'=>$id])->value('I_userID');
                
                $find = db('se_project')->where(['I_userID'=>$I_userID])->find();
                if($find){
                    
                return getJsonStrError('该会员已创建项目，不能审核为不通过！');
                }
                
            }
            
            
            $data = array();
            
            if($I_status==3){//审核通过
                if(!$Vc_erp_name){
                    return getJsonStrError('未填写公司实际名称！');
                }
                
                $cda = db('erp_company')->where('Vc_name',trim($Vc_erp_name))->find();
                if(!$cda){
                    return getJsonStrError('该公司不存在，或erp暂未同步到该数据！');
                }
           
            $data['Vc_companycode'] = $cda['Vc_companycode'];
            $data['Vc_erp_name'] = $cda['Vc_name'];
            $data['Vc_erp_shortname'] = $cda['Vc_shortname'];
            $data['Vc_erp_country'] = $cda['Vc_country'];
            $data['Vc_erp_area'] = $cda['Vc_area'];
            $data['Vc_erp_addr'] = $cda['Vc_addr'];
            $data['Vc_erp_tel'] = $cda['Vc_tel'];
            $data['Vc_erp_legalman'] = $cda['Vc_legalman'];
            $data['Vc_erp_contact'] = $cda['Vc_contact'];
            $data['Vc_erp_taxnum'] = $cda['Vc_taxnum'];
            $data['Vc_erp_billaddr'] = $cda['Vc_billaddr'];
            $data['Vc_erp_bank'] = $cda['Vc_bank'];
            $data['Vc_erp_account'] = $cda['Vc_account'];
            $data['I_erp_issupplier'] = $cda['I_issupplier'];
            $data['I_erp_iscustomer'] = $cda['I_iscustomer'];
             }
            $data['I_status'] = $I_status;
            //$uprow = db('sm_user_company')->where('id',$id)->setField(array('I_status'=>$I_status));
            $uprow = db('sm_user_company')->where('id',$id)->update($data);
            if($uprow > 0){
                $this->addManageLog('会员认证审核', '认证审核了公司名为'.$company.'的认证申请');
                return getJsonStrSuc('审核成功');
            }else{
                
                return getJsonStrError('审核失败');
            }
            
        }else{
        
            $id = $this->request->get('id',0);
            if(!$id){
                $this->error('信息未选择');
            }
            $data =  $this->userModel->getCompanyById($id);
            $this->assign([
                'user' =>$this->userModel,
                'data'=>$data,
            ]) ;
            return $this->fetch();
        }
        
    }
   

   

    
    
    
}
