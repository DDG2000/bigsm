<?php
namespace app\common\model ;
use \think\db\Query;
use app\admin\model\AdminModel;
class industryModel extends AdminModel {
	
	protected $table = "sm_industry" ;
	protected $pk = 'id' ;
	protected $createTime = 'Createtime' ;
	protected $updateTime = 'Createtime' ;
	protected $status = 'state' ;
	
	public function getAll(){
		return $this->all([$this->status=>1]);
	}
	public function getArr(){
		$re=$this->getAll;
		foreach ($re as $a){
			$va[$a['id']]=$a['Vc_name'];
		}
		return $va;
	}
}