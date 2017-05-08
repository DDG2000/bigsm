<?php if(!class_exists('raintpl')){exit;}?>    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>账户管理 - 账户信息</title>
</head>
<body>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/header") . ( substr("inc/usercenter/header",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/header") );?>
    <!-- 中间内容部分 -->
    <div id="content">
        <div class="w1100 c cb">
			<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/side") . ( substr("inc/usercenter/side",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/side") );?>
            <!-- 内容区 -->
            <div class="fr content-main">
            	<div class="management-wrapper">
            		<div class="crumblenav">
            			<a href="#">账户管理</a><span class="current">账户信息</span>
            		</div>
        			<div class="cb account-info">
        				<div class="fl account-info-imgage">
        					<img src="<?php echo $user["Vc_photo"];?>" width="100" height="100">
        				</div>
        				<div class="fl account-info-content" id="js-accountInfo">
							<?php if( !empty($data) ){ ?>
								<table>
									<tr>
										<td class="title">真实姓名：</td>
										<td class="con" id="js-name"><?php echo $data["Vc_truename"];?></td>
										<td class="title">公司名称：</td>
										<td class="con2" id="js-cname"><?php echo $data["Vc_companyname"];?></td>
									</tr>
									<tr>
										<td class="title">手机号码：</td>
										<td class="con" id="js-mobile"><?php echo $data["Vc_mobile"];?></td>
										<td class="title">公司地址：</td>
										<td class="con2" id="js-caddress"><?php echo $data["Vc_address_company"];?></td>
									</tr>
									<tr>
										<td class="title">邮箱：</td>
										<td class="con" id="js-email"><?php echo $data["Vc_Email"];?></td>
										<td class="title">公司性质：</td>
										<td class="con2" id="js-cproperty"><?php echo $data["propname"];?></td>
									</tr>
									<tr>
										<td class="title">个人地址：</td>
										<td class="con" id="js-uaddress"><?php echo $data["Vc_address_user"];?></td>
									</tr>
								</table>
							<?php } ?>
        					
        				</div>
    					<ul class="fl account-info-operation" id="account-change">
    						<li><a href="javascript:;" data-origin="#js-name" data-fname="Vc_truename" data-remote="mdytruename">修改姓名</a></li>
    						<li><a href="/index.php?act=user&m=account&w=safe" target="_blank">修改手机号</a></li>
    						<li><a href="/index.php?act=user&m=account&w=safe" target="_blank">修改邮箱</a></li>
    						<li><a href="/index.php?act=user&m=account&w=safe" target="_blank">修改个人地址</a></li>
    						<li><a href="javascript:;" data-origin="#js-cname" data-fname="Vc_name" data-remote="mdycompanyname">修改公司名称</a></li>
    						<li class="no"><a href="javascript:;" data-origin="#js-cproperty" data-fname="VC_address" data-remote="mdycompanyadd">修改公司地址</a></li>
    					</ul>
        			</div>
            	</div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
	<!--<div id="#mask">
		<div class="mask-bg"></div>
		<div class="mask-content userinfo-content">
			<form action="##" method="GET">
				<ul>
					<li>
						<label>姓名</label>
						<input type="text">
					</li>
				</ul>
			</form>
			<a href="javascript:void(0);" class="mask-btn-close"><img src="<?php  echo TPL_IMG;?>pop_close.gif"</a>
		</div>
		
	</div>-->
    <!-- 中间内容部分 -->
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>