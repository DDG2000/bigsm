<?php
// if(!defined('WEBROOT'))exit;

$w=$FL->requeststr('w',1,0,'w',1,1);

$m.='_'.$w;
$userinfo=getuser(0);
// 检验登录
// if(!$lg){returnjson(array('msg'=>'请先登录','err'=>-1,'url'=>'/index.php?act=user&m=public&w=login'));}else{loginouttime();}
// $uid= $lg['uid'];
if(IS_DEBUG){
    $uid=1;
}
require(WEBROOTINCCLASS.'Concentrated.php');
$objConcentrated = new Concentrated();

require(WEBROOTINC.'File.class.php');
$Fc = new FileClass();


switch($w){
    
    case 'itemclassinfo':
        /**
         * @author wh
         * 分类详情接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=concentrated&w=itemclassinfo
         * 输入：
         *
         * I_classID: int 分类id
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * message: 提示信息
         * 以下仅在err为0时会返回
         * itemList: Array 品名列表
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
    case 'detail':
        /**
         * @author wh
         * 集采详情接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=concentrated&w=detail
         * 输入：需登录后访问
         * id : int 集采id
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         *
         * */
            $id=$FLib->requestint('id',0,'集采id',1);
            if(!$id){returnjson(array('err'=>-1,'msg'=>'数据不合法'));}
            $I_buyerID=$uid;
            
            $r=$objConcentrated->getInfo($id, $uid);
         
            $root['err']=0;
            if($r){
                $sql="SELECT Vc_name from sm_item WHERE Status=1 and id in ({$r['Vc_itemIds']}) and I_classID={$r['I_itemClassId']}";
                $itemsArr =$Db->fetch_all_assoc($sql);
                $r['itemArr']=$itemsArr;
                $root['concentratedinfo']=$r;
            }else{
                $root['concentratedinfo']=null;
            }
            $r2=$objConcentrated->getRelatedInfo($id);
            
            if($r2){
                $root['shopinfo']=$r2;
            }else{
                $root['shopinfo']=null;
            }
           
                $p['data']=$root;
//                 returnjson($root);
       
            
            break;
case 'publish':
        
            /**
             * @author wh
             * 发布中标公告接口
             * url地址：
             * http://www.bigsm.com/index.php?act=user&m=concentrated&w=publish
             * 输入：需登录后访问
             * id : int 集采id
             * submit:不存在该参数时查看通过模版输出获取分类信息，存在该参数表示提交
                                     则还要传以下参数  : 
             * T_announcement：html 中标公告
             *
             * 输出：
             * err:int 结果状态 -1失败 0成功
             * msg: 提示信息
             *
             * */
        
           
             $id=$FLib->requestint('id',0,'集采id',1);
             if(!$id){returnjson(array('err'=>-1,'msg'=>'数据不合法'));}
             if(isset($_REQUEST['submit'])){ 
            $da['T_announcement']=$FL->requeststr('T_announcement',1,5000,'中标公告');
            if(!$da['T_announcement']){returnjson(array('err'=>-1,'msg'=>'中标公告不能为空'));}
            
            $da['T_announcetime'] = 'now()';
            $da['I_status']=40;
            $sqlw="id={$id}";
            $rs=$objConcentrated->update($da, $sqlw);
//             returnjson($rs);
            if($rs){
                returnjson(array('err'=>0,'msg'=>'提交成功！'));
            }else{
                returnjson(array('err'=>-1,'msg'=>'提交失败！'));
            
            }
        }else{
            
            $p['id']=$id;
            
            
        }
            break;        
case 'edit':
        
            /**
             * @author wh
             *重新上传标书接口
             * url地址：
             * http://www.bigsm.com/index.php?act=user&m=concentrated&w=edit
             * 输入：需登录后访问
             * id : int 集采id
             * 
             * Vc_doc：file 集采标书
             *
             * 输出：
             * err:int 结果状态 -1失败 0成功
             * msg: 提示信息
             *
             * */
        
           
             $id=$FLib->requestint('id',0,'集采id',1);
             if(!$id){returnjson(array('err'=>-1,'msg'=>'数据不合法'));}
    
            //接收文件
            if(!isset($_FILES['Vc_doc'])){
                returnjson(array('err'=>-1,'msg'=>'请选择文件'));
        
            }
        
            $p='Vc_doc';//上传文件变量名
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
             
            $da['Vc_doc']=$r;//返回路径地址
             
            $da['Createtime@'] = 'now()';
        
            $sqlw="id={$id}";
            $rs=$objConcentrated->update($da, $sqlw);
//             returnjson($rs);
            if($rs){
                returnjson(array('err'=>0,'msg'=>'提交成功！'));
            }else{
                returnjson(array('err'=>-1,'msg'=>'提交失败！'));
            
            }
             
            break;        
case 'add':
        
            /**
             * @author wh
             * 添加集采内容信息接口
             * url地址：
             * http://www.bigsm.com/index.php?act=user&m=concentrated&w=add
             * 输入：需登录后访问
             * submit:不存在该参数时查看通过模版输出获取分类信息，存在该参数表示提交
                                     则还要传以下参数  :
             * Vc_name：string 名称
             * I_itemClassId：string 所选分类ID
             * Vc_itemIds：string 经营的品类ID，多个品类中间，以半角逗号分割（2,3,4,5）
             * Vc_itemnames：string 经营的品类中文，多个品类中间，以半角逗号分割（2,3,4,5）
             * N_weight: string 总重量
             * 地址：
             * I_provinceID：int 省ID
             * I_cityID：int 市ID
             * I_districtID：int 地区ID
             * Vc_address：string 详细地址
             * 
             * D_start：string 开始时间 '2016-05-02'
             * D_end：string 结束时间 '2016-05-06'
             * 
             * Vc_doc：file 集采标书
             *
             * 输出：
             * err:int 结果状态 -1失败 0成功
             * msg: 提示信息
             *
             * */
        
            //1.默认撮合市场
            //      $I_userID = $_SESSION['user']['uid'];
            // $I_userID=1;//测试
    if(isset($_REQUEST['submit'])){
            $da['I_buyerID']=$uid;
            $da['Vc_name']=$FL->requeststr('Vc_name',1,100,'资源单名称');
            $da['I_itemClassId']=$FLib->requestint('I_itemClassId',0,'分类id',1);
            $da['Vc_itemIds']=$FL->requeststr('Vc_itemIds',1,100,'经营的品名表');
            $da['Vc_itemnames']=$FL->requeststr('Vc_itemnames',1,255,'经营的品名中文');
            $da['N_weight']=$FL->requeststr('N_weight',1,100,'重量');
            $da['I_provinceID']=$FLib->requestint('I_provinceID',0,'省ID',1);
            $da['I_cityID']=$FLib->requestint('I_cityID',0,'市ID',1);
            $da['I_districtID']=$FLib->requestint('I_districtID',0,'地区ID',1);
            $da['Vc_address']=$FL->requeststr('Vc_address',1,100,'详细地址');
            
            $da['D_start']=$FL->requeststr('D_start',1,10,'开始时间');
            $da['D_end']=$FL->requeststr('D_end',1,10,'结束时间');
           
            
        
            if(!$da['Vc_name']){returnjson(array('err'=>-1,'msg'=>'未填写名称'));}
            if(!$da['I_itemClassId']){returnjson(array('err'=>-1,'msg'=>'未选择所属分类'));}
            if(!$da['Vc_itemIds']){returnjson(array('err'=>-1,'msg'=>'未选择经营品类'));}
            if(!$da['N_weight']){returnjson(array('err'=>-1,'msg'=>'未填写重量'));}
            if(!$da['I_provinceID']){returnjson(array('err'=>-1,'msg'=>'未选择省'));}
            if(!$da['I_cityID']){returnjson(array('err'=>-1,'msg'=>'未选择市'));}
            if(!$da['I_districtID']){returnjson(array('err'=>-1,'msg'=>'未选择地区'));}
            if(!$da['Vc_address']){returnjson(array('err'=>-1,'msg'=>'未填写详细地址'));}
            
            if(!$da['D_start']){returnjson(array('err'=>-1,'msg'=>'未选择开始时间'));}
            if(!$da['D_end']){returnjson(array('err'=>-1,'msg'=>'未选择结束时间'));}
            
            
//             $FLib->FromatDate(iset($Rs['Dt_open']),'Y-m-d H:i')//日期格式化
            $da['D_start']=$FLib->fromatDate($da['D_start'], 'Y-m-d');
            $da['D_end']=$FLib->fromatDate($da['D_end'], 'Y-m-d');
            
            
            //接收文件
            if(!isset($_FILES['Vc_doc'])){
                returnjson(array('err'=>-1,'msg'=>'请选择文件'));
            }
        
            $p='Vc_doc';//上传文件变量名
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

            $da['Vc_doc']=$r;//返回路径地址
             
            $da['Createtime@'] = 'now()';
        
            $r=$objConcentrated->create($da);
            
            returnjson($r);
            
            
            
        
    }else{
        
        $I_mall_classID=1;
        $sql="SELECT * FROM sm_item_class where Status=1 order by I_order ";
        $r2= $Db->fetch_all_assoc($sql);
        
//         $root['err']=0;
         
        $root['itemClassList']=$r2;
//         $subItem=array();
//         foreach ($root['itemClassList'] as $k=> &$v){
//             //品名
//             $subItem['itemType']='品名';
//             $temp=$Db->fetch_all_assoc("select id,Vc_name,I_order from sm_item where I_mall_classID={$I_mall_classID} and I_classID={$v['id']} ORDER BY I_order");
//             $subItem['items']=$temp;
//             $v['subItem']=$subItem;
//         }
        
        $p['data']=$root;
//         returnjson($root);
        
    }
            
             
            break;   

 
case 'list':
        /**
         * @author wh
         * 集采信息列表及综合搜索
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=concentrated&w=list
         * 输入：需登录后访问
         *
         * 筛选条件(可选——可组合)
         * cpage:int 当前的页数,默认为1
         * psize: int 数据分页量,默认为15
         * I_status: int 集采状态：10：审核中20：招标中30：已截至40：已成交50：审核不通过
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
       

		
		$p['user']=$userinfo;
		
        
        $page=isset($_REQUEST['cpage'])?intval($_REQUEST['cpage']):1;
        $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):15;
        if(!is_numeric($page) || !is_numeric($psize)) {
            returnjson(array('err'=>-1,'msg'=>'数据不合法'));
        }
    
        $I_status=$FLib->requestint('I_status',10,'状态',1);//集采状态：10：审核中20：招标中30：已截至40：已成交50：审核不通过
    
        $endtime = $FL->requeststr('endtime',1,10,'结束时间');
        $starttime  = $FL->requeststr('starttime',1,10,'开始时间');
    
        $sqlw='a.Status=1 and a.I_buyerID='.$I_buyerID;
    
        if(isset($_REQUEST['I_status'])){
            $sqlw.=" and a.I_status = {$I_status}";
        }
       
    
        if($starttime)$sqlw .= " and a.Createtime >= '".$starttime."'";
        if($endtime)$sqlw .= " and  a.Createtime <= '".$endtime."'";
    
        $order='a.Createtime desc';
    
        $da=$objConcentrated->getDataListByPage($page, $psize, $sqlw, $order);
        
        $page = $da['page'];
    	$count = $da['count'];
    	$pcount = $da['pcount'];
    	$p['startime']=$starttime;
    	$p['endtime']=$endtime;
    	$p['data'] = $da;
    	$p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=user&m=concentrated&w=list&starttime={$starttime}&endtime={$endtime}");
    
//         returnjson($r);   
        break;
    
    
    

}

?>
