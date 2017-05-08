$(function(){
	//表单输入框 预提示 效果
	$(document).delegate(".inp .enter_psw,.inp :input","click",function(){
		$(this).parent().find(":input").focus();
	}).delegate(".inp :input","focusout",function(){
		var t=$(this),p=t.parent();if(p.find(":input").val()==''){p.find(".enter_psw").show();}
	}).delegate(".inp :input","focus keyup",function(){
		var t=$(this),p=t.parent();p.find(".enter_psw").hide();
	});
	
	var _isld=0;
	var f_reg = $("#f_reg"),f_lg = $("#f_lg"),o;
	var resetTip = function(o,isb,p){
		var oo=o.parent().find("em");
		if (2==isb) {
			oo.removeClass().hide() ;
		} else {
			oo.attr({"class":(isb?"yes":"no")}).show(),oo.html(isb?(p?p:"&nbsp;"):p) ;
		}
	};
	var baseResetTip = function(isb,p){
		var oobase=$("#f_lg").find("em");
		if(isb==2){oobase.removeClass().hide();}
		else{
			oobase.attr({"class":(isb?"yes":"no")}).show(),oobase.html(isb?(p?p:"&nbsp;"):p) ;
		}
	};
	
	//zys add 群发推荐邮件
	var f_email = $("#f_email");
	if(f_email.length>0){
		o=f_email;
		o.find("em").hide();
		resetyzm(o);
		o.find(".yzmimg").click(function(){resetyzm(o);});
		var put=[o.find("input[name='name']"),o.find("input[name='yzm']")];
		
		put[0].keyup(function(){
			var t=$(this),tv=t.val();
			if(tv!=''){$('.nicknames').html(tv);}
		});
		
		put[1].focusout(function(){
			if(_isld==1)return false;
			var t=$(this),tv=t.val();
			if(tv==''){resetTip(t, false, '请输入验证码');return;}
			$.ajax({url:"/index.php?act=user&m=userprocess&ajax=1&w=ckyzm",async:false,type:'post',dataType:'json',data:{skey:t.val(),tmp:Math.random()},success:function(o){
				if(o.flag<1){resetTip(t, false, o.msg);}
				else{resetTip(t,true);}
			}});
		}).focusin(function(){resetTip($(this), 2);});
		
		o.find(".btn_submit").click(function(){	
			if(o.find("em.no").length>0){console.log(o.find("em.no"));o.find(":input").focusout();return false;}
			showJsTipFun('邮件发送成功！');
			var param = {w:"sendEmail",tmp:Math.random()};
			param.name=put[0].val();			
			param.yzm=put[1].val();
			param.N=o.find("input[name='NCode']").val();
			param.emailList=o.find("input[name='emailList']").val();			
			if(_isld==1){showJsTipFun('提交中...请稍等片刻！');return false;}_isld=1;
			$.ajax({url:o.attr("data-url"),type:'post',dataType:'json',data:param,success:function(d){
				_isld=0;
				if(d.flag==1){_isld=1;
					showJsTipFun('邮件发送成功.');
					
				}
				else if(d.flag==-401){resetTip(put[2], false, '验证码错误');resetyzm(o);}
				else{showJsTipFun(d.msg);resetyzm(o);}
		
			}});
			
			setTimeout(function(){location.href='/index.php?act=user&m=account&w=introducer';},1000);
		});
		
		o.find(".btn_submit2").click(function(){
			$(".popup_close").click();
		});
	}	
	
	//注册
	if(f_reg.length>0){
		o=f_reg;
		o.find("em").hide();
		resetyzm(o);
		o.find(".yzmimg").click(function(){resetyzm(o);});
		o.find("input[name='upass']").keyup(function(){clearEmpty(this);});
		var put=[o.find("input[name='uname']"),o.find("input[name='upass']"),o.find("input[name='yzm']"),o.find("input[name='nickname']"),o.find("input[name='upass2']"),o.find("input[name='introducer']"),o.find("input[name='icy']")];
		put[0].emailMatch({wrapLayer:'#emailMathBox',emailTip:''});
		put[0].focusout(function(){
			var t=$(this),isb=!!t.val().match(/^[a-zA-Z0-9_\-]{1,}@[a-zA-Z0-9_\-]{1,}\.[a-zA-Z0-9_\-.]{1,}$/);
			if(!isb){
				if (t.val()!='') {
					resetTip(t, isb,'邮箱不正确');
					return;
				}
			}
			if (t.val()!='') {
				$.ajax({url:"/index.php?act=user&m=userprocess&ajax=1&w=ckname",type:'post',dataType:'json',data:{skey:t.val(),tmp:Math.random()},success:function(o){
					if(o.flag<1){resetTip(t, false, "该邮箱已使用");}
					else{resetTip(t,true);}
				}});
			} else {
				resetTip(t,true);
			}
		}).focusin(function(){resetTip($(this), 2)});
		put[3].focusout(function(){
			var t=$(this),tv=t.val(),isb=!!tv.match(/^[\u4e00-\u9fa5a-zA-Z0-9]{2,10}$/);
			if(!isb){resetTip(t, isb, tv!=''?(tv.length<2?'2－10个字母、汉字或数字':(tv.length>5?'2－10个字母、汉字或数字':'请使用字母、汉字或数字')):'请输入昵称');return;}
			$.ajax({url:"/index.php?act=user&m=userprocess&ajax=1&w=cknick",type:'post',dataType:'json',data:{skey:t.val(),tmp:Math.random()},success:function(o){
				if(o.flag<1){resetTip(t, false, o.msg);}
				else{resetTip(t,true, o.msg);}
			}});
		}).focusin(function(){resetTip($(this), 2)});
		put[1].focusout(function(){
			var t=$(this),tv=t.val(),isb=!!tv.match(/^\S{6,50}$/);
			resetTip(t, isb, tv!=''?(tv.length<6?'密码需要至少6位':'6-20位的非空字符'):'6-20位字母、字符或数字');
			if(put[4].val()!=""){
				put[4].focusout();
			}
		}).focusin(function(){resetTip($(this), 2)});
		put[4].focusout(function(){
			var t=$(this),tv=t.val(),isb = tv!=""&&tv==put[1].val();
			resetTip(t, isb, tv!=''?(tv!=put[1].val()?'两次密码输入不相同':''):'请再输入一次密码');
		}).focusin(function(){resetTip($(this), 2)});
		put[2].focusout(function(){
			if(_isld==1)return false;
			var t=$(this),tv=t.val();
			if(tv==''){resetTip(t, false, '请输入验证码');return;}
			$.ajax({url:"/index.php?act=user&m=userprocess&ajax=1&w=ckyzm",type:'post',dataType:'json',data:{skey:t.val(),tmp:Math.random()},success:function(o){
				if(o.flag<1){resetTip(t, false, o.msg);}
				else{resetTip(t,true);}
			}});
		}).focusin(function(){resetTip($(this), 2);});
		put[5].focusout(function(){
			var t=$(this),tv=t.val(),isb=!!tv.match(/^[0-9]{6}$/);//tv.match(/^[0-9a-zA-Z]{6}$/);
			// resetTip(t, isb, tv!=''?(tv.length!=6?'邀请码需要6位非空字符':'6位的非空字符'):'6位字母、字符或数字');
			// if(put[5].val()!=""){
			// 	put[5].focusout();
			// }
				if (!isb) {resetTip(t, isb, '请填写6位数字邀请码');return;}
				$.ajax({url:"index.php?act=user&m=userprocess&ajax=1&w=ckintrocode",type:'post',dataType:'json',data:{skey:tv,tmp:Math.random()},success:function(o){
					if(o.flag<1){resetTip(t, false, o.msg);}
					else{resetTip(t, true);}
				}});
		}).focusin(function(){resetTip($(this), 2)});
		o.find(".btn_submit").click(function(){
			if(o.find("input[name='is_check']").attr("checked")!='checked'){showJsTipFun('需要同意用户协议！');return false;}
			if(o.find("em.no").length>0){o.find(":input").focusout();return false;}
			if(_isld!=1){
				$(this).html("注册中...");
				if($("#loadimg").length <= 0){
					$(this).after('<img id="loadimg" style="margin-left:50px;width:32px;" src="tpl/inc/../image/loading.gif" >');
				}else{
					$("#loadimg").show();
				}
			}
			var param = {w:"reg",tmp:Math.random()};
			param.uname=put[0].val();
			param.nickname=put[3].val();
			param.upass=put[1].val();
			param.yzm=put[2].val();
			param.introducer=put[5].val();	
			param.icy=o.find("input[name='icy']").val();
			param.upass = encryptedString(rsaObj,param.upass);
			if(_isld==1){showJsTipFun('提交中...请稍等片刻！');return false;}_isld=1;
			
			$.ajax({url:o.attr("data-url"),type:'post',dataType:'json',data:param,success:function(d){
				_isld=0;
				if(d.flag==1){_isld=1;
					//$("#loadimg").hide();
					/*if(d.icy>0){//企业用户注册
						reg_company(3);
					}else{*/
						showJsTipFun('用户注册成功，请尽快完善实名认证信息！',1000);
						setTimeout(function(){location.href='/index.php?act=user&m=wizard';},500);//&m=fund&w=yiji
					//}
				}
				else if(d.flag==-401){
					resetTip(put[2], false, '验证码错误');resetyzm(o);
					o.find(".btn_submit").html("注册");
					$("#loadimg").hide();
				}
				else{showJsTipFun(d.msg);resetyzm(o);}
				
			}});
		});
		o.find(":input[name!='uname']").keyup(function(e){
            var key = e.which;
            if (key == 13) {
				o.find(".btn_submit").click();
            }
		});
	}
	//登录
	if(f_lg.length>0){
		
		o=f_lg;
		//o.find("em").hide();
		resetyzm(o);
		o.find(".yzmimg").click(function(){resetyzm(o);});
		var put=[o.find("input[name='uname']"),o.find("input[name='upass']"),o.find("input[name='yzm']")];
		var loginmail = getCookie("loginmail");
		if(loginmail!=''){
			//$("#emailMathBox").find('span').html('');
			loginmail = decodeURIComponent(loginmail) ;
			put[0].focus();
			put[0].val(loginmail);
			//put[0].parent().find('em').removeClass().hide();
		}
		//put[0].emailMatch({wrapLayer:'#emailMathBox',emailTip:''});
		put[0].focusout(function(){
			// var t=$(this),isb=!!t.val().match(/^[a-zA-Z0-9_\-]{1,}@[a-zA-Z0-9_\-]{1,}\.[a-zA-Z0-9_\-.]{1,}$/);
			// resetTip(t, isb, t.val()!=''?'邮箱格式错误':'请输入邮箱');
			var t=$(this),tv=t.val(),isb=tv!='';
			if(!isb)baseResetTip(isb, '请输入账号(邮箱/手机号/昵称)');
			if(isb){baseResetTip(2);}
		});
		put[1].focusout(function(){
			var t=$(this),tv=t.val(),isb=tv!=''; // !!tv.match(/^\S{6,50}$/);
			if(!isb)baseResetTip(isb, '请输入密码');
			if(isb){baseResetTip(2);}
		});
		put[2].focusout(function(){
			if(_isld==1)return false;
			var t=$(this),tv=t.val();
			if(tv==''){baseResetTip(false, '请输入验证码');return false;}
			else{
				$.ajax({url:"/index.php?act=user&m=userprocess&ajax=1&w=ckyzm",type:'post',dataType:'json',data:{skey:t.val(),tmp:Math.random()},success:function(o){
					if(o.flag<1){baseResetTip(false, o.msg);return false;}
					//else{baseResetTip(true);}
				}});
			}
		}).focusin(function(){put[0].focusout();put[1].focusout();});
		o.find(".btn_submit").click(function(){
			//if(o.find("em.no").length>0){o.find(":input").focusout();return false;}
			var isb;
			isb=put[0].val()!='';
			if(!isb){baseResetTip(isb, '请输入账号(邮箱/手机号/昵称)');return false;}
			isb=put[1].val()!='';
			if(!isb){baseResetTip(isb, '请输入密码');return false;}
			
			if(put[2].val()==''){baseResetTip(false, '请输入验证码');return false;}
			else{
				$.ajax({url:"/index.php?act=user&m=userprocess&ajax=1&w=ckyzm",type:'post',dataType:'json',data:{skey:put[2].val(),tmp:Math.random()},success:function(o){
					if(o.flag<1){baseResetTip(false, o.msg);return false;}
					//else{baseResetTip(true);}
				}});
			}

			var param = {w:"lg",tmp:Math.random()};
			param.uname=put[0].val();
			param.upass=put[1].val();
			param.yzm=put[2].val();
			param.isremember=getCheckbox('isremember');
			if(_isld!=1){
				$(this).html("登录中...");
				if($("#loadimg").length <= 0){
					$(this).after('<img id="loadimg" style="margin-left:50px;width:32px;height:32px;border:0;" src="tpl/inc/../image/loading.gif" >');
				}else{
					$("#loadimg").show();
				}
			}
			if(_isld==1){showJsTipFun('登录中...请稍等片刻！');return false;}_isld=1;
			param.upass = encryptedString(rsaObj,param.upass);
			$.ajax({url:o.attr("data-url"),type:'post',dataType:'json',data:param,success:function(d){
				_isld=0;d.flag=parseInt(d.flag); 
				if(d.flag==1){_isld==1;
					var lgto = "/";
					if (d.isNeedWizard == 1) {
						lgto = '/index.php?act=user&m=wizard';//如果邮箱/手机/易宝/二次分配没有验证，则跳转至注册引导页面
					} else {
						var lglocation = getCookie("lglocation");
						if(lglocation!=''){
							lgto=lglocation;
							setCookie('lglocation','',-1,'/');
						}
					}
					location.href=lgto;
				}else if(d.flag==-401){
					baseResetTip(false, '验证码错误');resetyzm(o);
					o.find(".btn_submit").html("登录");
					$("#loadimg").hide();
				}else if(d.flag==-402){
					baseResetTip(false, '账号不存在');resetyzm(o);
					o.find(".btn_submit").html("登录");
					$("#loadimg").hide();
				}else if(d.flag==-403){
					baseResetTip(false, '密码不正确，请重新输入');resetyzm(o);
					o.find(".btn_submit").html("登录");
					$("#loadimg").hide();
				}else{//alert(d.flag);
					showJsTipFun(d.msg);resetyzm(o);
					o.find(".btn_submit").html("登录");
					$("#loadimg").hide();
				}
				
			}});
		});
		o.find(":input[name!='uname']").keyup(function(e){
            var key = e.which;
            if (key == 13) {
				o.find(".btn_submit").click();
            }
		});
	}

	//企业注册易宝账户
	var f_open_company = $("#f_open_company");
	if(f_open_company.length > 0){
		o=f_open_company;
		o.find("em").hide();
		var put=[o.find("input[name='Vc_truename']"),
		         o.find("input[name='Vc_identity']"),
		         o.find("input[name='Vc_bank']"),
		         o.find("input[name='Vc_orgNo']"),
		         o.find("input[name='Vc_taxNo']"),
		         o.find("input[name='Vc_legal']"),
		         o.find("input[name='Vc_identity1']"),
		         o.find("input[name='Vc_contact']"),
		         o.find("input[name='Vc_email']")];
		put[0].focusout(function(){
			var t=$(this),tv=t.val(),isb=!!tv.match(/^[\u4e00-\u9fa5]{6,30}$/);
			resetTip(t, isb, tv!=''?((tv.length<6||tv.length>30)?'请输入6~30个汉字的企业全称':''):'请输入企业全称');
			if(put[0].val()!=""){
				// put[0].focusout();//fixed "too much recursion" by chenjx
			}
		}).focusin(function(){resetTip($(this), 2)});
		put[1].focusout(function(){
			var t=$(this),tv=t.val(),isb=!!tv.match(/^[0-9]{15}$/);
			resetTip(t, isb, tv!=''?(tv.length==15?'':'请输入15位数字的营业执照注册号'):'请输入营业执照注册号');
			if(put[1].val()!=""){
				// put[1].focusout();//fixed "too much recursion" by chenjx
			}
		}).focusin(function(){resetTip($(this), 2)});
		put[2].focusout(function(){
			var t=$(this),tv=t.val(),isb=!!tv.match(/^[0-9-]{12,13}$/);
			resetTip(t, isb, tv!=''?((tv.length==12||tv.length==13)?'':'请输入12位数字的开户银行许可证编号'):'请输入开户银行许可证编号');
			if(put[2].val()!=""){
				// put[2].focusout();//fixed "too much recursion" by chenjx
			}
		}).focusin(function(){resetTip($(this), 2)});
		put[3].focusout(function(){
			var t=$(this),tv=t.val(),isb=!!tv.match(/^[0-9-Xx]{9,10}$/);
			resetTip(t, isb, tv!=''?((tv.length==9||tv.length==10)?'':'请输入9位数字的组织机构代码证代码'):'请输入组织机构代码证代码');
			if(put[3].val()!=""){
				// put[3].focusout();//fixed "too much recursion" by chenjx
			}
		}).focusin(function(){resetTip($(this), 2)});
		put[4].focusout(function(){
			var t=$(this),tv=t.val(),isb=!!tv.match(/^[0-9Xx]{15}$/);
			resetTip(t, isb, tv!=''?(tv.length==15?'':'请输入15位数字的税务登记号'):'请输入税务登记号');
			if(put[4].val()!=""){
				// put[4].focusout();//fixed "too much recursion" by chenjx
			}
		}).focusin(function(){resetTip($(this), 2)});
		put[5].focusout(function(){
			var t=$(this),tv=t.val(),isb=!!tv.match(/^[\u4e00-\u9fa5a-zA-Z0-9]{2,10}$/);
			resetTip(t, isb, tv!=''?(tv.length<2?'请输入2~10个字母、汉字或数字':''):'请输入法人姓名');
		}).focusin(function(){resetTip($(this), 2)});
		put[6].focusout(function(){
			var t=$(this),tv=t.val(),isb=!!tv.match(/^[0-9]{18}$/);
			resetTip(t, isb, tv!=''?(tv.length==18?'':'请输入18位数字的法人身份证号'):'请输入法人身份证号');
			if(put[6].val()!=""){
				// put[6].focusout();//fixed "too much recursion" by chenjx
			}
		}).focusin(function(){resetTip($(this), 2)});
		put[7].focusout(function(){
			var t=$(this),tv=t.val(),isb=!!tv.match(/^[\u4e00-\u9fa5]{2,10}$/);
			resetTip(t, isb, tv!=''?(tv.length<2?'请输入2~10个汉字':''):'请输入企业联系人');
		}).focusin(function(){resetTip($(this), 2)});
		put[8].emailMatch({wrapLayer:'#emailMathBox',emailTip:''});
		put[8].focusout(function(){
			var t=$(this),isb=!!t.val().match(/^[a-zA-Z0-9_\-]{1,}@[a-zA-Z0-9_\-]{1,}\.[a-zA-Z0-9_\-.]{1,}$/);
			if(!isb){
				if (t.val()!='') {
					resetTip(t, isb,'邮箱不正确');
					return;
				}
			}
			if (t.val()!='') {
				$.ajax({url:"/index.php?act=user&m=userprocess&ajax=1&w=ckemailnew",type:'post',dataType:'json',data:{skey:t.val(),tmp:Math.random()},success:function(o){
					if(o.flag<1){resetTip(t, false, "该邮箱已使用");}
					else{resetTip(t,true);}
				}});
			} else {
				resetTip(t, false, "请输入邮箱地址");
				//resetTip(t,true);
			}
		}).focusin(function(){resetTip($(this), 2)});

		
		
	}
	
	//修改昵称
	if($("#f_mdyname").length>0){
		o=$("#f_mdyname");
		o.find("em").hide();
		resetyzm(o);
		o.find(".yzmimg").click(function(){resetyzm(o);});
		var put=[o.find("input[name='nickname']"),o.find("input[name='yzm']")];
		put[0].focusout(function(){
			var t=$(this),tv=t.val(),isb=!!tv.match(/^[\u4e00-\u9fa5a-zA-Z0-9]{2,10}$/);
			if(!isb){resetTip(t, isb, tv!=''?(tv.length<5?'昵称过短':(tv.length>10?'昵称过长':'2~10位汉字、字母、数字')):'请输入昵称');return;}
			$.ajax({url:"/index.php?act=user&m=userprocess&ajax=1&w=cknicked",type:'post',dataType:'json',data:{skey:t.val(),tmp:Math.random()},success:function(o){
				if(o.flag<1){resetTip(t, false, o.msg);}
				else{resetTip(t,true);}
			}});
		}).focusin(function(){resetTip($(this), 2)});
		put[1].focusout(function(){
			if(_isld==1)return false;
			var t=$(this),tv=t.val();
			if(tv==''){resetTip(t, false, '请输入验证码');return;}
			resetTip(t, 2);
		}).focusin(function(){resetTip($(this), 2)});
		o.find(".btn_submit").click(function(){
			if(o.find("em.no").length>0){o.find(":input").focusout();return false;}
			var param = {w:"mdyname",tmp:Math.random()};
			param.nickname=put[0].val();
			param.yzm=put[1].val();
			if(_isld==1){showJsTipFun('提交中...请稍等片刻！');return false;}_isld=1;
			$.ajax({url:o.attr("data-url"),type:'post',dataType:'json',data:param,success:function(d){
				_isld=0;
				if(d.flag==1){_isld=1;showJsTipFun('修改成功！');location.href='baseinfo.php';}
				else if(d.flag==-401){resetTip(put[1], false, '验证码有误');resetyzm(o);}
				else{showJsTipFun(d.msg);resetyzm(o);}
			}});
		});
	}
	
	
	//完善用户信息
	if($("#f_complete").length>0){
		o=$("#f_complete");
		resetyzm(o);
		o.find(".yzmimg").click(function(){resetyzm(o);});
		o.find(".btn_submit").click(function(){
			if(!checkform(o))return false;
			o.ajaxSubmit({
				dataType:'json',
				data: {w:'user_complete'},
				success: function(d) {
					if(d.flag==1){
						location.href='baseinfo.php';
						//showJsTipFun('修改成功！2秒后自动跳转到用户中心');setTimeout(function(){location.href='baseinfo.php';},2000);}
					}else{showJsTipFun(d.msg);}
				}
			});
		});
	}
	
	
});
/*
form check
*/
var checkform = function(o){
	var valid=true,len=0,mlen=o.find(":input").length;
	o.find(":input").each(function(){
		var _t=$(this),tip="",val=_t.val(),isc=_t.attr("isc"),_tp=_t.attr("tp"),_tps=_t.attr("tps");
		if(isc!=undefined && (_t.attr("nisc")==undefined || val!="")){
			if(val == ""||val == null){valid=false;tip="不能为空";}
			if(valid){
				if(isc.substr(isc.length-3)=="Fun"){
					var ro=eval(isc+"('"+val+"')");
					if(!ro.flag){valid=false;tip=ro.err};
				}else if(isc.substr(0,6)=="MaxLen"){
					var mL=parseInt(isc.substr(6));
					if(val.length>mL){valid=false;tip="长度不能超过"+mL;}
				}
			}
		}
		if(!valid){
			if(_tps==undefined){_tps = '【'+_tp+'】'+tip+'';}
			humanMsg.displayMsg('<span class="indent">'+_tps+'</span>');
			if(_t.attr("nofocus")==undefined){this.focus();}
			return false;
		}
	});
	return valid;
};
//针对select元素必选,且不为零
function select0Fun(v){
	if(v==0){ return {"flag":false,"err":"需要选择"};}
	return {"flag":true};
}
function cardCodeFun(v){
	if(v==''){ return {"flag":false,"err":"不能为空"};}
	var r=checkCardID(v);
	if(r[0]<1){ console.log(r[1]); return {"flag":false,"err":"有误！"};}
	return {"flag":true};
}
function telCheckFun(v){
	if(!isTel(v)){return {"flag":false,"err":"格式有误！"};}
	return {"flag":true};
}
function mobileCheckFun(v){
	if(!isMobile(v)){return {"flag":false,"err":"格式有误！"};}
	return {"flag":true};
}
function emailCheckFun(v){
	if(!isEmail(v)){return {"flag":false,"err":"格式有误！"};}
	return {"flag":true};
}
//日期格式检查
function datepatFun(v){
	if(!isDate(v)){ return {"flag":false,"err":"格式有误"};}
	return {"flag":true};
}
function nodatepatFun(v){
	if(v=="") return {"flag":true};
	return datepatFun(v);
}
function passwdFun(v){
	if(!v.match(/^\S{6,50}$/)){ return {"flag":false,"err":"格式有误"};}
	return {"flag":true};
}
//带两位小数的格式检查
function float2poiFun(v){
	var r = float2poi0Fun(v); if(!r.flag) return r;
	if(v<=0){ return {"flag":false,"err":"不是一个有效的数字"};}
	return r;
}
//带两位小数的格式检查
function float2poiFunAllow0(v){
	var r = float2poi0Fun(v); if(!r.flag) return r;
	if(v<=0){ return {"flag":false,"err":"不是一个有效的数字"};}
	return r;
}

function float2poi0Fun(v){
	if(!v.match(/^\d+(\.\d{1,2})?$/)){ return {"flag":false,"err":"只允许输入数字，且小数点保留两位"};}
	return {"flag":true};
}
//数字验证
function intFun(v){
	var r = int0Fun(v); if(!r.flag) return r;
	if(v<=0){ return {"flag":false,"err":"不是一个有效的数字"};}
	return {"flag":true};
}
function int0Fun(v){
	if(!v.match(/^\d+$/)){ return {"flag":false,"err":"只允许输入整型数字"};}
	return {"flag":true};
}











