<?php

namespace Mare\Controller;

use Think\Controller;

class IndexController extends CommonController {

    public function index() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/index';
        //登录的更新时间
        $userid = $_SESSION[$_SESSION['randomstr']]['userid'];
        $user = D('user');
        $data['lasttime'] = time();
        $tid    = $_SESSION[$_SESSION['randomstr']]['tid'];
        if($tid == 1){
            session(null);
            $this->redirect('Mare/Login/login_user');
        }elseif($tid == 2){
            $this->redirect('Mare/Device/dev_index');
            exit;
        }elseif($tid == 3){
            $this->redirect('Mare/Log/log_index');
            exit;
        }


        $licinfo_list = M('licinfo')->field('task_sum, max_task,grand_appnumber')->find();
        $apprecord_list = M('apprecord')->field('count(*) as count')->select();
        if ($licinfo_list['grand_appnumber'] == -1){
            $check_list['grand_appnumber_msg'] = '';
        } else {
            $check_list['grand_appnumber_msg'] = 'APP个数：'.($licinfo_list['grand_appnumber'] - $apprecord_list[0]['count']) . '个';
        }
        
        if($licinfo_list['max_task'] == -1) {
            $check_list['max_task_msg'] = '';
        } else {
            $check_list['max_task_msg'] = '任务次数：'.($licinfo_list['max_task'] - $licinfo_list['task_sum']) . '次';
        }
        
        if($licinfo_list['grand_appnumber'] == -1 && $licinfo_list['max_task'] == -1) {
            $check_list['none'] = 0;
        } else {
            $check_list['none'] = 1;
        }
        

        $this->assign('check_list', $check_list);
        
        $time = $user->where(array('userid' => $userid))->data($data)->save();


        $authorizationmessage = D('Licinfo')->where(array('id'=>1))->find();

        $this->assign('authorizationmessage', $authorizationmessage);
        @$personinfo=$user->where(array('userid'=>$userid))->find();
        // @addMareLog(array(
            // 'username'      =>$personinfo['realname'],
            // 'handleurl'     =>$url, 
            // 'handlecontent' =>'查看系统状态',
            // 'handleresult'  =>'成功',
            // 'handleremarks' =>'用户查看系统状态成功'
        // ));
        $this->display();
    }
}
