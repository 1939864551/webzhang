<?php
namespace Mare\Controller;
use Think\Controller;

class LoginController extends Controller {
	//用户登录
	public function login_user(){
        session_regenerate_id(); //会话标识
        //跨域请求
//		dump($_SESSION);die;
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问'; as
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }

        if($_SESSION['randomstr']){
			$this->redirect('Mare/Index/index');
		}
		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/login_user';
		if(IS_POST){
		    // header('Content-type:text/html;charset=utf8');
		    $model 			= D("User");
		    $username		= I('post.username');
		    $password		= I('post.password');
		    $data 			= array(
		        'loginemail'=>$username,
		        'password'	=>md5($password.C('PWD_AFTER'))
		        //'status'	=>0
		        //'check_status' => 1
		    );

		    //'check_status' => 1, //去掉
		    if( I('post.captcha') == $_SESSION['captcha'] || I('post.captcha') == null){
		        session('captcha',null);
		        $isExistUser = $model->where(array('loginemail'=>$username))->join('left join at_userauth on  at_userauth.uid= at_user.userid')->find();
		        //,'at_userauth.tid'=>array('gt',1)
		        //'status'=>0, 去掉
		        if($isExistUser){
                    if ($isExistUser['ldap']==1) {
                        //*************域用户登录判断*************chc
                        //调用AD域接口
                        $verify = $this->ldap_verify($username,$password);
                        if($verify) {
                            $res = $model->field('userid,tid,loginemail,check_status,status,realname as nickname,at_usertype.name as authname,lockstarttime')->where(array('loginemail'=>$username))->join('left join at_userauth on at_userauth.uid = at_user.userid')->join('left join at_usertype on at_usertype.id = at_userauth.tid')->find();
                        }else{
                            $res = false;
                        } 
                        //*************域用户登录判断************
                    }else{
                        $res = $model->field('userid,tid,loginemail,check_status,status,realname as nickname,at_usertype.name as authname,lockstarttime')->where($data)->join('left join at_userauth on at_userauth.uid = at_user.userid')->join('left join at_usertype on at_usertype.id = at_userauth.tid')->find();
                        if($res){
                            if ($res['check_status'] == 0) {
                                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'该用户未通过审核，请联系管理员']));
                            }
                        }
                    }
                    if ($res['tid'] == 1) {
                        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'测试员账号只能在安全猎手上登陆！']));
                    }
		            //P($res);die();
		            $maintain  		= D("Maintain");
		            $login_limit 	= json_decode($maintain->where(array('key'=>'login_limit'))->find()['value'],true);
		            if($res != false)
		            {
		                if ($res['status'] == 1) {
		                    $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'该用户已经被禁用，请联系管理员']));
		                }
		                
		                if($login_limit['login_limit'] == 'on'){
		                    if($res['lockstarttime'] > time()){
		                        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'您登录的密码错误过多，账号暂时被多冻结']));
		                    }
		                }
		                $randomstr 	= createRand(32);
		                session('randomstr',$randomstr);
		                session($randomstr,$res);
		                
		                $user_info['lasttime'] = time();
		                $user_info['ip'] = $_SERVER['REMOTE_ADDR'];
		                $user_info['errcount'] = 0;
		                $user_info['lockstarttime'] = 0;
		                
		                @$model->where(array('userid'=>$res['userid']))->data($user_info)->save();
		                
		                @$personinfo    = $model->where(array('userid'=>$res['userid']))->find();
		                
		                @addMareLog(array(
		                    'username'      =>$personinfo['realname'],
		                    'handleurl'     =>$url,
		                    'handlecontent' =>'登录',
		                    'handleresult'  =>'成功',
		                    'handleremarks' =>'用户登录成功'
		                ));
                        
		                
		                $this->ajaxReturn(array('code'=>'1', 'status' => 'success'));
		                //$this->redirect('Mare/Index/index');
		                // $this->ajaxReturn(array('code'=>'success'));
		            }
		            else
		            {
		                @addMareLog(array(
		                    'username'      =>$username,
		                    'handleurl'     =>$url,
		                    'handlecontent' =>'登录',
		                    'handleresult'  =>'失败',
		                    'handleremarks' =>'用户登录失败'
		                ));
		                
		                if($login_limit['login_limit'] == 'on'){
		                    if($isExistUser['errcount'] >= $login_limit['error_num']){
		                        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'您登录的密码错误过多，账号暂时被多冻结']));
		                    }else{
		                        if ($isExistUser['errcount'] + 1>= $login_limit['error_num']){
		                            $lockstarttime  = ($login_limit['login_time_limit']*60)+time();
		                            $model->where(array('userid'=>$isExistUser['userid']))
		                            ->data(['lockstarttime' => $lockstarttime,'errcount'=>($isExistUser['errcount']+1)])
		                            ->save();
		                        }
		                        $model->where(['userid'=>$isExistUser['userid']])
		                        ->data(['errcount'=>($isExistUser['errcount']+1)])
		                        ->save();
		                    }
		                    
		                }
		                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'登录的用户名或密码错误']));
		                // $this->ajaxReturn(array('code'=>'false','info'=>'LOGIN_ERROR'));
		            }
		        }else{
		            
		            
		            
		            @addMareLog(array(
		                'username'      =>$username,
		                'handleurl'     =>$url,
		                'handlecontent' =>'登录',
		                'handleresult'  =>'失败',
		                'handleremarks' =>'用户登录失败'
		            ));
		            $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'登录的用户名或密码错误']));
		            // $this->ajaxReturn(array('code'=>'false','info'=>'LOGIN_STATUS_ERROR'));
		        }
		    }
		    
		}
		if (getSetMode() == 2) {
			C('TOP_TEXT','MAIS');
            C('TITLE_TEXT','移动应用安全监测系统');
		}
		$maintain = D("Maintain");
        $login_captcha = $maintain->where(array('key'=>'login_captcha'))->find()['value'];
        $this->assign('login_captcha',$login_captcha);
		$this->display();			
	}

	//用户注册页面显示
	public function register_index(){

        //跨域请求
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问'; as
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }

        if (!C('REGISTER_MODE')) {
	        $this->error('访问错误');
	    }
	    $this->display();
	}
	
	//用户注册
	public function register_user(){
        //跨域请求
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问'; as
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
	    if (!C('REGISTER_MODE')) {
	        $this->error('访问错误');
	    }

	    $user = D('user');
	    $auth = D('userauth');
	    $maintain               = D("Maintain");
	    $passwd_complexity      = $maintain->where(array('key'=>'passwd_complexity'))->find()['value'];
	    $passwd_complexity_rule = 'passwd_complexity_'.$passwd_complexity;
	    $parten                 = $maintain->where(array('key'=>$passwd_complexity_rule))->find()['value'];
	    $parten_email = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
	    $parten_phone = '/^1\d{10}$/';
	    if($parten == null){
	        $parten = '[A-Za-z]{8,}';
	    }

	    $tid = 2;
	    
	    $data['realname'] = I('post.realname');
	    $data['address'] = trim(I('post.address'));
	    $data['phone'] = trim(I('post.phone'));
	    $data['loginemail'] = trim(I('post.loginemail'));
	    $data['email'] = trim(I('post.email'));
	    $data['regtime'] = trim(date());
	    $data['check_status'] = 0;
	    $pwd = trim(I('post.pwd'));
	    $pwd2 = trim(I('post.pwd2'));
	    
	    if($user->where(array('loginemail'=> $data['loginemail']))->find()) {
	        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'该用户已经被注册', 'obj' => 'loginemail']));
	    }
	    
// 	    if ($user->where(array('phone'=> $data['phone']))->find()) {
// 	        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'该手机号码被注册', 'obj' => 'phone']));
// 	    }
	    
// 	    if ($user->where(array('email'=> $data['email']))->find()) {
// 	        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'该邮箱被注册', 'obj' => 'email']));
// 	    }
	    
	    if (!$data['loginemail']) {
	        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'用户名不能为空', 'obj' => 'loginemail']));
	    }
	    
	    if (!$data['realname']) {
	        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'用户昵称不能为空', 'obj' => 'realname']));
	    }
	    
	    if (!$data['phone']) {
	        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'手机号码不能为空', 'obj' => 'phone']));
	    } else if (!preg_match($parten_phone, $data['phone'])) {
	        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'手机号码格式不正确', 'obj' => 'phone']));
	    }
	    
	    if (!$data['email']) {
	        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'邮箱地址不能为空', 'obj' => 'email']));
	    } else if(!preg_match($parten_email, $data['email'])) {
	        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'邮箱格式不正确', 'obj' => 'email']));
	    }
	    
	    if(!$pwd) {
	        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'密码不能为空', 'obj' => 'pwd']));
	    }
	    
	    if ($pwd != $pwd2) {
	        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'密码和确认密码不一致', 'obj' => 'pwd']));
	    }
	    if(!preg_match("/^{$parten}$/", $pwd)){
	        if($passwd_complexity == 2){
	            $tipmsg  = "请使用8(含8位)位以上包含字母和数字的密码";
	        }elseif($passwd_complexity == 3){
	            $tipmsg  = "请使用8(含8位)位以上包含字母和数字和特殊字符的密码";
	        }else{
	            $tipmsg = "请使用8(含8位)位以上包含大小写的字母的密码";
	        }
	        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg' => ['info' => $tipmsg, 'obj' => 'pwd']));
	    }
	    
	    $data['password'] = md5($pwd . C('PWD_AFTER'));
	    $data['regtime']  = time();
	    $as = $user->data($data)->add();
	    if ($as) {
	        $where['tid'] = $tid;
	        $where['uid'] = $as;
	        $au = $auth->data($where)->add();
	        if ($au) {
	            $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'msg'=>['info'=>'注册成功']));
	        } else {
	            $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'注册失败', 'obj' => 'loginemail']));
	        }
	    } else {
	        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'注册失败', 'obj' => 'loginemail']));
	    }
	}
	
	//域用户登录请求接口
	public function ldap_verify($user,$password){
        //跨域请求
        $ref=$_SERVER['HTTP_REFERER'];
        if($ref==''){
            //echo '对不起，不允许从地址栏访问'; as
        }else{
            $url=parse_url($ref);
            if($url['host']!=$_SERVER['HTTP_HOST']){
                echo "<script>alert('盗链请求，禁止访问')</script>";
                die;
            }
        }
	    //设定域信息
	    $domain = C('domain'); //设定域名
	    $basedn = C('basedn'); //如果域名为“b.a.com”,则此处为“dc=b,dc=a,dc=com”
	    //echo "$domain ".$user;
	    //exit();
	    
	    $ad = ldap_connect ( "ldap://{$domain}" ) or $this->error("域服务 $domain 器未响应");
	    ldap_set_option ( $ad, LDAP_OPT_PROTOCOL_VERSION, 3 );
	    ldap_set_option ( $ad, LDAP_OPT_REFERRALS, 0 );
	    @ldap_bind ( $ad, "{$user}@{$domain}", $password ) or $this->error('域账号或密码错误');
	    
	    return $user;
	}
	
	//当方法不存在的时候
	public function _empty(){
		// @addMareLog(array(
			// 'handleurl'     =>$_SERVER['REQUEST_URI'], 
            // 'handlecontent' =>'非法访问不存在的URL',
			// 'handleresult'  =>'成功',
            // ));
		echo '';
	}
}
?>