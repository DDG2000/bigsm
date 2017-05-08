<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>订单管理 - 全部订单</title>
</head>
<body>
   <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/header") . ( substr("inc/usercenter/header",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/header") );?>
    <!-- 中间内容部分 -->
    <div id="content">
        <div class="w1100 c cb">
			<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/side") . ( substr("inc/usercenter/side",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/side") );?>
			<script type="text/javascript">
					window.search = {
						I_status:"I_status=<?php echo $I_status;?>",
						starttime:"starttime=<?php echo $starttime;?>",
						endtime:"endtime=<?php echo $endtime;?>",
						keyword:"keyword=<?php echo $keyword;?>"
					};
			</script>
            <!-- 内容区 -->
            <div class="fr content-main">
            	<div class="crumblenav">
        			<a href="#">交易管理</a><span class="current">我的订单</span>
        		</div>
            	<div class="user-center-order">
            		<form class="cb filter-wrapper filter-myorder" id="js-orderForm">
	        			<div class="fl" id="js-orderFilter">
							<?php if( $I_status==10 ){ ?>
								<a href="javascript:void(0);" class="filter-menu">全部订单</a><a href="javascript:void(0);" class="filter-menu active" data-status="10">待审核</a>
								<a href="javascript:void(0);" class="filter-menu" data-status="20">已完成</a><a href="javascript:void(0);" class="filter-menu" data-isapp="1">待评价</a>
							<?php }elseif( $I_status==20 ){ ?>
								<a href="javascript:void(0);" class="filter-menu">全部订单</a><a href="javascript:void(0);" class="filter-menu" data-status="10">待审核</a>
								<a href="javascript:void(0);" class="filter-menu active" data-status="20">已完成</a><a href="javascript:void(0);" class="filter-menu" data-isapp="1">待评价</a>
							<?php }elseif( $I_status==70 ){ ?>
								<a href="javascript:void(0);" class="filter-menu">全部订单</a><a href="javascript:void(0);" class="filter-menu" data-status="10">待审核</a>
								<a href="javascript:void(0);" class="filter-menu" data-status="20">已完成</a><a href="javascript:void(0);" class="filter-menu active" data-isapp="1">待评价</a>
							<?php }elseif( $I_isapp==1 ){ ?>
								<a href="javascript:void(0);" class="filter-menu">全部订单</a><a href="javascript:void(0);" class="filter-menu" data-status="10">待审核</a>
								<a href="javascript:void(0);" class="filter-menu" data-status="20">已完成</a><a href="javascript:void(0);" class="filter-menu active" data-isapp="1">待评价</a>
							<?php }else{ ?>
								<a href="javascript:void(0);" class="filter-menu active">全部订单</a><a href="javascript:void(0);" class="filter-menu" data-status="10">待审核</a>
								<a href="javascript:void(0);" class="filter-menu" data-status="20">已完成</a><a href="javascript:void(0);" class="filter-menu" data-isapp="1">待评价</a>
							<?php } ?>
	        			</div>
	    				<div class="fl fs0 filter-form-search">
	    					<label class="ib form-label-date vmm tac"><input type="text" value="<?php echo $starttime;?>" data data-type="datepicker" name="starttime" placeholder="开始时间"/></label><em class="ib vmm">至</em>
	    					<label class="ib form-label-date vmm tac"><input type="text" value="<?php echo $endtime;?>" data data-type="datepicker" name="endtime" placeholder="结束时间"/></label>
	    					<input type="text" placeholder="请输入搜索关键字" name="keyword" value="<?php echo $keyword;?>"/>
	    					<a href="javascript:void(0);" class="ib filter-form-submit vmm tac" id="js-searchForm">搜索</a>
	    				</div>
	        		</form>
	        		<div class="cb fs14 c-444 myorder-header">
	        			<span class="s1">订单信息</span>
	        			<span class="s2">单价</span>
	        			<span class="s3">小计</span>
	        			<span class="s4">订单总计</span>
	        			<span class="s5">收货人</span>
						<span class="order-status">订单状态</span>
						<span class="s6">操作</span>
	        		</div>
            		<div class="table-myorder" id="order-list">
						<?php if( $data["data"] ){ ?>
							<?php $counter1=-1; if( isset($data["data"]) && is_array($data["data"]) && sizeof($data["data"]) ) foreach( $data["data"] as $key1 => $value1 ){ $counter1++; ?>
								<table data-type="order-li">
									<thead>
										<tr>
											<th colspan="12">
												订单号： <span class="order-num"><?php echo $value1['Vc_orderNO'];?></span><span class="order-date"><?php echo $value1['Createtime'];?></span><span class="order-name"><?php echo $value1['shopname'];?></span><span class="tel"><?php echo $value1['Vc_phone'];?></span>
											</th>
											<th class="delete"><a href="javascript:void(0);" class="ib" data-id="<?php echo $value1["id"];?>" title="删除订单"></a></th>
										</tr>
									</thead>
									<?php if( $value1["I_status"]==10 ){ ?>
										<?php echo $space = "";$status = "等待审核";;?>
									<?php }elseif( $value1["I_status"]==20 ){ ?>
										<?php echo $space = "";$status = "已完成";;?>
									<?php }elseif( $value1["I_status"]==70 ){ ?>
										<?php echo $space = "";$status = "已评价";;?>
									<?php }elseif( $value1["I_status"]==60 ){ ?>
										<?php echo $space = "";$status = "已取消";;?>
									<?php } ?>
									<tr>
										<td width="50"><?php echo $value1["goods"]["0"]['itemname'];?></td>
										<td width="50"><?php echo $value1["goods"]["0"]['stuffname'];?></td>
										<td width="100"><?php echo $value1["goods"]["0"]['specificationname'];?></td>
										<td width="50"><?php echo $value1["goods"]["0"]['factoryname'];?></td>
										<td width="75"><?php echo $value1["goods"]["0"]['warehouse'];?></td>
										<td width="50"><?php echo $value1["goods"]["0"]['N_amount'];?>件</td>
										<td width="70"><?php echo $value1["goods"]["0"]['N_weight'];?>吨</td>
										<td width="75">￥<?php echo $value1["goods"]["0"]['N_price'];?></td>
										<td width="85">￥<?php echo $value1["goods"]["0"]['N_amount_price'];?></td>
										<td rowspan="<?php echo count($value1["goods"]);; ?>" width="75" class="money"><?php echo $value1["N_amount_price"];?></td>
										<td rowspan="<?php echo count($value1["goods"]);; ?>" width="50"><?php echo $value1["Vc_consignee"];?></td>
										<td rowspan="<?php echo count($value1["goods"]);; ?>" width="65" class="prew status<?php echo $value1["I_status"];?>"><?php echo $status;?></td>
										<td rowspan="<?php echo count($value1["goods"]);; ?>">
											<?php if( $value1["I_status"]==10 ){ ?>
												<a href="javascript:void(0);" data-id="<?php echo $value1["id"];?>" class="cancel">取消订单</a>
												<a href="/index.php?act=user&m=order&w=detail&id=<?php echo $value1["id"];?>" target="_blank">订单详情</a>
											<?php }elseif( $value1["I_status"]==20 ){ ?>
												<?php if( $value1["I_isapp"]==1 ){ ?>
													<a href="/index.php?act=user&m=order&w=appraise&id=<?php echo $value1["id"];?>" target="_blank">评价</a>
												<?php }elseif( $value1["I_isapp"]==2 ){ ?>
													<a href="/index.php?act=user&m=order&w=appraisal&id=<?php echo $value1["id"];?>" target="_blank">查看评价</a>
												<?php } ?>
												<a href="/index.php?act=user&m=order&w=detail&id=<?php echo $value1["id"];?>" target="_blank">查看电子订货函</a>
												<a href="/index.php?act=user&m=order&w=detail&id=<?php echo $value1["id"];?>" target="_blank">订单详情</a>										
											<?php }elseif( $value1["I_status"]==60 ){ ?>
												<a href="/index.php?act=user&m=order&w=detail&id=<?php echo $value1["id"];?>" target="_blank">查看详情</a>
											<?php } ?>
										</td>
									</tr>
									<?php $counter2=-1; if( isset($value1["goods"]) && is_array($value1["goods"]) && sizeof($value1["goods"]) ) foreach( $value1["goods"] as $key2 => $value2 ){ $counter2++; ?>
										<?php if( $counter2>0 ){ ?>
											<tr>
												<td><?php echo $value2["itemname"];?></td>
												<td><?php echo $value2["stuffname"];?></td>
												<td><?php echo $value2["specificationname"];?></td>
												<td><?php echo $value2["factoryname"];?></td>
												<td><?php echo $value2["warehouse"];?></td>
												<td><?php echo $value2["N_amount"];?>件</td>
												<td><?php echo $value2["N_weight"];?>吨</td>
												<td>￥<?php echo $value2["N_price"];?></td>
												<td>￥<?php echo $value2["N_amount_price"];?></td>
											</tr>
										<?php } ?>
										
									<?php } ?>
								</table>
							<?php } ?>
							<div id="pagestr"><?php echo $pagestr;?></div>
						<?php } ?>
            		</div>
            	</div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
    <!-- 中间内容部分 -->
   	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>
