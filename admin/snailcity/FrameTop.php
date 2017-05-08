<?php
/****************************************************************** 
**创建者：kign
**创建时间：2013-1-28
**本页： 框架头 管理
**说明：
******************************************************************/
require_once('../include/TopFile.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title>框架头</title>
<link href="../style/link.css" rel="stylesheet" type="text/css" />
<script src="../js/jquery-1.7.2.min.js" type="text/javascript"></script>
<!--[if IE 6]>
<script type="text/javascript" src="../js/DDPN.js#"></script>
<![endif]-->
<script type="text/javascript">
$(function(){
	$(".info").delegate("#lefta","click",function(){
		if($(this).attr("href")=="#close"){
			$(this).html("[显示菜单]").attr("href","#show");parent.hideMenu();
		}else{
			$(this).html("[隐藏菜单]").attr("href","#close");parent.showMenu();
		}
	}).delegate("#flush","click",function(){
		parent.flushCon();
	}).delegate("#eixt","click",function(){
		parent.parent.exitSys();
	});
	$(".title").delegate("div","click",function(){
		$(".title > div.now").removeClass("now");
		$(this).addClass("now");
		parent.toMenu("FrameMenu.php?ClassType="+ $(this).attr("data-id"));
	});
	$(".title div[data-id]:last").click();
});
</script>
</head>

<body>
<div id="top">
	<div class="top1">
		<div class="logo"><img src="../image/logo_1.png" /></div>
		<div class="it">
			<div class="info"><?php echo $Admin->Uname;?> ,您好。 <a id="lefta" href="#close" class="a1">[隐藏菜单]</a> | <a id="flush" href="#" class="a1">[刷新]</a> | <a id="eixt" href="#" class="a1">[退出]</a></div>
			<div class="title">
				<?php
				echo $Admin->CheckPopedom("SM_SHOP") ? '<div data-id="Shop">商家管理</div>':'';
				echo $Admin->CheckPopedom("SM_ITEM") ? '<div data-id="Item">钢材市场管理</div>':'';
				echo $Admin->CheckPopedom("SM_CONCENTRATED") ? '<div data-id="Concentrated">集采管理</div>':'';
				echo $Admin->CheckPopedom("SM_PROJECT") ? '<div data-id="Project">项目管理</div>':'';
				echo $Admin->CheckPopedom("SC_MEMBER") ? '<div data-id="User">会员管理</div>':'';
				echo $Admin->CheckPopedom("SC_AUDIT") ? '<div data-id="Audit">审核管理</div>':'';
				echo $Admin->CheckPopedom("ES_ARTICLE") ? '<div data-id="Article">文章管理</div>':'';
				echo $Admin->CheckPopedom("SC_SITE") ? '<div data-id="Content">网站管理</div>':'';
				echo ($Admin->CheckPopedom("SC_SYSA")||$Admin->CheckPopedom("SC_MEMBER_BASE")) ? '<div data-id="System">系统管理</div>':'';
				?>
			</div>
		</div>
	</div>
	<div class="top2"></div>
</div>
</body>
</html>
