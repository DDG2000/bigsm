<?php
namespace app\home\controller ;

use message\ShortMessage;
use think\Controller;

class ShortMessageNotify extends Controller {
	
	private $sms ;
	
	public function _initialize() {
		$this->sms = new ShortMessage() ;
	}
	
	public function handle() {
		$randCode = input('rand_code') ;
		$idertifier = input('idertifier') ;
		$this->sms->saveCode($identifier, $code) ;
		return '{"res_code":0}' ;
	}
	
}