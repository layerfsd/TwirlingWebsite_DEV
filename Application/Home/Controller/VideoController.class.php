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

class VideoController extends Controller {
	function __construct() {
		parent::__construct();
		if (!session('tl_userid'))
		{
			$this->success('请登录', U('home/index/login'));
			die;
		}
	}
	/**
	 * 文件上传首页
	 */
	public function index() {
		$Model = M ();
		$user_id = session('tl_userid');
		
		$sql = "select * from tl_user where id = ". $user_id;
		$user_info = $Model->query ( $sql );
		
		if($user_info[0]['mobile'] == null || $user_info[0]['mobile'] == ''){
			redirect('/home/user/bindphone', 0, '');
		}
		
		$user_id = session('tl_userid');
		$sql = "select count(id) as c from tl_video where user_id = " . $user_id;
		$video_cnt = $Model->query ( $sql );
		if(intval($video_cnt[0]['c']) > 0){	//上传过视频跳转到列表页
			$this->display ( "list" );
		}else{
			$this->display ();
		}
	}
	
	//视频列表
	public function golist(){
		$status = $_GET['status']; 		//2合成中 3已合成 
		$Model = M ();
		import ( 'ORG.Util.Page' ); // 导入分页类
		$user_id = session('tl_userid');		
		
		$sql = "select * from tl_user where id = ". $user_id;
		$user_info = $Model->query ( $sql );
		
		if($user_info[0]['mobile'] == null || $user_info[0]['mobile'] == ''){
			redirect('/home/user/bindphone', 0, '');
		}
		
		$status_where = "";
		$sql_cnt = "select count(id) as c from tl_video where status = " . $status . " and user_id = " . $user_id ;
		$video_cnt_result = $Model->query ( $sql_cnt );
		$video_cnt = $video_cnt_result[0]['c'];
		$this->videocnt = $video_cnt;
		
		$Page = new Page ( $video_cnt, 10 ); // 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show (); // 分页显示输出
		
		
		$video_sql = "select * from tl_video where status = " . $status . " and user_id = " . $user_id . " order by create_time desc limit " . $Page->firstRow . ',' . $Page->listRows;
		$videos = $Model->query ( $video_sql );
		if ($status != null ) {
			$Page->parameter .= "&status=" . urlencode ( $status );
			$this->status = $status;
		} else {
			$this->status = 2;	//默认合成中
		}
		
		foreach ($videos as $k=>$v){
			//视频总数
			$sql = "select count(id) as c from tl_video_desc where uuid = '".$v['video_uuid']."' and video_type = 1";
			$video_cnt = $Model->query ( $sql );
			if($status == 3){
			    $sql = "select sum(video_whenlong) as c from `tl_video_translate` where `file_type`  = 1 and status = 2 order by `device_type` limit 1";
			    $video_whenlong_cnt = $Model->query ( $sql );
			}else{
			    $sql = "select sum(video_whenlong) as c from tl_video_desc where uuid = '".$v['video_uuid']."' and video_type = 1";
			    $video_whenlong_cnt = $Model->query ( $sql );
			}
			
			//音频总数
			$sql = "select count(id) as c from tl_video_desc where uuid = '".$v['video_uuid']."' and video_type = 2";
			$audio_cnt = $Model->query ( $sql );
// 			$sql = "select * from tl_user where id = ". $v['user_id'] ;
// 			$user_info = $Model->query ( $sql );
			$sql = "select * from tl_order where video_uuid = '".$v['video_uuid']."'";
			$order_info = $Model->query ( $sql );
			
			if($v['status'] != 3){
    			//获取第一个视频或音频的url地址
    			$sql = "select  aliyun_url  from tl_video_desc where uuid = '" . $v['video_uuid'] . "' order by video_type asc limit 0 , 1";
    			$url_info = $Model->query ( $sql );
    			$videos[$k]['aliyun_url'] = $url_info[0]['aliyun_url'];
			}
			//已合成,抓取一个视频文件，默认以pc为最高优先级
			if($v['status'] == 3){
			    $sql = "select * from `tl_video_translate` where `file_type`  = 2 and status = 2 and video_uuid = '" . $v['video_uuid'] . "' order by `device_type` limit 1";
			    $url_info = $Model->query ( $sql );
			    $videos[$k]['aliyun_url'] = $url_info[0]['url'];
			}
			
			$videos[$k]['video_long'] = $this->changeTimeType($video_whenlong_cnt[0]['c']);
			$videos[$k]['video_cnt'] = $video_cnt[0]['c'];
			$videos[$k]['audio_cnt'] = $audio_cnt[0]['c'];
			$videos[$k]['user_info'] = $user_info[0];
			$videos[$k]['order_info'] = $order_info[0];
		  
			
		}
       // var_dump($videos);die;
		$this->list = $videos;
		$this->show = $show;
		$this->display ("list");
		
	}
	
	// 添加视频项目
	public function goAdd() {
		
		$userid = $this->user_id = session('tl_userid');
		//判断用户是否已经上传了资质并且审核通过
		$user = M('user');
		$userinfo = $user->where("id = '$userid'")->find();
		/* 暂时隐藏掉控制用户上传的功能
		if($userinfo['zizhi_status'] != 2){
			redirect('/home/user/zizhi', 0, '');
		}*/
		$this->uuid = $this->guid();
		$this->display ( "add" );
	}
	
	
	
	
	// 添加视频
	public function doAdd() {
		$uuid = $_POST ['uuid'];
		$title = array (); // 视频标题
		$desc = array (); // 视频描述
		$filenames = array(); //文件名
		$file_arr = array();
		foreach ( $_POST as $k => $v ) {
			if (strstr ( $k, '|' )) { // 包含特定符号为描述
				$title_desc = explode ( '|', $k );
				$id = $title_desc[0];
				$key = $title_desc[1];
				if($key == 'video_desc'){
				    $file_arr[$id]['desc'] = $v;
				}else if($key == 'video_title'){
				    $file_arr[$id]['title'] = $v;
				}else if($key == 'file_name'){
					echo $v;
				   $file_arr[$id]['name'] = $v;
				}
			}
		}
		
		
		
		$user_id =  session('tl_userid');
		$Model = M ();
		// 插入主表
		$insert = "insert into tl_video (user_id,video_uuid,status,create_time) values(" . $user_id . ",'" . $uuid . "',1," . time () . ")";
		$result = $Model->execute ( $insert );
		foreach ( $file_arr as $k => $v ) {
			$update = "update tl_video_desc set status = 1,  user_id = ".$user_id.", video_title = '".$v['title']."' , video_desc ='".$v['desc']."' where uuid = '".$uuid."' and file_name = '".$v['name']."'";
			$Model->execute($update);
		}
		
		//获取本次上传的音视频总数
		$video_cnt_sql = "select count(id) as c from tl_video_desc where uuid = '" . $uuid . "' and video_type = 1";
		$audio_cnt_sql = "select count(id) as c from tl_video_desc where uuid = '" . $uuid . "' and video_type = 2";
		$video_cnt = $Model->query ( $video_cnt_sql );
		$audio_cnt = $Model->query ( $audio_cnt_sql );		
		
		if ($result) {
			$this->redirect ( 'Home/Video/goVideoNext?uuid=' . $uuid . "&vct=" .$video_cnt[0]['c'] . "&act=".$audio_cnt[0]['c']  );
		}
	}
	
	// 填写视频标题描述信息
	public function goVideoNext() {
		$uuid = $_GET ['uuid'];
		$vct = $_GET['vct'];
		$act = $_GET['act'];
		
		$this->uuid = $uuid;
		$this->vct = $vct;
		$this->act = $act;
		
		$this->display ( "next" );
	}
	
	// 填写视频标题描述信息
	public function doVideoNext() {
	    $uuid = $_POST ['uuid'];
	    $title = $_POST['title'];
	    $desc = $_POST['desc'];
	    $sql = "update tl_video set video_title = '".$title."' , video_desc = '".$desc."' where video_uuid = '". $uuid . "'";
	    $Model = M ();
	    $result = $Model->execute($sql);
	   
	    $mail = new sendmail();
	    $sql = "select * from tl_config where type = 1";
	    $result = $Model->query($sql);
        $send_arr = array();
        $to_arr = array();
        
        foreach ($result as $k=>$v){
            if(strstr($v['config_key'] , 'send_mail_')){
                $send_arr[$v['config_key']] = $v['config_val'];
            }else if($v['config_key'] == 'to_mail_name'){
                array_push($to_arr , $v['config_val']);
            }
        }
	    foreach ($to_arr as $k=>$v){
	        $data = array(
	            'Subject' => '有新的合成任务',
	            'body'    => '已有新的合成任务被上传完成，请登录后台查看！',
	            'to'      => $v,
	        );
	        $mail->send_config($data , $send_arr);
	    }
	   
	    $this->redirect ( "Home/Video/golist?status=1" );
	}
	
	
	public function downVideo(){
		$id = $_GET['id'];
		$file_name = C('UPLOAD_SITEIMG_OSS')['host']."/2016-03-15/c16ca4de-db90-6181-3beb-56e78ed3ddaf%40%23%401458045527%40%23%401%20-%20%E5%89%AF%E6%9C%AC.mp4";
		header('Content-Type:text/html; charset=utf-8');
		header('Content-Disposition:attachment; filename=' . $file_name);
	}
	
	//添加到array
	//array{'fileid'=>array('title','desc','name')}
	 function addArray($array , $id , $key, $val){
	    $flag = $this->array_exists($array , $id);
	    if($flag){
    	    foreach ($array as $k=>$v){
    	    	echo $k;
    	    	echo '</br>';
                if($k == $key){
                	$array[$k][$key] = $val;
                }	        
    	    }
	    }else{
	       $array[$id] = array($key => $val);
	    }
	    return $array;
	} 
	
	//删除
	public function doDel(){
	    
	    $id = $_GET['id'];
	    $sql = "update tl_video set status = 0 where video_uuid = '".$id."'";
	    $Model = M ();
	    $result = $Model->execute($sql);
	   if($result){
            $result = array (
                'success' => true,
                'msg' => "修改成功"
            );
            $json_string = json_encode ( $result );
            echo $json_string;
        }else{
            $result = array (
                'success' => false,
                'msg' => "修改失败"
            );
            $json_string = json_encode ( $result );
            echo $json_string;
        }
	    
	}
	
	//修改
	public function doedit(){
	    $uuid = $_POST['id'];
        $title = $_POST['title'];
        $desc = $_POST['desc'];
        $sql = "update tl_video set video_title = '".$title."' , video_desc = '".$desc."' where video_uuid = '".$uuid."'";
        $Model = M ();
        $result = $Model->execute($sql);
        if($result){
            $result = array (
                'success' => true,
                'msg' => "修改成功"
            );
            $json_string = json_encode ( $result );
            echo $json_string;
        }else{
            $result = array (
                'success' => false,
                'msg' => "修改失败"
            );
            $json_string = json_encode ( $result );
            echo $json_string;
        }
	}
	
	
	
	public function array_exists($arrya , $key){
	    foreach ($arrya as $k=>$v){
	        if($k == $key){
	            return true;
	        }
	    }
	    return false;
	}
	
	private function guid() {
		$microTime = microtime ();
		list ( $a_dec, $a_sec ) = explode ( " ", $microTime );
		$dec_hex = dechex ( $a_dec * 1000000 );
		$sec_hex = dechex ( $a_sec );
		$this->ensure_length ( $dec_hex, 5 );
		$this->ensure_length ( $sec_hex, 6 );
		$guid = "";
		$guid .= $dec_hex;
		$guid .= $this->create_guid_section ( 3 );
		$guid .= '-';
		$guid .= $this->create_guid_section ( 4 );
		$guid .= '-';
		$guid .= $this->create_guid_section ( 4 );
		$guid .= '-';
		$guid .= $this->create_guid_section ( 4 );
		$guid .= '-';
		$guid .= $sec_hex;
		$guid .= $this->create_guid_section ( 6 );
		return $guid;
	}
	
	function ensure_length(&$string, $length){
		$strlen = strlen($string);
		if($strlen < $length)
		{
			$string = str_pad($string,$length,"0");
		}
		else if($strlen > $length)
		{
			$string = substr($string, 0, $length);
		}
	}
	
	function create_guid_section($characters){
		$return = "";
		for($i=0; $i<$characters; $i++)
		{
		$return .= dechex(mt_rand(0,15));
		}
		return $return;
	}

    function down_load(){
        header("Content-type: text/html; charset=utf-8");
        $uuid = $_GET['uuid'];
        $order = M('video_translate');
        $r = $order->where("video_uuid = '$uuid' and device_type = 1 and file_type = 1")->find();
        $url = $r['url'];
        $file_name = $r['file_name'];

        //下载显示的名字
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        readfile("$url");
        exit();

    }

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



        echo '<krpano version="1.18" bgcolor="0x000000">
            <!-- the videoplayer interface skin -->
            <include url="/Public/videointerface/skin/videointerface.xml" />
            <!-- include the videoplayer plugin and load the video (use a low res video for iOS) -->
            <plugin name="video"
                    url.flash="%SWFPATH%/plugins/videoplayer.swf"
                    url.html5="%SWFPATH%/plugins/videoplayer.js"

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
                videointerface_addmenuitem(configmenu, q1, "1024x512",  false, change_video_file(q1, "../videos/film1_small.mp4|../videos/film1_small.mp3"););
                ,
                videointerface_addmenuitem(configmenu, vqtitle, "分辨率", true, videointerface_toggle_configmenu() );
                videointerface_addmenuitem(configmenu, q2, "2048x1024",  false, change_video_file(q2, "../videos/film1.mp4"); );
                videointerface_addmenuitem(configmenu, q1, "1024x512",  false, change_video_file(q1, "../videos/film1_small.mp4"); );
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










