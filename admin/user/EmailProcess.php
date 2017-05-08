<?php
/**
* 模块：基础模块
* 描述：邮件处理页
* 版本：管理系统 V0.1系统
* 作者：张绍海
* 书写日期：2011-03-16
* 修改日期：
*/
error_reporting(0);
set_time_limit(0);
require_once('../include/TopFile.php'); 
$Admin->CheckPopedoms('SC_MEMBER_EMAIL');
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);
if(!$Config->Link)
{
	$DataBase->OpenDataBase();
}

$tt = '邮件';
$obj = $FLib->RequestChar('obj',1,50,'父窗口标记',1);
switch($Work)
 {
 	case 'MdyReco': /**编辑邮件**/
		 $Admin->CheckPopedoms('SC_MEMBER_EMAIL_MDY');
	     GetValue($FLib);
		 $Id = $FLib->RequestInt('Id',0,9,'ID');
		 $DataBase->QuerySql("update site_email set Vc_title='$title',T_content='$content' where id='$Id'");
		 $Admin ->AddLog('会员管理','修改邮件：其Id为：'.$Id );
		 echo showSuc($tt.'编辑成功',$FLib->IsRequest('backurl'),$obj);
         break;
	case 'AddReco': /**增加邮件**/
		 $Admin->CheckPopedoms('SC_MEMBER_EMAIL_MDY');
		 GetValue($FLib);
		 $DataBase->QuerySql("insert into site_email(Vc_title,T_content,I_operatorID,Createtime) values
		 ('$title','$content','$Admin->Uid',now())");
		 $Admin ->AddLog('会员管理','增加邮件：其名称为：'.$title);
		 echo showSuc($tt.'添加成功',$FLib->IsRequest('backurl'),$obj);
         break;
    case 'DeleteReco': /**删除邮件**/
		 $Admin->CheckPopedoms('SC_MEMBER_EMAIL_MDY');
		 $IdList = $FLib->RequestChar('IdList',0,100,'IdList',1);
		 if(!$FLib->IsIdList($IdList)) { echo showErr('参数错误'); exit; }
		 $DataBase->QuerySql('update site_email set Status=0 where ID in('.$IdList.')');
		 
		 $Admin ->AddLog('会员管理','删除邮件：其ID为：'.$IdList);
		 echo showSuc($tt.'删除成功',$FLib->IsRequest('backurl'),$obj);

         break;
	case 'SendReco': /**发送邮件**/
		 $Admin->CheckPopedoms('SC_MEMBER_EMAIL_SEND');
	     require(WEBROOT.'admin/include/phpmailer/class.phpmailer.php');
		 $Id = $FLib->requestint('Id',0,9,'ID');
		 $Re0 = $DataBase->SelectSql("select * from site_email where status=1 and ID=$Id");
		 if($Rs0 = $DataBase->GetResultArray($Re0))
		 {
		 	$title = $Rs0['Vc_title'];
			$content = $Rs0['T_content'];
		 }else { echo showErr('邮件不存在！'); exit; }
		 $sql = "select Vc_name,Vc_Email from user_base where status=1 ";
		 $Re = $DataBase->SelectSql($sql);
		 while($Rs = $DataBase->GetResultArray($Re))
		 {
			$email = $Rs[1];
			$fromname = $Rs[0];
			smtp_mail($email,$title,$content,$email,'');
		 }
		 $DataBase->QuerySql("insert into site_email_record(I_emailID,T_factor,I_operatorID,Createtime) values('$Id','','$Admin->Uid',now())");
		 $Admin ->AddLog('会员管理','发送邮件：其ID为：'.$Id);
		 echo showSuc($tt.'发送成功',$FLib->IsRequest('backurl'),$obj);
         break;	 

    default:
	echo $FLib ->Alert('参数错误!','self','BACK');
	exit;
 } 
function smtp_mail($sendto_email, $subject, $body, $sendto_name,$fromname)
{
	global $g_conf;
	$fromname = $g_conf['cfg_email_name'];
	$fromname = iconv('utf-8','gb2312',$fromname);
	$replyto = iconv('utf-8','gb2312','管理员');
	$title = iconv('utf-8','gb2312',$subject);
	$content = iconv('utf-8','gb2312',$body);
	
	$mail = new PHPMailer();
	$mail->IsSMTP();                           // send via SMTP
	$mail->Host = $g_conf['cfg_email_server'];         // SMTP servers
	$mail->SMTPAuth = true;                    // turn on SMTP authentication
	$mail->FromName = $fromname;               // 显示发件人

	$mail->Username = $g_conf['cfg_email'];
	$mail->Password = $g_conf['cfg_email_pwd'];
	$mail->From     = $g_conf['cfg_email'];

	
	$mail->AddAddress($sendto_email,$sendto_name);       // 收件人邮箱和姓名
	$mail->AddReplyTo($g_conf['cfg_email'],$replyto);     // 返回信息的接受地址和姓名
	$mail->CharSet = "gb2312";                           // 这里指定(只对邮件内容)字符集！
	
	$mail->IsHTML(false);                                // send as HTML
	$mail->Subject = $title;                             // 邮件主题
	$mail->Body = $content;                              // 邮件内容
	$mail->AltBody = strip_tags($content);

	return $mail->Send();
	
}
function GetValue($FLib)
{
	global $title,$content;
    $title            = $FLib->requestchar('title',0,100,'标题',1);
	$content          = $FLib->requestchar('contentvalue',0,0,'文本内容',1,3);
}
$DataBase->CloseDataBase();   
?>
