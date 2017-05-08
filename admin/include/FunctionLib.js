/******************************************************************  
**创建者：李志勇
**创建时间：2008-7-25  
**本页：
**             js函数库
**说明：
******************************************************************/
//复选框的反选操作
function $n(o)
{
	return document.getElementsByName(o);
}
function SelectAllCheckBox(objName)
{
	var coll=$n(objName)
	if(!coll.length) return;
	if(coll.length){
		for(var i=0;i<coll.length;i++)
		{
			coll[i].checked=!coll[i].checked;
		}
	}else{
		$(objName).checked=!$(objName).checked;
	}
}

//取得复选框的选中项
function GetCheckBoxList(objName)
{
	var result = "";	
	var coll=$n(objName);
	if(!coll.length) 
	{
		return result;
	}
	if(coll.length){
		for(var i=0;i<coll.length;i++)
		{
			if(coll[i].checked)
			{
				result += (result == "")?coll[i].value:("," + coll[i].value);
			}
		}
	}else{
		return result;
	}
	return result;
}
//取得单选框的选中项
function GetRadioBox(objName)
{
    var Coll = $n(objName);
	if(!Coll) return null;
	if(Coll.length)
	{
		for(var i=0;i<Coll.length;i++)
		{
			if(Coll[i].checked)
			{
				return Coll[i].value;
			}
		}
		return null;
	}else{
		return Coll.checked?Coll.value:null;
	}
}

//对话框
function OpenMWin(url,w,h)
{
    var theDes = "status:no;center:yes;help:no;minimize:no;maximize:no;dialogWidth:"+w+"px;scroll:no;dialogHeight:"+h+"px;border:think";
    return self.showModalDialog(encodeURI(url),null,theDes);
}

//判断是否为日期
function IsDate(str)
{
	return (str.search(/^[\d]{4,4}\-[\d]{1,2}\-[\d]{1,2}[\s]{1,1}[\d]{1,2}\:[\d]{1,2}\:[\d]{1,2}$/i)!=-1?true:false);
}

function $(o)
{
    var value=document.getElementById(o);
	if (value)
	{
		return  value
	}
}
//判断IP地址字符串是否正确
function   checkIP(ipStr){
	//参数格式校验   成功继续,失败返回-1
	ipStr   =   ipStr.replace(/\s/g, "");
	var reg = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/;
	if (reg.test(ipStr)   ==   false)
	{
		return   -1;
	}
	//ip地址合法性校验   成功继续   ,失败返回-2
	var arr = ipStr.split(".");
	for (var i=0;i <4;i++)
	{
		arr[i]   =   parseInt(arr[i],10);
		if(parseInt(arr[i],10)   >   255)
		{	
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


//复制到剪切版
function copyToClipBoard(t)
{
	var txt=document.getElementById(t).value;
	if (window.clipboardData)
	{   
		window.clipboardData.clearData();
		window.clipboardData.setData("Text", txt);
		alert("复制成功！")
	} 
	else if (navigator.userAgent.indexOf("Opera") != -1) 
	{       
		window.location = txt;
		alert("复制成功！")
	} 
	else if (window.netscape) 
	{       
		try 
		{
			netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
		}catch (e)
		{       
			alert("被浏览器拒绝！\n请在浏览器地址栏输入'about:config'并回车\n然后将'signed.applets.codebase_principal_support'设置为'true'");
		}
		var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
		if (!clip)
		{
			return;
		}
		var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
		if (!trans)
		{
			return;
		}
		trans.addDataFlavor('text/unicode');
		var str = new Object();
		var len = new Object();
		var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
		var copytext = txt;
		str.data = copytext;
		trans.setTransferData("text/unicode",str,copytext.length*2);
		var clipid = Components.interfaces.nsIClipboard;
		if (!clip)
		{
			return false;
		}
		clip.setData(trans,null,clipid.kGlobalClipboard);
		alert("复制成功！")
	}
}
//创建DIV
function creatediv(id,w,h,css)
{
	if($(id))
	{
		$(id).style.display = 'block';
	}
	else
	{
	var div = document.createElement('div'); 
	div.id = id;
	if(css == '')
	{
		css = 'tran_css';   
	}
	div.className = css;  
	div.style.width = w + 'px';
	div.style.height = h + 'px';
	document.body.appendChild(div);
	}
}

function openwin(id)
{
	var id = $(id);
	if(!id) return;
	var w = document.body.clientWidth;;
	var h = document.body.clientHeight;
	creatediv('tran_div',w,h,'');
	id.style.position ='absolute';
	h = Math.max(document.body.scrollTop,document.documentElement.scrollTop) + 200; 
	w = (w /2) - (parseInt(id.style.width) / 2);
	id.style.left = w + 'px';
	id.style.top = h +'px';
	id.style.display = 'block';
}

//js 弹出提示
//str = 0 为弹出JS错的
//foc 光标
function Alert(info,str,foc,uu)
{
	var dn = 'alert_div';
	pk = foc;
	up = uu;
	if(!$(dn))
	{
		creatediv(dn,300,700,'tran_css1');
	}
	if(str == '')
	{
		$(dn).innerHTML ='<table border="0" cellspacing="0" cellpadding="0" id="main" align="center"><tr><td class="td_ct1"></td></tr><tr><td class="td_ct1"></td></tr><tr><td align="center"><table class="table_add1" cellpadding="0" cellspacing="0"></table></td></tr><tr><td align="center"><table class="table_add2_1" cellpadding="0" cellspacing="0"><tr><td class="td_add2_1" colspan="2" valign="top"><div class="td_add2_0">&nbsp;</div></td></tr><tr><td class="td_add2_7" colspan="2"><b class="color4" style="font-size:14px;">'+info+'</b></td></tr><tr><td class="td_add2_1" colspan="2"></td></tr><tr><td class="td_add2_6" ><input name="" type="button" class="button1" value="确 认" onclick="onclose();"/></td></tr><tr><td class="td_add2_1" colspan="2"></td></tr></table></td></tr><tr><td class="td_ct1"></td></tr></table>';
	}
	else
	{
		$(dn).innerHTML = '<table border="0" cellspacing="0" cellpadding="0" id="main" align="center"><tr><td class="td_ct1"></td></tr><tr><td class="td_ct1"></td></tr><tr><td align="center"><table class="table_add1" cellpadding="0" cellspacing="0"></table></td></tr><tr><td align="center"><table class="table_add2_1" cellpadding="0" cellspacing="0"><tr><td class="td_add2_1" colspan="2" valign="top"><div class="td_add2_0">&nbsp;</div></td></tr><tr><td class="td_add2_7" colspan="2"><b class="color4" style="font-size:14px;">'+info+'</b></td></tr><tr><td class="td_add2_1" colspan="2"></td></tr><tr><td class="td_add2_6" colspan="2"><input name="submit7" type="button" class="button1" value="确 认" onclick="onurl();"/>&nbsp;&nbsp;&nbsp;&nbsp;<input name="submit8" type="button" class="button1" onclick="onclose()" value="返 回"/></td></tr><tr><td class="td_add2_1" colspan="2"></td></tr></table></td></tr><tr><td class="td_ct1"></td></tr></table>';
	}
	openwin(dn);
}
//关闭窗口
function onclose()
{
	$('alert_div').style.display = 'none';
	$('tran_div').style.display  = 'none';
	if(pk) pk.focus();
}

//确定后跳转地址
function onurl()
{
   if(up)
   {
      parent.logininfo.location = up;
   }
}

function createXMLHttpRequest()
{
	var xmlHttp;

	if (window.XMLHttpRequest){

				var objXMLHttp = new XMLHttpRequest();

			}else{

				var MSXML = ['MSXML2.XMLHTTP.5.0', 'MSXML2.XMLHTTP.4.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP', 'Microsoft.XMLHTTP'];

				for(var n=0; n<MSXML.length; n++){

					try{

						var objXMLHttp = new ActiveXObject(MSXML[n]);

						break;

					}catch(e){}

				}

			}
	return objXMLHttp;
}