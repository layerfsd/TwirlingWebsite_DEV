<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/12 0012
 * Time: 下午 1:37
 */
class AudioController extends Controller
{
    public function insert2DB()
    {
        $Audio = D('audio');
        $data = array();
        $data['title'] = $_POST['title'];
        $data['user_id'] = $_POST['userId'];
        $data['coverphoto'] = $_POST['coverphoto'];
        $data['audio_path'] = $_POST['audioPath'];
        $data['content'] = $_POST['content'];
        $data['tag'] = $_POST['tag'];
        $insertId = $Audio->add($data);
        if ($insertId) {
            $array['status'] = 200;
            $array['msg'] = "数据上传成功";
            $data['id'] = $insertId;
            $array['data'] = $data;
            echo json_encode($array, JSON_UNESCAPED_SLASHES);
        } else {
            $array['status'] = 400;
            $array['msg'] = "数据上传失败";
            $array['data'] = "";
        }
//        dump($data);
    }

    public function selectAll()
    {
        $Audio = D('audio');
        if ($Audio->selectAll()) {
            $array['status'] = 200;
            $array['msg'] = "查询成功";
            $array['data'] = $Audio->selectAll();
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