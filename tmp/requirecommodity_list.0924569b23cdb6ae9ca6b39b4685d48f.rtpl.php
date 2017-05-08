<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>卖家中心 - 产品报价</title>
</head>
<body>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/shopcenter/header") . ( substr("inc/shopcenter/header",-1,1) != "/" ? "/" : "" ) . basename("inc/shopcenter/header") );?>
    <!-- 中间内容部分 -->
    <div id="content">
        <div class="w1100 c cb">
			<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/shopcenter/side") . ( substr("inc/shopcenter/side",-1,1) != "/" ? "/" : "" ) . basename("inc/shopcenter/side") );?>
            <!-- 内容区 -->
            <div class="fr content-main">
            	<div class="pr content-main-con no">
            		<form class="data-table-query fs0" action="http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=list" method="POST">
						<input type="hidden" name="I_mallClassID">
        				<label class="ib"><input type="text" name="starttime" value="<?php echo iset($starttime); ?>" data-type="datepicker"></label>
						<span>至</span><label class="ib"><input type="text" name="endtime" value="<?php echo iset($endtime); ?>" data-type="datepicker"></label>
    					<select class="ib" name="I_publish_status">
							<option value="1"<?php if( $I_publish_status==1 ){ ?> selected<?php } ?>>已成交</option>
							<option value="2"<?php if( $I_publish_status==2 ){ ?> selected<?php } ?>>未成交</option>
						</select>
						<select class="ib" name="I_offer_status">
							<option value="1"<?php if( $I_offer_status==1 ){ ?> selected<?php } ?>>已报价</option>
							<option value="2"<?php if( $I_offer_status==2 ){ ?> selected<?php } ?>>未报价</option>
							<option value="3"<?php if( $I_offer_status==3 ){ ?> selected<?php } ?>>请确认订货函</option>
							<option value="4"<?php if( $I_offer_status==4 ){ ?> selected<?php } ?>>已成交</option>
						</select>
    					<button class="ib btn-query" type="submit">查询</button>
    					<a href="http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=offerset" class="ib btn-setting">设置接收报价的产品类别</a>
        			</form>
            		<div class="data-list-table ib-a">
            			<table border="0">
            				<thead>
            					<th width="120">采购编号</th>
            					<th width="118">采购日期</th>
            					<th width="82">截止日期</th>
            					<th width="88">采购人</th>
            					<th width="126">采购单位</th>
            					<th width="100">采购状态</th>
            					<th width="90">报价状态</th>
            					<th width="180">操作</th>
            				</thead>
            				<tbody>
								<?php $counter1=-1; if( isset($data["data"]) && is_array($data["data"]) && sizeof($data["data"]) ) foreach( $data["data"] as $key1 => $value1 ){ $counter1++; ?>
									<?php $vo=$this->var['vo']=$value1;?>
									<?php if( $vo["I_undelete"]>0 ){ ?>
										<tr<?php if( $counter1%2!=0 ){ ?> class="even"<?php } ?>>
											<td><?php echo $vo["Vc_orderSn"];?></td>
											<td><?php echo formatTime($vo['Createtime'],'Y-m-d'); ?></td>
											<td><?php echo formatTime($vo['D_end'],'Y-m-d'); ?></td>
											<td><?php echo $vo["Vc_contact"];?></td>
											<td><?php echo $vo["Vc_company"];?></td>
											<td class="status"><?php if( $vo["I_publish_status"]==5 ){ ?>已成交<?php }else{ ?>未成交<?php } ?></td>
											<td>
												<?php if( $vo["I_offer_status"]==1 ){ ?>
													已报价
												<?php }elseif( $vo["I_offer_status"]==2 ){ ?>
													未报价
												<?php }elseif( $vo["I_offer_status"]==3 ){ ?>
													<a href="http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=detail&id=<?php echo $vo["id"];?>&cid=<?php echo $vo["cid"];?>">请确认订货函</a>
												<?php }elseif( $vo["I_offer_status"]==4 ){ ?>
													已成交
												<?php } ?>
											</td>
											<td>
												<a href="http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=detail&id=<?php echo $vo["id"];?>&cid=<?php echo $vo["cid"];?>" class="data-list-btn-watch">查看</a>
												<?php if( $vo["I_offer_status"]==2 ){ ?>
													|<a href="http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=edit&id=<?php echo $vo["id"];?>&cid=<?php echo $vo["cid"];?>" class="data-list-btn-change">报价</a>
												<?php }else{ ?>
													<?php if( (strtotime($vo["D_end"])>strtotime($nowtime))&&($vo["I_offer_status"]<3) ){ ?>
														|<a href="http://www.bigsm.com/index.php?act=shop&m=requirecommodity&w=edit&id=<?php echo $vo["id"];?>&cid=<?php echo $vo["cid"];?>" class="data-list-btn-change">修改报价</a><?php }else{ ?>|<a>修改报价</a>
													<?php } ?>
												<?php } ?>
												<?php if( ($vo["I_publish_status"]==5)&&($vo["I_offer_status"]==4) ){ ?>
													|<a href="javascript:;" class="data-list-btn-delete">删除</a>
												<?php } ?>
											</td>
										</tr>
									<?php } ?>
								<?php } ?>
            				</tbody>
            			</table>
						<div id="pagestr">
							<?php echo $pagestr;?>
						</div>
            		</div>
            	</div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
    <!-- 中间内容部分 -->
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>
