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
				<div class="guide_title">
					<h3 class="text-center" style="padding-left:50px;">请选择您准备进行安全检测的任务类型</h3>
				</div>

				<div class="clearfix center-block" style="min-height:300px;max-width:850px;">
					<div class="clearfix type_group">
						<div class="col-sm-6">
							<a href="javascript:void(0)" class="nopadding guide_box">
								<div class="guide_img" onclick="task_add('task_add')">
									<img src="/mare/Public/mars/img/iconfont-app.png" class="img-responsive" />
									<div class="guide_text">
										<h4 class="text-center">移动应用安全检测</h4>
									</div>
								</div>
							</a>
						</div>
						<div class="col-sm-6">
							<a href="javascript:void(0)" class="nopadding guide_box">
								<div class="guide_img" onclick="task_add('task_weixin_add')">
									<img src="/mare/Public/mars/img/iconfont-weixin.png" class="img-responsive" />
									<div class="guide_text">
										<h4 class="text-center">微信应用安全检测</h4>
									</div>
								</div>
								
							</a>
						</div>
					</div>
					<div class="clearfix type_group">
						<div class="col-sm-6">
							<a href="javascript:void(0)" class="nopadding guide_box">
								<div class="guide_img" onclick="task_add('task_awvs_add')">
									<img src="/mare/Public/mars/img/iconfont-web-beidong.png" class="img-responsive" />
									<div class="guide_text">
										<h4 class="text-center">Web应用被动安全检测</h4>
									</div>
								</div>
							</a>
						</div>
						
						<div class="col-sm-6">
							<a href="javascript:void(0)" class="nopadding guide_box">
								<div class="guide_img" onclick="task_add('task_web_add')">
									<img src="/mare/Public/mars/img/iconfont-web.png" class="img-responsive" />
									<div class="guide_text">
										<h4 class="text-center">Web应用主动安全检测</h4>
									</div>
								</div>
							</a>
						</div>
					</div>
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
	    <script>
            function task_add(task_type){
                $.ajax({
                    "url":"<?php echo U('check_task_add');?>",
                    "cache":false,
                    "data":{'task_type':task_type},
                    "async":true,
                    "type":'post',
                    "success":function(data){
                        if(data.status == 'success'){
                           var url = "<?php echo U('"+task_type+"');?>";
                           window.location.href = url;
                        } else if (data.status == 'error') {
                            var errorNewTips = '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">'+data.msg+'</div>';
                            $(".block-wrapper").append(errorNewTips);
                            setTimeout(function(){
                                $("#action-tips").alert("close");
                            },6000)                                       
                        }
                    }
                });              
            }
        </script>
	
	</body>
</html>