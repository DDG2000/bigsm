<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>需求管理 - 新增采购</title>
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
        			<a href="#">需求管理</a><span class="current">新增采购</span>
        		</div>
            	<div class="content-main-con add">
        			<form enctype="multipart/form-data" class="fs0 m-form m-form-full" id="js-pubCollection">
    					<div class="cb form-item chkbox" id="type-checkbox">
    						<label>行业：</label>
    						<?php $counter1=-1; if( isset($data["mallclass"]) && is_array($data["mallclass"]) && sizeof($data["mallclass"]) ) foreach( $data["mallclass"] as $key1 => $value1 ){ $counter1++; ?>
    							<?php $v=$this->var['v']=$value1;?>
    							<label class="<?php if( $counter1==0 ){ ?> selected" data-id="<?php echo $v["id"];?>"><input type="radio" checked<?php } ?> value="<?php echo $v["id"];?>" name="I_mall_classID"><?php echo $v["Vc_name"];?></label>
    						<?php } ?>
							<input type="text" class="hidden" name="Vc_itemClassIds" id="classIDs">
    					</div>
						<div id="type-subbox">
						</div>
    					<!-- 采购信息 -->
    					<table class="w purchase-pushinfo">
    						<tr>
    							<td class="td-title">公司名称：</td>
    							<td class="con1"><input name="Vc_company" type="text" value="<?php echo $data["userinfo"]["Vc_name"];?>"/></td>
    							<td class="td-title">采购人：</td>
    							<td class="con2"><input name="Vc_contact" type="text" value="<?php echo $data["userinfo"]["Vc_truename"];?>"/></td>
    							<td class="td-title">联系电话：</td>
    							<td><input name="Vc_contact_phone" type="text" value="<?php echo $data["userinfo"]["Vc_mobile"];?>"/></td>
    						</tr>
    						<tr>
    							<td class="td-title">交货地：</td>
    							<td>
									<select name="I_provinceID"><option value="1">四川</option></select>
									<select name="I_cityID"><option value="1">成都</option></select>
    							</td>
    							<td class="td-title">支付方式：</td>
    							<td>
									<select name="I_payType">
										<option value="1">现款</option>
										<option value="2">银行承兑</option>
									</select>
    							</td>
    							<td class="td-title">截止日期：</td>
    							<td><div class="data-select"><input name="D_end" type="text" data-type="datepicker"></div></td>
    						</tr>
    						<tr>
    							<td class="td-title">采购名称：</td>
    							<td><input name="Vc_name" type="text"></td>
								<td class="td-title">采购类别：</td>
    							<td>
									<input name="I_mallClassID" type="text" id="js-collection-class">
								</td>
    							<td class="td-title">备注：</td>
    							<td><input name="Vc_memo" type="text"></td>
    						</tr>
    						<tr>
    							<td class="con2" colspan="2"><label class="file"><input type="file" name="Vc_doc">选择文件</label><span class="filename">鑫能裕丰集采标书.xlsx</span><input type="text" name="id" class="hidden"></td>
    							<td class="con3" colspan="2"><label class="uploadfile"><input type="file" name="file_exl" id="file_exl">上传采购单</label><a href="javascript:void(0);" class="download">点击下载模板</a></td>
    						</tr>
    					</table>
    					<!-- 采购信息 -->
    					<table class="w purchase-pushlist" id="purchase-pushlist">
    						<thead>
    							<th width="130">品名</th>
    							<th width="130">材质</th>
    							<th width="130">规格</th>
    							<th width="130">数量</th>
    							<th width="130">单位</th>
    							<th width="130">钢厂</th>
    							<th>操作</th>
    						</thead>
    						<body>								
    							<tr>
    								<td><input type="text" name="Vc_item[]" value=""></td>
    								<td><input type="text" name="Vc_stuff[]" value=""></td>
    								<td><input type="text" name="Vc_specification[]" value=""></td>
    								<td><input type="text" name="N_amount[]" value="6"></td>
    								<td><select name="I_unitType[]">
    								<option value="1">件</option>
    								<option value="2">根</option>
    								<option value="3">吨</option>
    								</select>
    								</td>
    								<td><input type="text" name="Vc_factorys[]" value="龙钢，鞍钢，钳钢"></td>
    								<td><a href="javascript:void(0);" data-event="delete">删除</a><a href="javascript:void(0);" data-event="add">新增</a></td>
								</tr>
    						</body>
    					</table>
						<div class="pub-btn-wrapper">
							<button type="submit" class="pub-btn" data-submit="pub">发布</button>
							<button type="submit" class="pub-btn" data-submit="pub&send">发布并短信推送</button>
						</div>
        			</form>
                </div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
    <!-- 中间内容部分 -->
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>
