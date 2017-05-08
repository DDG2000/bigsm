<?php
if(!defined('WEBROOT'))exit;
if(empty($uid)){returnjson(array('err'=>-1,'msg'=>'获取用户参数失败'));}
require(WEBROOTINCCLASS . 'Address.php');
$a=new address();
//$sitename=$g_conf['cfg_web_name'];
$w=$FL->requeststr('w',1,0,'w',1,1);
switch($w){

    //展示所有收货地址
    case 'list':
        /**
         * @author zy
         * ajax
         * 账户管理>收货地址>展示收货地址接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=address&w=list
         * 输入：需登录后访问
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * array(0=>array(id:收货地址id,Vc_consignee:收货人,provincename:省名,
         * cityname:市名,disname:县名,Vc_consignee_address:地址,Vc_consignee_phone:手机))
         * */
        $da=$a->getAll($uid);
        $p['err']=0;
        $p['msg']='ok';
        $p['data']=$da;
        break;
    
    //新增收货地址
    case 'add':
        /**
         * @author zy
         * ajax
         * 账户管理>收货地址>新增收货地址接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=address&w=add
         * 输入：需登录后访问
         * I_provinceID:省ID
         * I_cityID:市ID
         * I_districtID:区ID
         * Vc_consignee_address:地址
         * Vc_consignee:收货人
         * Vc_consignee_phone:收货人电话
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        $da['I_userID']=$uid;
        //获取地址个数,判断是否大于20
        $num=$a->getNum($uid);
        if($num>20){returnjson(array('err'=>-1,'msg'=>'地址个数超过20'));}
        $da['I_provinceID']=$FLib->requestint('I_provinceID',0,'省份ID',1);
        $da['I_cityID']=$FLib->requestint('I_cityID',0,11,'城市ID',1);
        $da['I_districtID']=$FLib->requestint('I_districtID',0,11,'区县ID',1) ;
        $da['Vc_consignee_address']=$FLib->requeststr('Vc_consignee_address',0,50,'收货地址',1) ;
        $da['Vc_consignee']=$FLib->requeststr('Vc_consignee',0,50,'收货人',1) ;
//        $da['Vc_consignee_company']=$FLib->requeststr('Vc_consignee_company',0,50,'收货公司',1) ;
        $da['Vc_consignee_phone']=$FLib->requeststr('Vc_consignee_phone',0,50,'收货电话',1) ;
        $re=$a->add($da);
        if(!$re){ returnjson(array('err'=>-2,'msg'=>'添加失败'));}
        returnjson(array('err'=>0,'msg'=>'添加成功'));
        break;
    //修改收货地址
    case 'mdy':
        /**
         * @author zy
         * ajax
         * 账户管理>收货地址>修改收货地址接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=address&w=mdy
         * 输入：需登录后访问
         * I_provinceID:省ID
         * I_cityID:市ID
         * I_districtID:区ID
         * Vc_consignee_address:地址
         * Vc_consignee:收货人
         * Vc_consignee_phone:收货人电话
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        $aid=$FLib->requestint('id',0,11,'收货地址id',1) ;
        $da['I_provinceID']=$FLib->requestint('I_provinceID',0,11,'省份ID',1) ;
        $da['I_cityID']=$FLib->requestint('I_cityID',0,11,'城市ID',1) ;
        $da['I_districtID']=$FLib->requestint('I_districtID',0,11,'区县ID',1) ;
        $da['Vc_consignee_address']=$FLib->requeststr('Vc_consignee_address',0,50,'收货地址',1) ;
        $da['Vc_consignee']=$FLib->requeststr('Vc_consignee',0,50,'收货人',1) ;
//        $da['Vc_consignee_company']=$FLib->requeststr('Vc_consignee_company',0,50,'收货公司',1) ;
        $da['Vc_consignee_phone']=$FLib->requeststr('Vc_consignee_phone',0,50,'收货电话',1) ;
        //修改收货地址
        $re=$a->mdy($da,$uid,$aid);
        if(!$re){ returnjson(array('err'=>-1,'msg'=>'修改失败'));}
        //判断为默认收货地址,修改其他收货地址默认收货地址状态
        $da['I_is_default']=$FLib->requestint('I_is_default',0,11,'默认收货地址',1) ;
        if($da['I_is_default']){
            $re=$a->setDefault($aid,$uid);
            if(!$re){ returnjson(array('err'=>-2,'msg'=>'修改失败')); }
        }
        returnjson(array('err'=>0,'msg'=>'修改成功'));
        break;
    //删除收货地址
    case 'delete':
        /**
         * @author zy
         * ajax
         * 账户管理>收货地址>删除收货地址接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=address&w=delete
         * 输入：需登录后访问
         * id:地址id
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        $aid=$FLib->requestint('id',0,11,'收货地址id',1);
        $da['Status']=0;
        $re=$a->mdy($da,$uid,$aid);
        if(!$re){ returnjson(array('err'=>-1,'msg'=>'删除失败')); }
        returnjson(array('err'=>0,'msg'=>'删除成功'));
        break;
    //设为默认地址
    case 'setdefault';
        /**
         * @author zy
         * ajax
         * 账户管理>收货地址>设为默认收货地址接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=address&w=setdefault
         * 输入：需登录后访问
         * id:地址id
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        $aid=$FLib->requestint('id',0,11,'收货地址id',1);
        $re=$a->setDefault($aid,$uid);
        if(!$re){ returnjson(array('err'=>-1,'msg'=>'设置失败')); }
        returnjson(array('err'=>0,'msg'=>'设置成功'));
    break;
}