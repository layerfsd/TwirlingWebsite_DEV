<?php
include("Application/Common/Conf/config.php");
/**
 * Created by PhpStorm.
 * User: zhengyi
 * Date: 16/4/3
 * Time: 22:22
 */

//-------配置
$AppID = 'wxf3c36c13081d1ee2';
$AppSecret = '190e392f64b5305382253042672bd584';
$callback = 'http://' . $config["WEB_HOST"] . '/weixin_callbak.php'; //回调地址

//echo $AppID;exit;

//微信登录
session_start();
//-------生成唯一随机串防CSRF攻击
$state = md5(uniqid(rand(), TRUE));
$_SESSION["wx_state"] = $state; //存到SESSION
$callback = urlencode($callback);
$wxurl = "https://open.weixin.qq.com/connect/qrconnect?appid=" . $AppID .
    "&redirect_uri=" . $callback .
    "&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect";
header("Location: $wxurl");



