<?php 
function getSetMode(){
    try{
        $lic   = D('Licinfo');
        $setmode = $lic->getfield('set_mode');
        return $setmode;
    }
    catch(Exception $e){
        return 0;
    }
}

/**
 * 过滤远程代码执行漏洞的系统执行函数
 * by chenhaocheng   
 * @param string $str 所需要执行的系统命令
 * @return string 执行结果
 */
function safeSystem($str){
    $str = str_replace('|', '', $str);
    $str = str_replace(';', '', $str);
    $str = str_replace('&', '', $str);
    $ret = System($str);
    return $ret;
}


//获取ipa信息
function getIpaInfo($targetFile){
	if(file_exists($targetFile)){
		$targetFile = getcwd().substr($targetFile, 1);
		// $version 	= shell_exec("dpkg --info {$targetFile} | grep -oP 'Version:\s[0-9\.\-]+' | grep -oP '[0-9\.\-]+'");
		// $version 	= substr(shell_exec("dpkg --info {$targetFile} | grep -oP 'new debian package, version [0-9\.\-]+\.' | grep -oP '[0-9\.\-]+\.'"),0,strlen(shell_exec("dpkg --info {$targetFile} | grep -oP 'new debian package, version [0-9\.\-]+\.' | grep -oP '[0-9\.\-]+\.'"))-2);
		// $name 		= shell_exec("dpkg --info {$targetFile} | grep -oP 'Name:\s[0-9\.\-A-Za-z]+' | grep -oP '\s[a-zA-Z\.\-]+' | grep -oP '[a-zA-Z\.\-]+'");
		Vendor('CFPropertyList.CFPropertyList');
		$filecontent = shell_exec("/bin/bash /Apktest/MobAppSecAss/getIosUpdatInfo.sh {$targetFile} && echo '成功'");
		if( explode("\n",trim($filecontent,"\n"))[1] == '成功' ){
			$content = file_get_contents("/tmp/Info.plist");
			unlink("/tmp/Info.plist");
			$plist = new CFPropertyList\CFPropertyList();
			$plist->parse($content);
			$getIpaInfo = $plist->toArray();

			if($getIpaInfo['CFBundleVersion'] || $getIpaInfo['CFBundleShortVersionString']){
				return array('version_name'=>$getIpaInfo['CFBundleVersion'],'versioncode'=>$getIpaInfo['CFBundleShortVersionString'],'data'=>$getIpaInfo);
			}else{	
				return false;
			}
		}else{
			return false;
		}
	}else{
		return false;
	}	
}


//获取android的应用信息
//目标文件 targetFIle
//如果获取到android信息就返回apk应用的版本信息
//否则就返回返回false
function getAndroidInfo($targetFile){
	Vendor('ApkParser.Apkparser');
	$appObj 	= new Apkparser();
	if(file_exists($targetFile)){
		$res     	= $appObj->open($targetFile);
		if($res){
			return array('versioncode'=>$appObj->getVersionCode(),'version_name'=>$appObj->getVersionName());
		}else{
			return false;
		}		
	}else{
		return false;
	}	
}

//加密和解密方法
function tcodes($string, $isEncrypt = true, $key)
{
	$key  	= strtolower(md5($key));

    $dynKey = $isEncrypt ? hash('sha1', microtime(true)) : substr($string, 0, 40);
    $dynKey1 = substr($dynKey, 0, 20);
	$dynKey2 = substr($dynKey, 20);

	$fixKey = hash('sha1', $key);
    $fixKey1 = substr($fixKey, 0, 20);
    $fixKey2 = substr($fixKey, 20);

    $newkey = hash('sha1', $dynKey1 . $fixKey1 . $dynKey2 . $fixKey2);

    if($isEncrypt){
        $newstring = $fixKey1 . $string . $dynKey2;
    }else{
    	$newstring = base64_decode(substr($string, 40));
    }

    $re = '';
    $len = strlen($newstring);
    for ($i = 0; $i < $len; $i++)
    {
    	$j = $i % 40;
    	$re .= chr(ord($newstring{$i}) ^ ord($newkey{$j}));
    }

    return $isEncrypt ? $dynKey . str_replace('=', '_', base64_encode($re)) : substr($re, 20, -20);
}

/**
* 下载本地的文件
* @parm url 即本地的文件路径
*/
function localDownFile($url,$name_ = ''){
    if(file_exists($url)){
        if (!$name_){
            $name 	= (strrpos($url, '/') == true )? substr($url , (strrpos($url, '/')+1) ): $url;
        } else {
            $name = $name_;
        }
        if (strpos($_SERVER['HTTP_USER_AGENT'],"Triden") && $name_) {
            $name = urlencode($name);
        }
        $file 	= fopen($url, "r");  //打开文件url
        header("Content-Type: application/octet-stream"); //指定mime类型为八进制文件流
        header("Accept-Ranges: bytes");
        header("Accept-Length: ".filesize($url));
        header("Content-Disposition: attachment; filename=$name");  //$name是文件的名字，一般在$url的最后
        echo fread($file,filesize($url));
        fclose($file);
    }else{
        return false;
    }
}

//str 查找的字符串
//find 需要查询到的图片
//n 第n次
function str_n_pos($str,$find,$n){
	for ($i=1;$i<=$n;$i++){
		$pos = strpos($str,$find);
		$str = substr($str,$pos+1);
		$pos_val=$pos+$pos_val+1;
	}
	return $pos_val-1;
}

/** 获取当前时间戳，精确到毫秒 */
function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

/** 格式化时间戳，精确到毫秒，x代表毫秒 */
function microtime_format($tag, $time)
{
   list($usec, $sec) = explode(".", $time);
   $date = date($tag,$usec);
   return str_replace('x', $sec, $date);
}

/**
 *  生成图片验证码
 */
function createCaptcha($goal){
    $captcha = new \Think\Verify();
    $captcha->fontttf 	= '4.ttf';
    $captcha->length 	= '4';
    $captcha->entry($goal);
}

/**
 * 排序方法
 * $list 二维数据
 * $field 排序的字段
 * $sortby 怕徐规则
 * return 排好序的数组
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list))
    {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
        {
            $refer[$i] = &$data[$field];
        }
        switch ($sortby)
        {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
        {
            $resultSet[] = &$list[$key];
        }
        return $resultSet;
    }
    return false;
}

/**
 * @code 验证码的内容
 * return bool 返回布尔值,true成功，false失败
 */
function checkCaptcha($code,$goal){
    $captcha = new \Think\Verify();
    return $captcha->check($code,$goal);
}

/**
 *  返回显示的结果集
 *  此方法为下载报告，提交的的参数所用
 */
function showResultStr($str){
	$arr = array('pass'=>1,'normal'=>2,'warning'=>3,'danger'=>4);
	$resultShow = array();
	foreach ($str as $krs => $vrs) {
		if($vrs=='on'){
			$resultShow[] = $arr[$krs];
		}
	}
	return $resultShow;
}

/**
 * 生成word文档
 */
function word($data,$fileName=''){
	if(empty($data)) return '';
	$data='<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">'.$data.'</html>';
	if(empty($fileName)) $fileName=date('YmdHis').'.doc';
	$fp=fopen($fileName,'wb');
	fwrite($fp,$data);
	fclose($fp);
}

function downloadWord($content, $file=''){
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=$file");
    $ext = substr(end(explode('.', $file)), 0, 3);
    switch($ext){
        case 'doc' : 
            $html = '<html xmlns:v="urn:schemas-microsoft-com:vml"xmlns:o="urn:schemas-microsoft-com:office:office"
                 xmlns:w="urn:schemas-microsoft-com:office:word" xmlns:m="http://schemas.microsoft.com/office/2004/12/omml"xmlns="http://www.w3.org/TR/REC-html40">';
            $html .= '<head></head>';
            break;
        case 'xls':
            $html = '<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            $html .= '<head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name></x:Name><x:WorksheetOptions><x:Selected/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>';
    }
    echo $html . '<body>'.$content .'</body></html>';
     
}
/**
 * 去掉html标签 指定
 */
function strip_selected_tags($text, $tags = array())
{
	$args = func_get_args();
	$text = array_shift($args);
	$tags = func_num_args() > 2 ? array_diff($args,array($text))  : (array)$tags;
	foreach ($tags as $tag){
	   	if(preg_match_all('/<'.$tag.'[^>]*>(.*)<\/'.$tag.'>/iU', $text, $found)){
	       $text = str_replace($found[0],$found[1],$text);
	 	}
	}
	return $text;
}

/**
 *@data  需要添加的日志数据 
 */
function addMareLog($data){
    $Log 				= D('Log');
    $data['date']		= date('Y-m-d');
	$data['handleip']	= $_SERVER['REMOTE_ADDR'];
	$data['handletime']	= date('Y-m-d H:i:s');
	if(!($Log->where($data)->select())){
		
		openlog ( "MARS" ,  LOG_PID  |  LOG_PERROR ,  LOG_LOCAL0 );
        syslog(LOG_INFO,json_encode($data));
        closelog();
		
		$Log->data($data)->add();
	}    
}


/**
 * 
 * @return 返回内网IP地址
 */
function get_inter()  
{  
    $onlineip = '';  
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {  
        $onlineip = getenv('HTTP_CLIENT_IP');  
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {  
        $onlineip = getenv('HTTP_X_FORWARDED_FOR');  
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {  
        $onlineip = getenv('REMOTE_ADDR');  
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {  
        $onlineip = $_SERVER['REMOTE_ADDR'];  
    }  
    return $onlineip;  
}  

//读取数据
/**
 * @appid 			应用对应的id
 * @testname 		对应的检测项
 * return deteclist 返回对应的数据
 */
function commonfun($appid,$testname){
		$detecion = D('Detection');
		$deteclist = $detecion->field('at_detection.id,at_detection.appid,at_appinfo.realname as appname,at_appinfo.version,at_steps.info,at_detecname.zhtestname,at_detection.result,at_appinfo.subtime as uploaddate,at_steps.time as lastupgraddate')->where(array('at_detection.appid'=>$appid,'at_detection.testname'=>$testname))->join('left join at_steps on at_steps.appid = at_detection.appid and at_detection.testname = at_steps.detectionid')->join('left join at_appinfo on at_appinfo.appid = at_detection.appid')->join('left join at_detecname on at_detecname.id = at_detection.testname')->select();

		$tmp = array('通过','低危','中危','高危');
		foreach ($deteclist as $k => $v) {
			$tmp_id = $v['result'];
			$deteclist[$k]['resultname']  		=  $tmp[$tmp_id];		 
			$deteclist[$k]['uploaddate'] 		=  date('Y-m-d H:i:s',$v['uploaddate']);
			$deteclist[$k]['lastupgraddate'] 	=  date('Y-m-d H:i:s',$v['lastupgraddate']);
		}
		return $deteclist;
	}

/**
 *
 * @param string $userkey 		用户的可以
 * @param string $requesturl	用户的请求地址
 * @param string $requestatus 	返回的应用数据读取状态
 * @param string $info 			自己的留言信息
 * @return boolean
 */

function addApilog($userkey,$appid,$requesturl,$requeststatus,$info = null)
{
	$apilog = D('ApiLog');
	$data = array(
		'appid'			=> $appid,
		'time'			=> time(),
		'requestip'		=> $_SERVER['REMOTE_ADDR'],
		'requesturl'	=> $requesturl,
		'userkey'		=> $userkey,
		'requeststatus'	=> $requeststatus,
		'info'			=> $info
	);
	$apilog->data($data)->add();
}


/**
 * 发送HTTP请求
 *
 * @param string $url 请求地址
 * @param string $method 请求方式 GET/POST
 * @param string $refererUrl 请求来源地址
 * @param array $data 发送数据
 * @param string $contentType 
 * @param string $timeout
 * @param string $proxy
 * @return boolean
 */
function send_request($url, $data, $refererUrl = '', $method = 'GET', $contentType = 'application/json', $timeout = 30, $proxy = false) {
	$ch = null;
	if('POST' === strtoupper($method)) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER,0 );
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		if ($refererUrl) {
			curl_setopt($ch, CURLOPT_REFERER, $refererUrl);
		}
		if($contentType) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:'.$contentType));
		}
		if(is_string($data)){
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		} else {
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		}
	} else if('GET' === strtoupper($method)) {
		if(is_string($data)) {
			$real_url = $url. (strpos($url, '?') === false ? '?' : ''). $data;
		} else {
			$real_url = $url. (strpos($url, '?') === false ? '?' : ''). http_build_query($data);
		}

		$ch = curl_init($real_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:'.$contentType));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		if ($refererUrl) {
			curl_setopt($ch, CURLOPT_REFERER, $refererUrl);
		}
	} else {
		$args = func_get_args();
		return false;
	}

	if($proxy) {
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
	}
	$ret = curl_exec($ch);
	$info = curl_getinfo($ch);
	$contents = array(
			'httpInfo' => array(
					'send' => $data,
					'url' => $url,
					'ret' => $ret,
					'http' => $info,
			)
	);

	curl_close($ch);
	return $ret;
}

//随机字符串
function createRand($len = null){
	if($len == null){
		$len = 18;
	}
	$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789';
	$randstr = '';
    for ( $i = 0; $i < $len; $i++ ) 
    {
        // 这里提供两种字符获取方式
        // 第一种是使用 substr 截取$chars中的任意一位字符；
        // 第二种是取字符数组 $chars 的任意元素
        // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        $randstr .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }

    return $randstr;
}
//加密
function aes_encrypt($encodeaeskey,$userkey,$str){
	Vendor('aes.Aes');
	$aes  = new AES($encodeaeskey);
	return $aes->encrypt($str);
}
//解密
function aes_decrypt($encodeaeskey,$userkey,$str){
	Vendor('aes.Aes');
	$aes  = new AES($encodeaeskey);
	return $aes->decrypt($str);
}

/*
* 检测是否是apk
* 是apk返回包名
* 不是则返回false
*/
function checkApkInfo($file){
	Vendor('ApkParser.Apkparser');
	$appObj = new ApkParser();

	if($appObj->open($file)){
		return $appObj->getPackage();
	}else{
		return false;
	}
}

//$name  pdf名称
//$info  apk信息
//$detec 对应appid的视图
function makePdfReport($info,$detec){
	Vendor('tcpdf.tcpdf');
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Nicola Asuni');
	$pdf->SetTitle($info['realname']);
	$pdf->SetSubject('TCPDF Tutorial');
	$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

	// set default header data
	// $pdf->SetHeaderData('theme.png', '15', '海云兴信息技术有限公司',"海云兴帮您测试APK安全\nwww.baidu.com");

	// set header and footer fonts
	$pdf->setHeaderFont(Array('stsongstdlight', '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array('stsongstdlight', '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$pdf->setLanguageArray($l);
	}

	$pdf->AddPage();

	$html ="<br/><br/><br/><br/><br/>";
	$pdf->writeHTML($html, true, false, true, false, '');

	$pdf->SetFont('droidsansfallback', 'B', 25);
	$appname = $info['realname'];
	$pdf->Write(0, $appname.'应用安全检测评估报告', '', 0, 'C', true, 0, false, false, 0);

	$html ="<br/>";

	$pdf->writeHTML($html, true, false, true, false, '');

	// $pdf->SetFont('droidsansfallback', 'B', 25);
	$pdf->SetFont('droidsansfallback', 'B', 25);

	$pdf->Write(0, 'Android版', '', 0, 'C', true, 0, false, false, 0);

	$html = '<style>div .position{font-size:20;} </style>			
			<div style="text-align:center;">
			<div class="position">&nbsp;<br/><br/></div>	
				<div>';
	if(substr($info['icon'], 0,4) == 'http'){
		$html .= '<img width="100" src="'.$info['icon'].'" /><br/>';
	}else{
		$html .= '<img width="100" src="http://'.$_SERVER["HTTP_HOST"].__ROOT__.'/../hyx/'.$info["icon"].'" /><br/>';
	}
	$html .='</div>
			</div>
			<div class="position">&nbsp;<br/><br/><br/></div>
			<div width="80" float="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="15" width="100">应用名&nbsp;&nbsp;:</font><font size="15">'.$info["realname"].'</font></div>
			<div width="80" float="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="15" width="100">版&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;本:</font><font size="15">'.$info["version"].'</font></div>
			<div width="80" float="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="15" width="100">检测时间:&nbsp;</font><font size="15">'.$info["subtime"].'</font></div></div>';


	$pdf->SetFont('droidsansfallback', '', 10);

	$pdf->writeHTML($html, true, false, true, false, '');

	$pdf->AddPage();
	$pdf->SetFont('droidsansfallback', '', 10);


	$pdf->SetFont('droidsansfallback', 'B', 30);
	$pdf->Write(0, '一、检测概述', '', 0, 'L', true, 0, false, false, 0);

	$html = '<div style="text-align:center;">';
	if(substr($info['icon'], 0,4) == 'http'){
		$html .= '<img width="100" src="'.$info['icon'].'" /><br/>';
	}else{
		$html .= '<img width="100" src="http://'.$_SERVER["HTTP_HOST"].__ROOT__.'/../hyx/'.$info["icon"].'" /><br/>';
	}
	$html .= '<br/>
		<font  size="15">'.$info["realname"].'</font><br/>
		<font  size="15">安全等级</font><br/><font color="red" size="30">'.ranking($info["secscore"]).'</font>
	</div>
	';
	$pdf->writeHTML($html, true, 0, true, true);

	$pdf->SetFont('droidsansfallback', 'B', 30);
	$pdf->Write(0, '二、基本信息', '', 0, 'L', true, 0, false, false, 0);

	$gaowei = 0;
	foreach ($detec as $k => $v) {
		if($v["result"] == 4){
			++$gaowei;
		}
	}
	$html = '<style>
		td {
			cellspacing:0px;
			border:1px solid #cbcbcb;
			font-size:15px;
		}
	</style>
	<table style="text-align: left;width:900px;">
		<tbody>
			<tr>
				<td style="width:100px;">应用名称</td>
				<td>'.$info["realname"].'</td>
				<td style="width:100px;">版本号</td>
				<td>'.$info["version"].'</td>
			</tr>
			<tr>
				<td style="width:100px;">包名</td>
				<td>'.$info["package"].'</td>
				<td style="width:100px;">检测时间</td>
				<td>'.$info["subtime"].'</td>
			</tr>
			<tr>
				<td>高危漏洞个数</td><td >'.$gaowei.'</td>
				<td>漏洞个数</td><td >'.$info["bugs"].'</td>
			</tr>
			<tr>
				<td style="width:100px;">MD5</td><td colspan="3" >'.$info["md5"].'</td>
			</tr>
			<tr>
				<td style="width:100px;">SHA-1</td><td colspan="3" >'.$info["sha1"].'</td>
			</tr>
			<tr>
				<td style="width:100px;">SHA-256</td><td colspan="3" >'.$info["sha256"].'</td>
			</tr>
			<tr>
				<td style="width:100px;">证书信息</td><td colspan="5" >'.$info["cert"].'</td>
			</tr>
		</tbody>
	</table>';
	$pdf->SetFont('droidsansfallback', '', 10);

	$pdf->writeHTML($html, true, false, true, false, '');

	$pdf->SetFont('droidsansfallback', 'B', 30);
	$pdf->Write(0, '三、检测概述', '', 0, 'L', true, 0, false, false, 0);

	$html =  '<style>
	td {
		cellspacing:0px;
		border:1px solid #cbcbcb;
		font-size:15px;
	}
	</style>
	<table style="text-align: left;">
			<tr><td style="text-align:center;width:100px;" >序号</td><td style="text-align:center;width:330px;">检测项目</td><td style="text-align:center;width:110px;">检测类型</td><td style="text-align:center;width:110px;">危险系数</td></tr>';
	foreach ($detec as $k => $v) {
		$index = $k + 1;
		$html .= '<tr><td style="text-align:center;">'.$index.'</td><td>'.$v["zhtestname"].'</td><td style="text-align:center;">'.$v["zhtesttype"].'</td><td style="text-align:center;">';
		if($v['result'] == 1){
			$html .= '<font color="#10CA19">'.$v['resultname'].'</font>';
		}elseif($v['result'] == 2){
			$html .= '<font color="orange">'.$v['resultname'].'</font>';
		}
		elseif($v['result'] == 3){
			$html .= '<font color="#F5470A">'.$v['resultname'].'</font>';
		}else{
			$html .= '<font color="red">'.$v['resultname'].'</font>';
		}
		$html .= '</td></tr>';
	}

	$html .= '</table>';
	$pdf->SetFont('droidsansfallback', '', 10);

	$pdf->writeHTML($html, true, false, true, false, '');

	$pdf->SetFont('droidsansfallback', 'B', 30);
	$pdf->Write(0, '四、详细检测结果', '', 0, 'L', true, 0, false, false, 0);

	$oldtestytpt = 0;
	$testtypenum = 1;
	foreach ($detec as $k => $v) {

		if($oldtestytpt==$v['testtype']){
		}else{
			$pdf->SetFont('droidsansfallback', 'B', 20);
			$pdf->Write(0, "4 . ".$testtypenum."  ".$v['zhtesttype'], '', 0, 'L', true, 0, false, false, 0);

			$testtypenum ++;
		}
		$oldtestytpt=$v['testtype'];
		$html ='<style>
		th {
			width:90px;
			border:1px solid #cbcbcb;
			font-size:15px;
			text-align:center;
		}
		td {
			cellspacing:0px;
			border:1px solid #cbcbcb;
			font-size:15px;
			width:550px;
		}
		</style>';
		$html .= "<table><tr><th>检测名称</th><td>".$v['zhtestname']."</td></tr>";
		$html .= "<tr><th>检测说明</th><td>".$v['damage']."</td></tr>";
		$html .= "<tr><th>检测结果</th><td>";
		if($v['result'] == 1){
			$html .= '<font color="#10CA19">'.$v['resultname'].'</font>';
		}elseif($v['result'] == 2){
			$html .= '<font color="orange">'.$v['resultname'].'</font>';
		}
		elseif($v['result'] == 3){
			$html .= '<font color="#F5470A">'.$v['resultname'].'</font>';
		}else{
			$html .= '<font color="red">'.$v['resultname'].'</font>';
		}

		$html .= "</td></tr>";
		$html .= "<tr><th>检测详情</th><td>".$v['info']."</td></tr>";
		if($v['result'] != 1){
			$html .= "<tr><th>修复建议</th><td>".$v['suggest']."</td></tr>";
		}
		$html .= "</table><br/><br/><br/>";
		// set core font
		$pdf->SetFont('droidsansfallback', '', 10);

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');
	}

	$pdf->lastPage();
	$name = $info['package'].'_'.$info['subtime'].'.pdf';
	$pdf->Output( $name, 'D');
}

//管理的报告
function makePdfBrief($info,$detec){
	Vendor('tcpdf.tcpdf');
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Nicola Asuni');
	$pdf->SetTitle($info['realname']);
	$pdf->SetSubject('TCPDF Tutorial');
	$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

	// set default header data
	// $pdf->SetHeaderData('theme.png', '15', '海云安信息技术有限公司',"海云兴帮您测试APK安全\nwww.baidu.com");

	// set header and footer fonts
	$pdf->setHeaderFont(Array('stsongstdlight', '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array('stsongstdlight', '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$pdf->setLanguageArray($l);
	}

	$pdf->AddPage();
	$pdf->SetFont('droidsansfallback', 'B', 25);
	$appname = $info['realname'];
	$pdf->Write(0, $appname.'应用安全检测管理报告', '', 0, 'C', true, 0, false, false, 0);

	// $html ="<br/>";

	// $pdf->writeHTML($html, true, false, true, false, '');
	$pdf->SetFont('droidsansfallback', 'B', 25);

	$pdf->Write(0, 'Android版', '', 0, 'C', true, 0, false, false, 0);

	$html = '<style>div .position{font-size:20;} </style>			
			<div style="text-align:center;">';
	if(substr($info['icon'], 0,4) == 'http'){
		$html .= '<img width="100" src="'.$info['icon'].'" /><br/>';
	}else{
		$html .= '<img width="100" src="http://'.$_SERVER["HTTP_HOST"].__ROOT__.'/'.$info["icon"].'" /><br/>';
	}
	$html .='<span width="100px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<font size="60">'.ranking($info["secscore"]).'</font>
			</div>
			<div width="80" float="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="15" width="100">应用名&nbsp;&nbsp;:</font><font size="15">'.$info["realname"].'</font></div>
			<div width="80" float="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="15" width="100">版&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;本:</font><font size="15">'.$info["version"].'</font></div>
			<div width="80" float="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="15" width="100">MD5:&nbsp;</font><font size="15">'.$info["md5"].'</font></div>
			<div width="80" float="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="15" width="100">检测时间:&nbsp;</font><font size="15">'.$info["subtime"].'</font></div></div>';
	$pdf->SetFont('droidsansfallback', '', 6);

	$pdf->writeHTML($html, true, false, true, false, '');

	$html = '<div style="text-align:center;">
		<img height="14px" src="http://'.$_SERVER["HTTP_HOST"].__ROOT__.'/Public/css/icon_anquan.png"/><font class="fclowrisk">通过</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<img height="14px" src="http://'.$_SERVER["HTTP_HOST"].__ROOT__.'/Public/css/icon_diwei.png"/><font class="fcmidrisk">低危</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<img height="14px" src="http://'.$_SERVER["HTTP_HOST"].__ROOT__.'/Public/css/icon_zhongwei.png"/><font class="fczhongwei">中危</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<img height="14px" src="http://'.$_SERVER["HTTP_HOST"].__ROOT__.'/Public/css/icon_gaowei.png"/><font class="fchighrisk">高危</font></div>
	<style>
		.ws8{width:66.66667%}
		.fr{float:right;}
		.pt30{padding-top:30px;}
		.pb30{padding-bottom:30px;}
		.ml40{margin-left:40px;}
		.pl40{padding-left:40px;}
		.pr30{padding-right:30px;}
		.bdl9{border-left:1px solid #999;}
		.fs24{font-size:24px;}
		.mt15{margin-top:15px;}
		.ofh{overflow:hidden;}
		li.anqun{background:url(icon_anquan.png) left center no-repeat;}
		li.diwei{background:url(icon_diwei.png) left center no-repeat;}
		li.zhongwei{background:url(icon_zhongwei.png) left center no-repeat;}
		li.gaowei{background:url(icon_gaowei.png) left center no-repeat;}
		li span{padding-right:20px;}
		.fclowrisk{color:#10CA19; font-size:15;}
		.fczhongwei{color:#F5470A; font-size:15;}
		.fcmidrisk{color:orange; font-size:15;}
		.fchighrisk{color:red; font-size:15;}
		.fcwhite{color:#FFFFFF;}
	</style>
	<div class="ws8 fr pt30 pb30"><div class="pl40 pr30 show">';
	$num_to_zh			= array('0'=>'暂未评级','1'=>'安全','2'=>'低危','3'=>'中危','4'=>'高危');
	$zh_to_num 			= array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
	foreach ($detec as $k => $v) {
		if($v['issues_severity'] || $v['vulrisklevel']){
			if($v['issues_severity']){
				$risklevel = $v['issues_severity']; 
	        }elseif($v['issues_severity'] == null && $v['vulrisklevel'] !== null){
	        	$risklevel = $zh_to_num[$v['vulrisklevel']];
	        }
			if(($v['showdiv'] == 1) && ($v['first'] == 1)){ 
				$html .= '<div class="fs24">'.$v["hvtype"].'</div>';
				$html .=  '<div class="mt15 ofh">';
			}
	        $html .='<ul>';
	        if($risklevel == 1){
	            $html .='<img src="http://'.$_SERVER["HTTP_HOST"].__ROOT__.'/Public/css/icon_anquan.png" width="14px"/><span class="fclowrisk">&nbsp;&nbsp;&nbsp;&nbsp;'.$num_to_zh[$risklevel].'&nbsp;&nbsp;</span><span class="fclowrisk">&nbsp;&nbsp;'.$v["vulriskname"].'&nbsp;&nbsp;</span>';
	        }
	        if($risklevel == 2){
	            $html .='<img src="http://'.$_SERVER["HTTP_HOST"].__ROOT__.'/Public/css/icon_diwei.png" width="14px"/><span class="fcmidrisk">&nbsp;&nbsp;&nbsp;&nbsp;'.$num_to_zh[$risklevel].'&nbsp;&nbsp;</span><span class="fcmidrisk">&nbsp;&nbsp;'.$v["vulriskname"].'&nbsp;&nbsp;</span>';
	        }
	        if($risklevel == 3){
	            $html .='<img src="http://'.$_SERVER["HTTP_HOST"].__ROOT__.'/Public/css/icon_zhongwei.png" width="14px"/><span class="fczhongwei">&nbsp;&nbsp;&nbsp;&nbsp;'.$num_to_zh[$risklevel].'&nbsp;&nbsp;</span><span class="fczhongwei">&nbsp;&nbsp;'.$v["vulriskname"].'&nbsp;&nbsp;</span>';
	        }
	        if($risklevel == 4){
	            $html .='<img src="http://'.$_SERVER["HTTP_HOST"].__ROOT__.'/Public/css/icon_gaowei.png" width="14px"/><span class="fchighrisk">&nbsp;&nbsp;&nbsp;&nbsp;'.$num_to_zh[$risklevel].'&nbsp;&nbsp;</span><span class="fchighrisk">&nbsp;&nbsp;'.$v["vulriskname"].'&nbsp;&nbsp;</span>';
	        }
	        $html .= "</ul>";
	        if(($v['showdiv'] == 1) && ($v['end'] == 1)){
	        	$html .= '</div>';
	        	if($k != '-1'){
	        		$html .= '<hr />';
	        	}
	    	}
	    }
	}
	$html .= '</div></div>';

	$pdf->SetFont('droidsansfallback', '', 6);
	$pdf->writeHTML($html, true, false, true, false, '');


	// $pdf->lastPage();
	$name = $info['package'].'_brief_'.$info['subtime'].'.pdf';
	$pdf->Output( $name, 'I');
}

function addLog($userid,$action){
	if(D('User')->find($userid)){
		$info   = D('user')->find($userid);
	}
	if(D('Admins')->find($userid)){
		$info   = D('Admins')->find($userid);
	}
	$name   = $info['loginemail'];
	$data = array(
		'userid' 		=> $userid,
		'username'		=> $name,
		'handleip'		=> $_SERVER['REMOTE_ADDR'],
		'handletime'	=> time(),
		'handlecontent' => $action,
		'date'			=> date('Y-m-d')
		);
	$log 	= D('Log');
	$log->data($data)->add();
}

function addUploadApkRecord($userid){
	$admin = D('Admins');
	$user  = D('User');
	if  ($info = $admin->find($userid)) {
		$count = $info['count']+1;
		$admin->data(array('count'=>$count,'userid'=>$userid))->save();
	}
	if ($info = $user->find($userid)) {
		$count = $info['count']+1;
		$user->data(array('count'=>$count,'userid'=>$userid))->save();
	}
}
//展示日志
//$logPath 是.log的日志文件的路径
function logShow($logPath){
	$Path 			= explode('/',$logPath);
	$pLen 			= count($Path); 
	$logPath        = './'.$Path[$pLen-2]."/".$Path[$pLen-1];
	$content = file_get_contents($logPath); 
	$array = explode("\n", $content); 
	for($i=0; $i<count($array); $i++) 
	{ 
		$tmp[] = explode("\t\t", $array[$i]);
	} 
	foreach ($tmp as $k => $v) {
		foreach ($v as $k1 => $v1) {
			$tmp = explode(' ', $v1);
			if(count($tmp) ==2){
				$new_array[$k][] = $tmp[0];
				$new_array[$k][] = $tmp[1];
			}else{
				$new_array[$k][] = $v1;
			}
		}
	}
	return $new_array;
}
//搜索功能的数组
//$logPath 是.log的日志文件的路径
//$search  搜索条件
function logShowSearch($search,$arr){
	$slen 			= count($search);
	foreach ($arr as $k => $v) {
		$res = 0;
		foreach ($search as $ks => $vs) {
			foreach ($v as $k1 => $v1) {
				if(strpos($v1,$vs) !==  false){
					++$res;
				}
			}
			if($res >= $slen){
				$goalArray[] =  $arr[$k];
				break;
			}
		}
	}
	return $goalArray;
}

function newLogShowSearch($search,$arr){
	//搜索的条件长度
	$slen  			= count($search);
	//数组长度
	$alen 			= count($arr);
	//循环数组,进行搜索
	for ($i=0; $i < $alen; $i++) { 
		//一次循环设置计数值为0
		$res = 0;
		//搜索条件的循环$slen次
		for ($j=0; $j < $slen ; $j++) { 
			//数组中的第$i个是6个长度,默认
			for ($l=0; $l < 6; $l++) { 
				//如果满足$search[$l] 在 $arr[$i][$j] 能够找到,$res 计数器就加1
				if($j == 0 && $l == 1){
					if(strpos($arr[$i][$l],$search[$j]) !== false){
						++$res;
					}
					if($search[$j] == ''){
						++$res;
					}
				}
				if($j == 1 && $l == 3){
					if(strpos($arr[$i][$l],$search[$j]) !== false){
						++$res;
					}
					if($search[$j] == ''){
						++$res;
					}
				}
				if($j == 2 && $l == 5){
					if(strpos($arr[$i][$l],$search[$j]) !== false){
						++$res;
					}
					if($search[$j] == ''){
						++$res;
					}
				}
				if($j == 3 && $l == 2){
					if(strpos($arr[$i][$l],$search[$j]) !== false){
						++$res;
					}
					if($search[$j] == ''){
						++$res;
					}
				}
				if($j == 4 && $l == 4){
					if(strpos($arr[$i][$l],$search[$j]) !== false){
						++$res;
					}
					if($search[$j] == ''){
						++$res;
					}
				}
			}
			if($res >= $slen){
				$goalArray[] = $arr[$i];
				break;
			}

		}
	}
	return $goalArray;
}

/**
* 获取当前页面完整URL地址
*/
// function get_url() {
// 	return $_SERVER['PHP_SELF'];
// }
/**
* 获取当前页面完整URL地址
*/
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

/**
* 根据评分显示abcd四个等级
*/
function ranking($score){
	if($score <= 100 && $score >= 90){
		return '<font color="green">'.$score.'分(A)</font>';
	}elseif($score <= 89 && $score >= 75){
		return '<font color="#A9E2C8">'.$score.'分(B)</font>';
	}elseif($score <= 74 && $score >= 60){
		return '<font color="orange">'.$score.'分(C)</font>';
	}else{
		return '<font color="red">'.$score.'分(D)</font>';
	}
}



//生成pdf报告
function makeMpdfReport($info,$detec,$pdfname = null){
	Vendor('mpdf60.mpdf');
	// header('Content-Type:text/html;charset=utf8');
	ini_set("max_execution_time", "1800");
	$vulinfo 		= D('Vulinfo');
	//设置中文编码
	$mpdf 		= new \mPDF('zh-CN','A4',0,'宋体');
	$mpdf->useAdobeCJK=true; //打开这个选项比较好
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->SetWatermarkText($marktext);
	$mpdf->showWatermarkText = true;
	//页眉内容
	//第一页
	$html = '<style>
		table.show{ border: 1px solid;border-collapse: collapse;width:750px;height:auto;font-size:1.4rem;line-height:1.4;}
		.show td { border: 1px solid #cbcbcb; border-collapse: collapse;padding:5px 10px;}
		.show tr{ border: 1px solid #cbcbcb; border-collapse: collapse;}
		.showtd2 { height:auto;font-size:1.6rem; }
		.clearfix:after{display:block;content"";clear:both;}
		.pull-left{float:left;}	
		h1 font{ font-weight:5px; }
		b{ width:5px; }
		.title-wrapper{padding-left:80px;margin-bottom:20px;margin-top:80px;background-color:#1374b9;position:relative;}
		.main-title{background-color:#ffffff;display:block;margin:0;width:250px;text-align:center;}
		.childtitle{ font-size:2rem;font-weight:normal;margin: 10px 30px;background-color:#ffffff; }
		div .position{ font-size:20; } 
		.xiahuaxian{ text-decoration:underline;min-width: 400px; }
		.grandtitle{ font-size:20px;font-weight:bold;border-left:5px solid #1374b9;padding-left:5px; }
		</style>
	<h1>&nbsp;</h1><h1>&nbsp;</h1>
	<h1 align="center"><font size="20px"><b>'.$info['realname'].'</b></font>应用安全检测技术报告'.'</h1>
	<h1 align="center"><font size="20px"><b>';
	if($info['tasktype'] == 'ios'){
		$html .= "iOS版";
	}elseif($info['tasktype'] == 'wx'){
		$html .= "微信安全测试";
	}else{
		$html .= "Android版";
	}

	$html .= '</b></font></h1>
			<div style="text-align:center;">
				<div>
					<img width="100px" src="';
	if(!file_exists($info['icon'])){
		$html .= __ROOT__.'/'.UPLOAD_PATH.'/icon/default.png';
	}else{
		$html .= __ROOT__.'/'.$info["icon"];
	}
	$html .='" /></div></div>';
	if($info['tasktype'] != 'wx'){
		$html .='<div style="margin-top:100px;" align="center">
		<table style="font-size:20px;margin-left:auto;margin-right:auto;">
		<tr><td>应用名：</td><td><span>'.$info["realname"].'</span></td></tr>
		<tr><td>版本号：</td><td><span>'.$info["version"].'</span></td></tr>
		<tr><td>检测时间：</td><td><span>'.$info["subtime"].'</span></td></tr>
		</table>
		</div>';
	}
	$html .='</div>';
	$mpdf->writeHTML($html);

	$mpdf->AddPage();
	$gaowei = $info['gaowei'];
	$html = '<div class="title-wrapper"><h1 class="pull-left main-title">一、基本信息</h1><div class="clearfix"></div></div>
		<table class="show" style="text-align: left;width:750px;">';
	if($info['tasktype'] != 'wx'){
		$html .= '<tr>
				<td style="width:100px;">应用名称</td>
				<td>'.$info["realname"].'</td>
				<td style="width:100px;">版本号</td>
				<td>'.$info["version"].'</td>
			</tr>
			<tr>
				<td style="width:100px;">包名</td>
				<td>'.$info["package"].'</td>
				<td style="width:100px;">时间</td>
				<td>'.$info["subtime"].'</td>
			</tr>';
	}
		$html .='<tr><td width="110px">高危漏洞</td><td >'.$gaowei.'</td><td>漏洞数</td><td >'.$info["bugs"].'</td></tr>';

		if($info['tasktype'] != 'wx'){
			$html .='<tr>
				<td style="width:100px;">MD5</td><td colspan="3" >'.$info["md5"].'</td>
			</tr>
			<tr>
				<td style="width:100px;">SHA-1</td><td colspan="3" >'.$info["sha1"].'</td>
			</tr>
			<tr>
				<td style="width:100px;">SHA-256</td><td colspan="3" >'.$info["sha256"].'</td>
			</tr>
			<tr>
				<td style="width:100px;">证书信息</td><td colspan="3" >'.$info["cert"].'</td>
			</tr>';
		}
		$html .='</table>';
	$html .=  '<div class="title-wrapper"><h1 class="pull-left main-title">二、检测概述</h1><div class="clearfix"></div></div>
	<table style="text-align: left;" class="show">
			<tr><td style="text-align:center;width:80px;" >序号</td><td style="text-align:center;width:280px;">检测项目</td><td style="text-align:center;width:80px;">检测类型</td><td style="text-align:center;width:100px;">漏洞数</td><td style="text-align:center;width:110px;">危险系数</td></tr>';
	$zh_to_num 			= array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
    $num_to_zh			= array('0'=>'暂未评级','1'=>'安全','2'=>'低','3'=>'中','4'=>'高');
	foreach ($detec as $k => $v) {
		if($v['issues_severity']){
			$risklevel = $v['issues_severity'];
		}elseif ($v['vulrisklevel'] && $v['issues_severity'] == null) {
			$risklevel = $zh_to_num[$v['vulrisklevel']];
		}
		$index = $k + 1;
		$html .= '<tr><td style="text-align:center;">'.$index.'</td><td>'.$v["vulriskname"].'</td><td style="text-align:center;">'.$v["hvtype"].'</td><td>'.$v['issues_count'].'</td><td style="text-align:center;">';
		if($risklevel == 1){
			$html .= '<font color="#10CA19">';
		}elseif($risklevel == 2){
			$html .= '<font color="orange">';
		}
		elseif($risklevel == 3){
			$html .= '<font color="#F5470A">';
		}else{
			$html .= '<font color="red">';
		}
		$html .= $num_to_zh[$risklevel].'</font></td></tr>';
	}
	$html .= '</table>';

	$mpdf->writeHTML($html);
	$html = '<span>&nbsp;</span>
	<div class="title-wrapper"><h1 class="pull-left main-title">三、详细检测结果</h1><div class="clearfix"></div></div>';

	$oldtestytpt = 0;
	$testtypenum = 1;
	// foreach ($detec as $k => $v) {
	$requestname_val 	= C('SERVER_TRANS_ZH');
	$transportinfo 		= C('PORT_TRANS_ZH');
	$needshowportinfo 	= C('NEED_SHOW_PORT'); 		
	$len 				= count($detec);
	$testtypenum		= 1;

	$hvshowtrans 		= array('hvdid'=>'HVDID','cnvdid'=>'CNVD','cveid'=>'CVE','cweid'=>'CWE','cvss'=>'CVSS','owaspmobiltop10'=>'owaspmobiltop10','owaspwebtop10'=>'owaspwebtop10','vulriskname'=>'检测项名','vulimpression'=>'影响范围','vulrisklevel'=>'风险等级','hvtype'=>'检测项分类','hpoctype'=>'检测技术','vultype'=>'分类','vulrisktype'=>'利用方式','issues_vector_url'=>'URL','issues_img_url'=>'截图证明','issues_proof'=>'漏洞证明','vuldescribe'=>'问题描述','vulreferurl'=>'参考URL','vulrepair'=>'解决方案','vulpatch'=>'修复补丁');

	$ttt 				= array('id', 'appid', 'report_path', 'start_datetime', 'finish_datetime', 'issues_response', 'issues_hash', 'detectionid', 'statusconfirm');
    $ttt1 				= array('product_extrainfo', 'protocol', 'state');
    //中文翻译
    $sss 				= C('SERVER_TRANS_ZH');
    $sss1 				= C('PORT_TRANS_ZH');
	foreach ($detec as $k1 => $v1) {
		$html .= "<p class='grandtitle'>".($k1+1).'、  '.$v1['vulriskname']."</p>";
		// $html .= "</div>";
		$html .='<table style="text-align: left;" class="show">';

		//HVDID
		$html .= '<tr><td style="text-align:center;width:150px;" >HVDID</td><td>'.$v1['hvdid'].'</td></tr>';

		//CNVD
		if(trim($v1['cnvdid']) != null){
			$html .= '<tr><td style="text-align:center;width:150px;" >CNVD</td><td>'.$v1['cnvdid'].'</td></tr>';
		}

		//CVE
		if(trim($v1['cveid']) != null){
			$html .= '<tr><td style="text-align:center;width:150px;" >CVE</td><td>'.$v1['cveid'].'</td></tr>';
		}

		//CWE
		if(trim($v1['cweid']) != null){
			$html .= '<tr><td style="text-align:center;width:150px;" >CWE</td><td>'.$v1['cweid'].'</td></tr>';
		}

		//CVSS
		if(trim($v1['cvss']) != null){
			$html .= '<tr><td style="text-align:center;width:150px;" >CVSS</td><td>'.$v1['cvss'].'</td></tr>';
		}

		//owaspmobiltop10
		if(trim($v1['owaspmobiltop10']) != null){
			$html .= '<tr><td style="text-align:center;width:150px;" >owaspmobiltop10</td><td>'.$v1['owaspmobiltop10'].'</td></tr>';
		}

		//owaspwebtop10
		if(trim($v1['owaspwebtop10']) != null){
			$html .= '<tr><td style="text-align:center;width:150px;" >owaspwebtop10</td><td>'.$v1['owaspwebtop10'].'</td></tr>';
		}

		//检测项名
		$html .= '<tr><td style="text-align:center;width:150px;" >'.$hvshowtrans['vulriskname'].'</td><td>'.$v1['vulriskname'].'</td></tr>';

		//风险等级
		if($v1['issues_severity']){
			$risklevel = $v1['issues_severity'];
		}elseif ($v1['vulrisklevel'] && $v1['issues_severity'] == null) {
			$risklevel = $zh_to_num[$v1['vulrisklevel']];
		}
		$html .= '<tr><td style="text-align:center;width:150px;" >'.$hvshowtrans['vulrisklevel'].'</td><td>'.$v1['vulrisklevel'].'</td></tr>';

		//检测项分类
		$html .= '<tr><td style="text-align:center;width:150px;" >'.$hvshowtrans['hvtype'].'</td><td>'.$v1['hvtype'].'</td></tr>';

		//检测技术
		$html .= '<tr><td style="text-align:center;width:150px;" >'.$hvshowtrans['hpoctype'].'</td><td>'.$v1['hpoctype'].'</td></tr>';

		//分类
		$html .= '<tr><td style="text-align:center;width:150px;" >'.$hvshowtrans['vultype'].'</td><td>'.$v1['vultype'].'</td></tr>';
		
		//利用方式
		$html .= '<tr><td style="text-align:center;width:150px;" >'.$hvshowtrans['vulrisktype'].'</td><td>'.$v1['vulrisktype'].'</td></tr>';

		//url
		if(trim($v1['issues_vector_url']) != null){
			$html .= '<tr><td style="text-align:center;width:150px;" >'.$hvshowtrans['issues_vector_url'].'</td><td>'.$v1['issues_vector_url'].'</td></tr>';
		}

		//图片证明
		if(trim($v1['issues_img_url']) != null){
			$html .= '<tr><td style="text-align:center;width:150px;" >'.$hvshowtrans['issues_img_url'].'</td><td>';
			$issues_img_url = $vulinfo->field('issues_img_url')->where(array('appid'=>$info['appid'],'detectionid'=>$v1['detectionid']))->select(); 
			foreach ($issues_img_url as $keyimg => $valueimg) {
				$html .= '<img src="'.__ROOT__.'/'.$valueimg['issues_img_url'].'" height="60px"/>';
			}
			$html .='</td></tr>';
		}


		$html 			.= '<tr><td style="text-align:center;width:150px;" >'.$hvshowtrans['issues_proof'].'</td><td style="word-wrap:break-word; word-break:normal;">';
		
		$issues_proof  	= $vulinfo->field('issues_proof')->where(array('appid'=>$info['appid'],'detectionid'=>$v1['detectionid']))->select(); 
		foreach ($issues_proof as $keyproof => $valueproof) {
			if($keyproof != 0){
				$html .="<hr/>";
			}
			if(is_array(json_decode($valueproof['issues_proof'],1))){
				//显示具体的详细检测项信息
				$issues_valueproof = json_decode($valueproof['issues_proof'],1); 
				$html .= '<table class="table table-bordered" style="margin-bottom:1px;"><tbody>';
				foreach($issues_valueproof as $ksr => $ksv){
					if(preg_match("/^((25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)\.){3}(25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|[1-9])$/",$ksr)){
						$html .= '<tr><td class="td1"  style="width: 15%;text-align: center;">扫描的域名</td><td>'.$ksr.'</td></tr>';
						foreach($ksv as $ksvr => $ksvv){
							if($ksvr == 'bad_pwd'){
								$html .= '<tr><td class="td1" style="width: 15%;text-align: center;">'.$ksr.':'.$ksvr.'</td><td><table class="table table-bordered" style="margin-bottom:1px;"><tbody>';
									foreach($ksvv as $ksvvr => $ksvvv){
										foreach($ksvvv as $ksvvvr => $ksvvvv){
											$html .= '<tr><td class="td1" style="width: 30%;text-align: center;">'.$ksvvvr.'</td><td style="word-wrap:break-word; word-break:normal;">'.$ksvvvv.'</td></tr>';
										}
									}
								$html .= '</tbody></table ></td></tr>';
							}elseif($ksvr == 'portinfo'){
								$html .='<tr><td class="td1" style="width: 15%;text-align: center;">'.$ksr.':'.$ksvr.'</td><td style="word-wrap:break-word; word-break:normal;">';
								if(is_array($ksvv)){
									foreach($ksvv as $ksvvr => $ksvvv){
										$html .= '<table class="table table-bordered" style="margin-bottom:10px;"><tbody>';
											foreach($ksvvv as $ksvvvr => $ksvvvv){
												if(!in_array($ksvvvr, $ttt1)){
													$html .='<tr><td class="td1" style="width: 30%;text-align: center;">'.$sss1[$ksvvvr].'</td><td style="word-wrap:break-word; word-break:normal;">'.$ksvvvv.'</td></tr>';
												}
											}
										$html .='</tbody></table >';
									}
								}else{
									$html .= $ksvv;
								}
								$html .='</td></tr>';
							}else{
								$html .='<tr><td class="td1" style="width: 15%;text-align: center;">'.$ksr.':'.$ksvr.'</td><td style="word-wrap:break-word; word-break:normal;">'.$ksvv.'</td></tr>';
							}
						}
					}elseif($ksr == 'vul_info'){
						$html .= '<table class="table table-bordered"><tbody>';
						if(is_array($ksv)){
							$html .= '<tr><td class="td1"  style="width: 15%;text-align: center;">获得的信息</td><td>'.$ksr.'</td><tr>';
							foreach($ksv as $ksvr => $ksvv){
								$html .= '<tr><td class="td1" style="width: 15%;text-align: center;">'.$ksr.':'.$ksvr.'</td><td style="word-wrap:break-word; word-break:normal;">'.$ksvv.'</td></tr>';
							}
						}else{
							$html .='<tr><td class="td1" style="width: 15%;text-align: center;">'.$ksr.'</td><td style="word-wrap:break-word; word-break:normal;">'.$ksv.'</td></tr>';
						}
						
						
						//echo "</tbody></table>";
					}
				}
				$html .= '</tbody></table >';
			}elseif(strpos($valueproof['issues_proof'],"\nhttp") !== false || strpos($valueproof['issues_proof'],"\r\nhttp") !== false || strpos($valueproof['issues_proof'],"\rhttp") !== false){
				$html .= str_replace(array("\r\nhttp","\nhttp","\rhttp"),"<br/>http",$valueproof['issues_proof']);
			}elseif(strpos($valueproof['issues_proof'],'./class') !== false){
				$html .=str_replace(array("\r\n./class","\n./class","\r./class"),"<br/>./class",$valueproof['issues_proof']);
			}elseif(strpos($valueproof['issues_proof'],"\n") !== false || strpos($valueproof['issues_proof'],"\r\n") !== false || strpos($valueproof['issues_proof'],"\r") !== false){
				$html .=str_replace(array("\r\n","\n","\r"),"<br/>",$valueproof['issues_proof']);
			}else{
				$html .= htmlentities ($valueproof['issues_proof'],  ENT_QUOTES );
			}	
		}
		if($issues_proof){
			$html .="<hr/>";
		}
		$issues_user_input = $vulinfo->field('issues_user_input')->where(array('appid'=>$info['appid'],'detectionid'=>$value['detectionid']))->select();
		foreach ($issues_user_input as $ki => $vi) {
            $html .= $vi['issues_user_input'];
        }

		$html .='</td></tr>';

		//危害
		if(trim($v1['vuldescribe']) != null && $risklevel > 1){
			$html .= '<tr><td style="text-align:center;width:150px;" >'.$hvshowtrans['vuldescribe'].'</td><td>'.$v1['vuldescribe'].'</td></tr>';
		}
		//修复建议
		if(trim($v1['vulrepair']) != null && $risklevel > 1){
			$html .= '<tr><td style="text-align:center;width:150px;" >'.$hvshowtrans['vulrepair'].'</td><td>'.$v1['vulrepair'].'</td></tr>';
		}
		$html .= '</table>';
	}	

	// $mpdf->WriteHTML($html);
	// $name = $info['package'].strtotime($info['subtime']).'.pdf';
	// if($pdfname != null){
	// 	$name  = $pdfname.".pdf";
	// }
	// if($name == '.pdf'){
	// 	$name = $info['task_name'].".pdf";
	// }
	// $mpdf->Output($name,'I');
	echo $html;
	exit;
}

//生成word报告
function makeWordReport($info,$detec,$wordname = null){
	$html = '<style>
		table.show{ border: 1px solid;border-collapse: collapse;width:750px;height:auto;font-size:1.4rem;line-height:1.4;}
		.show td { border: 1px solid #cbcbcb; border-collapse: collapse;padding:5px 10px;}
		.show tr{ border: 1px solid #cbcbcb; border-collapse: collapse;}
		.showtd2 { height:auto;font-size:1.6rem; }
		.clearfix:after{display:block;content"";clear:both;}
		.pull-left{float:left;}
		.title-wrapper{margin-bottom:20px;margin-top:30px;}
		h1 font{ font-weight:5px; }
		b{ width:5px; }
		.main-title{ margin: 10px 10px;background-color:#ffffff;padding:10px 0;border-bottom:1px solid #107DD4; }
		.childtitle{ font-size:2rem;font-weight:normal;margin: 10px 30px;background-color:#ffffff; }
		div .position{ font-size:20; } 
		.xiahuaxian{ text-decoration:underline;min-width: 400px; }
		.grandtitle{ font-size:20px;font-weight:4; }
		</style>
	<h1>&nbsp;</h1><h1>&nbsp;</h1>
	<h1 align="center"><font size="20px"><b>'.$info['realname'].'</b></font>应用安全检测技术报告'.'</h1>
	<h1 align="center"><font size="20px"><b>';
	if($info['tasktype'] == 'ios'){
		$html .= "iOS版";
	}elseif($info['tasktype'] == 'wx'){
		$html .= "微信安全测试";
	}else{
		$html .= "Android版";
	}

	$html .= '</b></font></h1>
	<h1>&nbsp;</h1><h1>&nbsp;</h1>
			<div style="text-align:center;">
				<div>
					<img width="100px" src="';
	if(!file_exists($info['icon'])){
		$html .= __ROOT__.'/'.UPLOAD_PATH.'/icon/default.png';
	}else{
		$html .= __ROOT__.'/'.$info["icon"];
	}
	$html .='" /><br/>
		<font  size="18pt" style="padding-top: 0.5mm;padding-bottom: 0.5mm;">'.$info["realname"].'</font><br/>
	</div>';
	$gaowei = $info['gaowei'];
	$html .= '<div class="title-wrapper"><h1 class="pull-left main-title">二、基本信息</h1><div class="clearfix"></div></div>
	<table class="show" style="text-align: left;width:750px;">';
	if($info['tasktype'] != 'wx'){
		$html .='<tr>
			<td style="width:100px;">应用名称</td>
			<td>'.$info["realname"].'</td>
			<td style="width:100px;">版本号</td>
			<td>'.$info["version"].'</td>
		</tr>
		<tr>
			<td style="width:100px;">包名</td>
			<td>'.$info["package"].'</td>
			<td style="width:100px;">检测时间</td>
			<td>'.$info["subtime"].'</td>
		</tr>';
	}
		$html .= '<tr>
			<td width="110px">高危漏洞个数</td><td >'.$gaowei.'</td>
			<td>漏洞个数</td><td >'.$info["bugs"].'</td>
		</tr>';
	if($info['tasktype'] != 'wx'){
		$html .='<tr>
			<td style="width:100px;">MD5</td><td colspan="3" >'.$info["md5"].'</td>
		</tr>
		<tr>
			<td style="width:100px;">SHA-1</td><td colspan="3" >'.$info["sha1"].'</td>
		</tr>
		<tr>
			<td style="width:100px;">SHA-256</td><td colspan="3" >'.$info["sha256"].'</td>
		</tr>
		<tr>
			<td style="width:100px;">证书信息</td><td colspan="3" >'.$info["cert"].'</td>
		</tr>';
	}
	$statisticsData = R('Mare/Task/statisticalChart',array('appid'=>$info['appid']));
	foreach ($statisticsData['circularchar'] as $keyc => $valuec) {
		$circularStatistics[] = $valuec['value']; 
	}
	if($circularStatistics[0] != 0 && $circularStatistics[1] != 0 && $circularStatistics[2] != 0){
		$html .= "<img src='".circularStatistics($circularStatistics)."/>";
	}

	$html .=  '</table><div class="title-wrapper"><h1 class="pull-left main-title">三、检测概述</h1><div class="clearfix"></div></div>
	<table style="text-align: left;" class="show">
			<tr><td style="text-align:center;width:100px;" >序号</td><td style="text-align:center;width:280px;">检测项目</td><td style="text-align:center;width:110px;">检测类型</td><td style="text-align:center;width:50px;">漏洞个数</td><td style="text-align:center;width:110px;">危险系数</td></tr>';
	$zh_to_num 			= array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
    $num_to_zh			= array('0'=>'暂未评级','1'=>'安全','2'=>'低','3'=>'中','4'=>'高');
	foreach ($detec as $k => $v) {
		if($v['issues_severity']){
			$risklevel = $v['issues_severity'];
		}elseif ($v['vulrisklevel'] && $v['issues_severity'] == null) {
			$risklevel = $zh_to_num[$v['vulrisklevel']];
		}
		$index = $k + 1;
		$html .= '<tr><td style="text-align:center;">'.$index.'</td><td>'.$v["vulriskname"].'</td><td style="text-align:center;">'.$v["hvtype"].'</td><td>'.$v['issues_count'].'</td><td style="text-align:center;">';
		if($risklevel == 1){
			$html .= '<font color="#10CA19">';
		}elseif($risklevel == 2){
			$html .= '<font color="orange">';
		}
		elseif($risklevel == 3){
			$html .= '<font color="#F5470A">';
		}else{
			$html .= '<font color="red">';
		}
		$html .= $num_to_zh[$risklevel].'</font></td></tr>';
	}
	$html .= '</table>';

	$html .= '<span>&nbsp;</span>
	<div class="title-wrapper"><h1 class="pull-left main-title">四、详细检测结果</h1><div class="clearfix"></div></div>';

	$oldtestytpt = 0;
	$testtypenum = 1;
	// foreach ($detec as $k => $v) {
	$requestname_val 	= C('SERVER_TRANS_ZH');
	$transportinfo 		= C('PORT_TRANS_ZH');
	$needshowportinfo 	= C('NEED_SHOW_PORT'); 		
	$len 				= count($detec);
	$testtypenum		= 1;

	foreach ($detec as $k1 => $v1) {
		if($oldtestytpt==$v1['hvtypeid']){

		}else{
			$html .= "<p class='grandtitle'>".$testtypenum.'、  '.$v1['hvtype']."</p>";
			$testtypenum ++;
		}
		$oldtestytpt=$detec[$k1]['hvtype'];
		$html .= "<div style='border:1px solid #e1e1e1;padding:5px;width:750px;'>
			<div><p style='margin:5px 0;border-bottom:1px solid #e1e1e1;'>检测名称：<span>".$v1['vulriskname']."</span></p></div><div>";
		

		if(file_exists($v1['issues_img_url']) || $v1['issues_user_input'] || $v1['issues_proof']){
			$html .= "<p style='margin:5px 0;border-bottom:1px solid #e1e1e1;'>检测证明：<br/><span>";
			

			if(file_exists($v1['issues_img_url']) == true && strpos($v1['issues_img_url'],'./Upload') !== false){
				if($ks == 0){
					$html .= "<span>截图:</span><img src='".__ROOT__.'/'.$v1['issues_img_url']."' width='60px'>";
				}else{
					$html .= "<img src='".__ROOT__.'/'.$v1['issues_img_url']."' width='60px'>";
				}
			}
			if($v1['issues_user_input']){
				$html .= "<p>".$v1['issues_user_input']."</p>";
			}
		
			if($v1['issues_proof']){
				// $html .= "<p style='margin:5px 0;border-bottom:1px solid #e1e1e1;'>报文:</p>";
				$srequestraw = json_decode($v1['issues_proof'],true);
				foreach ($srequestraw as $kvs => $vvs) {
					if($kvs == 'vul_info'){
						// $html .= "<p style='margin:5px 0;border-bottom:1px solid #e1e1e1;'>检测信息：</p>";
						foreach ($vvs as $kvs1 => $vvs1) {
							$html .= "<p style='margin:5px 0;border-bottom:1px solid #e1e1e1;'><span>".$kvs1.": </span><span>".$vvs1."</span></p>";
						}
					}else{
						foreach ($vvs as $kvv1 => $value) {
							if($kvv1 == 'portinfo'){
								$html .= "<p style='margin:5px 0;border-bottom:1px solid #e1e1e1;'><span>端口扫描信息:</span></p>";
								foreach ($value as $kvv2 => $vvv2) {
									foreach ($vvv2 as $kvv3 => $vvv3) {
										if(in_array($kvv3, $needshowportinfo)){
											$html .= "<p style='margin:5px 0;border-bottom:1px solid #e1e1e1;'><span>".$transportinfo[$kvv3].": </span><span>".$vvv3."</span></p>";
										}
									}
									$html .= "<br/>";
								}
							}elseif($kvv1 == 'bad_pwd'){
								//当出现键值等于 bad_pwd的时候
								foreach ($value as $kbd => $vbd) {
									$html .= "<p style='margin:5px 0;border-bottom:1px solid #e1e1e1;'><span>".$kbd.": </span><span>".$vbd."</span></p>";
								}
								if($vt['port']){
	                                 $html .= "<p style='margin:5px 0;border-bottom:1px solid #e1e1e1;'><span>".$kvv1.": </span><span>".$value."</span></p>";
	                             }
	                             if($vt['service']){
	                                 $html .= "<p style='margin:5px 0;border-bottom:1px solid #e1e1e1;'><span>".$kvv1.": </span><span>".$value."</span></p>";
	                             }											
							}else{
								$html .= "<p style='margin:5px 0;border-bottom:1px solid #e1e1e1;'><br/><span>".$kvv1.":</span><span>".$value."</span></p>";
							}										
						}
					}
				}
				if(!is_array($srequestraw)){
					$html .= "<p style='margin:5px 0;border-bottom:1px solid #e1e1e1;'>";

					if(strpos($v1['issues_proof'],'http') !== false){
						$html .= str_replace(array("\r\nhttp","\nhttp","\rhttp"),"<br/>http",$v1['issues_proof']);
					}elseif(strpos($v1['issues_proof'],'./class') !== false){
						$html .= str_replace(array("\r\n./class","\n./class","\rh./class"),"<br/>./class",$v1['issues_proof']);
					}else{
						$html .= $v1['issues_proof'];
					}

					$html .="</p>";
				}
			}
		}

		$zh_to_num 			= array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
    	$num_to_zh			= array('0'=>'暂未评级','1'=>'安全','2'=>'低','3'=>'中','4'=>'高');
    	if($v1['issues_severity']){
			$risklevel = $v1['issues_severity'];
		}elseif ($v1['vulrisklevel'] && $v1['issues_severity'] == null) {
			$risklevel = $zh_to_num[$v1['vulrisklevel']];
		}
		$html .= "</span></p></div><div><p style='margin:5px 0;'>检测结果：<span>";
		if($risklevel == 1){
			$html .= '<font color="#10CA19">'.$num_to_zh[$risklevel].'</font>';
		}elseif($risklevel == 2){
			$html .= '<font color="orange">'.$num_to_zh[$risklevel].'</font>';
		}elseif($risklevel == 3){
			$html .= '<font color="#F5470A">'.$num_to_zh[$risklevel].'</font>';
		}else{
			$html .= '<font color="red">'.$num_to_zh[$risklevel].'</font>';
		}
		$html .= "</span></p></div>";
		
		if(trim($v1['vuldescribe']) != null && $risklevel > 1){
			$html .= "<div><p style='margin:5px 0;border-bottom:1px solid #e1e1e1;'>危&nbsp;&nbsp;&nbsp;&nbsp;害：<span>".$v['vuldescribe']."</span></p></div>";
		}
		if(trim($v1['vulrepair']) != null && $risklevel > 1){
			$html .= "<div><p style='margin:5px 0;border-bottom:1px solid #e1e1e1;'>修复建议：<span>".$v['vulrepair']."</span></p></div>";
		}
		
		$html .= "</div><br/><br/>";
	}	

	if($wordname == null){
		if($info['package'] == null){
			$wordname = $info['task_name'].'.doc';
		}else{
			$wordname = $info['package'].strtotime($info['subtime']).".doc";
		}
	}else{
		$wordname = $wordname.".doc";
	}
	header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=$wordname");
    $ext = substr(end(explode('.', $wordname)), 0, 3);
    switch($ext){
        case 'doc' : 
            $xxx = '<html xmlns:v="urn:schemas-microsoft-com:vml"xmlns:o="urn:schemas-microsoft-com:office:office"
                 xmlns:w="urn:schemas-microsoft-com:office:word" xmlns:m="http://schemas.microsoft.com/office/2004/12/omml"xmlns="http://www.w3.org/TR/REC-html40">';
            $xxx .= '<head></head>';
            break;
        case 'xls':
            $xxx = '<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            $xxx .= '<head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name></x:Name><x:WorksheetOptions><x:Selected/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>';
    }
    echo $xxx . '<body>'.$html.'</body></html>';	
}
//生成简略的word
function makeWordBrief($info,$detec,$wordname = null){
	//页眉内容
	//第一页
	$html = '<style>
		table.show{ border: 1px solid;border-collapse: collapse;width:750px;height:auto;font-size:1.4rem;line-height:1.4;}
		.show td { border: 1px solid #cbcbcb; border-collapse: collapse;padding:5px 10px;}
		.show tr{ border: 1px solid #cbcbcb; border-collapse: collapse;}
		.showtd2 { height:auto;font-size:1.6rem; }
		.clearfix:after{display:block;content"";clear:both;}
		.pull-left{float:left;}
		.title-wrapper{margin-bottom:20px;margin-top:30px;}
		h1 font{ font-weight:5px; }
		b{ width:5px; }
		.main-title{ margin: 10px 10px;background-color:#ffffff;padding:10px 0;border-bottom:1px solid #107DD4; }
		.childtitle{ font-size:2rem;font-weight:normal;margin: 10px 30px;background-color:#ffffff; }
		div .position{ font-size:20; } 
		.xiahuaxian{ text-decoration:underline;min-width: 400px; }
		.grandtitle{ font-size:20px;font-weight:4; }
		</style>
	<h1>&nbsp;</h1><h1>&nbsp;</h1>
	<h1 align="center"><font size="20px"><b>'.$info['realname'].'</b></font>应用安全检测评估报告'.'</h1>
	<h1 align="center"><font size="20px"><b>';
	if($info['tasktype'] == 'ios'){
		$html .= "iOS版";
	}elseif($info['tasktype'] == 'wx'){
		$html .= "微信安全测试";
	}else{
		$html .= "Android版";
	}

	$html .= '</b></font></h1>
	<h1>&nbsp;</h1><h1>&nbsp;</h1>
			<div style="text-align:center;">
				<div>
					<img width="100px" src="';
	if(!file_exists($info['icon'])){
		$html .= __ROOT__.'/'.UPLOAD_PATH.'/icon/default.png';
	}else{
		$html .= __ROOT__.'/'.$info["icon"];
	}
	$html .='" /></div></div>';
	if($info['tasktype'] != 'wx'){
		$html .='<div align="center">
				<table style="font-size:20px;margin-left:180px;">
					<tr><td>应用名</td><td><span>'.$info["realname"].'</span><hr width="300px" style="margin-top:0px;margin-bottom:-5px;"/></td></tr>
					<tr><td>版本号</td><td><span>'.$info["version"].'</span><hr width="300px" style="margin-top:0px;margin-bottom:-5px;"/></td></tr>
					<tr><td>检测时间</td><td><span>'.$info["subtime"].'</span><hr width="300px" style="margin-top:0px;margin-bottom:-5px;"/></td></tr>
				</table>
			</div>';
	}
	$html .= '</div>';

	$html .= '<div class="title-wrapper"><h1 class="pull-left main-title">一、检测概述</h1><div class="clearfix"></div></div>
		<div style="text-align:center;">
		<img width="100" src="';
	if(!file_exists($info['icon'])){
		$html .= __ROOT__.'/'.UPLOAD_PATH.'/icon/default.png';
	}else{
		$html .= __ROOT__.'/'.$info["icon"];
	}
	$html .='" /><br/>
		<font  size="18pt" style="padding-top: 0.5mm;padding-bottom: 0.5mm;">'.$info["realname"].'</font><br/>
	</div>';
	$gaowei = $info['gaowei'];
	$html .= '<div class="title-wrapper"><h1 class="pull-left main-title">二、基本信息</h1><div class="clearfix"></div></div>
	<table class="show" style="text-align: left;width:750px;">';
	if($info['tasktype'] != 'wx'){
		$html .='<tr>
			<td style="width:100px;">应用名称</td>
			<td>'.$info["realname"].'</td>
			<td style="width:100px;">版本号</td>
			<td>'.$info["version"].'</td>
		</tr>
		<tr>
			<td style="width:100px;">包名</td>
			<td>'.$info["package"].'</td>
			<td style="width:100px;">检测时间</td>
			<td>'.$info["subtime"].'</td>
		</tr>';
	}
		$html .='<tr>
			<td width="110px">高危漏洞个数</td><td >'.$gaowei.'</td>
			<td>漏洞个数</td><td >'.$info["bugs"].'</td>
		</tr>';
	if($info['tasktype'] != 'wx'){
		$html .='<tr>
			<td style="width:100px;">MD5</td><td colspan="3" >'.$info["md5"].'</td>
		</tr>
		<tr>
			<td style="width:100px;">SHA-1</td><td colspan="3" >'.$info["sha1"].'</td>
		</tr>
		<tr>
			<td style="width:100px;">SHA-256</td><td colspan="3" >'.$info["sha256"].'</td>
		</tr>
		<tr>
			<td style="width:100px;">证书信息</td><td colspan="3" >'.$info["cert"].'</td>
		</tr>';
	}
	$html .=  '</table><div class="title-wrapper"><h1 class="pull-left main-title">三、检测概述</h1><div class="clearfix"></div></div>
	<table style="text-align: left;" class="show">
			<tr><td style="text-align:center;width:100px;" >序号</td><td style="text-align:center;width:280px;">检测项目</td><td style="text-align:center;width:110px;">检测类型</td><td style="text-align:center;width:50px;">漏洞个数</td><td style="text-align:center;width:110px;">危险系数</td></tr>';

	$zh_to_num 			= array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
    $num_to_zh			= array('0'=>'暂未评级','1'=>'安全','2'=>'低','3'=>'中','4'=>'高');
	foreach ($detec as $k => $v) {
		if($v['issues_severity']){
			$risklevel = $v['issues_severity'];
		}elseif ($v['vulrisklevel'] && $v['issues_severity'] == null) {
			$risklevel = $zh_to_num[$v['vulrisklevel']];
		}
		$index = $k + 1;
		$html .= '<tr><td style="text-align:center;">'.$index.'</td><td>'.$v["vulriskname"].'</td><td style="text-align:center;">'.$v["hvtype"].'</td><td>'.$v['issues_count'].'</td><td style="text-align:center;">';
		if($risklevel == 1){
			$html .= '<font color="#10CA19">';
		}elseif($risklevel == 2){
			$html .= '<font color="orange">';
		}
		elseif($risklevel == 3){
			$html .= '<font color="#F5470A">';
		}else{
			$html .= '<font color="red">';
		}
		$html .= $num_to_zh[$risklevel].'</font></td></tr>';
	}
	$html .= '</table>';

	if($wordname == null){
		if($info['package'] == null){
			$wordname = $info['task_name'].'.doc';
		}else{
			$wordname = $info['package'].strtotime($info['subtime']).".doc";
		}
	}else{
		$wordname = $wordname.".doc";
	}
	header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=$wordname");
    $ext = substr(end(explode('.', $wordname)), 0, 3);
    switch($ext){
        case 'doc' : 
            $xxx = '<html xmlns:v="urn:schemas-microsoft-com:vml"xmlns:o="urn:schemas-microsoft-com:office:office"
                 xmlns:w="urn:schemas-microsoft-com:office:word" xmlns:m="http://schemas.microsoft.com/office/2004/12/omml"xmlns="http://www.w3.org/TR/REC-html40">';
            $xxx .= '<head></head>';
            break;
        case 'xls':
            $xxx = '<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            $xxx .= '<head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name></x:Name><x:WorksheetOptions><x:Selected/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>';
    }
    echo $xxx . '<body>'.$html.'</body></html>';	
}


function showtableContent($str){
	$onerow = 36;
	$n = ceil(mb_strlen($str,'utf-8')/$onerow);
	for($i=0;$i<=$n;++$i){
		$first = ($i-1)*$onerow;
		$end   = $i*$onerow;
		if($i == 1){
			$newstr = substr($str,$first,$end,'utf-8')."<br/>";
		}else{
			$newstr .= substr($str,$first,$end,'utf-8')."<br/>";
		}
	}
	return $newstr;
}
//生成简略报告pdf
function makeMpdfBrief($info,$detec,$pdfname =null){
	Vendor('mpdf60.mpdf');
	//设置中文编码
	$mpdf 		= new \mPDF('zh-CN','A4',0,'宋体');
	$mpdf->useAdobeCJK=true; //打开这个选项比较好
	$mpdf->SetDisplayMode('fullpage');
	$mpdf->SetWatermarkText($marktext);
	$mpdf->showWatermarkText = true;
	//页眉内容
	
	//第一页
	$html = '<style>
		table.show{ border: 1px solid;border-collapse: collapse;width:750px;height:auto;font-size:1.4rem;line-height:1.4;}
		.show td { border: 1px solid #cbcbcb; border-collapse: collapse;padding:5px 10px;}
		.show tr{ border: 1px solid #cbcbcb; border-collapse: collapse;}
		.showtd2 { height:auto;font-size:1.6rem; }
		.clearfix:after{display:block;content"";clear:both;}
		.pull-left{float:left;}
		.title-wrapper{margin-bottom:20px;margin-top:30px;}
		h1 font{ font-weight:5px; }
		b{ width:5px; }
		.main-title{ margin: 10px 10px;background-color:#ffffff;padding:10px 0;border-bottom:1px solid #107DD4; }
		.childtitle{ font-size:2rem;font-weight:normal;margin: 10px 30px;background-color:#ffffff; }
		div .position{ font-size:20; } 
		.xiahuaxian{ text-decoration:underline;min-width: 400px; }
		.grandtitle{ font-size:20px;font-weight:4; }
		</style>
	<h1>&nbsp;</h1><h1>&nbsp;</h1>
	<h1 align="center"><font size="20px"><b>'.$info['realname'].'</b></font>应用安全检测评估报告'.'</h1>
	<h1 align="center"><font size="20px"><b>';
	if($info['tasktype'] == 'ios'){
		$html .= "iOS版";
	}elseif($info['tasktype'] == 'wx'){
		$html .= "微信安全测试";
	}else{
		$html .= "Android版";
	}

	$html .= '</b></font></h1>
	<h1>&nbsp;</h1><h1>&nbsp;</h1>
			<div style="text-align:center;">
				<div>
					<img width="100px" src="';
	if(!file_exists($info['icon'])){
		$html .= __ROOT__.'/'.UPLOAD_PATH.'/icon/default.png';
	}else{
		$html .= __ROOT__.'/'.$info["icon"];
	}
	$html .='" /></div></div>';
	if($info['tasktype'] != 'wx'){
		$html .='<div align="center">
				<table style="font-size:20px;margin-left:180px;">
					<tr><td>应用名</td><td><span>'.$info["realname"].'</span><hr width="300px" style="margin-top:0px;margin-bottom:-5px;"/></td></tr>
					<tr><td>版本号</td><td><span>'.$info["version"].'</span><hr width="300px" style="margin-top:0px;margin-bottom:-5px;"/></td></tr>
					<tr><td>检测时间</td><td><span>'.$info["subtime"].'</span><hr width="300px" style="margin-top:0px;margin-bottom:-5px;"/></td></tr>
				</table>
			</div>';
	}		
	$html.='</div>';
	$mpdf->writeHTML($html);

	$mpdf->AddPage();
	$html = '<div class="title-wrapper"><h1 class="pull-left main-title">一、检测概述</h1><div class="clearfix"></div></div>
		<div style="text-align:center;">
		<img width="100" src="';
	if(!file_exists($info['icon'])){
		$html .= __ROOT__.'/'.UPLOAD_PATH.'/icon/default.png';
	}else{
		$html .= __ROOT__.'/'.$info["icon"];
	}
	$html .='" /><br/>
		<font  size="18pt" style="padding-top: 0.5mm;padding-bottom: 0.5mm;">'.$info["realname"].'</font><br/>
	</div>';
	$gaowei = $info['gaowei'];
	$html .= '<div class="title-wrapper"><h1 class="pull-left main-title">二、基本信息</h1><div class="clearfix"></div></div>
	<table class="show" style="text-align: left;width:750px;">';
	if($info['tasktype'] != 'wx'){
		$html .='<tr>
			<td style="width:100px;">应用名称</td>
			<td>'.$info["realname"].'</td>
			<td style="width:100px;">版本号</td>
			<td>'.$info["version"].'</td>
		</tr>
		<tr>
			<td style="width:100px;">包名</td>
			<td>'.$info["package"].'</td>
			<td style="width:100px;">检测时间</td>
			<td>'.$info["subtime"].'</td>
		</tr>';
	}
		$html .='<tr>
			<td width="110px">高危漏洞个数</td><td >'.$gaowei.'</td>
			<td>漏洞个数</td><td >'.$info["bugs"].'</td>
		</tr>';
	if($info['tasktype'] != 'wx'){
		$html .='<tr>
			<td style="width:100px;">MD5</td><td colspan="3" >'.$info["md5"].'</td>
		</tr>
		<tr>
			<td style="width:100px;">SHA-1</td><td colspan="3" >'.$info["sha1"].'</td>
		</tr>
		<tr>
			<td style="width:100px;">SHA-256</td><td colspan="3" >'.$info["sha256"].'</td>
		</tr>
		<tr>
			<td style="width:100px;">证书信息</td><td colspan="3" >'.$info["cert"].'</td>
		</tr>';
	}
	$html .=  '</table><div class="title-wrapper"><h1 class="pull-left main-title">三、检测概述</h1><div class="clearfix"></div></div>
	<table style="text-align: left;" class="show">
			<tr><td style="text-align:center;width:100px;" >序号</td><td style="text-align:center;width:280px;">检测项目</td><td style="text-align:center;width:110px;">检测类型</td><td style="text-align:center;width:50px;">漏洞个数</td><td style="text-align:center;width:110px;">危险系数</td></tr>';

	$zh_to_num 			= array('暂未评级'=>'0','安全'=>1,'低'=>2,'中'=>3,'高'=>4);
    $num_to_zh			= array('0'=>'暂未评级','1'=>'安全','2'=>'低','3'=>'中','4'=>'高');
	foreach ($detec as $k => $v) {
		if($v['issues_severity']){
			$risklevel = $v['issues_severity'];
		}elseif ($v['vulrisklevel'] && $v['issues_severity'] == null) {
			$risklevel = $zh_to_num[$v['vulrisklevel']];
		}
		$index = $k + 1;
		$html .= '<tr><td style="text-align:center;">'.$index.'</td><td>'.$v["vulriskname"].'</td><td style="text-align:center;">'.$v["hvtype"].'</td><td>'.$v['issues_count'].'</td><td style="text-align:center;">';
		if($risklevel == 1){
			$html .= '<font color="#10CA19">';
		}elseif($risklevel == 2){
			$html .= '<font color="orange">';
		}
		elseif($risklevel == 3){
			$html .= '<font color="#F5470A">';
		}else{
			$html .= '<font color="red">';
		}
		$html .= $num_to_zh[$risklevel].'</font></td></tr>';
	}
	$html .= '</table>';
	$mpdf->writeHTML($html);
	if($pdfname){
		$name = $pdfname.".pdf";
	}else{
		$name = $info['package'].'_brief_'.strtotime($info['subtime']).'.pdf';
	}
	$mpdf->Output($name,'D');
	exit;
}


/*
* 判断访问时ios还是android
*/

function getUserAgent(){
	$userAgent = $_SERVER['HTTP_USER_AGENT'];
	if(preg_match("/(iPod|iPad|iPhone)/", $userAgent)){
		return 'ios';
	}elseif(preg_match("/android/i", $userAgent)){
		return 'android';
	}elseif(preg_match("/WP/", $userAgent)){
		return 'wp';
	}else{
		return 'other';
	}
}


//柱状统计图
//$test 即为需要显示的数组 高,中,低的数组顺序
function histogram($test){
	import('Vendor.pChart.pDraw');
    import('Vendor.pChart.pData');
    import('Vendor.pChart.pImage');
	$MyData = new \pData();
    $MyData->addPoints($test, "");
    // $MyData->setAxisName(0,"Hits");

    $arr = array("高危", "中危", "低危");
    $MyData->addPoints($arr, "Browsers");

    //$MyData->addPoints(array("Firefox","Chrome","Internet Explorer","Opera","Safari","Mozilla","SeaMonkey","Camino","Lunascape"),"Browsers"); 
    $MyData->setSerieDescription("Browsers", "Browsers");
    $MyData->setAbscissa("Browsers");
   // $MyData->setAbscissaName("Browsers"); 
  //  $MyData->setAxisColor(0,array("R"=>255,"G"=>128,"B"=>0));
  
    /* Create the pChart object */
    $myPicture = new \pImage(515, 310, $MyData);
    // $a= UPLOAD_PATH.'fonts/heiti.ttf';
    $name = PUBLIC_PATH. 'fonts/simsun.ttc';


    $myPicture->setFontProperties(array("FontName" => $name, "FontSize" => 10));
    //$myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6)); 

    /* Draw the chart scale */
    $myPicture->setGraphArea(70, 80, 480, 280);
    //"CycleBackground" => TRUE,背景有背影
    $myPicture->drawScale(array("DrawSubTicks" => TRUE, "GridR" => 0, "GridG" => 0, "GridB" => 0, "GridAlpha" => 0, "Pos" => SCALE_POS_TOPBOTTOM));
  //标题

    $myPicture->drawText(150, 50, "漏洞等级分布图",array("R"=>7,"G"=>6,"B"=>6,"Alpha" => 150,"FontSize" => 22));
    //边框
  //  $myPicture->drawRectangle(20,2,510,290,array("R"=>198,"G"=>149,"B"=>149, "Alpha" => 60));
    
    /* Turn on shadow computing */
    $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));
    
    $Palette = array(
    	"0" => array("R" => 244, "G" => 67, "B" => 54),
        "1" => array("R" => 255, "G" => 152, "B" => 0),
        "2" => array("R" => 79, "G" => 202, "B" => 129),
    );

    /* Draw the chart *///图  
    $myPicture->drawBarChart(array("DisplayPos" => LABEL_POS_INSIDE, "DisplayValues" => TRUE, "Rounded" => TRUE, "Surrounding" => 30, "OverrideColors" => $Palette));
    $FileName = UPLOAD_PATH."/icon/two.png";

    $myPicture->render($FileName);
    // echo "<img src='".__ROOT__.'/'.$FileName."'/>";
    return __ROOT__.'/'.$FileName;
}

function histogramword($test){
	import('Vendor.pChart.pDraw');
    import('Vendor.pChart.pData');
    import('Vendor.pChart.pImage');
	$MyData = new \pData();
    $MyData->addPoints($test, "");
    // $MyData->setAxisName(0,"Hits");

    $arr = array("高危", "中危", "低危");
    $MyData->addPoints($arr, "Browsers");

    //$MyData->addPoints(array("Firefox","Chrome","Internet Explorer","Opera","Safari","Mozilla","SeaMonkey","Camino","Lunascape"),"Browsers"); 
    $MyData->setSerieDescription("Browsers", "Browsers");
    $MyData->setAbscissa("Browsers");
   // $MyData->setAbscissaName("Browsers"); 
  //  $MyData->setAxisColor(0,array("R"=>255,"G"=>128,"B"=>0));
  
    /* Create the pChart object */
    $myPicture = new \pImage(515, 310, $MyData);
    // $a= UPLOAD_PATH.'fonts/heiti.ttf';
    $name = PUBLIC_PATH. 'fonts/simsun.ttc';


    $myPicture->setFontProperties(array("FontName" => $name, "FontSize" => 10));
    //$myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6)); 

    /* Draw the chart scale */
    $myPicture->setGraphArea(70, 80, 480, 280);
    //"CycleBackground" => TRUE,背景有背影
    $myPicture->drawScale(array("DrawSubTicks" => TRUE, "GridR" => 0, "GridG" => 0, "GridB" => 0, "GridAlpha" => 0, "Pos" => SCALE_POS_TOPBOTTOM));
  //标题

    $myPicture->drawText(150, 50, "漏洞等级分布图",array("R"=>7,"G"=>6,"B"=>6,"Alpha" => 150,"FontSize" => 22));
    //边框
  //  $myPicture->drawRectangle(20,2,510,290,array("R"=>198,"G"=>149,"B"=>149, "Alpha" => 60));
    
    /* Turn on shadow computing */
    $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));
    
    $Palette = array(
    	"0" => array("R" => 244, "G" => 67, "B" => 54),
        "1" => array("R" => 255, "G" => 152, "B" => 0),
        "2" => array("R" => 79, "G" => 202, "B" => 129),
    );

    /* Draw the chart *///图  
    $myPicture->drawBarChart(array("DisplayPos" => LABEL_POS_INSIDE, "DisplayValues" => TRUE, "Rounded" => TRUE, "Surrounding" => 30, "OverrideColors" => $Palette));
    $FileName = UPLOAD_PATH."/icon/two.png";

    $myPicture->render($FileName);
    // echo "<img src='".__ROOT__.'/'.$FileName."'/>";
    return $FileName;
}

function makepiechart($data,$name) {
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
    
function makebarchart($datay,$name) {

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

function check_grants(){
		
		$licinfo = D('Licinfo')->where(array('id'=>1))->find();
		
		if($licinfo['end_date']<time()){
			return false;
		}else{
			return true;
		}
	} 
	
/**
 * 通用js弹窗 支持ajax返回
 * @param unknown $msg
 * @param unknown $url
 * @author qinxuening
 */
function common_alert($msg, $url = null) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        if ('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $isAjax = true;
        }
    }
    if (!empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')])) {
        // 判断Ajax方式提交
        $isAjax = true;
    }
    if ($isAjax) {
        $data['msg'] = $msg;
        $data['url'] = !empty($url) ? $url : NULL;
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    } else {
        if (empty($url)) {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
            echo '<script language="javascript"> alert("' . $msg . '");</script>';
            echo "<script>javascript:history.back(-1);</script>";
            exit();
        } else {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
            echo '<script language="javascript"> alert("' . $msg . '");</script>';
            echo '<script language="javascript">window.location.href="' . $url . '";</script>';
            exit();
        }
    }
}

/**
 * 判断分析员操作任务权限
 * @param unknown $appid
 * @return boolean
 * @author qinxuening
 */
function check_auth_user($appid) {
    $userid = $_SESSION[$_SESSION['randomstr']]['userid'];
    $appinfo = M('appinfo');
    $result_appinfo = $appinfo->where(['userid' =>$userid, 'appid' => $appid])->find();
    
    if ($result_appinfo) {
        return true;
    } else {
        return false;
    }
}

function get_url_headers($url,$timeout=10){
        $ch=curl_init();
    
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,true);
        curl_setopt($ch,CURLOPT_NOBODY,true);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);
    
        $data=curl_exec($ch);
        $data=preg_split('/\n/',$data);
    
        $data=array_filter(array_map(function($data){
            $data=trim($data);
            if($data){
                $data=preg_split('/:\s/',trim($data),2);
                $length=count($data);
                switch($length){
                    case 2:
                    return array($data[0]=>$data[1]);
                    break;
                    case 1:
                    return $data;
                    break;
                    default:
                        break;
                }
            }
        },$data));
    
            sort($data);
            foreach($data as $key=>$value){
                $itemKey=array_keys($value)[0];
                if(is_int($itemKey)){
                    if(stristr($value[$itemKey],"HTTP/")!=false){
                        $data[0]=$value[$itemKey];
                    }else{
                        $data[$key]=$value[$itemKey];
                    }
                    
                }elseif(is_string($itemKey)){
                    $data[$itemKey]=$value[$itemKey];
                    unset($data[$key]);
                }
            }
    
            return $data;
    }
/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author qinxuening
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}



/**
 * 获取当前用户信息
 * @return array
 */
function get_user_info(){
    $userid = $_SESSION[$_SESSION['randomstr']]['userid'];
    return M('user')->where(['userid'=>$userid])->find();
}

/**
 * 检测包名限制个数
 * @return boolean
 */
function get_grand_appnumber(){
    $grand_appnumber = M('licinfo')->where(array('id'=>1))->getField('grand_appnumber');

    $count_record = M('apprecord')->field('count(*) as count')->select();
    
    if ($grand_appnumber== -1) {
        return true;
    }
    
    if ($count_record[0]['count'] < $grand_appnumber){
        return true;
    } else {
        return false;
    }
}

/**
 * 检测APP累计测试次数
 * @return boolean
 */
function check_app_max_count() {
    $licinfo = M('licinfo');
    $max_task = $licinfo->field('task_sum, max_task')->find();
    if ($max_task['max_task'] == -1){
        return true;
    } else {
        if ($max_task['task_sum'] >=  $max_task['max_task']) {
            return false;
        } else {
            return true;
        }
    }

}

/**
 * 
 * @return string 返回检测任务类型名称
 * @author qxn
 */
function get_task_type ($info){
    if ($info== 'android') {
        $test_type = 'Android';
    } else if($info== 'ios') {
        $test_type = 'Ios';
    } else if ($info== 'wx') {
        $test_type = '微信应用';
    } else if ($info== 'web') {
        $test_type = 'Web应用被动';
    } else {
        $test_type = 'Web应用主动';
    }
    return $test_type;
}

/**
 * 通用打印
 * @param unknown $data
 * @author
 */
function P($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}


/**
 * 获取安全等级
 * @param unknown $security_level
 */
function get_security_level($security_level) {
    if ($security_level) {
        if ($security_level == 1) {
            $Internet_security_level = 6;
        } else if ($security_level == 2) {
            $Internet_security_level = 7;
        } else if ($security_level == 3) {
            $Internet_security_level = 8;
        } else if ($security_level == 4) {
            $Internet_security_level = 9;
        }
        return $Internet_security_level;
    }    
}

/**
 * 等级中文
 * @param unknown $security_level
 * @return string
 */
function get_security_level_c($security_level) {
    if ($security_level) {
        if ($security_level == 1) {
            $Internet_security_level = '一';
        } else if ($security_level == 2) {
            $Internet_security_level = '二';
        } else if ($security_level == 3) {
            $Internet_security_level = '三';
        } else if ($security_level == 4) {
            $Internet_security_level = '四';
        }
        return $Internet_security_level;
    }
}

/**
 * 
 * @param unknown $security_level
 * @return string
 */
function get_security_level_title($level, $pass_count, $no_pass_count) {
    return "针对本次测试，依据 GA/T 1390.3 —2017 信息安全技术 信息安全等级保护基本要求 第3部分：移动互联安全扩展要求 第".get_security_level_c($level)."级安全要求 ，开展测试工作。通过执行测试发现符合要求的有： {$pass_count} 项，发现不符合要求的有： {$no_pass_count} 项，系统表现如下：";
}

/**
 * 过滤读取
 * @param unknown $info
 * @param string $type
 * @return string
 */
function get_vul_result($info,$type="world", $level = 8) {
    $arr_s =  explode(', ', $info);
    foreach ($arr_s as $key_s => $value_s) {
        if(strpos($value_s, C('SECURITY_LEVEL_LIKE').$level) !== false) {
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
function array_sort($array,$row,$type){
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

function myLog($str) {
    $dir = getcwd(). '/Uploads/logs/';
//     echo $dir;
    if(!is_dir($dir)) {
      $flag =  mkdir($dir, 0777, true);
//       dump($flag);
      chmod($dir, 0777);

    }
    $file = $dir . date('Ymd') . '.log.txt';
    $fp = fopen($file, 'a+');

    if (flock($fp, LOCK_EX)) {
        $content = "[" . date('Y-m-d H:i:s') . "]\r\n";
        $content .= $str . "\r\n\r\n";
        fwrite($fp, $content);
        flock($fp, LOCK_UN);
        fclose($fp);
        chmod($file, 0777);
        //return true;
    } else {
        fclose($fp);
       // return false;
    }
}

/**
 * 根据配置文件配置要查询vulinfo的detectionid
 */
function get_detection_arr($detecresultinfo,$appid,$serverity) {
    if(count(C('DETECTION_ARR'))  > 0 && in_array('1', $serverity)) {
        $detection_arr = M('vulinfo')->field('DISTINCT(detectionid)')->where(['appid' => $appid, 'detectionid' => ['in',C('DETECTION_ARR')]])->select();
        $detection_arr_v = [];
        foreach ($detection_arr as $detection_arr_value) {
            $detection_arr_v[] = $detection_arr_value['detectionid'];
        }
        $array_diff_arr = array_diff(C('DETECTION_ARR'),$detection_arr_v);
        if(count($array_diff_arr) > 0) {
            $detection_exploit_arr = M('ExploitDb')->where(['id' => ['in', $array_diff_arr]])->select();
            foreach ($detection_exploit_arr as $detection_exploit_arr_value) {
                $append_data['appid'] = $appid;
                $append_data['detectionid'] = $detection_exploit_arr_value['id'];
                $append_data['case_name'] = $detection_exploit_arr_value['vulriskname'];
                $append_data['hvtype'] = $detection_exploit_arr_value['hvtype'];
                $append_data['hvtypeid'] = $detection_exploit_arr_value['hvtypeid'];
                $append_data['risk_description'] = $detection_exploit_arr_value['vuldescribe'];
                $append_data['hvtypeid'] = $detection_exploit_arr_value['hvtypeid'];
                $append_data['standard'] = $detection_exploit_arr_value['hvdid'];
                $append_data['risk_level'] = 1;
                $append_data['detection_process'] = '无';
                $append_data['suggestions'] = '无';
                $append_data['vulriskname'] = $detection_exploit_arr_value['vulriskname'];
                array_push($detecresultinfo, $append_data);
            }
        }
    }
    return $detecresultinfo;
}


/**
 * MARS导出报告在【基本信息】添加【系统版本、恶意代码版本、恶意URL版本、恶意行为版本、主机漏洞版本、应用漏洞版本】6个信息
 * html版本 
 */
function report_product_ino() {
    $vuldb_list = M('vuldb')->select();
    $product_ver = D('Licinfo')->field('product_ver')->where(array('id'=>1))->find();
    $html_product_info = "<table class='table' style='margin-bottom:20px;'><tbody><tr>
            <td>系统版本</td>
            <td>{$product_ver['product_ver']}</td>
            </tr>";
    foreach ($vuldb_list as &$vul_value) {
        $vul_value['vuldb_name']  = $vul_value['vuldb_name'].'版本';
        $html_product_info .=
        "<tr>
        <td>{$vul_value['vuldb_name']}</td>
        <td>{$vul_value['vuldb_version']}</td>
        </tr>";
    }
    $html_product_info .=
    "</tbody></table>";
    return $html_product_info;
}


/**
 * MARS导出报告在【基本信息】添加【系统版本、恶意代码版本、恶意URL版本、恶意行为版本、主机漏洞版本、应用漏洞版本】6个信息
 * word版本
 */
function report_product_ino_word($section,$cellColor,$cellColSpan3,$center,$BaseInfocellTextStyle) {
    $vuldb_list = M('vuldb')->select();
    $product_ver = D('Licinfo')->field('product_ver')->where(array('id'=>1))->find();
    $baseProductInfoTable = $section->addTable('styleBaseInfoTable');
    $baseProductInfoTable->addRow();
    $baseProductInfoTable->addCell(3000,$cellColor)->addText('系统版本',$BaseInfocellTextStyle,$center);
    $baseProductInfoTable->addCell(6000, $cellColSpan3)->addText($product_ver['product_ver'],$BaseInfocellTextStyle,null);
    foreach ($vuldb_list as &$vul_value) {
        $vul_value['vuldb_name']  = $vul_value['vuldb_name'].'版本';
        $baseProductInfoTable->addRow();
        $baseProductInfoTable->addCell(3000,$cellColor)->addText($vul_value['vuldb_name'],$BaseInfocellTextStyle,$center);
        $baseProductInfoTable->addCell(6000, $cellColSpan3)->addText($vul_value['vuldb_version'],$BaseInfocellTextStyle,null);
    }
    $section->addTextBreak(1);
}



function xml_encode2($data, $root='think', $item='item', $attr='', $id='id', $encoding='utf-8') {
    if(is_array($attr)){
        $_attr = array();
        foreach ($attr as $key => $value) {
            $_attr[] = "{$key}=\"{$value}\"";
        }
        $attr = implode(' ', $_attr);
    }
    
    $attr   = trim($attr);
    $attr   = empty($attr) ? '' : " {$attr}";
    $xml    = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>\r\n";
    $xml   .= "<!DOCTYPE {$root} [<!ENTITY ldquo  '“'>
    <!ENTITY rdquo  '”'>
    <!ENTITY trade  '™'>
    <!ENTITY rtrade '®'>
    <!ENTITY copyr  '©'>
    ]>";
    
    $xml   .= "<{$root}>\r\n";
    $xml   .= "<SCANINFO {$attr}/>";
    $xml   .= data_to_xml2($data, $item, '');
    $xml   .= "</{$root}>";
    return $xml;
}

/**
 * 数据XML编码
 * @param mixed  $data 数据
 * @param string $item 数字索引时的节点名称
 * @param string $id   数字索引key转换为的属性名
 * @return string
 */
function data_to_xml2($data, $item='item', $id='id') {
    $xml = $attr = $_attr1 = '';
    foreach ($data as $key => $val) {
        if(is_numeric($key)){
            $id && $attr = " {$id}=\"{$key}\"";
            $key  = $item;
        } else {
            $arr = explode('_', $key);
            if(count($arr) == 2) {
                $key = $arr[0];
                $item = $arr[1];
            }
            preg_match('/(.*?)(\[.*?\])/', $key,$result);
            if(count($result) == 3) {
                $key = $result[1];
                $attr = ltrim($result[2],'[');
                $attr = rtrim($attr,']');
                $attr = " {$attr}";
            }
            $arr1 = explode('——', $key);
            $key = $arr1[0];
            if(count($arr1) == 2) {
                //                 echo $arr1[1];die();
                foreach (json_decode($arr1[1],true) as $key1 => $value1) {
                    $_attr1[] = "{$key1}=\"{$value1}\"";
                }
                $attr = implode(' ', $_attr1);
            }
            unset($arr1);
            
        }
        //         if($attr1) {
        //             $xml    .= $attr1;
        //         }
        if($attr) {
            if($_attr1) {
                $xml .=  "<{$key} {$attr}/>";
            } else {
                $xml .=  "<{$key} {$attr}>";
            }
        } else {
            $xml .=  "<{$key}>";
        }
        unset($attr);
        $xml    .=  (is_array($val) || is_object($val)) ? data_to_xml2($val, $item, $id) : $val;
        if($_attr1) {
            $xml    .=  "\r\n";
        } else {
            $xml    .=  "</{$key}>\r\n";
        }
        unset($_attr1);
    }
    return $xml;
}


/**
 * 生成xml文件
 */
function writeXsml($str,$fileName) {
    $dir = getcwd(). '/Uploads/Xml/';
    if(!is_dir($dir)) {
        $flag =  mkdir($dir, 0777, true);
        chmod($dir, 0777);
    }
    $file = $dir . $fileName. '.xml';
    $fp = fopen($file, 'w+');
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, $str);
        flock($fp, LOCK_UN);
        fclose($fp);
        chmod($file, 0777);
    } else {
        fclose($fp);
    }
    localDownFile($file);
}





















