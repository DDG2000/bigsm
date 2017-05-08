<?php
namespace app\home\controller ;

use app\common\model\FindCarsModel;
use app\common\model\IndustryModel;
use app\common\controller\MyValidate;
use app\common\model\SlidesModel;
use Symfony\Component\HttpFoundation\Cookie;
use \think\Request;
use app\common\model\UserModel;
use GuzzleHttp\json_encode;
class FindCars extends WorkbenchController {

	private $findCarsModel,$industryModel,$userModel;
	public function __construct() {

		parent::_initialize() ;
		$this->findCarsModel = new FindCarsModel;
		$this->industryModel = new IndustryModel;
		$this->slidesModel = new SlidesModel;
		$this->userModel = new UserModel;
		parent::__construct();
//		$this->check_certify();

	}


	/**
	 * 获取缓存的货物树形结构
	 * @return \think\response\Json
	 */

	public  function getGoodsTree(){

		$arr = get_goodstree();
		return json($arr);

	}
	/**
	 * 到表单页面 新建/确认后修改cookie/工作台修改数据库
	 * @param $a 1确认页面 2 修改页面从COOKIE读取数据
	 * @param int $id 修改数据库读取数据
	 * @return mixed
	 */
	public function toForm($type=0,$id=0){
		//获取id
		if($type==1 && isset($id)){
			$uid = $this->getSessionUid();
			$order = $this->findCarsModel->getMQuery(['a.id'=>$id,'a.I_userID'=>$uid])->find();
			$goods=db('sm_findcars_list')->where(" state=1 and I_findcarsID={$order['id']}")->select();
			$addr['proname1']=$order['proname1'];
			$addr['cityname1']=$order['cityname1'];
			$addr['areaname1']=$order['areaname1'];
			$addr['proname2']=$order['proname2'];
			$addr['cityname2']=$order['cityname2'];
			$addr['areaname2']=$order['areaname2'];
		}
		//获取cookie数据
		else if($type==2 && cookie('findCarsOrder') && cookie('findCarsGoods')){
			$order=unserialize(cookie('findCarsOrder'));
			$goodsCode=unserialize(cookie('findCarsGoods'));
			foreach ($goodsCode as $v){
				$goodsArr = db('erp_goods_tree')->where('Vc_goods_code',$v['Vc_goods_code'])->find();
				$v['Vc_goods_type'] = $goodsArr['Vc_goods_type'];
				$v['Vc_goods_class'] = $goodsArr['Vc_goods_class'];
				$v['Vc_goods_factory'] = $goodsArr['Vc_goods_factory'];
				$v['Vc_goods_breed'] = $goodsArr['Vc_goods_breed'];
				$v['Vc_goods_material'] = $goodsArr['Vc_goods_material'];
				$v['Vc_goods_spec'] = $goodsArr['Vc_goods_spec'];
				$goods[]=$v;
			}
			$addr=unserialize(cookie('findCarsAddr'));
			$note=unserialize(cookie('findCarsNote'));
			$order['T_note']=$note;
		}
		if(isset($order)){
			$this->assign([
				'order'=>$order,
				'goods'=>$goods,
				'addr'=>$addr,
			]);
		}
		$this->assign([
			'findCarsModel'=>$this->findCarsModel,
			'title'=>'找车',
		]);
		return $this->fetch('form') ;
	}
	/**
	 * 提交找车信息
	 * @return mixed
	 */
	public function submitAdd(){
		$rules = [
//				['I_industryID','require|egt:0','未选择行业！|所选行业参数错误!'],
			['I_sent_provinceID','gt:0','未选择发货地址-省！'],
			['I_sent_cityID','gt:0','未选择发货地址-市！'],
			['I_sent_districtID','gt:0','未选择发货地址-区！'],
			['Vc_sent_address','require|strLenMax:50','未输入发货详细地址！|详细地址字数超过50限制'],
			['Vc_senter','require|strLenMax:30','未输入发货人姓名！|发货人姓名字数超过30限制'],
			['Vc_sent_tel','require|phone:1','未输入发货人联系方式！|发货人联系方式输入错误'],
			['I_receive_provinceID','gt:0','未选择收货地址-省！'],
			['I_receive_cityID','gt:0','未选择收货地址-市！'],
			['I_receive_districtID','gt:0','未选择收货地址-区！'],
			['Vc_receive_address|strLenMax:50','require','未输入收货详细地址！|收货详细地址字数超过50限制'],
			['Vc_receiver','require|strLenMax:30','未输入收货人姓名！|收货人姓名字数超过30限制'],
			['Vc_receiver_tel','require|phone:1','未输入收货人联系方式！|收货人联系方式输入错误'],
			['I_offertype','in:1,2','未选择期望报价方式！'],
			['N_expectprice','require','未输入期望价格！'],
			['I_plus_tax','in:0,1','未选择是否含税！'],
			['I_plus_dumper','in:0,1','未选择自卸车！'],
			['I_plus_deliveryfee','in:0,1','未选择垫出库费！'],
			['D_arrived_start','require','未输入到货日期！'],
			['T_note','strLenMax:200','备注字数超过200限制！'],
		];
		//找车订单信息
		$da=array();
		//id存在就是更新,0就是新增,id,Vc_orderSn放在隐藏域
		$da['id']=input('post.id/d',0);
		$da['Vc_orderSn']=input('post.Vc_orderSn/s','');

//			$da['I_industryID']=input('post.I_industryID/d',0);
		$da['I_sent_provinceID']=input('post.I_sent_provinceID/d',0);
		$da['I_sent_cityID']=input('post.I_sent_cityID/d',0);
		$da['I_sent_districtID']=input('post.I_sent_districtID/d',0);
		$da['Vc_sent_address']=input('post.Vc_sent_address/s','','trim');
		$da['Vc_senter']=input('post.Vc_senter/s','','trim');
		$da['Vc_sent_tel']=input('post.Vc_sent_tel/s','');
		$da['I_offertype']=input('post.I_offertype/d',0);
		$da['N_expectprice']=input('post.N_expectprice/s','');
		$da['I_plus_tax']=input('post.I_plus_tax/d',0);
		$da['I_plus_dumper']=input('post.I_plus_dumper/d',0);
		$da['I_plus_deliveryfee']=input('post.I_plus_deliveryfee/d',0);
		$da['T_note']=input('post.T_note/s','');
		$da['Createtime']=date("Y-m-d H:i:s");
		$da['D_arrived_start']=input('post.D_arrived_start/s','');
		$da['Vc_receiver']=input('post.Vc_receiver/s','','trim');
		$da['Vc_receiver_tel']=input('post.Vc_receiver_tel/s','');
		$da['I_receive_provinceID']=input('post.I_receive_provinceID/d',0);
		$da['I_receive_cityID']=input('post.I_receive_cityID/d',0);
		$da['I_receive_districtID']=input('post.I_receive_districtID/d',0);
		$da['Vc_receive_address']=input('post.Vc_receive_address/s','','trim');
		$validate = new MyValidate($rules);
		$res   = $validate->check($da);
		if(!$res){
			return getJsonStr(500,$validate->getError());
		}
		$pronames=input('post.proname/a','');
		$citynames=input('post.cityname/a','');
		$areanames=input('post.areaname/a','');
		for($i=0,$j=1;$i<count($pronames);$i++,$j++){
			$addr['proname'.$j]=$pronames[$i];
			$addr['cityname'.$j]=$citynames[$i];
			$addr['areaname'.$j]=$areanames[$i];
		}
		$da['Vc_orderSn']=get_ordernum();
		cookie('findCarsNote',serialize($da['T_note']),3600);
		unset($da['T_note']);
		cookie('findCarsAddr',serialize($addr),3600);
		cookie('findCarsOrder',serialize($da),3600);
		//货物信息
		$cda = array();
		$data = array();
		$data['Vc_goods_code']=input('post.Vc_goods_code/a','');
		$data['N_plan_weight']=input('post.N_plan_weight/a','');
		$data['Vc_unit']=input('post.Vc_unit/a','');
		for ($i=0,$j=1;$i<count($data['Vc_goods_code']);$i++,$j++){
			if(!$data['Vc_goods_code'][$i]){
				return getJsonStr(500,"第".$j."个货物未选择");
			}
			if(!$data['N_plan_weight'][$i]){
				return getJsonStr(500,"第".$j."个货物未填写数量");
			}
			if(!$data['Vc_unit'][$i]){
				return getJsonStr(500,"第".$j."个货物未选择单位");
			}
			$temp['Vc_goods_code'] = $data['Vc_goods_code'][$i];
			$temp['N_plan_weight'] = $data['N_plan_weight'][$i];
			$temp['Vc_unit'] = $data['Vc_unit'][$i];
			$cda[] = $temp;
		}
		cookie('findCarsGoods',serialize($cda),3600);
		return getJsonStrSuc('',[],url('find_cars/listAdd'));
	}

	/**
	 * @return mixed
	 */
	public function listAdd(){
		$order=unserialize(cookie('findCarsOrder'));
		$goodsCodes=unserialize(cookie('findCarsGoods'));
		$addr=unserialize(cookie('findCarsAddr'));
		$note=unserialize(cookie('findCarsNote'));
		$order['T_note']=$note;
		$count=count($goodsCodes);
		foreach ($goodsCodes as $good){
			$temp['Vc_goods_code'] = $good['Vc_goods_code'];
			$temp['N_plan_weight'] = $good['N_plan_weight'];
			$temp['Vc_unit'] = $good['Vc_unit'];
			$goodsArr = db('erp_goods_tree')->where('Vc_goods_code',$temp['Vc_goods_code'])->find();
			$temp['Vc_goods_type'] = $goodsArr['Vc_goods_type'];
			$temp['Vc_goods_class'] = $goodsArr['Vc_goods_class'];
			$temp['Vc_goods_factory'] = $goodsArr['Vc_goods_factory'];
			$temp['Vc_goods_breed'] = $goodsArr['Vc_goods_breed'];
			$temp['Vc_goods_material'] = $goodsArr['Vc_goods_material'];
			$temp['Vc_goods_spec'] = $goodsArr['Vc_goods_spec'];
			$cda[] = $temp;
		}
		$goods=$cda;
		$this->assign([
			'order'=>$order,
			'goods'=>$goods,
			'addr'=>$addr,
			'title'=>'找车(订单确认)',
			'count'=>$count,
			'industryModel'=>$this->industryModel,
			'user'=>$this->userModel->getById($this->getSessionUid()),
		]);
		return $this->fetch('listadd') ;
	}
	/**
	 * @return mixed
	 */
	public function comfirmAdd(){
		if(!cookie('findCarsOrder') && !cookie('findCarsGoods')){
			return getJsonStrError('获取数据失败或没有提交数据！');
		}
		$order=unserialize(cookie('findCarsOrder'));
		$goods=unserialize(cookie('findCarsGoods'));
		$note=unserialize(cookie('findCarsNote'));
		$order['T_note']=$note;
		$temp1=$order;
		$temp2=$goods;
		if($this->findCarsModel->checkOrderOne($temp1['Vc_orderSn'])){
			return getJsonStrError('订单已提交,请勿重复提交！');
		}
		$temp1['I_userID']=$this->getSessionUid();
		$temp1['I_industryID']=1;
		$temp1['I_status']=FindCarsModel::STATUS_CHECKING;
		$this->findCarsModel->startTrans();//在第一个模型里启用就可以了，或者第二个也行
		//更新数据
		if($temp1['id']!=0) {
			foreach ($temp2 as $v){
				$goodsArr = db('erp_goods_tree')->where('Vc_goods_code',$v['Vc_goods_code'])->find();
				$v['Vc_goods_type'] = $goodsArr['Vc_goods_type'];
				$v['Vc_goods_class'] = $goodsArr['Vc_goods_class'];
				$v['Vc_goods_factory'] = $goodsArr['Vc_goods_factory'];
				$v['Vc_goods_breed'] = $goodsArr['Vc_goods_breed'];
				$v['Vc_goods_material'] = $goodsArr['Vc_goods_material'];
				$v['Vc_goods_spec'] = $goodsArr['Vc_goods_spec'];
				$v['Createtime']=date("Y-m-d H:i:s");
				$v['I_findcarsID']=$temp1['id'];
				$temp3[]=$v;
			}
			$re1=$this->findCarsModel->update($temp1, ['id' => $temp1['id']]);
			$re2=db('sm_findcars_list')->where(['I_findcarsID' => $temp1['id']])->update(['state' => 0]);
			$re3=db('sm_findcars_list')->insertAll($temp3);
			if($re1 && $re2 && $re3){
				$this->findCarsModel->commit();//成功则提交
				return getJsonStrSuc('保存成功！');
			}else{
				$this->findCarsModel->rollback();//不成功，则回滚
				return getJsonStrError('获取数据失败或没有提交数据！');
			}
		}else{//新增数据
			$re1=$insrow = $this->findCarsModel->save($temp1);
			foreach ($temp2 as $v){
				$goodsArr = db('erp_goods_tree')->where('Vc_goods_code',$v['Vc_goods_code'])->find();
				$v['Vc_goods_type'] = $goodsArr['Vc_goods_type'];
				$v['Vc_goods_class'] = $goodsArr['Vc_goods_class'];
				$v['Vc_goods_factory'] = $goodsArr['Vc_goods_factory'];
				$v['Vc_goods_breed'] = $goodsArr['Vc_goods_breed'];
				$v['Vc_goods_material'] = $goodsArr['Vc_goods_material'];
				$v['Vc_goods_spec'] = $goodsArr['Vc_goods_spec'];
				$v['Createtime']=date("Y-m-d H:i:s");
				$v['I_findcarsID']=$insrow;
				$temp[]=$v;
			}
			$re2=db('sm_findcars_list')-> insertAll($temp);
			if($re1 && $re2){
				$this->findCarsModel->commit();//成功则提交
				return getJsonStrSuc('保存成功！');
			}else{
				$this->findCarsModel->rollback();//不成功，则回滚
				return getJsonStrError('获取数据失败或没有提交数据！');
			}
		}
	}


	/**
	 * 工作台订单列表
	 * @param number $type
	 * @return html
	 */
	public function listpage ($type=0) {
		$uid = $this->getSessionUid();
		$I_industryID = $this->request->get('I_industryID',0);
		$where['state']=1;
		$where['I_userID']=$uid;

		switch ($type){
			case 1:
				$where['I_status'] = 1;//审核中
				break;
			case 2:
				$where['I_status'] = 2;//已通过
				break;
			case 3:
				$where['I_status'] = 3;//未通过
				break;
			case 4:
				$where['I_status'] = 4;//未通过
				break;
		}
		//wait 等待审核 pass 通过审核 deny未通过 cancel取消  steel钢铁 chemical化工
		$count['wait'] =db('sm_findcars')->where(" state=1 and I_userID={$uid} and I_status=1 ")->count();
		$count['pass'] =db('sm_findcars')->where(" state=1 and I_userID={$uid} and I_status=2 ")->count();
		$count['deny'] =db('sm_findcars')->where(" state=1 and I_userID={$uid} and I_status=3")->count();
		$count['cancel'] =db('sm_findcars')->where(" state=1 and I_userID={$uid} and I_status=4")->count();
		if($I_industryID==0){
			$count['steel'] =db('sm_findcars')->where(" state=1 and I_userID={$uid} and I_industryID=1 ")->count();
			$count['chemical'] =db('sm_findcars')->where(" state=1 and I_userID={$uid} and I_industryID=2 ")->count();
		}else{
			$count['steel'] =db('sm_findcars')->where(" state=1 and I_userID={$uid} and I_industryID=1 and I_status=$I_industryID ")->count();
			$count['chemical'] =db('sm_findcars')->where(" state=1 and I_userID={$uid} and I_industryID=2 and I_status=$I_industryID ")->count();
		}
		switch ($I_industryID){
			case 1:
				$where['I_industryID'] = 1;//钢铁
				break;
			case 2:
				$where['I_industryID'] = 2;//化工
				break;
		}

		$param['type'] = $type;
		$param['I_industryID'] = $I_industryID;
		$list = $this->findCarsModel->where($where)->order(['createtime'=>'desc'])->paginate(5,false,['query'=>$param]);
		$listdata=[];
		foreach ($list->items() as $n=>$vo ) {
			$listdata[$n] = $vo->toArray();
			$listdata[$n]['order'] = $this->findCarsModel->getMQuery(['a.id' => $vo['id']])->find();
			$listdata[$n]['goods'] = db('sm_findcars_list')->where(" state=1 and I_findcarsID={$vo['id']}")->select();
			$listdata[$n]['count'] = count($listdata[$n]['goods']);
		}
		$this->assign([
			'uid'=>$uid,
			'count'=>$count,
			'listdata'=>$listdata,
			'param'=>$param,
			'type'=>$type,
			'I_industryID'=>$I_industryID,
			'industryModel'=>$this->industryModel,
			'findCarsModel'=>$this->findCarsModel,
			'list'=>$list,
		]) ;
		return $this->fetch('list') ;
	}


	/**
	 * 查看订单
	 */
	public function orderInfo ($id=0) {
		$id = $this->request->get('id',0);
		$uid = $this->getSessionUid();
		if(!$id){
			$this->error('不合法的请求');
		}
		$data = $this->findCarsModel->getMQuery(['a.id'=>$id,'a.I_userID'=>$uid])->find();
		$data['goods']=db('sm_findcars_list')->where(" state=1 and I_findcarsID={$id}")->select();
		$this->assign([
			'model' =>$this->findCarsModel,
			'data'=>$data,
		]) ;
		return $this->fetch('info');

	}
	/**
	 * 取消订单
	 * return json
	 */
	public function cancel ($id=0) {

		$id = $this->request->get('id',0);
		if(!$id){
			return getJsonStrError('不合法的请求');
		}
		$status=$this->findCarsModel->getById($id)['I_status'];
		if($status!=1){
			return getJsonStrError('请刷新页面!');
		}
		$data['I_status'] = 4;
		$uprow = $this->findCarsModel->update($data,['id'=>$id]);
		if($uprow){
			return getJsonStrSuc('取消成功');
		}else{
			return getJsonStrError('取消失败');
		}
	}
	/**
	 * 检查修改时订单状态
	 * @param int $id
	 * @return mixed
	 */
	public function checkModifyStatus($id=0){
		if($id==0){
			return getJsonStrError('参数错误!');
		}
		$status=$this->findCarsModel->getById($id)['I_status'];
		if($status!=1){
			return getJsonStrError('请刷新页面!');
		}
		return getJsonStrSucNoMsg(['id'=>$id]);
	}
}