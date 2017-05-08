<?php if(!class_exists('raintpl')){exit;}?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Insert title here</title>
</head>
<body>
<table>
<tr class="tr1">
	
	<td>排序号</td>
	<td>名称</td>
	<td>链接</td>
	<td>图片</td>
	<td>创建时间</td>
	<td>操作</td>
	
</tr>
<?php $counter1=-1; if( isset($data["list"]) && is_array($data["list"]) && sizeof($data["list"]) ) foreach( $data["list"] as $key1 => $value1 ){ $counter1++; ?>
<?php $vo=$this->var['vo']=$value1;?>
<tr data="it" >
	<input type="hidden" name="id" value=<?php echo $vo["id"];?>/>
	<td><?php echo $vo["I_order"];?></td>
	<td><?php echo $vo["Vc_name"];?></td>
	<td><?php echo $vo["Vc_linkurl"];?></td>
	<td><a href="<?php echo $vo["Vc_url"];?>#">查看</a></td>
	
	<td><?php echo formatTime($vo['Createtime'],'Y-m-d'); ?></td>
	
	
	<td>
	<a href="/index.php?act=shop&m=slides&w=edit&id=<?php echo $vo["id"];?>">编辑图片</a>
	
	</td>
	
	
</tr>
<?php } ?>

<?php if( empty($data['list']) ){ ?>
<tr data="it"><td colspan="3" align="center">无此相关数据！</td></tr>
<?php } ?>
</table>

</body>
</html>