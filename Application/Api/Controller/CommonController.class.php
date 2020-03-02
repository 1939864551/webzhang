<?php
namespace Api\Controller;
use Think\Controller;

class CommonController extends Controller {
	public $replymsg=array();
	//jiami	
	public function aes_encrypt($encodeaeskey,$userkey,$str){
		return aes_encrypt($encodeaeskey,$userkey,$str);
	}
	//jiemi
	public function aes_decrypt($encodeaeskey,$userkey,$str){
		return aes_decrypt($encodeaeskey,$userkey,$str);
	}
	public function _empty(){
		exit;
	}
	public function __construct(){
		/*//一天的秒数
		parent::__construct();
		if(IS_POST){
			$data = json_decode($GLOBALS['HTTP_RAW_POST_DATA'],true); 
			//dump($data);
			$userkey   = $data['userkey'];
			$timestamp = $data['timestamp'];
			$nonce 	   = $data['nonce'];
			//传过来的json数据,其"/" 会变成"\\/",所以需要替换成原有的数据
			$encrypmsg = str_replace('\\/', '/', $data['encrypmsg']);
			$signature = $data['signature']; 

			if(strtoupper(substr($signature, 0,32)) !== strtoupper(md5($userkey.'+'.$encrypmsg."+".$timestamp."+".$nonce))){
				$this->ajaxReturn(array('errcode'=>1,'errmsg'=>'认证失败'));
			}
			$jiemi['userkey'] 		= $userkey;
			$jiemi['encodeaeskey']	= $encodeaeskey;
			$this->_replymsg 	  = json_encode($jiemi);
		}else{
			$this->ajaxReturn(array('errcode'=>1,'errmsg'=>'认证失败'));
			exit;
		}*/
	}
}
