<?php
namespace Home\Controller;
use Think\Controller;



	class StaffController extends BaseApiController{
        public function _initialize(){
            parent::_initialize();
            if(is_admin_login()){
                $data['status']=0;
                $data['info']='管理员暂时不能报修';
                $this->ajaxReturn($data,'JSON');
                exit;
            }elseif(is_user_login()){
                $data['status']=0;
                $data['info']='你没有权限访问';
                $this->ajaxReturn($data,'JSON');
                exit;
            }
        }


        public function handle(){
            if(IS_POST){
               
                 $a=M('order');
                
				
				
                $data_order['number']=make_number();
                $data_order['user_id']=$_POST['user_id'];
                $data_order['time']=time();
                $data_order['vip']=$_POST['vip'];
                $data_order['staff_id']=$_SESSION['staff_id'];
                $data_order['status']=3;
                $data_order['distribute_time']=time();
                $data_order['staff_confirm_time']=time();
                $data_order['mode']=1;
                $data_order['computer_id']=$_POST['computer_id'];
                $order=$a->add($data_order);

                if($order){
                    $b=M('orderextend');
                    $data_extend['order_id']=$order;
                    $data_extend['description']=$_POST['description'];
                    $orderextend=$b->add($data_extend);
                    if($orderextend){
                        $data['status']=1;
                        $this->ajaxReturn($data,'JSON');

                    }else{
                        $data['status']=0;
                        $this->ajaxReturn($data,'JSON');
                    }

            }else{
                $data['status']=0;
                        $this->ajaxReturn($data,'JSON');
            }
        
        }
           }
        

public function search_user()
{

    if (IS_POST) {
        $a = M('userextend');
        $map['phone'] = $_POST['phone'];

        $userextend = $a->where($map)->find();
        if ($userextend) {
            $d = M('user');
            $map2['type'] = array('not in', '2');
            $map2['user_id'] = array('eq', $userextend['user_id']);
            $e = $d->where($map2)->find();
            if ($e) {
                //用户电脑信息
                $b = M('computer');
                $map1['user_id'] = $userextend['user_id'];
                $computer = $b->where($map1)->order('time desc')->select();
                if ($computer) {
                    $data['user_id'] = $userextend['user_id'];
                    $data['status'] = 2;
                    $data['error_code'] = '0';
                    $this->ajaxReturn($data, 'JSON');
                } else {
                    $data['status'] = 1;
                    $data['error_code'] = 'NO_COMPUTER';
                    $this->ajaxReturn($data, 'JSON');
                }
            } else {
                $data['status'] = 0;
                $data['error_code'] = 'NO_USER';
                $this->ajaxReturn($data, 'JSON');
            }

        } else {
            $data['status'] = 0;
            $data['error_code'] = 'NO_USER';
            $this->ajaxReturn($data, 'JSON');
        }

    }


}
}

?>