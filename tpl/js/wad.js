/**
*广告，type：1-图片，2-swf，3-txt
		ty:扩展字符-特殊处理
**/
var SNAIL_WAD=function(pm){
	var o=$.extend({"type":0,"info":"","link":"","w":0,"h":0,"ty":""},pm),s='';
	if(o.type==0){return true;}
	if(o.type==3){
		s='<a href="'+o.link+'" target="_blank">'+o.info+'</a>';
	}else if(o.type==1){
		s='<a href="'+o.link+'" target="_blank"><img src="'+o.info+'" style="width:'+o.w+'px;height:'+o.h+'px;"/></a>';
	}else if(o.type==2){
		var fid="",flashvars="";
		s='<a href="'+o.link+'" target="_blank"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="'+o.w+'" height="'+o.h+'" id="'+fid+'"><param name="movie" value="'+o.info+'"><param name="quality" value="high"> <param name="allowFullScreen" value="true" /><param name="flashvars" value="'+flashvars+'"><param name="wmode" value="transparent"><embed src="'+o.info+'" wmode="transparent" quality="high" allowFullScreen="true" pluginspage="http://www.macromedia.com/go/getflashplayer"  type="application/x-shockwave-flash" width="'+o.w+'" height="'+o.h+'" flashvars="'+flashvars+'" name="'+fid+'"></embed></object></a>';
	}
	if(o.ty.substr(0,2)=="AD"){
		try{
		document.getElementById(o.ty).innerHTML=s;
		}catch(e){console.log(o.ty+" id is not exist!");}
	}else{
		document.write(s);
	}
}

$(function(){
	//广告通用处理
	$("div[ads]").each(function(){
		var t=$(this),ads=t.attr("ads"),id=t.attr("id");
		if(t.find(".cn").html()!=''){t.show();}
		t.find(".close").click(function(){
			console.log("ty-");
			if(id=='ADS_0001'){return ;}
			t.hide();
		});
		//针对随屏滚动处理
		var initcss=function(){
			if($.browser.msie && $.browser.version <= 6){
				var ht=$(window).scrollTop()+170;
				t.css({position:"absolute",top:ht});
			}
		};
		if(ads=="FL"||ads=="FR"){
			if($.browser.msie && $.browser.version <= 6){
				$(window).bind("scroll",function(){
					initcss();
				});
			}
		}
	});
	var SD_Close=function(o,t){
		if(t=='SlideUp'){
			o.slideUp();
		}
		else{o.hide();}
	};
	//首页大图
	$("#ADS_0001").find(".close").click(function(){
		SD_Close($("#ADS_0001"), 'SlideUp');
	});
	if($("#ADS_0001:visible").length==1){
		setTimeout(function(){
			SD_Close($("#ADS_0001"), 'SlideUp');
		}, 5000);
	}
	//左右对联
	//var FLR = $("#ADS_0002,#ADS_0003");
});


