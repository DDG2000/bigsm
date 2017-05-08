<?php


require_once('../include/TopFile.php');

$t='数据库操作';
$Work   = $FLib->RequestChar('Work',1,50,'参数',1);



//sql 不为空时执行动作
$sql = $FLib->IsRequest('sql');
$sql = get_magic_quotes_gpc() ? stripslashes($sql) : $sql;
$act = strtolower(substr($sql,0,6));
if($act=='select'){
	//查询sql语句
	$htmls='<div class=\'sql\'><table>';
	$query = mysql_query($sql);
	$col = mysql_num_fields($query);
	$htmls .= '<tr>';
	for($i=0; $i<$col; $i++){
		$htmls .= '<td>'. mysql_field_name($query,$i) .'</td>';
	}
	$htmls .= '</tr>';
	if(mysql_num_rows($query) > 0){
		while($rs = mysql_fetch_row($query)){
			$htmls .= '<tr>';
			foreach($rs as $v){
				$htmls .= '<td>'. $v .'</td>';
			}
			$htmls .= '</tr>';
		}
	}else{
		 $htmls .= '<tr><td colspan=\'100\'>暂无相关数据</td></tr>';
	}
	$htmls .= '</table></div>';
	echo '<script>parent.showPhpTipFun({width:700,height:350,headingText:"查询结果",maincontentText:"'.$htmls.'"});</script>';
	exit;

}elseif($act=='update'||$act=='delete'){
	//执行sql 语句
	if(mysql_query($sql)){
		echo showSuc('sql语句执行成功','DataBaseControl.php','self'); 
		exit;
	}else{
		echo showSuc('sql语句执行失败','DataBaseControl.php','self'); 
		exit;
	}
}else{
	echo showSuc('sql语句格式不正确','DataBaseControl.php','self'); 
	exit;
}
