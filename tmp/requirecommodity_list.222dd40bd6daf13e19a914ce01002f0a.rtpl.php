<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>需求管理 - 我的产品需求</title>
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
        			<a href="#">需求管理</a><span class="current">我的产品需求列表</span>
        		</div>
            	<div class="pr content-main-con">
            		<form class="data-table-query fs0" method="POST" action="/index.php?act=user&m=requirecommodity&w=list">
            			<select  name="I_mallClassID" class="industry">
            				<option value="">选择行业</option>
							<?php $counter1=-1; if( isset($mallArr) && is_array($mallArr) && sizeof($mallArr) ) foreach( $mallArr as $key1 => $value1 ){ $counter1++; ?>
							<?php $vo=$this->var['vo']=$value1;?>
								<option value=<?php echo $vo["id"];?>><?php echo $vo["Vc_name"];?></option>
							<?php } ?>
            			</select>
        				<label class="ib"><input type="text" value="<?php echo iset($starttime); ?>" data-type="datepicker" name="starttime"></label><span>至</span><label class="ib"><input type="text" name="endtime" value="<?php echo iset($endtime); ?>" data-type="datepicker"></label>
    					<select name="I_publish_status">
							<?php if( $I_publish_status ){ ?>
								<?php if( $I_publish_status==1 ){ ?>
									<option value="2">未成交</option>
									<option value="1" selected>已成交</option>
								<?php }else{ ?>
									<option value="2" selected>未成交</option>
									<option value="1">已成交</option>
								<?php } ?>
							<?php }else{ ?>
								<option value="2">未成交</option>
								<option value="1">已成交</option>
							<?php } ?>
            			</select>
    					<button type="submit" class="ib btn-query">查询</button>
    					<a href="/index.php?act=user&m=requirecommodity&w=add" target="_blank" class="ib btn-add">发布采购计划</a>
        			</form>
            		<div class="data-list-table ib-a quanxian">
            			<table>
            				<thead>
            					<th width="100">采购编号</th>
            					<th width="140">采购名称</th>
            					<th width="105">发布时间</th>
								<th width="105">报价截止时间</th>
            					<th width="60">付款方式</th>
            					<th width="55">状态</th>
            					<th width="55">报价数量</th>
            					<th>操作</th>
            				</thead>
            				<tbody id="requirecommodity">
								<?php $counter1=-1; if( isset($data["data"]) && is_array($data["data"]) && sizeof($data["data"]) ) foreach( $data["data"] as $key1 => $value1 ){ $counter1++; ?>
									<tr class="<?php if( !($counter1%2==0) ){ ?>even<?php } ?>">
										<td><?php echo $value1["Vc_orderSn"];?></td>
										<td><?php echo $value1["Vc_name"];?></td>
										<td><?php echo formatTime($value1["Createtime"],'Y-m-d H:i'); ?></td>
										<td><?php echo formatTime($value1["D_end"],'Y-m-d H:i'); ?></td>
										<td><?php if( $value1["I_payType"]==1 ){ ?>现款<?php }elseif( $value1["I_payType"]==2 ){ ?>银行承兑<?php } ?></td>
										<td class="status"><?php if( $value1["I_publish_status"]==1 ){ ?>已成交<?php }else{ ?>未成交<?php } ?></td>
										<td>
											<?php echo $value1["I_bids"];?>
										</td>
										<td>
											<a href="/index.php?act=user&m=requirecommodity&w=detail&id=<?php echo $value1["id"];?>" target="_blank" class="btn-show">查看</a>|<a href="javascript:void(0);" class="btn" data-event="sendMsgs" data-id="<?php echo $value1["id"];?>">短信推送</a>|<a href="/index.php?act=requirement&m=commoditydetail&I_requirementID=<?php echo $value1["id"];?>" target="_blank" class="btn-open">打开链接</a>|<a href="javascript:void(0);" data-id="<?php echo $value1["id"];?>" class="btn" data-event="delete">删除</a>|<a href="javascript:void(0);" class="btn" data-id="<?php echo $value1["id"];?>" data-event="cancel">撤销发布</a>
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
	<!-- 采购管理 弹窗短信推送选择 -->
    <div id="mask-message" style="display:none;">
    	<div class="mask-bg"></div>
    	<div class="mask-content mask-content-message">
			<a href="javascript:void(0);" class="mask-btn-close"><img src="<?php  echo TPL_IMG;?>pop_close.gif" active-close data-origin="#mask-message"></a>
    		<div class="message-item">
				<label class="ib active message-item-title"><input type="radio">按产品类型推送：</label>
    			<div class="fs0 ib ib-a chk-select message-item-con" id="chk-select">
					<a href="javascript:;" class="selected" data-type="1">热卷</a><a href="javascript:;" data-type="2">中厚板</a>
					<a href="javascript:;" data-type="3">冷轧涂镀</a><a href="javascript:;" data-type="4">型钢</a>
					<a href="javascript:;" data-type="5">建筑钢材</a><a href="javascript:;" data-type="6">热卷</a>
					<a href="javascript:;" data-type="7">中厚板</a><a href="javascript:;" data-type="8">冷轧涂镀</a>
				</div>
    		</div>
    		<div class="message-item area">
    			<label class="ib message-item-title"><input type="radio">按商家推送：</label>
    			<div class="ib message-item-con">
    				<textarea></textarea>
    			</div>
    		</div>
    		<hr />
    		<a href="##" class="db submit">确定</a>
    	</div>
    </div>
    <!-- 采购管理 弹窗短信推送选择 -->
    <!-- 中间内容部分 -->
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>
    

