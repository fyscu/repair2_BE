<?php
	namespace Home\Controller;
	use Think\Controller;

	class AccountController extends Controller{
		public function register(){
			if(IS_POST){
				if($_POST['name']==""){
					$data['status']=0;
					$data['info']='真实姓名不能为空!';
					$this->ajaxReturn($data,'JSON');
					exit;
				}
				$a=M('user');
				$user_data['type']=0;
				$user_data['ucid']=$_SESSION['ucid'];
				$b=$a->add($user_data);
				if($b){
					$c=M('userextend');
					$user_extend_data['user_id']=$b;
					$user_extend_data['name']=$_POST['name'];
					$user_extend_data['phone']=$_SESSION['tel'];
					$user_extend_data['register_time']=time();
					$d=$c->add($user_extend_data);
					if($d){
						session('user_id',$b);
						session('type','0');
						$data['status']=1;
						$data['info']='add user info successed!';
						$this->ajaxReturn($data,'JSON');
					}else{
						//$data['in']=$_POST['name'];
						$data['status']=0;
						$data['info']='add user info failed!';
						$this->ajaxReturn($data,'JSON');
					}
		
				}else{
					$data['status']=0;
					$data['info']='add user info failed!';
					$this->ajaxReturn($data,'JSON');
				}
			}else{
				$data['status']=0;
				$data['info']='no post data!';
				$this->ajaxReturn($data,'JSON');
			}
		}
		public function admin_login(){
			not_login();
		}

	}
?>