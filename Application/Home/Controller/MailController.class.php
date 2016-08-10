<?php
/**
 * 发送邮件的范例
 *
 * @author wangqiu <2016-02-18 23:18:10>
 */
namespace Home\Controller;
use Think\Controller;
use Mail\sendmail;

class MailController extends Controller
{
    public function index()
    {
            $data = array(
                'Subject' => '123',
                'body'    => '456',
                'to'      => '44459211@qq.com'
            );
            $mail = new sendmail();
            $mail->send($data);
    }

}