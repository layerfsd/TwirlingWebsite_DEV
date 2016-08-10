<?php 
$id = $_GET['id'];
//链接数据库
$con = mysql_connect("rdsh144hk908eprp68eb.mysql.rds.aliyuncs.com","demo","123456");
mysql_select_db("demo", $con);
$result = mysql_query("SELECT * FROM vr_video_data where id=$id limit 1");
$r = mysql_fetch_array($result);

$dir = $r['dir'];
$m_video_mp4 = $dir.$r['m_video_mp4'];
$m_video_webm = $dir.$r['m_video_webm'];
$audio_mp3 = $dir.$r['audio_mp3'];
$m_poster_jpg = $dir.$r['m_poster_jpg'];
$pc_video_mp4 = $dir.$r['pc_video_mp4'];
$pc_video_webm = $dir.$r['pc_video_webm'];
$pc_poster_jpg = $dir.$r['pc_poster_jpg'];

$v_1024x512 = "videointerface_addsource('1024x512', '$m_video_mp4|$m_video_webm|$audio_mp3', '$m_poster_jpg');";
$v_1920x960 = "videointerface_addsource('1920x960', '$pc_video_mp4|$pc_video_webm|$audio_mp3', '$pc_poster_jpg');";

//echo $v_1920x960;die;
?>

<!--
	Beijing Tuoling Inc. 2015 - twirlingvr.com
-->
<krpano>
	
	<action name="startup" autorun="onstart">
		if(device.panovideosupport == false,
			error('Sorry, but panoramic videos are not supported by your current browser!');
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
			
			videointerface_addsource('1024x512', './videos/film1_small.mp4|./videos/film1_small.mp4|./videos/film1_small.mp3', './videos/film1_small_poster.jpg');
			videointerface_addsource('1920x960', './videos/film1.mp4|./videos/film1.mp4|', './videos/film1_poster.jpg');

			videointerface_addsource('1024x512', './videos/film1_small.mp4|./videos/film1_small.webm|./videos/film1_small.mp3', './videos/film1_small_poster.jpg');
			videointerface_addsource('1920x960', './videos/film1.mp4|./videos/film1.webm|./videos/iphone-audio.m4a', './videos/film1_poster.jpg');
			
			-->
			videointerface_addsource('1024x512', 'http://yun.twirlingvr.com/oss/down_2016-06-01/35179ac0-7a72-84f2-c311-574e99c8b955_666_1464769191_666_film1_small.mp4|http://yun.twirlingvr.com/oss//up_2016-06-02/film1_small.webm|http://yun.twirlingvr.com/oss/down_2016-06-01/35179ac0-7a72-84f2-c311-574e99c8b955_666_1464769191_666_film1_small.mp3', 'http://yun.twirlingvr.com/oss//down_2016-06-01/35179ac0-7a72-84f2-c311-574e99c8b955_666_1464769191_666_film1_small_poster.jpg');			
			videointerface_addsource('1920x960', 'http://yun.twirlingvr.com/oss/down_2016-06-01/35179ac0-7a72-84f2-c311-574e99c8b955_666_1464769191_666_film1.mp4|http://yun.twirlingvr.com/oss/up_2016-06-02/film1.webm|http://yun.twirlingvr.com/oss/down_2016-06-01/35179ac0-7a72-84f2-c311-574e99c8b955_666_1464769191_666_film1_small.mp3', 'http://yun.twirlingvr.com/oss/down_2016-06-01/35179ac0-7a72-84f2-c311-574e99c8b955_666_1464769191_666_film1_poster.jpg');
			
			
			
			
			<!--动态输出配置代码-->
			<?php //echo $v_1024x512;?>
			<?php //echo $v_1920x960;?>
			
			
			
			if(device.ios,
				<!-- iOS Safari has a very slow 'video-to-webgl-texture' transfer, therefore use a low-res video by default -->
				videointerface_play('1024x512');
			  ,
				videointerface_play('1920x960');
			  );
		</action>

	</scene>

</krpano>
