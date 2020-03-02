<?php
namespace Mare\Controller;
use Think\Controller;

class SettingController extends CommonController {
    public function __construct(){
        parent::__construct();
        
        $action_arr = [
            'set_client_rule',
            'add_client_rule',
            'get_client_rule',
            'update_client_rule',
            'stop_client_rule',
            'delete_client_rule'
        ];
        
        if (in_array(ACTION_NAME, $action_arr) && $this->tid != 5) {
            $this->redirect('Mare/Index/index');
        }
    }
	//设置系统配置
	public function set_index(){
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
		$url            			= MODULE_NAME.'/'.CONTROLLER_NAME.'/set_index';
		//超出次数限制
		$maintain 					= D('Maintain');
		$tid 						= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "4" || $tid == '5'){
			$handlemethod				= json_decode($maintain->where(array('key'=>'handlemethod'))->find()['value'],1);

			// $wifiinfo = $this->wificomdata();

			$file = file_get_contents('/Apktest/interfaces/wlan0');

			$preg = '/netmask [\d\.]+/i';
			preg_match($preg, $file, $netmask);
			$netmask = explode(' ', $netmask[0]);


			$preg = "/address [\d\.]+/i";
			preg_match($preg, $file, $address);
			$address = explode(' ', $address[0]);

			$preg = "/gateway [\d\.]+/i";
			preg_match($preg, $file, $gateway);
			$gateway = explode(' ', $gateway[0]);
			// var_export(array('netmask'=>$netmask,'address'=>$address,'gateway'=>$gateway));die;

			$resolv = file_get_contents('/etc/resolv.conf');

			$preg = '/nameserver [\d\.]+/i';
			preg_match_all($preg, $resolv,$tmp);
			foreach ($tmp[0] as $key => $value) {
				preg_match($preg, $value, $tmp1[$key]);
				$nameserver[$key] = explode(' ', $tmp1[$key][0])[1];
			}


			//获取当前连接的网卡
			@$currentconnect  = file_get_contents("/Apktest/wifi/currentconnect");

			$currentnet 		= explode("\n", $currentconnect)[0];		
			$currentgateway 	= explode("\n", $currentconnect)[1];
			$isauto = explode("\n", $currentconnect)[2];
			$user 			= D("User");
			$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
	        //@$personinfo    = $user->where(array('userid'=>$userid))->find();
			// @addMareLog(array(
				// 'username'      =>$personinfo['realname'],
	            // 'handleurl'     =>$url, 
	            // 'handlecontent' =>'查看网络配置',
	            // 'handleresult'  =>'成功',
	            // 'handleremarks' =>'用户查看网络配置成功'
	        // ));
			$licinfo 	= D("Licinfo");
			$systeminfo = $licinfo->field('wifi_online')->where(array('id'=>1))->find();

			$this->assign('systeminfo',$systeminfo);
			$this->assign('isauto',$isauto);
			$this->assign('currentnet',$currentnet);
			$this->assign('currentgateway',$currentgateway);
			$this->assign('netmask',$netmask);
			$this->assign('address',$address);
			$this->assign('gateway',$gateway);
			$this->assign('nameserver',$nameserver);
			$this->assign('wifiinfo',$wifiinfo);
			$this->assign('handlemethod',$handlemethod);
			$this->display();
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//返回WiFi列表
	public function wificomdata(){
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
		// $wifilist 		= shell_exec('/usr/bin/python /Apktest/autoTest/wifi.py');
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "4" || $tid == '5'){
			$wifilist 		= file_get_contents('/Apktest/wifi/wifilist_scan');//这里最好加一个文件是否存在的判断，不存在则返回XXX错误提示
			$sourcewifi 	=  explode("\n", trim($wifilist,","));
			array_pop($sourcewifi);
			$wifiinfolist	= $wifiinfo = array();
			$key_arr=array('SSID','SIGNAL','SECURITY');
			foreach ($sourcewifi as $key => $value) {
					$wifi = explode(',', $value);
					$wifiinfolist[0] = trim($wifi[0]);unset($wifi[0]);
					$wifiinfolist[1] = trim($wifi[1]);unset($wifi[1]);
					$wifiinfolist[2] = trim(implode(",", $wifi),',');	
					$wifiinfo[]=array_combine($key_arr,$wifiinfolist);
					unset($wifi);
			}
			//dump($wifiinfo);
			return $wifiinfo;		
		}else{
			$this->redirect('Mare/Index/index');
		}		
	}

	//判断是否存在联网的文件--这个是自己写入的 wifi 账号和密码
	/*public function isConnectNetExistFile(){
		// $fileerror = '/Apktest/wifi/connecterror';//连接错误文件路径
		$filesuccess 	= '/Apktest/wifi/connectsuccess';
		$filestatic		= '/Apktest/wifi/currentstatic'; //联网的wifi信息
		//$currentwifi    = '/Apktest/wifi/wifi_checkstatus';
		if(is_file($currentwifi)){
			$currentstatic 	= str_replace(array("\r","\r\n","\n"), '', file_get_contents($filestatic));
			if($currentstatic['current_wifi_ssid'] && $currentstatic['current_gateway']){
			$content 	= file_get_contents($currentwifi);
			$wifissid 	= explode("\n",$content)[0];//Mars_lab--wifi名称
			if(!$wifissid){$this->ajaxReturn(array('code'=>'false','info'=>'没有获取到联网的wifi信息'));exit;}
			//iwconfig wlan0 |  grep ESSID | awk '{print $4}'|awk -F '"' '{print $2}'
			//$res = shell_exec('iwconfig |grep wlan0 | grep "ESSID" | grep "'.$wifissid.'" && echo ok || echo false');	
			$res = shell_exec('iwconfig |grep  wlan0 | grep "ESSID" | grep "'.$wifissid.'" && echo connected || echo disconnected');
			//如果连接WiFi正常 则返回ok			   无线网卡          
			//echo   $wifissid."<br>";
			//echo  $res;                    
			//$res = shell_exec('iwconfig |grep wlan0 | grep "ESSID" | grep "'.$wifissid.'" && echo ok || echo false');
			$status  = explode("\n",trim($res,"\n"))[1];

			if( $status == 'connected'){
				$this->ajaxReturn(array('code'=>'success','info'=>'连接成功','wifi_ssid'=>$wifissid,'current_gateway'=>$currentstatic['current_gateway']));
			}else{
				$this->ajaxReturn(array('code'=>'false','info'=>'连接失败'));
			}
			}else{
				$this->ajaxReturn(array('code'=>'false','info'=>'没有获取到联网的wifi信息'));
			}
	}*/

	//显示wifi的数据和信息
	public function wifilist(){
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
		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/wifilist';
		$user 			= D("User");
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "4" || $tid == '5'){
			$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
	        @$personinfo    = $user->where(array('userid'=>$userid))->find();
			// @addMareLog(array(
				// 'username'      =>$personinfo['realname'],
	            // 'handleurl'     =>$url, 
	            // 'handlecontent' =>'查看附近的wifi',
	            // 'handleresult'  =>'成功',
	            // 'handleremarks' =>'用户查看附近的wifi成功'
	        // ));
			$wifiinfo = $this->wificomdata();
			$this->ajaxReturn($wifiinfo);
		}else{
			$this->redirect('Mare/Index/index');
		}		
	}

	//获取当前连接WIFI状态
	public function wifi_status(){
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
		// $result = shell_exec("/bin/bash /Apktest/wifi/wifi_checkstatus");
		$wifistatus  = "/Apktest/wifi/wifi_checkstatus";
		$result = '';
		if($fpcurrentconnect = fopen($wifistatus,'r')){
				$result = fread($fpcurrentconnect,filesize($wifistatus));
				fclose($fpcurrentconnect);
		}
		if (empty($result)||ord($result)==10) {
			$this->ajaxReturn(array('code'=>'false','info'=>'连接失败'));
		}
		else{
			$this->ajaxReturn(array('code'=>'success','info'=>$result));
		}
	}

	//链接wifi
	public function connect_wifi_new(){
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
		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/connect_wifi';
		$user 			= D("User");
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "4" || $tid == '5'){
			$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
	        @$personinfo    = $user->where(array('userid'=>$userid))->find();
			@addMareLog(array(
				'username'      =>$personinfo['realname'],
	            'handleurl'     =>$url, 
	            'handlecontent' =>'连接WIFI操作',
	            'handleresult'  =>'成功',
	            'handleremarks' =>'用户连接WIFI操作成功'
	        ));
			$ssid = I('post.id');
			$pwd  = I('post.pwd');

			$ssid = htmlspecialchars_decode($ssid);
			$pwd = htmlspecialchars_decode($pwd);

			$char_arr = array('$','"');
			$ssid = $this->strforshell($ssid,$char_arr);
			$pwd = $this->strforshell($pwd,$char_arr);
			$ssid = '"'.$ssid.'"';
			$pwd = '"'.$pwd.'"';
			//$this->ajaxReturn(array('ssid'=>$ssid,'pwd'=>$pwd));

			//链接shell脚本 shell的路径有待商榷
			/*shell_exec("/bin/bash /Apktest/wifi/wifi_connect.sh disconnect");//断开连接
		    $res = shell_exec("/bin/bash /Apktest/wifi/wifi_connect.sh connect {$ssid} {$pwd} >/dev/null &");*/
			$killwpa = shell_exec("/Apktest/UKEY/start_shell \"killall wpa_supplicant\" ");
			$res = shell_exec("/Apktest/wifi/build_wpa_supplicant.sh {$ssid} {$pwd}");
			$res2 = shell_exec("/Apktest/UKEY/start_shell \"bash /Apktest/wifi/bootnetwork.configure\" ");
			
			//当前wifi连接的配置 
			$currentwifi  = "/Apktest/wifi/currentwifi";
			$currentconnecttext = "{$ssid}\n{$pwd}\n";
			if($fpcurrentconnect = fopen($currentwifi,'w+')){
				fwrite($fpcurrentconnect,$currentconnecttext);
				fclose($fpcurrentconnect);
			}
			
			$this->ajaxReturn($res);
		}else{
			$this->redirect('Mare/Index/index');
		}		
	}


	//设置网络 管理员和系统管理员
	public function set_network(){
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
		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/set_network';
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "4" || $tid == '5'){
			if(IS_POST){
				$address_getway_rule 		= "/^((25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)\.){3}(25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|[1-9])$/";
				$mask_rule 					= "/^(25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|[0-9])){3}$/";
				$user 			= D("User");
				$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		        @$personinfo    = $user->where(array('userid'=>$userid))->find();

				$ipv4address 				= I('post.ipv4address');
				$ipv4mask 					= I('post.ipv4mask');
				$ipv4gateway 				= I('post.ipv4gateway');
				$ipv4server1 				= I('post.ipv4server1');
				$cable_wifi 				= I('post.cable_wifi');
				$isauto 					= I('post.setting');
				//远程代码执行漏洞特殊字符过滤
				$ipv4gateway = str_replace(['|',';','&'], '', $ipv4gateway);
				if($cable_wifi){
					if($cable_wifi == 'wifi'){
						$wlan0_eth0 = 'wlan0';
					}else{
						$wlan0_eth0 = 'eth0';
					}
				}else{
					$this->redirect('Mare/Setting/set_index',array('tip'=>'no_choice_wlan0_eth0'));
				}

				if($isauto=='auto'){
					//写入配置interface
					$filepath = "/Apktest/interfaces/".$wlan0_eth0;
					// $filepath = "/var/www/html/mare/wlan0";
					$text = "#auto {$wlan0_eth0}\niface {$wlan0_eth0} inet dhcp";	

					if($fp = fopen($filepath,'w+')){
						fwrite($fp,$text);
						fclose($fp);
					}

				if($wlan0_eth0 == 'wlan0'){
					#先断开WiFi再链接
					shell_exec("/bin/bash /Apktest/wifi/wifi_connect.sh disconnect");
					//$mycom  = "/bin/bash /bin/eth0down.sh & /sbin/ifup wlan0";
					$mycom  = "/bin/bash /bin/eth0down.sh & /bin/wlan0up.sh";
					$bootnetwork  = "/Apktest/wifi/bootnetwork.configure";
					$bootnetworkcmd = "{$mycom}\n";
					if($fpbootnetwork = fopen($bootnetwork,'w+')){
						fwrite($fpbootnetwork,$bootnetworkcmd);
						fclose($fpbootnetwork);
					}

					}else{
						//$mycom  = "/bin/bash /bin/wlan0down.sh & /sbin/ifup eth0";
						$mycom  = "/bin/bash /bin/wlan0down.sh & /bin/eth0up.sh";
						$bootnetwork  = "/Apktest/wifi/bootnetwork.configure";
						$bootnetworkcmd = "{$mycom}\n";
						if($fpbootnetwork = fopen($bootnetwork,'w+')){
							fwrite($fpbootnetwork,$bootnetworkcmd);
							fclose($fpbootnetwork);
						}
					}

					//当前连接的配置
					$currentconnect  = "/Apktest/wifi/currentconnect";
					$currentconnecttext = "{$wlan0_eth0}\n{$ipv4gateway}\n{$isauto}\n";
					if($fpcurrentconnect = fopen($currentconnect,'w+')){
						fwrite($fpcurrentconnect,$currentconnecttext);
						fclose($fpcurrentconnect);
					}

					@addMareLog(array(
						'username'      =>$personinfo['realname'],
			            'handleurl'     =>$url, 
			            'handlecontent' =>'自动获取IP成功',
			            'handleresult'  =>'成功',
			            'handleremarks' =>'用户自动获取IP成功'
			        ));
					// var_export($mycom);die;
					//执行系统的网络设置修改
					system($mycom);
					//system("echo {$wlan0_eth0} >/Apktest/log/connectstats &");
					$this->redirect('Mare/Setting/set_index');
				}
				else{
				// $namesearch 				= I('post.namesearch');
				if(preg_match ( $address_getway_rule  ,  $ipv4address  )){
					if(preg_match ( $address_getway_rule  ,  $ipv4gateway  )){
						if(preg_match ( $address_getway_rule  ,  $ipv4server1  )){	
							if(preg_match ( $mask_rule  ,  $ipv4mask  )){	
								//写入配置interface
								$filepath = "/Apktest/interfaces/".$wlan0_eth0;
								// $filepath = "/var/www/html/mare/wlan0";
								$text = "#auto {$wlan0_eth0}\niface {$wlan0_eth0} inet static\nnetmask {$ipv4mask}\naddress {$ipv4address}\ngateway {$ipv4gateway}";	

								if($fp = fopen($filepath,'w+')){
									fwrite($fp,$text);
									fclose($fp);
								}
								//resolv.conf配置
								$resolv  = "/etc/resolv.conf";
								// $resolv  = "/var/www/html/mare/resolv.conf";
								$resolvtext = "# Dynamic resolv.conf(5) file for glibc resolver(3) generated by resolvconf(8)\n#     DO NOT EDIT THIS FILE BY HAND -- YOUR CHANGES WILL BE OVERWRITTEN\nnameserver {$ipv4server1}\n";
								if($fpresolv = fopen($resolv,'w+')){
									fwrite($fpresolv,$resolvtext);
									fclose($fpresolv);
								}

								//当前连接的配置
								$currentconnect  = "/Apktest/wifi/currentconnect";
								// $resolv  = "/var/www/html/mare/resolv.conf";
								$currentconnecttext = "{$wlan0_eth0}\n{$ipv4gateway}\n{$isauto}\n";
								if($fpcurrentconnect = fopen($currentconnect,'w+')){
									fwrite($fpcurrentconnect,$currentconnecttext);
									fclose($fpcurrentconnect);
								}

								
								if($wlan0_eth0 == 'wlan0'){
									#先断开WiFi再链接
									shell_exec("/bin/bash /Apktest/wifi/wifi_connect.sh disconnect");
									$mycom  = "/bin/bash /bin/eth0down.sh &  /bin/bash /bin/wlan0up.sh {$ipv4gateway}";

									$bootnetwork  = "/Apktest/wifi/bootnetwork.configure";
									$bootnetworkcmd = "{$mycom}\n";
									if($fpbootnetwork = fopen($bootnetwork,'w+')){
										fwrite($fpbootnetwork,$bootnetworkcmd);
										fclose($fpbootnetwork);
									}

								}else{
									$mycom  = "/bin/bash /bin/wlan0down.sh & /bin/bash /bin/eth0up.sh  {$ipv4gateway}";

									$bootnetwork  = "/Apktest/wifi/bootnetwork.configure";
									$bootnetworkcmd = "{$mycom}\n";
									if($fpbootnetwork = fopen($bootnetwork,'w+')){
										fwrite($fpbootnetwork,$bootnetworkcmd);
										fclose($fpbootnetwork);
									}
								}
								@addMareLog(array(
									'username'      =>$personinfo['realname'],
						            'handleurl'     =>$url, 
						            'handlecontent' =>'修改IP地址',
						            'handleresult'  =>'成功',
						            'handleremarks' =>'用户修改IP地址成功'
						        ));
								// var_export($mycom);die;
								//执行系统的网络设置修改
								system($mycom);
								system("echo {$wlan0_eth0} >/Apktest/log/connectstats &");
								$this->redirect('Mare/Setting/set_index');
							}else{
								@addMareLog(array(
									'username'      =>$personinfo['realname'],
						            'handleurl'     =>$url, 
						            'handlecontent' =>'修改IP地址',
						            'handleresult'  =>'失败',
						            'handleremarks' =>'用户修改IP地址失败'
						        ));
								$this->redirect('Mare/Setting/set_index',array('tip'=>'ipv4maskerror'));
							}								
						}else{
							@addMareLog(array(
								'username'      =>$personinfo['realname'],
					            'handleurl'     =>$url, 
					            'handlecontent' =>'修改IP地址',
					            'handleresult'  =>'失败',
					            'handleremarks' =>'用户修改IP地址失败'
					        ));
							$this->redirect('Mare/Setting/set_index',array('tip'=>'ipv4server1error'));
						}				
					}else{
						@addMareLog(array(
							'username'      =>$personinfo['realname'],
				            'handleurl'     =>$url, 
				            'handlecontent' =>'修改IP地址',
				            'handleresult'  =>'失败',
				            'handleremarks' =>'用户修改IP地址失败'
				        ));
						$this->redirect('Mare/Setting/set_index',array('tip'=>'ipv4gatewayerror'));
					}
				}else{
					@addMareLog(array(
						'username'      =>$personinfo['realname'],
			            'handleurl'     =>$url, 
			            'handlecontent' =>'修改IP地址',
			            'handleresult'  =>'失败',
			            'handleremarks' =>'用户修改IP地址失败'
			        ));
					$this->redirect('Mare/Setting/set_index',array('tip'=>'ipv4addresserror'));
				}

				}//自动获取IP
			}else{
				@addMareLog(array(
					'username'      =>$personinfo['realname'],
		            'handleurl'     =>$url, 
		            'handlecontent' =>'修改IP地址',
		            'handleresult'  =>'失败',
		            'handleremarks' =>'用户修改IP地址失败'
		        ));
				$this->redirect('Mare/Index/index');
			}
		}else{
			$this->redirect('Mare/Index/index');
		}		
	}

	//设置登录失败 管理员和系统管理员
	public function set_logfalse(){
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
		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/set_logfalse';
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "4" || $tid == '5'){
			if(IS_POST){
				$user 			= D("User");
				$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
		        @$personinfo    = $user->where(array('userid'=>$userid))->find();
				//失败的次数
				$retrynum 		= I('post.retrynum');
				//处理的方法
				$handle   		= I('post.handle');
				//限制时间
				$locktime 		= I('post.locktime');

				//判断是否都为整数
				if(is_numeric($retrynum) && is_numeric($handle) && is_numeric($locktime)){
					$maintain 		= D("Maintain");
					//登录错误处理的key值
					$loginerrorhandle 	= 'loginerrorhandle';
					$handlemethodkey	= 'handlemethod';
					//先找出处理方法的翻译
					$handleactioncontent= json_decode($maintain->where(array('key'=>$handlemethodkey))->find()['value'],1);
					$handleaction = $handleactioncontent[$handle-1]['name'];
					$value 	= json_encode(array('handleaction'=>$handleaction,'errorcount'=>$retrynum,'timelength'=>$locktime));
					$res = $maintain->where(array('key'=>$loginerrorhandle))->data(array('value'=>$value))->save();
					if($res !== false){

			            // @addMareLog(array(
			            //     'userid' => $_SESSION[$_SESSION['randomstr']]['userid'],
			            //     'username' => $_SESSION[$_SESSION['randomstr']]['nickname'],
			            //     'handlecontent' => '设置登录失败，' . $url
			            // ));
			            @addMareLog(array(
							'username'      =>$personinfo['realname'],
				            'handleurl'     =>$url, 
				            'handlecontent' =>'获取网路配置信息',
				            'handleresult'  =>'成功',
				            'handleremarks' =>'用户获取网路配置信息成功'
				        ));
						$this->redirect('Mare/Setting/set_index',array('tip'=>'logfalse_updata_success'));
					}else{
						$this->redirect('Mare/Setting/set_index',array('tip'=>'logfalse_updata_false'));
					}
				}else{
					var_export(I('post.'));die;
					$this->redirect('Mare/Setting/set_index',array('tip'=>'logfalse_not_int'));
				}
			}else{
				$this->redirect('Mare/Index/index');
			}
		}else{
			$this->redirect('Mare/Index/index');
		}	
	}

	//获取网络信息配置
	public function requestNetInfo(){
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
		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/requestNetInfo';
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "4" || $tid == '5'){
			$wifi_cable 	= I('get.wifi_cable');
			if($wifi_cable == 'wifi'){
				$wlan0_eth0 = 'wlan0';
			}else{
				$wlan0_eth0 = 'eth0';
			}
			$file = file_get_contents('/Apktest/interfaces/'.$wlan0_eth0);

			$preg = '/netmask [\d\.]+/i';
			preg_match($preg, $file, $netmask);
			$netmask = explode(' ', $netmask[0]);


			$preg = "/address [\d\.]+/i";
			preg_match($preg, $file, $address);
			$address = explode(' ', $address[0]);

			$preg = "/gateway [\d\.]+/i";
			preg_match($preg, $file, $gateway);
			$gateway = explode(' ', $gateway[0]);
			// var_export(array('netmask'=>$netmask,'address'=>$address,'gateway'=>$gateway));die;

			$resolv = file_get_contents('/etc/resolv.conf');

			$preg = '/nameserver [\d\.]+/i';
			preg_match_all($preg, $resolv,$tmp);
			foreach ($tmp[0] as $key => $value) {
				preg_match($preg, $value, $tmp1[$key]);
				$nameserver[$key] = explode(' ', $tmp1[$key][0])[1];
			}
			$user 			= D("User");
			$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
	        @$personinfo    = $user->where(array('userid'=>$userid))->find();
			@addMareLog(array(
				'username'      =>$personinfo['realname'],
	            'handleurl'     =>$url, 
	            'handlecontent' =>'获取网路配置信息',
	            'handleresult'  =>'成功',
	            'handleremarks' =>'用户获取网路配置信息成功'
	        ));
	        if($wifi_cable == 'wifi' && file_exists('/Apktest/wifi/currenterror')){
	        	$this->ajaxReturn(array('code'=>'false','address'=>$address[1],'netmask'=>$netmask[1],'gateway'=>$gateway[1],'nameserver'=>$nameserver));
			}elseif($wifi_cable == 'wifi' && file_exists('/Apktest/wifi/currentstatic')){
				$currentstatic  = "/Apktest/wifi/currentstatic";
				$wifi_text =  json_decode(str_replace(array("\r","\r\n","\n"), ' ', file_get_contents($currentstatic)),1);
				
				if($wifi_text['current_wifi_ssid']){
					$this->ajaxReturn(array('code'=>'success','address'=>$address[1],'netmask'=>$netmask[1],'gateway'=>$gateway[1],'nameserver'=>$nameserver,'wifi_ssid'=>$wifi_text['current_wifi_ssid']));
				}else{
					$this->ajaxReturn(array('code'=>'success','address'=>$address[1],'netmask'=>$netmask[1],'gateway'=>$gateway[1],'nameserver'=>$nameserver));
				}
			}else{
				$this->ajaxReturn(array('code'=>'success','address'=>$address[1],'netmask'=>$netmask[1],'gateway'=>$gateway[1],'nameserver'=>$nameserver));	
			}
		}else{
			$this->redirect('Mare/Index/index');
		}	
	}

	//添加系统内置扫描规则
	public function set_globalrule(){
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
		if($tid == "4" || $tid == '5'){
			$maintain 		= D('Maintain');
			$globalscanrule = D('Customrules');

			$page 				= ($page == null) ? 0 : (int)I('get.p');
            $countnum 			= 15;
            $star 			 	= (I('get.p',1)-1)*$countnum;
            $count 			 	= $globalscanrule->count();
  			$p	= new \Think\Page($count,$countnum);
	    	$p->lastSuffix 		=false;
            $p->setConfig('header','<br/><li class="rows">共<b>%TOTAL_ROW%</b>条记录  每页<b>'.$countnum.'</b>条  第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
            $p->setConfig('prev','上一页');
            $p->setConfig('last','末页');
            $p->setConfig('first','首页');
            $p->setConfig('next','下一页');
            $p->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    		$page = $p->show();	

			$rulelist 		= $globalscanrule->limit($p->firstRow.','.$p->listRows)->select();
			$ruletypelist 	= json_decode($maintain->where(array('key'=>'ruletype'))->find()['value'],1);
			$this->assign('ruletypelist',$ruletypelist);
			$this->assign('pageshow',$page);
			$this->assign('rulelist',$rulelist);
			$this->display();
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//停止扫描规则
	public function globalrule_stopOrUser(){
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
		if($tid == "4" || $tid == '5'){
			$globalscanrule 	= D('Customrules');
			$id 				= (int)I('get.id');
			$goalrule 			= $globalscanrule->where(array('id'=>$id))->find(); 
			if($goalrule){
				if($goalrule['status'] == 1){
					$enable = 2;
				}else{
					$enable = 1;
				}
				$res 				= $globalscanrule->where(array('id'=>$id))->data(array('status'=>$enable))->save();
				$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/globalrule_stopOrUser';
				$user 			= D("User");
				$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
	        	@$personinfo    = $user->where(array('userid'=>$userid))->find();
			
				if($res !== false){
					@addMareLog(array(
						'username'      =>$personinfo['realname'],
			            'handleurl'     =>$url, 
			            'handlecontent' =>'停用扫描规则',
			            'handleresult'  =>'成功',
			            'handleremarks' =>'用户停用扫描规则成功'
			        ));
					$this->ajaxReturn(array('code'=>'success','info'=>'操作成功'));
				}else{
					@addMareLog(array(
						'username'      =>$personinfo['realname'],
			            'handleurl'     =>$url, 
			            'handlecontent' =>'停用扫描规则',
			            'handleresult'  =>'失败',
			            'handleremarks' =>'用户停用扫描规则失败'
			        ));
					$this->ajaxReturn(array('code'=>'false','info'=>'操作失败'));
				}
			}else{
				$this->ajaxReturn(array('code'=>'false','info'=>'操作失败'));
			}
		}else{
			$this->ajaxReturn(array('code'=>'false','info'=>'操作失败'));
		}
	}
	//删除扫描规则
	public function globalrule_del(){
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
		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/globalrule_del';	
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "4" || $tid == '5'){
			$globalscanrule 	= D('Customrules');
			$id 				= (int)I('get.id');
			$res 				= $globalscanrule->where(array('id'=>$id))->delete(); 
			$user 			= D("User");
			$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
        	@$personinfo    = $user->where(array('userid'=>$userid))->find();
				
			if($res !== false){
				@addMareLog(array(
					'username'      =>$personinfo['realname'],
		            'handleurl'     =>$url, 
		            'handlecontent' =>'删除扫描规则',
		            'handleresult'  =>'成功',
		            'handleremarks' =>'用户删除扫描规则成功'
		        ));
				$this->ajaxReturn(array('code'=>'success','info'=>'操作成功'));
			}else{
				@addMareLog(array(
					'username'      =>$personinfo['realname'],
		            'handleurl'     =>$url, 
		            'handlecontent' =>'删除扫描规则',
		            'handleresult'  =>'失败',
		            'handleremarks' =>'用户删除扫描规则失败'
		        ));
				$this->ajaxReturn(array('code'=>'false','info'=>'操作失败'));
			}
		}else{
			$this->ajaxReturn(array('code'=>'false','info'=>'操作失败'));
		}
	}

	//添加扫描规则
	public function set_addNewRule(){
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
	    if($tid == "4" || $tid == '5'){
	        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/set_addNewRule';
	        $user 			= D("User");
	        $userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
	        @$personinfo    = $user->where(array('userid'=>$userid))->find();
	        
	        $globalscanrule 	= D("Customrules");
	        $rulesname 			= I('post.rulesname');
	        $ruletype 			= I('post.ruletype');
	        $rulesinfo 			= I('post.rule_content');
	        $rule_remarks 		= I('post.rule_remarks');
	        $status 			= 1;
	        
	        /*------------bug修复 qinxuening  2017/8/3---------------*/
	        $data 				= array(
	            'rulesname'		=> $rulesname,
	            'rulesinfo'		=> $rulesinfo,
	            'ruletype'		=> $ruletype,
	            'status'		=> $status,
	            'remarks'       => $rule_remarks
	        );
	        
	        if (!$globalscanrule->create($data)) {
	            $this->ajaxReturn(['code' => 'false', 'msg' => $globalscanrule->getError()]);
	        } else {
	            $res = $globalscanrule->data($data)->add();
	        }
	        
	        /*------------bug修复 qinxuening  2017/8/3---------------*/
	        if( $res != false ){
	            @addMareLog(array(
	                'username'      =>$personinfo['realname'],
	                'handleurl'     =>$url,
	                'handlecontent' =>'添加扫描规则',
	                'handleresult'  =>'成功',
	                'handleremarks' =>'用户添加扫描规则成功'
	            ));
	            $this->ajaxReturn(['code' => 'success', 'msg'=>'添加规则成功']);
	        }else{
	            @addMareLog(array(
	                'username'      =>$personinfo['realname'],
	                'handleurl'     =>$url,
	                'handlecontent' =>'添加扫描规则',
	                'handleresult'  =>'失败',
	                'handleremarks' =>'用户添加扫描规则失败'
	            ));
	            $this->ajaxReturn(array('code'=>'false','info'=>'操作失败'));
	        }
	    }else{
	        $this->redirect('Mare/Index/index');
	    }
	}

	//修改规则
	public function set_updateRuleInfo(){
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
	    if($tid == "4" || $tid == '5'){
	        $globalscanrule 	= D("Customrules");
	        $maintain 			= D('Maintain');
	        $id 				= (int)I('post.id');
	        
	        /*------------bug修复 qinxuening  2017/8/3---------------*/
	        $data 				= array(
	            'rulesname'		=> I('post.rulesname'),
	            'ruletype'		=> I('post.ruletype'),
	            'rulesinfo'		=> I('post.rule_content'),
	            'remarks'       => I('post.rule_remarks')
	        );
	        
	        if (!$globalscanrule->create($data)) {
	            $this->ajaxReturn(['code' => -1, 'status' => 'error', 'msg' => $globalscanrule->getError()]);
	        } else {
	            $res = $globalscanrule->where(['id' => $id])->save($data);
	        }
	        /*------------bug修复 qinxuening  2017/8/3---------------*/
	        
	        $url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/set_updateRuleInfo';
	        $user 			= D("User");
	        $userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
	        @$personinfo    = $user->where(array('userid'=>$userid))->find();
	        
	        if($res !== false){
	            @addMareLog(array(
	                'username'      =>$personinfo['realname'],
	                'handleurl'     =>$url,
	                'handlecontent' =>'修改扫描规则',
	                'handleresult'  =>'成功',
	                'handleremarks' =>'用户修改扫描规则成功'
	            ));
	            $this->ajaxReturn(array('code' => 1, 'status' => 'success','msg'=>'操作成功'));
	        }else{
	            @addMareLog(array(
	                'username'      =>$personinfo['realname'],
	                'handleurl'     =>$url,
	                'handlecontent' =>'修改扫描规则',
	                'handleresult'  =>'失败',
	                'handleremarks' =>'用户修改扫描规则失败'
	            ));
	            $this->ajaxReturn(array('code'=>-1, 'status' => 'false','info'=>'操作失败'));
	        }
	    }else{
	        $this->redirect('Mare/Index/index');
	    }
	}

	//查看扫描规则
	public function set_lookrule(){
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
		if($tid == "4" || $tid == '5'){
			$globalscanrule 	= D("Customrules");
			$maintain 			= D('Maintain');
			$id  				= (int)I('get.id');
			$info 				= $globalscanrule->where(array('id'=>$id))->find();
			if($info){
				$ruletype = json_decode($maintain->where(array('key'=>'ruletype'))->find()['value'],1);
				foreach ($ruletype as $key => $value) {
					if($value['key'] == $info['ruletype']){
						$info['zhruletype']  = $value['value'];
					}
				}
				$this->ajaxReturn(array('code'=>'success','info'=>$info));
			}else{
				$this->ajaxReturn(array('code'=>'false','info'=>'获取信息错误','id'=>$id));
			}
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//设置系统服务
	public function set_service(){
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
		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/set_service';
		$maintain 		= D("Maintain");
		$vuldb 			= D("Vuldb");
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "4" || $tid == '5'){
			$authorizationmessage = $vuldb->select();
			$user 			= D("User");
			$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
	        @$personinfo    = $user->where(array('userid'=>$userid))->find();
			@addMareLog(array(
				'username'      =>$personinfo['realname'],
	            'handleurl'     =>$url, 
	            'handlecontent' =>'查看系统升级页面',
	            'handleresult'  =>'成功',
	            'handleremarks' =>'用户查看系统升级页面成功'
	        ));
	        $licinfo 		= D('Licinfo')->field('product_ver')->where(array('id'=>1))->find();
	        $clientinfo 	= D('VersionUpgrade')->field('versioncode')->select();
	        $this->assign('licinfo',$licinfo);
	        $this->assign('client',$clientinfo);
	        $timeInfo 		= D('Licinfo')->where(array('id'=>1))->find();
	        $this->assign('timeinfo',$timeInfo);
	        $this->assign('authorizationmessage',$authorizationmessage);
			$this->display();
		}else{
			$this->redirect('Mare/Index/index');
		}	
	}

	//病毒库文件上传 和 lic文件上传
	public function fileUpload(){
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
		if($tid == "4" || $tid == '5'){
			$url            	= MODULE_NAME.'/'.CONTROLLER_NAME.'/fileUpload';
			$user 				= D("User");
			$userid         	= $_SESSION[$_SESSION['randomstr']]['userid'];
  			@$personinfo    	= $user->where(array('userid'=>$userid))->find();
			$upload = new \Think\Upload();// 实例化上传类 
			$upload->maxSize 	= 10240000000 ;// 设置附件上传大小 
			// $upload->exts 		= array('zip','gz','tar','db','sql');// 设置附件上传类型 
			$upload->savePath 	= ''; // 设置附件上传（子）目录
			$upload->subName 	= '';
			// $upload->saveName 	= '';
			if(I('get.updategoal') == 'licupgradefile'){
				$upload->rootPath 	= '/Apktest/license/'; // 设置附件lic文件上传根目录 
				$Updatefile 		= '/Apktest/license';
				if(!is_dir($Updatefile)){
					if(!mkdir($Updatefile)){
						mkdir($Updatefile);
					}
				}
				$handlecontent = "上传License更新文件成功";
			}elseif(I('get.updategoal') == 'clientupgradefile'){
				$upload->rootPath 	= './Uploads/'; // 设置附件病毒更新库上传根目录 
				$Updatefile 		= './Uploads';
				if(!is_dir($Updatefile)){
					if(!mkdir($Updatefile)){
						mkdir($Updatefile);
					}
				}
				$handlecontent = "上传客户端文件成功";
			}else{
				$upload->rootPath 	= '/Apktest/updatapkg/'; // 设置附件病毒更新库上传根目录 
				$Updatefile 		= '/Apktest/updatapkg';
				if(!is_dir($Updatefile)){
					if(!mkdir($Updatefile)){
						mkdir($Updatefile);
					}
				}
				$handlecontent = "上传病毒库更新文件成功";
			}
			
			$info 				= $upload->upload(); 
			if($info){
				foreach ($info as $key => $value) {
					@addMareLog(array(
						'username'      =>$personinfo['realname'],
			            'handleurl'     =>$url, 
			            'handlecontent' =>$handlecontent,
			            'handleresult'  =>'成功',
			            'handleremarks' =>'用户'.$handlecontent.'成功'
			        ));
					//$this->ajaxReturn(array('code'=>'success','info'=>$upload->rootPath.$value['savename'],'ext'=>$value['ext'],'sourcename'=>$value['name']));
					$_SESSION['clientupgradefile']=$upload->rootPath.$value['savename'];
					$this->ajaxReturn(array('code'=>'success','ext'=>$value['ext'],'sourcename'=>$value['name']));
				}
				
			}else{
				@addMareLog(array(
						'username'      =>$personinfo['realname'],
			            'handleurl'     =>$url, 
			            'handlecontent' =>'上传更新文件失败',
			            'handleresult'  =>'成功',
			            'handleremarks' =>'用户上传更新文件失败成功'
			        ));
				$this->ajaxReturn(array('code'=>'false','info'=>$upload->getError()));
			}
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	public function clientfileadd(){
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
		//$clientupgradefile  = I('post.clientupgradefile');
		$clientupgradefile  = $_SESSION['clientupgradefile'];
		$phone_type 		= I('post.phone_type');
		if($phone_type !== false){
			//判断是否是android或者iphone,0就是android，1就是iphone
			if($phone_type == '0'){
				$versionCode 		= getAndroidInfo($clientupgradefile);
			}else{
				$versionCode 		= getIpaInfo($clientupgradefile);
			}
			
			if($versionCode != false){
				$VersionUpgrade 	= D('VersionUpgrade');
				$isNotExistRecord	= $VersionUpgrade->where(array('phone_type'=>$phone_type))->find();
				if($isNotExistRecord){
					$res = $VersionUpgrade->data(array('download_url'=>$clientupgradefile,'version_name'=>$versionCode['version_name'],'versioncode'=>$versionCode['versioncode']))->where(array('version_id'=>$isNotExistRecord['version_id']))->save();
					$old_download_url  	= $isNotExistRecord['download_url'];
				}else{
					$res = $VersionUpgrade->data(array('download_url'=>$clientupgradefile,'version_name'=>$versionCode['version_name'],'versioncode'=>$versionCode['versioncode'],'phone_type'=>$phone_type))->add();
				}

				if($res !== false){
					unlink($old_download_url);
					$this->ajaxReturn(array('code'=>'success','info'=>'更新成功','versioncode'=>$versionCode));
				}else{
					$this->ajaxReturn(array('code'=>'false','info'=>'更新失败','versioncode'=>$versionCode));
				}
			}else{
				$this->ajaxReturn(array('code'=>'false','info'=>'更新失败'));
			}
		}
		$this->ajaxReturn(array('code'=>'false','info'=>'更新失败'));
	}

	//设置安全界面
	public function set_security(){
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
		if($tid == "4" || $tid == '5'){
			$url            	= MODULE_NAME.'/'.CONTROLLER_NAME.'/set_security';
			$user 				= D("User");
			$userid         	= $_SESSION[$_SESSION['randomstr']]['userid'];
  			@$personinfo    	= $user->where(array('userid'=>$userid))->find();
			$maintain  			= D("Maintain");
			$login_captcha 		= $maintain->where(array('key'=>'login_captcha'))->find()['value'];
			$login_limit 		= json_decode($maintain->where(array('key'=>'login_limit'))->find()['value'],true);
			$passwd_complexity  = $maintain->where(array('key'=>'passwd_complexity'))->find()['value'];
			$this->assign('login_captcha',$login_captcha);
			$this->assign('login_limit',$login_limit);
			$this->assign('passwd_complexity',$passwd_complexity);
			@addMareLog(array(
					'username'      =>$personinfo['realname'],
		            'handleurl'     =>$url, 
		            'handlecontent' =>'查看用户安全信息',
		            'handleresult'  =>'成功',
		            'handleremarks' =>'用户查看用户安全信息成功'
		        ));
			$this->display();
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	/**
	*	a.登录验证码（作用于用户登录页面）
	*		a)开启/关闭 
	* 	开启验证码 		login_captcha on
	* 	不开启验证码 	login_captcha off
	*	b.登录限制（作用于用户登录页面）
	*		a)开启/关闭
	*		b)单用户登录错误｛5｝次冻结账号10分钟  
	*	不开启登录限制 {"login_limit":"on"}
	*	开启登录限制 {"login_limit":"on","error_num":5,"login_time_limit":10}
	*	c.密码复杂度（作用于用户密码设置/修改页面）
	*		a)密码长度｛8｝(不能小于8位)
	*		b)密码组合:
	*			i.包含大小写字母 A-Za-z
	*			ii.包含字母+数字 A-Za-z0-9
	*			iii.字母+数字+特殊字符 A-Za-z0-9+特殊字符
	*/

	public function set_security_info(){
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
		if($tid == "4" || $tid == '5'){
			$maintain   	= D("Maintain");
			$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/set_security_info';
			$user 			= D("User");
			$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
  			@$personinfo    = $user->where(array('userid'=>$userid))->find();
			if(IS_POST){
				$captcha 		= (I('post.captcha') == 'off') ? 'off' : 'on';
				$loginlimit 	= (I('post.loginlimit') == 'on') ? 'on' : 'off';
				
				$loginlimitarr = array(
						'login_limit'		=>$loginlimit
						);
				if($loginlimit == 'on'){
					if(is_numeric(I('post.mimaerrornum')) && I('post.mimaerrornum') > 0){
						$mimaerrornum = I('post.mimaerrornum');
					}else{
						$mimaerrornum = 5;
					}
					if(is_numeric(I('post.limitlogintime')) && I('post.mimaerrornum') > 0){
						$limitlogintime = I('post.limitlogintime');
					}else{
						$limitlogintime = 10;
					}
					$loginlimitarr['error_num'] 		= $mimaerrornum;
					$loginlimitarr['login_time_limit']	= $limitlogintime;
				}
				$loginlimitarrjson = json_encode($loginlimitarr);
				$complexity 	= (is_numeric(I('post.complexity')) == true ) ? I('post.complexity') : 1;

				$maintain->startTrans();
				$captcha_res 		= $maintain->where(array('key'=>'login_captcha'))->data(array('value'=>$captcha))->save();
				$login_limit_res 	= $maintain->where(array('key'=>'login_limit'))->data(array('value'=>$loginlimitarrjson))->save();
				$complexity_res		= $maintain->where(array('key'=>'passwd_complexity'))->data(array('value'=>$complexity))->save();
				if($captcha_res  !== false && $login_limit_res !== false && $complexity_res !== false){
					$maintain->commit();
					@addMareLog(array(
						'username'      =>$personinfo['realname'],
			            'handleurl'     =>$url, 
			            'handlecontent' =>'修改用户安全信息',
			            'handleresult'  =>'成功',
			            'handleremarks' =>'用户修改用户安全信息成功'
			        ));
					$this->ajaxReturn(array('code'=>'success','info'=>'修改成功'));
				}else{
					$maintain->rollback();
					@addMareLog(array(
						'username'      =>$personinfo['realname'],
			            'handleurl'     =>$url, 
			            'handlecontent' =>'修改用户安全信息',
			            'handleresult'  =>'失败',
			            'handleremarks' =>'用户修改用户安全信息失败'
			        ));
					$this->ajaxReturn(array('code'=>'false','info'=>'修改失败'));
				}
			}
			$this->ajaxReturn(array('code'=>'false','info'=>'修改失败'));
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//执行更新命令
	public function dataUpgrade(){
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
		$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/dataUpgrade';
		if($tid == "4" || $tid == '5'){
			$user 			= D("User");
			$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
  			@$personinfo    = $user->where(array('userid'=>$userid))->find();
			
			$filepath  = $_SESSION['clientupgradefile'];
			if(is_file($filepath) && substr($filepath, 0,16) == '/Apktest/license'){
				$filecontent = str_replace(array("\r","\n","\r\n"), '', file_get_contents($filepath));
				shell_exec('/Apktest/UKEY/start_shell "/Apktest/UKEY/UKEY 3 '.$filecontent.'"');
				@addMareLog(array(
					'username'      =>$personinfo['realname'],
		            'handleurl'     =>$url, 
		            'handlecontent' =>'执行更新文件',
		            'handleresult'  =>'成功',
		            'handleremarks' =>'用户执行更新文件成功'
		        ));
				$this->ajaxReturn(array('code'=>'success','info'=>'后台正在升级请稍后!!!'));
			}elseif(is_file($filepath) && substr($filepath, 0, 18) == '/Apktest/updatapkg'){
				shell_exec('/Apktest/UKEY/start_shell "/Apktest/UKEY/UKEY 4 '.$filepath.'"');
				@addMareLog(array(
					'username'      =>$personinfo['realname'],
		            'handleurl'     =>$url, 
		            'handlecontent' =>'执行更新文件',
		            'handleresult'  =>'成功',
		            'handleremarks' =>'用户执行更新文件成功'
		        ));
				$this->ajaxReturn(array('code'=>'success','info'=>'后台正在升级请稍后!!!'));
			}else{
				@addMareLog(array(
					'username'      =>$personinfo['realname'],
		            'handleurl'     =>$url, 
		            'handlecontent' =>'执行更新文件',
		            'handleresult'  =>'失败',
		            'handleremarks' =>'用户执行更新文件失败'
		        ));
				$this->ajaxReturn(array('code'=>'false','info'=>'无更新文件,请重新上传!'));
			}
		}else{
			$this->ajaxReturn(array('code'=>'false','info'=>'无权限更新任务'));
		}
	}

	//测试ping文件
	public function pingtest(){
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
		$paramar 	= I('post.paramar');
		if(preg_match('/^[a-zA-Z0-9][\.a-zA-Z0-9]{3,25}$/', $paramar) || preg_match('/^[0-9][\.0-9]{0,15}$/', $paramar)){
			$res 	= shell_exec("ping -c 10 {$paramar}");
			$info 	= str_replace(array("\n","\r\n","\r"), "<br/>", $res);
			$this->ajaxReturn($info);
		}
	}

	//设置水印和那个页眉页脚
	public function set_watermark(){
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
		$expand 	= D('Expand');
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "4" || $tid == '5'){

			$page 				= ($page == null) ? 0 : (int)I('get.p');
            $countnum 			= 15;
            $star 			 	= (I('get.p',1)-1)*$countnum;
            $count 			 	= $expand->count();
  			$p					= new \Think\Page($count,$countnum);
	    	$p->lastSuffix 		=false;
            $p->setConfig('header','<br/><li class="rows">共<b>%TOTAL_ROW%</b>条记录  每页<b>'.$countnum.'</b>条  第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
            $p->setConfig('prev','上一页');
            $p->setConfig('last','末页');
            $p->setConfig('first','首页');
            $p->setConfig('next','下一页');
            $p->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    		$page 				= $p->show();	
			$expandlist 		= $expand->limit($p->firstRow.','.$p->listRows)->select();
			$this->assign('pageshow',$page);
			$this->assign('expandlist',$expandlist);
			$this->display();
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//设置水印和那个页眉页脚
	public function set_lookwatermark(){
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
		$expand 		= D('Expand');
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "4" || $tid == '5'){
			$id 				= (int)I('get.id');
			$expandinfo 		= $expand->where(array('id'=>$id))->find();
			$this->assign('expandinfo',$expandinfo);
			$this->display();
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//上传水印图片
	public function set_uploadwaterpic(){
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
		if($tid == "4" || $tid == '5'){
			$expand 			= D("Expand");
			$upload 			= new \Think\Upload();// 实例化上传类 
			$upload->maxSize 	= 5097152;
	        $upload->exts       = array('jpg', 'gif','jpeg','png');	
	        $upload->rootPath   = UPLOAD_PATH;
	        $upload->subName   	= "icon";
	        $upload->saveRule 	= 'uniqid';
	        $info 				= $upload->upload();
	        if($info !== false){
	        	$watermarkpic 	= UPLOAD_PATH.$info['watermark']['savepath'].$info['watermark']['savename'];
	        	if(is_file($watermarkpic)){
	        		$this->ajaxReturn(array('code'=>'success','info'=>$watermarkpic));
	        	}else{
	        		$this->ajaxReturn(array('code'=>'false','info'=>'上传图片失败','path'=>$watermarkpic,'file'=>$_FILES,'par'=>$upload->rootPath));
	        	}
	        }else{
	        	$this->ajaxReturn(array('code'=>'false','info'=>$upload->getError()));
	        }
		}else{
			$this->ajaxReturn(array('code'=>'false','info'=>'无权限上传图片'));
		}
	}
	//修改水印信息
	public function set_updatewatermarkinfo(){
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
		$expand  		= D('Expand');
		$tid 			= $_SESSION[$_SESSION['randomstr']]['tid'];
		if($tid == "4" || $tid == '5'){
			$watermark 		= I('post.watermarkpath');
			$header 		= I('post.header');
			$footer 		= I('post.footer');
			$remarks 		= I('post.remarks');
			$id 			= (int)I('get.id');
			$data 		 	= array(
				'header'	=> $header,
				'footer'	=> $footer 
				);
			if(is_file($watermark)){
				$data['watermark']	= $watermark;
			}
			if(!empty($remarks)){
				$data['remarks'] = $remarks;
			}
			$res  	= $expand->where(array('id'=>$id))->data($data)->save();
			$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/set_updatewatermarkinfo';
			$user 			= D("User");
			$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
        	@$personinfo    = $user->where(array('userid'=>$userid))->find();
			if($res !== false){
				@addMareLog(array(
						'username'      =>$personinfo['realname'],
			            'handleurl'     =>$url, 
			            'handlecontent' =>'修改水印信息',
			            'handleresult'  =>'成功',
			            'handleremarks' =>'用户修改水印信息成功'
			        ));
				$this->ajaxReturn(array('code'=>'success','info'=>'修改成功'));
			}
			@addMareLog(array(
				'username'      =>$personinfo['realname'],
	            'handleurl'     =>$url, 
	            'handlecontent' =>'修改水印信息',
	            'handleresult'  =>'失败',
	            'handleremarks' =>'用户修改水印信息失败'
	        ));
			$this->ajaxReturn(array('code'=>'false','info'=>'修改失败'));
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//停用水印
	public function set_watermarkStopOrUse(){
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
		if($tid == "4" || $tid == '5'){
			$Expand 			= D('Expand');
			$id 				= (int)I('get.id');
			$goalexpand 		= $Expand->where(array('id'=>$id))->find(); 
			if($goalexpand){
				if($goalexpand['status'] == 1){
					$enable = 2;
				}else{
					$enable = 1;
				}
				$sss = M()->execute('UPDATE at_expand SET status=2 WHERE status = 1');
				$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/set_watermarkStopOrUse';
				$user 			= D("User");
				$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
	        	@$personinfo    = $user->where(array('userid'=>$userid))->find();
			
				if($sss !== false){
					$res 				= $Expand->where(array('id'=>$id))->data(array('status'=>$enable))->save();
					if($res !== false){
						@addMareLog(array(
							'username'      =>$personinfo['realname'],
				            'handleurl'     =>$url, 
				            'handlecontent' =>'停用水印',
				            'handleresult'  =>'成功',
				            'handleremarks' =>'用户停用水印成功'
				        ));
						$this->ajaxReturn(array('code'=>'success','info'=>'操作成功'));
					}else{
						@addMareLog(array(
							'username'      =>$personinfo['realname'],
				            'handleurl'     =>$url, 
				            'handlecontent' =>'停用水印',
				            'handleresult'  =>'失败',
				            'handleremarks' =>'用户停用水印失败'
				        ));
						$this->ajaxReturn(array('code'=>'false','info'=>'操作失败'));
					}
				}else{
					$this->ajaxReturn(array('code'=>'false','info'=>'操作失败'));
				}				
			}else{
				$this->ajaxReturn(array('code'=>'false','info'=>'操作失败'));
			}
		}else{
			$this->ajaxReturn(array('code'=>'false','info'=>'操作失败'));
		}
	}
	//删除水印
	public function set_watermarkdel(){
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
		if($tid == "4" || $tid == '5'){
			$Expand 			= D('Expand');
			$id 				= (int)I('get.id');
			$res 				= $Expand->where(array('id'=>$id))->delete(); 
			$url            = MODULE_NAME.'/'.CONTROLLER_NAME.'/set_watermarkdel';
			$user 			= D("User");
			$userid         = $_SESSION[$_SESSION['randomstr']]['userid'];
        	@$personinfo    = $user->where(array('userid'=>$userid))->find();
			
			if($res !== false){
				@addMareLog(array(
					'username'      =>$personinfo['realname'],
		            'handleurl'     =>$url, 
		            'handlecontent' =>'删除水印',
		            'handleresult'  =>'成功',
		            'handleremarks' =>'用户删除水印成功'
		        ));
				$this->ajaxReturn(array('code'=>'success','info'=>'操作成功'));
			}else{
				@addMareLog(array(
					'username'      =>$personinfo['realname'],
		            'handleurl'     =>$url, 
		            'handlecontent' =>'删除水印',
		            'handleresult'  =>'失败',
		            'handleremarks' =>'用户删除水印失败'
		        ));
				$this->ajaxReturn(array('code'=>'false','info'=>'操作失败'));
			}
		}else{
			$this->ajaxReturn(array('code'=>'false','info'=>'操作失败'));
		}
	}

	public function test(){
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
		$array = array(
			array('key'=>'1','value'=>'文件名检测(安装包解压目录文件检索,包括资源文件、代码文件等)','available'=>'apk,ipa'),
			array('key'=>'2','value'=>'安装包特征查找(安装包解压文件内容查找)','available'=>'apk,ipa'),
			array('key'=>'3','value'=>'二进制文件的特征码检测(对class.dex特征码检测)','available'=>'apk'),
			array('key'=>'4','value'=>'代码文件的函数或关键字检测','available'=>'apk'),
			array('key'=>'5','value'=>'AndroidManifest.xml文件的关键字检测','available'=>'apk'),
			array('key'=>'10','value'=>'URL过滤黑名单')
			);
		$this->ajaxReturn($array,'',JSON_UNESCAPED_UNICODE);
	}

	public function test_chc(){
		// $s1 = I('get.s1');
/*		$s1 = addcslashes($s1,'$');
		$s1 = addcslashes($s1,'"');*/
		// $s1 = addcslashes($s1,"'");
		// //echo $s1;
		// $str='\$1 ';
		// $str1='chc';
		// $res = shell_exec("/bin/bash /Apktest/wifi/test_chc.sh {$s1} {$str1}>/dev/null &");
		echo shell_exec("wpa_passphrase");
	}

	//shell转义方法
	public function strforshell($str,$char_arr){
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
		foreach ($char_arr as $value) {
			$str = addcslashes($str, $value);
		}
		return $str;
	}

	//获取连接方式与IP接口
	public function get_ip(){
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
		$result = shell_exec("/bin/cat /Apktest/wifi/currentstatic2");
		$array = explode(',', $result);
		if ($array[0]=='eth0') {
			$methd = '有线连接';
		}else{
			$methd = 'WiFi连接';
		}
		
		$mac = new GetMacAddr(PHP_OS);
		
		$this->ajaxReturn(array('methd'=>$methd,'ip'=>str_replace("\n", '', $array[1]),'mac'=>$mac->GetMacAddr(PHP_OS),'OS'=>PHP_OS));
	}

	/*****************报告过滤开始*****************/
	//渲染 #
	public function set_reportfilter(){
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
		if($tid == "4" || $tid == '5'){
			$id = I('get.id');
			$reportfilter = D('reportfilter');
			if (empty($id)) {
				$mouldinfo = $reportfilter->order('id')->find();
			}else{
				$mouldinfo = $reportfilter->where("id=$id")->find();
			}
			$namelist = $reportfilter->field('id,mouldname')->select();
			//var_dump($namelist);
			$thirdpart_arr = explode(',', $mouldinfo['thirdpart']);
			$custom_rule = explode(',', $mouldinfo['custom_rule']);
			$reportfilter_thirdpart = D('reportfilter_thirdpart');
			$thirdpartlist = $reportfilter_thirdpart->group('name')->order('id')->select();

			$this->assign('id',$id);
			$this->assign('namelist',$namelist);
			$this->assign('thirdpart',$thirdpart_arr);
			$this->assign('custom_rule',$custom_rule);
			$this->assign('thirdpartlist',$thirdpartlist);
			
			$this->display();
		}
		else{
			$this->redirect('Mare/Index/index');
		}
	}

	//获取新增模板ID接口 #
	public function get_newmould_id(){
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
		if($tid == "4" || $tid == '5'){
			$reportfilter = D('reportfilter');
			$id = $reportfilter->getfield('max(id)');
			$id++;
			$this->ajaxReturn(array('id'=>$id));
		}
		else{
			$this->ajaxReturn(array('code'=>'false','info'=>'操作失败'));
		}
	}

	//保存模板
	public function save_mould(){
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
		if($tid == "4" || $tid == '5'){
			$id = I('post.tpname');
			$mouldname = I('post.tptext');
			$thirdpart_arr =I('post.thirdpart');
			$custom_rule_arr =I('post.rules');

			$thirdpart = implode(',', $thirdpart_arr);
			$custom_rule = implode(',', $custom_rule_arr);

			$reportfilter = D('reportfilter');
			$idlist = $reportfilter->getfield('id',true);
			$data['mouldname']= $mouldname;
			$data['thirdpart']= $thirdpart;
			$data['custom_rule'] = $custom_rule;
			if (in_array($id, $idlist)) {
				$reportfilter->where("id=$id")->save($data);
			}
			else{
				$reportfilter->add($data);
			}
			
			$this->redirect('Mare/Setting/set_reportfilter');
		}else{
			$this->redirect('Mare/Index/index');
		}
	}

	//获取模板对应规则 #
	public function get_rule(){
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
		if($tid == "4" || $tid == '5'){
			$id =I('post.id');
			$reportfilter = D('reportfilter');
			$mouldinfo = $reportfilter->where("id=$id")->find();

			$thirdpart_arr = explode(',', $mouldinfo['thirdpart']);
			$custom_rule = explode(',', $mouldinfo['custom_rule']);

			$this->ajaxReturn(array('thirdpart'=>$thirdpart_arr,'custom_rule'=>$custom_rule));
		}else{
			$this->ajaxReturn(array('code'=>'false','info'=>'操作失败'));
		}
	}

	//删除模板 #
	public function delete_mould(){
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
		if($tid == "4" || $tid == '5'){
			$id = I('post.id');
			$reportfilter = D('reportfilter');
			$reportfilter->delete("$id");
			$this->ajaxReturn(array('code'=>'success','info'=>'操作成功'));
		}else{
			$this->ajaxReturn(array('code'=>'false','info'=>'操作失败'));
		}
	}

	/*****************报告过滤结束*****************/
	
	/**
	 * 客户端自定义规则
	 */
	public function set_client_rule() {
	    /*
	     "sensitive":{
	     "check_regex":"",//数据库
	     "check_xml":"", //xml文件
	     "check_txt":"", //日志文件
	     "check_hijack":"" //Activity劫持Toast提示语
	     }
	     * */
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
	    $client_rule_info = M('maintain')->where(['key' => 'client_rule'])->getField('value');
	    $client_rule_info = json_decode($client_rule_info, true);
	    
	    $result = M('client_rule')->select();
	    foreach ($result as $k => &$v) {
	        $v['rule_type'] = $client_rule_info[$v['rule_type']];
	    }

	    $this->assign('client_rule_info', $client_rule_info);
	    $this->assign('result_rule_info', $result);
	    $this->display('Setting/set_client_rule');
	}
	
	/**
	 * 新增客户端自定义规则
	 */
	public function add_client_rule() {
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
	    $rule_type = I('post.rule_type');
	    $rules_name = I('post.rules_name');
	    $rule_content = I('post.rule_content');
	    
	    $arr['rule_type'] = $rule_type;
	    $arr['rule_name'] = $rules_name;
	    $arr['rule_content'] = $rule_content;
	    $arr['status'] = 1;

	    if ($rule_type && $rules_name && $rule_content) {
	        if(M('client_rule')->add($arr)) {
	            $this->do_log('新增客户端自定义规则', '成功', '新增客户端自定义规则成功');
	            $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'msg'=>'添加成功'));
	        } else {
	            $this->do_log('新增客户端自定义规则', '失败', '新增客户端自定义规则失败');
	            $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>'添加失败'));
	        }
	    } else {
	        $this->do_log('新增客户端自定义规则', '失败', '新增客户端自定义规则失败');
	        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>'添加失败'));
	    }
	}
	
	
	/**
	 * 获取客户端自定义规则
	 */
	public function get_client_rule() {
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
	    $id =  intval(I('get.id'));
	    if ($id) {
	        $get_client_rule_info = M('client_rule')->where(['id' => $id])->find();
	        $get_client_rule_info['rule_content'] = htmlspecialchars_decode($get_client_rule_info['rule_content']);
	        $get_client_rule_info['rule_name'] = htmlspecialchars_decode($get_client_rule_info['rule_name']);
	        if ($get_client_rule_info) {
	            $this->do_log('获取客户端自定义规则', '成功', '获取客户端自定义规则成功');
	            $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'info'=> $get_client_rule_info));
	        } else {
	            $this->do_log('获取客户端自定义规则', '失败', '获取客户端自定义规则失败');
	        }
	    }
	}
	
	
	/**
	 * 更新客户端规则 
	 */
	public function update_client_rule() {
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
	    $id = intval(I('id'));
	    $rule_type = I('rule_type');
	    $rule_name = I('rule_name');
	    $rule_content = I('rule_content');
	    
	    $arr['rule_type'] = $rule_type;
	    $arr['rule_name'] = $rule_name;
	    $arr['rule_content'] = $rule_content;
	    
	    if ($id && $rule_type && $rule_name && $rule_content) {
	        $result = M('client_rule')->where(['id' => $id])->save($arr);
	        if (false !== $result) {
	            $this->do_log('更新客户端自定义规则', '成功', '更新客户端自定义规则成功');
	            $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'msg'=> '更新成功'));
	        } else {
	            $this->do_log('更新客户端自定义规则', '失败', '更新客户端自定义规则失败');
	            $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=> '更新失败'));
	        }
	    }
	}
	
	/**
	 * 启用禁用客户端规则 
	 */
	public function stop_client_rule() {
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
	    $id = intval(I('id'));
	    if($id) {
	        $status = M('client_rule')->where(['id' => $id])->getField('status');
	        if ($status == 1) {
	            $result = M('client_rule')->where(['id' => $id])->save(['status' => 2]);
	        } else {
	            $result = M('client_rule')->where(['id' => $id])->save(['status' => 1]);
	        }
	        
	        if (false !== $result) {
	            $this->do_log('启用禁用客户端自定义规则', '成功', '启用禁用客户端自定义规则成功');
	            $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'msg'=> '操作成功'));
	        } else {
	            $this->do_log('启用禁用客户端自定义规则', '失败', '启用禁用客户端自定义规则失败');
	            $this->ajaxReturn(array('code'=>'1', 'status' => 'error', 'msg'=> '操作失败'));
	        }
	    }
	}
	
	/**
	 * 删除客户端规则
	 */
	public function delete_client_rule() {
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
	    $id = intval(I('id'));
	    if($id) {
	        $result = M('client_rule')->where(['id' => $id])->delete();
	        if ($result) {
	            $this->do_log('删除客户端自定义规则', '成功', '删除客户端自定义规则成功');
	            $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'msg'=> '删除成功'));
	        } else {
	            $this->do_log('删除客户端自定义规则', '失败', '删除客户端自定义规则失败');
	            $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=> '删除失败'));
	        }
	    }
	}
	
	/**
	 * 修改wifi
	 */
	public function wifi_manager() {
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
	    if (IS_POST){
	        $wify_name = I('post.wify_name');
	        $wifi_password = I('post.wifi_password');
	        
	        //TODO
	        
	        $result = true;
	        
	        if ($result !== false) {
	            $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'info'=>'WiFi修改成功'));
	        } else {
	            $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'info'=>'WiFi修改失败'));
	        }
	       
	    }
	    $this->display();
	}
	
	/**
	 * 
	 */
	public function system_log() {
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
	    if (IS_AJAX) {
	        $result = 1;
	        $ip=I('iplist');
	        $port=I('port');
	        $level=I('log_level');
	        $status = I('status');
	        $pattern="/^(((1?\d{1,2})|(2[0-4]\d)|(25[0-5]))\.){3}((1?\d{1,2})|(2[0-4]\d)|(25[0-5]))$/";
	        $pattern1="/^([a-z0-9]+([a-z0-9-]*(?:[a-z0-9]+))?\.)?[a-z0-9]+([a-z0-9-]*(?:[a-z0-9]+))?(\.us|\.tv|\.org\.cn|\.org|\.net\.cn|\.net|\.mobi|\.me|\.la|\.info|\.hk|\.gov\.cn|\.edu|\.com\.cn|\.com|\.co\.jp|\.co|\.cn|\.cc|\.biz)$/i";
	        //$pattern="[a-zA-Z0-9.-]+([.][a-zA-Z0-9]{2,4})";
	        //差个正则表达式匹配
	        //^(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[0-9]{1,2})(\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[0-9]{1,2})){3}$
	        //[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?
	        if(($port=="" && $ip=="") || ((preg_match($pattern,$ip) || preg_match($pattern1,$ip)) && $port>0 && $port<65536)){
	            shell_exec("/Apktest/start_shell2 \" bash /Apktest/configure/sendlog.sh '$ip' '$port' '$status'\" ");
	            $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'info'=>'配置成功'));
	        }else{
	            $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'info'=>'配置失败'));
	        }
	    }
	    //$syslog_type = D("Surplus")->where(array('key'=>'syslog_type'))->find()['value'];
	    //$syslog_type = json_decode($syslog_type,1);//暂时没应用到
	    //"/Apktest/start_shell2 \"/bin/bash /Apktest/configure/date_setting.sh  ntp  {$synchronization_service1}  {$synchronization_service2} \" "
	    $res = shell_exec("/Apktest/start_shell2 \"/bin/bash /Apktest/configure/returnsetlog.sh > /Apktest/configure/returnsetlog \" ");
	    
	    $content=array('ip'=>"0.0.0.0",'port'=>null);//,'log_level'=>null
	    // while($content['ip']=="0.0.0.0" || $content['port']==null ){// || $content['log_level']==null
	    
	    $arr=file_get_contents("/etc/rsyslog.d/relay.conf");
	    //dump($arr);dump("sssssss");
	    
	    if($arr!=null && $arr!=""){
	        $flag = substr($arr, 0,1);
	        $ex_arr = substr($arr, 1);
	        $ex_arr=explode("\n",$ex_arr);
	        $temp=explode("@",$ex_arr[0]);
	        $temp2=explode(":",$temp[1]);
	        $content['status']=$flag;
	        $content['ip']=$temp2[0];
	        $content['port']=$temp2[1];
	        //$content['log_level']=$ex_arr[2];
	    }
	    $this->assign('syslog_type',$content);   
	    $this->display();
	}
	
	/**
	 * 获取map
	 */
// 	public function getMap() {
// 	    // TODO 
// 	    $data['map'] = 'f8:a9:d0:02:28:d6';  
// 	    $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'data' => $data));
// 	}
	
	/**
	 * 代理设置
	 */
	public function set_proxy() {
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
	    $filepath = './Uploads/Proxy/proxy.conf';
	    $proxy_info = file($filepath);
	    foreach ($proxy_info as $key => $value){
	        $list[] = explode(' ', $value);
	    }
	    
	    foreach ($list as $v) {
	        $result_arr[$v[0]] =  $v;
	    }
	    if (IS_POST){
	        $content = '';
            $http      = I('post.http');
            $http_port = I('post.http_port');
            $https     = I('post.https');
            $https_port = I('post.https_port');
            $no_proxy  = I('post.no_proxy');
            $username  = I('post.username');
            $password  = I('post.password');
            //$pattern= '/(d+).(d+).(d+).(d+)/';
            $pattern = '/\d+\.\d+\.\d+\.\d+/';
            //$pattern_url = '/^([w-]+.)+[w-]+(/[w-./?%&=]*)?$/';
            $pattern_url = '/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(:\d+)?(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?/';
            //$pattern= "/^((25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)\.){3}(25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|[1-9])$/";

            if ($http_port && $http) {
                if(!preg_match($pattern, $http)){
                    $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'ip地址不正确', 'obj' => 'http']));
                }
                $content = "http {$http} {$http_port}\n";
            }
            if ($https_port&& $https) {
                if(!preg_match($pattern, $https)){
                    $this->ajaxReturn(array('code'=>'-2', 'status' => 'error', 'msg'=>['info'=>'ip地址不正确', 'obj' => 'https']));
                }
                $content .= "https {$https} {$https_port}\n";
            }
            
            if ($no_proxy) {
                $no_proxy_arr = explode(',', $no_proxy);
                foreach ($no_proxy_arr as $v) {
                    if(!preg_match($pattern_url, $v)){
                        $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'msg'=>['info'=>'域名不正确', 'obj' => 'no_proxy']));
                    }
                }
                $content .= "no-proxy {$no_proxy}\n";
            }
            
            if ($username && !$password && $result_arr['password'][1]) {
                $password = $result_arr['password'][1];
                $content .= "username {$username}\npassword {$password}";
            } else if ($username && $password) {
                $content .= "username {$username}\npassword {$password}";
            } else if ($username && !$password && !$result_arr['password'][1]) {
                $content .= "username {$username}";
            }
                  
            //$filepath = "/Apktest/Proxy/proxy.conf";

            //$filepath = './Uploads/Proxy/proxy.conf';

            if(@$fp = fopen($filepath,'w+')){
                $result = fwrite($fp,$content);
                fclose($fp);
            } else {
                $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'info'=>'权限不够'));
            }
	        
	        
	        if ($result) {
	            $this->ajaxReturn(array('code'=>'1', 'status' => 'success', 'info'=>'代理配置保存成功'));
	        } else {
	            $this->ajaxReturn(array('code'=>'-1', 'status' => 'error', 'info'=>'代理配置保存失败'));
	        } 
	    }
	    

	    //print_r($result_arr);
	    $this->assign('result_arr', $result_arr);
	    $this->display();
	}
}



//获取mac地址类
class GetMacAddr{
    
    var $return_array = array(); // 返回带有MAC地址的字串数组
    var $mac_addr;
    
    function GetMacAddr($os_type){
        switch ( strtolower($os_type) ){
            case "Linux":
                $this->forLinux();
                break;
            case "linux":
                $this->forLinux();
                break;
            case "solaris":
                break;
            case "unix":
                break;
            case "aix":
                break;
            default:
                $this->forWindows();
                break;
                
        }
        
        
        $temp_array = array();
        foreach ( $this->return_array as $value ){
            if (
                preg_match("/[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f]/i",$value,$temp_array ) ){
                    $this->mac_addr = $temp_array[0];
                    break;
            }
            
        }
        unset($temp_array);
        return $this->mac_addr;
    }
    
    
    function forWindows(){
        @exec("ipconfig /all", $this->return_array);
        if ( $this->return_array )
            return $this->return_array;
            else{
                $ipconfig = $_SERVER["WINDIR"]."\system32\ipconfig.exe";
                if ( is_file($ipconfig) )
                    @exec($ipconfig." /all", $this->return_array);
                    else
                        @exec($_SERVER["WINDIR"]."\system\ipconfig.exe /all", $this->return_array);
                        return $this->return_array;
            }
    }
    
    
    
    function forLinux(){
        @exec("ifconfig -a", $this->return_array);
        return $this->return_array;
    }
    
    

} 