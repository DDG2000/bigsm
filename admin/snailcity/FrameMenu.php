<?php
/****************************************************************** 
 **创建者：kign
 **创建时间：2013-1-28
 **本页： 框架左栏菜单 管理
 **说明：
 ******************************************************************/
require_once ('../include/TopFile.php');
$ClassType = $FLib->IsRequest ( "ClassType" );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>框架左栏菜单</title>
<link href="../style/link.css" rel="stylesheet" type="text/css" />
<script src="../js/jquery-1.7.2.min.js" type="text/javascript"></script>
<!--[if IE 6]>
<script type="text/javascript" src="../js/DDPN.js#"></script>
<![endif]-->
<script type="text/javascript">
<!--
$(function(){
	var hideMenuClass=function(_o){
		_o.removeClass().addClass("tit_bt");
		_o.parent().find(".tit_tt").slideUp();
	};
	var showMenuClass=function(_o){
		_o.removeClass().addClass("tit_bt_now");
		_o.parent().find(".tit_tt").slideDown();
	};
	$("#left").delegate(".tit_bt","click",function(){
		hideMenuClass($(".tit_bt_now"));
		showMenuClass($(this));
	});
	$("#left").delegate(".tit_tt > div","click",function(){
		$(".tit_tt > div.now").removeClass("now").attr("style","");
		$(this).addClass("now");
		parent.toCon($(this).attr("href"));
	});
	$("#left").delegate(".tit_bt,.tit_bt_now","mouseenter",function(){
		$(this).animate({paddingRight:'25px'},"fast");
	}).delegate(".tit_bt,.tit_bt_now","mouseleave",function(){
		$(this).animate({paddingRight:'18px'},"fast");
	});
	$("#left").delegate(".tit_tt > div:not(.now)","mouseenter",function(){
		$(this).css({color:'#FFFFFF'});
	}).delegate(".tit_tt > div:not(.now)","mouseleave",function(){
		$(this).css({color:'#AAAAAA'});
	});
	
	//init
	$(".tit_tt").hide();
	showMenuClass($(".tit_bt,.tit_bt_now").eq(0));
	var _initurl=$("#initurl").attr("href");
	if(typeof(_initurl)!='undefined'){
		parent.toCon(_initurl);
	}else{
		$("div.tit_tt:first div:first").click();
	}
	//test
	//$(".tit_tt > div").attr("title", function(){return $(this).attr("href");});

});
//-->
</script>
</head>

<body class="leftbg">
	<div id="left">


<?php
$rnt = "\r\n\t\t";

if ($ClassType == '') {
	$ClassType = 'System';
}
switch ($ClassType) {
	//系统管理栏目
	case 'System' :
		$initurl = ($Admin->Uname == 'system' || strpos ( ',' . $Admin->Rule . ',', ',2,' ) !== false) ? 'ServerMessage.php' : 'LoginInfo.php';
		echo '<a id="initurl" href="' . $initurl . '" style="display:none;">默认页</a>';
		if ($Admin->CheckPopedom ( 'SC_MEMBER_BASE' )) {
			?>
		<div class="tit">
			<div class="tit_bt">当前用户</div>
			<div class="tit_tt">
				<div class="<?php echo $initurl=='LoginInfo.php'?'now':'';?>"
					href="../snailcity/LoginInfo.php">基本信息</div>
				<div
					href="../snailcity/ManagerMdy.php?Work=MdyPwd&Id=<?php echo $Admin->Uid;?>&burl=1">修改密码</div>

			</div>
		</div>
<?php
		}
		if ($Admin->CheckPopedom ( 'SC_SYS_SET' )) {
			echo '<div class="tit">' . "\r\n\t" . '<div class="tit_bt">系统安全</div>' . "\r\n\t" . '<div class="tit_tt">';
			if ($Admin->CheckPopedom ( 'SC_SYS_SET_POPEDOM' )) {
				echo $rnt . '<div href="../snailcity/PopedomList.php">权限列表</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SYS_SET_ROLE' )) {
				echo $rnt . '<div href="../snailcity/RoleList.php">角色列表</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SYS_SET_GROUP' ) && $Config->GroupTure == 1) {
				echo $rnt . '<div href="../snailcity/GroupList.php">分组列表</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SYS_SET_USER' )) {
				echo $rnt . '<div href="../snailcity/ManagerList.php">用户列表</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SYS_SET_LOG' )) {
				echo $rnt . '<div href="../snailcity/SysLogList.php">系统日志</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SYS_SET_DB_OPERATE' )) {
				echo $rnt . '<div href="../snailcity/DataBaseControl.php">数据库操作</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SYS_SET_USERCONFIG' )) {
				echo $rnt . '<div href="../snailcity/UserConfig.php">用户配置</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SYS_SET_CONFIG' )) {
				echo $rnt . '<div href="../snailcity/ConfigMdy.php">系统配置文件</div>';
			}
			echo "\r\n\t" . '</div></div>';
		}
		
		if ($Admin->CheckPopedom ( 'SC_SYS_TOOL' )) {
			echo '<div class="tit">' . "\r\n\t" . '<div class="tit_bt">系统工具</div>' . "\r\n\t" . '<div class="tit_tt">';
			if ($Admin->CheckPopedom ( 'SC_SYS_TOOL_DB' )) {
				echo $rnt . '<div href="../snailcity/DBControl.php">数据库备份</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SYS_TOOL_ISLOCKIP' )) {
				echo $rnt . '<div href="../snailcity/LockIpList.php?Status=1">黑名单</div>';
				echo $rnt . '<div href="../snailcity/LockIpList.php?Status=2">白名单</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SYS_TOOL_TRYLOGIN' )) {
				echo $rnt . '<div href="../snailcity/TryLoginList.php">登录记录</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SYS_TOOL_DETECT' )) {
				echo $rnt . '<div href="../snailcity/SystemDetection.php">系统运行检测</div>';
			}
			echo "\r\n\t" . '</div></div>';
		}
		break;
	//网站管理栏目
	case 'Content' :
		if ($Admin->CheckPopedom ( 'SC_SITE_INFO' )) {
			echo '<div class="tit">' . "\r\n\t" . '<div class="tit_bt">网站管理</div>' . "\r\n\t" . '<div class="tit_tt">';
			// 参数的两种设置方式
			if ($Admin->CheckPopedom ( 'SC_SITE_CONFIG_INIT' ) && strpos ( ',' . $Admin->Rule . ',', ',2,' ) !== false) {
				echo $rnt . '<div href="../snailcity/ConfigInit.php">初始参数设置</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SITE_CONFIG' )) {
				echo $rnt . '<div href="../snailcity/ConfigManager.php">参数设置</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SITE_ADPLACE' )) {
				echo $rnt . '<div href="../content/AdvertPlaceList.php">广告位信息</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SITE_AD' )) {
				echo $rnt . '<div href="../content/AdvertList.php">广告列表</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SITE_PIC' )) {
				echo $rnt . '<div href="../content/PictureList.php">图片列表</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SITE_ADROLL' )) {
				echo $rnt . '<div href="../content/AdIndexList.php">首页轮播</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SITE_BDWORD' )) {
				echo $rnt . '<div href="../content/BadWordList.php">坏词列表</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SITE_PTNAME' )) {
				echo $rnt . '<div href="../content/ProtectNameList.php">保留名列表</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SITE_LINK' )) {
				echo $rnt . '<div href="../content/ConnectionList.php">友情链接</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SITE_PARTNER' )) {
				echo $rnt . '<div href="../content/CooperatList.php">合作站点</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SITE_OPINION' )) {
				echo $rnt . '<div href="../content/FeedbackList.php">意见反馈</div>';
			}
			echo "\r\n\t" . '</div></div>';
			
			if ($Admin->CheckPopedom ( 'SC_SITE_MODELEMAIL' )) {
				echo '<div class="tit">' . "\r\n\t" . '<div class="tit_bt">测试白名单</div>' . "\r\n\t" . '<div class="tit_tt">';
				
				echo $rnt . '<div href="../content/TestPhoneEmailList.php?type=1">手机白名单</div>';
				echo $rnt . '<div href="../content/TestPhoneEmailList.php?type=2">邮箱白名单</div>';
				
				echo "\r\n\t" . '</div></div>';
			}
		}
		if ($Admin->CheckPopedom ( 'SC_SITE_AUDIT' )) {
			echo '<div class="tit">' . "\r\n\t" . '<div class="tit_bt">审核流程定义</div>' . "\r\n\t" . '<div class="tit_tt">';
			if ($Admin->CheckPopedom ( 'SC_SITE_AUDIT_ROLE' )) {
				echo $rnt . '<div href="../content/AuditRoleList.php">审核角色</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_SITE_AUDIT_FLOW' )) {
				echo $rnt . '<div href="../content/AuditFlowList.php">审核流程</div>';
			}
			echo "\r\n\t" . '</div></div>';
		}
		break;
	//文章管理栏目
	case 'Article' :
		if ($Admin->CheckPopedom ( 'ES_ARTICLE' )) {
			if ($Admin->CheckPopedom ( 'ES_ARTICLE_NEWS' )) {
				echo '<div class="tit">' . "\r\n\t" . '<div class="tit_bt">信息咨询</div>' . "\r\n\t" . '<div class="tit_tt">';
				if ($Admin->CheckPopedom('ES_ARTICLE_NEWS_INFO')) {
					echo $rnt . '<div href="../content/ArticleList.php?cid=2">资讯行情</div>';
				}
				if ($Admin->CheckPopedom('ES_ARTICLE_NEWS_BBS')) {
					echo $rnt . '<div href="../content/ArticleList.php?cid=6">网站公告</div>';
				}
				echo "\r\n\t" . '</div></div>';
			}
			if ($Admin->CheckPopedom ( 'ES_ARTICLE_HELP' )) {
				echo '<div class="tit">' . "\r\n\t" . '<div class="tit_bt">帮助中心</div>' . "\r\n\t" . '<div class="tit_tt">';
					echo $rnt . '<div href="../content/ArticleList.php?cid=8">下单指南</div>';
					echo $rnt . '<div href="../content/ArticleList.php?cid=13">支付方式</div>';
					echo $rnt . '<div href="../content/ArticleList.php?cid=14">特色服务</div>';
					echo $rnt . '<div href="../content/ArticleList.php?cid=15">平台规则</div>';
					echo $rnt . '<div href="../content/ArticleList.php?cid=16">常见问题</div>';
				echo "\r\n\t" . '</div></div>';
			}
			if ($Admin->CheckPopedom ( 'ES_ARTICLE_ABOUT' )) {
				echo '<div class="tit">' . "\r\n\t" . '<div class="tit_bt">关于我们</div>' . "\r\n\t" . '<div class="tit_tt">';
				echo $rnt . '<div href="../content/ArticleMdy.php?Work=MdyReco2&cid=17&Id=1">关于我们</div>';
				echo $rnt . '<div href="../content/ArticleMdy.php?Work=MdyReco2&cid=18&Id=2">人才招聘</div>';
				echo $rnt . '<div href="../content/ArticleMdy.php?Work=MdyReco2&cid=19&Id=3">联系我们</div>';
				echo $rnt . '<div href="../content/ArticleMdy.php?Work=MdyReco2&cid=20&Id=4">法律说明</div>';
				echo $rnt . '<div href="../content/ArticleMdy.php?Work=MdyReco2&cid=21&Id=5">安全保障</div>';
				echo "\r\n\t" . '</div></div>';
			}
		}
		break;
	//审核管理栏目
	case 'Audit' :
		if ($Admin->CheckPopedom ( 'SC_AUDIT' )) {
			echo '<div class="tit">' . "\r\n\t" . '<div class="tit_bt">审核管理</div>' . "\r\n\t" . '<div class="tit_tt">';
			if ($Admin->CheckPopedom ( 'SC_AUDIT' )) {
				echo $rnt . '<div href="../bigsm/ApplyList.php?I_entity=1">企业认证审核</div>';
				echo $rnt . '<div href="../bigsm/ApplyList.php?I_entity=2">开店审核</div>';
				echo $rnt . '<div href="../bigsm/ApplyList.php?I_entity=3">项目申请审核</div>';
				echo $rnt . '<div href="../bigsm/ApplyList.php?I_entity=4">金融申请审核</div>';
				echo $rnt . '<div href="../bigsm/ApplyList.php?I_entity=5">集采申请审核</div>';
				echo $rnt . '<div href="../bigsm/ApplyList.php?I_entity=6">项目用量申请审核</div>';
				echo $rnt . '<div href="../bigsm/ApplyList.php?I_entity=7">购买融资审核</div>';
			}
			echo "\r\n\t" . '</div></div>';
		}
		break;
	//会员管理栏目
	case 'User' :
		if ($Admin->CheckPopedom ( 'SC_MEMBER' )) {
			echo '<div class="tit">' . "\r\n\t" . '<div class="tit_bt">会员管理</div>' . "\r\n\t" . '<div class="tit_tt">';
			
			if ($Admin->CheckPopedom ( 'SC_MEMBER_CLASS' )) {
				echo $rnt . '<div href="../user/MemberClassList.php">会员类型管理</div>';
			}
			
			if ($Admin->CheckPopedom ( 'SC_MEMBER_BASE' )) {
				echo $rnt . '<div href="../user/MemberList.php?type=1&bc=0&icy=0">会员列表<!-- 普通会员 --></div>';
				echo $rnt . '<div href="../user/MemberList.php?type=1&bc=0&icy=1">企业列表</div>';
				echo $rnt . '<div href="../user/MemberList.php?type=1&ibad=1">会员黑名单</div>';
				echo $rnt . '<div href="../user/MemberList.php?type=31">会员实名认证列表</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_MEMBER_LEAVEWORD' )) {
				echo $rnt . '<div href="../user/LeavewordList.php">留言管理</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_MEMBER_GRADE' )) {
				echo $rnt . '<div href="../user/MemberGradeList.php">等级管理</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_MEMBER_POINT' )) {
				echo $rnt . '<div href="../user/MemberPointList.php">积分管理</div>';
			}
			
			echo "\r\n\t" . '</div></div>';
		}
		if ($Admin->CheckPopedom ( 'SC_MEMBER_EMAIL' )) {
			echo '<div class="tit">' . "\r\n\t" . '<div class="tit_bt">邮件管理</div>' . "\r\n\t" . '<div class="tit_tt">';
			if ($Admin->CheckPopedom ( 'SC_MEMBER_EMAIL' )) {
				echo $rnt . '<div href="../user/EmailList.php">邮件列表</div>';
			}
			if ($Admin->CheckPopedom ( 'SC_MEMBER_EMAIL_MDY' )) {
				echo $rnt . '<div href="../user/EmailRecordList.php">邮件记录</div>';
			}
			echo "\r\n\t" . '</div></div>';
		}
		break;
	//项目管理栏目
	case 'Project':
		if ($Admin->CheckPopedom ('SM_ITEM')) {
			echo '<div class="tit">' . "\r\n\t" . '<div class="tit_bt">项目管理</div>' . "\r\n\t" . '<div class="tit_tt">';
			if ($Admin->CheckPopedom ( 'SM_PROJECT' )) {
				echo $rnt . '<div href="../project/ProjectList.php">项目管理</div>';
			}
			if ($Admin->CheckPopedom ( 'SM_PROJECT_ORDER' )) {
// 				echo $rnt . '<div href="../project/ProjectOrderList.php">项目订单管理</div>';
			}
			if ($Admin->CheckPopedom ( 'SM_PROJECT_CONSUMPTIONS' )) {
// 				echo $rnt . '<div href="../project/ProjectConsumptionsList.php">项目用量管理</div>';
			}
			if ($Admin->CheckPopedom ( 'SM_PROJECT_DEAL' )) {
 				echo $rnt . '<div href="../project/ProjectDealList.php">成交项目管理</div>';
			}
			echo "\r\n\t" . '</div></div>';
		}
		break;
	//集采管理栏目
	case 'Concentrated':
		if ($Admin->CheckPopedom ('SM_CONCENTRATED')) {
			echo '<div class="tit">' . "\r\n\t" . '<div class="tit_bt">集采管理</div>' . "\r\n\t" . '<div class="tit_tt">';
			if ($Admin->CheckPopedom ( 'SM_CONCENTRATED_LIST' )) {
				echo $rnt . '<div href="../concentrated/ConcentratedList.php">集采列表</div>';
			}
	
			echo "\r\n\t" . '</div></div>';
		}
		break;
	//钢材市场管理栏目
	case 'Item' :
		if ($Admin->CheckPopedom ('SM_ITEM')) {
			echo '<div class="tit">' . "\r\n\t" . '<div class="tit_bt">钢材市场管理</div>' . "\r\n\t" . '<div class="tit_tt">';
			
			if ($Admin->CheckPopedom ( 'SM_ITEM_CLASS' )) {
				echo $rnt . '<div href="../item/ItemClassList.php">分类管理</div>';
			}
			if ($Admin->CheckPopedom ( 'SM_ITEM_BASE' )) {
				echo $rnt . '<div href="../item/ItemList.php">品名管理</div>';
			}
			if ($Admin->CheckPopedom ( 'SM_ITEM_STUFF' )) {
				echo $rnt . '<div href="../item/ItemStuffList.php">材质管理</div>';
			}
			if ($Admin->CheckPopedom ( 'SM_ITEM_SPECIFICATION' )) {
				echo $rnt . '<div href="../item/ItemSpecificationList.php">规格管理</div>';
			}
			if ($Admin->CheckPopedom ( 'SM_ITEM_FACTORY' )) {
				echo $rnt . '<div href="../item/ItemFactoryList.php">钢厂管理</div>';
			}
// 			if ($Admin->CheckPopedom ( 'SC_MEMBER_CLASS' )) {
// 				echo $rnt . '<div href="../user/MemberClassList.php">钢材市场商品类型管理</div>';
// 			}
			
// 			if ($Admin->CheckPopedom ( 'SC_MEMBER_BASE' )) {
// 				echo $rnt . '<div href="../user/MemberList.php?type=1&bc=0&icy=0">会员列表<!-- 普通会员 --></div>';
// 				echo $rnt . '<div href="../user/MemberList.php?type=1&bc=0&icy=1">企业列表</div>';
// 				echo $rnt . '<div href="../user/MemberList.php?type=1&ibad=1">会员黑名单</div>';
// 				echo $rnt . '<div href="../user/MemberList.php?type=31">会员实名认证列表</div>';
// 			}
			
			
			echo "\r\n\t" . '</div></div>';
		}
		
		break;
	//商家管理栏目
	case 'Shop' :
		if ($Admin->CheckPopedom ('SM_SHOP')) {
			echo '<div class="tit">' . "\r\n\t" . '<div class="tit_bt">商家管理</div>' . "\r\n\t" . '<div class="tit_tt">';
						if ($Admin->CheckPopedom ( 'SM_SHOP_LIST' )) {
							echo $rnt . '<div href="../shop/ShopList.php">店铺列表</div>';
						}
						if ($Admin->CheckPopedom ( 'SM_SHOP_CONFIG' )) {
						    echo $rnt . '<div href="../shop/ShopConfig.php">商铺参数设置</div>';
						}
// 			if ($Admin->CheckPopedom ( 'SM_ITEM_CLASS' )) {
// 				echo $rnt . '<div href="../item/ItemClassList.php">分类管理</div>';
// 			}

			
			echo "\r\n\t" . '</div></div>';
		}
		
		break;
}
?>



</div>
</body>
</html>

