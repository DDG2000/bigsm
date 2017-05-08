<?php if(!class_exists('raintpl')){exit;}?>    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>账户管理 - 账户安全</title>
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
            	<div class="management-wrapper">
            		<div class="crumblenav">
            			<a href="#">账户管理</a><span class="current">账户安全</span>
            		</div>
            		<div class="account-wrapper">
            			<div class="cb account-security-header">
            				<span class="fl account-security-title">账户安全：</span>
            				<div class="fl pr account-security-bar">
								<?php echo dump($data["safe"]);; ?>
                                <?php if( $data["safe"]==0 ){ ?>
                                    <?php echo $space = "";$level = "危险";;?>
                                <?php }elseif( $data["safe"]==1 ){ ?>
                                    <?php echo $space = "";$level = "一般";;?>
                                <?php }elseif( $data["safe"]==2 ){ ?>
                                    <?php echo $space = "";$level = "高";;?>
                                <?php } ?>
            					<div class="pa account-security-process level<?php echo $data["safe"];?>"></div>
            				</div>
            				<span class="fl account-security-level level-<?php echo $data["safe"];?>"><?php echo $level;?></span>
            			</div>
            			<ul class="account-security-content" id="js-accountinfo-change">
            				<li class="cb">
                                <?php echo $space1 = "";$number = $data["Vc_mobile"];$email = $data["Vc_Email"];;?>
            					<div class="fl account-security-item-status has"></div>
            					<span class="fl account-security-item-name">登录密码</span>
            					<div class="fl account-security-item-sumary">互联网账号存在被盗风险，建议您定期更改密码以保护账户安全</div>
            					<a href="##" class="fr account-security-btn change" data-origin="#js-changePass">修改</a>
            				</li>
            				<li class="cb">
            					<div class="fl account-security-item-status <?php if( $data["I_mobileauthenticate"] == 2 ){ ?>has<?php } ?>"></div>
            					<span class="fl account-security-item-name">手机验证</span>
            					<div class="fl account-security-item-sumary">您验证的手机：<?php echo $data["Vc_mobile"];?>若已丢失或停用，请立即更换，避免账户被盗</div>
            					<a href="##" class="fr account-security-btn change" data-origin="#js-changeMobile"><?php if( $data["I_mobileauthenticate"] == 2 ){ ?>修改<?php }else{ ?>立即验证<?php } ?></a>
            				</li>
            				<li class="cb no">
            					<div class="fl account-security-item-status <?php if( $data["I_Emailauthenticate"] == 2 ){ ?>has<?php } ?>"></div>
            					<span class="fl account-security-item-name">邮箱验证</span>
            					<div class="fl account-security-item-sumary">您验证的邮箱：<?php echo $data["Vc_Email"];?></div>
            					<a href="##" class="fr account-security-btn change" data-origin="#js-changeEmail"><?php if( $data["I_Emailauthenticate"] == 2 ){ ?>修改<?php }else{ ?>立即验证<?php } ?></a>
            				</li>
            			</ul>
            		</div>
            	</div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
	<div id="mask">
		<div class="mask-bg" active-close data-origin="#mask"></div>
		<div class="mask-content changeform changePass">
			<form action="/index.php?act=user&m=account&w=mdypass" id="js-changePass">
				<ul>
					<li>
						<label>旧密码：</label> <input type="password" name="oldpass">
					</li>
					<li>
						<label>新密码：</label> <input type="password" name="newpass">
					</li>
					<li>
						<label>确认密码：</label> <input type="password">
					</li>
				</ul>
				<button type="submit">确认修改</button>
			</form>
			<form action="/index.php?act=user&m=account&w=authemail" id="js-changeEmail">
				<ul>
					<li>
						<label>当前邮箱：</label> <input type="text" value="<?php echo $data["Vc_Email"];?>" disabled>
					</li>
					<li>
						<label>新邮箱：</label> <input type="text" name="new_email" data-rule="email;">
					</li>
				</ul>
				<button type="submit">确认修改</button>
			</form>
			<form action="/index.php?act=user&m=account&w=authmobile" id="js-changeMobile">
				<ul>
					<li>
						<label>旧手机号：</label> <input type="text" name="oldmobile">
					</li>
					<li>
						<label>新手机号：</label> <input type="text" name="Vc_mobile">
					</li>
					<li>
						<label>验证码：</label> <input type="text" name="activeCode" class="activeCode"><a href="javascript:;" class="btn-send" id="js-sendPass">发送</a>
					</li>
				</ul>
				<button type="submit">确认修改</button>
			</form>
			<a href="javascript:void(0);" class="mask-btn-close" active-close data-origin="#mask"><img src="<?php  echo TPL_IMG;?>pop_close.gif" title="关闭" alt="关闭"></a>
		</div>
	</div>
    <!-- 中间内容部分 -->
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>