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
//        dump($data);
//        dump($insertId);
    }
}