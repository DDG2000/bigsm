<?php
namespace app\home\controller ;

use app\common\controller\MemberbaseController;
use app\common\model\message\UserMessageConfig;
use app\common\model\message\SiteMessage;

class Message extends MemberbaseController {
	
	private $uid, $userCfgModel, $userCfg, $siteMessageModel ;
	
	public function _initialize() {
		parent::_initialize() ;
		$this->uid = $this->getSessionUid() ;
		$this->userCfgModel = new UserMessageConfig($this->uid) ;
		$this->userCfg = $this->userCfgModel->getConfig() ;
		$this->siteMessageModel = new SiteMessage() ;
	}
	
	public function content ($mid=0) {
		$content = $this->siteMessageModel->getContent($this->getSessionUid(), $mid) ;
		if (!$content) {
			throw new \Exception("非法请求") ;
		}
		$this->assign('content',$content) ;
		return $this->fetch('content') ;
	}
	
	public function getList ($page=1, $type=0, $unreadOnly=false) {
		$r = $this->siteMessageModel->getMessageList($this->uid, $page, $type, $unreadOnly) ;
		if (isset($r['data'])) {
			$ids = [1,2,3] ;
			foreach ($r['data'] as $msg) {
				$msgType = $msg['I_type'] ;
				if (1 == $msgType) {
					$ids[] = $msg['id'] ;
				}
			}
			if (!$unreadOnly) {
				SiteMessage::setReaded($ids) ;
			}
		}
		return getJsonStrSucNoMsg($r) ;
	}
	
	public function myMessages() {
		return $this->fetch('list') ;
	}
	
	public function config () {
		$this->assign('model',$this) ;
		return $this->fetch() ;
	}
	
	public function saveConfig() {
		$post = $_POST ;
		$config = [] ;
		foreach ($post as $key => $data) {
			if (strpos($key, 'save__') === 0 && is_array($data)) {
				$key = substr($key, 6, strlen($key)) ;
				$config[$key] = $this->calculate($data) ;
			}
		}
		$this->userCfgModel->saveConfig($config) ;
		return getJsonStrSucNoMsg() ;
	}
	
	public function getChecked ($type, $value) {
		return $this->isChecked($type, $value) ? "checked='checked'" : '' ;
	}
	
	public function isChecked ($type, $value) {
		if (key_exists($type, $this->userCfg)) {
			$val = $this->userCfg[$type] ;
			return ($val % $value === 0) ;
		}
		return false ;
	}
	
	private function calculate ($data) {
		$val = 1 ;
		foreach ($data as $d) {
			$val *= $d ;
		}
		return $val ;
	}
	
}