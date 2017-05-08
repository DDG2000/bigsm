<?php
// if(!defined('WEBROOT'))exit;

$w=$FL->requeststr('w',1,0,'w',1,1);

$m.='_'.$w;


require_once(WEBROOTINCCLASS.'Concentrated.php');
$objConcentrated = new Concentrated();



switch($w){
    
    
case 'test':
    // http://www.bigsm.com/index.php?act=shop&m=concentrated&w=test
    echo "123";

    exit();
    break;    
 
 
case 'detail':
        /**
         * @author wh
         * 查看详情接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=concentrated&w=detail
         * 输入：需登录后访问
         * id : int 集采id
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * 以下仅在err为0时输出
         * data array 数据
         * */
            $id=$FLib->requestint('id',0,'集采id',1);
            if(!$id){returnjson(array('err'=>-1,'msg'=>'数据不合法'));}
           
            
            $r=$objConcentrated->getInfo2($id);
            $root['err']=0;
            $root['data']=$r;
            
            if($r){
                $p['data']=$root;
                // returnjson($root);
            }else{
                returnjson(array('err'=>-1,'msg'=>'数据不存在'));
            }
       
            
            break;
case 'del':
        
            /**
             * @author wh
             * 删除（取消报名）接口
             * url地址：
             * http://www.bigsm.com/index.php?act=shop&m=concentrated&w=del
             * 输入：需登录后访问
             * id : int 集采id
             * 
             *
             * 输出：
             * err:int 结果状态 -1失败 0成功
             * msg: 提示信息
             *
             * */
//             $I_shopID = $objShop->isBeShop($uid);
           
             $id=$FLib->requestint('id',0,'集采id',1);
             if(!$id){returnjson(array('err'=>-1,'msg'=>'数据不合法'));}
            
            
            $da['Createtime@'] = 'now()';
            $da['Status'] = 0;
        
            $sqlw="I_cpID={$id} and I_shopID={$I_shopID}";
            $rs=$objConcentrated->updateRecord($da, $sqlw);
       
//             returnjson($rs);
            if($rs){
                returnjson(array('err'=>0,'msg'=>'删除成功！'));
            }else{
                returnjson(array('err'=>-1,'msg'=>'删除失败！'));
            
            }
             
            break;        
case 'edit':
        
            /**
             * @author wh
             *修改信息接口
             * url地址：
             * http://www.bigsm.com/index.php?act=shop&m=concentrated&w=edit
             * 输入：需登录后访问
             * id : int 集采id
             *  submit:不存在该参数时获取信息模板输出，存在该参数表示提交
                                     则还要传以下参数  :
             * company：string 公司名称
             * contact：string 联系人
             * phone：string 电话号码
             *
             * 输出：
             * err:int 结果状态 -1失败 0成功
             * msg: 提示信息
             *
             * */
        
           
//              $I_shopID = $objShop->isBeShop($uid);
             $id=$FLib->requestint('id',0,'集采id',1);
             if(!$id){returnjson(array('err'=>-1,'msg'=>'数据不合法'));}
             
             if(isset($_REQUEST['submit'])){
                 
                 $da['company']=$FL->requeststr('company',1,100,'公司名称');
                 $da['contact']=$FL->requeststr('contact',1,20,'联系人');
                 $da['phone']=$FL->requeststr('phone',1,20,'电话');
                 if(!$da['company']){returnjson(array('err'=>-1,'msg'=>'公司名称不能为空'));}
                 if(!$da['contact']){returnjson(array('err'=>-1,'msg'=>'联系人不能为空'));}
                 if(!$da['phone']){returnjson(array('err'=>-1,'msg'=>'电话不能为空'));}
                 
                 $sqlw="I_cpID={$id} and I_shopID={$I_shopID}";
                 $rs=$objConcentrated->updateRecord($da, $sqlw);
                 if($rs){
                     returnjson(array('err'=>0,'msg'=>'修改成功！'));
                 }else{
                     returnjson(array('err'=>-1,'msg'=>'修改失败！'));
                 }
                 
             }else{
                 
                 $r=$objConcentrated->getShopInfo($id, $I_shopID);
                 if($r){
                     $p['data']=$r;
//                      returnjson(array('err'=>0,'data'=>$r));
                 }else{
                     
                     returnjson(array('err'=>-1,'msg'=>'数据不存在'));
                 }
                 
             }
             
          
             
            break;        
case 'add':
        
            /**
             * @author wh
             * 报名接口
             * url地址：
             * http://www.bigsm.com/index.php?act=shop&m=concentrated&w=add
             * 输入：需登录后访问
             * id : int 集采id
             * company：string 公司名称
             * contact：string 联系人
             * phone：string 电话号码
             *
             * 输出：
             * err:int 结果状态 -1失败 0成功
             * msg: 提示信息
             *
             * */
        
           
//                  $I_shopID = $objShop->isBeShop($uid);
                 $id=$FLib->requestint('id',0,'集采id',1);
                 if(!$id){returnjson(array('err'=>-1,'msg'=>'数据不合法'));}
                 
                 
                 $da['company']=$FL->requeststr('company',1,100,'公司名称');
                 $da['contact']=$FL->requeststr('contact',1,20,'联系人');
                 $da['phone']=$FL->requeststr('phone',1,20,'电话');
                 if(!$da['company']){returnjson(array('err'=>-1,'msg'=>'公司名称不能为空'));}
                 if(!$da['contact']){returnjson(array('err'=>-1,'msg'=>'联系人不能为空'));}
                 if(!$da['phone']){returnjson(array('err'=>-1,'msg'=>'电话不能为空'));}
                 $da['I_cpID']=$id;
                 $da['I_shopID']=$I_shopID;
                
                 $rs=$objConcentrated->addRecord($da);
                 
                 if($rs){
                     returnjson(array('err'=>0,'msg'=>'报名成功！'));
                 }else{
                     returnjson(array('err'=>-1,'msg'=>'报名失败！'));
                 }
                 
            
             
          
             
            break;        
  
        
case 'list':
        /**
         * @author wh
         * 集采信息列表及综合搜索
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=concentrated&w=list
         * 输入：需登录后访问
         *
         * 筛选条件(可选——可组合)
         * cpage:int 当前的页数,默认为1
         * psize: int 数据分页量,默认为15
         * I_status: int 集采状态:1-已成交,2-未成交
         * record_status:int 参与状态：1-已报名,2-未报名
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
         * 详见模板输出
         * */
      
//        $I_shopID = $objShop->isBeShop($uid);
        
        //已报名集采id
        $IcpIDArr=array();
        $I_cpID=$objConcentrated->getIcpIDArr($I_shopID);
        foreach ($I_cpID as $dv){
            $IcpIDArr[]=$dv['I_cpID'];
        }
        $I_cpIds=join(',', $IcpIDArr);
//         echo $I_cpIds;exit;
        $page=isset($_REQUEST['cpage'])?intval($_REQUEST['cpage']):1;
        $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):15;
        if(!is_numeric($page) || !is_numeric($psize)) {
            returnjson(array('err'=>-1,'msg'=>'数据不合法'));
        }
    
        $I_status=$FLib->requestint('I_status',0,'状态',1);//集采状态：10：审核中20：招标中30：已截至40：已成交50：审核不通过
        $record_status=$FLib->requestint('record_status',0,'状态',1);//参与状态：1-已报名，2-未报名
    
        $endtime = $FL->requeststr('endtime',1,10,'结束时间');
        $starttime  = $FL->requeststr('starttime',1,10,'开始时间');
    
        $sqlw='a.Status=1 ';
        $p['I_status']=2;
        //集采状态判断
        if(isset($_REQUEST['I_status'])){
            if($I_status){
                $p['I_status']=$I_status;
                switch ($I_status){
                    case 1:
                        $sqlw.=" and a.I_status =40 ";
                        break;
                    case 2:
                        $sqlw.=" and a.I_status in (20,30)";
                        break;
                    default:
                        $sqlw.=" and a.I_status in (20,30,40)";
                }
            }
           
           
        }else {
            $sqlw.=" and a.I_status in (20,30,40)";
        }
       
        //参与状态判断
        $p['record_status']=1;
        if(isset($_REQUEST['record_status'])){
            if($record_status){
                $p['record_status']=$record_status;
                switch ($record_status){
                    case 1:
                        $sqlw.=" and a.id in ($I_cpIds) ";
                        break;
                    case 2:
                        $sqlw.=" and a.id  not in ($I_cpIds)";
                        break;
                
                }
                 
                
            }
           
        }
        
    
        if($starttime)$sqlw .= " and a.Createtime >= '".$starttime."'";
        if($endtime)$sqlw .= " and  a.Createtime <= '".$endtime."'";
    
        $order='a.Createtime desc';
//         echo $sqlw;exit;
        $da=$objConcentrated->getShopDataListByPage($page, $psize, $sqlw, $order);
        foreach ($da['data'] as $k=>&$v){
            
            $v['itemArray']=$objConcentrated->getItemList($v['Vc_itemIds']);
            
        }
        
       
        
        $page = $da['page'];
    	$count = $da['count'];
    	$pcount = $da['pcount'];
    	$p['startime']=$starttime;
    	$p['endtime']=$endtime;
    	$p['data'] = $da;
    	$p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=shop&m=concentrated&w=list&starttime={$starttime}&endtime={$endtime}");
    	$p['IcpIDArr'] = $IcpIDArr;
//         returnjson($da);
    
    
        break;
    
    
    

}

?>
