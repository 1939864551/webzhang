<?php

namespace Mare\Controller;

use Think\Controller;

class DeviceController extends CommonController {

    //设备列表
    public function dev_index() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/dev_index';
        $device         = D('device');
        $user           = D('User');
        $userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        if($tid == "2" || $tid == '5'){

            $page               = ($page == null) ? 0 : (int)I('get.p');
            $countnum           = 15;
            $star               = (I('get.p',1)-1)*$countnum;
            $count              = $device->count();
            $p  = new \Think\Page($count,$countnum);
            $p->lastSuffix      =false;
            $p->setConfig('header','<br/><li class="rows">共<b>%TOTAL_ROW%</b>条记录  每页<b>'.$countnum.'</b>条  第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
            $p->setConfig('prev','上一页');
            $p->setConfig('last','末页');
            $p->setConfig('first','首页');
            $p->setConfig('next','下一页');
            $p->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $page = $p->show(); 

            $devicelist = $device->join('left join at_user on at_user.userid = at_device.userid')->limit($p->firstRow.','.$p->listRows)->select();
            //  dump($devicelist);die;
            $this->assign('devicelist', $devicelist);
            $this->assign('pageshow',$page);
            
            @$personinfo    = $user->where(array('userid'=>$userid))->find();
            // @addMareLog(array(
                // 'username'      =>$personinfo['realname'],
                // 'handleurl'     =>$url, 
                // 'handlecontent' =>'获取测试任务',
                // 'handleresult'  =>'成功',
                // 'handleremarks' =>'用户设备列表成功'
            // ));
            $this->display();
        }else{
            $this->redirect('Mare/Index/index');
        }
    }

    //设备查看
    public function dev_view() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/dev_view';
        $devid          = I('get.devid');
        $user           = D('User');
        $device         = D('device');
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        if($tid == "2" || $tid == '5'){
            $devicelist = $device->where(array('devid' => $devid))->join('left join at_user on at_user.userid = at_device.userid')->find();
            $this->assign('devic', $devicelist);
            $userid    = $_SESSION[$_SESSION['randomstr']]['userid'];
            @$personinfo    = $user->where(array('userid'=>$userid))->find();
            // @addMareLog(array(
                // 'username'      =>$personinfo['realname'],
                // 'handleurl'     =>$url, 
                // 'handlecontent' =>'查看设备',
                // 'handleresult'  =>'成功',
                // 'handleremarks' =>'用户查看设备成功'
            // ));
            $this->display();
        }else{
            $this->redirect('Mare/Index/index');
        }
    }

    private function dev_modify() {
        $devid = I('get.devid');
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        if($tid == "2" || $tid == '5'){
            $device = D('device');
            $devicelist = $device->where(array('devid' => $devid))->join('left join at_user on at_user.userid = at_device.userid')->find();
            $this->assign('devic', $devicelist);
            $this->display();
        }else{
            $this->redirect('Mare/Index/index');
        }
    }

}
