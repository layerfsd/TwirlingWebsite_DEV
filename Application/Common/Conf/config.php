<?php

define('OSS', 'http://*.oss-cn-hangzhou.aliyuncs.com/'); //把*替换成对应的Bucket 由于经常用到该链接，所以定义成常量

$config = array(
    //网站域名
    'WEB_HOST' => 'yun-dev.twirlingvr.com',
    //'配置项'=>'配置值'
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => 'rdsh144hk908eprp68eb.mysql.rds.aliyuncs.com', // 服务器地址
    'DB_NAME' => 'twirling_dev', // 数据库名
    'DB_USER' => 'hnylchf', // 用户名
    'DB_PWD' => '123456', // 密码
    'DB_PORT' => 3306, // 端口
    'DB_PREFIX' => 'tl_', // 数据库表前缀
    'DB_CHARSET' => 'utf8', // 字符集

    //大鱼短信配置
    'AlidayuAppKey' => '23319075',  // app key
    'AlidayuAppSecret' => 'a1a0ab50ea5e417ce402837fe07fc722',  // app secret
    'AlidayuApiEnv' => 1, // api请求地址，1为正式环境，0为沙箱环境

    //发邮件配置
    'mail_mail_Port' => '25',
    'mail_Host' => 'smtp.mxhichina.com',
    'mail_Username' => 'notice@twirlingvr.com',
    'mail_Password' => 'Tt@123456',
    'mail_From' => 'notice@twirlingvr.com',
    'mail_FromName' => 'twirlingvr',

    //阿里云 oss 配置
    'UPLOAD_SITEIMG_OSS' => array(
        'host' => 'http://yun-twirlingvr.oss-cn-hangzhou.aliyuncs.com',
        'callbackUrl' => 'http://yun-dev.twirlingvr.com/Home/Callbak/index',
        'maxSize' => 5 * 1024 * 1024 * 1024,//文件大小
        'rootPath' => './',
        'saveName' => array('uniqid', ''),
        'savePath' => 'aliyun/',    //保存路径
        'driver' => 'Aliyun',
        'driverConfig' => array(
            'AccessKeyId' => 'PUZ0Nv5kjtoob96T',    //AccessKeyId
            'AccessKeySecret' => 'NRX92VHfTvE1A9MoFNjEWNpFwKOM5p',//AccessKeySecret
            'domain' => OSS,        //
            'Bucket' => 'yun-dev-twirlingvr',         //Bucket
            'Endpoint' => 'http://oss-cn-hangzhou.aliyuncs.com',
        ),
    ),
    'videos' => array('mp4', 'mov', 'avi'),       //支持的视频格式
    'audios' => array('aac', 'mp3', 'flac', 'ogg', 'wav', 'aiff', 'm4a'),       //支持的音频格式


    //支付宝配置参数
    'alipay_config' => array(
        'partner' => '2088122836413345',   //这里是你在成功申请支付宝接口后获取到的PID；
        'key' => 'ladmyz2t15spgp7j09c306pfmwxs5goj',//这里是你在成功申请支付宝接口后获取到的Key
        'sign_type' => strtoupper('MD5'),
        'input_charset' => strtolower('utf-8'),
        'cacert' => getcwd() . '\\cacert.pem',
        'transport' => 'http'
    ),

    'alipay' => array(
        //这里是卖家的支付宝账号，也就是你申请接口时注册的支付宝账号
        'seller_email' => 'twirlingvr@163.com',
        //这里是异步通知页面url，提交到项目的Pay控制器的notifyurl方法；
        'notify_url' => 'http://yun-dev.twirlingvr.com/index.php/home/alipay/notifyurl',
        //这里是页面跳转通知url，提交到项目的Pay控制器的returnurl方法；
        'return_url' => 'http://yun-dev.twirlingvr.com/index.php/home/alipay/returnurl',
        //支付成功跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参payed（已支付列表）
        'successpage' => 'http://yun-dev.twirlingvr.com/index.php/home/alipay/successpage',
        //支付失败跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参unpay（未支付列表）
        'errorpage' => 'http://yun-dev.twirlingvr.com/index.php/home/alipay/errorpage',
    ),

    'weixin' => array(
        'APPID' => 'wxf3c36c13081d1ee2',
        'APPSECRET' => '190e392f64b5305382253042672bd584',
        'CALLBACK' => 'http://yun-dev.twirlingvr.com/Home/weixin/callbak'
    ),

    'URL_ROUTER_ON' => true,
    'URL_ROUTE_RULES' => array(
        'api/:table/[:field]/[:id]' => array('api/index'),
    ),

);


//如果是本地测试环境修改数据库配置
if ($_SERVER['HTTP_HOST'] == "localhost") {
    $config['DB_TYPE'] = 'mysql'; // 数据库类型
    $config['DB_TYPE'] = 'mysql';
    $config['DB_HOST'] = 'localhost';
    $config['DB_USER'] = 'root';
    $config['DB_PWD'] = 'hnylchf';
    $config['DB_PORT'] = '3306';
    $config['DB_PREFIX'] = 'tl_';
    $config['DB_CHARSET'] = 'utf8';
}

return $config;
