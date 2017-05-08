<?php if(!class_exists('raintpl')){exit;}?>    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>卖家中心 - 设置报价类别</title>

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
            		<div class="companyinfo no">
            			<div class="fs0 m-form">
            				<form action="#" method="post" id="js-setGetType">
            					<div class="form-item chkbox" id="wg-radio">
                                    <label class="title">接收产品报价类别：</label>
                                    <?php $counter1=-1; if( isset($data["mallclass"]) && is_array($data["mallclass"]) && sizeof($data["mallclass"]) ) foreach( $data["mallclass"] as $key1 => $value1 ){ $counter1++; ?>
                                    <?php $vo=$this->var['vo']=$value1;?>
            						    <label class="chk<?php if( $vo["id"]==$I_mallclassID ){ ?> selected<?php } ?>"><?php echo $vo["Vc_name"];?></label><input type="radio" name="mallClassID" value="<?php echo $vo["id"];?>"<?php if( $vo["id"]==$I_mallclassID ){ ?> checked<?php } ?>>
                                    <?php } ?>
            					</div>
            					<div class="fs0 ib-a form-item chk-select" id="wg-checkbox">
									<input type="hidden" name="itemClassIds" class="ids">
									<?php $counter1=-1; if( isset($data["subitemclass"]) && is_array($data["subitemclass"]) && sizeof($data["subitemclass"]) ) foreach( $data["subitemclass"] as $key1 => $value1 ){ $counter1++; ?>
									<?php $vo=$this->var['vo']=$value1;?>
									<label for="chk<?php echo $vo["id"];?>"<?php if( isset($itemClassArr[$vo["id"]]) ){ ?> class="selected"<?php } ?>><?php echo $vo["Vc_name"];?></label>
									<input type="checkbox" value="<?php echo $vo["id"];?>" id="chk<?php echo $vo["id"];?>"<?php if( isset($itemClassArr[$vo["id"]]) ){ ?> checked<?php } ?>>
									<?php } ?>
								</div>
            					<input class="hidden" type="hidden" id="selected-value" value="1"/>
            					<button type="submit" class="submit">确定</button>
            				</form>
            			</div>
            		</div>
                </div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
    <!-- 中间内容部分 -->
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>
