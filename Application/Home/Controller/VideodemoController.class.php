<?php

/**
 * aliyun
 * @author zhengyi
 * @date    2016-03-9
 * @version 1.0
 */
namespace Home\Controller;

use Think\Controller;
use Oss\sdk;
use Think\Page;
use Think\Model;
use Mail\sendmail;

class VideodemoController extends Controller {
    function videoplay(){ //视频播放页面
        $video_uuid = $_GET['video_uuid'];
        $this->assign('video_uuid', $video_uuid);
        //获取视频信息
        $this->display();
    }

    function get_url_encode($url){
        $a = explode('/',$url);
        $fileName = urlencode($a[4]);
        $url = C('UPLOAD_SITEIMG_OSS')['host'].'/'.$a[3].'/'.$fileName;
        return $url;
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


        header("Content-type: text/xml");


		$time = time();
        echo '<krpano version="1.18" bgcolor="0x000000">
            <!-- the videoplayer interface skin -->
            <include url="/Public/videointerface/skin/videointerface.xml?a='.$time.'" />
            <!-- include the videoplayer plugin and load the video (use a low res video for iOS) -->
            <plugin name="video"
                    url.flash="/Public/viewer/plugins/videoplayer.swf"
                    url.html5="/Public/viewer/plugins/videoplayer.js?a='.$time.'"

                    posterurl.ios="'.$ios_cover_url.'"
                    videourl.ios="'.$ios_video_url.'|'.$ios_mp3_url.'"

                    posterurl.android="'.$Android_cover_url.'"
                    videourl.android="'.$Android_video_url.'"

                    posterurl.desktop="'.$pc_cover_url.'"
                    videourl.desktop="'.$pc_video_url.'"
                    
                    pausedonstart="true"
                    loop="true"
                    enabled="false"
                    zorder="0"
                    align="center" ox="0" oy="0"

                    width.no-panovideosupport="100%"
                    height.no-panovideosupport="prop"

                    onloaded="videointerface_setup_interface(get(name)); setup_video_controls();"
                    onvideoready="videointerface_videoready();"
                    />


            <!-- custom control setup - add items for selecting videos with a different resolution/quality -->
            <action name="setup_video_controls">
                <!-- add  items to the control menu of the videointerface skin -->
                if(device.ios OR device.android,
                videointerface_addmenuitem(configmenu, vqtitle, "分辨率", true, videointerface_toggle_configmenu() );
                videointerface_addmenuitem(configmenu, q1, "1024x512",  false, change_video_file(q1, "'.$ios_video_url.'|'.$ios_mp3_url.'"););
                ,
                videointerface_addmenuitem(configmenu, vqtitle, "分辨率", true, videointerface_toggle_configmenu() );
                videointerface_addmenuitem(configmenu, q2, "2048x1024",  false, change_video_file(q2, "'.$ios_video_url.'"); );
                videointerface_addmenuitem(configmenu, q1, "1024x512",  false, change_video_file(q1, "'.$pc_video_url.'"); );
                );

                <!-- select/mark the current video (see the initial videourl attribute) -->
                if(device.ios OR device.android,
                videointerface_selectmenuitem(configmenu, q1);
                ,
                videointerface_selectmenuitem(configmenu, q2);
                );
            </action>


            <!-- change the video file, but try keeping the same playback position -->
            <action name="change_video_file">
                plugin[video].playvideo("%CURRENTXML%/%2", null, get(plugin[video].ispaused), get(plugin[video].time));
                videointerface_deselectmenuitem(configmenu, q1);
                videointerface_deselectmenuitem(configmenu, q2);
                videointerface_selectmenuitem(configmenu, %1);
            </action>


            <!-- the panoramic video image -->
            <image devices="panovideosupport">
                <sphere url="plugin:video" />
            </image>


            <!-- set the default view - a light fisheye projection -->
            <view hlookat="0" vlookat="0" fovtype="DFOV" fov="110" fovmin="75" fovmax="150" fisheye="0.35" />

            <control mousetype="drag2d" />
            <security cors="use-credentials" />


        </krpano>
        ';

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










