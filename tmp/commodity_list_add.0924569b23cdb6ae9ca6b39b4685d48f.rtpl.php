<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
	<title>卖家中心 - 添加产品</title>
</head>
<body>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/shopcenter/header") . ( substr("inc/shopcenter/header",-1,1) != "/" ? "/" : "" ) . basename("inc/shopcenter/header") );?>
    <!-- 中间内容部分 -->
    <div id="content">
        <div class="w1100 c cb">
			<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/shopcenter/side") . ( substr("inc/shopcenter/side",-1,1) != "/" ? "/" : "" ) . basename("inc/shopcenter/side") );?>
            <!-- 内容区 -->
            <div class="fr content-main">
            	<form class="content-main-con no" id="js-product-form">
        			<div class="fs0 m-form m-form-full">
    					<div class="cb form-item chkbox" id="type-checkbox">
    						<label>行业：</label>
							<?php $counter1=-1; if( isset($data["mallClassList"]) && is_array($data["mallClassList"]) && sizeof($data["mallClassList"]) ) foreach( $data["mallClassList"] as $key1 => $value1 ){ $counter1++; ?>
							<?php $vo=$this->var['vo']=$value1;?>
								<label for="mall<?php echo $vo["id"];?>" class="chk selected"><?php echo $vo["Vc_name"];?></label>
								<input type="radio" name="I_mallClassID" value="<?php echo $vo["id"];?>" id="mall<?php echo $vo["id"];?>" checked>
							<?php } ?>
    						<a href="##" class="btn push-right">查看帮助</a>
    					</div>
						<div class="pr form-item-wrapper">
							<label for="file-idcard" class="form-item-file-label">选择文件</label>
							<input type="file" id="file-idcard" value="选择图片"/>
						</div>
						<span class="form-item-tips">鑫能裕丰集采标书.xlsx</span>
						<a href="##" class="ib form-upload-btn">上传</a>
						<a href="##" class="form-down">点击下载模版</a>
        			</div>
    				<table class="product-add-table" border="1" bordercolor="#F5F5F5" id="js-product-type">
    					<thead class="fs14 tac">
    						<th>类型</th>
    						<th>品名</th>
    						<th>材质</th>
    						<th>规格(mm)</th>
    						<th>钢厂</th>
    						<th>仓库</th>
    						<th>可供件数</th>
    						<th>件吨</th>
    						<th>销售单价</th>
    					</thead>
    					<tbody>
    						<tr>
    							<td>
									<select name="I_itemClassID">
									<?php $counter1=-1; if( isset($data["itemClassList"]) && is_array($data["itemClassList"]) && sizeof($data["itemClassList"]) ) foreach( $data["itemClassList"] as $key1 => $value1 ){ $counter1++; ?>
									<?php $vo=$this->var['vo']=$value1;?>
										<option value="<?php echo $vo["id"];?>"<?php if( $counter1==1 ){ ?> selected<?php } ?>><?php echo $vo["Vc_name"];?></option>
									<?php } ?>
									</select>
								</td>
    							<td><select name="I_itemID"><option></option></select></td>
    							<td><select name="I_stuffID"><option></option></select></td>
    							<td><select name="I_specificationID"><option></option></select></td>
    							<td><select name="I_factoryID"><option></option></select></td>
    							<td>
									<select name="I_warehouseID">
									<?php $counter1=-1; if( isset($data["wareHouseList"]) && is_array($data["wareHouseList"]) && sizeof($data["wareHouseList"]) ) foreach( $data["wareHouseList"] as $key1 => $value1 ){ $counter1++; ?>
										<?php $v=$this->var['v']=$value1;?>
											<option value="<?php echo $v["id"];?>" selected><?php echo $v["Vc_name"];?></option>
										<?php } ?>
									</select>
								</td>
    							<td><input name="N_amount" type="text"/></td>
								<td><input name="N_weight" type="text"/></td>
								<td><input name="N_price" type="text"/></td>
    						</tr>
    					</tbody>
    				</table>
					<button type="submit" class="product-add-submit" id="js-product-add">保存</button>
                </form>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
    <!-- 中间内容部分 -->
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>
