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
		<style type="text/css">
		
		</style>
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
					<div class="col-md-6 col-md-offset-3">
						<div class="block-content" id="output-report">
							<div class="block-head">
								<h4>检测报告导出</h4>
							</div>
							<div class="block-body">
								<form class="form-inline" action="<?php echo U('Mare/Report/rep_down');?>" id="downloadreport" method="post">
									<div class="clearfix row-bottom">
										<div class="col-md-3">
											<div class="form-group">
												<p>选择任务</p>
											</div>
										</div>
										<div class="col-md-9 form-group-input">
											<!-- <div class="col-xs-12">
												<div class="form-group">
													<div class="radio-inline radio">
														<input type="radio" name="task" id="normaltask" checked=""/>
													</div>
													<label for="normaltask">普通扫描任务</label>
												</div>
												<div class="form-group">
													<div class="radio-inline radio">
														<input type="radio" name="task" id="webtask"/>
													</div>
													<label for="webtask">Web应用扫描任务</label>
												</div>
												<div class="form-group">
													<div class="radio-inline radio">
														<input type="radio" name="task" id="iptask"/>
													</div>
													<label for="iptask">IP</label>
												</div>
											</div> -->
											
											<div class="form-group">
												<div class="input-group">
													<select class="mutipleselect" type="text" id="appid"  name="appid" style="width: 100%;" />
														<option value="">请选择检测完成的任务</option>
														<?php if(is_array($tasklist)): $i = 0; $__LIST__ = $tasklist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vtl): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vtl['appid']); ?>"><?php echo explode('_',$vtl['task_name'])[0]; ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
													</select>
												</div>
												<span id="ftips" style="position: absolute;bottom: -25px;left: 15px;color:red;display: none;">请先选择任务</span>
											</div>
										</div>
									</div>
									
									<div class="clearfix row-bottom">
										<div class="col-md-3">
											<div class="form-group">
												<p>报告格式</p>
											</div>
										</div>
										<div class="col-md-9 form-group-input">
											<div class="form-group">
												<label class="c-input c-input-radio" for="normaltask1">
													<input class="c-radio" type="radio" name="task" id="normaltask1" checked="" value="pdf"/>
													<span class="c-icon-radio"></span>
													PDF
												</label>
											</div>
											<div class="form-group">
												<label class="c-input c-input-radio" for="webtask1">
													<input class="c-radio" type="radio" name="task" id="webtask1" value="word" />
													<span class="c-icon-radio"></span>
													WORD
												</label>
											</div>
											<div class="form-group">
												<label class="c-input c-input-radio" for="htmltask">
													<input class="c-radio" type="radio" name="task" id="htmltask" value="html"/>
													<span class="c-icon-radio"></span>
													HTML
												</label>
											</div>
										</div>
										<div class="clearfix"></div>

										<div class="col-md-3">
											<div class="form-group">
												<p>报告类型</p>
											</div>
										</div>
										<div class="col-md-9 form-group-input">
											<div class="form-group">
												<label class="c-input c-input-radio" for="normaltask">
													<input class="c-radio" type="radio" name="reporttype" id="normaltask" <?php if(C("IS_PROPOSAL")== 0): ?>checked<?php endif; ?>  value="manage"/>
													<span class="c-icon-radio"></span>
													管理报告
												</label>
											</div>
											<div class="form-group">
												<label class="c-input c-input-radio" for="webtask">
													<input class="c-radio" type="radio" name="reporttype" id="webtask" value="techreport" />
													<span class="c-icon-radio"></span>
													技术报告
												</label>
											</div>
											<div class="form-group" style="display: <?php if(C("IS_PROPOSAL")== 1): ?>line-block<?php else: ?>none<?php endif; ?>">
												<label class="c-input c-input-radio" for="proposal">
													<input class="c-radio" type="radio" name="reporttype" id="proposal" <?php if(C("IS_PROPOSAL")== 1): ?>checked<?php endif; ?>  value="proposal" />
													<span class="c-icon-radio"></span>
													通报报告
												</label>
											</div>
										</div>
										<div class="clearfix"></div>
										
										<!-- <div class="col-md-3">
											<div class="form-group">
												<p>报表标题</p>
											</div>
										</div>
										<div class="col-md-9 form-group-input">
											<div class="form-group">
												<div class="input-group input-inline">
													<input class="input" type="text" name="reporttitle" id="reporttitle" value="" placeholder="报表标题" />
												</div>
											</div>
										</div>
										
										<div class="clearfix"></div> -->
										
										
										<!-- <div class="col-md-3">
											<div class="form-group">
												<p>报表模板</p>
											</div>
										</div>
										<div class="col-md-9 form-group-input">
											<div class="form-group">
												<div class="radio-inline radio">
													<input type="radio" name="model" id="fengxian" value="" checked="" value="risk" />
												</div>
												<label for="fengxian">风险模板</label>
											</div>
											<div class="form-group">
												<div class="radio-inline radio">
													<input type="radio" name="model" id="hegui" value="compliance" />
												</div>
												<label for="hegui">合规模板</label>
											</div>
										</div> -->
										
										<div class="clearfix"></div>
										
										<div class="col-md-3">
											<div class="form-group">
												<p>报告内容</p>
											</div>
										</div>
										<div class="col-md-9 form-group-input">
											<!-- <div class="form-group">
												<div class="checkbox-inline checkbox">
													<input type="checkbox" name="loudong" id="loudong" value="" />
												</div>
												<label for="loudong">漏洞</label>
											</div> -->
											<div class="form-group">
												<label class="c-input c-input-checkbox" for="gaofengxian">
													<input  class="c-checkbox" type="checkbox" name="riskgrade1" id="gaofengxian" checked value="4"  />
													<span class="c-icon-checked"></span>
													高风险
												</label>
											</div>
											<div class="form-group">
												<label class="c-input c-input-checkbox" for="zhongfengxian">
													<input class="c-checkbox" type="checkbox" name="riskgrade2" id="zhongfengxian" checked value="3" />
													<span class="c-icon-checked"></span>
													中风险
												</label>
											</div>
											<div class="form-group">
												<label class="c-input c-input-checkbox" for="difengxian">
													<input class="c-checkbox" type="checkbox" checked name="riskgrade3" id="difengxian" value="2" />
													<span class="c-icon-checked"></span>
													低风险
												</label>
											</div>
											<div class="form-group">
												<label class="c-input c-input-checkbox" for="safeitem">
													<input class="c-checkbox" type="checkbox" name="riskgrade4" id="safeitem" value="1" />
													<span class="c-icon-checked"></span>
													通过
												</label>
											</div>
										</div>	
									</div>
									<div class="form-group" style="margin-top: 15px;">
										<button type="button" onclick="downloadreport()" class="btn btn-blue"> 导出检测报告 </button>
									</div>
								</form>
							</div>
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
	<script type="text/javascript">
		var successTips = '<div id="action-tips" class="alert fade in alert-success global-tips" role="alert">下载成功，3秒后自动跳转，若没跳转，点击：<a href="<?php echo U('Mare/Report/rep_index');?>">这里</a></div>';
		var errorTips = '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">下载失败，请选择任务之后再点击下载</div>';
		var uploadErrorTips = '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">上传失败，请重试</div>';
		var uploadSuccessTips = '<div id="action-tips" class="alert fade in alert-success global-tips" role="alert">上传成功</div>';
		
		function downloadreport(){
			// console.log($('select[name=appid]').val() == '');return false;
			if($('select[name=appid]').val() == ''){
				$("#ftips").show();
				//$(".block-wrapper").append(errorTips);
				setTimeout(function(){
					$("#action-tips").alert("close");
				},3000)
				return false;
			}else{
				$("#ftips").hide();
				$('#downloadreport').submit();
			}
		}
		
		$("#appid").on("change",function(){
			if($('select[name=appid]').val() == ''){
				$("#ftips").show();
				return false;
			}else{
				$("#ftips").hide();
			}
			
		});
		
	</script>
	</body>
</html>