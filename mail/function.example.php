<?php
//完成单一功能

function getIP(){//获得访问者真实IP
	    if (isset($_SERVER)){
	        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
	            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
	            $realip = $_SERVER["HTTP_CLIENT_IP"];
	        } else {
	            $realip = $_SERVER["REMOTE_ADDR"];
	        }
	    } else {
	        if (getenv("HTTP_X_FORWARDED_FOR")){
	            $realip = getenv("HTTP_X_FORWARDED_FOR");
	        } else if (getenv("HTTP_CLIENT_IP")) {
	            $realip = getenv("HTTP_CLIENT_IP");
	        } else {
	            $realip = getenv("REMOTE_ADDR");
	        }
	    }
	    return $realip;
}

//ip设成变量 不要每次重新获取
function check_ip($ip){
	if($ip=='::1'||$ip=="localhost"||$ip=="127.0.0.1"||$ip="115.28.12.213")
	{
		// 'jhdjs' "abc$a" 单引号效率较高 双引号会先替换其中的变量
		return true;
	}
	return false;
}

//获取GET参数
//email => 收件人邮箱
//title => 邮件标题
//content => 邮件内容
function getParams($arr=array()){//设置默认参数
	$result = array();

	//注意没有直接用$_GET
	$email = isset($arr['email']);
	$title = isset($arr['title']);
	$content = isset($arr['content']);
	
	if($email && $title && $content){
		$result['email'] = $arr['email'];
		$result['title'] = $arr['title'];
		$result['content'] = $arr['content'];
		return $result;
	}else{
		return $result;
	}
	//$result['title'] = $arr['title'];
	//....
	return $result;
}

//发送邮件 返回布尔值
function sendMail($arr=array()){

		require_once("class.phpmailer.php"); //
		$mail = new PHPMailer(); 
		$mail->IsSMTP();
		$mail->Host = "smtp.sina.com";
		$mail->SMTPAuth = true;
		$mail->Username = "test@sina.com";
		$mail->Password = "test";//账号密码定义成常量
		$mail->Charset='UTF-8';
		$mail->Port=25;
		$mail->From = "test1@sina.com";
		$mail->FromName = "飞扬俱乐部";
		$mail->AddAddress($arr['email'], "技术员邮箱");
		
		// send as HTML
		$mail->IsHTML(true);  
    
		$mail->Subject = "=?utf-8?B?" . base64_encode($arr['title']) . "?="; 
		$mail->Body = $arr['content']; 
		
		if($mail->Send())
		{
			return true;
		}

		return false;
}


function sendMsg($arr=array()){
	return true;
}


function response($code=1){//返回
	$state = array(
		'1'=>'success',
		'2'=>'check ip error',
		'3'=>'get params error',
		'4'=>'sendmail error',
		//'5'=>'log error',
		);
	die(json_encode(array('code'=>$code,'msg'=>$state[$code])));
}


function mail_log($ip,$code = 1,$mailArr = array()){//记录日志

	@mysql_connect('localhost','fyyf','fyyf2013') or die('log error : can\'t connect to mysql : '.mysql_error());
	mysql_select_db('fyyf');

	$state = array(
		'1'=>'success',
		'2'=>'check ip error',
		'3'=>'get params error',
		'4'=>'sendmail error',
		);

	switch ($code) {
		case 1:
			$email = $mailArr['email'];
			$title = $mailArr['title'];
			$content = $mailArr['content'];
			$s = $state[$code];
			$sql = "insert into mail_log (ip,email,title,content,state) values ('$ip','$email','$title','$content','$s')";
			break;
		case 2:
			$sql = "insert into mail_log (ip,state) values ('$ip','$state[$code]')";
			break;

		case 3:
			$sql = "insert into mail_log (ip,state) values ('$ip','$state[$code]')";
			break;

		case 4:
			$email = $mailArr['email'];
			$title = $mailArr['title'];
			$content = $mailArr['content'];
			$s = $state[$code];
			$sql = "insert into mail_log (ip,email,title,content,state) values ('$ip','$email','$title','$content','$s')";
			break;
	}
	
	mysql_query($sql);
}

?>