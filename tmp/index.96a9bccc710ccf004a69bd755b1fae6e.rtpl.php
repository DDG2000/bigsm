<?php if(!class_exists('raintpl')){exit;}?>	
	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/css") . ( substr("inc/css",-1,1) != "/" ? "/" : "" ) . basename("inc/css") );?>
    <title>首页</title>
</head>
<body>
	<script type="text/html" id="sideNav">
		<% if(_data.length) {%>
			<% for(var i = 0;i<_data.length;i++){ %>
				<li class="<% if(i<1){ %>first <% } %>toggle-panel-li">
					<a href="#" class="toggle-panel-a"><%= _data[i].Vc_name; %></a>
					<div class="pa toggle-submenu">
						<div class="toggle-submenu-tab-content">
							<% _data[i].subItem.forEach(function(subitem,index) {%>
								<div class="item">
									<span class="ib item-name"><%= subitem.itemType; %></span>
									<div class="ib item-inner">
										<% subitem.items.forEach(function(name) {%>
											<a href="javascript:void(0);" data-id=""><%= name.Vc_name %></a>
										<% }) %>
									</div>
								</div>
							<% }) %>
						</div>
					</div>
				</li>
			<% } %>
		<% } %>
	</script>
    <!-- 头部 -->
    <div id="header" class="index">
        <div class="c w1100 cb top">
            你好！欢迎来到聚能
            <a href="http://www.bigsm.com/index.php?act=user&m=public&w=login#" class="ib top-userMenu top-userMenu-login">立即登录</a>
			<a href="http://www.bigsm.com/index.php?act=user&m=public&w=register#" class="ib top-userMenu">立即注册</a>
            <div class="fr top-userCtr">
                <a href="http://www.bigsm.com/index.php?act=user&m=index#" class="ib">我的聚能</a>
                <em>|</em>
                <a href="http://www.bigsm.com/index.php?act=shop&m=home#" class="ib">卖家中心</a>
                <span class="ib">交易中心：8:00-17:00</span>
            </div>
        </div>
        <div class="header-con index">
            <div class="c cb w1100">
                <a href="http://www.bigsm.com#" class="fl logo index"><h1>聚能、实时、快捷、全能服务</h1></a>
                <span class="fl header-con-title index">钢材市场</span>
                <form class="fl header-search">
                	<input type="text"/><button type="submit" accesskey="enter"><img src="<?php  echo TPL_IMG;?>search.png" alt="搜索" title="搜索"/></button>
                </form>
                <a href="http://www.bigsm.com/index.php?act=user&m=shopcar&w=list##" class="tac fr wg-cart">
                	<img src="<?php  echo TPL_IMG;?>wg-cart.png#" width="26" height="20">购物车<em class="cart-num">1</em>
                </a>
            </div>
        </div>
    </div>
    <!-- 头部 -->
    <div class="cb w mainnav-steel">
    	<div class="c cb w1100">
    		<div class="fl pr steel-sidenav">
    			<div class="toggle-menu">
    				<img src="<?php  echo TPL_IMG;?>toggle_menu.png" width="21" height="16">逛市场
    			</div>
    			<div class="pa toggle-panel hide">
    				<ul class="pa db-a toggle-panel-ul" id="side-nav">
    				</ul>
    			</div>
    		</div>
    		<!-- 侧边菜单栏  -->
			<div class="fl fl-li oh steel-mininav-wrapper">
    			<ul class="w110 cb">
					<li class="active">
						<a href="http://www.bigsm.com/index.php?act=mall&m=searchlist##">自营商城</a>
					</li>
					<li>
						<a href="http://www.bigsm.com/index.php?act=mall&m=searchlist&mallID=2##">撮合市场</a>
					</li>
					<li>
						<a href="http://www.bigsm.com/index.php?act=shop&m=shopcredit&w=searchlist##">商铺信用</a>
					</li>
					<li>
						<a href="http://www.bigsm.com/index.php?act=requirement&m=searchlist##">需求信息</a>
					</li>
					<li>
						<a href="http://www.bigsm.com/index.php?act=user&m=concentrateindex&w=searchlist##">终端采集</a>
					</li>
					<li>
						<a href="##">金融服务</a>
					</li>
					<li>
						<a href="##">仓储物流</a>
					</li>
					<li>
						<a href="##">产品加工</a>
					</li>
					<li>
						<a href="##">行业资讯</a>
					</li>
		    	</ul>
    		</div>
    		<!--         -->
    	</div>
    </div>
	<div class="w1100 c cb steel-wrapper1">
		<div id="pop-banner" class="pr fl">
			<ul class="oh list db-a">
				<li style="background: url(<?php  echo TPL_IMG;?>popbanner.png##) center no-repeat;" class="first"><a href="#"></a></li>
				<li style="background: url(<?php  echo TPL_IMG;?>popbanner.png) center no-repeat;"><a href="#"></a></li>
				<li style="background: url(<?php  echo TPL_IMG;?>popbanner.png) center no-repeat;"><a href="#"></a></li>
			</ul>
			<div class="pa w ib-a fs0 tac btn">
				<a href="##" class="active"></a>
				<a href="##"></a>
				<a href="##"></a>
			</div>
		</div>
		<div class="fr">
			<div class="mod-view mod-view-collection">
				<div class="cb mod-view-head">
					<span>采购需求</span><a href="#" class="more fr">更多></a>
				</div>
				<ul class="mod-view-con">
					<li><a href="##">100吨鞍钢螺纹钢采购计划.....</a></li>
					<li><a href="##">300吨板材采购计划.....</a></li>
					<li><a href="##">450吨型材采购计划.....</a></li>
					<li><a href="##">长城商贸有限公司采购计划.....</a></li>
				</ul>
			</div>
			<div class="mt10 mod-view mod-view-total">
				<div class="mod-view-head" type="2">
					<span>昨日成交</span>
				</div>
				<div class="mod-view-con">
					<p>155,179.02    吨</p>
					<p>9,759,179.02  元</p>
				</div>
			</div>
		</div>
	</div>
	<!-- 中间内容部分 -->
    <div id="container" class="c cb w1100">
    	<div class="ad-steel mt12 fl-a cb">
    		<a href="#"><img src="<?php  echo TPL_IMG;?>user/steel_ad1.png" width="360" height="200"/></a>
    		<a href="#"><img src="<?php  echo TPL_IMG;?>user/steel_ad2.png" width="360" height="200"/></a>
    		<a href="#" class="no"><img src="<?php  echo TPL_IMG;?>user/steel_ad3.png#" width="360" height="200"/></a>
    	</div>
    	<div class="cb mt12 mod-steel">
    		<div class="fl oh steel-main-left">
    			<!-- 服务 -->
				<ul class="w110  cb fl-li ib-i ib-a service">
    				<li>
    					<i class="i1"></i>
    					<h3>我有计划要采购</h3>
    					<p>瞬间获取厂家报价<br />自动对比排序分析<br />立即点击，开始采购</p>
    					<a href="##">- MORE -</a>
    				</li>
    				<li>
    					<i class="i2"></i>
    					<h3>我有现货要采购</h3>
    					<p>新计划短信通知<br />让你不错过每一个计划，<br />立即点击，开始报价</p>
    					<a href="##">- MORE -</a>
    				</li>
    				<li>
    					<i class="i3"></i>
    					<h3>我有项目要资金</h3>
    					<p row="1">上百亿的资金等着你来拿</p>
    					<a href="##">- MORE -</a>
    				</li>
    				<li>
    					<i class="i4"></i>
    					<h3>我有资金要项目</h3>
    					<p>年化回报12%-36%<br />各种项目等你选任你挑</p>
    					<a href="##">- MORE -</a>
    				</li>
    				<li>
    					<i class="i5"></i>
    					<h3>我需要工程设备</h3>
    					<p>我需要工程设备<br />我平台出租工程设备，<br />先用后付款，不收取资金利息</p>
    					<a href="##">- MORE -</a>
    				</li>
    				<li>
    					<i class="i6"></i>
    					<h3>我有设备要出租</h3>
    					<p>闲置工程设备，赚取每月固定收益<br />如果你有闲置设备加入我平台，<br />每月平台按时给予租金，免去收款烦恼。</p>
    					<a href="##">- MORE -</a>
    				</li>
    			</ul>
    			<!-- 服务 -->
    			<!-- 采集信息 -->
    			<div class="steel-collection">
    				<div class="mod-steel-title">
    					<h2>采集信息</h2>
    				</div>
    				<div class="cb mod-steel-con">
    					<ul class="fl fl-li steel-collection-list">
							<?php $counter1=-1; if( isset($concentrated) && is_array($concentrated) && sizeof($concentrated) ) foreach( $concentrated as $key1 => $value1 ){ $counter1++; ?>
    						<li>
    							<p>采集名称：<?php echo $value1["Vc_name"];?></p>
    							<p>采集名品名：<?php echo $value1["Vc_itemnames"];?></p>
    							<p>采集重量：<?php echo $value1["N_weight"];?>吨</p>
    							<p>项目地址：<?php echo $value1["address"];?></p>
    							<p>采集期限：<?php echo $value1["D_start"];?>——<?php echo $value1["D_end"];?></p>
    						</li>
							<?php } ?>
    					</ul>
    					<div class="fr steel-collection-action">
    						<div class="steel-collection-pub">
    							<div class="action-title">发布采集</div>
    							<ul class="steel-collection-pub-con" type='ul'>
    								<li><em>1</em>填写采集简要</li>
    								<li><em>2</em>上传标书文档</li>
    								<li><em>3</em>提交发布申请</li>
    								<li><em>4</em>发布成功</li>
    							</ul>
    							<a href="##" class="cb">&nbsp;&nbsp;去发布<span class="fr">>></span></a>
    						</div>
    						<div class="steel-collection-pub mt10">
    							<div class="action-title">发布采集</div>
    							<div class="steel-collection-pub-con" type='text'>
    								我有实力，去投标
    							</div>
    							<a href="##" class="cb">&nbsp;&nbsp;报名投标<span class="fr">>></span></a>
    						</div>
    					</div>
    				</div>
    			</div>
    			<!-- 采集信息 -->
    		</div>
    		<div class="fr steel-main-right">
    			<div class="viewpop">
    				<div class="viewpop-head fs0" line="2">
    					<h3 class="ib vmt">成交动态</h3>
    					<div class="ib view-head-txt">
    						<p>总成交量<span class="fr span">1000万吨</span></p>
    						<p>总成交金额<span class="fr">960万元</span></p>
    					</div>
    				</div>
    				<div class="viewpop-con" colum="4">
    					<div class="cb row" type="head">
    						<div class="colum1" type="colum">公司名称</div>
    						<div class="colum2" type="colum">物资类型</div>
    						<div class="colum3" type="colum">物资总量</div>
    						<div class="colum4" type="colum">成交金额</div>
    					</div>
    					<div class="oh viewpop-con-body">
    						<div class="pr viewpop-con-body-wrapper">
    							<div class="row" type="body">
		    						<div class="colum1" type="colum">成都长城**公司</div>
		    						<div class="colum2" type="colum">钢材</div>
		    						<div class="colum3" type="colum">100吨</div>
		    						<div class="colum4" type="colum">50万元</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">成都长城**公司</div>
		    						<div class="colum2" type="colum">钢材</div>
		    						<div class="colum3" type="colum">100吨</div>
		    						<div class="colum4" type="colum">50万元</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">成都长城**公司</div>
		    						<div class="colum2" type="colum">钢材</div>
		    						<div class="colum3" type="colum">100吨</div>
		    						<div class="colum4" type="colum">50万元</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">成都长城**公司</div>
		    						<div class="colum2" type="colum">钢材</div>
		    						<div class="colum3" type="colum">100吨</div>
		    						<div class="colum4" type="colum">50万元</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">成都长城**公司</div>
		    						<div class="colum2" type="colum">钢材</div>
		    						<div class="colum3" type="colum">100吨</div>
		    						<div class="colum4" type="colum">50万元</div>
		    					</div>
    						</div>
    					</div>
    				</div>
    			</div>
    			<div class="mt12 viewpop">
    				<div class="viewpop-head fs0" line="1">
    					<h3 class="ib vmt">成交项目</h3>
						<p class="ib">累计成交金额<span class="fr span">2000万元</span></p>
    				</div>
    				<div class="viewpop-con" colum="4">
    					<div class="cb row" type="head">
    						<div class="colum1" type="colum">公司名称</div>
    						<div class="colum2" type="colum">项目名称</div>
    						<div class="colum3" type="colum">年化回报</div>
    						<div class="colum4" type="colum">成交金额</div>
    					</div>
    					<div class="oh viewpop-con-body">
    						<div class="pr viewpop-con-body-wrapper">
    							<div class="row" type="body">
		    						<div class="colum1" type="colum">成都长城**公司</div>
		    						<div class="colum2" type="colum">华润像**建设</div>
		    						<div class="colum3" type="colum">17%</div>
		    						<div class="colum4" type="colum">200万元</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">成都长城**公司</div>
		    						<div class="colum2" type="colum">华润像**建设</div>
		    						<div class="colum3" type="colum">17%</div>
		    						<div class="colum4" type="colum">200万元</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">成都长城**公司</div>
		    						<div class="colum2" type="colum">华润像**建设</div>
		    						<div class="colum3" type="colum">17%</div>
		    						<div class="colum4" type="colum">200万元</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">成都长城**公司</div>
		    						<div class="colum2" type="colum">华润像**建设</div>
		    						<div class="colum3" type="colum">17%</div>
		    						<div class="colum4" type="colum">200万元</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">成都长城**公司</div>
		    						<div class="colum2" type="colum">华润像**建设</div>
		    						<div class="colum3" type="colum">17%</div>
		    						<div class="colum4" type="colum">200万元</div>
		    					</div>
    						</div>
    					</div>
    				</div>
    			</div>
    			<div class="mt10 viewpop">
    				<div class="viewpop-head fs0" line="1">
    					<h3 class="ib vmt">成交设备</h3>
						<p class="ib">累计成交设备<span class="fr span">100台</span></p>
    				</div>
    				<div class="viewpop-con" colum="3">
    					<div class="cb row" type="head">
    						<div class="colum1" type="colum">设备名称</div>
    						<div class="colum2" type="colum">租金</div>
    						<div class="colum3" type="colum">租用时间</div>
    					</div>
    					<div class="oh viewpop-con-body">
    						<div class="pr viewpop-con-body-wrapper">
    							<div class="row" type="body">
		    						<div class="colum1" type="colum">切割机</div>
		    						<div class="colum2" type="colum">150元/天</div>
		    						<div class="colum3" type="colum">3个月</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">切割机</div>
		    						<div class="colum2" type="colum">150元/天</div>
		    						<div class="colum3" type="colum">3个月</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">切割机</div>
		    						<div class="colum2" type="colum">150元/天</div>
		    						<div class="colum3" type="colum">3个月</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">切割机</div>
		    						<div class="colum2" type="colum">150元/天</div>
		    						<div class="colum3" type="colum">3个月</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">切割机</div>
		    						<div class="colum2" type="colum">150元/天</div>
		    						<div class="colum3" type="colum">3个月</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">切割机</div>
		    						<div class="colum2" type="colum">150元/天</div>
		    						<div class="colum3" type="colum">3个月</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">切割机</div>
		    						<div class="colum2" type="colum">150元/天</div>
		    						<div class="colum3" type="colum">3个月</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">切割机</div>
		    						<div class="colum2" type="colum">150元/天</div>
		    						<div class="colum3" type="colum">3个月</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">切割机</div>
		    						<div class="colum2" type="colum">150元/天</div>
		    						<div class="colum3" type="colum">3个月</div>
		    					</div>
		    					<div class="row" type="body">
		    						<div class="colum1" type="colum">切割机</div>
		    						<div class="colum2" type="colum">150元/天</div>
		    						<div class="colum3" type="colum">3个月</div>
		    					</div>
    						</div>
    					</div>
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
    <!-- 中间内容部分 -->
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/footer") . ( substr("inc/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/footer") );?>
	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>
	<script type="text/javascript">
		var URL = 'http://www.bigsm.com/index.php?act=item&m=itemclasslist';
		var _data;
		$.ajax({
			type:"get",
			url:URL,
			dataType:"json",
			async:true,
			success:function(data){
				_data = data.itemClassList;
				$("#side-nav").html(ejs.render(document.getElementById('sideNav').innerHTML,_data));
			}
		});
	</script>
</body>
</html>
