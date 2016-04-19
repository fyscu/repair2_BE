<?php
	namespace Home\Controller;
	use Think\Controller;
	class AccountPageController extends Controller{

		private $_fyuc = null;

		public function _initialize(){
			include MODULE_PATH.'Common/fyuc.class.php';
			$this->_fyuc = new \FYUC(C('APP_ID'),C('APP_KEY'));
		}

		public function logout(){
			session(null);
			$this->success('退出成功！',$this->_fyuc->loginUrl(C('UC_CALLBACK')));
		}

		public function ucLogin(){
			if(!$_GET['token']){
				$this->error('没有token,请重新登录',$this->_fyuc->loginUrl(C('UC_CALLBACK')));
				exit;
			}

			if(!$this->_fyuc->processCallback()){
				$this->error('获取资料失败,请重新登录',$this->_fyuc->loginUrl(C('UC_CALLBACK')));
				exit;
			}else{
				session('ucid',$_GET['account']);
				session('token',$_GET['token']);
				$a=is_user($_GET['account'],'ucid');
				$telUser=is_userExtend($_GET['account']);

				if($a){
					$user_id=$a['user_id'];
					$type=$a['type'];
					session('user_id',$user_id);
					session('type',$type);


					if($_SESSION['type']==3){
						$b=M('admin');
						$admin_map['user_id']=$user_id;
						$c=$b->where($admin_map)->find();
						session('admin_id',$c['admin_id']);

						redirect('/Home/AdminPage/index?token='.$_GET['token'].'&account='.$_GET['account']);
					}elseif($_SESSION['type']==2){
						$b=M('staff');
						$staff_map['user_id']=$user_id;
						$c=$b->where($staff_map)->find();
						//dump($c);exit;
						session('staff_id',$c['staff_id']);
						redirect('/Home/StaffPage/not?token='.$_GET['token'].'&account='.$_GET['account']);

					}else{
						redirect('/Home/Index/index?token='.$_GET['token'].'&account='.$_GET['account']);
					}
				}else if($telUser){
					$user=M('user');
					$usermap['user_id']=$telUser['user_id'];
					$data['ucid']=$_GET['account'];
					
					$user->where($usermap)->save($data);
					$user_id=$telUser['user_id'];

					$type=$user->where($usermap)->getField('type');

					session('user_id',$user_id);
					session('type',$type);
					if($_SESSION['type']==3){
						$b=M('admin');
						$admin_map['user_id']=$user_id;
						$c=$b->where($admin_map)->find();
						session('admin_id',$c['admin_id']);

						redirect('/Home/AdminPage/index?token='.$_GET['token'].'&account='.$_GET['account']);
					}elseif($_SESSION['type']==2){
						//echo '2';exit;
						$b=M('staff');
						$staff_map['user_id']=$user_id;
						$c=$b->where($staff_map)->find();
						//dump($c);exit;
						session('staff_id',$c['staff_id']);
						redirect('/Home/StaffPage/not?token='.$_GET['token'].'&account='.$_GET['account']);

					}else{
						//dump($_SESSION);exit;
						redirect('/Home/Index/index?token='.$_GET['token'].'&account='.$_GET['account']);
					}
				}else{

					session('tel',$_GET['account']);
					redirect('/Home/AccountPage/register?token='.$_GET['token'].'&account='.$_GET['account']);
				}
			}
		}
		public function register(){


			if (C('STOP_REPAIR')) {
				$this->error('您好,飞扬报修系统由于一些原因暂时关闭系统。系统重新开放后，我们将会在四川大学飞扬俱乐部官方微信/微博进行通知，尽请留意！', '', 8);
				exit;
			}

			if($_GET['token'] && $_GET['account']){
if(!$this->_fyuc->processCallback()){
				$this->error('获取资料失败,请重新登录',$this->_fyuc->loginUrl(C('UC_CALLBACK')));
				exit;
			}else{

					if(is_userExtend($_SESSION['tel'])){
						$this->error('您已经注册过飞扬报修系统了,将跳转至首页','/Home/Index/index?token='.$_GET['token'].'&account='.$_GET['account']);
						exit;
					}else{
						$this->display();
					}
				}
			}else if($_SESSION['token']){
					redirect(__SELF__.'?token='.$_SESSION['token'].'&account='.$_GET['account']);
			} else {
				not_login();
			}



		}


	}
?>
