/*jquery base [jstip need css]*/
if (typeof (console) == "undefined") { console = { log: function (s) { } }; }
var getCheckbox = function (name) { var r = []; $(":checkbox[name='" + name + "']").each(function () { if ($(this).attr("checked") != undefined) { r.push($(this).val()) } }); return r.join(",") }, getRadio = function (name) { var r = '', o = $(":radio[name='" + name + "']"); for (var i = 0; i < o.length; i++) { if (o.eq(i).attr("checked") != undefined) { r = o.eq(i).val(); break } } return r };
var iset = function (n, d) { d = d != undefined ? d : ''; return n != undefined ? n : d }, isetn = function (n) { return iset(n, 0) };
var humanMsg = { setup: function (appendTo, logName, msgOpacity) { humanMsg.msgID = 'humanMsg'; if (appendTo == undefined) appendTo = 'body'; if (logName == undefined) logName = 'Message Log'; humanMsg.msgOpacity = 0.95; if (msgOpacity != undefined) humanMsg.msgOpacity = parseFloat(msgOpacity); $(appendTo).append('<div id="' + humanMsg.msgID + '" class="humanMsg"><div class="round"></div><p></p><div class="round"></div></div>'); }, displayMsg: function (msg, ty) { if (msg == '') return; clearTimeout(humanMsg.t1); clearTimeout(humanMsg.t2); $('#' + humanMsg.msgID + ' p').html(msg); $('#' + humanMsg.msgID + '').show().animate({ opacity: humanMsg.msgOpacity }, 200); if (ty == undefined) { humanMsg.t1 = setTimeout("humanMsg.bindEvents()", 500); humanMsg.t2 = setTimeout("humanMsg.removeMsg()", 3000); } }, bindEvents: function () { $(window).mousemove(humanMsg.removeMsg).click(humanMsg.removeMsg).keypress(humanMsg.removeMsg) }, removeMsg: function () { $(window).unbind('mousemove', humanMsg.removeMsg).unbind('click', humanMsg.removeMsg).unbind('keypress', humanMsg.removeMsg); if ($('#' + humanMsg.msgID).css('opacity') > 0) { $('#' + humanMsg.msgID).animate({ opacity: 0 }, 500, function () { $(this).hide() }); } } };
function showJsTipFun(tips) { humanMsg.displayMsg(tips); }
function copyToClipBoard(txt) { if (window.clipboardData) { window.clipboardData.clearData(); window.clipboardData.setData("Text", txt); alert("复制成功！"); } else if (navigator.userAgent.indexOf("Opera") != -1) { window.location = txt; alert("复制成功！") } else if (window.netscape) { try { netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect"); } catch (e) { alert("被浏览器拒绝！\n请在浏览器地址栏输入'about:config'并回车\n然后将'signed.applets.codebase_principal_support'设置为'true'"); } var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard); if (!clip) { return; } var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable); if (!trans) { return; } trans.addDataFlavor('text/unicode'); var str = new Object(); var len = new Object(); var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString); var copytext = txt; str.data = copytext; trans.setTransferData("text/unicode", str, copytext.length * 2); var clipid = Components.interfaces.nsIClipboard; if (!clip) { return false; } clip.setData(trans, null, clipid.kGlobalClipboard); alert("复制成功！"); } }
function json2str(o) { var fmt = function (s) { if (typeof s == 'object' && s != null) { return json2str(s); } else { return /^(string|number)$/.test(typeof s) ? "'" + s + "'" : s; } }; var arr = []; for (var i in o) { arr.push("'" + i + "':" + fmt(o[i])); } return '{' + arr.join(',') + '}'; }
function str2json(s) { eval("var o = " + s); return o; }
function setCookie(name, value, hours, path) {
	var name = escape(name), value = escape(value), expires = new Date(); expires.setTime(expires.getTime() + hours * 3600000);
	path = path == "" ? "" : ";path=" + path;

	_expires = (typeof hours) == "string" ? "" : ";expires=" + expires.toUTCString(); document.cookie = name + "=" + value + _expires + path;
}
function getCookie(name) { var name = escape(name), allcookies = document.cookie; name += "="; var pos = allcookies.indexOf(name); if (pos != -1) { var start = pos + name.length, end = allcookies.indexOf(";", start); if (end == -1) end = allcookies.length; var value = allcookies.substring(start, end); return unescape(value); } else { return ""; } }
function addScriptTag(src) { var script = document.createElement('script'); script.setAttribute("type", "text/javascript"); script.src = src; document.body.appendChild(script) }
function isEmail(s) { var pattern = /^[a-zA-Z0-9_\-]{1,}@[a-zA-Z0-9_\-]{1,}\.[a-zA-Z0-9_\-.]{1,}$/; if (pattern.exec(s)) { return true; } return false }
function isMobile(s) { var pattern = /^0{0,1}(1[3578][0-9])[0-9]{8}$/; pattern = /^\d{11}$/; if (pattern.exec(s)) { return true; } return false }
function isTel(s) { var pattern = /^(([0\+]\d{2,3}-)?(0\d{2,3})-)?(\d{7,8})(-(\d{3,}))?$/; if (pattern.exec(s)) { return true; } return false }
function isPostalCode(s) { var pattern = /^[0-9]{6}$/; if (pattern.exec(s)) { return true; } return false }
function isCarCode(s) { var pattern = /^[\u4e00-\u9fa5]{1}[a-zA-Z]{1}[a-zA-Z_0-9]{3,6}$/; if (pattern.exec(s)) { return true; } return false }
function isDate(s) { var n = validateDate(s); if (n == 1) { return true; } return false }
function AddFavorite(url, t) { if (url == undefined) { url = location.href } if (t == undefined) { t = location.title } try { window.external.addFavorite(url, t) } catch (e) { try { window.sidebar.addPanel(t, url, "") } catch (e) { alert("加入收藏失败，请使用Ctrl+D进行添加"); } } }
function token_open(url) { var nw = 800, nh = 600, x = (screen.width - nw) / 2, y = (screen.height - nh) / 2; var newwin = window.open(url, '', 'width=' + nw + ',height=' + nh + ',toolbar=0,menubar=0,scrollbars=0, resizable=1,location=1,status=1'); newwin.moveTo(x, y); newwin.focus(); }
function getUrlParam(name) { var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); var r = window.location.search.substr(1).match(reg); if (r != null) return decodeURIComponent(r[2]); return ""; }
function clearEmpty(o) { o.value = o.value.replace(/(\s*)/g, "") }
function numed(no) { no = no.replace(/[^\d.]/g, "").replace(/^\./g, ""); no = no.replace(".", "$#$").replace(/\./g, "").replace("$#$", "."); return no };

function checkCardID(code) {
	code = code.replace(/(^\s*)|(\s*$)/g, "");
	var city = { 11: "北京", 12: "天津", 13: "河北", 14: "山西", 15: "内蒙古", 21: "辽宁", 22: "吉林", 23: "黑龙江 ", 31: "上海", 32: "江苏", 33: "浙江", 34: "安徽", 35: "福建", 36: "江西", 37: "山东", 41: "河南", 42: "湖北 ", 43: "湖南", 44: "广东", 45: "广西", 46: "海南", 50: "重庆", 51: "四川", 52: "贵州", 53: "云南", 54: "西藏 ", 61: "陕西", 62: "甘肃", 63: "青海", 64: "宁夏", 65: "新疆", 71: "台湾", 81: "香港", 82: "澳门", 91: "国外 " };
	if (!code || !/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)?$/i.test(code)) { return [-1, '身份证号格式错误']; }
	if (!city[code.substr(0, 2)]) { return [-2, '地址编码错误']; }
	if (code.length == 18) {
		var sbday = code.substr(6, 4) + "/" + parseInt(code.substr(10, 2)) + "/" + parseInt(code.substr(12, 2));
		var d = new Date(sbday);
		if (sbday != (d.getFullYear() + "/" + (d.getMonth() + 1) + "/" + d.getDate())) { return [-3, '生日有误']; }
		code = code.split('');
		var factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
		var parity = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];
		var sum = 0;
		for (var i = 0; i < 17; i++) {
			sum += code[i] * factor[i];
		}
		var last = parity[sum % 11];
		if (parity[sum % 11].toString() != code[17].toString().toUpperCase()) { return [-4, '校验位错误']; }
	} else if (code.length == 15) {
		var y = code.substr(6, 2), m = parseInt(code.substr(8, 2)), day = parseInt(code.substr(10, 2));
		var d = new Date(y, m - 1, day);
		if (y != (d.getYear() || m != d.getMonth() + 1 || day != d.getDate())) { return [-32, '生日有误']; }
	}
	return [1, 'OK'];
}
var pageCfg = { w: 990 };
pageCfg.Common_GoTop = function (htmls) { this.show = false; this.attr = { id: "goTopd", "class": "goTopd" }; if ($.browser.msie && $.browser.version < 7) { this.attr["class"] += " goTopd_ie6"; } $("<div>").attr(this.attr).appendTo("body"); var gt = $("#goTopd"), str = '<a class="goTop" href="#"></a>'; str += htmls != undefined ? htmls : ''; gt.html(str); gt.find(".goTop").bind("click", function () { pageCfg.Common_ToTop(); return false; }); function initCss() { var a = 0; if ($(window).width() > pageCfg.w) { a = ($(window).width() - pageCfg.w) / 2 - gt.width() - 5; a = a > 0 ? a : 0; } gt.css({ right: a }); if ($.browser.msie && $.browser.version <= 6) { var b = $(window).height() + $(window).scrollTop() - gt.height() - 80; b = b > 0 ? b : 0; gt.css({ top: b }); } } initCss(); var c; $(window).bind("scroll", function () { if (c) { clearTimeout(c) } c = setTimeout(function () { if ($(window).scrollTop() > 0) { if (!this.show) { gt.fadeIn(); this.show = true } } else { if (this.show) { gt.fadeOut(); this.show = false } } }, 500); if ($.browser.msie && $.browser.version <= 6) { initCss(); } }); $(window).bind("resize", function () { initCss() }); };
pageCfg.Common_ToTop = function () { var c = null; var b = $(window).scrollTop(); if ($.browser.msie) { window.scrollTo(0, 0) } else { function a() { b = Math.floor(b / 2); window.scrollTo(0, b); if (b > 2) { c = setTimeout(a, 40) } else { window.scrollTo(0, 0); clearTimeout(c) } } a() } };

$(function () {
	humanMsg.setup();
	//是否登录（需要自动登录）
	if (getCookie('username') != "" && $("#user_area .nologin:visible").length == 1) { checkUserLg(); }

	//分页函数中跳转
	$("#pages").delegate(":input.gotopage", "keyup", function () { $(this).val($(this).val().replace(/\D/g, '')) });
	$("#pages").delegate(".b_gotopage", "click", function () {
		var t = $(this), p = t.parent(), isgo = p.attr("goto"), pn = p.find(".gotopage"), n = pn.val(), uparam = pn.attr("data");
		if (n == '') { showJsTipFun('请输入跳转页面！'); pn.focus(); return false; }
		if (isgo == undefined) {
			location.href = '?page=' + n + uparam;
		}
		if (isgo.substr(isgo.length - 3) == "Fun") {
			eval(isgo + "(" + n + ");");
		}
	});

	//状态检查
	humanAlert.setup();
	$(".nuscheck").click(function () {
		var usflag = usjson == undefined ? -404 : usjson.flag;
		return nuscheck(usflag, '', $(this).attr("href"));
	});

	//
	$(document).delegate(".popup .popup_close", "click", function () {
		var t = $(this), p = t.parent().parent().parent();
		p.hide();
		if ($(".popup:visible").length == 0) { $(".yy").hide(); }
	});
});

function resetyzm(o) { o.find("img.yzmimg").css("cursor", "pointer").attr({ "src": web_core + "/user/yzm.php?tmp=" + Math.random() }); }


function loginback(o) {
	var a = $(".user_area"), n = a.find(".nologin"), m = a.find(".logined");
	if (o.flag == 1) {
		closeLoginPop();
		var u = o.user;
		$(".userid").html(u.uid);
		$(".username").html(u.uname);
		$(".usernickname").html(u.nickname);
		$(".messagenum").html(u.messagenum);
		if (u.photo != '' && u.photo != null) { m.find(".face img").attr("src", u.photo); }
		n.hide(); m.show();
		//延迟执行未登录时的行为
		var loginedFuns = getCookie("loginGoToFuns");
		if (loginedFuns != '') {
			try { eval(loginedFuns); } catch (e) { console.log(e); }
			setCookie('loginGoToFuns', '', -1, '/');
		}
	} else { n.show(); m.hide(); }
}
//关注贷款
function addfavo(id) {
	var t = $(".addfavo[data='" + id + "']"), _w = t.attr("iuf") == '1' ? 'delfavo' : 'addfavo';
	var param = { w: _w, tmp: Math.random() };
	param.id = id;
	$.ajax({
		url: "/index.php?act=user&m=userprocess&ajax=1", type: 'post', dataType: 'json', data: param, success: function (d) {
			if (d.flag == 1 || d.flag == -101) {
				showJsTipFun(d.msg);
				_w == 'addfavo' ? (t.attr("iuf", 1).html('取消收藏')) : (t.attr("iuf", 0).html('收&nbsp;藏'));
			}
			else { showJsTipFun(d.msg); }
		}
	});
}


//扩展 数组去重复(保留前一个)
Array.prototype.distinct = function () { var newArr = [], obj = {}; for (var i = 0, len = this.length; i < len; i++) { if (!obj[typeof (this[i]) + this[i]]) { newArr.push(this[i]); obj[typeof (this[i]) + this[i]] = 'new'; } } return newArr; };
/**
 * 将数值四舍五入(保留2位小数)后格式化成金额形式
 * @param num 数值(Number或者String)
 * @return 金额格式的字符串,如'1,234,567'
 * @type String
 */
function formatCurrency(num) {
    num = num.toString().replace(/\$|\,/g, '');
    if (isNaN(num))
		num = "0";
    sign = (num == (num = Math.abs(num)));
	cents = '';
    num = Math.round(num).toString();//
    for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
		num = num.substring(0, num.length - (4 * i + 3)) + ',' +
			num.substring(num.length - (4 * i + 3));
    return (((sign) ? '' : '-') + num + cents);
}


/*日期验证 1:正确 <1错误*/
function validateDate(dates) {
	var ore = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/
	if (!ore.test(dates)) return -1;
	var r = dates.split(RegExp.$1);
	var iday = parseInt(r[2], 10), iyear = parseInt(r[0], 10), imon = parseInt(r[1], 10);
	if (imon > 12 || imon < 1) { return -2; }
	var monsday = { '1': 31, '3': 31, '4': 30, '5': 31, '6': 30, '7': 31, '8': 31, '9': 30, '10': 31, '11': 30, '12': 31 };
	if (monsday[imon] != null) { if (iday <= monsday[imon] && iday != 0) { return 1; } }
	if (imon - 2 == 0) {
		var booLeapYear = (iyear % 4 == 0 && (iyear % 100 != 0 || iyear % 400 == 0));
		if (((booLeapYear && iday <= 29) || (!booLeapYear && iday <= 28)) && iday != 0) { return 1; }
	}
	return 0;
}

/*弹窗关闭*/
$.fn.popupclose = function () {
	return this.each(function () {
		var _t = $(this);
		_t.delegate(".close", "click", function () {
			$(".yy").hide(); _t.hide();
			return false;
		});
	});
};
/*光标定位到输入框内容最后*/
$.fn.foucsend = function () {
	return this.each(function () {
		var _t = $(this).get(0), sPos = $(this).val().length;
		if (_t.setSelectionRange) {
			setTimeout(function () {
				_t.setSelectionRange(sPos, sPos);
				_t.focus();
			}, 0);
		} else if (_t.createTextRange) {
			var rng = _t.createTextRange();
			rng.move('character', sPos);
			rng.select();
		}
	});
}

var humanAlert = {
	setup: function (appendTo) {
		humanAlert.msgID = 'humanAlert'; if (appendTo == undefined) appendTo = 'body';
		if ($(appendTo).find(".yy").length == 0) { $(appendTo).append('<div class="yy"></div>'); }
		var s = '<div id="' + humanAlert.msgID + '" class="pop popup" style="top:50%;left:50%;margin-left:-210px;margin-top:-125px;">';
		s += ' <div class="pbg clearfix">';
		s += '  <div class="error"></div>';
		s += '  <div class="info">';
		s += '    <div class="text">-==-</div>';
		s += '    <div class="button"><a href="javascript:;">确定</a></div>';
		s += '  </div>';
		s += '  <div class="close"><a href="javascript:humanAlert.close();" title="关闭"></a></div>';
		s += ' </div>';
		s += '</div>';
		$(appendTo).append(s);
	}, show: function (msg, T, url) {
		if (msg == '') return;
		jss = '';
		if (url == '' || url == 'BACK') {
			jss = 'history.back();';
		} else if (url == 'CLOSE') {
			jss = 'window.close();';
		} else if (url == 'CLOSEPOP') {
			jss = 'humanAlert.close();';
		} else {
			if (T == '') {
				jss = 'location.href="' + url + '";';
			} else if (T == 'open') {
				jss = 'window.open("' + url + '");void(0);';
			} else if (T == 'openclose') {
				jss = 'window.open("' + url + '");humanAlert.close();void(0);';
			} else if (T == 'to-core-div') {
				jss = 'humanAlert.close();to_core_div("' + url + '");void(0);';
			}
		}
		$('#' + humanAlert.msgID + ' .button a').attr("href", 'javascript:' + jss + '');
		$('#' + humanAlert.msgID + ' .text').html(msg);
		$('.yy').show();
		$('#' + humanAlert.msgID + '').fadeIn();
	}, close: function () {
		$('#' + humanAlert.msgID).animate({ opacity: 0 }, 500, function () { $(this).css({ opacity: 1 }).hide(); $(".yy").hide(); });
	}
};
function to_core_div(url, divname) {
	if (divname == null || '' == divname || 'null' == divname) {
		divname = "#core_div";//父框架中的唯一ID标识
	}
	$.get(url,
		function (data) {
			$(divname).html(data);
		}
	);
}
/*检查用户状态*/
function nuscheck(usflag, T, href) {
	if (usflag == -404) {
		humanAlert.show("您需要登录！", T, web_core + "/index.php?act=user&m=public&w=login");
		return false;
	} else if (usflag == -11) {
		var url = web_core + "/index.php?act=user&m=account&w=safe&ty=email#email";
		humanAlert.show("您需要<span>认证邮箱</span>,请前往<a href='" + url + "'>安全中心</a>认证！", T, url);
		return false;
	} else if (usflag == -12) {
		var url = web_core + "/index.php?act=user&m=account&w=safe&ty=mobile#mobile";
		humanAlert.show("您需要<span>认证手机</span>,请前往<a href='" + url + "'>安全中心</a>认证！", T, url);
		return false;
	} else if (usflag == -13) {
		humanAlert.show("您尚未开通易宝，需要立即开通！", T, web_core + "/index.php?act=user&m=fund&w=yiji");
		return false;
	} else if (usflag == -15) {
		var url = web_core + "/index.php?act=user";
		humanAlert.show("您的会员是未激活状态，请激活会员", T, url);
		return false;
	} else if (usflag == -16) {
		var url = web_core + "/index.php?act=user";
		humanAlert.show("您的会员是已过期状态，请续费充值", T, url);
		return false;
	} else if (usflag == -18) {
		var url = web_core + "/index.php?act=user&m=fund&w=mmmauto&ajax=1";
		humanAlert.show("您还没有授权二次分配", T, url);
		return false;
	}
	return true;
}






/*
form check
*/
var checkform = function (o) {
	var valid = true, len = 0, mlen = o.find(":input").length;
	o.find(":input").each(function () {
		var _t = $(this), tip = "", val = _t.val(), isc = _t.attr("isc"), _tp = _t.attr("tp"), _tps = _t.attr("tps");
		if (isc != undefined && (_t.attr("nisc") == undefined || val != "")) {
			if (val == "" || val == null) { valid = false; tip = "不能为空"; }
			if (valid) {
				if (isc.substr(isc.length - 3) == "Fun") {
					var ro = eval(isc + "('" + val + "')");
					if (!ro.flag) { valid = false; tip = ro.err };
				} else if (isc.substr(0, 6) == "MaxLen") {
					var mL = parseInt(isc.substr(6));
					if (val.length > mL) { valid = false; tip = "长度不能超过" + mL; }
				}
			}
		}
		if (!valid) {
			if (_tps == undefined) { _tps = '【' + _tp + '】' + tip + ''; }
			humanMsg.displayMsg('<span class="indent">' + _tps + '</span>');
			if (_t.attr("nofocus") == undefined) { this.focus(); }
			return false;
		}
	});
	return valid;
};
//针对select元素必选,且不为零
function select0Fun(v) {
	if (v == 0) { return { "flag": false, "err": "需要选择" }; }
	return { "flag": true };
}
function cardCodeFun(v) {
	if (v == '') { return { "flag": false, "err": "不能为空" }; }
	var r = checkCardID(v);
	if (r[0] < 1) { console.log(r[1]); return { "flag": false, "err": "有误！" }; }
	return { "flag": true };
}
function telCheckFun(v) {
	if (!isTel(v)) { return { "flag": false, "err": "格式有误！" }; }
	return { "flag": true };
}
function mobileCheckFun(v) {
	if (!isMobile(v)) { return { "flag": false, "err": "格式有误！" }; }
	return { "flag": true };
}
//日期格式检查
function datepatFun(v) {
	if (!isDate(v)) { return { "flag": false, "err": "格式有误" }; }
	return { "flag": true };
}
function nodatepatFun(v) {
	if (v == "") return { "flag": true };
	return datepatFun(v);
}
function passwdFun(v) {
	if (!v.match(/^\S{6,50}$/)) { return { "flag": false, "err": "格式有误" }; }
	return { "flag": true };
}
//带两位小数的格式检查
function float2poiFun(v) {
	var r = float2poi0Fun(v); if (!r.flag) return r;
	if (v <= 0) { return { "flag": false, "err": "不是一个有效的数字" }; }
	return r;
}
function float2poi0Fun(v) {
	if (!v.match(/^\d+(\.\d{1,2})?$/)) { return { "flag": false, "err": "只允许输入数字，且小数点保留两位" }; }
	return { "flag": true };
}
//数字验证
function intFun(v) {
	var r = int0Fun(v); if (!r.flag) return r;
	if (v <= 0) { return { "flag": false, "err": "不是一个有效的数字" }; }
	return { "flag": true };
}
function int0Fun(v) {
	if (!v.match(/^\d+$/)) { return { "flag": false, "err": "只允许输入数字" }; }
	return { "flag": true };
}


function getPageStrFunSd_JS(w, url) {
	$("#" + w).attr("src", url);

}

function setloginUrl() {
	return location.href = "/core/index.php?act=user&m=public&w=login&refer=" + encodeURI(window.location.href);
}

$(function () {
	$(document).on('click', '.clickable', function () {
		var $dom = $(this);
		var href = $dom.data('href'), target = $dom.data('target');
		if (href) {
			if (target) {
				window.open(href);
			} else {
				window.location.href = href;
				//window.parent.frames[0].location.href = href ;
			}
		}
	});
});