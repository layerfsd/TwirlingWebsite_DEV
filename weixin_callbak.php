<?php
/**
 * Created by PhpStorm.
 * User: zhengyi
 * Date: 16/4/3
 * Time: 22:30
 */

//if($_GET['state']!=$_SESSION["wx_state"]){
//    exit("5001");
//}

//$code = 'snsapi_login';

$AppID = 'wxf3c36c13081d1ee2';
$AppSecret = '190e392f64b5305382253042672bd584';
//$url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$AppID.'&secret='.$AppSecret.'&code='.$_GET['code'].'&grant_type=authorization_code';
$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $AppID .
    '&secret=' . $AppSecret .
    '&code=' . $_GET['code'] .
    '&grant_type=authorization_code';


$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_URL, $url);
$json = curl_exec($ch);
curl_close($ch);

$arr = json_decode($json, 1);

//得到 access_token 与 openid
//print_r($arr);

$url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $arr['access_token'] . '&openid=' . $arr['openid'] . '&lang=zh_CN';
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_URL, $url);
$json = curl_exec($ch);
curl_close($ch);
$arr = json_decode($json, 1);
//得到 用户资料
print_r($arr);