<?php if(!class_exists('raintpl')){exit;}?>	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/css") . ( substr("inc/usercenter/css",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/css") );?>
    <title>卖家中心 - 集采报名</title>
</head>
<body>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/shopcenter/header") . ( substr("inc/shopcenter/header",-1,1) != "/" ? "/" : "" ) . basename("inc/shopcenter/header") );?>
    <!-- 中间内容部分 -->
    <div id="content">
        <div class="w1100 c cb">
		<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/shopcenter/side") . ( substr("inc/shopcenter/side",-1,1) != "/" ? "/" : "" ) . basename("inc/shopcenter/side") );?>
            <!-- 内容区 -->
            <div class="fr content-main">
            	<div class="content-main-con no">
					
            		<div class="ib-a data-list-table data-list-table-other">
            			<table border="0">
            				<thead>
            					<th width="108">集采名称</th>
            					<th width="125">集采品名</th>
            					<th width="80">集采重量</th>
            					<th width="118">项目地址</th>
            					<th width="160">集采期限</th>
            					<th width="76">集采状态</th>
            					<th width="86">参与状态</th>
            					<th>操作</th>
            				</thead>
            				<tbody
								<?php $counter1=-1; if( isset($data["data"]) && is_array($data["data"]) && sizeof($data["data"]) ) foreach( $data["data"] as $key1 => $value1 ){ $counter1++; ?>
									<?php $vo=$this->var['vo']=$value1;?>
									<input type="hidden" name="id" value=<?php echo $vo["id"];?>/>
									<tr>
										<td><?php echo $vo["Vc_name"];?></td>
										<td>
											<?php $counter2=-1; if( isset($vo["itemArray"]) && is_array($vo["itemArray"]) && sizeof($vo["itemArray"]) ) foreach( $vo["itemArray"] as $key2 => $value2 ){ $counter2++; ?>
												<?php $sv=$this->var['sv']=$value2;?>
												<?php echo $sv["Vc_name"];?>、
											<?php } ?>
										</td>
										<td><?php echo $vo["N_weight"];?></td>
										<td>
											<?php if( $vo["proname"]==$vo["cityname"] ){ ?>
												<?php echo $vo["cityname"];?><?php echo $vo["disname"];?><?php echo $vo["Vc_address"];?>
											<?php }else{ ?>
												<?php echo $vo["proname"];?><?php echo $vo["cityname"];?><?php echo $vo["disname"];?><?php echo $vo["Vc_address"];?>
											<?php } ?>
										</td>
										<td><?php echo $vo["D_start"];?>—<?php echo $vo["D_end"];?></td>
										<td>
											<?php if( $vo["I_status"]==10 ){ ?>
												审核中
											<?php }elseif( $vo["I_status"]==20 ){ ?>
													招标中
											<?php }elseif( $vo["I_status"]==30 ){ ?>
												已截至
											<?php }elseif( $vo["I_status"]==40 ){ ?>
												已成交
											<?php }elseif( $vo["I_status"]==50 ){ ?>
												审核不通过
											<?php } ?>
										</td>
										<td>
											<?php if( in_array($vo["id"], $IcpIDArr) ){ ?>
												已报名
											<?php }else{ ?>
												未报名
											<?php } ?>
										</td>
										<td>
											<a href="http://www.bigsm.com/index.php?act=shop&m=concentrated&w=detail&id=<?php echo $vo["id"];?>" data-event="show">查看</a>
											<?php if( in_array($vo["id"], $IcpIDArr) ){ ?>
												<a href="javascript:;" class="change">修改信息</a>
											<?php }else{ ?>
												<a href="javascript:;" data-event="sign">报名</a>
											<?php } ?>
											<a href="javascript:;" data-event="delete">删除</a>
										</td>
									</tr>
								<?php } ?>
            				</tbody>
            			</table>
            		</div>
            	</div>
            </div>
            <!-- 内容区 -->
        </div>
    </div>
    <!-- 中间内容部分 -->
	<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/usercenter/footer") . ( substr("inc/usercenter/footer",-1,1) != "/" ? "/" : "" ) . basename("inc/usercenter/footer") );?>
    <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("inc/js") . ( substr("inc/js",-1,1) != "/" ? "/" : "" ) . basename("inc/js") );?>