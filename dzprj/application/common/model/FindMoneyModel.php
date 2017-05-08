<?php
namespace app\common\model ;
use \think\db\Query;
use app\admin\model\AdminModel;
class FindMoneyModel extends AdminModel {
	
	protected $table = "sm_findmoney" ;
	protected $pk = 'id' ;
	protected $createTime = 'Createtime' ;
	protected $updateTime = 'D_updatetime' ;
	protected $status = 'state' ;
	
	const STATUS_CHECKING  			    = 1 ;
	const STATUS_PASS  			        = 2 ;
	const STATUS_REJECT  		    	= 3 ;
	const STATUS_CANCEL		  			= 4 ;

	
	public $statusArray = [
			self::STATUS_CHECKING		=>'待审核',
			self::STATUS_PASS		    =>'审核通过',
			self::STATUS_REJECT		    =>'审核未通过',
			self::STATUS_CANCEL			=>'已取消',
	] ;

	public function getPage ($currentPage,$param=[]) {
		$where = [] ;

		if(iseta($param,'keywords')){

			$where['a.Vc_orderSn|su.Vc_name|a.Vc_ordername'] =['like','%'.trim($param['keywords']).'%'];


		}

		if(array_key_exists($param['applystatus'],$this->statusArray)){

			$where['a.I_status'] = $param['applystatus'];

		}

		$query = $this->getMQuery($where) ;
		$re= $this->getPaginationByQuery($query, $currentPage) ;
		return $re;
	}
	
	public function getById ($id) {
	    return $this->getMQuery(['a.id'=>$id])->find() ;
	}
	
	public function getTelsById ($fid) {
	    return $this->getContactQuery(['a.I_findmoneyID'=>$fid])->select() ;
	}
	private function getContactQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table('sm_findmoney_tel')->alias('a')
	    ->field('a.*')
	    ->where($where)
	    ->order(['a.Createtime'=>'asc']);
	    return $query ;
	}

	public function getMQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table($this->table)->alias('a')
	    ->field('a.*,su.Vc_name username,sp1.name proname1,sp2.name proname2,
	    sc1.name cityname1,sc2.name cityname2,sd1.name areaname1,sd2.name areaname2')
	    ->join('sm_user su','su.id = a.I_userID','left')
	    ->join('s_province sp1','sp1.id=a.I_cave_provinceID','left')
	    ->join('s_province sp2','sp2.id=a.I_belong_provinceID','left')
	    ->join('s_city sc1','sc1.id=a.I_cave_cityID','left')
	    ->join('s_city sc2','sc2.id=a.I_belong_cityID','left')
	    ->join('s_district sd1','sd1.id=a.I_cave_districtID','left')
	    ->join('s_district sd2','sd2.id=a.I_belong_districtID','left')
	    ->where($where)
	    ->order(['a.Createtime'=>'desc']);
	    return $query ;
	}
	/**检验订单是否重复添加
	 * @param $oid
	 * @return array|false|\PDOStatement|string|\think\Model
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function checkOrderOne($oid){
		$query = new Query() ;
		$where['state'] = self::DEFAULT_STATUS_NORMAL;
		$where['Vc_orderSn'] = $oid;
		return $query->table($this->table)->where($where)->find();
	}
}