<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>订单管理 - 我的订单</title>
</head>
<body>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/header") . ( substr("inc/usercenter/header",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/header") );?>
    <!-- 中间内容部分 -->
    <div id="content">
        <div class="w1100 c cb">
            <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/side") . ( substr("inc/usercenter/side",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/side") );?>
            <!-- 内容区 -->
            <div class="fr content-main">
            	<div class="cb user-center-head">
            		<img  src="<?php echo $Vc_photo;?>" width="100" height="100" class="fl">
        			<div class="fl user-center-info">
						<?php if( $safelv==0 ){ ?>
							<?php echo $space = "";$level = "危险";;?>
						<?php }elseif( $safelv==1 ){ ?>
							<?php echo $space = "";$level = "一般";;?>
						<?php }elseif( $safelv==2 ){ ?>
							<?php echo $space = "";$level = "高";;?>
						<?php } ?>
        				<p class="user-center-name"><?php echo $Vc_truename;?></p>
        				<div class="cb user-account-security">
        					<span class="fl account-security-title">账户安全：</span>
            				<div class="fl pr account-security-bar">
            					<div class="pa account-security-process level<?php echo $safelv;?>"></div>
            				</div>
            				<span class="fl account-security-level level-<?php echo $safelv;?>"><?php echo $level;?></span>
            			</div>
	        		</div>
        			<div class="fl fl-a ib-i user-operation">
        				<a href="##"><i class="i1"></i><br />待审核<em><?php echo $status['10'];?></em></a><a href="##"><i class="i2"></i><br />待评价<em><?php echo $status['70'];?></em></a><a href="##"><i class="i3"></i><br />待自提<em>0</em></a><a href="##"><i class="i4"></i><br />已完成<em><?php echo $status['20'];?></em></a>
        			</div>
            	</div>
            	<div class="user-center-order">
            		<div class="cb table-title">
            			我的订单<a href="/index.php?act=user&m=order&w=list" target="_blank" class="fr showAll">查看全部订单</a>
            		</div>
            		<div class="table-myorder" id="order-list">
						<?php if( $da ){ ?>
							<?php $counter1=-1; if( isset($da["data"]) && is_array($da["data"]) && sizeof($da["data"]) ) foreach( $da["data"] as $key1 => $value1 ){ $counter1++; ?>
								<table>
									<?php if( $value1["I_status"]==10 ){ ?>
										<?php echo $space = "";$status = "等待审核";;?>
									<?php }elseif( $value1["I_status"]==20 ){ ?>
										<?php echo $space = "";$status = "已完成";;?>
									<?php }elseif( $value1["I_status"]==70 ){ ?>
										<?php echo $space = "";$status = "待评价";;?>
									<?php }elseif( $value1["I_status"]==60 ){ ?>
										<?php echo $space = "";$status = "已取消";;?>
									<?php } ?>
									<thead>
										<tr>
											<th colspan="9">
												订单号： <span class="order-num"><?php echo $value1["Vc_orderNO"];?></span><span class="order-date"><?php echo $value1["Createtime"];?></span><span class="order-name"><?php echo $value1["shopname"];?></span><span class="tel"><?php echo $value1["Vc_phone"];?></span>
											</th>
											<th class="delete"><a href="javascript:void(0);" class="ib" data-id="<?php echo $value1["id"];?>" title="删除订单"></a></th>
										</tr>
									</thead>
									<tr>
										<td width="65"><?php echo $value1["goods"]["0"]['itemname'];?></td>
										<td width="48"><?php echo $value1["goods"]["0"]['stuffname'];?></td>
										<td width="104"><?php echo $value1["goods"]["0"]['specificationname'];?></td>
										<td width="45"><?php echo $value1["goods"]["0"]['factoryname'];?></td>
										<td width="75"><?php echo $value1["goods"]["0"]['warehouse'];?></td>
										<td width="63"><?php echo $value1["goods"]["0"]['N_weight'];?></td>
										<td rowspan="<?php echo count($value1["goods"],0)+1;; ?>" width="115"><?php echo $value1["Vc_consignee"];?></td>
										<td rowspan="<?php echo count($value1["goods"],0)+1;; ?>" width="130" class="money">￥<?php echo $value1["N_amount_price"];?></td>
										<td rowspan="<?php echo count($value1["goods"],0)+1;; ?>" width="132" class="prew status<?php echo $value1["I_status"];?>"><?php echo $status;?></td>
										<td rowspan="<?php echo count($value1["goods"],0)+1;; ?>">
											<?php if( $value1["I_status"]==10 ){ ?>
												<a href="javascript:void(0);" data-id="<?php echo $value1["id"];?>" class="cancel">取消订单</a>
												<a href="/index.php?act=user&m=order&w=detail&id=<?php echo $value1["id"];?>" target="_blank">订单详情</a>
											<?php }elseif( $value1["I_status"]==20 ){ ?>												<?php if( $value1["I_isapp"]==1 ){ ?>
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
										<?php if( $counter2 >0 ){ ?>
											<tr>
												<td><?php echo $value2["itemname"];?></td>
												<td><?php echo $value2["stuffname"];?></td>
												<td><?php echo $value2["specificationname"];?></td>
												<td><?php echo $value2["factoryname"];?></td>
												<td><?php echo $value2["warehouse"];?></td>
												<td><?php echo $value2["N_weight"];?></td>
											</tr>
										<?php } ?>
									<?php } ?>
								</table>
							<?php } ?>
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
