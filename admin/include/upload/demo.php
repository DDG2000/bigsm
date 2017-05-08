<?php
/****
注意这个上传组件需要 使用session_id 来和程序连接
而 该演示demo 的接收文件的程序，是正式文件，需要验证管理员是否登录，如果没有登录，则会上传失败（需要关闭浏览器，调试）
*/

session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery.upload.js"></script>
<script type="text/javascript">	
$(function(){
	//初始化上传组件 基于jquery
	/*参数一：上传组件显示区域ID
	 *参数二: from 表单input元素的名称，用于form表单提交（自动生成）（上传成功后，会将上传文件的临时地址赋值给改元素）
	 *参数三：上传文件检查元素(类型json) 下面对应 文件大小，类型，上传个数：如要扩展可参考jquery.upload.js
	*/
	new upload().init("#swfup","fpath",{file_size_limit:"100 MB", file_types:"*.jpg",file_upload_limit:1,PHPSESSID:"<?php echo session_id();?>",debug:true});

	//$(".imgMdy .change").click(function(){var t=$(this).parent();t.hide();t.next(".imgAdd").show();});
	//if($(".imgMdy").length>0){$(".imgMdy").next(".imgAdd").hide();}
});
</script>
<style>

/*上传文件样式**/
.upfile{}
.upfile .divLoadingContent_upfiles{background-color: #FFFF66; border-top: solid 4px #FF9966; border-bottom: solid 4px #FF9966; margin: 10px 25px; padding: 10px 15px; display: none;}
.upfile .divLongLoading_upfiles{background-color: #FFFF66; border-top: solid 4px #FF9966; border-bottom: solid 4px #FF9966; margin: 10px 25px; padding: 10px 15px; display: none;}
.upfile .divAlternateContent_upfiles{background-color: #FFFF66; border-top: solid 4px #FF9966; border-bottom: solid 4px #FF9966; margin: 10px 25px; padding: 10px 15px; display: none;}
.upfile .progressBarInProgress,.upfile .progressBarComplete,.upfile .progressBarError {font-size: 0;width: 0%;height: 2px;background-color: blue;margin-top: 2px;}
.upfile .progressContainer {margin: 5px;padding: 4px;border: solid 1px #E8E8E8;background-color: #F7F7F7;overflow: hidden;position:relative;}
.upfile a.progressCancel {position:absolute;right:5px;top:auto;font-size: 12px;display: block;height: 25px;width: 50px;background: url(images/bt.gif) no-repeat;text-align:center;cursor:pointer;}

</style>
</head>

<body>
<form name="ss" action="?" method="post">

商品大图:
<div class="upfile"><div id="swfup"></div></div>
尺寸为<span id="f_wh">500*500</span>

<input type="submit" name="ssdd" value="提交"/>
</form>
</body>
</html>
