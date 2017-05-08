<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>卖家中心 - 认证信息</title>
</head>
<body>
   	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/header") . ( substr("inc/usercenter/header",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/header") );?>
    <!-- 中间内容部分 -->
    <div id="content">
        <div class="w1100 c cb">
			<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/side") . ( substr("inc/usercenter/side",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/side") );?>
            <!-- 侧边菜单 -->
            <!-- 内容区 -->
            <div class="fr content-main">
            	<div class="pr content-main-con no">
            		<div class="cb content-main-con-head line">
            			<span class="head-title">认证信息</span>
            		</div>
            		<div class="pr cb companyinfo">
            			<div class="companyinfo-img fl">
            				<img src="<?php  echo TPL_IMG;?>companyimg.png" width="100" height="100">
            			</div>
						<?php if( $I_status==10 || $I_status == 20 || $I_status == 30 ){ ?>
							<div class="fl fs0 m-form">
								<form action="" method="post" id="renzheng" autocomplete="on" target="uploadFrame" enctype="multipart/form-data">
									<div class="form-item">
										<label class="title">公司名称：</label><span class="form-item-text"><?php echo $data["Vc_companyname"];?></span>
									</div>
									<div class="form-item">
										<label class="title">公司地址：</label><span class="form-item-text"><?php echo $data["Vc_address_company"];?></span>
									</div>
									<div class="form-item">
										<label class="title">法人姓名：</label><input type="text" name="Vc_lawname" value="<?php echo $data["Vc_lawname"];?>" disabled/>
									</div>
									<div class="form-item">
										<label class="title">法人身份证号：</label><input type="text" name="Vc_cityrenID" value="<?php echo $data["Vc_cityrenID"];?>" disabled/>
									</div>
									<div class="form-item">
										<label class="title">身份证正反面：</label>
										<div class="pr form-item-wrapper">
											<label for="idcard" class="form-item-file-label" disabled>已传图片</label>
											<input type="file" value="" id="idcard" name="Vc_imageID"  data-target="#idcard-img" disabled/>
										</div>
									</div>
									<div class="form-item">
										<div class="form-item-imgpreview">
											<img src="<?php echo $data["Vc_imageID"];?>" style="max-width:257px;" id="idcard-img">
										</div>
									</div>
									<div class="form-item">
										<label class="title">营业执照：</label>
										<div class="pr form-item-wrapper">
											<label for="license" class="form-item-file-label" disabled>已传图片</label>
											<input type="file" value="" id="license" name="Vc_imageLicense"  data-target="#lincens-img" disabled/>
										</div>
									</div>
									<div class="form-item">
										<div class="form-item-imgpreview">
											<img src="<?php echo $data["Vc_imageLicense"];?>" id="lincens-img" style="max-width:257px;">
										</div>
									</div>
								</form>
							</div>
						<?php }else{ ?>
							<iframe name="uploadFrame" style="display:none"></iframe>
							<div class="fl fs0 m-form">
								<form action="/index.php?act=user&m=account&w=authcompany" method="post" id="renzheng" autocomplete="on" target="uploadFrame" enctype="multipart/form-data">
									<div class="form-item">
										<label class="title">公司名称：</label><span class="form-item-text"><?php echo $data["Vc_name"];?></span>
										<input type="text" class="hidden" name="Vc_name" value="<?php echo $data["Vc_name"];?>"/>
										<input type="text" class="hidden" value="<?php echo $data["id"];?>" name="id"/>
									</div>
									<div class="form-item">
										<label class="title">公司地址：</label><span class="form-item-text"><?php echo $data["Vc_address_company"];?></span>
									</div>
									<div class="form-item">
										<label class="title">法人姓名：</label><input type="text" name="Vc_lawname" data-rule="法人姓名:required;chinese;"/>
									</div>
									<div class="form-item">
										<label class="title">法人身份证号：</label><input type="text" name="Vc_cityrenID"/>
									</div>
									<div class="form-item">
										<label class="title">身份证正反面：</label>
										<div class="pr form-item-wrapper">
											<label for="idcard" class="form-item-file-label">选择图片</label>
											<input type="file" value="" id="idcard" name="Vc_imageID"  data-target="#idcard-img"/>
										</div>
										<span class="form-item-tips">请上传文件</span>
									</div>
									<div class="form-item">
										<div class="form-item-imgpreview">
											<img src="<?php  echo TPL_IMG;?>default.jpg" style="max-width:257px;" id="idcard-img">
										</div>
									</div>
									<div class="form-item">
										<label class="title">营业执照：</label>
										<div class="pr form-item-wrapper">
											<label for="license" class="form-item-file-label">选择图片</label>
											<input type="file" value="" id="license" name="Vc_imageLicense"  data-target="#lincens-img"/>
										</div>
										<span class="form-item-tips">请上传文件</span>
									</div>
									<div class="form-item">
										<div class="form-item-imgpreview">
											<img src="<?php  echo TPL_IMG;?>default.jpg" id="lincens-img" style="max-width:257px;">
										</div>
									</div>
									<!--<div class="form-item">
										<label class="title">税务登记证：</label>
										<div class="pr form-item-wrapper">
											<label for="file-idcard" class="form-item-file-label">选择图片</label>
											<input type="file" value="选择图片"/>
										</div>
										<span class="form-item-tips">请上传文件</span>
									</div>
									<div class="form-item">
										<div class="form-item-imgpreview">
											<img src="tpl/user/<?php  echo TPL_IMG;?>default.jpg" width="257" height="160">
										</div>
									</div>
									<div class="form-item">
										<label class="title">组织机构代码证：</label>
										<div class="pr form-item-wrapper">
											<label for="file-idcard" class="form-item-file-label">选择图片</label>
											<input type="file" value="选择图片"/>
										</div>
										<span class="form-item-tips">请上传文件</span>
									</div>
									<div class="form-item">
										<div class="form-item-imgpreview">
											<img src="tpl/user/<?php  echo TPL_IMG;?>default.jpg" width="257" height="160">
										</div>
									</div>-->
									<div class="form-item form-item-apply">
										<button type="submit" class="submit">保存</button>
										<button type="reset" class="reset">取消</button>
									</div>
								</form>
							</div>	
						<?php } ?>
            		</div>
            		<hr class="form-hr pa" />
                </div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
    <!-- 中间内容部分 -->
	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>

