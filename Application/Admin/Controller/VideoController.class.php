<?php

namespace Admin\Controller;

use Think\Page;
use Think\Model;
use Alidayu\AlidayuClient as Client;
use Alidayu\Request\SmsNumSend;
use Mail\sendmail;
class VideoController extends BaseController {
	public function index() {
	    $Model = M ();
		$search = $_GET ['search']; // 搜索关键词
		$status = $_GET ['status']; // 状态
		import ( 'ORG.Util.Page' ); // 导入分页类
		$map = array (
			'status'=>array('neq',0)
		);
		
		if ($search != null && $search != '') {
			$map ['video_title'] = array (
					'like',
					'%' . $search . '%' 
			);
			$this->search = $search;
			$Page->parameter .= "&search=" . urlencode ( $search );
		}
		
		// -1全部
		if ($status != null && $status != - 1) {
			$map ['status'] = $status;
			$Page->parameter .= "&status=" . urlencode ( $status );
			$this->status = $status;
		} else {
			$this->status = - 1;
		}
		
		$video_cnt = M ( 'video' )->where ( $map )->count ();
		$this->videocnt = $video_cnt;
		$Page = new Page ( $video_cnt, 20 ); // 实例化分页类 传入总记录数和每页显示的记录数
		
		$show = $Page->show (); // 分页显示输出
		$videos = M ( 'video' )->where ( $map )->order ( array (
				'create_time' => 'desc' 
		) )->limit ( $Page->firstRow . ',' . $Page->listRows )->select ();
		
// 		echo 'select * from tl_video limit ' . $Page->firstRow . ',' . $Page->listRows;exit;
		
		foreach ($videos as $k=>$v){
		    //视频总数
		    $sql = "select count(id) as c from tl_video_desc where uuid = '".$v['video_uuid']."' and video_type = 1";
		    $video_cnt = $Model->query ( $sql );
		    //音频总数
		    $sql = "select count(id) as c from tl_video_desc where uuid = '".$v['video_uuid']."' and video_type = 2";
		    $audio_cnt = $Model->query ( $sql );
		    $sql = "select * from tl_user where id = ". $v['user_id'] ;
            $user_info = $Model->query ( $sql );
			//var_dump($user_info);die;
            $videos[$k]['video_cnt'] = $video_cnt[0]['c'];
            $videos[$k]['audio_cnt'] = $audio_cnt[0]['c'];

            if($user_info[0]['register_type'] == 1 || $user_info[0]['register_type'] == 2 || $user_info[0]['register_type'] == 3){
                if($user_info[0]['nick_name'] != ''){
					$user_info[0]['mobile'] = $user_info[0]['nick_name'];
				}
				
            }
			
			//var_dump($user_info);die;
            $videos[$k]['user_info'] = $user_info[0];
//            var_dump($videos[$k]['user_info']);
            $title = $videos[$k]['video_title'];
            $desc = $videos[$k]['video_desc'];
            if(strlen($title) > 18){
            	$title = mb_substr($title,0,10,'utf-8') . "...";
            }
            if(strlen($desc) > 10){
            	$desc = mb_substr($desc,0,10,'utf-8'). "...";
            }
            $videos[$k]['curr_video_title'] = $title;
            $videos[$k]['curr_video_desc'] = $desc;

		}
		//var_dump($videos);die;
		$this->list = $videos;
		$this->show = $show;
		$this->display ();
	}
	/**
	 * 视频详情
	 */
	public function datile() {
		$Model = M ();
		$uuid = $_GET ['uuid'];
		$sql = "select * from tl_video where video_uuid = '" . $uuid . "'";
		$video = $Model->query ( $sql );
		$sql = "select * from tl_video_desc where uuid = '" . $uuid . "' and status = 1";
		$video_desc = $Model->query ( $sql );
		$sql = "select * from tl_order where video_uuid = '" .$uuid . "'";
		$order = $Model->query ( $sql );
		$this->video = $video[0];
		$this->list = $video_desc;
		$this->uuid = $uuid;
		$this->order = $order[0];
		$this->display ();
	}
	//上传合成跳转
	public function goTranslate(){
		$Model = M ();
		$uuid = $_GET ['uuid'];
		
		$sql = "select * from tl_video_translate where video_uuid = '" . $uuid ."' and status != 0";
		$video = $Model->query ( $sql );
		$this->list = $video;
		$this->uuid = $uuid;
		$this->time = time();
		$this->display ("translate");
		
	}
	//确定合成视频
	public function doTranslate(){
		$Model = M ();
		$uuid = $_POST ['uuid'];
		$posts = $_POST;
		//处理文件类型及设备
		$arr = array();
		$keys = array_keys($posts);
		//{a.mp4->arrau(device=>1,file=>1)}
		foreach($posts as $k=>$v){
		    if(strstr($k,"device")){
		        $names = substr($k,12);
		        $names =$posts[$this->getKeys($keys , $names)];
		        $arr[$names]['device'] = $v;
		    }
		    if(strstr($k,"file_type")){
		         $names = substr($k,10);
		         $names =$posts[$this->getKeys($keys , $names)];
		         $arr[$names]['file'] = $v;
		    }
		}
        
		
		foreach ($arr as $k=>$v){
		    $sql = "select count(id) as c from tl_video_translate where  video_uuid ='".$uuid."' and device_type = " . $v['device'] . " and file_type = " .$v['file'] . " and status = 2";
		    $result = $Model->query( $sql );
		    //已存在当前设备当前状态的数据就修改文件的设备及文件类型
		    if($result[0]['c'] > 0){
		        $sql = "update tl_video_translate set device_type = " . $v['device'] . ", file_type = " . $v['file'] . " where video_uuid = '".$uuid."' and file_name = '".$k."' and status != 0";
		        $Model->execute( $sql );
		    }else{
		        $sql = "update tl_video_translate set device_type = " . $v['device'] . ", file_type = " . $v['file'] . " , status = 2 where video_uuid = '".$uuid."' and file_name = '".$k."' and status != 0";
		        $Model->execute( $sql );
		    }
		}
		
		$sql = "update tl_video set status = 3 , tr_time = ".time()." where video_uuid = '".$uuid."'";
		
		$video = $Model->execute( $sql );
		$this->uuid = $uuid;
		
		$sql = "select * from tl_config where type = 1";
		$result = $Model->query($sql);
		$send_arr = array();
		foreach ($result as $k=>$v){
		    if(strstr($v['config_key'] , 'send_mail_')){
		        $send_arr[$v['config_key']] = $v['config_val'];
		    }
		}
		
		$sql = "select u.email as c from tl_video t inner join `tl_user` u on t.`user_id`  = u.`id` where t.video_uuid = '".$uuid."'";
		$result = $Model->query( $sql );
		$mail = $result[0]['c'];
		if($mail){
		    //发送邮件或短信
		    $data = array(
		        'Subject' => '您的项目已更新',
		        'body'    => '亲，您的拓灵云项目已经有更新啦！请登录网站查看。',
		        'to'      => $mail
		    );
		    $mail = new sendmail();
		    $mail->send_config($data , $send_arr);
		}
		
	    $this->sms();
		$this->redirect ( "Admin/Video/index" );
	}
	
	
	function getKeys($arr , $key){
	    foreach ($arr as $k=>$v){
	        if(strstr($v , "file_name_")){
	            $val = substr($v,10);
	            if($val == $key){
	                return $v;
	            }
	        }
	    }
	}
	
	//删除
	public function dodel(){
        $name = $_GET['name'];
        $uuid = $_GET['uuid'];
        $sql = "update tl_video_translate set status = 0 where video_uuid = '" . $uuid . "' and file_name = '". $name . "'"  ;
        $Model = M ();
        $Model->execute( $sql );
        $result = array (
            'success' => true,
            'msg' => "删除成功"
        );
        $json_string = json_encode ( $result );
        echo $json_string;
	}
	
	function sms($number){
	    $client  = new Client;
	    $request = new SmsNumSend;
	    
	    // 短信内容参数
// 	    $smsParams = array(
// 	        'code'    => "111111",
// 	        'product' => '测试的'
// 	    );
	    
	    // 设置请求参数
	    $req = $request->setSmsTemplateCode(' SMS_6770509')
	    ->setRecNum($number)
// 	    ->setSmsParam(json_encode($smsParams))
	    ->setSmsFreeSignName('项目更新')
	    ->setSmsType('normal')
	    ->setExtend('demo');
	    
	    print_r($client->execute($req));
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
}
