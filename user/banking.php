<?php
/**
 * 用户金融管理
 */
if(!defined('WEBROOT'))exit;
//获取用户公司名
$user = new User();
$userinfo=$user->getUserInfo($uid);
$Vc_company=$userinfo['Vc_companyname'];

$re=$user->getCompanyStatus($uid);
if($re['I_status']!=30){returnjson(array('err'=>-5,'msg'=>'请先完成公司认证'));}

require_once( WEBROOTINCCLASS.'Banking.php');
$B=new Banking();
$w=$FL->requeststr('w',1,0,'w',1,1);
$m.='_'.$w;
switch($w) {
    case 'test':
        /**
         *
         * http://www.bigsm.com/index.php?act=user&m=banking&w=addinventory&Vc_company=兴元责任有限公司
         * &Vc_name=苹果电脑&Vc_grade=1*6&I_amount=2000台&N_price=1200万&Vc_address=双流龙岗&Vc_owen_company=航东
         * &Vc_bank_cycle=100天&Vc_bank_size=1000万&Vc_contactor=张辉&Vc_contactor_phone=17777777777&Vc_note=很急的&submit=
         *
         * http://www.bigsm.com/index.php?act=user&m=banking&w=addwarehouse&Vc_company=大东&Vc_type=普通
         * &Vc_name=沙发&I_amount=500件&N_price=2000万&Vc_warehouse=京东仓&Vc_bailor=张柳&Vc_keeper=琪琪&I_secure=1
         * &Vc_bank_cycle=一年&Vc_bank_size=少说5个亿&Vc_contactor=孙晓&Vc_contactor_phone=17777777777&Vc_note=我实力很强的&submit=
         *
         * http://www.bigsm.com/index.php?act=user&m=banking&w=addticket&Vc_company=大发集团&Dt_issuedate=2016-6-27
         * &N_price=100万&Dt_enddate=2016-10-9&Vc_holder=张三&I_sure=1&Vc_products=服装&Vc_size=200亿&Vc_bank_cycle=8年
         * &Vc_contactor=李四&Vc_contactor=王雪&Vc_contactor_phone=17777777777&Vc_note=一定要来哦&submit=
         *
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=banking&w=test
         * 输入：需登录后访问
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        $num=$FLib->requestint('num',0,'',1) ;
        for($i=0;$i<=$num;$i++){
            for($i=0;$i<=$num;$i++){
                echo " "*($num-1);
            }
            for($i=0;$i<=$num;$i++){
                echo "*"*$num;
            }
        }
        $re=$B->getcolume('Vc_name','id=1',1);
        break;
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 公用部分 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    //添加商票质押金融服务
    case 'add':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=banking&w=add
         * 添加页面
         * 输入：需登录后访问
         * 输出：Vc_company:str 公司名
         *
         * 添加提交
         * 输入:
         * Vc_company:str 公司名(需要)
         * I_banking_classID:str 金融申请类型 1质押  2仓单  3商票
         *
         * 1存货质押inventory
         * Vc_name:str 存货品名
         * Vc_grade:str 规格等级
         * I_amount:str 数量
         * N_price:str 市场价格
         * Vc_address:str 存货地点
         * Vc_owen_company:str 货权单位
         * Vc_bank_cycle:str 融资周期
         * Vc_bank_size;str 融资规模
         * Vc_contactor:str 联系人
         * Vc_contactor_phone 联系电话
         * Vc_note:str 备注
         *
         * 2仓单质押warehouse
         * Vc_type:str 仓单类型标准/普通
         * Vc_name:str 品名
         * I_amount:str 数量
         * N_price:str 市场价格
         * Vc_warehouse:str 仓库名称
         * Vc_bailor:str 存货人
         * Vc_keeper:str 仓管人
         * I_secure:int 是否购买保险1/0
         * Vc_bank_size;str 融资规模
         * Vc_bank_cycle;str 融资周期
         * Vc_contactor:str 联系人
         * Vc_contactor_phone 联系电话
         * Vc_note:str 备注
         *
         * 3商票质押ticket
         * Vc_drawer:str 出票人
         * Dt_issuedate:datetime 出票日
         * N_price:str 金额
         * Dt_enddate:datetime 到期日
         * Vc_holder:str 持票人
         * I_sure:int 银行是否保兑1/0
         * Vc_products:str 贸易产品
         * Vc_size:str 贸易规模
         * Vc_bank_cycle:str 融资周期
         * Vc_bank_size:str 融资规模
         * Vc_contactor:str 联系人
         * Vc_contactor_phone 联系电话
         * Vc_note:str 备注
         *
         * 输出:
         * err:int 结果状态 <0失败 0成功
         * msg: 提示信息
         * */
        //获取金融服务类型
        $re=$user->getCompanyStatus($uid);
        if($re['I_status']!=30){returnjson(array('err'=>-5,'msg'=>'请先完成公司认证'));}
        if(isset($_REQUEST['submit'])){
            //金融主表字段
            $bank['I_userID']=$uid;
            //获取金融类型
            $bank['I_banking_classID']=$FLib->requestint('I_banking_classID',0,'金融申请类型',1) ;
            $type=$bank['I_banking_classID'];
            $company=$FL->requeststr('Vc_company',1,20,'公司名');

            //质押
            if($type==1){
                $da['Vc_name']=$FL->requeststr('Vc_name',1,20,'存货品名');
                $da['Vc_grade']=$FL->requeststr('Vc_grade',1,20,'规格等级');
                $da['I_amount']=$FL->requeststr('I_amount',1,20,'数量');
                $da['N_price']=$FL->requeststr('N_price',1,20,'市场价格');
                $da['Vc_address']=$FL->requeststr('Vc_address',1,20,'存货地点');
                $da['Vc_owen_company']=$FL->requeststr('Vc_owen_company',1,50,'货权单位');
                $da['Vc_bank_cycle']=$FL->requeststr('Vc_bank_cycle',1,20,'融资周期');
                $da['Vc_bank_size']=$FL->requeststr('Vc_bank_size',1,20,'融资规模');
                $da['Vc_contactor']=$FL->requeststr('Vc_contactor',1,20,'联系人');
                $da['Vc_contactor_phone']=$FL->requeststr('Vc_contactor_phone',1,20,'联系电话');
                $da['Vc_note']=$FL->requeststr('Vc_note',1,255,'备注');
            }
            //仓单
            if($type==2){
                $da['Vc_type']=$FL->requeststr('Vc_type',1,20,'仓单类型标准/普通');
                $da['Vc_name']=$FL->requeststr('Vc_name',1,20,'品名');
                $da['I_amount']=$FL->requeststr('I_amount',1,20,'数量');
                $da['N_price']=$FL->requeststr('N_price',1,20,'市场价格');
                $da['Vc_warehouse']=$FL->requeststr('Vc_warehouse',1,255,'仓库名称');
                $da['Vc_bailor']=$FL->requeststr('Vc_bailor',1,20,'存货人');
                $da['Vc_keeper']=$FL->requeststr('Vc_keeper',1,20,'仓管人');
                $da['I_secure']=$FLib->requestint('I_secure',0,'是否购买保险1/0',1) ;
                $da['Vc_bank_cycle']=$FL->requeststr('Vc_bank_cycle',1,20,'融资周期');
                $da['Vc_bank_size']=$FL->requeststr('Vc_bank_size',1,20,'融资规模');
                $da['Vc_contactor']=$FL->requeststr('Vc_contactor',1,20,'联系人');
                $da['Vc_contactor_phone']=$FL->requeststr('Vc_contactor_phone',1,20,'联系电话');
                $da['Vc_note']=$FL->requeststr('Vc_note',1,255,'备注');
            }
            //商票
            if($type==3){
                //获取存货质押,所有字段不能为空
                $da['Vc_drawer']=$FL->requeststr('Vc_drawer',1,20,'出票人');
                $da['Dt_issuedate']=$FL->requeststr('Dt_issuedate',1,20,'出票日');
                $da['N_price']=$FL->requeststr('N_price',1,20,'金额');
                $da['Dt_enddate']=$FL->requeststr('Dt_enddate',1,20,'到期日');
                $da['Vc_holder']=$FL->requeststr('Vc_holder',1,20,'持票人');
                $da['I_sure']=$FLib->requestint('I_sure',0,'银行是否保兑1/0',1) ;
                $da['Vc_products']=$FL->requeststr('Vc_products',1,20,'贸易产品');
                $da['Vc_size']=$FL->requeststr('Vc_size',1,20,'贸易规模');
                $da['Vc_bank_cycle']=$FL->requeststr('Vc_bank_cycle',1,20,'融资周期');
                $da['Vc_bank_size']=$FL->requeststr('Vc_bank_size',1,20,'融资规模');
                $da['Vc_contactor']=$FL->requeststr('Vc_contactor',1,20,'联系人');
                $da['Vc_contactor_phone']=$FL->requeststr('Vc_contactor_phone',1,20,'联系电话');
                $da['Vc_note']=$FL->requeststr('Vc_note',1,255,'备注');
            }

            //检验手机号
            $re=$FL->checkMobile($da['Vc_contactor_phone']);
            if(!$re){returnjson(array('err'=>-200,'msg'=>'输入正确的手机号'));}

            //添加主表
            $da['I_bankingID']=$B->addfirst($bank);
            if(!$da['I_bankingID']){ returnjson(array('err'=>-1,'msg'=>'添加主表失败'));}

            //添加详细表,申请
            $re=$B->addsecond($da,$company,$type);
            if($re<0){ returnjson(array('err'=>$re,'msg'=>'添加申请失败'));}

            returnjson(array('err'=>0,'msg'=>'ok'));

        }else{
            returnjson(array('Vc_company'=>$Vc_company));
        }
        break;

    //修改商票质押ticket申请
    case 'mdy':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=banking&w=mdy
         *
         * 编辑页面
         * 输入：需登录后访问
         * id:int 金融申请表id
         * I_banking_classID:int 金融申请类型
         * 输出：
         * 1存货质押inventory
         * data:array
         *      Vc_company:str 公司名
         *      Vc_name:str 存货品名
         *      Vc_grade:str 规格等级
         *      I_amount:str 数量
         *      N_price:str 市场价格
         *      Vc_address:str 存货地点
         *      Vc_owen_company:str 货权单位
         *      Vc_bank_cycle:str 融资周期
         *      Vc_bank_size;str 融资规模
         *      Vc_contactor:str 联系人
         *      Vc_contactor_phone 联系电话
         *      Vc_note:str 备注
         *
         * 2仓单质押warehouse
         * data:array
         *      Vc_company:str 公司名
         *      Vc_type:str 仓单类型标准/普通
         *      Vc_name:str 品名
         *      I_amount:str 数量
         *      N_price:str 市场价格
         *      Vc_warehouse:str 仓库名称
         *      Vc_bailor:str 存货人
         *      Vc_keeper:str 仓管人
         *      I_secure:int 是否购买保险1/0
         *      Vc_bank_size;str 融资规模
         *      Vc_bank_cycle;str 融资周期
         *      Vc_contactor:str 联系人
         *      Vc_contactor_phone 联系电话
         *      Vc_note:str 备注
         *
         * 3商票质押ticket
         * data:array
         *      Vc_company:str 公司名
         *      Vc_drawer:str 出票人
         *      Dt_issuedate:datetime 出票日
         *      N_price:str 金额
         *      Dt_enddate:datetime 到期日
         *      Vc_holder:str 持票人
         *      I_sure:int 银行是否保兑1/0
         *      Vc_products:str 贸易产品
         *      Vc_size:str 贸易规模
         *      Vc_bank_cycle:str 融资周期
         *      Vc_bank_size:str 融资规模
         *      Vc_contactor:str 联系人
         *      Vc_contactor_phone 联系电话
         *      Vc_note:str 备注
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         *
         * 编辑提交页面
         * 输入：需登录后访问
         * id:int 详细表id
         * I_bankingID:int 金融申请表id
         * I_banking_classID:int 金融申请类型
         *
         * 1存货质押inventory:
         * Vc_company:str 公司名
         * Vc_name:str 存货品名
         * Vc_grade:str 规格等级
         * I_amount:str 数量
         * N_price:str 市场价格
         * Vc_address:str 存货地点
         * Vc_owen_company:str 货权单位
         * Vc_bank_cycle:str 融资周期
         * Vc_bank_size;str 融资规模
         * Vc_contactor:str 联系人
         * Vc_contactor_phone 联系电话
         * Vc_note:str 备注
         *
         * 2仓单质押warehouse
         * Vc_type:str 仓单类型标准/普通
         * Vc_name:str 品名
         * I_amount:str 数量
         * N_price:str 市场价格
         * Vc_warehouse:str 仓库名称
         * Vc_bailor:str 存货人
         * Vc_keeper:str 仓管人
         * I_secure:int 是否购买保险1/0
         * Vc_bank_cycle;str 融资规模
         * Vc_contactor:str 联系人
         * Vc_contactor_phone 联系电话
         * Vc_note:str 备注
         *
         * 3商票质押ticket
         * Vc_company:str 公司名
         * Vc_drawer:str 出票人
         * Dt_issuedate:datetime 出票日
         * N_price:str 金额
         * Dt_enddate:datetime 到期日
         * Vc_holder:str 持票人
         * I_sure:int 银行是否保兑1/0
         * Vc_products:str 贸易产品
         * Vc_size:str 贸易规模
         * Vc_bank_cycle:str 融资周期
         * Vc_bank_size:str 融资规模
         * Vc_contactor:str 联系人
         * Vc_contactor_phone 联系电话
         * Vc_note:str 备注
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        //申请提交
        if(isset($_REQUEST['submit'])){
            $id=$FLib->requestint('id',0,'详细表id',1) ;
            $bid=$FLib->requestint('I_bankingID',0,'金融申请表id',1) ;
            $type=$FLib->requestint('I_banking_classID',0,'金融申请类型',1) ;
            if($type==1){
                $da['Vc_name']=$FL->requeststr('Vc_name',1,20,'存货品名');
                $da['Vc_grade']=$FL->requeststr('Vc_grade',1,20,'规格等级');
                $da['I_amount']=$FL->requeststr('I_amount',1,20,'数量');
                $da['N_price']=$FL->requeststr('N_price',1,20,'市场价格');
                $da['Vc_address']=$FL->requeststr('Vc_address',1,255,'存货地点');
                $da['Vc_owen_company']=$FL->requeststr('Vc_owen_company',1,50,'货权单位');
                $da['Vc_bank_cycle']=$FL->requeststr('Vc_bank_cycle',1,20,'融资周期');
                $da['Vc_bank_size']=$FL->requeststr('Vc_bank_size',1,20,'融资规模');
                $da['Vc_contactor']=$FL->requeststr('Vc_contactor',1,20,'联系人');
                $da['Vc_contactor_phone']=$FL->requeststr('Vc_contactor_phone',1,20,'联系电话');
                $da['Vc_note']=$FL->requeststr('Vc_note',1,255,'备注');
            }
            if($type==2){
                $da['Vc_type']=$FL->requeststr('Vc_type',1,20,'仓单类型标准/普通');
                $da['Vc_name']=$FL->requeststr('Vc_name',1,20,'品名');
                $da['I_amount']=$FL->requeststr('I_amount',1,20,'数量');
                $da['N_price']=$FL->requeststr('N_price',1,20,'市场价格');
                $da['Vc_warehouse']=$FL->requeststr('Vc_warehouse',1,255,'仓库名称');
                $da['Vc_bailor']=$FL->requeststr('Vc_bailor',1,20,'存货人');
                $da['Vc_keeper']=$FL->requeststr('Vc_keeper',1,20,'仓管人');
                $da['I_secure']=$FLib->requestint('I_secure',0,'是否购买保险1/0',1) ;
                $da['Vc_bank_cycle']=$FL->requeststr('Vc_bank_cycle',1,20,'融资周期');
                $da['Vc_bank_size']=$FL->requeststr('Vc_bank_size',1,20,'融资规模');
                $da['Vc_contactor']=$FL->requeststr('Vc_contactor',1,20,'联系人');
                $da['Vc_contactor_phone']=$FL->requeststr('Vc_contactor_phone',1,20,'联系电话');
                $da['Vc_note']=$FL->requeststr('Vc_note',1,255,'备注');
            }
            if($type==3){
                $da['Vc_drawer']=$FL->requeststr('Vc_drawer',1,20,'出票人');
                $da['Dt_issuedate']=$FL->requeststr('Dt_issuedate',1,20,'出票日');
                $da['N_price']=$FL->requeststr('N_price',1,20,'金额');
                $da['Dt_enddate']=$FL->requeststr('Dt_enddate',1,20,'到期日');
                $da['Vc_holder']=$FL->requeststr('Vc_holder',1,20,'持票人');
                $da['I_sure']=$FLib->requestint('I_sure',0,'银行是否保兑1/0',1) ;
                $da['Vc_products']=$FL->requeststr('Vc_products',1,20,'贸易产品');
                $da['Vc_size']=$FL->requeststr('Vc_size',1,20,'贸易规模');
                $da['Vc_bank_cycle']=$FL->requeststr('Vc_bank_cycle',1,20,'融资周期');
                $da['Vc_bank_size']=$FL->requeststr('Vc_bank_size',1,20,'融资规模');
                $da['Vc_contactor']=$FL->requeststr('Vc_contactor',1,20,'联系人');
                $da['Vc_contactor_phone']=$FL->requeststr('Vc_contactor_phone',1,20,'联系电话');
                $da['Vc_note']=$FL->requeststr('Vc_note',1,255,'备注');
            }

            $re=$FL->checkMobile($da['Vc_contactor_phone']);
            if(!$re){returnjson(array('err'=>-200,'msg'=>'输入正确的手机号'));}

            $re=$B->edit($da,$id,$bid,$type);
            if(!$re){ returnjson(array('err'=>-1,'msg'=>'修改失败'));}
            returnjson(array('err'=>0,'msg'=>'ok'));
        }else{
            //回显金融申请数据
            $bid=$FLib->requestint('id',0,'金融申请表id',1) ;
            $type=$FLib->requestint('I_banking_classID',0,'金融申请类型',1) ;
            $re= $B->getOne($bid,$type);
            if($re){
                $re['Vc_company']=$Vc_company;
                $re['I_banking_classID']=$type;
                returnjson(array('err'=>0,'msg'=>'ok','data'=>$re));
            }else{
                returnjson(array('err'=>-1,'msg'=>'无数据','data'=>$re));
            }
        }
        break;
    //展示金融申请
    case 'list':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=banking&w=list
         * 输入：需登录后访问
         * (可无)
         * cpage:int 当前页码
         * psize;int 每页条数
         * I_status:int 申请状态
         * starttime:str 开始时间   
         * endtime:str 结束时间
         * 输出：array
         * startime:str 开始时间
         * endtime:str 结束时间
         * I_status:str 状态
         * pagestr:str 页码信息
         * data:array  分页内容
         *    page:int  当前页
         *    count:int  统计条数
         *    pcount:int  总页数
         *    data:array  数据
         *       id:int 申请主表id
         *       I_banking_classID:int 申请金融表类型
         *       I_status:int 申请状态
         *       status:int 申请状态 中文
         *       Createtime:date 申请时间
         *       Vc_name:str 金融申请名
         * */
        $re=$user->getCompanyStatus($uid);
        if($re['I_status']!=30){returnjson(array('err'=>-5,'msg'=>'请先完成公司认证'));}

        //获取页码
        $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):5;

        //获取查询内容
        $da['I_status']=$FLib->requestint('I_status',0,'申请状态',1) ;
        $da['starttime']=$FLib->requeststr('starttime',1,50,'开始时间',1);
        $da['endtime']=$FLib->requeststr('endtime',1,50,'结束时间',1);
        //分页展示
        $re=$B->getPages($page, $psize, $da,$uid);
        $page = $re['page'];
        $pcount = $re['pcount'];
        $p['startime']=$da['starttime'];
        $p['endtime']=$da['endtime'];
        $p['I_status']=$da['I_status'];
        $p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=user&m=banking&w=list&starttime={$da['starttime']}&endtime={$da['endtime']}");
        $p['data'] = $re;
        break;
    //查看申请
    case 'show':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=banking&w=show
         * 输入：需登录后访问
         * id:int 金融申请表id
         * I_banking_classID:int 金融申请类型
         * 输出：array
         *
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        $bid=$FLib->requestint('id',0,'金融申请表id',1) ;
        $type=$FLib->requestint('I_banking_classID',0,'金融申请类型',1) ;
        $re= $B->getOne($bid,$type);
        if($re){
            returnjson(array('err'=>0,'msg'=>'ok','data'=>$re));
        }else{
            returnjson(array('err'=>-1,'msg'=>'无数据'));
        }
        break;
    //删除申请
    case 'delete':
        /**
         * @author zy
         * 删除要删除两处
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=banking&w=delete&id=12&I_banking_classID=3
         * 输入：需登录后访问
         * id:int 金融申请表id
         * I_banking_classID:int 金融申请类型
         * 输出：array
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        $bid=$FLib->requestint('id',0,'金融申请表id',1) ;
        $type=$FLib->requestint('I_banking_classID',0,'金融申请类型',1) ;
        $re= $B->deleteBanking($bid,$uid,$type);
        if($re<0){ returnjson(array('err'=>$re,'msg'=>'删除失败'));}
        returnjson(array('err'=>0,'msg'=>'ok'));
        break;
    //再次申请
    case 'applyagain':
        /**
         * @author zy
         * 删除要删除两处
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=banking&w=applyagain&id=19&I_banking_classID=3
         * 输入：需登录后访问
         * id:int 金融申请表id
         * I_banking_classID:int 金融申请类型
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        $bid=$FLib->requestint('id',0,'金融申请表id',1) ;
        $type=$FLib->requestint('I_banking_classID',0,'金融申请类型',1) ;
        //修改后的id
        $nid=$B->getcolume('I_editNO'," I_bankingID=$bid",$type);
        echo $nid;
        if(!$nid){returnjson(array('err'=>-1,'msg'=>'请修改再提交'));}
        //再次提交申请
        $re=$B->again($nid,$type,$Vc_company,$uid);
        //修改申请修改状态
        $re=$B->setcolume(array('I_editNO'=>0),"I_bankingID=$bid",$type);
        if(!$re){ returnjson(array('err'=>-1,'msg'=>'删除失败'));}
        returnjson(array('err'=>0,'msg'=>'ok'));
        break;
}