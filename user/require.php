<?php
if(!defined('WEBROOT'))exit;
require_once(WEBROOTINCCLASS . 'Requirement.php');
$R=new Requirement();
require_once(WEBROOTINC.'File.class.php');
$Fc = new FileClass();

if(empty($uid)){returnjson(array('err'=>-1,'msg'=>'获取用户参数失败'));}

//公司认证判断
$user = new User();
$re=$user->getCompanyStatus($uid);
if($re['I_status']!=30){returnjson(array('err'=>-5,'msg'=>'请先完成公司认证'));}

$w=$FL->requeststr('w',1,0,'w',1,1);
$m.='_'.$w;
switch($w) {
    case 'test':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=require&w=test
         * 输入：Vc_mobile:str 手机号
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        break;
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>招标需求tender<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    //发布招标需求
    case 'addtender':
        /**
         * @author zy
         * url地址：需登录
         * http://www.bigsm.com/index.php?act=user&m=require&w=addtender
         * http://www.bigsm.com/index.php?act=user&m=require&w=addtender&Vc_name=华西一建设&D_start=2015-11-6&D_end=2015-12-9&Vc_contact=张三&Vc_contact_phone=17777777777&submit=
         * 添加招标需求页面
         * 输入:
         * 输出:
         *
         * 提交请求
         * 输入：
         * Vc_name:str 招标项目名称
         * D_start:datetime 招标开始时间
         * D_end:datetime 招标结束时间
         * Vc_excel:str  excel表单路径
         * Vc_contact:str 联系人
         * Vc_contact_phone:str 联系电话
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        if(isset($_REQUEST['submit'])){
            //主表添加数据
            $req['I_userID']=$uid;//发标人id
            $req['I_requirementClassID']=2;//需求类型id：1-产品需求
            //招标表中数据
            $reqire['Vc_name']=$FL->requeststr('Vc_name',0,100,'招标项目名称');
            $reqire['D_start']=$FL->requeststr('D_start',0,100,'招标开始时间');
            $reqire['D_end']=$FL->requeststr('D_end',0,100,'招标结束时间');
//            $reqire['Vc_excel']=$FL->requeststr('Vc_excel',0,100,'excel表单路径');
            $reqire['Vc_contact']=$FL->requeststr('Vc_contact',0,100,'联系人');
            $reqire['Vc_contact_phone']=$FL->requeststr('Vc_contact_phone',0,100,'联系电话');
            //添加主表,返回添加后id
            $reqire['I_requirementID']=$R->addfirst($req);
            if(!$reqire['I_requirementID']){ returnjson(array('err'=>-1,'msg'=>'添加需求失败'));}
            //添加招标表数据
            $re=$R->addsecond($reqire,2);
            if(!$re){ returnjson(array('err'=>-2,'msg'=>'添加招标失败'));}
            returnjson(array('err'=>0,'msg'=>'ok'));

        }else{

        }
        break;
    //分页展示招标需求
    case 'listtender':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=require&w=listtender
         * 输入：需登录
         * cpage:int 页数
         * psize:int 每页显示条数
         * 输出：
         * startime:datetime 开始时间
         * endtime:endtime 结束时间
         * pagestr:str 页码信息
         * data:array 数据
         *    page:int  当前页
         *    count:int  统计条数
         *    pcount:int  总页数
         *    data:array  招标需求
         * */
        //获取页码
        $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):15;
        
        $sqlw='a.Status=1 and b.Status=1 and a.I_requirementClassID=2 and a.I_userID='.$uid;
        //查询时间
        $endtime = $FL->requeststr('endtime',1,10,'结束时间');
        $starttime  = $FL->requeststr('starttime',1,10,'开始时间');
        if($starttime)$sqlw .= " and b.Createtime >= '".$starttime."'";
        if($endtime)$sqlw .= " and  b.Createtime <= '".$endtime."'";
        $order='b.Createtime desc';
        //获取分页数据
        $da=$R->getDataListByPage($page, $psize, $sqlw, $order,2);
        //模板输出数据
        $page = $da['page'];
        $count = $da['count'];
        $pcount = $da['pcount'];
        $p['startime']=$starttime;
        $p['endtime']=$endtime;
        $p['data'] = $da;
        $p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=user&m=require&w=listtender&starttime={$starttime}&endtime={$endtime}");

        break;
    //修改招标需求
    case 'mdytender':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=require&w=mdytender
         * 修改展示回显招标需求
         * 输入:
         * id:int  招标id
         * 输出：
         * array()
         * Vc_name:str 招标项目名称
         * D_start:datetime 招标开始时间
         * D_end:datetime 招标结束时间
         * Vc_excel:str  excel表单路径
         * Vc_contact:str 联系人
         * Vc_contact_phone:str 联系电话
         *
         * 修改提交
         * 输入：需登录
         * id:int  招标id
         * Vc_name:str 招标项目名称
         * D_start:datetime 招标开始时间
         * D_end:datetime 招标结束时间
         * Vc_excel:str  excel表单路径
         * Vc_contact:str 联系人
         * Vc_contact_phone:str 联系电话
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        //获取招标修改id
        if(isset($_REQUEST['submit'])){
            $rid=$FLib->requestint('id',0,'招标id',1);
            $reqire['Vc_name']=$FL->requeststr('Vc_name',0,100,'招标项目名称');
            $reqire['D_start']=$FL->requeststr('D_start',0,100,'招标开始时间');
            $reqire['D_end']=$FL->requeststr('D_end',0,100,'招标结束时间');
            $reqire['Vc_excel']=$FL->requeststr('Vc_excel',0,100,'excel表单路径');
            $reqire['Vc_contact']=$FL->requeststr('Vc_contact',0,100,'联系人');
            $reqire['Vc_contact_phone']=$FL->requeststr('Vc_contact_phone',0,100,'联系电话');
            //跟新招标需求表,2代表需求表
            $re=$R->setcolume($reqire,"id=$rid",2);
            if(!$re){ returnjson(array('err'=>-1,'msg'=>'修改招标需求失败'));}
            returnjson(array('err'=>0,'msg'=>'ok'));
        }else{
            $rid=$FLib->requestint('id',0,'招标id',1);
            $re=$R->getone($rid,2);
            returnjson($re);
        }
        break;
    //删除招标需求,删除主表和分表
    case 'deletetender':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=require&w=deletetender
         * 输入：需登录
         * id:int  招标id
         * I_requirementID:int 需求主表id
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        $rid1=$FLib->requestint('id',0,'招标id',1);
        $rid2=$FLib->requestint('I_requirementID',0,'招标id',1);
        if($rid1==0 || $rid2==0){returnjson(array('err'=>-1,'msg'=>'参数缺失'));}
        $re1=$R->setcolume(array('Status'=>0),"id=$rid1",2);
        $re2=$R->setcolume(array('Status'=>0),"id=$rid2",'');
        if(!$re1 || !$re2){ returnjson(array('err'=>-2,'msg'=>'删除失败'));}
        returnjson(array('err'=>0,'msg'=>'ok'));
        break;

    //上传标书
    case 'upload':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=require&w=upload
         * 输入：需登录
         * file_exl:file  标书excel文件
         * 输出：
         * savePath:str  输出路径
         * */
        //接收文件
        if(!isset($_FILES['file_exl'])){
            returnjson(array('err'=>-1,'msg'=>'请选择文件'));
        }
        $p='file_exl';//上传文件变量名
        $path = '/upload/user/tmp/';
//             $path = WEBROOT.'/upload/user/tmp/';
        $T='xlsx|xls';
        $Z=3072000;//文件大小不能超过3M
        $r=$Fc->uplodefile($p,$path,$T,$Z);

        if($r=='no_type'){
            returnjson(array('err'=>-1,'msg'=>'文件类型不正确，只支持xls格式的Excel！'));
        }elseif ($r=='no_size'){
            returnjson(array('err'=>-1,'msg'=>'文件大小不能超过3M！'));
        }elseif ($r===false){
            returnjson(array('err'=>-1,'msg'=>'资源文件上传失败！'));
        }
        $savePath=WEBROOT.$r;//返回路径地址
        returnjson($savePath);
        break;
    //预览标书TODO
    case 'view':

        break;
    //下载标书TODO
    case '':

        break;
    //重新上传标书
    case 'reload':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=require&w=reload
         * 输入：需登录
         * id:int  招标id
         * Vc_excel:str 标书路径
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        $rid=$FLib->requestint('id',0,'招标id',1);
        $ecl=$FL->requeststr('Vc_excel',0,100,'excel表单路径');
        $re=$R->setcolume(array('Vc_excel'=>$ecl),"id=$rid",'');
        if(!$re){ returnjson(array('err'=>-1,'msg'=>'撤销失败'));}
        returnjson(array('err'=>0,'msg'=>'ok'));
        break;
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>融资需求finance<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    //发布融资需求
    case 'addfinance':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=require&w=addfinance
         * 添加融资需求页面
         * 输入:
         * 输出:
         *
         * 提交融资需求
         * 输入：需登录
         * Vc_name:str 融资项目名称
         * Vc_amount:str 融资金额
         * Vc_deadline:str 融资期限
         * Vc_rate:str 期望利率
         * Vc_contact:str 联系人
         * Vc_contact_phone:str 联系电话
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        if(isset($_REQUEST['submit'])){
            //主表添加数据
            $req['I_userID']=$uid;//发标人id
            $req['I_requirementClassID']=4;//需求类型id：1-产品需求
            //融资表中数据
            $reqire['Vc_name']=$FL->requeststr('Vc_name',0,200,'融资项目名称');
            $reqire['Vc_amount']=$FL->requeststr('Vc_amount',0,20,'融资金额');
            $reqire['Vc_deadline']=$FL->requeststr('Vc_deadline',0,20,'融资期限');
            $reqire['Vc_rate']=$FL->requeststr('Vc_rate',0,20,'期望利率');
            $reqire['Vc_contact']=$FL->requeststr('Vc_contact',0,100,'联系人');
            $reqire['Vc_contact_phone']=$FL->requeststr('Vc_contact_phone',0,100,'联系电话');
            //添加主表,返回添加后id
            $reqire['I_requirementID']=$R->addfirst($req);
            if(!$reqire['I_requirementID']){ returnjson(array('err'=>-1,'msg'=>'添加需求失败'));}
            //添加融资表数据,4代表融资表
            $re=$R->addsecond($reqire,4);
            if(!$re){ returnjson(array('err'=>-2,'msg'=>'添加金融需求失败'));}
            returnjson(array('err'=>0,'msg'=>'ok'));
        }else{

        }
        break;
    //展示融资需求
    case 'listfinance':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=require&w=listfinance
         * 输入：需登录
         * cpage:int 页数
         * psize:int 每页显示条数
         * 输出：
         * startime:datetime 开始时间
         * endtime:endtime 结束时间
         * pagestr:str 页码信息
         * data:array 数据
         *    page:int  当前页
         *    count:int  统计条数
         *    pcount:int  总页数
         *    data:array  融资需求
         * */
        //获取页码
        $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):15;

        $sqlw='a.Status=1 and b.Status=1 and a.I_requirementClassID=4 and a.I_userID='.$uid;
        //查询时间
        $endtime = $FL->requeststr('endtime',1,10,'结束时间');
        $starttime  = $FL->requeststr('starttime',1,10,'开始时间');
        if($starttime)$sqlw .= " and b.Createtime >= '".$starttime."'";
        if($endtime)$sqlw .= " and  b.Createtime <= '".$endtime."'";
        $order='b.Createtime desc';
        //获取分页数据,4代表融资表
        $da=$R->getDataListByPage($page, $psize, $sqlw, $order,4);
        $page = $da['page'];
        $count = $da['count'];
        $pcount = $da['pcount'];
        $p['startime']=$starttime;
        $p['endtime']=$endtime;
        $p['data'] = $da;
        $p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=user&m=require&w=listfinance&starttime={$starttime}&endtime={$endtime}");
        break;
    //修改融资需求
    case 'mdyfinance':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=require&w=mdyfinance
         * 修改回显页面展示
         * 输入：需登录
         * id:int  融资id
         * 输出：
         * array()
         * id:str 融资id
         * Vc_name:str 融资项目名称
         * I_requirementID:int 需求主表id
         * Vc_amount:str 融资金额
         * Vc_deadline:str 融资期限
         * Vc_rate:str 期望利率
         * Vc_contact:str 联系人
         * Vc_contact_phone:str 联系电话
         *
         * 修改提交
         * 输入
         * id:str 融资id
         * Vc_name:str 融资项目名称
         * I_requirementID:int 需求主表id
         * Vc_amount:str 融资金额
         * Vc_deadline:str 融资期限
         * Vc_rate:str 期望利率
         * Vc_contact:str 联系人
         * Vc_contact_phone:str 联系电话
         * 输出
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        //获取融资修改id
        if(isset($_REQUEST['submit'])){
            $rid=$FLib->requestint('id',0,'融资id',1);
            $reqire['Vc_name']=$FL->requeststr('Vc_name',0,200,'融资项目名称');
            $reqire['Vc_amount']=$FL->requeststr('Vc_amount',0,20,'融资金额');
            $reqire['Vc_deadline']=$FL->requeststr('Vc_deadline',0,20,'融资期限');
            $reqire['Vc_rate']=$FL->requeststr('Vc_rate',0,20,'期望利率');
            $reqire['Vc_contact']=$FL->requeststr('Vc_contact',0,100,'联系人');
            $reqire['Vc_contact_phone']=$FL->requeststr('Vc_contact_phone',0,100,'联系电话');
            //跟新融资需求表,2代表需求表
            $re=$R->setcolume($reqire,"id=$rid",4);
            if(!$re){ returnjson(array('err'=>-1,'msg'=>'修改融资需求失败'));}
            returnjson(array('err'=>0,'msg'=>'ok'));
        }else{
            $rid=$FLib->requestint('id',0,'融资需求id',1);
            //获取金融表中的一条数据
            $re=$R->getone($rid,4);
            returnjson($re);
        }
        break;
    //删除融资需求
    case 'deletefinance':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=require&w=deletefinance
         * 输入：需登录
         * id:int  融资id
         * I_requirementID:int
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        $rid1=$FLib->requestint('id',0,'融资id',1);
        $rid2=$FLib->requestint('I_requirementID',0,'需求主表id',1);
        if($rid1==0 || $rid2==0){returnjson(array('err'=>-1,'msg'=>'参数缺失'));}
        $re1=$R->setcolume(array('Status'=>0),"id=$rid1",4);
        $re2=$R->setcolume(array('Status'=>0),"id=$rid2",'');
        if(!$re1 || !$re2){ returnjson(array('err'=>-2,'msg'=>'删除失败'));}
        returnjson(array('err'=>0,'msg'=>'ok'));
        break;
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>物流需求logistics<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
//发布物流需求
    case 'addlogistics':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=require&w=addlogistics
         * http://www.bigsm.com/index.php?act=user&m=require&w=addlogistics&Vc_name=鞍钢、花纹板、槽钢、
        冷轧涂镀&Vc_amount=100吨&D_transtime=2015-9-6&Vc_send=成都市锦江区三
        色路100号&Vc_get=绵阳市锦江区三
        色路100号&Vc_contact=张三&Vc_contact_phone=17777777777&submit=
         * 添加融资需求页面
         * 输入:
         * 输出:
         *
         * 提交需求
         * 输入：需登录
         * Vc_name:str 物流货品名称
         * Vc_amount:str 数量
         * D_transtime:str 运输时间
         * Vc_send;str 发货地
         * Vc_get;str 收货地
         * Vc_contact:str 联系人
         * Vc_contact_phone:str 联系电话
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        if(isset($_REQUEST['submit'])){
            //主表添加数据
            $req['I_userID']=$uid;//发标人id
            $req['I_requirementClassID']=3;//需求类型id：1-产品需求
            //物流表中数据
            $reqire['Vc_name']=$FL->requeststr('Vc_name',0,200,'物流货品名称');
            $reqire['Vc_amount']=$FL->requeststr('Vc_amount',0,20,'数量');
            $reqire['D_transtime']=$FL->requeststr('D_transtime',0,20,'运输时间');
            $reqire['Vc_send']=$FL->requeststr('Vc_send',0,20,'发货地');
            $reqire['Vc_get']=$FL->requeststr('Vc_get',0,20,'收货地');
            $reqire['Vc_contact']=$FL->requeststr('Vc_contact',0,100,'联系人');
            $reqire['Vc_contact_phone']=$FL->requeststr('Vc_contact_phone',0,100,'联系电话');
            //添加主表,返回添加后id
            $reqire['I_requirementID']=$R->addfirst($req);
            if(!$reqire['I_requirementID']){ returnjson(array('err'=>-1,'msg'=>'添加需求失败'));}
            //添加物流表数据,3代表物流表
            $re=$R->addsecond($reqire,3);
            if(!$re){ returnjson(array('err'=>-2,'msg'=>'添加物流需求失败'));}
            returnjson(array('err'=>0,'msg'=>'ok'));
        }else{

        }
        break;
    //展示物流需求
    case 'listlogistics':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=require&w=listlogistics
         * 输入：需登录
         * cpage:int 页数
         * psize:int 每页显示条数
         * 输出：
         * startime:datetime 开始时间
         * endtime:endtime 结束时间
         * pagestr:str 页码信息
         * data:array 数据
         *    page:int  当前页
         *    count:int  统计条数
         *    pcount:int  总页数
         *    data:array  物流需求
         * */
        //获取页码
        $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):15;

        $sqlw='a.Status=1 and b.Status=1 and a.I_requirementClassID=3 and a.I_userID='.$uid;
        //查询时间
        $endtime = $FL->requeststr('endtime',1,10,'结束时间');
        $starttime  = $FL->requeststr('starttime',1,10,'开始时间');
        if($starttime)$sqlw .= " and b.Createtime >= '".$starttime."'";
        if($endtime)$sqlw .= " and  b.Createtime <= '".$endtime."'";
        $order='b.Createtime desc';
        //获取分页数据,3代表物流表
        $da=$R->getDataListByPage($page, $psize, $sqlw, $order,3);
        //模板输出数据
        $page = $da['page'];
        $count = $da['count'];
        $pcount = $da['pcount'];
        $p['startime']=$starttime;
        $p['endtime']=$endtime;
        $p['data'] = $da;
        $p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=user&m=require&w=listlogistics&starttime={$starttime}&endtime={$endtime}");
        break;
    //修改物流需求
    case 'mdylogistics':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=require&w=mdylogistics
         * 修改回显页面展示
         * 输入：需登录
         * id:int  物流id
         * 输出：
         * array()
         * Vc_name:str 物流货品名称
         * Vc_amount:str 数量
         * D_transtime:str 运输时间
         * Vc_send;str 发货地
         * Vc_get;str 收货地
         * Vc_contact:str 联系人
         * Vc_contact_phone:str 联系电话
         *
         * 修改提交
         * 输入
         * id:str 物流id
         * Vc_name:str 物流货品名称
         * Vc_amount:str 数量
         * D_transtime:str 运输时间
         * Vc_send;str 发货地
         * Vc_get;str 收货地
         * Vc_contact:str 联系人
         * Vc_contact_phone:str 联系电话
         * 输出
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        //获取物流修改id
        if(isset($_REQUEST['submit'])){
            $rid=$FLib->requestint('id',0,'物流id',1);
            $reqire['Vc_name']=$FL->requeststr('Vc_name',0,200,'物流货品名称');
            $reqire['Vc_amount']=$FL->requeststr('Vc_amount',0,20,'数量');
            $reqire['D_transtime']=$FL->requeststr('D_transtime',0,20,'运输时间');
            $reqire['Vc_send']=$FL->requeststr('Vc_send',0,20,'发货地');
            $reqire['Vc_get']=$FL->requeststr('Vc_get',0,20,'收货地');
            $reqire['Vc_contact']=$FL->requeststr('Vc_contact',0,100,'联系人');
            $reqire['Vc_contact_phone']=$FL->requeststr('Vc_contact_phone',0,100,'联系电话');
            //更新物流需求表,2代表需求表
            $re=$R->setcolume($reqire,"id=$rid",3);
            if(!$re){ returnjson(array('err'=>-1,'msg'=>'修改物流需求失败'));}
            returnjson(array('err'=>0,'msg'=>'ok'));
        }else{
            $rid=$FLib->requestint('id',0,'物流需求id',1);
            //获取金融表中的一条数据
            return $R->getone($rid,3);
        }
        break;
    //删除物流需求
    case 'deletelogistics':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=require&w=deletelogistics
         * 输入：需登录
         * id:int  物流id
         * I_requirementID:int
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        $rid1=$FLib->requestint('id',0,'物流id',1);
        $rid2=$FLib->requestint('I_requirementID',0,'物流主表id',1);
        if($rid1==0 || $rid2==0){returnjson(array('err'=>-1,'msg'=>'参数缺失'));}
        $re1=$R->setcolume(array('Status'=>0),"id=$rid1",3);
        $re2=$R->setcolume(array('Status'=>0),"id=$rid2",'');
        if(!$re1 || !$re2){ returnjson(array('err'=>-1,'msg'=>'删除失败'));}
        returnjson(array('err'=>0,'msg'=>'ok'));
        break;
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>公用部分<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    //撤销需求
    case 'cancel':
        /**
         * @author zy
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=require&w=cancel
         * 输入：需登录
         * id:int  需求总表id
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * msg: 提示信息
         * */
        $rid=$FLib->requestint('id',0,'需求总表id',1);
        $re=$R->setcolume(array('I_publish_status'=>3),"id=$rid",'');
        if(!$re){ returnjson(array('err'=>-1,'msg'=>'撤销失败'));}
        returnjson(array('err'=>0,'msg'=>'ok'));
        break;
}