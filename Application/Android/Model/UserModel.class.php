<?php
namespace Home\Model;
use Think\Model;

class UserModel extends Model{

	protected $_validate = array(
		array('captcha','require','验证码必须'),
		);


}
