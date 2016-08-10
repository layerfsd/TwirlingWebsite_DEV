<?php 
$video_uuid = $_GET['video_uuid'];
//链接数据库
$con = mysql_connect("rdsh144hk908eprp68eb.mysql.rds.aliyuncs.com","hnylchf","123456");
mysql_select_db("twirling", $con);

//pc封面
$result = mysql_query("SELECT * FROM tl_video_translate where video_uuid='$video_uuid' and device_type = 1 and file_type = 2 limit 1");
$pc_cover = mysql_fetch_array($result);
$pc_cover_url = $pc_cover['url'];
//pc视频
$result = mysql_query("SELECT * FROM tl_video_translate where video_uuid='$video_uuid' and device_type = 1 and file_type = 1 limit 1");
$pc_video= mysql_fetch_array($result);
$pc_video_url = $pc_video['url'];
//pc_mac视频
$result = mysql_query("SELECT * FROM tl_video_translate where video_uuid='$video_uuid' and device_type = 4 and file_type = 1 limit 1");
$pc_mac_video= mysql_fetch_array($result);
$pc_mac_video_url = $pc_mac_video['url'];
//安卓封面
$result = mysql_query("SELECT * FROM tl_video_translate where video_uuid='$video_uuid' and device_type = 2 and file_type = 2 limit 1");
$Android_cover = mysql_fetch_array($result);
$Android_cover_url = $Android_cover['url'];
//安卓视频
$result = mysql_query("SELECT * FROM tl_video_translate where video_uuid='$video_uuid' and device_type = 2 and file_type = 1 limit 1");
$Android_video = mysql_fetch_array($result);
$Android_video_url = $Android_video['url'];
//ios封面
$result = mysql_query("SELECT * FROM tl_video_translate where video_uuid='$video_uuid' and device_type = 3 and file_type = 2 limit 1");
$ios_cover = mysql_fetch_array($result);
$ios_cover_url = $ios_cover['url'];
//ios视频
$result = mysql_query("SELECT * FROM tl_video_translate where video_uuid='$video_uuid' and device_type = 3 and file_type = 1 limit 1");
$ios_video = mysql_fetch_array($result);
$ios_video_url = $ios_video['url'];
//ios音频
$result = mysql_query("SELECT * FROM tl_video_translate where video_uuid='$video_uuid' and device_type = 3 and file_type = 3 limit 1");
$ios_mp3 = mysql_fetch_array($result);
$ios_mp3_url = $ios_mp3['url'];

function replace_url($url){ //替换阿里云地址
	$flog = preg_match('/iphone|android/i', strtolower($_SERVER['HTTP_USER_AGENT']));
	if($flog){
		$new_url = str_replace('yun-twirlingvr.oss-cn-hangzhou.aliyuncs.com','yun.twirlingvr.com/oss', $url);
	}else{
		$new_url = $url;
	}
	return $new_url;
}
$time = time();
$m_video_mp4 = replace_url($Android_video_url).'?t='.$time;
$m_video_webm = replace_url($ios_video_url).'?t='.$time;
$audio_mp3 = replace_url($ios_mp3_url).'?t='.$time;
$m_poster_jpg = replace_url($ios_cover_url).'?t='.$time;
$pc_video_mp4 = replace_url($pc_video_url).'?t='.$time;
$pc_video_webm = replace_url($pc_mac_video_url).'?t='.$time;
$pc_poster_jpg = replace_url($pc_cover_url).'?t='.$time;

//$audio_mp3 = '';
//$m_video_mp4 = '';
//$pc_video_mp4 = '';

$v_1024x512 = "videointerface_addsource('1024x512', '$m_video_mp4|$m_video_webm|$audio_mp3', '$m_poster_jpg');";
$v_1920x960 = "videointerface_addsource('1920x960', '$pc_video_mp4|$pc_video_webm|$audio_mp3', '$pc_poster_jpg');";

//echo $v_1920x960;die;
?>

<!--
	Beijing Tuoling Inc. 2015 - twirlingvr.com
-->
<krpano>
	<security cors="">
		<allowdomain domain="yun-twirlingvr.oss-cn-hangzhou.aliyuncs.com" />
	</security>
	
	<action name="startup" autorun="onstart">
		if(device.panovideosupport == false,
			error("Sorry, but panoramic videos are not supported by your current browser!");
		  ,
			loadscene(videopano);
		  );
	</action>

	<scene name="videopano" title="Twirling VR Player">

		<!-- include the videoplayer interface / skin (with VR support) -->
		<include url="videointerface/skin/videointerface.xml" />

		<!-- include the videoplayer plugin -->
		<plugin name="video"
		        url.html5="%SWFPATH%/plugins/videoplayer.js"
		        url.flash="%SWFPATH%/plugins/videoplayer.swf"
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
		<!--
			格式备份
			videointerface_addsource('1024x512', './videos/film1_small.mp4|./videos/film1_small.webm|./videos/film1_small.mp3', './videos/film1_small_poster.jpg');
			videointerface_addsource('1920x960', './videos/film1.mp4|./videos/film1.webm|./videos/iphone-audio.m4a', './videos/film1_poster.jpg');
			
		-->
		
		
			<!--动态输出配置代码-->
			<?php echo $v_1024x512;?>
			<?php echo "\n";?>
			<?php echo $v_1920x960;?>
			
			if(device.ios,
				<!-- iOS Safari has a very slow "video-to-webgl-texture" transfer, therefore use a low-res video by default -->
				videointerface_play("1024x512");
			  ,
				videointerface_play("1920x960");
			  );
		</action>

	</scene>

</krpano>
