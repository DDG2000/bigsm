<?php
// if(!defined('WEBROOT'))exit;

$w=$FL->requeststr('w',1,0,'w',1,1);


$m.='_'.$w;
require_once(WEBROOTINCCLASS.'ShopSlides.php');
require_once(WEBROOTINCCLASS.'ShopResource.php');
require_once(WEBROOTINCCLASS.'Order.php');
$objShopSlides = new ShopSlides();
$objres = new ShopResource();
$objOrder = new Order();
switch($w){
    
    
    case 'appraisal':
        /**
         * @author wh
         * 客户评价接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=shopcredit&w=appraisal
         * 输入：
         * id: int 公司id(必传)
    
         * 模板输出：
         * 
         *
         */
    
    
        $id=$FLib->requestint('id',0,'公司id',1);
        //         if(!$id){returnjson(array('err'=>-1,'msg'=>'参数有误'));}
        if(!$id){returnErrJson("参数有误");}
    
        //公司信息
        $p['shopinfo']=$objShop->getInfo($id);
        //-经营范围
        $sql="select Vc_name from sm_item_class WHERE Status=1 and id in ({$p['shopinfo']['Vc_itemIds']})";
        $p['shopinfo']['itemClassArr']=$Db->fetch_all_assoc($sql);
       
        //轮播图信息获取轮播图列表
        $p['shopslides']=$objShopSlides->getDataList($id);
    
        //客户评价列表
        $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):10;
        
        if(!is_numeric($page) || !is_numeric($psize)) {
            returnErrJson('分页参数不合法');
        }
        $sqlw=" b.I_shopID={$id}";
        $order=" a.Createtime desc ";
        $da = $objOrder->getShopAppraiseByPage($page, $psize, $sqlw, $order);
       // returnjson($da);
           $sumQuality=0;
           $sumTimely=0;
           $sumServer=0;
           $sumInvoice=0;
        if($da['count']){
         
         foreach ($da['data'] as &$v){
             //将获取的评价字符串转化为数组
             $score=explode(',',$v['Vc_score']);
             $v['quality']= $score['0'];
             $v['timely']= $score['1'];
             $v['server']= $score['2'];
             $v['invoice']= $score['3'];
             //将图片地址字符串转化为数组
             if($v['T_images']){
         
                 $v['T_images']=explode(',',$v['T_images']);
             }else{
                 $v['T_images']=null;
             }
             $sumQuality=$sumQuality+$v['quality'];
             $sumTimely=$sumTimely+$v['timely'];
             $sumServer=$sumServer+$v['server'];
             $sumInvoice=$sumInvoice+$v['invoice'];
         
         }
         $sumQuality=round($sumQuality/$da['count'],1);//保留1位小数
         $sumTimely=round($sumTimely/$da['count'],1);
         $sumServer=round($sumServer/$da['count'],1);
         $sumInvoice=round($sumInvoice/$da['count'],1);
         
         
     }
        
         $p['sumQuality']= $sumQuality;
         $p['sumTimely']= $sumTimely;
         $p['sumServer']= $sumServer;
         $p['sumInvoice']= $sumInvoice;
        
        $page = $da['page'];
        $count = $da['count'];
        $pcount = $da['pcount'];
        $p['data']=$da;
         
        $p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=shop&m=shopcredit&w=appraisal");
//         returnjson($p);
    
        $p['id'] = $id;
        $p['navActive']='appraisal';
        break;
    case 'certify':
        /**
         * @author wh
         * 认证信息接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=shopcredit&w=certify
         * 输入：
         * id: int 公司id(必传)
    
    
         * 模板输出：
         *
         * shopinfo：array 公司信息
         * shopslides：array 轮播图信息
         *
         */
    
    
        $id=$FLib->requestint('id',0,'公司id',1);
        //         if(!$id){returnjson(array('err'=>-1,'msg'=>'参数有误'));}
        if(!$id){returnErrJson("参数有误");}
    
    
        //公司信息
        $p['shopinfo']=$objShop->getInfo($id);
        //-经营范围
        $sql="select Vc_name from sm_item_class WHERE Status=1 and id in ({$p['shopinfo']['Vc_itemIds']})";
        $p['shopinfo']['itemClassArr']=$Db->fetch_all_assoc($sql);
        //      dump($p['shopinfo']);
        //      exit;
        //轮播图信息获取轮播图列表
        $p['shopslides']=$objShopSlides->getDataList($id);
    
        $p['id'] = $id;
        $p['navActive']='certify';
        break;
    case 'contact':
        /**
         * @author wh
         * 联系方式接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=shopcredit&w=contact
         * 输入：
         * id: int 公司id(必传)
    
    
         * 模板输出：
         *
         * shopinfo：array 公司信息
         * shopslides：array 轮播图信息
         *
         */
    
    
        $id=$FLib->requestint('id',0,'公司id',1);
        //         if(!$id){returnjson(array('err'=>-1,'msg'=>'参数有误'));}
        if(!$id){returnErrJson("参数有误");}
    
    
        //公司信息
        $p['shopinfo']=$objShop->getInfo($id);
        //-经营范围
        $sql="select Vc_name from sm_item_class WHERE Status=1 and id in ({$p['shopinfo']['Vc_itemIds']})";
        $p['shopinfo']['itemClassArr']=$Db->fetch_all_assoc($sql);
        //      dump($p['shopinfo']);
        //      exit;
        //轮播图信息获取轮播图列表
        $p['shopslides']=$objShopSlides->getDataList($id);
    
        $p['id'] = $id;
        $p['navActive']='contact';
        break;
    
    case 'downloadres':
        /**
         * @author wh
         * 下载资源单次数统计接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=shopcredit&w=downloadres
         * 输入：
         * id: int 资源单id(必传)
         * 输出：
         * code:int 结果状态 500失败 200成功
         * msg: 提示信息
         * 
         */
        
        $id=$FLib->requestint('id',0,'资源单id',1);
        if(!$id){returnErrJson("参数有误");}
        
        $sqlw="id={$id}";
        $daArr['I_downloadtimes@']='I_downloadtimes+1';
        $rs = $objres->update($daArr, $sqlw);
        if($rs){
            returnSucJson('',"更新成功");
        }else{
            returnErrJson("更新失败");
        }
        //$r1 = $Db->autoExecute('sm_shop_resource', array('I_bids@'=>'I_bids+1'),'update',$sqlw);
        
        
        
        break;
    case 'resource':
        /**
         * @author wh
         * 商铺资源单接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=shopcredit&w=resource
         * 输入：
         * id: int 公司id(必传)
          
        
         * 模板输出：
         *
         * shopinfo：array 公司信息
         * shopslides：array 轮播图信息
         * shopres:array 资源单信息
         */
       
        $id=$FLib->requestint('id',0,'公司id',1);
//         if(!$id){returnjson(array('err'=>-1,'msg'=>'参数有误'));}
        if(!$id){returnErrJson("参数有误");}
        
        
        //公司信息
        $p['shopinfo']=$objShop->getInfo($id);
        //-经营范围
        $sql="select Vc_name from sm_item_class WHERE Status=1 and id in ({$p['shopinfo']['Vc_itemIds']})";
        $p['shopinfo']['itemClassArr']=$Db->fetch_all_assoc($sql);
       
        //轮播图信息获取轮播图列表
        $p['shopslides']=$objShopSlides->getDataList($id);
        
        
        //资源单信息
        $shopres=$objres->getInfo($id);
//         $sql="SELECT * FROM sm_item where Status=1 and id in (". $r['Vc_itemIds'].") ";
//         $itemArr= $Db->fetch_all_assoc($sql);
        $sql="SELECT Vc_name FROM sm_item_class where Status=1 and id in (". $shopres['Vc_itemClassIds'].") order by I_order ";
        $shopres['itemClassArr']= $Db->fetch_all_assoc($sql);
        $p['shopres']=$shopres;
        $p['id'] = $id;
        $p['navActive']='resource';
        break;    
    case 'desc':
        /**
         * @author wh
         * 商铺公司简介接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=shopcredit&w=desc
         * 输入：
         * id: int 公司id(必传)
          
        
         * 模板输出：
         *
         * shopinfo：array 公司信息(包含公司简介)
         * shopslides：array 轮播图信息
         *
         */
        
  
        $id=$FLib->requestint('id',0,'公司id',1);
//         if(!$id){returnjson(array('err'=>-1,'msg'=>'参数有误'));}
        if(!$id){returnErrJson("参数有误");}
        
        
        //公司信息
        $p['shopinfo']=$objShop->getInfo($id);
        //-经营范围
        $sql="select Vc_name from sm_item_class WHERE Status=1 and id in ({$p['shopinfo']['Vc_itemIds']})";
        $p['shopinfo']['itemClassArr']=$Db->fetch_all_assoc($sql);
        //      dump($p['shopinfo']);
        //      exit;
        //轮播图信息获取轮播图列表
        $p['shopslides']=$objShopSlides->getDataList($id);
        $p['id'] = $id;
        $p['navActive']='desc';
        
        break;    

case 'search':
  
    /**
     * @author wh
     * 商铺首页综合搜索产品列表接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=shopcredit&w=search
     * 输入：
     * id: int  公司id(必传)
     * 筛选条件(可选——可组合)
     * page:int 当前的页数,默认为1
     * psize: int 数据分页量,默认为20
     * 
     * itemClassID: int 分类id
     * itemID: int 品名id
     * stuffID: int 材质id
     * specificationID: int 规格id
     * factoryID: int 钢厂id
     *
     * itemname:  string 品名
     * specificationname:  string 规格
     * stuffname:  string 材质
     * factoryname:  string 钢厂
     *
     * order_type: string 排序类型((默认id升序)/newest(最新：时间倒序)/price_asc(价格升序)/price_desc(价格降序))
     * 输出：
     * code:int 结果状态 500失败 200成功
     * msg: 提示信息
     * 以下仅在code为200时会返回
     * data：
     * searchData：array 已点导航数组
     * page: int 当前页数
     * count: string 数据总量
     * pcount: int 总页数
     * data:数据
     *
     */
    //公司信息、产品信息、轮播图信息
    $id=$FLib->requestint('id',0,'公司id',1);
//     if(!$id){returnjson(array('err'=>-1,'msg'=>'参数有误'));}
    if(!$id){returnErrJson("参数有误");}
     
    
    //数据列表
    
    //导航
    $I_itemClassID=$FLib->requestint('itemClassID',0,'分类id',1);
    $I_itemID=$FLib->requestint('itemID',0,'品名id',1);
    $I_stuffID=$FLib->requestint('stuffID',0,'材质id',1);
    $I_specificationID=$FLib->requestint('specificationID',0,'规格id',1);
    $I_factoryID=$FLib->requestint('factoryID',0,'钢厂id',1);
    //排序
    $order_type= $FL->requeststr('order_type',1,50,'排序类型');
    //模糊查询
    $itemname= $FL->requeststr('itemname',1,50,'品名');
    $specificationname= $FL->requeststr('specificationname',1,50,'规格');
    $stuffname= $FL->requeststr('stuffname',1,50,'材质');
    $factoryname= $FL->requeststr('factoryname',1,50,'钢厂');
//     $N_price_max = isset($_REQUEST['N_price_max'])?floatval($_REQUEST['N_price_max']):'';
//     $N_price_min = isset($_REQUEST['N_price_min'])?floatval($_REQUEST['N_price_min']):'';
    //分页
    $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
    $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):20;
    
    $I_mall_classID=$FLib->requestint('I_mall_classID',1,'默认钢材市场',1);//行业
        
    $ext_condition=" Status=1 AND I_mallClassID={$I_mall_classID} AND I_publish=1 AND I_shopID={$id}";
   
    $searchArr=array(); //导航点击数组
    if($I_itemClassID)
    {
    $ext_condition.=" and I_itemClassID = {$I_itemClassID} ";
    $searchArr['itemClassID']=$I_itemClassID;
     }

    if($I_itemID){
    
    $ext_condition.=" and I_itemID = {$I_itemID} ";
    $searchArr['itemID']=$I_itemID;
    }
    if($I_stuffID){
    
    $ext_condition.=" and I_stuffID = {$I_stuffID} ";
    $searchArr['stuffID']=$I_stuffID;
    
    }
    if($I_specificationID){
    
    $ext_condition.=" and I_specificationID = {$I_specificationID} ";
    $searchArr['specificationID']=$I_specificationID;
     
    }
    if($I_factoryID){
    
    $ext_condition.=" and I_factoryID = {$I_factoryID} ";
    $searchArr['factoryID']=$I_factoryID;
    
    }

    $wheresql=" where 1=1 ";
    
    if($itemname!=''){
        $wheresql.=" and a.Vc_name like '%".$itemname."%'";
    }
    if($specificationname!=''){
        $wheresql.=" and b.Vc_name like '%".$specificationname."%'";
    }
    if($stuffname!=''){
    $wheresql.=" and c.Vc_name like '%".$stuffname."%'";
    }
    if($factoryname!=''){
        $wheresql.=" and c.Vc_name like '%".$factoryname."%'";
    }

    //排序
    $order = "";
    if($order_type=='')/*默认*/
    $order= "t.id asc ";
    elseif($order_type=='newest')/*最新*/
    $order= " t.Createtime desc  ";
    elseif($order_type=='price_asc')/*价格升*/
    $order= " N_price asc  ";
    elseif($order_type=='price_desc')/*价格降*/
    $order= " N_price desc  ";
    
//     $order=" t.Createtime desc";
    
   //测试用
        $sql="SELECT t.* ,a.Vc_name as itemname,b.Vc_name as specificationname,c.Vc_name as stuffname,d.Vc_name as factoryname,
        e.Vc_province as provincename,f.Vc_city as cityname,g.Vc_name as warehouse
        from (SELECT id,Createtime,N_amount,N_weight,N_price,I_itemID,I_specificationID,I_stuffID,I_factoryID,I_provinceID,I_cityID,I_warehouseID,I_shopID FROM sm_commodity_steel
          WHERE I_mallClassID=1 AND Status=1 AND I_publish=1 AND I_shopID=1 ) t
          LEFT JOIN sm_item a on t.I_itemID=a.id
          LEFT JOIN sm_item_specification b on t.I_specificationID=b.id
          LEFT JOIN sm_item_stuff c on t.I_stuffID=c.id
          LEFT JOIN sm_item_factory d on t.I_factoryID=d.id
          LEFT JOIN site_province e on t.I_provinceID=e.ID
          LEFT JOIN site_city f on t.I_cityID=f.ID
		  LEFT JOIN sm_warehouse g on t.I_warehouseID=g.ID
            ORDER BY t.Createtime desc";
    
    $da= $objShop->getHomeDataListByPage($page, $psize, $ext_condition, $wheresql, $order);
    $da['searchData']=$searchArr;
    returnSucJson($da);
    
    
 
    break;    
    
case 'index':
    /**
     * @author wh
     * 商铺首页接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=shopcredit&w=index
     * 输入：
     * id: int 公司id(必传)
     

     * 模板输出：
     * 
     * shopinfo：array 公司信息
     * shopslides：array 轮播图信息
     * itemClassList：array 分类导航
     * itemList：array 品名导航
     * stuffList：array 材质导航
     * specificationList：array 规格导航
     * factoryList：array 钢厂导航
     * 
     * page: int 当前页数
     * count: string 数据总量
     * pcount: int 总页数
     * data:数据
     *
     */   

    //公司信息、产品信息、轮播图信息
    $id=$FLib->requestint('id',0,'公司id',1);
//     if(!$id){returnjson(array('err'=>-1,'msg'=>'参数有误'));}
    if(!$id){returnErrJson("参数有误");}
    
    $p['id']=$id;
    //公司信息
     $p['shopinfo']=$objShop->getInfo($id);
        //-经营范围
     $sql="select Vc_name from sm_item_class WHERE Status=1 and id in ({$p['shopinfo']['Vc_itemIds']})";
     $p['shopinfo']['itemClassArr']=$Db->fetch_all_assoc($sql);
//      dump($p['shopinfo']);
//      exit;
    //轮播图信息获取轮播图列表
    $p['shopslides']=$objShopSlides->getDataList($id);
    
    
  /**产品信息**/
    //分类导航
    $I_mall_classID=$FLib->requestint('I_mall_classID',1,'默认钢材市场',1);//行业
    $sql="SELECT id,Vc_name  from sm_item_class a,
        (SELECT I_itemClassID  from sm_commodity_steel WHERE Status=1 and  I_mallClassID={$I_mall_classID} and I_publish=1 GROUP BY I_itemClassID) b 
        WHERE a.id=b.I_itemClassID";
    
    $p['itemClassList']= $Db->fetch_all_assoc($sql);
    //品名导航
    $sql="SELECT id,Vc_name  from sm_item a,
        (SELECT I_itemID  from sm_commodity_steel WHERE Status=1 and  I_mallClassID=1 and I_publish=1 GROUP BY I_itemID) b 
        WHERE a.id=b.I_itemID";
    
    $p['itemList']= $Db->fetch_all_assoc($sql);
    
    //材质导航
    $sql="SELECT id,Vc_name  from sm_item_stuff a,
        (SELECT I_stuffID  from sm_commodity_steel WHERE Status=1 and  I_mallClassID=1 and I_publish=1 GROUP BY I_stuffID) b 
        WHERE a.id=b.I_stuffID";
    
    $p['stuffList']= $Db->fetch_all_assoc($sql);
    
    //规格导航
    $sql="SELECT id,Vc_name  from sm_item_specification a,
        (SELECT I_specificationID  from sm_commodity_steel WHERE Status=1 and  I_mallClassID=1 and I_publish=1 GROUP BY I_specificationID) b 
        WHERE a.id=b.I_specificationID";
    
    $p['specificationList']= $Db->fetch_all_assoc($sql);
    
    
    //钢厂导航
    
    $sql="SELECT id,Vc_name  from sm_item_factory a,
        (SELECT I_factoryID  from sm_commodity_steel WHERE Status=1 and  I_mallClassID=1 and I_publish=1 GROUP BY I_factoryID) b 
        WHERE a.id=b.I_factoryID";
    
    $p['factoryList']= $Db->fetch_all_assoc($sql);
    
   
    
    //数据列表
  
    
    $ext_condition=" Status=1 AND I_mallClassID={$I_mall_classID} AND I_publish=1 AND I_shopID={$id}";
    
  
    
    $wheresql=" where 1=1 ";
    
   
    
    //排序
    $order = " t.Createtime desc ";
   
   //测试用
    $sql="SELECT t.* ,a.Vc_name as itemname,b.Vc_name as specificationname,c.Vc_name as stuffname,d.Vc_name as factoryname,
        	e.Vc_province as provincename,f.Vc_city as cityname,g.Vc_name as warehouse
          from (SELECT id,Createtime,N_amount,N_weight,N_price,I_itemID,I_specificationID,I_stuffID,I_factoryID,I_provinceID,I_cityID,I_warehouseID,I_shopID FROM sm_commodity_steel
          WHERE I_mallClassID=1 AND Status=1 AND I_publish=1 AND I_shopID=1 ) t 
          LEFT JOIN sm_item a on t.I_itemID=a.id 
          LEFT JOIN sm_item_specification b on t.I_specificationID=b.id
          LEFT JOIN sm_item_stuff c on t.I_stuffID=c.id 
          LEFT JOIN sm_item_factory d on t.I_factoryID=d.id 
          LEFT JOIN site_province e on t.I_provinceID=e.ID 
          LEFT JOIN site_city f on t.I_cityID=f.ID 
		  LEFT JOIN sm_warehouse g on t.I_warehouseID=g.ID 
            ORDER BY t.Createtime desc";
    
    
    $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
    $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):20;
    
    
    $da= $objShop->getHomeDataListByPage($page, $psize, $ext_condition, $wheresql, $order);
//     returnjson($da);
//     exit;
  
    
    //商铺数据列表
    $page = $da['page'];
    $count = $da['count'];
    $pcount = $da['pcount'];
    $p['data']=$da;
     
    $p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=shop&m=shopcredit&w=index");
    $p['navActive']='index';
    
    
    
    break;
case 'citylist':
        /**
         * @author wh
         * 城市地区搜索列表接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=shopcredit&w=citylist
         * 输入：
         * provinceID: int 省id
         * 输出：
         * code:int 结果状态 500失败 200成功
         * msg: 提示信息
         * 以下仅在code为200时会返回
         * data：
         * citylist: array 城市列表
         *
         * */
       
    
        $provinceid=$FLib->requestint('provinceID',0,'省id',1);
    
        if(!$provinceid){
            returnjson(array('err'=>-1,'msg'=>'参数有误'));
            returnErrJson('参数不合法');
        }
    
        $sql="SELECT ID,Vc_city  from site_city a,
        (SELECT I_cityID  from sm_shop WHERE I_provinceID={$provinceid}  and Status=1 GROUP BY I_cityID) b
        WHERE a.ID=b.I_cityID";
        $data['citylist']= $Db->fetch_all_assoc($sql);
    
        returnSucJson($data);    

case 'searchlist':
        /**
         * @author wh
         * 综合搜索商铺接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=shopcredit&w=searchlist
         * 输入：
         * 筛选条件(可选——可组合)
         * page:int 当前的页数,默认为1
         * psize: int 数据分页量,默认为20
         * itemClassID: int 分类id
         * provinceID: int 省/直辖市id
         * cityID：int 城市id
         * 
         * skey:  string 商品名称
         * order_type: string 排序类型((默认开店时间升序)/amount_asc(成交量升)/amount_desc(成交量降)/money_asc(成交金额升)/money_desc(成交金额序))
        
         * 输出：
         * code:int 结果状态 500失败 200成功
         * msg: 提示信息
         * 以下仅在code为200时会返回
         * data：
         * searchData：array 已点导航数组
         * urlInfo：string 搜索链接
         * page: int 当前页数
         * count: string 数据总量
         * pcount: int 总页数
         * data:数据
         *
         */
       
       
            
            $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
            $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):20;
            
            $UrlInfo = "&page=" . urlencode($page) ."&psize=" . $psize;
            
            $I_itemClassID=$FLib->requestint('itemClassID',0,'分类id',1);
            $I_provinceID=$FLib->requestint('provinceID',0,'省/直辖市id',1);
            $I_cityID=$FLib->requestint('cityID',0,'城市id',1);
           
        
            $skey= $FL->requeststr('skey',1,50,'关键词');
            $order_type= $FL->requeststr('order_type',1,50,'排序类型');
            
            if(!is_numeric($page) || !is_numeric($psize)) {
                //                 returnjson(array('err'=>-1,'msg'=>'数据不合法'));
                returnErrJson('分页参数不合法');
            
            }
            
            $wheresql=' and 1=1  ';
            
            //模糊查询搜索条件
            
            if($skey!=''){
                $wheresql.=" and a.Vc_name like '%".$skey."%'";
            }
            //导航点击搜索条件
            $searchArr=array();
            if($I_itemClassID){
                $wheresql.=" and a.Vc_itemIds like '%".$I_itemClassID."%'";
                $searchArr['itemClassID']=$I_itemClassID;
                $UrlInfo.="&itemClassID=" . urlencode($I_itemClassID);
            }
            
            if($I_provinceID){
                $wheresql.=" and a.I_provinceID = {$I_provinceID}";
                $searchArr['provinceID']=$I_provinceID;
                $UrlInfo.="&provinceID=" . urlencode($I_provinceID);
            }
            
            if($I_cityID){
                $wheresql.=" and a.I_cityID = {$I_cityID}";
                $searchArr['cityID']=$I_cityID;
                $UrlInfo.="&cityID=" . urlencode($I_cityID);
            }
            
            
            if($order_type){
                $searchArr['order_type']=$order_type;
                $UrlInfo.="&order_type=" . urlencode($order_type);
            }
            //排序
            $order = "";
            if($order_type=='')//默认开店时间
                $order= "a.Dt_open asc ";
            elseif($order_type=='amount_asc')/*成交量升*/
            $order= " a.N_amount asc ";
            elseif($order_type=='amount_desc')/*成交量降*/
            $order= " a.N_amount desc ";
            elseif($order_type=='money_asc')/*交易量升*/
            $order= " a.N_money asc  ";
            elseif($order_type=='money_desc')/*交易量降*/
            $order= " a.N_money desc  ";
            
            $da= $objShop->getShopDataListByPage($page, $psize, $wheresql, $order);
            
            foreach ($da['data'] as &$v){
                
                $sql="select Vc_name from sm_item_class WHERE Status=1 and id in ({$v['Vc_itemIds']})";
                $v['itemClassArr']=$Db->fetch_all_assoc($sql);
            }
            
//             dump($da);
//             exit;
            $da['searchData']=$searchArr;//点击导航数据
            $da['urlInfo']=$UrlInfo;//导航数据
            returnSucJson($da);
            
     
        break;
case 'list':
    
    
        /**
         * @author wh
         * 商铺列表接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=shopcredit&w=list
         * 输入：
         
         * 模板输出：
         * itemClassList：array 分类导航
         * areaList：array 地区导航
         * page: int 当前页数
         * count: string 数据总量
         * pcount: int 总页数
         * data:数据
         *
         */
       
       
            
            $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
            $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):20;
            
            if(!is_numeric($page) || !is_numeric($psize)) {
                //                 returnjson(array('err'=>-1,'msg'=>'数据不合法'));
                returnErrJson('分页参数不合法');
            
            }
            
            //模糊查询搜索条件
            $wheresql=' and 1=1  ';
            
          
            //排序
            $order = "a.Dt_open asc";
           
           
            
            $da= $objShop->getShopDataListByPage($page, $psize, $wheresql, $order);
            
            foreach ($da['data'] as &$v){
                
                $sql="select Vc_name from sm_item_class WHERE Status=1 and id in ({$v['Vc_itemIds']})";
                $v['itemClassArr']=$Db->fetch_all_assoc($sql);
            }
            

            //分类导航
            $I_mall_classID=$FLib->requestint('I_mall_classID',1,'默认钢材市场',1);//行业
            
            
            $p['itemClassList']= CacheManager::getCache(CACHE_ITEM_CLASS) ;
            if (!$p['itemClassList']){
                require_once (WEBROOTINCCLASS . 'ItemClass.php');
                $itemclass=new ItemClass();
                $dArray=$itemclass->getArrayById($I_mall_classID);
                $daArr = CacheManager::saveCache(CACHE_ITEM_CLASS, $dArray) ;
                $p['itemClassList']=$dArray;
            }
            
            //地区导航
            $sql="SELECT ID,Vc_province  from site_province a,
                (SELECT I_provinceID  from sm_shop  GROUP BY I_provinceID) b
                 WHERE a.ID=b.I_provinceID";
            $p['areaList']= $Db->fetch_all_assoc($sql);
            
            //点击导航数据
         //   $p['searchData']=$searchArr;
            
            //商铺数据列表
            $page = $da['page'];
            $count = $da['count'];
            $pcount = $da['pcount'];
            $p['data']=$da;
         
            $p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=shop&m=shopcredit&w=searchlist");
            
     
        break;
    



}

?>
