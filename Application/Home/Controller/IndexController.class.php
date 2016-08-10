<?php
/*
 * 网站页面
 * */
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){ //网站首页
        //一次性引导
        if(session('tl_userid')){ //登录状态
            $userid = session('tl_userid');
            //获取用户
            $user = M('user');
            $userinfo = $user->where("id = '$userid'")->find();
            if($userinfo['ishelp'] == 0){ //还没有提示过
                $ishelp = '<script>$t.guide();</script>';
                //更新状态为已经提示过
                $r = $user->where("id = '$userid'")->save(array('ishelp'=>1));
                //var_dump($userid, $r);die;
            }else{
                $ishelp = '';
            }
        }
        $this->assign('ishelp', $ishelp);
        $this->display();
    }

    public function reg(){ //用户注册
        $this->display();
    }

    public function reged(){ //注册成功页面
        $mobile = I('get.mobile','','htmlspecialchars');
        $this->assign('mobile', $mobile);
        $this->display();
    }

    public function login(){ //用户注册登录
        //如果已经登录了就跳转到首页
        if(session('tl_userid')){
            redirect('/home/index/index', 0, '');
        }
        $this->display();
    }

    public function lost(){ //忘记密码
        $this->display();
    }

    public function resetpass(){ //重置密码
        $this->display();
    }

    public function contact(){ //我要咨询
        $this->display();
    }



	public function test1(){ //链接数据库测试

		$Model = M();
        //var_dump($Model);die;
		$sql = "select * from tl_user";
		$r = $Model->query($sql);

		var_dump($r);
		die;
		$this->display();
	}

    public function test2(){ //模板测试

        $this->display();
    }

    public function videptest(){ //视频播放器测试
        $this->display();
    }

    public function test_weixin(){
        $this->display("weixin");
    }
	
	public function about(){
        $this->display();
    }

    public function download(){
    	$this->display();
    }
	public function fuhua(){
		if(session('tl_userid')){
            redirect('/home/video/golist/status/3.html', 0, '');
        }
    	$this->display();
    }
	
	public function admin_fabuhui(){
		if ($_SERVER['PHP_AUTH_USER'] != 'admin' || $_SERVER['PHP_AUTH_PW'] != '123') {
		header('WWW-Authenticate: Basic Realm="Secret Stash"');
		header('HTTP/1.0 401 Unauthorized');
		header('Content-Type:text/html; charset=utf-8');
		print('必须输入密码才能查看!');
		exit;
}
		$Model = M();
        //var_dump($Model);die;
		$sql = "select * from tl_index_invite order by id desc";
		$list = $Model->query($sql);
		$this->assign('list', $list);
        $this->display();
    }

}
