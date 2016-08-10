<?php

namespace Admin\Controller;

use Think\Page;
use Think\Model;

class OrderController extends BaseController
{
    public function index()
    {
        $Model = M();
        $search = $_GET ['search']; // 搜索关键词
        $status = $_GET ['status']; // 状态
        import('ORG.Util.Page'); // 导入分页类
        $map = array();
        if ($search != null && $search != '') {
            $map ['order_sn'] = array(
                'like',
                '%' . $search . '%'
            );
            $this->search = $search;
            $Page->parameter .= "&search=" . urlencode($search);
        }

        // -1全部
        if ($status != null && $status != -1) {
            $map ['status'] = $status;
            $Page->parameter .= "&status=" . urlencode($status);
            $this->status = $status;
        } else {
            $this->status = -1;
        }

        $order_cnt = M('order')->where($map)->count();
        $this->order_cnt = $order_cnt;
        $Page = new Page ($order_cnt, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $orders = M('order')->where($map)->order(array(
            'create_time' => 'desc'
        ))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($orders as $k => $v) {
            //视频总数
            $sql = "select count(id) as c from tl_video_desc where uuid = '" . $v['video_uuid'] . "' and video_type = 1";
            $video_cnt = $Model->query($sql);
            //音频总数
            $sql = "select count(id) as c from tl_video_desc where uuid = '" . $v['video_uuid'] . "' and video_type = 2";
            $audio_cnt = $Model->query($sql);
            $sql = "select * from tl_user where id = " . $v['order_user_id'];

//            echo $sql;echo '</br>';
            $user_info = $Model->query($sql);
            //var_dump($user_info);die;
            $register_type = $user_info[0]['register_type'];
            $name = $user_info[0]['mobile'];
//            echo $register_type;echo '</br>';
//            if($register_type == 1 || $register_type == 2 || $register_type == 3){
//                echo 'ok';echo '</br>';
//                var_dump($user_info[0]);
//                $name = $user_info[0]['nick_name'];
//            }
//            echo $name;
            $orders[$k]['name'] = $name;
            $orders[$k]['video_cnt'] = $video_cnt[0]['c'];
            $orders[$k]['audio_cnt'] = $audio_cnt[0]['c'];
            $orders[$k]['user_info'] = $user_info[0];
        }
        $this->list = $orders;
        $this->show = $show;
        $this->display();
    }

    /**
     * 生成订单
     */
    public function doAdd()
    {
        $Model = M();
        $money = $_POST['money'];  //订单金额
        $uuid = $_POST['uid'];

        $sql = "select count(*) as c from tl_order where video_uuid = '" . $uuid . "'";
        $cnt = $Model->query($sql);
        if (intval($cnt[0]['c']) > 0) {//修改价格
            $sql = "update tl_order set order_money = " . $money . " where video_uuid = '" . $uuid . "'";
            $Model->execute($sql);
            $result = array(
                'success' => true,
                'msg' => "订单价格修改成功"
            );
            $json_string = json_encode($result);
            echo $json_string;
            exit;
        } else {//新增
            $sql = "select * from tl_video where video_uuid = '" . $uuid . "'";
            $video = $Model->query($sql);
            $user_id = $video[0]['user_id'];
            $order_sn = date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            //订单入库
            $sql = "insert into tl_order (order_sn,order_name , video_uuid,order_money,order_user_id,create_time,status) values('" . $order_sn . "' ,'视频合成费用','" . $uuid . "', " . $money . " , " . $user_id . " , " . time() . " , 0 )";
            $Model->execute($sql);
            //修改视频为合并中
            $sql = "update tl_video set status = 2 where video_uuid = '" . $uuid . "'";
            $Model->execute($sql);
            $result = array(
                'success' => true,
                'msg' => "定价成功"
            );
            // 	    $this->redirect("Admin/Video/index");
            $json_string = json_encode($result);
            echo $json_string;
        }

    }


    /**
     * 修改订单金额
     */
    public function updateMoney()
    {
        $Model = M();
        $money = $_POST['money'];  //订单金额
        $id = $_POST['id'];
        $sql = "select count(*) as c from tl_order where status = 0 and  id = '" . $id . "'";
        $cnt = $Model->query($sql);
        if (intval($cnt[0]['c']) > 0) {//修改价格
            $sql = "update tl_order set order_money = " . $money . " where id = '" . $id . "'";
            $Model->execute($sql);
            $result = array(
                'success' => true,
                'msg' => "订单价格修改成功"
            );
            $json_string = json_encode($result);
            echo $json_string;
            exit;
        } else {
            $result = array(
                'success' => true,
                'msg' => "订单不存在或已支付！"
            );
            echo $json_string;
            exit;
        }

    }

    public function test_json()
    { // 链接数据库测试
        $Model = M();
        $id = $_GET ['id'];
        $sql = "select * from tl_user where id = " . $id;
        $user = $Model->query($sql);
        if (count($user) <= 0) {
            $result = array(
                'success' => false,
                'msg' => "用户不存在",
                'data' => null
            );
        } else {
            $result = array(
                'success' => true,
                'msg' => "查询成功",
                'data' => $user [0]
            );
        }
        $json_string = json_encode($result);
        echo $json_string;
    }

    public function test2()
    { // 模板测试
        $this->display();
    }
}
