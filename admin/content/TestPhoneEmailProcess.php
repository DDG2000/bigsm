<?php
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 初始化参数
**说明：
******************************************************************/
require_once('../include/TopFile.php');
$Admin->CheckPopedoms('SC_SITE_MODELEMAIL');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);

$pt = '手机邮箱白名单';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
$type = $FLib->RequestInt('type',1,9,'类型');
$pt=($type==1?'手机':'邮箱').'白名单';
switch($Work)
{
	case 'MdyReco': /**修改记录**/
		GetValue($Config->AdminPopedKeypattern,$FLib);
		$Id = $FLib->RequestInt('Id',0,9,'ID');
		$Rs = $DataBase->GetResultOne("select count(*) from site_test_phone_email where Vc_value='".$da['Vc_value']."' and ID<>$Id and status>0");
		if($Rs[0]>0){ echo showErr('值已存在');exit;}
		$Db->autoExecute('site_test_phone_email', $da, 'update', "ID=$Id");
		
		$r = createtestcache($type);
		$msg = $r['flag']<1 ? ',生成失败!'.$r['err']:'';
		
		$Admin->AddLog('网站管理','修改'.$pt.'：其Id为：'.$Id );
		echo showSuc($pt.'修改完毕'.$msg,$FLib->IsRequest('backurl'),$obj);
		break;
	case 'AddReco': /**增加记录**/
		GetValue($Config->AdminPopedKeypattern,$FLib);
		$Rs = $DataBase->GetResultOne("select count(*) from site_test_phone_email where Vc_value='".$da['Vc_value']."' and status>0");
		if($Rs[0]>0){ echo showErr('值已存在');exit;}
		$da['Createtime@'] = 'now()';
		$Db->autoExecute('site_test_phone_email', $da);
		
		$r = createtestcache($type);
		$msg = $r['flag']<1 ? ',生成失败!'.$r['err']:'';
		
		$Admin ->AddLog('网站管理','增加'.$pt.'：其值为：'.$da['Vc_value']);
		echo showSuc($pt.'增加完毕'.$msg,$FLib->IsRequest('backurl'),$obj);
		break;
		 
    case 'DeleteReco': /**删除记录**/
		$IdList = $FLib->RequestChar('IdList',0,10000,'IdList',1);
		if (!$FLib->IsIdList($IdList)){ echo showErr('参数错误');exit;}
		$DataBase->QuerySql('update site_test_phone_email set Status=0 where ID in('. $IdList .')');
		
		$r = createtestcache($type);
		$msg = $r['flag']<1 ? ',生成失败!'.$r['err']:'';
		
		$Admin ->AddLog('网站管理','删除'.$pt.'：其ID为：'.$IdList);
		echo showSuc($pt.'删除完毕'.$msg,$FLib->IsRequest('backurl'),$obj);
		break;
}

function GetValue($P,$FLib)
{
	global $da;
	$da['Vc_value'] = $FLib->RequestChar('Vc_value',0,0,'值',1,3);
    $da['I_type'] = $FLib->RequestInt('I_type',1,50,'类型',1);
}
function createtestcache($type=1){
	global $DataBase;
	$fname = WEBROOTDATA.'test.'.($type==1?'phone':'email').'.cache.inc.php';
	$rn = "\r\n";
	$str = '<?php'.$rn;
	$str .= '$da_test=array(';//.$rn
	$da = $DataBase->fetch_all("select Vc_value from site_test_phone_email where status>0 and I_type={$type}");
	foreach($da as $k=>$v){
		$str .= $rn.($k>0? ',':'');
		$str .= $v['ID'].'\''.str_replace("'",'',$v['Vc_value']).'\'';
	}
	$str .= $rn.');';
	$r = writeincdata($str, $fname);
	if($r[0]<1){
		return array('flag'=>0,'err'=>$r[1]);
	}
	return array('flag'=>1);
}
$DataBase->CloseDataBase();
?>