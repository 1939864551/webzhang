<?php

namespace Mare\Controller;

use Think\Controller;

class UserController extends CommonController {
    protected $ldap;
    protected $tid;
    
    public function __construct(){
        parent::__construct();
        $this->tid = $_SESSION[$_SESSION['randomstr']]['tid'];
        $this->ldap = intval(I('get.ldap'));
        
    }

    //用户信息列表
    public function user_index() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/user_index';
        $model = D("Mare/User");
        $tid = $_SESSION[$_SESSION['randomstr']]['tid'];
        
        $ldap = intval(I('get.ldap'));
        if(isset($ldap) && $ldap == 1) {
            $where['ldap'] = 1;
            $data['user_title'] = '域用户列表';
            $data['username'] = '域用户帐号';
        } else {
            $where['ldap'] = 0;
            $data['user_title'] = '用户列表';
            $data['username'] = '登录帐号';
            $where['check_status'] = ['eq', 1];
        }

        
        if ($tid > 3) {
            $where['tid'] = array('lt', $tid);
            $usercount = $model->field('userid,loginemail,realname,phone,email,name as rolename')
                            ->join('left join at_userauth on at_userauth.uid = at_user.userid')
                            ->join('left join at_usertype on at_usertype.id = at_userauth.tid')
                            ->where($where)
                            ->count();
            $countnum = 15;
            $star = ((int)I('get.p', 1) - 1) * $countnum;
            $p = new \Think\Page($usercount, $countnum);
            $p->lastSuffix = false;
            $p->setConfig('header', '<br/><li class="rows">共<b>%TOTAL_ROW%</b>条记录  每页<b>' . $countnum . '</b>条  第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
            $p->setConfig('prev', '上一页');
            $p->setConfig('last', '末页');
            $p->setConfig('first', '首页');
            $p->setConfig('next', '下一页');
            $p->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $page = $p->show();


            $userlist = $model->field('userid,loginemail,realname,phone,email,name as rolename,status,tid,platform')
                            ->join('left join at_userauth on at_userauth.uid = at_user.userid')
                            ->join('left join at_usertype on at_usertype.id = at_userauth.tid')
                            ->where($where)
                            ->limit($p->firstRow . ',' . $p->listRows)
                            ->select();

            /****2017/12/6 在用户管理-管理用户-用户列表中，把用户分为三类：Web用户，Android端测试用户，iOS端测试用户****/
            foreach ($userlist as $key => &$value) {
                if ($value['tid'] != 1) {
                    $value['platform'] = 'Web用户';
                } else {
                    if($value['platform'] == 'ios') {
                        $value['platform'] = 'iOS端测试用户';
                    } else {
                        $value['platform'] = 'Android端测试用户';
                    }
                }
            }
            /****2017/12/6 在用户管理-管理用户-用户列表中，把用户分为三类：Web用户，Android端测试用户，iOS端测试用户****/
            
            //print_r($userlist);
            $this->data = $data;
            $this->assign('userlist', $userlist);
            $this->assign('pageshow', $page);

            $userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
            @$personinfo    = $model->where(array('userid'=>$userid))->find();
            @addMareLog(array(
                'username'      =>$personinfo['realname'],
                'handleurl'     =>$url, 
                'handlecontent' =>'获取用户信息列表',
                'handleresult'  =>'成功',
                'handleremarks' =>'用户获取用户信息列表成功'
            ));
            $this->display();
        } else {
            $this->redirect('Mare/Index/index');
        }
    }

    
    //删除用户,其实就是将用户禁用，即将用户的status改为1
    public function user_forbidden() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/user_forbidden';
        $model          = D("Mare/User");
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        $manuserid      = $_SESSION[$_SESSION['randomstr']]['tid'];
        $userid         = (int)I('get.userid');
        $ldap           = intval(I('get.ldap'));
        if ($ldap && $ldap != 1) {
            $this->redirect('Mare/User/user_index/ldap/1', array('tip' => 'forbidden_user_false'));
            $url = 'Mare/User/user_index/ldap/1';
        } else if($ldap == 1) {
            $url = 'Mare/User/user_index/ldap/1';
        } else {
            $url = 'Mare/User/user_index';
        }
        @$personinfo    = $model->where(array('userid'=>$manuserid))->find();
        if ($tid > 3 && $userid != null) {
            $oldstatus = $model->find($userid)['status'];
            //禁用帐号状态
            if ($oldstatus == 0) {
                $res = $model->where(array('userid' => $userid))->data(array('status' => 1))->save();
                if ($res !== false) {
                    @addMareLog(array(
                        'username'      =>$personinfo['realname'],
                        'handleurl'     =>$url, 
                        'handlecontent' =>'禁用用户',
                        'handleresult'  =>'成功',
                        'handleremarks' =>'用户禁用用户成功'
                    ));
                    $this->redirect($url, array('tip' => 'forbidden_user_success'));
                } else {
                    @addMareLog(array(
                        'username'      =>$personinfo['realname'],
                        'handleurl'     =>$url, 
                        'handlecontent' =>'禁用用户',
                        'handleresult'  =>'失败',
                        'handleremarks' =>'用户禁用用户失败'
                    ));
                    $this->redirect($url, array('tip' => 'forbidden_user_false'));
                }
            } else {
                //激活帐号状态
                $res = $model->where(array('userid' => $userid))->data(array('status' => 0))->save();
                if ($res !== false) {
                    @addMareLog(array(
                        'username'      =>$personinfo['realname'],
                        'handleurl'     =>$url, 
                        'handlecontent' =>'激活用户',
                        'handleresult'  =>'成功',
                        'handleremarks' =>'用户激活用户成功'
                    ));
                    $this->redirect($url, array('tip' => 'activation_user_success'));
                } else {
                    @addMareLog(array(
                        'username'      =>$personinfo['realname'],
                        'handleurl'     =>$url, 
                        'handlecontent' =>'激活用户',
                        'handleresult'  =>'失败',
                        'handleremarks' =>'用户激活用户失败'
                    ));
                    $this->redirect($url, array('tip' => 'activation_user_false'));
                }
            }
        } else {
            $this->redirect($url);
        }
    }

    //修改用户信息模板
    public function user_edit() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/user_edit';
        $model          = D("Mare/User");
        $usertype       = D('usertype');
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        $manuserid      = $_SESSION[$_SESSION['randomstr']]['tid'];
        @$personinfo    = $model->where(array('userid'=>$manuserid))->find();
        $userid = (int)I('get.userid');
        if ($tid > 3 && $userid != null) {
            $ldap = intval(I('get.ldap'));
            if(isset($ldap) && $ldap == 1) {
                $where['id'][] = ['lt', $tid];
                $where['id'][] = ['gt', $ldap];
                $data['user_title'] = '编辑域用户信息';
            }  else {
                $where['id'][] = ['lt', $tid];
                $data['user_title'] = '编辑用户信息';
            }     
            $userinfo = $model
            ->where(array('ldap'=>0))
            ->field('at_user.userid,at_user.loginemail,at_user.realname,at_user.email,at_user.phone,at_user.email,at_user.address,at_user.status,at_userauth.tid,at_usertype.name as rolename,at_user.platform')
            ->join('left join at_userauth on at_userauth.uid = at_user.userid')
            ->join('left join at_usertype on at_usertype.id = at_userauth.tid')
            ->find($userid);
            $type = $usertype->where($where)->select();
            $this->data = $data;
            $this->assign('type', $type);
            $this->assign('userinfo', $userinfo);
             @addMareLog(array(
                'username'      =>$personinfo['realname'],
                'handleurl'     =>$url, 
                'handlecontent' =>'查看编辑用户',
                'handleresult'  =>'成功',
                'handleremarks' =>'用户查看编辑用户成功'
            ));
            $this->display();

        } else {
            $this->redirect('Mare/Index/index');
        }
    }
    
    //用户信息修改
    public function modifyuser() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/modifyuser';
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        if($tid >=4){
            $user                   = D('user');
            $auth                   = D('userauth');
            $data['realname']       = I('post.realname');
            $userid                 = (int)I('post.userid');
            $data['phone']          = I('post.phone');
            $data['platform']       = I('post.platform');
            $data['ldap'] = intval(I('post.ldap'));

            $data['email']          = I('post.email');
            if (in_array(I('post.tid'), [1, 2, 3, 4])){ 
                $data_auth['tid']            = I('post.tid');
            } else {
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'非法操作']));
            }
            
            if($data['ldap'] && !in_array($data['ldap'], [0, 1])){
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'非法操作']));
            }
            
            $manageuserid           = $_SESSION[$_SESSION['randomstr']]['userid'];
            @$personinfo            = $user->where(array('userid'=>$manageuserid))->find();
            $maintain               = D("Maintain");
            $passwd_complexity      = $maintain->where(array('key'=>'passwd_complexity'))->find()['value'];
            $passwd_complexity_rule = 'passwd_complexity_'.$passwd_complexity;
            $parten                 = $maintain->where(array('key'=>$passwd_complexity_rule))->find()['value'];
            $parten_email = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
            $parten_phone = '/^1\d{10}$/';
            if($parten == null){
                $parten = '[A-Za-z]{8,}';
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
            
            if (!$data['ldap']) {
                $newpwd                 = I('post.newpwd');
                $pwd                    = I('post.pwd');
                if ($pwd != NULL || $newpwd != NULL) {
                    if ($newpwd != $pwd || !preg_match("/^{$parten}$/", $newpwd)) {
                        if($newpwd != $pwd){
                            $tipmsg  = "新密码和确认密码不一致";
                        }
                        if(!preg_match("/^{$parten}$/", $newpwd)){
                            if($passwd_complexity == 2){
                                $tipmsg  = "请使用8(含8位)位以上包含字母和数字的密码";
                            }elseif($passwd_complexity == 3){
                                $tipmsg  = "请使用8(含8位)位以上包含字母和数字和特殊字符的密码";
                            }else{
                                $tipmsg = "请使用8(含8位)位以上包含大小写的字母的密码";
                            }
                        }
                        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg' => ['info' => $tipmsg, 'obj' => 'pwd']));
                    }
                    $data['password'] = md5($newpwd . C('PWD_AFTER'));
                }
            }

            $user->startTrans();
            $arrt = $user->where(array('userid' => $userid))->data($data)->save();
            $authuser = $auth->where(array('uid' => $userid))->find();
            $att = $auth->where(array('id' => $authuser['id']))->data($data_auth)->save();
            
            if ($arrt !== false && $att !== false) {
                $user->commit();
                @addMareLog(array(
                    'username'      =>$personinfo['realname'],
                    'handleurl'     =>$url, 
                    'handlecontent' =>'修改用户信息',
                    'handleresult'  =>'成功',
                    'handleremarks' =>'用户修改用户信息成功'
                ));
                $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'msg'=>['info'=>'数据修改成功']));
            } else {
                $user->rollback();
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'修改信息失败']));
            }
        }else{
            $this->redirect('Mare/Index/index');
        }
    }

    //个人信息
    public function user_perinfo() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/user_perinfo';
        // dump($_SESSION);
        $userid = $_SESSION[$_SESSION['randomstr']]['userid'];
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        if($tid >1){
            $user = D('user');
            $personal = $user->where(array('userid' => $userid))->field('at_user.*,name as rolename')->join('left join at_userauth on at_userauth.uid = at_user.userid')->join('left join at_usertype on at_usertype.id = at_userauth.tid')->find();
            @$personinfo    = $user->where(array('userid'=>$userid))->find();
            $this->assign('personal', $personal);
            @addMareLog(array(
                'username'      =>$personinfo['realname'],
                'handleurl'     =>$url, 
                'handlecontent' =>'查看用户信息',
                'handleresult'  =>'成功',
                'handleremarks' =>'用户查看用户信息成功'
            ));
            $this->display();
        }else{
            $this->redirect('Mare/Index/index');
        }
    }

    //个人的信息修改模板
    public function user_peredit() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/user_peredit';
        $userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        if($tid >1){
            $usertype = D('Usertype');
            $user = D('user');
            @$personinfo    = $user->where(array('userid'=>$userid))->find();
            $personal = $user->where(array('userid' => $userid))->field('at_user.*,name as rolename')->join('left join at_userauth on at_userauth.uid = at_user.userid')->join('left join at_usertype on at_usertype.id = at_userauth.tid')->find();
            $this->assign('personal', $personal);
            @addMareLog(array(
                'username'      =>$personinfo['realname'],
                'handleurl'     =>$url, 
                'handlecontent' =>'查看个人信息',
                'handleresult'  =>'成功',
                'handleremarks' =>'用户查看个人信息成功'
            ));
            $this->display();
        }else{
            $this->redirect('Mare/Index/index');
        }
    }

    //修改个人信息
    public function modifyper() {
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
        $url                    = MODULE_NAME.'/'.CONTROLLER_NAME.'/modifyper';
        $user                   = D('user');
        $data['realname']       = I('post.realname');
        $userid                 = $_SESSION[$_SESSION['randomstr']]['userid'];
        $data['address']        = I('post.address');
        $data['phone']          = I('post.phone');
        $newpwd                 = I('post.newpwd');
        $pwd                    = I('post.pwd');
        $oldpwd                 = I('post.oldpwd');
        $data['email']          = I('post.email');
        @$personinfo            = $user->where(array('userid'=>$userid))->find();
        $maintain               = D("Maintain");
        $passwd_complexity      = $maintain->where(array('key'=>'passwd_complexity'))->find()['value'];
        $passwd_complexity_rule = 'passwd_complexity_'.$passwd_complexity;
        $parten                 = $maintain->where(array('key'=>$passwd_complexity_rule))->find()['value'];
        if($parten == null){
            $parten = '[A-Za-z]{8,}';
        }
        if ($pwd != NULL && $newpwd != NULL && $oldpwd != null) {
            if ($newpwd != $pwd || !preg_match("/^{$parten}$/", $newpwd)) {
                
                @addMareLog(array(
                    'username'      =>$personinfo['realname'],
                    'handleurl'     =>$url, 
                    'handlecontent' =>'修改个人信息',
                    'handleresult'  =>'失败',
                    'handleremarks' =>'用户修改个人信息失败'
                ));
                if($newpwd != $pwd){
                    $tipmsg  = "新密码和确认密码不一致";
                }
                if(!preg_match("/^{$parten}$/", $newpwd)){
                    if($passwd_complexity == 2){
                        $tipmsg  = "请使用8(含8位)位以上包含字母和数字的密码";
                    }elseif($passwd_complexity == 3){
                         $tipmsg  = "请使用8(含8位)位以上包含字母和数字和特殊字符的密码";
                    }else{
                        $tipmsg = "请使用8(含8位)位以上包含大小写的字母的密码";
                    }
                }
               $this->ajaxReturn(array('code'=>'perror','info'=>$tipmsg));
            }
            $shu = $user->where(array('userid' => $userid, 'password' => md5($oldpwd . C('PWD_AFTER'))))->find();
            if ($shu) {
                $data['password'] = md5($newpwd . C('PWD_AFTER'));
            } else {
                @addMareLog(array(
                    'username'      =>$personinfo['realname'],
                    'handleurl'     =>$url, 
                    'handlecontent' =>'修改个人信息',
                    'handleresult'  =>'失败',
                    'handleremarks' =>'用户修改个人信息失败'
                ));
                $this->ajaxReturn(array('code'=>'olderror','info'=>'旧密码错误'));
            }
        }


        $arr = $user->where(array('userid' => $userid))->data($data)->save();
        if ($arr !== false) {
            @addMareLog(array(
                'username'      =>$personinfo['realname'],
                'handleurl'     =>$url, 
                'handlecontent' =>'修改个人信息',
                'handleresult'  =>'成功',
                'handleremarks' =>'用户修改个人信息成功'
            ));
            $this->ajaxReturn(array('code'=>'success','info'=>'修改个人信息成功'));
        } else {
            @addMareLog(array(
                'username'      =>$personinfo['realname'],
                'handleurl'     =>$url, 
                'handlecontent' =>'修改个人信息',
                'handleresult'  =>'失败',
                'handleremarks' =>'用户修改个人信息失败'
            ));
            $this->ajaxReturn(array('code'=>'error','info'=>'修改个人信息失败'));
        }
    }

    //增加用户页面
    public function user_add() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/user_add';
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        
        if($tid >=4){
            $usertype       = D('usertype');
            $user           = D("User");
            
            $ldap = intval(I('get.ldap'));
            if(isset($ldap) && $ldap == 1) {
                $where['id'][] = ['lt', $tid];
                $where['id'][] = ['gt', $ldap];
            }  else {
                $where['id'][] = ['lt', $tid];
            }
            
            $type           = $usertype->where($where)->select();
            $this->assign('list', $type);
            $userid = $_SESSION[$_SESSION['randomstr']]['userid'];
            @$personinfo    = $user->where(array('userid'=>$userid))->find();
            @addMareLog(array(
                'username'      =>$personinfo['realname'],
                'handleurl'     =>$url, 
                'handlecontent' =>'查看添加用户页面',
                'handleresult'  =>'成功',
                'handleremarks' =>'用户查看添加用户页面成功'
            ));
            $this->display();
        }else{
            $this->direct('Mare/Index/index');
        }
    }

    //增加用户信息
    public function user_ad() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/user_ad';
        $user = D('user');
        $auth = D('userauth');
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid']; //？
        if($tid >=4){
            $data['realname'] = I('post.realname');
            $tid = (int)I('post.tid');
            $data['address'] = I('post.address');
            $data['phone'] = I('post.phone');
            $data['loginemail'] = I('post.loginemail');
            $data['email'] = I('post.email');
            $data['ldap'] = intval(I('post.ldap'));
            $data['check_status'] = 1;
            $data['regtime'] = date();
            $data['email']          = I('post.email');
            
            if($data['ldap'] && !in_array($data['ldap'], [0, 1])){
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'非法操作']));
            }
            
            $userid = $_SESSION[$_SESSION['randomstr']]['userid'];
            @$personinfo    = $user->where(array('userid'=>$userid))->find(); 
            $maintain               = D("Maintain");
            $passwd_complexity      = $maintain->where(array('key'=>'passwd_complexity'))->find()['value'];
            $passwd_complexity_rule = 'passwd_complexity_'.$passwd_complexity;
            $parten                 = $maintain->where(array('key'=>$passwd_complexity_rule))->find()['value'];
            
            $parten_email = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
            $parten_phone = '/^1\d{10}$/';
            
            if($parten == null){
                $parten = '[A-Za-z]{8,}';
            }
            if($user->where(array('loginemail'=>I('post.loginemail')))->select()){
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'该用户已经存在']));
            }

            if (!in_array(I('post.tid'), [1, 2, 3, 4])){
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'非法操作']));
            }
            
            if($tid == 1){
                if(I('post.platform') == 'android' || I('post.platform') == 'ios'){
                    $data['platform'] = I('post.platform');
                }else{
                    $data['platform'] = 'android';
                }
            }

            if (!$data['loginemail']) {
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'用户名不能为空', 'obj' => 'loginemail']));
            }
            
            if (!$data['realname']) {
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'用户昵称不能为空', 'obj' => 'realname']));
            }
            
            if (!$data['phone']) {
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'手机号码不能为空', 'obj' => 'phone']));
            }else if (!preg_match($parten_phone, $data['phone'])) {
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'手机号码格式不正确', 'obj' => 'phone']));
            }
            
            if (!$data['email']) {
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'邮箱地址不能为空', 'obj' => 'email']));
            } else if(!preg_match($parten_email, $data['email'])) {
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'邮箱格式不正确', 'obj' => 'email']));
            }
            
            if(!$tid) {
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'请选择用户角色', 'obj' => 'tid', 'input_type' => 'select']));
            } else if ($tid == 1 && $data['platform'] == '') {
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'请选择测试手机平台', 'obj' => 'platform', 'input_type' => 'select']));
            }
            if (!$data['ldap']){
                $pwd = I('post.pwd');
                $pwd2 = I('post.pwd2');
                
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
                    @addMareLog(array(
                        'username'      =>$personinfo['realname'],
                        'handleurl'     =>$url, 
                        'handlecontent' =>'添加用户',
                        'handleresult'  =>'失败',
                        'handleremarks' =>'用户添加用户失败'
                    ));
                    $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg' => ['info' => $tipmsg, 'obj' => 'pwd']));
                }
                $data['password'] = md5($pwd . C('PWD_AFTER'));
            }
            $data['regtime']  = time();
            M()->startTrans();
            $userinfo_result = $user->data($data)->add();
            if ($userinfo_result) {
                $data_auth['tid']            = I('post.tid');
                $data_auth['uid']            = $userinfo_result;
                $auth_result = $auth->data($data_auth)->add();
            }
            
            if($userinfo_result && $auth_result) {
                M()->commit();
                @addMareLog(array(
                    'username'      =>$personinfo['realname'],
                    'handleurl'     =>$url,
                    'handlecontent' =>'添加用户',
                    'handleresult'  =>'成功',
                    'handleremarks' =>'用户添加用户成功'
                ));
                $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'msg'=>['info'=>'添加用户成功']));
            } else {
                M()->rollback();
                @addMareLog(array(
                    'username'      =>$personinfo['realname'],
                    'handleurl'     =>$url,
                    'handlecontent' =>'添加用户',
                    'handleresult'  =>'成功',
                    'handleremarks' =>'用户添加用户成功'
                ));
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'添加用户失败']));
            }
        }else{
            $this->redirect('Mare/Index/index');
        }
    }

    //查看用户信息
    public function user_view() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/user_view';

        $model = D("Mare/User");
        $usertype = D('usertype');
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        if($tid >=4){
            $ldap = intval(I('get.ldap'));
            if(isset($ldap) && $ldap == 1) {
                $data['user_title'] = '域用户信息';
            }  else {
                $where['id'][] = ['lt', $tid];
                $data['user_title'] = '用户信息';
            }  
            $userid = I('get.userid');

            $userinfo = $model->field('at_user.userid,at_user.loginemail,at_user.realname,at_user.email,at_user.phone,at_user.address,at_user.status,at_userauth.tid,at_usertype.name as rolename,at_user.job,at_user.dvrtype,at_user.regtime,at_user.count,at_user.lasttime,at_user.ip,at_user.errcount,at_user.lockstarttime')
                            ->join('left join at_userauth on at_userauth.uid = at_user.userid')
                            ->join('left join at_usertype on at_usertype.id = at_userauth.tid')
                            ->find($userid);
            
            $userinfo['regtime'] = date("Y-m-d H:i:s", $userinfo['regtime']);
            $userinfo['lasttime'] = date("Y-m-d H:i:s", $userinfo['lasttime']);
            $userinfo['lockstarttime'] = date("Y-m-d H:i:s", $userinfo['lockstarttime']);
            if ($userinfo['status'] == 0) {
                $userinfo['status'] = '正常';
            } else {
                $userinfo['status'] = '禁用';
            }
            $this->data = $data;
            $this->assign('userinfo', $userinfo);
            $manuserid      = $_SESSION[$_SESSION['randomstr']]['userid'];
            @$personinfo    = $model->where(array('userid'=>$manuserid))->find();
            @addMareLog(array(
                'username'      =>$personinfo['realname'],
                'handleurl'     =>$url, 
                'handlecontent' =>'查看用户信息',
                'handleresult'  =>'成功',
                'handleremarks' =>'用户查看用户信息成功'
            ));
            $this->display();
        }else{
            $this->redirect('Mare/Index/index');
        }
    }
    
    /**
     * 待审核用户
     */
    public function check_status_index() {
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
        if ($this->tid >= 4 && C('REGISTER_MODE') == 1) {
            $User = D('User');
            $userlist = $User->field('userid,loginemail,realname,phone,email,name as rolename,check_status')
            ->join('left join at_userauth on at_userauth.uid = at_user.userid')
            ->join('left join at_usertype on at_usertype.id = at_userauth.tid');
            
            $where['check_status'] = 0;
            $userlist = $this->Tpage($User, $where);
            
            /****2017/12/6 在用户管理-管理用户-用户列表中，把用户分为三类：Web用户，Android端测试用户，iOS端测试用户****/
            foreach ($userlist as $key => &$value) {
                if ($value['tid'] != 1) {
                    $value['platform'] = 'Web用户';
                } else {
                    if($value['platform'] == 'ios') {
                        $value['platform'] = 'iOS端测试用户';
                    } else {
                        $value['platform'] = 'Android端测试用户';
                    }
                }
            }
            /****2017/12/6 在用户管理-管理用户-用户列表中，把用户分为三类：Web用户，Android端测试用户，iOS端测试用户****/
            
            $this->assign('userlist', $userlist);
       
            $this->display();
        } else {
            $this->redirect('Mare/Index/index');
        }
    }

    /**
     * 审核操作
     */
    public function check_pass() {
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
        if ($this->tid == 5 && C('REGISTER_MODE') == 1) {
            $User          = D("User");
            $userid         = intval(I('post.userid'));
            if ($userid && IS_POST) {
                $result = $User->where(array('userid' => $userid))->data(array('check_status' => 1))->save();
                if ($result!== false) {
                    $this->do_log('审核用户', '成功', '审核通过');
                    $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'msg'=>['info'=>'审核通过']));
                } else {
                    $this->do_log('审核用户', '失败', '审核失败');
                    $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'审核失败']));
                }
         }
        } else {
            $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'无权限操作']));
        }
    }
    
}
