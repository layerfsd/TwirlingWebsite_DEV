<?php
namespace Admin\Controller;

use Think\Controller;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/12 0012
 * Time: 下午 1:37
 */
class SoftwareController extends Controller
{
    public function makelist()
    {
        $Down = D('software');
        if ($Down->selectAll()) {
            $array['status'] = 200;
            $array['msg'] = "查询成功";
            $array['data'] = $Down->selectAll();
            echo json_encode($array, JSON_UNESCAPED_SLASHES);
            exit;
        } else {
            $array['status'] = 400;
            $array['msg'] = "查询失败";
            $array['data'] = "";
            echo json_encode($array, JSON_UNESCAPED_SLASHES);
            exit;
        }
    }
}