<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>需求管理 - 我的融资信息需求</title>
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
        			<a href="#">需求管理</a><span class="current">我的融资需求列表</span>
        		</div>
            	<div class="pr content-main-con">
            		<div class="data-table-query fs0">
        				<label class="ib"><input type="text" value="2015-09-09" data-type="datepicker"></label><span>至</span><label class="ib"><input type="text" value="2016-09-19" data-type="datepicker"></label>
    					<a href="javascript:void(0);" class="ib btn-query">查询</a>
    					<a href="/index.php?act=user&m=require&w=addfinance" target="_blank" class="ib btn-add">发布融资需求</a>
        			</div>
            		<div class="data-list-table">
            			<table>
            				<thead>
            					<th width="150">融资项目</th>
            					<th width="100">发布日期时间</th>
            					<th width="100">融资金额</th>
								<th width="100">融资期限</th>
            					<th width="100">期望利率</th>
            					<th width="100">联系人</th>
            					<th width="100">联系电话</th>
            					<th>操作</th>
            				</thead>
            				<tbody id="requirecommodity">
								<?php $counter1=-1; if( isset($data["data"]) && is_array($data["data"]) && sizeof($data["data"]) ) foreach( $data["data"] as $key1 => $value1 ){ $counter1++; ?>
									<tr class="<?php if( !($counter1%2==0) ){ ?>even<?php } ?>">
										<td><?php echo $value1["Vc_name"];?></td>
										<td><?php echo formatTime($value1["Createtime"],'Y-m-d H:i'); ?></td>
										<td><?php echo $value1["N_money"];?>万</td>
										<td><?php echo $value1["Vc_deadline"];?></td>
										<td><?php echo $value1["Vc_rate"];?></td>
										<td><?php echo $value1["Vc_contact"];?></td>
										<td><?php echo $value1["Vc_contact_phone"];?></td>
										<td>
											<a href="/index.php?act=requirement&m=detail&id=<?php echo $value1["id"];?>" target="_blank" class="btn-open">修改</a>|<a href="javascript:void(0);" data-id="<?php echo $value1["id"];?>" class="btn" data-event="delete">删除</a>|<a href="javascript:void(0);" class="btn" data-id="<?php echo $value1["id"];?>" data-event="cancel">撤销发布</a>
										</td>
									</tr>
								<?php } ?>
            				</tbody>
            			</table>
						<div id="pagestr"><?php echo $pagestr;?></div>
            		</div>
            	</div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
    <!-- 中间内容部分 -->
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>
    

