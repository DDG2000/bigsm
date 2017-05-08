<?php if(!class_exists('raintpl')){exit;}?><!-- 头部 -->
<div id="header">
    <div class="c w1100 cb top">
        <?php if( $lg ){ ?>
            你好！<?php echo $lg["hidemobile"];?>&nbsp;欢迎来到聚能<a href="http://www.bigsm.com/index.php?act=user&m=userprocess&w=lgout" id="exit">退出</a>
        <?php }else{ ?>
            <a href="http://www.bigsm.com/index.php?act=user&m=public&w=login" class="ib top-userMenu top-userMenu-login">立即登录</a><a href="http://www.bigsm.com/index.php?act=user&m=public&w=register" class="ib top-userMenu">立即注册</a>
            <span style="padding-left:100px;">服务热线:028-11111111</span>
        <?php } ?>
        <div class="fr top-userCtr">
            <a href="http://www.bigsm.com/index.php?act=user&m=index#" class="ib">我的聚能</a>
            <em>|</em>
            <a href="http://www.bigsm.com/index.php?act=shop&m=home" class="ib">卖家中心</a>
            <span class="ib">交易时间：8:00-17:00</span>
        </div>
    </div>
    <div class="header-con">
        <div class="c cb w1100">
            <a href="http://www.bigsm.com#" class="fl logo"><h1>聚能</h1></a>
            <span class="fl header-con-title">卖家中心</span>
            <div class="con fl ib-a c-fff header-con-nav">
                <a href="http://www.bigsm.com#">钢材首页</a>
                <a href="http://www.bigsm.com/index.php?act=mall&m=searchlist#">自营商城</a>
                <a href="http://www.bigsm.com/index.php?act=user&m=message&w=list#">消息</a>
            </div>
            <form class="fr header-con-search fs0">
                <input type="text" class="ib vmm" value="" placeholder="请输入产品名称">
                <button type="submit" class="ib vmm">搜索</button>
            </form>
        </div>
    </div>
</div>
<!-- 头部 -->