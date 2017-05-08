<?php
namespace app\admin\controller ;

use app\admin\model\MessageLogModel;

class MessageLog extends AdminController {
	
	private $model ;
	
	
	public function _initialize() {
		$this->model = new MessageLogModel() ;
		parent::_initialize();
	}
	

	public function listpage($page=1, $phone=false, $username=false, $type=false) {
		$pagedata = $this->model->getPage($page, $phone, $username, $type) ;
		$param = [
				'phone'		=>		$phone,
				'type'		=>		$type,
		] ;
		$this->assign([
				'page'		=>		$pagedata,
				'param'		=>		$param ,
				'model'		=>		$this->model ,
		]) ;
		return $this->fetch('list') ;
	}
	
}