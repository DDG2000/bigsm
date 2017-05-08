<?php if(!class_exists('raintpl')){exit;}?><div class="cb w mainnav-steel">
	<div class="c cb w1100">
		<div class="fl pr steel-sidenav">
			<div class="toggle-menu">
				<img src="tpl/inc/../image/toggle_menu.png" width="21" height="16">逛市场
			</div>
			<div class="pa toggle-panel hide">
				<script type="text/html" id="sideNav">
					<% if(side_nav_data.length) {%>
						<% for(var i = 0;i<side_nav_data.length;i++){ %>
							<li class="<% if(i<1){ %>first <% } %>toggle-panel-li">
								<a href="#" class="toggle-panel-a"><%= side_nav_data[i].Vc_name; %></a>
								<div class="pa toggle-submenu">
									<div class="toggle-submenu-tab-content">
										<% side_nav_data[i].subItem.forEach(function(subitem,index) {%>
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
				<ul class="pa db-a toggle-panel-ul" id="side-nav">
				</ul>
			</div>
		</div>
		<!-- 侧边菜单栏  -->
		<div class="fl fl-li oh steel-mininav-wrapper">
			<ul class="w110 cb">
	    		<li class="active">
	    			<a href="/index.php?act=mall&m=searchlist##">自营商城</a>
	    		</li>
	    		<li>
	    			<a href="/index.php?act=mall&m=searchlist&mallID=2##">撮合市场</a>
	    		</li>
	    		<li>
	    			<a href="/index.php?act=shop&m=shopcredit&w=searchlist##">商铺信用</a>
	    		</li>
	    		<li>
	    			<a href="/index.php?act=requirement&m=searchlist##">需求信息</a>
	    		</li>
	    		<li>
	    			<a href="/index.php?act=user&m=concentrateindex&w=searchlist##">终端采集</a>
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
		<!-- 侧边菜单栏  -->
	</div>
</div>