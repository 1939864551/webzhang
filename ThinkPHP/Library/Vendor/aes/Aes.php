<?php

class AES
{
    //$encodeaeskey 用户的秘钥
    //$userkey      用户的key,类似用户的用户名
    private $encodeaeskey;
    public function __construct($encodeaeskey) {
        $this->key = $encodeaeskey;
    }

    //$data 需要加密的数据
    public function encrypt($data,$userkey){
        $pc = new Prpcrypt($this->key);
        return $pc->encrypt($data, $userkey);
    }

    //$encrypt 加密过后的数据,用来解密
    public function decrypt($encrypt,$userkey) {
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
// class AES
// {
//     const CIPHER = MCRYPT_RIJNDAEL_128; // Rijndael-128 is AES
//     const MODE   = MCRYPT_MODE_CBC;

//     /* Cryptographic key of length 16, 24 or 32. NOT a password! */
//     private $key;
//     public function __construct($key) {
//         $this->key = $key;
//     }

//     public function encrypt($plaintext) {
//         $ivSize = mcrypt_get_iv_size(self::CIPHER, self::MODE);
//         $iv = mcrypt_create_iv($ivSize, MCRYPT_DEV_URANDOM);
//         $ciphertext = mcrypt_encrypt(self::CIPHER, $this->key, $plaintext, self::MODE, $iv);
//         return base64_encode($iv.$ciphertext);
//     }

//     public function decrypt($ciphertext) {
//         $ciphertext = base64_decode($ciphertext);
//         $ivSize = mcrypt_get_iv_size(self::CIPHER, self::MODE);
//         if (strlen($ciphertext) < $ivSize) {
//             throw new Exception('Missing initialization vector');
//         }

//         $iv = substr($ciphertext, 0, $ivSize);
//         $ciphertext = substr($ciphertext, $ivSize);
//         $plaintext = mcrypt_decrypt(self::CIPHER, $this->key, $ciphertext, self::MODE, $iv);
//         return rtrim($plaintext, "\0");
//     }
// }