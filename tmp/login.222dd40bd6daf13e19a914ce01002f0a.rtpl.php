<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
	<title>登录页面</title>
	</head>
	<body>
		<div class="userformpage w1100 c">
			<div class="userform-head cb">
				<a href="#" class="logo2 vmt ib">聚能</a><span class="logo2-text">欢迎登录</span>
			</div>
			<div class="cb userform-content">
				<div class="fl login-img">
					<img src="<?php  echo TPL_IMG;?>login_img.jpg" width="731" height="393">
				</div>
				<div class="fr login-form">
					<div class="login-form-title">会员登录 <a href="/index.php?act=user&m=public&w=register" class="fr">立即注册</a></div>
					<form action="/index.php?act=user&m=userprocess&w=lg" method="post" class="login" id="login" autocomplete="off">
						<div class="pr login-item">
							<input type="text" name="username" placeholder="手机号或邮箱"/>
						</div>
						<div class="pr login-item">
							<input type="password" name="Vc_password" placeholder="密码"/>
						</div>
						<div class="pr login-item">
							<input type="text" name="yzm" placeholder="验证码" class="yzm"/>
							<img src="/index.php?act=user&m=public&w=yzm" onclick="" width="110" height="38" id="reyzm">
						</div>
						<div class="cb login-auto">
							<label class="fl check"><input type="checkbox" name="isremember" value="1">自动登录</label>
							<a href="#" class="fr">忘记密码</a>
						</div>
						<input type="submit" value="登录"/>
					</form>
				</div>
			</div>
			<div class="userformpage-footer">
				<ul class="w userpage-nav ib-li tac">
					<li><a href="#">关于我们</a></li>|
					<li><a href="#">网站公告</a></li>|
					<li><a href="#">人才招聘</a></li>|
					<li><a href="#">联系我们</a></li>|
					<li><a href="#">法律说明</a></li>|
					<li><a href="#">安全保障</a></li>
				</ul>
				<div class="w cprt tac">
					四川鑫能裕丰电子商务有限公司版权所有<em class="space"></em><a href="#">蜀ICP备140000000000号</a>
				</div>
			</div>
		</div>
		<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>
