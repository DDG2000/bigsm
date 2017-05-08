<?php
// if(!defined('WEBROOT'))exit;

$w=$FL->requeststr('w',1,0,'w',1,1);

$m.='_'.$w;


require_once(WEBROOTINCCLASS . 'OrderUtils.php');
require_once(WEBROOTINCCLASS . 'RequireCommodity.php');
require_once(WEBROOTINCCLASS . 'MessageService.php');
require_once(WEBROOTINCCLASS . 'Shop.php');

$objShop=new Shop();
$objNo=new OrderUtils();
$objRequireCommodity=new RequireCommodity();
$objMsg=new MessageService();


require_once(WEBROOTINC.'File.class.php');
$Fc = new FileClass();


switch($w){
    
    
case 'test':
    // http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=test
//     echo "123";

//     exit();
    break;    
case 'companylist':
    /**
     * @author wh
     * 推送下商家模糊搜索接口
     * url地址：
     * http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=companylist
     * 输入：需登录后访问
     * skey:string 商家名称
     * 
     * 输出：
     * err:int 结果状态 500失败200成功
     * msg: 提示信息
     * data：array
     * id:商家id
     * Vc_name:商家名称
     *
     * */
    $Vc_name=$FL->requeststr('skey',1,50,'采购名称');
    if(!$Vc_name){returnErrJson('输入不能为空');}
    
    $sql="SELECT I_userID uid,Vc_name FROM sm_shop a WHERE  a.Status=1 and a.I_cert_status=3  and a.Vc_name like '%{$Vc_name}%' LIMIT 0,5";
    $da = $Db->fetch_all_assoc($sql);
    
    if($da){
        
        returnSucJson($da);
    }else{
        returnErrJson('不存在此商家或该商家未认证');
    }
    

//     exit();
    break;    
case 'pushmsg':
    /**
     * @author wh
     * 短信推送接口
     * url地址：
     * http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=pushmsg
     * 输入：需登录后访问
     * id: int 需求Id
     * Vc_name: string 采购名称
     * mallclassID：int 1 默认为钢材行业
     * submit:不存在该参数时查看通过模版输出信息，存在该参数表示提交
     则还要传以下参数  :
 
     * pushType：int 推送类型（1-按产品类型推送，2-按商家类型推送）
     * Vc_itemClassIds：string 分类id集合,多个id之间以英文半角逗号分隔
     * Vc_shopIds：string 商家id集合,多个id之间以英文半角逗号分隔
     *
     * 注意在前端校验替换中文半角和全角逗号
     * 
     * 输出：
     * err:int 结果状态 500失败200成功
     * msg: 提示信息
     *
     * */
    $I_mall_classID = $FLib->requestint('mallclassID',1,'默认为钢材行业',1);
    $id = $FLib->requestint('id',0,'需求Id',1);
    $Vc_name=$FL->requeststr('Vc_name',1,50,'采购名称');
    
    if(!$id){returnErrJson("需求id不能为空");}
    if(!$Vc_name){returnErrJson("采购名称");}
    // http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=test
    if(isset($_REQUEST['submit'])){
        
        
        $pushType = $FLib->requestint(' pushType',0,'推送类型',1);
        
        //发消息
        //查询到所有符合要求的最多100商家，循环发消息
        $da['title'] = '订单待报价:';
        $da['content'] =$Vc_name;
        $da['url'] = $Cfg->WebRoot.'index.php?act=requirement&m=commoditydetail&I_requirementID='.$id;
        if($pushType==1){
            
            $Vc_itemClassIds=$FL->requeststr('Vc_itemClassIds',1,50,'分类id集合');
            if(!$Vc_itemClassIds){
                returnErrJson("未选择分类！");
            }
            $Vc_itemClassIds = preg_replace("/，|，/s", ",", $Vc_itemClassIds);//将中文全角和半角逗号替换为英文半角
            
            $rs =$objShop->getShopUserids($Vc_itemClassIds,$I_mall_classID);
            
           
           if($rs){
               foreach ($rs as $shopId){
                   $da['userID']=$shopId['id'];
                   $objMsg->sendReqMsgSuccess($da);
               }
               
               returnSucJson(null,'推送成功');
           }else{
               
             returnErrJson("暂无符合条件的商家");
           }
            
            
        }elseif($pushType==2){
            
            $Vc_shopIds=$FL->requeststr('Vc_shopIds',1,50,'分类id集合');
            if(!$Vc_shopIds){
                returnErrJson("未选择商家！");
            }
            $Vc_shopIds = preg_replace("/，|，/s", ",", $Vc_shopIds);//将中文全角和半角逗号替换为英文半角
            $shopArr = explode(',', $Vc_shopIds);
            //发消息
            for($i=0;$i<count($shopArr);$i++){
                if($shopArr[$i]){
                    $da['userID']=$shopArr[$i];
                    $objMsg->sendReqMsgSuccess($da);
                }
                
            }
            
            returnSucJson(null,'推送成功');
            
            
        }else{
            
            returnErrJson("推送类型参数有误！");
        }
        
        
        
        
    }else{
        
        
        $sql="SELECT id,Vc_name FROM sm_item_class where Status=1 and I_mall_classID={$I_mall_classID}";
        $rs= $Db->fetch_all_assoc($sql);
        $p['data']=$rs;
        $p['id']=$id;
        $p['name']=$Vc_name;
//         returnjson($rs);
          
    }
          
          
    break;    
case 'lookletter':
    /**
     * @author wh
     * 查看订货函接口
     * url地址：
     * http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=lookletter
     * 输入：需登录后访问
     * I_commodityID : int 采购id
     * commodityorderID: int 采购产品id
     * I_shopID : int 商家id
     * 
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     *  以下仅在err为0时会返回
     *  data:
     * 
     * 测试地址：
     * 
     * http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=lookletter&I_commodityID=2&commodityorderID=4
     * */
    
    
    $I_commodityID=$FLib->requestint('I_commodityID',0,'采购id',1);
    $commodityorderID=$FLib->requestint('commodityorderID',0,'采购产品id',1);
    $I_shopID=$FLib->requestint('I_shopID',0,'商家id',1);
    if(!$I_commodityID){returnjson(array('err'=>-1,'msg'=>'采购id不合法'));}
    if(!$commodityorderID){returnjson(array('err'=>-1,'msg'=>'采购产品id不合法'));}
    if(!$I_shopID){returnjson(array('err'=>-1,'msg'=>'商家id不合法'));}
    $I_buyerID=$uid;
    if(!$I_buyerID){returnjson(array('err'=>-1,'msg'=>'请重新登录'));}
    
    $Rs = $objRequireCommodity->getOrderLetter4buyer($commodityorderID, $I_commodityID, $I_buyerID,$I_shopID);
    
//     returnjson($Rs);
    $p['data']=$Rs;
    
    break;    
case 'orderletter':
    /**
     * @author wh
     * 生成订货函接口
     * url地址：
     * http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=orderletter
     * 输入：需登录后访问
     * I_commodityID : int 采购id
     * commodityorderID: int 采购产品id
     * submit:不存在该参数时查看通过模版输出获取分类信息，存在该参数表示提交
     则还要传以下参数  :
     * I_shopID : int 商家id
     * N_amount: int 数量
     * I_unitType: int 单位
     * factory: string 钢厂名
     * price: string 含税单价
     * outType: int 出库费：1-包出，2-自提
     * warehouse: string 仓库名
     * memo: string 备注
     * totalprice: string 总金额
     * 
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     * ** 以下仅在err为0时会返回
     *
     *
     *测试
     *http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=orderletter&I_commodityID=2&commodityorderID=4
     *
     * */
    
    $I_commodityID=$FLib->requestint('I_commodityID',0,'采购id',1);
    $commodityorderID=$FLib->requestint('commodityorderID',0,'采购产品id',1);
    if(!$I_commodityID){returnjson(array('err'=>-1,'msg'=>'采购id不合法'));}
    if(!$commodityorderID){returnjson(array('err'=>-1,'msg'=>'采购产品id不合法'));}
    if(isset($_REQUEST['submit'])){
        
        $data['I_buyerID']=$uid;
        $data['I_shopID']=$FLib->requestint('I_shopID',0,'商家id',1);
        $data['N_amount']=$FLib->requestint('N_amount',0,'数量',1);
        $data['I_unitType']=$FLib->requestint('I_unitType',0,'单位',1);
        $data['factory']=$FL->requeststr('factory',1,50,'钢厂名');
        $data['price']=$FL->requeststr('price',1,50,'含税报价');
        $data['outType']=$FLib->requestint('outType',0,'出库方式',1);//出库费：1-包出，2-自提
        $data['warehouse']=$FL->requeststr('warehouse',1,50,'库房');
        $data['memo']=$FL->requeststr('memo',1,50,'备注');
        $data['totalprice']=$FL->requeststr('totalprice',1,50,'总金额');
        $data['I_status']=1;
        $data['I_commodityID']=$I_commodityID;
        $data['I_commodityorderID']=$commodityorderID;
        
        if(!$data['I_buyerID']){returnjson(array('err'=>-1,'msg'=>'请登录后提交'));}
        if(!$data['I_shopID']){returnjson(array('err'=>-1,'msg'=>'未收到卖家数据'));}
        if(!$data['N_amount']){returnjson(array('err'=>-1,'msg'=>'未收到数量'));}
        if(!$data['I_unitType']){returnjson(array('err'=>-1,'msg'=>'未收到单位'));}
        if(!$data['factory']){returnjson(array('err'=>-1,'msg'=>'未收到钢厂数据'));}
        if(!$data['price']){returnjson(array('err'=>-1,'msg'=>'未收到含税报价数据'));}
        if(!$data['outType']){returnjson(array('err'=>-1,'msg'=>'未收到出库方式数据'));}
        if(!$data['warehouse']){returnjson(array('err'=>-1,'msg'=>'未收到仓库数据'));}
//         if(!$data['memo']){returnjson(array('err'=>-1,'msg'=>'请登录后提交'));}
        if(!$data['totalprice']){returnjson(array('err'=>-1,'msg'=>'未收到总价数据'));}
        
        
        //订货数量判断
        
        $sql="SELECT N_amount from sm_requirement_commodityorder WHERE id={$commodityorderID}";
        $num1=$Db->fetch_one($sql);
        if($data['N_amount']>$num1['N_amount']){
            
           returnjson(array('err'=>-1,'msg'=>'已超出采购数量'));
        }
        
        $sql="SELECT sum(N_amount) amount from sm_requirement_commodityorder_letter WHERE Status=1 
            and I_buyerID={$uid} and I_commodityorderID={$commodityorderID} and I_commodityID={$I_commodityID}";
        
        $num2=$Db->fetch_one($sql);
        
        if($num2){
            
            if($num1['N_amount']<($num2['amount']+$data['N_amount'])){
                
                returnjson(array('err'=>-1,'msg'=>'已超出采购数量'));
            }
            
            
        }
        
        
        
        $rs = $objRequireCommodity->addOrderLetter($data);
        if($rs){
            
            
            //对应卖家报价状态修改为请确认订货函
            
            $sqlw=" I_shopID={$data['I_shopID']} and I_commodityorderID={$commodityorderID} and I_commodityID={$I_commodityID} ";
            $rs2 = $objRequireCommodity->updateOffer(array('I_status'=>3),$sqlw);
            
            
            //发消息
            //采购编号为xnyf0000000001的钢板采购计划订货单位********的订货函已发送，请查看确认
            $sql ="SELECT Vc_orderSn,Vc_name,Vc_company,I_requirementID FROM sm_requirement_commodity WHERE id = {$I_commodityID}";
            $r = $Db->fetch_one($sql);
            $daArr=array();
            $daArr['Vc_orderSn']=$r['Vc_orderSn'];
            $daArr['Vc_name']=$r['Vc_name'];
            $daArr['Vc_company']=$r['Vc_company'];
            $daArr['I_requirementID']=$r['I_requirementID'];
            $daArr['I_commodityID']=$I_commodityID;
            $daArr['weburl']=$Cfg->WebRoot;
            $daArr['uid']=$objShop->getuid($data['I_shopID']);

            $objMsg->sendOrderLetterSuccess($daArr);
            
            returnjson(array('err'=>0,'msg'=>'发送成功'));
            
//             $da=array();
//             $da['title'] = '请确认订货函:';
//             $da['content'] = "采购编号为".$r['Vc_orderSn']."的".$r['Vc_name'].",订货单位".$r['Vc_company']."的订货函已发送，请查看确认";//采购编号
//             $da['url'] = $Cfg->WebRoot.'index.php?act=shop&m=requirecommodity&w=detail&id='.$r['I_requirementID'].'&cid='.$I_commodityID;
//             //http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=detail&id=2&cid=2
//             //$data['I_shopID']根据商家id拿到用户uid
//             $da['userID']=$objShop->getuid($data['I_shopID']);
           
            
            
        }else{
            returnjson(array('err'=>-1,'msg'=>'发送失败'));
            
        }
        
        
    }else{
        
        
      
      
        $Rs=$objRequireCommodity->getOrderLetter($commodityorderID, $I_commodityID);
                      
        if($Rs){
            
            if($Rs['Vc_meta']){
                $Rs['Vc_meta']=unserialize($Rs['Vc_meta']);
            }
            if($Rs['Vc_factorys']){
                $Rs['Vc_factorys']=explode(',', $Rs['Vc_factorys']);
            }
        
        }
//         returnjson($Rs);
//         exit();
        $p['data']=$Rs;
    
    }
    
    
    
    break;    
case 'shoplist':
        /**
         * @author wh
         * 采购详情商家列表分页及排序接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=shoplist
         * 输入：需登录后访问
         * I_commodityID : int 采购id
         * commodityorderID: int 采购产品id
         * page：int  默认为1
         * psize：int 默认为15
         * 若报价排序则还要传参数：
         * factoryName：string 钢厂名字
         * orderType：int 排序类型：1-升序，2-降序（ 默认为1）
         * 
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * ** 以下仅在err为0时会返回
         * page: int 当前页数
         * count: string 数据总量
         * pcount: int 总页数
         * data:数据
         * */
        $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):15;
        if(!is_numeric($page) || !is_numeric($psize)) {
            returnjson(array('err'=>-1,'msg'=>'数据不合法'));
        }
        $I_commodityID=$FLib->requestint('I_commodityID',0,'采购id',1);
        $commodityorderID=$FLib->requestint('commodityorderID',0,'采购产品id',1);
        
        $factoryname=$FL->requeststr('factoryName',1,100,'钢厂名称');
        $orderType=$FLib->requestint('orderType',1,'排序类型',1);//1-默认升序，2-降序
        
        
//         $factoryname = "本钢";
        if(!$I_commodityID){returnjson(array('err'=>-1,'msg'=>'采购id不合法'));}
        if(!$commodityorderID){returnjson(array('err'=>-1,'msg'=>'采购产品id不合法'));}
        
        
        $data=$objRequireCommodity->getCommodityShopListByPage($page, $psize, $commodityorderID, $I_commodityID);
        
        if($data['data']){
            foreach ($data['data'] as &$v){
                $v['Vc_meta']=@unserialize($v['Vc_meta']);
                $v['sortprice'] =0;
                if($v['Vc_meta']){
                    foreach ($v['Vc_meta']['factorys'] as $k=>$vo){
                        $temp = array();
                        $temp['factorys'] = $vo;
                        $temp['price'] = $v['Vc_meta']['price'][$k];
                        $temp['outType'] = $v['Vc_meta']['outType'][$k];
                        $temp['warehouse'] = $v['Vc_meta']['warehouse'][$k];
                        $temp['memo'] = $v['Vc_meta']['memo'][$k];
                        $v['metalist'][]=$temp;
                        if($factoryname){
                            if($factoryname==$vo){
                                $v['sortprice']=$temp['price'];
                            }
                        }
                        
                    }
                }else{
                    $v['metalist'] = array();
                }
                
                unset($v['Vc_meta']);
                
            }
            if($factoryname){
                
                if($orderType==2){
                    
                    $data['data'] = array_sort($data['data'],'sortprice','desc');
                    
                }else{
                    
                $data['data'] = array_sort($data['data'],'sortprice','asc');
                    
                }
            }
        }
        returnjson($data);
    
    
    
 
        break;
case 'detail':
        /**
         * @author wh
         * 采购详情接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=detail
         * 输入：需登录后访问
         * id : int id 需求id
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         *
         * */

            $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
            $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):15;
            $pcsize=isset($_REQUEST['pcsize'])?intval($_REQUEST['pcsize']):2;
            if(!is_numeric($page) || !is_numeric($psize)) {
                returnjson(array('err'=>-1,'msg'=>'数据不合法'));
            }
    
            $id=$FLib->requestint('id',0,'需求id',1);
            if(!$id){returnjson(array('err'=>-1,'msg'=>'需求id有误'));}
            $Rs=$objRequireCommodity->getOfferInfo($id);
            $I_commodityID=$Rs['I_commodityID'];
           
            $data=array();
                if($Rs){
                    
                    $data['offerinfo']=$Rs;
                    
                    $Rs2=$objRequireCommodity->getCommodityOrderListByPage($page,$pcsize,$Rs['Vc_commodityorderIds']);


                   // $page = $Rs2['page'];
                    $count = $Rs2['count'];
                    $pcount = $Rs2['pcount'];
                    $p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=user&m=requirecommodity&w=detail&id=$id");
                    
                    foreach ($Rs2['data'] as &$v){
                        $v['Vc_factorys']=explode(',', $v['Vc_factorys']);
                        $v['isEqualNum']=0;//是否和采购数量相等
                        
//                         $v['shoplist']=$objRequireCommodity->getCommodityShopListByPage($page, $psize, $v['id'], $I_commodityID);
                        $result=$objRequireCommodity->getCommodityShopListByPage($page, $psize, $v['id'], $I_commodityID);
                      
                        if($result['data']){
                            foreach ($result['data'] as &$v2){
                                $v2['Vc_meta']=@unserialize($v2['Vc_meta']);
                                $v2['isHaveLetter']=0;//是否有订货函
                                
                                if($v2['Vc_meta']){
                                    foreach ($v2['Vc_meta']['factorys'] as $k=>$vo){
                                        $temp = array();
                                        $temp['factorys'] = $vo;
                                        $temp['price'] = $v2['Vc_meta']['price'][$k];
                                        $temp['outType'] = $v2['Vc_meta']['outType'][$k];
                                        $temp['warehouse'] = $v2['Vc_meta']['warehouse'][$k];
                                        $temp['memo'] = $v2['Vc_meta']['memo'][$k];
                                        $v2['metalist'][]=$temp;
                                    }
                                }else{
                                    $v2['metalist'] = array();
                                }
                                unset($v2['Vc_meta']);
                                
                                //判断是否有订货函和所有订货函数量是否和采购数量相等
                                

                               //--1.判断是否有订货函
                                $rsLetter =$objRequireCommodity->getShopOrderLetter($v['id'], $I_commodityID, $v2['I_shopID']);
                                if($rsLetter){
                                    $v2['isHaveLetter']=1;
                                }
                                
                                
                                
                            }
                        }
                        $v['shoplist'] = $result;
                               //--2.所有订货函数量是否和采购数量相等

                        $rsSum=$objRequireCommodity->getOrderLetterCommodityorderSum($v['id'], $I_commodityID);
                        if($rsSum){
                            if($v['N_amount']==$rsSum){
                                $v['isEqualNum']=1;
                            }
                        }
                        
                        
                    }
                    $data['commodityorderlist']=$Rs2;
                   
                    
                }
       
//                 returnjson($data);
                $p['data']=$data;
                $p['id']=$id;
             
            
                
            break;
case 'publish':
        
            /**
             * @author wh
             * 发布和撤销接口
             * url地址：
             * http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=publish
             * 输入：需登录后访问
             * id : int 采购计划id
             * 
             * type：int 操作类型:1-发布，2-撤销发布
             *
             * 输出：
             * err:int 结果状态 -1失败 0成功
             * msg: 提示信息
             *
             * */
        
           
             $id=$FLib->requestint('id',0,'id',1);
             if(!$id){returnjson(array('err'=>-1,'msg'=>'数据不合法'));}
             
             $type=$FLib->requestint('type',0,'操作类型',1);
            if(!$type){returnjson(array('err'=>-1,'msg'=>'未选择操作类型'));}
            switch($type){
                case 1:
                      $da['I_publish_status']=2;
                    break;
                case 2:
                      $da['I_publish_status']=3;
                    break;
                
            }
            $da['Createtime@'] = 'now()';

            $sqlw="id={$id}";
            $rs=$objRequireCommodity->updateRequirement($da, $sqlw);
//             returnjson($rs);
            if($rs){
                returnjson(array('err'=>0,'msg'=>'提交成功！'));
            }else{
                returnjson(array('err'=>-1,'msg'=>'提交失败！'));

            }
             
            break;        
case 'del':
        
            /**
             * @author wh
             * 删除采购计划接口
             * url地址：
             * http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=del
             * 输入：需登录后访问
             * id : int 采购计划id
             *
             * 输出：
             * err:int 结果状态 -1失败 0成功
             * msg: 提示信息
             *
             * */
        
           
             $id=$FLib->requestint('id',0,'采购id',1);
             if(!$id){returnjson(array('err'=>-1,'msg'=>'数据不合法'));}
           
            
            $da['Status']=0;
            $sqlw="id={$id}";
            $rs=$objRequireCommodity->updateRequirement($da, $sqlw);
            if($rs){
                returnjson(array('err'=>0,'msg'=>'删除成功！'));
            }else{
                returnjson(array('err'=>-1,'msg'=>'删除失败！'));
            
            }
             
            break;        
case 'uploadexl':
        
            /**
             * @author wh
             * 上传采购单接口
             * url地址：
             * http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=uploadexl
             * 输入：需登录后访问
             * 
             * isIE: int 1-是，0-不是
             * file_exl：file 采购单
             *
             * 输出：
             * err:int 结果状态 -1失败 0成功
             * msg: 提示信息
             * 以下仅在err为0时返回
             * data：array
             *
             * */
        
           
           
    
            //接收文件
            if(!isset($_FILES['file_exl'])){
                returnjson(array('err'=>-1,'msg'=>'请选择文件'));
        
            }
        
            $p='file_exl';//上传文件变量名
            $path = '/upload/user/tmp/';
//             $path = WEBROOT.'/upload/user/tmp/';
            
            $T='xls';
            $Z=3072000;//文件大小不能超过3M
            $r=$Fc->uplodefile($p,$path,$T,$Z);
        
            if($r=='no_type'){
                returnjson(array('err'=>-1,'msg'=>'文件类型不正确，只支持xls格式的Excel！'));
            }elseif ($r=='no_size'){
                returnjson(array('err'=>-1,'msg'=>'文件大小不能超过3M！'));
            }elseif ($r===false){
                returnjson(array('err'=>-1,'msg'=>'资源文件上传失败！'));
            }
            
            $savePath=WEBROOT.$r;//返回路径地址
            
            require(WEBROOTINC.'ExcelImport.php');
            
            $objExcelImport = new ExcelImport();
      
            $res = $objExcelImport->read( $savePath ,'utf-8');
            $curfile = $savePath;
            //读取完后就删除当前文件
            if(is_file($curfile)){
                chmod($curfile,0777);
                // echo "文件存在！已经删除!--您可以重新上传文件";
                if(!unlink($curfile)){
                    $msg = '删除上传excel失败,文件权限问题';
                    returnjson(array('err'=>-1,'msg'=>$msg));
                    exit;
                }
            
            }
            $data=array();
            foreach ( $res as $k => $v )
            {
                if ($k != 0)
                {
                    if($k>1){
                        $tempArr=array();

                        $tempArr['Vc_item']=trim($v[0]);
                        $tempArr['Vc_stuff']=trim($v[1]);
                        $tempArr['Vc_specification']=trim($v[2]);
                        $tempArr['N_amount']=trim($v[3]);
                        $tempArr['I_unitType']=trim($v[4]);
                        $Vc_factorys=trim($v[5]);
                        $tempArr['Vc_factorys'] = preg_replace("/，|，/s", ",", $Vc_factorys);//将中文全角和半角逗号替换为英文半角
                        
                        $data[]=$tempArr;
                    } 
                }
            }
//             var_dump($data);
//             exit;
            $root['err']=0;
            $root['data']=$data;
            if(isset($_REQUEST['isIE'])){
              echo "<script type='text/javascript'>
                                    　　　　window.top.window['callback'](".json_encode($root).");
                                    　　</script>";
              exit;
                
            }else {
            returnjson($root);
            }
             
            break;     
               
case 'add':
        
            /**
             * @author wh
             * 新增采购接口
             * url地址：
             * http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=add
             * 输入：需登录后访问
             * submit:不存在该参数时查看通过模版输出获取分类信息，存在该参数表示提交
                                     则还要传以下参数  :
             * I_mallClassID：int 所选分类ID
             * Vc_itemClassIds：string 分类id集合,多个id之间以英文半角逗号分隔
             * Vc_name：string 采购名称
             * Vc_company：string 公司名称
             * Vc_contact：string 采购人
             * Vc_contact_phone：string 联系电话
             * I_provinceID：int 省ID
             * I_cityID：int 市ID
             * I_payType：int 支付方式：1-现款，2-银行承兑
             * D_end：string 结束时间
             * Vc_memo：array 备注
             * 
             * 
             * Vc_item：array 品名数组
             * Vc_stuff：array 材质数组
             * Vc_specification：array 规格数组
             * Vc_factorys：array 钢厂数组，多个钢厂中间，以英文半角逗号分割（鞍钢,宝钢,长钢）
             * 注意在前端校验替换中文半角和全角逗号
             * N_amount：array 数量数组
             * I_unitType：array 单位数组，单位类型：1-件，2-根，3-吨
             *
             *
             * 输出：
             * err:int 结果状态 -1失败 0成功
             * msg: 提示信息
             *
             * */
        
          
    if(isset($_REQUEST['submit'])){
        
        //加文件锁
        
          //sm_requirement
        
            $dar['I_userID']=$uid;//发标人id
            $dar['I_requirementClassID']=1;//需求类型id：1-产品需求
           
            
         // sm_requirement_commodity  
          
            $darc['I_mallClassID']=$FLib->requestint('I_mallClassID',0,'行业id',1);
            $darc['Vc_itemClassIds']=$FL->requeststr('Vc_itemClassIds',1,100,'分类ids');
            $darc['Vc_orderSn']=$objNo->getOrderNo();//采购编号
            $darc['Vc_name']=$FL->requeststr('Vc_name',1,100,'采购名称');
            $darc['Vc_company']=$FL->requeststr('Vc_company',1,100,'公司名称');
            $darc['Vc_contact']=$FL->requeststr('Vc_contact',1,100,'采购人');
            $darc['Vc_contact_phone']=$FL->requeststr('Vc_contact_phone',1,100,'联系电话');
            $darc['I_provinceID']=$FLib->requestint('I_provinceID',0,'省ID',1);
            $darc['I_cityID']=$FLib->requestint('I_cityID',0,'市ID',1);
            $darc['I_payType']=$FLib->requestint('I_payType',0,'支付方式',1);//1-现款，2-银行承兑
            $darc['D_start']=date('Y-m-d',time());
            $darc['D_end']=$FL->requeststr('D_end',1,20,'结束时间');
            $darc['Vc_memo']=$FL->requeststr('Vc_memo',1,60,'备注');
            
            if(!$darc['I_mallClassID']){returnjson(array('err'=>-1,'msg'=>'未选择行业'));}
            if(!$darc['Vc_name']){returnjson(array('err'=>-1,'msg'=>'未填写采购名称'));}
            if(!$darc['Vc_company']){returnjson(array('err'=>-1,'msg'=>'未填写公司名称'));}
            if(!$darc['Vc_contact']){returnjson(array('err'=>-1,'msg'=>'未填写采购人'));}
            if(!$darc['Vc_contact_phone']){returnjson(array('err'=>-1,'msg'=>'未填写联系电话'));}
            if(!$darc['I_provinceID']){returnjson(array('err'=>-1,'msg'=>'未选择省'));}
            if(!$darc['I_cityID']){returnjson(array('err'=>-1,'msg'=>'未选择市'));}
            if(!$darc['I_payType']){returnjson(array('err'=>-1,'msg'=>'未选择'));}
            if(!$darc['D_end']){returnjson(array('err'=>-1,'msg'=>'未选择产品'));}
            
         //sm_requirement_commodityorder
        
            $itemArr= isset($_POST['Vc_item'])?$_POST['Vc_item']:'';
            $stuffArr= isset($_POST['Vc_stuff'])?$_POST['Vc_stuff']:'';
            $specificationArr= isset($_POST['Vc_specification'])?$_POST['Vc_specification']:'';
            $factorysArr= isset($_POST['Vc_factorys'])?$_POST['Vc_factorys']:'';
            $amountArr= isset($_POST['N_amount'])?$_POST['N_amount']:'';
            $unitTypeArr= isset($_POST['I_unitType'])?$_POST['I_unitType']:'';
            
            if(!$itemArr){returnjson(array('err'=>-1,'msg'=>'采购单未添加产品'));}

            $factorysArr = preg_replace("/，|，/s", ",", $factorysArr);//将中文全角和半角逗号替换为英文半角
//             $factorysArr=str_replace('，', ',', $factorysArr);
//             $factorysArr=str_replace('，', ',', $factorysArr);

              /* 文件锁开始 */
//          define("LOCK_FILE_PATH", "/lock/requirecommodity.lock");//到TopFile
            if( !file_exists(LOCK_FILE_PATH) ){
                
                $fp = fopen( LOCK_FILE_PATH, "w" );
                fclose ( $fp );
            
            }
            $fp = fopen( LOCK_FILE_PATH, "r" );
//             if (!$fp) {
//                 echo "Failed to open the lock file!";
//                 exit(1);//异常处理
//             }
            flock ( $fp, LOCK_EX );
            
            //添加原子操作代码
            //插入sm_requirement
            
            $requirementID=$objRequireCommodity->addStep_1($dar);
            if(!$requirementID){
                returnjson(array('err'=>-1,'msg'=>'添加失败'));
            }
            
            //插入sm_requirement_commodity
            $darc['I_requirementID']=$requirementID;
            
            $commodityID=$objRequireCommodity->addStep_2($darc);
            if(!$commodityID){
                returnjson(array('err'=>-1,'msg'=>'添加失败'));
            }
            
            $Vc_commodityorderIds='';
            for($i=0;$i<count($itemArr);$i++){
                
                $da['Vc_item']=trim($itemArr[$i]);
                $da['Vc_stuff']=trim($stuffArr[$i]);
                $da['Vc_specification']=$specificationArr[$i];
                $da['Vc_factoryIds']=rtrim($factorysArr[$i],',');//去除末尾的逗号
                $da['N_amount']=trim($amountArr[$i]);
                $da['I_unitType']=trim($unitTypeArr[$i]);
                //插入sm_requirement_commodityorder
                $rowid=$objRequireCommodity->addStep_3($da);
                if($i==0){
                    $Vc_commodityorderIds.=$rowid;
                }
                $Vc_commodityorderIds.=','.$rowid;
            }
            $sqlw="id={$commodityID}";
            $daArr['Vc_commodityorderIds']=$Vc_commodityorderIds;
            $r=$objRequireCommodity->update($daArr, $sqlw);//addStep_4
            
            flock ( $fp, LOCK_UN );
            fclose ( $fp );
            /* 文件锁结束 */
            
            if($r){
                returnjson(array('err'=>0,'msg'=>'添加成功'));
            }else{
                returnjson(array('err'=>-1,'msg'=>'添加失败'));
                
            }
            
        
    }else{
        
       
        $sql="SELECT id,Vc_name FROM sm_mall_class where Status=1 ";
        $r= $Db->fetch_all_assoc($sql);
//         if($r){
//             foreach ($r as &$v){
                
//                 $sql="SELECT id,Vc_name FROM sm_item_class where Status=1 and I_mall_classID={$v['id']}";
//                 $rs= $Db->fetch_all_assoc($sql);
//                 $v['subItemClass']=$rs;
//             }
            
//         }
//         $root['err']=0;
         //获取买家数据
        $I_buyerID=$uid;
        $sql="SELECT a.Vc_truename,a.Vc_mobile,b.Vc_name FROM user_base a 
            LEFT JOIN sm_company b on b.id = a.I_companyID
            WHERE a.Status=1 and a.id={$uid}";
        $r2= $Db->fetch_one($sql);
        $root['mallclass']=$r;
        $root['userinfo']=$r2;
  
        $p['data']=$root;
//         returnjson($root);
        
    }
            
             
            break;   
            
    case 'mallclassinfo':
                /**
                 * @author wh
                 * 行业下分类列表接口
                 * url地址：
                 * http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=mallclassinfo
                 * 输入：
                 *
                 * I_mall_classID: int 行业id
                 *
                 * 输出：
                 * err:int 结果状态 -1失败 0成功
                 * message: 提示信息
                 * 以下仅在err为0时会返回
                 * itemClassList: Array 类别列表
            
                 *
                 */
            
                //index.php?act=item&m=itemclassinfo&I_classID=1
                $I_mall_classID=$FLib->requestint('I_mall_classID',0,'行业id',1);
            
                if(!$I_mall_classID){returnjson(array('err'=>-1,'msg'=>'参数有误'));}
                //分类列表
                $sql="SELECT id,Vc_name FROM sm_item_class where Status=1 and I_mall_classID={$I_mall_classID}";
                $data['itemClassList']= $Db->fetch_all_assoc($sql);
                  
                $data['err']=0;
                returnjson($data);
            
                break;            
        
case 'list':
        /**
         * @author wh
         * 产品需求信息列表及综合搜索
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=list
         * 输入：需登录后访问
         *
         * 筛选条件(可选——可组合)
         * cpage:int 当前的页数,默认为1
         * psize: int 数据分页量,默认为15
         * I_mallClassID：int  行业ID
         * I_publish_status: int 状态：1-已成交，2-未成交
         * 
         * starttime: string 开始时间（格式为2016-04-01）
         * endtime: string 结束时间
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         ** 以下仅在err为0时会返回
         * page: int 当前页数
         * count: string 数据总量
         * pcount: int 总页数
         * data:数据
         *
         * 详见模板输出
         *
         * */
      
        $I_buyerID=$uid;
    
        $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):15;
        if(!is_numeric($page) || !is_numeric($psize)) {
            returnjson(array('err'=>-1,'msg'=>'数据不合法'));
        }
    
        $I_publish_status=$FLib->requestint('I_publish_status',0,'状态',1);//状态：1-已成交，2-未成交
        $I_mallClassID=$FLib->requestint('I_mallClassID',0,'行业ID',1);
    
        $endtime = $FL->requeststr('endtime',1,10,'结束时间');
        $starttime  = $FL->requeststr('starttime',1,10,'开始时间');
    
        $sqlw='a.Status=1 and b.Status=1 and a.I_requirementClassID=1 and a.I_userID='.$I_buyerID;
    
        //行业判断
        if(isset($_REQUEST['I_mallClassID'])){
            if( $I_mallClassID ){

                $sqlw.=" and b.I_mallClassID = {$I_mallClassID}";
                $p['I_mallClassID']=$I_mallClassID;
            } 

        }
       
        //状态判断
        if(isset($_REQUEST['I_publish_status'])){
            if($I_publish_status){
                $p['I_publish_status']=$I_publish_status;
                switch ($I_publish_status){
                    case 1:
                        $sqlw.=" and a.I_publish_status=5";
                        break;
                    case 2:
                        $sqlw.=" and a.I_publish_status in (1,2,3,4)";
                        break;
                    
                }
                
            }
             
        }
    
        if($starttime)$sqlw .= " and b.Createtime >= '".$starttime."'";
        if($endtime)$sqlw .= " and  b.Createtime <= '".$endtime."'";
    
        $order='b.Createtime desc';
    
        $da=$objRequireCommodity->getDataListByPage($page, $psize, $sqlw, $order);
        
        $page = $da['page'];
    	$count = $da['count'];
    	$pcount = $da['pcount'];
    	$p['startime']=$starttime;
    	$p['endtime']=$endtime;
    
    	$p['data'] = $da;
    	$p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=user&m=requirecommodity&w=list&starttime={$starttime}&endtime={$endtime}");
    
    	$sql="SELECT id,Vc_name FROM sm_mall_class where Status=1 ";
    	$p['mallArr']= $Db->fetch_all_assoc($sql);
    
//         returnjson($da);
    
        break;
    
    
    

}

?>
