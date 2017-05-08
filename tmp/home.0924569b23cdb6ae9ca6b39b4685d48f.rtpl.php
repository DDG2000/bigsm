<?php if(!class_exists('raintpl')){exit;}?> <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/shop/css") . ( substr("inc/shop/css",-1,1) != "/" ? "/" : "" ) . basename("inc/shop/css") );?>
<title>卖家中心- 首页</title>
</head>

<body>
	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/shopcenter/header") . ( substr("inc/shopcenter/header",-1,1) != "/" ? "/" : "" ) . basename("inc/shopcenter/header") );?>
	<!-- 中间内容部分 -->
	<div id="content">
		<div class="w1100 c cb">
			<!--   侧边菜单 -->
			<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/shopcenter/side") . ( substr("inc/shopcenter/side",-1,1) != "/" ? "/" : "" ) . basename("inc/shopcenter/side") );?>
			<!-- 侧边菜单 -->
			<!-- 内容区 -->
			<div class="fr content-main">
				<div class="w content-main-head">
					<div class="fl company-info">
						<div class="img fl">
							<img src="<?php echo $data["base"]["Vc_logo_pic"];?>#" width="100" height="100" />
						</div>
						<div class="fl c-666 comany-info-con">
							<p>公司名称:&nbsp;&nbsp;<?php echo $data["base"]["Vc_name"];?></p>
							<p>成交数量:&nbsp;&nbsp;<?php echo $data["base"]["N_amount"];?>吨</p>
							<p>成交金额:&nbsp;&nbsp;<?php echo $data["base"]["N_money"];?>元</p>
							<p>开店时间:&nbsp;&nbsp; <?php echo formatTime($data["base"]["Dt_open"],'Y-m-d'); ?>
							</p>
						</div>
					</div>
					<div class="fl db-a ib-i order-info">
						<a href="#" class="order-info-layer">
							<i class="pending"></i>待处理订单<em><?php echo $data["ordercount"];?></em>
						</a>
						<a href="#" class="pub-a order-info-layer">
							<i class="pub"></i>我的发布<em><?php echo $data["publishedcount"];?></em>
						</a>
					</div>
				</div>
				<div class="content-main-con">
					<div class="cb content-main-con-head">
						<span class="head-title">我的订单</span>
						<a href="#" class="fr head-btn">查看全部订单</a>
					</div>
					<div class="content-main-con-list">
						<div class="cb fs14 c-444 homeorder-header">
							<span class="s1">订单详情</span>
							<span class="s2">收货人</span>
							<span class="s3">总计</span>
							<span class="s4">订单状态</span>
							<span class="s5">操作</span>
						</div>
						<div class="table-myorder home">
							<?php $counter1=-1; if( isset($data["orderlist"]) && is_array($data["orderlist"]) && sizeof($data["orderlist"]) ) foreach( $data["orderlist"] as $key1 => $value1 ){ $counter1++; ?>
								<table>
									<thead>
										<tr>
											<th colspan="8">
												订单号： <a href="#" class="order-num"><?php echo $value1["Vc_orderNO"];?></a><span class="order-date">下单时间：<?php echo formatTime($value1["Createtime"],'Y-m-d H:i'); ?></span>
											</th>
										</tr>
									</thead>
									<tr>										
										<?php $v=$this->var['v']=$value1["commoditylist"]["0"];?>
										<td width="368" class="homecon"><?php echo $v["itemname"];?>&nbsp;/&nbsp;<?php echo $v["specificationname"];?>&nbsp;/&nbsp;<?php echo $v["stuffname"];?>&nbsp;/&nbsp;<?php echo $v["factoryname"];?>&nbsp;/&nbsp;<?php echo $v["warehouse"];?>&nbsp;/&nbsp;<?php echo $v["N_amount"];?>件&nbsp;/&nbsp;<?php echo $v["N_weight"];?>吨</td>
										<td rowspan="<?php echo count($value1["commoditylist"]); ?>" width="117" class="js-showInfo">
											<?php echo $value1["Vc_consignee"];?>
											<div class="thisInfo">
												<p>姓名：<?php echo $value1["Vc_consignee"];?></p>
												<p>地址：<?php echo $value1["Vc_consignee_address"];?></p>
												<p>电话：<?php echo $value1["Vc_consignee_phone"];?></p>
											</div>
										</td>
										<td rowspan="<?php echo count($value1["commoditylist"]); ?>" width="134" class="money">￥<?php echo $value1["N_amount_price"];?></td>
										<td rowspan="<?php echo count($value1["commoditylist"]); ?>" width="110" class="status<?php echo $value1["I_status"];?>">
											<?php if( $value1["I_status"]==10 ){ ?>
												等待审核
											<?php }elseif( $value1["I_status"]==20 ){ ?>
												已完成
											<?php }elseif( $value1["I_status"]==30 ){ ?>
												待发货
											<?php }elseif( $value1["I_status"]==40 ){ ?>
												待提货
											<?php }elseif( $value1["I_status"]==50 ){ ?>
												商家取消
											<?php }elseif( $value1["I_status"]==60 ){ ?>
												用户取消
											<?php } ?>
										</td>
										<td rowspan="<?php echo count($value1["commoditylist"]); ?>" width="98" class="last1">
											<?php if( $value1["I_status"]==10 ){ ?>
												<a href="##">修改价格</a>
											<?php } ?>
											<a href="http://www.bigsm.com/index.php?act=user&m=order&w=detail&id=<?php echo $value1["id"];?>">订单详情</a>
										</td>
										<?php if( $value1["I_status"]==10 ){ ?>
											<td rowspan="<?php echo count($value1["commoditylist"]); ?>" class="last">
												<a href="##">审核</a>
											</td>
										<?php } ?>
									</tr>
									<?php $counter2=-1; if( isset($value1["commoditylist"]) && is_array($value1["commoditylist"]) && sizeof($value1["commoditylist"]) ) foreach( $value1["commoditylist"] as $key2 => $value2 ){ $counter2++; ?>
										<?php if( $counter2>0 ){ ?>
											<?php $v=$this->var['v']=$value2;;?>
											<tr>
												<td width="368" class="homecon">&nbsp;<?php echo $v["itemname"];?>&nbsp;/&nbsp;<?php echo $v["specificationname"];?>&nbsp;/&nbsp;<?php echo $v["stuffname"];?>&nbsp;/&nbsp;<?php echo $v["factoryname"];?>&nbsp;/&nbsp;<?php echo $v["warehouse"];?>&nbsp;/&nbsp;<?php echo $v["N_amount"];?>件&nbsp;/&nbsp;<?php echo $v["N_weight"];?>吨</td>
											</tr>
										<?php } ?>
									<?php } ?>
								</table>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<!-- 内容区 -->
		</div>
	</div>
	<!-- 中间内容部分 -->
	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/shop/footer") . ( substr("inc/shop/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/shop/footer") );?>
	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>