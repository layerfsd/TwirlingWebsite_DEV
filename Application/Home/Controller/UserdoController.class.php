<?php
/*
 * 用户后台页面
 * */
namespace Home\Controller;
use Think\Controller;
class UserdoController extends Controller {

    function __construct() {
        parent::__construct();
        if (!session('tl_userid'))
        {
            $this->success('请登录', U('home/index/login'));
            die;
        }
    }

    public function usereditdo(){ //用户修改个人资料处理
        $User = M('user');
        $userid = session('tl_userid');
        $email = I('post.email','','htmlspecialchars');

        $save_data = array(
            'email' => $email
        );

        $User->where("id=$userid")->save($save_data);
        $this->success('修改成功');
    }
	
	public function userzizhido(){ //用户修改个人资料处理
		$User = M('user');
		$userid = session('tl_userid');
		
		$upload = new \Think\Upload();// 实例化上传类    
		$upload->maxSize   =     3145728 ;// 设置附件上传大小    
		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型    
		//$upload->savePath  =      './Public/Uploads/'; // 设置附件上传目录    // 上传文件   
		$upload->rootPath = './Public/Uploads/'; 		
		$info   =   $upload->upload();
		
		
		if(!$info) {// 上传错误提示错误信息        
			$this->error($upload->getError());    
		}else{// 上传成功
			//入库
			$zizhi = $info['zizhi']['savepath'].$info['zizhi']['savename'];
			$save_data = array(
				'zizhi' => $zizhi,
				'zizhi_status' => 1
			);

			$User->where("id=$userid")->save($save_data);
		
			$this->success('上传成功！');
		}
    }

    public function bindphone(){ //绑定手机号操作

        $User = new \Home\Model\UserModel();

        $data['mobile']      = I('post.mobile','','htmlspecialchars');
        $userid      = I('post.userid','','htmlspecialchars');
        $code = I('post.code','','htmlspecialchars');


        if($data['mobile'] ==  '' || $code == '' || $userid == ''){
            $this->error("参数不能为空!",'',1);
            die;
        }


        //验证码校验
        $code_obj = M('code');
        $timeout = time() - 60;
        $r_code = $code_obj->where(" mobile = " . $data['mobile']. " and ctime > $timeout and code = $code")->find();
        if(!$r_code){
            $this->error("验证码错误",'',1);
        }

        $r = $User->where("id=$userid")->save($data);
        if($r){
            //$this->success('新增成功', U('home/index/reged',array('mobile'=>$data['mobile'])));
            redirect('/home/user/binded', 0, '');
        }
    }

}
