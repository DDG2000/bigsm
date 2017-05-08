<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>需求管理 - 我的招标需求</title>
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
        			<a href="#">需求管理</a><span class="current">我的招标需求列表</span>
        		</div>
            	<div class="pr content-main-con">
            		<div class="data-table-query fs0">
        				<label class="ib"><input type="text" value="2015-09-09" data-type="datepicker"></label><span>至</span><label class="ib"><input type="text" value="2016-09-19" data-type="datepicker"></label>
    					<a href="javascript:void(0);" class="ib btn-query">查询</a>
    					<a href="/index.php?act=user&m=require&w=addtender" target="_blank" class="ib btn-add">发布招标需求</a>
        			</div>
            		<div class="data-list-table">
            			<table>
            				<thead>
            					<th width="100">招标项目</th>
            					<th width="120">发布时间</th>
            					<th width="120">招标时间</th>
								<th width="70">联系人</th>
            					<th width="85">联系方式</th>
            					<th width="100">标书下载数量</th>
            					<th width="135">标书</th>
            					<th>操作</th>
            				</thead>
            				<tbody id="tender-list">
								<?php $counter1=-1; if( isset($data["data"]) && is_array($data["data"]) && sizeof($data["data"]) ) foreach( $data["data"] as $key1 => $value1 ){ $counter1++; ?>
									<tr class="<?php if( !($counter1%2==0) ){ ?>even<?php } ?>">
										<td><?php echo $value1["Vc_name"];?></td>
										<td><?php echo formatTime($value1["Createtime"],'Y-m-d H:i'); ?></td>
										<td><?php echo formatTime($value1["D_end"],'Y-m-d H:i'); ?></td>
										<td><?php echo $value1["Vc_contact"];?></td>
										<td><?php echo $value1["Vc_contact_phone"];?></td>
										<td></td>
										<td>
											<a href="javascript:(0);" title="预览">预览</a><a href="<?php echo $value1["Vc_excel"];?>" class="download ie-download" title="下载" download="<?php echo $value1["Vc_name"];?>.xls" target="_blank">下载</a><a href="javascript:void(0);" data-event="reUpload" title="重新上传" data-id="<?php echo $value1["id"];?>">重新上传</a>
										</td>
										<td>
											<a href="javascript:void(0);" data-event="change" data-id="<?php echo $value1["id"];?>">修改</a>|<a href="javascript:void(0);" data-id="<?php echo $value1["id"];?>" data-event="delete" data-class="<?php echo $value1["I_requirementID"];?>">删除</a>|
											<?php if( $value1["I_publish_status"]!=3 ){ ?>
												<a href="javascript:void(0);" data-id="<?php echo $value1["id"];?>" data-class="<?php echo $value1["I_requirementID"];?>" data-event="cancel">撤销发布</a>
											<?php }else{ ?>
												<a href="javascript:;" style="cursor:default;color:#999;display:inline-block;width:48px;">已撤销</a>
											<?php } ?>
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
	<!-- 重新上传标书弹窗 -->
	<div class="mask-content-change" style="display:none;">
		<form action="javascript:;" class="tender-form" enctype="multipart/form-data" id="tender-form-change"></form>
	</div>
	<div class="mask-content-upload" style="display:none;">
		<form id="upload-tender" enctype="multipart/form-data">
			<input type="file" name="Vc_excel" class="hidden" id="tender-file"><label for="tender-file" class="file-input">浏览</label><span class="filename">请选择文件</span>
			<button type="submit">上传</button>
		</form>
	</div>
	<!-- 重新上传标书弹窗 -->
    <!-- 中间内容部分 -->
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>
    

