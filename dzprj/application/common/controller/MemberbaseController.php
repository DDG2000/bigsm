<?php
namespace app\common\controller ;

use app\common\controller\HomeController;

class MemberbaseController extends HomeController {
	

	function _initialize() {
	    parent::_initialize();
	  
	}
	public function __construct(){
	    parent::__construct();
	    $this->check_login();
	    $this->check_user();
	    $this->checkUnreadMessage() ;
	    $userStatus = $this->getCertifyStatus();
	    $userStatusInfo = $this->getCertifyStatusInfo();
	    $this->assign([
	        'userCertifyStatus'=>$userStatus,
	        'userCertifyStatusInfo'=>$userStatusInfo,
	    ]);
	}
	public  function getCertifyStatus(){
	    $uid = $this->getSessionUid();
	    $userStatus = db('sm_user_company')->where(['I_userID'=>$uid])->value('I_status') ;
	    if ($userStatus == 3) {
	
	        return  true;
	    }
	    return  false;
	
	}
	public  function getCertifyStatusInfo(){
	    $userStatus=4;
	    if(isset($_SESSION['user'])){
	        $uid = $this->getSessionUid();
	        $userStatus = db('sm_user_company')->where('I_userID',$uid)->value('I_status') ;
	    }
	    return  $userStatus;
	     
	
	}
	
}