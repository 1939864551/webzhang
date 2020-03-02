<?php
namespace Api\Controller;
use Think\Controller;
use Think\Model;

class ApptestController extends CommonController {
	//秘钥
	private $encodeaeskey='95cc7178e7bdfb0b';
	//日志文件路径
	private $logpath = '/Apktest/www/mare/Application/Api/Log/Apptest.log';

	//提交app检测
	public function apptest(){
		//实例化表
		$db_appinfo 	= D('appinfo');
		//$db_user 		= D('user');

		$data = $this->postData2Arr();

		//赋值
		$userkey 		= $data['userkey'];
		$appurl 		= $data['appurl'];
		$apptype 		= $data['apptype'];
		// echo $appurl;
		//判断参数中的url,应用类型apptype,应用md5是否为空
		if(!is_null($appurl)  && !is_null($userkey) && !is_null($apptype)){
			$code = get_url_headers($appurl)[0];
			//判断app url是否可达
			if(strpos($code,'200') !== false || strpos($code,'302') !== false){

				//url下载应用包
				if ($apptype==1) {
					$filename = createRand(32).'.apk';
					$type = 'apk';
					$tasktype = 'android';
				}else{
					$filename = createRand(32).'.ipa';
					$type = 'ipa';
					$tasktype = 'ios';
				}
				$filepath = './Uploads/App/'.date('Ymd').'/';
				$isdown = $this->downApkForUrl($appurl,$filepath,$filename);
			}else{
				$this->encodeReturn('206','url不可达');
			}
				
			//写入数据库
			if ($isdown) {
				$apptoken = createRand(25).time().createRand(25);
				$db_data['userid'] = $userkey;
				$db_data['task_name'] = time();
				$db_data['uploadtime']	= date('Y-m-d H:i:s');
				$db_data['status'] = 2;
				$db_data['apptoken'] = $apptoken;
				$db_data['type'] = $type;
				$db_data['tasktype'] = $tasktype;
				$db_data['apppath'] = $filepath.$filename;
				$appid = $db_appinfo->data($db_data)->add();
			}else{
				$this->encodeReturn('203','数据库写入失败');
			}
			//执行检测脚本
			if($appid !== false ){
				$parameter = 1;
				$file = getcwd().'/'.$filepath.$filename;
				$file = str_replace(array('|',';','&'), '', $file);
				if ($type=='ipa') {
					system("/Apktest/MobAppSecAss/apptest_ipa_info.sh   {$file} {$appid} {$userkey} {$parameter} >/dev/null &");
					system("/Apktest/MobAppSecAss/start_apptest 'ipastatic'   {$file} {$appid} {$userkey} {$parameter} 'localhost' >/dev/null &");
				}else{
					$ret = system("/Apktest/MobAppSecAss/apptest_apk_info.sh   {$file} {$appid} {$userkey} {$parameter} >/dev/null &");
					$ret2 = system("/Apktest/MobAppSecAss/start_apptest 'apkusb' {$file} {$appid} {$userkey} {$parameter} '' >/dev/null &");
				}
				
				$dataresult = array('code'=>'200','msg'=>'提交成功','appid'=>$appid);
				echo $this->encodeData($this->encodeaeskey,json_encode($dataresult));
				exit();
			}else{
				$dataresult  	= array('code'=>'20011','msg'=>$statusmsg."2");
				$appid 			= null;
				$requeststatus 	= 'false';
				$info 			= "应用检测:失败,不存在该应用";
			}
		}else{
			$this->encodeReturn('202','参数为空');
		}	
	}

	//返回响应数据
	private function returnresult($encodeaeskey,$userkey,$data){
		//返回的响应数据
		$responsemsg = json_encode($data,JSON_UNESCAPED_UNICODE);
		//给数据加密
		$encrypmsg = $this->aes_encrypt($encodeaeskey,$userkey,$responsemsg);

		return array('errcode'=>'0','errmsg'=>'','encrypmsg'=>$encrypmsg,'signature'=>md5($encrypmsg));

	}

	//对应的app的检测状态--0811chc
	public function getstatus(){
		$appinfo 		= D('appinfo');

		$data = $this->postData2Arr();

		$userkey 		= $data['userkey'];
		$appid 			= $data['appid']; 

		//获取appinfo信息
		$list = $appinfo->field('status')->where(array('appid'=>$appid,'userid'=>$userkey))->find();
		if ($list) {
			$appteststatus = M('maintain')->where(array('key'=>'appteststatus2'))->getfield('value');
			$status_arr = json_decode($appteststatus,true);
			$statusmsg = $status_arr[$list['status']];
			$result = array('code'=>200,'msg'=>'返回检测状态成功','status'=>$list['status'],'statusmsg'=>$statusmsg);
			echo $this->encodeData($this->encodeaeskey,json_encode($result));
			exit();
		}else{
			$this->encodeReturn('206','未找到任务');
		}
	}

	//下载报告地址
	public function getreport(){
		//实例化表
		$apiuser 		= D('ApiUser');
		$appinfo 		= D('Appinfo');
		$appstatus 		= D('Appstatus');
		// $teststatus 	= D('Teststatus');

		$requesturl    	= __ROOT__."/Apptest/getreport";
		//请求数据
		$data 			= json_decode($this->_replymsg,true);
		$userkey 		= $data['userkey'];
		$appid 			= $data['appid'];
		$encodeaeskey 	= $data['encodeaeskey'];
		if($userkey != null){
			if($appid == null){
				//不存在用户
				$responsemsg = array(
					'code' 	 	=> '2001',
					'msg'		=> '数据错误',
				);
				$requeststatus 		= 'false';
				$info 				= "获取报告地址:失败,不存在该应用";
			}else{
				$where 		= array('at_api_user.userkey'=>$userkey,'at_appinfo.appid'=>$appid);
				$goalapp 	= $appinfo->field('at_appinfo.userid as userid,at_appinfo.appid as appid,at_api_user.userkey as userkey,at_api_user.encodeaeskey,at_appinfo.status')->join('LEFT JOIN at_api_user ON at_api_user.userid = at_appinfo.userid')->where($where)->select()[0];
				if($goalapp == null){
					//无对应的应用信息
					$responsemsg = array(
						'code' 	 	=> '2001',
						'msg'		=> '数据错误'
					);
					$requeststatus 		= 'false';
					$info 				= "获取报告地址:失败,应用不属于该用户";
				}else{
					if($goalapp['status'] == '0'){
						//检测未完成
						$responsemsg =array(
							'code'		=> '2003',
							'msg'		=> '未检测完成'
						);
						$requeststatus 		= 'false';
						$info 				= "获取报告地址:失败,应用未检测完成";
					}elseif($goalapp['status'] == '1'){
						$reportkey 		= D('Reportkey');
						$keyval 		= createRand(32);
						$reportkeyid 	= $reportkey->data(array('reportkey'=>$keyval))->add();
						if($reportkeyid !== false){
							// $reporturl 		= 'http://'.C("FINANCIAL_INDUSTRY").__ROOT__.'/Report/down_report/appid/'.$appid.'/key/'.$keyval;
							//$reporturl 		= 'http://'.$_SERVER["HTTP_HOST"].__ROOT__.'/Report/down_pdf_report/appid/'.$appid.'/type/pdf'.'/classify/technology'.'/key/'.$keyval;
							$reporturl 		= 'http://'.$_SERVER["HTTP_HOST"].__ROOT__.'/Report/down_pdf_report/appid/'.$appid.'/key/'.$keyval;
							//检测完成
							$responsemsg 	= array(
								'code'		=> '0000',
								'msg'		=> '',
								'reporturl'	=> $reporturl
							);
						}
						$requeststatus 		= 'true';
						$info 				= "获取报告地址:成功";
					}
				}				
			}
		}else{
			//无获取的userkey
			$responsemsg = array(
				'code' 	 	=> '2001',
				'msg'		=> '数据错误'
			);
			$requeststatus 		= 'false';
			$info 				= "获取报告地址:失败,没有获取到用户的USEKEY";
		}
		$this->ajaxReturn($this->returnresult($encodeaeskey,$userkey,$responsemsg));
	}

	//返回检测的json数据
	public function getreportdata(){
		//实例化表
		$ab_appinfo 			= D('Appinfo');
		$analysis_results   = D('analysis_results');

		//请求数据
		$data = $this->postData2Arr();

		$userkey 		= $data['userkey'];
		$appid 			= $data['appid']; 

		$where['appid']		= array('eq',$appid);
		$list = $ab_appinfo->where(array('appid'=>$appid,'userid'=>$userkey))->find();
		if ($list) {
			//获取应用基础信息
			$appinfo      	= $ab_appinfo->where($where)->field("appid,package,size,type,realname,version,cert,secscore,md5,sha1,sha256")->find();

			//获取统计中高低危漏洞数量
			$bug_statistics = $analysis_results->where($where)->field('risk_level,sum(issues_count) as number')->group('risk_level')->select();
			$low = $bug_statistics[1]['number'];
			$mid = $bug_statistics[2]['number'];
			$high = $bug_statistics[3]['number'];
			$bugstatistics = array('low'=>$low,'mid'=>$mid,'high'=>$high);

			//获取检测应用分类及分类的中高低漏洞
			$bugtype = $analysis_results->where($where)->field('hvtypeid,risk_level,sum(issues_count) as number')->group('hvtypeid,risk_level')->select();

			//获取应用漏洞详情
			$buginfos = $analysis_results->field('case_name,hvtypeid,standard,risk_level,risk_description,detection_method,detection_process,suggestions,issues_count')->where($where)->select();

			$result['code'] = 200; 
			$result['msg'] = '获取报告数据成功';
			$result['appinfo'] = $appinfo;
			$result['bugstatistics'] = $bugstatistics;
			$result['bugtype'] = $bugtype;
			$result['buginfos'] = $buginfos;
			echo $this->encodeData($this->encodeaeskey,json_encode($result));
			exit();
		}else{
			$this->encodeReturn('206','未找到任务');
		}
	}

	/**
	 * 接受请求数据解密转化为数组
	 * by chenhaocheng	
	 * @return string 明文数组
	 */
	private function postData2Arr(){
		//echo __FUNCTIONNAME__;
		$post_data = file_get_contents("php://input");

		if (empty($post_data)) {
			$this->encodeReturn('201','数据为空');
		}

		//数据解密
		$decode_data = $this->decodeData($this->encodeaeskey,$post_data);
		$decode_data = rtrim(rtrim($decode_data), "\x00..\x1F");

		//json数据转化为数组
		$data 	= json_decode($decode_data,true);

		if (!is_array($data)) {
			$this->encodeReturn('205','解密失败');
		}
		return $data;
	}

	/**
	 * 数据解密方法
	 * by chenhaocheng	
	 * @param string $encodeaeskey 秘钥
	 * @param string $str 解密目标数据
	 * @return string 明文数据
	 */
	private function decodeData($encodeaeskey,$str){
		$dedata = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $encodeaeskey, base64_decode($str), MCRYPT_MODE_CBC,$encodeaeskey);
		$dedata = preg_replace("/[\x01-\x1f\x7f]/iu",'',$dedata);
		return $dedata;
	}

	/**
	 * 数据加密方法
	 * by chenhaocheng	
	 * @param string $encodeaeskey 秘钥
	 * @param string $str 加密目标数据
	 * @return string 加密数据
	 */
	private function encodeData($encodeaeskey,$str){
		$dedata = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $encodeaeskey, $str, MCRYPT_MODE_CBC,$encodeaeskey));
		//$dedata = preg_replace("/[\x01-\x1f\x7f]/iu",'',$dedata);
		return $dedata;
	}

	/**
	 * 接口响应方法-加密
	 * by chenhaocheng	
	 * @param string $code 响应码
	 * @param string $msg 说明
	 * @return string 响应json的加密数据
	 */
	private function encodeReturn($code,$msg){
		$data = $this->encodeData($this->encodeaeskey,json_encode(array('code'=>$code,'msg'=>$msg)));
		echo $data;
		exit;
	}

	/**
	 * 应用下载接口
	 * by chenhaocheng	
	 * @param string $url 请求地址
	 * @param string $filepath 应用文件保存路径
	 * @param string $filename 应用文件保存名称
	 * @return boolean
	 */
	private function downApkForUrl($url,$filepath,$filename){
		if (!is_dir($filepath)) {
			mkdir($filepath);
		}
		$ch = curl_init();
     	$timeout = 60;
     	curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
	    $file_contents = curl_exec($ch);
	    curl_close($ch);                                                //使用curl $ch 为返回的文件流
	    if (!empty($file_contents)) {
	        file_put_contents($filepath.$filename, $file_contents);  //保存到本地的地址
	        return true;
	    }else{
	        return false;
	    }
	}

	 /**
     * API接口日志写入文件方法
     * by chenhaocheng   
     * @param string $log_filepath 日志文件地址
     * @param string $log_str 日志数据
     * @return 
     */
	private function apptestLog($log_str){
		$myfile = fopen($this->logpath, "a");
		fwrite($myfile, date("Y-m-d H:i:s").' : '.$log_str."\n");
		fclose($myfile);
	}

	public function _empty(){
		exit;
	}
	
}