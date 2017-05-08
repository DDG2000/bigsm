var isIE = /msie/i.test(navigator.userAgent) && !window.opera;
function fileChange(target) {   
	var fileSize = 0;
	if (isIE && !target.files) {
		var filePath = target.value;
		try{
			var fileSystem = new ActiveXObject("Scripting.FileSystemObject");
			fileSystem.FileExists(filePath);
			var file = fileSystem.GetFile (filePath);
			fileSize = file.Size;
		}catch(e){}
	} else {   
		fileSize = target.files[0].size; 
	}
	return fileSize/1024;
} 
$(function(){

	var bar = $('.bar');
	var percent = $('.percent');
	var showimg = $('#showimg');
	var progress = $(".progress");
	var files = $(".files");
	var btn = $(".btn span");
	$("#fileupload").wrap("<form id='myupload' action='"+"http://www.bigsm.com/shop/logo_action.php' method='post' enctype='multipart/form-data'></form>");
	$("#fileupload").after("<img src='#' id='prev_img' style='display:none;'>");
	$("#fileupload").change(function(){  //选择文件
		//重置错误信息
		files.html("支持GIF、JPG、JPEG、PNG图像格式，大小不超过2M");
		//js判定
		//以下为限制变量
		var AllowExt=".jpg|.jpeg|.gif|.png|" //允许上传的文件类型 每个扩展名后边要加一个"|" 小写字母表示
		var AllowImgFileSize=2048;  //允许上传图片文件的大小 0为无限制  单位：KB  ---js 需要判定浏览器 -等待 取不到
		var obj=this;
		//判断文件类型是否允许上传
		var FileExt=obj.value.substr(obj.value.lastIndexOf(".")).toLowerCase();
		if(obj.value=='') {
			showJsTipFun('请选择图片!');
			return false;
		}
		if(AllowExt!=0&&AllowExt.indexOf(FileExt+"|")==-1) {
			showJsTipFun('该图片文件类型不允许上传');
			return false;
		}
		var fsize = fileChange(this);//Ie有问题，不检查
		if(fsize>AllowImgFileSize){
			showJsTipFun('该图片大小不能超过2M');
			return false;
		}
		$("#myupload").ajaxSubmit({
			dataType:  'json',	//数据格式为json 
			beforeSend: function() {	//开始上传 
				showimg.empty();	//清空显示的图片
				progress.show();	//显示进度条
				var percentVal = '0%';	//开始进度为0%
				bar.width(percentVal);	//进度条的宽度
				percent.html(percentVal);	//显示进度为0% 
				btn.html("上传中...");	//上传按钮显示上传中
			},
			uploadProgress: function(event, position, total, percentComplete) {
				var percentVal = percentComplete + '%';	//获得进度
				bar.width(percentVal);	//上传进度条宽度变宽
				percent.html(percentVal);	//显示上传进度百分比
			},
			success: function(data) {	//成功
				show_face_cut(data);
			},
			error:function(xhr){	//上传失败
				btn.html("上传失败");
				bar.width('0')
				files.html(xhr.responseText);	//返回失败信息
				showJsTipFun(xhr.responseText);
			}
		});
	});
	
	var jcrop_api,boundx,boundy,bi=1;
	function show_face_cut(d){
		$(".up_face1").hide();
		$(".up_face2").show();$("#pop_avatar").css({marginTop:-260});
		//显示上传后的图片
		var img = d.pic;
		//判断上传图片的大小 然后设置图片的高与宽的固定宽
		if(d.width<d.height){
			//if(d.height>300){bi=300/d.height;}
			bi=300/d.height;
		}else{
			//if(d.width>300){bi=300/d.width;}
			bi=300/d.width;
		}
		var nw=d.width*bi,nh=d.height*bi;
		showimg.html("<img src='"+img+"' id='cropbox' width='"+nw+"' height='"+nh+"' />");
		//传给php页面，进行保存的图片值
		$("#src").val(img);
		$("#preview").attr({"src":img}).css({width:nw,height:nh});
		var tpg=new Image();tpg.src=img;
		tpg.onload=function(){
			$(".up_face2 .prew").hide();
		};
		//或者5秒后自动隐藏
		setTimeout(function(){
			$(".up_face2 .prew").hide();
		},15000);
		$("#bi").val(bi);
		boundx = nw;
		boundy = nh;
		//截取图片的js
		$('#cropbox').Jcrop({
			onChange: updatePreview,
			onSelect: updatePreview,
			aspectRatio: 1,
			minSize:[96,96],
			maxSize:[300,300],
			allowSelect:false, //允许选择
			allowResize:true, //是否允许调整大小
			setSelect: [ 20,20, 96, 96 ]
		},function(){
			var bounds = this.getBounds();
			boundx = bounds[0];
			boundy = bounds[1];
			jcrop_api = this;
		});
	
		btn.html("上传图片");	//上传按钮还原
	}
	$("#restart,.btn_reset").click(function(){
		$(".up_face2").hide();
		$(".up_face1").show();$("#pop_avatar").css({marginTop:-120});
		$("#myupload").get(0).reset();
		$('.progress').hide();//重新上传头像时，隐藏原有的进度条 add by zhaowf 20140911
		//location.reload();
	});
	function updatePreview(c){
        if (parseInt(c.w) > 0)
        {
          var rx = 96 / c.w;
          var ry = 96 / c.h;
          $('#preview').css({
            width: Math.round(rx * boundx) + 'px',
            height: Math.round(ry * boundy) + 'px',
            marginLeft: '-' + Math.round(rx * c.x) + 'px',
            marginTop: '-' + Math.round(ry * c.y) + 'px'
          });
        }
		$('#x').val(c.x);
		$('#y').val(c.y);
		$('#w').val(c.w);
		$('#h').val(c.h);
	};
	function checkCoords(){
		if (parseInt($('#w').val())) return true;
		alert('Please select a crop region then press submit.');
		return false;
	};
	$("#f_modifyface .btn_submit").click(function(){
		$("#f_modifyface").ajaxSubmit({
			dataType:  'json',
			uploadProgress: function(event, position, total, percentComplete) {
			},
			success: function(d) {
				if(d.flag==1){
					try{
						showJsTipFun('修改成功！');
						$(".userhead img").attr({src:d.photo});
						$(".pop .close").click();
					}
					catch(e){console.log(e);}
				}else{alert(d.msg);};
			}
		});
	});
});