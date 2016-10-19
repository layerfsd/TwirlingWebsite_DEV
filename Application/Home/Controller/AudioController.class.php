<?php
/*
 * 音频
 * */
namespace Home\Controller;

use Think\Controller;

class AudioController extends Controller
{
    Public function danmu()
    {
        $this->display('danmu');
    }

    Public function danmu2()
    {
        $this->display('danmu2');
    }

    Public function sale()
    {
        $this->display('sale');
    }

    Public function audioLsit1()
    {
        $this->display('audioLsit1');
    }

    Public function demo()
    {
        $this->display('demo');
    }

    Public function invite_s()
    {
        require_once('./plugin/wx/jssdk.php');
        $jssdk = new \JSSDK("wx9ba74d771ad9b653", "17ec17f5e4d574df6091c6ffc082dcac");
        $signPackage = $jssdk->GetSignPackage();
        $this->assign('signPackage', $signPackage);
        $this->display('invite_s');
    }


    Public function sale_do()
    {

        //验证码校验
        $code_obj = M('code');
        $timeout = time() - 180;
        $where = " mobile = " . trim($_POST['f_phone']) . " and ctime > $timeout and code = " . trim($_POST['f_verify']);
        $r_code = $code_obj->where($where)->find();
        if (!$r_code) {
            $this->error("验证码错误或超时", '', 1);
            die;
        }

        $index_sale = M('index_sale');
        $save_data = array(
            'f_name' => $_POST['f_name'],
            'f_company' => $_POST['f_company'],
            'f_address' => $_POST['f_address'],
            'f_sum' => $_POST['f_sum'],
            'f_phone' => $_POST['f_phone'],
            'ctime' => time()
        );
        $r = $index_sale->add($save_data);
        $this->success('添加成功');
    }

    Public function invite()
    {
        require_once('./plugin/wx/jssdk.php');
        $jssdk = new \JSSDK("wx9ba74d771ad9b653", "17ec17f5e4d574df6091c6ffc082dcac");
        $signPackage = $jssdk->GetSignPackage();
        $this->assign('signPackage', $signPackage);
        $this->display('invite');
    }

    Public function invite_do()
    {
        $index_invite = M('index_invite');
        $save_data = array(
            'i_company' => $_POST['i_company'],
            'i_name' => $_POST['i_name'],
            'i_duty' => $_POST['i_duty'],
            'i_phone' => $_POST['i_phone'],
            'i_wechat' => $_POST['i_wechat'],
            'i_email' => $_POST['i_email'],
            'ctime' => time()
        );
        $r = $index_invite->add($save_data);
        redirect('/home/audio/invite_s?name=' . $_POST['i_name'], 0, '');

    }

    public function audioList()
    {
        $video = M('video');

        $list = $video->where("user_id = '18'")->order('create_time desc')->select();
        foreach ($list as $k => $v) {
            $video_translate = M('video_translate');
            $r = $video_translate->where("video_uuid = '" . $v['video_uuid'] . "' and device_type = 1 and file_type = 2 and status = 2")->find();
            $list[$k]['pic_url'] = $r['url'];
            $r = $video_translate->where("video_uuid = '" . $v['video_uuid'] . "' and device_type = 3 and file_type = 3 and status = 2")->find();
            $list[$k]['mp3'] = $r['url'];
        }
        $this->assign('list', $list);
        $this->display();
    }

    //弹幕入库接口
    Public function barrage_add()
    {
         $db_barrage = M('barrage');
        $save_data = array(
            'sid' => $_POST['sid'],
            'text' => $_POST['text'],
            'ctime' => $_POST['ctime']
        );
        $r = $db_barrage->add($save_data);
        if ($r) {
            echo 'ok';
        }
    }

    //弹幕list
    Public function barrage_list()
    {
        $db_barrage = M('barrage');
        $sid = $_POST['sid'];
        if ($sid == '') {
            echo 'parameter error';
            die;
        }
        $list = $db_barrage->where("sid = $sid")->order("ctime asc")->select();
        //var_dump($list);die;
        $list_json = json_encode($list);
        echo $list_json;
        die;
    }

}
