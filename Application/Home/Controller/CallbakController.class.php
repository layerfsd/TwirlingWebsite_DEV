<?php

namespace Home\Controller;

use Think\Controller;

class CallbakController extends Controller {
	
	// 阿里云上传回调
	public function index() {
		file_put_contents('thenewlog.log','666'."\n",FILE_APPEND);
		$authorizationBase64 = "";
		$pubKeyUrlBase64 = "";
		/*
		 * 注意：如果要使用HTTP_AUTHORIZATION头，你需要先在apache或者nginx中设置rewrite，以apache为例，修改 配置文件/etc/httpd/conf/httpd.conf(以你的apache安装路径为准)，在DirectoryIndex index.php这行下面增加以下两行 RewriteEngine On RewriteRule .* - [env=HTTP_AUTHORIZATION:%{HTTP:Authorization},last]
		 */
		$headers = getallheaders ();
		if (isset ( $headers ['Authorization'] )) {
			$authorizationBase64 = $headers ['Authorization'];
		}
		if (isset ( $_SERVER ['HTTP_X_OSS_PUB_KEY_URL'] )) {
			$pubKeyUrlBase64 = $_SERVER ['HTTP_X_OSS_PUB_KEY_URL'];
		}
		if ($authorizationBase64 == '' || $pubKeyUrlBase64 == '') {
			header ( "http/1.1 403 Forbidden" );
		}
		// 2.获取OSS的签名
		$authorization = base64_decode ( $authorizationBase64 );
		// 3.获取公钥
		$pubKeyUrl = base64_decode ( $pubKeyUrlBase64 );
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $pubKeyUrl );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		$pubKey = curl_exec ( $ch );
		if ($pubKey == "") {
			header ( "http/1.1 403 Forbidden" );
			exit ();
		}
		// 4.获取回调body
		$body = file_get_contents ( 'php://input' );
		// 5.拼接待签名字符串
		$authStr = '';
		$path = $_SERVER ['REQUEST_URI'];
		$pos = strpos ( $path, '?' );
		if ($pos === false) {
			$authStr = urldecode ( $path ) . "\n" . $body;
		} else {
			$authStr = urldecode ( substr ( $path, 0, $pos ) ) . substr ( $path, $pos, strlen ( $path ) - $pos ) . "\n" . $body;
		}
		// 处理body内容
		$code = urldecode ( $body );
		$url = explode ( "=", $body );
		$res = explode ( "/", $code );
		$file_arr = explode ( "=", $res [0] );
		$arr = explode ( "_666_", $res [1] );
		
		$date = $file_arr [1];
		$uuid = $arr [0];
		$file_name = $arr [1];
		$type = $this->getFileType ( $file_name );

		//写日志
		file_put_contents('thenewlog.log',$code."\n",FILE_APPEND);
		$urls = C('UPLOAD_SITEIMG_OSS')['host']."/" .$date . "/" . urlencode($res [1]);
		$sql = "insert into  tl_video_desc (status,video_type,aliyun_url,uuid,file_name,create_time)  values(1," . $type . ",'" . $urls . "','" . $uuid . "','" . $file_name . "' , ".time().")";
		
		$Model = M ();
		$Model->execute ( $sql );
		$curl = "http://".C("WEB_HOST")."/plugin/aliyun-api/index.php?type=1&name=" . urlencode($res [1]) . "&uuid=" . $uuid . "&file_name=" . urlencode($file_name) . "&day=" . $date;
		$ch = file_get_contents($curl);
// 		$Model->execute ( "insert into test (a) values('".$curl."')" );
		
		// 6.验证签名
		$ok = openssl_verify ( $authStr, $authorization, $pubKey, OPENSSL_ALGO_MD5 );
		if ($ok == 1) {
			header ( "Content-Type: application/json" );
			$data = array (
					"Status" => "Ok" 
			);
			// $this->writelog ( $data );
			echo json_encode ( $data );
		} else {
			// header("http/1.1 403 Forbidden");
			exit ();
		}
	}
	
	//后台合成上传回调
	public function admin() {
		file_put_contents('thenewlog.log','7777'."\n",FILE_APPEND);
		$authorizationBase64 = "";
		$pubKeyUrlBase64 = "";
		/*
		 * 注意：如果要使用HTTP_AUTHORIZATION头，你需要先在apache或者nginx中设置rewrite，以apache为例，修改 配置文件/etc/httpd/conf/httpd.conf(以你的apache安装路径为准)，在DirectoryIndex index.php这行下面增加以下两行 RewriteEngine On RewriteRule .* - [env=HTTP_AUTHORIZATION:%{HTTP:Authorization},last]
		 */
		$headers = getallheaders ();
		if (isset ( $headers ['Authorization'] )) {
			$authorizationBase64 = $headers ['Authorization'];
		}
		if (isset ( $_SERVER ['HTTP_X_OSS_PUB_KEY_URL'] )) {
			$pubKeyUrlBase64 = $_SERVER ['HTTP_X_OSS_PUB_KEY_URL'];
		}
		if ($authorizationBase64 == '' || $pubKeyUrlBase64 == '') {
			header ( "http/1.1 403 Forbidden" );
		}
		// 2.获取OSS的签名
		$authorization = base64_decode ( $authorizationBase64 );
		// 3.获取公钥
		$pubKeyUrl = base64_decode ( $pubKeyUrlBase64 );
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $pubKeyUrl );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		$pubKey = curl_exec ( $ch );
		if ($pubKey == "") {
			header ( "http/1.1 403 Forbidden" );
			exit ();
		}
		// 4.获取回调body
		$body = file_get_contents ( 'php://input' );
		// 5.拼接待签名字符串
		$authStr = '';
		$path = $_SERVER ['REQUEST_URI'];
		$pos = strpos ( $path, '?' );
		if ($pos === false) {
			$authStr = urldecode ( $path ) . "\n" . $body;
		} else {
			$authStr = urldecode ( substr ( $path, 0, $pos ) ) . substr ( $path, $pos, strlen ( $path ) - $pos ) . "\n" . $body;
		}
		// 处理body内容
		$code = urldecode ( $body );
		$url = explode ( "=", $body );
		$res = explode ( "/", $code );
		$file_arr = explode ( "=", $res [0] );
		$arr = explode ( "_666_", $res [1] );
		$date = $file_arr [1];
		$uuid = $arr [0];
		$file_name = $arr [2];
		$date = $file_arr [1];
		
		$urls = C('UPLOAD_SITEIMG_OSS')['host']."/" .$date . "/" . urlencode($res [1]);
		$sql = "insert into  tl_video_translate (url,file_name,video_uuid,create_time,status,device_type,file_type) values('".$urls."','".$file_name."','".$uuid."',".time().",1,0,0)"; 
		
		
		$Model = M ();
		$Model->execute ( $sql );
        		
		
		
// 		var_dump($ch);

		if(strstr($file_name , "mp4") || strstr($file_name , "mov") || strstr($file_name , "avi")){
		    $curl = "http://".C("WEB_HOST")."/plugin/aliyun-api/index.php?type=2&name=" . urlencode($res [1]) . "&uuid=" . $uuid . "&file_name=" . urlencode($file_name) . "&day=" . $date;
		    $ch = file_get_contents($curl);
//     		$Model->execute ( "insert into test (a) values('".$ch."')" );
		}
		
		// 6.验证签名
		$ok = openssl_verify ( $authStr, $authorization, $pubKey, OPENSSL_ALGO_MD5 );
		if ($ok == 1) {
			header ( "Content-Type: application/json" );
			$data = array (
					"Status" => "Ok" 
			);
			echo json_encode ( $data );
		} else {
			header("http/1.1 403 Forbidden");
			exit ();
		}
	}
	function writelog($str) {
		$open = fopen ( "log.txt", "a" );
		fwrite ( $open, $str );
		fclose ( $open );
	}
	// 获取文件类型
	function getFileType($file_name) {
		$videos = C ( 'videos' );
		$audios = C ( 'audios' );
		
		foreach ( $videos as $k => $v ) {
			if (strstr ( $file_name, $v )) {
				return 1;
			}
		}
		
		foreach ( $audios as $k => $v ) {
			if (strstr ( $file_name, $v )) {
				return 2;
			}
		}
		return - 1;
	}
}
