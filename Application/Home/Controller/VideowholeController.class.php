<?php
/*
 * 视频
 * */
namespace Home\Controller;
use Think\Controller;
class VideowholeController extends Controller {
	
    public function video(){
		$video_uuid = $_GET['video_uuid'];
		$video = M('video');
		
        $list = $video->where("user_id = '35'")->order('create_time desc')->select();
		//
        foreach($list as $k=>$v){
			$video_translate = M('video_translate');
			$r = $video_translate->where("video_uuid = '".$v['video_uuid']."' and device_type = 1 and file_type = 2 and status = 2")->find();
			$list[$k]['pic_url'] = $r['url'];
		}

		$this->assign('list', $list);
        $this->display();
    }
    public function broadcast(){
        $this->display();
    }

}

