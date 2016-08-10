<?php

/**
 * aliyun
 * @author wangqiu
 * @date    2016-05-31
 * @version 1.0
 */
namespace Home\Controller;

use Think\Controller;
use Oss\sdk;
use Think\Page;
use Think\Model;
use Mail\sendmail;

class VideodemonewController extends Controller {
    function videoplay(){ //视频播放页面
        $video_uuid = $_GET['video_uuid'];
        $this->assign('video_uuid', $video_uuid);
        //获取视频信息
        $this->display();
    }

    function get_url_encode($url){
		return $url;
		/*
        $a = explode('/',$url);
        $fileName = urlencode($a[4]);
        $url = C('UPLOAD_SITEIMG_OSS')['host'].'/'.$a[3].'/'.$fileName;
        return $url;
		*/
    }

    function getvideoxml(){ //视频播放xml

	
        //获取视频信息
        $video_uuid = $_GET['video_uuid'];
        //echo $video_uuid;die;
        $video_translate = M('video_translate');


        //pc封面
        $pc_cover = $video_translate->where("video_uuid='$video_uuid' and device_type = 1 and file_type = 2")->find();
        $pc_cover_url = $this->get_url_encode($pc_cover['url']);

        //pc视频
        $pc_video = $video_translate->where("video_uuid='$video_uuid' and device_type = 1 and file_type = 1")->find();
        $pc_video_url = $this->get_url_encode($pc_video['url']);
        //安卓封面
        $Android_cover = $video_translate->where("video_uuid='$video_uuid' and device_type = 2 and file_type = 2")->find();
        $Android_cover_url = $this->get_url_encode($Android_cover['url']);

        //安卓视频
        $Android_video = $video_translate->where("video_uuid='$video_uuid' and device_type = 2 and file_type = 1")->find();
        $Android_video_url = $this->get_url_encode($Android_video['url']);

        //ios封面
        $ios_cover = $video_translate->where("video_uuid='$video_uuid' and device_type = 3 and file_type = 2")->find();
        $ios_cover_url = $this->get_url_encode($ios_cover['url']);

        //ios视频
        $ios_video = $video_translate->where("video_uuid='$video_uuid' and device_type = 3 and file_type = 1")->find();
        $ios_video_url = $this->get_url_encode($ios_video['url']);

        //ios音频
        $ios_mp3 = $video_translate->where("video_uuid='$video_uuid' and device_type = 3 and file_type = 3")->find();
        $ios_mp3_url = $this->get_url_encode($ios_mp3['url']);

        if(!$ios_video_url){
            $ios_video_url = $pc_video_url;
        }

        if(!$Android_video_url){
            $Android_video_url = $pc_video_url;
        }
		


        //header("Content-type: text/xml");
		echo '<krpano>
	
	<action name="startup" autorun="onstart">
		if(device.panovideosupport == false,
			error("Sorry, but panoramic videos are not supported by your current browser!");
		  ,
			loadscene(videopano);
		  );
	</action>

	<scene name="videopano" title="Twirling VR Player">

		<!-- include the videoplayer interface / skin (with VR support) -->
		<include url="/Public/videointerface/skin/videointerface.xml" />

		<!-- include the videoplayer plugin -->
		<plugin name="video"
		        url.html5="/Public/viewer/plugins/videoplayer.js"
		        url.flash="/Public/viewer/plugins/videoplayer.swf"
		        pausedonstart="true"
		        loop="true"
		        volume="1.0"
		        onloaded="add_video_sources();"
		        />

		<!-- use the videoplayer plugin as panoramic image source -->
		<image>
			<sphere url="plugin:video" />
		</image>

		<!-- set the default view -->
		<view hlookat="0" vlookat="0" fovtype="DFOV" fov="130" fovmin="75" fovmax="150" distortion="0.0" />

		<!-- add the video sources and play the video -->
		<action name="add_video_sources">
		<!--格式备份
			-->
			videointerface_addsource("1024x512", "http://yun-twirlingvr.oss-cn-hangzhou.aliyuncs.com/down_2016-06-01/48e7dd8f-0ca4-1557-b01c-57282c73f3cf_666_1464715005_666_AudioDemo960X540_4MB.mp4||http://yun-twirlingvr.oss-cn-hangzhou.aliyuncs.com/down_2016-06-01/48e7dd8f-0ca4-1557-b01c-57282c73f3cf_666_1464715005_666_AudioDemo.mp3", "http://yun-twirlingvr.oss-cn-hangzhou.aliyuncs.com/down_2016-06-01/48e7dd8f-0ca4-1557-b01c-57282c73f3cf_666_1464715005_666_AudioDemo960.jpg");
			videointerface_addsource("1920x960", "http://yun-twirlingvr.oss-cn-hangzhou.aliyuncs.com/down_2016-06-01/48e7dd8f-0ca4-1557-b01c-57282c73f3cf_666_1464715005_666_AudioDemo960X540_4MB.mp4||http://yun-twirlingvr.oss-cn-hangzhou.aliyuncs.com/down_2016-06-01/48e7dd8f-0ca4-1557-b01c-57282c73f3cf_666_1464715005_666_AudioDemo.mp3", "http://yun-twirlingvr.oss-cn-hangzhou.aliyuncs.com/down_2016-06-01/48e7dd8f-0ca4-1557-b01c-57282c73f3cf_666_1464715005_666_AudioDemo960.jpg");
			
		
		
			<!--动态输出配置代码-->
			<?php //echo $v_1024x512;?>
			<?php //echo $v_1920x960;?>
			
			if(device.ios,
				<!-- iOS Safari has a very slow "video-to-webgl-texture" transfer, therefore use a low-res video by default -->
				videointerface_play("1024x512");
			  ,
				videointerface_play("1920x960");
			  );
		</action>

	</scene>

</krpano>';

				
    }
    

    function changeTimeType($seconds){
        if ($seconds>3600){
            $hours = intval($seconds/3600);
            $minutes = $seconds600;
            $time = $hours."时".gmstrftime('%M分%S秒', $minutes);
        }else{
            $time = gmstrftime('%H时%M分%S秒', $seconds);
        }
        return $time;
    }
	
}










