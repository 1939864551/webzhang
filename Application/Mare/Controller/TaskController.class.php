<?php
namespace Mare\Controller;
use Think\Controller;
class TaskController extends CommonController {
    /**
     * 分析员只能操作自己数据
     * @author qinxuening
     */
    public function __construct(){
        parent::__construct();
        $appid 	   		= intval(I('get.appid'));
        $tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
        if ($tid == "2" && $appid) {
            if(!check_auth_user($appid)) {
                common_alert('无权限操作', U('Task/task_list'));
            }
        }
    }
    
    

    
	//添加任务
	public function task_add(){
        set_time_limit(600);
//        dump(I("get.testtype") );die;
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		if(!check_grants()){//检查授权时间
			$this->redirect("Index/index",array('tip'=>'END_OF_GRANTS'));//差个跳转提醒处理
			exit;
		}

		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/task_add';
		$user 			= D("User");
		$model 			= D("Mare/Appinfo");
		$scanrule 		= D('Scanrule');
		$device 		= D('Device');
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		$userid 		= $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo    = $user->where(array('userid'=>$userid))->find();
		if($tid == "2" || $tid == '5'){
		    //echo kk;die;
			if(IS_POST){

			    if (!check_app_max_count()) {
			        $this->ajaxReturn(array('code'=>'false','info'=>'检测任务不能超过授权次数！'));
			    }

			    //$this->ajaxReturn(array('code'=>'false','info'=>'检测任务不能超过授权次数！'));
				//限制检测应用创建数量
				//$detectNo = $model->where(array('userid'=>$userid,'status'=>array('lt',8)))->count();
				$detectNo = $model->where(array('userid'=>$userid,'status'=>array(array('egt',2),array('lt',8),'AND')))->count();
				if($detectNo >= 8){
					$this->ajaxReturn(array('code'=>'false','info'=>'检测应用数量过多,请先检测完成在添加！'));
				}
				if(I('post.addtime') != $_SESSION['addtime']){
					$this->ajaxReturn(array('code'=>'false','info'=>'reportsubmit'));
				}else{
					session('addtime',null);
				}
				if(I("get.testtype") == 'webscan'){
					if(substr($_SESSION[$_SESSION['randomstr']]['showaction'],2,1) != '1'){
						$this->ajaxReturn(array('code'=>'false','herf'=>U('Mare/Task/task_add')));
					}
					$checks				= array_values(I('post.screeningcondition'));
					foreach ($checks as $ch_key => $ch_value) {
						$scanoption['checks'][]  = D('BackScanrule')->where(array('id'=>$ch_value))->find()['back_rule']; 
					}
					
					$scanoption['scope']['include_scope'] 	= I('post.scope');
					//判断高级选项是否打开
					if(I('post.cust_test') == 'on'){
						//录入用户名和密码
						if(I('post.authentication_type') == 'auth'){
							$authusername 				= I('post.authusername');
							$authuserpwd 				= I('post.authuserpwd');
							$scanoption['http']['authentication_type'] = 'AUTH';
							$scanoption['http']['authentication_info'] = $authusername."/".$authuserpwd;
						}else{
							//获取表头信息
							$scanoption['http']['authentication_type'] = 'COOKIE';
							$scanoption['http']['authentication_info'] = htmlentities(I('post.authentication_cookie'));
						}
						
						$scanoption['weaker_ciphers'] = I('post.weaker_ciphers');

						//扫描深度
						$scanoption['scope']['directory_depth_limit'] 	= I('post.directory_depth_limit');
						//是否自动消重
						$scanoption['scope']['auto_redundant']			=I('post.auto_redundant');
						//排除的链接
						$scanoption['scope']['exclude_pattern']			=I('post.exclude_pattern');
						//扫描并发数
						$scanoption['http']['request_concurrency'] 		= I('post.request_concurrency');
						//用的客户端信息
						$scanoption['http']['user_agent'] 				= I('post.user_agent');
					}

					$data  = array(
						'targeturl'		=> 	I('post.targeturl'),
						'scanoption'	=> 	json_encode($scanoption),
						'tasktype'		=> 	'awvs'
						);
				}elseif(I("get.testtype") == 'awvs'){
					if(substr($_SESSION[$_SESSION['randomstr']]['showaction'],2,1) != '1'){
						$this->ajaxReturn(array('code'=>'false','herf'=>U('Mare/Task/task_add')));
					}
					$data = array(
						'tasktype' 				=> 'web',
						'targeturl'				=> I('post.targeturl'),
						'test_userid'			=> I('post.tester'),
						'test_phone'			=> I('post.test_phone'),
						'test_domain'			=> I('post.test_domain'),
						'test_serverip'			=> I('post.test_serverip')
						);
					//扫描进程和弱口令限制
					if(I('post.cust_test') == 'on'){
						$request_concurrency 	=  I('post.request_concurrency');
						if( $request_concurrency<= 0 ){
							$request_concurrency =20;
						}
						$data['scanoption'] 	= json_encode(array('weaker_ciphers'=>I('post.weaker_ciphers'),'request_concurrency'=>$request_concurrency));
					}
				}else{
					$test_username_pwd 	= I('post.test_username_pwd1').",".I('post.test_username_pwd2');
					if (C('USB_MODE') != 1) {
					    //|| substr(I('post.filepath'),'-3') == 'ipa'
					    $data = array(
					        'test_username_pwd'		=>$test_username_pwd,
					        'test_phone'			=>I('post.test_phone'),
					        'test_domain'			=>I('post.test_domain'),
					        'test_serverip'			=>I('post.test_serverip'),
					        'test_userid'			=>I('post.tester'),
					    );
					}
					if(I("get.testtype") == 'weixin'){
						if(substr($_SESSION[$_SESSION['randomstr']]['showaction'],1,1) != '1'){
							$this->ajaxReturn(array('code'=>'false','herf'=>U('Mare/Task/task_add')));
						}
						$data['icon'] 					= I('post.imgfilepath');
						// $data['status']					= 3;
						$data['tasktype'] 				= 'wx';
						$data['mpid'] 					= md5(createRand(32).time());
					}else{
						if(substr($_SESSION[$_SESSION['randomstr']]['showaction'],0,1) != '1'){
							$this->ajaxReturn(array('code'=>'false','herf'=>U('Mare/Task/task_add')));
						}
						$data['apppath']				=I('post.filepath');
						// $data['repackageappurl']		=I('post.filepath');
						$data['sourcename']				=I('post.sourcename');
						if(substr(I('post.filepath'), -3) == 'ipa'){
							$data['tasktype'] 				= 'ios';
						}else{
							$data['tasktype'] 				= 'android';
						}
					}
					//扫描进程和弱口令限制
					if(I('post.cust_test') == 'on'){
						$request_concurrency 	=  I('post.request_concurrency');
						if( $request_concurrency<= 0 ){
							$request_concurrency =20;
						}
						$data['scanoption'] 	= json_encode(array('weaker_ciphers'=>I('post.weaker_ciphers'),'request_concurrency'=>$request_concurrency));
						if(I("get.testtype") != 'weixin'){
							$data['customrules']  = implode(',', I('post.userule'));
						}
						
					}
				}
				$apptoken 						= 	createRand(25).time().createRand(25);
				$data['userid']					=	$userid;
				$data['task_name']				=	I('post.taskname');
				$data['uploadtime'] 			=	date('Y-m-d H:i:s');
				$data['status']					=	2;
				$data['apptoken']				=	$apptoken;
				
				if (I('internet_security_level')) {
				    $data['internet_security_level'] = I('internet_security_level');
				}
				
				if (I("get.testtype") != 'webscan') {
				    if ($this->add_task_defined_check()) {
				        $data = array_merge($data, $this->add_task_defined_check());
				    }
				}

				foreach ($data as $key => $value) {
					if($key == 'test_domain' || $key == 'test_serverip' || $key == 'customrules'){
						continue;
					}
					if(!$value){
						$this->ajaxReturn(array('code'=>'false','info'=>'表单提交的参数不够','parameter'=>$data,'get'=>I('get.testtype')));
					}
				}
// 				37行已经做限制
				/*--------------任务限制添加量 qinxuening---------------*/
// 				$licinfo = M('licinfo');
// 				$max_task = $licinfo->field('task_sum, max_task')->find();
			
// 				if ($max_task['max_task'] != -1){
//     				if ($max_task['task_sum'] > $max_task['max_task']) {
//     				    $this->ajaxReturn(array('code'=>'false','herf'=>U('Mare/Task/task_select_type')));
//     				}
// 				}
				/*--------------任务限制添加量 qinxuening---------------*/
				
				$appid  = $model->data($data)->add();
				

				if (I("get.testtype") == 'weixin' || I("get.testtype") == 'awvs' || I("get.testtype") == 'webscan') {
				    M('licinfo')->where('id=1')->setInc('task_sum'); //统计任务添加数量
				}
				if($appid){
//                    dump($appid);die;
					//当服务测试类型不是微信的时候
					if(I("get.testtype") != 'weixin'){
						$rule_filetype = substr(I('post.filepath'),'-4');
						if($rule_filetype == '.ipa'){
							$rule_filetype = 'ipa';
						}else{
							$rule_filetype = 'apk';
						}
						//添加文件名检测规则
						$filename_search = 	$_POST['filename_search'];
						foreach ($filename_search as $kf => $vf) {
							if($vf){
								$scanrule->data(array('ruletype'=>'1','scanrule'=>$vf,'userid'=>$userid,'addtime'=>date('Y-m-d H:i:s'),'appid'=>$appid,'apptype'=>$rule_filetype))->add();
							}
						}


						//添加内容规则
						$content_search = 	$_POST['content_search'];
						foreach ($content_search as $kc => $vc) {
							if($vc){
								$scanrule->data(array('ruletype'=>'2','scanrule'=>$vc,'userid'=>$userid,'addtime'=>date('Y-m-d H:i:s'),'appid'=>$appid,'apptype'=>$rule_filetype))->add();
							}
						}

						//添加dex检索规则
						$dex_search = 	$_POST['dex_search'];
						foreach ($dex_search as $kd => $vd) {
							if($vd){
								$scanrule->data(array('ruletype'=>'2','scanrule'=>$vd,'userid'=>$userid,'addtime'=>date('Y-m-d H:i:s'),'appid'=>$appid,'apptype'=>$rule_filetype))->add();
							}
						}

						//添加代码函数/关键字规则
						$code_function_keyword = 	$_POST['code_function_keyword'];
						foreach ($code_function_keyword as $kcfk => $vcfk) {
							if($vcfk){
								$scanrule->data(array('ruletype'=>'3','scanrule'=>$vcfk,'userid'=>$userid,'addtime'=>date('Y-m-d H:i:s'),'appid'=>$appid,'apptype'=>$rule_filetype))->add();
							}
						}


						//添加AndroidManifest.xml规则
						$xml_search = 	$_POST['xml_search'];
						foreach ($xml_search as $kxs => $vxs) {
							if($vxs){
								$scanrule->data(array('ruletype'=>'4','scanrule'=>$vxs,'userid'=>$userid,'addtime'=>date('Y-m-d H:i:s'),'appid'=>$appid,'apptype'=>$rule_filetype))->add();
							}
						}
					}
					if(I("get.testtype") !== 'awvs' && I("get.testtype") !== 'webscan' && I("get.testtype") != 'weixin'){
					    //dump("kk");die;
						//判断是什么文件类型
						//如果文件名后缀是.apk,就执行检测apk的脚本
						$path = getcwd()."/".I('post.filepath');
						$path = str_replace(array('|',';','&'), '', $path);
						$parameter = 1;
						if(substr($path, -4) == '.ipa'){
							system("/Apktest/MobAppSecAss/apptest_ipa_info.sh   {$path} {$appid} {$userid} {$parameter} >/dev/null");
							$this->app_authorise($appid);
							if (C('USB_MODE') == 1) {
// 							    system("/Apktest/MobAppSecAss/start_apptest 'ipa'  {$path} {$appid} {$userid} {$parameter} '' >/dev/null &");
							    system("/Apktest/MobAppSecAss/start_apptest 'ipa'  {$path} {$appid} {$userid} {$parameter} 'localhost' >/dev/null &");
							}else {
							    if (I('post.isdynamic') != 1 && C('ISDYNAMIC_MODE') == 1) {
							        system("/Apktest/MobAppSecAss/start_apptest 'ipastatic'  {$path} {$appid} {$userid} {$parameter} '' >/dev/null &");
							    }
							}

							//ipa不跑USB模式
// 							if (C('USB_MODE') == 1) {
// 							    system("/Apktest/MobAppSecAss/start_apptest 'ipa'  {$path} {$appid} {$userid} {$parameter} 'localhost' >/dev/null &");
// 							}		
						}else{
						    //dump("sd");die;
                                exec("/Apktest/MobAppSecAss/apptest_apk_info.sh   {$path} {$appid} {$userid} {$parameter} >/dev/null");
						    $a =	$this->app_authorise($appid);
						    //dump($a);die;
							if (C('USB_MODE') == 1) {
// 							    myLog("/Apktest/MobAppSecAss/start_apptest 'apk'  {$path} {$appid} {$userid} {$parameter} '' >/dev/null &");
// 							    echo "/Apktest/MobAppSecAss/start_apptest 'apk'  {$path} {$appid} {$userid} {$parameter} '' >/dev/null &";
							    system("/Apktest/MobAppSecAss/start_apptest 'apkusb'  {$path} {$appid} {$userid} {$parameter} '' >/dev/null &");
							} else {
							    if (I('post.isdynamic') != 1 && C('ISDYNAMIC_MODE') == 1) {
							        system("/Apktest/MobAppSecAss/start_apptest 'apkstatic'  {$path} {$appid} {$userid} {$parameter} '' >/dev/null &");
							    }
							}
							
						}
					}
					

					@addMareLog(array(
						'username'      =>$personinfo['realname'],
			            'handleurl'     =>$url, 
			            'handlecontent' =>'添加任务',
			            'handleresult'  =>'成功',
			            'handleremarks' =>'用户添加任务成功'
			        ));
					$this->ajaxReturn(array('code'=>'success','herf'=>U('Mare/Task/task_list')));
					// $this->redirect('Mare/Task/task_list',array('tip'=>'task_add_success'));
				}else{
					@addMareLog(array(
						'username'      =>$personinfo['realname'],
			            'handleurl'     =>$url, 
			            'handlecontent' =>'添加任务',
			            'handleresult'  =>'失败',
			            'handleremarks' =>'用户添加任务失败'
			        ));
					$this->ajaxReturn(array('code'=>'false','herf'=>U('Mare/Task/task_add')));
				}		
			}else{
			    /*--------------任务限制添加量 qinxuening---------------*/
			    $checkt_ask_max = $this->check_task_add();
			    if (!$checkt_ask_max) {
			        $this->redirect('Mare/Task/task_select_type');
			    }
			    /*--------------任务限制添加量 qinxuening---------------*/
			    
				$testerlist = $device->field('at_user.userid,realname')->join('left join at_user on at_user.userid = at_device.userid')->select();
				$this->assign('testerlist',$testerlist);
				session('addtime',mt_rand(1,100000000000000));

				$maintain 		= D("Maintain");
				$ruletypelist	= json_decode($maintain->where(array('key'=>'ruletype'))->find()['value'],1);

				$customrules 	= D("Customrules");

				foreach ($ruletypelist as $key => $value) {
					$ruletypelist[$key]['rulelist'] =  $customrules->where(array('ruletype'=>$value['key'],'status'=>1))->select();
				}

				$this->assign('ruletypelist',$ruletypelist);

				// @addMareLog(array(
					// 'username'      =>$personinfo['realname'],
		            // 'handleurl'     =>$url, 
		            // 'handlecontent' =>'查看添加任务页面',
		            // 'handleresult'  =>'成功',
		            // 'handleremarks' =>'用户查看添加任务页面成功'
		        // ));
		        
		        $this->get_client_rule();
		        $this->get_check_url();
		        
				$this->display();
			}
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//应用上传
	public function task_upload(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$model 			= D("Mare/Appinfo");
		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/task_upload';
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
		if($tid == "2" || $tid == '5'){
			if(IS_POST){
				//限制检测应用创建数量
				//$detectNo = $model->where(array('userid'=>$userid,'status'=>array('lt',8)))->count();
				$detectNo = $model->where(array('userid'=>$userid,'status'=>array(array('egt',2),array('lt',8),'AND')))->count();
				if($detectNo >= 8){
					$this->ajaxReturn(array('code'=>'false','info'=>'检测应用数量过多,请先检测完成在添加！'));
				}
				$data = array();
				if($_FILES){
					$upload = new \Think\Upload();// 实例化上传类 
					$upload->maxSize 	= 10240000000;// 设置附件上传大小 
					if(I('get.testtype') == 'app'){
						$upload->exts = array('apk','ipa','zip');// 设置附件上传类型
					}else{
						$upload->exts = array('jpg','jpeg','png');// 设置附件上传类型
					}
					 
					$upload->rootPath 	= './Uploads/App/'; // 设置附件上传根目录 
					$upload->saveName 	= array('uniqid',''); //唯一名称
					$upload->subName 	= array('date','Ymd');
					$info 				= $upload->upload(); 
					if(!$info) {// 上传错误提示错误信息 
						$this->ajaxReturn(array('code'=>'false','info'=>$upload->getError()));
			            // @addMareLog(array(
			            	// 'username'      =>$personinfo['realname'],
				            // 'handleurl'     =>$url, 
				            // 'handlecontent' =>'上传文件',
				            // 'handleresult'  =>'失败',
				            // 'handleremarks' =>'用户上传文件失败'
				        // ));
					}else{ // 上传成功 获取上传文件信息 
						// @addMareLog(array(
							// 'username'      =>$personinfo['realname'],
				            // 'handleurl'     =>$url, 
				            // 'handlecontent' =>'上传文件',
				            // 'handleresult'  =>'成功',
				            // 'handleremarks' =>'用户上传文件成功'
				        // ));
						$this->ajaxReturn(array('code'=>'success','info'=>$upload->rootPath.date('Ymd')."/".$info['uploadfile']['savename'],'ext'=>$info['uploadfile']['ext'],'sourcename'=>$info['uploadfile']['name']));
					}
				}
			}else{
				// @addMareLog(array(
					// 'username'      =>$personinfo['realname'],
		            // 'handleurl'     =>$url, 
		            // 'handlecontent' =>'上传文件',
		            // 'handleresult'  =>'失败',
		            // 'handleremarks' =>'用户上传文件失败'
		        // ));
				$this->ajaxReturn(array('code'=>'false','info'=>'上传方式不正确'));
			}
		}else{
			// @addMareLog(array(
				// 'username'      =>$personinfo['realname'],
	            // 'handleurl'     =>$url, 
	            // 'handlecontent' =>'上传文件',
	            // 'handleresult'  =>'失败',
	            // 'handleremarks' =>'用户上传文件失败'
	        // ));
			$this->ajaxReturn(array('code'=>'false','info'=>'无权限上传'));
		}
	}

	public function task_select_type(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$this->assign("grants",check_grants());
		if(getSetMode() == 2){
			$this->display('Task:task_add');
		}else{
			$this->display();
		}
	}
	//添加微信任务
	public function task_weixin_add(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
	    /*--------------任务限制添加量 qinxuening---------------*/
	    $checkt_ask_max = $this->check_task_add();
	    if (!$checkt_ask_max) {
	        $this->redirect('Mare/Task/task_select_type');
	    }
	    /*--------------任务限制添加量 qinxuening---------------*/
	    
		//功能授权限制
		if(substr($_SESSION[$_SESSION['randomstr']]['showaction'],1,1) != '1'){
			$this->error('无法新增微信检测，请开通更多功能授权');
		}
		if(!check_grants()){
			$this->redirect("Index/index",array('tip'=>'END_OF_GRANTS'));
			exit;
		}
		$maintain 		= D("Maintain");
		$ruletypelist	= json_decode($maintain->where(array('key'=>'ruletype'))->find()['value'],1);

		$customrules 	= D("Customrules");

		foreach ($ruletypelist as $key => $value) {
			$ruletypelist[$key]['rulelist'] =  $customrules->where(array('ruletype'=>$value['key'],'status'=>1))->select();
		}
		
		$this->get_client_rule();
		$this->get_check_url();
		
		$this->assign('ruletypelist',$ruletypelist);
		$this->display();
	}

	//添加web任务
	public function task_web_add(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
	    /*--------------任务限制添加量 qinxuening---------------*/
	    $checkt_ask_max = $this->check_task_add();
	    if (!$checkt_ask_max) {
	        $this->redirect('Mare/Task/task_select_type');
	    }
	    /*--------------任务限制添加量 qinxuening---------------*/
	    
		//功能授权限制
		if(substr($_SESSION[$_SESSION['randomstr']]['showaction'],2,1) != '1'){
			$this->error('无法新增web被动检测，请开通更多功能授权');
		}
		if(!check_grants()){
			$this->redirect("Index/index",array('tip'=>'END_OF_GRANTS'));
			exit;
		}
		if(substr($_SESSION[$_SESSION['randomstr']]['showaction'],2,1) != '1'){
			$this->redirect('Mare/Task/task_select_type');
		}else{
			$back_scanrule = D('BackScanrule')->where('back_rule_zh is not null')->select();
			$this->assign('back_scanrule',$back_scanrule);
			$this->display();
		}
	}

	//添加主动测试任务
	public function task_awvs_add(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
	    /*--------------任务限制添加量 qinxuening---------------*/
	    $checkt_ask_max = $this->check_task_add();
	    if (!$checkt_ask_max) {
	        $this->redirect('Mare/Task/task_select_type');
	    }
	    /*--------------任务限制添加量 qinxuening---------------*/
	    
		//功能授权限制
		if(substr($_SESSION[$_SESSION['randomstr']]['showaction'],2,1) != '1'){
			$this->error('无法新增web主动检测，请开通更多功能授权');
		}
		if(!check_grants()){
			$this->redirect("Index/index",array('tip'=>'END_OF_GRANTS'));
			exit;
		}
		if(substr($_SESSION[$_SESSION['randomstr']]['showaction'],2,1) != '1'){
			$this->redirect('Mare/Task/task_select_type');
		}else{
			$maintain 		= D("Maintain");
			$ruletypelist	= json_decode($maintain->where(array('key'=>'ruletype'))->find()['value'],1);

			$customrules 	= D("Customrules");

			foreach ($ruletypelist as $key => $value) {
				$ruletypelist[$key]['rulelist'] =  $customrules->where(array('ruletype'=>$value['key'],'status'=>1))->select();
			}
			$this->assign('ruletypelist',$ruletypelist);
			
			$this->get_client_rule();
			$this->get_check_url();
			
			$this->display();
		}
	}



	//任务列表
	public function task_list(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		if(IS_GET){
			
			$condition=array();
			$assign=array();
			
			if(I('starttime')&&I('endtime')){
				
			$startTime=intval(strtotime(I('starttime')));
			$endTime=intval(strtotime(I('endtime')));
			
			$condition['UNIX_TIMESTAMP(uploadtime)'] = array(array('egt',$startTime),array('elt',$endTime),'AND');
			$assign['starttime']=$startTime;
			$assign['endtime']=$endTime;
			}
			
			$type=I('type');$value=trim(I('condition'));
			
			if ($type && $value) {
				
				if ($type == "task") {
					$condition ['at_appinfo.task_name'] = array ('like','%' . $value . '%' );
					$assign['type']=$type;
					$assign['value']=$value;
				} elseif ($type == "member") {
					$condition ['at_user.realname'] = array ('like','%' . $value . '%' );
					$assign['type']=$type;
					$assign['value']=$value;
				} elseif ($type == "app") {
					$condition ['at_appinfo.realname'] = array ('like','%' . $value . '%' );
					$assign['type']=$type;
					$assign['value']=$value;
				}
			}
			
			
			if(I('status')){
				$condition=array();
				$condition ['at_appinfo.status']=array('in',array('3','4','5','6','7','8','9'));
				//dump(I('status'));//die;
			}else{
				$this->assign("condition",$assign);
			}
			
		}
		//dump($condition);die;
		$model 		= D("Mare/Appinfo");
		$maintain  	= D('Maintain');
		$tid 		= $_SESSION[$_SESSION['randomstr']]['tid'];
		$authname 	= $_SESSION[$_SESSION['randomstr']]['authname'];
		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
        if($tid == "2" || $tid == '5'){
            
            //普通测试人员只能看自己添加任务
            if($tid == 2) {
                $condition['at_appinfo.userid'] = $_SESSION[$_SESSION['randomstr']]['userid'];
            }
            
        	$page = ($page == null) ? 0 : (int)I('get.p');
            $countnum = 12;
            $star 			 	= (I('get.p',1)-1)*$countnum;
            $count 			 	= $model->where($condition)->join('left join at_user on at_user.userid = at_appinfo.test_userid')->count();
            
            // 分页
            $p	= new \Think\Page($count,$countnum);
	    	$p->lastSuffix 		=false;
            $p->setConfig('header','<br/><li class="rows">共<b>%TOTAL_ROW%</b>条记录  每页<b>'.$countnum.'</b>条  第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
            $p->setConfig('prev','上一页');
            $p->setConfig('last','末页');
            $p->setConfig('first','首页');
            $p->setConfig('next','下一页');
            $p->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    		$page = $p->show();

	   		$list = $model->where($condition)->field('at_appinfo.appid,at_appinfo.realname as app,at_appinfo.uploadtime,at_appinfo.task_name,at_user.realname,at_appinfo.status,at_appinfo.icon,at_appinfo.type,at_appinfo.task_execute_ip,at_appinfo.tasktype,at_appinfo.dexdump,at_appinfo.dexfile,at_appinfo.task_status,at_appinfo.sensitive')->limit($p->firstRow.','.$p->listRows)->join('left join at_user on at_user.userid = at_appinfo.test_userid')->order('appid desc')->select();
	   		
	   		/***脱壳包下载限制条件**/
	   		foreach ($list as $k => $v) {
	   		    $arr[] = $v['appid'];
	   		}
	   		$where_['detectionid'] = 5;
	   		
	   		if ($arr) {
	   		    $where_['appid'] = ['in', $arr];
	   		}

	   		$vulinfo_info = M('vulinfo')->field('appid,count(*) as count')->where($where_)->group('appid')->select();
	   		
	   		foreach ($vulinfo_info as $k_ => $v_) {
	   		    $arr_[$v_['appid']] = $v_['count'];
	   		}
	   		$this->assign('arr', $arr_);
	   		
	   		
// 	   		echo M('vulinfo')->getLastSql();
	   		//print_r($vulinfo_info);
// 	   		foreach ($list as $k => &$v) {
// 	   		    $v['sensitive'] = json_decode($v['sensitive']);
// 	   		}
// 	   		print_r($list);
	   		$where['at_appinfo.status']=array('in',array('3','4','5','6','7','8','9'));
	   		if($tid == 2) {
	   		    $where['at_appinfo.userid'] = $_SESSION[$_SESSION['randomstr']]['userid'];
	   		}
	   		$unsuccess = $model->where($where)->join('left join at_user on at_user.userid = at_appinfo.test_userid')->select();
	   		//dump(count($unsuccess));die;
	   		//dump($model->getLastSql());
           // dump($list);die;
	   		$this->assign('num',count($unsuccess));
            $this->assign('page',$page);
            $this->assign('star',$star);
            $this->assign('tasklist',$list);
            

            //判断是否开启脱壳包接口
            $isdexdown = 0;
            $dexfilepath = "/Apktest/www/mare/Application/Common/Conf/Opts.conf";
            if (file_exists($dexfilepath)) {//文件是否存在
            	$dexresult = file_get_contents($dexfilepath);
            	if (!empty($dexresult)) {//文件是否为空
            		$dexarr = json_decode($dexresult,true);
            		if (is_array($dexarr)) {//是否为json数组格式
            			if ($dexarr['dexdown']==1) {
            				$isdexdown = 1;
            			}
            		}
            	}
            }
            $this->assign('isdexdown',$isdexdown);
             
            $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/task_list';
			// @addMareLog(array(
				// 'username'		=>$personinfo['realname'],
	            // 'handleurl'     =>$url, 
	            // 'handlecontent' =>'查看任务列表',
	            // 'handleresult'  =>'成功',
	            // 'handleremarks' =>'用户查看任务列表成功'
	        // ));

            //$appstatus  = json_decode($maintain->where(array('key'=>'appteststatus'))->find()['value'],1);
			//var_export($appstatus);die();
            $appstatus  = json_decode($maintain->where(array('key'=>'appteststatus2'))->find()['value'],1);
            $this->assign('appstatus',$appstatus);
			$this->display();
		}else{
			$this->redirect('Mare/Index/index');
		}      
	}


	//删除任务
	public function task_del(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		$url        	= MODULE_NAME.'/'.CONTROLLER_NAME.'/task_del';

		$appid 			= I('get.appid');
		$appinfo 		= D('Appinfo');
		$vulinfo 		= D('Vulinfo');

 		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
		if(($tid == 5 || $tid == 2) && is_numeric($appid) ){
		 	$goalappinfo = $appinfo->field('status')->where(array('appid'=>$appid))->find();

		 	$appStatus   = $goalappinfo['status'];
		 	
			//允许删除的状态
		 	$allowDelStatus = array('-1','-2','11','18');   //2018-7-10 允许状态'-1','-2','11','18'
			if(!in_array($appStatus, $allowDelStatus)){
				$this->ajaxReturn(array('code'=>'false'));
			}

			$appinfo->startTrans();
			$del_app_res 	= $appinfo->where(array('appid'=>$appid))->delete();
			$del_vul_res 	= $vulinfo->where(array('appid'=>$appid))->delete();
			$del_analy_res  = D('AnalysisResults')->where(array('appid'=>$appid))->delete();

			if($del_app_res !== false && $del_vul_res !== false && $del_analy_res !== false){
				$appinfo->commit();
				//删除管理报告
				if(file_exists($goalappinfo['managereportpath'])){
					@unlink($goalappinfo['managereportpath']);
				}
				//删除技术报告
				if(file_exists($goalappinfo['techreportpath'])){
					@unlink($goalappinfo['techreportpath']);
				}
				//删除上传应用
				if(file_exists($goalappinfo['apppath'])){
					@unlink($goalappinfo['apppath']);
				}
				//删除加固后应用
				if(file_exists($goalappinfo['repackageappurl'])){
					@unlink($goalappinfo['repackageappurl']);
				}
				//删除应用图片
				if(file_exists($goalappinfo['icon'])){
					@unlink($goalappinfo['icon']);
				}

				@addMareLog(array(
					'username'		=>$personinfo['realname'],
		            'handleurl'     =>$url, 
		            'handlecontent' =>'删除任务',
		            'handleresult'  =>'成功',
		            'handleremarks' =>'用户删除任务成功'
		        ));
				$this->ajaxReturn(array('code'=>'success'));
			}else{
				$appinfo->rollback();
				@addMareLog(array(
					'username'		=>$personinfo['realname'],
		            'handleurl'     =>$url, 
		            'handlecontent' =>'删除任务',
		            'handleresult'  =>'失败',
		            'handleremarks' =>'用户删除任务失败'
		        ));
				$this->ajaxReturn(array('code'=>'false'));
			}
		}else{
			$this->ajaxReturn(array('code'=>'false'));
			// $this->redirect('Mare/Task/task_list');
		}
	}

	//多项应用删除
	public function task_array_del(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		$url        	= MODULE_NAME.'/'.CONTROLLER_NAME.'/task_del';

		$appid 			= I('get.appidarr');
		$appinfo 		= D('Appinfo');
		$vulinfo 		= D('Vulinfo');
		$appidarr 		= explode(",", $appid);

 		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
		if(($tid == 5 || $tid == 2) && is_array($appidarr) ){
			// $appStatus 	= $appinfo->field('status')->where(array('appid'=>$appid))->find()['status'];
			// //允许删除的状态
			// $allowDelStatus = array('8','9','11');
			// if(!in_array($appStatus, $allowDelStatus)){
			// 	$this->ajaxReturn(array('code'=>'false'));
			// }
			
		    /*******************2018-7-10 允许状态'-1','-2','11','18'*********************/
		    $appidarr = $appinfo->where(array('appid'=>array('in',$appidarr),'status'=>array('in','-1,-2,11,18')))->getField('appid', true);
		    if(!$appidarr) {
		        $this->ajaxReturn(array('code'=>'false'));
		    }
		    /*******************2018-7-10 允许状态'-1','-2','11','18'*********************/
		    
		    
			$appinfo->startTrans();
			$goalappinfoarr  		= array();
			foreach ($appidarr as $key => $value) {
				$goalappinfoarr[$key] 	= $appinfo->where(array('appid'=>$value))->find();
			}

			
			
			$del_app_res 	= $appinfo->where(array('appid'=>array('in',$appid),'status'=>array('in','-1,-2,11,18')))->delete();
			
			$del_vul_res 	= $vulinfo->where(array('appid'=>array('in',$appid)))->delete();
			
			$del_analy_res  = D('AnalysisResults')->where(array('appid'=>array('in',$appid)))->delete();
			
			if($del_app_res !== false && $del_vul_res !== false && $del_analy_res !== false){
				foreach ($goalappinfoarr as $kga => $vga) {					
					//删除管理报告
					if(file_exists($vga['managereportpath'])){
						@unlink($vga['managereportpath']);
					}
					//删除技术报告
					if(file_exists($vga['techreportpath'])){
						@unlink($vga['techreportpath']);
					}
					//删除上传应用
					if(file_exists($vga['apppath'])){
						@unlink($vga['apppath']);
					}
					//删除加固后应用
					if(file_exists($vga['repackageappurl'])){
						@unlink($vga['repackageappurl']);
					}
					//删除应用图片
					if(file_exists($vga['icon'])){
						@unlink($vga['icon']);
					}
				}
				$appinfo->commit();
				@addMareLog(array(
					'username'		=>$personinfo['realname'],
		            'handleurl'     =>$url, 
		            'handlecontent' =>'删除任务',
		            'handleresult'  =>'成功',
		            'handleremarks' =>'用户删除任务成功'
		        ));
				$this->ajaxReturn(array('code'=>'success'));
			}else{
				$appinfo->rollback();
				@addMareLog(array(
					'username'		=>$personinfo['realname'],
		            'handleurl'     =>$url, 
		            'handlecontent' =>'删除任务',
		            'handleresult'  =>'失败',
		            'handleremarks' =>'用户删除任务失败'
		        ));
				$this->ajaxReturn(array('code'=>'false'));
			}
		}else{
			$this->ajaxReturn(array('code'=>'false'));
			// $this->redirect('Mare/Task/task_list');
		}
	}

	//停止任务
	public function task_stop(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
	    $appid 			= (int)I('get.appid');
	    $appinfo 		= D("Appinfo");
	    $where['appid'] = $appid;
	    $where['status'] = ['in',[8,9]]; //8/9at_vulinfo有数据,at_appinfo状态置10，无数据11
	    $list = $appinfo->where($where)->find();
	    if($appinfo->where(array('appid'=>$appid,'status'=>array('lt',8)))->find()){
	        $res = $appinfo->data(array('status'=>10))->where(array('appid'=>$appid))->save();
	        if($res !== false){
	            $this->ajaxReturn(array('code'=>'success','info'=>'应用停止成功!'));
	        }else{
	            $this->ajaxReturn(array('code'=>'false','info'=>'应用停止失败!'));
	        }
	    }else if ($list) {
	        $vulinfo = M('vulinfo');
	        $vulinfo_list = $vulinfo->where(['appid' => $appid])->find();
	        if ($vulinfo_list) {
	            $res = $appinfo->data(array('status'=>10))->where(array('appid'=>$appid))->save();
	        } else {
	            $res = $appinfo->data(array('status'=>11))->where(array('appid'=>$appid))->save();
	        }
	        if($res !== false){
	            $this->ajaxReturn(array('code'=>'success','info'=>'应用停止成功!'));
	        }else{
	            $this->ajaxReturn(array('code'=>'false','info'=>'应用停止失败!'));
	        }
	    }else{
	        $this->ajaxReturn(array('code'=>'false','info'=>'该应用应检测完成，不需要停止!'));
	    }
	}


	private function task_count_item(){

		$exploit_db 						= D("ExploitDb");
		//应用安全类型所属
	    $detectionidlist['application'] 	= $exploit_db->field('id')->where(array('hvtypeid'=>'1'))->select();
	    //业务操作安全
		$detectionidlist['business'] 		= $exploit_db->field('id')->where(array('hvtypeid'=>'2'))->select();
		//数据通信安全
		$detectionidlist['communication'] 	= $exploit_db->field('id')->where(array('hvtypeid'=>'3'))->select();
		//服务端安全
		$detectionidlist['server'] 			= $exploit_db->field('id')->where(array('hvtypeid'=>'4'))->select();

		foreach ($detectionidlist as $kd => $vd) {
			$detecitem[$kd] = array();
			foreach ($vd as $kd1 => $vd2) {
				array_push($detecitem[$kd],$vd2['id']);
			}
		}
		return $detecitem;	
	}

	//任务的统计信息
	public function task_detial(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$model 			= D('Appinfo');
		$appid 			= I('get.appid');
		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/task_item_parameter';
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
		if($tid == "2" || $tid == '5'){
			$appid 	   		= (int)I('get.appid');
	       	//统计应用的高中低威胁,如果某一类中出现一个高，则该类就显示高危的颜色
	       	$exploit_db 	= D("ExploitDb");
	       	$vulinfo 		= D('Vulinfo');
	       	$analysisResults= D('AnalysisResults');

	        $taskname  		= $model->field('task_name')->find($appid)['task_name'];
	        $detecitem 		= $this->task_count_item();
	        $detecresult = array();

	        foreach ($detecitem as $value) {
	        	if(is_array($value)){
	        		$where['detectionid'] 	= array('in',$value);
		            $where['appid'] 		= array('eq',$appid);
		            // $detetesult[] 			= $vulinfo->field('detectionid,vulrisklevel,issues_severity')->where($where)->join('left join at_exploit_db on at_exploit_db.id = at_vulinfo.detectionid')->where("(detectionid in ('92','93','98','89') and statusconfirm = '2') OR (detectionid not in ('92','93','98','89'))")->group('detectionid asc')->order('detectionid asc')->select();
		            $detetesult[] 			= $analysisResults->where($where)->select();
	        	}else{
	        		$detetesult[] 			= array();
	        	}
	        }
	        
	        $zh_to_num 			= array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
	        $num_to_zh			= array('0'=>'暂未评级','1'=>'安全','2'=>'低','3'=>'中','4'=>'高');
	        foreach ($detetesult as $kd => $vd) {
	            //判断数组中的两个字段，当num字段大于其他所有的值是，去掉其他多余数组
	            //当num相等的情况下，我们就判断result字段，result字段大的留下，小的去掉
	            $tmp = null;
	            foreach ($vd as $kv => $vv) {
	                if ($kv == 0) {
	                    $tmp 		= $vv;
	                    if($tmp['risk_level'] == 4){
	                    	break;
	                    }
	                } else {

	                   	if( $tmp['risk_level'] < $vv['risk_level']) {
	                   		$tmp 		= $vv;
	                   		if($tmp['risk_level'] == 4){
		                    	break;
		                    }
	                    }
	                }   
	            }
	            if ($tmp) {
	                $detetesult[$kd] = $tmp;
	            }	            
	        }
			// @addMareLog(array(
				// 'username'		=>$personinfo['realname'],
			    // 'handleurl'     =>$url, 
			    // 'handlecontent' =>'查看任务',
			    // 'handleresult'  =>'成功',
			    // 'handleremarks' =>'用户查看任务成功'
			// ));
            $appinfo 	= $model->find($appid);
            $this->assign('appinfo',$appinfo);
	        $this->assign('taskname',$taskname);
	        $this->assign('detecresult', $detetesult);
			$this->display();
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	
	//应用的统计数据
	public function statisticalChart() {
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
	    $appid 		= (int)I('get.appid');
	    $appinfo 	= D('Appinfo');
	    // $vulinfo 				= D('Vulinfo');
	    $exploit_db 			= D("ExploitDb");
	    $analysisResults 		= D('AnalysisResults');
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "2" || $tid == '5'){
		    $tasktype = $appinfo->find($appid)['tasktype'];
		    if($tasktype == 'wx'){
		    	$detec_item_list		= $analysisResults->field('*,sum(issues_count) as itemcount,detectionid')->where(array('appid'=>$appid,'at_exploit_db.hvtypeid'=>array('in','2,3,4')))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->group('detectionid')->order('detectionid')->select();
		    }else{
		    	$detec_item_list		= $analysisResults->field('*,sum(issues_count) as itemcount')->where(array('appid'=>$appid,'at_exploit_db.hvtypeid'=>array('in','1,2,3,4')))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->group('detectionid')->order('detectionid')->select();
		    }
		    
		  
		    
		    $application = 1;
		    $business = 2;
		    $communication = 3;
		    $server = 4;

		    // $detec_item_list = $this->task_item_common($detec_item_list,$appid);
		    $highcount = $midcount = $lowcount = '0';
		   
		    $higharr = $midarr = $lowarr = array(0,0,0,0);
		    foreach ($detec_item_list as $key => $value) {
		    	// if($value['statusconfirm']  == 2 || $value['statusconfirm']  == null){
			    	if($value['risk_level'] == 4){
			    		$highcount += $value['itemcount'];
			    		if($tasktype == 'wx' || $tasktype == 'web'){
				    		if($value['hvtypeid'] == $business){
				    			$higharr[0] += $value['itemcount'];
				    		}
				    		if($value['hvtypeid'] == $communication){
				    			$higharr[1] += $value['itemcount'];
				    		}
				    		if($value['hvtypeid'] == $server){
				    			$higharr[2] += $value['itemcount'];
				    		}
			    		}elseif($tasktype == 'awvs'){
			    			if($value['hvtypeid'] == $server){
				    			$higharr[0] += $value['itemcount'];
				    		}
			    		}else{
			    			if($value['hvtypeid'] == $application){
				    			$higharr[0] += $value['itemcount'];
				    		}
				    		if($value['hvtypeid'] == $business){
				    			$higharr[1] += $value['itemcount'];
				    		}
				    		if($value['hvtypeid'] == $communication){
				    			$higharr[2] += $value['itemcount'];
				    		}
				    		if($value['hvtypeid'] == $server){
				    			$higharr[3] += $value['itemcount'];
				    		}
			    		}
			    		
			    	}
			    	if($value['risk_level'] == 3){
			    		
			    		$midcount += $value['itemcount'];
			    		if($tasktype == 'wx' || $tasktype == 'web'){
				    		if($value['hvtypeid'] == $business){
				    			$midarr[0] += $value['itemcount'];
				    		}
				    		if($value['hvtypeid'] == $communication){
				    			$midarr[1] += $value['itemcount'];
				    		}
				    		if($value['hvtypeid'] == $server){
				    			$midarr[2] += $value['itemcount'];
				    		}
			    		}elseif($tasktype == 'awvs'){
							if($value['hvtypeid'] == $server){
				    			$midarr[0] += $value['itemcount'];
				    		}
			    		}else{
			    			if($value['hvtypeid'] == $application){
				    			$midarr[0] += $value['itemcount'];
				    		}
				    		if($value['hvtypeid'] == $business){
				    			$midarr[1] += $value['itemcount'];
				    		}
				    		if($value['hvtypeid'] == $communication){
				    			$midarr[2] += $value['itemcount'];
				    		}
				    		if($value['hvtypeid'] == $server){
				    			$midarr[3] += $value['itemcount'];
				    		}
			    		}
				    		
			    	}
			    	if($value['risk_level'] == 2){
			    		// array_push($tlowarr,$value);
			    		$lowcount += $value['itemcount'];
			    		if($tasktype == 'wx' || $tasktype == 'web'){
				    		if($value['hvtypeid'] == $business){
				    			$lowarr[0] += $value['itemcount'];
				    		}
				    		if($value['hvtypeid'] == $communication){
				    			$lowarr[1] += $value['itemcount'];
				    		}
				    		if($value['hvtypeid'] == $server){
				    			$lowarr[2] += $value['itemcount'];
				    		}
			    		}elseif($tasktype == 'awvs'){
							if($value['hvtypeid'] == $server){
								$lowarr[0] += $value['itemcount'];
							}
			    		}else{
			    			if($value['hvtypeid'] == $application){
				    			$lowarr[0] += $value['itemcount'];
				    		}
				    		if($value['hvtypeid'] == $business){
				    			$lowarr[1] += $value['itemcount'];
				    		}
				    		if($value['hvtypeid'] == $communication){
				    			$lowarr[2] += $value['itemcount'];
				    		}
				    		if($value['hvtypeid'] == $server){
				    			$lowarr[3] += $value['itemcount'];
				    		}
			    		}	
			    	}
			    // }
		    }
		

		    $appname = $appinfo->find($appid)['package'];

		    if(!$appname){
		    	$appname ='服务类检测';
		    }
		    
		    if($tasktype == 'wx' || $tasktype == 'web'){
		    	$dataname = array('业务操作安全', '数据通信安全', '服务端安全');
		    	$urladdr  = array('task_item_business','task_item_communication','task_item_server');
		    }elseif($tasktype == 'awvs'){
				$dataname = array('服务端安全');
				$urladdr  = array('task_item_server');
			}else{
		    	$dataname = array('应用安全', '业务操作安全', '数据通信安全', '服务端安全');
		    	$urladdr  = array('task_item_application','task_item_business','task_item_communication','task_item_server');
		    }
		    $key = array_search('服务端安全', $dataname);
			if ($key !== false&&getSetMode()==2)
    		array_splice($dataname, $key, 1);
		    $data = array(
		        'circularchar' => array(
		            // array('name' => '未评定', 'value' => $norating),
		            array('name' => '高危', 'value' => $highcount),
		            array('name' => '中危', 'value' => $midcount),
		            array('name' => '低危', 'value' => $lowcount),
		            // array('name' => '通过', 'value' => $passcount),
		        ),
		        'circularcharlegend'=>array("高危 ".$highcount." 个","中危 ".$midcount." 个","低危 ".$lowcount." 个"),
		        'columnChar' => array(
		            'dataname' => $dataname,
		            'high' => $higharr,
		            'mid' => $midarr,
		            'low' => $lowarr,
		            // 'pass' => $passarr,
		            // 'norating' => $noratingarr
		        ),
		        'appname' => $appname,
		        'urladdr' => $urladdr
		    );
		    $this->ajaxReturn($data);
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//获取任务的详细参数
	public function task_item_parameter(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$model 			= D('Appinfo');
		$maintain 		= D('Maintain');
		$appid 			= (int)I('get.appid');
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
		if($tid == "2" || $tid == '5'){
			$taskname  = $model->field('task_name')->find($appid)['task_name'];
			$taskinfo  = $model->field('at_user.realname as tester_name,at_appinfo.*')->join('left join at_user on at_user.userid = at_appinfo.test_userid')->find($appid);
			$appstatus = json_decode($maintain->where(array('key'=>'appteststatus'))->find()['value'],1);
			$taskinfo['appstatustrans']	= $appstatus[$taskinfo['status']];
			$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/task_item_parameter';
			// @addMareLog(array(
				// 'username'		=>$personinfo['realname'],
			    // 'handleurl'     =>$url, 
			    // 'handlecontent' =>'查看任务参数',
			    // 'handleresult'  =>'成功',
			    // 'handleremarks' =>'用户查看任务参数成功'
			// ));
			$scanrule   	= D("Scanrule");
			$scanruledata 	= $scanrule->where(array('appid'=>$appid))->select();
			$requesttemp 	= D('Requesttemp')->where(array('appid'=>$appid))->select();
			$this->assign('requesttemp',$requesttemp);
			$this->assign('scanruledata',$scanruledata);
			$this->assign('taskinfo',$taskinfo);
			$this->assign('taskname',$taskname);
			$appstatus  = json_decode($maintain->where(array('key'=>'appteststatus1'))->find()['value'],1);
            $this->assign('appstatus',$appstatus);
			$this->display();
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//获取各大检测项汇总的数据
	public function task_item_summary(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$model 			= D('Appinfo');
		$maintain 		= D('Maintain');
		// $appid 			= I('get.appid');
		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/task_item_summary';
		$appid 	   		= (int)I('get.appid');
	       	//统计应用的高中低威胁,如果某一类中出现一个高，则该类就显示高危的颜色
       	$exploit_db 	= D("ExploitDb");
       	// $vulinfo 		= D('Vulinfo');
       	$analysisResults= D('AnalysisResults');
       	$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();

		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "2" || $tid == '5'){
			$taskname  = $model->field('task_name')->find($appid)['task_name'];
			$detectionidlist = $exploit_db->field('id')->where(array('hvtypeid'=>array('in',array('1','2','3','4'))))->select();
			foreach ($detectionidlist as $key => $value) {
				if($key == 0){
					$tmp = $value['id'];
				}else{
					$tmp .= ",".$value['id'];
				}
			}

			$page 				= ($page == null) ? 0 : (int)I('get.p');
            $countnum			= 15;
            $star 			 	= (I('get.p',1)-1)*$countnum;
            $count 				= count($analysisResults->where(array('detectionid'=>array('in',$tmp),'appid' => $appid,'issues_count'=>array('gt',0),'risk_level'=>array('gt',1)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->group('detectionid desc')->select());
            
            

            // 分页
            $p	= new \Think\Page($count,$countnum);
	    	$p->lastSuffix 		=false;
            $p->setConfig('header','<br/><li class="rows">共<b>%TOTAL_ROW%</b>条记录  每页<b>'.$countnum.'</b>条  第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
            $p->setConfig('prev','上一页');
            $p->setConfig('last','末页');
            $p->setConfig('first','首页');
            $p->setConfig('next','下一页');
            $p->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    		$page 				= $p->show();

     
       		$detec_item_list  	= $analysisResults->field('detectionid,sum(issues_count) as issues_count,risk_level,case_name')->where(array('detectionid'=>array('in',$tmp),'appid' => $appid,'issues_count'=>array('gt',0),'risk_level'=>array('gt',1)))->limit($p->firstRow.','.$p->listRows)->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC,detectionid ASC')->group('detectionid asc')->select();
       		$zh_to_num          = array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
	        $num_to_zh          = array('0'=>'暂未评级','1'=>'安全','2'=>'低','3'=>'中','4'=>'高');
	       
	        // $detec_item_list = list_sort_by($detec_item_list, 'issues_severity', $sortby = 'desc');
       		// @addMareLog(array(
       			// 'username'		=>$personinfo['realname'],
			    // 'handleurl'     =>$url, 
			    // 'handlecontent' =>'查看任务汇总信息',
			    // 'handleresult'  =>'成功',
			    // 'handleremarks' =>'用户查看任务汇总信息成功'
			// ));
	        $taskinfo = $model->find($appid);
	        $scanrule   	= D("Scanrule");
			$scanruledata 	= $scanrule->where(array('appid'=>$appid))->select();
			$requesttemp 	= D('Requesttemp')->where(array('appid'=>$appid))->select();
			$this->assign('requesttemp',$requesttemp);
			$this->assign('scanruledata',$scanruledata);
	        $this->assign('taskinfo',$taskinfo);
       		$this->assign('detecitme',$detec_item_list);
       		$this->assign('pageshow',$page);
       		$this->assign('star',$star);
			$this->assign('taskname',$taskname);
			$this->display();
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	

	//获取任务的应用安全的数据
	public function task_item_application(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$model 			= D('Appinfo');
		$maintain 		= D('Maintain');

		$appid 	   		= (int)I('get.appid');
	       	//统计应用的高中低威胁,如果某一类中出现一个高，则该类就显示高危的颜色
       	$exploit_db 	= D("ExploitDb");
       	// $vulinfo 		= D('Vulinfo');
       	$analysisResults= D('AnalysisResults');
       	$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/task_item_application';
       	$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "2" || $tid == '5'){
			if(substr($_SESSION[$_SESSION['randomstr']]['showaction'],2,1) != '1'){
				$this->redirect('Mare/Task/task_item_parameter',array('appid'=>$appid));die;
			}
			$taskname  = $model->field('task_name')->find($appid)['task_name'];
			$detectionidlist = $exploit_db->field('id')->where(array('hvtypeid'=>'1'))->select();
			foreach ($detectionidlist as $key => $value) {
				if($key == 0){
					$tmp = $value['id'];
				}else{
					$tmp .= ",".$value['id'];
				}
			}

			$page 				= ($page == null) ? 0 : (int)I('get.p');
            $countnum			= 15;
            $star 			 	= ((int)I('get.p',1)-1)*$countnum;
            
            $count 				= count($analysisResults->where(array('detectionid'=>array('in',$tmp),'appid' => $appid,'issues_count'=>array('gt',0),'risk_level'=>array('gt',1)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->group('detectionid desc')->select());
            // 分页
            $p	= new \Think\Page($count,$countnum);
	    	$p->lastSuffix 		=false;
            $p->setConfig('header','<br/><li class="rows">共<b>%TOTAL_ROW%</b>条记录  每页<b>'.$countnum.'</b>条  第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
            $p->setConfig('prev','上一页');
            $p->setConfig('last','末页');
            $p->setConfig('first','首页');
            $p->setConfig('next','下一页');
            $p->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    		$page 				= $p->show();

       		$detec_item_list  	= $analysisResults->field('detectionid,sum(issues_count) as issues_count,risk_level,case_name')->where(array('detectionid'=>array('in',$tmp),'appid' => $appid,'issues_count'=>array('gt',0),'risk_level'=>array('gt',1)))->limit($p->firstRow.','.$p->listRows)->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC,detectionid ASC')->group('detectionid asc')->select();

       		$zh_to_num          = array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
	        $num_to_zh          = array('0'=>'暂未评级','1'=>'安全','2'=>'低','3'=>'中','4'=>'高');
	        
	        // $detec_item_list = list_sort_by($detec_item_list, 'issues_severity', $sortby = 'desc');

       		// @addMareLog(array(
       			// 'username'		=>$personinfo['realname'],
			    // 'handleurl'     =>$url, 
			    // 'handlecontent' =>'查看应用安全信息',
			    // 'handleresult'  =>'成功',
			    // 'handleremarks' =>'用户查看应用安全信息成功'
			// ));
			$scanrule   	= D("Scanrule");
			$scanruledata 	= $scanrule->where(array('appid'=>$appid))->select();
			$requesttemp 	= D('Requesttemp')->where(array('appid'=>$appid))->select();
			$this->assign('requesttemp',$requesttemp);
			$this->assign('scanruledata',$scanruledata);
       		$this->assign('detecitme',$detec_item_list);
       		$this->assign('pageshow',$page);
       		$this->assign('star',$star);
			$this->assign('taskname',$taskname);
			$this->display('task_item_summary');
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//获取业务操作安全的数据
	public function task_item_business(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$model 			= D('Appinfo');
		$maintain 		= D('Maintain');
		// $appid 			= I('get.appid');

		$appid 	   		= (int)I('get.appid');
	       	//统计应用的高中低威胁,如果某一类中出现一个高，则该类就显示高危的颜色
       	$exploit_db 	= D("ExploitDb");
       	// $vulinfo 		= D('Vulinfo');
       	$analysisResults= D('AnalysisResults');
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
		if($tid == "2" || $tid == '5'){
			if(substr($_SESSION[$_SESSION['randomstr']]['showaction'],2,1) != '1'){
				$this->redirect('Mare/Task/task_item_parameter',array('appid'=>$appid));die;
			}
			$taskname  = $model->field('task_name')->find($appid)['task_name'];
			$detectionidlist = $exploit_db->field('id')->where(array('hvtypeid'=>'2'))->select();
			foreach ($detectionidlist as $key => $value) {
				if($key == 0){
					$tmp = $value['id'];
				}else{
					$tmp .= ",".$value['id'];
				}
			}
			$page 				= ($page == null) ? 0 : (int)I('get.p');
            $countnum			= 15;
            $star 			 	= ((int)I('get.p',1)-1)*$countnum;
            $count 				= count($analysisResults->where(array('detectionid'=>array('in',$tmp),'appid' => $appid,'issues_count'=>array('gt',0),'risk_level'=>array('gt',1)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->group('detectionid desc')->select());
            // 分页
            $p	= new \Think\Page($count,$countnum);
	    	$p->lastSuffix 		=false;
            $p->setConfig('header','<br/><li class="rows">共<b>%TOTAL_ROW%</b>条记录  每页<b>'.$countnum.'</b>条  第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
            $p->setConfig('prev','上一页');
            $p->setConfig('last','末页');
            $p->setConfig('first','首页');
            $p->setConfig('next','下一页');
            $p->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    		$page 				= $p->show();

       		
    		$detec_item_list  	= $analysisResults->field('detectionid,sum(issues_count) as issues_count,risk_level,case_name')->where(array('detectionid'=>array('in',$tmp),'appid' => $appid,'issues_count'=>array('gt',0),'risk_level'=>array('gt',1)))->limit($p->firstRow.','.$p->listRows)->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC,detectionid ASC')->group('detectionid asc')->select();
    		$zh_to_num          = array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
	        $num_to_zh          = array('0'=>'暂未评级','1'=>'安全','2'=>'低','3'=>'中','4'=>'高');
	       
	        // $detec_item_list = list_sort_by($detec_item_list, 'issues_severity', $sortby = 'desc');

       		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/task_item_business';
			// @addMareLog(array(
				// 'username'		=>$personinfo['realname'],
			    // 'handleurl'     =>$url, 
			    // 'handlecontent' =>'查看业务操作安全信息',
			    // 'handleresult'  =>'成功',
			    // 'handleremarks' =>'用户查看业务操作安全信息成功'
			// ));
	        $taskinfo = $model->find($appid);
	        $scanrule   	= D("Scanrule");
			$scanruledata 	= $scanrule->where(array('appid'=>$appid))->select();
			$requesttemp 	= D('Requesttemp')->where(array('appid'=>$appid))->select();
			$this->assign('requesttemp',$requesttemp);
			$this->assign('scanruledata',$scanruledata);
	        $this->assign('taskinfo',$taskinfo);
       		$this->assign('detecitme',$detec_item_list);
       		$this->assign('pageshow',$page);
       		$this->assign('star',$star);
			$this->assign('taskname',$taskname);
			$this->display('task_item_summary');
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//获取数据通信安全的数据
	public function task_item_communication(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$model 			= D('Appinfo');
		$maintain 		= D('Maintain');
		// $appid 			= I('get.appid');

		$appid 	   		= (int)I('get.appid');
	    //统计应用的高中低威胁,如果某一类中出现一个高，则该类就显示高危的颜色
       	$exploit_db 	= D("ExploitDb");
       	// $vulinfo 		= D('Vulinfo');
       	$analysisResults= D("AnalysisResults");
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
		if($tid == "2" || $tid == '5'){
			if(substr($_SESSION[$_SESSION['randomstr']]['showaction'],2,1) != '1'){
				$this->redirect('Mare/Task/task_item_parameter',array('appid'=>$appid));die;
			}
			$taskname  = $model->field('task_name')->find($appid)['task_name'];
			//获得检测项id的集合 testtype = 2,8,9,10,11,12,13 详情请看 detectype表和detecname表
			// $detectionidlist 	= $detecname->field('id')->where(array('testtype'=>'4'))->select();
			$detectionidlist = $exploit_db->field('id')->where(array('hvtypeid'=>'3'))->select();
			foreach ($detectionidlist as $key => $value) {
				if($key == 0){
					$tmp = $value['id'];
				}else{
					$tmp .= ",".$value['id'];
				}
			}
			$page 				= ($page == null) ? 0 : (int)I('get.p');
            $countnum			= 15;
            $star 			 	= (I('get.p',1)-1)*$countnum;
            $count 				= count($analysisResults->where(array('detectionid'=>array('in',$tmp),'appid' => $appid,'issues_count'=>array('gt',0),'risk_level'=>array('gt',1)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->group('detectionid desc')->select());

            // 分页
            $p	= new \Think\Page($count,$countnum);
	    	$p->lastSuffix 		=false;
            $p->setConfig('header','<br/><li class="rows">共<b>%TOTAL_ROW%</b>条记录  每页<b>'.$countnum.'</b>条  第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
            $p->setConfig('prev','上一页');
            $p->setConfig('last','末页');
            $p->setConfig('first','首页');
            $p->setConfig('next','下一页');
            $p->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    		$page 				= $p->show();

       		

    		$detec_item_list  	= $analysisResults->field('detectionid,sum(issues_count) as issues_count,risk_level,case_name')->where(array('detectionid'=>array('in',$tmp),'appid' => $appid,'issues_count'=>array('gt',0),'risk_level'=>array('gt',1)))->limit($p->firstRow.','.$p->listRows)->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC,detectionid ASC')->group('detectionid asc')->select();
    		$zh_to_num          = array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
	        $num_to_zh          = array('0'=>'暂未评级','1'=>'安全','2'=>'低','3'=>'中','4'=>'高');
	        
	        // $detec_item_list = list_sort_by($detec_item_list, 'issues_severity', $sortby = 'desc');

       		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/task_item_communication';
			// @addMareLog(array(
				// 'username'		=>$personinfo['realname'],
			    // 'handleurl'     =>$url, 
			    // 'handlecontent' =>'查看数据通信安全信息',
			    // 'handleresult'  =>'成功',
			    // 'handleremarks' =>'用户查看数据通信安全信息成功'
			// ));
			$taskinfo = $model->find($appid);
			$scanrule   	= D("Scanrule");
			$scanruledata 	= $scanrule->where(array('appid'=>$appid))->select();
			$requesttemp 	= D('Requesttemp')->where(array('appid'=>$appid))->select();
			$this->assign('requesttemp',$requesttemp);
			$this->assign('scanruledata',$scanruledata);
	        $this->assign('taskinfo',$taskinfo);
       		$this->assign('detecitme',$detec_item_list);
       		$this->assign('pageshow',$page);
       		$this->assign('star',$star);
			$this->assign('taskname',$taskname);
			$this->display('task_item_summary');
		}else{
			$this->redirect('Mare/Index/index');
		}
	}
	
	//获取服务器端安全的数据
	public function task_item_server(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$model 			= D('Appinfo');
		$maintain 		= D('Maintain');
		// $appid 			= I('get.appid');

		$appid 	   		= (int)I('get.appid');
	       	//统计应用的高中低威胁,如果某一类中出现一个高，则该类就显示高危的颜色
       	$exploit_db 	= D("ExploitDb");
       	// $vulinfo 		= D('Vulinfo');
       	$analysisResults= D('AnalysisResults');
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
		if($tid == "2" || $tid == '5'){
			$taskname  = $model->field('task_name')->find($appid)['task_name'];
			//获得检测项id的集合 testtype = 2,8,9,10,11,12,13 详情请看 detectype表和detecname表
			// $detectionidlist 	= $detecname->field('id')->where(array('testtype'=>'7'))->select();
			$detectionidlist = $exploit_db->field('id')->where(array('hvtypeid'=>'4'))->select();
			foreach ($detectionidlist as $key => $value) {
				if($key == 0){
					$tmp = $value['id'];
				}else{
					$tmp .= ",".$value['id'];
				}
			}

			$page 				= ($page == null) ? 0 : (int)I('get.page');
            $countnum			= 15;
            $star 			 	= (I('get.p',1)-1)*$countnum;
            $count 				= count($analysisResults->where(array('detectionid'=>array('in',$tmp),'appid' => $appid,'issues_count'=>array('gt',0),'risk_level'=>array('gt',1)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->group('detectionid desc')->select());
           
            // 分页
            $p	= new \Think\Page($count,$countnum);
	    	$p->lastSuffix 		=false;
            $p->setConfig('header','<br/><li class="rows">共<b>%TOTAL_ROW%</b>条记录  每页<b>'.$countnum.'</b>条  第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
            $p->setConfig('prev','上一页');
            $p->setConfig('last','末页');
            $p->setConfig('first','首页');
            $p->setConfig('next','下一页');
            $p->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    		$page 				= $p->show();

       		$detec_item_list  	= $analysisResults->field('detectionid,sum(issues_count) as issues_count,risk_level,case_name')->where(array('detectionid'=>array('in',$tmp),'appid' => $appid,'issues_count'=>array('gt',0),'risk_level'=>array('gt',1)))->limit($p->firstRow.','.$p->listRows)->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC,detectionid ASC')->group('detectionid asc')->select();
       		$zh_to_num          = array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
	        $num_to_zh          = array('0'=>'暂未评级','1'=>'安全','2'=>'低','3'=>'中','4'=>'高');
	        // $detec_item_list = list_sort_by($detec_item_list, 'issues_severity', $sortby = 'desc');
       		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/task_item_server';
			// @addMareLog(array(
				// 'username'		=>$personinfo['realname'],
			    // 'handleurl'     =>$url, 
			    // 'handlecontent' =>'查看服务端安全信息',
			    // 'handleresult'  =>'成功',
			    // 'handleremarks' =>'用户查看服务端安全信息成功'
			// ));
	        $taskinfo = $model->find($appid);
	        $scanrule   	= D("Scanrule");
			$scanruledata 	= $scanrule->where(array('appid'=>$appid))->select();
			$requesttemp 	= D('Requesttemp')->where(array('appid'=>$appid))->select();
			$this->assign('requesttemp',$requesttemp);
			$this->assign('scanruledata',$scanruledata);
	        $this->assign('taskinfo',$taskinfo);
       		$this->assign('resultTrans',$resultTrans);
       		$this->assign('detecitme',$detec_item_list);
       		$this->assign('pageshow',$page);
       		$this->assign('star',$star);
			$this->assign('taskname',$taskname);
			$this->display('task_item_summary');
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//调试功能
	public function task_console(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$model 			= D('Appinfo');
		$maintain 		= D('Maintain');
		$requesttemp 	= D('Requesttemp');

		$appid 	   		= (int)I('get.appid');

	       	//统计应用的高中低威胁,如果某一类中出现一个高，则该类就显示高危的颜色
       	$exploit_db 	= D("ExploitDb");
       	// $vulinfo 		= D('Vulinfo');
       	$analysisResults= D('AnalysisResults');
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
		if($tid == "2" || $tid == '5'){
			$taskname  = $model->field('task_name')->find($appid)['task_name'];
			//获得检测项id的集合 testtype = 2,8,9,10,11,12,13 详情请看 detectype表和detecname表
			// $detectionidlist 	= $detecname->field('id')->where(array('testtype'=>'7'))->select();
			

			$page 				= ($page == null) ? 0 : (int)I('get.page');
            $countnum			= 12;
            $star 			 	= (I('get.p',1)-1)*$countnum;
            $count 				= count($requesttemp->where(array('appid' => $appid))->select());
           
            // 分页
            $p	= new \Think\Page($count,$countnum);
	    	$p->lastSuffix 		=false;
            $p->setConfig('header','<br/><li class="rows">共<b>%TOTAL_ROW%</b>条记录  每页<b>'.$countnum.'</b>条  第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
            $p->setConfig('prev','上一页');
            $p->setConfig('last','末页');
            $p->setConfig('first','首页');
            $p->setConfig('next','下一页');
            $p->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    		$page 				= $p->show();

       		$detec_item_list  	= $requesttemp->where(array('appid' => $appid))->limit($p->firstRow.','.$p->listRows)->select();
       		$zh_to_num          = array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
	        $num_to_zh          = array('0'=>'暂未评级','1'=>'安全','2'=>'低','3'=>'中','4'=>'高');
	       

       		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/task_console';
			// @addMareLog(array(
				// 'username'		=>$personinfo['realname'],
			    // 'handleurl'     =>$url, 
			    // 'handlecontent' =>'查看服务端安全信息',
			    // 'handleresult'  =>'成功',
			    // 'handleremarks' =>'用户查看服务端安全信息成功'
			// ));
	        $taskinfo = $model->find($appid);
	        $scanrule   	= D("Scanrule");
			$scanruledata 	= $scanrule->where(array('appid'=>$appid))->select();
			
			$this->assign('scanruledata',$scanruledata);
	        $this->assign('taskinfo',$taskinfo);
       		$this->assign('resultTrans',$resultTrans);
       		$this->assign('detecitme',$detec_item_list);
       		$this->assign('pageshow',$page);
       		$this->assign('star',$star);
			$this->assign('taskname',$taskname);
			$this->display();
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	public function task_getneedconsole(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
		if($tid == "2" || $tid == '5'){
			$id 				= I('get.id');
			$requesttemp 		= D('Requesttemp');
			$goalrequesttemp	= $requesttemp->where(array('id'=>$id))->find();
			$sss 				= htmlspecialchars_decode(explode("\n\n", $goalrequesttemp['request_raw'])[0]);  
			$ssarr 				= array_slice(explode("\n",trim($sss,"\n")),1);
			foreach($ssarr as $key => $val){
				$tmp = explode(": ",trim($val,"\n"));
				$goalarr[$key][1] = str_replace("-","",trim($tmp[1]));
				$goalarr[$key][0] = trim($tmp[0]);
			}
			$this->ajaxReturn(array('code'=>'success','info'=>$goalarr));
		}else{
			$this->ajaxReturn(array('code'=>'false','info'=>'无权限获取信息'));
		}
	}

	public function task_getappconsole(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$exploit_db 	= D("ExploitDb");
       	$requesttemp 	= D('Requesttemp');
       	$analysisResults= D('AnalysisResults');
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		$appid 			= I('get.appid');
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
		if($tid == "2" || $tid == '5'){
			$requesttemplist 		= $requesttemp->where(array('appid'=>$appid))->select();

			foreach ($requesttemplist as $key => $value) {
				// $requesttemplist[$key]['head'] = 
				$ssss = array_slice(explode("\n",trim(htmlspecialchars_decode(explode("\n\n", $value['request_raw'])[0]),"\n")),1);
				$head = array();
				foreach ($ssss as $kss => $vss) {
					$tmp 	= explode(": ", trim($vss,"\n"));
					$head[str_replace("-","",trim($tmp[0]))] = str_replace("-","",trim($tmp[1]));
				}
				unset($requesttemplist[$key]['request_raw']);
				$requesttemplist[$key]['head'] = $head;
			}
			$this->ajaxReturn(array('code'=>'success','info'=>$requesttemplist));
		}
	}

	//检测项的详细检测结果信息

	public function task_item_detial(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		if (C('IS_SIMPLE')) {
			$this->redirect('Task/task_list');
			exit();
		}
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
		if($tid == "2" || $tid == '5'){
			$detectionid 	= (int)I('get.detectionid');
			$appinfo 		= D('Appinfo');
			$appid 	   		= (int)I('get.appid');
	       	//统计应用的高中低威胁,如果某一类中出现一个高，则该类就显示高危的颜色
	       	//$exploit_db 	= D("ExploitDb");
	       	// $vulinfo 		= D('Vulinfo');
	       	$analysisResults= D("AnalysisResults");
	       	$scanrule   	= D("Scanrule");
			$scanruledata 	= $scanrule->where(array('appid'=>$appid))->select();

			//获得任务名称
			$goalAppinfo 	= $appinfo->field('tasktype,task_name')->find($appid);
			$taskname 		= $goalAppinfo['task_name'];

			$detec_item_detial_info 		= 	$analysisResults->field('*')->where(array('appid'=>$appid,'detectionid'=>$detectionid))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->find();
			$zhtestname 	= $detec_item_detial_info['case_name'];
			$detection_process=$detec_item_detial_info['detection_process'];
// 			echo $detection_process;die();
			//对含有url的detection_process进行处理
			if($detectionid==44) {
			    $arr=explode("相关内容：",$detection_process);
			} else if($detectionid==142) {
			    $arr=explode("请求URL：",$detection_process);
			    
			}
			
			$temp=explode("####",$arr[1]);

			if($detectionid==44) {
			     $arr[1] = $temp[0];
			} else {
			    $arr[1] = $temp;
			}
			
			$arr[1]=str_replace(array("<br/>","\\n"), "", $arr[1]);
			//对含有url的detection_process进行处理
			if(($detectionid==44 ||  $detectionid == 142) && (!is_null(json_decode(htmlspecialchars_decode($arr[1]))) || (is_array($arr[1]) && count($arr[1]) > 1))){
// 			    print_r($arr[1]);die();
			    if($detectionid == 142) {
// 			        echo count($arr[1]);die();
			        foreach ($arr[1] as $k => $v) {
			            $arr_list = json_decode(htmlspecialchars_decode($v));
			            foreach ($arr_list as $k1 => $v1) {
			                $inner_data[] = $v1;
			            }
			            unset($arr_list);
			            
			        }
			    } else {
			        $inner_data=json_decode(htmlspecialchars_decode($arr[1]));
			    }
			    
			    
			    $detec_item_detial_info['detection_process_instruction']=$arr[0];
			    $detec_item_detial_info['detection_process']=$inner_data;
			    if ($detectionid==44) {
    			    if(trim($temp[1])) {
    			        $detec_item_detial_info['detection_process_str'] = str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars(trim($temp[1])));
    			    }
			    }
			    
			    
			}else {
			    $detec_item_detial_info['detection_process'] = preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/","",$detec_item_detial_info['detection_process']);
			} 
			

			//P($detec_item_detial_info);die();
			$num_to_zh			= array('0'=>'暂未评级','1'=>'安全','2'=>'低','3'=>'中','4'=>'高');
			if(is_numeric($detec_item_detial_info['risk_level'])){
				$detec_item_detial_info['risk_level'] = $num_to_zh[$detec_item_detial_info['risk_level']];
			}

			$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/task_item_detial';
			@addMareLog(array(
				'username'		=>$personinfo['realname'],
			    'handleurl'     =>$url, 
			    'handlecontent' =>'查看具体检查项信息',
			    'handleresult'  =>'成功',
			    'handleremarks' =>'用户查看具体检查项信息成功'
			));
			//print_r($detec_item_detial_info);die;
			$this->assign('scanruledata',$scanruledata);
			$this->assign('didi',$detec_item_detial_info);
			$this->assign('goalAppinfo',$goalAppinfo);
			$this->assign('detecname',$zhtestname);
			$this->assign('taskname',$taskname);
			$this->display();
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//获取测试的人
	public function task_tester(){

		$tester 		= I('get.tester');
		$device 		= D('Device');
		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "2" || $tid == '5'){
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
			if(is_string($tester) && $tester != null){
				$testerlist = $device->field('at_user.userid,realname')->where(array('devplatform'=>$tester,'status'=>0))->join('left join at_user on at_user.platform = at_device.devplatform')->group('loginemail')->select();
			}else{
				//$testerlist = $device->field('at_user.userid,realname')->join('left join at_user on at_user.platform = at_device.devplatform')->where('at_user.platform is not null and status=0')->group('loginemail')->select();
				//2017.5.18 去掉iOS设备检测微信、Web的安全检测，iOS只测试iOS的APP安全检测任务。
				$testerlist = $device->field('at_user.userid,realname')->where(array('devplatform'=>'android','status'=>0))->join('left join at_user on at_user.platform = at_device.devplatform')->where('at_user.platform is not null and status=0')->group('loginemail')->select();
			}
			$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/task_tester';
			// @addMareLog(array(
				// 'username'		=>$personinfo['realname'],
			    // 'handleurl'     =>$url, 
			    // 'handlecontent' =>'获取测试人员',
			    // 'handleresult'  =>'成功',
			    // 'handleremarks' =>'用户获取测试人员成功'
			// ));
			$this->ajaxReturn($testerlist);
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//查看自定义规则
	public function task_custom_rule(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$appid 	   		= (int)I('get.appid');
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		@$personinfo  	= $user->where(array('userid'=>$userid))->find();
		if($tid == "2" || $tid == '5'){
			$scanrule   	= D("Scanrule");
			$model 			= D('Appinfo');
			$taskname  		= $model->field('task_name')->find($appid)['task_name'];
			$scanruledata 	= $scanrule->field('ruletype,count(ruletype) as ruletypecount')->where(array('appid'=>$appid))->group('ruletype ASC')->select();
			$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/task_custom_rule';
			$ruletypelist 	= json_decode(D('Maintain')->where(array('key'=>'ruletype'))->find()['value'],1);
			$requesttemp 	= D('Requesttemp')->where(array('appid'=>$appid))->select();
			$this->assign('requesttemp',$requesttemp);
			$this->assign('ruletypelist',$ruletypelist);
			// @addMareLog(array(
				// 'username'		=>$personinfo['realname'],
			    // 'handleurl'     =>$url, 
			    // 'handlecontent' =>'查看自定义规则',
			    // 'handleresult'  =>'成功',
			    // 'handleremarks' =>'用户查看自定义规则成功'
			// ));
			$this->assign('taskname',$taskname);
			$this->assign('scanruledata',$scanruledata);
			$this->display();
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//查看详细的扫描结果
	public function task_custom_rule_detial(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$appid 	   		= (int)I('get.appid');
		$ruletype 		= (int)I('get.ruletype');
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		$user 			= D("User");
		$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		if($tid == "2" || $tid == '5'){
			$scanrule   	= D("Scanrule");
			$model 			= D('Appinfo');
			$taskname  		= $model->field('task_name')->find($appid)['task_name'];
			$scanruledata 	= $scanrule->where(array('appid'=>$appid,'ruletype'=>$ruletype))->select();
			$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/task_custom_rule';
			$ruletypelist 	= json_decode(D('Maintain')->where(array('key'=>'ruletype'))->find()['value'],1);


			$this->assign('ruletypelist',$ruletypelist);
			// @addMareLog(array(
				// 'username'		=>$personinfo['realname'],
			    // 'handleurl'     =>$url, 
			    // 'handlecontent' =>'查看自定义规则',
			    // 'handleresult'  =>'成功',
			    // 'handleremarks' =>'用户查看自定义规则成功'
			// ));
			$this->assign('taskname',$taskname);
			$this->assign('scanruledata',$scanruledata);
			$this->display();
		}else{
			$this->redirect('Mare/Index/index');
		}
	}


	//获取调试命令行的方法
	public function task_send_python_handle(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$postdata  		= I('post.');
		$this->ajaxReturn(array('code'=>'success','info'=>$postdata,'get'=>I('get.')));
	}

	//下载脱壳包
	public function dexdown(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$appid = I('get.appid');
		$model = D("Mare/Appinfo");
		$dexfile = $model->where('appid='.$appid)->getfield('dexfile');
		if (file_exists($dexfile)) {
			localDownFile($dexfile);
			exit();
		}else{
			$this->error('文件不存在');
		}
	}

	//判断userid是否有权限操作appid
	public function check_userid_appid(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$tid = $_SESSION[$_SESSION['randomstr']]['tid'];
		if ($tid !=5) {
			$userid = $_SESSION[$_SESSION['randomstr']]['userid'];
			$appid = I('post.appid');
			$appinfo = D('appinfo');
			$user = $appinfo->where(array('appid'=>$appid))->getfield('userid');
			if ($userid == $user) {
				return true;
			}else{
				return false;
			}
		}else{
			return true; //方法已改为返回true
		}
	}

	//动态更新进度条状态接口
	public function get_status_list(){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
		$appid = I('post.appid');
		array_filter($appid);
		$where = '';
		foreach ($appid as $id) {
			$where .= 'appid='.$id.' or ';
		}
		if (!empty($where)) {
			$where = substr($where,0, -4);
		}

		$maintain = D("Mare/Maintain");
		$status_result = $maintain->field('value')->where(array('key'=>'appteststatus2'))->find();
		$status_arr = json_decode($status_result['value'],true);

		$model = D("Mare/Appinfo");
		$list = $model->field('appid,status,task_status,realname,tasktype,icon')->where($where)->order('appid desc')->limit(12)->select();

		$i = 0;
		foreach ($list as $key => $value) {
			$list[$i]['percent'] = $status_arr[$value['status']];
			$i++;
		}

		echo json_encode($list);
	}

	/**
	 * 任务限制添加量
	 * @param unknown $task_type
	 * @return boolean
	 * @author qinxuening
	 */
	public function check_task_add($task_type = null){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
	    //检查授权时间
	    if(!check_grants()){
	        $this->ajaxReturn(
	            [
	                'code' =>'-1',
	                'status' => 'error',
	                'msg'=>'授权时间已过期！'
	            ]);
	    }
	    
	    
	    
	    //只限制APP
// 	    if (I('task_type') != 'task_add') {
// 	        if(IS_AJAX){
// 	            $this->ajaxReturn(
// 	                [
// 	                    'code' =>'1',
// 	                    'status' => 'success',
// 	                    'msg'=>['data' => '操作成功']
// 	                ]);
// 	        } else {
// 	            return true;
// 	        }
// 	    }
	    
	    $licinfo = M('licinfo');
	    $max_task = $licinfo->field('task_sum, max_task')->find();
	    if ($max_task['max_task'] == -1){
	        if(IS_AJAX){
	            $this->ajaxReturn(
	                [
	                    'code' =>'1',
	                    'status' => 'success',
	                    'msg'=>['data' => '操作成功']
	                ]);
	        } else {
	            return true;
	        }
	    } else {
	        if ($max_task['task_sum'] >= $max_task['max_task']) {
	            if (IS_AJAX) {
	                $this->ajaxReturn(
	                    [
	                        'code' =>'-1',
	                        'status' => 'error',
	                        'msg'=>'检测任务不能超过授权次数！'
	                    ]);
	            } else {
	                return false;
	            }
	        } else {
	            if(IS_AJAX){
	                $this->ajaxReturn(
	                    [
	                        'code' =>'1',
	                        'status' => 'success',
	                        'msg'=>['data' => '操作成功']
	                    ]);
	            } else {
	                return true;
	            }
	            
	        }
	    }
	    
	}
	
	/**
	 * 重置任务限制添加量
	 * @return boolean
	 * @author qinxuening
	 */
	private  function reset_task() {
	    $licinfo = M('licinfo');
	    $result = $licinfo->where(['id' => 1])->save(['task_sum' => 0]);
	    if (false !== $result){
	        return true;
	    } else {
	        return false;
	    }
	}
	
	
	/**
	 * app授权
	 * @param unknown $appid
	 */
	public function app_authorise($appid){
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
	    $Appinfo= D("Appinfo");
	    $userid 		= $_SESSION[$_SESSION['randomstr']]['userid'];
	    $app_info = $Appinfo->where(array('appid' => $appid))->find();
	    //P($app_info);die();
	    if ($app_info['type'] == 'ipa' || $app_info['type'] == 'apk') {
	        $data['task_execute_ip'] = 1;
	        $apprecord['appname'] = $app_info['realname'];
	        $apprecord['package'] = $app_info['package'];
	        $apprecord['md5'] = $app_info['md5'];
	        $apprecord['userid'] = $userid;
	        $apprecord['time'] = time();
	        $apprecord['type'] = $app_info['type'];
	        $package_arr = M('apprecord')->getField('package', true);
	        
	        if (!in_array($apprecord['package'], $package_arr)){
	            if (!get_grand_appnumber()){
	                $delete_app = $Appinfo->where('appid=%d',$appid)->delete();
	                if($delete_app){
	                    $this->ajaxReturn(array('code'=>'error', 'info' => '检测APP不能超过授权个数'));
	                }
	            } else {
	                $apprecord = M('apprecord')->add($apprecord);
	                //M('appinfo')->where(array('appid' => $appid))->save($data);
	            }
	        }
	        
	       // if (check_app_max_count()){
	            $task_record['userid'] = $userid;
	            $task_record['appname'] = $app_info['realname'];
	            $task_record['package'] = $app_info['package'];
	            $task_record['time'] = time();
	            $apprecord_result = M('taskrecord')->add($task_record);
	            M('appinfo')->where(array('appid' => $appid))->save($data);
	            M('licinfo')->where('id=1')->setInc('task_sum');
	        /*} else {
	            $delete_app = $Appinfo->where('appid=%d',$appid)->delete();
	            if($delete_app){
	                $this->ajaxReturn(array('code'=>'error', 'info' => '检测任务不能超过授权次数'));
	            }
	            
	        }*/
	        
	    }
	}
	
	/**
	 * 客户端自定义配置选项
	 */
	private function get_client_rule() {
	    $client_rule_info = M('maintain')->where(['key' => 'client_rule'])->getField('value');
	    $client_rule_info = json_decode($client_rule_info, true);
	    $result = M('client_rule')->where(['status' => 1])->select();
	    foreach ($result as $k => $v) {
	        $arr[$v['rule_type']][$v['id']] = $v['rule_name'];
	    }
// 	    print_r($client_rule_info);
	    $this->assign('client_rule_type', $client_rule_info);
	    $this->assign('client_rule_info', $arr);

	}
	
	/**
	 * 获取url规则配置项
	 */
	private function get_check_url() {
	    $check_url_info = M('maintain')->where(['key' => 'check_url'])->getField('value');
	    $check_url_info = json_decode($check_url_info, true);

	    $this->assign('check_url_info', $check_url_info);
	    $this->assign('check_url_keys', json_encode(array_keys($check_url_info)));
	}
	
	/**
	 * 获取客户端自定义规则
	 */
	public function get_client_rule_detail() {
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
	    $id =  intval(I('id'));
	    if ($id) {
	        $get_client_rule_info = M('client_rule')->where(['id' => $id])->field("rule_type, rule_content")->find();
	        $get_client_rule_info['rule_type'] = htmlspecialchars_decode($get_client_rule_info['rule_type']);
	        $get_client_rule_info['rule_content'] = htmlspecialchars_decode($get_client_rule_info['rule_content']);
	        if ($get_client_rule_info) {
	            $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'info'=> $get_client_rule_info));
	        }
	    }
	}
	
	/**
	 * 客户端自定义规则、URL匹配配置数据保存
	 */
// 	private function add_task_defined_check() {
// 	    $client_rule_info = M('maintain')->where(['key' => 'client_rule'])->getField('value');
// 	    $client_rule_info = json_decode($client_rule_info, true);
// 	    $json = array();
// 	    $itemObject = new \stdClass();
// 	    foreach ($client_rule_info as $k => $v) {
// 	        if (I("$k")) {
// 	            //$client_rule["$k"] = I("$k");
// 	            $itemObject->$k= I("$k");
	           
// 	        }
// 	    }
// 	    array_push($json, $itemObject);
// 	    if ($json) {
// 	        $data['sensitive'] = json_encode($json,JSON_PRETTY_PRINT);
// 	    }
	    
// 	    $check_url_info = M('maintain')->where(['key' => 'check_url'])->getField('value');
// 	    $check_url_info = json_decode($check_url_info, true);
	    
// 	    $json_ = array();
// 	    $itemObject_ = new \stdClass();
// 	    foreach ($check_url_info as $k_ => $v_) {
// 	        if (I("$k_")) {
// // 	            $check_url["$k_"] = I("$k_");
// 	            $itemObject_->$k_= I("$k_");
// 	        }
// 	    }
// 	    array_push($json_, $itemObject_);
// 	    if ($json_) {
// 	        $data['check_url'] = json_encode($json_,JSON_PRETTY_PRINT);
// 	    }
// 	    return $data;
// 	}

	private function add_task_defined_check() {
	    $client_rule_info = M('maintain')->where(['key' => 'client_rule'])->getField('value');
	    $client_rule_info = json_decode($client_rule_info, true);
	    
	    foreach ($client_rule_info as $k => $v) {
	        if (I("$k")) {
	            $client_rule["$k"] = I("$k");
	        }
	    }
	    if ($client_rule) {
	        $data['sensitive'] = json_encode($client_rule);
	    }
	    
	    $check_url_info = M('maintain')->where(['key' => 'check_url'])->getField('value');
	    $check_url_info = json_decode($check_url_info, true);
	    
	    foreach ($check_url_info as $k_ => $v_) {
	        if (I("$k_")) {
	            $check_url["$k_"] = I("$k_");
	        }
	    }
	    
	    if ($check_url) {
	        $data['check_url'] = json_encode($check_url);
	    }
	    return $data;
	}
	
	/**
	 * 触发输入表单自动搜索出前十条匹配记录
	 */
	public function focus_form_input() {
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问';
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
	    $field = I('field');
	    $value = I('value');
	    $testid = I('testid');
	    $where['test_userid'] = $testid;
	    if ($testid) {
	        $check_url_info = M('maintain')->where(['key' => 'check_url'])->getField('value');
	        $check_url_info = json_decode($check_url_info, true);
	        $check_url_info = array_keys($check_url_info);
	        if (in_array($field, $check_url_info)) {
	            $where_['test_userid'] = $testid;
	            $where_['check_url'] = ['neq', ''];
	            $result = M('appinfo')->distinct(true)->field("check_url")->where($where_)->order("appid desc")->select();
	            $count = 0;
	            $arr = [];
	            if ($result) {
	                foreach ($result as $v) {
	                    foreach (json_decode($v['check_url'], true) as $k_ => $v_) {
	                        if ($field == $k_) {
	                            if (!in_array($v_, $arr[$k_])) {
	                                $arr[$k_][] = $v_;
	                                $count += 1;
	                            }
	                        }
	                    }
	                    if ($count == 10) break;
	                }
	            }
	            
	        } else {
	            if ($value && $value != '无') {
	                $where["$field"][] = ['like', $value."%"];
	            } else {
	                $where["$field"][] = ['neq', ''];
	            }
	            
	            if ($field == 'test_phone') {
	                $where["$field"][] = ['neq', '无'];
	                $where["$field"][] = ['neq', ''];
	            }
	            
	            $result = M('appinfo')->distinct(true)->field("$field")->where($where)->order("appid desc")->limit(10)->select();
	            foreach ($result as $v) {
	                $arr[$field][] = $v["$field"];
	            }
	        }
	        
	        $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'info'=> $arr));
	    }
	    
	}

	
	
// 	public function test_123() {
// 	    myLog('123');
// 	}
	
	
	
	
	
	
	
	
}