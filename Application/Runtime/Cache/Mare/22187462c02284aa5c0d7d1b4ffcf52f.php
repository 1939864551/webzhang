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
					<div class="col-xs-12">
						<div class="block-content">
							<ol class="breadcrumb">
							  <li><a href="<?php echo U('Mare/Task/task_list');?>">任务列表</a></li>
							  <li class="active"><?php echo explode('_',$taskname)[0]; ?></li>
							</ol>
						</div>
					</div>
				
					<div class="col-md-6">
						<div class="block-content">
							<div class="block-body">
								<div id="main-charts" style="min-height: 400px;"></div>
								<div class="charts" id="chart-1"></div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="block-content">
							<div class="block-body">
								<div id="main-charts2" style="min-height: 400px;"></div>
							</div>
						</div>
					</div>
					
					<div class="clearfix"></div>
					<div class="clearfix items-wrapper">
						<?php if($appinfo['tasktype'] != 'wx' AND $appinfo['tasktype'] != 'web' AND $appinfo['tasktype'] != 'awvs'): ?><div class="col-md-3">
								<a href="<?php echo U('Mare/Task/task_item_application',array('appid'=>$_GET['appid']));?>">
									<div class="block-content">
										<div class="col-sm-6 box-img">
											<div class="svg-wrapper">
												<svg class="center-block" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g class="transform-group"><g transform="scale(0.1953125, 0.1953125)"><path d="M338.592648 6.274917 131.495096 6.274917c-68.619488 0-124.353179 55.665053-124.353179 124.284541l0 207.162577c0 68.6231 55.733691 124.284541 124.353179 124.284541l207.097552 0c68.691738 0 124.280929-55.665053 124.280929-124.284541l0.007225-207.162577C462.880802 61.93997 407.284386 6.274917 338.592648 6.274917zM391.616962 347.967092c0 25.056318-20.089127 45.495858-44.71917 45.495858L123.193565 393.462951c-24.630043 0-44.71917-20.443152-44.71917-45.495858L78.474395 120.318013c0-25.059931 20.013265-45.499471 44.71917-45.499471l223.704226 0c24.630043 0 44.71917 20.360065 44.71917 45.499471L391.616962 347.967092zM338.592648 561.032498 131.426459 561.032498c-68.55085 0-124.284541 55.665053-124.284541 124.280929l0 207.097552c0 68.55085 55.661441 124.277316 124.284541 124.277316l207.090327 0c68.7676 0 124.288154-55.733691 124.288154-124.277316l0-207.097552C462.880802 616.697552 407.284386 561.032498 338.592648 561.032498zM398.278417 905.473788c0 25.674056-20.941678 46.619347-46.622959 46.619347L118.367262 952.093135c-25.677669 0-46.630184-20.94529-46.630184-46.619347l0-233.208721c0-25.757144 20.87304-46.706047 46.630184-46.706047L351.568757 625.559021c25.681281 0 46.630184 20.948903 46.630184 46.706047L398.278417 905.473788 398.278417 905.473788zM896.102956 561.032498l-206.949439 0c-68.69535 0-124.356791 55.665053-124.356791 124.280929l0 207.097552c0 68.688125 55.661441 124.356791 124.356791 124.356791l206.949439 0c68.691738 0 124.277316-55.668666 124.277316-124.356791l0-207.097552C1020.380272 616.697552 964.791082 561.032498 896.102956 561.032498zM955.633387 905.322063c0 25.637931-20.840528 46.565159-46.474847 46.565159l-232.74632 0c-25.814944 0-46.644634-20.927228-46.644634-46.565159l0-232.901658c0-25.724631 20.836915-46.651859 46.644634-46.651859l232.74632 0c25.641544 0 46.474847 20.927228 46.474847 46.651859L955.633387 905.322063zM718.245001 431.607366c20.49734 20.41064 47.341847 30.61596 74.052692 30.61596 26.779482 0 53.501164-10.20532 73.976829-30.61596l123.276653-123.41754c40.89353-40.897142 40.89353-107.201016 0-148.025908l-123.197178-123.48979c-20.486503-20.41064-47.190122-30.619572-73.980442-30.619572-26.714457 0-53.562577 10.208932-74.045467 30.619572l-123.269428 123.41754c-40.83573 40.824892-40.83573 107.056516 0 148.025908L718.245001 431.607366zM636.551866 216.757384l138.503351-138.423876c6.018429-5.928117 12.676272-7.149143 17.3075-7.149143 4.627616 0 11.296296 1.221026 17.3075 7.149143l138.655077 138.506964c5.852254 5.928117 7.156368 12.672659 7.156368 17.300275 0 4.631228-1.304113 11.372158-7.315318 17.303888l-138.496126 138.499739c-6.011204 5.928117-12.679884 7.149143-17.3075 7.149143-4.631228 0-11.451633-1.221026-17.3075-7.149143l-138.503351-138.420264C627.050984 241.943752 627.050984 226.344966 636.551866 216.757384z"  ></path></g></g></svg>
											</div>
										</div>
										<div class="col-sm-6 box-text">
											<h4 class="reset-p text-center">应用代码安全</h4>
											<div class="project-info">
												<?php if(($detecresult['0']['risk_level'] == '1' ) OR ($detecresult['0']['vulrisklevel'] == '安全' AND $detecresult['0']['risk_level'] == null )): ?><span class="tips pass-bg"></span>
			                                    <?php elseif(($detecresult['0']['risk_level'] == '2' ) OR ($detecresult['0']['vulrisklevel'] == '低' AND $detecresult['0']['risk_level'] == null )): ?>
			                                    	<span class="tips normal-bg"></span>
			                                    <?php elseif(($detecresult['0']['risk_level'] == '3' ) OR ($detecresult['0']['vulrisklevel'] == '中' AND $detecresult['0']['risk_level'] == null )): ?>
			                                    	<span class="tips warning-bg"></span>
			                                    <?php elseif(($detecresult['0']['risk_level'] == '4' ) OR ($detecresult['0']['vulrisklevel'] == '高' AND $detecresult['0']['risk_level'] == null )): ?>
			                                    	<span class="tips danger-bg"></span>
			                                    <?php else: ?>
			                                    	<span class="tips unrated-bg"></span><?php endif; ?>
											</div>
										</div>
										<div class="clearfix"></div>
									</div>
								</a>
							</div><?php endif; ?>
						<?php if($appinfo['tasktype'] != 'awvs'): ?><!-- <div class="col-md-3">
							<a href="<?php echo U('Mare/Task/task_item_business',array('appid'=>$_GET['appid']));?>">
								<div class="block-content">
									<div class="col-sm-6 box-img">
										<div class="svg-wrapper">
											<svg class="center-block" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g class="transform-group"><g transform="scale(0.1953125, 0.1953125)"><path d="M919.232 241.344l-239.488 0c-4.672-87.232-73.728-149.248-158.464-149.248S367.552 154.048 362.88 241.344L104.768 241.344C82.24 241.344 64 259.52 64 281.92l0 609.344c0 22.464 18.24 40.64 40.768 40.64l814.528 0c22.528 0 40.768-18.176 40.768-40.64L960.064 539.84 960.064 465.6l0-183.68C960 259.52 941.76 241.344 919.232 241.344L919.232 241.344zM516.928 156.928c54.912 0 88.96 29.312 92.16 84.352L424.896 241.28C428.032 186.24 462.08 156.928 516.928 156.928L516.928 156.928zM885.312 857.984 140.032 857.984 140.032 539.84 398.08 539.84l0 62.976L398.016 602.816l0 53.248 250.048 0L648.064 602.752l-0.064 0L648 539.84l237.312 0L885.312 857.984 885.312 857.984zM456.192 604.8 456.192 539.84l130.176 0L586.368 604.8 456.192 604.8 456.192 604.8zM884.032 456 144 456 144 308.032l740.032 0L884.032 456 884.032 456zM903.872 465.6"  ></path></g></g></svg>
										</div>
									</div>
									<div class="col-sm-6 box-text">
										<h4 class="reset-p text-center">业务操作安全</h4>
										<div class="project-info">
											<?php if(($detecresult['1']['risk_level'] == '1' ) OR ($detecresult['1']['vulrisklevel'] == '安全' AND $detecresult['1']['risk_level'] == null )): ?><span class="tips pass-bg"></span>
		                                    <?php elseif(($detecresult['1']['risk_level'] == '2' ) OR ($detecresult['1']['vulrisklevel'] == '低' AND $detecresult['1']['risk_level'] == null )): ?>
		                                    	<span class="tips normal-bg"></span>
		                                    <?php elseif(($detecresult['1']['risk_level'] == '3' ) OR ($detecresult['1']['vulrisklevel'] == '中' AND $detecresult['1']['risk_level'] == null )): ?>
		                                    	<span class="tips warning-bg"></span>
		                                    <?php elseif(($detecresult['1']['risk_level'] == '4' ) OR ($detecresult['1']['vulrisklevel'] == '高' AND $detecresult['1']['risk_level'] == null )): ?>
		                                    	<span class="tips danger-bg"></span>
		                                    <?php else: ?>
		                                    	<span class="tips unrated-bg"></span><?php endif; ?>
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
							</a>
						</div> -->
					<!-- 	<div class="col-md-3">
							<a href="<?php echo U('Mare/Task/task_item_communication',array('appid'=>$_GET['appid']));?>">
								<div class="block-content">
									<div class="col-sm-6 box-img">
										<div class="svg-wrapper">
											<svg class="center-block" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g class="transform-group"><g transform="scale(0.1953125, 0.1953125)"><path d="M855.831 148.248c0.073 0.039 0.144 0.087 0.216 0.127C858.379 149.556 855.831 148.248 855.831 148.248zM856.047 148.375c-0.561-0.284-1.401-0.711-2.63-1.336-102.483-52.447-218.557-82.044-341.572-82.044-121.908 0-237.078 29.095-338.853 80.633-7.246 3.726-5.033 2.62-5.033 2.62-9.157 4.832-16.609 17.214-16.609 27.581l0 3.023 0 47.715 0.098 438.314c0 51.848 36.443 114.865 81.342 140.838l262.747 151.607c8.961 5.132 23.554 5.132 32.515 0l263.047-151.81c44.898-25.869 81.342-88.989 81.342-140.837L872.441 300.662l0-0.909 0-73.286 0-50.034C872.44 165.55 865.109 153.287 856.047 148.375zM808.013 301.469l0 0.905 0 359.183c0 32.919-23.153 72.985-51.642 89.398l-244.427 141.137L267.32 750.851c-28.489-16.408-51.642-56.474-51.642-89.393L215.678 195.364c89.594-42.987 190.062-67.151 296.167-67.151 106.106 0 206.471 24.061 296.168 67.151L808.013 301.469zM704.863 312.645l-0.004-0.112 0.005-0.236c0.005-0.19 0.01-0.38 0.01-0.571 0-69.102-84.117-123.231-191.5-123.231s-191.5 54.129-191.5 123.23c0 0.2 0.005 0.399 0.011 0.598l0.005 0.208-0.003 0.078c-0.007 0.19-0.013 0.381-0.013 0.573l0 109.695 0.007 0.39c-0.004 0.103-0.007 0.206-0.007 0.31l0 109.695 0.007 0.389c-0.004 0.104-0.007 0.206-0.007 0.31l0 109.695c0 69.101 84.117 123.229 191.5 123.229s191.5-54.129 191.5-123.229L704.874 533.972c0-0.095-0.002-0.188-0.007-0.323l0.007-110.07c0-0.095-0.002-0.189-0.007-0.324l0.007-110.071C704.874 313.003 704.869 312.824 704.863 312.645zM611.307 355.792c-26.04 14.294-60.82 22.166-97.933 22.166s-71.893-7.872-97.933-22.166c-22.897-12.568-36.567-29.041-36.567-44.065s13.67-31.498 36.567-44.066c26.04-14.293 60.82-22.165 97.933-22.165s71.893 7.872 97.933 22.165c22.897 12.568 36.567 29.042 36.567 44.066S634.204 343.224 611.307 355.792zM647.874 401.771l0 241.896c0 15.024-13.67 31.497-36.567 44.064-26.04 14.294-60.819 22.165-97.933 22.165-37.112 0-71.893-7.872-97.933-22.165-22.897-12.568-36.567-29.041-36.567-44.064L378.874 401.771c35.569 22.385 82.981 34.644 134.5 34.644S612.305 424.155 647.874 401.771z"  ></path></g></g></svg>
										</div>
									</div>
									<div class="col-sm-6 box-text">
										<h4 class="reset-p text-center">数据通信安全</h4>
										<div class="project-info">
											<?php if(($detecresult['2']['risk_level'] == '1' ) OR ($detecresult['2']['vulrisklevel'] == '安全' AND $detecresult['2']['risk_level'] == null )): ?><span class="tips pass-bg"></span>
		                                    <?php elseif(($detecresult['2']['risk_level'] == '2' ) OR ($detecresult['2']['vulrisklevel'] == '低' AND $detecresult['2']['risk_level'] == null )): ?>
		                                    	<span class="tips normal-bg"></span>
		                                    <?php elseif(($detecresult['2']['risk_level'] == '3' ) OR ($detecresult['2']['vulrisklevel'] == '中' AND $detecresult['2']['risk_level'] == null )): ?>
		                                    	<span class="tips warning-bg"></span>
		                                    <?php elseif(($detecresult['2']['risk_level'] == '4' ) OR ($detecresult['2']['vulrisklevel'] == '高' AND $detecresult['2']['risk_level'] == null )): ?>
		                                    	<span class="tips danger-bg"></span>
		                                    <?php else: ?>
		                                    	<span class="tips unrated-bg"></span><?php endif; ?>
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
							</a>
						</div> -->
							<div class="col-md-3">
								<a href="<?php echo U('Mare/Task/task_item_business',array('appid'=>$_GET['appid']));?>">
									<div class="block-content">
										<div class="col-sm-6 box-img">
											<div class="svg-wrapper">
												<svg class="center-block" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g class="transform-group"><g transform="scale(0.1953125, 0.1953125)"><path d="M919.232 241.344l-239.488 0c-4.672-87.232-73.728-149.248-158.464-149.248S367.552 154.048 362.88 241.344L104.768 241.344C82.24 241.344 64 259.52 64 281.92l0 609.344c0 22.464 18.24 40.64 40.768 40.64l814.528 0c22.528 0 40.768-18.176 40.768-40.64L960.064 539.84 960.064 465.6l0-183.68C960 259.52 941.76 241.344 919.232 241.344L919.232 241.344zM516.928 156.928c54.912 0 88.96 29.312 92.16 84.352L424.896 241.28C428.032 186.24 462.08 156.928 516.928 156.928L516.928 156.928zM885.312 857.984 140.032 857.984 140.032 539.84 398.08 539.84l0 62.976L398.016 602.816l0 53.248 250.048 0L648.064 602.752l-0.064 0L648 539.84l237.312 0L885.312 857.984 885.312 857.984zM456.192 604.8 456.192 539.84l130.176 0L586.368 604.8 456.192 604.8 456.192 604.8zM884.032 456 144 456 144 308.032l740.032 0L884.032 456 884.032 456zM903.872 465.6"  ></path></g></g></svg>
											</div>
										</div>
										<div class="col-sm-6 box-text">
											<h4 class="reset-p text-center">业务操作安全</h4>
											<div class="project-info">
												<?php if(($detecresult['1']['risk_level'] == '1' ) OR ($detecresult['1']['vulrisklevel'] == '安全' AND $detecresult['1']['risk_level'] == null )): ?><span class="tips pass-bg"></span>
			                                    <?php elseif(($detecresult['1']['risk_level'] == '2' ) OR ($detecresult['1']['vulrisklevel'] == '低' AND $detecresult['1']['risk_level'] == null )): ?>
			                                    	<span class="tips normal-bg"></span>
			                                    <?php elseif(($detecresult['1']['risk_level'] == '3' ) OR ($detecresult['1']['vulrisklevel'] == '中' AND $detecresult['1']['risk_level'] == null )): ?>
			                                    	<span class="tips warning-bg"></span>
			                                    <?php elseif(($detecresult['1']['risk_level'] == '4' ) OR ($detecresult['1']['vulrisklevel'] == '高' AND $detecresult['1']['risk_level'] == null )): ?>
			                                    	<span class="tips danger-bg"></span>
			                                    <?php else: ?>
			                                    	<span class="tips unrated-bg"></span><?php endif; ?>
											</div>
										</div>
										<div class="clearfix"></div>
									</div>
								</a>
							</div>
							<div class="col-md-3">
								<a href="<?php echo U('Mare/Task/task_item_communication',array('appid'=>$_GET['appid']));?>">
									<div class="block-content">
										<div class="col-sm-6 box-img">
											<div class="svg-wrapper">
												<svg class="center-block" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g class="transform-group"><g transform="scale(0.1953125, 0.1953125)"><path d="M855.831 148.248c0.073 0.039 0.144 0.087 0.216 0.127C858.379 149.556 855.831 148.248 855.831 148.248zM856.047 148.375c-0.561-0.284-1.401-0.711-2.63-1.336-102.483-52.447-218.557-82.044-341.572-82.044-121.908 0-237.078 29.095-338.853 80.633-7.246 3.726-5.033 2.62-5.033 2.62-9.157 4.832-16.609 17.214-16.609 27.581l0 3.023 0 47.715 0.098 438.314c0 51.848 36.443 114.865 81.342 140.838l262.747 151.607c8.961 5.132 23.554 5.132 32.515 0l263.047-151.81c44.898-25.869 81.342-88.989 81.342-140.837L872.441 300.662l0-0.909 0-73.286 0-50.034C872.44 165.55 865.109 153.287 856.047 148.375zM808.013 301.469l0 0.905 0 359.183c0 32.919-23.153 72.985-51.642 89.398l-244.427 141.137L267.32 750.851c-28.489-16.408-51.642-56.474-51.642-89.393L215.678 195.364c89.594-42.987 190.062-67.151 296.167-67.151 106.106 0 206.471 24.061 296.168 67.151L808.013 301.469zM704.863 312.645l-0.004-0.112 0.005-0.236c0.005-0.19 0.01-0.38 0.01-0.571 0-69.102-84.117-123.231-191.5-123.231s-191.5 54.129-191.5 123.23c0 0.2 0.005 0.399 0.011 0.598l0.005 0.208-0.003 0.078c-0.007 0.19-0.013 0.381-0.013 0.573l0 109.695 0.007 0.39c-0.004 0.103-0.007 0.206-0.007 0.31l0 109.695 0.007 0.389c-0.004 0.104-0.007 0.206-0.007 0.31l0 109.695c0 69.101 84.117 123.229 191.5 123.229s191.5-54.129 191.5-123.229L704.874 533.972c0-0.095-0.002-0.188-0.007-0.323l0.007-110.07c0-0.095-0.002-0.189-0.007-0.324l0.007-110.071C704.874 313.003 704.869 312.824 704.863 312.645zM611.307 355.792c-26.04 14.294-60.82 22.166-97.933 22.166s-71.893-7.872-97.933-22.166c-22.897-12.568-36.567-29.041-36.567-44.065s13.67-31.498 36.567-44.066c26.04-14.293 60.82-22.165 97.933-22.165s71.893 7.872 97.933 22.165c22.897 12.568 36.567 29.042 36.567 44.066S634.204 343.224 611.307 355.792zM647.874 401.771l0 241.896c0 15.024-13.67 31.497-36.567 44.064-26.04 14.294-60.819 22.165-97.933 22.165-37.112 0-71.893-7.872-97.933-22.165-22.897-12.568-36.567-29.041-36.567-44.064L378.874 401.771c35.569 22.385 82.981 34.644 134.5 34.644S612.305 424.155 647.874 401.771z"  ></path></g></g></svg>
											</div>
										</div>
										<div class="col-sm-6 box-text">
											<h4 class="reset-p text-center">数据通信安全</h4>
											<div class="project-info">
												<?php if(($detecresult['2']['risk_level'] == '1' ) OR ($detecresult['2']['vulrisklevel'] == '安全' AND $detecresult['2']['risk_level'] == null )): ?><span class="tips pass-bg"></span>
			                                    <?php elseif(($detecresult['2']['risk_level'] == '2' ) OR ($detecresult['2']['vulrisklevel'] == '低' AND $detecresult['2']['risk_level'] == null )): ?>
			                                    	<span class="tips normal-bg"></span>
			                                    <?php elseif(($detecresult['2']['risk_level'] == '3' ) OR ($detecresult['2']['vulrisklevel'] == '中' AND $detecresult['2']['risk_level'] == null )): ?>
			                                    	<span class="tips warning-bg"></span>
			                                    <?php elseif(($detecresult['2']['risk_level'] == '4' ) OR ($detecresult['2']['vulrisklevel'] == '高' AND $detecresult['2']['risk_level'] == null )): ?>
			                                    	<span class="tips danger-bg"></span>
			                                    <?php else: ?>
			                                    	<span class="tips unrated-bg"></span><?php endif; ?>
											</div>
										</div>
										<div class="clearfix"></div>
									</div>
								</a>
							</div><?php endif; ?>
						<?php if(getSetMode() != 2):?>
						<div class="col-md-3">
							<a href="<?php echo U('Mare/Task/task_item_server',array('appid'=>$_GET['appid']));?>">
								<div class="block-content">
									<div class="col-sm-6 box-img">
										<div class="svg-wrapper">
											<svg class="center-block" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g class="transform-group"><g transform="scale(0.1953125, 0.1953125)"><path d="M870.2976 751.3088l-130.048 119.8592c0 0-32.0512-30.0544-121.344-119.8592-137.3184-153.9584-105.5744-246.5792-105.5744-246.5792l20.1216-221.5424 206.848-77.568 215.5008 77.568c0 0 4.3008 87.6032 15.104 221.5424C981.6576 638.6688 870.2976 751.3088 870.2976 751.3088zM912.6912 322.4576l-172.3904-65.6384-165.4784 65.6384-18.5856 190.3616c0 0-22.8864 75.4688 86.9888 205.7728 71.424 75.9808 100.5568 101.376 100.5568 101.376l88.576-102.3488c0 0 100.6592-91.4432 92.0576-204.8S912.6912 322.4576 912.6912 322.4576zM760.6784 641.4848l-46.0288 0 0-102.4-92.0576 0 0-51.2 92.0576 0 0-102.4 46.0288 0 0 102.4 92.0576 0 0 51.2-92.0576 0L760.6784 641.4848zM188.0576 154.4192l0 51.2L142.0288 205.6192l0-51.2L188.0576 154.4192zM280.1152 154.4192l0 51.2L234.0864 205.6192l0-51.2L280.1152 154.4192zM372.1216 154.4192l0 51.2L326.0928 205.6192l0-51.2L372.1216 154.4192zM418.1504 154.4192l46.0288 0 0 51.2L418.1504 205.6192 418.1504 154.4192zM786.2784 103.2192l-39.6288 0-0.3072-0.1024-0.3072 0.1024L96 103.2192l0 153.6L446.464 256.8192l-4.7104 51.2L96 308.0192c-35.6864 0-46.0288-1.28-46.0288-51.2l0-153.6c0-34.0992 1.024-51.2 46.0288-51.2l690.2784 0c42.1376 0 46.0288 8.3456 46.0288 51.2l0 31.1808-46.0288-16.7424L786.2784 103.2192zM188.0576 461.6192l0 51.2L142.0288 512.8192l0-51.2L188.0576 461.6192zM280.1152 461.6192l0 51.2L234.0864 512.8192l0-51.2L280.1152 461.6192zM372.1216 461.6192l0 51.2L326.0928 512.8192l0-51.2L372.1216 461.6192zM96 359.2192l341.0432 0-4.7104 51.2L96 410.4192l0 153.6 322.4064 0c0.6144 14.08 2.8672 31.1808 7.7312 51.2L96 615.2192c-35.6864 0-46.0288-1.28-46.0288-51.2l0-153.6C50.0224 376.32 50.9952 359.2192 96 359.2192zM188.0576 768.8192l0 51.2L142.0288 820.0192l0-51.2L188.0576 768.8192zM280.1152 768.8192l0 51.2L234.0864 820.0192l0-51.2L280.1152 768.8192zM372.1216 768.8192l0 51.2L326.0928 820.0192l0-51.2L372.1216 768.8192zM464.1792 768.8192l0 51.2L418.1504 820.0192l0-51.2L464.1792 768.8192zM96 666.4192l346.9824 0c6.4512 15.9744 14.336 32.9216 24.3712 51.2L96 717.6192l0 153.6 486.912 0c19.3536 19.1488 36.7104 36.096 52.3776 51.2L96 922.4192c-35.6864 0-46.0288-1.1776-46.0288-51.2l0-149.6064C50.0224 687.5648 50.9952 666.4192 96 666.4192z"  ></path></g></g></svg>
										</div>
									</div>
									<div class="col-sm-6 box-text">
										<h4 class="reset-p text-center">服务器端安全</h4>
										<div class="project-info">
										 	<?php if(($detecresult['3']['risk_level'] == '1' ) OR ($detecresult['3']['vulrisklevel'] == '安全' AND $detecresult['3']['risk_level'] == null )): ?><span class="tips pass-bg"></span>
		                                    <?php elseif(($detecresult['3']['risk_level'] == '2' ) OR ($detecresult['3']['vulrisklevel'] == '低' AND $detecresult['3']['risk_level'] == null )): ?>
			                                    <span class="tips normal-bg"></span>
		                                    <?php elseif(($detecresult['3']['risk_level'] == '3' ) OR ($detecresult['3']['vulrisklevel'] == '中' AND $detecresult['3']['risk_level'] == null )): ?>
			                                    <span class="tips warning-bg"></span>
		                                    <?php elseif(($detecresult['3']['risk_level'] == '4' ) OR ($detecresult['3']['vulrisklevel'] == '高' AND $detecresult['3']['risk_level'] == null )): ?>
			                                    <span class="tips danger-bg"></span>
		                                    <?php else: ?>
			                                    <span class="tips unrated-bg"></span><?php endif; ?>
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					<?php endif;?>
					</div>
					
				</div>
			</div>
			
		</div>

		<!--主体-->
		
	<script src="/mare/Public/mars/js/lib/jquery/jquery-1.11.0.js" type="text/javascript" charset="utf-8"></script>
	<script src="/mare/Public/mars/js/lib/bootstrap/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="/mare/Public/mars/js/lib/scrollreveal/scrollreveal.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="/mare/Public/mars/js/lib/smoothsroll/SmoothScroll.js" type="text/javascript" charset="utf-8"></script>
	<script src="/mare/Public/mars/js/lib/echarts/dist/echarts-all.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript">

	var option,option2;
		$(window).ready(function(){
			var data;
    		$.ajax({
		        type: "post",
		        url: "/mare/index.php/Mare/Task/statisticalChart/appid/<?php echo ($_GET['appid']); ?>",
		        async: true,
		        success: function (data) {
		        	option = {
                        title: {
                            text: data.appname + ' 风险分布',
                            x: 'center'
                        },
                        tooltip: {
                            trigger: 'item',
                            formatter: "{a} <br/>{b} : {c} ({d}%)"
                        },
                        legend: {
                            orient: 'vertical',
                            x: 'left',
                            data: [ '高危', '中危', '低危']
                        },
                        calculable: false,
                        series: [
                            {
                                name: '安全等级',
                                type: 'pie',
                                radius: '55%',
                                center: ['50%', 225],
                                itemStyle: {
                                    normal: {
                                    	label:{
							              	show:true,
							                formatter:'{b} : {c} ({d}%)'
							            },
                                        color: function (params) {
                                            var colorList = [
                                                '#F44336', '#FF9800', '#4fca81'
                                            ];
                                            return colorList[params.dataIndex]
                                        }
                                    },
                                    emphasis: {
                                    	
                                    }
                                },
                                // data:[
                                // 	{value:40,name:'未评定'},
                                //     {value:335, name:'高危'},
                                //     {value:310, name:'中危'},
                                //     {value:1548, name:'低危'},
                                //     {value:234, name:'通过'}

                                // ]
                                data: data.circularchar
                               
                            }
                        ]
                    };

                    console.log(data,"123");
                    option2 = {
                        title: {
                            text: '风险比例',
                            x: 'center',
                            y: 30
                        },
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {
                                type: 'shadow'
                            }
                        },
                        legend: {
                            data: [ '高危', '中危', '低危']
                        },
                        toolbox: {
                            show: true,
                            orient: 'vertical',
                            y: 'center',
                            feature: {
                                mark: {show: false},
                                magicType: {show: true, type: ['line', 'bar', 'tiled']},
                                restore: {show: false},
                                saveAsImage: {show: false}
                            }
                        },
                        calculable: false,
                        xAxis: [
                            {
                                type: 'category',
                                // data : ['周一','周二','周三','周四','周五','周六','周日']
                                data: data.columnChar.dataname
                            }
                        ],
                        yAxis: [
                            {
                                type: 'value',
                                splitArea: {show: true}
                            }
                        ],
                        grid: {
                            x2: 40
                        },
                        series: [
                       
                            {
                                name: '高危',
                                type: 'bar',
                                stack: '总量',
                                itemStyle: {
                                    normal: {
                                        color: '#F44336',
                                        label : {
					                        show: true, 
					                        position: 'insideTop',
					                        formatter:labelShow
					                    }
                                    },
                                    emphasis: {
                                    	label : {
					                        show: true, 
					                        position: 'insideTop',
					                        formatter:labelShow
					                    }
                                    }
                                },
                                // data:[320, 332, 301, 334, 390, 330, 320]
                                data: data.columnChar.high
                            },
                            {
                                name: '中危',
                                type: 'bar',
                                stack: '总量',
                                itemStyle: {
                                    normal: {
                                        color: '#FF9800',
                                        label : {
					                        show: true, 
					                        position: 'insideTop',
					                        formatter:labelShow
					                    }
                                    },
                                    emphasis: {
                                    	label : {
					                        show: true, 
					                        position: 'insideTop',
					                        formatter:labelShow
					                    }
                                    }
                                },
                                // data:[120, 132, 101, 134, 90, 230, 210]
                                data: data.columnChar.mid
                            },
                            {
                                name: '低危',
                                type: 'bar',
                                stack: '总量',
                                itemStyle: {
                                    normal: {
                                        color: '#4fca81',
                                        label : {
					                        show: true, 
					                        position: 'insideTop',
					                        formatter:labelShow
					                    }
                                    },
                                    emphasis: {
                                    	label : {
					                        show: true, 
					                        position: 'insideTop',
					                        formatter: labelShow
					                    }
                                    }
                                },                            
                                // data:[820, 932, 901, 934, 1290, 1330, 1320]
                                data: data.columnChar.low

                            }
                        ]
                    };

            var ecConfig = echarts.config;
			var myChart = echarts.init(document.getElementById('main-charts'));
			var myChart2 = echarts.init(document.getElementById('main-charts2'));

			myChart.setOption(option);
			myChart2.setOption(option2);
			
			myChart.connect(myChart2);
			myChart2.connect(myChart);

			// var myChartdata = myChart.getOption().series[0].data;
			// var tipList = "<ul>";
			// var countNum=0;
			// $.each(myChartdata, function(i) {
			// 	countNum += Number(this.value);
			// 	tipList += "<li>"+this.name+"数："+this.value+"</li>";
			// });
			
			// tipList = tipList+ "<li>漏洞总数："+countNum+"</li></ul>";
			// $("#chart-1").html(tipList);
			// //点击图片查看应用详细
			// $('#chart-1').click(function(){
			// 	location.href = "<?php echo U('Mare/Task/task_item_summary',array('appid'=>$_GET['appid']));?>";
			// });
			function labelShow(data){
				if (data.data==0) {
            		return false;
            	}	
            	else{
            		return data.data;
            	}	
			}
			
//			function thisEle(e){
//				var partname;
//				// console.log(data.urladdr);ddd
//              if (e.dataIndex == '0') {
//                  // partname = 'application';
//                  partname = data.urladdr[0];
//              } else if (e.dataIndex == '1') {
//                  // partname = 'business';
//                  partname = data.urladdr[1];
//              } else if (e.dataIndex == '2') {
//                  // partname = 'communication';
//                  partname = data.urladdr[2];
//              } else if (e.dataIndex == '3') {
//                  // partname = 'server';
//                  partname = data.urladdr[3];
//              }
//              if (partname == null) {
//                  // partname = 'application';
//                  partname = data.urladdr[0];
//              }
//				window.location = "/mare/index.php/Mare/Task/"+partname+"/appid/<?php echo ($_GET['appid']); ?>.html";
//			}
//			myChart2.on(ecConfig.EVENT.CLICK, thisEle);
			
			setTimeout(function (){
			    window.onresize = function () {
			        myChart.resize();
			        myChart2.resize();
			    }
			},200)

		        }
		    });
			
			
				
		});
	</script>
	</body>
</html>