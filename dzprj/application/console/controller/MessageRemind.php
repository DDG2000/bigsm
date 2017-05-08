<?php
namespace app\console\controller ;

use app\common\model\message\MessageService;

class MessageRemind extends ConsoleController {
	
	private $messageService ;
	
	public function _initialize() {
		$this->messageService = new MessageService() ;
	}
	
	/**
	 * 对账提醒
	 */
	public function check () {
	}
	
	/**
	 * 还款提醒
	 */
	public function repay() {
		
	}
	
}