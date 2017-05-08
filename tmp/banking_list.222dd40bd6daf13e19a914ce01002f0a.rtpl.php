<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>融资管理 - 申请记录</title>
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
        			<a href="#">金融管理</a><span class="current">申请记录</span>
        		</div>
            	<div class="data-list-wrapper">
            		<div class="data-table-query fs0">
        				<label class="ib"><input type="text" value="2015-9-9" data-type="datepicker"></label><span>至</span><label class="ib"><input type="text" value="2015-9-9" data-type="datepicker"></label>
    					<select class="ib"><option>未成交</option></select>
    					<a href="##" class="ib btn-query" type="">查询</a>
            		</div>
            		<table class="data-table-review" id="banking-list">
            			<thead>
            				<th width="312">类型</th>
            				<th width="128">申请日期</th>
            				<th width="280">状态</th>
            				<th>操作</th>
            			</thead>
            			<tbody>
							<?php $counter1=-1; if( isset($data["data"]) && is_array($data["data"]) && sizeof($data["data"]) ) foreach( $data["data"] as $key1 => $value1 ){ $counter1++; ?>
								<?php if( $value1["I_banking_classID"]==1 ){ ?>
									<?php echo $space="";$type="mdyinventory";?>
								<?php }elseif( $value1["I_banking_classID"]==2 ){ ?>
									<?php echo $space="";$type="mdywarehouse";?>
								<?php }elseif( $value1["I_banking_classID"]==3 ){ ?>
									<?php echo $space="";$type="mdyticket";?>
								<?php } ?>
								<tr>
									<td><?php echo $value1["Vc_name"];?></td>
									<td><?php echo $value1["Createtime"];?></td>
									<td><?php echo $value1["status"];?></td>
									<td>
										<?php if( $value1["I_status"] == 10 ){ ?>
											<a href="javascript:void(0);" data-event="show" data-type="<?php echo $type;?>" data-type-id="<?php echo $value1["I_banking_classID"];?>" data-id="<?php echo $value1["id"];?>">查看</a>
											<a href="javascript:void(0);" data-event="change" data-type="<?php echo $type;?>" data-type-id="<?php echo $value1["I_banking_classID"];?>" data-id="<?php echo $value1["id"];?>">修改</a>
											<a href="javascript:void(0);" data-event="delete" data-id="<?php echo $value1["id"];?>" data-type-id="<?php echo $value1["I_banking_classID"];?>">删除</a>
										<?php }elseif( $value1["I_status"]==20 ){ ?>
											<a href="javascript:void(0);" data-event="show" data-type="<?php echo $type;?>" data-type-id="<?php echo $value1["I_banking_classID"];?>" data-id="<?php echo $value1["id"];?>">查看</a>
											<a href="javascript:void(0);" data-event="change" data-type="<?php echo $type;?>" data-type-id="<?php echo $value1["I_banking_classID"];?>" data-id="<?php echo $value1["id"];?>">修改</a>
											<a href="javascript:void(0);" data-event="delete" data-id="<?php echo $value1["id"];?>" data-type-id="<?php echo $value1["I_banking_classID"];?>">删除</a>
											<a href="javascript:void(0);" data-event="reapply" data-id="<?php echo $value1["id"];?>" data-type-id="<?php echo $value1["I_banking_classID"];?>">再次申请</a>
										<?php }elseif( $value1["I_status"]==30 ){ ?>
											<a href="javascript:void(0);" data-event="show" data-type="<?php echo $type;?>" data-type-id="<?php echo $value1["I_banking_classID"];?>" data-id="<?php echo $value1["id"];?>">查看</a>
											<a href="javascript:void(0);" data-event="delete" data-id="<?php echo $value1["id"];?>" data-type-id="<?php echo $value1["I_banking_classID"];?>">删除</a>
											<a href="javascript:void(0);" data-event="reapply" data-id="<?php echo $value1["id"];?>" data-type-id="<?php echo $value1["I_banking_classID"];?>">再次申请</a>
										<?php } ?>
									</td>
								</tr>
							<?php } ?>
            			</tbody>
            		</table>
					<div id="pagestr">
						<?php echo $pagestr;?>
					</div>
					<div id="loadMask">
						<div class="mask-bg"></div>
						<div class="mask-content" id="p2p-form">
							<div class="wrap"></div>
							<a href="javascript:void(0);" class="mask-btn-close" title="关闭"><img src="tpl/user/../image/pop_close.gif"/></a>
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
