<?php
require_once('../include/TopFile.php');
require_once('../include/File.class.php');
$File = new FileClass;
$Admin->CheckPopedoms('SC_SITE_AD_MDY');
$IdList = $FLib->RequestChar('IdList',0,0,'参数',1);

if (!is_numeric(str_replace(" ","",str_replace(",","",$IdList)))) { echo showErr('参数错误');exit;}

$sql = 'Select ID,Vc_name,I_placeID,I_type,Vc_content,Vc_image,Vc_flash,Vc_link from site_advertise Where Status<>0  and ID in ('.$IdList.')';
$Rs  = $DataBase->GettArrayResult($sql);
$k=0;
$htmlcord = "";
$mm = "0";
$nn = count($Rs);
if(is_array($Rs)){
	for($k=0;$k<count($Rs);$k++){     
		$Re1=$DataBase->SelectSql("SELECT ID,Vc_name,Vc_start,Vc_end,Vc_file,I_width,I_height FROM site_advertise_place where Status <> 0 and ID =".$Rs[$k][2]);
		//判断是否存在广告位
		if($DataBase->GetResultRows($Re1) >0){
			$Rs1 = $DataBase->GetResultArray($Re1);
			if (strchr($Rs1[4],',')!=""){
				$file_name = explode(',',$Rs1[4]);
			}else{
				$file_name[0] = $Rs1[4];
			}
			//广告位循环	
			for ($i=0;$i<count($file_name);$i++){
				if($file_name[$i]!=""){	
					$rt = $File->ReadFile(WEBROOT.str_replace('/',L,substr($file_name[$i],1)),1);
					if(isset($rt['err'])){echo showErr($rt['err']);exit;}

					if($Rs[$k][3] == 1 && $Rs[$k][5]<>""){
						$htmlcord = "<img src='".$Rs[$k][5]."' width='".$Rs1[5]."' height='".$Rs1[6]."' />";
					}else if($Rs[$k][3] == 2 && $Rs[$k][6]<>""){
						$htmlcord = "<script language='javascript'>write_flash('".$Rs1[5]."','".$Rs1[6]."','".$Rs[$k][6]."')</script>";
					}else if($Rs[$k][3] == 3 && $Rs[$k][4]<>""){
						$htmlcord = $Rs[$k][4];
					}
					if($htmlcord!=""){
						if($Rs[$k][7] != ""){
							$htmlcord = '<a href="http://'.$Rs[$k][7].'" target="_blank" >'.$htmlcord.'</a>';
						}
					}
					$rt  = $File->WriteFile(WEBROOT.str_replace('/',L,substr($file_name[$i],1)),$File->Re_C($File->ReadFile(WEBROOT.str_replace('/',L,substr($file_name[$i],1))),'|'.$File->FilterStr($Rs1[2]).'[\S\s]*?'.$File->FilterStr($Rs1[3]).'|',$Rs1[2].$htmlcord.$Rs1[3]));
					if(isset($rt['err'])){echo showErr($rt['err']);exit;}

					//将同一广告位的其他广告置为禁用状态
					$DataBase->QuerySql("UPDATE site_advertise SET I_active=0 WHERE status<>0 and I_placeID=".$Rs[$k][2]);
					$DataBase->QuerySql("UPDATE site_advertise SET I_active=1 WHERE status<>0 and ID=".$Rs[$k][0]);  
				}
			} 	 
		}
		$mm = $mm + 1;
	}//循环结束
}//判断是否为数组
$Admin ->AddLog('广告管理','更新广告：更新成功'.$mm.'条，失败'.($nn-$mm).'条。');

unset($Rs);
unset($Rs1);
//echo showSuc('1.符合更新条件数：'.$nn.'<br>2.实际更新成功数：'.$mm.'<br>3.本次更新失败数：'.($nn-$mm),$FLib->IsRequest('backurl'),'self');
echo showSuc('1.符合更新条件数：'.$nn.'<br>2.实际更新成功数：'.$mm.'<br>3.本次更新失败数：'.($nn-$mm),'AdvertList.php?status=1','self');
$DataBase->CloseDataBase();
?>

