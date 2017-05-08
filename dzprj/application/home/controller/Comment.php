<?php
namespace app\home\controller ;
use think\Validate;
use app\common\model\CommentModel;
class Comment extends WorkbenchController {
	
	private $commentModel;
	public function __construct() {

		parent::_initialize() ;
		$this->commentModel = new CommentModel();
		parent::__construct();
// 		$this->check_certify();
	}
	
	public function index () {
	     
	  
	    return $this->fetch('help/feedBack');
	}
	
	public  function save(){
	
	    $rules = [
	        ['I_userID','gt:0','未登录！'],
	        ['Vc_content','require','未填写建议内容！'],
	        ['Vc_phone','require','未填写手机号！']
	
	    ];
	    $da=array();
	    $da['I_userID']=$this->getSessionUid();
	    $da['Vc_content']=input('post.Vc_content/s','','htmlspecialchars');
	    $da['Vc_imgpath']=input('post.Vc_imgpath/s','');
	    $da['Vc_phone']=input('post.Vc_phone','');
	    $da['Createtime'] = date('Y-m-d H:i:s');
	    $da['I_status'] = 1;
	    $validate = new Validate($rules);
	    $res   = $validate->check($da);
	    if(!$res){
	        return getJsonStr(500,$validate->getError());
	    }
	    if(!funcphone($da['Vc_phone'])){
	        return getJsonStr(500,'手机号码不正确！');
	    }
	    if(empty(trim($da['Vc_content']))){
	        return getJsonStr(500,'未填写建议内容！');
	    }
	   
	        
	    $insrow = db('sm_comment')-> insertGetId($da);
	        
	      if($insrow){
	          return getJsonStrSuc('提交成功！');
	      }else{
	          return getJsonStr(500,"提交失败！");
	      
	      }
	   
	   
	    
	     
	}
	
	
	
	
	
	
}