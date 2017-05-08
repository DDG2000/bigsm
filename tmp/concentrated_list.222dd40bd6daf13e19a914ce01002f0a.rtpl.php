<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
<title>项目管理 - 我的集采</title>
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
        			<a href="#">项目管理</a><span class="current">我的集采</span>
        		</div>
            	<div class="data-list-wrapper">
            		<div class="data-table-query fs0">
        				<label class="ib "><input type="text" value="2015-9-9"></label><span>至</span><label class="ib"><input type="text" value="2015-9-9"></label>
    					<select class="ib"><option>审核中</option></select>
    					<a href="##" class="ib btn-query">查询</a>
    					<a href="/index.php?act=user&m=concentrated&w=add" class="data-btn-add ib" target="_blank">添加集采公告</a>
            		</div>
            		<table class="mycollection" id="js-ie-concentrated">
            			<thead>
            				<th width="138">集采名称</th>
            				<th width="62">类型</th>
            				<th width="167">集采期限</th>
            				<th width="153">发布时间</th>
            				<th width="68">状态</th>
            				<th width="60">报名数量</th>
            				<th width="104">标书</th>
            				<th>操作</th>
            			</thead>
            			<tbody>
							<?php $counter1=-1; if( isset($data["data"]) && is_array($data["data"]) && sizeof($data["data"]) ) foreach( $data["data"] as $key1 => $value1 ){ $counter1++; ?>
								<tr <?php if( $counter1%2!==0 ){ ?>class="even"<?php } ?>>
									<td><a href="/index.php?act=user&m=concentrated&w=detail&id=<?php echo $value1["id"];?>" class="link"><?php echo $value1["Vc_name"];?></a></td>
									<td><?php echo $value1["itemclassname"];?></td>
									<td><?php echo $value1["D_start"];?>—<?php echo $value1["D_end"];?></td>
									<td><?php echo $value1["Createtime"];?></td>
									<td class="status<?php echo $value1["I_status"]/10;?>">
										<?php if( $value1["I_status"]==10 ){ ?>
											待审核
										<?php }elseif( $value1["I_status"]==20 ){ ?>
											报名中
										<?php }elseif( $value1["I_status"]==30 ){ ?>
											已截止
										<?php }else{ ?>
											已成交
										<?php } ?>
									</td>
									<td><?php echo $value1["I_numbers"];?></td>
									<td><a href="javascript:(0);" class="search" title="预览">预览</a><a href="<?php echo $value1["Vc_doc"];?>" class="download ie-download" title="下载" download="<?php echo $value1["Vc_name"];?>.xls" target="_blank">下载</a><?php if( $value1["I_status"]==10 ){ ?><a href="##" class="share" title="重新上传">重新上传</a><?php } ?></td>></td>
									<td><a href="/index.php?act=user&m=concentrated&w=publish&id=<?php echo $value1["id"];?>" target="_blank" class="pub">发布中标公告</a>|<a href="/index.php?act=user&m=concentrated&w=detail&id=<?php echo $value1["id"];?>" target="_blank" class="delete">详情</a></td>
								</tr>
							<?php } ?>
            			</tbody>
            		</table>
					<div id="pagestr">
						<?php echo $pagestr;?>
					</div>
            	</div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
    <!-- 中间内容部分 -->
 	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>