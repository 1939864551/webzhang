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
					<form id="addtaskform" class="form-vertical" action="#" method="post" enctype="multipart/form-data">
						<div class="col-lg-6">
							<div class="block-content">
								<div class="block-head">
									<h4>新建WEB应用主动安全检测任务</h4>
								</div>
								<div class="block-body">
									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="taskname">任务名称 <font 	color="red">*</font> </label>
										</div>
										<div class="col-md-9">
											<input class="input" type="text" name="taskname" id="taskname" value="Web主动检测-<?php echo date('YmdHis');?>" placeholder="输入任务名称" />
										</div>
									</div>
									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="targeturl">扫描目标 <font 	color="red">*</font> </label>
										</div>
										<div class="col-md-9">
											<input type="hidden" name="addtime" value="<?php echo (session('addtime')); ?>"/>
											<input class="input isURLAddress required" type="text" name="targeturl" id="targeturl" value="" placeholder="输入扫描目标地址" />
										</div>
									</div>
									
									<div class="form-group row" id="hightestitem">
										<div class="col-md-3">
											<label class="inputlabel" for="webscan">扫描规则</label>
										</div>
										<div class="col-md-9 form-group-input">
											<?php if(is_array($back_scanrule)): $i = 0; $__LIST__ = $back_scanrule;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="form-group" style="float:left;">
												<label class="c-input c-input-checkbox" for="scanrule_<?php echo ($vo["id"]); ?>">
													<input  class="c-checkbox" type="checkbox" name="screeningcondition[]" id="scanrule_<?php echo ($vo["id"]); ?>" checked="" value="<?php echo ($vo["id"]); ?>" />
													<span class="c-icon-checked"></span>
													<?php echo ($vo['back_rule_zh']); ?>
												</label>
											</div><?php endforeach; endif; else: echo "" ;endif; ?>
										</div>
									</div>

									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="scope">扫描类型</label>
										</div>
										<div class="col-md-9">
											<select class="form-control" name="scope">
												<option value="ALL">完整扫描</option>
												<option value="SUBDOMAINS">扫描包括子域名</option>
												<option value="URL">只扫描当前URL</option>
											</select>
										</div>
									</div>
									
									<div class="form-group row" id="hightestitem">
										<div class="col-md-3">
											<label class="inputlabel" for="cust_test">高级选项</label>
										</div>
										<div class="col-md-9">
											<input class="checkbox" type="checkbox"  name="cust_test" id="cust_test" />
										</div>
									</div>
									<div class="form-group clearfix">
										<!--<button class="btn btn-blue pull-right" type="button" id="addnew" onclick="$('.form-vertical').submit()">确认新建</button>-->
										<button class="btn btn-blue pull-right" type="submit" id="addnew">确认新建</button>
									</div>		
									
								</div>
							</div>
						</div>
						
						<div class="col-lg-6" id="high-test" style="margin-bottom: 50px;">
							<div class="block-content">
								<div class="block-head">
									<h4>高级选项</h4>
								</div>
								<div class="block-body">
									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="scope">弱口令扫描</label>
										</div>
										<div class="col-md-9">
											<div class="form-group" style="float:left;">
												<label class="c-input c-input-checkbox" for="weaker_ciphers_use">
													<input  class="c-checkbox" type="radio" name="weaker_ciphers" id="weaker_ciphers_use" checked=""  value="1" />
													<span class="c-icon-checked"></span>
													启用
												</label>
											</div>
											<div class="form-group" style="float:left;">
												<label class="c-input c-input-checkbox" for="weaker_ciphers_close">
													<input  class="c-checkbox" type="radio" name="weaker_ciphers" id="weaker_ciphers_close"  value="0" />
													<span class="c-icon-checked"></span>
													关闭
												</label>
											</div>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="scope">认证类型</label>
										</div>
										<div class="col-md-9">
											<div class="form-group" style="float:left;">
												<label class="c-input c-input-checkbox" for="auth_user">
													<input  class="c-checkbox" type="radio" name="authentication_type" id="auth_user" checked="" onclick="$('#loginmethod').hide();$('#usermethod').show();" value="auth" />
													<span class="c-icon-checked"></span>
													BASE认证
												</label>
											</div>
											<div class="form-group" style="float:left;">
												<label class="c-input c-input-checkbox" for="auth_login">
													<input  class="c-checkbox" type="radio" name="authentication_type" id="auth_login" onclick="$('#loginmethod').show();$('#usermethod').hide();" value="cookie" />
													<span class="c-icon-checked"></span>
													COOIKE认证
												</label>
											</div>
										</div>
									</div>
									<div class="form-group row" id="usermethod">
										<div class="col-md-3">
											<label class="inputlabel" for="scope">BASE认证</label>
										</div>
										<div class="col-md-9">
											<div class="form-group">
												<label class="c-input c-input-checkbox" for="auth_user">
													用户名:<input name="authusername"  type="text" class="input" style="width:85%;" placeholder="请输入用户名" />
													密&nbsp;&nbsp;&nbsp;码:<input name="authuserpwd"  type="password" class="input" style="width:85%;"  placeholder="请输入密码" />
												</label>
											</div>
										</div>
									</div>
									<div class="form-group row" id="loginmethod">
										<div class="col-md-3">
											<label class="inputlabel" for="scope">COOIKE认证</label>
										</div>
										<div class="col-md-9">
											<div class="form-group">
												<label class="c-input c-input-checkbox" for="auth_user">
													<textarea class="form-control" cols="50" rows="3" placeholder="请输入登录的COOKIE信息" name="authentication_cookie"></textarea>
												</label>
											</div>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="scope">扫描深度</label>
										</div>
										<div class="col-md-9">
											<div class="form-group">
												<label class="c-input c-input-checkbox" for="auth_user">
													<input type="text" style="width:40px;" name="directory_depth_limit" value="5"/> 扫描中的追叙目录层次深度
												</label>
											</div>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="scope">扫描并发</label>
										</div>
										<div class="col-md-9">
											<div class="form-group">
												<label class="c-input c-input-checkbox" for="auth_user">
													<input type="text" style="width:40px;" name="request_concurrency" value="20"/> 并发数越大速度越快服务器压力越大
												</label>
											</div>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="scope">页面消重</label>
										</div>
										<div class="col-md-9">
											<div class="form-group" style="float:left;">
												<label class="c-input c-input-checkbox" for="auto_redundant_1">
													<input  class="c-checkbox" type="radio" name="auto_redundant" id="auto_redundant_1" checked='' value="1" />
													<span class="c-icon-checked"></span>
													消重
												</label>
											</div>
											<div class="form-group" style="float:left;">
												<label class="c-input c-input-checkbox" for="auto_redundant_2">
													<input  class="c-checkbox" type="radio" name="auto_redundant" id="auto_redundant_2" value="0" />
													<span class="c-icon-checked"></span>
													不消重
												</label>
											</div>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="scope">排除链接</label>
										</div>
										<div class="col-md-9">
											<div class="form-group">
												<label class="c-input c-input-checkbox" for="auth_user">
													<textarea class="form-control" cols="50" rows="3" placeholder="请输入不需要扫描的链接,多项则使用|号隔开." name="exclude_pattern"></textarea>
												</label>
											</div>
										</div>
									</div>
										<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="scope">自定义User-Agent</label>
										</div>
										<div class="col-md-9">
											<div class="form-group">
												<label class="c-input c-input-checkbox" for="auth_user">
													<textarea class="form-control" cols="50" rows="3" placeholder="请输入不自定义的浏览器客服户端信息." name="user_agent" >Mozilla/5.0(Windows NT 10.0;Win64;x64) AppleWebKit/537.36(KHTML,like Gecko) Chrome/52.0.2743.82 Safari/537.36</textarea>
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			
		</div>
		<!--主体-->
		<div class="mask" id="mask-overlay">&nbsp;</div>
<script src="/mare/Public/mars/js/lib/jquery/jquery-1.11.0.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/lib/bootstrap/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/lib/scrollreveal/scrollreveal.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/lib/smoothsroll/SmoothScroll.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/script/globalscript.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/js/common.js" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" type="text/css" href="/mare/Public/plugins/jqueryUpload/css/jquery.fileupload.css">
		<script type="text/javascript" src="/mare/Public/plugins/jqueryUpload/js/vendor/jquery.ui.widget.js"></script>
		<script type="text/javascript" src="/mare/Public/plugins/jqueryUpload/js/jquery.fileupload.js"></script>
		<script src="/mare/Public/mars/js/lib/jqueryvalidate/jquery.form.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="/mare/Public/mars/js/lib/jqueryvalidate/jquery.validate.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="/mare/Public/mars/js/lib/jqueryvalidate/messages_zh.min.js" type="text/javascript" charset="utf-8"></script>

	<script type="text/javascript">
	

	
	var successTips = '<div id="action-tips" class="alert fade in alert-success global-tips" role="alert">新建成功，3秒后自动跳转，若没跳转，点击：<a href="<?php echo U('Mare/Task/task_list');?>">这里</a></div>';
	var errorTips = '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">新建失败，请重试</div>';
	var reporterrorTips = '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">新建失败，请不要重复提交</div>';
	var uploadErrorTips = '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">上传失败，请重试</div>';
	var uploadSuccessTips = '<div id="action-tips" class="alert fade in alert-success global-tips" role="alert">上传成功</div>';
		


		var phone = /^1\d{10}$/gi;//检测手机号码是否符合要求
		var username_pass = /^[\w]+\/[\w]+/;


		$("#addtaskform").validate({
			onsubmit:true,// 是否在提交是验证
			onfocusout:false,// 是否在获取焦点时验证
			onkeyup :false,// 是否在敲击键盘时验证
			debug:true,
			rules: {
				test_username_pwd1: true,
				test_username_pwd2: true,
				test_phone: true
			},
			messages:{
				test_username_pwd1: {
					required:"不能为空"
				},
				test_username_pwd2: {
					required:"不能为空"
				},
				test_phone: {
					required:"不能为空"
				},
				targeturl:{
					required:'不能为空',
				}
			},
			
			submitHandler:function(form){
				var getFormData = $("#addtaskform").serialize();
				var url = "<?php echo U('Mare/Task/task_add/testtype/webscan');?>";
	            $.ajax({
	            	type:"post",
					url:url,
					data:getFormData,
					async:true,
					success: function (data) { //表单提交后更新页面显示的数据
						if (data.code == "success") {
						$(".block-wrapper").append(successTips);
							setTimeout(function(){
								$("#action-tips").alert("close");
								window.location.href = "<?php echo U('Mare/Task/task_list');?>";
							},3000)
						}else if(data.code == "false" && data.info != null && data.href == null){
							var errorNewTips = '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">'+data.info+'</div>';
					  		$(".block-wrapper").append(errorNewTips);
				          	setTimeout(function(){
								$("#action-tips").alert("close");
								window.location.href = "<?php echo U('Mare/Task/task_list');?>";
							},6000)
						}else if(data.info == 'reportsubmit'){
							$(".block-wrapper").append(reporterrorTips);
							setTimeout(function(){
								$("#action-tips").alert("close");
							},3000)
						}else{
							$(".block-wrapper").append(errorTips);
							setTimeout(function(){
								$("#action-tips").alert("close");
							},3000)
						}
					},
					error:function(){
						$(".block-wrapper").append(errorTips);
						setTimeout(function(){
							$("#action-tips").alert("close");
						},3000)
					}
				});
	        }  
		});
		$.validator.addMethod("isURLAddress", function(value, element) {
			// var chrnum = /^((https|http|ftp|rtsp|mms)?:\/\/)+[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/;
			// var chrnum = /^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)?((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.[a-zA-Z]{2,4})(\:[0-9]+)?(/[^/][a-zA-Z0-9\.\,\?\'\\/\+&amp;%\$#\=~_\-@]*)*$/;
			var chrnum = /^[a-zA-z]+:\/\/[^\s]*$/;
			return this.optional(element) || (chrnum.test(value));
		}, "请输入类似(http://168.168.168.168)的url地址");
		$.validator.addMethod("chrnum", function(value, element) {
			var chrnum = /^([a-zA-Z0-9]+)$/;
			return this.optional(element) || (chrnum.test(value));
		}, "只能输入数字和字母(字符A-Z, a-z, 0-9)");
		
		var groupNumber = 1;
		
		var increaseInput = function(e){
			e.preventDefault();
			var limit = 10;
			var thisInputGroup = $(this).prev().find(".form-group");
			var thisGroupName = thisInputGroup[0].children[0].name;
			var strMatch = "";
			
			$.each(thisInputGroup,function(i){
				strMatch += $(this).attr("id");
			});
			// console.log(strMatch,321);
			
			var newId = thisGroupName + "-" + groupNumber;
			
			if ( newId.match(strMatch)==null || newId.match(strMatch).index==0 ) {
				newId = newId.substring(0,newId.indexOf('-'));
				var inputObject = '<div class="form-group has-feedback" id="'+ newId +'"><input type="text" class="input" name="'+ newId +'" placeholder="请输入相关文字/代码">'+
								'<span class="deletebtn glyphicon glyphicon-minus form-control-feedback" aria-hidden="true"></span>'+
								'</div>';
				if ( $(this).prev().find(".form-group").length <= 10 ) {
					thisInputGroup = $(this).prev().append(inputObject);
				}
				else{
					alert("添加已经达到上限10个");
				}
				groupNumber++;
			}
			else{
				
				//当ID重复时，需要做的事情
				console.log(String(newId),newId.match(strMatch));
			}	
		}
		
		var delItems = function(e){
			e.preventDefault();
			$(this).parent().remove();
		}
		
		$(window).ready(function() {
		    $("#addtaskform").validate();
		    if ($("#cust_test").is(":checked")) {
	    		$("#high-test").fadeIn();
	    	}
	    	if ($("#auth_user").is(":checked")) {
		    	$('#loginmethod').hide();$('#usermethod').show();
		    }
		    if ($("#auth_login").is(":checked")) {
		    	$('#loginmethod').show();$('#usermethod').hide();
		    }
		    $("#cust_test").on("change",function(){
		    	
		    	if ($("#cust_test").is(":checked")) {
		    		$("#high-test").fadeIn();
		    	}
		    	else{
		    		$("#high-test").fadeOut();
		    	}
		    });
		    
		    $(".container-fluid").on("change","select[name=test-items]",function(){
		    	var selectType = $("select[name=test-items]").val();
		    	if ( selectType==1 ) {
		    		$("#option1").show();
		    		$("#option1").siblings().hide();
		    	}
		    	else
		    	if( selectType==2 ){
		    		$("#option2").show();
		    		$("#option2").siblings().hide();
		    	}
		    	else 
		    	if( selectType==3 ){
		    		$("#option3").show();
		    		$("#option3").siblings().hide();
		    	}
		    	else 
		    	if( selectType==4 ){
		    		$("#option4").show();
		    		$("#option4").siblings().hide();
		    	}
		    	else 
		    	if( selectType==5 ){
		    		$("#option5").show();
		    		$("#option5").siblings().hide();
		    	}
		    	
		    })
		    
		    $(".btn-increase").on("click",increaseInput);
		    $(".container-fluid").on("click",".deletebtn",delItems);
		    // $("#addnew").on("click",submitForm);
		});
	</script>
	</body>
</html>