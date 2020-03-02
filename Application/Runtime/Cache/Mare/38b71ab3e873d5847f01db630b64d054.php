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
					<div class="col-sm-12">
						<div class="block-content">
							<div class="block-head">
								<h4>自定义扫描规则 <span class="tool-icon plus" id="add_rule" data-toggle="modal" data-target="#rule_modal"><i class="fa fa-plus"></i> 新增</span></h4>
							</div>
							<div class="block-body" style="">
								<table class="table">
									<thead>
										<tr>
											<th>编号</th>
											<th>规则名称</th>
											<th>类型</th>
											<th>规则内容</th>
											<th>备注</th>
											<th>状态</th>
											<th>操作</th>
										</tr>
									</thead>
									<tbody>
                                        <?php if(is_array($rulelist)): foreach($rulelist as $key=>$v): ?><tr>
											<td><?php echo ($v['id']); ?></td>
											<td><?php echo ($v["rulesname"]); ?></td>
											<td>
											<?php foreach($ruletypelist as $valueruletype){ if($v['ruletype'] == $valueruletype['key']){ echo $valueruletype['value']; break; } } ?>
											</td>
											<td>
											<?php if(strlen($v['rulesinfo']) > 20){ echo substr($v['rulesinfo'],0,15).'...'; }else{ echo $v['rulesinfo']; } ?>
											</td>
											<td><?php echo ($v["remarks"]); ?></td>
											<td>
											<?php if($v['status'] == 1){ echo "启用"; }else{ echo '禁用'; } ?>
											</td>
											<td>
												<a id="check_the_rule" data-toggle="modal" url="<?php echo U('Mare/Setting/set_lookrule',array('id'=>$v['id']));?>" data-target="#check_rule" onclick="getRuleInfo($(this).attr('url'));" class="btn-sm btn-blue-border" data-toggle="tooltip" data-placement="top" title="编辑">
													<i class="fa fa-pencil" aria-hidden="true"></i>
												</a>
												<?php if($v['status'] == 1): ?><a  onclick="stoptask('<?php echo ($v[id]); ?>')" class="btn-sm btn-blue-border" data-toggle="tooltip" data-placement="top" title="停用">
													<i class="fa fa-stop" aria-hidden="true"></i>
												</a>
												<?php else: ?>
												<a  onclick="stoptask('<?php echo ($v[id]); ?>')" class="btn-sm btn-blue-border" data-toggle="tooltip" data-placement="top" title="启用">
													<i class="fa fa-play" aria-hidden="true"></i>
												</a><?php endif; ?> 
												<?php if($_SESSION[$_SESSION['randomstr']]['tid'] == 5): ?><a  onclick="deletetask('<?php echo ($v[id]); ?>')" class="btn-sm btn-red-border" data-toggle="tooltip" data-placement="top" title="删除">
													<i class="fa fa-trash-o"></i>
												</a><?php endif; ?>

											</td>
										</tr><?php endforeach; endif; ?>
									</tbody>
								</table>
								<div style="text-align: center;"><?php echo ($pageshow); ?></div>
							</div>
						</div>					
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			
		</div>
		<!--主体-->
		
		<!-- Modal -->
		<div class="modal fade" id="rule_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		        <h4 class="modal-title" id="myModalLabel">添加新规则详情</h4>
		      </div>
		      <div class="modal-body">
		        <form action="#" class="form-vertical" method="post">
		        	<div class="form-group row">
						<div class="col-md-3">
							<label class="inputlabel" for="test_phone">类型 <font color="red">*</font> </label>
						</div>
						<div class="col-md-9">
							<select class="form-control" name="ruletype">
								<option value="">请选择规则类型</option>
								<?php if(is_array($ruletypelist)): $i = 0; $__LIST__ = $ruletypelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ruletypeid): $mod = ($i % 2 );++$i;?><option value="<?php echo ($ruletypeid['key']); ?>"><?php echo ($ruletypeid['value']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
							</select>
						</div>
					</div>
					
					<div class="form-group row">
						<div class="col-md-3">
							<label class="inputlabel" for="rulesname">规则名 <font color="red">*</font> </label>
						</div>
						<div class="col-md-9">
							<input class="input" type="text" name="rulesname" id="rulesname" value="" placeholder="规则" required="required" aria-required="true">
						</div>
					</div>
					
					<div class="form-group row">
						<div class="col-md-3">
							<label class="inputlabel" for="rule_content">规则内容 <font color="red">*</font> </label>
						</div>
						<div class="col-md-9">
							<!-- <input class="input" type="text" name="rule_content" id="rule_content" value="" placeholder="abc.png" required="required" aria-required="true"> -->
							<textarea class="form-control" name="rule_content" id="rule_content"  rows="3"></textarea>
						</div>
					</div>
					
					<div class="form-group row">
						<div class="col-md-3">
							<label class="inputlabel" for="rule_remarks">备注 <font color="red">*</font> </label>
						</div>
						<div class="col-md-9">
							<input class="input" type="text" name="rule_remarks" id="rule_remarks" value="" placeholder="这是备注" required="required" aria-required="true">
						</div>
					</div>
		        </form>
		      </div>
		      <div class="modal-footer">
		        <span class="btn btn-blue-border" data-dismiss="modal">关闭</span>
		        <a href="javascript:;" class="btn btn-blue" data-dismiss="modal" id="addnewrule">提交</a>
		      </div>
		    </div>
		  </div>
		</div>
		
		<!-- Modal -->
		<div class="modal fade" id="check_rule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		        <h4 class="modal-title" id="myModalLabel">规则名称</h4>
		      </div>
		      <div class="modal-body">
		        <form action="#" class="form-vertical" method="post">
		        	<div class="form-group row">
						<div class="col-md-3">
							<label class="inputlabel">类型 <font color="red">*</font> </label>
						</div>
						<div class="col-md-9">
							<select class="form-control" id="lookruletypename">
								<option value="">请选择规则类型</option>
								<?php if(is_array($ruletypelist)): $i = 0; $__LIST__ = $ruletypelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ruletypeid): $mod = ($i % 2 );++$i;?><option value="<?php echo ($ruletypeid['key']); ?>"><?php echo ($ruletypeid['value']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
							</select>
							<input type="hidden" id="ruleid"/> 
<!-- 							<input name="ruletype" id="lookruletypename" placeholder="请输入类型名"> -->
						</div>
					</div>
					
					<div class="form-group row">
						<div class="col-md-3">
							<label class="inputlabel">规则名 <font color="red">*</font> </label>
						</div>
						<div class="col-md-9">
							<input class="input" type="text" id="lookrulename" placeholder="请输入规则名称">
						</div>
					</div>
					
					<div class="form-group row">
						<div class="col-md-3">
							<label class="inputlabel">规则内容 <font color="red">*</font> </label>
						</div>
						<div class="col-md-9">
							<!-- <input class="input" type="text" name="rulesinfo" id="lookrulesinfo" placeholder="请输入规则内容文字"> -->
							<textarea class="form-control" id="lookrulesinfo" placeholder="请输入规则内容文字" rows="3"></textarea>
						</div>
					</div>
					
					<div class="form-group row">
						<div class="col-md-3">
							<label class="inputlabel">备注 <font color="red">*</font> </label>
						</div>
						<div class="col-md-9">
							<input class="input" type="text" name="remarks"  id="lookruleremarks"  placeholder="请输入备注">
						</div>
					</div>
		        </form>
		      </div>
		      <div class="modal-footer">
		        <span class="btn btn-blue-border" data-dismiss="modal">关闭</span>
		        <a href="javascript:;" class="btn btn-blue" data-dismiss="modal" id="updateruleinfo">修改</a>
		      </div>
		    </div>
		  </div>
		</div>
		
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
	<script type="text/javascript">
		var errorTips 	= '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">修改失败</div>';
		var successTips = '<div id="action-tips" class="alert fade in alert-success global-tips" role="alert">修改成功</div>';
		function deletetask(id){
			$.ajax({
		        type: "get",
		        url: "<?php echo U('Mare/Setting/globalrule_del');?>",
		        async: true,
		        data: {id:id},
		        dataType: "json",
		        success: function (data1) {
		        	if(data1.code == 'false'){
		        		var actiontip  = '删除失败';
		        	}else{
		        		var actiontip  = '删除成功';
		        	}
		        	var successTips = '<div id="action-tips" class="alert fade in alert-success global-tips" role="alert">'+actiontip+'</div>';
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
		function stoptask(id){
			var r = confirm('确定执行操作吗?');
			if(r){
				$.ajax({
			        type: "get",
			        url: "<?php echo U('Mare/Setting/globalrule_stopOrUser');?>",
			        async: true,
			        data: {id:id},
			        dataType: "json",
			        success: function (data1) {
			        	if(data1.code == 'false'){
			        		var successTips = '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">操作失败</div>';
			        	}else{
			        		var successTips = '<div id="action-tips" class="alert fade in alert-success global-tips" role="alert">操作成功</div>';
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
		function getRuleInfo(url){
			$.ajax({
	        	type:"post",
				url:url,
				async:true,
				success: function (data) { //表单提交后更新页面显示的数据
					if (data.code == "success") {
						$('#ruleid').val(data.info['id']);
						$("#lookruletypename [value="+data.info['ruletype']+"]").attr("selected",true);
						$('#lookrulename').val(data.info['rulesname']);
						$('#lookrulesinfo').val(data.info['rulesinfo']);
						$("#lookruleremarks").val(data.info['remarks']);
					}
				}
			});
		}
		$("#updateruleinfo").on('click',function(){
			//规则类型
			var ruletype 			= $("#lookruletypename").val();
			var rulesname 			= $("#lookrulename").val();
			var rule_content 		= $("#lookrulesinfo").val();
			var rule_remarks 		= $("#lookruleremarks").val();
			var id  				= $("#ruleid").val();
			if(ruletype == ''){
				alert('请选择规则类型');
				return false;
			}
			if(rulesname == ''){
				alert('请填写规则名');
				return false;
			}
			if(ruletype == ''){
				alert('请填写规则内容');
				return false;
			}
			if(id == ''){
				alert('信息错误');
				return false;
			}

			var getFormData = {id:id,ruletype:ruletype,rulesname:rulesname,rule_content:rule_content,rule_remarks:rule_remarks};
			var url = "<?php echo U('Mare/Setting/set_updateRuleInfo');?>";
	        $.ajax({
	        	type:"post",
				url:url,
				data:getFormData,
				async:true,
				success: function (data) { //表单提交后更新页面显示的数据
					var errorTips 	= '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">修改规则失败</div>';
					var successTips = '<div id="action-tips" class="alert fade in alert-success global-tips" role="alert">修改规则成功</div>';
					if (data.code == "success") {
					$(".block-wrapper").append(successTips);
						setTimeout(function(){
							$("#action-tips").alert("close");
							location.reload();
						},3000)
					}else if(data.code == "false" && data.info == 'HAVE_NECESSARY_EMPTY_STR'){
						var errorTipsOther 	= '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">请填写必备参数</div>';
						$(".block-wrapper").append(errorTipsOther);
						setTimeout(function(){
							$("#action-tips").alert("close");
						},3000)
					}else{
						$(".block-wrapper").append(errorTips);
						setTimeout(function(){
							$("#action-tips").alert("close");
						},3000)
					}
				}
			});
		});
		$("#addnewrule").on('click',function(){
			//规则类型
			var ruletype 			= $("select[name=ruletype]").val();
			var rulesname 			= $("input[name=rulesname]").val();
			var rule_content 		= $("textarea[name=rule_content]").val();
			var rule_remarks 		= $("input[name=rule_remarks]").val();
			if(ruletype == ''){
				alert('请选择规则类型');
				return false;
			}
			if(rulesname == ''){
				alert('请填写规则名');
				return false;
			}
			if(ruletype == ''){
				alert('请填写规则内容');
				return false;
			}

			var getFormData = {ruletype:ruletype,rulesname:rulesname,rule_content:rule_content,rule_remarks:rule_remarks};
			var url = "<?php echo U('Mare/Setting/set_addNewRule');?>";
	        $.ajax({
	        	type:"post",
				url:url,
				data:getFormData,
				async:true,
				success: function (data) { //表单提交后更新页面显示的数据
					var errorTips 	= '<div id="action-tips" class="alert fade in alert-danger global-tips" role="alert">添加规则失败</div>';
					var successTips = '<div id="action-tips" class="alert fade in alert-success global-tips" role="alert">添加规则成功</div>';
					if (data.code == "success") {
					$(".block-wrapper").append(successTips);
						setTimeout(function(){
							$("#action-tips").alert("close");
							location.reload();
						},3000)
					}else{
						$(".block-wrapper").append(errorTips);
						setTimeout(function(){
							$("#action-tips").alert("close");
						},3000)
					}
				}
			});
		});
	</script>
	</body>
</html>