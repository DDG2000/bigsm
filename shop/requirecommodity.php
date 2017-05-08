<?php
// if(!defined('WEBROOT'))exit;

$w=$FL->requeststr('w',1,0,'w',1,1);

$m.='_'.$w;



require_once(WEBROOTINCCLASS . 'OrderUtils.php');
require_once(WEBROOTINCCLASS . 'RequireCommodity.php');
require_once(WEBROOTINCCLASS . 'MessageService.php');
$objMsg=new MessageService();
$objNo=new OrderUtils();
$objRequireCommodity=new RequireCommodity();


require_once(WEBROOTINC.'File.class.php');
$Fc = new FileClass();


switch($w){
    
      
 case 'offerset':
         /**
         * @author wh
         * 报价设置接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=offerset
         * 输入：需登录后访问
         * mallClassID : int 行业id
         * itemClassIds: string 分类id集合，多个id之间以英文半角逗号分隔
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         *
         *
         *
         * 结合使用行业下分类列表接口
         * url地址：
         *  http://www.bigsm.com/index.php?act=item&m=mallclassinfo
         * */
     
//      $I_shopID = $objShop->isBeShop($uid);
     
     
     $r=$objRequireCommodity->getOfferset($I_shopID);
     
     
      if(isset($_REQUEST['submit'])){
         
          $da['I_shopID']=$I_shopID;
          
          $da['I_mallClassID']=$FLib->requestint('mallClassID',0,'行业ID',1);
          $da['Vc_itemClassIds']=$FL->requeststr('itemClassIds',1,150,'所属类别');
          
          if(!$da['I_mallClassID']){returnjson(array('err'=>-1,'msg'=>'未选择报价类别'));}
          if(!$da['Vc_itemClassIds']){returnjson(array('err'=>-1,'msg'=>'未选择所属类别'));}
          
          //存在则更新，不存在则添加
          if($r){
              
              $sqlw="id={$r['id']}";
              $rs=$objRequireCommodity->updateOfferset($da, $sqlw);
          }else{
              
          $rs=$objRequireCommodity->addOfferset($da);
          }
          
          
          if($rs){
              returnjson(array('err'=>0,'msg'=>'设置成功！'));
          }else{
              returnjson(array('err'=>-1,'msg'=>'设置失败！'));
          
          }
        
      }else{
         
          
                $sql="SELECT id,I_mallclassID,Vc_itemClassIds FROM sm_shop_commodity_offerset where Status=1 and I_shopID=".$I_shopID;
                $rs= $Db->fetch_one($sql);
                if($rs){
                    
                $root=array();
                
                $sql="SELECT id,Vc_name FROM sm_mall_class where Status=1 ";
                $root['mallclass']= $Db->fetch_all_assoc($sql);
                
                $sql="SELECT id,Vc_name FROM sm_item_class where Status=1 and I_mall_classID={$rs['I_mallclassID']}";
                $root['subitemclass']= $Db->fetch_all_assoc($sql);
            
                $itemClassArr = explode(",", $rs['Vc_itemClassIds']);
                $p['itemClassArr']=array_flip($itemClassArr);
                $p['data']=$root;
                $p['issubed']=1;
                $p['I_mallclassID']=$rs['I_mallclassID'];
                
                }else{
                    
                    $sql="SELECT id,Vc_name FROM sm_mall_class where Status=1 ";
                    $r= $Db->fetch_all_assoc($sql);
                    $root=array();
                    if($r){//默认取第一个行业的数据
                       
                            $sql="SELECT id,Vc_name FROM sm_item_class where Status=1 and I_mall_classID={$r[0]['id']}";
                            $rs= $Db->fetch_all_assoc($sql);
                            $root['subitemclass']=$rs;
            
                    }else{
                        $root['subitemclass']=null;
                    }
                    $root['mallclass']=$r;
                    $p['data']=$root;
                    $p['issubed']=0;
            //         returnjson($root);
                    
                }
//          returnjson($root);
          
      }
        
        
        break;

        
 case 'list':
            /**
             * @author wh
             * 报价信息列表及综合搜索
             * url地址：
             * http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=list
             * 输入：需登录后访问
             *
             * 筛选条件(可选——可组合)
             * cpage:int 当前的页数,默认为1
             * psize: int 数据分页量,默认为15
             * I_mallClassID：int  行业ID
             * I_publish_status: int 状态：1-已成交，2-未成交
             * I_offer_status: int 状态：1-已报价，2-未报价，3-请确认订货函，4-已成交
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
//              $I_shopID = $objShop->isBeShop($uid);
//      $sql ="SELECT I_status from sm_requirement_commodityorder_offer WHERE I_shopID=1 and I_commodityID=2 GROUP BY I_status";
//      $rsOffer = $Db->fetch_all_assoc($sql);
//      dump($rsOffer);exit();
             $r_set=$objRequireCommodity->getOfferset($I_shopID);
             
          
            $page=isset($_REQUEST['cpage'])?intval($_REQUEST['cpage']):1;
            $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):15;
            if(!is_numeric($page) || !is_numeric($psize)) {
                returnjson(array('err'=>-1,'msg'=>'数据不合法'));
            }
        
            $I_publish_status=$FLib->requestint('I_publish_status',0,'采购状态',1);//状态：1-已成交，2-未成交
            $I_offer_status=$FLib->requestint('I_offer_status',0,'报价状态',1);//1-已报价，2-未报价，3-请确认订货函，4-已成交
        
            $endtime = $FL->requeststr('endtime',1,10,'结束时间');
            $starttime  = $FL->requeststr('starttime',1,10,'开始时间');
        
           $sqlw='a.Status=1 and b.Status=1 and a.I_requirementClassID=1 ';

            //报价状态判断
            $p['I_offer_status']=4;
            if(isset($_REQUEST['I_offer_status'])){
                if($I_offer_status){
                    $p['I_offer_status']=$I_offer_status;
                    switch ($I_offer_status){
                        case 1:
                            $sqlw.=" and b.id  in ( SELECT I_commodityID from sm_requirement_commodityorder_offer  where Status=1 and I_status=1 and  I_shopID={$I_shopID} )";
                            break;
                        case 2:
                            $sqlw.=" and b.id not in ( SELECT I_commodityID from sm_requirement_commodityorder_offer  where Status=1 and I_status in (1,3,4) and  I_shopID={$I_shopID} )";
                            break;
                        case 3:
                            $sqlw.=" and b.id  in ( SELECT I_commodityID from sm_requirement_commodityorder_offer  where Status=1 and I_status=3 and  I_shopID={$I_shopID} )";
                            break;
                        case 4:
                            $sqlw.=" and b.id  in ( SELECT I_commodityID from sm_requirement_commodityorder_offer  where Status=1 and I_status=4 and  I_shopID={$I_shopID} )";
                            break;
                            
                    }
                }
                 
            }
             $p['I_publish_status']=2;
            //采购成交状态判断
            if(isset($_REQUEST['I_publish_status'])){
                if($I_publish_status){
                    $p['I_publish_status']=$I_publish_status;
                    switch ($I_publish_status){
                        case 1:
                            $sqlw.=" and a.I_publish_status=5";
                            break;
                        case 2:
                            $sqlw.=" and a.I_publish_status in (2,3,4)";
                            break;
                            
                    }

                }  
            }
          
            if($starttime)$sqlw .= " and b.Createtime >= '".$starttime."'";
            if($endtime)$sqlw .= " and  b.Createtime <= '".$endtime."'";
        
            $order='b.Createtime desc';
        
            $da=$objRequireCommodity->getOffsetListByPage($page, $psize, $sqlw, $order, $r_set['I_mallClassID'], $r_set['Vc_itemClassIds']);
            //1.查报价表-是否报价->是
            //2.查订货函表是否有订货函I_offer_status：1-已报价，2-未报价，3-请确认订货函，4-已成交
            //有看每个订货函是否都确认，都确认为4-已成交，否则为请确认订货函
            foreach ($da['data'] as &$v){
                
                $v['I_offer_status']='';
                $sql ="SELECT I_status from sm_requirement_commodityorder_offer WHERE I_shopID={$I_shopID} and I_commodityID={$v['cid']} GROUP BY I_status";
                $rsOffer = $Db->fetch_all_assoc($sql);
                if($rsOffer){
                    $statusArr=array();
                    foreach ($rsOffer as $vo){
                        $statusArr[]=$vo['I_status'];
                    }
                    if(in_array(3, $statusArr)){
                        
                        $v['I_offer_status']=3;
                        
                    }else{
                        if(in_array(4, $statusArr)){
                            $v['I_offer_status']=4;
                        }else{
                            $v['I_offer_status']=1;
                        }
                        
                    }
                    
                    
                }else{
                    $v['I_offer_status']=2;
                }
                
            }
            
            $page = $da['page'];
            $count = $da['count'];
            $pcount = $da['pcount'];
            $p['nowtime']=date('Y-m-d H:i:s');
            $p['starttime']=$starttime;
            $p['endtime']=$endtime;
            $p['data'] = $da;
            $p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=shop&m=requirecommodity&w=list&starttime={$starttime}&endtime={$endtime}");
        
//                    returnjson($da);
        
            break;

                
    case 'add':
            
                /**
                 * @author wh
                 * 报价接口
                 * url地址：
                 * http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=add
                 * 输入：需登录后访问
                 * id：int 需求ID
                 * 
                 * submit:不存在该参数时查看通过模版输出信息，存在该参数表示提交
                 则还要传以下参数  :
                 * cid：int 采购单id
                 * corderID：array 采购单产品id数组
                 * factorys：array2 钢厂名称二维数组
                 * price：array2 价格二维数组
                 * outType：array2 仓库费二维数组
                 * warehouse：array2 仓库二维数组
                 * memo：array2 备注二维数组
                 *
                 *
                 * 输出：
                 * err:int 结果状态 -1失败 0成功
                 * msg: 提示信息
                 * 测试
                 *http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=add&id=2
                 * */
            
            
//                     $I_shopID = $objShop->isBeShop($uid);
                    $id=$FLib->requestint('id',0,'需求id',1);
                    if(!$id){returnjson(array('err'=>-1,'msg'=>'需求id有误'));}
                if(isset($_REQUEST['submit'])){
            
                    //加文件锁
                    

                    $I_commodityID=$FLib->requestint('cid',0,'采购单id',1);
                    if(!$I_commodityID){returnjson(array('err'=>-1,'msg'=>'采购单id有误'));}
            
                    
            
                    $corderIDArr= isset($_POST['corderID'])?$_POST['corderID']:'';
                    $factorysArr= isset($_POST['factorys'])?$_POST['factorys']:'';
                    $priceArr= isset($_POST['price'])?$_POST['price']:'';
                    $outTypeArr= isset($_POST['outType'])?$_POST['outType']:'';
                    $warehouseArr= isset($_POST['warehouse'])?$_POST['warehouse']:'';
                    $memoArr= isset($_POST['memo'])?$_POST['memo']:'';
                 
                    for($i=0;$i<count($corderIDArr);$i++){
                        $da=array();
                        $da['I_shopID']=$I_shopID;
                        $da['I_commodityID']=$I_commodityID;
                        $da['I_commodityorderID']=$corderIDArr[$i];
                        $tempArr=array();
                        $tempArr['factorys']=$factorysArr[$da['I_commodityorderID']];
                        $tempArr['price']=$priceArr[$da['I_commodityorderID']];
                        $tempArr['outType']=$outTypeArr[$da['I_commodityorderID']];
                        $tempArr['warehouse']=$warehouseArr[$da['I_commodityorderID']];
                        $tempArr['memo']=$memoArr[$da['I_commodityorderID']];
                        
                        $da['Vc_meta']=serialize($tempArr);
                     
                        //插入sm_requirement_commodityorder_offer
                        $rs=$objRequireCommodity->addOffer($da);

                    }
                    
                    //投资报价人数+1
                    //sm_requirement_bid+sm_requirement
                    $Db->QuerySql('begin');
//                     
                    $sqlw="id={$id}";
                    $r1 = $Db->autoExecute('sm_requirement', array('I_bids@'=>'I_bids+1'),'update',$sqlw);
                    
                    $reqda['I_reqID']=$id;
                    $reqda['I_userID']=$I_shopID;
                    $r2=$objRequireCommodity->addbids($reqda);
                    
                    
                    if($r1&&$r2){
                        $Db->QuerySql('commit');
                        returnjson(array('err'=>0,'msg'=>'报价成功'));
                    }else{
                        $Db->QuerySql('rollback');
                    }
                    
                   
            
                }else{
                    
                  
//                     $id=$FLib->requestint('id',0,'需求id',1);//
                    
//                     if(!$id){returnjson(array('err'=>-1,'msg'=>'参数不合法'));}
                    
                    $Rs=$objRequireCommodity->getOfferInfo($id);
                    $data=array();
                    if($Rs){
                        
                        $data['offerinfo']=$Rs;
                        
                        $Rs2=$objRequireCommodity->getCommodityOrderList($Rs['Vc_commodityorderIds']);
                        foreach ($Rs2 as &$v){
                            $v['Vc_factorys']=explode(',', $v['Vc_factorys']);
                        }
                        $data['commoditylist']=$Rs2;
                        
                        
                    }
                    
//                     require_once(WEBROOTINCCLASS.'Warehouse.php');
//                     $Warehouse=new Warehouse();
                    //库房信息
//                     $root['data']=$data;
                   $p['data']=$data;
                   //http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=add&id=2
//                    returnjson($data);
            
                }
            
                 
                break;
    case 'edit':
            
                /**
                 * @author wh
                 * 修改报价接口
                 * url地址：
                 * http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=edit
                 * 输入：需登录后访问
                 * id：int 需求ID
                 * cid：int 采购单id
                 * submit:不存在该参数时查看通过模版输出信息，存在该参数表示提交
                 * 则还要传以下参数  :
                 * 
                 * corderID：array 采购单产品id数组
                 * factorys：array2 钢厂名称二维数组
                 * price：array2 价格二维数组
                 * outType：array2 仓库费二维数组
                 * warehouse：array2 仓库二维数组
                 * memo：array2 备注二维数组
                 *
                 *
                 * 输出：
                 * err:int 结果状态 -1失败 0成功
                 * msg: 提示信息
                 *
                 *
                 *测试
                 *  http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=edit&id=2&cid=2
                 * */
        
//                      $I_shopID = $objShop->isBeShop($uid);
                 
                    
                    $id=$FLib->requestint('id',0,'需求id',1);
                    $I_commodityID=$FLib->requestint('cid',0,'采购单id',1);
                    if(!$id){returnjson(array('err'=>-1,'msg'=>'需求id'));}
                    if(!$I_commodityID){returnjson(array('err'=>-1,'msg'=>'采购单id有误'));}
                    
                   if(isset($_REQUEST['submit'])){
            
                    $corderIDArr= isset($_POST['corderID'])?$_POST['corderID']:'';
                    $factorysArr= isset($_POST['factorys'])?$_POST['factorys']:'';
                    $priceArr= isset($_POST['price'])?$_POST['price']:'';
                    $outTypeArr= isset($_POST['outType'])?$_POST['outType']:'';
                    $warehouseArr= isset($_POST['warehouse'])?$_POST['warehouse']:'';
                    $memoArr= isset($_POST['memo'])?$_POST['memo']:'';
                 
                    for($i=0;$i<count($corderIDArr);$i++){
                        $da=array();
                        $tempArr=array();
                        $tempArr['factorys']=$factorysArr[$da['I_commodityorderID']];
                        $tempArr['price']=$priceArr[$da['I_commodityorderID']];
                        $tempArr['outType']=$outTypeArr[$da['I_commodityorderID']];
                        $tempArr['warehouse']=$warehouseArr[$da['I_commodityorderID']];
                        $tempArr['memo']=$memoArr[$da['I_commodityorderID']];
                        
                        $da['Vc_meta']=serialize($tempArr);
                        $sqlw="I_commodityID={$I_commodityID} and I_shopID={$I_shopID} and I_commodityorderID={$corderIDArr[$i]}";

                        //更新sm_requirement_commodityorder_offer//进行修改
                       $rs=$objRequireCommodity->updateOffer($da,$sqlw);
                      

                    }
             
                        returnjson(array('err'=>0,'msg'=>'修改成功'));
                   
                   
            
                }else{
                  
                   
                    $Rs=$objRequireCommodity->getOfferInfo($id);
                    $data=array();
                    if($Rs){
                        
                        $data['offerinfo']=$Rs;
                        
                        $Rs2=$objRequireCommodity->getCommodityOrderList2($Rs['Vc_commodityorderIds'],$I_shopID,$I_commodityID);
                       
                        foreach ($Rs2 as &$v){
                            $v['Vc_meta']=@unserialize($v['Vc_meta']);
                            if($v['Vc_meta']){
                                foreach ($v['Vc_meta']['factorys'] as $k=>$vo){
                                    $temp = array();
                                    $temp['factorys'] = $vo;
                                    $temp['price'] = $v['Vc_meta']['price'][$k];
                                    $temp['outType'] = $v['Vc_meta']['outType'][$k];
                                    $temp['warehouse'] = $v['Vc_meta']['warehouse'][$k];
                                    $temp['memo'] = $v['Vc_meta']['memo'][$k];
                                    $v['metalist'][]=$temp;
                                }
                            }else{
                                $v['metalist'] = array();
                            }
                           unset($v['Vc_meta']);
                        }
                        $data['commoditylist']=$Rs2;
                        
                        
                    }
//                     returnjson($data);
                    $p['data']=$data;
                   //http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=edit&id=2&cid=2
            
                }
            
                 
                break;
    case 'del':
                
                    /**
                     * @author wh
                     * 删除已成交记录接口
                     * url地址：
                     * http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=del
                     * 输入：需登录后访问
                     * 
                     * cid：int 采购单id
                     *
                     * 输出：
                     * err:int 结果状态 -1失败 0成功
                     * msg: 提示信息
                     *
                     * */
        
        
//                    $I_shopID = $objShop->isBeShop($uid);
                   
                    $I_commodityID=$FLib->requestint('cid',0,'采购单id',1);

                    if(!$I_commodityID){returnjson(array('err'=>-1,'msg'=>'采购单id有误'));}
                     
                
                    $da['Status']=0;
                    $sqlw="I_commodityID={$I_commodityID} and I_shopID={$I_shopID} ";
            
                    $rs=$objRequireCommodity->updateOffer($da, $sqlw);
                    if($rs){
                        returnjson(array('err'=>0,'msg'=>'删除成功！'));
                    }else{
                        returnjson(array('err'=>-1,'msg'=>'删除失败！'));
                
                    }
                    //订货函
                     
                    break;                
   
    case 'detail':
        /**
         * @author wh
         * 报价详情与订货函确认接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=detail
         * 输入：需登录后访问
         * id：int 需求ID
         * cid：int 采购单id
         * submit:不存在该参数时查看通过模版输出信息，存在该参数表示提交
         则只传以下参数  :
         * orderLetterId：int 订货函id
         *
         * 输出：
         * code:int 结果状态 500失败 200成功
         * msg: 提示信息
         * data：数据
         *
         *
         *测试地址
         * //http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=detail&id=2&cid=2
         * */
   
//          $I_shopID = $objShop->isBeShop($uid);
    
        if(isset($_REQUEST['submit'])){
    
            $letterId=$FLib->requestint('orderLetterId',0,'订货函id',1);
            if(!$letterId){
                returnErrJson('订货函id不能为空');
            }
            $dataArr['I_status']=2;
            $sqlw="id={$letterId} and I_shopID={$I_shopID}";
            $rs = $objRequireCommodity->updateOrderLetter($dataArr, $sqlw);
            
            
            
    
            if($rs){
                
                $sql="select I_buyerID,I_commodityID,I_commodityorderID  FROM  sm_requirement_commodityorder_letter  WHERE ".$sqlw;
                $r = $Db->fetch_one($sql);

             //   该采购单采购状态：所有商家的订货函都确认，所有都成交才算成交,每次确认时查询采购单所有产品的订货函是否都报价确认
                //1.拿到所有产品id
                $sql ="SELECT a.id,a.N_amount FROM sm_requirement_commodityorder a 
                 WHERE 
                find_in_set(a.id,(SELECT Vc_commodityorderIds from sm_requirement_commodity  WHERE Status=1 and id = {$r['I_commodityID']}));";
                
                $commodityorderArr = $Db->fetch_all_assoc($sql);
                
                $cgflag =1;
                //2.遍历确认所有产品的订货函
                foreach ($commodityorderArr as $v){
                    
                    $sql = "SELECT sum(t.N_amount) amount FROM sm_requirement_commodityorder_letter t
                WHERE t.Status= 1 and t.I_commodityID={$r['I_commodityID']} and t.I_status=2 and t.I_commodityorderID={$v['id']}";
                    
                    $rsnum  = $Db->fetch_one($sql);
                    if($rsnum){
                        if($rsnum['amount']!=$v['N_amount']){
                            $cgflag = false;
                            
                        }
                    }else {
                        $cgflag = false;
                    }
                }
                if($cgflag){
                    $sql=" SELECT I_requirementID from sm_requirement_commodity WHERE Status =1 and id={$r['I_commodityID']}";
              
                    $I_requirementID=$Db->fetch_val($sql);
                    //修改采购需求状态为成交
                    $reqRs = $objRequireCommodity->updateRequirement(array('I_publish_status'=>5), ' id='.$I_requirementID);
                    
                }
                
                
             //   对应采购单报价状态：此处单笔成交
                $da['I_status'] = 4;
                $sqlw2 ="I_commodityID={$r['I_commodityID']} and I_commodityorderID={$r['I_commodityorderID']} and I_shopID={$I_shopID}";
                $rs2 = $objRequireCommodity->updateOffer($da, $sqlw2);
                
                //发消息
                $sql ="SELECT Vc_orderSn,Vc_name,I_requirementID FROM sm_requirement_commodity WHERE id = {$r['I_commodityID']}";
                $r2 = $Db->fetch_one($sql);
                
                //--拿到供货公司
                $sql = "SELECT Vc_name FROM sm_shop WHERE id ={$I_shopID}";
                $r3 = $Db->fetch_one($sql);
                
                
                $daArr=array();
                $daArr['Vc_orderSn']=$r2['Vc_orderSn'];
                $daArr['Vc_name']=$r2['Vc_name'];
                $daArr['Vc_company']=$r3['Vc_name'];
                $daArr['I_requirementID']=$r2['I_requirementID'];
//                 $daArr['I_commodityID']=$r['I_commodityID'];
                $daArr['weburl']=$Cfg->WebRoot;
                $daArr['uid']=$r['I_buyerID'];
               
                $objMsg->sendConfirmLetterSuccess($daArr);
                
              //  用户采购编号为xnyf0000000001的钢板采购计划订货函供货单位********已确认，请联系交易！
                //             $da=array();
                //             $da['title'] = '已确认订货函:';
                //             $da['content'] = "采购编号为".$r['Vc_orderSn']."的".$r['Vc_name'].",订货函供货单位".$r['Vc_company']."已确认，请联系交易";//
                //             $da['url'] = $Cfg->WebRoot.'index.php?act=user&m=requirecommodity&w=detail&id='.$r['I_requirementID'];
                //             //http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=detail&id=2
                //             //$data['I_shopID']根据商家id拿到用户uid
                //             $da['userID']=$r['I_buyerID'];
                
                returnSucJson('确认成功');
            }else{
                returnErrJson('确认失败');
            }
            //                 returnErrJson(12,'dd');
            //                 returnSucJson('成功');
    
        }else{
            $id=$FLib->requestint('id',0,'需求id',1);
            $I_commodityID=$FLib->requestint('cid',0,'采购单id',1);
            if(!$id){ returnErrJson('需求id不能为空');}
            if(!$I_commodityID){returnErrJson('采购单id不能为空');}
    
            $Rs=$objRequireCommodity->getOfferInfo($id);
            $data=array();
            if($Rs){
    
                $data['offerinfo']=$Rs;
    
                $Rs2=$objRequireCommodity->getCommodityOrderList2($Rs['Vc_commodityorderIds'],$I_shopID,$I_commodityID);
    
                foreach ($Rs2 as &$v){
                    $v['Vc_meta']=@unserialize($v['Vc_meta']);
                    if($v['Vc_meta']){
                        foreach ($v['Vc_meta']['factorys'] as $k=>$vo){
                            $temp = array();
                            $temp['factorys'] = $vo;
                            $temp['price'] = $v['Vc_meta']['price'][$k];
                            $temp['outType'] = $v['Vc_meta']['outType'][$k];
                            $temp['warehouse'] = $v['Vc_meta']['warehouse'][$k];
                            $temp['memo'] = $v['Vc_meta']['memo'][$k];
                            $v['metalist'][]=$temp;
                        }
                    }else{
                        $v['metalist'] = array();
                    }
                    
                    unset($v['Vc_meta']);
                }
                $data['commoditylist']=$Rs2;
    
                $Rs3=$objRequireCommodity->getOrderLetter4shoper($Rs['Vc_commodityorderIds'], $I_commodityID, $I_shopID);
                $data['orderletterlist']=$Rs3;
    
            }
//                             returnjson($data);
            //                 returnErrJson(12,'dd');
            //                 returnSucJson('成功');
            $p['data']=$data;
    
        }
    
        break;
                    


            
       
        

    
    
    

}

?>
