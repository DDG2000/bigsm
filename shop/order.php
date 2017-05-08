<?php
// if(!defined('WEBROOT'))exit;

$w=$FL->requeststr('w',1,0,'w',1,1);

$m.='_'.$w;

 require_once(WEBROOTINCCLASS.'OrderCommodity.php');

 $objOrderCommodity = new OrderCommodity();

switch($w){
    
    
case 'del':
    /**
     * @author wh
     * 取消订单接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=order&w=del
     * 输入：需登录后访问
     * id : int 订单id
     
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     * 
     *
     * */
    $id=$FLib->requestint('id',0,'订单id',1);
    if(!$id){returnjson(array('err'=>-1,'msg'=>'参数不合法'));}
    $da['I_status']=50;//商家取消订单
    $da['Dt_last_modify@']='now()';//商家完成时间
    $sqlw="id={$id}";
    $rs=$objOrder->update($da, $sqlw);
    if($rs){
        returnjson(array('err'=>0,'msg'=>'取消成功！'));
    }else{
        returnjson(array('err'=>-1,'msg'=>'取消失败！'));
    
    }

 
    break;    
case 'check':
    /**
     * @author wh
     * 审核接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=order&w=check
     * 输入：需登录后访问
     * id : int 订单id
     
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     * 在err为0时跳转至电子确认函信息接口
     * 
     * http://www.bigsm.com/index.php?act=shop&m=order&w=checkinfo
     *
     * */
    $id=$FLib->requestint('id',0,'订单id',1);
    if(!$id){returnjson(array('err'=>-1,'msg'=>'参数不合法'));}
    $da['I_status']=20;//商家完成订单
    $da['Dt_last_modify@']='now()';//商家完成时间
    $sqlw="id={$id}";
    $rs=$objOrder->update($da, $sqlw);
    
    
    if($rs){
    
        returnjson(array('err'=>0,'msg'=>'审核成功！'));
    
    }else{
    
        returnjson(array('err'=>-1,'msg'=>'审核失败！'));
    
    }
    //生成电子确认函信息
//     $data=$objOrder->getOne($id);
//     var_dump($data);
//     exit;
    

 
    break;    
case 'checkinfo':
    /**
     * @author wh
     * 审核后电子确认函信息接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=order&w=checkinfo
     * 输入：需登录后访问
     * id : int 订单id
     
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     * 
     *
     * */
    $id=$FLib->requestint('id',0,'订单id',1);
    if(!$id){returnjson(array('err'=>-1,'msg'=>'参数不合法'));}
    
    //生成电子确认函信息
    $data=$objOrder->getOne($id);
    $data['commoditylist']=$objOrderCommodity->getCommodityList($id);
    $rs=$objOrder->getShopAppraise($id);
    if($rs){
    $data['comments']=$objOrder->getShopAppraise($id);
    }else{
    $data['comments']=null;
        
    }
    
    returnjson(array('err'=>0,'data'=>$data));
    

 
    break;    
 
 
    case 'edit':
       /**
         * @author wh
         * 修改价格接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=order&w=edit
         * 输入：需登录后访问
         * 
         * id : int 订单id
         * N_amount_price：string 修改后的订单总价
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         *
         * */
    
        $id=$FLib->requestint('id',0,'订单id',1);
        
        $da['N_amount_price']=$FL->requeststr('N_amount_price',1,30,'订单总价');
       
        
        if(!$id){returnjson(array('err'=>-1,'msg'=>'参数不合法'));}
        if(!$da['N_amount_price']){returnjson(array('err'=>-1,'msg'=>'未填写价格信息'));}
        
        
        $sqlw="id={$id}";
        $rs=$objOrder->update($da, $sqlw);
        if($rs){
            
            returnjson(array('err'=>0,'msg'=>'修改成功！'));
            
        }else{
            
            returnjson(array('err'=>-1,'msg'=>'修改失败！'));
        
        }
        
        break; 
        
case 'list':
    /**
     * @author zy
     * 订单管理接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=order&w=list
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

//                $I_shopID = $objShop->isBeShop($uid);
                //获取搜索数据I_isapp 是否评价 1 未评价 2 已评价
                $da['I_status']=$FLib->requestint('I_status',0,'订单状态',1) ;
                $da['I_isapp']=$FLib->requestint('I_isapp',0,'是否评价',1) ;
                $da['keyword']=$FLib->requeststr('keyword',1,50,'关键字',1);
                $da['starttime']=$FLib->requeststr('starttime',1,50,'开始时间',1);
                $da['endtime']=$FLib->requeststr('endtime',1,50,'结束时间',1);
                $page=$FLib->requestint('CurrentPage',1,'当前页',1) ;
                //获取分页数
                $PageSize=$Cfg->OrderPageSize;
                //获取当页信息,2代表商家订单
                $re=$objOrder->getPages($I_shopID,$page,$PageSize,$da,2);
                //获取每单商品信息
                if($re['data']){
                    foreach($re['data'] as $k=>$v){
                        $oid=$v['id'];
                        //获取当前单号所有商品
                        $r=$objOrderCommodity->get($oid);
                        //添加字段goods在每一订单中,包含所有商品
                        $v['goods']=$r;
                        $re['data'][$k]=$v;
                    }
                }
                //        dump($re);
                $pcount = $re['pcount'];
                $p['I_status'] =$da['I_status'];
                $p['I_isapp'] =$da['I_isapp'];
                $p['keyword'] =$da['keyword'];
                $p['starttime'] =$da['starttime'];
                $p['endtime'] =$da['endtime'];
                $p['data'] =$re;
                $p['pagestr'] = getPageStrFunSd($pcount, $page, "?act=shop&m=order&w=list&starttime={$da['starttime']}&endtime={$da['endtime']}");
//                 returnjson($re);
                break;
                
//订单详情
case 'detail':
    /**
     * @author zy
     * 交易管理>订单详情接口
     * 获取一个订单的详情,订单概况,收货信息,订货函,评价
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=order&w=detail
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
    
//             $I_shopID = $objShop->isBeShop($uid);
            $oid=$FLib->requestint('id',0,'订单编号',1) ;
            //获取一个订单详情,1卖家
            $re=$objOrder->getDetail($oid,$I_shopID,2);
            if(!$re){returnjson(array('err'=>-1,'msg'=>'获取订单详情失败'));}
            //获取订单的所有商品
            $r=$objOrderCommodity->get($oid);
            if(!$r){returnjson(array('err'=>-2,'msg'=>'获取订单商品失败'));}
            //添加goods键名,对应所有商品信息
            $re['goods']=$r;
            //获取订单的评价
            //获取订单的状态,没有完成就是非法操作
            $r=$objOrder->getStatus($oid,$I_shopID,2);
            //已完成
        
            $p['app']='';
            if($r['I_status']!=10){
                if($r['I_isapp']==2){$p['app']=$objOrder->getAppraise($oid);}
            }
            $p['I_status']=$r['I_status'];
            $p['I_isapp']=$r['I_isapp'];
            $p['err']=0;
            $p['msg']='ok';
            $p['data']=$re;
            break;
                
        case 'appraisal':
            /**
             * @author zy
             * 交易管理>查看评价接口
             * url地址：
             * http://www.bigsm.com/index.php?act=shop&m=order&w=appraisal
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
//             $I_shopID = $objShop->isBeShop($uid);
            //获取订单编号
            $oid=$FLib->requestint('id',0,'订单编号',1) ;
            //获取订单详情
            $re=$objOrder->getDetail($oid,$I_shopID,2);
            if(!$re){returnjson(array('err'=>-1,'msg'=>'获取订单详情失败'));}
            $r=$objOrderCommodity->get($oid);
            if(!$r){returnjson(array('err'=>-2,'msg'=>'获取订单商品失败'));}
            $re['goods']=$r;
            //获取该订单评价
            $r=$objOrder->getAppraise($oid);
            if(empty($r)){$re['app']='无评价';}else{$re['app']=$r;}
            $p['err']=0;
            $p['msg']='ok';
            $p['data']=$re;
            break;
            
  
    case 'elecorder':
        /**
         * @author zy
         * 交易管理>查看电子订货函接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=order&w=elecorder
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
        
//                $I_shopID = $objShop->isBeShop($uid);
                $oid=$FLib->requestint('id',0,'订单编号',1) ;
                //获取订单商品
                $re=$objOrder->getDetail($oid,$I_shopID,2);
                if(!$re){returnjson(array('err'=>-1,'msg'=>'获取订单详情失败'));}
                $re=$objOrder->getStatus($oid,$I_shopID,2);
                if($re['I_status']!=20){returnjson(array('err'=>-1,'msg'=>'订单未完成'));}
                $da['N_amount_price']=$re['N_amount_price'];
                $re=$objOrderCommodity->get($oid);
                if(!$re){returnjson(array('err'=>-1,'msg'=>'获取订单商品失败'));}
                $da['goods']=$re;
                $p['err']=0;
                $p['msg']='ok';
                $p['data']=$da;
                break;

}

?>
