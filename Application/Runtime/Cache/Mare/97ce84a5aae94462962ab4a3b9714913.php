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
					<div class="col-md-4 col-sm-4">
						
						<div class="block-content">
							<div class="block-head" style="position: relative;">
								<h4>网络配置 <!--<a href="javascript:;" onclick="$('#tipspic').toggle();"><i id="tipsicon" data-toggle="tooltip" data-placement="right" title="Tooltip on right" class="fa fa-question-circle"></i></a>--> </h4>
									<!--<div id="tipspic" style="position:absolute;top:18px;left:150px;text-align: center;display: none;"><span>必须手动设置网络IP地址</span></div>--> 
							<!-- </div>
							<div class="block-head"> -->
							</div>
							<div class="block-body">
								<form class="form-horizontal" id="networkset" action="<?php echo U('Mare/Setting/set_network');?>" method="post">
									<div class="form-group">
										<div class="col-md-6">
											<p style="font-weight: bold;">当前连接方式： <span id="connect-type">获取中</span></p>
										</div>
										<div class="col-md-6">
											<p style="font-weight: bold;">当前连接IP地址： <span id="connect-ip">获取中</span></p>
										</div>
									</div>
									<hr />
									<div class="form-group">
										<div class="col-md-6">
											<label class="c-input c-input-radio" for="cable"  onclick="requestNetInfo('cable');$('#wifimodule').hide();">
												<input class="c-radio" type="radio" id="cable" name="connect" style="margin-left: 30px;" <?php if( $currentnet == 'eth0'){ echo "checked='checked'"; } ?>>
												<span class="c-icon-radio"></span>
												有线连接
											</label>
										</div>
										<?php if(strpos($systeminfo['wifi_online'],'1') !== false): ?>
										<div class="col-md-6">
											<label class="c-input c-input-radio" for="wifi" onclick="requestNetInfo('wifi');$('#wifimodule').show();">
												<input type="radio" id="wifi" class="c-radio" name="connect"  value="wifi" <?php if($currentnet == 'wlan0'): ?>checked<?php endif; ?> >
												<span class="c-icon-radio"></span>
												WiFi连接
											</label>
										</div>
										<?php endif; ?>
									</div>
									<hr />
									<div class="form-group">
										<div class="col-md-6">
											<label class="c-input c-input-radio" for="auto-get">
												<input type="radio" id="auto-get" class="c-radio" name="setting" value="auto" <?php if($isauto == 'auto'): ?>checked<?php endif; ?>>
												<span class="c-icon-radio"></span>
												DHCP获取IP地址
											</label>
										</div>
										<div class="col-md-6">
											<label class="c-input c-input-radio" for="handle">
												<input type="radio" id="handle" class="c-radio" name="setting" value="hand" <?php if($isauto == 'hand'): ?>checked<?php endif; ?>>
												<span class="c-icon-radio"></span>
												手工配置IP地址
											</label>
										</div>
									</div>
									<div id="ip-group" style="display: none;">
										<input type="hidden" name="cable_wifi" >
										<div class="form-group">
											<div class="col-md-4">
												<label class="inputlabel" for="ipv4address">IPV4 地址</label>
											</div>
											<div class="col-md-8">
												<input class="input" type="text" name="ipv4address" id="ipv4address" value="" placeholder="类似 192.168.199.44" />
											</div>
										</div>
										<div class="form-group">
											<div class="col-md-4">
												<label class="inputlabel" for="ipv4mask">IPV4 子网掩码</label>
											</div>
											<div class="col-md-8">
												<input class="input" type="text" name="ipv4mask" id="ipv4netmask" value="" placeholder="类似 255.255.255.0"/>
											</div>
										</div>
										<div class="form-group">
											<div class="col-md-4">
												<label class="inputlabel" for="ipv4gateway">IPV4 默认网关</label>
											</div>
											<div class="col-md-8">
												<input class="input" type="text" name="ipv4gateway" id="ipv4gateway" value="" placeholder="类似 192.168.199.1"/>
											</div>
										</div>
										<div class="form-group">
											<div class="col-md-4">
												<label class="inputlabel" for="ipv4server1">IPV4 DNS服务器</label>
											</div>
											<div class="col-md-8">
												<input class="input" type="text" name="ipv4server1" id="ipv4server1" value="" placeholder="类似 114.114.114.114"/>
											</div>
										</div>
										<!--
										<div class="form-group">
											<div class="col-md-4">
												<label class="inputlabel" for="ipv4server2">IPV4 DNS服务器二</label>
											</div>
											<div class="col-md-8">
												<input class="input" type="text" name="ipv4server2" id="ipv4server2" value="" placeholder="类似 114.114.114.114" />
											</div>
										</div>
										-->
									</div>
									<div class="form-group" style="text-align: right;">
										<div class="col-md-12">
										<!--<button class="btn btn-blue btn-shadow" type="reset" >重置</button>-->
											<button class="btn btn-blue btn-shadow push-right" type="button" onclick="$('input[name=cable_wifi]').val($('input[name=connect]:checked').val());$('#networkset').submit();">保存&应用</button>
										</div>
									</div>
								</form>
							</div>
						</div>
						
						<div class="block-content">
							<div class="block-head" style="position: relative;">
								<h4>网络Ping检测</h4>
							</div>
							<div class="block-body">
								<div class="form-horizontal">
									<div class="form-group">
										<div class="col-md-9">
											<input class="input" type="text" id="pingval" value="" placeholder="输入IP/域名地址 例如:192.168.1.1" />
										</div>
										<div class="col-md-3">
											
											<button class="btn btn-blue btn-shadow push-right" onclick="clickPing($('#pingval').val())" >PING</button>
										</div>
									</div>
									<div class="form-group">
										<div class="col-md-12" id="pinginfo">
											
										</div>
									</div>
									
								</div>
							</div>
						</div>
						
					</div>
					
					<div class="col-md-8 col-sm-8" id='wifimodule' 
					<?php if($currentnet == 'wlan0'){ echo 'style="display:block;"'; }elseif($currentnet == 'eth0'){ echo 'style="display:none;"'; }else{ echo 'style="display:none;"'; } ?>
					>
						<div class="block-content">
							<div class="block-head">
								<h4>连接WiFi</h4>
							</div>
							<div class="block-body">
								<div class="row">
									<div class="col-xs-12">
										<div class="row">
											<div class="col-md-8">
												<h5>当前连接：<span id="wifiname">无</span></h5>
												<span id="success-tips">已连接</span>
											</div>
											<div class="col-md-4">
												<h5>连接状态：<span id="connect_static"><i class="fa fa-close danger-color"></i></span></h5>
											</div>
											
										</div>

										<div class="form-inline" style="margin-bottom: 30px;">
										  <div class="form-group">
										    <div class="input-group">
										      <input class="input" type="text" name="" id="wifipwd" placeholder="请输入Wi-Fi 密码">
										      <div class="input-group-addon btn-blue" style="display: table-cell;" id="submit-wifi">连接</div>
										    </div>
										  </div>
										</div>
										
										<h5 style="position: relative;">Wi-Fi列表：<span class="tool-icon refresh"><i class="fa fa-refresh"></i></span></h5>
										<table class="table table-hover wifi-group" id="wifirefesh">
											<thead>
												<tr>
													<th>wifi名</th>
													<th>信号强度</th>
													<th>加密方式</th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>					
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			<input type="hidden" id="tip" value="<?php echo ($_GET['tip']); ?>"/>
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
		
		var isauto ='<?php echo $isauto;?>';
		
		if(isauto=='hand'){
			$("#ip-group").show();
		}
		
		function initIP(){
			$.ajax({
				type:"post",
				url:"<?php echo U('Mare/Setting/get_ip');?>",
				async:true,
				dataType:"json",
				success:function(data){
					$("#connect-type").text(data.methd);
					$("#connect-ip").text(data.ip);
				},
				error:function(){
					
				}
			});
		}
		
		var tip = $('#tip').val();
		if(tip){
			var info='';
			if(tip == 'no_choice_wlan0_eth0'){
				info = '没有选择网线连接或者无线连接';
			}
			if(tip == 'ipv4maskerror'){
				info = '子网掩码格式错误';
			}
			if(tip == 'ipv4server2error'){
				info = 'DNS服务器二格式错误';
			}
			if(tip == 'ipv4server1error'){
				info = 'DNS服务器一格式错误';
			}
			if(tip == 'ipv4gatewayerror'){
				info = '默认网关格式错误';
			}
			if(tip == 'ipv4addresserror'){
				info = 'IP地址格式错误';
			}
			var errorTips = '<div id="action-tips" class="alert fade in alert-success global-tips" role="alert">'+info+'</div>';
			$(".block-wrapper").append(errorTips);
			setTimeout(function(){
				$("#action-tips").alert("close");
			},3000)
			
		}
		
		var loadWifi = function(e){
			var h4text = "<h4 class='text-center'>正在读取可用wifi列表 <i class='fa fa-spinner fa-spin'></i></h4>";
			$('#wifirefesh').find("tbody").html(h4text);
			$.ajax({
				type:"get",
				url:"<?php echo U('Mare/Setting/wifilist');?>",
				async:true,
				success:function(data){
					//console.log(data);
					if (data[0].SSID != "") {
						var wifihtml = "";
						for(var i =0;i < data.length; i++){
							wifihtml += "<tr id="+ data[i]['SSID'] +"><td style='width:30px;'>"+ data[i]['SSID'] +"</td><td style='margin-left:20px;width:10px;'>"+ data[i]['SIGNAL'] +"</td><td style='margin-left:20px;width:30px;'>"+ data[i]['SECURITY'] +"</td></tr>";
						}
						 
						$('#wifirefesh').find("tbody").html(wifihtml);
						// alert("成功读取wifi列表");
					}
				},
				error:function(){
					alert("服务器出错");
				}
			});
		}

		$(".refresh").on("click",loadWifi);
		
		
		//点击切换wifi按钮
		$("input[name=connect]").on("change",function(e){
			var thisKey = $(this).val();
			if ( thisKey=="wifi" ) {
				loadWifi();
				//setTimeoutFn(8000);
			}
			else if ( thisKey=="on" ) {
				stopTimeFn();
			}
		});
		
		var timesOut
		
		var setTimeoutFn = function(time){
			timesOut = setInterval(loadWifi,time);
		}
		
		var stopTimeFn = function(){
			clearInterval(timesOut)
		}
		
		
		$(".wifi-group tbody").on("click","tr",function(e){
			$(this).addClass("active");
			$(this).siblings().removeClass("active");
			$("#success-tips").removeClass("show");
			$("#wifipwd").val("");
		});
		
		var linkTo = function(t){
			var submitID = $(".wifi-group tbody active").attr("id");
			$("#wifiname").text(submitID);
		}
		
		//点击连接 wifi 按钮
		$("#submit-wifi").on("click",function(){
			linkTo();
			var wifiPwd = $("#wifipwd").val();
			var wifiID = $(".wifi-group .active").children("td").eq(0).text();
			console.log("%s",wifiID);
			var wifidata = {};
			wifidata = {"id":wifiID,"pwd":wifiPwd};
			if (wifiID=="undefined") {
				alert("请输入选中要连接的wifi");
				return false;
			}
			else if (wifiPwd == "") {
				alert("请输入wifi密码");
				return false;
			}else if ($("#success-tips").hasClass("show")) {
				alert("当前wifi已在连接状态");
				return false;
			}

			$.ajax({
				type:"post",
				url:"<?php echo U('Mare/Setting/connect_wifi_new');?>",
				async:true,
				data:wifidata,
				success:function(data){
					// $("#success-tips").addClass("show");
					$('#wifiname').html('连接中 <i class="fa fa-spinner fa-spin"></i>');
				}
			});
			
			//3秒后判断是否连接成功网络
			setTimeout(loopExecution,3000);
		});
		
		var loopExecution = setInterval(confirmConnectIsInNet,3000);
		//确认是否联网
		
		var successTip = '<i class="fa fa-check success-color"></i>';
		var failedTip = '<i class="fa fa-close danger-color"></i>';
		
		function confirmConnectIsInNet(){
			$.ajax({
				type:"post",
				//url:"<?php echo U('Mare/Setting/isConnectNetExistFile');?>",
				url: "<?php echo U('Mare/Setting/wifi_status');?>",
				dataType:"json",
				async:true,
				success:function(data){
					//console.log(data,"123");
					if(data.code == 'success'){
						// $("#success-tips").html('联网成功');
						$("#wifiname").html( data.info );
						$("#connect_static").html(successTip);
					}else{
						//clearInterval(loopExecution);
						$("#wifiname").html('未连接');
						$("#connect_static").html(failedTip);
					}
				}
			});
		}
		
		var pingTips = '<span id="ping-tips">PING命令正在执行中  <i class="fa fa-spinner fa-spin"></i></span>';
		function clickPing(val){
			$('#pinginfo').html(pingTips);
			var partern 	= /^[a-zA-Z0-9][\.a-zA-Z0-9]{3,25}$/;
			var ippartern 	= /^[0-9][\.0-9]{0,15}$/;
			if(ippartern.test(val) || partern.test(val)){
				$.ajax({
					type:"post",
					url:"<?php echo U('Mare/Setting/pingtest');?>",
					async:true,
					dataType:"json",
					data:{paramar:val},
					success:function(data){
						$('#pinginfo').html(data);
					},
				});
			}else{
				alert('请输入合适的PING的IP或地址');
			}
		}
		//获取内置的ip地址信息和dns代理信息	
		function requestNetInfo(wifi_cable){
			$.ajax({
				type:"get",
				url:"/mare/index.php/Mare/Setting/requestNetInfo/wifi_cable/"+wifi_cable,
				async:true,
				dataType:"json",
				success:function(data){
					if(data.code == 'success'){
						if(data.wifi_ssid != ''){
							$('#wifiname').html(data.wifi_ssid);
						}else{
							$('#wifiname').html('连接错误,请重新选择wifi');
						}
					}
					$('#ipv4netmask').val(data.netmask);
					$('#ipv4address').val(data.address);
					$('#ipv4gateway').val(data.gateway);
					$('#ipv4server1').val(data.nameserver[0]);
					$('#ipv4server2').val(data.nameserver[1]);
				}
			});
		}
		$(document).ready(function(){
			if($('#cable').length >0 && $('#cable').attr('checked') == 'checked'){
				requestNetInfo('cable');
			}
			if($('#wifi').length >0 && $('#wifi').attr('checked') == 'checked'){
				requestNetInfo('wifi');
			}
			$("input[name=setting]").on("change",function(){
				var activeItem = $(this).val();
				if ( activeItem=="auto" ) {
					$("#ip-group").hide();
				}
				else{
					$("#ip-group").show();
				}
			});
			
			initIP();
			loadWifi();
			
			setInterval(initIP,2000)
		});
	</script>
	</body>
</html>