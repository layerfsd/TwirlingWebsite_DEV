<?php
namespace Admin\Controller;

use Think\Controller;

class BaseController extends Controller
{

    //会员中心 核心继承
    protected function _initialize()
    {
        $config = S('DB_CONFIG_DATA');
        if (!$config) {
            $config = S('DB_CONFIG_DATA');
        }
        C($config);
        /* 如果有用户登录，读取用户信息 */
        if (session('AUTH_KEY') > 0) {
            $this->UserInfo = session('UserInfo');
            $this->assign('UserInfo', $this->UserInfo);
        }

        $AUTH_KEY = session('AUTH_KEY');
// 		判断如果auth_key等于空则跳到登陆网关
        if ($AUTH_KEY == null || $AUTH_KEY == '') {
// 			redirect(U('Admin/Public/index'));
            $this->redirect('Admin/Public/login');
        }
    }
}