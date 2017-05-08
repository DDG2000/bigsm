<?php
namespace app\home\controller;

use app\common\controller\MemberbaseController;
class WorkbenchController extends MemberbaseController {
   
    function _initialize() {
        parent::_initialize();
        

    }
//     public function __construct(){
//         parent::__construct();
//         $userStatus = $this->getCertifyStatus();
//         $this->assign([
//             'userCertifyStatus'=>$userStatus,
//         ]);
//     }
    /**
     * 检查会员是否认证
     */
    protected function  check_certify(){
		$user = $this->getSessionUser();
		$uid = $user['id'] ;
		$userStatus = db('sm_user_company')->where('I_userID',$uid)->value('I_status') ;
		if ($userStatus != 3) {
            $this->redirect(url('account/alert'));
		}
    }
    
//     public  function getCertifyStatus(){
//         $uid = $this->getSessionUid();
//         $userStatus = db('sm_user_company')->where('I_userID',$uid)->value('I_status') ;
//         if ($userStatus == 3) {
            
//             return  true;
//         }
//         return  false;
      
//     }
    
    
}
