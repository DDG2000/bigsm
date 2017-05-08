<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>权限分配</title>
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
        			<a href="#">交易管理</a><span class="current">我的订单</span>
        		</div>
            	<div class="pr content-main-con">
            		<div class="data-table-query fs0">
        				<label class="ib"><input type="text" value="2015-9-9"></label><span>至</span><label class="ib"><input type="text" value="2015-9-9"></label>
    					<a href="##" class="ib btn-query">查询</a>
    					<a href="##" class="ib btn-add">添加子帐号</a>
        			</div>
            		<div class="data-list-table ib-a quanxian">
            			<table border="0">
            				<thead>
            					<th width="77">序号</th>
            					<th width="81">用户名</th>
            					<th width="108">真实姓名</th>
            					<th width="121">联系电话</th>
            					<th width="161">邮箱</th>
            					<th width="100">创建时间</th>
            					<th>操作</th>
            				</thead>
            				<tbody>
							<?php if( !empty($data) ){ ?>
							<?php $counter1=-1; if( isset($data["data"]) && is_array($data["data"]) && sizeof($data["data"]) ) foreach( $data["data"] as $key1 => $value1 ){ $counter1++; ?>
            					<tr>
            						<td><?php echo $counter1+1;?></td>
            						<td><?php echo $value1["Vc_name"];?></td>
            						<td><?php echo $value1["Vc_truename"];?></td>
            						<td><?php echo $value1["Vc_mobile"];?></td>
            						<td><?php echo $value1["Vc_Email"];?></td>
            						<td><?php echo $value1["Createtime"];?></td>
            						<td><a href="##" class="data-btn">分配权限</a>|<a href="##" class="data-btn">重置密码</a>|<a href="##" class="data-btn">启用</a>|<a href="##" class="data-btn-delete">删除</a></td>
            					</tr>
							<?php } ?>
							<?php } ?>
            				</tbody>
            			</table>
            		</div>
            	</div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>
