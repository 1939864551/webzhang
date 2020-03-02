<?php
namespace Api\Controller;
use Think\Controller;
use Think\Model;

class ApiencodeController extends CommonController {
    //秘钥
    private $encodeaeskey='95cc7178e7bdfb0b';

    /**
     * api测试接口数据加密返回
     * by chenhaocheng  
     * @return string 加密数据
     */
    public function getEncodeData(){
        $post_data = file_get_contents("php://input");
        if ($this->isJson($post_data)) {
            $encodedata = $this->encodeData($this->encodeaeskey,$post_data);
            echo $encodedata;
            exit();
        }else{
            echo 'false';
        } 
    }

    /**
     * api测试接口数据解密返回
     * by chenhaocheng  
     * @return string 明文数据
     */
    public function getDecodeData(){
        $post_data = file_get_contents("php://input");
        echo $this->decodeData($this->encodeaeskey,$post_data);
    }

    private function isJson($json){
        return is_array( json_decode($json,true) );
    }

    /**
     * 数据解密方法
     * by chenhaocheng  
     * @param string $encodeaeskey 秘钥
     * @param string $str 解密目标数据
     * @return string 明文数据
     */
    private function decodeData($encodeaeskey,$str){
        //$str = '2gq1suqaoxAnpgxzDLddgMDzXmyIddwQ2sH+IcNI6dHljTTxzElWIX8st4NLLb1N';
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

    public function _empty(){
        exit;
    }
    
}