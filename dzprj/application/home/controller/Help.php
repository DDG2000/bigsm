<?php
namespace app\home\controller ;

use app\admin\model\ArticleModel;
use app\admin\model\ArticleClassModel;
use app\common\controller\HomeController;
class Help extends HomeController {

	private $articleModel,$articleClassModel;
	public function __construct() {

		parent::_initialize() ;
		$this->articleModel = new ArticleModel;
		$this->articleClassModel = new ArticleClassModel;
		parent::__construct();
		$userStatus = $this->getUserStatus();
		$userStatusInfo = $this->getCertifyStatusInfo();
		$this->assign([
		    'userCertifyStatus'=>$userStatus,
		    'userCertifyStatusInfo'=>$userStatusInfo,
		]);

	}
	
	
	
	/**获取所有栏目信息
	 * @return mixed
	 */
	
//	public function index(){
//		$this->assign([
//			'class'=>$this->articleClassModel->getAll(),
//		]) ;
//		return $this->fetch('index') ;
//	}

	/**
	 * 展示栏目
	 * @param int $class 栏目名
	 * @param int $type 1栏目下只有一篇文章 返回一篇文章 0返回多篇文章
	 * @return mixed
	 */
	public function index($class=0,$type=0){
		$classes=$this->articleClassModel->getAll();
		if($class==0){
			$class=$classes['0']['id'];
		}
		$re='';
		if($type==1){
			$re=$this->articleModel->getListByClassId($class);
			$re=$re['0'];
		}else{
			$param['class']=$class;
			$re = $this->articleModel->where(['I_article_classID'=>$param['class'],'state'=>1])->paginate(10,false,['query'=>$param]);
		}
		$this->assign([
			'type'=>$type,
			'data'=>$re,
			'classId'=>$class,
			'class'=>$this->articleClassModel->getAll(),
		]) ;
		return $this->fetch('list') ;
	}

	/**
	 * 展示一篇文章
	 * @param int $id
	 * @return mixed
	 */
	public function getById($id=0,$class=0){
		if($id==0){return false;}
		$re=$this->articleModel->getById($id);
		$this->assign([
			'type'=>1,
			'classId'=>$class,
			'id'=>$id,
			'data'=>$re,
			'class'=>$this->articleClassModel->getAll(),	
		]) ;
		return $this->fetch('list') ;
	}

	/**
	 * 展示早报 I_article_classID=10
	 * @param int $type 1栏目下只有一篇文章 返回一篇文章 0返回多篇文章
	 * @return mixed
	 */
	public function newsList($type=0){
		$id=$this->articleClassModel->getNewsId;
		$re = $this->articleModel->where(['I_article_classID'=>$id,'state'=>1])->order(['I_order'=>'desc','D_releasetime'=>'desc'])->paginate(10,false);
		$this->assign([
			'data'=>$re,
			'type'=>$type,
		]) ;
		return $this->fetch('news') ;
	}

	/**
	 * 展示一篇早报	
	 * @param int $id
	 * @return mixed
	 */
	public function news($id=0){
		if($id==0){return false;}
		$re=$this->articleModel->getById($id);
		$this->assign([
			'type'=>1,
			'id'=>$id,
			'data'=>$re,
		]) ;
		return $this->fetch('news') ;
	}
	
	/**
	 *  意见反馈页面
	 *  @author 唐
	 */
	public function feedback(){
		$this->check_login();
		// 这里添上你们的代码
		$this->assign([
			'class'=>$this->articleClassModel->getAll(),
		]) ;
		return $this->fetch('feedBack');
		
	}
	
}
