<?php

namespace Mare\Controller;

use Think\Controller;


class ReportController extends CommonController {
    private $is_proposal;
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
        $this->is_proposal = [
            '1'=> '是',
            '2'=>'否',
        ];
    }

    //下载报表的模板
    public function rep_index() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/rep_index';
        $model          = D("Mare/Appinfo");
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        $user           = D("User");
        if ($tid == 2 || $tid == 5) {
            /***************分析员下载限制条件qinxuening 2017/8/9********************/
            if($tid == 2) {
                $userid = $_SESSION[$_SESSION['randomstr']]['userid'];
                $where['userid'] = $userid;
            }
            $where['status'] = ['egt', 8];
            /***************分析员下载限制条件qinxuening 2017/8/9********************/
            
            $tasklist = $model->field('appid,task_name')->where($where)->order('appid desc')->select();
            $this->assign('tasklist', $tasklist);
            $userid    = $_SESSION[$_SESSION['randomstr']]['userid'];
            @$personinfo    = $user->where(array('userid'=>$userid))->find();

            // @addMareLog(array(
                // 'username'      =>$personinfo['realname'],
                // 'handleurl'     =>$url, 
                // 'handlecontent' =>'查看下载页面',
                // 'handleresult'  =>'成功',
                // 'handleremarks' =>'用户查看下载页面成功'
            // ));
            $this->display();
        } else {
            $this->redirect('Mare/Index/index');
        }
    }

     //下载报表的模板
    public function rep_down_index() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/rep_down_index';
        $model          = D("Mare/Appinfo");
        $tid            = $_SESSION[$_SESSION['randomstr']]['tid'];
        $user           = D("User");

        if ($tid == 2 || $tid == 5) {
            // $tasklist = $model->field('appid,task_name')->where(array('status' => array('egt', 8)))->select();
            // $this->assign('tasklist', $tasklist);
            $appid          = I('get.appid'); 
            $goalappinfo    = $model->find($appid);
            $this->assign('taskinfo',$goalappinfo);
            $userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
            @$personinfo    = $user->where(array('userid'=>$userid))->find();

            // @addMareLog(array(
                // 'username'      =>$personinfo['realname'],
                // 'handleurl'     =>$url, 
                // 'handlecontent' =>'查看下载页面',
                // 'handleresult'  =>'成功',
                // 'handleremarks' =>'用户查看下载页面成功'
            // ));
            $this->display();
        } else {
            $this->redirect('Mare/Index/index');
        }
    }



    //下载报表
    public function rep_down() {
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
        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/rep_down';
        $model          = D("Mare/Appinfo");
        $vulinfo        = D('Vulinfo');
        $user           = D("User");
        $tid = $_SESSION[$_SESSION['randomstr']]['tid'];
        if ($tid == 2 || $tid == 5) {
            $appid = (int)I('post.appid');
            
            if ($tid == 2) {
                if(!check_auth_user($appid)) {
                    $this->ajaxReturn(array('code' => 'false','info'=>'无权限下载')); //qinxuenig 2017/8/9
                }
            }
            
            $info = $model->where(array('status' => array('egt', 8)))->find($appid);
            if ($appid == null || $info == null) {
                $this->ajaxReturn(array('code' => 'false','info'=>'请选择任务'));
            }
            $resultshow = I('post.riskgrade');
            $tmp =array();
            if(I('post.riskgrade1') || I('post.riskgrade2') || I('post.riskgrade3') || I('post.riskgrade4')){
                if(I('post.riskgrade1')){
                     // $tmp .= ','.(int)I('post.riskgrade1');
                     $issues_severity[] = (int)I('post.riskgrade1');
                }
                if(I('post.riskgrade2')){
                     // $tmp .= ','.(int)I('post.riskgrade2');
                     $issues_severity[] = (int)I('post.riskgrade2');
                }
                if(I('post.riskgrade3')){
                     // $tmp .= ','.(int)I('post.riskgrade3');
                     $issues_severity[] = (int)I('post.riskgrade3');
                }
                if(I('post.riskgrade4')){
                     // $tmp .= ','.(int)I('post.riskgrade3');
                     $issues_severity[] = (int)I('post.riskgrade4');
                }
            }
            

            //判断是否有限制显示高中低通过的报告的数据记录
            if ($issues_severity) {
                $serverity = join("_",$issues_severity);
            }

            $apptoken           = $info['apptoken'];
            $teachaddress       = './Uploads/pdf/'.createRand(25).time().createRand(25).'.pdf';
            $techreportpath     = getcwd().'/'.$teachaddress;
            if(I('post.task') == "pdf" && I('post.reporttype') == 'techreport'){
                header('Content-Type:text/html;charset=utf8');
//                 $fileName_ = $info['realname']==null?trim($info['task_name']).'_techreport.pdf':trim($info['realname']).'_techreport.pdf';
                $fileName_ = $info['realname']==null?trim($info['task_name']):trim($info['realname']);
                $test_type = get_task_type($info['tasktype']);
                $fileName_ = $fileName_.'_'.$test_type.'_技术报告_'.date('Y_m_d',time()).'.pdf';
                
//                 $teachaddress       = './Uploads/pdf/'.$fileName.'_techreport.pdf';
//                 $techreportpath     = getcwd().'/'.$teachaddress;
                if($serverity){                    
                    $teachaddress       = './Uploads/pdf/'.createRand(25).time().createRand(25).'.pdf';
                    $techreportpath     = getcwd().'/'.$teachaddress;
                    $res                = shell_exec('xvfb-run -a -s "-screen 0 640x480x16" wkhtmltopdf https://localhost:443'.U('Android/Api/makeTechReport',array('apptoken'=>$apptoken,'serverity'=>$serverity)).' '.$techreportpath);
                    if(strpos($res,'Done')){
                        session('conditionpdf_'.$appid,array('appid'=>$appid,'high_mid_low'=>$serverity,'report_address'=>$teachaddress));
                    }else{
                        echo "<script >alert('生成失败');history.go(-1);</script>";
                    } 
                   
                    $url = __ROOT__.'/'.$teachaddress;
                    if(is_file($teachaddress)){
                        localDownFile($teachaddress,$fileName_); 
                    }else{
                         echo "<script>alert('生成失败');history.go(-1);</script>";
                    }
                    unset($_SESSION['conditionpdf_'.$appid]);
                }else{
                    $serverity          = "2_3_4";
                    $res                = shell_exec('xvfb-run -a -s "-screen 0 640x480x16" wkhtmltopdf https://localhost:443'.U('Android/Api/makeTechReport',array('apptoken'=>$apptoken,'serverity'=>$serverity)).' '.$techreportpath);
                    if(strpos($res,'Done')){
                        session('conditionpdf_'.$appid,array('appid'=>$appid,'high_mid_low'=>$serverity,'report_address'=>$teachaddress));
                    }else{
                        echo "<script>alert('生成失败');history.go(-1);</script>";
                    } 
                    $url = __ROOT__.'/'.$teachaddress;
                    // header ( "Location: {$url}" );
                    if(is_file($teachaddress)){
                        localDownFile($teachaddress,$fileName_); 
                    }else{
                         echo "<script>alert('生成失败');history.go(-1);</script>";
                    }
                    unset($_SESSION['conditionpdf_'.$appid]);
                }
            }elseif(I('post.task') == "pdf" && I('post.reporttype') == 'manage'){
//                 $fileName_ = $info['realname']==null?trim($info['task_name']).'_manage.pdf':trim($info['realname']).'_manage.pdf';
                $fileName_ = $info['realname']==null?trim($info['task_name']):trim($info['realname']);
                $test_type = get_task_type($info['tasktype']);
                $fileName_ = $fileName_.'_'.$test_type.'_管理报告_'.date('Y_m_d',time()).'.pdf';
//                 $teachaddress       = './Uploads/pdf/'.$fileName.'_manage.pdf';
//                 $techreportpath     = getcwd().'/'.$teachaddress;
                // $url = __ROOT__.'/'.$info['managereportpath'];
                $res                = shell_exec('xvfb-run -a -s "-screen 0 640x480x16" wkhtmltopdf https://localhost:443'.U('Android/Api/makeManagehReport',array('apptoken'=>$apptoken,'serverity'=>$serverity)).' '.$techreportpath);
                if(strpos($res,'Done')){
                    session('conditionpdf_'.$appid,array('appid'=>$appid,'report_address'=>$teachaddress));
                }else{
                    echo "<script>alert('生成失败');history.go(-1);</script>";
                } 
                // $url = __ROOT__.'/'.$teachaddress;
                // header ( "Location: {$url}" );
                // localDownFile($teachaddress);
                if(is_file($teachaddress)){
                    localDownFile($teachaddress,$fileName_); 
                }else{
                     echo "<script>alert('生成失败');history.go(-1);</script>";
                }
                unset($_SESSION['conditionpdf_'.$appid]);
            }elseif(I('post.task') == "word" && I('post.reporttype') == 'techreport'){
            	$this->makeTechWord($apptoken,$serverity);
            }elseif(I('post.task') == "word" && I('post.reporttype') == 'manage'){
                $this->makeManagehWord($appid,$serverity);
            }elseif(I('post.task') == "html" && I('post.reporttype') == 'techreport'){
                header("Location:".U('Mare/Report/htmlTeachReoprt',array('apptoken'=>$apptoken,'serverity'=>$serverity)));
            }elseif(I('post.task') == "html" && I('post.reporttype') == 'manage'){
                header("Location:".U('Mare/Report/htmlManageReoprt',array('apptoken'=>$apptoken,'serverity'=>$serverity)));
            }elseif(I('post.task') == "word" && I('post.reporttype') == 'proposal') {
                $this->makeTechProposal($apptoken,$serverity);
            }elseif(I('post.task') == "html" && I('post.reporttype') == 'proposal'){
                header("Location:".U('Mare/Report/proposalTeachReoprt',array('apptoken'=>$apptoken,'serverity'=>$serverity)));
            }elseif(I('post.task') == "pdf" && I('post.reporttype') == 'proposal'){
                header('Content-Type:text/html;charset=utf8');
                //                 $fileName_ = $info['realname']==null?trim($info['task_name']).'_techreport.pdf':trim($info['realname']).'_techreport.pdf';
                $fileName_ = $info['realname']==null?trim($info['task_name']):trim($info['realname']);
                $test_type = get_task_type($info['tasktype']);
                $fileName_ = $fileName_.'_'.$test_type.'_通报报告_'.date('Y_m_d',time()).'.pdf';
                if($serverity){
                    $teachaddress       = './Uploads/pdf/'.createRand(25).time().createRand(25).'.pdf';
                    $techreportpath     = getcwd().'/'.$teachaddress;
                    $res                = shell_exec('xvfb-run -a -s "-screen 0 640x480x16" wkhtmltopdf https://localhost:443'.U('Android/Api/makeProposalReport',array('apptoken'=>$apptoken,'serverity'=>$serverity)).' '.$techreportpath);
                    if(strpos($res,'Done')){
                        session('conditionpdf_'.$appid,array('appid'=>$appid,'high_mid_low'=>$serverity,'report_address'=>$teachaddress));
                    }else{
                        echo "<script >alert('生成失败');history.go(-1);</script>";
                    }
                    
                    $url = __ROOT__.'/'.$teachaddress;
                    if(is_file($teachaddress)){
                        localDownFile($teachaddress,$fileName_);
                    }else{
                        echo "<script>alert('生成失败');history.go(-1);</script>";
                    }
                    unset($_SESSION['conditionpdf_'.$appid]);
                }else{
                    $serverity          = "2_3_4";
                    $res                = shell_exec('xvfb-run -a -s "-screen 0 640x480x16" wkhtmltopdf https://localhost:443'.U('Android/Api/makeProposalReport',array('apptoken'=>$apptoken,'serverity'=>$serverity)).' '.$techreportpath);
                    if(strpos($res,'Done')){
                        session('conditionpdf_'.$appid,array('appid'=>$appid,'high_mid_low'=>$serverity,'report_address'=>$teachaddress));
                    }else{
                        echo "<script>alert('生成失败');history.go(-1);</script>";
                    }
                    $url = __ROOT__.'/'.$teachaddress;
                    if(is_file($teachaddress)){
                        localDownFile($teachaddress,$fileName_);
                    }else{
                        echo "<script>alert('生成失败');history.go(-1);</script>";
                    }
                    unset($_SESSION['conditionpdf_'.$appid]);
                }
            }

            //判断pdf文件大小是否大于25M,大于就删除该文件下的文件,以释放空间

            $pdfdir         = getcwd()."/Uploads/pdf";
            $getpdffilesize = shell_exec("du -h --max-depth=1 {$pdfdir}");
            $exploderes     = explode("\t", $getpdffilesize);
            if((substr($exploderes['0'],0,-1) > '25' && substr($exploderes['0'],-1) == 'M') || (substr($exploderes['0'],-1) == 'G')){
                if(trim($pdfdir) == trim($exploderes[1])){
                   @shell_exec("rm -rf {$pdfdir}/*");
                }
            }
            exit;
        } else {
            $this->redirect('Mare/Index/index');
        }
    }
    
    public function makepiechart($data,$name) {
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
    	Vendor('jpgraph.jpgraph');
    	Vendor('jpgraph.jpgraph_pie');
    	Vendor('jpgraph.jpgraph_pie3d');
    	//vendor ('Examples/jpgraph/jpgraph.php'); //载入基本类
    	//require_once ('Examples/jpgraph/jpgraph_bar.php');//载入柱形图类
        //     	$appinfo = D('Appinfo');
        //     	$detec = D('Detection');
    	// dump($data);
    	$arr = array("高危", "中危", "低危");
     	//$data = array(9,44,356);
        //     	$data[] = $detec->where(array('appid' => $appid, 'result' => critical))->count();
        //     	$data[] = $detec->where(array('appid' => $appid, 'result' => high))->count();
        //     	$data[] = $detec->where(array('appid' => $appid, 'result' => medium))->count();
        //     	$data[] = $detec->where(array('appid' => $appid, 'result' => low))->count();
    	//dump($data);
    	//$data = array(19,23,34,38,45,67,71,78,85,87,90,96);
    	//dump($data);die;
    	$graph = new \PieGraph(400,300);
    	 
    	//$graph->SetMarginColor('lightblue');//设置图形颜色
    	$graph->SetShadow();
    	//$graph->SetColor('#f0f0f0');
    	 
    	$graph->title->Set("风险等级分布图");
    	//     	$graph->title->SetPos(0.5,0.5);
    	$graph->title->SetFont(FF_SIMSUN,FS_BOLD,16);
    	$graph->legend->SetFont(FF_SIMSUN,FS_BOLD,14);//设置图例字体
    	 
    	$graph->legend->Pos(0.25,0.85);
    	//$graph->legend->SetColor("darkred","darkred");
    	//$graph->legend->
    	$pieplot = new \PiePlot($data); //创建PiePlot对象
    	//$pieplot->SetSliceColors(array('red','blue','green','orange'));
    	//$pieplot->SetTheme('earth');//earth、pastel、sand、water
    	$pieplot->SetSize(0.3);
    	$pieplot->ShowBorder();
    	$pieplot ->SetCenter(0.5);
    	//$pieplot->ExplodeSlice(2,10);
    	$pieplot->value->SetColor("white");//darkred
    	$pieplot->value->SetFont(FF_SIMSUN,FS_BOLD,12);
    	//$pieplot->value->SetFormat('%0.1f'); //值的格式化
    	$pieplot->ExplodeAll(10);
    	 
    	//$pieplot->SetAngle(80);
    	//$bplot->SetFillGradient("navy","lightsteelblue",GRAD_MIDVER);
    	//$pieplot->SetSliceColors(array('#f0f0f0','#f0f0f0','#f0f0f0','#f0f0f0'));
    	 
    	$pieplot->SetLegends(array("高危", "中危", "低危")); //设置图例
    	
    	$pieplot->SetLabelPos(0.65);
    	$pieplot->SetShadow();
    	$graph->Add($pieplot);
    	$pieplot->SetSliceColors(array('#cc0000','#ff7a00','#377aa6'));
    	//setfillcolor
    	 
    	//$graph->Add($bplot);
    	 
    	$FileName = UPLOAD_PATH . "temp/" . $name."pie.png";
    	//$graph->StrokeCSIM();
    	
    	unlink($FileName);
    	$graph->Stroke($FileName);
    	 
    	//$graph->Stroke($FileName);
    }
    

    public function makebarchart($datay,$name) {
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
    
    	Vendor('jpgraph.jpgraph');
    	Vendor('jpgraph.jpgraph_bar');
    	//dump($datay);die;
        //     	$appinfo = D('Appinfo');
        //     	$detec = D('Detection');
            	 
        //     	$datay[] = $detec->where(array('appid' => $appid, 'result' => critical))->count();
        //     	$datay[] = $detec->where(array('appid' => $appid, 'result' => high))->count();
        //     	$datay[] = $detec->where(array('appid' => $appid, 'result' => medium))->count();
        //     	$datay[] = $detec->where(array('appid' => $appid, 'result' => low))->count();
    	 
    	// $MyData->setAxisName(0,"Hits");
    	 
    	$arr = array("高危数", "中危数", "低危数");
    	//$datay=array(1992,1993,1995,1996,1997,1998,2001);
    
    	// Size of graph
    	$width=400;
    	$height=300;
    
    	// Set the basic parameters of the graph
    	$graph = new \Graph($width,$height);
    	$graph->SetScale('textint');
    	//$graph->legend->SetFont(FF_SIMSUN,FS_NORMAL,16);//设置图例字体
    	//$graph->set
    	$top = 60;$bottom = 30;$left = 80;$right = 40;
    	$graph->Set90AndMargin($left,$right,$top,$bottom);
    	//$graph->SetShadow("green");
    	//$graph->SetColor("green");
    
    	$graph->title->Set("风险统计图");
    	//     	$graph->title->SetPos(0.5,0.5);
    	$graph->title->SetFont(FF_SIMSUN,FS_BOLD,16);
    	//$graph->legend->SetFont(FF_SIMSUN,FS_NORMAL);//设置图例字体
    	 
    	 
    
    	// Setup labels
    	//$lbl = array("Andrew\nTait","Thomas\nAnderssen","Kevin\nSpacey","Nick\nDavidsson",
    	//"David\nLindquist","Jason\nTait","Lorin\nPersson");
    	$graph->xaxis->SetFont(FF_SIMSUN,FS_BOLD,14);//设置图例字体
    	$graph->xaxis->SetTickLabels($arr);
    
    	// Label align for X-axis
    	//$graph->xaxis->SetLabelAlign('right','center','right');
    
    	// Label align for Y-axis
    	//$graph->yaxis->SetLabelAlign('center','bottom');
    
    	// Titles
    
    	$graph->title->Set("各级风险统计图");
    
    	// Create a bar pot
    	$bplot = new \BarPlot($datay);
    	 
    	 
    	//     	$bplot->SetColor(array('blue','orange','yellow','black'));
    	//     	//$bplot->SetFillColor(array('#cc0000','#ff7a00','#33ff66','#377aa6'));
    	//     	$bplot->SetFillColor(array('red','blue','green'));
    
    	$bplot->SetWidth(0.6);
    	
    	
    	
    	//$bplot->SetColor("green");
    	 
    	//$bplot->SetYMin(1990);
    	//$bplot->SetFillGradient('navy','orange',GRAD_RAISED_PANEL);填充渐变颜色
    	$graph->Add($bplot);
    	 
    	$bplot->SetFillColor(array('#cc0000','#ff7a00','#377aa6'));//这句得写在$graph->Add($bplot);后面才有效
    	$bplot->SetShadow();
    	$bplot->value->Show();
    	$bplot->value->SetColor("black");
    	$bplot->value->SetFormat("%d");
    	$bplot->value->SetFont(FF_SIMSUN,FS_BOLD,12);
    	//$graph->StrokeCSIM();
    	$FileName = UPLOAD_PATH . "temp/" . $name."bar.png";
        unlink($FileName);
    	$graph->Stroke($FileName);
    
    
    	//$graph->Stroke($FileName);
    }

    public function htmlTeachReoprt(){
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
        $apptoken = I('get.apptoken');
        $serverity = I('get.serverity');
        $this->makeTechReportHtml($apptoken,$serverity);
    }
    
    public function makeTechReportHtml($apptoken,$serverity){
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
        $model                  = D("Appinfo");
        // $vulinfo                = D("Vulinfo");
        $analysisResults        = D('AnalysisResults');
        $appid                  = $model->where(array('apptoken'=>$apptoken))->find()['appid'];
        $info                   = $model->where(array('appid'=>$appid))->find();
        
        if ($info['internet_security_level']) {
            $Internet_security_level = get_security_level($info['internet_security_level']);;
            $where['standard'] = ['like',"%".C('SECURITY_LEVEL_LIKE').$Internet_security_level."%"];
        }

        if($serverity){
            $serverity = explode('_', $serverity);
            if(in_array('4', $serverity)){
                $histogram['0']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
            }else{
                $histogram['0']     = 0;
            }

            if(in_array('3', $serverity)){
                $histogram['1']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 3))->sum('issues_count');
            }else{
                $histogram['1']     = 0;
            }
            
            if(in_array('2', $serverity)){
                $histogram['2']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 2))->sum('issues_count');
            }else{
                $histogram['2']     = 0;
            }
            $info['bugs']               = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level'=>array('in',$serverity)))->sum('issues_count');
            // var_export(array('appid'=>$appid,'risk_level'=>array('in',$serverity)));die;
            if(in_array('4', $serverity)){
                $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
            }
            if(!$info['gaowei']){
                $info['gaowei']         = 0;
            }

            $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('in',$serverity)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
            
            $detecresultinfo = get_detection_arr($detecresultinfo, $appid,$serverity);
            
        }else{
            $info['bugs']           = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2) ))->sum('issues_count');
            $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>4 ))->sum('issues_count');
            $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
            //高危漏洞类型数量
            if(!$info['gaowei']){
                $info['gaowei']         = 0;
            }
            $histogram['0']         = $info['gaowei'];
            //中危漏洞类型数量
            $histogram['1']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>3 ))->sum('issues_count');
            // var_export($histogram['mid']);die;
            //低危漏洞类型数量
            $histogram['2']             = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>2 ))->sum('issues_count');
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
                $value_i['standard'] = get_vul_result($value_i['standard'], 'world', $Internet_security_level);
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
                        $html .='<img src="'.__ROOT__."/".UPLOAD_PATH . "temp/" . $info['realname']."bar.png".'" style="width:50%;height:50%;"/><img src="'.__ROOT__."/".UPLOAD_PATH . "temp/" . $info['realname']."pie.png".'" style="width:50%;height:50%;"/>';
                        if ($info['internet_security_level']) {
                            $title_str = get_security_level_title($info['internet_security_level'], $pass_count, $no_pass_count);
                        }
                        if ($info['internet_security_level']) {
                        $html .='<div class="report-section">
                            <h2 class="main-title">二、检测概述</h2>
							<div class="clearfix">&nbsp;</div>
                            <div style="margin-bottom:10px;">'.$title_str.'</div>
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
                                $html .= 'safe" style="font-size:3px;';
                            }                          
                            $html .= '">'.$num_to_zh[$value['risk_level']].'</span></td>
                                </tr>';
                            $html2 .= '<div class="sec-wrapper">
                                <span class="badge"></span>
                                <h3 class="sec-title">'.($key+1).'、 '.$value['vulriskname'].'</h3>
                            </div>';
                            $detection_process = str_replace(array("[.]","[img]","[/img]","&lt;bbr&gt;",'&lt;bbr/&gt;'),array("\"","<img",">","<b>","</b>"),str_replace(array("\r","\r\n","\n","<!--","-->"),"<br/>", htmlspecialchars($value['detection_process'])));
                            $detection_process= preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/","",$detection_process);
                            $suggestions = str_replace(array("[.]","[img]","[/img]"),array("\"","<img",">"),str_replace(array("\r","\r\n","\n"),"<br/>",$value['suggestions']));
                            // if(!$detection_process){
                                // $detection_process = '空';
                            // }
                            if($detection_process == 'N/A') {
                                $detection_process = '无';
                            }
                            
                            if($suggestions== 'N/A') {
                                $suggestions= '无';
                            }

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
                                    <td colspan="2">对标</td>
                                    <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE'))))).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2">风险描述</td>
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
                                }else {
                                    $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td style="width:20%;">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;">对标</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE'))))).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
                                    if($detection_process){
                                        $html2 .='<tr>
		                        <td style="width:20%;">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
                                    }
                                }
                                unset($inner_data);
                            } else {
                                $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td style="width:20%;">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;">对标</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE'))))).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
                                if($detection_process){
                                    $html2 .='<tr>
		                        <td style="width:20%;">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
                                }
                            }
                            
                            $html2 .='
                            <tr>
                                <td colspan="2">风险系数</td>
                                <td colspan="6">'.$num_to_zh[$value['risk_level']].'</td>
                            </tr>
                            <tr>
                                <td colspan="2">修复建议</td>
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
                                
                                $html .='<tr>
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
                                    $html .= 'safe" style="font-size:3px;';
                                }
                                $html .= '">'.$num_to_zh[$value['risk_level']].'</span></td>
                                </tr>';
                                
                                $html2 .= '<div class="sec-wrapper">
                                <span class="badge"></span>
                                <h3 class="sec-title">'.($key+1).'、 '.$value['vulriskname'].'</h3>
                            </div>';
                                $detection_process = str_replace(array("[.]","[img]","[/img]","&lt;bbr&gt;",'&lt;bbr/&gt;'),array("\"","<img",">","<b>","</b>"),str_replace(array("\r","\r\n","\n","<!--","-->"),"<br/>", htmlspecialchars($value['detection_process'])));
                                $detection_process= preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/","",$detection_process);
                                $suggestions = str_replace(array("[.]","[img]","[/img]"),array("\"","<img",">"),str_replace(array("\r","\r\n","\n"),"<br/>",$value['suggestions']));
                                // if(!$detection_process){
                                // $detection_process = '空';
                                // }
                                if($detection_process == 'N/A') {
                                    $detection_process = '无';
                                }
                                
                                if($suggestions== 'N/A') {
                                    $suggestions = '无';
                                }
                                if($value['detectionid'] == 44 || $value['detectionid'] == 142){
//                                     $arr=explode("相关内容：",$detection_process);
//                                     $temp=explode("####",$arr[1]);
//                                     $arr[1] = $temp[0];
//                                     $arr[1]=str_replace("<br/>", "", $arr[1]);
//                                     $arr[2]=str_replace("<br/><br/>", "<br/>", $temp[1]);
//                                     //                                 $arr[2] = $temp[1];
//                                     //                                 $inner_data=json_decode(htmlspecialchars_decode($arr[1]));
//                                     $inner_data=json_decode(htmlspecialchars_decode($arr[1]));
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
                                    <td colspan="2">风险编号</td>
                                    <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars($value['standard'])).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2">风险描述</td>
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
                                    }else {
                                        $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td style="width:20%;">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;">风险编号</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars($value['standard'])).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
                                        if($detection_process){
                                            $html2 .='<tr>
		                        <td style="width:20%;">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
                                        }
                                    }
                                    unset($inner_data);
                                } else {
                                    $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td style="width:20%;">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;">风险编号</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars($value['standard'])).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
                                    if($detection_process){
                                        $html2 .='<tr>
		                        <td style="width:20%;">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
                                    }
                                }
                                
                                $html2 .='
                            <tr>
                                <td colspan="width:20%;">风险等级</td>
                                <td colspan="6">'.$num_to_zh[$value['risk_level']].'</td>
                            </tr>
                            <tr>
                                <td colspan="width:20%;">修复建议</td>
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
    //生成管理报告html
    public function htmlManageReoprt(){
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
        $apptoken = I('get.apptoken');
        $serverity = I('get.serverity');
        $this->makeManagehReportHtml($apptoken,$serverity);
    }
    public function makeManagehReportHtml($apptoken,$serverity){
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
        $model                  = D("Appinfo");
        $analysisResults        = D('AnalysisResults');
        $appid                  = $model->where(array('apptoken' => $apptoken))->find()['appid'];
        $info                   = $model->where(array('appid'=>$appid))->find();
        if ($info['internet_security_level']) {
            $Internet_security_level = get_security_level($info['internet_security_level']);
            $where['standard'] = ['like',"%".C('SECURITY_LEVEL_LIKE').$Internet_security_level."%"];
        }
        if($serverity){
            $serverity = explode('_', $serverity);
            if(in_array('4', $serverity)){
                $histogram['0']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
            }else{
                $histogram['0']     = 0;
            }

            if(in_array('3', $serverity)){
                $histogram['1']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 3))->sum('issues_count');
            }else{
                $histogram['1']     = 0;
            }
            
            if(in_array('2', $serverity)){
                $histogram['2']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 2))->sum('issues_count');
            }else{
                $histogram['2']     = 0;
            }
            $info['bugs']               = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level'=>array('in',$serverity)))->sum('issues_count');
            // var_export(array('appid'=>$appid,'risk_level'=>array('in',$serverity)));die;
            if(in_array('4', $serverity)){
                $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
            }
            if(!$info['gaowei']){
                $info['gaowei']         = 0;
            }
            $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('in',$serverity)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
            $detecresultinfo = get_detection_arr($detecresultinfo, $appid,$serverity);
        }else{
            $info['bugs']           = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2) ))->sum('issues_count');
            $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>4 ))->sum('issues_count');
            $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
            //高危漏洞类型数量
            if(!$info['gaowei']){
                $info['gaowei']         = 0;
            }
            $histogram['0']         = $info['gaowei'];
            //中危漏洞类型数量
            $histogram['1']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>3 ))->sum('issues_count');
            // var_export($histogram['mid']);die;
            //低危漏洞类型数量
             $histogram['2']            = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>2 ))->sum('issues_count');
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
        
//         $vuldb_list = M('vuldb')->select();
//         $product_ver = D('Licinfo')->field('product_ver')->where(array('id'=>1))->find();
//         $html_product_info = "<table class='table' style='margin-bottom:20px;'><tbody>";
//         foreach ($vuldb_list as &$vul_value) {
//             $vul_value['vuldb_name']  = $vul_value['vuldb_name'].'版本';
//             $html_product_info .=
//             "<tr>
//             <td>{$vul_value['vuldb_name']}</td>
//             <td>{$vul_value['vuldb_version']}</td>
//             </tr>";
//         }
//         $html_product_info .=             
//             "<tr>
//                 <td>系统版本</td>
//                 <td>{$product_ver['product_ver']}</td>
//             </tr></tbody></table>";

        
        
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
                      $html .= '<table class="table">
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
                        $html .='<img src="'.__ROOT__."/".UPLOAD_PATH . "temp/" . $info['realname']."bar.png".'" style="width:50%;height:50%;"/><img src="'.__ROOT__."/".UPLOAD_PATH . "temp/" . $info['realname']."pie.png".'" style="width:50%;height:50%;"/>';
                        if ($info['internet_security_level']) {
                            $title_str = get_security_level_title($info['internet_security_level'], $pass_count, $no_pass_count);
                        }
                        if ($info['internet_security_level']) {
                            $html .='<div class="report-section">
                                <h2 class="main-title">二、检测概述</h2>
                                <div class="clearfix">&nbsp;</div>
                                <div style="margin-bottom:10px;">'.$title_str.'</div>
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
                                    $html .= 'safe" style="font-size:3px;';
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
                                    $html .= 'safe';
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

    //创建技术的word报告
    public function makeTechWord($apptoken,$serverity){
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
        Vendor('PHPWord');
 
        $PHPWord = new \PHPWord();

        //设置文件名
        $fileName = 'create_word';
        $low='..'.__ROOT__.'/Public/img/low.png';
        $safe='..'.__ROOT__.'/Public/img/safe.png';
        $medium='..'.__ROOT__.'/Public/img/medium.png';
        $high='..'.__ROOT__.'/Public/img/high.png';
        $title='..'.__ROOT__.'/Public/img/title.png';
        $cellColor=array('bgColor'=>'EAEAEA');
        //设置默认字体
        $PHPWord-> setDefaultFontName('微软雅黑');
        
        // New portrait section
        $section = $PHPWord->createSection();
             
        $styleTable = array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center','tableInden' => 1440);//表格样式
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');//合并行单元格
        $cellRowContinue = array('vMerge' => 'continue');//被合并行单元格
        $cellColSpan = array('gridSpan' => 3, 'valign' => 'center');//合并列单元格
        $cellHCentered = array('align' => 'center');//水平居中
        $cellVCentered = array('valign' => 'center');//垂直居中
        
        $PHPWord->addTableStyle('Colspan Rowspan', $styleTable);//添加表格样式类型

         
        $PHPWord->addTitleStyle(1, array('size'=>20, 'color'=>'333333', 'bold'=>true));
        $PHPWord->addTitleStyle(2, array('size'=>15, 'color'=>'333333'));
        $PHPWord->addTitleStyle(3, array('size'=>12, 'color'=>'333333'));
        $center=array('align'=>'center');
		$left=array('align'=>'left');
        $vcenter = array('valign' => 'center');
        $styleName="FontStyle";   
        $styleFont = array('spaceAfter'=>60, 'name'=>'Tahoma', 'size'=>10,'bold'=>true);
        $PHPWord->addFontStyle($styleName, $styleFont);
        
        $PHPWord->addFontStyle('rStyle', array('bold'=>true, '微软雅黑'=>true, 'size'=>14));
        //$PHPWord->addParagraphStyle('pStyle', array('align'=>'center', 'spaceAfter'=>100));//段落样式
        
        
        
        $model                  = D("Appinfo");
        // $vulinfo                = D("Vulinfo");
        $analysisResults        = D("AnalysisResults");
        //$apptoken               = I('get.apptoken');
        // $serverity               = I('get.serverity');
        $appid                  = $model->where(array('apptoken'=>$apptoken))->find()['appid'];
        $info                   = $model->where(array('appid'=>$appid))->find();
        
        if ($info['internet_security_level']) {
            $Internet_security_level = get_security_level($info['internet_security_level']);
            $where['standard'] = ['like',"%".C('SECURITY_LEVEL_LIKE').$Internet_security_level."%"];
        }
        if($serverity){
            $serverity = explode('_', $serverity);
            if(in_array('4', $serverity)){
               $info['gaowei']  =  $histogram['0']  = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
            }

            if($histogram['0'] == null){
                $histogram['0'] = 0;
            }

            if(in_array('3', $serverity)){
                $histogram['1']       = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 3))->sum('issues_count');
            }
            if($histogram['1'] == null){
                $histogram['1'] = 0;
            }

            
            if(in_array('2', $serverity)){
                $histogram['2']       = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 2))->sum('issues_count');
            }
            if($histogram['2'] == null){
                $histogram['2'] = 0;
            }

            // var_export(array('appid'=>$appid,'risk_level'=>array('in',$serverity)));die;
            if(in_array('4', $serverity)){
                $info['0']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
            }
            if(!$info['gaowei']){
                $info['gaowei']         = 0;
            }
            $info['bugs']           = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('in',$serverity)))->sum('issues_count');
            $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('in',$serverity)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
            $detecresultinfo = get_detection_arr($detecresultinfo, $appid,$serverity);
        }else{
            $info['bugs']           = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2) ))->sum('issues_count');
            $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>4 ))->sum('issues_count');
            $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
            //高危漏洞类型数量
            if(!$info['0']){
                $info['0']         = 0;
            }
            $histogram['0']      = $info['gaowei'];
            //中危漏洞类型数量
            $histogram['1']       = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>3 ))->sum('issues_count');
            if(!$histogram['1']){
                $histogram['1']         = 0;
            }
            // var_export($histogram['mid']);die;
            //低危漏洞类型数量
            $histogram['2']          = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>2 ))->sum('issues_count');
            if(!$histogram['2']){
                $histogram['2']         = 0;
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
                $value_i['standard'] = get_vul_result($value_i['standard'], 'world', $Internet_security_level);
            }
            $detecresultinfo = array_sort($detecresultinfo, 'standard', 'asc');
        }
        $zh_to_num          = array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
        $num_to_zh          = array('0'=>'暂未评级','1'=>$safe,'2'=>$low,'3'=>$medium,'4'=>$high);

        // $detecresultinfo = list_sort_by($detecresultinfo, 'issues_severity', $sortby = 'desc');
                        //设置中文编码

       // dump($serverity);
       // dump($info);
        ///dump($apptoken);
       // $this->display();
        //die;
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
        
        $section->addTextBreak(3);
        $section->addText("应用安全检测技术报告",array('bold'=>true, '微软雅黑'=>true, 'size'=>30),array('align' =>'center'));    
        $section->addTextBreak(5);
        
        if(file_exists($info['icon'])){
            $img =$info['icon'];
        }
        //$section->addText($img);

        $section->addImage($img,array('width'=>200, 'height'=>200,'align'=>'center'));
        
        $section->addTextBreak(7);
        

        
        //'borderSize' => 6, 'borderColor' => '999999''Colspan Rowspan','tableInden' => 1440
        $styleIndexTable = array('alignMent' => 'center','cellMarginTop'=>300);//表格样式   
        $PHPWord->addTableStyle('styleIndexTable', $styleIndexTable);
        $indextable = $section->addTable('styleIndexTable');
        
        //array('cellMerge' => 'restart', 'valign' => "center")
        $cellTextStyleParagraph = array('align' => 'center');//,'spacing'=>100
        $cellTextStyle = array('bold'=>true, '微软雅黑'=>true, 'size'=>16,'textDirection'=>\PHPWord_Style_Cell::TEXT_DIR_BTLR);
        $cellValueStyle = array('borderBottomSize'=>9,'borderBottomColor'=>'999999');
        if ($info ['realname']) {
            $indextable->addRow();
            $indextable->addCell(2000)->addText("应用名称:",$cellTextStyle);
            
            $indextable->addCell(4000,$cellValueStyle)->addText($info ['realname'],$cellTextStyle,$cellTextStyleParagraph);
            //$section->addText($info ['realname']);
        }
        if ($info ['tasktype'] == 'ios') {
            $indextable->addRow();
            $indextable->addCell(2000)->addText("系统:",$cellTextStyle);
            $indextable->addCell(4000,$cellValueStyle)->addText("iOS",$cellTextStyle,$cellTextStyleParagraph);
            //$section->addText("iOS版");
        } elseif ($info ['tasktype'] == 'wx') {
            $indextable->addRow();
            $indextable->addCell(2000)->addText("系统:",$cellTextStyle);
            $indextable->addCell(4000,$cellValueStyle)->addText("微信安全测试",$cellTextStyle,$cellTextStyleParagraph);
            //$section->addText("微信安全测试");
        } elseif($info['tasktype'] == 'android'){
            $indextable->addRow();
            $indextable->addCell(2000)->addText("系统:",$cellTextStyle);
            $indextable->addCell(4000,$cellValueStyle)->addText("Android",$cellTextStyle,$cellTextStyleParagraph);
            //$section->addText("Android版");
        }else{
            $indextable->addRow();
            $indextable->addCell(2000)->addText("系统:",$cellTextStyle);
            $indextable->addCell(4000,$cellValueStyle)->addText("WEB扫描",$cellTextStyle,$cellTextStyleParagraph);
        }   
        if ($info ['version']) {
            $indextable->addRow();
            $indextable->addCell(2000)->addText("版本号:",$cellTextStyle);
            $indextable->addCell(4000,$cellValueStyle)->addText($info['version'],$cellTextStyle,$cellTextStyleParagraph);
            //$section->addText($info['version']);
        }
        
        $indextable->addRow();
        $indextable->addCell(2000)->addText("检测时间:",$cellTextStyle);
        $indextable->addCell(4000,$cellValueStyle)->addText($info ['uploadtime'],$cellTextStyle,$cellTextStyleParagraph);
        //$section->addText($info ['subtime']);
                         
        
        
        $section->addPageBreak();
        
        $section->addText("目录",array('微软雅黑'=>true, 'size'=>24),array('align' =>'center'));
        $section->addTextBreak(1);
        
        //设置目录
        $styleTOC = array('tabLeader'=>\PHPWord_Style_TOC::TABLEADER_DOT);//'tabPos'=>5000,目录样式
        
        $section->addTOC(array('spaceAfter'=>60, 'name'=>'Tahoma', 'size'=>14), $styleTOC);
        
        
        
        // 页眉图片
        
        
        //添加页眉
        $header = $section->createHeader();
        
        $headerdata=M('expand')->where(array('status'=>1))->find();
        $url='..'.__ROOT__.substr($headerdata['watermark'], 1);
        //dump($url);die;
        $table = $header->addTable();
        $table->addRow();
        $table->addCell(800,$cellRowSpan)->addImage($url, array('width'=>35, 'height'=>35, 'align'=>'left'));
        $table->addCell(5000)->addText($headerdata['header']);
        $table->addRow();
        $table->addCell(null, $cellRowContinue);
        $table->addCell(5000)->addText($headerdata['footer']);
    
        //添加页脚
        $footer = $section->createFooter();
        $footer->addPreserveText('第 {PAGE}页 /共{NUMPAGES}页.',null,array('align'=>'center'));
        
        $section->addPageBreak();
        
        
        //第三页
        
        $section->addTitle("1   基本信息",1);
        $section->addTextBreak(1);
        $p = 1;
        //$titleTable=$section->addTable();
        //$titleTable->addRow();
        //$titleTable->addCell()->addImage($title,array('width'=>25, 'height'=>25,'align'=>'left'));
        //$section->addImage($title,array('width'=>25, 'height'=>25,'align'=>'left'));
        //$titleTable->addCell()->addTitle("1.1   应用概况",2);
//         $section->addTitle("1.1   应用概况",2);
//         $styleBaseInfoTable = array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center','cellMarginTop'=>80,'cellMarginLeft'=>80,'cellMarginRight'=>80,'cellMarginBottom'=>80);
//         //array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center','tableInden' => 1440);//表格样式          
//         $PHPWord->addTableStyle('styleBaseInfoTable', $styleBaseInfoTable);
//         $BaseInfocellTextStyle = array('size'=>11);
        
        $styleBaseInfoTable = array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center','cellMarginTop'=>80,'cellMarginLeft'=>80,'cellMarginRight'=>80,'cellMarginBottom'=>80);
        //array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center','tableInden' => 1440);//表格样式
        $PHPWord->addTableStyle('styleBaseInfoTable', $styleBaseInfoTable);
        $BaseInfocellTextStyle = array('size'=>11);
        
        $section->addTitle("1.$p   系统信息",2);
        $p ++ ;
        
        report_product_ino_word($section,$cellColor,$cellColSpan3,$center,$BaseInfocellTextStyle);
        
        $section->addTitle("1.$p   应用概况",2);
        $p ++ ;
        
        $baseInfoTable = $section->addTable('styleBaseInfoTable');
        if ($info ['tasktype'] == 'android' || $info ['tasktype'] == 'ios') {
            
            $baseInfoTable->addRow ();
            $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '应用名称', $BaseInfocellTextStyle ,$center);
            $baseInfoTable->addCell ( 2500 )->addText ( $info ['realname'], $BaseInfocellTextStyle );
            $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '版本号', $BaseInfocellTextStyle ,$center);
            $baseInfoTable->addCell ( 3500 )->addText ( $info ['version'], $BaseInfocellTextStyle );
            
            
            //$baseInfoTable->addCell ( 4000 )->addText($new);
            //$baseInfoTable->addCell ( 4000 )->addText ( $info ['version'], $BaseInfocellTextStyle );
            
            $baseInfoTable->addRow ();
            $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '包名', $BaseInfocellTextStyle ,$center);
            $baseInfoTable->addCell ( 2500 )->addText ( $info ['package'], $BaseInfocellTextStyle );
            $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '时间', $BaseInfocellTextStyle ,$center);
            $baseInfoTable->addCell ( 3500 )->addText ( $info ['subtime'], $BaseInfocellTextStyle );
        } elseif ($info ['tasktype'] == 'web' || $info ['tasktype'] == 'awvs') {
            $baseInfoTable->addRow ();
            $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '目标网站', $BaseInfocellTextStyle ,$center);
            $baseInfoTable->addCell ( 2500 )->addText ( $info ['targeturl'], $BaseInfocellTextStyle );
        }
        
        if ($info ['tasktype'] == 'awvs') {
            
            $scanoption = json_decode ( $info ['scanoption'], 1 );
            $checkslen = count ( $scanoption ['checks'] );
            
            if ($checkslen > 0) {
                
                $baseInfoTable->addRow ();
                $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '扫描规则', $BaseInfocellTextStyle ,$center);
                
                $temp = null;
                foreach ( $scanoption ['checks'] as $keyck => $valck ) {
                    $temp = $rulelist [$valck];
                    if ($keyck != ($checkslen - 1)) {
                        $html .= ",";
                    }
                }
                $baseInfoTable->addCell ( 2500 )->addText ( $temp, $BaseInfocellTextStyle );
            }
        }
        $baseInfoTable->addRow();
        $baseInfoTable->addCell(1500,$cellColor)->addText('高危风险',$BaseInfocellTextStyle,$center);
        $baseInfoTable->addCell(2500)->addText($info['gaowei'],$BaseInfocellTextStyle);
        if ($info ['tasktype'] == 'web' || $info ['tasktype'] == 'awvs'){
            $baseInfoTable->addRow();
        }
        $baseInfoTable->addCell(1500,$cellColor)->addText('风险数',$BaseInfocellTextStyle,$center);
        $baseInfoTable->addCell(3500)->addText($info['bugs'],$BaseInfocellTextStyle);
        
        
        
        if($info['tasktype'] == 'android' || $info['tasktype'] == 'ios'){
            $baseInfoTable->addRow();
            $baseInfoTable->addCell(1500,$cellColor)->addText('MD5',$BaseInfocellTextStyle,$center);
            $cellColSpan3 = array('gridSpan' => 3);//合并列单元格
            $baseInfoTable->addCell(7500, $cellColSpan3)->addText($info['md5'],$BaseInfocellTextStyle,null);
            
            $baseInfoTable->addRow();
            $baseInfoTable->addCell(1500,$cellColor)->addText('SHA-1',$BaseInfocellTextStyle,$center);
            $baseInfoTable->addCell(7500, $cellColSpan3)->addText($info['sha1'],$BaseInfocellTextStyle,null);
            
            $baseInfoTable->addRow();
            $baseInfoTable->addCell(1500,$cellColor)->addText('SHA-256',$BaseInfocellTextStyle,$center);        
            $baseInfoTable->addCell(7500, $cellColSpan3)->addText($info['sha256'],$BaseInfocellTextStyle,null);
            if($info['tasktype'] != 'ios'){
                $baseInfoTable->addRow();
                $baseInfoTable->addCell(1500,$cellColor)->addText('证书信息',$BaseInfocellTextStyle,$center);   
                $baseInfoTable->addCell(7500, $cellColSpan3)->addText($info['cert'],$BaseInfocellTextStyle,null);
            }
        }

        if($histogram[0] != false || $histogram[1] != false || $histogram[2] != false){
             $section->addTextBreak(1);
            //dump($histogram);die;
            $section->addTitle("1.$p   风险图例",2);
//             echo $info['realname'];
//             print_r($histogram);die();
            $this->makepiechart($histogram,$info['realname']);
            $this->makebarchart($histogram,$info['realname']);
            //dump($histogram);die;

            $LevelimageStyle = array('width'=>280, 'height'=>180, 'align'=>'center');
            $imagetable=$section->addTable('styleBaseInfoTable');
            $imagetable->addRow();
            $imagetable->addCell(4500)->addImage(UPLOAD_PATH . "temp/" . $info['realname']."bar.png",$LevelimageStyle);
            $imagetable->addCell(null,array('borderSize'=>6,'borderTopColor'=>'FFFFFF','borderBottomColor'=>'FFFFFF'))->addText("A",array('color'=>'FFFFFF'));
            $imagetable->addCell(4500)->addImage(UPLOAD_PATH . "temp/" .$info['realname']. "pie.png",$LevelimageStyle);
     
            
            $section->addPageBreak();

            //第四页
             
            $section->addTitle("2   检测概述",1);
            if ($info['internet_security_level']) {
                $title_str = get_security_level_title($info['internet_security_level'], $pass_count, $no_pass_count);
                $section->addText($title_str,array('size'=>12),array('align' =>'left')); 
            }
//             $section->addTextBreak(1);
//             $section->addCell(8000,$cellColor)->addText($title_str,$BaseInfocellTextStyle);

            $styleCheckTable = array('borderSize' => 6,'word-break'=>'break-all','borderColor' => '999999','alignMent' => 'center','cellMarginTop'=>80,'cellMarginLeft'=>80,'cellMarginRight'=>80,'cellMarginBottom'=>80);
            //array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center','tableInden' => 1440);//表格样式
            $PHPWord->addTableStyle('styleCheckTable', $styleCheckTable);
            $BaseInfocellTextStyle = array('size'=>12);
             
            $checkTable = $section->addTable('styleCheckTable');
            if ($info['internet_security_level']) {
                $checkTable->addRow();       
                $checkTable->addCell(800,$cellColor)->addText('序号',$BaseInfocellTextStyle);
                $checkTable->addCell(3000,$cellColor)->addText('检测项目',$BaseInfocellTextStyle);
                $checkTable->addCell(2400,$cellColor)->addText('对标',$BaseInfocellTextStyle);
                $checkTable->addCell(2200,$cellColor)->addText('检测结果',$BaseInfocellTextStyle);
                $checkTable->addCell(1200,$cellColor)->addText('危险系数',$BaseInfocellTextStyle);
        
                foreach ($detecresultinfo as $key => $value) {
                    $checkTable->addRow();
                    $checkTable->addCell(1000,$vcenter)->addText(($key+1),$BaseInfocellTextStyle,$center);
                    $checkTable->addCell(2000)->addText($value['vulriskname'],$BaseInfocellTextStyle);
                    $str_string = trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE'))));
                    $checkTable->addCell(2000)->addText($str_string,$BaseInfocellTextStyle);               
                    if($value['risk_level'] == 1){
                        if($value['issues_count']){
                            $checkTable->addCell(2000)->addText('符合(发现'.$value['issues_count'].'处)',$BaseInfocellTextStyle);
                        } else {
                            $checkTable->addCell(2000)->addText('符合',$BaseInfocellTextStyle);
                        }  
                    }else{
                        $checkTable->addCell(2000)->addText('不符合(发现'.$value['issues_count'].'处)',$BaseInfocellTextStyle);
                    }
                    if($value['risk_level'] == 1) {
                        $value['risk_level'] = $value['vulrisklevel'];
                    }
                    $checkTable->addCell(1000,$vcenter)->addImage($num_to_zh[$value['risk_level']],array('width'=>25, 'height'=>25,'align'=>'center'));
                }
            } else {
                $checkTable->addRow();
                $checkTable->addCell(1000,$cellColor)->addText('序号',$BaseInfocellTextStyle);
                $checkTable->addCell(3000,$cellColor)->addText('检测项目',$BaseInfocellTextStyle);
                $checkTable->addCell(3000,$cellColor)->addText('检测类型',$BaseInfocellTextStyle);
                $checkTable->addCell(1000,$cellColor)->addText('风险数',$BaseInfocellTextStyle);
                $checkTable->addCell(1300,$cellColor)->addText('危险系数',$BaseInfocellTextStyle);             
                foreach ($detecresultinfo as $key => $value) {   
                    $checkTable->addRow();
                    $checkTable->addCell(1000,$vcenter)->addText(($key+1),$BaseInfocellTextStyle,$center);
                    $checkTable->addCell(2000)->addText($value['vulriskname'],$BaseInfocellTextStyle);
                    $checkTable->addCell(2000)->addText($value['hvtype'],$BaseInfocellTextStyle);
                    if($value['issues_count']){
                        $checkTable->addCell(1000,$vcenter)->addText($value['issues_count'],$BaseInfocellTextStyle,$center);
                    }else{
                        $checkTable->addCell(1000,$vcenter)->addText('-',$BaseInfocellTextStyle,$center);
                    }
                    // $checkTable->addCell(1000,$vcenter)->addText($num_to_zh[$value['risk_level']],$BaseInfocellTextStyle,$center);
                    $checkTable->addCell(1000,$vcenter)->addImage($num_to_zh[$value['risk_level']],array('width'=>25, 'height'=>25,'align'=>'center'));
                }
            }
             $section->addPageBreak();
            
            
            //第四页
            
            $section->addTitle("3   详细检测结果",1);
            $section->addTextBreak(1);
            
            foreach ($detecresultinfo as $key => $value) {
                $section->addTitle('3.'.($key+1).' '.$value['case_name'],2);
                $checkTable = $section->addTable('styleCheckTable');
                //$checkTableCellstyle=array('valign' => 'center','bgColor'=>'EAEAEA');
                $checkTableCellstyle=array('gridSpan' => 2,'valign' => 'center','bgColor'=>'EAEAEA');
                //$checkTableTitlestyle=array('gridSpan' => 2,'valign' => 'center','bgColor'=>'EAEAEA');
                $checkTableCellstyle2=array('gridSpan' => 4,'valign' => 'center');
                
                /*@author:qxn 2017-11-23 1、“3、详细检测结果”对标和风险编号做判断，添加用例名称（与pdf和html报告对应）*/
                $checkTable->addRow();
                $checkTable->addCell(2000,$checkTableCellstyle)->addText('用例名称',$BaseInfocellTextStyle,$center);
                $checkTable->addCell(8000,$checkTableCellstyle2)->addText($value['case_name'],$BaseInfocellTextStyle);
                
                $checkTable->addRow();
                if ($info['internet_security_level']) {
                     $checkTable->addCell(2000,$checkTableCellstyle)->addText('对标',$BaseInfocellTextStyle,$center);
                } else {
                    $checkTable->addCell(2000,$checkTableCellstyle)->addText('风险编号',$BaseInfocellTextStyle,$center);
                }
                /*2017-11-23*/
                
                
                if ($info['internet_security_level']) {
                    $str_string = get_vul_result($value['standard'],'world',$Internet_security_level);
                } else {
                    $str_string = $value['standard'];
                }
                $checkTable->addCell(8000,$checkTableCellstyle2)->addText($str_string,$BaseInfocellTextStyle);

                
                //$checkTable->addCell(8000,$checkTableCellstyle2)->addText($value['standard'],$BaseInfocellTextStyle);

                // $checkTable->addRow();
                
                // $checkTable->addCell(2000,$checkTableCellstyle)->addText('风险编号',$BaseInfocellTextStyle,$center);
                // $text=$value['hvdid'];
                // $checkTable->addCell(8000)->addText($text, $BaseInfocellTextStyle );

                
                $checkTable->addRow();
                $checkTable->addCell(2000,$checkTableCellstyle)->addText('风险描述',$BaseInfocellTextStyle,$center);
                $checkTable->addCell(8000,$checkTableCellstyle2)->addText($value['risk_description'],$BaseInfocellTextStyle);
                 
                // $checkTable->addRow();
                // $checkTable->addCell(2000,$checkTableCellstyle)->addText('检测方法',$BaseInfocellTextStyle,$center);
                // $checkTable->addCell(8000)->addText($value['detection_method'],$BaseInfocellTextStyle);

                $checkTable->addRow();
                //$cellColSpan2 = array('gridSpan' => 2, 'valign' => 'center','bgColor'=>'EAEAEA');
                $cellColSpan2= array('gridSpan' => 6, 'valign' => 'center','bgColor'=>'EAEAEA');
                //$cellColSpanValue = array('gridSpan' => 2, 'valign' => 'center');
                $cellColSpanValue= array('gridSpan' => 6, 'valign' => 'center');
                $cellColSpan4 = array('gridSpan' => 2, 'valign' => 'center','bgColor'=>'EAEAEA');
                $style = array('word-break'=>'break-all');
            if($value['detection_process'] && $value['detection_process'] != "N/A"){
                    $checkTable->addCell(10000,$cellColSpan2)->addText('检测过程',$BaseInfocellTextStyle,$center);

                    $checkTable->addRow();
//                     $detection_process = trim($detection_process);
//                     $detection_process = rtrim(str_replace(array("[.]","[img]","[/img]","&lt;bbr&gt;",'&lt;bbr/&gt;'),array("\"","<img",">","<b>","</b>"),str_replace(array("\r","\r\n","\n","<!--","-->"),"\\n", $value['detection_process'])),"\\n");
                    $detection_process = rtrim(str_replace(array("[.]","[img]","[/img]","&lt;bbr&gt;",'&lt;bbr/&gt;'),array("\"","<img",">","<b>","</b>"),str_replace(array("\r","\r\n","\n","<!--","-->"),"\\n", $value['detection_process'])));
                    $detection_process = str_replace(array("\\n \\n", "\\n\\n"), "\\n", $detection_process);
                    $detection_process= preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/","",$detection_process);

                    if (substr($detection_process, -2) == "\\n"){
                        $detection_process = substr($detection_process, 0, strlen($detection_process)-2);
                    } 
                    
                    //var_export($detection_process);
                    //$LevelimageStyle = array('width'=>300, 'height'=>200, 'align'=>'center');
                    $process=$checkTable->addCell(10000,$cellColSpanValue);

    				preg_match_all('/<img src=\"\/mare\/\.\/Uploads\/([^>]+)\" width=\"200px\"[\/]*>/', $detection_process, $sp);

                    preg_match_all('/<bbr>[a-zA-Z\.0-9]+<bbr\/>/', $detection_process, $zujian);
                    
    				if($sp[0] && $sp[1]){
    					for($i=0;$i<count($sp[0]);$i++){
    						$str=explode($sp[0][$i], $detection_process);
    					    $process->addText($str[0],null,$left);
    						$process->addImage(UPLOAD_PATH.$sp[1][$i],array('width'=>200, 'height'=>300));
    						$detection_process=$str[1];
    						if($i==count($sp[0])-1){
    							$process->addText($str[1],null,$left);
    						}
    						
    					}
                    }else{
                        if($zujian[0]){
                            for($j=0;$j<count($zujian[0]);$j++){
                                $zujianstr=explode($zujian[0][$j], $detection_process);
                                $process->addText($zujianstr[0],array('color'=>'454545'),$left);
                                $process->addText(str_replace(array("<bbr>","<bbr/>"),"",$zujian[0][$j]),$BaseInfocellTextStyle,$left);
                                $detection_process=$zujianstr[1];
                                if($j==count($zujian[0])-1){
                                    $process->addText($zujianstr[1],null,$left);
                                }
                            }
                            
                            
                        }else{
                            //在此处理URL
                            if($value['detectionid'] == 44 || $value['detectionid'] == 142){
//                                 $arr=explode("URL：",$detection_process);
//                                 $arr=explode("相关内容：",$detection_process);

                                if($value['detectionid'] == 44) {
                                    $arr=explode("相关内容：",$detection_process);
                                } else if($value['detectionid'] == 142) {
                                    $arr=explode("请求URL：",$detection_process);
                                    
                                }
                                
                                $temp=explode("####",$arr[1]);
//                                 print_r($temp);
//                                 echo count($temp);die();
                                if($value['detectionid']==44) {
                                    $arr[1]=$temp[0];
                                    $arr[2] = $temp[1];
                                    $inner_data=json_decode(str_replace("\\n", "", $arr[1]));
                                } else if($value['detectionid'] == 142) {
//                                     var_dump($temp);die();
//                                     echo count($temp);die();
                                    $arr[1]=$temp;
                                    foreach ($arr[1] as $k => $v) {
                                        $arr_list = json_decode(str_replace("\\n", "", $v), true);
//                                         print_r($arr_list);die();
                                        foreach ($arr_list as $k1 => $v1) {
                                            $inner_data[] = $v1;
                                        }
                                        unset($arr_list);   
                                    }
//                                     print_r($inner_data);die();
                                }
                                
                                
                               
                                if ($inner_data) {
                                    $arr[0] = trim($arr[0]);
                                    if (substr($arr[0], -2) == "\\n"){
                                        $arr[0]= substr($arr[0], 0, strlen($arr[0])-2);
                                    } 
                                    $process->addText($arr[0],null,array('align'=>'left'));
                                    $styleCheckTable = array('borderSize' => 6, 'word-break'=>'break-all','borderColor' => '999999','alignMent' => 'center','cellMarginTop'=>80,'cellMarginLeft'=>10,'cellMarginRight'=>10,'cellMarginBottom'=>80);
                                    $PHPWord->addTableStyle('styleCheckTable', $styleCheckTable);
                                    $inner_detection_process = $section->addTable('styleCheckTable');
                                    
                                    $checkTableInnerCellTitlestyle = array('valign' => 'center','bgColor'=>'EAEAEA');//,'bgColor'=>'EAEAEA'
                                    $checkTableInnerCellValuestyle = array('valign' => 'center');
                                    
                                    $innerTableTitleStyle = array('size'=>10);//$innerTableTitleStyle = array('size'=>10,'bold'=>true);
                                    $innerTableTextStyle = array('size'=>9);
                                    $left=array('align'=>'left');
                                    
                                    $inner_detection_process->addRow();
                                    $inner_detection_process->addCell(600,$checkTableInnerCellTitlestyle)->addText('序号',$innerTableTitleStyle,$center);
                                    $inner_detection_process->addCell(1400,$checkTableInnerCellTitlestyle)->addText('域名',$innerTableTitleStyle,$center);
                                    $inner_detection_process->addCell(2000,$checkTableInnerCellTitlestyle)->addText('url',$innerTableTitleStyle,$center);
                                    $inner_detection_process->addCell(2000,$checkTableInnerCellTitlestyle)->addText('IP列表',$innerTableTitleStyle,$center);
                                    $inner_detection_process->addCell(2000,$checkTableInnerCellTitlestyle)->addText('注册域名商',$innerTableTitleStyle,$center);
                                    $inner_detection_process->addCell(2000,$checkTableInnerCellTitlestyle)->addText('DNS解析记录',$innerTableTitleStyle,$center);
                                    
                                    
                                    if($value['detectionid'] == 44) {
                                      $inner_data=json_decode(str_replace("\\n", "", $arr[1]));
                                    } 
                                    
    //                                 foreach ($inner_data as $key_s => $value_s) {
    //                                     $arr_s[$key_s]['host'] =  $value_s['host'];
    //                                     $arr_s[$key_s]['url'] =  wordwrap(implode("\n", $value_s['url']),20,'\n',true);
    //                                     $arr_s[$key_s]['ip_list'] =  implode("\n", $value_s['ip_list']);
    //                                     $arr_s[$key_s]['register'] =  $value_s['register'];
    //                                     $arr_s[$key_s]['dns_history'] =  implode("\n", $value_s['dns_history']);
    //                                 }
    //                                 P($inner_data);
    //                                 P($arr_s);die();
    //                                 foreach ($arr_s as $k => $v) {
    //                                     $checkTable->addRow();
    //                                     $checkTable->addCell(1000)->addText($k+1,$innerTableTextStyle,$left);
    //                                     $checkTable->addCell(1400)->addText($v['host'],$innerTableTextStyle,$left);
    //                                     $checkTable->addCell(3000)->addText($v['url'],$innerTableTextStyle,$left);
    //                                     $checkTable->addCell(2000)->addText($v['ip_list'],$innerTableTextStyle,$left);
    //                                     $checkTable->addCell(1500)->addText($v['register'],$innerTableTextStyle,$left);
    //                                     $checkTable->addCell(1500)->addText($v['dns_history'],$innerTableTextStyle,$left);
    //                                 }
                                    if($value['detectionid'] == 44) {
                                        foreach ($inner_data as $k=>$v){
                                            
                                            $inner_detection_process->addRow();
                                            $inner_detection_process->addCell(600)->addText($k+1,$innerTableTextStyle,$left);
                                            $inner_detection_process->addCell(1400)->addText($v->host,$innerTableTextStyle,$left);
                                            
                                            $url_list_cell=$inner_detection_process->addCell(2000);
                                            foreach ($v->url as $k1=>$v1){
                                                $url_list_cell->addText($v1,$innerTableTextStyle,$left);
                                                //                                         $url_list_cell->addText(wordwrap(trim($v1),20,'\n',true),$innerTableTextStyle,$left);
                                            }
                                            
                                            $ip_list_cell=$inner_detection_process->addCell(2000);
                                            foreach ($v->ip_list as $k1=>$v1){
                                                $ip_list_cell->addText($v1,$innerTableTextStyle,$left);
                                            }
                                            
                                            $inner_detection_process->addCell(2000)->addText(wordwrap(trim($v->register),30,'\n',true),$innerTableTextStyle,$left);
                                            
                                            $dns_history_cell=$inner_detection_process->addCell(2000);
                                            foreach ($v->dns_history as $k1=>$v1){
                                                $dns_history_cell->addText($v1,$innerTableTextStyle,$left);
                                            }
                                            
                                        }
                                    } else {
                                        foreach ($inner_data as $k=>$v){
                                            
                                            $inner_detection_process->addRow();
                                            $inner_detection_process->addCell(600)->addText($k+1,$innerTableTextStyle,$left);
                                            $inner_detection_process->addCell(1400)->addText($v['host'],$innerTableTextStyle,$left);
                                            
                                            $url_list_cell=$inner_detection_process->addCell(2000);
                                            foreach ($v['url'] as $k1=>$v1){
                                                $url_list_cell->addText($v1,$innerTableTextStyle,$left);
        //                                         $url_list_cell->addText(wordwrap(trim($v1),20,'\n',true),$innerTableTextStyle,$left);
                                            }
        
                                            $ip_list_cell=$inner_detection_process->addCell(2000);
                                            foreach ($v['ip_list'] as $k1=>$v1){
                                                $ip_list_cell->addText($v1,$innerTableTextStyle,$left);
                                            }
                                            
                                            $inner_detection_process->addCell(2000)->addText(wordwrap(trim($v['register']),30,'\n',true),$innerTableTextStyle,$left);
                                            
                                            $dns_history_cell=$inner_detection_process->addCell(2000);
                                            foreach ($v['dns_history'] as $k1=>$v1){
                                                $dns_history_cell->addText($v1,$innerTableTextStyle,$left);
                                            }
                                            
                                        }
                                    }
                                    if($value['detectionid']==44) {
                                        if(trim($arr[2])) {
                                            $cellColSpan5 = array('gridSpan' => 6, 'valign' => 'center');
                                            $inner_detection_process->addRow();
                                            $inner_detection_process->addCell(10000, $cellColSpan5)->addText(trim($arr[2]),$BaseInfocellTextStyle,$left);
                                        }
                                    }
                                } else {
                                    $process->addText($detection_process,null,array('align'=>'left'));
                                }
                                unset($inner_data);
                            }else{
                                $process->addText($detection_process,null,array('align'=>'left'));
                            }
                    	}
                    }
                } else {
                    $checkTable->addCell(10000,$cellColSpan2)->addText('检测过程',$BaseInfocellTextStyle,$center);
                    $checkTable->addRow();
                    $process=$checkTable->addCell(10000,$cellColSpanValue);
                    $process->addText('无',null,array('align'=>'left','font-size'=>11));
                }

                $checkTable->addRow();
                $checkTable->addCell(2000,$checkTableCellstyle)->addText('风险系数',$BaseInfocellTextStyle,$center);
                $vcenter = array('gridSpan' => 4,'valign' => 'center');
                //$checkTableCellstyle2=array('gridSpan' => 4,'valign' => 'center');
                // $checkTable->addCell(8000)->addText($num_to_zh[$value['risk_level']],$BaseInfocellTextStyle);
//                 if($value['risk_level'] == 1) {
//                     $value['risk_level'] = 1;
//                 }
                $checkTable->addCell(8000,$checkTableCellstyle2)->addImage($num_to_zh[$value['risk_level']],array('width'=>25, 'height'=>25,'align'=>'left'));
                if($value['risk_level'] != '1' && $value['suggestions'] && $value['suggestions'] != "N/A"){
                    $checkTable->addRow();
                    $checkTable->addCell(2000,$checkTableCellstyle)->addText('修复建议',$BaseInfocellTextStyle,$center);
                    
                    $suggestions = str_replace(array("[.]","[img]","[/img]"),array("\"","<img",">"),str_replace(array("\r","\n","\r\n"),"\\n",html_entity_decode($value['suggestions'])));
                    preg_match_all('/<img src=\"\/mare\/\.\/Uploads\/([^>]+)\" width=\"\d+px\"[\/]*>/', $suggestions, $su);
                    $checkTableCellstyle6=array('gridSpan' => 4,'valign' => 'center');
                    $suggestions_t = $checkTable->addCell(8000,$checkTableCellstyle6);
                    if ($su[0] && $su[1]) {
                         for($j=0;$j<count($su[0]);$j++){
                                $stru=explode($su[0][$j], $suggestions);
                                $suggestions_t->addText(trim($stru[0],'\n\n'),null,$BaseInfocellTextStyle);
                                $suggestions_t->addImage(UPLOAD_PATH.$su[1][$j],array('width'=>200, 'height'=>300));
                                $suggestions=$stru[1];
                                if($j ==count($su[0])-1){
                                    $suggestions_t->addText($stru[1],null,$BaseInfocellTextStyle);
                                }
                            }
                    }else {
                        $suggestions_t->addText(str_replace(array("\r","\n","\r\n"),"\\n", $suggestions), $BaseInfocellTextStyle);
                    }
                    
//                     $checkTable->addCell(8000,$checkTableCellstyle2)->addText(str_replace(array("\r","\n","\r\n"),"\\n",html_entity_decode($value['suggestions'])),$BaseInfocellTextStyle);
                } else {
                    $checkTable->addRow();
                    $checkTable->addCell(2000,$checkTableCellstyle)->addText('修复建议',$BaseInfocellTextStyle,$center);
                    $suggestions_t = $checkTable->addCell(8000,$checkTableCellstyle6);
                    $suggestions_t->addText('无', null, array('align'=>'left','font-size'=>11));
                }
            }
        }
        
        // Save File
        $fileName = $info['realname']==null?$info['task_name']:$info['realname'];
        
        $test_type = get_task_type($info['tasktype']);      
        $fileName = $fileName.'_'.$test_type.'_技术报告_'.date('Y_m_d',time());
        
        $u=__ROOT__."/Uploads/word/word.docx";
        $url='./Uploads/word/'.$fileName;
        
        
        header('Pragma: public');  
        header('Expires: 0');  
        header('Cache-Control:must-revalidate， post-check=0， pre-check=0');  
        header('Content-Type:application/force-download');  
        header('Content-Type:application/vnd.ms-word');  
        header('Content-Type:application/octet-stream');  
        header('Content-Type:application/download');  
        header('Content-Disposition:attachment;filename='.$fileName.'.docx');  
        header('Content-Transfer-Encoding:binary');  
        $objWriter = \PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');  
        $objWriter->save('php://output');  
    }

    
    
    //创建管理的word报告
    public function makeManagehWord($appid,$serverity){
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
        Vendor('PHPWord');
 
        $PHPWord = new \PHPWord();

        //设置文件名
        $fileName = 'create_word';
        $low='..'.__ROOT__.'/Public/img/low.png';
        $safe='..'.__ROOT__.'/Public/img/safe.png';
        $medium='..'.__ROOT__.'/Public/img/medium.png';
        $high='..'.__ROOT__.'/Public/img/high.png';
        $title='..'.__ROOT__.'/Public/img/title.png';
        $cellColor=array('bgColor'=>'EAEAEA');
        //设置默认字体
        $PHPWord-> setDefaultFontName('微软雅黑');
        
        // New portrait section
        $section = $PHPWord->createSection();
             
        $styleTable = array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center','tableInden' => 1440);//表格样式
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');//合并行单元格
        $cellRowContinue = array('vMerge' => 'continue');//被合并行单元格
        $cellColSpan = array('gridSpan' => 3, 'valign' => 'center');//合并列单元格
        $cellHCentered = array('align' => 'center');//水平居中
        $cellVCentered = array('valign' => 'center');//垂直居中
        
        $PHPWord->addTableStyle('Colspan Rowspan', $styleTable);//添加表格样式类型
        $PHPWord->addTitleStyle(1, array('size'=>20, 'color'=>'333333', 'bold'=>true));
        $PHPWord->addTitleStyle(2, array('size'=>15, 'color'=>'333333'));
        $PHPWord->addTitleStyle(3, array('size'=>12, 'color'=>'333333'));
        $center=array('align'=>'center');
        $vcenter = array('valign' => 'center');
        $styleName="FontStyle";   
        $styleFont = array('spaceAfter'=>60, 'name'=>'Tahoma', 'size'=>10,'bold'=>true);
        $PHPWord->addFontStyle($styleName, $styleFont);
        
        $PHPWord->addFontStyle('rStyle', array('bold'=>true, '微软雅黑'=>true, 'size'=>14));
        //$PHPWord->addParagraphStyle('pStyle', array('align'=>'center', 'spaceAfter'=>100));//段落样式

        $model                  = D("Appinfo");
        $analysisResults        = D("AnalysisResults");
        $info                   = $model->where(array('appid'=>$appid))->find();

        // $info['bugs']           = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2) ))->sum('issues_count');
        // $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>4 ))->sum('issues_count');
        // $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
        // //高危漏洞类型数量
       
        // $histogram['0']         = $info['gaowei'];
        // //中危漏洞类型数量
        // $histogram['1']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>3 ))->sum('issues_count');
        // // var_export($histogram['mid']);die;
        // //低危漏洞类型数量
        // $histogram['2']          = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>2 ))->sum('issues_count');
        if ($info['internet_security_level']) {
            $Internet_security_level = get_security_level($info['internet_security_level']);
            $where['standard'] = ['like',"%".C('SECURITY_LEVEL_LIKE').$Internet_security_level."%"];
        }
        if($serverity){
            $serverity = explode('_', $serverity);
            if(in_array('4', $serverity)){
                $info['gaowei'] = $histogram['0']  = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
            }else{
                $histogram['0']      = 0;
            }

            if(in_array('3', $serverity)){
                $histogram['1']       = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 3))->sum('issues_count');
            }else{
                $histogram['1']   = 0;
            }
            
            if(in_array('2', $serverity)){
                $histogram['2']       = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 2))->sum('issues_count');
            }else{
                $histogram['2']   = 0;
            }

            // var_export(array('appid'=>$appid,'risk_level'=>array('in',$serverity)));die;
            if(in_array('4', $serverity)){
                $info['0']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
            }
            if(!$info['gaowei']){
                $info['gaowei']         = 0;
            }
            $info['bugs']           = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('in',$serverity)))->sum('issues_count');
            $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('in',$serverity)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
            $detecresultinfo = get_detection_arr($detecresultinfo, $appid,$serverity);
        }else{
            $info['bugs']           = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2) ))->sum('issues_count');
            $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>4 ))->sum('issues_count');
            $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,issues_count DESC')->select();
            //高危漏洞类型数量
            if(!$info['0']){
                $info['0']         = 0;
            }
            $histogram['0']      = $info['gaowei'];
            //中危漏洞类型数量
            $histogram['1']       = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>3 ))->sum('issues_count');
            // var_export($histogram['mid']);die;
            //低危漏洞类型数量
             $histogram['2']          = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>2 ))->sum('issues_count');
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
                $value_i['standard'] = get_vul_result($value_i['standard'], 'world', $Internet_security_level);
            }
    
            $detecresultinfo = array_sort($detecresultinfo, 'standard', 'asc');
        }
        $zh_to_num          = array('暂未评级'=>'0','通过'=>1,'低'=>2,'中'=>3,'高'=>4);
        $num_to_zh          = array('0'=>'暂未评级','1'=>$safe,'2'=>$low,'3'=>$medium,'4'=>$high);
       
        // $detecresultinfo = list_sort_by($detecresultinfo, 'issues_severity', $sortby = 'desc');
                        //设置中文编码

       // dump($serverity);
       // dump($info);
        ///dump($apptoken);
       // $this->display();
        //die;
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
        
        $section->addTextBreak(3);
        $section->addText("应用安全检测管理报告",array('bold'=>true, '微软雅黑'=>true, 'size'=>30),array('align' =>'center'));    
        $section->addTextBreak(5);
        
        if(file_exists($info['icon'])){
            $img =$info['icon'];
        }
        //$section->addText($img);

        $section->addImage($img,array('width'=>200, 'height'=>200,'align'=>'center'));
        
        $section->addTextBreak(7);
        

        
        //'borderSize' => 6, 'borderColor' => '999999''Colspan Rowspan','tableInden' => 1440
        $styleIndexTable = array('alignMent' => 'center','cellMarginTop'=>300);//表格样式   
        $PHPWord->addTableStyle('styleIndexTable', $styleIndexTable);
        $indextable = $section->addTable('styleIndexTable');
        
        //array('cellMerge' => 'restart', 'valign' => "center")
        $cellTextStyleParagraph = array('align' => 'center');//,'spacing'=>100
        $cellTextStyle = array('bold'=>true, '微软雅黑'=>true, 'size'=>16,'textDirection'=>\PHPWord_Style_Cell::TEXT_DIR_BTLR);
        $cellValueStyle = array('borderBottomSize'=>9,'borderBottomColor'=>'999999');
        if ($info ['realname']) {
            $indextable->addRow();
            $indextable->addCell(2000)->addText("应用名称:",$cellTextStyle);
            
            $indextable->addCell(4000,$cellValueStyle)->addText($info ['realname'],$cellTextStyle,$cellTextStyleParagraph);
            //$section->addText($info ['realname']);
        }
        if ($info ['tasktype'] == 'ios') {
            $indextable->addRow();
            $indextable->addCell(2000)->addText("系统:",$cellTextStyle);
            $indextable->addCell(4000,$cellValueStyle)->addText("iOS",$cellTextStyle,$cellTextStyleParagraph);
            //$section->addText("iOS版");
        } elseif ($info ['tasktype'] == 'wx') {
            $indextable->addRow();
            $indextable->addCell(2000)->addText("系统:",$cellTextStyle);
            $indextable->addCell(4000,$cellValueStyle)->addText("微信安全测试",$cellTextStyle,$cellTextStyleParagraph);
            //$section->addText("微信安全测试");
        } elseif($info['tasktype'] == 'android'){
            $indextable->addRow();
            $indextable->addCell(2000)->addText("系统:",$cellTextStyle);
            $indextable->addCell(4000,$cellValueStyle)->addText("Android",$cellTextStyle,$cellTextStyleParagraph);
            //$section->addText("Android版");
        }else{
            $indextable->addRow();
            $indextable->addCell(2000)->addText("系统:",$cellTextStyle);
            $indextable->addCell(4000,$cellValueStyle)->addText("WEB扫描",$cellTextStyle,$cellTextStyleParagraph);
        }   
        if ($info ['version']) {
            $indextable->addRow();
            $indextable->addCell(2000)->addText("版本号:",$cellTextStyle);
            $indextable->addCell(4000,$cellValueStyle)->addText($info['version'],$cellTextStyle,$cellTextStyleParagraph);
            //$section->addText($info['version']);
        }
        
        $indextable->addRow();
        $indextable->addCell(2000)->addText("检测时间:",$cellTextStyle);
        $indextable->addCell(4000,$cellValueStyle)->addText($info ['uploadtime'],$cellTextStyle,$cellTextStyleParagraph);
        //$section->addText($info ['subtime']);
                         
        
        
        $section->addPageBreak();
        
        $section->addText("目录",array('微软雅黑'=>true, 'size'=>24),array('align' =>'center'));
        $section->addTextBreak(2);
        
        //设置目录
        $styleTOC = array('tabLeader'=>\PHPWord_Style_TOC::TABLEADER_DOT);//'tabPos'=>5000,目录样式
        
        $section->addTOC(array('spaceAfter'=>60, 'name'=>'Tahoma', 'size'=>14), $styleTOC);
        
        
        
        // 页眉图片
        
        
        //添加页眉
        $header = $section->createHeader();
        
        $headerdata=M('expand')->where(array('status'=>1))->find();
        $url='..'.__ROOT__.substr($headerdata['watermark'], 1);
        //dump($url);die;
        $table = $header->addTable();
        $table->addRow();
        $table->addCell(800,$cellRowSpan)->addImage($url, array('width'=>35, 'height'=>35, 'align'=>'left'));
        $table->addCell(5000)->addText($headerdata['header']);
        $table->addRow();
        $table->addCell(null, $cellRowContinue);
        $table->addCell(5000)->addText($headerdata['footer']);
    
        //添加页脚
        $footer = $section->createFooter();
        $footer->addPreserveText('第 {PAGE}页 /共{NUMPAGES}页.',null,array('align'=>'center'));
        
        $section->addPageBreak();
        
        
        //第三页
        
        $section->addTitle("1   基本信息",1);
        $section->addTextBreak(1);
        $p = 1;
        //$titleTable=$section->addTable();
        //$titleTable->addRow();
        //$titleTable->addCell()->addImage($title,array('width'=>25, 'height'=>25,'align'=>'left'));
        //$section->addImage($title,array('width'=>25, 'height'=>25,'align'=>'left'));
        //$titleTable->addCell()->addTitle("1.1   应用概况",2);
        $styleBaseInfoTable = array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center','cellMarginTop'=>80,'cellMarginLeft'=>80,'cellMarginRight'=>80,'cellMarginBottom'=>80);
        //array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center','tableInden' => 1440);//表格样式
        $PHPWord->addTableStyle('styleBaseInfoTable', $styleBaseInfoTable);
        $BaseInfocellTextStyle = array('size'=>11);
        
        $section->addTitle("1.$p   系统信息",2);
        $p ++ ;
        
        report_product_ino_word($section,$cellColor,$cellColSpan3,$center,$BaseInfocellTextStyle);
        
        $section->addTitle("1.$p   应用概况",2);
        $p ++ ;
        $baseInfoTable = $section->addTable('styleBaseInfoTable');
        if ($info ['tasktype'] == 'android' || $info ['tasktype'] == 'ios') {
            
            $baseInfoTable->addRow ();
            $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '应用名称', $BaseInfocellTextStyle ,$center);
            $baseInfoTable->addCell ( 2500 )->addText ( $info ['realname'], $BaseInfocellTextStyle );
            $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '版本号', $BaseInfocellTextStyle ,$center);
            $baseInfoTable->addCell ( 3500 )->addText ( $info ['version'], $BaseInfocellTextStyle );
            
            
            //$baseInfoTable->addCell ( 4000 )->addText($new);
            //$baseInfoTable->addCell ( 4000 )->addText ( $info ['version'], $BaseInfocellTextStyle );
            
            $baseInfoTable->addRow ();
            $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '包名', $BaseInfocellTextStyle ,$center);
            $baseInfoTable->addCell ( 2500 )->addText ( $info ['package'], $BaseInfocellTextStyle );
            $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '时间', $BaseInfocellTextStyle ,$center);
            $baseInfoTable->addCell ( 3500 )->addText ( $info ['subtime'], $BaseInfocellTextStyle );
        } elseif ($info ['tasktype'] == 'web' || $info ['tasktype'] == 'awvs') {
            $baseInfoTable->addRow ();
            $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '目标网站', $BaseInfocellTextStyle ,$center);
            $baseInfoTable->addCell ( 2500 )->addText ( $info ['targeturl'], $BaseInfocellTextStyle );
        }
        
        if ($info ['tasktype'] == 'awvs') {
            
            $scanoption = json_decode ( $info ['scanoption'], 1 );
            $checkslen = count ( $scanoption ['checks'] );
            
            if ($checkslen > 0) {
                
                $baseInfoTable->addRow ();
                $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '扫描规则', $BaseInfocellTextStyle ,$center);
                
                $temp = null;
                foreach ( $scanoption ['checks'] as $keyck => $valck ) {
                    $temp = $rulelist [$valck];
                    if ($keyck != ($checkslen - 1)) {
                        $html .= ",";
                    }
                }
                $baseInfoTable->addCell ( 2500 )->addText ( $temp, $BaseInfocellTextStyle );
            }
        }
        $baseInfoTable->addRow();
        $baseInfoTable->addCell(1500,$cellColor)->addText('高危风险',$BaseInfocellTextStyle,$center);
        $baseInfoTable->addCell(2500)->addText($info['gaowei'],$BaseInfocellTextStyle);
        if ($info ['tasktype'] == 'web' || $info ['tasktype'] == 'awvs'){
            $baseInfoTable->addRow();
        }
        $baseInfoTable->addCell(1500,$cellColor)->addText('风险数',$BaseInfocellTextStyle,$center);
        $baseInfoTable->addCell(3500)->addText($info['bugs'],$BaseInfocellTextStyle);
        
        
        
        if($info['tasktype'] == 'android' || $info['tasktype'] == 'ios'){
            $baseInfoTable->addRow();
            $baseInfoTable->addCell(1500,$cellColor)->addText('MD5',$BaseInfocellTextStyle,$center);
            $cellColSpan3 = array('gridSpan' => 3);//合并列单元格
            $baseInfoTable->addCell(7500, $cellColSpan3)->addText($info['md5'],$BaseInfocellTextStyle,null);
            
            $baseInfoTable->addRow();
            $baseInfoTable->addCell(1500,$cellColor)->addText('SHA-1',$BaseInfocellTextStyle,$center);
            $baseInfoTable->addCell(7500, $cellColSpan3)->addText($info['sha1'],$BaseInfocellTextStyle,null);
            
            $baseInfoTable->addRow();
            $baseInfoTable->addCell(1500,$cellColor)->addText('SHA-256',$BaseInfocellTextStyle,$center);        
            $baseInfoTable->addCell(7500, $cellColSpan3)->addText($info['sha256'],$BaseInfocellTextStyle,null);
            if($info['tasktype'] != 'ios'){
                $baseInfoTable->addRow();
                $baseInfoTable->addCell(1500,$cellColor)->addText('证书信息',$BaseInfocellTextStyle,$center);   
                $baseInfoTable->addCell(7500, $cellColSpan3)->addText($info['cert'],$BaseInfocellTextStyle,null);
            }
        }
        if($histogram[0] != false || $histogram[1] != false || $histogram[2] != false){
            $section->addTextBreak(1);
            //dump($histogram);die;
            $section->addTitle("1.$p   风险图例",2);
            $this->makepiechart($histogram,$info['realname']);
            $this->makebarchart($histogram,$info['realname']);
            //dump($histogram);die;

            $imageTable = array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center');       
            $PHPWord->addTableStyle('imageTable', $imageTable);
            $LevelimageStyle = array('width'=>280, 'height'=>200, 'align'=>'center');
            $imagetable=$section->addTable('imageTable');

            $imagetable->addRow();
            $imagetable->addCell(4500)->addImage(UPLOAD_PATH . "temp/" . $info['realname']."bar.png",$LevelimageStyle);
            $imagetable->addCell(null,array('borderSize'=>6,'borderTopColor'=>'FFFFFF','borderBottomColor'=>'FFFFFF'))->addText("A",array('color'=>'FFFFFF'));
            $imagetable->addCell(4500)->addImage(UPLOAD_PATH . "temp/" .$info['realname']. "pie.png",$LevelimageStyle);
     
            
            $section->addPageBreak();

            //第四页
            $section->addTitle("2   检测概述",1);
//             $section->addTextBreak(1);
            if ($info['internet_security_level']) {
                $title_str = get_security_level_title($info['internet_security_level'], $pass_count, $no_pass_count);
                $section->addText($title_str,array('size'=>12),array('align' =>'left')); 
            }
            $styleCheckTable = array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center','cellMarginTop'=>80,'cellMarginLeft'=>80,'cellMarginRight'=>80,'cellMarginBottom'=>80);
            
            //array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center','tableInden' => 1440);//表格样式
            $PHPWord->addTableStyle('styleCheckTable', $styleCheckTable);
            $BaseInfocellTextStyle = array('size'=>12);
             
            $checkTable = $section->addTable('styleCheckTable');
            if ($info['internet_security_level']) {
                $checkTable->addRow(500);
                $checkTable->addCell(800,$cellColor)->addText('序号',$BaseInfocellTextStyle);
                $checkTable->addCell(3000,$cellColor)->addText('检测项目',$BaseInfocellTextStyle);
                $checkTable->addCell(2400,$cellColor)->addText('对标',$BaseInfocellTextStyle);
                $checkTable->addCell(2200,$cellColor)->addText('检测结果',$BaseInfocellTextStyle);
                $checkTable->addCell(1200,$cellColor)->addText('危险系数',$BaseInfocellTextStyle);
    
                foreach ($detecresultinfo as $key => $value) {
                    $checkTable->addRow();
                    $checkTable->addCell(1000,$vcenter)->addText(($key+1),$BaseInfocellTextStyle,$center);
                    $checkTable->addCell(2000)->addText($value['vulriskname'],$BaseInfocellTextStyle);
                    $str_string = trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE'))));
                    $checkTable->addCell(2000)->addText($str_string,$BaseInfocellTextStyle);
                    if($value['risk_level'] == 1){
                        if($value['issues_count']){
                            $checkTable->addCell(2000)->addText('符合(发现'.$value['issues_count'].'处)',$BaseInfocellTextStyle);
                        } else {
                            $checkTable->addCell(2000)->addText('符合',$BaseInfocellTextStyle);
                        }  
                    }else{
                        $checkTable->addCell(2000)->addText('不符合(发现'.$value['issues_count'].'处)',$BaseInfocellTextStyle);
                    }
                    if($value['risk_level'] == 1) {
                        $value['risk_level'] = $value['vulrisklevel'];
                    }
                    $checkTable->addCell(1000,$vcenter)->addImage($num_to_zh[$value['risk_level']],array('width'=>25, 'height'=>25,'align'=>'center'));
                }
            } else {
                $checkTable->addRow(500);
                
                $checkTable->addCell(1000,$cellColor)->addText('序号',$BaseInfocellTextStyle);
                $checkTable->addCell(3000,$cellColor)->addText('检测项目',$BaseInfocellTextStyle);
                $checkTable->addCell(3000,$cellColor)->addText('检测类型',$BaseInfocellTextStyle);
                $checkTable->addCell(1000,$cellColor)->addText('风险数',$BaseInfocellTextStyle);
                $checkTable->addCell(1300,$cellColor)->addText('危险系数',$BaseInfocellTextStyle);
                
                foreach ($detecresultinfo as $key => $value) {
                    $checkTable->addRow();
                    $checkTable->addCell(1000,$vcenter)->addText(($key+1),$BaseInfocellTextStyle,$center);
                    $checkTable->addCell(2000)->addText($value['vulriskname'],$BaseInfocellTextStyle);
                    $checkTable->addCell(2000)->addText($value['hvtype'],$BaseInfocellTextStyle);
                    if($value['issues_count']){
                        $checkTable->addCell(1000,$vcenter)->addText($value['issues_count'],$BaseInfocellTextStyle,$center);
                    }else{
                        $checkTable->addCell(1000,$vcenter)->addText('-',$BaseInfocellTextStyle,$center);
                    }
                    $checkTable->addCell(1000,$vcenter)->addImage($num_to_zh[$value['risk_level']],array('width'=>25, 'height'=>25,'align'=>'center'));
                }
            }
        }
         // Save File
        $fileName = $info['realname']==null?$info['task_name']:$info['realname'];
        
        $test_type = get_task_type($info['tasktype']);
        $fileName = $fileName.'_'.$test_type.'_管理报告_'.date('Y_m_d',time());
        
        $u=__ROOT__."/Uploads/word/word.docx";
        $url='./Uploads/word/'.$fileName;
        
        
        header('Pragma: public');  
        header('Expires: 0');  
        header('Cache-Control:must-revalidate， post-check=0， pre-check=0');  
        header('Content-Type:application/force-download');  
        header('Content-Type:application/vnd.ms-word');  
        header('Content-Type:application/octet-stream');  
        header('Content-Type:application/download');  
        header('Content-Disposition:attachment;filename='.$fileName.'.docx');  
        header('Content-Transfer-Encoding:binary');  
        $objWriter = \PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');  
        $objWriter->save('php://output');  
    }

    /**
     * 通报报告word
     * @param unknown $apptoken
     * @param unknown $serverity
     */
    public function makeTechProposal($apptoken,$serverity){
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
        Vendor('PHPWord');
        
        $PHPWord = new \PHPWord();
        
        //设置文件名
        $fileName = 'create_word';
        $low='..'.__ROOT__.'/Public/img/low.png';
        $safe='..'.__ROOT__.'/Public/img/safe.png';
        $medium='..'.__ROOT__.'/Public/img/medium.png';
        $high='..'.__ROOT__.'/Public/img/high.png';
        $title='..'.__ROOT__.'/Public/img/title.png';
        $cellColor=array('bgColor'=>'EAEAEA');
        //设置默认字体
        $PHPWord-> setDefaultFontName('微软雅黑');
        
        // New portrait section
        $section = $PHPWord->createSection();
        
        $styleTable = array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center','tableInden' => 1440);//表格样式
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');//合并行单元格
        $cellRowContinue = array('vMerge' => 'continue');//被合并行单元格
        $cellColSpan = array('gridSpan' => 3, 'valign' => 'center');//合并列单元格
        $cellHCentered = array('align' => 'center');//水平居中
        $cellVCentered = array('valign' => 'center');//垂直居中
        
        $PHPWord->addTableStyle('Colspan Rowspan', $styleTable);//添加表格样式类型
        
        
        $PHPWord->addTitleStyle(1, array('size'=>20, 'color'=>'333333', 'bold'=>true));
        $PHPWord->addTitleStyle(2, array('size'=>15, 'color'=>'333333'));
        $PHPWord->addTitleStyle(3, array('size'=>12, 'color'=>'333333'));
        $center=array('align'=>'center');
        $left=array('align'=>'left');
        $vcenter = array('valign' => 'center');
        
        $styleName="FontStyle";
        $styleFont = array('spaceAfter'=>60, 'name'=>'Tahoma', 'size'=>10,'bold'=>true);
        $PHPWord->addFontStyle($styleName, $styleFont);
        
        $PHPWord->addFontStyle('rStyle', array('bold'=>true, '微软雅黑'=>true, 'size'=>14));
        //$PHPWord->addParagraphStyle('pStyle', array('align'=>'center', 'spaceAfter'=>100));//段落样式
        
        
        
        $model                  = D("Appinfo");
        // $vulinfo                = D("Vulinfo");
        $analysisResults        = D("AnalysisResults");
        //$apptoken               = I('get.apptoken');
        // $serverity               = I('get.serverity');
        $appid                  = $model->where(array('apptoken'=>$apptoken))->find()['appid'];
        $info                   = $model->where(array('appid'=>$appid))->find();
        
        if ($info['internet_security_level']) {
            $Internet_security_level = get_security_level($info['internet_security_level']);
            $where['standard'] = ['like',"%".C('SECURITY_LEVEL_LIKE').$Internet_security_level."%"];
        }
        if($serverity){
            $serverity = explode('_', $serverity);
            if(in_array('4', $serverity)){
                $info['gaowei']  =  $histogram['0']  = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
            }
            
            if($histogram['0'] == null){
                $histogram['0'] = 0;
            }
            
            if(in_array('3', $serverity)){
                $histogram['1']       = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 3))->sum('issues_count');
            }
            if($histogram['1'] == null){
                $histogram['1'] = 0;
            }
            
            
            if(in_array('2', $serverity)){
                $histogram['2']       = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 2))->sum('issues_count');
            }
            if($histogram['2'] == null){
                $histogram['2'] = 0;
            }
            
            // var_export(array('appid'=>$appid,'risk_level'=>array('in',$serverity)));die;
            if(in_array('4', $serverity)){
                $info['0']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
            }
            if(!$info['gaowei']){
                $info['gaowei']         = 0;
            }
            $info['bugs']           = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('in',$serverity)))->sum('issues_count');
            
            $proposal_count = $analysisResults->where($where)->where(['appid'=>$appid, 'risk_level' => ['in',$serverity]])->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->count();
            
            $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('in',$serverity)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,is_proposal ASC,issues_count DESC')->select();
            $detecresultinfo = get_detection_arr($detecresultinfo, $appid,$serverity);
        }else{
            $info['bugs']           = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2) ))->sum('issues_count');
            $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>4 ))->sum('issues_count');
            $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,is_proposal ASC,issues_count DESC')->select();
            $proposal_count = $analysisResults->where($where)->where(['appid'=>$appid, 'risk_level' => ['egt',2]])->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->count();
            //高危漏洞类型数量
            if(!$info['0']){
                $info['0']         = 0;
            }
            $histogram['0']      = $info['gaowei'];
            //中危漏洞类型数量
            $histogram['1']       = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>3 ))->sum('issues_count');
            if(!$histogram['1']){
                $histogram['1']         = 0;
            }
            // var_export($histogram['mid']);die;
            //低危漏洞类型数量
            $histogram['2']          = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>2 ))->sum('issues_count');
            if(!$histogram['2']){
                $histogram['2']         = 0;
            }
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
                $value_i['standard'] = get_vul_result($value_i['standard'], 'world', $Internet_security_level);
            }
            $detecresultinfo = array_sort($detecresultinfo, 'standard', 'asc');
        }
        $zh_to_num          = array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
        $num_to_zh          = array('0'=>'暂未评级','1'=>$safe,'2'=>$low,'3'=>$medium,'4'=>$high);
        
        // $detecresultinfo = list_sort_by($detecresultinfo, 'issues_severity', $sortby = 'desc');
        //设置中文编码
        
        // dump($serverity);
        // dump($info);
        ///dump($apptoken);
        // $this->display();
        //die;
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
        
        $section->addTextBreak(3);
        $section->addText("应用安全检测通报报告",array('bold'=>true, '微软雅黑'=>true, 'size'=>30),array('align' =>'center'));
        $section->addTextBreak(5);
        
        if(file_exists($info['icon'])){
            $img =$info['icon'];
        }
        //$section->addText($img);
        
        $section->addImage($img,array('width'=>200, 'height'=>200,'align'=>'center'));
        
        $section->addTextBreak(7);
        
        
        
        //'borderSize' => 6, 'borderColor' => '999999''Colspan Rowspan','tableInden' => 1440
        $styleIndexTable = array('alignMent' => 'center','cellMarginTop'=>300);//表格样式
        $PHPWord->addTableStyle('styleIndexTable', $styleIndexTable);
        $indextable = $section->addTable('styleIndexTable');
        
        //array('cellMerge' => 'restart', 'valign' => "center")
        $cellTextStyleParagraph = array('align' => 'center');//,'spacing'=>100
        $cellTextStyle = array('bold'=>true, '微软雅黑'=>true, 'size'=>16,'textDirection'=>\PHPWord_Style_Cell::TEXT_DIR_BTLR);
        $cellValueStyle = array('borderBottomSize'=>9,'borderBottomColor'=>'999999');
        if ($info ['realname']) {
            $indextable->addRow();
            $indextable->addCell(2000)->addText("应用名称:",$cellTextStyle);
            
            $indextable->addCell(4000,$cellValueStyle)->addText($info ['realname'],$cellTextStyle,$cellTextStyleParagraph);
            //$section->addText($info ['realname']);
        }
        if ($info ['tasktype'] == 'ios') {
            $indextable->addRow();
            $indextable->addCell(2000)->addText("系统:",$cellTextStyle);
            $indextable->addCell(4000,$cellValueStyle)->addText("iOS",$cellTextStyle,$cellTextStyleParagraph);
            //$section->addText("iOS版");
        } elseif ($info ['tasktype'] == 'wx') {
            $indextable->addRow();
            $indextable->addCell(2000)->addText("系统:",$cellTextStyle);
            $indextable->addCell(4000,$cellValueStyle)->addText("微信安全测试",$cellTextStyle,$cellTextStyleParagraph);
            //$section->addText("微信安全测试");
        } elseif($info['tasktype'] == 'android'){
            $indextable->addRow();
            $indextable->addCell(2000)->addText("系统:",$cellTextStyle);
            $indextable->addCell(4000,$cellValueStyle)->addText("Android",$cellTextStyle,$cellTextStyleParagraph);
            //$section->addText("Android版");
        }else{
            $indextable->addRow();
            $indextable->addCell(2000)->addText("系统:",$cellTextStyle);
            $indextable->addCell(4000,$cellValueStyle)->addText("WEB扫描",$cellTextStyle,$cellTextStyleParagraph);
        }
        if ($info ['version']) {
            $indextable->addRow();
            $indextable->addCell(2000)->addText("版本号:",$cellTextStyle);
            $indextable->addCell(4000,$cellValueStyle)->addText($info['version'],$cellTextStyle,$cellTextStyleParagraph);
            //$section->addText($info['version']);
        }
        
        $indextable->addRow();
        $indextable->addCell(2000)->addText("检测时间:",$cellTextStyle);
        $indextable->addCell(4000,$cellValueStyle)->addText($info ['uploadtime'],$cellTextStyle,$cellTextStyleParagraph);
        //$section->addText($info ['subtime']);
        
        
        
        $section->addPageBreak();
        
        $section->addText("目录",array('微软雅黑'=>true, 'size'=>24),array('align' =>'center'));
        $section->addTextBreak(1);
        
        //设置目录
        $styleTOC = array('tabLeader'=>\PHPWord_Style_TOC::TABLEADER_DOT);//'tabPos'=>5000,目录样式
        
        $section->addTOC(array('spaceAfter'=>60, 'name'=>'Tahoma', 'size'=>14), $styleTOC);
        
        
        
        // 页眉图片
        
        
        //添加页眉
        $header = $section->createHeader();
        
        $headerdata=M('expand')->where(array('status'=>1))->find();
        $url='..'.__ROOT__.substr($headerdata['watermark'], 1);
        //dump($url);die;
        $table = $header->addTable();
        $table->addRow();
        $table->addCell(800,$cellRowSpan)->addImage($url, array('width'=>35, 'height'=>35, 'align'=>'left'));
        $table->addCell(5000)->addText($headerdata['header']);
        $table->addRow();
        $table->addCell(null, $cellRowContinue);
        $table->addCell(5000)->addText($headerdata['footer']);
        
        //添加页脚
        $footer = $section->createFooter();
        $footer->addPreserveText('第 {PAGE}页 /共{NUMPAGES}页.',null,array('align'=>'center'));
        
        $section->addPageBreak();
        
        
        //第三页
        
        $section->addTitle("1   基本信息",1);
        $section->addTextBreak(1);
        $p = 1;
        //$titleTable=$section->addTable();
        //$titleTable->addRow();
        //$titleTable->addCell()->addImage($title,array('width'=>25, 'height'=>25,'align'=>'left'));
        //$section->addImage($title,array('width'=>25, 'height'=>25,'align'=>'left'));
        //$titleTable->addCell()->addTitle("1.1   应用概况",2);
        $styleBaseInfoTable = array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center','cellMarginTop'=>80,'cellMarginLeft'=>80,'cellMarginRight'=>80,'cellMarginBottom'=>80);
        //array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center','tableInden' => 1440);//表格样式
        $PHPWord->addTableStyle('styleBaseInfoTable', $styleBaseInfoTable);
        $BaseInfocellTextStyle = array('size'=>11);
        
        $section->addTitle("1.$p   系统信息",2);
        $p ++ ;
        
        report_product_ino_word($section,$cellColor,$cellColSpan3,$center,$BaseInfocellTextStyle);
        
        $section->addTitle("1.$p   应用概况",2);
        $p ++ ;
        $baseInfoTable = $section->addTable('styleBaseInfoTable');
        if ($info ['tasktype'] == 'android' || $info ['tasktype'] == 'ios') {
            
            $baseInfoTable->addRow ();
            $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '应用名称', $BaseInfocellTextStyle ,$center);
            $baseInfoTable->addCell ( 2500 )->addText ( $info ['realname'], $BaseInfocellTextStyle );
            $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '版本号', $BaseInfocellTextStyle ,$center);
            $baseInfoTable->addCell ( 3500 )->addText ( $info ['version'], $BaseInfocellTextStyle );
            
            
            //$baseInfoTable->addCell ( 4000 )->addText($new);
            //$baseInfoTable->addCell ( 4000 )->addText ( $info ['version'], $BaseInfocellTextStyle );
            
            $baseInfoTable->addRow ();
            $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '包名', $BaseInfocellTextStyle ,$center);
            $baseInfoTable->addCell ( 2500 )->addText ( $info ['package'], $BaseInfocellTextStyle );
            $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '时间', $BaseInfocellTextStyle ,$center);
            $baseInfoTable->addCell ( 3500 )->addText ( $info ['subtime'], $BaseInfocellTextStyle );
        } elseif ($info ['tasktype'] == 'web' || $info ['tasktype'] == 'awvs') {
            $baseInfoTable->addRow ();
            $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '目标网站', $BaseInfocellTextStyle ,$center);
            $baseInfoTable->addCell ( 2500 )->addText ( $info ['targeturl'], $BaseInfocellTextStyle );
        }
        
        if ($info ['tasktype'] == 'awvs') {
            
            $scanoption = json_decode ( $info ['scanoption'], 1 );
            $checkslen = count ( $scanoption ['checks'] );
            
            if ($checkslen > 0) {
                
                $baseInfoTable->addRow ();
                $baseInfoTable->addCell ( 1500 ,$cellColor)->addText ( '扫描规则', $BaseInfocellTextStyle ,$center);
                
                $temp = null;
                foreach ( $scanoption ['checks'] as $keyck => $valck ) {
                    $temp = $rulelist [$valck];
                    if ($keyck != ($checkslen - 1)) {
                        $html .= ",";
                    }
                }
                $baseInfoTable->addCell ( 2500 )->addText ( $temp, $BaseInfocellTextStyle );
            }
        }
        $baseInfoTable->addRow();
        $baseInfoTable->addCell(1500,$cellColor)->addText('高危风险',$BaseInfocellTextStyle,$center);
        $baseInfoTable->addCell(2500)->addText($info['gaowei'],$BaseInfocellTextStyle);
        if ($info ['tasktype'] == 'web' || $info ['tasktype'] == 'awvs'){
            $baseInfoTable->addRow();
        }
        $baseInfoTable->addCell(1500,$cellColor)->addText('风险数',$BaseInfocellTextStyle,$center);
        $baseInfoTable->addCell(3500)->addText($info['bugs'],$BaseInfocellTextStyle);
        
        
        
        if($info['tasktype'] == 'android' || $info['tasktype'] == 'ios'){
            $baseInfoTable->addRow();
            $cellColSpan3 = array('gridSpan' => 3);//合并列单元格
            
            $baseInfoTable->addCell(1500,$cellColor)->addText('建议通报数',$BaseInfocellTextStyle,$center);
            $baseInfoTable->addCell(7500, $cellColSpan3)->addText($proposal_count, $BaseInfocellTextStyle,null);
            
            $baseInfoTable->addRow();
            $baseInfoTable->addCell(1500,$cellColor)->addText('MD5',$BaseInfocellTextStyle,$center);
            $baseInfoTable->addCell(7500, $cellColSpan3)->addText($info['md5'],$BaseInfocellTextStyle,null);
            
            $baseInfoTable->addRow();
            $baseInfoTable->addCell(1500,$cellColor)->addText('SHA-1',$BaseInfocellTextStyle,$center);
            $baseInfoTable->addCell(7500, $cellColSpan3)->addText($info['sha1'],$BaseInfocellTextStyle,null);
            
            $baseInfoTable->addRow();
            $baseInfoTable->addCell(1500,$cellColor)->addText('SHA-256',$BaseInfocellTextStyle,$center);
            $baseInfoTable->addCell(7500, $cellColSpan3)->addText($info['sha256'],$BaseInfocellTextStyle,null);
            if($info['tasktype'] != 'ios'){
                $baseInfoTable->addRow();
                $baseInfoTable->addCell(1500,$cellColor)->addText('证书信息',$BaseInfocellTextStyle,$center);
                $baseInfoTable->addCell(7500, $cellColSpan3)->addText($info['cert'],$BaseInfocellTextStyle,null);
            }
        }
        
        if($histogram[0] != false || $histogram[1] != false || $histogram[2] != false){
            $section->addTextBreak(1);
            //dump($histogram);die;
            $section->addTitle("1.$p   风险图例",2);
            //             echo $info['realname'];
            //             print_r($histogram);die();
            $this->makepiechart($histogram,$info['realname']);
            $this->makebarchart($histogram,$info['realname']);
            //dump($histogram);die;
            
            $LevelimageStyle = array('width'=>280, 'height'=>180, 'align'=>'center');
            $imagetable=$section->addTable('styleBaseInfoTable');
            $imagetable->addRow();
            $imagetable->addCell(4500)->addImage(UPLOAD_PATH . "temp/" . $info['realname']."bar.png",$LevelimageStyle);
            $imagetable->addCell(null,array('borderSize'=>6,'borderTopColor'=>'FFFFFF','borderBottomColor'=>'FFFFFF'))->addText("A",array('color'=>'FFFFFF'));
            $imagetable->addCell(4500)->addImage(UPLOAD_PATH . "temp/" .$info['realname']. "pie.png",$LevelimageStyle);
            
            
            $section->addPageBreak();
            
            //第四页
            
            $section->addTitle("2   检测概述",1);
            if ($info['internet_security_level']) {
                $title_str = get_security_level_title($info['internet_security_level'], $pass_count, $no_pass_count);
                $section->addText($title_str,array('size'=>12),array('align' =>'left'));
            }
            //             $section->addTextBreak(1);
            //             $section->addCell(8000,$cellColor)->addText($title_str,$BaseInfocellTextStyle);
            
            $styleCheckTable = array('borderSize' => 6,'word-break'=>'break-all','borderColor' => '999999','alignMent' => 'center','cellMarginTop'=>80,'cellMarginLeft'=>80,'cellMarginRight'=>80,'cellMarginBottom'=>80);
            //array('borderSize' => 6, 'borderColor' => '999999','alignMent' => 'center','tableInden' => 1440);//表格样式
            $PHPWord->addTableStyle('styleCheckTable', $styleCheckTable);
            $BaseInfocellTextStyle = array('size'=>12);
            
            $checkTable = $section->addTable('styleCheckTable');
            if ($info['internet_security_level']) {
                $checkTable->addRow();
                $checkTable->addCell(800,$cellColor)->addText('序号',$BaseInfocellTextStyle);
                $checkTable->addCell(3000,$cellColor)->addText('检测项目',$BaseInfocellTextStyle);
                $checkTable->addCell(2400,$cellColor)->addText('对标',$BaseInfocellTextStyle);
                $checkTable->addCell(2200,$cellColor)->addText('检测结果',$BaseInfocellTextStyle);
                $checkTable->addCell(1200,$cellColor)->addText('危险系数',$BaseInfocellTextStyle);
                
                foreach ($detecresultinfo as $key => $value) {
                    $checkTable->addRow();
                    $checkTable->addCell(1000,$vcenter)->addText(($key+1),$BaseInfocellTextStyle,$center);
                    $checkTable->addCell(2000)->addText($value['vulriskname'],$BaseInfocellTextStyle);
                    $str_string = trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE'))));
                    $checkTable->addCell(2000)->addText($str_string,$BaseInfocellTextStyle);
                    if($value['risk_level'] == 1){
                        if($value['issues_count']){
                            $checkTable->addCell(2000)->addText('符合(发现'.$value['issues_count'].'处)',$BaseInfocellTextStyle);
                        } else {
                            $checkTable->addCell(2000)->addText('符合',$BaseInfocellTextStyle);
                        }
                    }else{
                        $checkTable->addCell(2000)->addText('不符合(发现'.$value['issues_count'].'处)',$BaseInfocellTextStyle);
                    }
                    if($value['risk_level'] == 1) {
                        $value['risk_level'] = $value['vulrisklevel'];
                    }
                    $checkTable->addCell(1000,$vcenter)->addImage($num_to_zh[$value['risk_level']],array('width'=>25, 'height'=>25,'align'=>'center'));
                }
            } else {
                $checkTable->addRow();
                $checkTable->addCell(1000,$cellColor)->addText('序号',$BaseInfocellTextStyle);
                $checkTable->addCell(3000,$cellColor)->addText('检测项目',$BaseInfocellTextStyle);
                $checkTable->addCell(2000,$cellColor)->addText('检测类型',$BaseInfocellTextStyle);
                
                $checkTable->addCell(1700,$cellColor)->addText('是否通报建议',$BaseInfocellTextStyle);
                
                $checkTable->addCell(1000,$cellColor)->addText('风险数',$BaseInfocellTextStyle);
                $checkTable->addCell(1300,$cellColor)->addText('危险系数',$BaseInfocellTextStyle);
                foreach ($detecresultinfo as $key => $value) {
                    $checkTable->addRow();
                    $checkTable->addCell(1000,$vcenter)->addText(($key+1),$BaseInfocellTextStyle,$center);
                    $checkTable->addCell(3000)->addText($value['vulriskname'],$BaseInfocellTextStyle);
                    $checkTable->addCell(2000)->addText($value['hvtype'],$BaseInfocellTextStyle);
                    if($value['is_proposal'] == 1) {
                        $checkTable->addCell(1700,$vcenter)->addText($this->is_proposal[$value['is_proposal']],array('size'=>11,'color'=>'f44336'),$center);
                    } else {
                        $checkTable->addCell(1700,$vcenter)->addText($this->is_proposal[$value['is_proposal']],$BaseInfocellTextStyle,$center);
                    }
                    
                    if($value['issues_count']){
                        $checkTable->addCell(1000,$vcenter)->addText($value['issues_count'],$BaseInfocellTextStyle,$center);
                    }else{
                        $checkTable->addCell(1000,$vcenter)->addText('-',$BaseInfocellTextStyle,$center);
                    }
                    // $checkTable->addCell(1000,$vcenter)->addText($num_to_zh[$value['risk_level']],$BaseInfocellTextStyle,$center);
                    $checkTable->addCell(1300,$vcenter)->addImage($num_to_zh[$value['risk_level']],array('width'=>25, 'height'=>25,'align'=>'center'));
                }
            }
            $section->addPageBreak();
            
            
            //第四页
            
            $section->addTitle("3   详细检测结果",1);
            $section->addTextBreak(1);
            
            foreach ($detecresultinfo as $key => $value) {
                $section->addTitle('3.'.($key+1).' '.$value['case_name'],2);
                $checkTable = $section->addTable('styleCheckTable');
                //$checkTableCellstyle=array('valign' => 'center','bgColor'=>'EAEAEA');
                $checkTableCellstyle=array('gridSpan' => 2,'valign' => 'center','bgColor'=>'EAEAEA');
                //$checkTableTitlestyle=array('gridSpan' => 2,'valign' => 'center','bgColor'=>'EAEAEA');
                $checkTableCellstyle2=array('gridSpan' => 4,'valign' => 'center');
                
                /*@author:qxn 2017-11-23 1、“3、详细检测结果”对标和风险编号做判断，添加用例名称（与pdf和html报告对应）*/
                $checkTable->addRow();
                $checkTable->addCell(2000,$checkTableCellstyle)->addText('用例名称',$BaseInfocellTextStyle,$center);
                $checkTable->addCell(8000,$checkTableCellstyle2)->addText($value['case_name'],$BaseInfocellTextStyle);
                
                $checkTable->addRow();
                if ($info['internet_security_level']) {
                    $checkTable->addCell(2000,$checkTableCellstyle)->addText('对标',$BaseInfocellTextStyle,$center);
                } else {
                    $checkTable->addCell(2000,$checkTableCellstyle)->addText('风险编号',$BaseInfocellTextStyle,$center);
                }
                /*2017-11-23*/
                
                
                if ($info['internet_security_level']) {
                    $str_string = get_vul_result($value['standard'],'world',$Internet_security_level);
                } else {
                    $str_string = $value['standard'];
                }
                $checkTable->addCell(8000,$checkTableCellstyle2)->addText($str_string,$BaseInfocellTextStyle);
                
                
                //$checkTable->addCell(8000,$checkTableCellstyle2)->addText($value['standard'],$BaseInfocellTextStyle);
                
                // $checkTable->addRow();
                
                // $checkTable->addCell(2000,$checkTableCellstyle)->addText('风险编号',$BaseInfocellTextStyle,$center);
                // $text=$value['hvdid'];
                // $checkTable->addCell(8000)->addText($text, $BaseInfocellTextStyle );
                
                $checkTable->addRow();
                $checkTable->addCell(2000,$checkTableCellstyle)->addText('是否通报建议',$BaseInfocellTextStyle,$center);
                $checkTable->addCell(8000,$checkTableCellstyle2)->addText($this->is_proposal[$value['is_proposal']],$BaseInfocellTextStyle);
                
                $checkTable->addRow();
                $checkTable->addCell(2000,$checkTableCellstyle)->addText('风险描述',$BaseInfocellTextStyle,$center);
                $checkTable->addCell(8000,$checkTableCellstyle2)->addText($value['risk_description'],$BaseInfocellTextStyle);
                
                // $checkTable->addRow();
                // $checkTable->addCell(2000,$checkTableCellstyle)->addText('检测方法',$BaseInfocellTextStyle,$center);
                // $checkTable->addCell(8000)->addText($value['detection_method'],$BaseInfocellTextStyle);
                
                $checkTable->addRow();
                //$cellColSpan2 = array('gridSpan' => 2, 'valign' => 'center','bgColor'=>'EAEAEA');
                $cellColSpan2= array('gridSpan' => 6, 'valign' => 'center','bgColor'=>'EAEAEA');
                //$cellColSpanValue = array('gridSpan' => 2, 'valign' => 'center');
                $cellColSpanValue= array('gridSpan' => 6, 'valign' => 'center');
                $cellColSpan4 = array('gridSpan' => 2, 'valign' => 'center','bgColor'=>'EAEAEA');
                $style = array('word-break'=>'break-all');
                if($value['detection_process'] && $value['detection_process'] != "N/A"){
                    $checkTable->addCell(10000,$cellColSpan2)->addText('检测过程',$BaseInfocellTextStyle,$center);
                    
                    $checkTable->addRow();
                    //                     $detection_process = trim($detection_process);
                    //                     $detection_process = rtrim(str_replace(array("[.]","[img]","[/img]","&lt;bbr&gt;",'&lt;bbr/&gt;'),array("\"","<img",">","<b>","</b>"),str_replace(array("\r","\r\n","\n","<!--","-->"),"\\n", $value['detection_process'])),"\\n");
                    $detection_process = rtrim(str_replace(array("[.]","[img]","[/img]","&lt;bbr&gt;",'&lt;bbr/&gt;'),array("\"","<img",">","<b>","</b>"),str_replace(array("\r","\r\n","\n","<!--","-->"),"\\n", $value['detection_process'])));
                    $detection_process = str_replace(array("\\n \\n", "\\n\\n"), "\\n", $detection_process);
                    $detection_process= preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/","",$detection_process);
                    
                    if (substr($detection_process, -2) == "\\n"){
                        $detection_process = substr($detection_process, 0, strlen($detection_process)-2);
                    }
                    
                    //var_export($detection_process);
                    //$LevelimageStyle = array('width'=>300, 'height'=>200, 'align'=>'center');
                    $process=$checkTable->addCell(10000,$cellColSpanValue);
                    
                    preg_match_all('/<img src=\"\/mare\/\.\/Uploads\/([^>]+)\" width=\"200px\"[\/]*>/', $detection_process, $sp);
                    
                    preg_match_all('/<bbr>[a-zA-Z\.0-9]+<bbr\/>/', $detection_process, $zujian);
                    
                    if($sp[0] && $sp[1]){
                        for($i=0;$i<count($sp[0]);$i++){
                            $str=explode($sp[0][$i], $detection_process);
                            $process->addText($str[0],null,$left);
                            $process->addImage(UPLOAD_PATH.$sp[1][$i],array('width'=>200, 'height'=>300));
                            $detection_process=$str[1];
                            if($i==count($sp[0])-1){
                                $process->addText($str[1],null,$left);
                            }
                            
                        }
                    }else{
                        if($zujian[0]){
                            for($j=0;$j<count($zujian[0]);$j++){
                                $zujianstr=explode($zujian[0][$j], $detection_process);
                                $process->addText($zujianstr[0],array('color'=>'454545'),$left);
                                $process->addText(str_replace(array("<bbr>","<bbr/>"),"",$zujian[0][$j]),$BaseInfocellTextStyle,$left);
                                $detection_process=$zujianstr[1];
                                if($j==count($zujian[0])-1){
                                    $process->addText($zujianstr[1],null,$left);
                                }
                            }
                            
                            
                        }else{
                            //在此处理URL
                            if($value['detectionid'] == 44 || $value['detectionid'] == 142){
                                //                                 $arr=explode("URL：",$detection_process);
                                //                                 $arr=explode("相关内容：",$detection_process);
                                
                                if($value['detectionid'] == 44) {
                                    $arr=explode("相关内容：",$detection_process);
                                } else if($value['detectionid'] == 142) {
                                    $arr=explode("请求URL：",$detection_process);
                                    
                                }
                                
                                $temp=explode("####",$arr[1]);
                                //                                 print_r($temp);
                                //                                 echo count($temp);die();
                                if($value['detectionid']==44) {
                                    $arr[1]=$temp[0];
                                    $arr[2] = $temp[1];
                                    $inner_data=json_decode(str_replace("\\n", "", $arr[1]));
                                } else if($value['detectionid'] == 142) {
                                    //                                     var_dump($temp);die();
                                    //                                     echo count($temp);die();
                                    $arr[1]=$temp;
                                    foreach ($arr[1] as $k => $v) {
                                        $arr_list = json_decode(str_replace("\\n", "", $v), true);
                                        //                                         print_r($arr_list);die();
                                        foreach ($arr_list as $k1 => $v1) {
                                            $inner_data[] = $v1;
                                        }
                                        unset($arr_list);
                                    }
                                    //                                     print_r($inner_data);die();
                                }
                                
                                
                                
                                if ($inner_data) {
                                    $arr[0] = trim($arr[0]);
                                    if (substr($arr[0], -2) == "\\n"){
                                        $arr[0]= substr($arr[0], 0, strlen($arr[0])-2);
                                    }
                                    $process->addText($arr[0],null,array('align'=>'left'));
                                    $styleCheckTable = array('borderSize' => 6, 'word-break'=>'break-all','borderColor' => '999999','alignMent' => 'center','cellMarginTop'=>80,'cellMarginLeft'=>10,'cellMarginRight'=>10,'cellMarginBottom'=>80);
                                    $PHPWord->addTableStyle('styleCheckTable', $styleCheckTable);
                                    $inner_detection_process = $section->addTable('styleCheckTable');
                                    
                                    $checkTableInnerCellTitlestyle = array('valign' => 'center','bgColor'=>'EAEAEA');//,'bgColor'=>'EAEAEA'
                                    $checkTableInnerCellValuestyle = array('valign' => 'center');
                                    
                                    $innerTableTitleStyle = array('size'=>10);//$innerTableTitleStyle = array('size'=>10,'bold'=>true);
                                    $innerTableTextStyle = array('size'=>9);
                                    $left=array('align'=>'left');
                                    
                                    $inner_detection_process->addRow();
                                    $inner_detection_process->addCell(600,$checkTableInnerCellTitlestyle)->addText('序号',$innerTableTitleStyle,$center);
                                    $inner_detection_process->addCell(1400,$checkTableInnerCellTitlestyle)->addText('域名',$innerTableTitleStyle,$center);
                                    $inner_detection_process->addCell(2000,$checkTableInnerCellTitlestyle)->addText('url',$innerTableTitleStyle,$center);
                                    $inner_detection_process->addCell(2000,$checkTableInnerCellTitlestyle)->addText('IP列表',$innerTableTitleStyle,$center);
                                    $inner_detection_process->addCell(2000,$checkTableInnerCellTitlestyle)->addText('注册域名商',$innerTableTitleStyle,$center);
                                    $inner_detection_process->addCell(2000,$checkTableInnerCellTitlestyle)->addText('DNS解析记录',$innerTableTitleStyle,$center);
                                    
                                    
                                    if($value['detectionid'] == 44) {
                                        $inner_data=json_decode(str_replace("\\n", "", $arr[1]));
                                    }
                                    
                                    //                                 foreach ($inner_data as $key_s => $value_s) {
                                    //                                     $arr_s[$key_s]['host'] =  $value_s['host'];
                                    //                                     $arr_s[$key_s]['url'] =  wordwrap(implode("\n", $value_s['url']),20,'\n',true);
                                    //                                     $arr_s[$key_s]['ip_list'] =  implode("\n", $value_s['ip_list']);
                                    //                                     $arr_s[$key_s]['register'] =  $value_s['register'];
                                    //                                     $arr_s[$key_s]['dns_history'] =  implode("\n", $value_s['dns_history']);
                                    //                                 }
                                    //                                 P($inner_data);
                                    //                                 P($arr_s);die();
                                    //                                 foreach ($arr_s as $k => $v) {
                                    //                                     $checkTable->addRow();
                                    //                                     $checkTable->addCell(1000)->addText($k+1,$innerTableTextStyle,$left);
                                    //                                     $checkTable->addCell(1400)->addText($v['host'],$innerTableTextStyle,$left);
                                    //                                     $checkTable->addCell(3000)->addText($v['url'],$innerTableTextStyle,$left);
                                    //                                     $checkTable->addCell(2000)->addText($v['ip_list'],$innerTableTextStyle,$left);
                                    //                                     $checkTable->addCell(1500)->addText($v['register'],$innerTableTextStyle,$left);
                                    //                                     $checkTable->addCell(1500)->addText($v['dns_history'],$innerTableTextStyle,$left);
                                    //                                 }
                                    if($value['detectionid'] == 44) {
                                        foreach ($inner_data as $k=>$v){
                                            
                                            $inner_detection_process->addRow();
                                            $inner_detection_process->addCell(600)->addText($k+1,$innerTableTextStyle,$left);
                                            $inner_detection_process->addCell(1400)->addText($v->host,$innerTableTextStyle,$left);
                                            
                                            $url_list_cell=$inner_detection_process->addCell(2000);
                                            foreach ($v->url as $k1=>$v1){
                                                $url_list_cell->addText($v1,$innerTableTextStyle,$left);
                                                //                                         $url_list_cell->addText(wordwrap(trim($v1),20,'\n',true),$innerTableTextStyle,$left);
                                            }
                                            
                                            $ip_list_cell=$inner_detection_process->addCell(2000);
                                            foreach ($v->ip_list as $k1=>$v1){
                                                $ip_list_cell->addText($v1,$innerTableTextStyle,$left);
                                            }
                                            
                                            $inner_detection_process->addCell(2000)->addText(wordwrap(trim($v->register),30,'\n',true),$innerTableTextStyle,$left);
                                            
                                            $dns_history_cell=$inner_detection_process->addCell(2000);
                                            foreach ($v->dns_history as $k1=>$v1){
                                                $dns_history_cell->addText($v1,$innerTableTextStyle,$left);
                                            }
                                            
                                        }
                                    } else {
                                        foreach ($inner_data as $k=>$v){
                                            
                                            $inner_detection_process->addRow();
                                            $inner_detection_process->addCell(600)->addText($k+1,$innerTableTextStyle,$left);
                                            $inner_detection_process->addCell(1400)->addText($v['host'],$innerTableTextStyle,$left);
                                            
                                            $url_list_cell=$inner_detection_process->addCell(2000);
                                            foreach ($v['url'] as $k1=>$v1){
                                                $url_list_cell->addText($v1,$innerTableTextStyle,$left);
                                                //                                         $url_list_cell->addText(wordwrap(trim($v1),20,'\n',true),$innerTableTextStyle,$left);
                                            }
                                            
                                            $ip_list_cell=$inner_detection_process->addCell(2000);
                                            foreach ($v['ip_list'] as $k1=>$v1){
                                                $ip_list_cell->addText($v1,$innerTableTextStyle,$left);
                                            }
                                            
                                            $inner_detection_process->addCell(2000)->addText(wordwrap(trim($v['register']),30,'\n',true),$innerTableTextStyle,$left);
                                            
                                            $dns_history_cell=$inner_detection_process->addCell(2000);
                                            foreach ($v['dns_history'] as $k1=>$v1){
                                                $dns_history_cell->addText($v1,$innerTableTextStyle,$left);
                                            }
                                            
                                        }
                                    }
                                    if($value['detectionid']==44) {
                                        if(trim($arr[2])) {
                                            $cellColSpan5 = array('gridSpan' => 6, 'valign' => 'center');
                                            $inner_detection_process->addRow();
                                            $inner_detection_process->addCell(10000, $cellColSpan5)->addText(trim($arr[2]),$BaseInfocellTextStyle,$left);
                                        }
                                    }
                                } else {
                                    $process->addText($detection_process,null,array('align'=>'left'));
                                }
                                unset($inner_data);
                            }else{
                                $process->addText($detection_process,null,array('align'=>'left'));
                            }
                        }
                    }
                } else {
                    $checkTable->addCell(10000,$cellColSpan2)->addText('检测过程',$BaseInfocellTextStyle,$center);
                    $checkTable->addRow();
                    $process=$checkTable->addCell(10000,$cellColSpanValue);
                    $process->addText('无',null,array('align'=>'left','font-size'=>11));
                }
                
                $checkTable->addRow();
                $checkTable->addCell(2000,$checkTableCellstyle)->addText('风险系数',$BaseInfocellTextStyle,$center);
                $vcenter = array('gridSpan' => 4,'valign' => 'center');
                //$checkTableCellstyle2=array('gridSpan' => 4,'valign' => 'center');
                // $checkTable->addCell(8000)->addText($num_to_zh[$value['risk_level']],$BaseInfocellTextStyle);
                //                 if($value['risk_level'] == 1) {
                //                     $value['risk_level'] = 1;
                //                 }
                $checkTable->addCell(8000,$checkTableCellstyle2)->addImage($num_to_zh[$value['risk_level']],array('width'=>25, 'height'=>25,'align'=>'left'));
                if($value['risk_level'] != '1' && $value['suggestions'] && $value['suggestions'] != "N/A"){
                    $checkTable->addRow();
                    $checkTable->addCell(2000,$checkTableCellstyle)->addText('修复建议',$BaseInfocellTextStyle,$center);
                    
                    $suggestions = str_replace(array("[.]","[img]","[/img]"),array("\"","<img",">"),str_replace(array("\r","\n","\r\n"),"\\n",html_entity_decode($value['suggestions'])));
                    preg_match_all('/<img src=\"\/mare\/\.\/Uploads\/([^>]+)\" width=\"\d+px\"[\/]*>/', $suggestions, $su);
                    $checkTableCellstyle6=array('gridSpan' => 4,'valign' => 'center');
                    $suggestions_t = $checkTable->addCell(8000,$checkTableCellstyle6);
                    if ($su[0] && $su[1]) {
                        for($j=0;$j<count($su[0]);$j++){
                            $stru=explode($su[0][$j], $suggestions);
                            $suggestions_t->addText(trim($stru[0],'\n\n'),null,$BaseInfocellTextStyle);
                            $suggestions_t->addImage(UPLOAD_PATH.$su[1][$j],array('width'=>200, 'height'=>300));
                            $suggestions=$stru[1];
                            if($j ==count($su[0])-1){
                                $suggestions_t->addText($stru[1],null,$BaseInfocellTextStyle);
                            }
                        }
                    }else {
                        $suggestions_t->addText(str_replace(array("\r","\n","\r\n"),"\\n", $suggestions), $BaseInfocellTextStyle);
                    }
                    
                    //                     $checkTable->addCell(8000,$checkTableCellstyle2)->addText(str_replace(array("\r","\n","\r\n"),"\\n",html_entity_decode($value['suggestions'])),$BaseInfocellTextStyle);
                } else {
                    $checkTable->addRow();
                    $checkTable->addCell(2000,$checkTableCellstyle)->addText('修复建议',$BaseInfocellTextStyle,$center);
                    $suggestions_t = $checkTable->addCell(8000,$checkTableCellstyle6);
                    $suggestions_t->addText('无', null, array('align'=>'left','font-size'=>11));
                }
            }
        }
        
        // Save File
        $fileName = $info['realname']==null?$info['task_name']:$info['realname'];
        
        $test_type = get_task_type($info['tasktype']);
        $fileName = $fileName.'_'.$test_type.'_通报报告_'.date('Y_m_d',time());
        
        $u=__ROOT__."/Uploads/word/word.docx";
        $url='./Uploads/word/'.$fileName;
        
        
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control:must-revalidate， post-check=0， pre-check=0');
        header('Content-Type:application/force-download');
        header('Content-Type:application/vnd.ms-word');
        header('Content-Type:application/octet-stream');
        header('Content-Type:application/download');
        header('Content-Disposition:attachment;filename='.$fileName.'.docx');
        header('Content-Transfer-Encoding:binary');
        $objWriter = \PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
        $objWriter->save('php://output');  
    }
    
    public function proposalTeachReoprt() {
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
        $apptoken = I('get.apptoken');
        $serverity = I('get.serverity');
        $model                  = D("Appinfo");
        // $vulinfo                = D("Vulinfo");
        $analysisResults        = D('AnalysisResults');
        $appid                  = $model->where(array('apptoken'=>$apptoken))->find()['appid'];
        $info                   = $model->where(array('appid'=>$appid))->find();
        
        if ($info['internet_security_level']) {
            $Internet_security_level = get_security_level($info['internet_security_level']);;
            $where['standard'] = ['like',"%".C('SECURITY_LEVEL_LIKE').$Internet_security_level."%"];
        }
        
        if($serverity){
            $serverity = explode('_', $serverity);
            if(in_array('4', $serverity)){
                $histogram['0']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
            }else{
                $histogram['0']     = 0;
            }
            
            if(in_array('3', $serverity)){
                $histogram['1']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 3))->sum('issues_count');
            }else{
                $histogram['1']     = 0;
            }
            
            if(in_array('2', $serverity)){
                $histogram['2']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 2))->sum('issues_count');
            }else{
                $histogram['2']     = 0;
            }
            $info['bugs']               = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level'=>array('in',$serverity)))->sum('issues_count');
            // var_export(array('appid'=>$appid,'risk_level'=>array('in',$serverity)));die;
            if(in_array('4', $serverity)){
                $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' => 4))->sum('issues_count');
            }
            if(!$info['gaowei']){
                $info['gaowei']         = 0;
            }
            //             $proposal_count = $analysisResults->where($where)->where(['appid'=>$appid, 'risk_level' => ['in',$serverity]])->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->count();
            $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('in',$serverity)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,is_proposal ASC,issues_count DESC')->select();
            $detecresultinfo = get_detection_arr($detecresultinfo, $appid,$serverity);
            
        }else{
            $info['bugs']           = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2) ))->sum('issues_count');
            $info['gaowei']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>4 ))->sum('issues_count');
            $detecresultinfo        = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>array('egt',2)))->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->order('risk_level DESC,is_proposal ASC,issues_count DESC')->select();
            //             $proposal_count = $analysisResults->where($where)->where(['appid'=>$appid, 'risk_level' => ['egt',2]])->join('left join at_exploit_db on at_exploit_db.id = at_analysis_results.detectionid')->count();
            //高危漏洞类型数量
            if(!$info['gaowei']){
                $info['gaowei']         = 0;
            }
            $histogram['0']         = $info['gaowei'];
            //中危漏洞类型数量
            $histogram['1']         = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>3 ))->sum('issues_count');
            // var_export($histogram['mid']);die;
            //低危漏洞类型数量
            $histogram['2']             = $analysisResults->where($where)->where(array('appid'=>$appid,'risk_level' =>2 ))->sum('issues_count');
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
                $value_i['standard'] = get_vul_result($value_i['standard'], 'world', $Internet_security_level);
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
        
        $html .= '<h1>应用安全检测通报报告</h1>
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
            $html .='
                                    <tr>
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
            $html .='<img src="'.__ROOT__."/".UPLOAD_PATH . "temp/" . $info['realname']."bar.png".'" style="width:50%;height:50%;"/><img src="'.__ROOT__."/".UPLOAD_PATH . "temp/" . $info['realname']."pie.png".'" style="width:50%;height:50%;"/>';
            if ($info['internet_security_level']) {
                $title_str = get_security_level_title($info['internet_security_level'], $pass_count, $no_pass_count);
            }
            if ($info['internet_security_level']) {
                $html .='<div class="report-section">
                            <h2 class="main-title">二、检测概述</h2>
							<div class="clearfix">&nbsp;</div>
                            <div style="margin-bottom:10px;">'.$title_str.'</div>
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
                        $html .= 'safe" style="font-size:3px;';
                    }
                    $html .= '">'.$num_to_zh[$value['risk_level']].'</span></td>
                                </tr>';
                    $html2 .= '<div class="sec-wrapper">
                                <span class="badge"></span>
                                <h3 class="sec-title">'.($key+1).'、 '.$value['vulriskname'].'</h3>
                            </div>';
                    $detection_process = str_replace(array("[.]","[img]","[/img]","&lt;bbr&gt;",'&lt;bbr/&gt;'),array("\"","<img",">","<b>","</b>"),str_replace(array("\r","\r\n","\n","<!--","-->"),"<br/>", htmlspecialchars($value['detection_process'])));
                    $detection_process= preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/","",$detection_process);
                    $suggestions = str_replace(array("[.]","[img]","[/img]"),array("\"","<img",">"),str_replace(array("\r","\r\n","\n"),"<br/>",$value['suggestions']));
                    // if(!$detection_process){
                    // $detection_process = '空';
                    // }
                    if($detection_process == 'N/A') {
                        $detection_process = '无';
                    }
                    
                    if($suggestions== 'N/A') {
                        $suggestions= '无';
                    }
                    
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
                                    <td colspan="2">对标</td>
                                    <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE'))))).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2">风险描述</td>
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
                        }else {
                            $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td style="width:20%;">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;">对标</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE'))))).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
                            if($detection_process){
                                $html2 .='<tr>
		                        <td style="width:20%;">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
                            }
                        }
                        unset($inner_data);
                    } else {
                        $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td style="width:20%;">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;">对标</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",trim(mb_substr($value['standard'], mb_strlen(C('SECURITY_LEVEL_LIKE'))))).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
                        if($detection_process){
                            $html2 .='<tr>
		                        <td style="width:20%;">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
                        }
                    }
                    
                    $html2 .='
                            <tr>
                                <td colspan="2">风险系数</td>
                                <td colspan="6">'.$num_to_zh[$value['risk_level']].'</td>
                            </tr>
                            <tr>
                                <td colspan="2">修复建议</td>
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
                                        <td>是否通报建议</td>
                                        <td>风险数</td>
                                        <td>危险系数</td>
                                    </tr>';
                
                $html2 .= '<div class="report-section">
                                <h2 class="main-title">三、详细检测结果</h2>
                                <div class="clearfix">&nbsp;</div>';
                
                
                foreach ($detecresultinfo as $key => $value) {
                    
                    $html .='<tr>
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
                        $html .= 'safe" style="font-size:3px;';
                    }
                    $html .= '">'.$num_to_zh[$value['risk_level']].'</span></td>
                                </tr>';
                    
                    $html2 .= '<div class="sec-wrapper">
                                <span class="badge"></span>
                                <h3 class="sec-title">'.($key+1).'、 '.$value['vulriskname'].'</h3>
                            </div>';
                    $detection_process = str_replace(array("[.]","[img]","[/img]","&lt;bbr&gt;",'&lt;bbr/&gt;'),array("\"","<img",">","<b>","</b>"),str_replace(array("\r","\r\n","\n","<!--","-->"),"<br/>", htmlspecialchars($value['detection_process'])));
                    $detection_process= preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/","",$detection_process);
                    $suggestions = str_replace(array("[.]","[img]","[/img]"),array("\"","<img",">"),str_replace(array("\r","\r\n","\n"),"<br/>",$value['suggestions']));
                    // if(!$detection_process){
                    // $detection_process = '空';
                    // }
                    if($detection_process == 'N/A') {
                        $detection_process = '无';
                    }
                    
                    if($suggestions== 'N/A') {
                        $suggestions = '无';
                    }
                    if($value['detectionid'] == 44 || $value['detectionid'] == 142){
                        //                                     $arr=explode("相关内容：",$detection_process);
                        //                                     $temp=explode("####",$arr[1]);
                        //                                     $arr[1] = $temp[0];
                        //                                     $arr[1]=str_replace("<br/>", "", $arr[1]);
                        //                                     $arr[2]=str_replace("<br/><br/>", "<br/>", $temp[1]);
                        //                                     //                                 $arr[2] = $temp[1];
                        //                                     //                                 $inner_data=json_decode(htmlspecialchars_decode($arr[1]));
                        //                                     $inner_data=json_decode(htmlspecialchars_decode($arr[1]));
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
                                    <td colspan="2">风险编号</td>
                                    <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars($value['standard'])).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2">风险描述</td>
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
                        }else {
                            $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td colspan="2">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
                            <tr>
                                <td colspan="2">是否通报建议</td>
                                <td colspan="6">'.$this->is_proposal[$value['is_proposal']].'</td>
                            </tr>
		                    <tr>
		                        <td style="width:20%;">风险编号</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars($value['standard'])).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
                            if($detection_process){
                                $html2 .='<tr>
		                        <td style="width:20%;">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
                            }
                        }
                        unset($inner_data);
                    } else {
                        $html2 .='<table class="table">
		                	<tbody><tr>
		                        <td style="width:20%;">用例名称</td>
		                        <td colspan="6">'.$value['case_name'].'</td>
		                    </tr>
                            <tr>
                                <td style="width:20%;">是否通报建议</td>
                                <td colspan="6">'.$this->is_proposal[$value['is_proposal']].'</td>
                            </tr>
		                    <tr>
		                        <td style="width:20%;">风险编号</td>
		                        <td colspan="6">'.str_replace(array("\r","\r\n","\n"),"<br/>",htmlspecialchars($value['standard'])).'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:20%;">风险描述</td>
		                        <td colspan="6">'.$value['risk_description'].'</td>
		                    </tr>';
                        if($detection_process){
                            $html2 .='<tr>
		                        <td style="width:20%;">检测过程</td>
		                        <td colspan="6" style="word-wrap:break-word; word-break:normal;word-break:break-all;">'.$detection_process.'</td>
		                    </tr>';
                        }
                    }
                    
                    $html2 .='
                            <tr>
                                <td colspan="width:20%;">风险等级</td>
                                <td colspan="6">'.$num_to_zh[$value['risk_level']].'</td>
                            </tr>
                            <tr>
                                <td colspan="width:20%;">修复建议</td>
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
    private function get_vul_result($info,$type="world", $level = 8) {
        $arr_s =  explode(', ', $info);
        foreach ($arr_s as $key_s => $value_s) {
            if(strpos($value_s, "GA/T 1390.3 —2017 网络安全等级保护基本要求 $level") !== false) {
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

//     function array_sort($array,$row,$type){
//         $array_temp = array();
//         foreach($array as $k => $v){
//             $array_temp[$v[$row]] = $v;
//         }
        
//         if($type == 'asc'){
//             ksort($array_temp);
//         }elseif($type='desc'){
//             krsort($array_temp);
//         }else{
//         }
//         return $array_temp;
//     }
    
}
