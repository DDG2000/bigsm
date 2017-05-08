<?php
namespace app\home\controller ;

use app\common\model\FindMoneyModel;
use app\common\model\IndustryModel;
use app\common\controller\MyValidate;
use app\common\model\SlidesModel;
use Symfony\Component\HttpFoundation\Cookie;
use \think\Request;
use app\common\model\UserModel;
use GuzzleHttp\json_encode;
class FindMoney extends WorkbenchController {

	private $findMoneyModel,$slidesModel,$industryModel,$userModel;
	public function __construct() {

		parent::_initialize() ;
		$this->findMoneyModel = new FindMoneyModel;
		$this->industryModel = new IndustryModel;
		$this->slidesModel = new SlidesModel;
		$this->userModel = new UserModel;
		parent::__construct();
		//$this->check_certify();
	}
	/**
	 * 到表单页面 新建/确认后修改cookie/工作台修改数据库
	 * @param $a 1确认页面 2 修改页面从COOKIE读取数据
	 * @param int $id 修改数据库读取数据
	 * @return mixed
	 */
	public function toForm($type=0,$id=0){
		//获取id
		if($type==1 && $id!=0){
			$uid = $this->getSessionUid();
			$order = $this->findMoneyModel->getMQuery(['a.id'=>$id,'a.I_userID'=>$uid])->find();
			$tels=db('sm_findmoney_tel')->where(" state=1 and I_findmoneyID={$order['id']}")->select();
			$addr['proname1']=$order['proname1'];
			$addr['cityname1']=$order['cityname1'];
			$addr['areaname1']=$order['areaname1'];
			$addr['proname2']=$order['proname2'];
			$addr['cityname2']=$order['cityname2'];
			$addr['areaname2']=$order['areaname2'];
		}//获取cookie数据
		else if($type==2 && cookie('findMoneyOrder') && cookie('findMoneyTel')){
			$order=unserialize(cookie('findMoneyOrder'));
			$tels=unserialize(cookie('findMoneyTel'));
			$addr=unserialize(cookie('findMoneyAddr'));
			$note=unserialize(cookie('findMoneyNote'));
			$order['T_note']=$note;
		}
		if(isset($order)){
			$this->assign([
				'order'=>$order,
				'tels'=>$tels,
				'addr'=>$addr,
				'title'=>'资金申请',
			]);
		}
		$this->assign([
			'title'=>'资金申请',
		]);
		return $this->fetch('form') ;
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
		$status=$this->findMoneyModel->getById($id)['I_status'];
		if($status!=1){
			return getJsonStrError('请刷新页面!');
		}
		return getJsonStrSucNoMsg(['id'=>$id]);
	}
	/**
	 * 提交表单数据  保存在cookie
	 * @return mixed
	 */
	public function submitAdd(){
		$rules = [
			['Vc_ordername','require|strLenMax:20','未输入存货品名！|存货品名字数超过20限制'],
			['Vc_level','require|strLenMax:11','未输入存货规格等级！|存货规格等级字数超过11限制'],
			['N_amount','require','未输入数量！'],
			['I_cave_provinceID','gt:0','未选择存货地点-省！'],
			['I_cave_cityID','gt:0','未选择存货地点-市！'],
			['I_cave_districtID','gt:0','未选择存货地点-区！'],
			['Vc_cave_address','require|strLenMax:50','未输入存货地点详细地址！|详细地址字数超过50限制'],
			['I_belong_provinceID','gt:0','未选择货权单位-省！'],
			['I_belong_cityID','gt:0','未选择货权单位-市！'],
			['I_belong_districtID','gt:0','未选择货权单位-区！'],
			['Vc_belong_address','require|strLenMax:50','未输入货权单位详细地址！|详细地址字数超过50限制'],
			['N_needed','require|gt:0','未输入融资额度！|融资额度不能为0'],
			['N_days','require|gt:0','未输入融资期限！|融资天数不能为0'],
		];
		//找资金订单信息
		$da=array();
		//id存在就是更新,0就是新增,id,Vc_orderSn放在隐藏域
		$da['id']=input('post.id/d',0);
		$da['Vc_orderSn']=input('post.Vc_orderSn/s','');

		$da['Vc_ordername']=input('post.Vc_ordername/s','','trim');
		$da['Vc_level']=input('post.Vc_level/s','','trim');
		$da['N_amount']=input('post.N_amount/s','');
		$da['Vc_unit']=input('post.Vc_unit/s','');
		$da['I_cave_provinceID']=input('post.I_cave_provinceID/d',0);
		$da['I_cave_cityID']=input('post.I_cave_cityID/d',0);
		$da['I_cave_districtID']=input('post.I_cave_districtID/d',0);
		$da['Vc_cave_address']=input('post.Vc_cave_address/s','','trim');
		$da['I_belong_provinceID']=input('post.I_belong_provinceID/d',0);
		$da['I_belong_cityID']=input('post.I_belong_cityID/d',0);
		$da['I_belong_districtID']=input('post.I_belong_districtID/d',0);
		$da['Vc_belong_address']=input('post.Vc_belong_address/s','','trim');
		$da['N_needed']=input('post.N_needed/s','');
		$da['N_days']=input('post.N_days/s','');
		$da['T_note']=input('post.T_note/s','');
		$da['Createtime']=date("Y-m-d H:i:s");
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
		cookie('findMoneyNote',serialize($da['T_note']));
		unset($da['T_note']);
		cookie('findMoneyAddr',serialize($addr));
		cookie('findMoneyOrder',serialize($da));
		//联系人信息
		$data=$cda=array();
		$data['Vc_contacts']=input('post.Vc_contacts/a','','trim');
		$data['Vc_contact_tels']=input('post.Vc_contact_tels/a','');
		for ($i=0,$j=1;$i<count($data['Vc_contacts']);$i++,$j++){
			if(!$data['Vc_contacts'][$i]){
				return getJsonStr(500,"第".$j."个联系人未输入姓名");
			}
			if(mb_strlen($data['Vc_contacts'][$i],'UTF-8')>30){
				return getJsonStr(500,"第".$j."个联系人字数大于30限制");
			}
			if(!$data['Vc_contact_tels'][$i] || !funcphone($data['Vc_contact_tels'][$i])){
				return getJsonStr(500,"第".$j."个联系人未输入手机号或手机格式错误!");
			}
			$temp['Vc_contact'] = $data['Vc_contacts'][$i];
			$temp['Vc_contact_tel'] = $data['Vc_contact_tels'][$i];
			$temp['Createtime'] = date("Y-m-d H:i:s");
			$cda[] = $temp;
		}
		cookie('findMoneyTel',serialize($cda));
		return getJsonStrSuc('',[],url('find_money/listAdd'));

	}
	/**
	 * @return mixed
	 */
	public function listAdd(){
		$order=unserialize(cookie('findMoneyOrder'));
		$tels=unserialize(cookie('findMoneyTel'));
		$addr=unserialize(cookie('findMoneyAddr'));
		$note=unserialize(cookie('findMoneyNote'));
		$order['T_note']=$note;
		$count=count($tels);
		$this->assign([
			'order'=>$order,
			'tels'=>$tels,
			'addr'=>$addr,
			'count'=>$count,
			'title'=>'资金申请(订单确认)',
			'user'=>$this->userModel->getById($this->getSessionUid()),
		]);
		return $this->fetch('listadd') ;
	}
	/**
	 * 确认表单,保存/更新找资金
	 * @return mixed
	 */
	public function comfirmAdd(){
		if(!cookie('findMoneyOrder') && !cookie('findMoneyTel')){
			return getJsonStrError('获取数据失败或没有提交数据！');
		}
		$order=unserialize(cookie('findMoneyOrder'));
		$tels=unserialize(cookie('findMoneyTel'));
		$note=unserialize(cookie('findMoneyNote'));
		$order['T_note']=$note;
		$temp1=$order;
		$temp2=$tels;
		if($this->findMoneyModel->checkOrderOne($temp1['Vc_orderSn'])){
			return getJsonStrError('订单已提交,请勿重复提交！');
		}
		$temp1['I_userID']=$this->getSessionUid();
		$temp1['I_status']=FindMoneyModel::STATUS_CHECKING;
		//更新数据
		$this->findMoneyModel->startTrans();//在第一个模型里启用就可以了，或者第二个也行
		if($temp1['id']!=0){
			foreach ($temp2 as $v){
				$v['I_findmoneyID']=$temp1['id'];
				$temp3[]=$v;
			}
			$re1=$this->findMoneyModel->update($temp1,['id'=>$temp1['id']]);
			$re2=db('sm_findmoney_tel')->where(['I_findmoneyID'=>$temp1['id']])->update(['state'=>0]);//先删除电话
			$re3=db('sm_findmoney_tel')-> insertAll($temp3);//在提添加电话
			if($re1 && $re2 && $re3){
				$this->findMoneyModel->commit();//成功则提交
				return getJsonStrSuc('保存成功！');
			}else{
				$this->findMoneyModel->rollback();//不成功，则回滚
				return getJsonStrError('获取数据失败或没有提交数据！');
			}
		}else{//新增数据
			$re1=$insrow = $this->findMoneyModel->save($temp1);
			foreach ($temp2 as $r){
				$r['I_findmoneyID']=$insrow;
				$temp[]=$r;
			}
			$re2=db('sm_findmoney_tel')-> insertAll($temp);
			if($re1 && $re2){
				$this->findMoneyModel->commit();//成功则提交
				return getJsonStrSuc('保存成功！');
			}else{
				$this->findMoneyModel->rollback();//不成功，则回滚
				return getJsonStrError('获取数据失败或没有提交数据！');
			}
		}
	}

	/**
	 * 工作台找资金列表
	 * @param number $type
	 */
	public function listpage ($type=0) {


		$uid = $this->getSessionUid();
		$where['state']=1;
		$where['I_userID']=$uid;
		//筛选审核情况
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


		$count['wait'] =db('sm_findmoney')->where(" state=1 and I_userID={$uid} and I_status=1 ")->count();
		$count['pass'] =db('sm_findmoney')->where(" state=1 and I_userID={$uid} and I_status=2 ")->count();
		$count['deny'] =db('sm_findmoney')->where(" state=1 and I_userID={$uid} and I_status=3")->count();
		$count['cancel'] =db('sm_findmoney')->where(" state=1 and I_userID={$uid} and I_status=4")->count();
		$param['type'] = $type;
		$listdata=[];
		$list = $this->findMoneyModel->where($where)->order(['createtime'=>'desc'])->paginate(5,false,['query'=>$param]);
		foreach ($list->items() as $n=>$vo ) {
			$listdata[$n] = $vo->toArray();
			$listdata[$n]['order'] = $this->findMoneyModel->getMQuery(['a.id' => $vo['id']])->find();
			$listdata[$n]['tels'] = db('sm_findmoney_tel')->where(" state=1 and I_findmoneyID={$vo['id']}")->select();
		}
		$this->assign([
			'uid'=>$uid,
			'count'=>$count,
			'listdata'=>$listdata,
			'findMoneyModel'=>$this->findMoneyModel,
			'type'=>$type,
			'list'=>$list,
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
		$data = $this->findMoneyModel->getMQuery(['a.id'=>$id,'a.I_userID'=>$uid])->find();
		$tels=db('sm_findmoney_tel')->where(" state=1 and I_findmoneyID={$id}")->select();
		$count=count($tels);
		$this->assign([
			'model' =>$this->findMoneyModel,
			'data'=>$data,
			'tels'=>$tels,
			'count'=>$count,
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
		$status=$this->findMoneyModel->getById($id)['I_status'];
		if($status!=1){
			return getJsonStrError('请刷新页面!');
		}
		$data['I_status'] = 4;
		$uprow = $this->findMoneyModel->update($data,['id'=>$id]);
		if($uprow){
			return getJsonStrSuc('取消成功');
		}else{

			return getJsonStrError('取消失败');
		}
	}
}