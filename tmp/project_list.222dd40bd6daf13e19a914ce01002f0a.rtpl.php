<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>会员中心 - 我的项目</title>
</head>
<body>
  <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/header") . ( substr("inc/usercenter/header",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/header") );?>
    <!-- 中间内容部分 -->
    <div id="content">
        <div class="w1100 c cb">
			<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/side") . ( substr("inc/usercenter/side",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/side") );?>
            <!-- 内容区 -->
            <div class="fr content-main">
            	<div class="management-wrapper">
            		<div class="crumblenav">
            			<a href="#">项目管理</a><span class="current">我的项目</span>
            		</div>
            		<div class="management-wrapper-inner">
            			<div class="tab-menu">
            				<ul>
	        					<li class="active">在执行项目</li>
	        					<li>已完成项目</li>
	        				</ul>
            			</div>
        				<div class="oh pr w tab-item">
        					<ul class="pr cb fl-li w110">
								<?php $counter1=-1; if( isset($data["data"]) && is_array($data["data"]) && sizeof($data["data"]) ) foreach( $data["data"] as $key1 => $value1 ){ $counter1++; ?>
	        					<li>
	        						<p>项目名称：<span><?php echo $value1["Vc_name"];?></span></p>
	        						<p>项目业主：<span><?php echo $value1["Vc_admin"];?></span></p>
	        						<p>项目地址：<?php echo $value1["Vc_address"];?></p>
	        						<p>联&nbsp;&nbsp;系&nbsp;人：<?php echo $value1["Vc_contact"];?></p>
	        						<p>垫资金额：￥<?php echo $value1["N_loan_amount"];?>万<a href="##" class="fr"><?php echo $value1["Vc_name"];?></a></p>
	        						<a href="##" class="edit-btn pa"><img src="tpl/user/../image/user/edit.png" width="20" height="20"></a>
	        					</li>
								<?php } ?>
	        				</ul>
        				</div>
            		</div>
            	</div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>