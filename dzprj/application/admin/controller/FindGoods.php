<?php
namespace app\admin\controller ;
use think\paginator\driver\Bootstrap;
use app\common\model\UserModel;
use app\common\model\FindGoodsModel;
use app\common\model\IndustryModel;

class FindGoods extends AdminController {
    private $findGoodsModel,$industryModel;

    public function _initialize() {
        $this->findGoodsModel = new FindGoodsModel();
        $this->industryModel = new IndustryModel();
        parent::_initialize();
    }

    public function index($page=1) {
        $param['keywords'] = input('keywords/s','');
        $param['applystatus'] = input('applystatus/d',-1);
        $pages = $this->findGoodsModel->getPage($page,$param) ;
        $this->assign([
            'industryModel' =>$this->industryModel,
            'findgoods' =>$this->findGoodsModel,
            'page' =>$pages,
            'param'=>$param,
        ]) ;
        return $this->fetch("index") ;
    }


    /**
     * 获取合同对应公司在平台的所有认证用户
     * @return \think\response\Json
     */
    public function getUserList ($cname=0) {

        $where['Vc_contractSn']=trim($cname);
        $rs = db('erp_project')->where($where)->find();
        if($rs){

            $data = db('sm_user_company')->where('Vc_companycode',$rs['Vc_companycode'])->field('I_userID id,Vc_applicantName name')->select();

            if($data){
                return getJsonStrSucNoMsg($data);
            }else{
                return getJsonStrError('合同签约公司尚未在平台注册认证通过！');
            }

        }else{
            return getJsonStrError('合同号有误,或暂未同步到该erp数据！');
        }

    }


    public function  edit(){

        if($this->request->isPost()){
            $id = input('post.id/d',0);
            $data['I_industryID'] = input('post.I_industryID/d',0);
            $data['I_status'] = input('post.I_status/d',0);
            $uprow = db('sm_findgoods')->where('id',$id)->update($data);
            if($uprow > 0){
                $this->addManageLog('找货申请审核', '认证审核了id为'.$id.'的找货申请');
                return getJsonStrSuc('审核成功');
            }else{
                return getJsonStrError('审核失败');
            }

        }else{

            $id = $this->request->get('id',0);
            if(!$id){
                $this->error('信息未选择');
            }
            $data =  $this->findGoodsModel->getById($id);
            $goodsList = $this->findGoodsModel->getGoodsListById($id);
            $this->assign([
                'model' =>$this->industryModel,
                'findgoods' =>$this->findGoodsModel,
                'data'=>$data,
                'goodsList'=>$goodsList,
            ]) ;
            return $this->fetch();
        }

    }
    /**
     * 快速审核
     * @return mixed
     */
    public function  check(){
        $id = input('get.id/d',0);
        if(!$id){return getJsonStrSuc('参数1获取失败');}
        $check = input('get.check/d',0);
        if(!$check){return getJsonStrSuc('参数2获取失败');}
        if($check==1){
            $data['I_status']=FindGoodsModel::STATUS_PASS;
        }elseif($check==2){
            $data['I_status']=FindGoodsModel::STATUS_REJECT;
        }
        $uprow = db('sm_findgoods')->where('id',$id)->update($data);
        if($uprow > 0){
            $this->addManageLog('找货申请审核', '认证审核了id为'.$id.'的找货申请');
            return getJsonStrSuc('审核完成');
        }else{
            return getJsonStrError('审核失败');
        }
    }

    public function modifyAjax(){
        $id = input('get.id/d',0);
        $oid = input('get.orderId/d',0);
        $data['N_offer_amount'] = input('get.amount');
        $data['N_offer_price'] = input('get.price');
        $data['N_offer_totalprice'] = input('get.total');
        $data['I_status'] = FindGoodsModel::CHARGE_DONE;
//        $da['I_status']=FindGoodsModel::STATUS_PASS;
//        $this->findGoodsModel->startTrans();
        $re1 = db('sm_findgoods_list')->where('id',$id)->update($data);
//        $re2 = db('sm_findgoods')->where('id',$oid)->update($da);
        if($re1 ){
//            $this->findGoodsModel->commit();//成功则提交
            $this->addManageLog('找货报价', '编辑了id为'.$id.'的货物报价');
//            $this->addManageLog('找货申请审核', '认证审核了id为'.$id.'的找货申请');
            return getJsonStrSuc('报价成功');
        }else{
//            $this->findGoodsModel->rollback();//不成功，则回滚
            return getJsonStrError('报价失败');
        }
    }
    public function modifyIndustry(){
        $id = input('get.id/d',0);
        $data['I_industryID'] = input('get.industry');
        $uprow = db('sm_findgoods')->where('id',$id)->update($data);
        if($uprow > 0){
            $this->addManageLog('找货行业修改', '修改了id为'.$id.'的找货订单的行业');
            return getJsonStrSuc('行业修改成功');
        }else{
            return getJsonStrError('行业修改失败');
        }
    }

    public function Mul(){
        $number = floatval(input('number/s',''));
        $price = floatval(input('price/s',''));
        return bcmul($number,$price,2);
    }




}
