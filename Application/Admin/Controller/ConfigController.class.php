<?php

namespace Admin\Controller;

use Think\Controller;
use Mail\sendmail;
class ConfigController extends BaseController {
	public function index() {
	    //获取配置
	    $Model = M ();
	    $sql = "select * from tl_config where type = 1 ";
	    $send_arr = array();
	    $to_arr = array();
	    $r = $Model->query ( $sql );
	    foreach ($r as $k=>$v){
	        if(strstr($v['config_key'] , 'send_mail_')){
	            $send_arr[$v['config_key']] = $v['config_val'];
	        }else if($v['config_key'] == 'to_mail_name'){
	            $to_arr[$k] = $v;
	        }
	    }
	    $this->mail_arr = $send_arr;
	    $this->list = $to_arr;
	    $this->display ();
	}
	
	public function add(){
	    $Model = M ();
	    //清空
        $sql = "delete from tl_config where type = 1";
        $Model->execute ( $sql );
	    $send_mails = $_POST['to_mail_name'];
	    foreach ($send_mails as $k=>$v){
	        if($v == ''){
	            continue;
	        }
            $sql = "insert into tl_config (config_key,config_val,type) values('to_mail_name','".$v."',1)";
            $Model->execute ( $sql );
	    }
	    
	    foreach ($_POST as $k=>$v){
	        if(strstr($k , 'send_mail_')){
	            $sql = "insert into tl_config (config_key,config_val,type) values('".$k."','".$v."',1)";
	            $Model->execute ( $sql );
	        }
	    }
	    
	   $this->redirect ( "Admin/Config/index" );
	}
	
	
}
