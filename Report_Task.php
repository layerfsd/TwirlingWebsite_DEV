<?php
header('Content-type:text/html;charset=utf-8');
try {
	define(DB_HOST, 'rdsh144hk908eprp68eb.mysql.rds.aliyuncs.com');
	define(DB_USER, 'hnylchf');
	define(DB_PASS, '123456');
	define(DB_DATABASENAME, 'twirling');

	$conn = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("connect failed" . mysql_error());
	mysql_select_db(DB_DATABASENAME, $conn);


	date_default_timezone_set("Asia/Shanghai");
	$create_time =  date("Y-m-d H:i:s",intval(time()));

	$date = date('Y-m-d',strtotime('-1 day'));
	$start_time = strtotime($date);
	$end_time = strtotime(date('Y-m-d'));
	
	//当日注册量
	$result = array();
	$query_sql = sprintf("select coalesce(count(*),0) as cnt from tl_user where create_time > " . $start_time ." and create_time <= " . $end_time);
	$results = mysql_query($query_sql, $conn);
	if ($row=mysql_fetch_array($results, MYSQL_ASSOC))//与$row=mysql_fetch_assoc($result)等价
	{
		$cnt = $row;
	}
	$result['user'] = $cnt['cnt'];
	
	//当日上传视频总数
	$query_sql = sprintf("select coalesce(count(*),0) as cnt from tl_video_desc where video_type = 1 and create_time > " . $start_time ." and create_time <= " . $end_time);
	$results = mysql_query($query_sql, $conn);
	if ($row=mysql_fetch_array($results, MYSQL_ASSOC))//与$row=mysql_fetch_assoc($result)等价
	{
		$cnt = $row;
	}
	$result['up_video'] = $cnt['cnt'];

	//当日上传音频
	$query_sql = sprintf("select coalesce(count(*),0) as cnt from tl_video_desc where video_type = 2 and create_time > " . $start_time ." and create_time <= " . $end_time);
	$results = mysql_query($query_sql, $conn);
	if ($row=mysql_fetch_array($results, MYSQL_ASSOC))//与$row=mysql_fetch_assoc($result)等价
	{
		$cnt = $row;
	}
	$result['up_audio'] = $cnt['cnt'];
	
	//当日订单总数
	
	//当日订单总数
	$query_sql = sprintf("select coalesce(count(*),0) as cnt from tl_order where create_time > " . $start_time ." and create_time <= " . $end_time);
	$results = mysql_query($query_sql, $conn);
	if ($row=mysql_fetch_array($results, MYSQL_ASSOC))//与$row=mysql_fetch_assoc($result)等价
	{
		$cnt = $row;
	}
	$result['order'] = $cnt['cnt'];
	
	//当日支付
	$query_sql = sprintf("select coalesce(count(*),0) as cnt from tl_order where status = 1 and create_time > " . $start_time ." and create_time <= " . $end_time);
	$results = mysql_query($query_sql, $conn);
	if ($row=mysql_fetch_array($results, MYSQL_ASSOC))//与$row=mysql_fetch_assoc($result)等价
	{
		$cnt = $row;
	}
	$result['order_pay'] = $cnt['cnt'];
	
	//当日未支付
	$query_sql = sprintf("select coalesce(count(*),0) as cnt from tl_order where status = 0 and create_time > " . $start_time ." and create_time <= " . $end_time);
	$results = mysql_query($query_sql, $conn);
	if ($row=mysql_fetch_array($results, MYSQL_ASSOC))//与$row=mysql_fetch_assoc($result)等价
	{
		$cnt = $row;
	}
	$result['order_pay_no'] = $cnt['cnt'];

	//当日支付总额
	$query_sql = sprintf("select coalesce(sum(order_money),0) as cnt from tl_order where status = 1 and create_time > " . $start_time ." and create_time <= " . $end_time);
	$results = mysql_query($query_sql, $conn);
	if ($row=mysql_fetch_array($results, MYSQL_ASSOC))//与$row=mysql_fetch_assoc($result)等价
	{
		$cnt = $row;
	}
	$result['pay_money'] = $cnt['cnt'];
	$sql = "insert into tl_report (up_video,up_audio,regist,`order`,pay_order,not_pay_order,pay_money,report_date)  values(".$result['up_video'].",".$result['up_audio'].",".$result['user'].",".$result['order'].",".$result['order_pay'].",".$result['order_pay_no'].",".$result['pay_money'].",'".$date."')";
	
	
	
	
	$query_sql = sprintf("select coalesce(count(id),0) as cnt from tl_report where report_date = '".$date."' ");
	$results = mysql_query($query_sql, $conn);
	if ($row=mysql_fetch_array($results, MYSQL_ASSOC))//与$row=mysql_fetch_assoc($result)等价
	{
		$cnt = $row;
	}
	if($cnt['cnt'] > 0){	//存在
		$query_sql = sprintf("delete from tl_report where report_date = '".$date."' ");
		$results = mysql_query($query_sql, $conn);
	}
	$results = mysql_query($sql, $conn);
	
	
	
	
	
	mysql_free_result($results);
	mysql_close($conn);
	echo '操作完成';
}catch (Exception $e){
	var_dump($e);
	//print_json(0,$e->getMessage());
}



