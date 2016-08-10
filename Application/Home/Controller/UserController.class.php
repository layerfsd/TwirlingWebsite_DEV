<?php
/*
 * 用户后台页面
 * */
namespace Home\Controller;

use Think\Controller;

class UserController extends Controller
{

    function __construct()
    {
        parent::__construct();
        if (!session('tl_userid')) {
            $this->success('请登录', U('home/index/login'));
            die;
        }
    }

    public function index()
    { //用户后台首页(购买产品页面)
        $userid = session('tl_userid');
        //获取用户信息
        $user = M('user');
        $userinfo = $user->where("id = '$userid'")->find();
        //var_dump($userinfo);die;
        $userinfo['create_time'] = date('Y-m-d', $userinfo['create_time']);
        $this->assign('userinfo', $userinfo);
        $this->display();
    }

    public function fuwu()
    { //购买服务页面
        $userid = session('tl_userid');
        //获取用户信息
        $user = M('user');
        $userinfo = $user->where("id = '$userid'")->find();
        //var_dump($userinfo);die;
        $userinfo['create_time'] = date('Y-m-d', $userinfo['create_time']);
        $this->assign('userinfo', $userinfo);
        $this->display();
    }

    public function edit()
    { //修改用户资料
        //获取用户资料
        $userid = session('tl_userid');
        $user = M('user');
        $userinfo = $user->where("id = '$userid'")->find();
        $this->assign('userinfo', $userinfo);
        $this->display();
    }

    public function zizhi()
    { //上传资质
        //获取用户资料
        $userid = session('tl_userid');
        $user = M('user');
        $userinfo = $user->where("id = '$userid'")->find();
        $this->assign('userinfo', $userinfo);
        $this->display();
    }

    public function bindphone()
    { //綁定手機
        $userid = session('tl_userid');
        $user = M('user');
        $userinfo = $user->where("id = '$userid'")->find();
        //判断用户是否已经绑定过手机如果绑定过直接跳转到首页
        if ($userinfo['mobile']) {
            redirect('/home/index/index', 0, '');
        }
        $this->assign('userinfo', $userinfo);
        $this->display();
    }

    public function binded()
    { //绑定成功页面
        $this->display();
    }

    public function orderlist()
    { //订单列表
        //获取用户资料
        $userid = session('tl_userid');
        $user = M('user');
        $userinfo = $user->where("id = '$userid'")->find();

        //获取订单列表
        $order = M('order');
        $list = $order->where("order_user_id = '$userid'")->order('create_time desc')->select();

        $this->assign('userinfo', $userinfo);
        $this->assign('list', $list);
        $this->display();
    }

    public function address()
    { //确认收货地址
        $ordername = $_GET['ordername'];
        $price = $_GET['price'];
        $this->assign('ordername', $ordername);
        $this->assign('price', $price);
        $this->display();

    }

    public function productbuy()
    { //确认订单

        //生成订单号
        $order_no = date('Ymd', time());
        $rand = $this->randString();
        $order_no .= $rand;

        $price = $_POST['price'];
        $ordername = $_POST['ordername'];
        $order_method_pay = 2;

        //生成订单
        $order = M('order');
        $data = array(
            'order_name' => $_POST['ordername'],
            'order_sn' => $order_no,
            'order_money' => $price,
            'order_method_pay' => $order_method_pay,
            'order_user_id' => session('tl_userid'),
            'address' => $_POST['address'],
            'address_mobile' => $_POST['address_mobile'],
            'create_time' => time(),
            'type' => 2,
            'status' => 0

        );
        $r = $order->add($data);


        //生成订单记录

        $this->assign('ordername', $ordername);
        $this->assign('price', $price);
        $this->assign('address', $_POST['address']);
        $this->assign('address_mobile', $_POST['address_mobile']);
        $this->assign('order_no', $order_no);

        $this->display();

    }

    public function buy()
    { //确认订单

        $order_no = $_GET['order_sn']; //订单号
        //获取订单信息
        $order = M('order');
        $order_r = $order->where("order_sn = '$order_no'")->find();
        if ($order_r['type'] == 1) {
            $ordername = '视频合成订单';
        } else {
            $ordername = $order_r['order_name'];
        }
        $price = $order_r['order_money']; //商品价格


        $this->assign('ordername', $ordername);
        $this->assign('price', $price);
        $this->assign('order_no', $order_no);

        $this->display();

    }

    public function download()
    {
        $userid = session('tl_userid');
        //获取用户信息
        $user = M('user');
        $userinfo = $user->where("id = '$userid'")->find();

        if ($userinfo['is_vip'] != 1) { //如果没有授权就不能下载
            $this->error("您还不是授权用户", '', 3);
            die;
        }

        if ($_GET['type'] == 'Android') {
            header("Location: http://down-load.oss-cn-shanghai.aliyuncs.com/AudioSDK/Twirling%20Audio%20SDK%201.2%20for%20android.rar");
            die;
        }
        if ($_GET['type'] == 'ios') {
            header("Location: http://down-load.oss-cn-shanghai.aliyuncs.com/AudioSDK/Twirling%20Audio%20SDK%201.2%20for%20iOS.rar");
            die;
        }
        if ($_GET['type'] == 'Windows') {
            header("Location: http://down-load.oss-cn-shanghai.aliyuncs.com/AudioSDK/Twirling%20Audio%20SDK%201.2%20for%20windows.rar");
            die;
        }
        if ($_GET['type'] == 'Unity') {
            header("Location: http://down-load.oss-cn-shanghai.aliyuncs.com/AudioSDK/Twirling%20Audio%20SDK%201.2%20for%20unity3d.rar");
            die;
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
}








