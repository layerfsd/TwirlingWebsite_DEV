<?php
/*
 * 逻辑处理
 *
 * */
namespace Home\Controller;

use Think\Controller;
use Alidayu\AlidayuClient as Client;
use Alidayu\Request\SmsNumSend;
use Mail\sendmail;

class DoController extends Controller
{

    public function reg()
    { //用户注册请求处理

        $User = new \Home\Model\UserModel();

        $data['mobile'] = I('post.mobile', '', 'htmlspecialchars');
        $data['user_pwd'] = I('post.user_pwd', '', 'htmlspecialchars');
        $data['user_pwd_2'] = I('post.user_pwd_2', '', 'htmlspecialchars');
        $code = I('post.code', '', 'htmlspecialchars');

        if ($data['mobile'] == '' || $data['user_pwd'] == '' || $data['user_pwd_2'] == '' || $code == '') {
            $this->error("参数不能为空!", '', 1);
            die;
        }
        if ($data['user_pwd'] != $data['user_pwd_2']) {
            $this->error("两次密码不一致!", '', 1);
            die;
        }
        //用户是否已经存在
        $r = $User->where('mobile=' . $data['mobile'])->find();
        if ($r) {
            $this->error("用户已存在", '', 1);
        }

        //验证码校验
        $code_obj = M('code');
        $timeout = time() - 60;
        $r_code = $code_obj->where(" mobile = " . $data['mobile'] . " and ctime > $timeout and code = $code")->find();
        if (!$r_code) {
            $this->error("验证码错误", '', 1);
        }

        $data['register_type'] = 3;
        $data['create_time'] = time();
        $data['user_pwd'] = md5($data['user_pwd']);
        $r = $User->adduser($data);
        if ($r) {
            //$this->success('新增成功', U('home/index/reged',array('mobile'=>$data['mobile'])));
            redirect('/home/index/reged', 0, '');
        }
    }

    public function login()
    { //登陆

        $mobile = I('post.mobile', '', 'htmlspecialchars');
        $user_pwd = I('post.user_pwd', '', 'htmlspecialchars');
        // $nextURL = I('post.nextURL','','');
        $nextURL = I('post.nextURL', '', 'htmlspecialchars');
        // $nextURL = $_POST["nextURL"];
        // echo "<script>";
        // echo "alert('輸入錯誤!!');";
        // echo "$('#nextURL').val(nextURL)";
        // echo "</script>";
        $user_pwd = md5($user_pwd);

        if ($mobile == '' || $user_pwd == "") {
            $this->error("用户名密码不能为空", '', 2);
        }
        //验证手机号正确性
        $user = M('user');
        $r_user = $user->where(" mobile = '$mobile' and user_pwd = '$user_pwd' ")->find();
        if ($r_user) {
            session('tl_mobile', $mobile);
            session('tl_userid', $r_user['id']);

            //$this->success('登陆成功', U('home/index/index'));

            //用户登录的时候 记录登录行为****************
            $userid = $r_user['id'];
            $mobile = $mobile;
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];


            $save_data = array(
                'userid' => $userid,
                'mobile' => $mobile,
                'ip' => $ip,
                'ctime' => time()
            );
            $contact = M('user_login_log');
            $r = $contact->add($save_data);
            //end*****************************************
            // $nextURL = $_POST["nextURL"];
            if ($nextURL != null) {
                redirect($nextURL, 0, '');
            } else {
                // redirect('/home/index/index', 0, '');
                redirect('/home/index/index', 0, '');
            }
        } else {
            $this->error("用户名或密码错误", '', 2);
        }
    }

    public function logout()
    { //退出登录
        session('tl_userid', null);
        session('tl_mobile', null);
        //$this->success('退出登录', U('home/index/index'));
        redirect('/home/index/index', 0, '');
    }

    public function sendcode()
    { //发送验证码


        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];


        $code = $this->randString();
        $mobile = I('post.mobile', '', 'htmlspecialchars'); //电话号码

        $key = I('post.key', '', 'htmlspecialchars'); //key
        if ($key != 1366) {
            die('...');
        }

        if (!$mobile) {
            die('no mobile');
        }

        $code_obj = M('code');
        //今天0点时间戳
        $today = strtotime(date('Y-m-d'));

        $userCount = $code_obj->where("ip='" . $ip . "' and ctime > " . $today)->count("id");
        //echo $userCount;die;
        if ($userCount > 10) {
            die('too much');
        }

        //获取最近的一条记录
        $r_code = $code_obj->where('mobile=' . $mobile)->order('ctime desc')->limit(1)->find();
        $interval = time() - $r_code['ctime'];
        if ($interval < 55) {
            die('sms send error');
        }


        //插入验证码记录
        $code_data = array(
            'code' => $code,
            'mobile' => $mobile,
            'ip' => $ip,
            'ctime' => time()
        );

        $code_obj->add($code_data);

        //die;
        //----------------------------

        $client = new Client;
        $request = new SmsNumSend;

        // 短信内容参数
        $smsParams = array(
            'code' => $code,
            'product' => '用户注册'
        );

        // 设置请求参数
        $req = $request->setSmsTemplateCode('SMS_5250694')
            ->setRecNum($mobile)
            ->setSmsParam(json_encode($smsParams))
            ->setSmsFreeSignName('活动验证')
            ->setSmsType('normal')
            ->setExtend('demo');

        $r = $client->execute($req);
        //print_r($client->execute($req));die;
        echo $r['alibaba_aliqin_fc_sms_num_send_response']['result']['err_code'];


    }

    public function lostpwd()
    { //重置密码处理

        $User = new \Home\Model\UserModel();

        $data['mobile'] = I('post.mobile', '', 'htmlspecialchars');
        $data['user_pwd'] = I('post.user_pwd', '', 'htmlspecialchars');
        $code = I('post.code', '', 'htmlspecialchars');

        if ($data['mobile'] == '' || $data['user_pwd'] == '' || $code == '') {
            $this->error("参数不能为空!", '', 1);
            die;
        }
        //用户是否已经存在
        $r = $User->where('mobile=' . $data['mobile'])->find();
        if (!$r) {
            $this->error("用户不存在", '', 1);
        }

        //验证码校验
        $code_obj = M('code');
        $timeout = time() - 60;
        $r_code = $code_obj->where(" mobile = " . $data['mobile'] . " and ctime > $timeout and code = $code")->find();
        if (!$r_code) {
            $this->error("验证码错误", '', 1);
        }

        $save_data = array(
            'user_pwd' => md5($data['user_pwd'])
        );

        $r = $User->where('mobile=' . $data['mobile'])->save($save_data);

        if ($r) {
            $this->success('密码修改成功', U('home/index/login', array('mobile' => $data['mobile'])));
        }

    }

    public function contact()
    { //留言入库

        $name = I('post.name', '', 'htmlspecialchars');
        $telemail = I('post.telemail', '', 'htmlspecialchars');
        $content = I('post.content', '', 'htmlspecialchars');
        $save_data = array(
            'name' => $name,
            'telemail' => $telemail,
            'content' => $content,
            'ctime' => time()
        );
        $contact = M('contact');
        $r = $contact->add($save_data);

        if ($r) {
            $this->success('留言成功！', U('home/index/contact'));
        }
    }

    public function contact_new()
    { //新版留言入库

        $name = I('post.name', '', 'htmlspecialchars');
        $email = I('post.email', '', 'htmlspecialchars');
        $type = I('post.type', '', 'htmlspecialchars');
        $content = I('post.content', '', 'htmlspecialchars');
        $save_data = array(
            'name' => $name,
            'email' => $email,
            'type' => $type,
            'content' => $content,
            'ctime' => time()
        );
        $contact = M('contact_new');
        $r = $contact->add($save_data);
        //
        $Model = M();
        $mail = new sendmail();
        $sql = "select * from tl_config where type = 1";
        $result = $Model->query($sql);
        $send_arr = array();
        $to_arr = array();
        foreach ($result as $k => $v) {
            if (strstr($v['config_key'], 'send_mail_')) {
                $send_arr[$v['config_key']] = $v['config_val'];
            } else if ($v['config_key'] == 'to_mail_name') {
                array_push($to_arr, $v['config_val']);
            }
        }
        foreach ($to_arr as $k => $v) {
            $data = array(
                'Subject' => '您好，官网有新的留言',
                'body' => $content,
                'to' => $v,
            );
            $mail->send_config($data, $send_arr);
        }
        //
        if ($r) {
            $this->success('留言成功！', U('home/index/about'));
        }
    }


    /**
     * 获取随机位数数字
     * @param  integer $len 长度
     * @return string
     */
    protected static function randString($len = 4)
    {
        $chars = str_repeat('0123456789', $len);
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
        return $str;
    }


    function test1()
    { //用于测试使用

        //var_dump($_SERVER);

        //echo md5('******');
        //用户登录数据入log库
        /*
        $userid = '11';
        $mobile = '11';
        $ip = get_client_ip();


        $save_data = array(
            'userid' => $userid,
            'mobile' => $mobile,
            'ip' => $ip,
            'ctime' => time()
        );
        $contact = M('user_login_log');
        $r = $contact->add($save_data);
        */

    }

}
