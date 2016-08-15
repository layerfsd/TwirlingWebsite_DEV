<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/10 0010
 * Time: 上午 11:32
 */
namespace Admin\Controller;

use Think\Controller;

class LoginController extends Controller
{
    /**
     * 用户登录验证并返回
     * @param null $email 邮箱
     * @param null $password 密码
     * Created by Dr.Chan<cynmsg@gmail.com>
     */
    public function login($mobile = null, $password = null)
    {
        $User = D('user');
        if ($mobile == null || $password == null) {
            $array['status'] = -1;
            $array['msg'] = "手机或密码不能为空";
            echo json_encode($array, JSON_UNESCAPED_SLASHES);    //JSON_UNESCAPED_SLASHES使url不转义
            exit;
        }
        if ($User->checkMobilePassword($mobile, $password)) {
            //更新登录时间和ip
            //   $data['loginip'] = get_client_ip();
            //   $data['logintime'] = date('Y-m-d H:i:s', time());
            //   $User->where('uid=' . $result['uid'])->save($data);
            //   $token = $User->createToken($result); //创建token
            $array['status'] = 200;
            $array['msg'] = "登录成功";
            //   $array['token'] = $token;
            echo json_encode($array, JSON_UNESCAPED_SLASHES);
            exit;
        } else {
            $array['status'] = 400;
            $array['msg'] = "手机或密码错误";
            echo json_encode($array, JSON_UNESCAPED_SLASHES);
            exit;
        }
    }
}