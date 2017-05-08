<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>需求管理 - 新增物流需求</title>
</head>
<body>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/header") . ( substr("inc/usercenter/header",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/header") );?>
    <!-- 中间内容部分 -->
    <div id="content">
        <div class="w1100 c cb">
			<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/side") . ( substr("inc/usercenter/side",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/side") );?>
            <!-- 内容区 -->
            <div class="fr content-main">
            	<div class="crumblenav">
        			<a href="#">需求管理</a><a href="#">我的物流需求</a><span class="current">发布物流需求</span>
        		</div>
            	<div class="content-main-con">
					<form class="tender-form" id="logistics-form">
						<ul>
							<li><label class="title">物流货品名称</label><input type="text" name="Vc_name" data-rule="物流货品名称:required;"></li>
							<li><label class="title">数量</label><input type="text" name="Vc_amount" data-rule="数量:required;integer(+);"></li>
							<li><label class="title">运输时间</label><input type="text" name="D_transtime" data-type="datepicker" data-rule="运输时间:required;date;"></li>
							<li><label class="title">发货地</label><input type="text" name="Vc_send" data-rule="发货地:required;"></li>
							<li><label class="title">收货地</label><input type="text" name="Vc_get" data-rule="收货地:required;"></li>
							<li><label class="title">联系人</label><input type="text" name="Vc_contact" data-rule="联系人:required;"></li>
							<li><label class="title">联系电话</label><input type="text" name="Vc_contact_phone" data-rule="联系电话:required;mobile;"></li>
							<li><label class="title"></label><button type="submit">提交</button></li>
						</ul>
					</form>
                </div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
    <!-- 中间内容部分 -->
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>
