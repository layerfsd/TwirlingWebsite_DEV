<?php

namespace Admin\Controller;
use Think\Page;
use Think\Model;
/**
 * 报表
 * @author Administrator
 *
 */
class ReportController extends BaseController {
	
	
    public function index() {
        $Model = M ();
        $start_time = $_GET ['start_time']; // 开始时间
        $end_time = $_GET ['end_time']; // 开始时间
        import ( 'ORG.Util.Page' ); // 导入分页类
        
        $map = array (
            	
        );
    
        if ($start_time != null && $start_time != '') {
            $map ['report_date'] = array (
                'EGT',  $start_time 
            );
            $this->start_time = $start_time;
            $Page->parameter .= "&start_time=" . urlencode ( $search );
        }
        
        
        if ($end_time != null && $end_time != '') {
            $map ['report_date'] = array (
                'ELT', $end_time
            );
            $this->end_time = $end_time;
            $Page->parameter .= "&end_time=" . urlencode ( $search );
        }
        
        
    
    
        $report_cnt = M ( 'report' )->where ( $map )->count ();
        $this->report_cnt = $report_cnt;
        $Page = new Page ( $report_cnt, 10 ); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show (); // 分页显示输出
        $reports = M ( 'report' )->where ( $map )->order ( array (
            'report_date' => 'desc'
        ) )->limit ( $Page->firstRow . ',' . $Page->listRows )->select ();
    
    
        $this->list = $reports;
        $this->show = $show;
        $this->display ();
    }
    
    
    
    
    function str_format_time($timestamp = '')
    {
        if (preg_match("/[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} (0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])/i", $timestamp))
            {
                list($date,$time)=explode(" ",$timestamp);
                list($year,$month,$day)=explode("-",$date);
                list($hour,$minute,$seconds )=explode(":",$time);
                $timestamp=gmmktime($hour,$minute,$seconds,$month,$day,$year);
            }
        else
            {
                $timestamp=time();
            }
            
            echo '<br />';
            echo date("Y-m-d H:i:s", $timestamp);
            
            
        return $timestamp;
    }
    
}
