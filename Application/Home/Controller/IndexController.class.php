<?php
	namespace Home\Controller;
	use Think\Controller;


	class IndexController extends BasePageController {
        public function _initialize(){
            parent::_initialize();
            if(is_admin_login()){
                redirect('/Home/AdminPage/index?token='.$_GET['token'].'&account='.$_GET['account']);
            }elseif(is_staff_login()){
                redirect('/Home/StaffPage/not?token='.$_GET['token'].'&account='.$_GET['account']);
            }
        }

        public function index(){
            //调用checkComputer函数，判断是否注册电脑，如果没有则跳转到注册页面注册
            if(!checkComputer($_SESSION['user_id'])){
                redirect('/Home/IndexPage/registerpc?token='.$_GET['token'].'&account='.$_GET['account']);
            }

            //调用checkOrder函数，判断是有订单未完成，如果有则跳转至【我的订单】
            if(checkOrder($_SESSION['user_id'])){
                $this->error('您有尚未完成的订单，请确认完成后再进行报修！','/Home/IndexPage/order?token='.$_GET['token'].'&account='.$_GET['account']);
            }

            //查找用户信息
            $a=M('userextend');
            $map['user_id']=$_SESSION['user_id'];
            $userextend=$a->where($map)->find();

            //用户电脑信息
            $b=M('computer');
            $computer=$b->where($map)->order('time desc')->select();

            //用户类型 type
            $c=M('user');
            $user=$c->where($map)->find();

            $d=M('order');
            $ordermap['time']=array('egt',get_week_start());
            $ordermap['status']=array('in','0,1,3,4');
            $order=$d->where($ordermap)->count();

            $set=M('set');
            $setting=$set->where('id=1')->find();
            if($user['type']==1 && $order>=$setting['week_max']){
							$tips="当前接机量已超过限制".$setting['week_max']."台，但由于你尊贵的会员身份，你现在依然可以报修，非会员本周内无法报修。";
							$repair_status="0";
						}elseif($order>=$setting['week_max']){
                $tips="本周接机量已达上限，请等待下周报修。";
                $repair_status="1";
            }else{
                $tips="您可继续报修,当接机量超过".$setting['week_max']."台，本周内无法报修。";
                $repair_status="0";
            }
            $this->assign('repair_status',$repair_status);
            $this->assign('order_count',$order);
            $this->assign('tips',$tips);
            $this->assign('user',$userextend);//赋值输出 userextend表 中的用户扩展信息
            $this->assign('computer_list',$computer);
            $this->assign('type',$user);
            $this->display();
        }

    }
?>
