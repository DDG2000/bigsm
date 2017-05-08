<?php
namespace app\admin\model ;

class ErpSyncModel extends AdminModel {
	
	protected $table = 'sync_config' ;
	protected $pk = 'id' ;
	protected $createTime = 'Createtime' ;
	protected $updateTime = false ;
	protected $status = 'state' ;
	
	public function getSavedData() {
		return $this->all(['state'=>parent::DEFAULT_STATUS_NORMAL]) ;
	}
	
	public function removeAll() {
		$this->where('1=1')->delete() ;
	}
	
	public function addTask ($name , $hour) {
		$task = [
				'Vc_type'		=>	$name,
				'Vc_sync_hour'	=>	$hour,
				'Vc_sync_min'	=>	0,
				'state'			=>	1,
		] ;
		$this->create($task) ;
	}
	
}