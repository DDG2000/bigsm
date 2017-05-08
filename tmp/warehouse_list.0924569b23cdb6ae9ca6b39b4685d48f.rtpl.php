<?php if(!class_exists('raintpl')){exit;}?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Insert title here</title>
</head>
<body>
<table>
<tr class="tr1">
	
	<td>序号</td>
	<td>仓库名称</td>
	<td>仓库地址</td>
	<td>创建时间</td>
	<td>操作</td>
	
</tr>
<?php $counter1=-1; if( isset($data["data"]) && is_array($data["data"]) && sizeof($data["data"]) ) foreach( $data["data"] as $key1 => $value1 ){ $counter1++; ?>
<?php $vo=$this->var['vo']=$value1;?>
<tr data="it" >
	<td><?php echo $vo["id"];?></td>
	<td><?php echo $vo["Vc_name"];?></td>
	<td>
	<?php if( $vo["proname"]==$vo["cityname"] ){ ?>
	<?php echo $vo["proname"];?><?php echo $vo["disname"];?><?php echo $vo["Vc_address"];?>
	<?php }else{ ?>
	<?php echo $vo["proname"];?><?php echo $vo["cityname"];?><?php echo $vo["disname"];?><?php echo $vo["Vc_address"];?>
	<?php } ?>
	
	</td>

	<td><?php echo formatTime($vo['Createtime'],'Y-m-d H:i'); ?></td>
	
	
	<td>
	<a href="/index.php?act=shop&m=warehouse&w=edit&id=<?php echo $vo["id"];?>">编辑</a>
	<a href="index.php?act=shop&m=warehouse&w=del&id=<?php echo $vo["id"];?>">删除</a>
	
	</td>
	
	
</tr>
<?php } ?>

<?php if( $data["count"]==0 ){ ?>
<tr data="it"><td colspan="3" align="center">无此相关数据！</td></tr>
<?php } ?>
</table>
<div id="pages" class="page"><?php echo $pagestr;?></div>
</body>
</html>