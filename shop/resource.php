<?php
// if(!defined('WEBROOT'))exit;

$w=$FL->requeststr('w',1,0,'w',1,1);

$m.='_'.$w;
require_once(WEBROOTINCCLASS.'ShopResource.php');
// require(WEBROOTINC.'File.class.php');
// $Fc = new FileClass();
// $obj = new Shop();
$objres = new ShopResource();
switch($w){
    /**
     * 资源单模板地址
     * http://www.bigsm.com/data/resource/shop/resfiletemp.xls
     */
    
case 'test':
    //文件上传测试用例
    // http://www.bigsm.com/index.php?act=shop&m=resource&w=test
   
    break;    
case 'del':
 
    /**
     * @author wh
     * 删除资源单接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=resource&w=del
     * 输入：需登录后访问
     *
     * id：int  资源单id
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     *
     * */
    
//     $a=WEBROOT.'upload/shop/resfile/20160428/test.txt';
  
    
    $id=$FLib->requestint('id',0,'资源单id',1);
    //删除资源单和文件
    
    if(!$id){
     returnjson(array('err'=>-1,'msg'=>'参数不合法！'));
    
    }
    $resfile=$objres->getResFile($id);
    
    $resfile=WEBROOT.$resfile;
    $da['Status']=0;
    $sqlw='id='.$id;
    $r=$objres->update($da, $sqlw);
    if($r){

        //读取完后就删除当前文件
        if(is_file($resfile)){
            chmod($resfile,0777);
            @unlink($resfile);
        
        }
        
        returnjson(array('err'=>0,'msg'=>'删除成功！'));
    }else{
        returnjson(array('err'=>-1,'msg'=>'删除失败！'));
    }
    
   
    break;    
case 'upload':
    /**
     * @author wh
     * 上传资源文件接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=resource&w=upload
     * 输入：需登录后访问
     *
     * resfile：file  资源文件
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     *
     * */
//     var_dump($_FILES);
//     exit;
    
    
    if(!isset($_FILES['resfile'])){
        returnjson(array('err'=>-1,'msg'=>'请选择资源文件'));
      
    }
    
    $p='resfile';//上传文件变量名
    $path = '/upload/shop/resfile/';
    $T='txt|doc|xls|xlsx';
    $Z=3072000;//文件大小不能超过3M
    $r=$Fc->uplodefile($p,$path,$T,$Z);
        
    if($r=='no_type'){
        returnjson(array('err'=>-1,'msg'=>'文件类型不正确！'));
    }elseif ($r=='no_size'){
        returnjson(array('err'=>-1,'msg'=>'文件大小不能超过3M！'));
    }elseif ($r===false){
        returnjson(array('err'=>-1,'msg'=>'资源文件上传失败！'));
    }
        
    echo $r;//文件路径--存入数据库
     exit;   

    break;  
      
case 'list_add':
       /**
         * @author wh
         * 资源单添加列表接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=resource&w=list_add
         * 输入：需登录后访问
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * 以下仅在err为0时会返回
         * info: array  基本信息
         * itemClassList: array  已勾选分类列表
         * itemList: array  已勾选品名列表
         * 
         * 数据为null则到添加页面
         * */
//       $I_shopID = $objShop->isBeShop($uid);
   
    $r=$objres->getInfo($I_shopID);
    if(!$r){
        $root['err']=0;
        $root['info']=null;
        $root['itemClassList']=null;
        $root['itemList']=null;
        $root['msg']='资源单为空，快去添加吧~';
        returnjson($root);
    }
    
    
    $sql="SELECT * FROM sm_item where Status=1 and id in (". $r['Vc_itemIds'].") ";
    $r1= $Db->fetch_all_assoc($sql);
    
    $sql="SELECT * FROM sm_item_class where Status=1 and id in (". $r['Vc_itemClassIds'].") order by I_order ";
    $r2= $Db->fetch_all_assoc($sql);
    
    $root['err']=0;
    $root['info']=$r;
    $root['itemClassList']=$r2;
    $root['itemList']=$r1;
    $p['data']=$root;
//     returnjson($root);
    
        break;
case 'list':
       /**
         * @author wh
         * 资源单基本信息接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=resource&w=list
         * 输入：需登录后访问
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * 以下仅在err为0时会返回
         * info: array  基本信息
         * itemClassList: array  已勾选分类列表
         * itemList: array  已勾选品名列表
         * 
         * 数据为null则到添加页面
         * */
//      $I_shopID = $objShop->isBeShop($uid);
    $r=$objres->getInfo($I_shopID);
    // if(!$r){
    //     $root['err']=0;
    //     $root['info']=null;
    //     $root['itemClassList']=null;
    //     $root['itemList']=null;
    //     $root['msg']='资源单为空，快去添加吧~';
    //     returnjson($root);
    // }
    
    
    $sql="SELECT * FROM sm_item where Status=1 and id in (". $r['Vc_itemIds'].") ";
    $r1= $Db->fetch_all_assoc($sql);
    
    $sql="SELECT * FROM sm_item_class where Status=1 and id in (". $r['Vc_itemClassIds'].") order by I_order ";
    $r2= $Db->fetch_all_assoc($sql);
    
    $root['err']=0;
    $root['info']=$r;
    $root['itemClassList']=$r2;
    $root['itemList']=$r1;
    $p['data']=$root;
//     returnjson($root);
    
        break;
case 'add':

    /**
         * @author wh
         * 添加或编辑资源单信息接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=resource&w=add
         * 输入：需登录后访问
         * 
         * 
         * Vc_name：string 资源单名称
         * Vc_itemClassIds：string 所选分类表ID， 多个分类中间，以半角逗号分割(1,3,4)
         * Vc_itemIds：string 经营的品类表ID，多个品类中间，以半角逗号分割（2,3,4,5）
         * 
         * Vc_desc：string 资源单说明
         * Vc_contact：string 联系人
         * Vc_contact_phone：string 联系人电话
         * Vc_res_file：string 资源单文件url
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         *
         * */
        
        //1.默认撮合市场
//         $I_shopID = $objShop->isBeShop($uid);
       
        
        $da['Vc_name']=$FL->requeststr('Vc_name',1,100,'资源单名称');
        $da['Vc_itemClassIds']=$FL->requeststr('Vc_itemIds',1,100,'所选分类表');
        $da['Vc_itemIds']=$FL->requeststr('Vc_itemIds',1,100,'经营的品名表');
        $da['Vc_desc']=$FL->requeststr('Vc_desc',1,200,'资源单说明');
        $da['Vc_contact']=$FL->requeststr('Vc_contact',1,25,'联系人');
        $da['Vc_contact_phone']=$FL->requeststr('Vc_contact_phone',1,100,'联系人电话');
        $da['Vc_res_file']=$FL->requeststr('Vc_res_file',1,255,'资源单url');
        
        if(!$da['Vc_name']){returnjson(array('err'=>-1,'msg'=>'未填写资源单名称'));}
        if(!$da['Vc_itemClassIds']){returnjson(array('err'=>-1,'msg'=>'未选择所属分类'));}
        if(!$da['Vc_itemIds']){returnjson(array('err'=>-1,'msg'=>'未选择经营品类'));}
        if(!$da['Vc_desc']){returnjson(array('err'=>-1,'msg'=>'未填写资源单说明'));}
        if(!$da['Vc_contact']){returnjson(array('err'=>-1,'msg'=>'未填写联系人'));}
        if(!$da['Vc_contact_phone']){returnjson(array('err'=>-1,'msg'=>'未填写联系人电话'));}
        if(!$da['Vc_res_file']){returnjson(array('err'=>-1,'msg'=>'未上传资源文件'));}
        
        //接收文件
//         if(!isset($_FILES['Vc_res_file'])){
//             returnjson(array('err'=>-1,'msg'=>'请选择资源文件'));
        
//         }
        
//         $p='Vc_res_file';//上传文件变量名
//         $path = '/upload/shop/resfile/';
//         $T='txt|doc|xls|xlsx';
//         $Z=3072000;//文件大小不能超过3M
//         $r=$Fc->uplodefile($p,$path,$T,$Z);
        
//         if($r=='no_type'){
//             returnjson(array('err'=>-1,'msg'=>'文件类型不正确！'));
//         }elseif ($r=='no_size'){
//             returnjson(array('err'=>-1,'msg'=>'文件大小不能超过3M！'));
//         }elseif ($r===false){
//             returnjson(array('err'=>-1,'msg'=>'资源文件上传失败！'));
//         }
       
//         $da['Vc_res_file']=$r;
       
        
        
        $da['Createtime@'] = 'now()';
  
        
        $sql="SELECT id FROM sm_shop_resource where Status=1 and I_shopID=".$I_shopID;
        $r= $Db->fetch_one($sql);
        if(!$r){
            
            $rs=$objres->add($da);
            
        }else{
            
            $sqlw="I_shopID={$I_shopID}";
            $rs=$objres->update($da, $sqlw);
            
        }
        
        if($rs){
        returnjson(array('err'=>0,'msg'=>'信息保存成功！'));
        }else{
        returnjson(array('err'=>-1,'msg'=>'信息保存失败！'));
            
        }
        
        
       
       
   
    break;    
    
case 'itemclasslist':
    /**
     * @author wh
     * 资源单主营品类列表接口------样式详见钢银中国http://member.banksteel.com/consult2/sell/resource/add.jsp
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=resource&w=itemclasslist
     * 输入(无)：需登录后访问
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * message: 提示信息
     * 以下仅在err为0时会返回
     * itemClassList: array  分类列表
     * 
     
     * */
    
//     $I_shopID = $objShop->isBeShop($uid);
    
//     $sql="SELECT id,Vc_mall_classIds,Vc_itemIds FROM sm_shop where Status=1 and I_userID=".$uid;
//     $r= $Db->fetch_one($sql);
//     if(!$r){
        
//         $root['err']=-1;
//         $root['msg']='请先完善基本信息';
//         $root['itemClassList']=null;
//         returnjson($root);
        
        
//     }
    
    $sql="SELECT * FROM sm_item_class where Status=1 order by I_order ";
    $r2= $Db->fetch_all_assoc($sql);
    
    $root['err']=0;
   
    $root['itemClassList']=$r2;
    
    returnjson($root);

    break;
  case 'itemclassinfo':
        /**
         * @author wh
         * 分类详情接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=resource&w=itemclassinfo
         * 输入：
         * 
         * I_classID: int 分类id
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * message: 提示信息
         * 以下仅在err为0时会返回
         * itemList: Array 品名列表
        
         *
         */
    
        //index.php?act=item&m=itemclassinfo&I_classID=1
        $I_classID=$FLib->requestint('I_classID',0,'分类id',1);
    
        if(!$I_classID){returnjson(array('err'=>-1,'msg'=>'参数有误'));}
    
        $I_mall_classID=$FLib->requestint('I_mall_classID',1,'商城总分类id',1);
    
        //品名
        $data['itemList']=$Db->fetch_all_assoc("select id,Vc_name,I_order from sm_item where I_mall_classID={$I_mall_classID} and I_classID={$I_classID} ORDER BY I_order");
       
        $data['err']=0;
        returnjson($data);
    
        break;



}

?>
