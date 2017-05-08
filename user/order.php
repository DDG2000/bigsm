<?php
/**
 * 订单处理
 */
if(!defined('WEBROOT'))exit;
//检验登录
if(empty($uid)){returnjson(array('err'=>-1,'msg'=>'获取用户参数失败'));}
//引入订单类
require(WEBROOTINCCLASS . 'Order.php');
//引入订单商品类
require_once(WEBROOTINCCLASS . 'OrderCommodity.php');
//引入生成商品订单类
require_once(WEBROOTINCCLASS . 'OrderUtils.php');
//引入购物车类
require(WEBROOTINCCLASS . 'ShopCar.php');
$order=new Order();
$ordercommodity=new OrderCommodity();
$no=new OrderUtils();
$shopCar=new ShopCar();
$u=new User();

//用户公司认证判断
$re=$u->getCompanyStatus($uid);
if($re['I_status']!=30){returnjson(array('err'=>-5,'msg'=>'请先完成公司认证'));}

$w=$FL->requeststr('w',1,0,'w',1,1);
$m.='_'.$w;
//默认访问展示订单页
if($w==''){$w='list';}
switch($w){
    
    //添加订单 直接从购物车中读取
    case 'add':
        /**
         * @author zy
         * 购物车添加订单接口
         * 获取购物车信息,跟新购物车表,添加购物车信息到订单表,清空购物车
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=order&w=add
         * 输入：需登录后访问
         * Vc_consignee:收货人
         * Vc_consignee_phone:收货人电话
         * Vc_consignee_address:收货人地址
         * Vc_consignee_company:收货人单位
         * 输出：
         * err:int 结果状态 <0失败 0成功
         * msg: 提示信息
         * */
        /**
         * 生成订单号
         * 根据不同的商家生成不同的订单号,首先获取商家的id,
         * 判断是否有多个商家,再根据不同的商家生成相应的订单号
         */
        //获取收件信息
        $da['I_buyerID']=$uid;
        $da['Vc_consignee']=$FLib->requeststr('Vc_consignee',0,50,'收货人',1) ;
        $da['Vc_consignee_phone']=$FLib->requeststr('Vc_consignee_phone',0,50,'收货人电话',1) ;
        $da['Vc_consignee_address']=$FLib->requeststr('Vc_consignee_address',0,50,'收货地址',1) ;
        $da['Vc_consignee_company']=$FLib->requeststr('Vc_consignee_company',0,50,'收货单位',1) ;
        //从购物车中读取购物信息,以卖家的id为键名
        $re=$shopCar->index($uid);
        if(!$re){returnjson(array('err'=>-1,'msg'=>'获取购物信息失败'));}
        //循环,一次循环就产生一个订单,添加订单信息
        foreach ($re as $kk=>$value){
            $total=0;
            //生成订单号
            $da['Vc_orderNO']=$no->getOrderNo();
            $da['I_shopID']=$kk;
            $da['I_status']='10';
            ////计算一个订单的商品总价
            foreach($value as $k=>$v){
                $total+=$v['N_amount']*$v['N_price'];
            }
            $da['N_amount_price']=$total;
            //添加订单,返回订单编号,区别订单号
            $re=$order->add($da);
            if(!$re){returnjson(array('err'=>-2,'msg'=>'添加订单失败'));}
            $si['I_orderID']=$re;
            //循环,产生订单内每个商品的信息,添加商品信息
            foreach($value as $k=>$v){
                $si['I_commodityID']=$v['I_commodityID'];
                $si['N_amount']=$v['N_amount'];
                $si['N_price']=$v['N_price'];
                $si['N_amount_price']=$v['N_amount']*$v['N_price'];
                //添加订单商品
                $re=$ordercommodity->add($si);
                if(!$re){returnjson(array('err'=>-3,'msg'=>'添加订单商品失败'));}
            }
        }
        //清除购物车
        $re=$shopCar->cleanCar($uid);
        if(!$re){returnjson(array('err'=>-4,'msg'=>'清空购物车失败'));}
        returnjson(array('err'=>0,'msg'=>'添加订单成功'));
        break;
    //展示订单 分页
    case 'list':
        /**
         * @author zy
         * 交易管理>我的订单接口
         * 展示我的订单
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=order&w=list
         * 输入：需登录后访问
         * I_status:int 订单状态(待审核10 已完成20 已取消60 待评价70)
         * keyword:str 关键字
         * starttime:str  开始时间
         * endtime:str 结束时间
         * CurrentPage:int 当前页
         * 输出:
         * I_status:int 订单状态
         * I_isapp:int 是否评价
         * keyword:str 关键字
         * starttime:str 开始时间
         * endtime:str 结束时间
         * pagestr:str 页码信息
         * data:array 分页信息
         *      page:int 当前页
         *      count:str 总条数
         *      pcount:float 页数
         *      data:array 订单详情
         *          id:int 订单号(隐藏域)
         *          Vc_orderNO:str 订单编号
         *          shopname:str 卖家公司
         *          Vc_phone:str 公司电话
         *          Createtime:str 创建时间
         *          Vc_consignee:str 收件人
         *          N_amount_price:str 订单总价
         *          I_status:str 订单状态
         *          goods=>array 商品信息
         *              I_commodityID:str 商品ID
         *              itemname:str 商品名
         *              stuffname:str 材质
         *              specificationname:str 规格名
         *              factoryname:str 钢厂名
         *              warehouse:str 仓库名
         *              N_amount:str 数量
         *              N_weight:str 重量
         *              N_price:str 单价
         *              N_amount_price:str 总价
         * */
        //获取搜索数据I_isapp 是否评价 1 未评价 2 已评价
        $da['I_status']=$FLib->requestint('I_status',0,'订单状态',1) ;
        $da['I_isapp']=$FLib->requestint('I_isapp',0,'是否评价',1) ;
        $da['keyword']=$FLib->requeststr('keyword',1,50,'关键字',1);
        $da['starttime']=$FLib->requeststr('starttime',1,50,'开始时间',1);
        $da['endtime']=$FLib->requeststr('endtime',1,50,'结束时间',1);
        $page=$FLib->requestint('CurrentPage',1,'当前页',1) ;
        //获取分页数
        $PageSize=$Cfg->OrderPageSize;
        //获取当页信息
        $re=$order->getPages($uid,$page,$PageSize,$da,1);
        //获取每单商品信息
        if($re['data']){
            foreach($re['data'] as $k=>$v){
                $oid=$v['id'];
                //获取当前单号所有商品
                $r=$ordercommodity->get($oid);
                //添加字段goods在每一订单中,包含所有商品
                $v['goods']=$r;
                $re['data'][$k]=$v;
            }
        }
        $pcount = $re['pcount'];
        
        $p['I_status'] =$da['I_status'];
        $p['I_isapp'] =$da['I_isapp'];
        $p['keyword'] =$da['keyword'];
        $p['starttime'] =$da['starttime'];
        $p['endtime'] =$da['endtime'];
        $p['data'] =$re;
        $p['pagestr'] = getPageStrFunSd($pcount, $page, "?act=user&m=order&w=list&starttime={$da['starttime']}&endtime={$da['endtime']}");
        break;
    //test
    case 'test':
        /**
         * @author zy
         * 交易管理>订单详情接口
         * 获取一个订单的详情,订单概况,收货信息,订货函,评价
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=order&w=test
         * 输入：需登录后访问
         * id:订单号
         * 输出：
         * */
        $da['starttime']=$FLib->requeststr('starttime',1,50,'开始时间',1);
        dump($_FILES);
        if($_FILES){
            echo 1;
        }
        echo 2;
        break;
    //订单详情
    case 'detail':
        /**
         * @author zy
         * 交易管理>订单详情接口
         * 获取一个订单的详情,订单概况,收货信息,订货函,评价
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=order&w=detail
         * 输入：需登录后访问
         * id:订单号
         * 输出：
         * err:int 结果状态 <0失败 0成功
         * msg: 提示信息
         * id:int 订单号(隐藏域)
         * Vc_orderNO:str 订单编号
         * I_status:str 订单状态
         * Createtime:str 创建时间
         * Dt_last_modify:str 完成时间
         * N_amount_price:str 订单总价
         * Vc_consignee:str 收件人
         * Vc_consignee_address:str 收件人公司
         * Vc_consignee_phone:str 收件人电话
         * Vc_consignee_address:str 收件人地址
         * goods=>I_commodityID:str 商品ID
         *      itemname:str 商品名
         *      stuffname:str 材质
         *      specificationname:str 规格名
         *      factoryname:str 钢厂名
         *      warehouse:str 仓库名
         *      N_amount:str 数量
         *      N_weight:str 重量
         *      N_price:str 单价
         *      N_amount_price:str 总价
         * app:array 评价
         *      quality:str 产品质量
         *      timely:str 物流及时性
         *      server:str 服务态度
         *      invoice:str 发票及时性
         *      Vc_text:str 评  论
         *      T_images:str 图片
         * I_status:int 订单状态 10：等待审核；20：已完成；30：待发货；40：待提货;50：商家取消；60：用户取消
         * I_isapp:int 评价状态 1未评价 2已评价  0(订单未完成,没有评价)
         * */
        $oid=$FLib->requestint('id',0,'订单编号',1) ;
        //获取一个订单详情,1卖家
        $re=$order->getDetail($oid,$uid,1);
        if(!$re){returnjson(array('err'=>-1,'msg'=>'获取订单详情失败'));}
        //获取订单的所有商品
        $r=$ordercommodity->get($oid);
        if(!$r){returnjson(array('err'=>-2,'msg'=>'获取订单商品失败'));}
        //添加goods键名,对应所有商品信息
        $re['goods']=$r;
        //获取订单的评价
        //获取订单的状态,没有完成就是非法操作
        $r=$order->getStatus($oid,$uid,2);
  
        //已完成

        $p['app']='';
        if($r['I_status']!=10){
            if($r['I_isapp']==2){$p['app']=$order->getAppraise($oid);}
        }
        $p['I_status']=$r['I_status'];
        $p['I_isapp']=$r['I_isapp'];
        $p['err']=0;
        $p['msg']='ok';
        $p['data']=$re;
        break;
    //删除订单 ajax
    case 'delete':
        /**
         * @author zy
         * 交易管理>删除订单接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=order&w=delete
         * 输入：需登录后访问
         * id:int 订单号
         * 输出：
         * err:int 结果状态 <0失败 0成功
         * msg: 提示信息
         * */
        //获取订单编号,并判断
        $oid=$FLib->requestint('id',0,'订单编号',1);
        //执行删除
        $re=$order->editOrder($oid,$uid);
        if(!$re){returnjson(array('err'=>-2,'msg'=>'删除失败'));}
        returnjson(array('err'=>0,'msg'=>'删除成功'));
        break;
    //取消订单 和删除订单差不多
    case 'cancel':
        /**
         * @author zy
         * 交易管理>取消订单接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=order&w=cancel
         * 输入：需登录后访问
         * id:int 订单号
         * 输出：
         * err:int 结果状态 <0失败 0成功
         * msg: 提示信息
         * */
        //获取订单编号,并判断
        $oid=$FLib->requestint('id',0,'订单编号',1) ;
        //获取订单状态,只有在订单状态为10时才能取消
        $re=$order->getStatus($oid,$uid,1);
        if($re['I_status']!=10){returnjson(array('err'=>-1,'msg'=>'审核中才能取消'));}
        //执行取消
        $re=$order->editOrder($oid,$uid,array('I_status'=>'60','Dt_last_modify@'=>'now()'));
        if(!$re){returnjson(array('err'=>-3,'msg'=>'取消失败'));}
        returnjson(array('err'=>0,'msg'=>'取消成功'));
        break;
    //查看电子订货函 可以前台js用已有数据 ajax
    case 'elecorder':
        /**
         * @author zy
         * 交易管理>查看电子订货函接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=order&w=elecorder
         * 输入：需登录后访问
         * id:订单号
         * 输出：
         * err:int 结果状态 <0失败 0成功
         * msg: 提示信息
         * N_amount_price:str 总价
         * goods:array 商品
         *    0=>I_commodityID:str 商品ID
         *      itemname:str 商品名
         *      stuffname:str 材质
         *      specificationname:str 规格名
         *      factoryname:str 钢厂名
         *      warehouse:str 仓库名
         *      N_amount:str 数量
         *      N_weight:str 重量
         *      N_price:str 单价
         *      N_amount_price:str 总价
         * */
        $oid=$FLib->requestint('id',0,'订单编号',1) ;
        //获取订单商品
        $re=$order->getDetail($oid,$uid,1);
        if(!$re){returnjson(array('err'=>-1,'msg'=>'获取订单详情失败'));}
        $re=$order->getStatus($oid,$uid,1);
        if($re['I_status']!=20){returnjson(array('err'=>-1,'msg'=>'订单未完成'));}
        $da['N_amount_price']=$re['N_amount_price'];
        $re=$ordercommodity->get($oid);
        if(!$re){returnjson(array('err'=>-1,'msg'=>'获取订单商品失败'));}
        $da['goods']=$re;
        $p['err']=0;
        $p['msg']='ok';
        $p['data']=$da;
        break;
    //评价
    case 'appraise':
        /**
         * @author zy
         * 交易管理>我的订单>评价接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=order&w=appraise
         * 评价展示页面
         * 输入：需登录后访问
         * id:订单号
         * 输出：
         * err:int 结果状态 <0失败 0成功
         * msg: 提示信息
         * data=>id:int 订单号(隐藏域)
         *      I_status:str 订单状态
         *      Dt_last_modify:str 完成时间
         *      goods=>array()
         *        0=>I_commodityID:str 商品ID
         *          itemname:str 商品名
         *          stuffname:str 材质
         *          specificationname:str 规格名
         *          factoryname:str 钢厂名
         *          warehouse:str 仓库名
         *          N_amount:str 数量
         *          N_weight:str 重量
         *          N_price:str 单价
         *          N_amount_price:str 总价
         *
         * 评价提交
         * 输入：需登录后访问
         * id:订单号
         * quality:产品质量
         * timely:时间及时性
         * server:服务态度
         * invoice:发票及时性
         * Vc_text:评论
         * T_images:上传图片
         * 输出：
         * err:int 结果状态 <0失败 0成功
         * msg: 提示信息
         * */
        //提交评价,新加
        if(isset($_REQUEST['quality'])){
            $da['I_orderID']=$FLib->requestint('id',0,'订单编号',1) ;
            //获取订单的状态,在完成时才能够添加
            $re=$order->getStatus($da['I_orderID'],$uid);
            if(!$re||$re['I_status']!=20){returnjson(array('err'=>-1,'msg'=>'订单未完成,不能评价'));}
            //获取数据
            $sc['quality']=$FLib->requeststr('quality',0,11,'产品质量',1) ;
            $sc['timely']=$FLib->requeststr('timely',0,11,'物流及时性',1) ;
            $sc['server']=$FLib->requeststr('server',0,11,'服务态度',1) ;
            $sc['invoice']=$FLib->requeststr('invoice',0,11,'发票及时性',1) ;
            //将评论连接成字符串
            $da['Vc_score']=implode(',',$sc);
            $da['Vc_text']=$FLib->requeststr('Vc_text',0,255,'评论',1) ;
            //多图片上传
            if ($_FILES)
            {
                $upload=new UploadFile();
                //调用多图片长传
                $re=$upload->mulitype($_FILES['T_images'],'Appraiseimage');
                if(!$re){returnjson(array('err'=>-2,'msg'=>'图片上传失败'));}
                $data=array();
                //获取多个图片的保存路径
                foreach ($re as $v){
                    $data[]=$v['pic'];
                }
                //将路径用逗号连接
                $da['T_images']=implode(',',$data);
            }
            //添加评价
            $re=$order->addAppraise($da,$da['I_orderID']);
            if(!$re){returnjson(array('err'=>-3,'msg'=>'评价失败'));}
            returnjson(array('err'=>0,'msg'=>'评价成功'));
            //展示新加评价页面商品信息
        }else{
            //获取订单编号
            $oid=$FLib->requestint('id',0,'订单编号',1) ;
            $re=$order->getStatus($oid,$uid);
            if($re['I_status']!=20 || $re['I_isapp']!=1){returnjson(array('err'=>-1,'msg'=>'订单未完成,或已评价'));}
            //获取订单详情
            $re=$order->getDetail($oid,$uid,1);
            if(!$re){returnjson(array('err'=>-2,'msg'=>'获取订单详情失败'));}
            $r=$ordercommodity->get($oid);
            if(!$r){returnjson(array('err'=>-3,'msg'=>'获取订单商品失败'));}
            $re['goods']=$r;
            $p['err']=0;
            $p['msg']='ok';
            $p['data']=$re;
        }
        break;
    //查看评价
    case 'appraisal':
        /**
         * @author zy
         * 交易管理>查看评价接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=order&w=appraisal
         * 输入：需登录后访问
         * id:订单号
         * 输出：
         * err:int 结果状态 <0失败 0成功
         * msg: 提示信息
         * data=>id:int 订单号(隐藏域)
         *      I_status:str 订单状态
         *      Dt_last_modify:str 完成时间
         *      goods=>array()
         *        0=>I_commodityID:str 商品ID
         *          itemname:str 商品名
         *          stuffname:str 材质
         *          specificationname:str 规格名
         *          factoryname:str 钢厂名
         *          warehouse:str 仓库名
         *          N_amount:str 数量
         *          N_weight:str 重量
         *          N_price:str 单价
         *          N_amount_price:str 总价
         * */
        //获取订单编号
        $oid=$FLib->requestint('id',0,'订单编号',1) ;
        //获取订单详情
        $re=$order->getDetail($oid,$uid,1);
        if(!$re){returnjson(array('err'=>-1,'msg'=>'获取订单详情失败'));}
        $r=$ordercommodity->get($oid);
        if(!$r){returnjson(array('err'=>-2,'msg'=>'获取订单商品失败'));}
        $re['goods']=$r;
        //获取该订单评价
        $r=$order->getAppraise($oid);
        if(empty($r)){$re['app']='无评价';}else{$re['app']=$r;}
        $p['err']=0;
        $p['msg']='ok';
        $p['data']=$re;
        break;
    //删除评价 (没有这个需求)
    case 'deleteappraisal':
        /**
         * @author zy
         * 交易管理>删除评价接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=order&w=deleteappraisal
         * 输入：需登录后访问
         * id:订单号
         * 输出：
         * err:int 结果状态 <0失败 0成功
         * msg: 提示信息
         * */
        $oid=$FLib->requestint('id',0,'订单编号',1) ;
        //删除
        $re=$order->deleteAppraise($oid);
        if(!$re){returnjson(array('err'=>-1,'msg'=>'删除评价失败'));}
        returnjson(array('err'=>0,'msg'=>'删除评价成功'));
        break;
}