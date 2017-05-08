<?php
namespace app\home\controller ;

use app\common\model\UserModel;

use think\Validate;
use think\console\Input;
class Account extends WorkbenchController {
	
	private $userModel;
	public function __construct() {

		parent::_initialize() ;
		$this->userModel = new UserModel;
		parent::__construct();
// 		$this->check_certify();
		
	}
	
	public function index () {
	    
	    $user = $this->userModel->getById($this->getSessionUid());
	    $this->assign([
	        'data'=>$user,
	    ]) ;
		return $this->fetch() ;
	}
	public function alert () {
	    
	    return $this->fetch() ;
	}
	
	
	public  function save(){

	    $rules = [
	        ['id','gt:0','参数非法！'],
	        ['Vc_name','require','未填写用户名！'],
// 	        ['Vc_realname','require','真实姓名不能为空！'],
	        ['I_sex','require','未选择性别！'],
// 	        ['Dt_birthday','require|dateFormat:Y-m-d','未选择生日！|生日格式不正确！'],
	        ['I_provinceID','gt:0','未选择省！'],
	        ['I_cityID','gt:0','未选择市！'],
	        ['I_districtID','gt:0','未选择区！'],
	        ['Vc_address','require','未填写地址！'],
	        ['Vc_Email','require|email','未填写邮箱！|邮箱格式不正确！']
	         
	    ];
	    $da=array();
	    $da['id']=input('post.uid/d',0);
	    $da['Vc_name']=input('post.Vc_name/s','');
// 	    $da['Vc_realname']=input('post.Vc_realname/s','');
	    $da['I_sex']=input('post.I_sex/d',0);
// 	    $da['Dt_birthday']=input('post.Dt_birthday','');
	    $da['I_provinceID']=input('post.I_provinceID/d',0);
	    $da['I_cityID']=input('post.I_cityID/d',0);
	    $da['I_districtID']=input('post.I_districtID/d',0);
	    $da['Vc_address']=input('post.Vc_address','');
	    $da['Vc_Email']=input('post.Vc_Email','');
	    
	    $validate = new Validate($rules);
	    $res   = $validate->check($da);
	    if(!$res){
	        return getJsonStr(500,$validate->getError());
	    }
	    
	    $map['id'] = $da['id'];
	    $map['Vc_name']=$da['Vc_name'];
	    $map['state']=1;
	    $find = $this->userModel->checkParamUpdate($map);
	    if(!$find){
	        return getJsonStr(501,'该用户名已被使用');
	    }
	  
	     $uprow=$this->userModel->update($da);
	    if($uprow){
	            return getJsonStrSuc('保存成功！');
        }else{
	            
	        return getJsonStr(500,"保存失败！");
	            
 	        }
	    
	}
	
	
	public function certify () {
	     
	    $user = $this->userModel->getCompanyById($this->getSessionUid());
	    
	       $filenames['CompanyLogo'] = db('configure')->where("code='CompanyLogo'")->value('filename');
	       $filenames['LicencePic'] =    db('configure')->where("code='LicencePic'")->value('filename');
	       $filenames['OrgPic'] =   db('configure')->where("code='OrgPic'")->value('filename');
	       $filenames['TaxPic'] =    db('configure')->where("code='TaxPic'")->value('filename');
	       $filenames['AccPic'] =   db('configure')->where("code='AccPic'")->value('filename');
	       $filenames['AuthPic'] =   db('configure')->where("code='AuthPic'")->value('filename');
	       $filenames['AuthWord'] =   db('configure')->where("code='AuthWord'")->value('filename');
	    
	    $this->assign([
	        'data'=>$user,
	        'filenames'=>$filenames,
	    ]) ;
	    return $this->fetch() ;
	}
	
	public function  download(){
	    
	 
	    //文件下载
	    //readfile
	    
	    $filename = input('filename/s');
	    $filepath = input('filepath/s');
// 	    $filename = "是的aa.png";
// 	    $filepath = "20161129/ae3e29404f67b6edb4e09f9ea16974bf.png";
	    $file_path=config('img_path').$filepath;
	    
	    $fileinfo = pathinfo($filepath);
	    header('Content-type: application/x-'.$fileinfo['extension']);
	    header('Content-Disposition: attachment; filename='.$filename);
	    header('Content-Length: '.filesize($file_path));
	    readfile($file_path);
	    exit();
	    
	    
	}
	
	
	public  function save_certify(){
	
	    $rules = [
	        ['I_userID','gt:0','未登录！'],
	        ['Vc_applicantName','require','未填写联系人！'],
	        ['Vc_name','require','未填写公司名！'],
	        ['I_provinceID','gt:0','未选择省！'],
	        ['I_cityID','gt:0','未选择市！'],
	        ['I_districtID','gt:0','未选择区！'],
	        ['Vc_address','require','未填写地址！'],
	        ['Vc_logo','require','未上传公司logo！'],
	        ['Vc_licence_pic','require','未上传营业执照！'],
	        ['Vc_org_pic','require','未上传组织机构代码证！'],
	        ['Vc_tax_pic','require','未上传税务登记证！'],
	        ['Vc_acc_pic','require','未上传开户许可证！'],
	        ['Vc_auth_pic','require','未上传企业认证授权书！']
	
	    ];
	    $da=array();
	    $da['I_userID']=$this->getSessionUid();
	    $da['Vc_name']=input('post.Vc_name/s','');
	    $da['Vc_applicantName']=input('post.Vc_applicantName/s','');
	    $da['I_provinceID']=input('post.I_provinceID/d',0);
	    $da['I_cityID']=input('post.I_cityID/d',0);
	    $da['I_districtID']=input('post.I_districtID/d',0);
	    $da['Vc_address']=input('post.Vc_address','');
	    $da['Vc_logo']=input('post.Vc_logo','');
	    $da['Vc_licence_pic']=input('post.Vc_licence_pic','');
	    $da['Vc_org_pic']=input('post.Vc_org_pic','');
	    $da['Vc_tax_pic']=input('post.Vc_tax_pic','');
	    $da['Vc_acc_pic']=input('post.Vc_acc_pic','');
	    $da['Vc_auth_pic']=input('post.Vc_auth_pic','');
	    $da['Createtime'] = date('Y-m-d H:i:s');
	    $da['I_status'] = 1;
	    // 	    dump($da);die;
	     
	    $validate = new Validate($rules);
	    $res   = $validate->check($da);
	    if(!$res){
	        return getJsonStr(500,$validate->getError());
	    }
	    $map['state']=1;
	    $map['I_userID']=$da['I_userID'];
	    $have = db('sm_user_company')->where($map)->count();
	    
	    if($have){
	        
	        $uprow=db('sm_user_company')->where($map)->update($da);
	        if($uprow){
	            return getJsonStrSuc('保存成功！');
	        }else{
	            return getJsonStr(500,"保存失败！");
	             
	        }
	        
	    }else{
	        
	      $insrow = db('sm_user_company')-> insertGetId($da);
	        
	      if($insrow){
	          return getJsonStrSuc('保存成功！');
	      }else{
	          return getJsonStr(500,"保存失败！");
	      
	      }
	    }
	    
	   
	    
	     
	}
	
	
	
	
	
	
}