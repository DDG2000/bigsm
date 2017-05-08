<?php
if(!isset($m))exit;

// if(IS_DEBUG){
//     $uid=1;
// }
if($m==''){$m='home';}

//目录访问限制
if(!in_array($m,array('home','account','certify_action','commodity','concentrated','logo_action','message','resource','warehouse','order','slides','requirecommodity','shopcredit')))exit;


//登录限制
if(!$lg){
    if(!in_array($m,array('shopcredit'))){
        if(!$uid){
            loginouttime();
        }
    }
}

require_once(WEBROOTINCCLASS.'Commodity.php');
require_once(WEBROOTINC.'ExcelOut.php');
require_once(WEBROOTINC.'ExcelImport.php');
require_once(WEBROOTINCCLASS.'Order.php');
require_once(WEBROOTINCCLASS.'Shop.php');
require_once(WEBROOTINCCLASS.'OrderCommodity.php');
require_once(WEBROOTINC.'File.class.php');
$Fc = new FileClass();
$obj = new Commodity();
$excelobj=new ExcelOut();
$objOrder = new Order();
$objShop = new Shop();
$objOrderCommodity = new OrderCommodity();


//认证卖家可访问菜单
if($uid){
    if(in_array($m,array('home','commodity','concentrated','resource','warehouse','order','slides','requirecommodity'))){
        $I_shopID = $objShop->isBeShop($uid);//$I_shopID为全局参数
    }
    
}




//放在引入的类后
if($m!='index'){
    if(file_exists("{$act}/{$m}.php")){
        require("{$act}/{$m}.php");
    }
}


switch($m){
    
    
    case 'home':
        /**
         * @author wh
         * 卖家中心首页信息接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=home
         * 输入(无)：需登录后访问
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * message: 提示信息
         * 以下仅在err为0时会返回
         * base: array   卖家基本信息包括公司logo，公司名，开店时间等
         * ordercount: string 待处理订单数
         * publishedcount: string 已发布的产品数
         * orderlist: array 待处理的订单默认显示3条
         *
         * */
        
        
       // $I_shopID = $objShop->isBeShop($uid);
       
        //商家信息
        $sql="SELECT id,I_type,Vc_logo_pic,Vc_name,N_amount,N_money,Dt_open,I_cert_status FROM sm_shop where Status=1 and id=".$I_shopID;
        $r= $Db->fetch_one($sql);

        //待处理订单数
        $c=$objOrder->getOrderingCount();
        //已发布产品数
        $c2=$obj->getPublishedCount();
        //待处理订单列表
        $page=1;
        $psize=3;

        $sqlw='Status=1 and I_shopID='.$I_shopID;
        $order='Createtime desc';
        $r2=$objOrder->getShopOrderListPages($page, $psize, $sqlw, $order);
        foreach ($r2['data'] as $k=> &$v ){
        
            $data=$objOrderCommodity->getCommodityList($v['id']);
        
            $v['commoditylist']=$data;
        }
        
        $root['err']=0;
        $root['base']=$r;
        $root['ordercount']=$c;
        $root['publishedcount']=$c2;
        $root['orderlist']=$r2['data'];//没有则为空数组
        
        $p['data']=$root;
//         returnjson($root);
        
 
        break;
        default:
            
            //index首页
            // 	    exit;
            // 		header('location: '.'./index.php?act=invest&m=list');
            // 		die;
            
            

    
   
  
  
}

?>