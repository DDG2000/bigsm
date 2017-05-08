<?php
namespace app\home\controller ;

use app\common\model\FindGoodsModel;
use app\common\model\IndustryModel;
use app\common\controller\MyValidate;
use app\common\model\SlidesModel;
use Symfony\Component\HttpFoundation\Cookie;
use \think\Request;
use app\common\model\UserModel;
use GuzzleHttp\json_encode;
class FindGoods extends WorkbenchController {

	private $findGoodsModel,$industryModel,$userModel;
	public function __construct() {

		parent::_initialize() ;
		$this->findGoodsModel = new FindGoodsModel;
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
	 * @param $type 1确认页面 2 修改页面从COOKIE读取数据
	 * @param int $id 修改数据库读取数据
	 * @return mixed
	 */
	public function toForm($type=0,$id=0){
		//获取id
		if($type==1 && isset($id)){
			$uid = $this->getSessionUid();
			$order = $this->findGoodsModel->getMQuery(['a.id'=>$id,'a.I_userID'=>$uid])->find();
			$goods=db('sm_findgoods_list')->where(" state=1 and I_findgoodsID={$id}")->select();
			$addr['proname1']=$order['proname'];
			$addr['cityname1']=$order['cityname'];
			$addr['areaname1']=$order['areaname'];
		}
		//获取cookie数据
		else if($type==2 && cookie('findGoodsOrder') && cookie('findGoodsData')){
			$order=unserialize(cookie('findGoodsOrder'));
			$goodsCode=unserialize(cookie('findGoodsData'));
			$addr=unserialize(cookie('findGoodsAddr'));
			$note=unserialize(cookie('findGoodsNote'));
			$order['T_note']=$note;
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
		}
		if(isset($order)){
			$this->assign([
				'order'=>$order,
				'goods'=>$goods,
				'addr'=>$addr,
				'findGoodsModel'=>$this->findGoodsModel,
			]);
		}
		$this->assign([
			'findGoodsModel'=>$this->findGoodsModel,
			'title'=>'找货',
			'industryModel'=>$this->industryModel,
			'GoodsJudgePrice'=>db('configure')->where('code','GoodsJudgePrice')->value('value'),
		]);
		return $this->fetch('form') ;
	}
	//添加找货
	public function submitAdd(){
		$rules = [
			['I_provinceID','gt:0','未选择收货地址-省！'],
			['I_cityID','gt:0','未选择收货地址-市！'],
			['I_districtID','gt:0','未选择收货地址-区！'],
			['Vc_address','require|strLenMax:50','未输入收货地址详细信息！|详细地址字数超过50限制'],
			['I_paytype','in:1,2','未选择支付方式！'],
			['D_end','require','未输入报价截止时间！'],
			['Vc_consignee','require|strLenMax:30','未输入收货人姓名！|收货人姓名字数超过30限制'],
			['Vc_phone','require|phone:1','未输入收货人联系方式！|收货人联系方式输入错误'],
//			['I_industryID','egt:0','未选择行业！'],
		];
		//找货订单信息
		$da=array();
		//id存在就是更新,0就是新增,id,Vc_orderSn放在隐藏域
		$da['id']=input('post.id/d',0);
		$da['Vc_orderSn']=input('post.Vc_orderSn/s','');
		$da['I_industryID']=input('post.I_industryID/d',0);
		$da['I_paytype']=input('post.I_paytype/d',0);
		$da['D_end']=input('post.D_end/s','');
		$da['I_provinceID']=input('post.I_provinceID/d',0);
		$da['I_cityID']=input('post.I_cityID/d',0);
		$da['I_districtID']=input('post.I_districtID/d',0);
		$da['Vc_address']=input('post.Vc_address/s','','trim');
		$da['Vc_consignee']=input('post.Vc_consignee/s','','trim');
		$da['Vc_phone']=input('post.Vc_phone/s','');
		$da['N_judge_totalprice']=input('post.N_judge_totalprice/s','');
		$da['T_note']=input('post.T_note/s','','trim');
		$da['N_judge_totalprice']=input('post.N_judge_totalprice/s','');
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
		cookie('findGoodsNote',serialize($da['T_note']));
		unset($da['T_note']);
		cookie('findGoodsAddr',serialize($addr));
		cookie('findGoodsOrder',serialize($da));
		//货物信息
		$data=array();
		$cda = array();
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
		cookie('findGoodsData',serialize($cda));
		return getJsonStrSuc('',[],url('find_goods/listAdd'));
	}
	/**
	 * @return mixed
	 */
	public function listAdd(){
		$order=unserialize(cookie('findGoodsOrder'));
		$goodsCodes=unserialize(cookie('findGoodsData'));
		$addr=unserialize(cookie('findGoodsAddr'));
		$note=unserialize(cookie('findGoodsNote'));
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
			'title'=>'找货(订单确认)',
			'count'=>$count,
			'addr'=>$addr,
			'findGoodsModel'=>$this->findGoodsModel,
			'industryModel'=>$this->industryModel,
			'user'=>$this->userModel->getById($this->getSessionUid()),
		]);
		return $this->fetch('listadd') ;
	}
	/**
	 * 确认添加
	 * @return mixed
	 */
	public function comfirmAdd(){
		if(!cookie('findGoodsOrder') && !cookie('findGoodsData')){
			return getJsonStrError('获取数据失败或没有提交数据！');
		}
		$order=unserialize(cookie('findGoodsOrder'));
		$goods=unserialize(cookie('findGoodsData'));
		$note=unserialize(cookie('findGoodsNote'));
		$order['T_note']=$note;
		$temp1=$order;
		$temp2=$goods;
		if($this->findGoodsModel->checkOrderOne($temp1['Vc_orderSn'])){
//			cookie('findGoods',null);
			return getJsonStrError('订单已提交,请勿重复提交！');
		}
		$temp1['I_userID']=$this->getSessionUid();
		$temp1['I_status']=FindGoodsModel::STATUS_CHECKING;
		$this->findGoodsModel->startTrans();//在第一个模型里启用就可以了，或者第二个也行
		//更新数据
		if($temp1['id']!=0){
			foreach ($temp2 as $v){
				$goodsArr = db('erp_goods_tree')->where('Vc_goods_code',$v['Vc_goods_code'])->find();
				$v['Vc_goods_type'] = $goodsArr['Vc_goods_type'];
				$v['Vc_goods_class'] = $goodsArr['Vc_goods_class'];
				$v['Vc_goods_factory'] = $goodsArr['Vc_goods_factory'];
				$v['Vc_goods_breed'] = $goodsArr['Vc_goods_breed'];
				$v['Vc_goods_material'] = $goodsArr['Vc_goods_material'];
				$v['Vc_goods_spec'] = $goodsArr['Vc_goods_spec'];
				$v['Createtime']=date("Y-m-d H:i:s");
				$v['I_findgoodsID']=$temp1['id'];
				$temp3[]=$v;
			}
			$re1=$this->findGoodsModel->update($temp1,['id'=>$temp1['id']]);
			$re2=db('sm_findgoods_list')->where(['I_findgoodsID'=>$temp1['id']])->update(['state'=>0]);
			$re3=db('sm_findgoods_list')-> insertAll($temp3);
			if($re1 && $re2 && $re3){
				$this->findGoodsModel->commit();//成功则提交
				cookie('findGoods',null);
				return getJsonStrSuc('保存成功！');
			}else{
				$this->findGoodsModel->rollback();//不成功，则回滚
				return getJsonStrError('获取数据失败或没有提交数据！');
			}
		}else{//新增数据
			$re1=$insrow = $this->findGoodsModel->save($temp1);
			//echo $this->findGoodsModel->getLastSql();die;
			foreach ($temp2 as $v){
				$goodsArr = db('erp_goods_tree')->where('Vc_goods_code',$v['Vc_goods_code'])->find();
				$v['Vc_goods_type'] = $goodsArr['Vc_goods_type'];
				$v['Vc_goods_class'] = $goodsArr['Vc_goods_class'];
				$v['Vc_goods_factory'] = $goodsArr['Vc_goods_factory'];
				$v['Vc_goods_breed'] = $goodsArr['Vc_goods_breed'];
				$v['Vc_goods_material'] = $goodsArr['Vc_goods_material'];
				$v['Vc_goods_spec'] = $goodsArr['Vc_goods_spec'];
				$v['Createtime']=date("Y-m-d H:i:s");
				$v['I_findgoodsID']=$insrow;
				$temp[]=$v;
			}
			$re2=db('sm_findgoods_list')-> insertAll($temp);
			if($re1 && $re2){
				$this->findGoodsModel->commit();//成功则提交
//				cookie('findGoods',null);
				return getJsonStrSuc('保存成功！');
			}else{
				$this->findGoodsModel->rollback();//不成功，则回滚
				return getJsonStrError('获取数据失败或没有提交数据！');
			}
		}
	}

	/**
	 * 工作台找货列表
	 * @param number $I_status 报价状态 1待报价 2已报价 3取消
	 */
	public function listpage ($type=0) {
		$uid = $this->getSessionUid();
		$I_status = $this->request->get('I_status',0);
		$where=$sqlwhere='';
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
				$where['I_status'] = 4;//已取消
				break;
		}
		//审核 1审核中 2通过 3未通过 4取消
		$count['wait'] =db('sm_findgoods')->where(" state=1 and I_userID={$uid} and I_status=1 ")->count();
		$count['pass'] =db('sm_findgoods')->where(" state=1 and I_userID={$uid} and I_status=2 ")->count();
		$count['deny'] =db('sm_findgoods')->where(" state=1 and I_userID={$uid} and I_status=3 ")->count();
		$count['cancel'] =db('sm_findgoods')->where(" state=1 and I_userID={$uid} and I_status=4 ")->count();
		$param['type'] = $type;
		$list = $this->findGoodsModel->where($where)->order(['createtime'=>'desc'])->paginate(5,false,['query'=>$param]);
		$listdata=[];
		foreach ($list->items() as $n=>$vo ) {
			$listdata[$n] = $vo->toArray();
			$listdata[$n]['order'] = $this->findGoodsModel->getMQuery(['a.id' => $vo['id']])->find();
			$listdata[$n]['goods'] = db('sm_findgoods_list')->where(" state=1 and I_findgoodsID={$vo['id']}")->select();
			$listdata[$n]['count'] = count($listdata[$n]['goods']);
			$total=0;
			foreach ($listdata[$n]['goods'] as $v){
				if($v['N_offer_totalprice']){
					$total=bcadd($total,$v['N_offer_totalprice'],2);//精确计算
				}
			}
			$listdata[$n]['total']=$total;
		}
		$this->assign([
			'uid'=>$uid,
			'count'=>$count,
			'listdata'=>$listdata,//详细信息
			'type'=>$type,
			'list'=>$list,//分页信息
			'findGoodsModel'=>$this->findGoodsModel,
		]) ;
		return $this->fetch('list') ;
	}
	/**
	 * 订单详情
	 */
	public function orderInfo ($id=0) {

		$id = $this->request->get('id',0);
		$uid = $this->getSessionUid();
		if(!$id){
			$this->error('不合法的请求');
		}
		$data = $this->findGoodsModel->getMQuery(['a.id'=>$id,'a.I_userID'=>$uid])->find();
		$data['goods'] = db('sm_findgoods_list')->where(" state=1 and I_findgoodsID={$id}")->select();
		$total=0;
		foreach ($data['goods'] as $v){
			if($v['N_offer_totalprice']){
				$total=bcadd($total,$v['N_offer_totalprice'],2);//精确计算
			}
		}
		$data['total']=$total;
		$this->assign([
			'model' =>$this->findGoodsModel,
			'data'=>$data,
			'vo'=>$data,
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
		$status=$this->findGoodsModel->getById($id)['I_status'];
		if($status!=1){
			return getJsonStrError('请刷新页面!');
		}
		$data['I_status'] = 4;
		$uprow = $this->findGoodsModel->update($data,['id'=>$id]);
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
		$status=$this->findGoodsModel->getById($id)['I_status'];
		if($status!=1){
			return getJsonStrError('请刷新页面!');
		}
		return getJsonStrSucNoMsg(['id'=>$id]);
	}
}