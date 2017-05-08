<?php
/**
 * 购物车流程
 */
if(!defined('WEBROOT'))exit;
//新建购物车类
require(WEBROOTINCCLASS . 'ShopCar.php');
//新建地址管理类
require(WEBROOTINCCLASS . 'Address.php');
//生成随机融资单号
require_once(WEBROOTINCCLASS . 'OrderUtils.php');
//申请融资类
require(WEBROOTINCCLASS . 'Funding.php');
$user=new User();

//获取用户企业认证信息
$re=$user->getCompanyStatus($uid);
if($re['I_status']!=30){returnjson(array('err'=>-5,'msg'=>'请先完成公司认证'));}

$shopCar=new ShopCar();
$a=new address();
$no=new OrderUtils();
$fund=new Funding();
//获取操作方式
$w=$FL->requeststr('w',1,0,'w',1,1);
switch($w){
    //展示获取购物车信息
    case 'list':
        /**
         * @author zy
         * ajax
         * 购物车
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=shopcar&w=list
         * 输入：需登录后访问
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * shopcar;array 购物车信息
         *      0=>id;int 购物车id
         *      I_commodityID:str 商品id
         *      Vc_name_item:str 品名
         *      Vc_name_shop:str 卖家
         *      Vc_name_warehouse:str 仓库
         *      Vc_name_stuff:str 材质
         *      Vc_name_specification:str 规格
         *      Vc_name_factory:str 钢厂
         *      Vc_name_class:str 商品分类
         *      N_amount:decimal 商品数量
         *      N_weight:decimal 产品重量
         *      N_price:decimal 产品价格
         *      stotal:float 小计
         * stotal:float 总计
         * */
        $re=$shopCar->listCar($uid);
        if($re){
            $p['err']=0;
            $p['msg']='ok';
            $p['data']=$re;
        }else{
            $p['err']=-1;
            $p['msg']='无数据';
        }
        //返回json数据,即购物车信息
        break;
    //添加购物车
    case 'add':
        /**
         * @author zy
         * ajax
         * 购物车
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=shopcar&w=add
         * 输入：需登录后访问
         * I_commodityID:int 商品id
         * N_amount: 商品数量
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * shopcar;array 购物车信息
         *      0=>id;int 购物车id
         *      I_commodityID:str 商品id
         *      Vc_name_item:str 品名
         *      Vc_name_shop:str 卖家
         *      Vc_name_warehouse:str 仓库
         *      Vc_name_stuff:str 材质
         *      Vc_name_specification:str 规格
         *      Vc_name_factory:str 钢厂
         *      Vc_name_class:str 商品分类
         *      N_amount:decimal 商品数量
         *      N_weight:decimal 产品重量
         *      N_price:decimal 产品价格
         *      stotal:float 小计
         * stotal:float 总计
         * */

        //获取商品的id,数量
        $da['I_commodityID']=$FLib->requestint('I_commodityID',0,11,'商品ID',1) ;
        $da['N_amount']=isset($_GET['N_amount'])?intval($_GET['N_amount']):'';
        //判断参数是否定义
        if(empty($da['I_commodityID'])&&empty($da['N_amount'])){returnjson(array('err'=>-1,'msg'=>'参数有误'));}
        //判断购物车是否已经存在该商品,通过用户id和商品id判断
        $data=$shopCar->getGoods($uid,$da);
        //如果存在该商品,商品数量相加,保存
        if($data){
            $da['N_amount']+=intval($data['N_amount']);
            //保存商品信息
            $re=$shopCar->addCar($uid,$da,0);
            if(!$re){returnjson(array('err'=>-2,'msg'=>'添加购物车失败'));}
            //如果商品不存在,新加该商品信息
        }else{
            $re=$shopCar->addCar($uid,$da,1);
            if(!$re){returnjson(array('err'=>-3,'msg'=>'添加购物车失败'));}
        }
        returnjson(array('err'=>0,'msg'=>'添加购物车成功'));
        break;
    //删除购物商品,包括批量删除
    case 'delete':
        /**
         * @author zy
         * ajax
         * 购物车
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=shopcar&w=list
         * 输入：需登录后访问
         * I_commodityID:array 商品id
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        //获取商品id
        $da['I_commodityID']=$FLib->requestint('I_commodityID',0,11,'商品ID',1) ;
        //修改购物车商品的状态
        //判断是否是批量删除
        if(count($da['I_commodityID'])>1){
            $da['I_commodityID']=implode(',',$da['I_commodityID']);
            $sqlw="I_userID=$uid and I_commodityID in {$da['I_commodityID']}";
        }else{
            $sqlw="I_userID=$uid and I_commodityID={$da['I_commodityID']}";
        }
        $re=$shopCar->update(array('Status'=>0),$sqlw);
        if(!$re){returnjson(array('err'=>-2,'msg'=>'删除商品失败'));}
        returnjson(array('err'=>0,'msg'=>'删除商品成功'));
        break;
    //现金购买 购物信息可能有改动,要编辑一下
    case 'cash':
        /**
         * @author zy
         * ajax
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=shopcar&w=cash
         * 输入：需登录后访问
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * shopcar;array 购物车信息
         *      0=>id;int 购物车id
         *      I_commodityID:str 商品id
         *      Vc_name_item:str 品名
         *      Vc_name_shop:str 卖家
         *      Vc_name_warehouse:str 仓库
         *      Vc_name_stuff:str 材质
         *      Vc_name_specification:str 规格
         *      Vc_name_factory:str 钢厂
         *      Vc_name_class:str 商品分类
         *      N_amount:decimal 商品数量
         *      N_weight:decimal 产品重量
         *      N_price:decimal 产品价格
         *      stotal:float 小计
         * stotal:float 总计
         * address:array 地址
         *      0=>id:int 地址编号
         *          Vc_consignee:str 收件人
         *          Vc_consignee_phone: str 收件人电话
         *          detailaddress:收件人地址
         * */
        //更新所有的购物信息
        $da=$_POST;
        $re=$shopCar->updateAll($uid,$da);
        if(!$re){returnjson(array('err'=>-1,'msg'=>'更新购物车失败'));}
        //获取送货清单
        $re=$shopCar->listCar($uid);
        if(!$re){returnjson(array('err'=>-2,'msg'=>'获取失败'));}
        //获取收件人信息
        $re['address']=$a->getAll($uid);
        returnjson(array('err'=>0,'msg'=>'获取成功','data'=>$re));
        break;
    //融资
    case 'finance':
        /**
         * @author zy
         * ajax
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=shopcar&w=finance
         * 输入：需登录后访问
         * I_commodityID:array 商品id
         * N_amount:array 商品数量
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        //更新所有的购物信息
        $data=$_POST;
        $re=$shopCar->updateAll($uid,$data);
        if(!$re){returnjson(array('err'=>-1,'msg'=>'更新购物车失败'));}
        //读取购物车,将数据保存到融资表
        //从购物车中读取购物信息,以卖家的id为键名
        $goods=$shopCar->getCar($uid);
        if(!$goods){returnjson(array('err'=>-2,'msg'=>''));}
        //融资单号数据
        $da['I_buyerID']=$uid;
        $da['Vc_fundNO']=$no->getOrderNo();
        // 审核状态在前台直接保存  与审核流程有点矛盾
        $da['I_status']='10';
        //计算一个融资单的商品总价
        $total=0;
        foreach($goods as $k=>$v){
            $total+=$v['N_amount']*$v['N_price'];
        }
        $da['N_amount_price']=$total;
        //添加订单,返回订单编号,区别订单号
        $re=$shopCar->addFund($da);
        if(!$re){returnjson(array('err'=>-3,'msg'=>''));}
        //融资单编号
        $si['I_fundID']=$re;
        //循环,产生订单内每个商品的信息,添加商品信息
        foreach($goods as $k=>$v){
            $si['I_commodityID']=$v['I_commodityID'];
            $si['I_shopID']=$v['I_shopID'];
            $si['N_amount']=$v['N_amount'];
            $si['N_price']=$v['N_price'];
            $si['N_amount_price']=$v['N_amount']*$v['N_price'];
            //添加融资商品
            $re=$shopCar->addFundCom($si);
            if(!$re){returnjson(array('err'=>-3,'msg'=>''));}
        }
        //添加融资申请
        //融资名称用户名电话总价,内容商品详情
        $userinfo=$user->getInfo($uid,'Vc_truename,Vc_mobile');
        //融资内容,购买商品的信息(商品的所有信息)价格
        $re=$shopCar->listCar($uid);
        if(!$re){returnjson(array('err'=>-4,'msg'=>'获取失败'));}
        $content='';
        foreach($re['shopcar'] as $k=>$v){
            $content.=$v['Vc_name_item'].' '.$v['Vc_name_shop'].' '.$v['Vc_name_stuff'].' '.
                $v['Vc_name_specification'].' '.$v['Vc_name_factory'].' '.$v['N_amount'].'*'.$v['N_price'].'='.$v['stotal'].'/n/t';
        }
        //融资单号
        $re=$fund->create($si['I_fundID'],$userinfo,$total,$content);
        if(!$re){returnjson(array('err'=>-5,'msg'=>''));}
        //清空购物车
        $re=$shopCar->cleanCar($uid);
        if(!$re){returnjson(array('err'=>-6,'msg'=>''));}
        returnjson(array('err'=>0,'msg'=>'添加融资成功'));
        break;
}