<?php
// if(!defined('WEBROOT'))exit;

$w=$FL->requeststr('w',1,0,'w',1,1);

$m.='_'.$w;
require_once(WEBROOTINCCLASS.'Warehouse.php');

$obj2 = new Warehouse();


switch($w){
 
case 'del':
    /**
     * @author wh
     * 删除仓库接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=warehouse&w=del
     * 输入：需登录后访问
     * id : int 仓库id
     
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     *
     * */
    $id=$FLib->requestint('id',0,'仓库id',1);
    if(!$id){returnjson(array('err'=>-1,'msg'=>'数据不合法'));}
    $da['Status']=0;
    $sqlw="id={$id}";
    $rs=$obj2->update($da, $sqlw);
    if($rs){
        returnjson(array('err'=>0,'msg'=>'删除成功！'));
    }else{
        returnjson(array('err'=>-1,'msg'=>'删除失败！'));
    
    }

 
    break;    
case 'edit':
   /**
     * @author wh
     * 编辑仓库接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=warehouse&w=edit
     * 输入：需登录后访问
     * id : int 仓库id
     * 
     * submit:不存在该参数时查看该仓库信息，存在该参数表示提交
     * 则还要传以下参数：
     * Vc_name：string 仓库名称
     * I_provinceID：int 省ID
     * I_cityID：int 市ID
     * I_districtID：int 地区ID
     * Vc_address：string 仓库详细地址
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     *
     * */
//     $I_shopID = $objShop->isBeShop($uid);
    
    $id=$FLib->requestint('id',0,'仓库id',1);
    if(!$id){returnjson(array('err'=>-1,'msg'=>'数据不合法'));}
    
    
    if(isset($_REQUEST['submit'])){
    
    
    $da['Vc_name']=$FL->requeststr('Vc_name',1,100,'仓库名称');
    $da['I_provinceID']=$FLib->requestint('I_provinceID',0,'省ID',1);
    $da['I_cityID']=$FLib->requestint('I_cityID',0,'市ID',1);
    $da['I_districtID']=$FLib->requestint('I_districtID',0,'地区ID',1);
    $da['Vc_address']=$FL->requeststr('Vc_address',1,100,'详细地址');
    
    if(!$da['Vc_name']){returnjson(array('err'=>-1,'msg'=>'未填写仓库名称'));}
    if(!$da['I_provinceID']){returnjson(array('err'=>-1,'msg'=>'未选择省'));}
    if(!$da['I_cityID']){returnjson(array('err'=>-1,'msg'=>'未选择市'));}
    if(!$da['I_districtID']){returnjson(array('err'=>-1,'msg'=>'未选择地区'));}
    if(!$da['Vc_address']){returnjson(array('err'=>-1,'msg'=>'未填写详细地址'));}
    
    
    
    $sqlw="id={$id} and I_shopID={$I_shopID}";
    $rs=$obj2->update($da, $sqlw);
    if($rs){
        returnjson(array('err'=>0,'msg'=>'保存成功！'));
    }else{
        returnjson(array('err'=>-1,'msg'=>'保存失败！'));
    
    }
    
    }else{
        
     
        $r=$obj2->getInfo($id,$I_shopID);
        $p['data']=$r;
//         if($r){
//             returnjson(array('err'=>0,'data'=>$r));
//         }else{
//             returnjson(array('err'=>-1,'msg'=>'不存在该仓库'));
//         }
        
        
    }
    
    break;    
case 'add':
   
    /**
     * @author wh
     * 添加仓库接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=warehouse&w=add
     * 输入：需登录后访问
     ** submit:不存在该参数时模板输出，存在该参数表示提交
     * 则还要传以下参数：
     * Vc_name：string 仓库名称
     * I_provinceID：int 省ID
     * I_cityID：int 市ID
     * I_districtID：int 地区ID
     * Vc_address：string 仓库详细地址
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     *
     * */

 
//     $I_shopID = $objShop->isBeShop($uid);
    if(isset($_REQUEST['submit'])){
    $da['I_shopID']=$I_shopID;
    
    $da['Vc_name']=$FL->requeststr('Vc_name',1,100,'仓库名称');
    $da['I_provinceID']=$FLib->requestint('I_provinceID',0,'省ID',1);
    $da['I_cityID']=$FLib->requestint('I_cityID',0,'市ID',1);
    $da['I_districtID']=$FLib->requestint('I_districtID',0,'地区ID',1);
    $da['Vc_address']=$FL->requeststr('Vc_address',1,100,'详细地址');
    
    if(!$da['Vc_name']){returnjson(array('err'=>-1,'msg'=>'未填写仓库名称'));}
    if(!$da['I_provinceID']){returnjson(array('err'=>-1,'msg'=>'未选择省'));}
    if(!$da['I_cityID']){returnjson(array('err'=>-1,'msg'=>'未选择市'));}
    if(!$da['I_districtID']){returnjson(array('err'=>-1,'msg'=>'未选择地区'));}
    if(!$da['Vc_address']){returnjson(array('err'=>-1,'msg'=>'未填写详细地址'));}
    
   
     $rs=$obj2->add($da);
    
    
    if($rs){
        returnjson(array('err'=>0,'msg'=>'保存成功！'));
    }else{
        returnjson(array('err'=>-1,'msg'=>'保存失败！'));
    
    }

    }
    break;    


case 'list':
    /**
     * @author wh
     * 仓库列表接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=warehouse&w=list
     * 输入：需登录后访问
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
     * */
//     $I_shopID = $objShop->isBeShop($uid);
    
    $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
    $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):15;
    if(!is_numeric($page) || !is_numeric($psize)) {
        returnjson(array('err'=>-1,'msg'=>'数据不合法'));
    
    }
    $wheresql='a.Status=1 and a.I_shopID='.$I_shopID;
    $order='Createtime desc';
    $data=$obj2->getDataListByPage($page, $psize, $wheresql, $order);
    
//     $data['err']=0;
//     returnjson($data);
    $p['data']=$data;
    $page = $data['page'];
    $count = $data['count'];
    $pcount = $data['pcount'];
 
     
    $p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=shop&m=warehouse&w=list");
  

    break;
    


	



}

?>
