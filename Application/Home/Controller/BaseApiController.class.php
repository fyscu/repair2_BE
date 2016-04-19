<?php
namespace Home\Controller;
use Think\Controller;



class BaseApiController extends Controller
{
	private $_fyuc = null;
	/*------初始化...------*/
	public function _initialize()
	{
		if (C('STOP_REPAIR')) {
			$data['status']=0;
			$data['info']='您好,飞扬报修系统由于一些原因暂时关闭系统。系统重新开放后，我们将会在四川大学飞扬俱乐部官方微信/微博进行通知，尽请留意！';
			$this->ajaxReturn($data,'JSON');
			exit;
		}

		if(C('DEBUG')){

			//不做权限检测
		}else{

		if($_SESSION['token']){
			// include MODULE_PATH.'Common/fyuc.class.php';
			// $this->_fyuc = new \FYUC(C('APP_ID'),C('APP_KEY'));
			// if($_GET['token']!=$_SESSION['token']){
			// 	session(null);
			// 	$data['status']=0;
			// 	$data['info']='登录超时,请重新登录';
			// 	$this->ajaxReturn($data,'JSON');
			// 	exit;
			// }

			// if(!$this->_fyuc->processCallback()){
			// 	$data['status']=0;
			// 	$data['info']='登录超时,请重新登录';
			// 	$this->ajaxReturn($data,'JSON');
			// 	exit;
			// }
		} else {
			$data['status']=0;
			$data['info']='未登录,请重新登录';
			$this->ajaxReturn($data,'JSON');
			exit;
		}
	}
	}
}
?>