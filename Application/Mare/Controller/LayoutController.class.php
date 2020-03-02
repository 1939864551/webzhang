<?php
namespace Mare\Controller;
use Think\Controller;

class LayoutController extends Controller {
//其他位置的验证码
    public function create_other_captcha(){
        createCaptcha();
    }

    //验证码
    public function chk_chkcode($code)
	{
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
		 $verify = new \Think\Verify();
		 return $verify->check($code);
	}

    //验证验证码是否符合生成的代码
    public function verify_captcha(){
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
        $maintain = D("Maintain");
        $login_captcha = $maintain->where(array('key'=>'login_captcha'))->find()['value'];
        if($login_captcha == 'on'){
            $code  = $_REQUEST['captcha'];
            $verify =  self::chk_chkcode($code);
            session('captcha',$code);
            $data['info']="failure";
            if($verify){
                 $data['info']="success";
            }
        }else{
            $data['info']="success";
        }
        $this->ajaxReturn($data);
    }

    
    /* 加密验证码 */
    // private function authcode($str){
    //     $key = substr(md5('ThinkPHP.CN'), 5, 8);
    //     $str = substr(md5($str), 8, 10);
    //     return md5($key . $str);
    // }

    //显示头部文件
    public function header(){
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
        $this->display();
    }
    //显示script文件
    public function script(){
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
        $this->display();
    }
    //显示底部文件
    public function footer(){
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
        $this->display();
    }
    //显示左侧文件目录
    public function left_tab(){
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
        $this->display();
    }
    //退出登录
    public function logout(){
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
        session('randomstr',null);
        $this->redirect('Mare/Login/login_user');
    }
    //重启
    public function reboot(){
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
        if($tid >= 4){
            system('/sbin/reboot');
        }
    }
    //关闭电源
    public function poweroff(){
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
        if($tid >= 4){
            system('/sbin/poweroff');
        }
    }
}