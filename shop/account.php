<?php
// if(!defined('WEBROOT'))exit;

$w=$FL->requeststr('w',1,0,'w',1,1);

$m.='_'.$w;

// http://www.bigsm.com/index.php?act=shop&m=account&w=avatar
switch($w){
    
 
case 'certify-save':
   
    /**
     * @author wh
     * 添加或编辑认证信息接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=account&w=certify-save
     * 输入：需登录后访问
     *
     * Vc_corporation：string  法人姓名
     * Vc_identity：string  法人身份证号码
     * Vc_identity_pic1：string  身份证正面照片url
     * Vc_identity_pic2：string  身份证背面照片url
     * Vc_licence_pic：string  营业执照照片url
     * Vc_tax_pic：string  税务登记证图片url
     * Vc_org_pic：string  组织机构代码证照片url
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     *
     * */

    
    
    $sql="SELECT id,Vc_name,Vc_contact,I_type FROM sm_shop where I_userID=".$uid;
    $r= $Db->fetch_one($sql);
    if(!$r){
//         returnjson(array('err'=>-1,'msg'=>'请先完善基本信息'));
        header("location:/index.php?act=shop&m=account&w=base");
    }
    
    $I_shopID=$r['id'];
    $da['id']=$I_shopID;
    $da['Vc_name']=$r['Vc_name'];
    $da['Vc_contact']=$r['Vc_contact'];
    $da['Vc_corporation']=$FL->requeststr('Vc_corporation',1,50,'法人姓名');
    $da['Vc_identity']=$FL->requeststr('Vc_identity',1,30,'法人身份证号码');
    $da['Vc_identity_pic1']=$FL->requeststr('Vc_identity_pic1',1,300,'身份证正面照片url');
    $da['Vc_identity_pic2']=$FL->requeststr('Vc_identity_pic2',1,300,'身份证背面照片url');
    $da['Vc_licence_pic']=$FL->requeststr('Vc_licence_pic',1,300,'营业执照照片url');
    $da['Vc_tax_pic']=$FL->requeststr('Vc_tax_pic',1,300,'税务登记证图片url');
    $da['Vc_org_pic']=$FL->re   queststr('Vc_org_pic',1,300,'组织机构代码证照片url');
    
    if(!$da['Vc_corporation']){returnjson(array('err'=>-1,'msg'=>'未填写法人姓名'));}
    if(!$da['Vc_identity']){returnjson(array('err'=>-1,'msg'=>'未填写身份证号码'));}
    if(!$da['Vc_identity_pic1']){returnjson(array('err'=>-1,'msg'=>'未上传身份证正面照片'));}
    if(!$da['Vc_identity_pic2']){returnjson(array('err'=>-1,'msg'=>'未上传身份证背面照片'));}
    if(!$da['Vc_licence_pic']){returnjson(array('err'=>-1,'msg'=>'未上传营业执照照片'));}
    if(!$da['Vc_tax_pic']){returnjson(array('err'=>-1,'msg'=>'未上传税务登记证图片'));}
    if(!$da['Vc_org_pic']){returnjson(array('err'=>-1,'msg'=>'未上传组织机构代码证照片'));}
    

    $r=$objShop->create($da);
    
    if($r['flag']<1){ returnjson($r); }
    $r['err']=0;
    returnjson($r);

    break;    
case 'info-save':
    /**
     * @author wh
     * 添加公司简介接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=account&w=info-save
     * 输入：需登录后访问
     *
     * T_desc：string  简介内容
     *
     * 输出：
     * err:int 结果状态 -1失败 0成功
     * msg: 提示信息
     *
     * */
  
    $sql="SELECT id,I_type FROM sm_shop where I_userID=".$uid;
    $r= $Db->fetch_one($sql);
    if(!$r){
//         returnjson(array('err'=>-1,'msg'=>'请先完善基本信息'));
        header("location:/index.php?act=shop&m=account&w=base");
    }
    
    $da['T_desc']=htmlspecialchars_decode($FL->requeststr('T_desc',1,30000,'公司简介'));
    if(!$da['T_desc']){returnjson(array('err'=>-1,'msg'=>'未填写简介'));}
    $sqlw="I_userID={$uid}";
    $rs=$objShop->update($da, $sqlw);
    if($rs){
        returnjson(array('err'=>0,'msg'=>'保存成功！'));
    }else{
        returnjson(array('err'=>-1,'msg'=>'保存失败！'));
    
    }
    
    
    break;    
case 'base-save':

    /**
         * @author wh
         * 添加或编辑基本信息接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=account&w=base-save
         * 输入：需登录后访问
         *
         * submit:不存在该参数时查看通过模版输出信息，存在该参数表示提交
                 则还要传以下参数  :
         * Vc_logo_pic：string 公司logo照片url
         * Vc_name：string 公司名称
         * I_provinceID：int 省ID
         * I_cityID：int 市ID
         * I_districtID：int 地区ID
         * Vc_address：string 公司详细地址
         * Vc_phone：string 公司电话
         * Vc_fax：string 公司传真
         * Vc_contact：string 公司联系人
         * Vc_contact_phone：string 联系人电话
     * 
         * Vc_service_qq：string 客服qq
         * I_mallclassID：int 经营范围ID 
         * Vc_itemIds：string 经营的品类表ID 多个品类中间，以半角逗号分割
         *
         *
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         *
         * */
    
        
        $Vc_logo_pic=$FL->requeststr('Vc_logo_pic',1,250,'公司logo照片url');
        $Vc_name=$FL->requeststr('Vc_name',1,60,'公司名称');
        $I_provinceID=$FLib->requestint('I_provinceID',0,'省ID',1);
        $I_cityID=$FLib->requestint('I_cityID',0,'市ID',1);
        $I_districtID=$FLib->requestint('I_districtID',0,'地区ID',1);
        $Vc_address=$FL->requeststr('Vc_address',1,100,'公司详细地址');
        $Vc_phone=$FL->requeststr('Vc_phone',1,20,'公司电话');
        $Vc_fax=$FL->requeststr('Vc_fax',1,20,'公司传真');
        $Vc_contact=$FL->requeststr('Vc_contact',1,100,'公司联系人');
        $Vc_contact_phone=$FL->requeststr('Vc_contact_phone',1,100,'联系人电话');
        $Vc_service_qq=$FL->requeststr('Vc_service_qq',1,60,'客服qq');
        $I_mallclassID=$FLib->requestint('I_mallclassID',0,'经营范围ID',1);
        $Vc_itemIds=$FL->requeststr('Vc_itemIds',1,100,'经营的品类表');
        
        
        if(!$Vc_logo_pic){returnjson(array('err'=>-1,'msg'=>'未上传公司logo图片'));}
        if(!$Vc_name){returnjson(array('err'=>-1,'msg'=>'未填写公司名称'));}
        if(!$I_provinceID){returnjson(array('err'=>-1,'msg'=>'未选择省'));}
        if(!$I_cityID){returnjson(array('err'=>-1,'msg'=>'未选择市'));}
        if(!$I_districtID){returnjson(array('err'=>-1,'msg'=>'未选择地区'));}
        if(!$I_mallclassID){returnjson(array('err'=>-1,'msg'=>'未选择经营范围'));}
        
        
        
        $da=array();
        
        $da['Vc_logo_pic']=$Vc_logo_pic;
        $da['Vc_name']=$Vc_name;
        $da['I_provinceID']=$I_provinceID;
        $da['I_cityID']=$I_cityID;
        $da['I_districtID']=$I_districtID;
        $da['Vc_address']=$Vc_address;
        $da['I_type']=2;
        $da['Vc_phone']=$Vc_phone;
        $da['Vc_fax']=$Vc_fax;
        $da['Vc_contact']=$Vc_contact;
        $da['Vc_contact_phone']=$Vc_contact_phone;
        $da['Vc_service_qq']=$Vc_service_qq;
        $da['I_mallclassID']=$I_mallclassID;
        $da['Vc_itemIds']=$Vc_itemIds;
        $da['Createtime@'] = 'now()';
    
//         $rs=$Db->autoExecute('sm_commodity_steel', $da,'INSERT');
        
        $sql="SELECT id,I_type FROM sm_shop where I_userID=".$uid;
        $r= $Db->fetch_one($sql);
        if(!$r){
            
            $rs=$objShop->add($da);
            
        }else{
            
            $sqlw="I_userID={$uid}";
            $rs=$objShop->update($da, $sqlw);
            
        }
        
        if($rs){
        returnjson(array('err'=>0,'msg'=>'信息保存成功！'));
        }else{
        returnjson(array('err'=>-1,'msg'=>'信息保存失败！'));
            
        }

        
   
    break;    
    
case 'base':
    /**
     * @author wh
     * 账户管理基本信息接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=account&w=base
     * 输入(无)：需登录后访问
     *
     * 模板输出：
     * 分已提交和未提交两种情况
     * 
     * logo图片异步上传--Jcrop插件
     * 结合基本信息logo图片处理接口
     * 
     * 
     * 未提交的模板经营范围
     * 结合行业下分类列表接口
     * http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=mallclassinfo
     * */
    
  
    
    $sql="SELECT id,I_mallclassID,Vc_itemIds FROM sm_shop where Status=1 and I_userID=".$uid;
    $rs= $Db->fetch_one($sql);
    if($rs){
        
    $root=array();
    
    $shopRs=$objShop->getInfo($rs['id']);//$rs['I_mallclassID'],$rs['Vc_itemIds']
    $root['baseinfo']=$shopRs;
    $sql="SELECT id,Vc_name FROM sm_mall_class where Status=1 ";
    $root['mallclass']= $Db->fetch_all_assoc($sql);
    
    $sql="SELECT id,Vc_name FROM sm_item_class where Status=1 and I_mall_classID={$rs['I_mallclassID']}";
    $root['subitemclass']= $Db->fetch_all_assoc($sql);
//     dump($root);
//     exit;
    $itemClassArr = explode(",", $shopRs['Vc_itemIds']);
    $p['itemClassArr']=array_flip($itemClassArr);
    $p['data']=$root;
    $p['issubed']=1;
    
    
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

    break;

case 'info':
    /**
     * @author wh
     * 账户管理公司简介接口
     * url地址：
     * http://www.bigsm.com/index.php?act=shop&m=account&w=info
     * 输入(无)：需登录后访问
     *
     * 模板输出：
     * 分已提交和未提交两种情况
     * 
     * */
    
    //1.根据登录的userid查sm_shop表拿到各分类信息Vc_mall_classIds，Vc_itemIds
    //  $I_userID = $_SESSION['user']['uid'];
//     $I_userID=1;//测试
    
    $sql="SELECT T_desc FROM sm_shop where Status=1 and I_userID=".$uid;
    $rs= $Db->fetch_one($sql);
    if($rs){
        
        //dump($rs);exit;
    $p['data']=$rs;
    $p['issubed']=1;
    
    
    }else{
      
        $p['issubed']=0;
        
    }

         break;
case 'certify':
        /**
         * @author wh
         * 账户管理认证信息接口
         * url地址：
         * http://www.bigsm.com/index.php?act=shop&m=account&w=certify
         * 输入(无)：需登录后访问
         *
         * 模板输出：
         * 分已提交和未提交两种情况
         *
         * 各图片裁剪异步上传----Jcrop插件
         * 结合认证信息所有图片处理接口接口
         * 
         * */
    
    
        $sql="SELECT id FROM sm_shop where Status=1 and I_userID=".$uid;
        $isExistShop= $Db->fetch_one($sql);
        
        if($isExistShop){
        
        $p['isExistShop']=1;
        $shopRs=$objShop->getInfo($isExistShop['id']);
        
        $sql="SELECT Vc_corporation FROM sm_shop where Status=1 and I_userID=".$uid;
        $rs= $Db->fetch_one($sql);
        if($rs){//已认证
    
            $p['issubed']=1;
            
            $p['data']=$shopRs;
    
        }else{
            //未认证
            
            $p['issubed']=0;
    
          }
        
        }else{
             
             
             $p['isExistShop']=0;
             
             
         }
        break;

case 'avatar'://shoplogo例子
//     http://www.bigsm.com/index.php?act=shop&m=account&w=avatar
    /**
     * @author wh
     * logo上传和裁剪保存
     * url地址：
     * http://www.bigsm.com/shop/logo_action.php
     * 输入：
     * mypic：file 图片
     *
     * 输出：
     {
    "name":"butt.png",
    "pic":"/upload/shop/logo/tmp/20160426115551521_n.jpg",
    "width":130,
    "height":40
}
     *
     */
    
    
        $m = 'avatar';
        break;
case 'upimg':
    //上传临时图片统一访问，拿到临时图片url
    //http://www.bigsm.com/include/upimgtmp.php
    echo "123";
    
    exit;
    break;

    
	



}

?>
