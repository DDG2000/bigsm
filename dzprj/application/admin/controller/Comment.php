<?php
namespace app\admin\controller ;
use think\paginator\driver\Bootstrap;
use think\db\Query;
use think\Validate;
use app\common\model\CommentModel;

class Comment extends AdminController {
	private $commentModel ;
	
	public function _initialize() {
		$this->commentModel = new CommentModel();
		parent::_initialize();
	}
	
    public function index() {
        $param['keywords'] = input('keywords/s','');
    	$currentPage = $this->request->get('page',1) ;
        $param['applystatus'] = input('applystatus/d',-1);
    	$page = $this->commentModel->getPageList($currentPage,$param) ;
    	$this->assign([
    	    'commentModel' =>$this->commentModel,
    	    'page'=>$page,
    	    'param'=>$param,
    	]) ;
    	
    	return $this->fetch("index") ;
    }
    public function del() {
        $id=  $this->request->get('id/d',0,'htmlspecialchars');
        $data['state'] = 0;
        $result  =db('sm_comment')->where("id=$id")->update($data);
        if($result){
            $this->addManageLog('意见反馈', '删除ID为'.$id.'的意见反馈');
            return getJsonStrSuc('删除成功');
        } else {
            return getJsonStr(500,'删除失败');
        }
    }
   
   

    
    public function  edit(){

        if($this->request->isPost()){
            $id = input('post.id/d',0);
            if(!$id){return getJsonStrError('参数获取失败');}
            $data['Vc_replay'] = input('post.Vc_replay/s','');
            $data['I_status'] = input('post.approved/d',0);
           if(!$data['I_status']){return getJsonStrError('未选择处理状态！');}
            $data['D_replaytime'] =date("Y-m-d H:i:s");
            $uprow = db('sm_comment')->where('id',$id)->update($data);
            if($uprow > 0){
                $this->addManageLog('意见反馈', '处理了ID为'.$id.'的反馈');
                return getJsonStrSuc('操作成功');
            }else{
                return getJsonStrError('操作失败');
            }

        }else{

            $id = $this->request->get('id',0);
            if(!$id){
                $this->error('信息未选择');
            }
            $data =  $this->commentModel->getById($id);
            $this->assign([
                'commentModel' =>$this->commentModel,
                'data'=>$data,
            ]) ;
            return $this->fetch();
        }

    }
    
    
    
    
  
}
