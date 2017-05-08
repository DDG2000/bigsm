
//密码长度检查
function pwdlenFun(v){
	var isb=!!v.match(/^\S{6,20}$/);
	if(!isb){ return {"flag":false,"err":"必须是以非空字符的6-20位组成"};}
	return {"flag":true};
}
//密码重复检查
function pwd2chkFun(v){
	if(v!=$('input[name="pwd"]').val()){ return {"flag":false,"err":"与新密码不一致"};}
	return {"flag":true};
}
//用户名格式检查
function namepatFun(v){
	if(v.length<2 || v.length>20){ return {"flag":false,"err":"用户名字符长度在[2-20]之间"};}
	if(!v.match(/^[a-zA-Z0-9_]{2,20}$/)){ return {"flag":false,"err":"只允许字母,数字,下划线"};}
	return {"flag":true};
}
//数字格式检查
function orderpatFun(v){
	if(!v.match(/^[0-9]+$/)){ return {"flag":false,"err":"只允许数字"};}
	return {"flag":true};
}
//带1-2位小数的格式检查
function float2poi0Fun(v){
	if(!v.match(/^\d+(\.\d{1,2})?$/)){ return {"flag":false,"err":"只允许输入数字，且最多保留两位小数"};}
	return {"flag":true};
}
//浮点数检查 非零
function float2poiFun(v){
	var r = float2poi0Fun(v);
	if(!r.flag)return r;
	if(v<=0){ return {"flag":false,"err":"不是一个有效的数字"};}
	return {"flag":true};
}
//权限标记格式检查
function popedompatFun(v){
	if(v.substr(-1)=='_'){ return {"flag":false,"err":"不完整"};}
	if(!v.toUpperCase().match(/^[A-Z0-9_]{2,50}$/)){ return {"flag":false,"err":"只允许字母,数字,下划线"};}
	return {"flag":true};
}
//IP地址格式检查
function ippatFun(v){
	if(checkIP(v)<1){ return {"flag":false,"err":"格式有误"};}
	return {"flag":true};
}
//文件名格式检查
function filenamepatFun(v){
	if(!v.match(/\.htm$/i)&&!v.match(/\.html$/i)&&!v.match(/\.php$/i)){ return {"flag":false,"err":"填写有误"};}
	return {"flag":true};
}
//广告图片尺寸提醒
function showWHFun(id){
	$.ajax({url:"Getwidth.php?Work=PROD&id="+id+"&tmp="+Math.random(),type:'post',dataType:'html',data:{},success:function(htmls){
		if(htmls != 'no'){
			var arrp = htmls.split('|||||');
			$("#f_wh").html(arrp[0]+"*"+arrp[1]);
		}
	}});
}
//针对select元素必选,且不为零
function select0Fun(v){
	if(v==0){ return {"flag":false,"err":"需要选择"};}
	return {"flag":true};
}
//针对select元素必选,且不为[请选择]
function select99Fun(v){
	if(v==99){ return {"flag":false,"err":"需要选择"};}
	return {"flag":true};
}
//网站介绍分类改变效果
function articleClassChangeFun(id){

}
//日期格式检查
function datepatFun(v){
	if(!v.match(/^[\d]{4}-[\d]{1,2}-[\d]{1,2}( \d{1,2}:\d{1,2}(:\d{1,2})?)?$/)){ return {"flag":false,"err":"格式有误"};}
	return {"flag":true};
}
function nodatepatFun(v){
	if(v=="") return {"flag":true};
	return datepatFun(v);
}
//生成卷批次名格式检查
function name4Fun(v){
	if(!v.match(/^[A-Za-z0-9_]{4}$/)){ return {"flag":false,"err":"格式有误"};}
	return {"flag":true};
}
//商品添加扩展属性随分类改变
function changCategoryFun(_cid,_jsonV){
	var _T_attribute= _jsonV!=undefined ? _jsonV:'';
	$.ajax({url:"MallProductCategoryAjax.php",type:'post',dataType:'html',data:{cid:_cid,T_attribute:_T_attribute,tmp:Math.random()},success:function(htmls){
		$("tr:[data='extend']").remove();
		if(htmls!=""){
			$("tr:contains('上架时间')").after(htmls);
			extendAttrFun();
		}
	}});
}
//商品扩展属性显示 init
function extendAttrFun(){
	$("tr:[data='extend']").each(function(){
		var _t=$(this),paid=_t.attr("parent_aid");
		if(paid!="0"){
			//add
			_t.hide();
			_t.find("span[parent_vid]").hide();
			//mdy
			$("tr:[data='extend'][aid='"+paid+"']").find(":checkbox,:radio").each(function(i){
				if($(this).attr("checked")=="checked"){
					_t.show();
					_t.find("span[parent_vid='"+$(this).val()+"']").show();
				}
			});
		}
	});
}
//商品添加扩展属性 二级关联(选择不同属性值 显示它的二级关联属性及，相应的值) 绑定点击事件
$(function(){
	$(".table").delegate("tr:[data='extend'] :checkbox, tr:[data='extend'] :radio","click",function(){
		var _put=$(this),_tr=_put.parent().parent().parent('tr'),aid=_tr.attr("aid"),vid=_put.val();
		var _child_tr = $("tr:[data='extend'][parent_aid='"+aid+"']");
		_child_tr.show();
		_tr.find(":checkbox,:radio").each(function(i){
			if($(this).attr("checked")=="checked"){
				_child_tr.find("span[parent_vid='"+$(this).val()+"']").show();
			}else{
				_child_tr.find("span[parent_vid='"+$(this).val()+"']").hide().find(":checkbox,:radio").removeAttr("checked");
			}
		});
	});
});

//手机电话二选一检查
function phonemobileFun(v){
	if($('input[name="phone"]').val()=='' && $('input[name="mobil"]').val()==''){ return {"flag":false,"err":"固定电话与移动电话二选一填写"};}
	return {"flag":true};
}








//判断IP地址字符串是否正确
function checkIP(ipStr){
	//格式校验 失败返回-1
	ipStr   =   ipStr.replace(/\s/g, "");
	var reg = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/;
	if (reg.test(ipStr)   ==   false) {
		return   -1;
	}
	//合法性校验 失败返回-2
	var arr = ipStr.split(".");
	for (var i=0;i <4;i++) {
		arr[i]   =   parseInt(arr[i],10);
		if(parseInt(arr[i],10)   >   255) {	
			return   -2;
		}
	}
	var ip = arr.join(".");
	//返回IP地址的类型   包括:
	//异常:0   A类:1   B类:2   C类:3   D类:4   E类:5   A类私有:6   B类私有:7   C类私有:8   本机IP:9   广播地址:10
	//A类子网掩码:11   B类子网掩码:12   C类子网掩码:13
	var retVal  =   0;
	var n       =   arr[0];
	if (ip == "255.255.255.255 ") retVal   =   10;
	else if(ip  ==   "255.255.255.0 ") retVal   =   13;
	else if(ip  ==   "255.255.0.0 ")   retVal   =   12;
	else if(ip  ==   "255.0.0.0 ")     retVal   =   11;
	else if(ip  ==   "0.0.0.0 " || ip == "127.0.0.1 ")   retVal   =   9;
	else if(n   <=   126) retVal   =   (n   ==   10    ?   6   :   1);
	else if(n   <=   191) retVal   =   (n   ==   172   ?   7   :   2);
	else if(n   <=   223) retVal   =   (n   ==   192   ?   8   :   3);
	else if(n   <=   239) retVal   =   4;
	else if(n   <=   255) retVal   =   5;
	return   retVal;
}


//数字有效范围
function intAangeFun(vv,imin,imax){
	if(isNaN(vv)){ return {"flag":false,"err":"请输入有效值！"};}
	v = parseInt(vv);
	var Imin=parseInt(imin),Imax=parseInt(imax);
	if(imax=='*'){
		if(v<Imin){return {"flag":false,"err":"必须不小于"+Imin};}
		if(vv.length>10){return {"flag":false,"err":"过长了"};}
	}else{
		if(v<Imin || v>Imax){ return {"flag":false,"err":"必须在["+Imin+"-"+Imax+"]之间"};}
	}
	return {"flag":true};
}
function floatAangeFun(vv,imin,imax){
	if(isNaN(vv)){ return {"flag":false,"err":"请输入有效值！"};}
	v = parseFloat(vv);
	var Imin=parseFloat(imin),Imax=parseFloat(imax);
	if(imax=='*'){
		if(v<Imin){return {"flag":false,"err":"必须不小于"+Imin};}
		if(vv.length>14){return {"flag":false,"err":"过长了"};}
	}else{
		if(v<Imin || v>Imax){ return {"flag":false,"err":"必须在["+Imin+"-"+Imax+"]之间"};}
	}
	return {"flag":true};
}


function dynamicFloatRangeFun (val , minDom , maxDom) {
	if(isNaN(val)){ 
		return {"flag":false,"err":"请输入有效值！"};
	}
	val = parseFloat(val) ;
	var max = parseFloat($(maxDom).val()) , min = parseFloat($(minDom).val()) ;
	var isMax = val == max ;
	if (!isNaN(max) && !isNaN(min)) {
		if (min > max) {
			return {
				"flag" : false ,
				"err" : isMax ? "不得小于["+min+"]" : "不得大于["+max+"]"
			} ;
		}
	}
	return {"flag":true};
}







