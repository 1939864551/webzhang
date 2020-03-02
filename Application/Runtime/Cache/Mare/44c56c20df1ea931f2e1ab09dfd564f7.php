<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link rel="stylesheet" type="text/css" href="/mare/Public/mars/css/bootstrap/bootstrap.css"/>
		<link rel="stylesheet" type="text/css" href="/mare/Public/mars/css/font-awesome.min.css"/>
		<link rel="stylesheet" type="text/css" href="/mare/Public/mars/css/font-awesome-ie7.min.css"/>
		<link rel="stylesheet" type="text/css" href="/mare/Public/mars/css/style.css"/>
		<link rel="stylesheet/less" type="text/css" href="/mare/Public/mars/less/style.less" />
		<link rel="stylesheet" media="all" type="text/css" href="/mare/Public/mars/css/timepicker/jquery-ui.css" />
		<link rel="stylesheet" type="text/css" href="/mare/Public/mars/css/timepicker/jquery-ui-timepicker-addon.css"/>
		
		<script src="/mare/Public/mars/js/lib/less-1.3.3.min.js" type="text/javascript" charset="utf-8"></script>
		<!-- HTML5 shim for IE8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<![endif]-->
		<!--[if IE 7]>
		<link rel="stylesheet" href="__TMPL__Public/simpleboot/font-awesome/4.4.0/css/font-awesome-ie7.min.css">
		<![endif]-->
		<!--[if lte IE 6]>
		<link rel="stylesheet" type="text/css" href="/mare/Public/mars/bootstrap/css/bootstrap-ie6.css">
		<link rel="stylesheet" type="text/css" href="/mare/Public/mars/bootstrap/css/ie.css">
		<![endif]-->
		<title><?php echo (C("TITLE_TEXT")); ?></title>
	</head>
<style>
.btn-no:hover,
.btn-no:focus
{
	background-color: #f7f7f7;
	cursor: default;
}
</style>
	<body>
		<!--边栏-->
		<div class="sidebar-container navbar-fixed-left">
			<h3 class="navbar-brand-m">
				<?php echo (C("TOP_TEXT")); ?>
			</h3>
			<div class="clearfix"></div>
			<ul class="main-menu panel-group" id="main-menu" role="tablist" aria-multiselectable="true">
	<?php $tid = $_SESSION[$_SESSION['randomstr']]['tid']; ?>
	
	<?php if($tid == 4): ?>
	<li class="panel" role="tab" id="heading1">
		<a role="button" data-toggle="collapse" data-parent="#main-menu" href="#collapse1" aria-expanded="false" aria-controls="collapse1" 
			<?php if(MODULE_NAME == 'Mare' and (CONTROLLER_NAME == 'Index' OR CONTROLLER_NAME == 'Setting')): ?>class="active"<?php endif; ?>
		 >
			<i class="fa fa-dashboard"></i>  系统设置 <i class="menu-icon fa fa-angle-right"></i>
		</a>
		
		<?php if(MODULE_NAME == 'Mare' and (CONTROLLER_NAME == 'Index' OR CONTROLLER_NAME == 'Setting')): ?><ul id="collapse1" role="tabpanel" aria-labelledby="heading1" aria-expanded="true"  class="children-list panel-collapse collapse in active">
		<?php else: ?>
		<ul id="collapse1" role="tabpanel" aria-labelledby="heading1" aria-expanded="false"  class="children-list panel-collapse collapse"><?php endif; ?>
			<li>
				<a href="<?php echo U('Mare/Index/index');?>"  
					<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Index' and ACTION_NAME == 'index'): ?>class="active"<?php endif; ?>
				>
					系统信息 <i class="menu-icon fa fa-angle-right"></i>
				</a>
			</li>
			<li>
				<a href="<?php echo U('Mare/Setting/set_index');?>"
					<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Setting' and ACTION_NAME == 'set_index'): ?>class="active"<?php endif; ?>
				>
					网络配置 <i class="menu-icon fa fa-angle-right"></i>
				</a>
			</li>

			<li>
				<a href="<?php echo U('Mare/Setting/system_log');?>"
				<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Setting' and ACTION_NAME == 'system_log'): ?>class="active"<?php endif; ?>
				>
				日志设置 <i class="menu-icon fa fa-angle-right"></i>
				</a>
			</li>
		</ul>
	</li>
	<?php endif; ?>
	<?php if($tid ==5): ?>
	<li class="panel" role="tab" id="heading1">
		<a role="button" data-toggle="collapse" data-parent="#main-menu" href="#collapse1" aria-expanded="false" aria-controls="collapse1" 
			<?php if(MODULE_NAME == 'Mare' and (CONTROLLER_NAME == 'Index' OR CONTROLLER_NAME == 'Setting' OR CONTROLLER_NAME == 'Device')): ?>class="active"<?php endif; ?>
		 >
			<i class="fa fa-dashboard"></i>  系统设置 <i class="menu-icon fa fa-angle-right"></i>
		</a>
		
		<?php if(MODULE_NAME == 'Mare' and (CONTROLLER_NAME == 'Index' OR CONTROLLER_NAME == 'Setting' OR CONTROLLER_NAME == 'Device' )): ?><ul id="collapse1" role="tabpanel" aria-labelledby="heading1" aria-expanded="true"  class="children-list panel-collapse collapse in active">
		<?php else: ?>
		<ul id="collapse1" role="tabpanel" aria-labelledby="heading1" aria-expanded="false"  class="children-list panel-collapse collapse"><?php endif; ?>
			<li>
				<a href="<?php echo U('Mare/Index/index');?>"  
					<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Index' and ACTION_NAME == 'index'): ?>class="active"<?php endif; ?>
				>
					系统信息 <i class="menu-icon fa fa-angle-right"></i>
				</a>
			</li>
			<li>
				<a href="<?php echo U('Mare/Setting/set_index');?>"
					<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Setting' and ACTION_NAME == 'set_index'): ?>class="active"<?php endif; ?>
				>
					网络配置 <i class="menu-icon fa fa-angle-right"></i>
				</a>
			</li>
			<li>
				<a href="<?php echo U('Mare/Setting/system_log');?>"
				<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Setting' and ACTION_NAME == 'system_log'): ?>class="active"<?php endif; ?>
				>
				日志设置 <i class="menu-icon fa fa-angle-right"></i>
				</a>
			</li>
			<!--  <li>
				<a href="<?php echo U('Mare/Setting/wifi_manager');?>"
				<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Setting' and ACTION_NAME == 'wifi_manager'): ?>class="active"<?php endif; ?>
				>
				WiFi管理 <i class="menu-icon fa fa-angle-right"></i>
				</a>
			</li>
			-->

			<!--<li>-->
				<!--<a href="<?php echo U('Mare/Setting/set_proxy');?>"-->
				<!--<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Setting' and ACTION_NAME == 'set_proxy'): ?>-->
					<!--class="active"-->
				<!--<?php endif; ?>-->
				<!--&gt;-->
				<!--代理设置 <i class="menu-icon fa fa-angle-right"></i>-->
				<!--</a>-->
			<!--</li>-->

			<li>
				<a href="<?php echo U('Mare/Setting/set_globalrule');?>"
					<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Setting' and ACTION_NAME == 'set_globalrule'): ?>class="active"<?php endif; ?>
				>
					扫描规则配置 <i class="menu-icon fa fa-angle-right"></i>
				</a>
			</li>

			<li>
				<a href="<?php echo U('Mare/Setting/set_client_rule');?>"
				<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Setting' and ACTION_NAME == 'set_client_rule'): ?>class="active"<?php endif; ?>
				>
				客户端自定义规则 <i class="menu-icon fa fa-angle-right"></i>
				</a>
			</li>

			<li>
				<a href="<?php echo U('Mare/Setting/set_watermark');?>"
					<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Setting' and (ACTION_NAME == 'set_watermark' OR ACTION_NAME == 'set_lookwatermark')): ?>class="active"<?php endif; ?>
				>
					报告模版设置 <i class="menu-icon fa fa-angle-right"></i>
				</a>
			</li>
			<li>
				<a href="<?php echo U('Mare/Setting/set_security');?>"
					<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Setting' and ACTION_NAME == 'set_security'): ?>class="active"<?php endif; ?>
				>
					安全设置 <i class="menu-icon fa fa-angle-right"></i>
				</a>
			</li>
			
			<?php if($tid == 2 || $tid ==5): ?>
			<li>
				<a href="<?php echo U('Mare/Device/dev_index');?>" 
					<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Device' and (ACTION_NAME == 'dev_index' OR ACTION_NAME == 'dev_view' )): ?>class="active"<?php endif; ?>
				>
					 设备列表 <i class="menu-icon fa fa-angle-right"></i>
				</a>
			</li>
			<?php endif; ?>
				
			<li>
				<a href="<?php echo U('Mare/Setting/set_service');?>"
					<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Setting' and ACTION_NAME == 'set_service'): ?>class="active"<?php endif; ?>
				>
					系统升级 <i class="menu-icon fa fa-angle-right"></i>
				</a>
			</li>
		</ul>
	</li>
	<?php endif; ?>
		

		
	<?php if($tid == 2 || $tid ==5): ?>
	<li>
		<a href="<?php echo U('Mare/Task/task_select_type');?>"
			<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Task' and (ACTION_NAME == 'task_add' OR ACTION_NAME == 'task_select_type' OR ACTION_NAME == 'task_web_add' OR ACTION_NAME == 'task_weixin_add' OR ACTION_NAME == 'task_awvs_add')): ?>class="active"<?php endif; ?>
		>
			<i class="fa fa-tasks"></i>  新建任务 <i class="menu-icon fa fa-angle-right"></i>
		</a>
	</li>
	<li>
		<a href="<?php echo U('Mare/Task/task_list');?>"
			<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Task' and (ACTION_NAME != 'task_add' AND ACTION_NAME != 'task_select_type' AND ACTION_NAME != 'task_web_add' and ACTION_NAME != 'task_weixin_add' and ACTION_NAME != 'task_awvs_add')): ?>class="active"<?php endif; ?>
		>
			<i class="fa fa-list-ol"></i>  任务管理 <i class="menu-icon fa fa-angle-right"></i>
		</a>
	</li>
	<?php endif; ?>

	<?php if($tid == 2 || $tid ==5): ?>
	<li>
		<a href="<?php echo U('Mare/Report/rep_index');?>"
			<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Report'): ?>class="active"<?php endif; ?>>
			<i class="fa fa-list-alt"></i>  报告导出 <i class="menu-icon fa fa-angle-right"></i>
		</a>
	</li>
	<?php endif; ?>
	<?php if($tid == 3 || $tid ==5): ?>
	<li>
		<a href="<?php echo U('Mare/Log/log_index');?>"
			<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Log'): ?>class="active"<?php endif; ?>
		>
			<i class="fa fa-clipboard"></i>  日志审计 <i class="menu-icon fa fa-angle-right"></i>
		</a>
	</li>
	<?php endif; ?>
		<?php if($tid ==5): ?>
	<li class="panel" role="tab" id="heading1">
		<a role="button" data-toggle="collapse" data-parent="#main-menu" href="#collapse2" aria-expanded="false" aria-controls="collapse2" 
			<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'User' and (ACTION_NAME == 'user_index' OR ACTION_NAME == 'user_edit' OR ACTION_NAME == 'user_add' OR ACTION_NAME == 'user_view' OR ACTION_NAME == 'ad_user' OR ACTION_NAME == 'check_status_index')): ?>class="active"<?php endif; ?>
		 >
			<i class="fa fa-users"></i>  用户管理 <i class="menu-icon fa fa-angle-right"></i>
		</a>
		
		<?php if(MODULE_NAME == 'Mare' and (CONTROLLER_NAME == 'User') and (ACTION_NAME != 'user_perinfo') and (ACTION_NAME != 'user_peredit')): ?><ul id="collapse2" role="tabpanel" aria-labelledby="heading1" aria-expanded="true"  class="children-list panel-collapse collapse in active">
		<?php else: ?>
		<ul id="collapse2" role="tabpanel" aria-labelledby="heading1" aria-expanded="false"  class="children-list panel-collapse collapse"><?php endif; ?>
			<li>
				<a href="<?php echo U('Mare/User/user_index');?>"  
					<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'User' and (ACTION_NAME == 'user_index' and $_GET['ldap'] == '') OR (ACTION_NAME == 'user_edit' and $_GET['ldap'] == '') OR (ACTION_NAME == 'user_add' and $_GET['ldap'] == '') OR (ACTION_NAME == 'user_view' and $_GET['ldap'] == '')): ?>class="active"<?php endif; ?>
				>
					管理用户 <i class="menu-icon fa fa-angle-right"></i>
				</a>
			</li>
            <?php if(C("REGISTER_MODE")== 1): ?><li>
                        <a href="<?php echo U('Mare/User/check_status_index');?>"
                        <?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'User' and (ACTION_NAME == 'check_status_index')): ?>class="active"<?php endif; ?>
                        >
                        待审核用户 <i class="menu-icon fa fa-angle-right"></i>
                        </a>
                    </li><?php endif; ?>			
			<?php if(C("ADUSER_MODE")== 1): ?><li>
				<a href="<?php echo U('Mare/User/user_index', ['ldap' => 1]);?>"  
					<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'User' and (ACTION_NAME == 'user_index' and $_GET['ldap'] == 1) OR (ACTION_NAME == 'user_edit' and $_GET['ldap'] == 1) OR (ACTION_NAME == 'user_add' and$_GET['ldap'] == 1) OR (ACTION_NAME == 'user_view' and $_GET['ldap'] == 1)): ?>class="active"<?php endif; ?>
				>
					域用户管理 <i class="menu-icon fa fa-angle-right"></i>
				</a>
			</li><?php endif; ?>
		</ul>
	</li>
	<?php endif; ?>
		<?php if($tid ==5): ?>
	<li>
		<a href="<?php echo U('Mare/Database/index');?>"
			<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'Database' and (ACTION_NAME == 'index')): ?>class="active"<?php endif; ?>>
			<i class="fa fa-database"></i>  数据库管理 <i class="menu-icon fa fa-angle-right"></i>
		</a>
	</li>
	<?php endif; ?>
	<li>
		<a href="<?php echo U('Mare/User/user_perinfo');?>"
			<?php if(MODULE_NAME == 'Mare' and CONTROLLER_NAME == 'User' and (ACTION_NAME == 'user_perinfo' OR ACTION_NAME == 'user_peredit')): ?>class="active"<?php endif; ?>>
			<i class="fa fa-user"></i>  个人信息 <i class="menu-icon fa fa-angle-right"></i>
		</a>
	</li>
	
</ul>
		</div>
		<!--边栏-->
		<!--主体-->
		<div class="wrapper">
			<!--顶栏-->
			<div class="header-container">
	<div class="container-fluid">
		<div class="menu-btn pull-left">
			<div class="line line-1"></div>
			<div class="line line-2"></div>
			<div class="line line-3"></div>
		</div>
		
		<div class="user-info pull-right">
			<ul class="info-group">
                <li>
                	<a >您好，&nbsp;
	                    <span style='color: #2196F3;font-size: 14px;'> <?php echo $_SESSION[$_SESSION['randomstr']]['nickname'] ?></span>
	                    &nbsp;
	                    <?php $authname = $_SESSION[$_SESSION['randomstr']]['authname']; echo $authname; ?>
                    </a>
                </li>
                <li><a href="<?php echo U('Layout/logout');?>">&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-size: 14px;'>登出</span>&nbsp;&nbsp;</a></li>
			</ul>
		</div>
	</div>
</div>
			<!--顶栏-->
			
			<div class="block-wrapper">
				<div class="container-fluid">
					
					<div class="col-xs-12">
						<div class="block-content">
							<div class="block-head">
								<h4>任务列表</h4>
							</div>
							<div class="block-body">
								<form class="form-vertical clearfix" id="search" action="/mare/index.php/Mare/Task/task_list" method="get">
									<div class="form-group col-md-4 form-inline">
										<div class="input-group">
											<label for="starttime">开始时间</label>
											<input class="form-control" name="starttime" type="text" id="starttime" value="<?php echo ($_GET['starttime']); ?>" placeholder="开始时间" />
										</div>
										
										<div class="input-group">
											<label for="totime">结束时间</label>
											<input class="form-control" name="endtime" type="text"  value="<?php echo ($_GET['endtime']); ?>" id="totime" placeholder="结束时间" />
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group col-md-6">
											<label for="">类型</label>
											<select class="form-control"  name="type">
											
											<option value="task" <?php if($_GET['type']== task): ?>selected="true"<?php endif; ?>>任务名</option>
											
										
											
											<option value="app"  <?php if($_GET['type']== app): ?>selected="true"<?php endif; ?>>应用名</option>
											
										
											
											<option value="member"  <?php if($_GET['type']== member): ?>selected="true"<?php endif; ?>>测试员</option>
											
											
											<!-- <?php if(is_array($tasktype)): $i = 0; $__LIST__ = $tasktype;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i;?><option value="<?php echo ($type['tasktype']); ?>"><?php echo ($type['tasktype']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?> -->
											</select>
										</div>
										
										<div class="form-group col-md-6">
											<label for="user">名称</label>
											<input class="form-control" type="text" value="<?php echo ($_GET['condition']); ?>" name="condition" />
										</div>
									</div>
									<div class="form-group col-md-4" style="padding-top: 25px;">
										<button type="button" id="search" onclick="check()" class="btn-sm btn-blue"><i class="fa fa-search"></i> 查询</button>
										<!--  <button type="button" id="export" class="btn-sm btn-blue-border"><i class="fa fa-mail-forward"></i> 导出</button>
										<button type="button" id="empty" class="btn-sm btn-red-border"><i class="fa fa-trash-o"></i>清空</button>
									--></div>
									<div class="clearfix"></div>
								</form>
							</div>
						</div>
					</div>
					
					<div class="col-xs-12">
						<div class="block-content">
							<div class="block-body">
								<table class="table table-hover">
									<thead>
										<tr>
											<th width="100px"><div style="margin-top: 4px;"><INPUT name="chkAll" class="checkbox pull-left" id="chkAll" title="全选" onClick="ChkAllClick('chkSon','chkAll')" type="checkbox" /><span class="pull-left" style="margin-top: 4px;">全选</span></div></th>
											<th>编号</th>
											<th>任务名</th>
											<th>图标</th>
											<th>类型</th>
                                            <th>应用名</th>
                                            <th>测试员</th>
                                            <!-- <th>检测IP</th> -->
											<th>检测时间</th>
											<?php $maintain = D('Maintain'); if($private_or_public = $maintain->where(array('key'=>'private_or_public'))->find()['value']): ?>
											<th>任务状态</th>
											<?php else: ?>
											<th>结束状态</th>
											<?php endif; ?>
											<th>操作</th>
										</tr>
									</thead>
									<tbody>
                                        <?php if(is_array($tasklist)): foreach($tasklist as $key=>$vo): ?><tr>
											<td><INPUT class="checkbox pull-left" name="chkSon" id="chkSon<?php echo ($key + 1 + $star); ?>" type="checkbox"  value="<?php echo ($vo['appid']); ?>" onclick="ChkSonClick('chkSon','chkAll')" /></td>
											<td><span class="pull-left" style="height: 20px;margin-top: 4px;margin-left:3px;"><?php echo ($key + 1 + $star); ?></span></td>
											<!-- <td><a href="<?php echo U('Mare/Task/task_item_parameter',array('appid'=>$vo['appid']));?>"> -->
											<td><a href="<?php echo U('Mare/Task/task_detial',array('appid'=>$vo['appid']));?>">
											<?php  if(strlen($vo['task_name']) > 40){ echo mb_substr($vo['task_name'],0,20,'utf-8')."..."; }else{ echo $vo['task_name']; } ?>
											</a></td>
											<td id='icon'>
											<?php if($vo['tasktype'] == 'web' || trim($vo['tasktype']) == 'awvs'):?>
											-
											<?php else:?>
											<img src="/mare/<?php echo ($vo["icon"]); ?>" width="25px" />
											<?php endif;?>
											</td>
											<td>
											<?php if($vo["tasktype"] == 'ios'): ?>iOS
											<?php elseif($vo["tasktype"] == 'wx'): ?>
											微信
											<?php elseif($vo['tasktype'] == 'web'): ?>
											WEB被动检测
											<?php elseif(trim($vo['tasktype']) == 'awvs'): ?>
											WEB主动检测
											<?php else: ?>
											Android<?php endif; ?>
											</td>
                                            <td id='realname'>
                                            <?php if(trim($vo['tasktype']) == 'web' || trim($vo['tasktype']) == 'awvs' || trim($vo['tasktype']) == 'wx'):?>
											-
											<?php else:?>
											<?php echo ($vo["app"]); ?>
											<?php endif;?>
                                            </td>
											<td>
											<?php if($vo['realname'] == null) :?>
											-
											<?php else:?>
											<?php echo ($vo["realname"]); ?>
											<?php endif;?>
											</td>
											<td><?php echo ($vo["uploadtime"]); ?></td>
											<?php $maintain = D('Maintain'); if($private_or_public = $maintain->where(array('key'=>'private_or_public'))->find()['value']): ?>
											<td id='status'><?php  if ( $vo['status'] == -2 ){ $html_str = "<div title=\"原因：APP未签名\">".$appstatus[$vo['status']]."<div>"; echo $html_str; }elseif ( $vo['status'] == -1 ){ $html_str = "<div title=\"原因：APP安装失败\">".$appstatus[$vo['status']]."<div>"; echo $html_str; }elseif ( $vo['status'] == 10 ){ $html_str = "<div title=\"原因：正在停止\">".$appstatus[$vo['status']]."<div>"; echo $html_str; } elseif ( $vo['status'] == 11 ){ $html_str = "<div title=\"原因：停止检测\">".$appstatus[$vo['status']]."<div>"; echo $html_str; }elseif ( $vo['status'] == 7 &&!empty($vo['task_status']) ){ $html_str = "<div class='w-progress-wrapper'><span class='w-progress' style='width:".$appstatus[$vo['status']].";background:#e22517;'></span></div>"; echo $html_str; }else if ($vo['status'] == 9 && ($vo['tasktype'] == 'web' || $vo['tasktype'] == 'wx')) { $html_str = "<div class='w-progress-wrapper' <div title=\"任务配置或操作不正确,建议重新检测！\"><span class='w-progress' style='width:".$appstatus[$vo['status']].";background:#e22517;'></span></div>"; echo $html_str; }else{ $html_str = "<div class='w-progress-wrapper'><span class='w-progress' style='width:".$appstatus[$vo['status']]."'></span></div>"; echo $html_str; } ?><!-- <?php echo $vo['status']; ?> --></td>
											<?php else: ?>
											<td>
												<?php if($vo['status'] >= 8): ?>已结束
												<?php else: ?>
													未结束<?php endif; ?>
											</td>
											<?php endif; ?>
											<td id='action'>
											<!--
												<a href="<?php echo U('Mare/Task/task_detial',array('appid'=>$vo['appid']));?>" class="btn-sm btn-blue-border tooltips" data-toggle="tooltip" data-placement="top" title="查看">
													<i class="fa fa-eye"></i>
												</a> 
											-->
											<?php if($vo['status'] == 11 or $vo['status'] == 18): ?><a href="<?php echo U('Mare/report/rep_down_index',array('appid'=>$vo['appid']));?>" class="btn-sm btn-blue-border" data-toggle="tooltip" data-placement="top" title="导出报告">
												<i class="fa fa-file-text-o"></i>
											</a> 
											<?php else: ?>
											<!-- 
												<a class="btn-sm btn-white-border" data-toggle="tooltip" data-placement="top" title="导出报告">
													<font color="#afabab"><i class="fa fa-mail-forward"></i></font>
												</a>  --><?php endif; ?>
											<?php if($_SESSION[$_SESSION['randomstr']]['tid'] == 5 ): if($vo['status'] < 11 or $vo['status'] == 17):?>	
													<a class="btn-sm btn-red-border btn-disabled" data-toggle="tooltip" data-placement="top" title="删除" >
														<i class="fa fa-trash-o"></i>
													</a> 
													<a  onclick="stoptask('<?php echo ($vo[appid]); ?>')" class="btn-sm btn-red-border" data-toggle="tooltip" data-placement="top" title="停止">
														<i class="fa fa-stop"></i>
													</a>
												<?php else:?>
													<a onclick="deletetask('<?php echo ($vo[appid]); ?>')" class="btn-sm btn-red-border" data-toggle="tooltip" data-placement="top" title="删除">
														<i class="fa fa-trash-o"></i>
													</a> 
												<?php endif; endif; ?>
											</if>
											<?php if($isdexdown == 1): if($vo['dexdump'] == 1 and $arr[$vo['appid']] > 1): ?><a href="<?php echo U('Mare/task/dexdown',array('appid'=>$vo['appid']));?>" class="btn-sm btn-blue-border" data-toggle="tooltip" data-placement="top" title="下载脱壳包">
												<i class="fa fa-arrow-down"></i>
											</a>
											<?php else: ?>
											<a href="javascript:volid(0);" class="btn-no btn-sm btn-blue-border" style="color:#adadad;border:2px solid #adadad;" data-toggle="tooltip" data-placement="top" title="下载脱壳包">
												<i class="fa fa-arrow-down"></i>
											</a><?php endif; endif; ?>
											</td>
										</tr><?php endforeach; endif; ?>
									</tbody>
                                                                      
								</table>
								<div>
									<!-- <div style="margin-top: 4px;">
										<INPUT name="chkAll" class="checkbox pull-left" id="chkAll" title="全选" onClick="ChkAllClick('chkSon','chkAll')" type="checkbox" /><span class="pull-left" style="margin-top: 4px;">全选</span>
									</div> -->
									<div style="margin-left:5px;margin-top: 4px;">
										<INPUT name="chkOpposite" class="checkbox pull-left" id="chkOpposite" title="反选" onClick="ChkOppClick('chkSon')" type="checkbox" /><span class="pull-left" style="margin-top: 4px;">反选</span>
									</div>
									<div style="margin-left:5px;">
										<button onclick="executeOperation()" class="btn-sm btn-blue">执行删除</button>
									</div>
								</div>
                                <div style='text-align:center;'><?php echo ($page); ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php if($num != 0): ?><div class="corner_box">
				<div class="corner_content">
					<span class="close_corner">
						<i class="fa fa-close"></i>
						<i class="fa fa-envelope-o fa-fw hide"></i>
					</span>
					<div class="corner_main">
						<p class="text-center">当前系统有 <span><a href="<?php echo U('Task/task_list?status=unsuccess');?>" class="red_color"><?php echo ($num); ?></a></span> 个未完成的检测任务</p>
					</div>
				</div>
			</div><?php endif; ?>
		</div>
		<!--主体-->
			<div class="mask" id="mask-overlay">&nbsp;</div>
<script src="/mare/Public/mars/js/lib/jquery/jquery-1.11.0.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/lib/bootstrap/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/lib/scrollreveal/scrollreveal.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/lib/smoothsroll/SmoothScroll.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/script/globalscript.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/js/common.js" type="text/javascript" charset="utf-8"></script>
			<script type="text/javascript" src="/mare/Public/mars/js/lib/timepicker/jquery-ui.min.js"></script>
	<script src="/mare/Public/mars/js/lib/timepicker/jquery-ui-timepicker-addon.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" src="/mare/Public/mars/js/lib/timepicker/i18n/jquery-ui-timepicker-addon-i18n.min.js"></script>
	<script src="/mare/Public/mars/js/lib/timepicker/i18n/jquery-ui-timepicker-zh-CN.js" type="text/javascript" charset="utf-8"></script>
	<script src="/mare/Public/mars/js/lib/timepicker/jquery-ui-sliderAccess.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript">
		$("#starttime").datetimepicker();
		$("#totime").datetimepicker();
		var cornerBox = $(".corner_box");
		var timeOut = setTimeout(function(){
				$(".corner_box").addClass("hide_box");
				$(".fa-envelope-o").removeClass("hide");
				$(".fa-close").addClass("hide");
			},15000);
		if (cornerBox.hasClass("hide_box")==false) {
			timeOut;
		}
		
		$(".close_corner").on("click",function(){
			clearTimeout(timeOut);
			if (cornerBox.hasClass("hide_box")==false) {
				$(".corner_box").addClass("hide_box");
				$(".fa-envelope-o").removeClass("hide");
				$(".fa-close").addClass("hide");
			}
			else{
				$(".corner_box").removeClass("hide_box");
				$(".fa-envelope-o").addClass("hide");
				$(".fa-close").removeClass("hide");
			}
		});

		$(function(){
			task_list_status();
			window.setInterval(task_list_status,5000); 
		})

		function task_list_status(){
			var appid_arr=[];
			$('input[name="chkSon"]').each(function(){ 
				appid_arr.push($(this).val());
			});
            $.ajax({
                type:"post",
                url:"/mare/index.php/Mare/Task/get_status_list.html",
                //url:"/index.php/Mare/Task/get_status_list.html",
                data:{appid:appid_arr},
                dataType:'json',
                async:true, //是否异步
                success: function(data){			
       				for (var i = 0; i <= data.length - 1; i++) {
       					if(data[i].status <= 3 ||data[i].status >= 0){
       						if ($('td#icon').eq(i).children('img').attr('src')=='/mare/') {
       							$('td#icon').eq(i).children('img').attr('src','/mare/'+data[i].icon);
       							$('td#realname').eq(i).text(data[i].realname);
       						};
       					}
       					if (data[i].status <= 9 && data[i].status >= 1){
       						if (data[i].status == 7 && data[i].task_status) {
       							$('td#status').eq(i).children('div').addClass('w-progress-wrapper');
       							$('td#status').eq(i).attr('title',data[i].task_status);
       							$('td#status').children('div').eq(i).html('<span  class="w-progress" style="width:' + data[i].percent + ';background:#e22517;">');
       						}else if (data[i].status == 9 && (data[i].tasktype == 'web' || data[i].tasktype =='wx')) {
                                $('td#status').eq(i).children('div').addClass('w-progress-wrapper');
                                $('td#status').eq(i).attr('title','任务配置或操作不正确,建议重新检测！');
                                $('td#status').children('div').eq(i).html('<span  class="w-progress" style="width:' + data[i].percent + ';background:#e22517;">');
							}else{
       							$('td#status').eq(i).children('div').addClass('w-progress-wrapper');
       							$('td#status').eq(i).attr('title',data[i].percent);
       							$('td#status').children('div').eq(i).html('<span class="w-progress" style="width:' + data[i].percent+'">');
       						}
       					}else{
	   						if (data[i].status == 18) {
	   							$('td#status').eq(i).children('div').addClass('w-progress-wrapper');
	   							$('td#status').eq(i).attr('title','检测完成');
       							$('td#status').children('div').eq(i).html('<span class="w-progress" style="width:' + data[i].percent+'">');
       						} else if (data[i].status == 17) {
       							$('td#status').eq(i).children('div').addClass('w-progress-wrapper');
	   							$('td#status').eq(i).attr('title','正在生成报告');
       							$('td#status').children('div').eq(i).html('<span class="w-progress" style="width:' + data[i].percent+'">');
       						} else if (data[i].status == 10 ||data[i].status == 11 ||data[i].status == -1 ||data[i].status == -2) {
       							$('td#status').eq(i).children('div').removeClass('w-progress-wrapper');
	       						$('td#status').eq(i).attr('title',data[i].percent);
       							$('td#status').children('div').eq(i).html('<span>'+data[i].percent+'</span>');
       						}
       					};
       					if (data[i].status == 11 || data[i].status == 18) { 
       						var id = appid_arr[i];
       						var inputa = $('td#action').eq(i).children('a').eq(0);
       						var inputa1 = $('td#action').eq(i).children('a').eq(1);
       						inputa.removeAttr("onclick");
       						inputa.attr("href","/mare/index.php/Mare/report/rep_down_index/appid/"+id+".html");
       						inputa.attr("title","导出报告");
       						inputa.children('i').attr("class","fa fa-file-text-o");
       						inputa.attr("class","btn-sm btn-blue-border");
       						inputa1.attr("onclick","deletetask('"+id+"')");
       						inputa1.attr("title","删除");
       						inputa1.children('i').attr("class","fa fa-trash-o");
       					};
       				}
                }
            })

		}

		function deletetask(appid){
			var r = confirm('确认删除任务?');
			if(r){
				$.ajax({
			        type: "get",
			        url: "<?php echo U('Mare/Task/task_del');?>",
			        async: true,
			        data: {appid:appid},
			        dataType: "json",
			        success: function (data1) {
			        	if(data1.code == 'false'){
			        		var actiontip  = '删除失败,请确定应用状态是否停止或检测完成!';
			        		var successTips = '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">'+actiontip+'</div>';
			        	}else{
			        		var actiontip  = '删除成功';
			        		var successTips = '<div id="action-tips" class="alert fade in alert-success global-tips" role="alert">'+actiontip+'</div>';
			        	}
			        	
			        	$(".block-wrapper").append(successTips);
			          	setTimeout(function(){
							$("#action-tips").alert("close");
							location.reload();
						},3000)
			           	
			        },
			        error: function () {
			            console.log('系统代码异常');
			        }
			    });
			}
			
		}
		function stoptask(appid){
			var r = confirm('确定停止?');
			if(r){
				$.ajax({
			        type: "get",
			        url: "<?php echo U('Mare/Task/task_stop');?>",
			        async: true,
			        data: {appid:appid},
			        dataType: "json",
			        success: function (data1) {
			        	if(data1.code == 'false'){
			        		var actiontip  = '停止检测失败';
			        		var successTips = '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">'+actiontip+'</div>';
			        	}else{
			        		var actiontip  = '停止检测成功';
			        		var successTips = '<div id="action-tips" class="alert fade in alert-success global-tips" role="alert">'+actiontip+'</div>';
			        	}
			        	
			        	$(".block-wrapper").append(successTips);
			          	setTimeout(function(){
							$("#action-tips").alert("close");
							location.reload();
						},3000)
			           	
			        },
			        error: function () {
			             console.log('系统代码异常');
			        }
			    });
			}
		}
		function check() {  
		  	var bb = "";  var temp = "";
		    var begins=0;
		    var ends=0;
		    var b = document.getElementsByName("starttime")[0].value;  
		    var e = document.getElementsByName("endtime")[0].value;

		    var reg=/^(\d{4})(-|\/)(\d{2})\2(\d{2}) (\d{2}):(\d{2})$/;
		    //alert(b);
		    if(b=="" || e==""){
		    	document.getElementById("search").submit();
		    }else{
		    	if (!reg.test(b) || !reg.test(e)) {
					alert("日期格式有误，请选择正确的日期!");
					return false;
				} else {
				//	alert(b);alert(e);
					var end = new Date();
					end.setFullYear(e.substring(0, 4));//年份
					end.setMonth(e.substring(5, 7) - 1);//月份
					end.setDate(e.substring(8, 10));//日数

					var begin = new Date();
					begin.setFullYear(b.substring(0, 4));//年份
					begin.setMonth(b.substring(5, 7) - 1);//月份
					begin.setDate(b.substring(8, 10));//日数
					//alert(Date.parse(end)/1000-Date.parse(begin)/1000);
					begins = Date.parse(begin) / 1000;
					ends = Date.parse(end) / 1000;
					if (ends - begins <= 0) {
						alert("结束日期需要大于起始日期！");
						return false;
					}
				//	document.getElementById("search").submit();
				}
			//alert("success");
				document.getElementById("search").submit();
			//$('search').submit();				
		    } 
		}
		// --列头全选框被单击---
function ChkAllClick(sonName, cbAllId){
    var arrSon = document.getElementsByName(sonName);
	var cbAll = document.getElementById(cbAllId);
	var tempState=cbAll.checked;
	for(i=0;i<arrSon.length;i++) {
		if(arrSon[i].checked!=tempState)
	       	arrSon[i].click();
	}
	document.getElementsByName("chkOpposite")[0].checked = false;
}

// --子项复选框被单击---
function ChkSonClick(sonName, cbAllId) {
	var arrSon = document.getElementsByName(sonName);
	var cbAll = document.getElementById(cbAllId);
	for(var i=0; i<arrSon.length; i++) {
		if(!arrSon[i].checked) {
			cbAll.checked = false;
			return;
		}
	}
	cbAll.checked = true;
}

// --反选被单击---
function ChkOppClick(sonName){
	var arrSon = document.getElementsByName(sonName);
	for(i=0;i<arrSon.length;i++) {
		arrSon[i].click();
	}
}
function executeOperation(){
	var r = confirm("您执行了删除操作,但是你不能删除检测中的应用,如需删除,需要停止检测!!");
	if(r){
		var arrSon = document.getElementsByName('chkSon');
		var appidarr 	= '';
		for(i=0;i<arrSon.length;i++) {
			if(arrSon[i].checked == true){
				appidarr += arrSon[i].value + ',';
			}
		}
		var appidstr 	= appidarr.substring(0,(appidarr.length-1));
		if(appidarr == ''){
			$(".block-wrapper").append('<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">请选择需要删除的应用!</div>');
          	setTimeout(function(){
				$("#action-tips").alert("close");
			},1000)
			return false;
		}
		$.ajax({
	        type: "get",
	        url: "/mare/index.php/Mare/Task/task_array_del/appidarr/"+appidstr,
	        async: true,
	        dataType: "json",
	        success: function (data1) {
	        	if(data1.code == 'false'){
	        		var actiontip  = '删除应用失败';
	        		var successTips = '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">'+actiontip+'</div>';
	        	}else{
	        		var actiontip  = '删除应用成功';
	        		var successTips = '<div id="action-tips" class="alert fade in alert-success global-tips" role="alert">'+actiontip+'</div>';
	        	}
	        	
	        	$(".block-wrapper").append(successTips);
	          	setTimeout(function(){
					$("#action-tips").alert("close");
					location.reload();
				},3000)
	           	
	        },
	        error: function () {
	             console.log('系统代码异常');
	        }
	    });
	}
	
}	
</script>

	</body>
</html>