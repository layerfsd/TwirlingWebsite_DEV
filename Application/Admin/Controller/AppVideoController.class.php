<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/12 0012
 * Time: 下午 1:36
 */
class AppVideoController extends Controller
{
    public function makelist($mobile = 'null', $openid = 'null')
    {
        if ($mobile == null) {
            $mobile = 'null';
        }
        $Live = D('app_movies');
        $User = D('user');
        //
        if ($User->checkIsVip($mobile, $openid)) {
            $array['status'] = 200;
            $array['msg'] = "查询成功";
            $array['data'] = $Live->selectVip();
            echo json_encode($array, JSON_UNESCAPED_SLASHES);
            exit;
        }
        //
        if ($Live->selectLive($mobile, $openid)) {
            $array['status'] = 200;
            $array['msg'] = "查询成功";
            $array['data'] = $Live->selectLive($mobile, $openid);
            echo json_encode($array, JSON_UNESCAPED_SLASHES);
            exit;
        }
        $array['status'] = 400;
        $array['msg'] = "查询失败";
        $array['data'] = "";
        echo json_encode($array, JSON_UNESCAPED_SLASHES);
        exit;
    }

}