var jspathup = (function() {var elements = document.getElementsByTagName('script');for (var i = 0, len = elements.length; i < len; i++) {	if (elements[i].src && elements[i].src.match(/jquery.upload.js/)) {return elements[i].src.substring(0, elements[i].src.lastIndexOf('/')-2);}}return '';})();
var swfupjs = ['swfupload.js.php','swfqueue.js.php','swfobject.js.php','fileprogress.js.php','swfhandlers.js.php']
for(var i=0;i<swfupjs.length;i++){
	document.write("<script type=\"text/javascript\" src=\""+jspathup+"js/"+swfupjs[i]+"\" > <\/script>");
}
function upload(){
	var fileUpload_upfiles,
	rands = Math.random(),
	_swfup=null,
	//上传 swf 按钮区域ID
	swfupfileID = "upload_upfiles"+rands,
	//定义随机ID 用于显示上传列表及进度
	fsupfileID = "fsUploadProgress_upfiles"+rands,
	//接收返回文件地址组件
	upinput = '',
	nparam = {},
	PHPSESSID = '',
	upClientStatus = '0',//上传客户端包标识
	issubmit = false,
	//param = {file_size_limit:"100 MB", file_types:"*.jpg",file_upload_limit:1} //上传大小 类型 个数 限制
	Init_upfiles = function() {
		var settings = {
			flash_url : jspathup+"js/swfupload.swf",
			upload_url: jspathup+"js/fileupload.php?ty="+(nparam.ty!=undefined?nparam.ty:0),
			post_params: {"PHPSESSID" : PHPSESSID, "uploadFileName": "upfiles", "upClientStatus": upClientStatus},
			file_post_name:"upfiles",
			file_size_limit : "100 MB",
			file_types : "*.*",
			file_types_description : "All Files",
			file_upload_limit : 1,
			file_queue_limit : 0,
			custom_settings : {progressTarget : fsupfileID},
			debug: false,
			prevent_swf_caching:true,

			//Button settings
			button_image_url: jspathup+"images/cnfileselect.png",
			button_width: "61",
			button_height: "22",
			button_placeholder_id: swfupfileID,
			//button_action : SWFUpload.BUTTON_ACTION.SELECT_FILE,
			button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,
			
			swfupload_loaded_handler : swfUploadLoaded_upfiles,
			file_dialog_start_handler:fileDialogStart_upfiles,
			file_queued_handler : fileQueued,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess_upfiles,
			upload_complete_handler : uploadComplete,

			//SWFObject settings
			minimum_flash_version : "9.0.28",
			swfupload_pre_load_handler : swfUploadPreLoad_upfiles,
			swfupload_load_failed_handler : swfUploadLoadFailed_upfiles
		};
		fileUpload_upfiles = new SWFUpload($.extend(settings, nparam));
		initEventsHandler();
	},
	initEventsHandler	= function() {
		_swfup.delegate("a[name='sub']", "click", function() {
			var filenum = _swfup.find(".progressContainer:visible").length;
			if(filenum ==0){
				showJsTipFun("请[浏览]上传文件！");return ;
			}else if(!issubmit){
				issubmit = true;
				fileUpload_upfiles.startUpload();
			}else{
				showJsTipFun("文件已上传！");return ;
			}
			return false;
		});
	},
	swfUploadPreLoad_upfiles = function () {
		var self = this;
		var loading = function () {
			_swfup.find(".divLoadingContent_upfiles").show();
			var longLoad = function () {
				_swfup.find(".divLoadingContent_upfiles").hide();
				_swfup.find(".divLongLoading_upfiles").show();
			};
			this.customSettings.loadingTimeout = setTimeout(function () {
					longLoad.call(self)
				},
				15 * 1000
			);
		};
		
		this.customSettings.loadingTimeout = setTimeout(function () {
				loading.call(self);
			},
			1*1000
		);
	},
	swfUploadLoaded_upfiles = function () {
		var self = this;
		clearTimeout(this.customSettings.loadingTimeout);
		_swfup.find(".divLoadingContent_upfiles").hide();
		_swfup.find(".divLongLoading_upfiles").hide();
		_swfup.find(".divAlternateContent_upfiles").hide();
	},
	swfUploadLoadFailed_upfiles = function () {
		clearTimeout(this.customSettings.loadingTimeout);
		_swfup.find(".divLoadingContent_upfiles").hide();
		_swfup.find(".divLongLoading_upfiles").hide();
		_swfup.find(".divAlternateContent_upfiles").show();
	},
	//打开文件选择选择窗口时触发的事件
	fileDialogStart_upfiles = function () {
	},
	//文件传输完成触发事件
	uploadSuccess_upfiles = function (file, serverData) {
		try {
			var arr=serverData.split('|');
			var progress = new FileProgress(file, this.customSettings.progressTarget);
			progress.setComplete();
			progress.setStatus(arr[0]);
			progress.toggleCancel(false);
			this.setButtonDisabled(true);
			var _input = _swfup.find("input[name='"+upinput+"']");
			_input.val(_input.val()+(_input.val()==''?'':',')+arr[1]).attr({"data":_input.attr("data")+' '+arr[2]}).change();
			if (upClientStatus == 1) {//上传客户端包，返回url及packsize
				$("input[name='url']").val(arr[3]);
				$("input[name='packsize']").val(arr[4]);
				$("input[name='url_dis']").val(arr[3]);
				$("input[name='packsize_dis']").val(arr[4]);
			}
			var allLen = $('.progressContainer').length ;
			//所有图片都上传完成后触发的事件
			if (_input.val().split(',').length == allLen) {
				if (nparam.allSuccessed) {
					nparam.allSuccessed(_input.val()) ;
				}
			}
		} catch (ex) {
			this.debug(ex);
		}
	},
	toString = function(){
		var _htmls = '';
		_htmls += '<div class="imgAdd">';
		_htmls += '<div style="height:25px;">';
		_htmls += '  <div style="height:25px; width:80px; float:left;"><span id="'+swfupfileID+'"></span></div>';
		_htmls += '  <div style="height:25px;float:left; width:100px;position:relative;"><a name="sub" class="progressCancel">上传</a></div>';
		_htmls += '</div>';
		_htmls += '<div id="'+fsupfileID+'"></div>';
		_htmls += '<div class="divLoadingContent_upfiles" >上传控件加载中，请稍候...</div>';
		_htmls += '<div class="divLongLoading_upfiles" >上传控件加载超时，原因可能是：请保证您的flash插件是开启状态，上传控件需要Flash Player 9或以上的支持！</div>';
		_htmls += '<div class="divAlternateContent_upfiles" >上传控件加载失败，原因可能是：您的Flash Player版本过低！您可以访问<a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" target=_blank>Adobe网站</a>获得最新Flash Player版本。</div>';
		_htmls += '<input type="hidden" name="'+ upinput +'" value=""></div>';
		return _htmls;
	},
	init = function(sup,input,param){
		rands = Math.random();
		//上传 swf 按钮区域ID
		swfupfileID = "upload_upfiles"+rands;
		//定义随机ID 用于显示上传列表及进度
		fsupfileID = "fsUploadProgress_upfiles"+rands;
		PHPSESSID = param.PHPSESSID;
		if (param.upClientStatus != undefined)//上传客户端包标识
			upClientStatus = param.upClientStatus;
		upinput = input;
		nparam = param;
		_swfup = $(sup);
		_swfup.html(toString());
		Init_upfiles();
	};

	return {
		init         : init,
		swfUploadPreLoad_upfiles : swfUploadPreLoad_upfiles,
		swfUploadLoaded_upfiles	: swfUploadLoaded_upfiles,
		swfUploadLoadFailed_upfiles : swfUploadLoadFailed_upfiles,
		fileDialogStart_upfiles : fileDialogStart_upfiles,
		uploadSuccess_upfiles : uploadSuccess_upfiles
	};
}


