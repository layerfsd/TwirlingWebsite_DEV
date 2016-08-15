<?php

namespace Admin\Controller;

use Think\Page;
use Think\Model;

class ContactController extends BaseController {
	public function index() {
	    

	    $Model = M ();
	    $search = $_GET ['search']; // 搜索关键词
// 	    $status = $_GET ['status']; // 状态
	    import ( 'ORG.Util.Page' ); // 导入分页类
	    $map = array (
	    );
	    
	    if ($search != null && $search != '') {
	        $map ['content'] = array (
	            'like',
	            '%' . $search . '%'
	        );
	        $this->search = $search;
	        $Page->parameter .= "&search=" . urlencode ( $search );
	    }
	    
	    $cnt = M ( 'contact' )->where ( $map )->count ();
	    $this->cnt = $cnt;
	    $Page = new Page ( $cnt, 10 ); // 实例化分页类 传入总记录数和每页显示的记录数
	    $show = $Page->show (); // 分页显示输出
	    $orders = M ( 'contact' )->where ( $map )->order ( array (
	        'ctime' => 'desc'
	    ) )->limit ( $Page->firstRow . ',' . $Page->listRows )->select ();

	    $this->list = $orders;
	    $this->show = $show;
	    $this->display ();
	}
}
