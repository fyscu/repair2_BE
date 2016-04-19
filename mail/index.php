<?php
/**
*index.php?
*参数email 收件人邮箱
*title 邮件标题
*content 邮件内容
*
*日志记录在mail_log表里
*/
//0.引用
require('function.php');
//1.判断权限
$ip = getIP();
$sig = check_ip($ip);
if($sig){
//2.组装参数
	$params = getParams($_GET);
	if(!empty($params)){
//3.发送信息
	//send 
		$send = sendMail($params);
		if($send)
		{
			//mail_log($ip, 1, $params);
			response(1);
		}else{
			//mail_log($ip, 4, $params);
			response(4);
		}
	}else{
		//mail_log($ip, 3);
		response(3);
	}
	//4.记录日志 返回
	//response($send);
}else{
	//mail_log($ip, 2);
	response(2);
}

//注释功能完成的步骤 分步实现 注意返回 和引用0步
//业务完成在一个文件中 功能在其他文件中实现
?>