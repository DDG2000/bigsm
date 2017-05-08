<?php
if(!isset($m))exit;
require_once(WEBROOTINCCLASS . 'Concentrated.php');
$C=new Concentrated();
require_once(WEBROOTINCCLASS.'Shop.php');
$objShop = new Shop();
$w=$FL->requeststr('w',1,0,'w',1,1);
$m.='_'.$w;
switch($w) {
    case 'test':

        break;
    //获取城市列表
    case 'citylist':
        /**
         * @author zy
         * 城市地区搜索列表接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=concentrateindex&w=citylist
         * 输入：
         * I_provinceID: int 省id
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * message: 提示信息
         * 以下仅在err为0时会返回
         * citylist: array 城市列表
         *
         * */


        $I_provinceID=$FLib->requestint('I_provinceID',0,'省id',1);

        if(!$I_provinceID){returnjson(array('err'=>-1,'msg'=>'参数有误'));}

        $data['err']=0;
        $sql="SELECT ID,Vc_city  from site_city a,
        (SELECT I_cityID  from sm_commodity_steel WHERE I_provinceID={$I_provinceID} and Status=1 GROUP BY I_cityID) b 
        WHERE a.ID=b.I_cityID";
        $data['citylist']= $Db->fetch_all_assoc($sql);
        returnjson($data);

        break;
    //获取搜索结果
    case 'searchlist':
        /**
         * @author zy
         * 搜索结果
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=concentrateindex&w=searchlist
         * 输入：
         * I_requirementClassID;int 需求主表id 1产品  2招标  3物流  4融资
         * I_provinceID: int 省/直辖市id
         * I_cityID：int 城市id
         * starttime:str 开始时间
         * endtime:str 结束时间
         * keyword:str 关键字
         * cpage:int 当前的页数,默认为1
         * psize: int 数据分页量,默认为8
         *
         * 输出：
         * startime:str 开始时间
         * endtime:str 结束时间
         * I_requirementClassID:int 需求类
         * I_provinceID:int 省id
         * I_cityID:int 城市id
         * keyword:str 关键字
         * pagestr:str页码信息
         * data:array 分页信息
         *      page: int 当前页数
         *      count: string 数据总量
         *      pcount: int 总页数
         *      total:int 总条数
         *      num1:int 型材集采数
         *      num2:int 建材集采数
         *      data:arrray 集采详情
         *          id;int 集采编号(隐藏域)
         *          shopcount:int 商家报名数
         *          Vc_name:str 集采名
         *          I_itemClassId:int 集采类型
         *          Vc_itemIds:str 集采品名号
         *          Vc_itemnames:str 集采品名
         *          N_weight:int 集采重量
         *          address:集采地址
         *          D_start:str 开始时间
         *          D_end:str 结束时间
         *          Vc_doc:str 标书
         */

        $type=$FLib->requestint('I_itemClassId',0,'所属分类',1);
        $I_provinceID=$FLib->requestint('I_provinceID',0,'省/直辖市id',1);
        $I_cityID=$FLib->requestint('I_cityID',0,'城市id',1);

        $starttime=$FLib->requeststr('starttime',1,50,'开始时间',1);
        $endtime=$FLib->requeststr('endtime',1,50,'结束时间',1);

        $keyword=$FLib->requeststr('keyword',1,50,'关键字',1);

        $page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $psize=isset($_REQUEST['psize'])?intval($_REQUEST['psize']):8;

        if(!is_numeric($page) || !is_numeric($psize)) {
            returnjson(array('err'=>-1,'msg'=>'数据不合法'));
        }

//        $sql="select c.id,e.id as rid,
//i.Vc_name as commodityname,i.Vc_province,i.Vc_city,i.I_payType,
//k.Vc_name as tendername,k.D_start,k.D_end,k.Vc_excel,
//j.Vc_name as logisticsname,j.Vc_send,j.Vc_get,j.D_transtime,
//e.Vc_name as financename,e.N_money,e.Vc_deadline,e.Vc_rate,
//c.I_publish_status, d.Vc_name as Vc_company,c.Createtime,c.I_requirementClassID from
//(select a.*,b.I_companyID,b.I_provinceID,b.I_cityID from sm_requirement a left join user_base b on a.I_userID=b.ID
//where a.Status=1 and a.I_publish_status <>1 and $ext_condition) c
//left join sm_company d on d.id=c.I_companyID
//left join (select f.*,g.Vc_province,h.Vc_city from sm_requirement_commodity f left join site_province g on g.ID=f.I_provinceID
//left join site_city h on h.ID=f.I_cityID) i on i.I_requirementID=c.id
//left join sm_requirement_tender k on k.I_requirementID=c.id
//left join sm_requirement_logistics j on j.I_requirementID=c.id
//left join sm_requirement_finance e on e.I_requirementID=c.id
//{$wheresql} {$order}";

        //导航点击搜索条件
        $ext_condition = " a.Status=1 and a.I_status <>10 ";

        if($type){$ext_condition.=" and a.I_itemClassId=$type ";}

        if($starttime){$ext_condition.=" and a.Createtime>$starttime ";}

        if($endtime){$ext_condition.=" and a.Createtime>$endtime ";}

        if($I_provinceID){$ext_condition.=" and a.I_provinceID = $I_provinceID ";}

        if($I_cityID){$ext_condition.=" and a.I_cityID = $I_cityID ";}

        //模糊查询搜索条件
        $wheresql='where 1=1  ';

        if($keyword!=''){$wheresql.=" and (a.Vc_itemnames like '%".$keyword."%' or a.Vc_name like '%".$keyword."%' 
        or a.N_weight like '%".$keyword."%' or concat(c.Vc_province,d.Vc_city,e.Vc_district,a.Vc_address) like '%".$keyword."%' )";}

        $order= " order by c.Createtime desc  ";


        //关联多表查询sql--测试验证用
        $data= $C->getHomeDataListByPage($page, $psize, $ext_condition, $wheresql, $order);
        $page = $data['page'];
        $pcount = $data['pcount'];
        $p['startime']=$starttime;
        $p['endtime']=$endtime;
        $p['I_requirementClassID']=$type;
        $p['I_provinceID']=$I_provinceID;
        $p['I_cityID']=$I_cityID;
        $p['keyword']=$keyword;
        $p['data']=$data;
        $p['pagestr'] = getPageStrFunSd($pcount, $page, "&act=concentrated&m=searchlist&starttime={$starttime}&endtime={$endtime}");
        break;
    //集采报名提交
    case 'signup':
        /**
         * @author zy
         * 集采报名接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=concentrateindex&w=signup
         * 输入：
         * 集采id: int 集采id
         * company:str 公司名
         * contact:str 联系人
         * phone:str 联系电话
         * 输出：
         * err:int 结果状态 -1失败 0成功
         * message: 提示信息
         * */
        //未完成公司认证的,无法报名
        $shopID=$objShop->getsid($I_userID);
        
        if(!$lg){returnjson(array('msg'=>'请先登录','err'=>-1,'url'=>'/index.php?act=user&m=public&w=login'));}
        if(!$shopID){
            returnjson(array('err'=>-1,'msg'=>'请先完成认证'));
        }
        $da['I_cpID']=$FLib->requestint('id',0,'集采id',1);
        $da['I_shopID']=$lg['I_companyID'];
        $da['company']=$FLib->requeststr('company',1,50,'公司名',1);
        $da['contact']=$FLib->requeststr('contact',1,50,'联系人',1);
        $da['phone']=$FLib->requeststr('phone',1,50,'联系电话',1);
        $rs=$C->addRecord($da);
        if($rs){
            returnjson(array('err'=>0,'msg'=>'报名成功！'));
        }else{
            returnjson(array('err'=>-1,'msg'=>'报名失败！'));
        }
        break;

    case 'detail':
        /**
         * @author wh
         * 查看详情接口
         * url地址：
         * http://www.bigsm.com/index.php?act=user&m=concentrateindex&w=detail
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
        $r=$C->getInfo2($id);
        $p['data']=$r;
        break;
}