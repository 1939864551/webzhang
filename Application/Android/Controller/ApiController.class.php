<?php
namespace Android\Controller;
use Think\Controller;
class ApiController extends Controller {
    private $is_proposal;
    public function __construct(){
        parent::__construct();
        $this->is_proposal = [
            '1'=> '是',
            '2'=>'否',
        ];
    }
	/**
	* 判断手机是什么类型
	*/
	private function device_type(){
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
         	return 'iOS';
	    }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
	     	return 'Android';
	    }else{
	     	return 'other';
    	}
	}
	

	public function androidCrashLog(){
		$crash 			= D("Crashlog");
		$url   			= MODULE_NAME.'/'.CONTROLLER_NAME.'/androidCrashLog';
                
		//判断传输过来的数据是否是json数据
		$jsondata			= $GLOBALS['HTTP_RAW_POST_DATA'];

		if($jsondata)
		{
			$requestData = json_decode($jsondata,1);
			$data = array(
				'crash_userid'		=> $requestData['userid'],
				'crash_abnormal'	=> json_encode($requestData['abnormal']),
				'crash_time'		=> date('Y-m-d H:i:s')
				);
			if($requestData['appid']){
				$data['crash_appid'] = $requestData['appid'];
			}
			$res = $crash->data($data)->add();
			if($res){
				// @addMareLog(array(
					// 'handleurl'   	=>$url, 
					// 'handlecontent'	=>'添加异常日志',
					// 'handleresult'	=>'成功',
					// 'handleremarks'	=>'用户添加异常日志成功'
				// ));
				$this->ajaxReturn(array('code'=>'success'));
			}else{
				// @addMareLog(array(
					// 'handleurl'   	=>$url, 
					// 'handlecontent'	=>'添加异常日志',
					// 'handleresult'	=>'失败',
					// 'handleremarks'	=>'用户添加异常日志失败'
				// ));
				$this->ajaxReturn(array('code'=>'false','msg'=>C('RECORD_ADD_FALSE')));
			}
		}else{
			// @addMareLog(array(
				// 'handleurl'   	=>$url, 
				// 'handlecontent'	=>'添加异常日志',
				// 'handleresult'	=>'失败',
				// 'handleremarks'	=>'用户添加异常日志失败'
			// ));
			$this->ajaxReturn(array('code'=>'fail','msg'=>C('METHOD_TIP')));
		}
	}

	//用户登录
	public function login()
	{
		$header  		= getallheaders();
		$user 			= D('User');
		$usertoken 		= D('Usertoken');
		$timestamp 		= D('Timnonce');
		$device 		= D('Device');
		$appinfo 		= D("Appinfo");
		$url   			= MODULE_NAME.'/'.CONTROLLER_NAME.'/login';
                
		//判断传输过来的数据是否是json数据
		$jsondata			= $GLOBALS['HTTP_RAW_POST_DATA'];

		if($jsondata)
		{
			//判断头部的signature是否等于传过来的json数据,随机数的MD5加密值
			$requestData 		= json_decode($jsondata,true);
			if(strtoupper($header['signature']) == strtoupper(md5($jsondata.$requestData['nonce'])) ){
				$username 			= $requestData['username'];
				$password 			= $requestData['password'];
				$devonlyid 			= $requestData['devonlyid'];
				$devname 			= $requestData['devname'];
				$appid 				= $requestData['appid'];
				$devip    			= $requestData['devip'];
				if($username != null && $password != null && $requestData['timestamp'] != null && $requestData['nonce'] != null)
				{	
					// if(!($timestamp->where(array('timestamp'=>$requestData['timestamp'],'nonce'=>$requestData['nonce']))->select()))
					// {
						//获取mac地址
						@exec("arp -n",$macarray); //执行arp -a命令，结果放到数组$array中
						foreach($macarray as $value){ 
							//匹配结果放到数组$mac_array
							if(strpos($value,$devip) === 0 && preg_match("/(:?[0-9A-F]{2}[:-]){5}[0-9A-F]{2}/i",$value,$mac_array)){
								$devmac = $mac_array[0]; 
								break; 
							} 
						}


						$data = array(
							'loginemail'	=> $username,
							'password'		=> $password
						);
						$platform 	= strtolower($this->device_type());
						$trueplatform = $user->where(array('loginemail'=>$username))->where(array('at_user.platform'=>$platform))->join('left join at_device on at_device.devplatform = at_user.platform')->select();
						if($trueplatform){
							$info 		= $user->where($data)->where(array('tid'=>1,'status'=>0))->join('left join at_userauth on at_userauth.uid = at_user.userid')->select();
							if($info)
							{
								$getdevplatform = getUserAgent();
								if(!$device->where(array('devonlyid'=>$devonlyid))->find()){
									$devdata = array('devplatform'=>$getdevplatform,'devonlyid'=>$devonlyid,'devname'=>$devname,'devip'=>$devip);
									if($devmac){
										$devdata['devmac'] = $devmac;
									}
									$devdata['devtask']	= time();
									@$device->data($devdata)->add();
								}else{
									@$device->where(array('devonlyid'=>$devonlyid))->data(array('devip'=>$devip,'devmac'=>$devmac,'devtask'=>time()))->save();
								}
								$token = createRand(32);
								$usertoken->startTrans();
								if(  $usertoken->where(array('userid'=>$info[0]['userid']))->select() ){
									$resusertoken  = $usertoken->where(array('userid'=>$info[0]['userid']))->data(array('token'=>$token,'timestamp'=>time()))->save();
								}else{
									$resusertoken  = $usertoken->data(array('token'=>$token,'timestamp'=>time(),'userid'=>$info[0]['userid']))->add();
								}
								
								$restimestamp  = $timestamp->data(array('timestamp'=>$requestData['timestamp'],'nonce'=>$requestData['nonce']))->add();
								if($resusertoken !== false && $restimestamp !== false){
									// @addMareLog(array(
										// 'username' 		=> $info[0]['realname'],
										// 'handleurl'   	=>$url, 
										// 'handlecontent'	=>'登录',
										// 'handleresult'	=>'成功',
										// 'handleremarks'	=>'用户登录成功'
									// ));
									if(isset($appid) && !is_null($appid) && !empty($appid)){
										$isExistApp = $appinfo->where(array('appid'=>$appid,'test_userid'=>$info[0]['userid']))->find();
										if($isExistApp){
											$isdown = false; //$isdown true的话为重新下载任务,false则不需要
										}else{
											$isdown = true;
										}
									}else{
										$isdown = true;
									}
									@$user->where(array('userid'=>$info['0']['userid']))->data(array('lasttime'=>time(),'ip'=>$devip))->save();
									$usertoken->commit();
									$this->ajaxReturn(array('code'=>'success','msg'=>C('LOGIN_SUCCESS'),'token'=>$token,'isdown'=>$isdown,'userid'=>$info['0']['userid']));
								}else{
									$usertoken->rollback();
									// @addMareLog(array(
										// 'handleurl'   	=>$url, 
										// 'handlecontent'	=>'登录',
										// 'handleresult'	=>C('LOGIN_ERROR'),
										// 'handleremarks'	=>'用户登录失败'
									// ));
									$this->ajaxReturn(array('code'=>'fail','msg'=>C('LOGIN_ERROR')));
								}
							}
							else
							{
								// @addMareLog(array(
									// 'handleurl'   	=>$url, 
									// 'handlecontent'	=>'登录',
									// 'handleresult'	=>'失败',
									// 'handleremarks'	=>'用户登录失败'
								// ));
								$this->ajaxReturn(array('code'=>'fail','msg'=>C('LOGIN_ERROR')));
							}
						}else{
							// @addMareLog(array(
								// 'handleurl'   	=>$url, 
								// 'handlecontent'	=>'登录',
								// 'handleresult'	=>'失败',
								// 'handleremarks'	=>'用户登录失败'
							// ));
							$this->ajaxReturn(array('code'=>'fail','msg'=>C('LOGIN_PLATFROM_ERROR')));
						}						
					// }
					// else
					// {
					//  @addMareLog(array(
							// 'handlecontent'	=>C('DATE_TIME_REPEAT')." : ".$jsondata.'，'.$url
							// ));
						// 	$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATE_TIME_REPEAT')));
					// }
				}
				else
				{
					// @addMareLog(array(
						// 'handleurl'   	=>$url, 
						// 'handlecontent'	=>'登录',
						// 'handleresult'	=>'失败',
						// 'handleremarks'	=>'用户登录失败'
					// ));
					$this->ajaxReturn(array('code'=>'fail','msg'=>C('NEED_PARAMETER')));
				}
			}
			else
			{
				// @addMareLog(array(
					// 'handleurl'   	=>$url, 
					// 'handlecontent'	=>'登录',
					// 'handleresult'	=>'失败',
					// 'handleremarks'	=>'用户登录失败'
				// ));
				$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATA_VALIDATION_ERROR')));
			}
		}
		else
		{
			// @addMareLog(array(
				// 'handleurl'   	=>$url, 
				// 'handlecontent'	=>'登录',
				// 'handleresult'	=>'失败',
				// 'handleremarks'	=>'用户登录失败'
			// ));
			$this->ajaxReturn(array('code'=>'fail','msg'=>C('METHOD_TIP')));
		}
	}

	//修改密码
	public function changpwd()
	{
		$header  		= getallheaders();
		$user 			= D('User');
		$usertoken 		= D('Usertoken');
		$timestamp 		= D('Timnonce');
		$url   			= MODULE_NAME.'/'.CONTROLLER_NAME.'/changpwd';
		//判断传输过来的数据是否是json数据
		// if($header['Content-Type'] == "application/json"){
			//接收到的数据
		$jsondata			= $GLOBALS['HTTP_RAW_POST_DATA'];
		if($jsondata)
		{
			//判断头部的signature是否等于传过来的json数据,随机数,token数据的MD5加密值
			$requestData 		= json_decode($jsondata,true);
			$userid 			= $requestData['userid'];
			$oldpwd 			= $requestData['oldpwd'];
			$newpwd 			= $requestData['newpwd'];
			if($userid)
			{
				$gettoken  			= $usertoken->where(array('userid'=>$userid))->order('timestamp DESC')->limit(1)->select()[0];
				$this->overtime($gettoken['timestamp']);
				@$usertoken->where(array('id'=>$gettoken['id']))->save(array('timestamp'=>time()));
				//判断头部的signature是否等于传过来的json数据,随机数,token数据的MD5加密值
				if( $header['signature'] == md5($jsondata.$requestData['nonce'].$gettoken['token']) )
				{

					// if(!($timestamp->where(array('timestamp'=>$requestData['timestamp'],'nonce'=>$requestData['nonce']))->select()))
					// 	{
							// @$timestamp->data(array('timestamp'=>$requestData['timestamp'],'nonce'=>$requestData['nonce']))->add();
							if($userid != null && $oldpwd != null && $newpwd != null && $requestData['timestamp'] != null && $requestData['nonce'] != null)
							{	
								//用户的个人信息
								@$personinfo = $user->find($userid);
								//判断是否存在该用户
								if($user->where(array('userid'=>$userid,'password'=>$oldpwd)))
								{
									//
									$res = $user->where(array('userid'=>$userid))->save(array('password'=>$newpwd));
									if($res)
									{
										@$timestamp->data(array('timestamp'=>$requestData['timestamp'],'nonce'=>$requestData['nonce']))->add();
										// @addMareLog(array(
											// 'handleurl'   	=>$url, 
											// 'handlecontent'	=>'修改密码',
											// 'handleresult'	=>'成功',
											// 'handleremarks'	=>'用户修改密码成功'
										// ));
										$this->ajaxReturn(array('code'=>'success','msg'=>C('DATA_UPDATE_SUCCESS')));
									}
									else
									{
										// @addMareLog(array(
											// 'handleurl'   	=>$url, 
											// 'handlecontent'	=>'修改密码',
											// 'handleresult'	=>'失败',
											// 'handleremarks'	=>'用户修改密码失败'
										// ));
										$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATA_UPDATE_FALSE')));
									}
								}
								else
								{
									// @addMareLog(array(
											// 'handleurl'   	=>$url, 
											// 'handlecontent'	=>'修改密码',
											// 'handleresult'	=>'失败',
											// 'handleremarks'	=>'用户修改密码失败'
										// ));
									//旧密码错误
									$this->ajaxReturn(array('code'=>'fail','msg'=>C('UPDATE_PWD_ERROR')."旧密码错误"));
								}
							}
							else
							{
								// @addMareLog(array(
									// 'handleurl'   	=>$url, 
									// 'handlecontent'	=>'修改密码',
									// 'handleresult'	=>'失败',
									// 'handleremarks'	=>'用户修改密码失败'
								// ));
								$this->ajaxReturn(array('code'=>'fail','msg'=>C('NEED_PARAMETER')));
							}
						// }
						// else
						// {
						//  @addMareLog(array(
						// 	'handleurl'   	=>$url, 
						// 	'handlecontent'	=>'修改密码',
						// 	'handleresult'	=>'失败',
						// 	'handleremarks'	=>'用户修改密码失败'
						// ));
						// 	$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATE_TIME_REPEAT')));
						// }
				}
				else
				{
					// @addMareLog(array(
						// 'handleurl'   	=>$url, 
						// 'handlecontent'	=>'修改密码',
						// 'handleresult'	=>'失败',
						// 'handleremarks'	=>'用户修改密码失败'
					// ));
					$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATA_VALIDATION_ERROR')));
				}
			}
			else
			{
				// @addMareLog(array(
					// 'handleurl'   	=>$url, 
					// 'handlecontent'	=>'修改密码',
					// 'handleresult'	=>'失败',
					// 'handleremarks'	=>'用户修改密码失败'
				// ));
				$this->ajaxReturn(array('code'=>'fail','msg'=>C('NEED_PARAMETER')));
			}
		}
		else
		{
			// @addMareLog(array(
				// 'handleurl'   	=>$url, 
				// 'handlecontent'	=>'修改密码',
				// 'handleresult'	=>'失败',
				// 'handleremarks'	=>'用户修改密码失败'
			// ));
			$this->ajaxReturn(array('code'=>'fail','msg'=>C('METHOD_TIP')));
		}
	}

	//超时登录
	public function overtime($dbtime)
	{
		$overtime  		= C('OVERTIME');
		$currenttime 	= time();
		if($currenttime - $dbtime > $overtime){
			$this->ajaxReturn(array('code'=>'fail','msg'=>C('LOGIN_OVERTIME')));exit;
		}
	}


	//添加测试步骤数据和截图
	public function saverecord()
	{
		if(IS_POST){
			$userid 			= I('post.userid');
			$appid 				= I('post.appid');
			$time 				= I('post.time');
			$rinfo 				= I('post.info');
			$stepid 			= I('post.stepid');
			$deteclist 			= I('post.deteclist');
			$timestamp 			= I('post.timestamp');
			$currentstate		= I('post.currentstate');
			$nonce 				= I('post.nonce');
			$usertoken 			= D('Usertoken');
			$timestamptb		= D('Timnonce');
			$user 				= D('User');
			$appinfo 			= D('Appinfo');
			$vulinfo 			= D("Vulinfo");
			$exploit_db			= D("ExploitDb");
			$url   				= MODULE_NAME.'/'.CONTROLLER_NAME.'/saverecord';

			if(!empty($userid) && !empty($appid) && !empty($stepid) && !empty($deteclist) && !empty($timestamp)&& !empty($nonce))
			{

				$header  		= getallheaders();
				$gettoken 		= $usertoken->where(array('userid'=>$userid))->order('timestamp DESC')->limit(1)->select()[0];
				//判断是否登录超时
				// $this->overtime($gettoken['timestamp']);
				// if(!($timestamptb->where(array('timestamp'=>$timestamp,'nonce'=>$nonce))->select()))
				// {
				// 	@$timestamptb->data(array('timestamp'=>$timestamp,'nonce'=>$nonce))->add();
					$usertoken->where(array('id'=>$gettoken['id']))->save(array('timestamp'=>time()));
					if($header['signature'] == md5($userid.$appid.$stepid.$time.$rinfo.$deteclist.$currentstate.$timestamp.$nonce.$nonce.$gettoken['token']))
					{
						if($user->where(array('userid'=>$userid))->select())
						{				
							//用户信息
							@$personinfo 		= $user->find($userid);

							//如果有如果有图片上传
							if($_FILES){
								$upload = new \Think\Upload();
								// 实例化上传类 
								$upload->maxSize = 3145728 ; //3M
								// $upload->maxSize = 20971520 ; //20M
								// 设置附件上传大小 
								$upload->saveName	= array('uniqid','');
								$upload->exts = array('jpg', 'gif', 'png', 'jpeg');
								// $upload->savePath	= UPLOAD_PATH.'Step/'; 
								$upload->rootPath   = UPLOAD_PATH;
								$upload->savePath	= 'Step/';
								$upload->subName    = array('date','Ymd');
								$info = $upload->upload(); 
								if(!$info) {
									// @addMareLog(array(
										// 'handleurl'   	=>$url, 
										// 'handlecontent'	=>'上传人工测试信息',
										// 'handleresult'	=>'失败',
										// 'handleremarks'	=>'用户上传人工测试信息失败'
									// ));
									// 上传错误提示错误信息 
									$this->ajaxReturn(array('code'=>'fail','msg'=>$upload->getError()));
								}else{
									$img 		= UPLOAD_PATH.$info['img']['savepath'].$info['img']['savename'];
									if($info['img']['name'] == 'hya.png'){
										@unlink($img);
										$img = '';
									}	
								}
							}
							$tmp 		= explode(',', $deteclist);
							$count1 	= $vulinfo->count();
							foreach ($tmp as $k => $v) {
								//hash值等于 appid+detectionid+stepid+issues_img_url+issues_user_input的md5值
								// $issues_name = $detecname->where(array('id'=>$v))->find()['zhtestname'];
								$issues_name  = D("ExploitDb")->where(array('id'=>$v))->find()['vulriskname'];
								$issues_hash = md5($appid.$v.$stepid.$img.$rinfo);
								$vulrisklevel = $exploit_db->where(array('id'=>$v))->find()['vulrisklevel'];
								$data 	= array(
									'appid'				=> $appid,
									'issues_user_input'	=> $rinfo,
									'stepid'			=> $stepid,
									'detectionid'		=> $v,
									'issues_severity'	=> $vulrisklevel,
									'issues_img_url'	=> $img,
									'issues_name'		=> $issues_name,
									'issues_hash'		=> $issues_hash
								);

								//如果存在了相同的appid和detectionid(74)相同则覆盖，否则添加
								if($v == 74){
									$have74detection = $vulinfo->where(array('detectionid'=>74,'appid'=> $appid))->find();
									if($have74detection){
										@$vulinfo->where(array('id'=>$have74detection['id']))->data($data)->save();
									}else{
										@$vulinfo->data($data)->add();
									}
								}else{
									@$vulinfo->data($data)->add();
								}
								
							}
							$count2 	= $vulinfo->count();

							// if($count2-$count1 == count($tmp))
							// {
								@$timestamptb->data(array('timestamp'=>$timestamp,'nonce'=>$nonce))->add();
								if($currentstate == 1){
									if($appinfo->where(array('status'=>array(array('egt',3),array('lt',6),'and')))->find()){
										$res = $appinfo->where(array('appid'=>$appid))->save(array('status'=>6));
									}
								}else{
									if($appinfo->where(array('status'=>3,'appid'=>$appid))->find()){
										$res = $appinfo->where(array('appid'=>$appid))->save(array('status'=>4));
									}
								}
								if($res !== false){
									// @addMareLog(array(
										// 'username' 		=>$personinfo['realname'],
										// 'handleurl'   	=>$url, 
										// 'handlecontent'	=>'上传测试信息',
										// 'handleresult'	=>'成功',
										// 'handleremarks'	=>'用户上传测试信息成功'
									// ));
									$this->ajaxReturn(array('code'=>'success','msg'=>C('RECORD_ADD_SUCCESS'),'stepid'=>$stepid));
								}else{
									// @addMareLog(array(
										// 'username' 		=>$personinfo['realname'],
										// 'handleurl'   	=>$url, 
										// 'handlecontent'	=>'上传测试信息',
										// 'handleresult'	=>'失败',
										// 'handleremarks'	=>'用户上传测试信息失败'
									// ));
									$this->ajaxReturn(array('code'=>'false','msg'=>C('RECORD_ADD_FALSE'),'stepid'=>$stepid));
								}
								
							// }
							// else
							// {	
							// 	// @addMareLog(array(
							// 		// 'username' 		=>$personinfo['realname'],
							// 		// 'handleurl'   	=>$url, 
							// 		// 'handlecontent'	=>'上传测试信息',
							// 		// 'handleresult'	=>'失败',
							// 		// 'handleremarks'	=>'用户上传测试信息失败'
							// 	// ));
							// 	$this->ajaxReturn(array('code'=>'fail','msg'=>C('RECORD_ADD_FALSE')));
							// }				
						}
						else
						{
							// @addMareLog(array(
								// 'handleurl'   	=>$url, 
								// 'handlecontent'	=>'上传测试信息',
								// 'handleresult'	=>'失败',
								// 'handleremarks'	=>'用户上传测试信息失败'
							// ));
							//不存在该用户
							$this->ajaxReturn(array('code'=>'fail','msg'=>C('USER_NOT_EXIST')));
						}
					}
					else	
					{
						// @addMareLog(array(
							// 'handleurl'   	=>$url, 
							// 'handlecontent'	=>'上传测试信息',
							// 'handleresult'	=>'失败',
							// 'handleremarks'	=>'用户上传测试信息失败'
						// ));
						$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATA_VALIDATION_ERROR')));
					}
				// }
				// else
				// {
					//@addMareLog(array(
						// 'userid'			=>$userid,
						// 'handlecontent'	=>C('DATE_TIME_REPEAT').": ".json_encode(I('post.')).'，'.$url
						// ));
					// 	$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATE_TIME_REPEAT')));
				// }
			}
			else
			{
				// @addMareLog(array(
					// 'handleurl'   	=>$url, 
					// 'handlecontent'	=>'上传测试信息',
					// 'handleresult'	=>'失败',
					// 'handleremarks'	=>'用户上传测试信息失败'
				// ));
				$this->ajaxReturn(array('code'=>'fail','msg'=>C('NEED_PARAMETER')));
			}			
		}
		else
		{
			// @addMareLog(array(
				// 'handleurl'   	=>$url, 
				// 'handlecontent'	=>'上传测试信息',
				// 'handleresult'	=>'失败',
				// 'handleremarks'	=>'用户上传测试信息失败'
			// ));
			$this->ajaxReturn(array('code'=>'fail','msg'=>C('METHOD_TIP')));
		}
	}


	//添加ios测试步骤数据和截图
	public function saveiosrecord()
	{
		if(IS_POST){
			$userid 			= I('post.userid');
			$appid 				= I('post.appid');
			$time 				= I('post.time');
			$image 				= I('post.img');
			$rinfo 				= I('post.info');
			$stepid 			= I('post.stepid');
			$deteclist 			= I('post.deteclist');
			$timestamp 			= I('post.timestamp');
			$currentstate		= I('post.currentstate');
			$nonce 				= I('post.nonce');
			$usertoken 			= D('Usertoken');
			$timestamptb		= D('Timnonce');
			$user 				= D('User');
			$appinfo 			= D('Appinfo');
			$vulinfo 			= D('Vulinfo');
			$exploit_db 		= D("ExploitDb");

			$url   				= MODULE_NAME.'/'.CONTROLLER_NAME.'/saverecord';
			if(!empty($userid) && !empty($appid) && !empty($time) && !empty($rinfo) && !empty($stepid) && !empty($deteclist) && !empty($timestamp)&& !empty($nonce))
			{

				$header  		= getallheaders();
				$gettoken 		= $usertoken->where(array('userid'=>$userid))->order('timestamp DESC')->limit(1)->select()[0];
				//判断是否登录超时
				// $this->overtime($gettoken['timestamp']);
				// if(!($timestamptb->where(array('timestamp'=>$timestamp,'nonce'=>$nonce))->select()))
				// {
				// 	@$timestamptb->data(array('timestamp'=>$timestamp,'nonce'=>$nonce))->add();

					$usertoken->where(array('id'=>$gettoken['id']))->save(array('timestamp'=>time()));
					if($header['signature'] == md5($userid.$appid.$stepid.$time.$image.$rinfo.$deteclist.$timestamp.$nonce.$nonce.$gettoken['token']))
					{
						if($user->where(array('userid'=>$userid))->select())
						{
							//用户信息
							@$personinfo 		= $user->find($userid);
							if(is_null($image)){
								$this->ajaxReturn(array('code'=>'false','info'=>C('IMAGE_IS_NULL')));
							}
							if(is_null($rinfo)){
								$this->ajaxReturn(array('code'=>'false','info'=>C('WORD_IS_NULL')));
							}
							//目录 不存在则创建
							$dir = UPLOAD_PATH.'Step/'.date('Ymd');
							if(!is_dir($dir)){
								$dirres = mkdir($dir,0755);
							}
							//写入图片文件
							$filepath = UPLOAD_PATH.'Step/'.date('Ymd').'/'.createRand(18).time().'.png';
							$fp = fopen($filepath,'w+');
							if($fp){
								$imagebc = fwrite($fp,base64_decode($image));
					 			fclose($fp);
							}else{
								$this->ajaxReturn(array('code'=>'false','info'=>'不能写入图片文件'));
							}
						 	if($imagebc !== false){
								$tmp 		= explode(',', $deteclist);
								$count1 	= $vulinfo->count();
								foreach ($tmp as $k => $v) {
									//hash值等于 appid+detectionid+stepid+issues_img_url+issues_user_input的md5值
									$issues_name = $exploit_db->where(array('id'=>$v))->find()['vulriskname'];
									$issues_hash = md5($appid.$v.$stepid.$filepath.$rinfo);
									if(!$vulinfo->where(array('issues_hash'=>$issues_hash))->select()){
										$vulrisklevel = $exploit_db->where(array('id'=>$v))->find()['vulrisklevel'];
										$data 	= array(
											'appid'				=> $appid,
											'issues_user_input'	=> $rinfo,
											'stepid'			=> $stepid,
											'detectionid'		=> $v,
											'issues_severity'	=> $vulrisklevel,
											'issues_img_url'	=> $filepath,
											'issues_name'		=> $issues_name,
											'issues_hash'		=> $issues_hash
										);
										@$vulinfo->data($data)->add();
									}else{
										$this->ajaxReturn(array('code'=>'false','msg'=>C('HASH_REPERT')));
									}
								}
							}else{
								// @addMareLog(array(
									// 'username' 		=>$personinfo['realname'],
									// 'handleurl'   	=>$url, 
									// 'handlecontent'	=>'上传测试信息',
									// 'handleresult'	=>'失败',
									// 'handleremarks'	=>'用户上传测试信息失败'
								// ));
								$this->ajaxReturn(array('code'=>'fail','msg'=>C('PERMISSION_NOT_ENOUGH')),'',JSON_UNESCAPED_UNICODE);
							}
							$count2 	= $vulinfo->count();

							if($count2-$count1 == count($tmp))
							{
								@$timestamptb->data(array('timestamp'=>$timestamp,'nonce'=>$nonce))->add();
								if($currentstate == 1){
									if($appinfo->where(array('status'=>array(array('egt',3),array('lt',6),'and'),'appid'=>$appid))->find()){
										$res = $appinfo->where(array('appid'=>$appid))->save(array('status'=>6));
									}
								}else{
									if($appinfo->where(array('status'=>3,'appid'=>$appid))->find()){
										$res = $appinfo->where(array('appid'=>$appid))->save(array('status'=>4));
									}
								}
								if($res !== false){
									// @addMareLog(array(
										// 'username' 		=>$personinfo['realname'],
										// 'handleurl'   	=>$url, 
										// 'handlecontent'	=>'上传测试信息',
										// 'handleresult'	=>'成功',
										// 'handleremarks'	=>'用户上传测试信息成功'
									// ));
									$this->ajaxReturn(array('code'=>'success','msg'=>C('RECORD_ADD_SUCCESS'),'stepid'=>$stepid));
								}else{
									// @addMareLog(array(
										// 'username' 		=>$personinfo['realname'],
										// 'handleurl'   	=>$url, 
										// 'handlecontent'	=>'上传测试信息',
										// 'handleresult'	=>'失败',
										// 'handleremarks'	=>'用户上传测试信息失败'
									// ));
									$this->ajaxReturn(array('code'=>'false','msg'=>C('RECORD_ADD_FALSE'),'stepid'=>$stepid));
								}
							}
							else
							{
								// @addMareLog(array(
									// 'username' 		=>$personinfo['realname'],
									// 'handleurl'   	=>$url, 
									// 'handlecontent'	=>'上传测试信息',
									// 'handleresult'	=>'失败',
									// 'handleremarks'	=>'用户上传测试信息失败'
								// ));
								$this->ajaxReturn(array('code'=>'fail','msg'=>C('RECORD_ADD_FALSE')));
							}
			
						}
						else
						{
							// @addMareLog(array(
								// 'handleurl'   	=>$url, 
								// 'handlecontent'	=>'上传测试信息',
								// 'handleresult'	=>'失败',
								// 'handleremarks'	=>'用户上传测试信息失败'
							// ));
							//不存在该用户
							$this->ajaxReturn(array('code'=>'fail','msg'=>C('USER_NOT_EXIST')));
						}
					}
					else	
					{
						// @addMareLog(array(
							// 'handleurl'   	=>$url, 
							// 'handlecontent'	=>'上传测试信息',
							// 'handleresult'	=>'失败',
							// 'handleremarks'	=>'用户上传测试信息失败'
						// ));
						$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATA_VALIDATION_ERROR')));
					}
				// }
				// else
				// {
					// @addMareLog(array(
					// 	'handleurl'   	=>$url, 
					// 	'handlecontent'	=>'上传测试信息',
					// 	'handleresult'	=>'失败',
					// 	'handleremarks'	=>'用户上传测试信息失败'
					// ));
					// 	$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATE_TIME_REPEAT')));
				// }
			}
			else
			{
				// @addMareLog(array(
					// 'handleurl'   	=>$url, 
					// 'handlecontent'	=>'上传测试信息',
					// 'handleresult'	=>'失败',
					// 'handleremarks'	=>'用户上传测试信息失败'
				// ));
				$this->ajaxReturn(array('code'=>'fail','msg'=>C('NEED_PARAMETER')));
			}			
		}
		else
		{
			// @addMareLog(array(
				// 'handleurl'   	=>$url, 
				// 'handlecontent'	=>'上传测试信息',
				// 'handleresult'	=>'失败',
				// 'handleremarks'	=>'用户上传测试信息失败'
			// ));
			$this->ajaxReturn(array('code'=>'fail','msg'=>C('METHOD_TIP')));
		}
	}
	//request请求入库
	public function requestin()
	{

		$header  		= getallheaders();
		$usertoken 		= D('Usertoken');
		$appinfo 		= D('Appinfo');
		$timestamptb 	= D('Timnonce');
		$user 			= D('User');
		$requesttemp 	= D('Requesttemp');

		$url   			= "URL:".__CONTROLLER__.'/requestin';
		$model			= new \Think\Model();
		$model->startTrans();
		//判断传输过来的数据是否是json数据
		//接收到的数据
		$jsondata			= $GLOBALS['HTTP_RAW_POST_DATA'];
		if($jsondata)
		{
			//判断头部的signature是否等于传过来的json数据,随机数,token数据的MD5加密值
			$requestData 		= json_decode($jsondata,true);	
			if($header['signature'] == md5($jsondata.$requestData['nonce']) )
			{
				$timestamp 		= $requestData['timestamp'];
				$nonce 			= $requestData['nonce'];
				$appid 			= $requestData['appid'];
				$clientip 		= $requestData['clientip'];
				$url 			= $requestData['url'];
				$method 		= $requestData['method'];
				$request_raw 	= $requestData['request_raw'];
				$hash 			= $requestData['hash'];
				
				if(!empty($timestamp) && !empty($nonce) && !empty($appid) && !empty($clientip) && !empty($url) && !empty($method) && !empty($request_raw) && !empty($hash))
				{
					// if(!($timestamptb->where(array('timestamp'=>$requestData['timestamp'],'nonce'=>$requestData['nonce']))->select()))
					// {

						$requestinfo 	= $appinfo->field('userid')->find($appid);
						@$personinfo 	= $user->find($requestinfo['userid']);
						// if($info['userid']){
						// 	$timetmp = $usertoken->field('timestamp')->where(array('userid'=>$requestinfo['userid']))->order('timestamp DESC')->limit(1)->select();
						// 	if($timetmp['timestamp']){
						// 		$this->overtime($timetmp['timestamp']);
						// 	}
						// }

						unset($requestData['nonce']);
						unset($requestData['timestamp']);
						$requestData['time']  = time();
						if($model->table(C('DB_PREFIX').'requesttemp')->where(array('hash'=>$hash))->select())
						{
							$this->ajaxReturn(array('code'=>'fail','msg'=>C('HASH_REPERT')));exit;
						}
						$trtemp 	= $model->table(C('DB_PREFIX').'requesttemp')->data($requestData)->add();
						if($trtemp)
						{
							$this->ajaxReturn(array('code'=>'success','msg'=>C('DATA_INSERT_SUCCESS')));
						}
						else
						{
							$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATA_INSERT_FALSE')));
						}
						

						
					// }
					// else
					// {
					// 	$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATE_TIME_REPEAT')));
					// }					
				}
				else
				{
					$this->ajaxReturn(array('code'=>'fail','msg'=>C('NEED_PARAMETER')));	
				}
			}
			else
			{
				$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATA_VALIDATION_ERROR')));
			}
		}
		else
		{
			$this->ajaxReturn(array('code'=>'fail','msg'=>C('METHOD_TIP')));
		}
	}

	//获取待检测request list
	public function requestlist()
	{


		$header  		= getallheaders();
		$appinfo 		= D('Appinfo');
		$requesttemp 	= D('Requesttemp');
		$timestamptb 	= D('Timnonce');
		$url   			= "URL:".__CONTROLLER__.'/requestlist';
		$switch			= true;//后台服务端检测开关
		if(getSetMode() == 2){
			$switch			= false;
		}
		//判断传输过来的数据是否是json数据
		//接收到的数据
		$jsondata			= $GLOBALS['HTTP_RAW_POST_DATA'];
		if($jsondata)
		{
			//判断头部的signature是否等于传过来的json数据,随机数,token数据的MD5加密值
			$requestData 		= json_decode($jsondata,true);
			$timestamp 			= $requestData['timestamp'];	
			$nonce 				= $requestData['nonce'];
			if($header['signature'] == md5($jsondata.$requestData['nonce']) )
			{
				if(!empty($timestamp) && !empty($nonce))
				{
					// if(!($timestamptb->where(array('timestamp'=>$requestData['timestamp'],'nonce'=>$requestData['nonce']))->select()))
					// {
						$appoldlist = $appinfo->field('appid,test_domain,test_serverip,scanoption')->where(array('at_appinfo.status'=>6))->limit(1)->select()[0];
						
						//var_export($appoldlist);die;

						$applist =  $appinfo->field('at_appinfo.appid,at_appinfo.userid,at_requesttemp.clientip,at_requesttemp.host,at_requesttemp.url,at_requesttemp.method,request_raw')->join('left join at_requesttemp on at_requesttemp.appid = at_appinfo.appid')->join('left join at_user on at_user.userid =  at_appinfo.userid')->where(array('at_requesttemp.status'=>0,'at_appinfo.status'=>6,'at_appinfo.appid'=>$appoldlist['appid']))->select();
						if($applist)
						{
                            $applist1 = json_encode($applist);
                            $file_paths = UPLOAD_PATH."test1.json";
                            $fh = fopen($file_paths,"w");
                            fwrite($fh,"if wai:".$applist1."\n\r");
							$newapplist = array();
							foreach ($applist as $k => $v) {
								//当获取到检测数据的时候，将检测状态置为漏扫中
								if($appinfo->where(array('status'=>6,'appid'=>$v['appid']))->find()){
                                    $applist1 = json_encode($applist);
                                    $file_paths = UPLOAD_PATH."test1.json";
                                    $fh = fopen($file_paths,"w");
                                    fwrite($fh,"if nei :2");

									@$appinfo->where(array('appid'=>$v['appid']))->save(array('status'=>7));
								}
								@$requesttemp->where(array('appid'=>$v['appid']))->save(array('status'=>1,'log'=>$_SERVER['REMOTE_ADDR']));
								
								if($v['appid'] == null){
									unset($applist[$k]);
								}
								if($v['appid'] != null){
									array_push($newapplist, $applist[$k]);
								}
							}
							 // var_export($newapplist);die;
							if (!$switch) {
								$ip = explode('|', $appoldlist['test_serverip']);
								$domain = explode('|', $appoldlist['test_domain']);
								$this->ajaxReturn(array('code'=>'success','msg'=>C('REQUEST_DATA_SUCCESS'),'content'=>$newapplist,'ip'=>$ip,'domain'=>$domain,'appid'=>$appoldlist['appid'],'scan'=>0,'scanoption'=>$appoldlist['scanoption']));
								
							}
							if(count($newapplist) == 0)
							{
								$this->ajaxReturn(array('code'=>'fail','msg'=>C('NO_DATA')));
							}
							else
							{
								$ip = explode('|', $appoldlist['test_serverip']);
								$domain = explode('|', $appoldlist['test_domain']);
								$this->ajaxReturn(array('code'=>'success','msg'=>C('REQUEST_DATA_SUCCESS'),'content'=>$newapplist,'ip'=>$ip,'domain'=>$domain,'scanoption'=>$appoldlist['scanoption']));
							}							
						}
						else
						{
							if($appoldlist != null){
								//2017.4.13如果数据为空，就将应用状态置为9，异常
								//2017.4.18 如果requesttemp数据同时为空时，就将应用状态置为9，异常
								//2017.4.27 添加requesttemp的status为0情况下数据同时为空时，就将应用状态置为9，异常
								if( $appinfo->where(array('status'=>6,'appid'=>$appoldlist['appid']))->find() && empty($requesttemp->where(array('appid'=>$appoldlist['appid'],'status'=>0))->find()) ){
								    $date = date('Y-m-d H:i:s', time());
								    $appinfo->where(array('appid'=>$appoldlist['appid']))->data(array('status'=>9,'subtime'=>$date))->save();
								}
								@$this->createTechAndManageReport($appoldlist['appid']);
							}
							
							$this->ajaxReturn(array('code'=>'fail','msg'=>C('NO_DATA'),'data'=>$applist));
						}
					// }
					// else
					// {
					// 	$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATE_TIME_REPEAT')));
					// }	
				}
				else
				{
					$this->ajaxReturn(array('code'=>'fail','msg'=>C('NEED_PARAMETER')));	
				}
			}
			else
			{
				
				$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATA_VALIDATION_ERROR')));
			}
		}
		else
		{
			$this->ajaxReturn(array('code'=>'fail','msg'=>C('METHOD_TIP')));
		}
	}

	//提交检测结果 requestreport
	public function requestreport()
	{
		$header  		= getallheaders();
		$appinfo 		= D('Appinfo');
		$timestamptb 	= D('Timnonce');
		$vulinfo 		= D('Vulinfo');
		$exploit_db		= D("ExploitDb");
		$url   			= "URL:".__CONTROLLER__.'/requestreport';
		//判断传输过来的数据是否是json数据
		//接收到的数据
		$jsondata			= $GLOBALS['HTTP_RAW_POST_DATA'];
		if($jsondata)
		{
			//判断头部的signature是否等于传过来的json数据,随机数,token数据的MD5加密值
			$requestData 		= json_decode($jsondata,true);
			$timestamp 			= $requestData['timestamp'];	
			$nonce 				= $requestData['nonce'];
			$report 			= $requestData['report'];
			$appid 				= $requestData['appid'];

			if($header['signature'] == md5($jsondata.$requestData['nonce']) )
			{
				if(!empty($timestamp) && !empty($nonce) && !empty($appid))
				{
					// if(!($timestamptb->where(array('timestamp'=>$requestData['timestamp'],'nonce'=>$requestData['nonce']))->select()))
					// {

						$requestinfo 	= $appinfo->field('userid')->find($appid);
						if($info['userid']){
							$timetmp = $usertoken->field('timestamp')->where(array('userid'=>$requestinfo['userid']))->order('timestamp DESC')->limit(1)->select();
							if($timetmp['timestamp']){
								$this->overtime($timetmp['timestamp']);
							}
						}
						/*
						状态由【vulscan_report】修改，不需要php修改状态了
						//检测完成之后，将检测的结果置为8,检测完成
						if($appinfo->where(array('status'=>array(array('egt',6),array('lt',9),'and'),'appid'=>$appid))->find()){
							@$appinfo->where(array('appid'=>$appid))->save(array('status'=>8));
						}
						*/
						//判断数据是否为空,不空就插入数据
						foreach ($report as $k => $v) {
							if($v)
							{
								$v['appid'] = $appid;
								//实体化代码issues_proof
								//2017.6.19 By-Mr.x 修复HTML前后台同时过滤问题导致显示异常
								//$v['issues_proof'] = htmlentities(htmlspecialchars($v['issues_proof'],ENT_QUOTES));
								$v['issues_proof'] = $v['issues_proof'];
								if($v['detectionid']){
									$v['issues_severity'] = $exploit_db->where(array('id'=>$v['detectionid']))->find()['vulrisklevel'];
								}
								if($vulinfo->where(array('issues_hash'=>$v['issues_hash']))->select()){
									continue;
								}else{
									@$res = $vulinfo->data($v)->add();
									if($res === false)
									{
										$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATA_INSERT_FALSE')));
										die;
									}
								}
								
							}
						}
						@$this->createTechAndManageReport($appid);
						$this->ajaxReturn(array('code'=>'success','msg'=>C('DATA_INSERT_SUCCESS')));

						
					// }
					// else
					// {
						// 	$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATE_TIME_REPEAT')));
					// }	
				}
				else
				{
					$this->ajaxReturn(array('code'=>'fail','msg'=>C('NEED_PARAMETER')));	
				}
			}
			else
			{
				$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATA_VALIDATION_ERROR')));
			}
		}
		else
		{
			$this->ajaxReturn(array('code'=>'fail','msg'=>C('METHOD_TIP')));
		}
	}

	//提交自动检测结果
	public function acreport()
	{
		$url   			= "URL:".__CONTROLLER__.'/acreport';
		$jsondata 		= $this->jsonverification('acreport');
		if($this->headerverification($jsondata,'acreport')){
			$data 		= json_decode($jsondata,true);
			$appinfo 	= D("Appinfo");
			if(!empty($data['timestamp']) && !empty($data['nonce']) && !empty($data['appid'])  && !empty($data['detectionid']) && !is_null($data['vul_state']))
			{
				$vulinfo 	= D("Vulinfo");
				$exploit_db = D('ExploitDb');
				if($data['vul_state'] == true)
				{
					$statusconfirm = 2;
				}
				else
				{
					$statusconfirm = 3;
				}

				if(is_array($data['requestraw'])){
					$requestraw1  = json_encode($data['requestraw']);
				}else{
					$requestraw1 = $data['requestraw'];
				}
				$issues_name_vulrisklevel	= $exploit_db->where(array('id'=>$data['detectionid']))->find();
				$issues_name 		= $issues_name_vulrisklevel['vulriskname'];
				$vulrisklevel 		= $issues_name_vulrisklevel['vulrisklevel'];
				$issues_hash 		= md5($data['appid'].$data['detectionid'].$requestraw1);
				if(!$vulinfo->where(array('issues_hash'=>$issues_hash))->select()){
					$res =  $vulinfo->add(array('issues_proof'=>$requestraw1,'issues_name'=>$issues_name,'statusconfirm'=>$statusconfirm,'appid'=>$data['appid'],'detectionid'=>$data['detectionid'],'issues_user_input'=>$data['info'],'issues_hash'=>$issues_hash,'issues_severity'=>$vulrisklevel));
					if($appinfo->where(array('status'=>3,'appid'=>$data['appid']))->find()){
						@$appinfo->where(array('appid'=>$data['appid']))->data(array('status'=>4))->save();
					}
					if($res !== false)
					{
						$this->ajaxReturn(array('code'=>'success','msg'=>C('DATA_INSERT_SUCCESS')));
					}
					else
					{
						$this->ajaxReturn(array('code'=>'success','msg'=>C('DATA_INSERT_FALSE')));
					}
				}else{
					if($appinfo->where(array('status'=>3,'appid'=>$data['appid']))->find()){
						@$appinfo->where(array('appid'=>$data['appid']))->data(array('status'=>4))->save();
					}
					$this->ajaxReturn(array('code'=>'false','msg'=>C('HASH_REPERT')));
				}
			}
			else
			{
				$this->ajaxReturn(array('code'=>'fail','msg'=>C('NEED_PARAMETER'),'data'=>$data));	
			}
		}
	}

	//获取测试任务
	public function tasklist()
	{
		$usertoken 		= D('Usertoken');
		$appinfo 		= D('Appinfo');
		$guidelist 		= D('Guidelist');
		$maintain 		= D('Maintain');
		$guide 			= D('Guidelist'); 
		$step 			= D('Stepinfo');
		$exploit_db 	= D("ExploitDb");
		$user 			= D('User');
		$url   			= MODULE_NAME.'/'.CONTROLLER_NAME.'/tasklist';
		$jsondata 	=	$this->jsonverification('tasklist');
		if($this->headerverification($jsondata,'tasklist',1)){
			$requestData 	= json_decode($jsondata,true);
			$token 			= $requestData['usertoken'];
			$devip 			= $requestData['devip'];
			if(!empty($requestData['timestamp']) && !empty($requestData['nonce']) && !empty($token)){
				$testuserid 	= $usertoken->where(array('token'=>$token))->find()['userid'];
				@$personinfo   	= $user->where(array('userid'=>$testuserid))->find(); 
				if($testuserid){
					$useragent=  getUserAgent();
					// if($useragent == 'ios'){
					// $tasklist 	= $appinfo->field('userid,appid,realname,package as packagename,md5,icon,subtime,status,apppath,appurl,repackageappurl,type,test_username_pwd,test_phone,task_name,tasktype,mpid,targeturl,test_domain,test_serverip')->where(array('test_userid'=>$testuserid,'status'=>array(array('egt',2),array('lt',6),'AND'),'cert'=>array('neq','NULL')))->order('uploadtime asc')->limit('1')->select();
					$tasklist 	= $appinfo->field('userid,appid,realname,package as packagename,md5,icon,subtime,status,apppath,appurl,repackageappurl,type,test_username_pwd,test_phone,task_name,tasktype,mpid,targeturl,test_domain,test_serverip,sensitive,check_url')->where(array('test_userid'=>$testuserid,'status'=>array(array('egt',2),array('lt',6),'AND'),'cert'=>array('neq','NULL')))->where("task_execute_ip=1")->where("(( LENGTH(package) > 1) or (LENGTH(package) < 1 and tasktype in ('awvs','web','wx')))")->order('uploadtime asc')->limit('1')->select();
				
					// }else{
					// 	$tasklist 	= $appinfo->field('userid,appid,realname,package as packagename,md5,icon,subtime,status,apppath,appurl,repackageappurl,type,test_username_pwd,test_phone,task_name,tasktype,mpid')->where(array('test_userid'=>$testuserid,'status'=>array(array('egt',3),array('lt',6),'AND')))->order('uploadtime asc')->limit('1')->select();
					// }
					
					$appstatus =  json_decode($maintain->where(array('key'=>'appteststatus'))->select()[0]['value'],1);

					$applist 	= array();
					foreach ($tasklist as $key => $value) {
						$applist[$key] = $value;
						if(file_exists($value['icon'])){
							$applist[$key]['icon'] = __ROOT__."/".$value['icon'];
						}else{
							$applist[$key]['icon'] = __ROOT__.'/Uploads/default.png';
						}
						if(file_exists($value['appurl'])){
							$applist[$key]['appurl'] = __ROOT__."/".$value['appurl'];
						}else{
							$applist[$key]['appurl'] =__ROOT__."/".$value['apppath'];
						}
						if(file_exists($value['repackageappurl'])){
							$applist[$key]['repackageappurl'] = __ROOT__."/".$value['repackageappurl'];
						}else{
							$applist[$key]['repackageappurl'] = '';
						}
						$applist[$key]['task_name'] =explode('_',$value['task_name'])[0];
						$applist[$key]['subtime'] =strtotime($value['subtime']);
						
						$applist[$key]['sensitive'] =json_decode($value['sensitive']);
						$applist[$key]['check_url'] =json_decode($value['check_url']);
						
						$guidelist 		= $guide->field('steplist')->where(array('platform'=>$value['tasktype']))->select()[0]['steplist'];

							if($appinfo->where(array('status'=>array('lt',3),'appid'=>$value['appid']))->find()){
								@$appinfo->data(array('status'=>3))->where(array('appid'=>$value['appid']))->save();
							
								$parameter 		= 1;
								$iosappurl 		= getcwd()."/".$value['apppath'];
								$taskuserid		= $value['userid'];
								$taskappid 		= $value['appid'];
								$iphoneaddress 	= $devip;
								if($value['tasktype'] != 'wx'){
									if($value['tasktype'] == 'ios'){
										system("/Apktest/MobAppSecAss/start_apptest 'ipa'  {$iosappurl} {$taskappid} {$taskuserid} {$parameter} {$iphoneaddress} >/dev/null &");
									}else{
										system("/Apktest/MobAppSecAss/start_apptest 'apk'  {$iosappurl} {$taskappid} {$taskuserid} {$parameter} {$iphoneaddress} >/dev/null &");
									}
								}
							}
						// }
						
						$tmpguide 		= explode(',', $guidelist);
						// sort($tmpguide);
						foreach ($tmpguide as $k => $v) {
							//是否找到该步的数据
							if($step->find($v))
							{
								$testguide[$k] = $step->find($v);
							}
							$tmpdetec = explode(',', $testguide[$k]['deteclist']);
							foreach ($tmpdetec as $key => $value) {

								// $detecnametmp  = $detecname->field('zhtestname as abbrzhname')->find($value)['abbrzhname'];
								$detecnametmp  = $exploit_db->field('vulriskname as abbrzhname')->find($value)['abbrzhname'];
								//判断该检测项名称是否为空
								if($detecnametmp != null)
								{
									if($key == 0 )
									{
										$testguide[$k]['deteclistinfo'] = 	$detecnametmp;
									}
									else
									{
										$testguide[$k]['deteclistinfo'] .= ','.$detecnametmp;
									}
								}else{
									$testguide[$k]['deteclistinfo'] = '尽情测试';
								}
							}
						}
						$stepcount  = count($testguide);
						$applist[$key]['testguide'] 	= $testguide;
						$applist[$key]['stepcount']		= $stepcount;
						$applist[$key]['statusinfo'] 	= $appstatus[$v['status']];
						$appid = $tasklist[0]['appid'];
						@$appinfo->where(array('appid'=>$appid))->data(array('task_execute_ip'=>$devip))->save();
					}
					// @addMareLog(array(
					// 	'username'		=>$personinfo['realname'],
					// 	'handleurl'   	=>$url, 
					// 	'handlecontent'	=>'获取测试任务',
					// 	'handleresult'	=>'成功',
					// 	'handleremarks'	=>'用户获取测试任务成功'
					// ));
					$this->ajaxReturn(array('code'=>'success','msg'=>C('REQUEST_DATA_SUCCESS'),'applist'=>$applist[0]));
				}else{
					// @addMareLog(array(
					// 	'username'		=>$personinfo['realname'],
					// 	'handleurl'   	=>$url, 
					// 	'handlecontent'	=>'获取测试任务',
					// 	'handleresult'	=>'失败',
					// 	'handleremarks'	=>'用户获取测试任务失败'
					// ));
					$this->ajaxReturn(array('code'=>'fail','msg'=>C('NOT_ALLOCATING_TASK')));
				}
			}
			else
			{
				// @addMareLog(array(
				// 	'handleurl'   	=>$url, 
				// 	'handlecontent'	=>'获取测试任务',
				// 	'handleresult'	=>'失败',
				// 	'handleremarks'	=>'用户获取测试任务失败'
				// ));
				$this->ajaxReturn(array('code'=>'fail','msg'=>C('NEED_PARAMETER')));
			}
		}
	}


	//验证是否是发送过来的json数据
	private function jsonverification($controllername)
	{
		$jsondata		= $GLOBALS['HTTP_RAW_POST_DATA'];
		if($jsondata)
		{
			return $jsondata;
		}
		else
		{
			$this->ajaxReturn(array('code'=>'fail','msg'=>C('METHOD_TIP')));
		}
	}

	//请求头签名和body+none+token的md5值验证
	private function headerverification($jsondata,$controllername,$token = false)
	{
		$header  		= getallheaders();
		$requestData 	= json_decode($jsondata,true);
		$userid 		= $requestData['userid'];
		if($token){
			if($requestData['userid']){
				$usertoken 	= D('Usertoken');
				$gettoken  			= $usertoken->where(array('userid'=>$userid))->order('timestamp DESC')->limit(1)->select()[0];
				$signature  = md5($jsondata.$requestData['nonce'].$gettoken['token']);
			}
			if($requestData['usertoken']){
				$signature  = md5($jsondata.$requestData['nonce'].$requestData['usertoken']);
			}
		}else{
			$signature  = md5($jsondata.$requestData['nonce']);
		}
		if(strtoupper($header['signature']) == strtoupper($signature))
		{
			return true;
		}
		else
		{
			$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATA_VALIDATION_ERROR')));
			exit;
		}
	}
	//图片验证码自动识别
	public function vcr(){
		$jsondata  	= $this->jsonverification('vcr');
		if($this->headerverification($jsondata,'vcr')){
			$requestdata = json_decode($jsondata,true);
			$mod 		 = $requestdata['mod'];
			$detectionid = $requestdata['detectionid'];
			$appid 		 = $requestdata['appid'];
			$id 		 = $requestdata['id'];
			$vcode		 = $requestdata['requestraw']['vul_info']['vcode'];
			
			
			$requestraw  = json_encode($requestdata['requestraw']);
			
			$appinfo 	 = D('Appinfo');
			$vulinfo 	 = D('Vulinfo');
			$exploit_db  = D("ExploitDb");
			// $where['detectionid'] 	= array('in','30,33,34,47');
			$where['detectionid'] 	= array('in','92,93,98,89');

			// $goalstep 				= $steps->where($where)->limit(1)->select()[0];
			$goalstep 				= $vulinfo->where($where)->where('issues_proof is null')->limit(1)->select()[0];
			// var_export($goalstep);
			if ( empty($appid) ) $appid	= $goalstep['appid'];
			
			$test_username_pwd 		= $appinfo->field('test_username_pwd')->find($appid)['test_username_pwd'];
			//$img_id 			   	= $steps->field('img,id')->where(array('appid'=>$appid,'detectionid'=>47))->find(); 
			

			if(file_exists($goalstep['issues_img_url'])){
				//$img = "http://".$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].__ROOT__."/".$goalstep['img'];
				$img = $goalstep['issues_img_url'];
			}else{
				$img ='';
			}
			
			
			// if($goalstep['detectionid'] != 47){
			if($goalstep['detectionid'] != 98){
				$firstpass 		   = explode('/',explode(',', $test_username_pwd)[0])[1];
			}else{
				$firstpass 		   = $goalstep['issues_user_input'];
			}
			
			
			if(empty($mod)){
				$content 		   = array(
						'appid'		   =>$appid,
						'id' 		   =>$goalstep['id'],
						'detectionid'  =>$goalstep['detectionid'],
						'img'		   =>$img,
						'cp_string'	   =>$firstpass				
						);

				if($goalstep){
					$this->ajaxReturn(array('code'=>'success','msg'=>C('REQUEST_DATA_SUCCESS'),'content'=>$content));
				}else{
					$this->ajaxReturn(array('code'=>'false','msg'=>C('NO_DATA')));
				}				
			}
			
			if($mod == 'imcoming'){
				//print_r($vcode);
				//print_r($appid);
				//print_r($detectionid);
				//die;
				if ( !empty($vcode) ) {
					$vulrisklevel = $exploit_db->where(array('id'=>$detectionid))->find()['vulrisklevel'];
					$res = $vulinfo->data(array('issues_proof'=>$requestraw,'statusconfirm' =>2,'issues_severity'=>$vulrisklevel))->where(array('appid'=>$appid,'detectionid'=>$detectionid))->save();
					if($res !== false){
						
						$content 		   = array(
							'appid'		   =>$appid,
							'id' 		   =>$id,
							'detectionid'  =>$detectionid,
							'img'		   =>$img,
							'cp_string'	   =>$firstpass					
							);
	
						$this->ajaxReturn(array('code'=>'success','msg'=>C('DATA_UPDATE_SUCCESS'),'content'=>$content));
					}else{
						$this->ajaxReturn(array('code'=>'false','msg'=>C('DATA_UPDATE_FALSE')));
					}
				}else
					$res = $vulinfo->data(array('issues_proof'=>$requestraw))->where(array('appid'=>$appid,'detectionid'=>$detectionid))->save();
					if($res !== false){
						
						$content 		   = array(
							'appid'		   =>$appid,
							'id' 		   =>$id,
							'detectionid'  =>$detectionid,
							'img'		   =>$img,
							'cp_string'	   =>$firstpass					
							);
	
						$this->ajaxReturn(array('code'=>'success','msg'=>C('DATA_UPDATE_SUCCESS'),'content'=>$content));
					}else{
						$this->ajaxReturn(array('code'=>'false','msg'=>C('DATA_UPDATE_FALSE')));
					}
			}
		}
	}

	

	//获取微信测试任务
	public function wxmp(){
		// $url 				= "URL:".__CONTROLLER__.'/'."wxmp";
		$url   				= MODULE_NAME.'/'.CONTROLLER_NAME.'/wxmp';
		$mpid 				= I('get.mpid');

		if($mpid)
		{
			//将这个mpid变量放在哪里
			$Appinfo 		= D('Appinfo');
			$user 			= D('User');
			$wxtask 		= $Appinfo->where(array('mpid'=>$mpid))->find()['icon'];
			$test_userid 	= $Appinfo->where(array('mpid'=>$mpid))->find()['test_userid'];
			@$personinfo 	= $user->where(array('userid'=>$test_userid))->find();
			if($wxtask){
				// @addMareLog(array(
					// 'username' 		=>$personinfo['realname'],
					// 'handleurl'   	=>$url, 
					// 'handlecontent'	=>'获取微信测试任务',
					// 'handleresult'	=>'成功',
					// 'handleremarks'	=>'用户获取微信测试任务成功'
				// ));
				echo "<html><img src='".__ROOT__."/".$wxtask."'/></html>";
			}else{
				// @addMareLog(array(
					// 'username' 		=>$personinfo['realname'],
					// 'handleurl'   	=>$url, 
					// 'handlecontent'	=>'获取微信测试任务',
					// 'handleresult'	=>'失败',
					// 'handleremarks'	=>'用户获取微信测试任务失败'
				// ));
				$this->ajaxReturn(array('code'=>'false','msg'=>C('WX_TASK_NOT_EXIST')));
			}
		}
		else
		{
			// @addMareLog(array(
				// 'handleurl'   	=>$url, 
				// 'handlecontent'	=>'获取微信测试任务',
				// 'handleresult'	=>'失败',
				// 'handleremarks'	=>'用户获取微信测试任务失败'
			// ));
			$this->ajaxReturn(array('code'=>'fail','msg'=>C('NEED_PARAMETER')));
		}
	}

	
	//应用手动检测结束
	public function apptestend(){
		$appinfo 			= D('Appinfo');
		$usertoken 			= D('Usertoken');
		$timestamptb		= D('Timnonce');
		$user 				= D('User');
		// $url   				= "URL:".__CONTROLLER__.'/'."apptestend";
		$url   				= MODULE_NAME.'/'.CONTROLLER_NAME.'/apptestend';
		$jsondata  			= $this->jsonverification('apptestend');
		if($jsondata)
		{
			//判断头部的signature是否等于传过来的json数据,随机数,token数据的MD5加密值
			$requestData 		= json_decode($jsondata,true);
			$userid 			= $requestData['userid'];
			$appid 				= $requestData['appid'];
			$header 			= getallheaders();

			if($userid)
			{
				$user 				= D('User');
				@$personinfo 		= $user->find($userid);
				$gettoken  			= $usertoken->where(array('userid'=>$userid))->order('timestamp DESC')->limit(1)->select()[0];
				//判断是否登录超时
				// $this->overtime($gettoken['timestamp']);
				@$usertoken->where(array('id'=>$gettoken['id']))->save(array('timestamp'=>time()));
				//判断头部的signature是否等于传过来的json数据,随机数,token数据的MD5加密值

				if( $header['signature'] == md5($jsondata.$requestData['nonce'].$gettoken['token']) )
				{
					//防重放
					// if(!($timestamp->where(array('timestamp'=>$requestData['timestamp'],'nonce'=>$requestData['nonce']))->select()))
					// {	
					// 	@$timestamp->data(array('timestamp'=>$requestData['timestamp'],'nonce'=>$requestData['nonce']))->add();
						//判断接收到的数据是变量是否为空

						if($userid != null && $requestData['timestamp'] != null && $requestData['nonce'] != null && $appid != null)
						{	
							//目标应用
							$goalapp = $appinfo->where(array('appid'=>$appid,'test_userid'=>$userid))->select();
							@$personinfo 	= $user->where(array('userid'=>$userid))->find();
							if($goalapp){
								if($appinfo->where(array('status'=>array(array('egt',3),array('lt',6),'and'),'appid'=>$appid))->find()){
									$res = $appinfo->where(array('appid'=>$appid))->data(array('status'=>'6'))->save();
								}
								if($res !== false){
									// @addMareLog(array(
										// 'username' 		=>$personinfo['realname'],
										// 'handleurl'   	=>$url, 
										// 'handlecontent'	=>'结束测试任务',
										// 'handleresult'	=>'成功',
										// 'handleremarks'	=>'用户结束测试任务成功'
									// ));
									$this->ajaxReturn(array('code'=>'success','msg'=>C('DATA_UPDATE_SUCCESS')));
								}else{
									// @addMareLog(array(
										// 'username' 		=>$personinfo['realname'],
										// 'handleurl'   	=>$url, 
										// 'handlecontent'	=>'结束测试任务',
										// 'handleresult'	=>'失败',
										// 'handleremarks'	=>'用户结束测试任务失败'
									// ));
									$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATA_UPDATE_FALSE')));
								}
							}else{
								// @addMareLog(array(
									// 'username' 		=>$personinfo['realname'],
									// 'handleurl'   	=>$url, 
									// 'handlecontent'	=>'结束测试任务',
									// 'handleresult'	=>'失败',
									// 'handleremarks'	=>'用户结束测试任务失败'
								// ));
								$this->ajaxReturn(array('code'=>'fail','msg'=>C('APP_NOT_EXISTS')));
							}							
						}
						else
						{
							// @addMareLog(array(
								// 'handleurl'   	=>$url, 
								// 'handlecontent'	=>'结束测试任务',
								// 'handleresult'	=>'失败',
								// 'handleremarks'	=>'用户结束测试任务失败'
							// ));
							$this->ajaxReturn(array('code'=>'fail','msg'=>C('NEED_PARAMETER')));
						}
					// }
					// else
					// {
						// @addMareLog(array(
						// 				'handleurl'   	=>$url, 
						// 				'handlecontent'	=>'结束测试任务',
						// 				'handleresult'	=>'失败',
						// 				'handleremarks'	=>'用户结束测试任务失败'
						// 			));
					// 	$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATE_TIME_REPEAT')));
					// }
				}
				else
				{
				// @addMareLog(array(
					// 'handleurl'   	=>$url, 
					// 'handlecontent'	=>'结束测试任务',
					// 'handleresult'	=>'失败',
					// 'handleremarks'	=>'用户结束测试任务失败'
				// ));
					$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATA_VALIDATION_ERROR')));
				}
			}
			else
			{
				// @addMareLog(array(
						// 'handleurl'   	=>$url, 
						// 'handlecontent'	=>'结束测试任务',
						// 'handleresult'	=>'失败',
						// 'handleremarks'	=>'用户结束测试任务失败'
					// ));
				$this->ajaxReturn(array('code'=>'fail','msg'=>C('NEED_PARAMETER')));
			}
		}
		else
		{	
			// @addMareLog(array(
				// 'handleurl'   	=>$url, 
				// 'handlecontent'	=>'结束测试任务',
				// 'handleresult'	=>'失败',
				// 'handleremarks'	=>'用户结束测试任务失败'
			// ));
			$this->ajaxReturn(array('code'=>'fail','msg'=>C('METHOD_TIP')));
		}
	}
	//生成技术报告pdf
	
    public function makeTechReport(){
        $model                  = D("Appinfo");
        // $vulinfo                = D("Vulinfo");
        $analysisResults 		= D('AnalysisResults');
        $apptoken               = I('get.apptoken');
        $serverity 				= I('get.serverity');
        $appid 					= $model->where(array('apptoken'=>$apptoken))->find()['appid'];
        $info                   = $model->where(array('appid'=>$appid))->find();
        
        if ($info['internet_security_level']) {
            $Internet_security_level = get_security_level($info['internet_security_level']);
            $where['standard'] = ['like',"%".C('SECURITY_LEVEL_LIKE').$Internet_security_level."%"];
        }

        if($serverity){
        	$serverity = explode('_', $serverity);
        	if(in_array('4', $serverity)){
	        	$histogram['0'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
        	}else{
        		$histogram['0']  	= 0;
        	}

        	if(in_array('3', $serverity)){
        		$histogram['1'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 3))->sum('issues_count');
        	}else{
        		$histogram['1']  	= 0;
        	}
        	
        	if(in_array('2', $serverity)){
	        	$histogram['2'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 2))->sum('issues_count');
        	}else{
        		$histogram['2']  	= 0;
        	}
    	 	$info['bugs'] 				= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level'=>array('in',$serverity)))->sum('issues_count');
    	 	// var_export(array('appid'=>$appid,'risk_level'=>array('in',$serverity)));die;
    	 	if(in_array('4', $serverity)){
	        	$info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
	    	}
	    	if(!$info['gaowei']){
	    		$info['gaowei'] 		= 0;
	    	}

         	$detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('in',$serverity)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
         	$detecresultinfo = get_detection_arr($detecresultinfo, $appid,$serverity);
        }else{
	        $info['bugs']           = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2) ))->sum('issues_count');
	        $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>4 ))->sum('issues_count');
	        $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
	        //高危漏洞类型数量
	        if(!$info['gaowei']){
	        	$info['gaowei'] 		= 0;
	        }
	        $histogram['0'] 		= $info['gaowei'];
	        //中危漏洞类型数量
	        $histogram['1'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>3 ))->sum('issues_count');
	        // var_export($histogram['mid']);die;
	        //低危漏洞类型数量
	        $histogram['2'] 			= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>2 ))->sum('issues_count');
    	}
    	
    	if ($info['internet_security_level']) {
        	$pass_count = 0;
        	$no_pass_count = 0;
        	foreach ($detecresultinfo as $key_i => &$value_i) {
        	    if ($value_i['risk_level'] == 1) {
        	        $pass_count += 1;
        	    } else {
        	        $no_pass_count += 1;
        	    }
        	    $value_i['standard'] = get_vul_result($value_i['standard'], 'html', $Internet_security_level);
        	}
        	$detecresultinfo = array_sort($detecresultinfo, 'standard', 'asc');
    	}
    	
        $zh_to_num          = array('暂未评级'=>'0','通过'=>1,'低'=>2,'中'=>3,'高'=>4);
        $num_to_zh          = array('0'=>'暂未评级','1'=>'通过','2'=>'低','3'=>'中','4'=>'高');
       	
        // $detecresultinfo = list_sort_by($detecresultinfo, 'risk_level', $sortby = 'desc');
        //设置中文编码
        
        $rulelist = array(
        	'sql'                               =>'SQL注入',
		    'xxe'                               =>'XML注入',
		    'code_injection'                    =>'代码注入',
		    'os_cmd_injection'                  =>'系统命令执行',
		    'xss'                               =>'XSS漏洞',
		    'backup_file'                       =>'备份文件检测',
		    'csrf'                              =>'csrf漏洞',
		    'file_inclusion'                    =>'文件包含漏洞',
		    'path_traversal'                    =>'文件下载漏洞',
		    'directory_listing'                 =>'目录遍历',
		    'backdoors'                         =>'后门检测',
		    'source_code_disclosure'            =>'源代码泄漏',
		    'sensitive_info'                    =>'信息泄漏',
		    'weaker_ciphers'                    =>'弱口令检测'
		);
        //页眉内容
        //第一页
        $html = '
<!DOCTYPE html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title>报告</title>
                <style type="text/css">
                *{
                    margin:0;
                    padding: 0;
                }
                html,body{
                    background-color: #777777;
                }
                .table{
                    border: 1px solid;
                    border-collapse: collapse;
                    width: 100%;
                    height: auto;
                    font-size: 1.2rem;
                    line-height: 1.4;
                    margin-bottom: 80px;
                }
                .table tr {
                    border: 1px solid #cbcbcb;
                    border-collapse: collapse;
                }
                .table tr td {
                    border: 1px solid #cbcbcb;
                    border-collapse: collapse;
                    padding: 5px 10px;
                }
                .table tr td p{
                    word-break: break-word;
                    page-break-inside:avoid;
                }
                .container{
                    width: 1024px;
                    margin-left: auto;
                    margin-right: auto;
                    background-color: #ffffff;
                    border-left: 1px solid #e1e1e1;
                    border-right: 1px solid #e1e1e1;
                    padding-top: 50px;
                    padding-bottom: 100px;
                    padding-left: 30px;
                    padding-right: 30px;
                }
                .top-title{
                    position: relative;
                    padding-top: 50px;
                    min-height: 190px;
                }
                .top-title > h1{
                    margin-bottom: 30px;
                }
                .top-title > h1, .top-title > h2{
                    text-align: center;
                }
                .sub-title{
                    position: absolute;
                    left: 50%;
                    transform: translateX(-50%);
                    -webkit-transform: translateX(-50%);
                    -moz-transform: translateX(-50%);
                }
                .sub-title .sub-text{
                    float: left;
                    margin-left: 10px;
                }
                .sub-title .sub-img{
                    float: left;
                }
                .report-section{
                    margin-bottom: 50px;
                }
                .report-content .report-section .main-title{
                    margin-bottom: 30px;
                    margin-top: 80px;
                    padding: 5px 15px 5px 5px;
                    background-color: #2196F3;
                    border-top-right-radius: 20px;
                    border-bottom-right-radius: 20px;
                    -webkit-border-top-right-radius: 20px;
                    -webkit-border-bottom-right-radius: 20px;
                    float: left;
                    color: #ffffff;
                }
                .report-content .report-section .sec-wrapper{
                    position: relative;
                    padding-left: 35px;
                    margin-top: 10px;
                    margin-bottom: 5px;
                }
                .badge{
                    position: absolute;
                    display: block;
                    width: 25px;
                    height: 25px;
                    background-color: #8BC34A;
                    border-radius: 50%;
                    -webkit-border-radius: 50%;
                    top: -3px;
                    left: 0;
                    -webkit-user-select:none;
                    cursor: default;
                }
                .badge:after{
                    display: block;
                    content: "\25BC";
                    color: #ffffff;
                    padding: 5px;
                }
                .clearfix{
                    clear: both;
                    display: block;
                    height: 0;
                }
                .level{
                    display: inline-block;
                    padding: 10px;
                    border-radius: 50%;
                    background-color: #e1e1e1;
                    color: #ffffff;
                    line-height: 1;
                }
                .low{
                    background-color: #377aa6;
                }
                .middel{
                    background-color: #FF9800;
                }
                .safe{
                    background-color: #4fca81;
                }
                .high{
                    background-color: #f44336;
                }
                .report-footer{
                    border-top: 1px solid #e1e1e1;
                    padding-top: 10px;
                }
    			.detailtable {
				  max-width: 100%;
				  width: 100%;
				  margin-bottom: 60px;
				  border: 1px solid black;
				  word-break: break-all;
				  border-collapse: collapse;
				}
                </style>
            </head>
            <body>
            <div class="container">
                <div class="top-title">';
        
        $html .= '<h1>应用安全检测技术报告</h1>
                        <div class="sub-title">';
        if(file_exists($info['icon'])){
            $img = __ROOT__.'/'.$info['icon'];
        }
        if($img){
            if($info['tasktype'] == 'wx'){
                $html .= '<div class="sub-img"><img src="'.$img.'" style="width:150px;"/></div><br/>';
            }else{
                $html .= '<div class="sub-img"><img src="'.$img.'" style="width:150px;" /></div>';
            }
        }
        
        
        $html .= '<div class="sub-text">';
        if($info['realname']){
            $thml .= '<h2>'.$info['realname'].'</h2>';
        }
        $html .= '<h3>';
        if($info['tasktype'] == 'ios'){
            $html .= "iOS版";
        }elseif($info['tasktype'] == 'wx'){
            $html .= "微信安全测试";
        }elseif($info['tasktype'] == 'android'){
            $html .= "Android版";
        }else{
            $html .= "WEB扫描";
        }
        $html .='</h3>';
        if($info['version']){
            $html .='<h3>'.$info['version'].'</h3>';
        }
        
        $html .='<h3>'.$info['subtime'].'</h3>';
        $html .='</div>
                    </div>
                </div>
            
                <!-- 包裹内容 -->
                <div class="report-content">
                    <!-- 每一块内容Start -->
                    <div class="report-section">
                        <h2 class="main-title">一、基本信息</h2>
                        <div class="clearfix">&nbsp;</div>';
                        $html .= report_product_ino();
                        $html .='<table class="table">
                            <tbody>';
        if($info['tasktype'] == 'android' || $info['tasktype'] == 'ios' ){
            $html .= '<tr>
                                    <td style="width:100px;">应用名称</td>
                                    <td>'.$info['realname'].'</td>
                                    <td>版本号</td>
                                    <td>'.$info['version'].'</td>
                                </tr>
                                <tr>
                                    <td>包名</td>
                                    <td>'.$info['package'].'</td>
                                    <td>时间</td>
                                    <td>'.$info['subtime'].'</td>
                                </tr>';
        }elseif($info['tasktype'] == 'web' || $info['tasktype'] == 'awvs'){
            $html .= '<tr>
                                        <td>目标网站</td>
                                        <td colspan="3">'.$info['targeturl'].'</td>
                                    </tr>';
            
            if($info['tasktype'] == 'awvs'){
                $scanoption = json_decode($info['scanoption'],1);
                $checkslen = count($scanoption['checks']);
                if($checkslen >0){
                    $html .='<tr>
                                        <td style="width:20%;">扫描规则</td>
                                        <td colspan="3" style="word-wrap:break-word; word-break:normal;">';
                    foreach ($scanoption['checks'] as $keyck => $valck) {
                        $html .= $rulelist[$valck];
                        if($keyck != ($checkslen -1)){
                            $html.=",";
                        }
                    }
                    $html.='</td>
                                    </tr>';
                }
            }
        }
        
        $html.= '<tr><td>高危风险</td>
                                    <td>'.$info['gaowei'].'</td>
                                    <td>风险数</td>
                                    <td>'.$info['bugs'].'</td>
                                </tr>';
        if($info['tasktype'] == 'android' || $info['tasktype'] == 'ios'){
            $html .='<tr>
                                        <td>MD5</td>
                                        <td colspan="3">'.$info['md5'].'</td>
                                    </tr>
                                    <tr>
                                        <td>SHA-1</td>
                                        <td colspan="3">'.$info['sha1'].'</td>
                                    </tr>
                                    <tr>
                                        <td>SHA-256</td>
                                        <td colspan="3">'.$info['sha256'].'</td>
                                    </tr>';
            if($info['tasktype'] != 'ios'){
                $html .='<tr>
                                            <td>证书信息</td>
                                            <td colspan="3">'.$info['cert'].'</td>
                                        </tr>';
            }
            
        }
        $html .='</tbody>
                        </table>
                    </div>
                    <!-- 每一块内容END -->
                    ';
        
        if($histogram['0'] != false || $histogram['1'] != false || $histogram['2'] != false){
            
            makepiechart($histogram,$info['realname']);
            makebarchart($histogram,$info['realname']);
            // var_dump($histogram,$info['realname']);
            $html .='<img src="'.__ROOT__."/".UPLOAD_PATH . "temp/" . $info['realname']."bar.png".'" style="width:512px;height:384px;"/><img src="'.__ROOT__."/".UPLOAD_PATH . "temp/" . $info['realname']."pie.png".'" style="width:512px;height:384px;"/>';
            if ($info['internet_security_level']) {
            $title_str = get_security_level_title($info['internet_security_level'], $pass_count, $no_pass_count);
            $html .='<div class="report-section">
                            <h2 class="main-title">二、检测概述</h2>
							<div class="clearfix">&nbsp;</div>
                            <div style="margin-bottom:10px;font-size:20px;">'.$title_str.'</div>
                            <div class="clearfix">&nbsp;</div>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>序号</td>
                                        <td>检测项目</td>
                                        <td>对标</td>
                                        <td>检测结果</td>
                                        <td>危险系数</td>
                                    </tr>';
            
            $html2 .= '<div class="report-section">
                                <h2 class="main-title">三、详细检测结果</h2>
                                <div class="clearfix">&nbsp;</div>';
            

            foreach ($detecresultinfo as $key => $value) {
                
                $html .='<tr>
                                        <td style="text-align:center;">'.($key+1).'</td>
                                        <td>'.$value['case_name'].'</td>
                                        <td>'.trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE')))).'</td>
                                        <td style="text-align:center;">';
                if($value['risk_level'] == 1){
                    if($value['issues_count']) {
                        $html.= '符合(发现'.$value['issues_count'].'处)';
                    } else {
                        $html .= '符合';
                    }   
                } else {
                    $html.= '不符合(发现'.$value['issues_count'].'处)';
                }
                $html .= '</td>
                                        <td style="text-align:center;"><span class="level ';
                if($value['risk_level'] == 1) {
                    $value['risk_level'] = $value['vulrisklevel'];
                }
                if($value['risk_level'] == 3){
                    $html .= 'middel';
                }elseif($value['risk_level'] == 4){
                    $html .= 'high';
                }elseif($value['risk_level'] == 2){
                    $html .= 'low';
                }else{
                    $html .= 'safe" style="font-size:12px;';
                }
                $html .= '">'.$num_to_zh[$value['risk_level']].'</span></td>
                                </tr>';
                
                $html2 .= '<div class="sec-wrapper">
                                <span class="badge"></span>
                                <h3 class="sec-title">'.($key+1).'、 '.$value['vulriskname'].'</h3>
                            </div>';
                $detection_process = str_replace(array("[.]","[img]","[/img]","&lt;bbr&gt;",'&lt;bbr/&gt;'),array("\"","<img",">","<b>","</b>"),str_replace(array("\r","\r\n","\n","<!--","-->"),"<br/>", htmlspecialchars($value['detection_process'])));
                $suggestions = str_replace(array("[.]","[img]","[/img]"),array("\"","<img",">"),str_replace(array("\r","\r\n","\n"),"<br/>",$value['suggestions']));
                
                if($detection_process == 'N/A') {
                    $detection_process = '无';
                }
                
                if($suggestions== 'N/A') {
                    $suggestions = '无';
                }
                
                // if(!$detection_process){
                // $detection_process = '空';
                // }
                if($value['detectionid'] == 44 || $value['detectionid'] == 142){
                    if($value['detectionid'] == 44) {
                        $arr=explode("相关内容：",$detection_process);
                    } else if($value['detectionid'] == 142) {
                        $arr=explode("请求URL：",$detection_process);
                    }
                    
                    $temp=explode("####",$arr[1]);
                    
                    if($value['detectionid'] == 44) {
                        $arr[1] = $temp[0];
                        $arr[1]=str_replace("<br/>", "", $arr[1]);
                        $arr[2]=str_replace("<br/><br/>", "<br/>", $temp[1]);
                        //                                 $arr[2] = $temp[1];
                        //                                 $inner_data=json_decode(htmlspecialchars_decode($arr[1]));
                        $inner_data=json_decode(htmlspecialchars_decode($arr[1]));
                    } else if($value['detectionid'] == 142) {
                        $arr[1] = $temp;
                        foreach ($arr[1] as $k => $v) {
                            $v=str_replace("<br/>", "", $v);
                            $v=str_replace("<br/><br/>", "<br/>", $v);
                            $arr_list = json_decode(htmlspecialchars_decode($v));
                            foreach ($arr_list as $k1 => $v1) {
                                $inner_data[] = $v1;
                            }
                            unset($arr_list);
                        }
                    }
                    if ($inner_data) {
                        $html2 .='<table class="detailtable table">
                                <tbody><tr>
                                    <td colspan="2">用例名称</td>
                                    <td colspan="6">'.$value['case_name'].'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" >对标</td>
                                    <td colspan="6" >'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars(trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE')))))).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" >风险描述</td>
                                    <td colspan="6">'.$value['risk_description'].'</td>
                                </tr>
                                <tr>
                                    <td colspan="2">检测过程</td>
                                    <td colspan="6">';
                        
                        
                        $html2 .=$arr[0].' </td></tr>
    		                                	 			<tr>
    					                                	<td style="width:6%;">序号</td>
    		                                	 			<td style="width:14%;">域名</td>
    					                                	<td style="width:30%;">url</td>
    		                                	 			<td style="width:20%;">IP列表</td>
    		                                	 			<td style="width:17%;">注册域名商</td>
    		                                	 			<td style="width:16%;">DNS解析记录</td>
    					                                	</tr>';
                        
                        foreach ($inner_data as $k=>$v){
                            $html2 .='<tr>
    			                                	<td style="width:6%;">'.($k+1).'</td>
                                    	 			<td style="width:14%;">'.$v->host.'</td>
    			                                	<td style="width:30%;">';
                            
                            foreach ($v->url as $k1=>$v1){
                                $html2 .=$v1."<br><br>";
                            }
                            $html2 .='</td>
                                    	 		 	  <td style="width:20%;">';
                            
                            foreach ($v->ip_list as $k1=>$v1){
                                $html2 .=$v1."<br>";
                            }
                            $html2 .='</td>
                                    	 			  <td style="width:17%;">'.$v->register.'</td>
                                    	 			  <td style="width:16%;">';
                            
                            foreach ($v->dns_history as $k1=>$v1){
                                $html2 .=$v1."<br>";
                            }
                            
                            $html2 .='</td></tr>';
                            
                        }
                        if($value['detectionid'] == 44) {
                            if (trim($arr[2])) {
                                $html2 .='<tr><td colspan="8">'.trim($arr[2]).'</td></tr>';
                            }
                        }
                        
                    } else {
                        $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td style="width:20%;" colspan="2">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">对标</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars(trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE')))))).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
                        if($detection_process){
                            $html2 .='<tr>
		                        <td style="width:20%;" colspan="2">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
                        }
                    }
                    unset($inner_data);
                }else {
                    $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td style="width:20%;" colspan="2">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">对标</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars(trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE')))))).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
                    if($detection_process){
                        $html2 .='<tr>
		                        <td style="width:20%;" colspan="2">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
                    }
                }
                $html2 .='<tr>
                                <td colspan="2" >风险等级</td>
                                <td colspan="6" >'.$num_to_zh[$value['risk_level']].'</td>
                            </tr>
                            <tr>
                                <td colspan="2" >修复建议</td>
                                <td colspan="6">'.$suggestions.'</td>
                            </tr></tbody></table>';
                $detection_process = null;
            }
            } else {
                $html .='<div class="report-section">
                            <h2 class="main-title">二、检测概述</h2>
                            <div class="clearfix">&nbsp;</div>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>序号</td>
                                        <td>检测项目</td>
                                        <td>检测类型</td>
                                        <td>风险数</td>
                                        <td>危险系数</td>
                                    </tr>';
                
                $html2 .= '<div class="report-section">
                                <h2 class="main-title">三、详细检测结果</h2>
                                <div class="clearfix">&nbsp;</div>';
                
                
                foreach ($detecresultinfo as $key => $value) {
                    
                    $html .='<tr >
                                        <td style="text-align:center;">'.($key+1).'</td>
                                        <td>'.$value['case_name'].'</td>
                                        <td>'.$value['hvtype'].'</td>
                                        <td style="text-align:center;">';
                    if($value['issues_count']){
                        $html .= $value['issues_count'];
                    }else{
                        $html .= '-';
                    }
                    $html .= '</td>
                                        <td style="text-align:center;"><span class="level ';
                    if($value['risk_level'] == 3){
                        $html .= 'middel';
                    }elseif($value['risk_level'] == 4){
                        $html .= 'high';
                    }elseif($value['risk_level'] == 2){
                        $html .= 'low';
                    }else{
                        $html .= 'safe" style="font-size:12px;';
                    }
                    $html .= '">'.$num_to_zh[$value['risk_level']].'</span></td>
                                </tr>';
                    
                    $html2 .= '<div class="sec-wrapper">
                                <span class="badge"></span>
                                <h3 class="sec-title">'.($key+1).'、 '.$value['vulriskname'].'</h3>
                            </div>';
                    $detection_process = str_replace(array("[.]","[img]","[/img]","&lt;bbr&gt;",'&lt;bbr/&gt;'),array("\"","<img",">","<b>","</b>"),str_replace(array("\r","\r\n","\n","<!--","-->"),"<br/>", htmlspecialchars($value['detection_process'])));
                    $suggestions = str_replace(array("[.]","[img]","[/img]"),array("\"","<img",">"),str_replace(array("\r","\r\n","\n"),"<br/>",$value['suggestions']));
                   
                    if($detection_process == 'N/A') {
                        $detection_process = '无';
                    }
                    
                    if($suggestions== 'N/A') {
                        $suggestions = '无';
                    }
                    
                    // if(!$detection_process){
                    // $detection_process = '空';
                    // }
                    if($value['detectionid'] == 44 || $value['detectionid'] == 142){
                        if($value['detectionid'] == 44) {
                            $arr=explode("相关内容：",$detection_process);
                        } else if($value['detectionid'] == 142) {
                            $arr=explode("请求URL：",$detection_process);
                        }
                        
                        $temp=explode("####",$arr[1]);
                        
                        if($value['detectionid'] == 44) {
                            $arr[1] = $temp[0];
                            $arr[1]=str_replace("<br/>", "", $arr[1]);
                            $arr[2]=str_replace("<br/><br/>", "<br/>", $temp[1]);
                            //                                 $arr[2] = $temp[1];
                            //                                 $inner_data=json_decode(htmlspecialchars_decode($arr[1]));
                            $inner_data=json_decode(htmlspecialchars_decode($arr[1]));
                        } else if($value['detectionid'] == 142) {
                            $arr[1] = $temp;
                            foreach ($arr[1] as $k => $v) {
                                $v=str_replace("<br/>", "", $v);
                                $v=str_replace("<br/><br/>", "<br/>", $v);
                                $arr_list = json_decode(htmlspecialchars_decode($v));
                                foreach ($arr_list as $k1 => $v1) {
                                    $inner_data[] = $v1;
                                }
                                unset($arr_list);
                            }
                        }
                        if ($inner_data) {
                            $html2 .='<table class="detailtable table">
                                <tbody><tr>
                                    <td colspan="2">用例名称</td>
                                    <td colspan="6">'.$value['case_name'].'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" >风险编号</td>
                                    <td colspan="6" >'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars($value['standard'])).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" >风险描述</td>
                                    <td colspan="6">'.$value['risk_description'].'</td>
                                </tr>
                                <tr>
                                    <td colspan="2">检测过程</td>
                                    <td colspan="6">';
                            
                            
                            $html2 .=$arr[0].' </td></tr>
    		                                	 			<tr>
    					                                	<td style="width:6%;">序号</td>
    		                                	 			<td style="width:14%;">域名</td>
    					                                	<td style="width:30%;">url</td>
    		                                	 			<td style="width:20%;">IP列表</td>
    		                                	 			<td style="width:17%;">注册域名商</td>
    		                                	 			<td style="width:16%;">DNS解析记录</td>
    					                                	</tr>';
                            
                            foreach ($inner_data as $k=>$v){
                                $html2 .='<tr>
    			                                	<td style="width:6%;">'.($k+1).'</td>
                                    	 			<td style="width:14%;">'.$v->host.'</td>
    			                                	<td style="width:30%;">';
                                
                                foreach ($v->url as $k1=>$v1){
                                    $html2 .=$v1."<br><br>";
                                }
                                $html2 .='</td>
                                    	 		 	  <td style="width:20%;">';
                                
                                foreach ($v->ip_list as $k1=>$v1){
                                    $html2 .=$v1."<br>";
                                }
                                $html2 .='</td>
                                    	 			  <td style="width:17%;">'.$v->register.'</td>
                                    	 			  <td style="width:16%;">';
                                
                                foreach ($v->dns_history as $k1=>$v1){
                                    $html2 .=$v1."<br>";
                                }
                                
                                $html2 .='</td></tr>';
                                
                            }
                            if($value['detectionid'] == 44) {
                                if (trim($arr[2])) {
                                    $html2 .='<tr><td colspan="8">'.trim($arr[2]).'</td></tr>';
                                }
                            }
                            
                        } else {
                            $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td style="width:20%;" colspan="2">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">风险编号</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars($value['standard'])).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
                            if($detection_process){
                                $html2 .='<tr>
		                        <td style="width:20%;" colspan="2">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
                            }
                        }
                        unset($inner_data);
                    }else {
                        $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td style="width:20%;" colspan="2">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">风险编号</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars($value['standard'])).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
                        if($detection_process){
                            $html2 .='<tr>
		                        <td style="width:20%;" colspan="2">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
                        }
                    }
                    $html2 .='<tr>
                                <td colspan="2" >风险等级</td>
                                <td colspan="6" >'.$num_to_zh[$value['risk_level']].'</td>
                            </tr>
                            <tr>
                                <td colspan="2" >修复建议</td>
                                <td colspan="6">'.$suggestions.'</td>
                            </tr></tbody></table>';
                    $detection_process = null;
                }
            }
            $html .='</tbody>
                                </table>
                            </div>';
        }
        $html2 .='</div>
            
                </div>
                <!-- 包裹内容END -->
            </div>
            </body>
        </html>';
        echo $html.$html2;
    }
    //生成管理报告pdf

    public function makeManagehReport(){
        $model                  = D("Appinfo");
        $analysisResults 		= D('AnalysisResults');
        $serverity 				= I('get.serverity');
        $apptoken               = I('get.apptoken');
        $appid 					= $model->where(array('apptoken' => $apptoken))->find()['appid'];
        $info                   = $model->where(array('appid'=>$appid))->find();

       
        // $info['bugs'] 			= $analysisResults->where(array('appid'=>$appid))->sum('issues_count');

        // $info['gaowei'] 		= $analysisResults->where(array('appid'=>$appid,'risk_level'=>4))->sum('issues_count');

       
        // $detecresultinfo        = $analysisResults->field('at_analysis_results.*,at_exploit_db.vulriskname,at_exploit_db.hvtype')->where(array('appid'=>$appid,'risk_level'=>array('egt',2)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
        // //高危漏洞类型数量
        // if(!$info['gaowei']){
        // 	$info['gaowei'] 		= 0;
        // }

        // $histogram['high'] 		= $info['gaowei'];
        
        // //中危漏洞类型数量
        // $histogram['mid'] 		= $analysisResults->where(array('appid'=>$appid,'risk_level'=>3))->sum('issues_count');
        // //低危漏洞类型数量
        // $histogram['low'] 		= $analysisResults->where(array('appid'=>$appid,'risk_level'=>2))->sum('issues_count');
        if($info['internet_security_level']) {
            $Internet_security_level = get_security_level($info['internet_security_level']);
            $where['standard'] = ['like',"%".C('SECURITY_LEVEL_LIKE').$Internet_security_level."%"];
        }
        if($serverity){
        	$serverity = explode('_', $serverity);
        	if(in_array('4', $serverity)){
	        	$histogram['0'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
        	}else{
        		$histogram['0']  	= 0;
        	}

        	if(in_array('3', $serverity)){
        		$histogram['1'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 3))->sum('issues_count');
        	}else{
        		$histogram['1']  	= 0;
        	}
        	
        	if(in_array('2', $serverity)){
	        	$histogram['2'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 2))->sum('issues_count');
        	}else{
        		$histogram['2']  	= 0;
        	}
    	 	$info['bugs'] 				= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level'=>array('in',$serverity)))->sum('issues_count');
    	 	// var_export(array('appid'=>$appid,'risk_level'=>array('in',$serverity)));die;
    	 	if(in_array('4', $serverity)){
	        	$info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
	    	}
	    	if(!$info['gaowei']){
	    		$info['gaowei'] 		= 0;
	    	}
         	$detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('in',$serverity)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
         	$detecresultinfo = get_detection_arr($detecresultinfo, $appid,$serverity);
        }else{
	        $info['bugs']           = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2) ))->sum('issues_count');
	        $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>4 ))->sum('issues_count');
	        $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
	        //高危漏洞类型数量
	        if(!$info['gaowei']){
	        	$info['gaowei'] 		= 0;
	        }
	        $histogram['0'] 		= $info['gaowei'];
	        //中危漏洞类型数量
	        $histogram['1'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>3 ))->sum('issues_count');
	        // var_export($histogram['mid']);die;
	        //低危漏洞类型数量
	         $histogram['2'] 			= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>2 ))->sum('issues_count');
    	}

    	if ($info['internet_security_level']) {
        	$pass_count = 0;
        	$no_pass_count = 0;
        	foreach ($detecresultinfo as $key_i => &$value_i) {
        	    if ($value_i['risk_level'] == 1) {
        	        $pass_count += 1;
        	    } else {
        	        $no_pass_count += 1;
        	    }
        	    $value_i['standard'] = get_vul_result($value_i['standard'], 'html', $Internet_security_level);
        	}
        	
        	$detecresultinfo = array_sort($detecresultinfo, 'standard', 'asc');
    	}
        $zh_to_num          = array('暂未评级'=>'0','通过'=>1,'低'=>2,'中'=>3,'高'=>4);
        $num_to_zh          = array('0'=>'暂未评级','1'=>'通过','2'=>'低','3'=>'中','4'=>'高');
       	
         $rulelist = array(
        	'sql'                               =>'SQL注入',
		    'xxe'                               =>'XML注入',
		    'code_injection'                    =>'代码注入',
		    'os_cmd_injection'                  =>'系统命令执行',
		    'xss'                               =>'XSS漏洞',
		    'backup_file'                       =>'备份文件检测',
		    'csrf'                              =>'csrf漏洞',
		    'file_inclusion'                    =>'文件包含漏洞',
		    'path_traversal'                    =>'文件下载漏洞',
		    'directory_listing'                 =>'目录遍历',
		    'backdoors'                         =>'后门检测',
		    'source_code_disclosure'            =>'源代码泄漏',
		    'sensitive_info'                    =>'信息泄漏',
		    'weaker_ciphers'                    =>'弱口令检测'
		);
        // $detecresultinfo = list_sort_by($detecresultinfo, 'risk_level', $sortby = 'desc');
        //设置中文编码

        //页眉内容
        //第一页
        $html = '<!DOCTYPE html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title>报告</title>
                <style type="text/css">
                *{
                    margin:0;
                    padding: 0;
                }
                html,body{
                    background-color: #777777;
					
                }
                .table{
                    border: 1px solid;
                    border-collapse: collapse;
                    width: 100%;
                    height: auto;
                    font-size: 1.2rem;
                    line-height: 1.4;
                    margin-bottom: 80px;
                }
                .table tr {
                    border: 1px solid #cbcbcb;
                    border-collapse: collapse;
                }
                .table tr td {
                    border: 1px solid #cbcbcb;
                    border-collapse: collapse;
                    padding: 5px 10px;
                }
                .table tr td p{
                    word-break: break-word;
                }
                .container{
                    width: 1024px;
                    margin-left: auto;
                    margin-right: auto;
                    background-color: #ffffff;
                    border-left: 1px solid #e1e1e1;
                    border-right: 1px solid #e1e1e1;
                    padding-top: 50px;
                    padding-bottom: 100px;
                    padding-left: 30px;
                    padding-right: 30px;

                }
                .top-title{
                    position: relative;
                    padding-top: 50px;
                    min-height: 190px;
                }
                .top-title > h1{
                    margin-bottom: 30px;
                }
                .top-title > h1, .top-title > h2{
                    text-align: center;
                }
                .sub-title{
                    position: absolute;
                    left: 50%;
                    transform: translateX(-50%);
                    -webkit-transform: translateX(-50%);
                    -moz-transform: translateX(-50%);
                }
                .sub-title .sub-text{
                    float: left;
                    margin-left: 10px;
                }
                .sub-title .sub-img{
                    float: left;
                }
                .report-section{
                    margin-bottom: 50px;
                }
                .report-content .report-section .main-title{
                    margin-bottom: 30px;
                    margin-top: 80px;
                    padding: 5px 15px 5px 5px;
                    background-color: #2196F3;
                    border-top-right-radius: 20px;
                    border-bottom-right-radius: 20px;
                    -webkit-border-top-right-radius: 20px;
                    -webkit-border-bottom-right-radius: 20px;
                    float: left;
                    color: #ffffff;
                }
                .report-content .report-section .sec-wrapper{
                    position: relative;
                    padding-left: 35px;
                    margin-top: 10px;
                    margin-bottom: 5px;
                }
                .badge{
                    position: absolute;
                    display: block;
                    width: 25px;
                    height: 25px;
                    background-color: #8BC34A;
                    border-radius: 50%;
                    -webkit-border-radius: 50%;
                    top: -3px;
                    left: 0;
                    -webkit-user-select:none;
                    cursor: default;
                }
                .badge:after{
                    display: block;
                    content: "\25BC";
                    color: #ffffff;
                    padding: 5px;
                }
                .clearfix{
                    clear: both;
                    display: block;
                    height: 0;
                }
                .level{
                    display: inline-block;
                    padding: 10px;
                    border-radius: 50%;
                    background-color: #e1e1e1;
                    color: #ffffff;
                    line-height: 1;
                }
                // .low{
                //     background-color: #00BCD4;
                // }
                // .middel{
                //     background-color: #FF9800;
                // }
                // .high{
                //     background-color: rgba(255, 0, 0,1);
                // }
                .low{
                    background-color: #377aa6;
                }
                .middel{
                    background-color: #FF9800;
                }
                .safe{
                	background-color: #4fca81;
                }
                .high{
                    background-color: #f44336;
                }
                .report-footer{
                    border-top: 1px solid #e1e1e1;
                    padding-top: 10px;
                }
                </style>
            </head>
            <body>
            <div class="container">
                <div class="top-title">';

                $html .= '<h1>应用安全检测管理报告</h1>
                        <div class="sub-title">';
                if(file_exists($info['icon'])){
                    $img = __ROOT__.'/'.$info['icon'];
                }

                if($img){
                	if($info['tasktype'] == 'wx'){
                     $html .= '<div class="sub-img"><img src="'.$img.'" style="width:150px;"/></div><br/>';
	                }else{
	                	$html .= '<div class="sub-img"><img src="'.$img.'" style="width:150px;"/></div>';
	                }
                }
               
                $html .= '<div class="sub-text">';
                if($info['realname']){
                    $thml .= '<h2>'.$info['realname'].'</h2>';
                }
                $html .= '<h3>';
                if($info['tasktype'] == 'ios'){
                    $html .= "iOS版";
                }elseif($info['tasktype'] == 'wx'){
                    $html .= "微信安全测试";
                }elseif($info['tasktype'] == 'android'){
                    $html .= "Android版";
                }else{
                	$html .= "WEB扫描";
                }
                $html .='</h3>';
                if($info['version']){
                    $html .='<h3>'.$info['version'].'</h3>';
                }
                
                $html .='<h3>'.$info['subtime'].'</h3>';
                $html .='</div>
                    </div>
                </div>

                <!-- 包裹内容 -->
                <div class="report-content">
                    <!-- 每一块内容Start -->
                    <div class="report-section">
                        <h2 class="main-title">一、基本信息</h2>
                        <div class="clearfix">&nbsp;</div>';
                        $html .= report_product_ino();
                        $html .='<table class="table">
                            <tbody>';
                          
                             if($info['tasktype'] == 'android' || $info['tasktype'] == 'ios' ){
								$html .= '<tr>
                                    <td>应用名称</td>
                                    <td>'.$info['realname'].'</td>
                                    <td>版本号</td>
                                    <td>'.$info['version'].'</td>
                                </tr>
                                <tr>
                                    <td>包名</td>
                                    <td>'.$info['package'].'</td>
                                    <td>时间</td>
                                    <td>'.$info['subtime'].'</td>
                                </tr>';
                            }elseif($info['tasktype'] == 'web' || $info['tasktype'] == 'awvs'){
                            	$html .= '<tr>
                                    	<td>目标网站</td>
                                    	<td colspan="3">'.$info['targeturl'].'</td>
	                                </tr>';
                            	if($info['tasktype'] == 'awvs'){
                            		$scanoption = json_decode($info['scanoption'],1);
	                                $checkslen = count($scanoption['checks']);
	                                if($checkslen >0){
	                                	 $html .='<tr>
	                                    <td style="width:20%;">扫描规则</td>
	                                    <td colspan="3" style="word-wrap:break-word; word-break:normal;">';
	                                    	foreach ($scanoption['checks'] as $keyck => $valck) {
	                                    		$html .= $rulelist[$valck];
	                                    		if($keyck != ($checkslen -1)){
	                                    			$html.=",";
	                                    		}
	                                    	}
	                                    $html.='</td>
	                                </tr>';
	                                }
                            	}                            	
                            }
                                $html.='<tr><td>高危风险</td>
                                    <td>'.$info['gaowei'].'</td>
                                    <td>风险数</td>
                                    <td>'.$info['bugs'].'</td>
                                </tr>';
                               if($info['tasktype'] == 'android' || $info['tasktype'] == 'ios'){
	                                $html .='<tr>
	                                    <td>MD5</td>
	                                    <td colspan="3">'.$info['md5'].'</td>
	                                </tr>
	                                <tr>
	                                    <td>SHA-1</td>
	                                    <td colspan="3">'.$info['sha1'].'</td>
	                                </tr>
	                                <tr>
	                                    <td>SHA-256</td>
	                                    <td colspan="3">'.$info['sha256'].'</td>
	                                </tr>';
	                                if($info['tasktype'] != 'ios'){
	                                	$html .= '<tr>
		                                    <td>证书信息</td>
		                                    <td colspan="3">'.$info['cert'].'</td>
		                                </tr>';
	                                }
	                            }
                            $html .='</tbody>
                        </table>
                    </div>
                    <!-- 每一块内容END -->';
                    if($histogram['0'] != false || $histogram['1'] != false || $histogram['2'] != false){
						makepiechart($histogram,$info['realname']);
                    	makebarchart($histogram,$info['realname']);
                    	// var_dump($histogram,$info['realname']);
            $html .='<img src="'.__ROOT__."/".UPLOAD_PATH . "temp/" . $info['realname']."bar.png".'" style="width:512px;height:384px;"/><img src="'.__ROOT__."/".UPLOAD_PATH . "temp/" . $info['realname']."pie.png".'" style="width:512px;height:384px;"/>';
						if($info['internet_security_level']) {
						$title_str = get_security_level_title($info['internet_security_level'], $pass_count, $no_pass_count);
	                    $html .='<div class="report-section">
	                        <h2 class="main-title">二、检测概述</h2>
                            <div class="clearfix">&nbsp;</div>
                            <div style="margin-bottom:10px;font-size:20px;">'.$title_str.'</div>
	                        <div class="clearfix">&nbsp;</div>
	                        <table class="table">
	                            <tbody>
                                    <tr>
                                        <td>序号</td>
                                        <td>检测项目</td>
                                        <td>对标</td>
                                        <td>检测结果</td>
                                        <td>危险系数</td>
                                    </tr>';
		                $statusconfirm2 = array('92','93','98','89');
							
		                foreach ($detecresultinfo as $key => $value) {
		                    $html .='<tr>
		                                <td style="text-align:center;">'.($key+1).'</td>
		                                <td>'.$value['vulriskname'].'</td>
		                                <td>'.trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE')))).'</td>
		                                <td style="text-align:center;">';
            		                    if($value['risk_level'] == 1){
            		                        if($value['issues_count']) {
            		                            $html.= '符合(发现'.$value['issues_count'].'处)';
            		                        } else {
            		                            $html .= '符合';
            		                        }   
            		                    } else {
            		                        $html.= '不符合(发现'.$value['issues_count'].'处)';
            		                    }
		                                $html .= '</td>
		                                <td style="text-align:center;"><span class="level ';
                            if($value['risk_level'] == 1) {
                                $value['risk_level'] = $value['vulrisklevel'];
                            }
		                    if($value['risk_level'] == 3){
		                        $html .= 'middel';
		                    }elseif($value['risk_level'] == 4){
		                        $html .= 'high';
		                    }elseif($value['risk_level'] == 2){
		                        $html .= 'low';
		                    }else{
		                        $html .= 'safe" style="font-size:12px;';
		                    }
		                    $html .= '">'.$num_to_zh[$value['risk_level']].'</span></td>
		                    </tr>';
		                }
						} else {
						    $html .='<div class="report-section">
	                        <h2 class="main-title">二、检测概述</h2>
	                        <div class="clearfix">&nbsp;</div>
	                        <table class="table">
	                            <tbody>
	                                <tr>
	                                    <td>序号</td>
	                                    <td>检测项目</td>
	                                    <td>检测类型</td>
	                                    <td>风险数</td>
	                                    <td>危险系数</td>
	                                </tr>';
						    $statusconfirm2 = array('92','93','98','89');
						    
						    foreach ($detecresultinfo as $key => $value) {
						        $html .='<tr>
		                                <td style="text-align:center;">'.($key+1).'</td>
		                                <td>'.$value['vulriskname'].'</td>
		                                <td>'.$value['hvtype'].'</td>
		                                <td style="text-align:center;">';
						        if($value['issues_count']){
						            $html .= $value['issues_count'];
						        }else{
						            $html .= '-';
						        }
						        $html .= '</td>
		                                <td style="text-align:center;"><span class="level ';
						        if($value['risk_level'] == 3){
						            $html .= 'middel';
						        }elseif($value['risk_level'] == 4){
						            $html .= 'high';
						        }elseif($value['risk_level'] == 2){
						            $html .= 'low';
						        }else{
						            $html .= 'safe" style="font-size:12px;';
						        }
						        $html .= '">'.$num_to_zh[$value['risk_level']].'</span></td>
		                    </tr>';
						    }
						}
	                    $html .='</tbody>
	                        </table>
	                    </div>';
                	}
                    $html2 .='</div>
            </body>
        </html>';
        echo $html.$html2;
    }

    //使用wkhtmltopdf来生成技术报告和管理报告
    private function createTechAndManageReport($appid){
        $appinfo            = D("Appinfo");
        $apptoken  			= $appinfo->find($appid)['apptoken'];
        $teachaddress       ='./Uploads/pdf/'.createRand(25).time().createRand(25).'.pdf';
        $techreportpath     = getcwd().'/'.$teachaddress;
        $manageaddress      = './Uploads/pdf/'.createRand(25).time().createRand(25).'.pdf';
        $managereportpath   = getcwd().'/'.$manageaddress;
        system('xvfb-run -a -s "-screen 0 640x480x16" wkhtmltopdf https://localhost:443'.U('Android/Api/makeTechReport',array('apptoken'=>$apptoken)).' '.$techreportpath.' >/dev/null &');
        system('xvfb-run -a -s "-screen 0 640x480x16" wkhtmltopdf https://localhost:443'.U('Android/Api/makeManagehReport',array('apptoken'=>$apptoken)).' '.$managereportpath.' >/dev/null &');
        @$appinfo->data(array('techreportpath'=>$teachaddress,'managereportpath'=>$manageaddress))->where(array('appid'=>$appid))->save();
    }

	public function _empty(){
		header('Content-type:text/html;charset=utf8');
        $this->show('<div style="text-align:center;margin-top:20%;"><font size="6">你访问的地址,呗杀死了！</font></div>');
    }

	public function safe2System($str){
		$str = str_replace(['|',';','&'], '', $str);
	    $str = $str.' &';
	    $ret = system($str);
	    return $ret;
	}

	public function dynamic_exec(){
		$customrules 		= D("Customrules");
		$customruleslist 	= $customrules->field("ruletype,group_concat(rulesinfo order by rulesinfo desc) as rulesinfo")->where(array('ruletype'=>10))->find();
		$imp = implode("','",array_unique(explode(',', $customruleslist['rulesinfo'])));
		$dynamic_str  = file_get_contents('/Apktest/autoTest/dynamic.php.py');
		if ( !empty($imp) ){
			$ok_str = str_replace("Mr.x.com", $imp, $dynamic_str);
		}else{
			$ok_str = $dynamic_str;
		}
		@exec("ifconfig eth0",$macarray); //执行arp -a命令，结果放到数组$array中
		foreach($macarray as $value){ 
			//匹配结果放到数组$mac_array
			if(preg_match("/(:?[0-9A-F]{2}[:-]){5}[0-9A-F]{2}/i",$value,$mac_array)){
				$devmac = $mac_array[0]; 
				break; 
			} 
		}
		echo  tcodes($ok_str, $isEncrypt = true, $devmac);
		// echo "<hr/>".$ok_str."<hr/>";
		// echo tcodes($s, $isEncrypt = false, $devmac);
	}

	//客户端升级
	public function appClientUpgrade(){
		$url   			= MODULE_NAME.'/'.CONTROLLER_NAME.'/appClientUpgrade';
		$jsondata 	=	$this->jsonverification('appClientUpgrade');
		// $this->ajaxReturn(md5($jsondata.json_decode($jsondata,1)['nonce']));
		// $jsondatadecode  =json_decode($jsondata,1);
		// var_dump($jsondata,$jsondatadecode['none'],md5($jsondata.$jsondatadecode['none']));die;
		if($this->headerverification($jsondata,'appClientUpgrade',0)){
			$requestData 	= json_decode($jsondata,true);
			$versionCode 	= $reportData['versioncode'];
			$useragent  	= (getUserAgent() == 'android') ? '0' : '1';

			//对应客户端手机信息
			$clientVersionInfo = D('VersionUpgrade')->where(array('phone_type'=>$useragent))->find();
			if( intval($clientVersionInfo['versioncode']) > intval($versionCode) ){
				if(file_exists($clientVersionInfo['download_url'])){
					$clientVersionInfo['download_url'] = __ROOT__."/".$clientVersionInfo['download_url'];
				}else{
					$clientVersionInfo['download_url'] = '';
				}
				
				$this->ajaxReturn(array('code'=>'success','msg'=>C('REQUEST_DATA_SUCCESS'),'data'=>$clientVersionInfo));
			}else{
				$this->ajaxReturn(array('code'=>'fail','msg'=>C('REQUEST_DATA_FALSE')));
			}
		}
		else
		{
			$this->ajaxReturn(array('code'=>'fail','msg'=>C('NEED_PARAMETER'),'data'=>$requestData));	
		}
	}

	//获取到的usertoken
	// apptoken值
	// serverity值 4_3_2,4_2,3,2_3
	//获取测试任务的报告json数据
	public function getTaskReportJson()
	{
		$model                  = D("Appinfo");
        // $vulinfo                = D("Vulinfo");
        $analysisResults 		= D('AnalysisResults');
        $usertoken 				= I('get.usertoken');
        $apptoken               = I('get.apptoken');
        $serverity 				= I('get.serverity');
        $tokentimeandtoekn      = D('Usertoken')->where(array('at_usertoken.token'=>$usertoken,'at_userauth.tid'=>2))->join('left join at_userauth on at_usertoken.userid = at_userauth.uid')->find();
        if($tokentimeandtoekn){
        	if( time() - $tokentimeandtoekn['timestamp'] > 0 && time() - $tokentimeandtoekn['timestamp'] < 3600){
        		$appid 					= $model->where(array('apptoken'=>$apptoken))->find()['appid'];
		        $info                   = $model->where(array('appid'=>$appid))->find();
		        if($serverity){
		        	$serverity = explode('_', $serverity);
		        	if(in_array('4', $serverity)){
			        	$histogram['high'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
		        	}else{
		        		$histogram['high']  	= 0;
		        	}

		        	if(in_array('3', $serverity)){
		        		$histogram['mid'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 3))->sum('issues_count');
		        	}else{
		        		$histogram['mid']  	= 0;
		        	}
		        	
		        	if(in_array('2', $serverity)){
			        	$histogram['low'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 2))->sum('issues_count');
		        	}else{
		        		$histogram['low']  	= 0;
		        	}
		    	 	$info['bugs'] 				= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level'=>array('in',$serverity)))->sum('issues_count');
		    	 	// var_export(array('appid'=>$appid,'risk_level'=>array('in',$serverity)));die;
		    	 	if(in_array('4', $serverity)){
			        	$info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
			    	}
			    	if(!$info['gaowei']){
			    		$info['gaowei'] 		= 0;
			    	}

		         	$detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('in',$serverity)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
		        }else{
			        $info['bugs']           = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2) ))->sum('issues_count');
			        $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>4 ))->sum('issues_count');
			        $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
			        //高危漏洞类型数量
			        if(!$info['gaowei']){
			        	$info['gaowei'] 		= 0;
			        }
			        $histogram['high'] 		= $info['gaowei'];
			        //中危漏洞类型数量
			        $histogram['mid'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>3 ))->sum('issues_count');
			        // var_export($histogram['mid']);die;
			        //低危漏洞类型数量
			        $histogram['low'] 			= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>2 ))->sum('issues_count');
		    	}
		        $zh_to_num          = array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
		        $num_to_zh          = array('0'=>'暂未评级','1'=>'安全','2'=>'低','3'=>'中','4'=>'高');
		       	
		        // $detecresultinfo = list_sort_by($detecresultinfo, 'risk_level', $sortby = 'desc');
		        //设置中文编码
		        
		        $rulelist = array(
		        	'sql'                               =>'SQL注入',
				    'xxe'                               =>'XML注入',
				    'code_injection'                    =>'代码注入',
				    'os_cmd_injection'                  =>'系统命令执行',
				    'xss'                               =>'XSS漏洞',
				    'backup_file'                       =>'备份文件检测',
				    'csrf'                              =>'csrf漏洞',
				    'file_inclusion'                    =>'文件包含漏洞',
				    'path_traversal'                    =>'文件下载漏洞',
				    'directory_listing'                 =>'目录遍历',
				    'backdoors'                         =>'后门检测',
				    'source_code_disclosure'            =>'源代码泄漏',
				    'sensitive_info'                    =>'信息泄漏',
				    'weaker_ciphers'                    =>'弱口令检测'
				);

				$reportData = array();
				//----------------------报告头----------------------
				if($info['tasktype'] == 'ios'){
		            $reportType = "iOS版";
		        }elseif($info['tasktype'] == 'wx'){
		            $reportType = "微信安全测试";
		        }elseif($info['tasktype'] == 'android'){
		            $reportType = "Android版";
		        }else{
		        	$reportType = "WEB扫描";
		        }

				$headerData = array(
					'report_title'	=> 		'应用安全检测技术报告',
					'app_testtime'	=> 		$info['uploadtime'],
					'report_type'	=> 		$reportType,
					'app_version'	=> 		$info['version']
					); 
				if(file_exists($info['icon'])){
					$headerData['app_icon']	= __ROOT__."/".$info['icon'];
				}

				//----------------------报告头----------------------

				//----------------------应用基本信息----------------------
				$basicAppinfo 			= array(
					'high_risk_vulnerabilities'	=>	$info['gaowei'],
					'number_of_holes'			=>	$info['bugs']
					);
				if($info['tasktype'] == 'android' || $info['tasktype'] == 'ios' ){
		            $basicAppinfo['apply_name'] 	= $info['realname'];
		            $basicAppinfo['version_number'] = $info['version'];
		            $basicAppinfo['package_name'] 	= $info['package'];
		            $basicAppinfo['detection_time'] = $info['subtime'];

		        }elseif($info['tasktype'] == 'web' || $info['tasktype'] == 'awvs'){
		            $basicAppinfo['target_website'] = $info['targeturl'];
		        	if($info['tasktype'] == 'awvs'){
		                $basicAppinfo['scanoption'] = json_decode($info['scanoption'],1);
		                foreach ($basicAppinfo['scanoption']['checks'] as $key => $value) {
		                	if($rulelist[$value]){
		                		$goalchecks[] = $rulelist[$value];
		                	}
		                }
		                $basicAppinfo['scanoption']['checks'] = $goalchecks;
		        	}                            	
		        }
		           
		        if($info['tasktype'] == 'android' || $info['tasktype'] == 'ios'){
		            $basicAppinfo['md5'] 		= $info['md5'];
		            $basicAppinfo['sha1'] 		= $info['sha1'];
		            $basicAppinfo['sha256'] 	= $info['sha256'];
		            if($info['tasktype'] != 'ios'){
		                $basicAppinfo['cert'] 	= $info['cert'];
		            }            
		    	}
		    	$basicAppinfo['vulnerability_statistics'] 	= $histogram;
				//----------------------应用基本信息----------------------

				//------------------------检测概述和详细检测结果------------------------
		        $detectionOverview  = $detailedDetection = [];
		        foreach ($detecresultinfo as $key => $value) {
		        	$detectionOverview[$key]['detection_program'] 	= $value['case_name'];
		        	$detectionOverview[$key]['detection_type'] 		= $value['hvtype'];
		        	$detectionOverview[$key]['number_of_holes'] 	= $value['issues_count'];
		            // if($value['risk_level'] == 3){
		            //     $risk_level = 'middel';
		            // }elseif($value['risk_level'] == 4){
		            //     $risk_level = 'high';
		            // }else{
		            //     $risk_level = 'low';
		            // }
		            // $detectionOverview[$key]['risk_level'] 			= $risk_level;
		            
		            
		            
		            $detailedDetection[$key]['use_case_name'] 		= $value['case_name'];
		            $detailedDetection[$key]['vulnerability_number']= str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars($value['standard']));
		            $detailedDetection[$key]['risk_description'] 	= $value['risk_description'];
					$detailedDetection[$key]['detection_process'] 	= str_replace(array("[.]","[img]","[/img]","&lt;br&gt;","&lt;/br&gt;"),array("\"","<img",">","<b>","</b>"),str_replace(array("\r","\r\n","\n","<!--","-->"),"<br/>", htmlspecialchars($value['detection_process'])));
					$detailedDetection[$key]['risk_level'] 			= $value['risk_level'];
		            $detailedDetection[$key]['repair_recommendations'] 	= str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars($value['suggestions']));
		        }
				//------------------------检测概述和详细检测结果------------------------

				$reportData['headerData'] 	= $headerData;
				$reportData['basicAppinfo']	= $basicAppinfo;
				$reportData['detectionOverview'] = $detectionOverview;
				$reportData['detailedDetection'] = $detailedDetection;
				$this->ajaxReturn($reportData);
        	}else{
        		$this->ajaxReturn(array('code'=>'false','info'=>C('LOGIN_OVERTIME')));
        	}        	
        }else{
        	$this->ajaxReturn(array('code'=>'false','info'=>C('PERMISSION_NOT_ENOUGH')));
        }       
	}

	//获取到的usertoken
	// apptoken值
	// serverity值 4_3_2,4_2,3,2_3
	//获得管理报告的json数据
	public function getManageTaskReportJson()
	{
		$model                  = D("Appinfo");
        // $vulinfo                = D("Vulinfo");
        $analysisResults 		= D('AnalysisResults');
        $apptoken               = I('get.apptoken');
        $serverity 				= I('get.serverity');
        $usertoken 				= I('get.usertoken');
        $tokentimeandtoekn      = D('Usertoken')->where(array('at_usertoken.token'=>$usertoken,'at_userauth.tid'=>2))->join('left join at_userauth on at_usertoken.userid = at_userauth.uid')->find();
        if($tokentimeandtoekn ){
        	if(time() - $tokentimeandtoekn['timestamp'] > 0 && time() - $tokentimeandtoekn['timestamp'] < 3600 ){
		        $appid 					= $model->where(array('apptoken'=>$apptoken))->find()['appid'];
		        $info                   = $model->where(array('appid'=>$appid))->find();
		        if($serverity){
		        	$serverity = explode('_', $serverity);
		        	if(in_array('4', $serverity)){
			        	$histogram['high'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
		        	}else{
		        		$histogram['high']  	= 0;
		        	}

		        	if(in_array('3', $serverity)){
		        		$histogram['mid'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 3))->sum('issues_count');
		        	}else{
		        		$histogram['mid']  	= 0;
		        	}
		        	
		        	if(in_array('2', $serverity)){
			        	$histogram['low'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 2))->sum('issues_count');
		        	}else{
		        		$histogram['low']  	= 0;
		        	}
		        	
		    	 	$info['bugs'] 				= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level'=>array('in',$serverity)))->sum('issues_count');
		    	 	// var_export(array('appid'=>$appid,'risk_level'=>array('in',$serverity)));die;
		    	 	if(in_array('4', $serverity)){
			        	$info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
			    	}
			    	if(!$info['gaowei']){
			    		$info['gaowei'] 		= 0;
			    	}

		         	$detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('in',$serverity)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
		        }else{
			        $info['bugs']           = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2) ))->sum('issues_count');
			        $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>4 ))->sum('issues_count');
			        $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
			        //高危漏洞类型数量
			        if(!$info['gaowei']){
			        	$info['gaowei'] 		= 0;
			        }
			        $histogram['high'] 		= $info['gaowei'];
			        //中危漏洞类型数量
			        $histogram['mid'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>3 ))->sum('issues_count');
			        // var_export($histogram['mid']);die;
			        //低危漏洞类型数量
			        $histogram['low'] 			= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>2 ))->sum('issues_count');
		    	}
		        $zh_to_num          = array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
		        $num_to_zh          = array('0'=>'暂未评级','1'=>'安全','2'=>'低','3'=>'中','4'=>'高');
		       	
		        // $detecresultinfo = list_sort_by($detecresultinfo, 'risk_level', $sortby = 'desc');
		        //设置中文编码
		        
		        $rulelist = array(
		        	'sql'                               =>'SQL注入',
				    'xxe'                               =>'XML注入',
				    'code_injection'                    =>'代码注入',
				    'os_cmd_injection'                  =>'系统命令执行',
				    'xss'                               =>'XSS漏洞',
				    'backup_file'                       =>'备份文件检测',
				    'csrf'                              =>'csrf漏洞',
				    'file_inclusion'                    =>'文件包含漏洞',
				    'path_traversal'                    =>'文件下载漏洞',
				    'directory_listing'                 =>'目录遍历',
				    'backdoors'                         =>'后门检测',
				    'source_code_disclosure'            =>'源代码泄漏',
				    'sensitive_info'                    =>'信息泄漏',
				    'weaker_ciphers'                    =>'弱口令检测'
				);

				$reportData = array();
				//----------------------报告头----------------------
				if($info['tasktype'] == 'ios'){
		            $reportType = "iOS版";
		        }elseif($info['tasktype'] == 'wx'){
		            $reportType = "微信安全测试";
		        }elseif($info['tasktype'] == 'android'){
		            $reportType = "Android版";
		        }else{
		        	$reportType = "WEB扫描";
		        }

				$headerData = array(
					'report_title'	=> 		'应用安全检测技术报告',
					'app_testtime'	=> 		$info['uploadtime'],
					'report_type'	=> 		$reportType,
					'app_version'	=> 		$info['version']
					); 
				if(file_exists($info['icon'])){
					$headerData['app_icon']	= __ROOT__."/".$info['icon'];
				}

				//----------------------报告头----------------------

				//----------------------应用基本信息----------------------
				$basicAppinfo 			= array(
					'high_risk_vulnerabilities'	=>	$info['gaowei'],
					'number_of_holes'			=>	$info['bugs']
					);
				if($info['tasktype'] == 'android' || $info['tasktype'] == 'ios' ){
		            $basicAppinfo['apply_name'] 	= $info['realname'];
		            $basicAppinfo['version_number'] = $info['version'];
		            $basicAppinfo['package_name'] 	= $info['package'];
		            $basicAppinfo['detection_time'] = $info['subtime'];

		        }elseif($info['tasktype'] == 'web' || $info['tasktype'] == 'awvs'){
		            $basicAppinfo['target_website'] = $info['targeturl'];
		        	if($info['tasktype'] == 'awvs'){
		                $basicAppinfo['scanoption'] = json_decode($info['scanoption'],1);
		                foreach ($basicAppinfo['scanoption']['checks'] as $key => $value) {
		                	if($rulelist[$value]){
		                		$goalchecks[] = $rulelist[$value];
		                	}
		                }
		                $basicAppinfo['scanoption']['checks'] = $goalchecks;
		        	}                            	
		        }
		           
		        if($info['tasktype'] == 'android' || $info['tasktype'] == 'ios'){
		            $basicAppinfo['md5'] 		= $info['md5'];
		            $basicAppinfo['sha1'] 		= $info['sha1'];
		            $basicAppinfo['sha256'] 	= $info['sha256'];
		            if($info['tasktype'] != 'ios'){
		                $basicAppinfo['cert'] 	= $info['cert'];
		            }            
		    	}
		    	$basicAppinfo['vulnerability_statistics'] 	= $histogram;
				//----------------------应用基本信息----------------------

				//------------------------检测概述和详细检测结果------------------------
		        $detectionOverview  = [];
		        foreach ($detecresultinfo as $key => $value) {
		        	$detectionOverview[$key]['detection_program'] 	= $value['case_name'];
		        	$detectionOverview[$key]['detection_type'] 		= $value['hvtype'];
		        	$detectionOverview[$key]['number_of_holes'] 	= $value['issues_count'];
		            // if($value['risk_level'] == 3){
		            //     $risk_level = 'middel';
		            // }elseif($value['risk_level'] == 4){
		            //     $risk_level = 'high';
		            // }else{
		            //     $risk_level = 'low';
		            // }
		            // $detectionOverview[$key]['risk_level'] 			= $risk_level;
		        }
				//------------------------检测概述和详细检测结果------------------------

				$reportData['headerData'] 	= $headerData;
				$reportData['basicAppinfo']	= $basicAppinfo;
				$reportData['detectionOverview'] = $detectionOverview;
				$this->ajaxReturn($reportData);
			}else{
        		$this->ajaxReturn(array('code'=>'false','info'=>C('LOGIN_OVERTIME')));
        	}   
		}else{
        	$this->ajaxReturn(array('code'=>'false','info'=>C('PERMISSION_NOT_ENOUGH')));
        } 
	}

	//加固壳上传
	public function detection_shell()
	{
		if(IS_POST){
			// $userid 			= I('post.userid');
			$appid 				= I('post.appid');
			$timestamp 			= I('post.timestamp');
			$nonce 				= I('post.nonce');
			$gettoken 			= I('post.usertoken');
			$devip 				= I('post.devip');
			$usertoken 			= D('Usertoken');
			$timestamptb		= D('Timnonce');
			$user 				= D('User');
			$appinfo 			= D('Appinfo');
			$url   				= MODULE_NAME.'/'.CONTROLLER_NAME.'/detection_shell';

			if(!empty($appid) && !empty($gettoken) && !empty($timestamp)&& !empty($nonce))
			{

				$header  		= getallheaders();
				
				//判断是否登录超时
				// $this->overtime($gettoken['timestamp']);
				// if(!($timestamptb->where(array('timestamp'=>$timestamp,'nonce'=>$nonce))->select()))
				// {
				// 	@$timestamptb->data(array('timestamp'=>$timestamp,'nonce'=>$nonce))->add();
					$usertokeninfo 		= $usertoken->where(array('token'=>$gettoken))->find();
					$usertoken->where(array('id'=>$usertokeninfo['id']))->save(array('timestamp'=>time()));
					if($header['signature'] == md5($appid.$timestamp.$nonce.$gettoken.$devip.$nonce))
					{
						if($user->where(array('userid'=>$usertokeninfo['userid']))->select() && $_FILES)
						{				
							//用户信息
							@$personinfo 		= $user->find($usertokeninfo['userid']);

							//如果有如果有检测文件上传
							$upload = new \Think\Upload();
							// 实例化上传类 
							// $upload->maxSize = 3145728 ; //3M
							$upload->maxSize = 209715200 ; //200M
							// 设置附件上传大小 
							$upload->saveName	= array('uniqid','');
							// $upload->saveName = '';//保存名字为原名
							$upload->exts = array('zip');
							// $upload->savePath	= UPLOAD_PATH.'Step/'; 
							$upload->rootPath   = UPLOAD_PATH;
							$upload->savePath	= 'shelldetec/';
							// $upload->subName    = array('date','Ymd');
							$info = $upload->upload(); 
							if(!$info) {
								// 上传错误提示错误信息 
								$this->ajaxReturn(array('code'=>'fail','msg'=>$upload->getError()));
							}else{
								$detectionfile 	= getcwd()."/".UPLOAD_PATH.$info['detectionfile__']['savepath'].$info['detectionfile__']['savename'];
								//添加脱壳成功表示-sql
								$dexdata['dexfile']=$detectionfile;
								$dexdata['dexdump']=1;
								$appinfo->where(array("appid"=>$appid))->save($dexdata);
								// 执行脚本
								system("/Apktest/MobAppSecAss/start_apptest 'apk'  {$detectionfile} {$appid} {$usertokeninfo['userid']} 1 {$devip} >/dev/null &");
								$this->ajaxReturn(array('code'=>'success','filename'=>$detectionfile));
							}			
						}
						else
						{
							// @addMareLog(array(
								// 'handleurl'   	=>$url, 
								// 'handlecontent'	=>'上传测试信息',
								// 'handleresult'	=>'失败',
								// 'handleremarks'	=>'用户上传测试信息失败'
							// ));
							//不存在该用户
							$this->ajaxReturn(array('code'=>'fail','msg'=>C('USER_NOT_EXIST')));
						}
					}
					else	
					{
						// @addMareLog(array(
							// 'handleurl'   	=>$url, 
							// 'handlecontent'	=>'上传测试信息',
							// 'handleresult'	=>'失败',
							// 'handleremarks'	=>'用户上传测试信息失败'
						// ));
						$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATA_VALIDATION_ERROR')));
					}
				// }
				// else
				// {
					//@addMareLog(array(
						// 'userid'			=>$userid,
						// 'handlecontent'	=>C('DATE_TIME_REPEAT').": ".json_encode(I('post.')).'，'.$url
						// ));
					// 	$this->ajaxReturn(array('code'=>'fail','msg'=>C('DATE_TIME_REPEAT')));
				// }
			}
			else
			{
				// @addMareLog(array(
					// 'handleurl'   	=>$url, 
					// 'handlecontent'	=>'上传测试信息',
					// 'handleresult'	=>'失败',
					// 'handleremarks'	=>'用户上传测试信息失败'
				// ));
				$this->ajaxReturn(array('code'=>'fail','msg'=>C('NEED_PARAMETER')));
			}			
		}
		else
		{
			// @addMareLog(array(
				// 'handleurl'   	=>$url, 
				// 'handlecontent'	=>'上传测试信息',
				// 'handleresult'	=>'失败',
				// 'handleremarks'	=>'用户上传测试信息失败'
			// ));
			$this->ajaxReturn(array('code'=>'fail','msg'=>C('METHOD_TIP')));
		}
	}

	////需要的参数是
	// body
	// username  为用户名
	// password  为用户密码
	// timestamp 为用户发送的时间戳
	// nonce 为随机数
	// 头部参数 signature = MD5(body的json数据.nonce)
	// 返回成功或者是否，并在成功时返回用户的usertoken值
	//分析员登录
	public function analyst_login(){
		$header  		= getallheaders();
		$url   			= MODULE_NAME.'/'.CONTROLLER_NAME.'/analyst_login';
		$jsondata		= $GLOBALS['HTTP_RAW_POST_DATA'];
		if($jsondata)
		{
			//判断头部的signature是否等于传过来的json数据,随机数的MD5加密值
			$requestdata 		= json_decode($jsondata,true);
			if(strtoupper($header['signature']) == strtoupper(md5($jsondata.$requestdata['nonce'])) ){
				if($requestdata['username'] && $requestdata['password']){
					$username 	= $requestdata['username'];
					$password 	= md5($requestdata['password'].C('PWD_AFTER'));
					$timestamp 	= time();
					$user 		= D('User');
					$usertoken 	= D('Usertoken');

					$userinfo 	= $user->field('at_user.userid,at_usertoken.*,at_userauth.tid')->where(array('at_user.loginemail'=>$username,'at_user.password'=>$password,'at_userauth.tid'=>2))->join('left join at_userauth on at_user.userid = at_userauth.uid')->join('left join at_usertoken on at_user.userid = at_usertoken.userid')->find();

					if($userinfo){
						$usertokeninfo = $usertoken->where(array('userid'=>$userinfo['userid']))->find();
						//判断用户的登录时间是否操过3600秒
						if( $requestdata['timestamp'] - $usertokeninfo['timestamp'] > 0 && $requestdata['timestamp'] - $usertokeninfo['timestamp'] < 3600 ){
							$this->ajaxReturn(array('code'=>'success','info'=>$usertokeninfo['token']));
						}else{
							//操过3600秒就从新生成新的token
							$hashval 	= hash('md5',$username.$password.$timestamp);
							//判断是否存在过登录的分析员记录，如果不存在就添加，否则就更新记录
							if($usertokeninfo){
								$res 		= $usertoken->where(array('id'=>$usertokeninfo['id']))->data(array('token'=>$hashval,'timestamp'=>$timestamp))->save();
							}else{
								$res 		= $usertoken->data(array('userid'=>$userinfo['userid'],'token'=>$hashval,'timestamp'=>$timestamp))->add();
							}
							if($res !== false){
								//登录成功的info值作为或应用列表和获取对应报告的json数据的条件
								$this->ajaxReturn(array('code'=>'success','info'=>$hashval));
							}else{
								$this->ajaxReturn(array('code'=>'false','info'=>C('LONIN_USER_PASS_ERROR')));
							}			
						}
					}else{
						$this->ajaxReturn(array('code'=>'false','info'=>C('PERMISSION_NOT_ENOUGH')));
					}
				}else{
					$this->ajaxReturn(array('code'=>'false','msg'=>C('NEED_PARAMETER')));
				}
			}else{
				$this->ajaxReturn(array('code'=>'false','msg'=>C('DATA_VALIDATION_ERROR')));
			}
		}else{
			$this->ajaxReturn(array('code'=>'false','msg'=>C('METHOD_TIP')));
		}
	}

	//需要的参数是
	// body
	// usertoken 用户登录成功后获取倒的info值为32位的字符串
	// timestamp 为用户发送的时间戳
	// nonce 为随机数
	// 头部参数 signature = MD5(body的json数据.nonce)
	// 返回成功或者是否，并在成功时所有的应用类型值
	//分析员获取任务列表
	public function analyst_get_tasklist(){
		$header  		= getallheaders();
		$url   			= MODULE_NAME.'/'.CONTROLLER_NAME.'/analyst_get_tasklist';
		$jsondata		= $GLOBALS['HTTP_RAW_POST_DATA'];
		if($jsondata)
		{
			//判断头部的signature是否等于传过来的json数据,随机数的MD5加密值
			$requestdata 		= json_decode($jsondata,true);
			if(strtoupper($header['signature']) == strtoupper(md5($jsondata.$requestdata['nonce'])) ){
				if($requestdata['usertoken']){
					$timestamp 	= time();
					$user 		= D('User');
					$usertoken 	= D('Usertoken');
					$userinfo 	= $usertoken->field('at_userauth.tid,at_usertoken.*')->where(array('at_usertoken.token'=>$requestdata['usertoken'],'at_userauth.tid'=>2))->join('left join at_userauth on at_usertoken.userid = at_userauth.uid')->find();
					if($userinfo){
						//判断是否登录超时
						if( $requestdata['timestamp'] - $userinfo['timestamp'] > 3600 ){
							// $hashval 	= hash('md5',$username.$password.$timestamp);
							//更新token的时间
							@$usertoken->data(array('timestamp'=>$timestamp))->where(array('id'=>$userinfo['id']))->save();
							$applist 	= D('Appinfo')->field('userid,appid,package,realname,subtime,apptoken,tasktype')->select();
							if($applist !== false){
								//将token值传个分析员
								$this->ajaxReturn(array('code'=>'success','info'=>$applist));
							}else{
								$this->ajaxReturn(array('code'=>'false','info'=>C('REQUEST_DATA_FALSE')));
							}
						}else{
							//登录超时,请重新登录
							$this->ajaxReturn(array('code'=>'false','info'=>C('LOGIN_OVERTIME')));
						}		
					}else{
						$this->ajaxReturn(array('code'=>'false','info'=>C('PERMISSION_NOT_ENOUGH')));
					}
				}else{
					$this->ajaxReturn(array('code'=>'false','msg'=>C('NEED_PARAMETER')));
				}
			}else{
				$this->ajaxReturn(array('code'=>'false','msg'=>C('DATA_VALIDATION_ERROR')));
			}
		}else{
			$this->ajaxReturn(array('code'=>'false','msg'=>C('METHOD_TIP')));
		}
	}

	public function test()
	{
		// $res = shell_exec('xvfb-run -a -s "-screen 0 640x480x16" wkhtmltopdf https://localhost:443/mare/index.php/Android/Api/makeTechReport/appid/17/serverity/4_3.html 4_3.pdf');
		// var_export($res);die;
		header('Content-type:text/html;charset=utf8');

		// echo "<pre>";
		// echo "===============原文=================\n";
		// echo $GLOBALS['HTTP_RAW_POST_DATA']; echo "\n";
		// echo "=============解开成数组===================\n";
		// $data = json_decode($GLOBALS['HTTP_RAW_POST_DATA'],true);	
		// $header 			= getallheaders();
		// // var_dump($header);
		// $jsonstr  = $GLOBALS['HTTP_RAW_POST_DATA'].$data['nonce'];
		// echo "<br/>jsonstr   :$jsonstr<br/>signature   ".md5($jsonstr)."  <br/>headersignature  ".$header['signature'];

		// echo "<br/>mima.   ".md5("1Qaz2wsx".C('PWD_AFTER'));
		// $action = htmlspecialchars(file_get_contents('./Uploads/apptest-log.txt'));
		// addLog(1,$action);
		// var_export(htmlentities(file_get_contents('./Uploads/apptest-log.txt')));
		
		// $res = D('AnalysisResults')->data(
		// 	array(
		// 		'detection_process'	=>htmlspecialchars(file_get_contents('./Uploads/apptest-log.txt')),
		// 		))->add();
		// 	var_dump($res);

		var_export( htmlspecialchars_decode(D('AnalysisResults')->where(array('id'=>21828))->find()['detection_process']));
	}

	public function datacreate(){
		// $data  = array(
		// 	'username'=>'analysts',
		// 	'password'=>'1Qaz2wsx',
		// 	'timestamp'=>time(),
		// 	'nonce'=>'fsdflkjsdfhsdkjfhsdjfsjh23453nfjsdhfkshk'
		// 	);
		$data  = array(
			'usertoken'=>'3a92ed85c118c9002da1184b796cddb6',
			'timestamp'=>time(),
			'nonce'=>'fsdflkjsdfhsdkjfhsdjfsjh23453nfjsdhfkshk'
			);
		$jsondata =  json_encode($data);
		$jsonstr  =  $jsondata.$data['nonce'];
		echo $jsonstr;
		echo "<br/>-----------------------------------------------<br/>";

		$signature = md5($jsonstr);
		// var_dump($jsondata,$signature);
		echo $signature;
	}
	
	/**
	 * 通报报告
	 */
	public function makeProposalReport() {
	    $model                  = D("Appinfo");
	    // $vulinfo                = D("Vulinfo");
	    $analysisResults 		= D('AnalysisResults');
	    $apptoken               = I('get.apptoken');
	    $serverity 				= I('get.serverity');
	    $appid 					= $model->where(array('apptoken'=>$apptoken))->find()['appid'];
	    $info                   = $model->where(array('appid'=>$appid))->find();
	    
	    if ($info['internet_security_level']) {
	        $Internet_security_level = get_security_level($info['internet_security_level']);
	        $where['standard'] = ['like',"%".C('SECURITY_LEVEL_LIKE').$Internet_security_level."%"];
	    }
	    
	    if($serverity){
	        $serverity = explode('_', $serverity);
	        if(in_array('4', $serverity)){
	            $histogram['0'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
	        }else{
	            $histogram['0']  	= 0;
	        }
	        
	        if(in_array('3', $serverity)){
	            $histogram['1'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 3))->sum('issues_count');
	        }else{
	            $histogram['1']  	= 0;
	        }
	        
	        if(in_array('2', $serverity)){
	            $histogram['2'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 2))->sum('issues_count');
	        }else{
	            $histogram['2']  	= 0;
	        }
	        $info['bugs'] 				= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level'=>array('in',$serverity)))->sum('issues_count');
	        // var_export(array('appid'=>$appid,'risk_level'=>array('in',$serverity)));die;
	        if(in_array('4', $serverity)){
	            $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
	        }
	        if(!$info['gaowei']){
	            $info['gaowei'] 		= 0;
	        }
	        
	        // 	    	$proposal_count = $analysisResults->where($where)->where(['appid'=>$appid, 'risk_level' => ['in',$serverity]])->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->count();
	        
	        $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('in',$serverity)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,is_proposal ASC,issues_count DESC')->select();
	        $detecresultinfo = get_detection_arr($detecresultinfo, $appid,$serverity);
	    }else{
	        $info['bugs']           = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2) ))->sum('issues_count');
	        $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>4 ))->sum('issues_count');
	        $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,is_proposal ASC,issues_count DESC')->select();
	        // 	        $proposal_count = $analysisResults->where($where)->where(['appid'=>$appid, 'risk_level' => ['egt',2]])->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->count();
	        //高危漏洞类型数量
	        if(!$info['gaowei']){
	            $info['gaowei'] 		= 0;
	        }
	        $histogram['0'] 		= $info['gaowei'];
	        //中危漏洞类型数量
	        $histogram['1'] 		= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>3 ))->sum('issues_count');
	        // var_export($histogram['mid']);die;
	        //低危漏洞类型数量
	        $histogram['2'] 			= $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>2 ))->sum('issues_count');
	    }
	    
	    $proposal_count = 0;
	    foreach ($detecresultinfo as $de_key => &$de_value) {
	        if($de_value['risk_level'] <= 2) {
	            $de_value['is_proposal'] = 2;
	        }
	        
	        if($de_value['is_proposal'] == 1) {
	            $proposal_count += 1;
	        }
	    }
	    
	    if ($info['internet_security_level']) {
	        $pass_count = 0;
	        $no_pass_count = 0;
	        foreach ($detecresultinfo as $key_i => &$value_i) {
	            if ($value_i['risk_level'] == 1) {
	                $pass_count += 1;
	            } else {
	                $no_pass_count += 1;
	            }
	            $value_i['standard'] = get_vul_result($value_i['standard'], 'html', $Internet_security_level);
	        }
	        $detecresultinfo = array_sort($detecresultinfo, 'standard', 'asc');
	    }
	    
	    $zh_to_num          = array('暂未评级'=>'0','通过'=>1,'低'=>2,'中'=>3,'高'=>4);
	    $num_to_zh          = array('0'=>'暂未评级','1'=>'通过','2'=>'低','3'=>'中','4'=>'高');
	    
	    // $detecresultinfo = list_sort_by($detecresultinfo, 'risk_level', $sortby = 'desc');
	    //设置中文编码
	    
	    $rulelist = array(
	        'sql'                               =>'SQL注入',
	        'xxe'                               =>'XML注入',
	        'code_injection'                    =>'代码注入',
	        'os_cmd_injection'                  =>'系统命令执行',
	        'xss'                               =>'XSS漏洞',
	        'backup_file'                       =>'备份文件检测',
	        'csrf'                              =>'csrf漏洞',
	        'file_inclusion'                    =>'文件包含漏洞',
	        'path_traversal'                    =>'文件下载漏洞',
	        'directory_listing'                 =>'目录遍历',
	        'backdoors'                         =>'后门检测',
	        'source_code_disclosure'            =>'源代码泄漏',
	        'sensitive_info'                    =>'信息泄漏',
	        'weaker_ciphers'                    =>'弱口令检测'
	    );
	    //页眉内容
	    //第一页
	    $html = '
<!DOCTYPE html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title>报告</title>
                <style type="text/css">
                *{
                    margin:0;
                    padding: 0;
                }
                html,body{
                    background-color: #777777;
                }
                .table{
                    border: 1px solid;
                    border-collapse: collapse;
                    width: 100%;
                    height: auto;
                    font-size: 1.2rem;
                    line-height: 1.4;
                    margin-bottom: 80px;
                }
                .table tr {
                    border: 1px solid #cbcbcb;
                    border-collapse: collapse;
                }
                .table tr td {
                    border: 1px solid #cbcbcb;
                    border-collapse: collapse;
                    padding: 5px 10px;
                }
                .table tr td p{
                    word-break: break-word;
                    page-break-inside:avoid;
                }
                .container{
                    width: 1024px;
                    margin-left: auto;
                    margin-right: auto;
                    background-color: #ffffff;
                    border-left: 1px solid #e1e1e1;
                    border-right: 1px solid #e1e1e1;
                    padding-top: 50px;
                    padding-bottom: 100px;
                    padding-left: 30px;
                    padding-right: 30px;
                }
                .top-title{
                    position: relative;
                    padding-top: 50px;
                    min-height: 190px;
                }
                .top-title > h1{
                    margin-bottom: 30px;
                }
                .top-title > h1, .top-title > h2{
                    text-align: center;
                }
                .sub-title{
                    position: absolute;
                    left: 50%;
                    transform: translateX(-50%);
                    -webkit-transform: translateX(-50%);
                    -moz-transform: translateX(-50%);
                }
                .sub-title .sub-text{
                    float: left;
                    margin-left: 10px;
                }
                .sub-title .sub-img{
                    float: left;
                }
                .report-section{
                    margin-bottom: 50px;
                }
                .report-content .report-section .main-title{
                    margin-bottom: 30px;
                    margin-top: 80px;
                    padding: 5px 15px 5px 5px;
                    background-color: #2196F3;
                    border-top-right-radius: 20px;
                    border-bottom-right-radius: 20px;
                    -webkit-border-top-right-radius: 20px;
                    -webkit-border-bottom-right-radius: 20px;
                    float: left;
                    color: #ffffff;
                }
                .report-content .report-section .sec-wrapper{
                    position: relative;
                    padding-left: 35px;
                    margin-top: 10px;
                    margin-bottom: 5px;
                }
                .badge{
                    position: absolute;
                    display: block;
                    width: 25px;
                    height: 25px;
                    background-color: #8BC34A;
                    border-radius: 50%;
                    -webkit-border-radius: 50%;
                    top: -3px;
                    left: 0;
                    -webkit-user-select:none;
                    cursor: default;
                }
                .badge:after{
                    display: block;
                    content: "\25BC";
                    color: #ffffff;
                    padding: 5px;
                }
                .clearfix{
                    clear: both;
                    display: block;
                    height: 0;
                }
                .level{
                    display: inline-block;
                    padding: 10px;
                    border-radius: 50%;
                    background-color: #e1e1e1;
                    color: #ffffff;
                    line-height: 1;
                }
                .low{
                    background-color: #377aa6;
                }
                .middel{
                    background-color: #FF9800;
                }
                .safe{
                    background-color: #4fca81;
                }
                .high{
                    background-color: #f44336;
                }
                .report-footer{
                    border-top: 1px solid #e1e1e1;
                    padding-top: 10px;
                }
    			.detailtable {
				  max-width: 100%;
				  width: 100%;
				  margin-bottom: 60px;
				  border: 1px solid black;
				  word-break: break-all;
				  border-collapse: collapse;
				}
                </style>
            </head>
            <body>
            <div class="container">
                <div class="top-title">';
	    
	    $html .= '<h1>应用安全检测技术报告</h1>
                        <div class="sub-title">';
	    if(file_exists($info['icon'])){
	        $img = __ROOT__.'/'.$info['icon'];
	    }
	    if($img){
	        if($info['tasktype'] == 'wx'){
	            $html .= '<div class="sub-img"><img src="'.$img.'" style="width:150px;"/></div><br/>';
	        }else{
	            $html .= '<div class="sub-img"><img src="'.$img.'" style="width:150px;" /></div>';
	        }
	    }
	    
	    
	    $html .= '<div class="sub-text">';
	    if($info['realname']){
	        $thml .= '<h2>'.$info['realname'].'</h2>';
	    }
	    $html .= '<h3>';
	    if($info['tasktype'] == 'ios'){
	        $html .= "iOS版";
	    }elseif($info['tasktype'] == 'wx'){
	        $html .= "微信安全测试";
	    }elseif($info['tasktype'] == 'android'){
	        $html .= "Android版";
	    }else{
	        $html .= "WEB扫描";
	    }
	    $html .='</h3>';
	    if($info['version']){
	        $html .='<h3>'.$info['version'].'</h3>';
	    }
	    
	    $html .='<h3>'.$info['subtime'].'</h3>';
	    $html .='</div>
                    </div>
                </div>
	        
                <!-- 包裹内容 -->
                <div class="report-content">
                    <!-- 每一块内容Start -->
                    <div class="report-section">
                        <h2 class="main-title">一、基本信息</h2>
                        <div class="clearfix">&nbsp;</div>';
                        $html .= report_product_ino();
                        $html .='<table class="table">
                            <tbody>';
	    if($info['tasktype'] == 'android' || $info['tasktype'] == 'ios' ){
	        $html .= '<tr>
                                    <td style="width:100px;">应用名称</td>
                                    <td>'.$info['realname'].'</td>
                                    <td>版本号</td>
                                    <td>'.$info['version'].'</td>
                                </tr>
                                <tr>
                                    <td>包名</td>
                                    <td>'.$info['package'].'</td>
                                    <td>时间</td>
                                    <td>'.$info['subtime'].'</td>
                                </tr>';
	    }elseif($info['tasktype'] == 'web' || $info['tasktype'] == 'awvs'){
	        $html .= '<tr>
                                        <td>目标网站</td>
                                        <td colspan="3">'.$info['targeturl'].'</td>
                                    </tr>';
	        
	        if($info['tasktype'] == 'awvs'){
	            $scanoption = json_decode($info['scanoption'],1);
	            $checkslen = count($scanoption['checks']);
	            if($checkslen >0){
	                $html .='<tr>
                                        <td style="width:20%;">扫描规则</td>
                                        <td colspan="3" style="word-wrap:break-word; word-break:normal;">';
	                foreach ($scanoption['checks'] as $keyck => $valck) {
	                    $html .= $rulelist[$valck];
	                    if($keyck != ($checkslen -1)){
	                        $html.=",";
	                    }
	                }
	                $html.='</td>
                                    </tr>';
	            }
	        }
	    }
	    
	    $html.= '<tr><td>高危风险</td>
                                    <td>'.$info['gaowei'].'</td>
                                    <td>风险数</td>
                                    <td>'.$info['bugs'].'</td>
                                </tr>';
	    if($info['tasktype'] == 'android' || $info['tasktype'] == 'ios'){
	        $html .='                <tr>
                                        <td>建议通报数</td>
                                        <td colspan="3">'.$proposal_count.'</td>
                                    </tr>
                                    <tr>
                                        <td>MD5</td>
                                        <td colspan="3">'.$info['md5'].'</td>
                                    </tr>
                                    <tr>
                                        <td>SHA-1</td>
                                        <td colspan="3">'.$info['sha1'].'</td>
                                    </tr>
                                    <tr>
                                        <td>SHA-256</td>
                                        <td colspan="3">'.$info['sha256'].'</td>
                                    </tr>';
	        if($info['tasktype'] != 'ios'){
	            $html .='<tr>
                                            <td>证书信息</td>
                                            <td colspan="3">'.$info['cert'].'</td>
                                        </tr>';
	        }
	        
	    }
	    $html .='</tbody>
                        </table>
                    </div>
                    <!-- 每一块内容END -->
                    ';
	    
	    if($histogram['0'] != false || $histogram['1'] != false || $histogram['2'] != false){
	        
	        makepiechart($histogram,$info['realname']);
	        makebarchart($histogram,$info['realname']);
	        // var_dump($histogram,$info['realname']);
            $html .='<img src="'.__ROOT__."/".UPLOAD_PATH . "temp/" . $info['realname']."bar.png".'" style="width:512px;height:384px;"/><img src="'.__ROOT__."/".UPLOAD_PATH . "temp/" . $info['realname']."pie.png".'" style="width:512px;height:384px;"/>';
	        if ($info['internet_security_level']) {
	            $title_str = get_security_level_title($info['internet_security_level'], $pass_count, $no_pass_count);
	            $html .='<div class="report-section">
                            <h2 class="main-title">二、检测概述</h2>
							<div class="clearfix">&nbsp;</div>
                            <div style="margin-bottom:10px;font-size:20px;">'.$title_str.'</div>
                            <div class="clearfix">&nbsp;</div>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>序号</td>
                                        <td>检测项目</td>
                                        <td>对标</td>
                                        <td>检测结果</td>
                                        <td>危险系数</td>
                                    </tr>';
	            
	            $html2 .= '<div class="report-section">
                                <h2 class="main-title">三、详细检测结果</h2>
                                <div class="clearfix">&nbsp;</div>';
	            
	            
	            foreach ($detecresultinfo as $key => $value) {
	                
	                $html .='<tr>
                                        <td style="text-align:center;">'.($key+1).'</td>
                                        <td>'.$value['case_name'].'</td>
                                        <td>'.trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE')))).'</td>
                                        <td style="text-align:center;">';
	                if($value['risk_level'] == 1){
	                    if($value['issues_count']) {
	                        $html.= '符合(发现'.$value['issues_count'].'处)';
	                    } else {
	                        $html .= '符合';
	                    }
	                } else {
	                    $html.= '不符合(发现'.$value['issues_count'].'处)';
	                }
	                $html .= '</td>
                                        <td style="text-align:center;"><span class="level ';
	                if($value['risk_level'] == 1) {
	                    $value['risk_level'] = $value['vulrisklevel'];
	                }
	                if($value['risk_level'] == 3){
	                    $html .= 'middel';
	                }elseif($value['risk_level'] == 4){
	                    $html .= 'high';
	                }elseif($value['risk_level'] == 2){
	                    $html .= 'low';
	                }else{
	                    $html .= 'safe" style="font-size:12px;';
	                }
	                $html .= '">'.$num_to_zh[$value['risk_level']].'</span></td>
                                </tr>';
	                
	                $html2 .= '<div class="sec-wrapper">
                                <span class="badge"></span>
                                <h3 class="sec-title">'.($key+1).'、 '.$value['vulriskname'].'</h3>
                            </div>';
	                $detection_process = str_replace(array("[.]","[img]","[/img]","&lt;bbr&gt;",'&lt;bbr/&gt;'),array("\"","<img",">","<b>","</b>"),str_replace(array("\r","\r\n","\n","<!--","-->"),"<br/>", htmlspecialchars($value['detection_process'])));
	                $suggestions = str_replace(array("[.]","[img]","[/img]"),array("\"","<img",">"),str_replace(array("\r","\r\n","\n"),"<br/>",$value['suggestions']));
	                
	                if($detection_process == 'N/A') {
	                    $detection_process = '无';
	                }
	                
	                if($suggestions== 'N/A') {
	                    $suggestions = '无';
	                }
	                
	                // if(!$detection_process){
	                // $detection_process = '空';
	                // }
	                if($value['detectionid'] == 44 || $value['detectionid'] == 142){
	                    if($value['detectionid'] == 44) {
	                        $arr=explode("相关内容：",$detection_process);
	                    } else if($value['detectionid'] == 142) {
	                        $arr=explode("请求URL：",$detection_process);
	                    }
	                    
	                    $temp=explode("####",$arr[1]);
	                    
	                    if($value['detectionid'] == 44) {
	                        $arr[1] = $temp[0];
	                        $arr[1]=str_replace("<br/>", "", $arr[1]);
	                        $arr[2]=str_replace("<br/><br/>", "<br/>", $temp[1]);
	                        //                                 $arr[2] = $temp[1];
	                        //                                 $inner_data=json_decode(htmlspecialchars_decode($arr[1]));
	                        $inner_data=json_decode(htmlspecialchars_decode($arr[1]));
	                    } else if($value['detectionid'] == 142) {
	                        $arr[1] = $temp;
	                        foreach ($arr[1] as $k => $v) {
	                            $v=str_replace("<br/>", "", $v);
	                            $v=str_replace("<br/><br/>", "<br/>", $v);
	                            $arr_list = json_decode(htmlspecialchars_decode($v));
	                            foreach ($arr_list as $k1 => $v1) {
	                                $inner_data[] = $v1;
	                            }
	                            unset($arr_list);
	                        }
	                    }
	                    if ($inner_data) {
	                        $html2 .='<table class="detailtable table">
                                <tbody><tr>
                                    <td colspan="2">用例名称</td>
                                    <td colspan="6">'.$value['case_name'].'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" >对标</td>
                                    <td colspan="6" >'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars(trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE')))))).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" >风险描述</td>
                                    <td colspan="6">'.$value['risk_description'].'</td>
                                </tr>
                                <tr>
                                    <td colspan="2">检测过程</td>
                                    <td colspan="6">';
	                        
	                        
	                        $html2 .=$arr[0].' </td></tr>
    		                                	 			<tr>
    					                                	<td style="width:6%;">序号</td>
    		                                	 			<td style="width:14%;">域名</td>
    					                                	<td style="width:30%;">url</td>
    		                                	 			<td style="width:20%;">IP列表</td>
    		                                	 			<td style="width:17%;">注册域名商</td>
    		                                	 			<td style="width:16%;">DNS解析记录</td>
    					                                	</tr>';
	                        
	                        foreach ($inner_data as $k=>$v){
	                            $html2 .='<tr>
    			                                	<td style="width:6%;">'.($k+1).'</td>
                                    	 			<td style="width:14%;">'.$v->host.'</td>
    			                                	<td style="width:30%;">';
	                            
	                            foreach ($v->url as $k1=>$v1){
	                                $html2 .=$v1."<br><br>";
	                            }
	                            $html2 .='</td>
                                    	 		 	  <td style="width:20%;">';
	                            
	                            foreach ($v->ip_list as $k1=>$v1){
	                                $html2 .=$v1."<br>";
	                            }
	                            $html2 .='</td>
                                    	 			  <td style="width:17%;">'.$v->register.'</td>
                                    	 			  <td style="width:16%;">';
	                            
	                            foreach ($v->dns_history as $k1=>$v1){
	                                $html2 .=$v1."<br>";
	                            }
	                            
	                            $html2 .='</td></tr>';
	                            
	                        }
	                        if($value['detectionid'] == 44) {
	                            if (trim($arr[2])) {
	                                $html2 .='<tr><td colspan="8">'.trim($arr[2]).'</td></tr>';
	                            }
	                        }
	                        
	                    } else {
	                        $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td style="width:20%;" colspan="2">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">对标</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars(trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE')))))).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
	                        if($detection_process){
	                            $html2 .='<tr>
		                        <td style="width:20%;" colspan="2">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
	                        }
	                    }
	                    unset($inner_data);
	                }else {
	                    $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td style="width:20%;" colspan="2">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">对标</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars(trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE')))))).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
	                    if($detection_process){
	                        $html2 .='<tr>
		                        <td style="width:20%;" colspan="2">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
	                    }
	                }
	                $html2 .='<tr>
                                <td colspan="2" >风险等级</td>
                                <td colspan="6" >'.$num_to_zh[$value['risk_level']].'</td>
                            </tr>
                            <tr>
                                <td colspan="2" >修复建议</td>
                                <td colspan="6">'.$suggestions.'</td>
                            </tr></tbody></table>';
	                $detection_process = null;
	            }
	        } else {
	            $html .='<div class="report-section">
                            <h2 class="main-title">二、检测概述</h2>
                            <div class="clearfix">&nbsp;</div>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>序号</td>
                                        <td>检测项目</td>
                                        <td>检测类型</td>
                                        <td>是否建议通报</td>
                                        <td>风险数</td>
                                        <td>危险系数</td>
                                    </tr>';
	            
	            $html2 .= '<div class="report-section">
                                <h2 class="main-title">三、详细检测结果</h2>
                                <div class="clearfix">&nbsp;</div>';
	            
	            
	            foreach ($detecresultinfo as $key => $value) {
	                
	                $html .='<tr >
                                        <td style="text-align:center;">'.($key+1).'</td>
                                        <td>'.$value['case_name'].'</td>
                                        <td>'.$value['hvtype'].'</td>';
	                if($value['is_proposal'] == 1) {
	                    $html .='<td style="color:red;text-align:center;">'.$this->is_proposal[$value['is_proposal']].'</td>';
	                } else {
	                    $html .='<td style="text-align:center;">'.$this->is_proposal[$value['is_proposal']].'</td>';
	                }
	                $html .='<td style="text-align:center;">';
	                if($value['issues_count']){
	                    $html .= $value['issues_count'];
	                }else{
	                    $html .= '-';
	                }
	                $html .= '</td>
                                        <td style="text-align:center;"><span class="level ';
	                if($value['risk_level'] == 3){
	                    $html .= 'middel';
	                }elseif($value['risk_level'] == 4){
	                    $html .= 'high';
	                }elseif($value['risk_level'] == 2){
	                    $html .= 'low';
	                }else{
	                    $html .= 'safe" style="font-size:12px;';
	                }
	                $html .= '">'.$num_to_zh[$value['risk_level']].'</span></td>
                                </tr>';
	                
	                $html2 .= '<div class="sec-wrapper">
                                <span class="badge"></span>
                                <h3 class="sec-title">'.($key+1).'、 '.$value['vulriskname'].'</h3>
                            </div>';
	                $detection_process = str_replace(array("[.]","[img]","[/img]","&lt;bbr&gt;",'&lt;bbr/&gt;'),array("\"","<img",">","<b>","</b>"),str_replace(array("\r","\r\n","\n","<!--","-->"),"<br/>", htmlspecialchars($value['detection_process'])));
	                $suggestions = str_replace(array("[.]","[img]","[/img]"),array("\"","<img",">"),str_replace(array("\r","\r\n","\n"),"<br/>",$value['suggestions']));
	                
	                if($detection_process == 'N/A') {
	                    $detection_process = '无';
	                }
	                
	                if($suggestions== 'N/A') {
	                    $suggestions = '无';
	                }
	                
	                // if(!$detection_process){
	                // $detection_process = '空';
	                // }
	                if($value['detectionid'] == 44 || $value['detectionid'] == 142){
	                    if($value['detectionid'] == 44) {
	                        $arr=explode("相关内容：",$detection_process);
	                    } else if($value['detectionid'] == 142) {
	                        $arr=explode("请求URL：",$detection_process);
	                    }
	                    
	                    $temp=explode("####",$arr[1]);
	                    
	                    if($value['detectionid'] == 44) {
	                        $arr[1] = $temp[0];
	                        $arr[1]=str_replace("<br/>", "", $arr[1]);
	                        $arr[2]=str_replace("<br/><br/>", "<br/>", $temp[1]);
	                        //                                 $arr[2] = $temp[1];
	                        //                                 $inner_data=json_decode(htmlspecialchars_decode($arr[1]));
	                        $inner_data=json_decode(htmlspecialchars_decode($arr[1]));
	                    } else if($value['detectionid'] == 142) {
	                        $arr[1] = $temp;
	                        foreach ($arr[1] as $k => $v) {
	                            $v=str_replace("<br/>", "", $v);
	                            $v=str_replace("<br/><br/>", "<br/>", $v);
	                            $arr_list = json_decode(htmlspecialchars_decode($v));
	                            foreach ($arr_list as $k1 => $v1) {
	                                $inner_data[] = $v1;
	                            }
	                            unset($arr_list);
	                        }
	                    }
	                    if ($inner_data) {
	                        $html2 .='<table class="detailtable table">
                                <tbody><tr>
                                    <td colspan="2">用例名称</td>
                                    <td colspan="6">'.$value['case_name'].'</td>
                                </tr>
                                <tr>
                                    <td colspan="2">是否通报建议</td>
                                    <td colspan="6">'.$this->is_proposal[$value['is_proposal']].'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" >风险编号</td>
                                    <td colspan="6" >'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars($value['standard'])).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" >风险描述</td>
                                    <td colspan="6">'.$value['risk_description'].'</td>
                                </tr>
                                <tr>
                                    <td colspan="2">检测过程</td>
                                    <td colspan="6">';
	                        
	                        
	                        $html2 .=$arr[0].' </td></tr>
    		                                	 			<tr>
    					                                	<td style="width:6%;">序号</td>
    		                                	 			<td style="width:14%;">域名</td>
    					                                	<td style="width:30%;">url</td>
    		                                	 			<td style="width:20%;">IP列表</td>
    		                                	 			<td style="width:17%;">注册域名商</td>
    		                                	 			<td style="width:16%;">DNS解析记录</td>
    					                                	</tr>';
	                        
	                        foreach ($inner_data as $k=>$v){
	                            $html2 .='<tr>
    			                                	<td style="width:6%;">'.($k+1).'</td>
                                    	 			<td style="width:14%;">'.$v->host.'</td>
    			                                	<td style="width:30%;">';
	                            
	                            foreach ($v->url as $k1=>$v1){
	                                $html2 .=$v1."<br><br>";
	                            }
	                            $html2 .='</td>
                                    	 		 	  <td style="width:20%;">';
	                            
	                            foreach ($v->ip_list as $k1=>$v1){
	                                $html2 .=$v1."<br>";
	                            }
	                            $html2 .='</td>
                                    	 			  <td style="width:17%;">'.$v->register.'</td>
                                    	 			  <td style="width:16%;">';
	                            
	                            foreach ($v->dns_history as $k1=>$v1){
	                                $html2 .=$v1."<br>";
	                            }
	                            
	                            $html2 .='</td></tr>';
	                            
	                        }
	                        if($value['detectionid'] == 44) {
	                            if (trim($arr[2])) {
	                                $html2 .='<tr><td colspan="8">'.trim($arr[2]).'</td></tr>';
	                            }
	                        }
	                        
	                    } else {
	                        $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td style="width:20%;" colspan="2">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
                            <tr>
                                <td colspan="2">是否通报建议</td>
                                <td colspan="6">'.$this->is_proposal[$value['is_proposal']].'</td>
                            </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">风险编号</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars($value['standard'])).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
	                        if($detection_process){
	                            $html2 .='<tr>
		                        <td style="width:20%;" colspan="2">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
	                        }
	                    }
	                    unset($inner_data);
	                }else {
	                    $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td style="width:20%;" colspan="2">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
                            <tr>
                                <td colspan="2">是否通报建议</td>
                                <td colspan="6">'.$this->is_proposal[$value['is_proposal']].'</td>
                            </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">风险编号</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars($value['standard'])).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;" colspan="2">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
	                    if($detection_process){
	                        $html2 .='<tr>
		                        <td style="width:20%;" colspan="2">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
	                    }
	                }
	                $html2 .='<tr>
                                <td colspan="2" >风险等级</td>
                                <td colspan="6" >'.$num_to_zh[$value['risk_level']].'</td>
                            </tr>
                            <tr>
                                <td colspan="2" >修复建议</td>
                                <td colspan="6">'.$suggestions.'</td>
                            </tr></tbody></table>';
	                $detection_process = null;
	            }
	        }
	        $html .='</tbody>
                                </table>
                            </div>';
	    }
	    $html2 .='</div>
	        
                </div>
                <!-- 包裹内容END -->
            </div>
            </body>
        </html>';
	    echo $html.$html2;
	}
	
	/**
	 * 过滤读取
	 * @param unknown $info
	 * @param string $type
	 * @return string
	 */
	private function get_vul_result($info,$type="world") {
	    $arr_s =  explode(', ', $info);
	    foreach ($arr_s as $key_s => $value_s) {
	        if(strpos($value_s, 'GA/T 1390.3 —2017 网络安全等级保护基本要求 8') !== false) {
	            $str_s[] = $value_s;
	        }
	    }
	    if (sort($str_s)) {
	        if ($type == "world") {
	            $str_string = implode('\n', $str_s);
	        } else if($type == 'html') {
	            $str_string = implode('<br/>', $str_s);
	        }
	        
	    }
	    return $str_string;
	}
	
	/**
	 * 排序
	 * @param unknown $array
	 * @param unknown $row
	 * @param unknown $type
	 * @return unknown
	 */
	private function array_sort($array,$row,$type){
	    $array_temp = array();
	    foreach($array as $k => $v){
	        $array_temp[$v[$row]][] = $v;
	    }
	    
	    if($type == 'asc'){
	        ksort($array_temp);
	    }elseif($type='desc'){
	        krsort($array_temp);
	    }else{
	        
	    }
	    
	    foreach ($array_temp as $key => $value) {
	        foreach ($value as $value_) {
	            $arr [] = $value_;
	        }
	    }
	    
	    return $arr;
	}
}