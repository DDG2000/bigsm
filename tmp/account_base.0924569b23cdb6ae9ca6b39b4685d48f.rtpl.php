<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>卖家中心 - 基本信息</title>
</head>
<body>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/shopcenter/header") . ( substr("inc/shopcenter/header",-1,1) != "/" ? "/" : "" ) . basename("inc/shopcenter/header") );?>
    <!-- 中间内容部分 -->
    <div id="content">
        <div class="w1100 c cb">
			<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/shopcenter/side") . ( substr("inc/shopcenter/side",-1,1) != "/" ? "/" : "" ) . basename("inc/shopcenter/side") );?>
            <!-- 内容区 -->
            <div class="fr content-main">
            	<div class="content-main-con no">
					<div class="cb content-main-con-head line">
						<span class="head-title">基本信息</span>
					</div>
					<?php if( $issubed==1 ){ ?>
						<?php $v=$this->var['v']=$data["baseinfo"];?>
						<form class="companyinfo cb" method="POST" id="js-baseInfo">
							<input  type="hidden" name="Vc_logo_pic" value="<?php echo $v["Vc_logo_pic"];?>"/>
							<div class="companyinfo-img fl">
								<a href="javascript:;" class="upload-btn">点击上传</a>
								<img src="<?php echo $v["Vc_logo_pic"];?>" width="100" height="100">
							</div>
							<div class="fl fs0 m-form">
								<div class="form-item">
									<label class="title">公司名称：</label><input type="text" name="Vc_name" value="<?php echo $v["Vc_name"];?>"/>
								</div>
								<div class="form-item">
									<label class="title">公司地址：</label><select name="I_provinceID"><option value="<?php echo $v["I_provinceID"];?>" selected><?php echo $v["proname"];?></option></select><select name="I_cityID"><option value="<?php echo $v["I_cityID"];?>" selected><?php echo $v["cityname"];?></option></select><select name="I_districtID"><option value="<?php echo $v["I_districtID"];?>" selected><?php echo $v["disname"];?></option></select>
								</div>
								<div class="form-item">
									<input type="text" name="Vc_address" value="<?php echo $v["Vc_address"];?>" class="address"/>
								</div>
								<div class="form-item">
									<label class="title">公司电话：</label><input type="text" name="Vc_phone" value="<?php echo $v["Vc_phone"];?>"/>
								</div>
								<div class="form-item">
									<label class="title">公司传真：</label><input type="text" name="Vc_fax" value="<?php echo $v["Vc_fax"];?>"/>
								</div>
								<div class="form-item">
									<label class="title">公司联系人：</label><input type="text" name="Vc_contact" value="<?php echo $v["Vc_contact"];?>"/>
								</div>
								<div class="form-item">
									<label class="title">联系人电话：</label><input type="text" name="Vc_contact_phone" value="<?php echo $v["Vc_contact_phone"];?>"/>
								</div>
								<div class="form-item">
									<label class="title">客服QQ：</label><input type="text" name="Vc_service_qq" value="<?php echo $v["Vc_service_qq"];?>"/>
								</div>
								<div class="form-item chkbox" id="wg-radio">
									<label class="title">经营范围：</label>
									<?php $counter1=-1; if( isset($data["mallclass"]) && is_array($data["mallclass"]) && sizeof($data["mallclass"]) ) foreach( $data["mallclass"] as $key1 => $value1 ){ $counter1++; ?>
									<?php $vo=$this->var['vo']=$value1;?>
										<label for="radio<?php echo $vo["id"];?>" class="chk<?php if( $vo["id"]==$v["I_mallclassID"] ){ ?> selected<?php } ?>"><?php echo $vo["Vc_name"];?></label>
										<input id="radio<?php echo $vo["id"];?>" type="radio" name="I_mallclassID" value="<?php echo $vo["id"];?>"<?php if( $vo["id"]==$v["I_mallclassID"] ){ ?> checked<?php } ?>>
									<?php } ?>
								</div>
								<div class="fs0 ib-a form-item chk-select" id="wg-checkbox">
									<input type="hidden" name="Vc_itemIds" class="ids">
									<?php $counter1=-1; if( isset($data["subitemclass"]) && is_array($data["subitemclass"]) && sizeof($data["subitemclass"]) ) foreach( $data["subitemclass"] as $key1 => $value1 ){ $counter1++; ?>
									<?php $vo=$this->var['vo']=$value1;?>
									<label for="chk<?php echo $vo["id"];?>"<?php if( isset($itemClassArr[$vo["id"]]) ){ ?> class="selected"<?php } ?>"><?php echo $vo["Vc_name"];?></label>
									<input type="checkbox" value="<?php echo $vo["id"];?>" id="chk<?php echo $vo["id"];?>" <?php if( isset($itemClassArr[$vo["id"]]) ){ ?>checked<?php } ?>>
									<?php } ?>
								</div>
								<button type="submit" class="submit">保存</button>
							</div>
						</form>
					<?php }else{ ?>
						<form class="companyinfo cb" action="http://www.bigsm.com/index.php?act=shop&m=account&w=base-save" enctype="multipart/form-data" method="POST" id="js-baseInfo">
							<input  type="hidden" name="Vc_logo_pic" value="" />
							<div class="companyinfo-img fl">
								<a href="javascript:;" class="upload-btn">点击上传</a>
								<img width="100" height="100" alt="跪求真相">
							</div>
							<div class="fl fs0 m-form">
								<div class="form-item">
									<label class="title">公司名称：</label><input type="text" name="Vc_name" value=""/>
								</div>
								<div class="form-item">
									<label class="title">公司地址：</label><select name="I_provinceID"><option>请选择</option></select><select name="I_cityID"><option>请选择</option></select><select name="I_districtID"><option>请选择</option></select>
								</div>
								<div class="form-item">
									<input type="text" name="Vc_address" class="address"/>
								</div>
								<div class="form-item">
									<label class="title">公司电话：</label><input type="text" name="Vc_phone"/>
								</div>
								<div class="form-item">
									<label class="title">公司传真：</label><input type="text" name="Vc_fax"/>
								</div>
								<div class="form-item">
									<label class="title">公司联系人：</label><input type="text" name="Vc_contact"/>
								</div>
								<div class="form-item">
									<label class="title">联系人电话：</label><input type="text" name="Vc_contact_phone"/>
								</div>
								<div class="form-item">
									<label class="title">客服QQ：</label><input type="text" name="Vc_service_qq"/>
								</div>
								<div class="form-item chkbox" id="wg-radio">
									<label class="title">经营范围：</label>
									<?php $counter1=-1; if( isset($data["mallclass"]) && is_array($data["mallclass"]) && sizeof($data["mallclass"]) ) foreach( $data["mallclass"] as $key1 => $value1 ){ $counter1++; ?>
									<?php $vo=$this->var['vo']=$value1;?>
										<label for="radio<?php echo $vo["id"];?>" class="chk<?php if( $counter1==0 ){ ?> selected<?php } ?>"><?php echo $vo["Vc_name"];?></label>
										<input type="radio" name="I_mallclassID" value="<?php echo $vo["id"];?>" id="radio<?php echo $vo["id"];?>" <?php if( $counter1==0 ){ ?> checked<?php } ?>>
									<?php } ?>
								</div>
								<div class="fs0 ib-a form-item chk-select" id="wg-checkbox">
									<input type="hidden" name="Vc_itemIds" class="ids">
									<?php $counter1=-1; if( isset($data["subitemclass"]) && is_array($data["subitemclass"]) && sizeof($data["subitemclass"]) ) foreach( $data["subitemclass"] as $key1 => $value1 ){ $counter1++; ?>
									<?php $vo=$this->var['vo']=$value1;?>
									<label for="chk<?php echo $vo["id"];?>"><?php echo $vo["Vc_name"];?></label>
									<input type="checkbox" value="<?php echo $vo["id"];?>" id="chk<?php echo $vo["id"];?>">
									<?php } ?>
								</div>
								<button type="submit" class="submit">保存</button>
							</div>
						</form>
					<?php } ?>
                </div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
	<div id="headimg-cut">
		<form>
			<input type="hidden" name="x">
			<input type="hidden" name="y">
			<input type="hidden" name="w">
			<input type="hidden" name="h">
			<input type="hidden" name="src">
			<input type="hidden" name="bi">
		</form>
		<input type="hidden" name="x">
		<input type="hidden" name="y">
		<input type="hidden" name="w">
		<input type="hidden" name="h">
		<input type="hidden" name="src">
		<input type="hidden" name="bi">
		<div class="smallimg">
			<img src="tpl/shop/#" width="100" height="100">
			<p>当前LOGO</p>
		</div>
		<div class="cutarea">
			<div class="cutarea-upload">
				<div class="cutarea-upload-filename">
					<label for="cutarea-file">选择文件</label><span></span>
				</div>
				<a href="javascript:;" class="cutarea-upload-btn">上传</a>
				<p>支持jpg、jpeg、gif、png图像格式，大小不超过2M</p>
			</div>
			<div class="cutarea-wrap">
				<img src="tpl/shop/#">
				<p>请先裁剪再保存，大小为100*100</p>
				<a href="javascript:;">保存图片</a>
			</div>
		</div>
	</div>
    <!-- 中间内容部分 -->
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>
