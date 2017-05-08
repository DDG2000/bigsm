/*
	jquery base admin
*/

var basePath = (function() {var elements = document.getElementsByTagName('script');for (var i = 0, len = elements.length; i < len; i++) {	if (elements[i].src && elements[i].src.match(/.base.js/)) {return elements[i].src.substring(0, elements[i].src.lastIndexOf('/') - 2);}}return '';})();
hsparam={outlineType:'rounded-bwn',wrapperClassName:'draggable-bwn'}
$(function(){
	if(typeof(hs)!='undefined'){
		hs.showCredits=false;//默认标题
		hs.preserveContent = false;//html缓存
	}
	$(".hs").click(function(){
		//console.log($(this).attr("href"));
		var w=typeof($(this).attr("w"))=='undefined'?700:$(this).attr("w"),
			h=typeof($(this).attr("h"))=='undefined'?450:$(this).attr("h"),
			olt=typeof($(this).attr("olt"))=='undefined'?'before':'after';//页面加载前\后显示 
		hs.htmlExpand(this, $.extend({objectType:'iframe',width:w,height:h,headingText:$(this).attr("title"),objectLoadTime:olt}, hsparam) )
		return false;
	});
	if($("#hideframe").length==0){$('<iframe id="hideframe" name="hideframe" style="display:none;">').appendTo("body");}
	$(".h_s").click(function(){
		$(this).attr("target", "hideframe");
		var _url = $(this).attr("href");
		$(this).attr("href",_url+(_url.search(/\?/)>-1?'&':'?')+"obj=self");
	});
	$(".del").click(function(){
		var _t=$(this),IdList = "";
		$(":input[type=checkbox][name='"+_t.attr("rel")+"']").each(function(){
			if(!!$(this).attr("checked")){
				IdList += (IdList==""?"":",")+$(this).val();
			}
		});
		if(IdList == "") {showJsTipFun('请选择下列项！');return false;}
		_t.attr("target", "hideframe");
		_t.attr("href", _t.attr("href")+IdList+"&obj=self");
		if(_t.attr("href").toLowerCase().indexOf("delete")>-1){
			var title=typeof(_t.attr("title"))=='undefined'?'':'<br>'+_t.attr("title"),info = '是否确认['+_t.html()+']选中项'+title;
			return confirmFun($(this), info);
		}
	});
	$(".delall,.goto").click(function(){
		$(this).attr("target", "hideframe");
		$(this).attr("href", $(this).attr("href")+"&obj=self");
		return confirmFun($(this), $(this).attr("title"));
	});
	$(".ck_fan").click(function(){
		$(":input[type=checkbox][name='"+$(this).attr("rel")+"']").each(function(){
			if($(this).attr("disabled")!=undefined)return ;
			$(this).attr("checked",!$(this).attr("checked"));
		});
		//return false;
	});
	//列表页
	$(".table table").delegate("tr:not(.tr_bt)","mouseenter",function(){
		$(this).addClass("onmouseover1");
	}).delegate("tr:not(.tr_bt)","mouseleave",function(){
		$(this).removeClass("onmouseover1");
	});
	//表单验证
	//console.log($(".table table").find(":input"));
	$(".table").delegate(":input","focus",function(){
		$(this).addClass("input_now");
	}).delegate(":input","blur",function(){
		$(this).removeClass("input_now");
	});

	//表单验证[有isc属性的], nums=纯数字,isc可自定义为函数(form.checkfun.js 后缀Fun,前缀no_,则不验证空)
	$("form[check]").submit(function(){
		var valid=true,_form=$(this);
		if(_form.attr("issubmit")=="yes"){showJsTipFun('提交中...');return false;}
		$(this).find(":input[isc]").each(function(){
			var _t=$(this),tip="",val=_t.val().replace(/^\s+/,''),isc=_t.attr("isc");
			var name=_t.attr("name")!=undefined?_t.attr("name"):"";
			//不可空且内容需验证
			if(val == "" && _t.attr("ennull")==undefined && _t.attr("nisc")==undefined){
				if(name=="order"){
				  _t.val(0);
				}else{
				valid=false;tip="不能为空";
				}
			}else if(isc=="nums"){
				var pattern =/^\d+$/
				if(!pattern.exec(val)){valid=false;tip="应为数字";}
			}else if(isc=="email"){
				var pattern =/^[a-zA-Z0-9_\-]{1,}@[a-zA-Z0-9_\-]{1,}\.[a-zA-Z0-9_\-.]{1,}$/
				if(!pattern.exec(val)){valid=false;tip="格式不正确";}
			}else if(isc.substr(isc.length-3)=="Fun"){
				if( ( isc.substr(0,3)=='int' && (r=isc.match(/\[(\d+)-([\d|\*]+)\]/)) ) 
					|| ( isc.substr(0,5)=='float' && (r=isc.match(/\[([\d\.]+)-([\d|\*\.]+)\]/)) ) ){
					var ro=eval(isc.replace(r[0],"")+"('"+val+"','"+r[1]+"','"+r[2]+"')");
				}else{
					var ro=eval(isc+"('"+val+"')");
				}
				if(!ro.flag){valid=false;tip=ro.err};
			}else if(isc.substr(0,6)=="MaxLen"){
				var mL=parseInt(isc.substr(6));
				if(val.length>mL){valid=false;tip="长度不能超过"+mL;}
			}
			if(!valid){
				var tname = _t.parents().parents().find("td:first").html().replace(/<[^>]+>[\s\S]*<[^>]+>|：/g,"");
				if(tname=='') tname='输入项';
				showJsTipFun('<strong>错误:</strong> <span class="indent">['+tname+']'+tip+'</span>');
				if(_t.attr("nofocus")==undefined){ this.focus();}
				return false;
			}
		});
		if(this.name=="search"){
		  var v="";
		  $(this).find(":input").each(function(){
		    v+="&"+this.name+"="+encodeURIComponent(this.value);
		  });
		  self.location.href=this.action+v;
		  return false;
		}
		if(valid){ _form.attr("issubmit","yes"); }
		return valid;
	});
	$("form a[name='submit']").click(function(){
		var submit = $(this).parent().find(":input[name='sub__']")
		if(submit.length==0){
			submit=$('<input type="submit" name="sub__" style="display:none;">');
			$(this).after(submit);
		}
		submit.click();
	});
	$("form a[name='reset']").click(function(){$(this).parent().parent("form").get(0).reset();});
	//输入框获得焦点,弹出,有返回值
	$(":input[data-url]").focus(function(){
		//$(this).attr("disabled",'true');
		var w=typeof($(this).attr("w"))=='undefined'?700:$(this).attr("w"),h=typeof($(this).attr("h"))=='undefined'?450:$(this).attr("h");
		hs.htmlExpand(this, $.extend({objectType:'iframe',width:w,height:h,headingText:$(this).attr("title"),src:$(this).attr("data-url")+$(this).val()+"&returnInput="+$(this).attr("name")}, hsparam) )
		return false;
	});
	//输入框只能输入数字
	$(":input[type='number']").keyup(function(){ 
		$(this).val($(this).val().replace(/\D/g,''));
	});
	//select 美化
	var Chosen_config = {
      '.chzn-select'           : {disable_search_threshold:10},
      '.chzn-select-deselect'  : {allow_single_deselect:true},
      '.chzn-select-no-single' : {disable_search_threshold:10},
      '.chzn-select-no-results': {no_results_text:'Oops, nothing found!'}
    }
	if(typeof(Chosen)!="undefined"){
		for (var selector in Chosen_config) {
		  $(selector).chosen(Chosen_config[selector]);
		}
	}

	//hs tip
	var confirmFun = function(o, info){
		var attrs = typeof(o.attr("target"))=='undefined'?'':'target="'+o.attr("target")+'"';
		var htmls = '<div class="pop"><div class="text">'+info+'</div><div class="buttons"><a href="'+ o.attr("href") +'" '+attrs+' class="apop_bt1" onclick="hs.close(this)">确定</a><a href="#" onclick="hs.close(this)" class="apop_bt2">取消</a></div></div>';
		var _parent=o.parent();
		if(_parent.find(".highslide-maincontent").length==0){_parent.append('<div class="highslide-maincontent"></div>');}
		_parent.find(".highslide-maincontent").html(htmls);
		var w=typeof(o.attr("w"))=='undefined'?350:o.attr("w"),h=typeof(o.attr("h"))=='undefined'?160:o.attr("h");
		hs.htmlExpand(o.get(0), $.extend({width:w,height:h,headingText:"系统提示"}, hsparam));
		return false;
	}
	//reset Main Padding
	if(!!parent.isOneWin){
		$("#main").css({padding:"0 5px 5px"});
	}
	//reset mdy button
	resize.init();
	$(window).resize(function(){resize.doresizeh()});
});

//human alert check form
function showJsTipFun(tips){humanMsg.displayMsg(tips);}
function showPhpTipFun(obj){hs.htmlExpand(null, $.extend(obj, hsparam));}
//highslide-close nouse 
function isOneWin(){return false;}
//highslide-close nouse 
function hidePopWin(){
	$(".highslide-close a").click();
}
function reloadParent(){
	parent.window.location.replace(parent.window.location.href);
}
var clicks='';//delay actoin
function showTip(obj){
	$("form[check][issubmit='yes']").attr("issubmit","no");
	//console.log(obj);
	var pus='';
	if(obj.url=='CLOSE'){
		clicks='hidePopWin()';
	}else{
		clicks=obj.w+".location.href='"+obj.url+"';";
		if(obj.ty=='succ'){setTimeout('eval(clicks);',500);}
		if(obj.ty=='tourl'){eval(clicks);return true;}
	}
	//succ 和 error 类型使用 showJsTipFun
	if(obj.ty=='error' || obj.ty=='succ'){
		showJsTipFun(obj.str);
		return true;
	}
	var htmls = '<div class="pop"><div class="text"><img src="'+basePath+'/image/'+obj.ty+'.gif" style="vertical-align:middle;margin-right:15px;"/>'+obj.str+'</div><div class="buttons"><a href="#" onclick="'+ clicks +'" class="apop_bt1">确定</a>'+(obj.ty=='pause'?'<a href="#" onclick="hs.close(this)" class="apop_bt2">取消</a>':'')+'</div></div>';

	hs.htmlExpand(null, $.extend({width:400,height:170,headingText:"系统提示",maincontentText:htmls}, hsparam));
	if(obj.ty=='succ'){
		$(document).delegate(".highslide-close a", "click", function(){
			eval(clicks);
		});
	}
	return true;
}
var resize={
	init:function(){
		resize.t = setTimeout("resize.doresizeh()", 500);
	},
	doresizeh:function(){
		clearTimeout(resize.t);
		var wh=$(window).height(),bh=$("body").height();
		var fix = !!parent.isOneWin ? 'fix':'fix_old';
		$("#btndiv").removeClass("fix");
		if($("#btndiv")){
			if(wh>bh){
				$("#btndiv").removeClass(fix).addClass("btn clearfix");
				$("#main").css({paddingBottom:"0px"});
			}else{
				$("#btndiv").removeClass("btn clearfix").addClass(fix);
				$("#main").css({paddingBottom:"60px"});
			}
		}
	}
}


function copyToClipBoard(txt){if (window.clipboardData){window.clipboardData.clearData();window.clipboardData.setData("Text", txt);alert("复制成功！");} else if (navigator.userAgent.indexOf("Opera") != -1){window.location = txt;alert("复制成功！")} else if (window.netscape) {try {netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");}catch (e){alert("被浏览器拒绝！\n请在浏览器地址栏输入'about:config'并回车\n然后将'signed.applets.codebase_principal_support'设置为'true'");}var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);if (!clip){return;}var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);if (!trans){return;}trans.addDataFlavor('text/unicode');var str = new Object();var len = new Object();var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);var copytext = txt;str.data = copytext;trans.setTransferData("text/unicode",str,copytext.length*2);var clipid = Components.interfaces.nsIClipboard;if (!clip){return false;}clip.setData(trans,null,clipid.kGlobalClipboard);alert("复制成功！");}}
if(typeof(console)=="undefined"){console={log:function(s){}};}
