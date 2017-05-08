<?php
namespace app\common\model ;
use \think\db\Query;
use app\admin\model\AdminModel;
class FindGoodsModel extends AdminModel {
	
	protected $table = "sm_findgoods" ;
	protected $pk = 'id' ;
	protected $createTime = 'Createtime' ;
	protected $updateTime = 'D_updatetime' ;
	protected $status = 'state' ;
	//找货审核
	const STATUS_CHECKING  			    = 1 ;
	const STATUS_PASS  			        = 2 ;
	const STATUS_REJECT  		    	= 3 ;
	const STATUS_CANCEL		  			= 4 ;
	//商品报价
	const CHARGE_WAITING  			    = 1 ;
	const CHARGE_DONE  			    	= 2 ;
	const CHARGE_CANCEL 			    = 3 ;
	//支付方式
	const PAY_BANKING  			   		= 1 ;
	const PAY_CASH  			    	= 2 ;

	public $statusArray = [
			self::STATUS_CHECKING		=>'待审核',
			self::STATUS_PASS		    =>'审核通过',
			self::STATUS_REJECT		    =>'审核未通过',
			self::STATUS_CANCEL			=>'已取消',
	] ;
	public $goodsChargeArray = [
		self::CHARGE_WAITING		=>'待报价',
		self::CHARGE_DONE		    =>'已报价',
		self::CHARGE_CANCEL		    =>'已取消',
	] ;
	public $payArray = [
		self::PAY_BANKING		=>'银行承兑',
		self::PAY_CASH		    =>'现款转账',
	] ;
	public function getPage ($currentPage,$param=[]) {
	    $where = [] ;
		if(iseta($param,'keywords')){
		//订单号,用户,订单名称,采购人,采购名称查询
		   $where['a.Vc_orderSn|su.Vc_name|a.Vc_consignee'] =['like','%'.trim($param['keywords']).'%'];
	    }
		if(array_key_exists($param['applystatus'],$this->statusArray)){
			$where['a.I_status'] = $param['applystatus'];
		}
	    $query = $this->getMQuery($where) ;
	    return $this->getPaginationByQuery($query, $currentPage) ;
	}
	
	
	public function getById ($id) {
	    return $this->getMQuery(['a.id'=>$id])->find() ;
	}
	
	public function getGoodsListById ($fid) {
	    return $this->getContactQuery(['a.I_findgoodsID'=>$fid])->select() ;
	}
	private function getContactQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table('sm_findgoods_list')->alias('a')
	    ->field('a.*')
	    ->where($where)
	    ->order(['a.Createtime'=>'asc']);
	    return $query ;
	}

	public function getMQuery ($where = []) {
	    $query = new Query() ;
	    $where['a.state'] = self::DEFAULT_STATUS_NORMAL;
	    $query->table($this->table)->alias('a')
	    ->field('a.*,si.Vc_name industryname,su.Vc_name username,su.Vc_mobile userphone,suc.Vc_erp_name companyname,sp.name proname,
	    sc.name cityname,sd.name areaname')
	    ->join('sm_industry si','si.id=a.I_industryID','left')
	    ->join('sm_user su','su.id = a.I_userID','left')
	    ->join('sm_user_company  suc','suc.I_userID = a.I_userID','left')
	    ->join('s_province sp','sp.id=a.I_provinceID','left')
	    ->join('s_city sc','sc.id=a.I_cityID','left')
	    ->join('s_district sd','sd.id=a.I_districtID','left')
	    ->where($where)
	    ->order(['a.Createtime'=>'desc']);
	    return $query ;
	}
	private function getMLQuery ($where = []) {
		$query = new Query() ;
		$where['a.state'] = self::DEFAULT_STATUS_NORMAL;
		$query->table($this->table)->alias('a')
			->field('a.*,si.Vc_name industryname,su.Vc_name username,sp.name proname,
	    sc.name cityname,sd.name areaname')
			->join('sm_industry si','si.id=a.I_industryID','left')
			->join('sm_user su','su.id = a.I_userID','left')
			->join('s_province sp','sp.id=a.I_provinceID','left')
			->join('s_city sc','sc.id=a.I_cityID','left')
			->join('s_district sd','sd.id=a.I_districtID','left')
			->join('sm_findgoods_list sfl','sfl.I_findgoodsID=a.id','left')
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