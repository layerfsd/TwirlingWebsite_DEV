<?php
/*
 * 第三方登录回调处理
 * */
namespace Home\Controller;
use Think\Controller;
class WeixinController extends Controller {



    public function index(){
        $weixin_config = C('weixin');

        $AppID = $weixin_config['APPID'];
        $AppSecret = $weixin_config['APPSECRET'];
        $callback  =  $weixin_config['CALLBACK'];

        //微信登录
        session_start();
        //-------生成唯一随机串防CSRF攻击‰
        $state  = md5(uniqid(rand(), TRUE));
        $_SESSION["wx_state"]    =   $state; //存到SESSION
        $callback = urlencode($callback);
        $wxurl = "https://open.weixin.qq.com/connect/qrconnect?appid=".$AppID."&redirect_uri=".$callback."&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect";
        header("Location: $wxurl");

    }

    public function callbak(){
        $weixin_config = C('weixin');
        $AppID = $weixin_config['APPID'];
        $AppSecret = $weixin_config['APPSECRET'];
        $callback  =  $weixin_config['CALLBACK'];
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$AppID.'&secret='.$AppSecret.'&code='.$_GET['code'].'&grant_type=authorization_code';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);

        $arr=json_decode($json,1);



        $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$arr['openid'].'&lang=zh_CN';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        $arr=json_decode($json,1);
        if($arr['unionid'] == null || $arr['unionid'] == ''){
            $this->error("登陆错误，请与系统管理员联系",'',1);
        }

        //判断账号是否存在，存在则使用当前userid，如果不存在则新建账户。按照用户的unionid查询
        $Model = M ();
        $sql = "select * from tl_user where openid = '".$arr['unionid']."'";
        $result = $Model->query ( $sql );
        if(!$result){ //不存在增加账号
            $sql = "insert into tl_user (nick_name,create_time,last_update_time,status,openid,register_type) VALUES ('".$arr['nickname']."',".time().",".time().",1,'".$arr['unionid']."',2)";
            $Model->execute ( $sql );
            $sql = "select * from tl_user where openid = '".$arr['unionid']."'";
            $result = $Model->query ( $sql );
            
            session('tl_nickname',$result[0]['nick_name']);
            session('tl_userid',$result[0]['id']);
            redirect('/home/user/bindphone', 0, '');
            
        }else{
        	if($result[0]['mobile']){
        		session('tl_nickname',$result[0]['nick_name']);
        		session('tl_userid',$result[0]['id']);
        		redirect('/home/index/index', 0, '');
        	}else{
        		session('tl_nickname',$result[0]['nick_name']);
        		session('tl_userid',$result[0]['id']);
        		redirect('/home/user/bindphone', 0, '');
        	}
        	
        }

      

    }


}
