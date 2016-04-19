<?php
	namespace Home\Controller;
	use Think\Controller;

	class AutoController extends Controller{
		public function _initialize(){
	
		if(is_localhost()){
           
		}else{
			dump('不是本机访问');
			//exit;
		}
		}


       public function completed(){
       	if(isset($_GET['day'])){
       		$day=$_GET['day'];
       	}	else{
       		$day=7;
       	}	
		
		//dump((time()-$day*86400));exit;
		$a=M('order');
		$order_map['status']=3;
		$order_map['staff_confirm_time']=array('lt',(time()-$day*86400));
		$order_map['staff_confirm_time']=array('neq',0);
		$b=$a->where($order_map)->select();
		//dump($b);exit;
		foreach ($b as $k => $v) {
			    $order_id['order_id']=$v['order_id'];
				$order_set['status']=4;
				 $order_set['user_confirm_time']=time();
				$c=$a->where($order_id)->save($order_set);
				dump($c);
				if(!$c){
                sendmail('order update set error.'.date("Y-m-d H:i"),'error','164773165@qq.com');
                return false;
                exit;
				}
		
		}
       }
	
//设置技术员的每日最大接机数;
		//参数:max,数字类型,get类型,为每日最大接机数;
	
		public function set_staff_max(){

			if(isset($_GET['max'])){
            $staff_max_data['max']=$_GET['max'];
			}else{
            $staff_max_data['max']=2;
			}
			$a=M('staff');
			$staff_map['status']=0;
			$b=$a->where($staff_map)->select();
			foreach ($b as $k => $v) {
				if($v['max']!=$staff_max_data['max']){
					$staff_set['staff_id']=$v['staff_id'];
				$c=$a->where($staff_set)->save($staff_max_data);
				dump($c);
				if(!$c){
                sendmail('staff_max set error.'.date("Y-m-d H:i"),'error','164773165@qq.com');
                return false;
                exit;
				}	
				}
			
			}
			echo 'success';
			return true;

		}
	}
?>