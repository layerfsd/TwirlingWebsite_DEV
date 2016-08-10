<?php

namespace Admin\Controller;

use Think\Controller;

class IndexController extends BaseController {
	public function index() {
	   
// 		$this->display ();
	    $this->redirect ( "Admin/Video/index" );
	}
	public function test1() { // 链接数据库测试
		session();
		$Model = M ();
		$sql = "select * from tl_us";
		$r = $Model->query ( $sql );
		var_dump ( $r );
		die ();
		$this->display ();
	}
	public function test2() { // 模板测试
		$this->display();
	}
}
