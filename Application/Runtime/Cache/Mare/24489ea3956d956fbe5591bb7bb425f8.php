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
							  	<li><a href="<?php echo U('Mare/Task/task_item_summary',array('appid'=>$_GET['appid']));?>"><?php echo explode('_',$taskname)[0]; ?></a></li>
							  	<li class="active">检测项详细结果</li>
							</ol>
						</div>
					</div>

					<div class="col-xs-12">
						<div class="navbar-custom">
							<ul>
								<li>
									<a href="<?php echo U('Mare/Task/task_item_parameter',array('appid'=>$_GET['appid']));?>">任务信息概要</a>
								</li>
								<li>
									<a href="<?php echo U('Mare/Task/task_item_summary',array('appid'=>$_GET['appid']));?>">检测结果汇总</a>
								</li>
								<!-- if($tasktype == 'wx' || $tasktype == 'web'){
								business
								communication
								server
								}elseif($tasktype == 'awvs'){
								server
								}else{
								application
								business
								communication
								server
								} -->
								<?php if($goalAppinfo['tasktype'] == 'android' or $goalAppinfo['tasktype'] == 'ios'): ?><li>
									<a href="<?php echo U('Mare/Task/task_item_application',array('appid'=>$_GET['appid']));?>" >应用代码安全</a>
								</li><?php endif; ?>
								<?php if($goalAppinfo['tasktype'] != 'awvs'): ?><li>
									<a href="<?php echo U('Mare/Task/task_item_business',array('appid'=>$_GET['appid']));?>">业务操作安全</a>
								</li>
								<li>
									<a href="<?php echo U('Mare/Task/task_item_communication',array('appid'=>$_GET['appid']));?>">数据通信安全</a>
								</li><?php endif; ?>
								<li>
									<a href="<?php echo U('Mare/Task/task_item_server',array('appid'=>$_GET['appid']));?>">服务器端安全</a>
								</li>
								<?php if($scanruledata != null): ?><li>
									<a href="<?php echo U('Mare/Task/task_custom_rule',array('appid'=>$_GET['appid']));?>">自定义扫描规则</a>
								</li><?php endif; ?>
								<li style="margin-right: 5%;">
									<a href="<?php echo U('Mare/Task/task_detial',array('appid'=>$_GET['appid']));?>">返回报告摘要</a>
								</li>
							</ul>
						</div>
					</div>

					<div class="col-md-12">
						<div class="block-content">
							<div class="block-head">
								<h3>检测项 << <?php echo ($detecname); ?> >> 的检测结果</h3>
							</div>
							<div class="block-body">

								<?php  $hvshowtrans = array('standard'=>'风险编号','hvdid'=>'HVDID','cnvdid'=>'CNVD','cveid'=>'CVE','cweid'=>'CWE','cvss'=>'CVSS','owaspmobiltop10'=>'OWASP Mobil Top10','owaspwebtop10'=>'OWASP Web Top10','vulriskname'=>'用例名称','vulimpression'=>'影响范围','vulrisklevel'=>'风险等级','hvtype'=>'检测分类','hpoctype'=>'检测方法','vultype'=>'问题分类','vulrisktype'=>'利用方式','vuldescribe'=>'风险描述','vulreferurl'=>'参考URL','vulrepair'=>'修复建议','vulpatch'=>'修复补丁','detection_method'=>'检测方法'); $sss = C('SERVER_TRANS_ZH'); ?>
								<div class="report-wrapper">									
									<!-- 自动检测项检测信息 -->
									<table class="table table-bordered">
										<tbody>
											<volist name="detec_item_detial_info" id="didi">
											
											<!-- 问题名称 -->
											<tr>
												<td class="td1" style="width: 15%;text-align: center;"><?php echo ($hvshowtrans['vulriskname']); ?></td>
												<td><?php echo ($didi['vulriskname']); ?></td>
											</tr>
											
											<!-- 漏洞编号 -->
											<tr>
												<td class="td1" style="width: 15%;text-align: center;"><?php echo ($hvshowtrans['standard']); ?></td>
												<td>
												<?php echo ($didi['standard']); ?>
												</td>
											</tr>
											<!-- 风险描述 -->
											<tr>
												<td class="td1" style="width: 15%;text-align: center;"><?php echo ($hvshowtrans['vuldescribe']); ?></td>
												<td><?php echo str_replace(array("\r","\n","\r\n"),"<br/>",$didi['risk_description']); ?></td>
											</tr>
										
											
											<!-- 检测过程 -->
											<tr>
												<td class="td1" style="width: 15%;text-align: center;">检测过程</td>
												<td>
												<?php if(is_array($didi['detection_process'])){ $output_table=$didi['detection_process_instruction'].' </td></tr></tbody></table><table  class="table table-bordered">
		                                	 			<tr>
					                                	<td width="6%">序号</td>
		                                	 			<td style="width:10%;">域名</td>
					                                	<td style="width:27%;">url</td>
		                                	 			<td style="width:27%;">IP列表</td>
		                                	 			<td style="width:17%;">注册域名商</td>
		                                	 			<td style="width:16%;">DNS解析记录</td>
					                                	</tr>'; $output_table_data=''; foreach ($didi['detection_process'] as $k=>$v){ $output_table_data.='<tr>
															<td width="6%">'.($k+1).'</td>
															<td style="width:10%;">'.$v->host.'</td>
															<td style="width:27%;">'; foreach ($v->url as $k1=>$v1){ $output_table_data.=$v1."<br><br>"; } $output_table_data.='</td>
															<td style="width:27%;">'; foreach ($v->ip_list as $k1=>$v1){ $output_table_data.=$v1."<br>"; } $output_table_data.='</td>
															<td style="width:17%;">'.$v->register.'</td>
															<td style="width:13%;">'; foreach ($v->dns_history as $k1=>$v1){ $output_table_data.=$v1."<br>"; } $output_table_data.='</td></tr>'; } if($didi['detection_process_str']) { $output_table_data.='<tr ><td style="padding:20px;" colspan="6">'.$didi['detection_process_str'].'</td></tr>'; } echo $output_table.$output_table_data.'</table><table  class="table table-bordered">'; }else{ echo str_replace(array("[.]","[img]","[/img]","&lt;bbr&gt;",'&lt;bbr/&gt;'),array("\"","<img",">","<b>","</b>"),str_replace(array("\r","\n","\r\n"),"<br/>",htmlspecialchars($didi['detection_process']))); } ?>
												</td>
											</tr>
											
											
												<!-- 风险等级 -->
											<tr>
												<td class="td1" style="width: 15%;text-align: center;"><?php echo ($hvshowtrans['vulrisklevel']); ?></td>
												<td>
												<?php echo ($didi['risk_level']); ?>
												</td>
											</tr>
											<!-- 修复建议 -->
											<tr>
												<td class="td1" style="width: 15%;text-align: center;"><?php echo ($hvshowtrans['vulrepair']); ?></td>
												<td><?php echo str_replace(array("\r","\rn","\n"),"<br/>",$didi['suggestions']); ?></td>
											</tr>
											
											<!-- 修复补丁 -->
											<!-- <?php if($didi['vulpatch'] != null): ?><tr>
												<td class="td1" style="width: 15%;text-align: center;"><?php echo ($hvshowtrans['vulpatch']); ?></td>
												<td><?php echo ($didi['vulpatch']); ?></td>
											</tr><?php endif; ?> -->
										</tbody>
									</table>
							
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="fancy-img-wrapper">
				<div class="fancy-img-box">
					<img src="/mare/Public/mars/img/bg.jpg" class="img-responsive">
				</div>
				<div class="close-box">
					<i class="fa fa-close fa-4x"></i>
				</div>
			</div>
			<!-- 图片放大窗体 -->

		</div>
		<!--主体-->

		<script src="/mare/Public/mars/js/lib/jquery/jquery-1.11.0.js" type="text/javascript" charset="utf-8"></script>
		<script src="/mare/Public/mars/js/lib/bootstrap/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="/mare/Public/mars/js/lib/scrollreveal/scrollreveal.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="/mare/Public/mars/js/lib/smoothsroll/SmoothScroll.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript">
			function fancyImgBox(e){
				var getImgUrl = $(this).find("img").attr("src");
				$(".fancy-img-wrapper").find(".fancy-img-box img").attr("src",getImgUrl);
				$(".fancy-img-wrapper").fadeIn();
				closeFancyBox();
			}
			function closeFancyBox(){
				$(".close-box").on("click",function(){
					$(".fancy-img-wrapper").fadeOut();
				});
				$(".fancy-img-wrapper").on("click",function(){
					$(".fancy-img-wrapper").fadeOut();
				});
			}
			$(document).ready(function(){
				$(".thumbnail-img-box").find(".thumbnail-img-wrapper").on("click",fancyImgBox);//点击显示大图
			});
		</script>
	</body>
</html>