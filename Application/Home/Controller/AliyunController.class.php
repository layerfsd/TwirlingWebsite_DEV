<?php
/**
 * aliyun
 * @author wangqiu
 * @date    2016-03-9
 * @version 1.0
 */
namespace Home\Controller;

use Think\Controller;
use Oss\sdk;

class AliyunController extends Controller
{

    /**
     * 文件上传demo頁面
     */
    public function index()
    {
        $this->display();
    }

    /*
     * 文件上传处理
     */
    public function get()
    {

        $id = C("UPLOAD_SITEIMG_OSS")["driverConfig"]['AccessKeyId'];
        $key = C("UPLOAD_SITEIMG_OSS")["driverConfig"]['AccessKeySecret'];
        $host = C('UPLOAD_SITEIMG_OSS')['host'];
        $callbackUrl = C("UPLOAD_SITEIMG_OSS")["callbackUrl"];
        $callbackHost = C("WEB_HOST");
        $callback_body = '{"callbackUrl":"' . $callbackUrl . '",
                                        "callbackHost":"' . $callbackHost . '",
                                        "callbackBody":"filename=${object}",
                                        "callbackBodyType":"application/x-www-form-urlencoded"}';

        //var_dump($id, $key, $host, $callbackUrl, $callbackHost,$callback_body);die;
        $base64_callback_body = base64_encode($callback_body);
        $now = time();
        $expire = 30; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
        $end = $now + $expire;
        $expiration = $this->gmt_iso8601($end);

        $oss_sdk_service = new sdk($id, $key, $host);
        $dir = "up_" . date("Y-m-d") . "/";

        //最大文件大小.用户可以自己设置
        $condition = array(0 => 'content-length-range', 1 => 0, 2 => 1048576000);
        $conditions[] = $condition;

        //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
        $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
        $conditions[] = $start;


        $arr = array('expiration' => $expiration, 'conditions' => $conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        $response = array();
        $response['accessid'] = $id;
        $response['host'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['callback'] = $base64_callback_body;
        //这个参数是设置用户上传指定的前缀
        $response['dir'] = $dir;
        echo json_encode($response);
    }

    public function gmt_iso8601($time)
    {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }


}
