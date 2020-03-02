<?php
namespace Android\Controller;
use Think\Controller;
class IndexController extends Controller {
	//跳转至Home平台下的index/index方法中
	public function index(){
		$this->redirect('Mare/Index/index');
	}

    private function test1(){
       // tuxing();
        xxxx();
        // circularStatistics(array(1,2,4));
    }
}