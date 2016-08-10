<?php
/*
 * 第三方登录回调处理
 * */
namespace Home\Controller;
use Think\Controller;
class CallbackController extends Controller {

    public function qq(){ //qq回调
        $this->display();
    }

}
