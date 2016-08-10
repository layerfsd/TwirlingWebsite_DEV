<?php
/*
 * qq登录逻辑处理
 *
 * */
namespace Home\Controller;
use Think\Controller;
use Alidayu\AlidayuClient as Client;
use Alidayu\Request\SmsNumSend;

class QqController extends Controller {

    public function qqlogin(){ //qq登录业务逻辑
        $openId = $_POST['openId'];
        $nickname = $_POST['nickname'];

        //查询库中是否存在qq用户 openid
        $user = M('user');
        $r_user = $user->where(" openid = '$openId' ")->find();

        if($r_user){ //如果存在用户就执行登录操作
            session('tl_userid',$r_user['id']);
            session('tl_nickname',$nickname);
        }else{ //不存在执行注册操作
            $data = array(
                'nick_name' => $nickname,
                'openid' => $openId,
                'create_time' => time(),
                'last_update_time' => time(),
                'register_type' => 0
            );
            $r = $user->add($data);

            session('tl_userid',$r);
            session('tl_nickname',$nickname);
        }
    }
}
