<?php
namespace app\home\controller;
use think\Controller;
use app\common\model\SlidesModel;
use app\common\controller\HomeController;
use \app\admin\model\ArticleClassModel ;
use \app\admin\model\LinkModel ;
use app\common\model\FindGoodsModel;
use app\common\model\IndustryModel;
use app\common\model\FindCarsModel;
use app\common\controller\MyValidate;
class Index extends HomeController
{
    private $slidesModel,$findCarsModel,$linkModel,$findGoodsModel,$industryModel;
    public function __construct() {
        $this->findGoodsModel = new FindGoodsModel;
        $this->findCarsModel = new FindCarsModel;
        $this->industryModel = new IndustryModel;
        $this->slidesModel = new SlidesModel;
        $this->linkModel = new LinkModel;
        $this->articleClassModel = new ArticleClassModel();
        parent::__construct();
        $userStatus = $this->getUserStatus();
        $userStatusInfo = $this->getCertifyStatusInfo();
        $this->getNewsFlow();
        $this->assign([
            'newsflow'=>  cache('newsflow'),
            'userCertifyStatus'=>$userStatus,
            'userCertifyStatusInfo'=>$userStatusInfo,
        ]);
    }
    public  function getCertifyStatusInfo(){
        $userStatus=4;
        if(isset($_SESSION['user'])){
	    $uid = $this->getSessionUid();
	    $userStatus = db('sm_user_company')->where('I_userID',$uid)->value('I_status') ;
        }
	    return  $userStatus;
	    
	
	}
    
    /**
     * 
     */
    public  function getUserStatus()
    {
        $userStatus= false;
        if(isset($_SESSION['user'])){
            $uid = $this->getSessionUid();
            $uStatus = db('sm_user_company')->where(['I_userID'=>$uid])->value('I_status') ;
            if ($uStatus == 3) {
        
                $userStatus= true;
            }
            
        }
        return $userStatus;
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
     * 获取底部文章
     * @return mixed
     */
    public function getNewsFlow(){
        if(!cache('newsflow')){
            $re=db('sm_article_class')->field('id')->where(['I_isLeftMenu'=>2,'state'=>1])->order('Createtime','desc')->find();
            $newsflow=db('sm_article')->where(['I_article_classID'=>$re['id'],'state'=>1])->order('Createtime','desc')->limit(10)->select();
            cache('newsflow',$newsflow,120);
        }
    }
    /**大首页
     * @return mixed
     */
    public function index()
    {
        
//        $slidesModel=new SlidesModel;
        $param['Needs']= db('configure')->where('code','IndexFindMoneyNums')->value('value');
        $param['Totals']= db('configure')->where('code','IndexFindMoneyTotals')->value('value');
        $param['Goods']= db('configure')->where('code','IndexGoods')->value('value');
    
        
        
        $this->assign([
            'slide'=> $this->slidesModel->getSlideByType(SlidesModel::STATUS_HOME),
        
            'param'=>$param,
        ]);
        //return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
//         return 'Hello,World这是前台！';
        return $this->fetch() ;
    }
    public function findMoney(){
        $this->assign([
            'slide'=>$this->slidesModel->getSlideByType(SlidesModel::STATUS_MONEY),
            'links'=>$this->linkModel->getFour(LinkModel::LINK_FIND_MONEY),
        ]);
        return $this->fetch('findmoney') ;
    }
    public function findGoods(){
        $param['Salers']= db('configure')->where('code','FindGoodsSalers')->value('value');
        $param['Needs']= db('configure')->where('code','FindGoodsNeeds')->value('value');
        $param['Deels']= db('configure')->where('code','FindGoodsDeels')->value('value');
        $this->assign([
            'slide'=>$this->slidesModel->getSlideByType(SlidesModel::STATUS_GOODS),
            'param'=>$param,
            'links'=>$this->linkModel->getFour(LinkModel::LINK_FIND_GOODS),
        ]);
        return $this->fetch('findgoods');
    }
    public function findGoodsIndex(){
        if($this->request->isPost()){
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
        }else{
            $this->assign([
                'findGoodsModel'=>$this->findGoodsModel,
                'title'=>'找货',
                'industryModel'=>$this->industryModel,
                'GoodsJudgePrice'=>db('configure')->where('code','GoodsJudgePrice')->value('value'),
            ]);
            return $this->fetch('find_goods/form') ;
        }
    }
    public function findCars(){
        $param['Teams']= db('configure')->where('code','FindCarsTeams')->value('value');
        $param['Needs']= db('configure')->where('code','FindCarsNeeds')->value('value');
        $param['Dilivers']= db('configure')->where('code','FindCarsDilivers')->value('value');
        $this->assign([
            'slide'=>$this->slidesModel->getSlideByType(SlidesModel::STATUS_CARS),
            'param'=>$param,
            'links'=>$this->linkModel->getFour(LinkModel::LINK_FIND_CARS),
        ]);
        return $this->fetch('findcars') ;
    }
    public function findCarsIndex(){
        if($this->request->isPost()){
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
        }else{
            $this->assign([
                'findCarsModel'=>$this->findCarsModel,
                'title'=>'找车',
            ]);
            return $this->fetch('find_cars/form') ;
        }
    }
}
