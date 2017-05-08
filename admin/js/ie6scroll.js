/***
*解决iframe中页面width=100%时，当页面高度大于一屏时，ie6会同时出现垂直和水平滚动条
***/
function myBrowser(){
	var Sys = {};
	var ua = navigator.userAgent.toLowerCase();
	window.ActiveXObject ? Sys.ie = ua.match(/msie ([\d.]+)/)[1] :
	document.getBoxObjectFor ? Sys.firefox = ua.match(/firefox\/([\d.]+)/)[1] :
	window.MessageEvent && !document.getBoxObjectFor ? Sys.chrome = ua.match(/chrome\/([\d.]+)/)[1] :
	window.opera ? Sys.opera = ua.match(/opera.([\d.]+)/)[1] :
	window.openDatabase ? Sys.safari = ua.match(/version\/([\d.]+)/)[1] : 0;
	return Sys;
}
function addStyle(content){
    var style;
    if(document.all){    //IE
        style = document.createStyleSheet();
        style.cssText = content;
    }else{
        style = document.createElement("style");
        style.type = "text/css";
        style.textContent = content;
    }
    try{document.getElementsByTagName("head")[0].appendChild(style);}catch(e){}//IE Error:不支持此接口
}

function ie6scroll(){
	var h1 = document.body.clientHeight,h11 = document.documentElement.clientHeight;
	var Sys = myBrowser();
	if(Sys.ie == '6.0' && h1 > h11){
		addStyle("html{overflow-y: scroll;}");
	}
}
window.onload = function(){	ie6scroll();}
