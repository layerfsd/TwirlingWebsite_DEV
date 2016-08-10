<?php

namespace Admin\Controller;

use Think\Page;

class UserController extends BaseController
{
    public function index()
    {
        $search = $_GET ['search']; // 搜索关键词
        $status = $_GET ['status']; // 状态
        $zizhi_status = $_GET ['zizhi_status']; // 状态
        import('ORG.Util.Page'); // 导入分页类
        $map = array(
            'status' => 1
        );

        if ($search != null && $search != '') {
            $map ['mobile'] = array(
                'like',
                '%' . $search . '%'
            );
            $this->search = $search;
            $parmap ['user_name'] = $search;
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

        if ($zizhi_status !== null) {
            $map ['zizhi_status'] = $zizhi_status;
            $Page->parameter .= "&zizhi_status=" . urlencode($zizhi_status);
            $this->zizhi_status = $zizhi_status;
        }

        $user_cnt = M('user')->where($map)->count();

        $this->usercnt = $user_cnt;
        $Page = new Page ($user_cnt, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $users = M('user')->where($map)->order(array(
            'create_time' => 'desc'
        ))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->list = $users;
        $this->show = $show;
        $this->display();
    }

    /**
     * 查看用户详情
     */
    public function datile()
    {
        $Model = M();
        $id = $_GET ['id'];
        $sql = "select * from tl_user where id = " . $id;
        $user = $Model->query($sql);
        $this->user = $user [0];
        $this->display();
    }

    /**
     * 修改用户信息
     */
    public function update()
    {
        $user_pwd = $_POST ['user_pwd']; // 密码
        $email = $_POST ['email']; // 邮箱
        $nick_name = $_POST ['nick_name']; // 真实姓名
        $introduction = $_POST ['introduction']; // 简介
        $address = $_POST ['address']; // 地址
        $mobile = $_POST ['mobile']; // 手机号
        $id = $_POST ['id'];
        $last_update_time = time(); // 当前时间戳
        if (M('user')->save($data)) {
            $this->redirect('Student/Index');
        } else {
            $this->redirect('Student/goAdd');
        }
    }

    /**
     * 检查邮箱或手机号是否已被使用
     * type=1 手机 2邮箱
     */
    public function check()
    {
        $Model = M();
        $type = $_GET ['type'];
        $val = $_GET ['val'];
        $id = $_GET ['id'];

        $user_sql = "select * from tl_user where id = " . $id;
        $curr_user = $Model->query($user_sql);

        $where = "";
        if ($type == 1) {
            if ($curr_user [0] ['mobile'] == $val) { // 未做修改直接返回true
                $result = array(
                    'success' => true,
                    'msg' => "可以使用"
                );
                $json_string = json_encode($result);
                echo $json_string;
                exit ();
            } else {
                $where = "mobile = '" . $val . "'";
            }
        } else {
            if ($curr_user [0] ['email'] == $val) { // 未做修改直接返回true
                $result = array(
                    'success' => true,
                    'msg' => "可以使用"
                );
                $json_string = json_encode($result);
                echo $json_string;
                exit ();
            } else {
                $where = "email = '" . $val . "'";
            }
        }
        $sql = "select count(id) as cnt from tl_user where " . $where;
        $user = $Model->query($sql);
        if (intval($user [0] ['cnt']) > 0) {
            $result = array(
                'success' => false,
                'msg' => "已被使用"
            );
        } else {
            $result = array(
                'success' => true,
                'msg' => "可以使用"
            );
        }
        $json_string = json_encode($result);
        echo $json_string;
    }

    /**
     * 修改用户信息(用户名不可修改，密码修改时使用MD5加密)
     */
    public function doUpdate()
    {
        header("Content-Type:text/html; charset=utf-8");
        $User = M('user');
        $user_info = array();
        if ($_POST ['user_pwd'] != '******') {
            $user_info ['user_pwd'] = $_POST ['user_pwd'];
        }
        $user_info ['email'] = $_POST ['email'];
        $user_info ['nick_name'] = $_POST ['nick_name'];
        $user_info ['address'] = $_POST ['address'];
        $user_info ['mobile'] = $_POST ['mobile'];
        $user_info ['introduction'] = $_POST ['introduction'];
        $user_info ['zizhi_status'] = $_POST ['zizhi_status'];
        $user_info ['is_vip'] = $_POST ['is_vip'];
        $user_info ["last_update_time"] = time();
        $id = $_POST ['id'];
        if ($user_info ['user_pwd'] != '******') {
            $pwd = md5($user_info ['user_pwd']);
            $user_info ['user_pwd'] = $pwd;
        }


        $result = $User->where("id=$id")->save($user_info);

        if ($result) {
            $this->redirect('Admin/User/index');
        }
    }

    /**
     * 修改用户状态
     */
    public function doUpdateStatus()
    {
        $type = $_GET ['type'];
        $id = $_GET ['id'];
        $sql = "update tl_user set status = " . $type . " , last_update_time = " . time() . " where id = " . $id;
        $Model = M();
        $result = $Model->execute($sql);
        if ($result) {
            $result = array(
                'success' => true,
                'msg' => "修改成功"
            );
        } else {
            $result = array(
                'success' => false,
                'msg' => "修改失败"
            );
        }
        $json_string = json_encode($result);
        echo $json_string;
    }

    /**
     * 修改密码跳转
     */
    public function goUpdateAdminPwd()
    {
        $user_info = session("UserInfo");
        //如果当前session为空跳转到登陆页
        if ($user_info == null) {
            $this->redirect('Public/login');
        } else {
            $this->user = $user_info;
            $this->display("update_admin");
        }
    }

    /**
     * 验证原始密码
     */
    public function checkpwd()
    {
        $pwd = $_GET['pwd'];
        $id = $_GET['id'];
        $map = array(
            'user_pwd' => md5($pwd),
            'status' => 1,
            'id' => $id
        );
        $userinfo = M('admin')->where($map)->find();
        if ($userinfo) {
            $result = array(
                'success' => true,
                'msg' => "验证成功"
            );
        } else {
            $result = array(
                'success' => false,
                'msg' => "验证失败"
            );
        }
        $json_string = json_encode($result);
        echo $json_string;

    }

    /**
     * 修改管理员密码
     */
    public function updateAdminPwd()
    {
        $now_pwd = $_GET['now_pwd'];    //原始密码
        $new_pwd = $_GET['new_pwd'];    //新密码
        $id = $_GET['user_id'];                //修改的ID
        $sql = "update tl_admin set user_pwd = '" . md5($new_pwd) . "' where id = " . $id;
        $Model = M();
        $result = $Model->execute($sql);
        if ($result) {
            $result = array(
                'success' => true,
                'msg' => "修改成功"
            );
        } else {
            $result = array(
                'success' => false,
                'msg' => "修改失败"
            );
        }
        $json_string = json_encode($result);
        echo $json_string;
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
