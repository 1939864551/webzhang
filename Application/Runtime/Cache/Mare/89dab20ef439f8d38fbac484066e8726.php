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
<div class="mask" id="mask-overlay">&nbsp;</div>
<script src="/mare/Public/mars/js/lib/jquery/jquery-1.11.0.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/lib/bootstrap/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/lib/scrollreveal/scrollreveal.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/lib/smoothsroll/SmoothScroll.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/mars/js/script/globalscript.js" type="text/javascript" charset="utf-8"></script>
<script src="/mare/Public/js/common.js" type="text/javascript" charset="utf-8"></script>
<style>
	#get_url li:hover{
		background-color:#4d87ff
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
					<form id="addtaskform" class="form-vertical" action="#" method="post" enctype="multipart/form-data">
						<div class="col-lg-6">
							<div class="block-content">
								<div class="block-head">
									<h4>新建WEB应用被动安全检测任务</h4>
								</div>
								<div class="block-body">
									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="taskname">任务名称 <font 	color="red">*</font> </label>
										</div>
										<div class="col-md-9">
											<input class="input" type="text" name="taskname" id="taskname" value="Web被动检测-<?php echo date('YmdHis');?>" placeholder="输入任务名称" />
										</div>
									</div>

									<div class="form-group row" id='imgupload'>
										<div class="col-md-3">
											<label class="inputlabel" for="uploadfile">扫描目标（须先用真机测试终端登陆） <font 	color="red">*</font> </label>
										</div>
										<div class="col-md-9">
											<input type="hidden" name="addtime" value="<?php echo (session('addtime')); ?>"/>
											<input class="input isURLAddress required" type="text" name="targeturl" id="taskname" value="" placeholder="输入扫描目标地址" />
										</div>
									</div>
									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="tester">测试人员 <font 	color="red">*</font> </label>
										</div>
										<div class="col-md-9">
											<select class="form-control" name="tester">
												<option value="">请选择测试人员</option>
												<!-- <?php if(is_array($testerlist)): $i = 0; $__LIST__ = $testerlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vt): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vt['userid']); ?>"><?php echo ($vt['realname']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?> -->
											</select>
										</div>
									</div>
									
									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="test_phone">接收手机短信手机号码 <font 	color="red">*</font> </label>
										</div>
										<div class="col-md-9">
											<input class="input" type="text" name="test_phone" id="test_phone" value="无" placeholder="测试员手机号" required="required" autocomplete="off"/>
										</div>
									</div>
									
									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="test_username_pwd1">被测应用帐号A/密码 <font 	color="red">*</font> </label>
										</div>
										<div class="col-md-9">
											<input class="input" type="text" name="test_username_pwd1" id="test_username_pwd1" value="无/无" placeholder="被测应用帐号1/密码" required="required" />
										</div>
									</div>
									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="test_username_pwd2">被测应用帐号B/密码 <font 	color="red">*</font> </label>
										</div>
										<div class="col-md-9">
											<input class="input" type="text" name="test_username_pwd2" id="test_username_pwd2" value="无/无" placeholder="被测应用帐号2/密码" required="required"/>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="test_domain">被测服务器域名（多个用|分开）</label>
										</div>
										<div class="col-md-9">
											<input class="input" type="text" name="test_domain" id="test_domain" value="" placeholder="被测服务器域名" autocomplete="off"/>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-md-3">
											<label class="inputlabel" for="test_serverip">被测服务器IP（多个用|分开）</label>
										</div>
										<div class="col-md-9">
											<input class="input" type="text" name="test_serverip" id="test_serverip" value="" placeholder="被测服务器IP" autocomplete="off"/>
										</div>
									</div>


									<div class="form-group row" id="hightestitem">
										<div class="col-md-3">
											<label class="inputlabel" for="cust_test">高级选项</label>
										</div>
										<div class="col-md-9">
											<input class="checkbox" type="checkbox" name="cust_test" id="cust_test" />
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
									<div class="row">
										<div class="col-md-12">
											<div class="clearfix">
												<div class="col-md-3">
													<div class="form-group">
														<label>弱口令扫描</label>
													</div>
												</div>
												<div class="col-md-9 form-group-input form-inline">
													<div class="form-group">
														<label class="c-input c-input-radio" for="isuse1">
															<input class="c-radio" type="radio" name="weaker_ciphers" id="isuse1" value="1">
															<span class="c-icon-radio"></span>
															启用
														</label>
													</div>
													<div class="form-group">
														<label class="c-input c-input-radio" for="isuse2">
															<input class="c-radio" type="radio" name="weaker_ciphers" id="isuse2" checked="" value="0">
															<span class="c-icon-radio"></span>
															关闭
														</label>
													</div>
													<div class="form-group">
														<label class="col-md-30">
															开启后可能会被防火墙或其他安全产品拦截导致测试无法正常进行
														</label>
													</div>
												</div>
												
												<div class="clearfix"></div>
											
												<div class="col-md-3">
													<div class="form-group">
														<label class="inputlabel" for="scan_num">扫描并发</label>
													</div>
												</div>
												
												<div class="col-md-9 form-inline">
													<div class="form-group">
													
														<input class="input" type="number" name="request_concurrency" id="scan_num" value="20" placeholder="输入并发数" required="required" aria-required="true">
														
													</div>
													
													<div class="form-group">
														<label class="col-md-30">
															并发数越大速度越快服务器压力越大
														</label>
													</div>
														
													
												</div>
											</div>
										</div>
									</div>
								</div>
								<!--客户端规则配置选项-->
								<div class="block-head">
    <div class="row">
        <div class="col-md-4"><h4>客户端规则配置</h4></div>
        <div class="col-md-8" style="margin-top: 5px;">
            <input class="checkbox" type="checkbox" name="set_client_rule_action" id="set_client_rule_action"/>
        </div>
    </div>

</div>
<div class="block-body">
    <!--<div class="form-group row" style="margin-top: 15px;">-->
        <!--<div class="col-md-3">-->
            <!--<label class="inputlabel" for="set_client_rule">客户端规则配置选项</label>-->
        <!--</div>-->
        <!--<div class="col-md-9">-->
            <!--<input class="checkbox" type="checkbox" name="set_client_rule_action" id="set_client_rule_action"/>-->
        <!--</div>-->
    <!--</div>-->
    <div id="set_client_rule" style="display: none">
        <?php if(is_array($client_rule_type)): foreach($client_rule_type as $k=>$vo): ?><div class="form-group row">
                <div class="col-md-3">
                    <label class="inputlabel"><?php echo ($vo); ?>：</label>
                </div>

                <div class="col-md-2">
                    <select class="form-control" name="client_rule_info_<?php echo ($k); ?>"
                            id="client_rule_info_<?php echo ($k); ?>">
                        <option value="">请选择规则</option>
                        <?php if(is_array($client_rule_info[$k])): $i = 0; $__LIST__ = $client_rule_info[$k];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($info); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
                <div class="col-md-7">
                    <input class="input" type="text" name="<?php echo ($k); ?>"
                           id="client_rule_detail_<?php echo ($k); ?>"/>
                </div>
            </div>
            <script type="text/javascript">
                $(function () {
                    var k = "<?php echo ($k); ?>";
                    $('select[name=client_rule_info_' + k + ']').change(function () {
                        var value = $(this).find("option:selected").val()
                        $.ajax({
                            type: "post",
                            url: "<?php echo U('Mare/Task/get_client_rule_detail');?>",
                            data: {'id': value},
                            async: true,
                            success: function (data) {
                                if (data.status == "success") {
                                    var id = '#client_rule_detail_' + data.info.rule_type;
                                    $(id).val(data.info.rule_content);
                                }
                            }
                        });
                    });
                    $("#set_client_rule_action").on("change",function(){
                        if ($(this).is(":checked")) {
                            $("#set_client_rule").fadeIn();
                        } else{
                            $("#set_client_rule").fadeOut();
                        }
                    });



                });
            </script><?php endforeach; endif; ?>
    </div>
</div>

								<!--URL匹配配置选项-->
								<div class="block-head">
    <div class="row">
        <div class="col-md-4"><h4>URL匹配配置</h4></div>
        <div class="col-md-8" style="margin-top: 5px;">
            <input class="checkbox" type="checkbox" name="check_url_action" id="check_url_action"/>
        </div>
    </div>

</div>
<div class="block-body">
    <!--<div class="form-group row" style="margin-top: 15px;">-->
        <!--<div class="col-md-3">-->
            <!--<label class="inputlabel" for="check_url">URL匹配配置选项</label>-->
        <!--</div>-->
        <!--<div class="col-md-9">-->
            <!--<input class="checkbox" type="checkbox" name="check_url_action" id="check_url_action"/>-->
        <!--</div>-->
    <!--</div>-->
    <div id="check_url"  style="display: none">
        <?php if(is_array($check_url_info)): foreach($check_url_info as $key_url=>$vo_url): ?><div class="form-group row">
                <div class="col-md-3">
                    <label class="inputlabel"  for=""><?php echo ($vo_url); ?>URL：</label>
                </div>
                <div class="col-md-9">
                    <input class="input" type="text" name="<?php echo ($key_url); ?>" id="<?php echo ($key_url); ?>" value="" autocomplete="off"/>
                </div>
            </div><?php endforeach; endif; ?>
    </div>
    <script type="text/javascript">
        $(function () {
            $("#check_url_action").on("change",function(){
                if ($(this).is(":checked")) {
                    $("#check_url").fadeIn();
                } else{
                    $("#check_url").fadeOut();
                }
            });
        });
    </script>
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
		
		var url = "<?php echo U('Mare/Task/task_upload/testtype/weixin');?>";
		var postdata = null;
		$.ajax({
	        type: "get",
	        url: "<?php echo U('Mare/Task/task_tester');?>",
	        async: true,
	        data: postdata,
	        dataType: "json",
	        success: function (data1) {
	            // data1.replace('\\','');
	            var html ='';
	            for(var i = 0; i < data1.length;i++){
	            	html +="<option value="+data1[i]['userid']+">"+data1[i]['realname']+"</option>";
	            }
	            $('select[name=tester]').html(html);
	        },
	        error: function () {
	            $("#ajax-content").html("暂无数据内容");
	        }
	    });


		

		var phone = /^1\d{10}$/gi;//检测手机号码是否符合要求
		var username_pass = /^[\w]+\/[\w]+/;

		//获取微信的测试人员
		function weixintester(){
			$('#hightestitem').hide();
			$.ajax({
		        type: "get",
		        url: "<?php echo U('Mare/Task/task_tester');?>",
		        async: true,
		        // data: {tester:tester},
		        dataType: "json",
		        success: function (data1) {
		            var html ='';
		            for(var i = 0; i < data1.length;i++){
		            	html +="<option value="+data1[i]['userid']+">"+data1[i]['realname']+"</option>";
		            }
		            $('select[name=tester]').html(html);
		        },
		        error: function () {
		            $("#ajax-content").html("暂无数据内容");
		        }
		    });
		}

		//获取测试应用的测试人员
		function apptester(){
			$('#hightestitem').show();
			var file = $('#filepath').val();
			// var file = '23432.xxx';
			if(file != ''){
				var ext = file.substr(file.length-3);
				if(ext == 'ipa'){
		  			var scanruleext 	= 'ipa';
		  			var tester 			= 'ios';
		  		}else{
		  			var tester 			= 'android';
		  			var scanruleext 	= 'apk';
		  		}
		  		var data = {"tester":tester};
		  		$.ajax({
			        type: "get",
			        url: "<?php echo U('Mare/Task/task_tester');?>",
			        async: true,
			        data: data,
			        dataType: "json",
			        success: function (data1) {
			        	// console.log(data1);
			            // data1.replace('\\','');
			            var html ='';
			            for(var i = 0; i < data1.length;i++){
			            	html +="<option value="+data1[i]['userid']+">"+data1[i]['realname']+"</option>";
			            }
			            $('select[name=tester]').html(html);
			        },
			        error: function () {
			            $("#ajax-content").html("暂无数据内容");
			        }
			    });
			}else{
				$('select[name=tester]').html("<option value='null'>请选择测试人员</option>");
			}
			
			
		}

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
				var url = "<?php echo U('Mare/Task/task_add/testtype/awvs');?>";
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
		    $("#cust_test").on("change",function(){
		    	if ($("#cust_test").is(":checked")) {
		    		$("#high-test").fadeIn();
		    	}
		    	else{
		    		$("#high-test").fadeOut();
		    	}
		    });

            /**
             * 表单输入触发操作
             */


            $("input").click(function () {
                var arr = ['test_phone', 'test_domain', 'test_serverip']; //允许触发表单项
                var check_url_info = '<?php echo ($check_url_keys); ?>';
                if ( $.parseJSON(check_url_info)) {
                    arr = arr.concat($.parseJSON(check_url_info)); //数组合并
                }

                var name = $(this).attr('name');
                var testid = $("select[name=tester]").find("option:selected").val();

                if ($.inArray(name, arr) != -1) {
                    var value = $(this).val();
                    // alert(value);
                    $.ajax({
                        type: "post",
                        url: "<?php echo U('Mare/Task/focus_form_input');?>",
                        data: {'field' : name, 'value' : value, 'testid' : testid},
                        async: true,
                        success: function (data) {
                            if (data.status == "success") {
                                $("select[name=tester]").trigger('change');
                                if (data.info) {
                                    var info = data.info[name];
                                    console.log(info);
                                    //var length = info.length;
                                    if ($("input[name='"+name+"']").next('ul').length > 0) {
                                        // $("input[name='"+name+"']").next('ul').remove();
                                        var html = '';
                                        $.each(info, function (index, value) {
                                            html += "<li style=\"border-bottom: 1px solid #e6e6e6;padding: 2px 4px;\">"+value+"</li>";
                                        })
                                        $("input[name='"+name+"']").next('ul').appendTo(html);
                                    } else {
                                        var html = "<ul id=\"get_url\" style=\"border-radius: 6px;position: absolute;z-index: 10000;width: 96%;overflow: hidden;padding: 10px 0; background: white; border-left:1px solid #ccc;border-right:1px solid #ccc;border-bottom:1px solid #ccc;\">";
                                        $.each(info, function (index, value) {
                                            html += "<li style=\"border-bottom: 1px solid #e6e6e6;padding: 2px 4px;\">"+value+"</li>";
                                        })
                                        html += "</ul>";
                                        $("input[name='"+name+"']").after(html);
                                    }

                                }
                            }
                        }
                    });
                }
            }).blur(function () {
                var flag = false;
                var testid = $("select[name=tester]").find("option:selected").val();
                var prev_name = $("#get_url").prev().attr('name');
                var arr = ['test_phone', 'test_domain', 'test_serverip'];
                var check_url_info = '<?php echo ($check_url_keys); ?>';
                if ( $.parseJSON(check_url_info)) {
                    arr = arr.concat($.parseJSON(check_url_info));
                }
                if ($.inArray(prev_name, arr) != -1 && testid) {
                    $("body").on('click','#get_url li',function(){
                        flag = true;
                        $("#get_url").prev().val($(this).text());
                        $("#get_url").remove();
                    });
                }
                setTimeout(function(){
                    $("#get_url").remove();
                },100)

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