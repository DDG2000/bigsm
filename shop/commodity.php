<?php
// if(!defined('WEBROOT'))exit;

$w=$FL->requeststr('w',1,0,'w',1,1);

$m.='_'.$w;




switch($w){

    case 'list_add'://添加列表
        /**
         * @author wh
         * 产品添加列表接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=commodity&w=list_add
         * 输入(无)：需登录后访问
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * message: 提示信息
         * 以下仅在err为0时会返回
         * mallClassList: array 行业
         * itemClassList: array 类型
         * wareHouseList: array 仓库（为空则提示用户在仓库管理中添加）
         *
         *注意：
         *类型选中后用I_classID访问http://www.bigsm.com/index.php?act=item&m=itemclassinfo
         可拿到对应的分类下品名、材质、规格、钢厂信息
         * */
    
        //$I_shopID = $objShop->isBeShop($uid);
    
        $sql="SELECT id,I_mallclassID,Vc_itemIds FROM sm_shop where Status=1 and I_userID=".$uid;
        $r= $Db->fetch_one($sql);
//                 var_dump($r);
//                 exit();
        $sql="SELECT * FROM sm_mall_class where Status=1 and id in (". $r['I_mallclassID'].") ";
        $r1= $Db->fetch_all_assoc($sql);
    
        $sql="SELECT * FROM sm_item_class where Status=1 and id in (". $r['Vc_itemIds'].") order by I_order ";
        $r2= $Db->fetch_all_assoc($sql);
    
        $sql="SELECT id,Vc_name FROM sm_warehouse  where Status=1 and I_shopID=". $r['id']." order by Createtime ";
        $r3= $Db->fetch_all_assoc($sql);
    
    
        $root['mallClassList']=$r1;
        $root['itemClassList']=$r2;//选中后用I_classID访问http://www.bigsm.com/index.php?act=item&m=itemclassinfo可拿到对应的分类下品名、材质、规格、钢厂信息
        $root['wareHouseList']=$r3;
    
        $p['data'] = $root;
//         returnjson($root);
    
        break;
    case 'add':
        /**
         * @author wh
         * 产品添加接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=commodity&w=add
         * 输入：需登录后访问
         *
         * I_mallClassID：int 行业id
         * I_itemClassID: int 类型id
         * I_itemID: int 品名id
         * I_stuffID: int 材质id
         * I_specificationID: int 规格id
         * I_factoryID: int 钢厂id
         * I_warehouseID: int 仓库id
         * N_amount: int 数量
         * N_weight: string 重量
         * N_price: string 单价
         *
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * message: 提示信息
         *
         * */
        //http://www.bigsm.com/index.php?act=shop&m=add&I_itemClassID=1&I_itemID=1&I_stuffID=1&I_specificationID=1&I_factoryID=1&I_warehouseID=1&N_amount=2&N_weight=2&N_price=2000
    
        //1.根据登录的userid查sm_shop表的I_type判断是自营商城还是撮合市场
        //2.地区为仓库的地区，根据仓库id拿到仓库的省市区id
       
        $sql="SELECT id,I_type FROM sm_shop where I_userID=".$uid;
        $r= $Db->fetch_one($sql);
    
        $I_mallID=$r['I_type'];    //1-自营商城，2-撮合市场
//         $I_shopID=$r['id'];//所属商家id
    
        $I_mallClassID=$FLib->requestint('I_mallClassID',0,'行业id',1);
        $I_itemClassID=$FLib->requestint('I_itemClassID',0,'类型id',1);
        $I_itemID=$FLib->requestint('I_itemID',0,'品名id',1);
        $I_stuffID=$FLib->requestint('I_stuffID',0,'材质id',1);
        $I_specificationID=$FLib->requestint('I_specificationID',0,'规格id',1);
        $I_factoryID=$FLib->requestint('I_factoryID',0,'钢厂id',1);
        $I_warehouseID=$FLib->requestint('I_warehouseID',0,'仓库id',1);
        $N_amount=$FLib->requestint('N_amount',0,'商品剩余数量',1);
    
        $N_weight=floatval($FL->requeststr('N_weight',1,50,'产品单位重量'));
        $N_price= floatval($FL->requeststr('N_price',1,50,'产品单价'));
    
    
        if(!$I_mallClassID){returnjson(array('err'=>-1,'msg'=>'未选择行业'));}
        if(!$I_itemClassID){returnjson(array('err'=>-1,'msg'=>'未选择类型'));}
        if(!$I_itemID){returnjson(array('err'=>-1,'msg'=>'未选择品名'));}
        if(!$I_stuffID){returnjson(array('err'=>-1,'msg'=>'未选择材质'));}
        if(!$I_specificationID){returnjson(array('err'=>-1,'msg'=>'未选择规格'));}
        if(!$I_factoryID){returnjson(array('err'=>-1,'msg'=>'未选择钢厂'));}
        if(!$I_warehouseID){returnjson(array('err'=>-1,'msg'=>'未选择仓库'));}
        if(!$N_weight){returnjson(array('err'=>-1,'msg'=>'未填写产品单位重量'));}
        if(!$N_price){returnjson(array('err'=>-1,'msg'=>'未填写产品单价'));}
        if(!is_numeric($N_weight) || !is_numeric($N_price)) {
            returnjson(array('err'=>-1,'msg'=>'产品单价或产品单位重量数据不合法'));
    
        }
    
        $sql="SELECT I_provinceID,I_cityID,I_districtID FROM sm_warehouse where Status=1 and id=".$I_warehouseID;
        $r1= $Db->fetch_one($sql);
      
        $I_provinceID=$r1['I_provinceID'];
        $I_cityID=$r1['I_cityID'];
        $I_districtID=$r1['I_districtID'];
    
        $da=array();
        $da['I_shopID'] = $I_shopID;
        $da['I_mallID']=$I_mallID;
        $da['I_mallClassID']=$I_mallClassID;
        $da['I_itemClassID']=$I_itemClassID;
        $da['I_itemID']=$I_itemID;
        $da['I_stuffID']=$I_stuffID;
        $da['I_specificationID']=$I_specificationID;
        $da['I_factoryID']=$I_factoryID;
        $da['I_warehouseID']=$I_warehouseID;
        $da['I_provinceID']=$I_provinceID;
        $da['I_cityID']=$I_cityID;
        $da['I_districtID']=$I_districtID;
        $da['N_amount']=$N_amount;
        $da['N_weight']=$N_weight;
        $da['N_price']=$N_price;
        $da['Createtime@'] = 'now()';
    
        //         $rs=$Db->autoExecute('sm_commodity_steel', $da,'INSERT');
        $rs=$obj->add($da);
        if($rs){
            returnjson(array('err'=>0,'msg'=>'产品添加成功！'));
        }else{
            returnjson(array('err'=>-1,'msg'=>'产品添加失败！'));
    
        }
         
        exit;
        break;
    
    
    case 'list':
        /**
         * @author wh
         * 产品管理列表及搜索接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=commodity&w=list
         * 输入：需登录后访问
         *
         * 筛选条件(可选——可组合)
         * page:int 当前的页数,默认为1
         * psize: int 数据分页量,默认为15
         * ispublished: int 是否发布(传1-未发布，2-已发布,不传-全部)
         * sType: int 搜索类型（1-品名，2-材质，3-规格，4-钢厂，5-仓库）
         * sKey: string 关键词
         * starttime: string 开始时间（格式为2016-04-01）
         * endtime: string 结束时间
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * message: 提示信息
         * 以下仅在err为0时会返回
         * page: int 当前页数
         * count: string 数据总量
         * pcount: int 总页数
         * data:数据
         *
         *
         */
        //拿到商家id来进行查询
       
        
     //   $I_shopID = $objShop->isBeShop($uid);
        

        $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):15;
        if(!is_numeric($page) || !is_numeric($psize)) {
            returnjson(array('err'=>-1,'msg'=>'数据不合法'));
    
        }
         
    
        $ispublished=$FLib->requestint('ispublished',0,'是否发布',1);//是否发布传1-未发布，2-已发布
        $sType  = $FLib->requestint('sType',0,9,'搜索类型');//1-品名，2-材质，3-规格，4-钢厂，5-仓库
        $sKey= $FL->requeststr('sKey',1,50,'关键词');
    
        $endtime = $FL->requeststr('endtime',1,10,'结束时间');
        $starttime  = $FL->requeststr('starttime',1,10,'开始时间');
    
    
        $ext_condition = " Status =1 and I_shopID= ".$I_shopID;

        $p['ispublished'] = $ispublished;
        if(isset($_REQUEST['ispublished'])){
            if($ispublished) {
                switch ($ispublished){
                     case 1:
                        $ext_condition.=" and I_publish =0 ";
                     break;
                      case 2:
                        $ext_condition.=" and I_publish =1 ";
                     break;
                }
            }
            
        }
        
    
        //模糊查询搜索条件
        $wheresql='where 1=1  ';
    
        if($sKey!=''){
            $p['sKey'] = $sKey;
            switch ($sType){
                //1-品名，2-材质，3-规格，4-钢厂，5-仓库
                case 1:
                    $wheresql.=" and a.Vc_name like '%".$sKey."%'";
                    break;
                case 2:
                    $wheresql.=" and c.Vc_name like '%".$sKey."%'";
                    break;
                case 3:
                    $wheresql.=" and b.Vc_name like '%".$sKey."%'";
                    break;
                case 4:
                    $wheresql.=" and d.Vc_name like '%".$sKey."%'";
                    break;
                case 5:
                    $wheresql.=" and g.Vc_name like '%".$sKey."%'";
                    break;
                default:
                    $wheresql.=" and 1=1";
                    break;
            }
        }
        if($starttime)$wheresql .= " and Dt_publish >= '".$starttime."'";
        if($endtime)$wheresql .= " and  Dt_publish <= '".$endtime."'";
    
    
        //关联多表查询sql
        $sql="SELECT t.* ,f.Vc_name as mallclassname,e.Vc_name as itemclassname,a.Vc_name as itemname,b.Vc_name as specificationname,c.Vc_name as stuffname,d.Vc_name as factoryname
        ,g.Vc_name as warehouse
          from (SELECT id,I_mallClassID,I_itemClassID,I_itemID,I_specificationID,I_stuffID,I_factoryID,I_warehouseID,N_amount,N_weight,N_price,Dt_publish,I_publish FROM sm_commodity_steel
          WHERE Status=1 and I_shopID=1  ) t
          LEFT JOIN sm_item a on t.I_itemID=a.id
          LEFT JOIN sm_item_specification b on t.I_specificationID=b.id
          LEFT JOIN sm_item_stuff c on t.I_stuffID=c.id
          LEFT JOIN sm_item_factory d on t.I_factoryID=d.id
		  LEFT JOIN sm_warehouse g on t.I_warehouseID=g.id
		  LEFT JOIN sm_item_class e on t.I_itemClassID=e.id
		  LEFT JOIN sm_mall_class f on t.I_mallClassID=f.id
            ORDER BY Dt_publish asc";
    
        $order="Dt_publish asc";
        
        $da=$obj->getDataListByPage($page, $psize, $ext_condition, $wheresql, $order);
    
        $da['err']=0;
    
        // returnjson($da);
        $page = $da['page'];
        $count = $da['count'];
        $pcount = $da['pcount'];
        $p['starttime']=$starttime;
        $p['endtime']=$endtime;
        $p['data'] = $da;
        $p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=shop&m=commodity&w=list&starttime={$starttime}&endtime={$endtime}");
        
    
    
    
        break; 
 
  case 'publish':
            /**
             * @author wh
             * 发布及撤销接口
             * url地址：
             * http://www.bigsm.com/index.php?act=shop&m=commodity&w=publish
             * 输入：需登录后访问
             * pType: int 类型（1-发布，2-撤销发布）
             * ids: string 产品id组合（多个产品中间，以英文半角逗号分割,格式为1,3,4）
             *
             *
             * 输出：
             * err:int 结果状态 -1失败 0成功
             * msg: 提示信息
             *
             */
            $pType  = $FLib->requestint('pType',0,9,'类型');
            $ids= $FL->requeststr('ids',1,50,'产品id组合');
            if(!$ids){returnjson(array('err'=>-1,'msg'=>'未选择产品'));}
            if(!$pType){returnjson(array('err'=>-1,'msg'=>'未选择发布类型'));}
            if($pType==1){
        
                $da['I_publish']=1;
                $da['Dt_publish@']='now()';
        
            }else if($pType==2){
        
                $da['I_publish']=0;
        
            }
            $sqlw="id in ({$ids})";
            //         $rs=$Db->autoExecute('sm_commodity_steel',$da,'UPDATE',$sqlw);
            $rs=$obj->update($da, $sqlw);
        
            if($rs){
                returnjson(array('err'=>0,'msg'=>'更新成功！'));
            }else{
                returnjson(array('err'=>-1,'msg'=>'更新失败！'));
        
            }
             
            break;
            
     case 'delete':
                /**
                 * @author wh
                 * 删除产品接口
                 * url地址：
                 * http://www.bigsm.com/index.php?act=shop&m=commodity&w=delete
                 * 输入：需登录后访问
                 *
                 * ids: string 产品id组合（多个产品中间，以英文半角逗号分割,格式为1,3,4）
                 *
                 * 输出：
                 * err:int 结果状态 -1失败 0成功
                 * msg: 提示信息
                 *
                 *
                 */
            
                $ids= $FL->requeststr('ids',1,50,'产品id组合');
                if(!$ids){returnjson(array('err'=>-1,'msg'=>'未选择产品'));}
            
                $da['Status']=0;
                $sqlw="id in ({$ids})";
                // $rs=$Db->autoExecute('sm_commodity_steel',$da,'UPDATE',$sqlw);
                $rs=$obj->update($da, $sqlw);
                if($rs){
                    returnjson(array('err'=>0,'msg'=>'删除成功！'));
                }else{
                    returnjson(array('err'=>-1,'msg'=>'删除失败！'));
                }
                 
                break;
                
                
     case 'edit':
                    /**
                     * @author wh
                     * 编辑产品接口（行业不能更改）
                     * url地址：
                     * http://www.bigsm.com/index.php?act=shop&m=commodity&w=edit
                     * 输入：需登录后访问
                     *
                     * id: int 产品id (必填)
                     *
                     * issubmit：有则进行编辑，无则展示该产品信息
                     * 编辑时需提交的参数
                     * I_itemClassID: int 类型id
                     * I_itemID: int 品名id
                     * I_stuffID: int 材质id
                     * I_specificationID: int 规格id
                     * I_factoryID: int 钢厂id
                     * I_warehouseID: int 仓库id
                     * N_amount: int 数量
                     * N_weight: string 重量
                     * N_price: string 单价
                     *
                     * 输出：
                     * err:int 结果状态 -1失败 0成功
                     * msg: 提示信息
                     * 以下仅在err为0时会返回
                     * data:数据
                     *
                     */
                
                    $id  = $FLib->requestint('id',0,9,'类型');
                    if(!$id){returnjson(array('err'=>-1,'msg'=>'未选择产品'));}
                
//                         $I_shopID = $objShop->isBeShop($uid);
                    if(isset($_REQUEST['issubmit'])){
                        
                        $sql="SELECT id,I_type FROM sm_shop where I_userID=".$uid;
                        $r= $Db->fetch_one($sql);
                
                        //         $I_mallID=$r['I_type'];    //1-自营商城，2-撮合市场
                        //         $I_shopID=$r['id'];//所属商家id
                        //         $I_mallClassID=$FLib->requestint('I_mallClassID',0,'行业id',1);
                        $I_itemClassID=$FLib->requestint('I_itemClassID',0,'类型id',1);
                        $I_itemID=$FLib->requestint('I_itemID',0,'品名id',1);
                        $I_stuffID=$FLib->requestint('I_stuffID',0,'材质id',1);
                        $I_specificationID=$FLib->requestint('I_specificationID',0,'规格id',1);
                        $I_factoryID=$FLib->requestint('I_factoryID',0,'钢厂id',1);
                        $I_warehouseID=$FLib->requestint('I_warehouseID',0,'仓库id',1);
                        $N_amount=$FLib->requestint('N_amount',0,'商品剩余数量',1);
                
                        $N_weight=floatval($FL->requeststr('N_weight',1,50,'产品单位重量'));
                        $N_price= floatval($FL->requeststr('N_price',1,50,'产品单价'));
                
                        //         if(!$I_mallClassID){returnjson(array('err'=>-1,'msg'=>'未选择行业'));}
                        if(!$I_itemClassID){returnjson(array('err'=>-1,'msg'=>'未选择类型'));}
                        if(!$I_itemID){returnjson(array('err'=>-1,'msg'=>'未选择品名'));}
                        if(!$I_stuffID){returnjson(array('err'=>-1,'msg'=>'未选择材质'));}
                        if(!$I_specificationID){returnjson(array('err'=>-1,'msg'=>'未选择规格'));}
                        if(!$I_factoryID){returnjson(array('err'=>-1,'msg'=>'未选择钢厂'));}
                        if(!$I_warehouseID){returnjson(array('err'=>-1,'msg'=>'未选择仓库'));}
                        if(!$N_weight){returnjson(array('err'=>-1,'msg'=>'未填写产品单位重量'));}
                        if(!$N_price){returnjson(array('err'=>-1,'msg'=>'未填写产品单价'));}
                        if(!is_numeric($N_weight) || !is_numeric($N_price)) {
                            returnjson(array('err'=>-1,'msg'=>'产品单价或产品单位重量数据不合法'));
                
                        }
                
                        $sql="SELECT I_provinceID,I_cityID,I_districtID FROM sm_warehouse where Status=1 and id=".$I_warehouseID;
                        $r1= $Db->fetch_one($sql);
                
                        $I_provinceID=$r1['I_provinceID'];
                        $I_cityID=$r1['I_cityID'];
                        $I_districtID=$r1['I_districtID'];
                
                        $da=array();
                        //         $da['I_shopID'] = $I_shopID;
                        //         $da['I_mallID']=$I_mallID;
                        //         $da['I_mallClassID']=$I_mallClassID;
                        $da['I_itemClassID']=$I_itemClassID;
                        $da['I_itemID']=$I_itemID;
                        $da['I_stuffID']=$I_stuffID;
                        $da['I_specificationID']=$I_specificationID;
                        $da['I_factoryID']=$I_factoryID;
                        $da['I_warehouseID']=$I_warehouseID;
                        $da['I_provinceID']=$I_provinceID;
                        $da['I_cityID']=$I_cityID;
                        $da['I_districtID']=$I_districtID;
                        $da['N_amount']=$N_amount;
                        $da['N_weight']=$N_weight;
                        $da['N_price']=$N_price;
                        $da['Createtime@'] = 'now()';
                        $sqlw="id={$id}";
                        //  $rs=$Db->autoExecute('sm_commodity_steel',$da,'UPDATE',$sqlw);
                        $rs=$obj->update($da, $sqlw);
                        if($rs){
                            returnjson(array('err'=>0,'msg'=>'产品更新成功！'));
                        }else{
                            returnjson(array('err'=>-1,'msg'=>'产品更新失败！'));
                
                        }
                    }else{
                
                       
                        $r=$obj->getDataArrayById($id);
                        $data['err']=0;
                        $data['data']=$r['0'];
                        returnjson($data);
                
                    }
                     
                     
                    break;
                    
      case 'exportexl':
                    
                        /**
                         * @author wh
                         * 分页导出产品Excel
                         * url地址：
                         * http://www.bigsm.com/index.php?act=shop&m=commodity&w=exportexl
                         * 输入：需登录后访问     
                         * 筛选条件(可选——可组合)
                        * page:int 当前的页数,默认为1
                        * psize: int 数据分页量,默认为15
                        * ispublished: int 是否发布(传1-未发布，2-已发布,不传-全部)
                        * sType: int 搜索类型（1-品名，2-材质，3-规格，4-钢厂，5-仓库）
                        * sKey: string 关键词
                        * starttime: string 开始时间（格式为2016-04-01）
                        * endtime: string 结束时间
                         * 输出：
                         * excel文件
                         */
                       
//           $I_shopID = $objShop->isBeShop($uid);
        $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):15;
        
         
    
        $ispublished=$FLib->requestint('ispublished',0,'是否发布',1);//是否发布传1-未发布，2-已发布
        $sType  = $FLib->requestint('sType',0,9,'搜索类型');//1-品名，2-材质，3-规格，4-钢厂，5-仓库
        $sKey= $FL->requeststr('sKey',1,50,'关键词');
    
        $endtime = $FL->requeststr('endtime',1,10,'结束时间');
        $starttime  = $FL->requeststr('starttime',1,10,'开始时间');
    
    
        $ext_condition = " Status =1 and I_shopID= ".$I_shopID;

        
        if(isset($_REQUEST['ispublished'])){
            if($ispublished) {
                switch ($ispublished){
                     case 1:
                        $ext_condition.=" and I_publish =0 ";
                     break;
                      case 2:
                        $ext_condition.=" and I_publish =1 ";
                     break;
                }
            }
            
        }
        
    
        //模糊查询搜索条件
        $wheresql='where 1=1  ';
    
        if($sKey!=''){
           
            switch ($sType){
                //1-品名，2-材质，3-规格，4-钢厂，5-仓库
                case 1:
                    $wheresql.=" and a.Vc_name like '%".$sKey."%'";
                    break;
                case 2:
                    $wheresql.=" and c.Vc_name like '%".$sKey."%'";
                    break;
                case 3:
                    $wheresql.=" and b.Vc_name like '%".$sKey."%'";
                    break;
                case 4:
                    $wheresql.=" and d.Vc_name like '%".$sKey."%'";
                    break;
                case 5:
                    $wheresql.=" and g.Vc_name like '%".$sKey."%'";
                    break;
                default:
                    $wheresql.=" and 1=1";
                    break;
            }
        }
        if($starttime)$wheresql .= " and Dt_publish >= '".$starttime."'";
        if($endtime)$wheresql .= " and  Dt_publish <= '".$endtime."'";
                    
                      
                      
                        $order="Dt_publish asc";
                    
                    
                       
                    
                    
                        $rs=$obj->getDataListByPage($page, $psize, $ext_condition, $wheresql, $order);
                        //         var_dump($rs);
                        //         exit();
                        $dat['rs']=$rs['data'];
                        //         $dat['rscount']=iset($DataBase->fetch_val($sqlcount),0);
                        $dat['rscount']=count($rs['data']);
                        //         $dat['filename']=$title;
                        $dat['fields']=array();
                        $dat['fields'][]=array('类别','mallclassname');
                        $dat['fields'][]=array('类型','itemclassname');
                        $dat['fields'][]=array('品名','itemname');
                        $dat['fields'][]=array('材质','stuffname');
                        $dat['fields'][]=array('规格','specificationname');
                        $dat['fields'][]=array('钢厂','factoryname');
                        $dat['fields'][]=array('仓库','warehouse');
                        $dat['fields'][]=array('件数','N_amount');
                        $dat['fields'][]=array('件/吨','N_weight');
                        $dat['fields'][]=array('销售单价','N_price');
                        $dat['fields'][]=array('发布时间','Dt_publish','date');
                        $dat['fields'][]=array('状态','($Rs[$i][\'I_publish\']==1?\'已发布\':\'未发布\')','other');
                    
                        $filename='产品'.date('YmdHis',time());    //生成的Excel文件文件名
                    
                         
                        $res=$excelobj->push($dat,$filename);
                        // returnjson(array('err'=>0,'msg'=>'成功导出'.$dat['rscount'].'条数据'));
                    
                        break;
                    
        case 'downloadexl':
                            /**
                             * @author wh
                             * 下载产品模板
                             * url地址：
                             * http://www.bigsm.com/index.php?act=shop&m=commodity&w=downloadexl
                             * 输入：(无)
                             *
                             */
                        
                            /*
                             * 导出excel
                             */
                            $r=array();
                            $rs['data']=$r;
                            $dat['rs']=$rs['data'];
                            //         $dat['rscount']=iset($DataBase->fetch_val($sqlcount),0);
                            $dat['rscount']=count($rs['data']);
                            //         $dat['filename']=$title;
                            $dat['fields']=array();
                            $dat['fields'][]=array('类别','mallclassname');
                            $dat['fields'][]=array('类型','itemclassname');
                            $dat['fields'][]=array('品名','itemname');
                            $dat['fields'][]=array('材质','stuffname');
                            $dat['fields'][]=array('规格','specificationname');
                            $dat['fields'][]=array('钢厂','factoryname');
                            $dat['fields'][]=array('仓库','warehouse');
                            $dat['fields'][]=array('件数','N_amount');
                            $dat['fields'][]=array('件/吨','N_weight');
                            $dat['fields'][]=array('销售单价','N_price');
                            $filename='产品导入模板'.date('YmdHis',time());    //生成的Excel文件文件名
                             
                            $res=$excelobj->push($dat,$filename);
                        
                            exit;
                        
                        
                            break;
        case 'preview':
                            /**
                             * @author wh
                             * 预览模板接口
                             * url地址：
                             * http://www.bigsm.com/index.php?act=shop&m=commodity&w=preview
                             * 输入：(无)
                             *
                             */
                        
                            /*
                             * 导出excel
                             */
                            $r=array();
                            $rs['data']=$r;
                            $dat['rs']=$rs['data'];
                            //         $dat['rscount']=iset($DataBase->fetch_val($sqlcount),0);
                            $dat['rscount']=count($rs['data']);
                            //         $dat['filename']=$title;
                            $dat['fields']=array();
                            $dat['fields'][]=array('类别','mallclassname');
                            $dat['fields'][]=array('类型','itemclassname');
                            $dat['fields'][]=array('品名','itemname');
                            $dat['fields'][]=array('材质','stuffname');
                            $dat['fields'][]=array('规格','specificationname');
                            $dat['fields'][]=array('钢厂','factoryname');
                            $dat['fields'][]=array('仓库','warehouse');
                            $dat['fields'][]=array('件数','N_amount');
                            $dat['fields'][]=array('件/吨','N_weight');
                            $dat['fields'][]=array('销售单价','N_price');
                            $filename='产品导入模板'.date('YmdHis',time());    //生成的Excel文件文件名
                             
                            $res=$excelobj->preview($dat,$filename);
                        
                            exit;
                        
                        
                            break;


                            
                            
    case 'importexlstep1':
        /**
         * @author wh
         * 导入产品Excel
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=commodity&w=importexlstep1
         * 输入：
         * file_cp：file 产品文件excel
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * 以下仅在err为0时会返回
         * data: Array 校验后的数组数据（含原数据和出错提示）
         *
         *
         *
         * 注意：
         * 校验后的数组数据，序列化后不含-1
         * 才能将dataArray提交到
         * http://www.bigsm.com/index.php?act=shop&m=importexlstep2
         *
         */
    
         
        //校验数据
//         $I_shopID = $objShop->isBeShop($uid);
    
        $sql="SELECT id,I_type,Vc_mall_classIds,Vc_itemIds FROM sm_shop where Status=1 and I_userID=".$uid;
         
        $r= $Db->fetch_one($sql);
        $I_mallID=$r['I_type'];    //1-自营商城，2-撮合市场
//         $I_shopID=$r['id'];//所属商家id
    
        if(!$r['Vc_mall_classIds']){
            returnjson(array('err'=>-1,'msg'=>'请先选择经营行业'));
        }
    
        if(!$r['Vc_itemIds']){
            returnjson(array('err'=>-1,'msg'=>'请先选择经营类型'));
        }
    
        $sql="SELECT id,Vc_name FROM sm_mall_class where Status=1 and id in (". $r['Vc_mall_classIds'].") ";
        $r1= $Db->fetch_all_assoc($sql);
    
        $mallClassArray=array();
        foreach ($r1 as $k=>$v){
            $mallClassArray[$v['id']]=$v['Vc_name'];
    
        }
        $checkMallClass=array_flip($mallClassArray);
         
         
        $sql="SELECT * FROM sm_item_class where Status=1 and id in (". $r['Vc_itemIds'].") order by I_order ";
        $r2= $Db->fetch_all_assoc($sql);
    
        $itemClassArray=array();
        foreach ($r2 as $k=>$v){
            $itemClassArray[$v['id']]=$v['Vc_name'];
    
        }
    
        $itemClassIds=array_keys($itemClassArray);
        $checkItemClass=array_flip($itemClassArray);
    
    
        $sql="SELECT id,Vc_name FROM sm_warehouse  where Status=1 and I_shopID=". $r['id']." order by Createtime ";
        $r3= $Db->fetch_all_assoc($sql);
        if(!$r3){
            returnjson(array('err'=>-1,'msg'=>'请先添加仓库信息'));
        }
    
        $wareHouseArray=array();
        foreach ($r3 as $k=>$v){
            $wareHouseArray[$v['id']]=$v['Vc_name'];
    
        }
        $checkWareHouse=array_flip($wareHouseArray);
    
    
        if (! empty ( $_FILES ['file_cp'] ['name'] ))
        {
            $tmp_file = $_FILES ['file_cp'] ['tmp_name'];
            $file_types = explode ( ".", $_FILES ['file_cp'] ['name'] );
            $file_type = $file_types [count ( $file_types ) - 1];
            /*判别是不是.xls文件，判别是不是excel文件*/
            if (strtolower ( $file_type ) != "xls")
            {
                $msg = '不是后缀为xls的Excel文件，请重新上传';
                returnjson(array('err'=>-1,'msg'=>$msg));
    
            }
             
            /*设置上传路径*/
            $savePath = WEBROOT . 'data/upfile/excel/cp/';
            /*以时间来命名上传的文件*/
            $str = date ( 'Ymdhis' );
            $file_name = $str . "." . $file_type;
            /*是否上传成功*/
            if (! copy ( $tmp_file, $savePath . $file_name ))
            {
                 
                $msg = '文件上传失败';
                returnjson(array('err'=>-1,'msg'=>$msg));
            }
            /*
             *对上传的Excel数据进行处理生成编程数据,ExcelToArray类中
             *这里调用执行了类里面的read函数，把Excel转化为数组并返回给$res,再进行数据库写入
             */
            $excel = new ExcelImport();
            $res = $excel->read( $savePath . $file_name ,'utf-8');
            $curfile = $savePath . $file_name;
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
            //                       var_dump($res);
            //                         exit();
             
    
            $checkArr=array();
            /*对生成的数组进行校验*/
            foreach ( $res as $k => $v )
            {
                if ($k != 0)
                {
                    if($k>1){
                        //全部check
                        $tempArr=array();
                        $mallClass = trim($v[0]);
                        $itemClass = trim($v[1]);
                        $item = trim($v[2]);
                        $itemStuff = trim($v[3]);
                        $itemSpecification = trim(strval($v[4]));
                        $itemFactory = trim($v[5]);
                        $warehouse = trim($v[6]);
                        $N_amount = trim($v[7]);
                        $N_weight = trim($v[8]);
                        $N_price = trim($v[9]);
                        $v[10]=0;
                        $v[11]='';
    
                        if(!isset($checkMallClass[$mallClass])){
    
                            $v[10]=-1;
                            $v[11]='第'.$k.'行有误,未经营或不存在该类别：'.$mallClass.';';
                            // returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误,未经营或不存在该类别：'.$mallClass));
                        }
    
    
                        if(!isset($checkItemClass[$itemClass])){
                            $v[10]=-1;
                            $v[11].='第'.$k.'行有误,未经营或不存在该类型：'.$itemClass.';';
                            //returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误,未经营或不存在该类型：'.$itemClass));
                        }else {
    
                            //校验并拿到对应id
    
                            $itemArr = $obj->checkItem($checkItemClass[$itemClass],$item);
                            if(!$itemArr){
                                $v[10]=-1;
                                $v[11].='第'.$k.'行有误,该'.$itemClass.'类型下不存在该品名:'.$item.';';
                                //                             returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误,该'.$itemClass.'类型下不存在该品名:'.$item));
                            }
    
    
                            $itemStuffArr = $obj->checkItemStuff($checkItemClass[$itemClass],$itemStuff);
                             
                            if(!$itemStuffArr){
    
                                $v[10]=-1;
                                $v[11].='第'.$k.'行有误,该'.$itemClass.'类型下不存在该材质:'.$itemStuff.';';
                                //                             returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误,该'.$itemClass.'类型下不存在该材质:'.$itemStuff));
                            }
    
                            $itemSpecificationArr = $obj->checkItemSpecification($checkItemClass[$itemClass],$itemSpecification);
                            //                             var_dump($itemSpecificationArr);
                             
                            if(!$itemSpecificationArr){
    
                                $v[10]=-1;
                                $v[11].='第'.$k.'行有误,该'.$itemClass.'类型下不存在该规格:'.$itemSpecification.';';
                                //                             returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误,该'.$itemClass.'类型下不存在该规格:'.$itemSpecification));
                            }
    
                            $itemFactoryArr = $obj->checkItemFactory($checkItemClass[$itemClass],$itemFactory);
                            if(!$itemFactoryArr){
                                $v[10]=-1;
                                $v[11].='第'.$k.'行有误,该'.$itemClass.'类型下不存在该钢厂:'.$itemFactory.';';
                                //                             returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误,该'.$itemClass.'类型下不存在该钢厂:'.$itemFactory));
                            }
    
    
                        }
                         
    
                        if(!isset($checkWareHouse[$warehouse])){
                            $v[10]=-1;
                            $v[11].='第'.$k.'行有误不存在该仓库：'.$warehouse.';';
                            //                             returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误不存在该仓库：'.$warehouse));
                        }
    
    
                        if(!is_numeric($N_amount)||!is_numeric($N_amount)||!is_numeric($N_amount)){
                            $v[10]=-1;
                            $v[11].='第'.$k.'行有误,件数、重量、单价都应为数字'.';';
                            //                             returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误,件数、重量、单价都应为数字'));
                        }
    
    
                        $tempArr=$v;
                        $checkArr[]=$tempArr;
    
                    }
                     
                }
            }
    
    
            //             var_dump($checkArr);
            returnjson(array('err'=>0,'msg'=>'校验成功！','data'=>$checkArr));
    
            //             echo serialize($checkArr);
            //             if(strstr(serialize($checkArr),'-1')){
            //                 echo "err";
            //             }
            //             exit;
    
            break;
    
        }else {
    
            $msg = '未选择任何文件';
            //             echo showErr($msg);
            returnjson(array('err'=>-1,'msg'=>$msg));
            exit;
            break;
    
        }
    
    
        break;
    case 'importexlstep2':
        /**
         * @author wh
         * 导入产品Excel
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=commodity&w=importexlstep2
         * 输入(post方式提交json数据)：
         * data：Array 校验后的数组数据（json格式）
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         *
         */
    
    
        /**
         *
         * 1.拿到允许商家经营的所有类目及仓库数据，无仓库则返回提示先添加仓库
         * 2.读取上传excel数据，拿到类目及仓库数据进行中文比对，出错返回错误信息
         * 3.将类目及仓库数据转成对应id，插入数据库
         *
         */
        //校验数据
    
//         $I_shopID = $objShop->isBeShop($uid);
    
        $sql="SELECT id,I_type,Vc_mall_classIds,Vc_itemIds FROM sm_shop where Status=1 and I_userID=".$uid;
         
        $r= $Db->fetch_one($sql);
        $I_mallID=$r['I_type'];    //1-自营商城，2-撮合市场
//         $I_shopID=$r['id'];//所属商家id
    
        if(!$r['Vc_mall_classIds']){
            returnjson(array('err'=>-1,'msg'=>'请先选择经营行业'));
        }
    
        if(!$r['Vc_itemIds']){
            returnjson(array('err'=>-1,'msg'=>'请先选择经营类型'));
        }
    
        $sql="SELECT id,Vc_name FROM sm_mall_class where Status=1 and id in (". $r['Vc_mall_classIds'].") ";
        $r1= $Db->fetch_all_assoc($sql);
    
        $mallClassArray=array();
        foreach ($r1 as $k=>$v){
            $mallClassArray[$v['id']]=$v['Vc_name'];
    
        }
        $checkMallClass=array_flip($mallClassArray);
         
         
        $sql="SELECT * FROM sm_item_class where Status=1 and id in (". $r['Vc_itemIds'].") order by I_order ";
        $r2= $Db->fetch_all_assoc($sql);
    
        $itemClassArray=array();
        foreach ($r2 as $k=>$v){
            $itemClassArray[$v['id']]=$v['Vc_name'];
    
        }
    
        $itemClassIds=array_keys($itemClassArray);
        $checkItemClass=array_flip($itemClassArray);
    
    
        $sql="SELECT id,Vc_name FROM sm_warehouse  where Status=1 and I_shopID=". $r['id']." order by Createtime ";
        $r3= $Db->fetch_all_assoc($sql);
        if(!$r3){
            returnjson(array('err'=>-1,'msg'=>'请先添加仓库信息'));
        }
    
        $wareHouseArray=array();
        foreach ($r3 as $k=>$v){
            $wareHouseArray[$v['id']]=$v['Vc_name'];
    
        }
        $checkWareHouse=array_flip($wareHouseArray);
    
        //获取post过来的json数据
        $HTTP_RAW_POST_DATA=file_get_contents('php://input');
        // $HTTP_RAW_POST_DATA='[["\u94a2\u6750","\u5efa\u7b51\u94a2\u6750","\u87ba\u7eb9\u94a2\t","HRB400","\u03a68","\u840d\u94a2","\u946b\u80fd1\u5e93","2","2.00","2000.00",0,""],["\u94a2\u6750","\u70ed\u5377","\u70ed\u8f67\u5f00\u5e73\u677f","Q235B","7.75*1500*C ","\u6b66\u94a2","\u946b\u80fd1\u5e93","15","42.00","2320.00",0,""],["\u94a2\u6750","\u5efa\u7b51\u94a2\u6750","\u87ba\u7eb9\u94a2\t","HRB400","12*9","\u840d\u94a2","\u946b\u80fd1\u5e93","20","20.00","2500.00",0,""],["\u94a2\u6750","\u5efa\u7b51\u94a2\u6750","\u87ba\u7eb9\u94a2\t","HRB400","\u03a68","\u840d\u94a2","\u946b\u80fd1\u5e93","15","42.00","2420.00",0,""],["\u94a2\u6750","\u70ed\u5377","\u666e\u5377","Q235B","5.75*1500*C","\u901a\u94a2","\u946b\u80fd1\u5e93","15","42.00","2420.00",0,""],["\u94a2\u6750","\u5efa\u7b51\u94a2\u6750","\u76d8\u87ba","HPB300","25*9","\u5357\u94a2","\u946b\u80fd1\u5e93","20","20.00","2500.00",0,""],["\u94a2\u6750","\u70ed\u5377","\u4f4e\u5408\u91d1\u5377","Q235B","4.75*1500*C","\u6b66\u94a2","\u946b\u80fd1\u5e93","15","42.00","2000.00",0,""],["\u94a2\u6750","\u5efa\u7b51\u94a2\u6750","\u87ba\u7eb9\u94a2\t","HRB400E","\u03a68","\u6d9f\u94a2","\u946b\u80fd1\u5e93","20","20.00","2500.00",0,""]]';
        if($HTTP_RAW_POST_DATA){
            $res=json_decode($HTTP_RAW_POST_DATA,true);
             
            if(strstr(serialize($res),'-1')){
    
                returnjson(array('err'=>-1,'msg'=>'提交的数据中还存在错误，请确定全部正确后提交！'));
            }
    
    
            /*对数组再次校验进行数据库的写入*/
            foreach ( $res as $k => $v )
            {
                if ($k != 0)
                {
                    if($k>1){
                        //全部check
                        $mallClass = trim($v[0]);
                        $itemClass = trim($v[1]);
                        $item = trim($v[2]);
                        $itemStuff = trim($v[3]);
                        $itemSpecification = trim($v[4]);
                        $itemFactory = trim($v[5]);
                        $warehouse = trim($v[6]);
                        $N_amount = trim($v[7]);
                        $N_weight = trim($v[8]);
                        $N_price = trim($v[9]);
                         
                        if(!isset($checkMallClass[$mallClass])){
                            returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误,未经营或不存在该类别：'.$mallClass));
                        }
    
                        if(!isset($checkItemClass[$itemClass])){
                            returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误,未经营或不存在该类型：'.$itemClass));
                        }
    
                        if(!isset($checkWareHouse[$warehouse])){
                            returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误不存在该仓库：'.$warehouse));
                        }
    
    
                        if(!is_numeric($N_amount)||!is_numeric($N_amount)||!is_numeric($N_amount)){
    
                            returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误,件数、重量、单价都应为数字'));
                            //return false;
                        }
                        //校验并拿到对应id
    
                        $itemArr = $obj->checkItem($checkItemClass[$itemClass],$item);
                        if(!$itemArr){
                            returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误,该'.$itemClass.'类型下不存在该品名:'.$item));
                        }
    
    
                        $itemStuffArr = $obj->checkItemStuff($checkItemClass[$itemClass],$itemStuff);
                        if(!$itemStuffArr){
                            returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误,该'.$itemClass.'类型下不存在该材质:'.$itemStuff));
                        }
    
                        $itemSpecificationArr = $obj->checkItemSpecification($checkItemClass[$itemClass],$itemSpecification);
                        if(!$itemSpecificationArr){
                            returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误,该'.$itemClass.'类型下不存在该规格:'.$itemSpecification));
                        }
    
                        $itemFactoryArr = $obj->checkItemFactory($checkItemClass[$itemClass],$itemFactory);
                        if(!$itemFactoryArr){
                            returnjson(array('err'=>-1,'msg'=>'第'.$k.'行有误,该'.$itemClass.'类型下不存在该钢厂:'.$itemFactory));
                        }
    
                        $warehouseArr = $obj->getWareHouseInfo($checkWareHouse[$warehouse]);
                         
                        $I_provinceID=$warehouseArr['I_provinceID'];
                        $I_cityID=$warehouseArr['I_cityID'];
                        $I_districtID=$warehouseArr['I_districtID'];
    
                        $da=array();
                        $da['I_shopID'] = $I_shopID;
                        $da['I_mallID']=$I_mallID;
                        $da['I_mallClassID']=$checkMallClass[$mallClass];
                        $da['I_itemClassID']=$checkItemClass[$itemClass];
                        $da['I_itemID']=$itemArr['id'];
                        $da['I_stuffID']=$itemStuffArr['id'];
                        $da['I_specificationID']=$itemSpecificationArr['id'];
                        $da['I_factoryID']=$itemFactoryArr['id'];
                        $da['I_warehouseID']=$checkWareHouse[$warehouse];
                        $da['I_provinceID']=$I_provinceID;
                        $da['I_cityID']=$I_cityID;
                        $da['I_districtID']=$I_districtID;
                        $da['N_amount']=$N_amount;
                        $da['N_weight']=$N_weight;
                        $da['N_price']=$N_price;
                        $da['Createtime@'] = 'now()';
                         
                        $rs=$obj->add($da);
    
                    }
                     
                }
            }
    
            returnjson(array('err'=>0,'msg'=>'导入成功！'));
    
            break;
    
        }else {
    
            $msg = '未提交任何数据';
            //             echo showErr($msg);
            returnjson(array('err'=>-1,'msg'=>$msg));
            exit;
            break;
    
        }
    
    
        break;
    
    
                            

                            
                            
                            
                            
                            
                            
                    
                    
                    
                    

}

?>
