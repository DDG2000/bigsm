<?php
/*
 * BaseController.php
 *
 * Copyright Sichuan Great Wall Software Technology Co.,LTD. All Rights Reserved.
 * Author sakura 2016年7月11日上午11:02:41
 */
//////////////////////////////////////////////////////

namespace app\index\controller;

use think\Controller;
use think\Request;

class BaseController extends Controller{
	
	protected $beforeActionList = [
			'urlFiltered',
	];
	/**
	 * pc进入
	 * Author sakura 2016年7月11日上午11:01:57
	 */
	public function urlFiltered(){
		$domain = $this->request->domain();//域名+http://
		$pathInfo = 'g'.$this->request->path();//访问路径无域名
		if (stripos($domain,'www.') === false/*  || !stripos($pathInfo,'index/') === 1 */){
			/* if(count(explode(".",$domain)) == 3){
				$domain = stristr($domain, '.');
				return $this->redirect('http://www'.$domain);//跳转至www.
			} */
		}
	}
	
	public function _initialize() {
		$this->request = Request::instance() ;
	}
}
