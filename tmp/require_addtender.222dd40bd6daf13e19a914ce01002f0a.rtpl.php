<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>需求管理 - 新增招标</title>
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
        			<a href="http://www.bigsm.com/index.php?act=user&m=requirecommodity&w=list">需求管理</a><a href="http://www.bigsm.com/index.php?act=user&m=require&w=listtender">我的招标需求</a><span class="current">发布招标需求</span>
        		</div>
            	<div class="content-main-con">
					<form class="tender-form" enctype="multipart/form-data" id="tender-form">
						<ul>
							<li><label class="title">招标项目</label><input type="text" name="Vc_name"></li>
							<li>
								<label class="title">招标时间</label><input type="text" name="D_start" class="input-date input-date-start" data-type="datepicker" data-rule="开始时间:require;date;">&nbsp;—&nbsp;<input type="text" name="D_end" class="input-date input-date-end" data-type="datepicker" data-rule="结束时间:require;date;match(gt, D_start, date);">
							</li>
							<li><label class="title">联系人</label><input type="text" name="Vc_contact"></li>
							<li><label class="title">联系方式</label><input type="text" name="Vc_contact_phone" data-rule="require;mobile;"></li>
							<!--<li>
								<label class="title">验证码</label><input type="text" name="yzm" class="yzm" data-rule="require;"><img src="http://www.bigsm.com/index.php?act=user&m=public&w=yzm" id="reyzm" width="110" height="32"/>
							</li>-->
							<input type="text" class="hidden" value="xxxx.xls" name="Vc_excel">
							<li><label class="title">标书</label><label class="label-file">选择文件<input type="file" name="file_exl" id="tender-file"></label><span class="filename">鑫能裕丰标书.xlsx</span></li>
							<li><label class="title"></label><button type="submit">提交</button></li>
						</ul>
					</form>
                </div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
    <!-- 中间内容部分 -->
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>
