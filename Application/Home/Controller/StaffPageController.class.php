<?php
namespace Home\Controller;
use Think\Controller;



	class StaffPageController extends BasePageController{
        public function _initialize(){
            parent::_initialize();
            //echo 'index';exit;
            //dump($_SESSION);exit;
            if(is_admin_login()){
                redirect('/Home/AdminPage/index?token='.$_GET['token']);
            }elseif(is_user_login()){
                redirect('/Home/Index/index?token='.$_GET['token']);
            }
        }

        public function add(){

        		$this->display();

    }

public function add_order(){
     //调用checkComputer函数，判断是否注册电脑，如果没有则跳转到注册页面注册
           if(!checkComputer($_GET['user_id'])){
                $this->error('该用户尚未添加电脑！','/Home/StaffPage/add?token='.$_GET['token']);
           }

         if(!is_user($_GET['user_id'],'user_id')){
            $this->error('没有该用户','/Home/StaffPage/add?token='.$_GET['token']);
         }
         if(!isset($_GET['user_id'])){
exit;
         }

            //查找用户信息
            $a=M('userextend');
            $map['user_id']=$_GET['user_id'];
            $userextend=$a->where($map)->find();

            //用户电脑信息
            $b=M('computer');
            $computer=$b->where($map)->order('time desc')->select();

            //用户类型 type
            $c=M('user');
            $user=$c->where($map)->find();

            $this->assign('user',$userextend);//赋值输出 userextend表 中的用户扩展信息
            $this->assign('computer_list',$computer);
            $this->assign('type',$user);
            $this->display();
}

		/*---------技术员未完成的订单--------------*/
        public function not(){

        	//分页
            if(isset($_GET['p'])){
    			$p=$_GET['p'];
			}else{
    			$p='1';
			}
			if(isset($_GET['pagecount'])){
   				$pagecount=$_GET['pagecount'];
			}else{
    			$pagecount='20';
			}

			//查找该技术员的所有订单
            $a=M('order');
            $map_order['staff_id']=$_SESSION['staff_id'];
            $map_order['status']=array('in','1,3');
            $order=$a->where($map_order)->page($p.','.$pagecount)->order('time desc')->select();

            //统计未完成的订单数量
            $map_count_not['staff_id']=$_SESSION['staff_id'];
            $map_count_not['status']=array('in','1,3');//订单状态为1，3的都是未完成的订单
            $count_not=$a->where($map_count_not)->count('status');
            $this->assign('count_not',$count_not);

			//统计已完成的订单数量
            $map_count_has['staff_id']=$_SESSION['staff_id'];
            $map_count_has['status']=4;
            $count_has=$a->where($map_count_has)->count('status');
            $this->assign('count_has',$count_has);

            //从各个表中查找订单的信息，并合并到数组 $info 中
            foreach ($order as $key => $value) {
                $b=M('userextend');
                $map_userextend['user_id']=$value['user_id'];
                $userextend=$b->where($map_userextend)->find();
                $c=M('computer');
                $map_computer['computer_id']=$value['computer_id'];
                $computer=$c->where($map_computer)->find();
                $d=M('orderextend');
                $map_orderextend['order_id']=$value['order_id'];
                $orderextend=$d->where($map_orderextend)->find();
                $info[$key]=array_merge($computer,$value,$userextend,$orderextend);
            }

   			$count      = $a->where($map_count_not)->count();// 查询满足要求的总记录数
    		$Page       = new \Think\Page($count,$pagecount);// 实例化分页类 传入总记录数和每页显示的记录数
    		$show       = $Page->show();// 分页显示输出
    		$this->assign('page',$show);// 赋值分页输出
            $this->assign('info',$info);
            $this->display();
        }


		/*---------技术员已完成的订单--------------*/
        public function has(){

			if(isset($_GET['p'])){
    			$p=$_GET['p'];
			}else{
    			$p='1';
			}
			if(isset($_GET['pagecount'])){
   				$pagecount=$_GET['pagecount'];
			}else{
    			$pagecount='20';
			}

			//查找该技术员的所有订单
            $a=M('order');
            $map_order['staff_id']=$_SESSION['staff_id'];
            $map_order['status']=4;
            $order=$a->where($map_order)->page($p.','.$pagecount)->order('time desc')->select();

			//统计已完成的订单数量
            $map_count_has['staff_id']=$_SESSION['staff_id'];
            $map_count_has['status']=4;
            $count_has=$a->where($map_count_has)->count('status');
            $this->assign('count_has',$count_has);

			//统计未完成的订单数量
            $map_count_not['staff_id']=$_SESSION['staff_id'];
            $map_count_not['status']=array('in','1,3');//订单状态为1，3的都是未完成的订单
            $count_not=$a->where($map_count_not)->count('status');
            $this->assign('count_not',$count_not);

            foreach ($order as $key => $value) {
                $b=M('userextend');
                $map_userextend['user_id']=$value['user_id'];
                $userextend=$b->where($map_userextend)->find();
                $c=M('computer');
                $map_computer['computer_id']=$value['computer_id'];
                $computer=$c->where($map_computer)->find();
                $d=M('orderextend');
                $map_orderextend['order_id']=$value['order_id'];
                $orderextend=$d->where($map_orderextend)->find();
                $info[$key]=array_merge($computer,$value,$userextend,$orderextend);
            }

            $count      = $a->where($map_count_has)->count();// 查询满足要求的总记录数
    		$Page       = new \Think\Page($count,$pagecount);// 实例化分页类 传入总记录数和每页显示的记录数
    		$show       = $Page->show();// 分页显示输出
    		$this->assign('page',$show);// 赋值分页输出
            $this->assign('info',$info);
            $this->display();
        }


}

?>
