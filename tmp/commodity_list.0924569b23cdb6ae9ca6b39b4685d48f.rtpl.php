<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>卖家中心 - 产品管理</title>

</head>
<body>
	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/shopcenter/header") . ( substr("inc/shopcenter/header",-1,1) != "/" ? "/" : "" ) . basename("inc/shopcenter/header") );?>
    <!-- 中间内容部分 -->
    <div id="content">
        <div class="w1100 c cb">
			<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/shopcenter/side") . ( substr("inc/shopcenter/side",-1,1) != "/" ? "/" : "" ) . basename("inc/shopcenter/side") );?>
            <!-- 内容区 -->
            <div class="fr content-main">
        		<div class="cb filter-wrapper">
        			<div class="fl" id="product-filter">
        				<a href="javascript:;" class="filter-menu<?php if( empty($ispublished) ){ ?> active<?php } ?>" data-id="">全部产品</a>
						<a href="javascript:;" class="filter-menu<?php if( $ispublished==2 ){ ?> active<?php } ?>" data-id="2">已发布</a><a href="javascript:;" class="filter-menu<?php if( $ispublished==1 ){ ?> active<?php } ?>" data-id="1">未发布</a>
        			</div>
    				<form action="http://www.bigsm.com/index.php?act=shop&m=commodity&w=list&submit" method="POST" class="fr fs0 filter-form-search" id="js-search-product-form">
						<!-- date: 2016.06.30 comment: 待添加类型筛选 -->
						<input type="hidden" name="ispublished" value="<?php echo iset($ispublished); ?>">
    					<input type="text" placeholder="请输入关键字" name="sKey" class="ib vmm" value="<?php echo iset($sKey); ?>">
    					<label class="ib form-label-date vmm tac"><input type="text" value="<?php echo iset($starttime); ?>" class="datetime" data-type="datepicker" name="starttime"></label><em class="ib vmm">至</em>
    					<label class="ib form-label-date vmm tac"><input type="text" value="<?php echo iset($endtime); ?>" class="datetime" data-type="datepicker" name="endtime"></label>
    					<button type="submit" class="ib filter-form-submit vmm tac">搜索</button>
    				</form>
        		</div>
        		<div class="product-list-wrapper">
        			<table class="w product-list-all">
	        			<thead>
	        				<th width="37"><input type="checkbox" id="btn-checkAll"></th>
	        				<th width="46">类别</th>
	        				<th width="85">类型</th>
	        				<th width="59">品名</th>
	        				<th width="92">材质</th>
	        				<th width="110">规格</th>
	        				<th width="52">钢厂</th>
	        				<th width="100">仓库</th>
	        				<th width="26">件数</th>
	        				<th width="65">件/吨</th>
	        				<th width="100">销售单价</th>
	        				<th width="72">发布时间</th>
	        				<th>状态</th>
	        			</thead>
	        			<tbody>
							<?php $counter1=-1; if( isset($data["data"]) && is_array($data["data"]) && sizeof($data["data"]) ) foreach( $data["data"] as $key1 => $value1 ){ $counter1++; ?>
							<?php $vo=$this->var['vo']=$value1;?>
								<tr>
									<td><input type="checkbox" value="<?php echo $vo["id"];?>"></td>
									<td><?php echo $vo["mallclassname"];?></td>
									<td><?php echo $vo["itemclassname"];?></td>
									<td><?php echo $vo["itemname"];?></td>
									<td><?php echo $vo["stuffname"];?></td>
									<td><?php echo $vo["specificationname"];?></td>
									<td><?php echo $vo["factoryname"];?></td>
									<td><?php echo $vo["warehouse"];?></td>
									<td><?php echo $vo["N_amount"];?></td>
									<td><?php echo $vo["N_weight"];?></td>
									<td>￥<?php echo $vo["N_price"];?></td>
									<td>
									<?php if( !empty($vo["Dt_publish"]) ){ ?>

									<?php echo formatTime($vo['Dt_publish'],'Y-m-d'); ?>
									<?php }else{ ?>
									-- --
									<?php } ?>
									</td>
									<td class="unrelease"><?php if( $vo["I_publish"]==0 ){ ?>未发布<?php }else{ ?>已发布<?php } ?></td>
								</tr>
							<?php } ?>
	        			</tbody>
	        		</table>
        			<div class="product-list-opreation">
	        			<a href="javascript:;" class="product-btn-edit">编辑选中</a>|<a href="javascript:;" class="product-btn-delete">删除选中</a>| <a href="javascript:;" class="product-btn-release">发布</a>|<a href="javascript:;" class="product-btn-revocation">撤销发布</a>|<a href="javascript:;" class="product-btn-export">导出</a>
	        		</div>
					<div id="pagestr">
						<?php echo $pagestr;?>
					</div>
        		</div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>