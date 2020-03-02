<?php

namespace Mare\Controller;

use Think\Controller;

class LogController extends CommonController {

    //日志首页
    public function log_index() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/log_index';
        $user           = D('User');
        $model          = D("Mare/Log");
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        if ($tid == 3 || $tid == 5) {

            $page = ($page == null) ? 0 : I('get.p');
            $countnum = 12;
            $star = (I('get.p', 1) - 1) * $countnum;
            $count = $model->count();

            // 分页
            $p = new \Think\Page($count, $countnum);
            $p->lastSuffix = false;
            $p->setConfig('header', '<br/><li class="rows">共<b>%TOTAL_ROW%</b>条记录  每页<b>' . $countnum . '</b>条  第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
            $p->setConfig('prev', '上一页');
            $p->setConfig('last', '末页');
            $p->setConfig('first', '首页');
            $p->setConfig('next', '下一页');
            $p->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $page = $p->show();

            $list = $model->limit($p->firstRow . ',' . $p->listRows)->order('handletime desc')->select();

            $userlist = $user->field('realname')->order('realname DESC')->select();
            $this->assign('userlist', $userlist);

            //操作用户的ip地址
            $iplist = $model->field('handleip')->group('handleip ASC')->order('handleip ASC')->select();
            $this->assign('iplist', $iplist);

            //操作用户的操作动作
            $handlecontentlist = $model->field('handlecontent')->group('handlecontent ASC')->order('handlecontent ASC')->select();
            $this->assign('handlecontentlist', $handlecontentlist);
            //操作用户的操作动作结果
            $handleresultlist = $model->field('handleresult')->group('handleresult ASC')->order('handleresult ASC')->select();
            $this->assign('handleresultlist', $handleresultlist);


            $this->assign('star', $star);
            $this->assign('pageshow', $page);
            $this->assign('loglist', $list);
            $this->display();
        } else {
            $this->redirect('Mare/Index/index');
        }
    }

    //日志搜索
    public function log_search() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/log_search';
        $model          = D("Mare/Log");
        $user           = D('User');
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        if ($tid == 3 || $tid == 5) {
            $starttime          = strtotime(I('get.starttime'));
            $endtime            = strtotime(I('get.totime'));
            $handleip           = I('get.actionip');
            $userlist           = I('get.user');

            $handleaction       = I('get.handleaction');
            $handleactionresult = I('get.handleactionresult');

            //时间的范围
            if ($starttime != false || $endtime != false) {
                if ($starttime != false && $endtime != false) {
                    $where['handletime'] = array(array('egt', date('Y-m-d H:i:s', $starttime)), array('elt', date('Y-m-d H:i:s', $endtime)), 'AND');
                    if ($starttime > $endtime) {
                        $where['handletime'] = array(array('elt', date('Y-m-d H:i:s', $starttime)), array('egt', date('Y-m-d H:i:s', $endtime)), 'AND');
                    }
                } elseif ($starttime != false && $endtime == false) {
                    $where['handletime'] = array('egt', date('Y-m-d H:i:s', $starttime));
                } elseif ($starttime == false && $endtime != false) {
                    $where['handletime'] = array('elt', date('Y-m-d H:i:s', $endtime));
                }
            }
            //判断操作ip是否为空
            if ($handleip != null && $model->where(array('handleip'=>$handleip))->select()) {
                $where['handleip'] = array('in', $handleip);
            }
            if ($userlist != null && $model->where(array('username'=>$userlist))->select()) {
                $where['username'] = array('in', $userlist);
            }
            if($handleactionresult == "成功" || $handleactionresult == '失败'){
                $where['handleresult']  = array('eq',$handleactionresult);
            }
            if($handleaction != null && $model->where(array('handlecontent'=>$handleaction))->select()){
                $where['handlecontent'] = array('eq',$handleaction);
            }

            $page = ($page == null) ? 0 : (int)I('get.p');
            $countnum = 12;
            $star = (I('get.p', 1) - 1) * $countnum;
            $count = $model->where($where)->count();

            // 分页
            $p = new \Think\Page($count, $countnum);
            $p->lastSuffix = false;
            $p->setConfig('header', '<br/><li class="rows">共<b>%TOTAL_ROW%</b>条记录  每页<b>' . $countnum . '</b>条  第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
            $p->setConfig('prev', '上一页');
            $p->setConfig('last', '末页');
            $p->setConfig('first', '首页');
            $p->setConfig('next', '下一页');
            $p->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $page = $p->show();

            $list = $model->limit($p->firstRow . ',' . $p->listRows)->where($where)->order('handletime desc')->select();

            $userlist = $user->field('realname')->order('realname DESC')->select();
            //print_r($userlist);die();
            $this->assign('userlist', $userlist);

            //操作用户的ip地址
            $iplist = $model->field('handleip')->group('handleip ASC')->order('handleip ASC')->select();
            $this->assign('iplist', $iplist);

             //操作用户的操作动作
            $handlecontentlist = $model->field('handlecontent')->group('handlecontent ASC')->order('handlecontent ASC')->select();
            $this->assign('handlecontentlist', $handlecontentlist);
            //操作用户的操作动作结果
            $handleresultlist = $model->field('handleresult')->group('handleresult ASC')->order('handleresult ASC')->select();
            $this->assign('handleresultlist', $handleresultlist);
            
            /*------------bug修复 qinxuening  2017/8/3---------------*/
            if ($starttime && $endtime && $starttime > $endtime) {
                echo "<script>alert('开始时间不能大于结束时间')</script>";
                return $this->display();
            }
            /*------------bug修复 qinxuening  2017/8/3---------------*/
            
            $this->assign('star', $star);
            $this->assign('pageshow', $page);
            $this->assign('loglist', $list);
            $this->display();
        } else {
            $this->redirect('Mare/Index/index');
        }
    }

    //删除日志
    public function deletelog(){
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
        $id             = (int)I('post.id');
        $model          = D("Mare/Log");
        $user           = D('User');
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/deletelog';
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        if ($tid == 3 || $tid == 5) {
            $res            = $model->where(array('logid'=>$id))->delete();
            $userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
            @$personinfo    = $user->where(array('userid'=>$userid))->find();
            if($res !== false){
                @addMareLog(array(
                    'username'      =>$personinfo['realname'],
                    'handleurl'     =>$url, 
                    'handlecontent' =>'删除用户操作日志',
                    'handleresult'  =>'成功',
                    'handleremarks' =>'用户删除用户操作日志成功'
                ));
                $this->ajaxReturn(array('code'=>'success','info'=>'删除日志成功'));
            }else{
                @addMareLog(array(
                    'username'      =>$personinfo['realname'],
                    'handleurl'     =>$url, 
                    'handlecontent' =>'删除用户操作日志',
                    'handleresult'  =>'失败',
                    'handleremarks' =>'用户删除用户操作日志失败'
                ));
                $this->ajaxReturn(array('code'=>'false','info'=>'删除日志失败'));
            }
        }else{
            $this->redirect('Mare/Index/index');
        }
    }

    //导出日志
    public function exportexcel(){
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
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        if ($tid == 3 || $tid == 5) {
            $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/exportexcel';
            $userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
            @$personinfo    = D('User')->where(array('userid'=>$userid))->find();
            $model = D("Log");

            $starttime          = strtotime(I('get.starttime'));
            $endtime            = strtotime(I('get.totime'));
            $handleip           = I('get.actionip');
            $userlist           = I('get.user');

            $handleaction       = I('get.handleaction');
            $handleactionresult = I('get.handleactionresult');

            //时间的范围
            if ($starttime != false || $endtime != false) {
                if ($starttime != false && $endtime != false) {
                    $where['handletime'] = array(array('egt', date('Y-m-d H:i:s', $starttime)), array('elt', date('Y-m-d H:i:s', $endtime)), 'AND');
                    if ($starttime > $endtime) {
                        $where['handletime'] = array(array('elt', date('Y-m-d H:i:s', $starttime)), array('egt', date('Y-m-d H:i:s', $endtime)), 'AND');
                    }
                } elseif ($starttime != false && $endtime == false) {
                    $where['handletime'] = array('egt', date('Y-m-d H:i:s', $starttime));
                } elseif ($starttime == false && $endtime != false) {
                    $where['handletime'] = array('elt', date('Y-m-d H:i:s', $endtime));
                }
            }
            //判断操作ip是否为空
            if ($handleip != null && $model->where(array('handleip'=>$handleip))->select()) {
                $where['handleip'] = array('in', $handleip);
            }
            if ($userlist != null && $model->where(array('username'=>$userlist))->select()) {
                $where['username'] = array('in', $userlist);
            }
            if($handleactionresult == "成功" || $handleactionresult == '失败'){
                $where['handleresult']  = array('eq',$handleactionresult);
            }
            if($handleaction != null && $model->where(array('handlecontent'=>$handleaction))->select()){
                $where['handlecontent'] = array('eq',$handleaction);
            }
            
            $data = $model->where($where)->order('handletime desc')->select();
            
            $fileName='logfile';
            
            vendor('PHPExcel');
            //设置基本信息
            $PHPExcel = new \PHPExcel();
            
            $PHPExcel->getProperties()->setCreator("本义")
            ->setLastModifiedBy("本义")
            ->setTitle("深圳海云安网络科技有限公司")
            ->setSubject("Mars日志报表")
            ->setDescription("")
            ->setKeywords("Mars")
            ->setCategory("");
            
            $PHPExcel->setActiveSheetIndex(0);
            $PHPExcel->getActiveSheet()->setTitle($fileName);
            
            $subObject = $PHPExcel->getSheet(0);
            $subObject->getProtection()->setSheet(true);
            $subObject->protectCells('A1:C1000',time());
            
            //填入主标题
            $PHPExcel->getActiveSheet()->setCellValue('A1', '深圳海云安网络科技有限公司Mars系统日志');//哪里合并的  在下面mergeCells
            //填入副标题
            $PHPExcel->getActiveSheet()->setCellValue('A2', '导出日期：'.date('Y-m-d',time()));
            
            $PHPExcel->getActiveSheet()->setCellValue('A3', '用户名');
            $PHPExcel->getActiveSheet()->setCellValue('B3', '操作URL');
            $PHPExcel->getActiveSheet()->setCellValue('C3', '操作动作');
            $PHPExcel->getActiveSheet()->setCellValue('D3', '操作结果');
            $PHPExcel->getActiveSheet()->setCellValue('E3', '操作IP');
            $PHPExcel->getActiveSheet()->setCellValue('F3', '操作时间');
            
            foreach ($data as $key => $value) {
            	$PHPExcel->getActiveSheet()->setCellValue('A'.($key+4), $value['username']);
            	$PHPExcel->getActiveSheet()->setCellValue('B'.($key+4), $value['handleurl']);
            	$PHPExcel->getActiveSheet()->setCellValue('C'.($key+4), $value['handlecontent']);
            	$PHPExcel->getActiveSheet()->setCellValue('D'.($key+4), $value['handleresult']);
            	$PHPExcel->getActiveSheet()->setCellValue('E'.($key+4), $value['handleip']);
            	$PHPExcel->getActiveSheet()->setCellValue('F'.($key+4), $value['handletime']);
            }
            
            $PHPExcel->getActiveSheet()->mergeCells('A1:F1');
            $PHPExcel->getActiveSheet()->mergeCells('A2:F2');
            
            
            //设置单元格宽度
            $PHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $PHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
            $PHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $PHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
            $PHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $PHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
            
            
            $PHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(35);
            $PHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(22);
            $PHPExcel->getActiveSheet()->getRowDimension(3)->setRowHeight(20);
          
            
            //设置字体样式
            $PHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('黑体');
            $PHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $PHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
            $PHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $PHPExcel->getActiveSheet()->getStyle('A3:AE3')->getFont()->setBold(true);
            
            $PHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('宋体');
            $PHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(16);
            
            $PHPExcel->getActiveSheet()->getStyle('A4:AE'.($k+2))->getFont()->setSize(10);
            //设置居中
            $PHPExcel->getActiveSheet()->getStyle('A1:AE'.($k+2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            //所有垂直居中
            $PHPExcel->getActiveSheet()->getStyle('A1:AE'.($k+2))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            
            //设置单元格边框
            $PHPExcel->getActiveSheet()->getStyle('A3:AE'.($k+2))->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
            
            //设置自动换行
            $PHPExcel->getActiveSheet()->getStyle('A3:AE'.($k+2))->getAlignment()->setWrapText(true);
            
            //保存为2003格式
            $objWriter = new \PHPExcel_Writer_Excel5($PHPExcel);
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
            header("Content-Type:application/force-download");
            header("Content-Type:application/vnd.ms-execl");
            header("Content-Type:application/octet-stream");
            header("Content-Type:application/download");
        
            //多浏览器下兼容中文标题
            $encoded_filename = urlencode($fileName);
            $ua = $_SERVER["HTTP_USER_AGENT"];
            if (preg_match("/MSIE/", $ua)) {
                header('Content-Disposition: attachment; filename="' . $encoded_filename . '.xls"');
            } else if (preg_match("/Firefox/", $ua)) {
                header('Content-Disposition: attachment; filename*="utf8\'\'' . $fileName . '.xls"');
            } else {
                header('Content-Disposition: attachment; filename="' . $fileName . '.xls"');
            }
            @addMareLog(array(
                'username'      =>$personinfo['realname'],
                'handleurl'     =>$url, 
                'handlecontent' =>'导出操作日志',
                'handleresult'  =>'成功',
                'handleremarks' =>'用户导出操作日志成功'
            ));
            header("Content-Transfer-Encoding:binary");
            $objWriter->save('php://output');
            }else{
                $this->redirect('Mare/Index/index');
        }
    }


    //清空日志
    public function clearedOut(){
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
        $model          = D("Mare/Log");
        $user           = D('User');
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/exportexcel';
        $userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
        if ($tid == 3 || $tid == 5) {
            @$personinfo    = D('User')->where(array('userid'=>$userid))->find();
            if($model->count() == 0){
                $this->ajaxReturn(array('code'=>'success','info'=>'清空日志成功'));
            }else{
                $res = $model->execute('TRUNCATE table at_log;');
                if($res !== false){
                    @addMareLog(array(
                        'username'      =>$personinfo['realname'],
                        'handleurl'     =>$url, 
                        'handlecontent' =>'清空日志',
                        'handleresult'  =>'成功',
                        'handleremarks' =>'用户清空日志成功'
                    ));
                    $this->ajaxReturn(array('code'=>'success','info'=>'清空日志成功'));
                }else{
                    @addMareLog(array(
                        'username'      =>$personinfo['realname'],
                        'handleurl'     =>$url, 
                        'handlecontent' =>'清空日志',
                        'handleresult'  =>'失败',
                        'handleremarks' =>'用户清空日志失败'
                    ));
                    $this->ajaxReturn(array('code'=>'false','info'=>'清空日志失败'));
                }
            }
        }else{
            $this->redirect('Mare/Index/index');
        }
    }
}
