<?php if(!class_exists('raintpl')){exit;}?><!-- 头部 -->
<div id="header" class="index">
    <div class="c w1100 cb top">
        <?php if( $lg ){ ?>
            你好！<?php echo $lg["hidemobile"];?>&nbsp;欢迎来到聚能<a href="/index.php?act=user&m=userprocess&w=lgout">退出</a>
        <?php }else{ ?>
            <a href="/index.php?act=user&m=public&w=login" class="ib top-userMenu top-userMenu-login">立即登录</a><a href="/index.php?act=user&m=public&w=register" class="ib top-userMenu">立即注册</a>
            <span style="padding-left:100px;">服务热线:028-11111111</span>
        <?php } ?>
        <div class="fr top-userCtr">
            <a href="/index.php?act=user&m=index#" class="ib">我的聚能</a>
            <em>|</em>
            <a href="/index.php?act=shop&m=home" class="ib">卖家中心</a>
            <span class="ib">交易中心：8:00-17:00</span>
        </div>
    </div>
    <div class="header-con index">
        <div class="c cb w1100">
            <a href="#" class="fl logo index"><h1>聚能、实时、快捷、全能服务</h1></a>
            <span class="fl header-con-title index">钢材市场</span>
            <form class="fl header-search">
            	<input type="text"/><button type="submit" accesskey="enter"><img src="<?php  echo TPL_IMG;?>search.png###" alt="搜索" title="搜索"/></button>
            </form>
            <a href="/index.php?act=user&m=shopcar&w=list##" class="tac fr wg-cart">
            	<img src="<?php  echo TPL_IMG;?>wg-cart.png" width="26" height="20">购物车<em class="cart-num">1</em>
            </a>
        </div>
    </div>
</div>
<!-- 头部 -->