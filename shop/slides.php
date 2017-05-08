<?php
// if(!defined('WEBROOT'))exit;

$w=$FL->requeststr('w',1,0,'w',1,1);

$m.='_'.$w;


require_once(WEBROOTINCCLASS.'ShopSlides.php');
$objShopSlides = new ShopSlides();




switch($w){
    
    
  
case 'del':
    /**
     * @author wh
     * 删除图片接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=slides&w=del
     * 输入：需登录后访问
     * id : int 图片id
     
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     *
     * */
    $id=$FLib->requestint('id',0,'图片id',1);
    if(!$id){returnjson(array('err'=>-1,'msg'=>'数据不合法'));}
    
    $imgfile=$objShopSlides->getImgFile($id);
    
    $imgfile=WEBROOT.$imgfile;
    
    $da['Status']=0;
    $sqlw="id={$id}";
    $rs=$objShopSlides->update($da, $sqlw);
    if($rs){

        //读取完后就删除当前文件
        if(is_file($imgfile)){
            chmod($imgfile,0777);
            @unlink($imgfile);
        
        }
        
        returnjson(array('err'=>0,'msg'=>'删除成功！'));
    }else{
        returnjson(array('err'=>-1,'msg'=>'删除失败！'));
    
    }

 
    break;    
case 'edit':
   /**
     * @author wh
     * 编辑轮播图接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=slides&w=edit
     * 输入：需登录后访问
     * 
     * id : int 图片id
     * submit:不存在该参数时查看该轮播图信息，存在该参数表示提交
     * 则还要传以下参数，
     * 
     * Vc_name：string 图片名称
     * Vc_linkurl：string 跳转地址
     * Vc_img：file 图片文件
     * I_order：int 排序号
     * 
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     * data:数据
     * */
    
    
//      $I_shopID = $objShop->isBeShop($uid);
     
     
    $id=$FLib->requestint('id',0,'图片id',1);
    if(!$id){returnjson(array('err'=>-1,'msg'=>'数据不合法'));}
    
    
if(isset($_REQUEST['submit'])){
    
    $da['Vc_name']=$FL->requeststr('Vc_name',1,50,'图片名称');
    $da['Vc_linkurl']=$FL->requeststr('Vc_linkurl',1,255,'跳转链接');
    $da['I_order']=$FLib->requestint('I_order',0,'排序号',1);
    
    if(!$da['Vc_name']){returnjson(array('err'=>-1,'msg'=>'未填写图片名称'));}
    if(!$da['Vc_linkurl']){returnjson(array('err'=>-1,'msg'=>'未填写跳转链接'));}
    if(!$da['I_order']){returnjson(array('err'=>-1,'msg'=>'未填写排序号'));}
    
    //接收文件
    if(!isset($_FILES['Vc_img'])){
        returnjson(array('err'=>-1,'msg'=>'请选择图片'));
        
    }
   
    $p='Vc_img';//上传文件变量名
    $path = '/upload/shop/slides/';
    $T='jpg|png|bmp';
    $Z=500000;//文件大小不能超过500k
    $r=$Fc->uplodefile($p,$path,$T,$Z);//返回图片路径
    
    if($r=='no_type'){
        returnjson(array('err'=>-1,'msg'=>'文件类型不正确！'));
    }elseif ($r=='no_size'){
        returnjson(array('err'=>-1,'msg'=>'文件大小不能超过500k！'));
    }elseif ($r===false){
        returnjson(array('err'=>-1,'msg'=>'资源文件上传失败！'));
    }
     
    $da['Vc_url']=$r;
    
    $da['Createtime@'] = 'now()';
    
    
    
    $sqlw="id={$id} and I_shopID={$I_shopID}";
    $rs=$objShopSlides->update($da, $sqlw);
    if($rs){
        returnjson(array('err'=>0,'msg'=>'提交成功！'));
    }else{
        returnjson(array('err'=>-1,'msg'=>'提交失败！'));
    
    }
}else{
   // $I_shopID=$objShop->getsid($I_userID);
    $r=$objShopSlides->getInfo($id,$I_shopID);
    
    $p['data']=$r;
//     if($r){
//        returnjson(array('err'=>0,'data'=>$r));
//     }else{
//         returnjson(array('err'=>-1,'msg'=>'不存在该图片'));
//     }
}
    
    break;    
case 'add':
   
    /**
     * @author wh
     * 添加图片接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=slides&w=add
     * 输入：需登录后访问
     *
     * submit:不存在该参数时模板输出，存在该参数表示提交
     * 则还要传以下参数：
     * Vc_name：string 图片名称
     * Vc_linkurl：string 跳转地址
     * Vc_img：file 图片文件
     * I_order：int 排序号
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     *
     * */
//      $I_shopID = $objShop->isBeShop($uid);
    
    if(isset($_REQUEST['submit'])){
    $max_adroll_num=$g_conf['cfg_shop_adroll_num'];
    //查询已有轮播图数量
    $c = $objShopSlides->getSlidesCount($I_shopID);
    if($c>=$max_adroll_num){
        returnjson(array('err'=>-1,'msg'=>'最多添加'.$max_adroll_num.'张轮播图'));
    }
    
    
    $da['I_shopID']=$I_shopID;
    
    $da['Vc_name']=$FL->requeststr('Vc_name',1,50,'图片名称');
    $da['Vc_linkurl']=$FL->requeststr('Vc_linkurl',1,255,'跳转链接');
    $da['I_order']=$FLib->requestint('I_order',0,'排序号',1);
    
    if(!$da['Vc_name']){returnjson(array('err'=>-1,'msg'=>'未填写图片名称'));}
    if(!$da['Vc_linkurl']){returnjson(array('err'=>-1,'msg'=>'未填写跳转链接'));}
    if(!$da['I_order']){returnjson(array('err'=>-1,'msg'=>'未填写排序号'));}
    
    //接收文件
    if(!isset($_FILES['Vc_img'])){
        returnjson(array('err'=>-1,'msg'=>'请选择图片'));
        
    }
   
    $p='Vc_img';//上传文件变量名
    $path = '/upload/shop/slides/';
    $T='jpg|png|bmp';
    $Z=500000;//文件大小不能超过500k
    $r=$Fc->uplodefile($p,$path,$T,$Z);//返回图片路径
    
    if($r=='no_type'){
        returnjson(array('err'=>-1,'msg'=>'文件类型不正确！'));
    }elseif ($r=='no_size'){
        returnjson(array('err'=>-1,'msg'=>'文件大小不能超过500k！'));
    }elseif ($r===false){
        returnjson(array('err'=>-1,'msg'=>'资源文件上传失败！'));
    }
     
    $da['Vc_url']=$r;
    
    $da['Createtime@'] = 'now()';
    
    $rs=$objShopSlides->add($da);
    
    
    if($rs){
        returnjson(array('err'=>0,'msg'=>'添加成功！'));
    }else{
        returnjson(array('err'=>-1,'msg'=>'添加失败！'));
    
    }
    
    }else{
        
        
    }
    
    
    
    break;    


case 'list':
    /**
     * @author wh
     * 轮播图片列表接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=slides&w=list
     * 输入：需登录后访问
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     ** 以下仅在err为0时会返回
     * list:数据
     *
     * */
        
    
//     $I_shopID = $objShop->isBeShop($uid);
    
    $data['list']=$objShopSlides->getDataList($I_shopID);
    
 
//     $page=isset($_REQUEST['cpage'])?intval($_REQUEST['cpage']):1;
//     $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):15;
//     if(!is_numeric($page) || !is_numeric($psize)) {
//         returnjson(array('err'=>-1,'msg'=>'数据不合法'));
    
//     }
//     $wheresql='a.Status=1 and a.I_shopID='.$I_shopID;
//     $order='Createtime desc';
//     $data=$objShopSlides->getDataListByPage($page, $psize, $wheresql, $order);
   
    $p['data'] = $data;
//     returnjson($data);
    
  

    break;
    


	



}

?>
