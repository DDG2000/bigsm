<?php
namespace  app\admin\controller ;

use app\admin\model\ErpSyncModel;
use utils\Http ;
class Erpsync extends AdminController {
	
	private $model ;
	
	private $tasks = ['goodsSyncTask','employeeSyncTask','placeSyncTask','companySyncTask',
				'purchaseProofSyncTask','billSyncTask','projectSyncTask'] ;
	
	public function _initialize() {
		parent::_initialize() ;
		$this->model = new ErpSyncModel() ;
	}
	
	public function index() {
		$this->assign('model',$this) ;
		$this->assign('removeUrl',config('sync_service')) ;
		return $this->fetch("index") ;
	}
	
	public function getConfig() {
		$data = $this->model->getSavedData();
		return getJsonStrSucNoMsg($data) ;
	}
	
	public function saveConfig() {
		$post = $_POST ;
		$this->model->removeAll() ;
		foreach ($post as $name => $hours) {
			foreach ($hours as $h) {
				if (in_array($name, $this->tasks)) {
					$this->model->addTask($name, $h) ;
				}
			}
		}
		$http = new Http() ;
		$result = $http->requestURL(config('sync_service')) ;
		if ("SUCCESS" == $result) {
			$this->addManageLog("系统设置", "修改ERP同步时间") ;
			return getJsonStrSuc("设置成功") ;
		}
		return getJsonStrError("设置出错，请稍后再试。") ;
	}
	
	public function getHtml($name) {
		$html = "" ;
		for ($i = 0 ; $i < 24 ; $i ++) {
			$thtml = "<label><input type='checkbox' name='{$name}[]' value='{$i}' class='flat'>&nbsp;{$i}:00</label>" 
					. (($i + 1) % 6 === 0 ? '<br/>' : '') ;
			$html .= $thtml ;
		}
		return $html ;
	}
	
}