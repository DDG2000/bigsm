<?php if(!class_exists('raintpl')){exit;}?> <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/css") . ( substr("inc/css",-1,1) != "/" ? "/" : "" ) . basename("inc/css") );?>
<title>钢材市场 - 招标需求</title>
</head>

<body>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/header") . ( substr("inc/header",-1,1) != "/" ? "/" : "" ) . basename("inc/header") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/mainnav") . ( substr("inc/mainnav",-1,1) != "/" ? "/" : "" ) . basename("inc/mainnav") );?>
    <!-- 中间部分 -->
    <div id="container" class="oh w1100 c cb">
        <div class="fl need-wrapper">
            <div class="need-filter fl-li fs0">
                <div class="row" line="1">
                    <span class="ib title vmm">类型：</span>
                    <a href="#" class="all active ib vmm">全部</a>
                    <ul class="ib vmm db-a">
                        <li><a href="#">产品需求</a></li>
                        <li><a href="#">招标需求</a></li>
                        <li><a href="#">融资需求</a></li>
                        <li><a href="#">物流需求</a></li>
                    </ul>
                </div>
                <div class="row" line="2">
                    <span class="ib title vmt">区域：</span>
                    <a href="#" class="ib active vmt all">全部</a>
                    <ul class="ib db-a">
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                        <li><a href="#">成都</a></li>
                    </ul>
                </div>
            </div>
            <div class="need-head">
                <span class="s1">发布信息共<em><?php echo $data["total"];?></em>条</span><span>招标<em><?php echo $data["tennum"];?></em>条</span><span>融资<em><?php echo $data["finnum"];?></em>条</span><span>物流<em><?php echo $data["lognum"];?></em>条</span><span>产品<em><?php echo $data["comnum"];?></em>条</span>
                <form action="##" class="ib fs0 vmm">
                    <div class="ib select">
                        <input type="text" value="2015-01-01" data-type="datepicker"/>
                    </div>
                    <em class="ib space">至</em>
                    <div class="ib select">
                        <input type="text" value="2015-01-01" data-type="datepicker"/>
                    </div>
                    <input type="text" value="" class="search" placeholder="关键字搜索"/>
                    <button class="ib vmm" type="submit">搜索</button>
                </form>
            </div>
            <div class="need-con">
                <ul>
                    <?php $counter1=-1; if( isset($data["data"]) && is_array($data["data"]) && sizeof($data["data"]) ) foreach( $data["data"] as $key1 => $value1 ){ $counter1++; ?> 
                        <?php if( $value1["I_requirementClassID"]==1 ){ ?>
                            <li class="cb" type="4">
                                <span class="pa title">产品</span>
                                <p>产品名称：<?php echo $value1["commodityname"];?></p>
                                <p>公司名称：<?php echo $value1["Vc_company"];?></p>
                                <p>交货地：<?php echo $value1["Vc_province"];?><?php echo $value1["Vc_city"];?></p>
                                <p>付款方式：现款</p>
                                <span class="pa date"><?php echo formatTime($value1["Createtime"],'Y-m-d H:i'); ?></span>
                                <span class="pa" status="1">报价中</span>
                                <a href="http://www.bigsm.com/index.php?act=requirement&m=detail&id=<?php echo $value1["id"];?>" class="pa product" btn="read">查看</a>
                            </li>
                        <?php }elseif( $value1["I_requirementClassID"]==2 ){ ?>
                            <li class="cb" type="1">
                                <span class="pa title">招标</span>
                                <p>招标项目：<?php echo $value1["tendername"];?></p>
                                <p>公司名称：<?php echo $value1["Vc_company"];?></p>
                                <p>招标时间：<?php echo formatTime($value1["D_start"],'Y-m-d H:i'); ?>—<?php echo formatTime($value1["D_end"],'Y-m-d H:i'); ?></p>
                                <p>招标书：<a href="<?php echo $value1["Vc_excel"];?>" class="down">下载</a></p>
                                <span class="pa date"><?php echo formatTime($value1["Createtime"],'Y-m-d H:i'); ?></span>
                                <span class="pa" status="1">招标中</span>
                                <a href="javascript:void(0);" class="pa" btn="read">查看</a>
                            </li>
                        <?php }elseif( $value1["I_requirementClassID"]==3 ){ ?>
                            <li class="cb" type="3">
                                <span class="pa title">物流</span>
                                <p>运输货品：<?php echo $value1["logisticsname"];?></p>
                                <p>发货地:<?php echo $value1["Vc_send"];?></p>
                                <p>收获地：<?php echo $value1["Vc_get"];?></p>
                                <p>运输时间：<?php echo $value1["D_transtime"];?></p>
                                <span class="pa date"><?php echo formatTime($value1["Createtime"],'Y-m-d H:i'); ?></span>
                                <span class="pa" status="1">招标中</span>
                                <a href="javascript:void(0);" class="pa" btn="read">查看</a>
                            </li>
                        <?php }elseif( $value1["I_requirementClassID"]==4 ){ ?>
                            <li class="cb" type="2">
                                <span class="pa title">融资</span>
                                <p>融资项目：<?php echo $value1["financename"];?></p>
                                <p>融资金额：<?php echo $value1["N_money"];?></p>
                                <p>融资期限：<?php echo $value1["Vc_deadline"];?></p>
                                <p>期望利率：<?php echo $value1["Vc_rate"];?></p>
                                <span class="pa date"><?php echo formatTime($value1["Createtime"],'Y-m-d H:i'); ?></span>
                                <span class="pa" status="1">招标中</span>
                                <a href="javascript:void(0);" class="pa" btn="read">查看</a>
                            </li>
                        <?php } ?> 
                    <?php } ?>
                </ul>
                <div id="pagestr">
                    <?php echo $pagestr;?>
                </div>
            </div>
        </div>
        <div class="fr need-side">
            <div class="need-pub">
                <div class="head">发布需求</div>
                <div class="con cb fl-a">
                    <a href="#" type="5">特殊材料</a><a href="#" type="3">产品</a><a href="#" type="1">招标</a><a href="#" type="2">融资</a><a href="#" type="4">物流</a>
                    <a href="#" class="pa" btn>去发布&nbsp;>></a>
                </div>
            </div>
            <!-- 成交动态 -->
            <div class="deel">
                <div class="head">成交动态</div>
                <div class="db-a con">
                    <ul>
                        <?php $counter1=-1; if( isset($comfired) && is_array($comfired) && sizeof($comfired) ) foreach( $comfired as $key1 => $value1 ){ $counter1++; ?>
                        <li>
                            <a href="##">
                                <div class="name"><?php echo $value1["Vc_mobile"];?></div>
                                <p>以<em><?php echo $value1["price"];?>元 / <?php echo $value1["I_unitType"]==1?'件':$value1["I_unitType"]==2?'根':'吨';?></em>的价格成交<em>260<?php echo $value1["I_unitType"]==1?'件':$value1["I_unitType"]==2?'根':'吨';?></em></p>
                                <p><?php echo $value1["Vc_item"];?>&nbsp;<?php echo $value1["Vc_stuff"];?>&nbsp;&nbsp;<?php echo $value1["Vc_specification"];?>&nbsp;&nbsp;<span class="pa time"><?php echo $value1["Createtime"];?></span></p>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <!-- 成交动态 -->
        </div>
    </div>
    <!-- 中间部分 -->
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>