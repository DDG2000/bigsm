(function ($) {
	
	$(function () {

		$(document).on("keypress", "[data-keypress]", function (event) {
			var $this = $(this);
			// console.log($this.data("keypress") == false);
			switch ($this.data("keypress")) {

				case false:
					event.preventDefault();
					return false;

				case "number":

					if (!(event.which <= 57 && event.which >= 48)) {
						return false;
					}

					break;
			}
		})


		/**
		 *  钢材市场首页，悬浮固定导航
		*/
		if($("#sideNav").length){
			$.ajax({
				type: "get",
				url: "/index.php?act=item&m=itemclasslist",
				dataType: "json",
				async: true,
				success: function (data) {
					if($("#sideNav").length){
						window.side_nav_data = data.itemClassList;
						$("#side-nav").html(ejs.render($("#sideNav").html(), side_nav_data));
					}
					
				},
				error: function (err) {
					console.log(err);
				}
			});
		}
		
		function getSelfShopList(opts) {
			//  筛选条件 
			dataLoader({
				url: "/index.php?act=item&m=conditionslist&issearch=1",
				success: function (data) {
					window.self_shop_filter = data;
					$("#self-shop-filter").html(ejs.render($("#self-shop-filter-template").html(), self_shop_filter))
					// renderClass(tempCurrent);
					var current = data.searchData;
					$.each(current, function (item, value) {
						$("#self-shop-filter [data-id='" + value + "'][data-type='" + item + "']").addClass('active').siblings().removeClass('active');
						// console.log("#self-shop-filter [data-id='" + value + "'][data-type='" + item + "']");
						$("#self-shop-filter-area [data-id='" + value + "'][data-type='" + item + "']").addClass('active').siblings().removeClass('active');
					})
				},
				//   失败
				error: function (code) {
					console.log('查询失败...');
				}

			}, opts);

			// 搜索结果处理
			dataLoader({
				url: "/index.php?act=item&m=searchlist&issearch=1",
				//  成功
				success: function (data) {
					
					if($("#self-shop-list-template").length){
						window.self_shop_list = data;
						$("#self-shop-list").html(ejs.render($("#self-shop-list-template").html(), self_shop_list));
						pagenumRender(self_shop_list);
					}
					
				},
				//   失败
				error: function (code) {
					console.log('查询失败...');
				}
			},
			opts);
		}

		$(document).on("click", "#pagenum a[data-id]", function () {
			var data = $(this).data("id");
			console.log(data);
			getSelfShopList({
				cpage: data,
				psize: 7
			})
		});

		$(document).on("click", "#pagenum a.pagenum-btn-target", function () {
			var data = $(this).prevAll('input').val();
			getSelfShopList({
				cpage: data,
				psize: 7
			})
		})


		function pagenumRender(data) {
			if ($("#pagenum-template").length) {
				window.pagenumData = data;
				$("#pagenum").html(ejs.render($("#pagenum-template").html(), data));
			} else {
				$.ajax({
					url: "/index.php?act=item&m=searchlist",
					dataType: "json",
					type: 'GET',
					success: function (data) {
						if($("#pagenum-template").length){
							window.pagenumData = data;
							$("#pagenum").html(ejs.render($("#pagenum-template").html(), data));
						}
						
					}
				})
			}
		}

		if($("#pagenum-template").length){
			pagenumRender();
		}


		/**
		 * @param{string} url 请求数据的url
		 * @param{object} opts 请求数据的筛选条件
		 */

		function dataLoader(settings, opts) {

			var callee = arguments.callee;

			if (!callee.cache) {
				callee.cache = {};
			}

			var cache = callee.cache;
			cache = $.extend(cache, opts);

			var url = settings.url;

			for (var name in cache) {

				url += "&" + name + "=" + cache[name];
				
				if(cache[name] != ""){
					//当data-id = 0是，为全部条件;	
					if (cache[name] == 0) {
						url = url.replace("&" + name + "=0", '');
						// console.log("&" + name + "=0");
					}
				}
				
			}

			$.ajax({
				url: url,
				dataType: "json",
				type: 'GET',
				success: function (data) {
					if (settings.success) {
						settings.success(data);
					}
				},
				error: function (code) {
					if (settings.error) {
						settings.error(code);
					}
				}
			});

		}

		var tempCurrent = [];

		function renderClass(arr) {

			arr.forEach(function (item) {
				$("#self-shop-filter [data-id='" + item.id + "'][data-type='" + item.type + "']").addClass("active").siblings().removeClass("active");
				// console.log("[data-id='" + item.id + "'][data-type='" + item.type + "']");
			})

		}

		//条件筛选
		$(document).on("click", "#self-shop-filter a", function () {

			tempCurrent.push({
				type: $(this).data('type'),
				id: $(this).data('id')
			});

			$(this).addClass('active').siblings().removeClass('active');

			var opts = {};
			var $id = $(this).data("id");
			var type = $(this).data("type");
			opts[type] = $id;

			getSelfShopList(opts);

		})

		$("#self-shop-filter-area label").click(function (event) {

			var opts = {},
				$id,
				type,
				chk = $(this).find('input[type="checkbox"]');

			if (chk.prop("checked")) {
				chk.prop("checked", false);
				$id = 0;
				$(this).removeClass('active');

			} else {
				chk.prop("checked", true);
				$id = chk.data("id");
				//  console.log('...');
				$(this).addClass('active');

			}
			type = chk.data("type");
			opts[type] = $id;
			getSelfShopList(opts);

			event.stopPropagation();
			event.preventDefault();

		})

		// 筛选 -- 城市
		$("#self-shop-filter-form input[text]").focus(function () {

			if ($(this).nextAll().children().length >= 1) {
				$(this).nextAll(".province-selector").slideDown(200);
			}

		});

		// 弹出选择框
		$("#self-shop-filter-form .province-selector a").click(function () {

			var $this = $(this);
			$this.parent().slideUp(100);
			$this.parent().prev().attr("value", $this.data("id"));
			$this.parent().prevAll('input[text]').val($(this).text());

		});

		$("#self-shop-filter-form li").mouseleave(function () {
			$(this).find(".province-selector").slideUp(200);
		});

		$("#self-shop-filter select").change(function () {

			var opts = {};
			opts['I_cityID'] = $(this).val();
			getSelfShopList(opts);

		});
			
		$.fn.serializeObject = function () {
			var i = {};
			var n = this.serializeArray();
			$.each(n, function () {
				if (i[this.name] !== undefined) {
					if (!i[this.name].push) {
						i[this.name] = [i[this.name]];
					}
					i[this.name].push($.trim(this.value) || "");
				} else {
					i[this.name] = $.trim(this.value) || "";
				}
			});
			return i;
		}
		
		$("#self-shop-filter-form").submit(function (event) {

			event.preventDefault();
			var data = $(this).serializeObject();

			console.log(data);

			getSelfShopList(data);

		});
		
	});
	
	(function () {
		
		var Order = commonJs.order;
		
		//   删除订单
		$("#order-list").on("click",".delete a",function(){
			var self = $(this);
			layer.confirm("确认删除该订单？删除后将不可恢复！",{
				icon:3,
				title:"提示信息"
			},function () { 
				
				Order.deletes(self.data("id"),function(code){
					if(code){
						layer.msg("删除成功");
						location.reload();
					}else{
						layer.msg("删除失败");
					}
				});
			 })
			
		});
		
		//   取消订单
		$("#order-list").on("click",".cancel",function(){
			if(window.confirm("确认取消该订单？取消后可重新下单")){
				Order.cancel($(this).data("id"),function(code){
					if(code){
						layer.msg("取消成功");
						location.reload();
					}else{
						layer.msg("取消失败");
					}
				});
			}
		});
		
	})()
	
})(jQuery);
