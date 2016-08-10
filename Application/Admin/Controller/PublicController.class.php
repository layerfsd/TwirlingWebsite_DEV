<?php

namespace Admin\Controller;


use Think\Controller;


class PublicController extends Controller {
	public function index() {
// 		$data = array (
// 				'id' => 123,
// 				'name' => 'asaaaaaaa' 
// 		);
// 		// this->date = $date;
// 		$this->data = $data;
// 		$this->name = 'abc';
// 		$this->display ();
		
		
		
	}
	public function login($username = null, $password = null, $verify = null) {
		if (IS_POST) {
			$username = I ( "post.username", "", "trim" );
			$password =   I ( "post.password", "", "trim" ) ;
			if (empty ( $username ) || empty ( $password )) {
				$this->error ( "用户名或者密码不能为空，请重新输入！", U ( C ( 'AUTH_USER_GATEWAY' ) ) );
			}
			$map = array (
					'user_name' => $username,
					'user_pwd' => md5($password),
					'st_status' => 1 
			);
			$userinfo = M ( 'admin' )->where ( $map )->find ();
			if ($userinfo) {
				session ( 'AUTH_KEY' , $userinfo ['id'] );
				session ( 'UserInfo', $userinfo );
				$this->redirect ( 'Admin/Index/index');
			} else {
				$this->error ( "用户名密码错误或者此用户已被禁用！", U ( C ( 'AUTH_USER_GATEWAY' ) ) );
			}
		} else {
			$ModelKey = session ( 'ModelKey.User' );
			if ($this->is_login () && $ModelKey == 1) {
				$this->redirect ( 'Index/index' );
			} else {
				$this->display ();
			}
		}
	}
	
	
	 public function logout(){
		if (!$this->is_login()) {
			echo 1;
// 			echo  U ( C ( 'AUTH_USER_GATEWAY' ) );
// 			exit;
			$this->error ( "尚未登录", "login" );
		}else{
// 			action_log('Admin_Logout', 'User', is_login());
			session ( null );
			if (session (  'AUTH_KEY' )) {
				$this->error ( "退出失败", U ( C ( 'AUTH_USER_GATEWAY' ) ) );
			}else{
				$this->success ( "退出成功！", "login" );
			}
		}
    }
	
	function is_login(){
		$user = session('AUTH_KEY');
		if (empty($user)) {
			return 0;
		} else {
			return session('AUTH_KEY');
		}
	}
}