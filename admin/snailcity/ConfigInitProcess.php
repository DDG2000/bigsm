<?php
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 初始化参数
**说明：
******************************************************************/
require_once('../include/TopFile.php');
require_once('ConfigCommon.php');
$Admin->CheckPopedoms('SC_SITE_CONFIG_INIT');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$pt = '初始化参数';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
{
	case 'MdyReco': /**修改记录**/
		GetValue($Config->AdminPopedKeypattern,$FLib);
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne("select count(*) from site_parameter where Vc_name='".$da['Vc_name']."' and ID<>$Id");
		if($Rs[0]>0){ echo showErr('参数名已存在');exit;}
		$Db->autoExecute('site_parameter', $da, 'update', "ID=$Id");
		$Admin->AddLog('网站管理','修改'.$pt.'：其Id为：'.$Id );
		
		$r = writeConfigValue();
		$msg = '';
		if($r[0]<1){
			$msg .= ',生成失败!'.$r[1];
		}
		echo showSuc($pt.'修改完毕'.$msg,$FLib->IsRequest('backurl'),$obj);
		break;
	case 'AddReco': /**增加记录**/
		GetValue($Config->AdminPopedKeypattern,$FLib);
		$Rs = $DataBase->GetResultOne("select count(*) from site_parameter where Vc_name='".$da['Vc_name']."'");
		if($Rs[0]>0){ echo showErr('参数名已存在');exit;}
		$da['Createtime@'] = 'now()';
		$Db->autoExecute('site_parameter', $da);
		$Admin ->AddLog('网站管理','增加'.$pt.'：其名称为：'.$da['Vc_name']);
		
		$r = writeConfigValue();
		$msg = '';
		if($r[0]<1){
			$msg .= ',生成失败!'.$r[1];
		}
		echo showSuc($pt.'增加完毕'.$msg,$FLib->IsRequest('backurl'),$obj);
		break;
		 
    case 'DeleteReco': /**删除记录**/
		$IdList = $FLib->RequestChar('IdList',0,10000,'IdList',1);
		if (!$FLib->IsIdList($IdList)){ echo showErr('参数错误');exit;}
		$DataBase->QuerySql('update site_parameter set Status=0 where ID in('. $IdList .')');
		$Admin ->AddLog('网站管理','删除'.$pt.'：其ID为：'.$IdList);
		
		$r = writeConfigValue();
		$msg = '';
		if($r[0]<1){
			$msg .= ',生成失败!'.$r[1];
		}
		echo showSuc($pt.'删除完毕'.$msg,$FLib->IsRequest('backurl'),$obj);
		break;
    case 'CreateReco': /**生成记录**/
		//write begin
		$r = writeConfigValue();
		if($r[0]<1){echo showErr($r[1]);exit;}
		//write end
		$Admin ->AddLog('网站管理',$pt.'生成文件成功');
		echo showSuc($pt.'生成文件成功',$FLib->IsRequest('backurl'),$obj);
		break;
}

function GetValue($P,$FLib)
{
	global $da;
	$da['I_group'] = $FLib->RequestInt('I_group',0,8,'父ID');
    $da['Vc_name'] = $FLib->RequestChar('Vc_name',0,50,'参数名',1);
	$da['Vc_value'] = $FLib->RequestChar('Vc_value',0,0,'参数值',1,3);
    $da['Vc_type'] = $FLib->RequestChar('Vc_type',1,50,'类型',1);
    $da['Vc_intro'] = $FLib->RequestChar('Vc_intro',1,100,'参数说明',1);
    $da['Vc_tip'] = $FLib->RequestChar('Vc_tip',1,500,'参数Tip',1,3);
	$da['I_show'] = $FLib->RequestInt('I_show',1,8,'是否显示');
}
$DataBase->CloseDataBase();
?>