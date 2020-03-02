<?php

/**
*AES class
*加密和解密方法
*/
class Aes
{
	/**
     * 对需要加密的明文进行填充补位
     * @param $encodeaeskey 加密秘钥
     * @param $userkey 用户key
     * @param $data 加密的数据
     * @param $encrypt 需要解密的数据
     * @return 补齐明文字符串
     */
    private $encodeaeskey;
    public function __construct($encodeaeskey) {
        $this->key = $encodeaeskey;
    }
    //加密方法
    public function encrypt($userkey,$data){
        $pc = new Prpcrypt($this->key);
        return $pc->encrypt($data, $userkey);
    }
    //解密方法
    public function decrypt($userkey,$encrypt) {
        $pc = new Prpcrypt($this->key);
        return  $pc->decrypt($encrypt,$userkey);
    }
}



/**
 * PKCS7Encoder class
 *
 * 提供基于PKCS7算法的加解密接口.
 */
class PKCS7Encoder
{
    public static $block_size = 32;

    /**
     * 对需要加密的明文进行填充补位
     * @param $text 需要进行填充补位操作的明文
     * @return 补齐明文字符串
     */
    function encode($text)
    {
        $block_size = PKCS7Encoder::$block_size;
        $text_length = strlen($text);
        //计算需要填充的位数
        $amount_to_pad = PKCS7Encoder::$block_size - ($text_length % PKCS7Encoder::$block_size);
        if ($amount_to_pad == 0) {
            $amount_to_pad = PKCS7Encoder::block_size;
        }
        //获得补位所用的字符
        $pad_chr = chr($amount_to_pad);
        $tmp = "";
        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }

    /**
     * 对解密后的明文进行补位删除
     * @param decrypted 解密后的明文
     * @return 删除填充补位后的明文
     */
    function decode($text)
    {

        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }

}

/**
 * Prpcrypt class
 *
 * 提供接收和推送给公众平台消息的加解密接口.
 */
class Prpcrypt
{
    public $key;

    function Prpcrypt($k)
    {   
        $this->key = base64_decode($k . "=");
    }

    /**
     * 对明文进行加密
     * @param string $text 需要加密的明文
     * @return string 加密后的密文
     */
    public function encrypt($text, $appid)
    {
        //获得16位随机字符串，填充到明文之前
        // $random = $this->getRandomStr();
        // $text = $random . pack("N", strlen($text)) . $text . $appid;
        // 网络字节序
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $iv = substr($this->key, 0, 16);
        //使用自定义的填充方式对明文进行补位填充
        $pkc_encoder = new PKCS7Encoder;
        $text = $pkc_encoder->encode($text);
        mcrypt_generic_init($module, $this->key, $iv);
        //加密
        $encrypted = mcrypt_generic($module, $text);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);

        //print(base64_encode($encrypted));
        //使用BASE64对加密后的字符串进行编码
        // return array(ErrorCode::$OK, base64_encode($encrypted));
        return base64_encode($encrypted);
    }

    /**
     * 对密文进行解密
     * @param string $encrypted 需要解密的密文
     * @return string 解密得到的明文
     */
    public function decrypt($encrypted, $appid)
    {
        //使用BASE64对需要解密的字符串进行解码
        $ciphertext_dec = base64_decode($encrypted);
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $iv = substr($this->key, 0, 16);
        mcrypt_generic_init($module, $this->key, $iv);

        //解密
        $decrypted = mdecrypt_generic($module, $ciphertext_dec);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);

        //去除补位字符
        $pkc_encoder = new PKCS7Encoder;
        $result = $pkc_encoder->decode($decrypted); 
        return $result;
    }


    /**
     * 随机生成16位字符串
     * @return string 生成的字符串
     */
    function getRandomStr()
    {

        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }

}

//组合数据

class ApptestMsgCrypt
{

	 //随机函数
    private function createRand($len = null){
		if($len == null){
			$len = 18;
		}
		$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789';
		$randstr = '';
	    for ( $i = 0; $i < $len; $i++ ) 
	    {
	        $randstr .= $chars[ mt_rand(0, strlen($chars) - 1) ];
	    }
	    return $randstr;
	}

    /**
    *   @param $encodeaeskey 加密秘钥
    *   @param $userkey 用户的key
    *   @param $jsonData 发送的json数据
    */
	public function encryptMsg($encodeaeskey,$userkey,$jsonData)
	{
		$aes  				= new AES($encodeaeskey); 
        $timestamp          = time();
        $nonce              = $this->createRand();
		$encrypmsg 			= $aes->encrypt($userkey,$jsonData);
		$msg                = $userkey.'+'.$encrypmsg.'+'.$timestamp.'+'.$nonce;
		$signature 				= md5($msg);
		$params 			= json_encode(
		array(
			'userkey'        =>$userkey,
			'encrypmsg'      =>$encrypmsg,
			'signature'      =>$signature,
			'timestamp'      =>$timestamp, 
			'nonce'          =>$nonce
			)
		);



		return $params;
	}

    /**
    *   @param $encodeaeskey 加密秘钥
    *   @param $userkey 用户的key
    *   @param $jsonData 的经过json_decode的数据
    */
    public function decryptMsg($encodeaeskey,$userkey,$jsonData)
    {
        $aes            = new AES($encodeaeskey); 
        $encrypmsg      = $jsonData['encrypmsg']; 
        $decryptmsg     = $aes->decrypt($userkey,$encrypmsg);
        return $decryptmsg;
    }

}


