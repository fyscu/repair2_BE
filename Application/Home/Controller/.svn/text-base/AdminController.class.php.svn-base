<?php
namespace Home\Controller;
use Think\Controller;
class AdminController extends BaseApiController {
  			public function _initialize(){
  				parent::_initialize();
			//dump($_SESSION);exit;
			if(!is_admin_login()){


			}
		}
	//首页视图
	public function index(){

	$a=M('order');

	$b['all']=$a->count();

	$completed['status']=4;
	$b['completed']=$a->where($completed)->count();
	$confirmed['status']=3;
	$b['confirmed']=$a->where($confirmed)->count();
	$canceled['status']=2;
	$b['canceled']=$a->where($canceled)->count();
	$distributed['status']=1;
	$b['distributed']=$a->where($distributed)->count();
	$submitted['status']=array('in','0,5');
	$b['submitted']=$a->where($submitted)->count();
$this->assign('count',$b);
		$this->display();
	}
	public function staff_max_change(){
		if(IS_AJAX){
			$a=M('staff');
			$staff_map['staff_id']=$_POST['staff_id'];
			$staff_data['max']=$_POST['max'];
			$b=$a->where($staff_map)->save($staff_data);
			if($b){
             $data['status']=1;
             $data['info']='change max success';
			}else{
             $data['status']=0;
             $data['info']='change max error';
			}
			$this->ajaxReturn($data);
		}
	}
		public function staff_status_change(){
		if(IS_AJAX){
			$a=M('staff');
			$staff_map['staff_id']=$_POST['staff_id'];
			$staff_data['status']=$_POST['status'];
			$b=$a->where($staff_map)->save($staff_data);
			if($b){
             $data['status']=1;
             $data['info']='change status success';
			}else{
             $data['status']=0;
             $data['info']='change status error';
			}
			$this->ajaxReturn($data);
		}
	}
	//调用类型:GET
	//调用参数:p,传入数字类型,第几页;默认为1;
	//调用参数:pagecount,传入数字类型，这一页输出多少数据;默认为20;
	//调用参数:status,技术员状态，有3个选项,包括yes,no,all;默认为all

	public function staff(){
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
 	$a = M('staff');
 	if(isset($_GET['status'])){
 		if($_GET['status']=='yes'){
	$staff_map['status']=0;
 		}
 		if($_GET['status']=='no'){
 				$staff_map['status']=1;
 		}

 	}

 	$b = $a->where($staff_map)->page($p.','.$pagecount)->select();
  $f=M('order');
   $order_map['status']=array('in','1,3');
 	foreach ($b as $k => $v) {
 	 $c=M('userextend');
 	 $userextend_map['user_id']=$v['user_id'];
    $d=$c->where($userextend_map)->find();
     $order_map['staff_id']=$v['staff_id'];
     $g=$f->where($order_map)->count();
   $h['doing_count']=$g;
   $e[$k]=array_merge($h,$d,$v);
 	}


 	$count      = $a->where($staff_map)->count();// 查询满足要求的总记录数
 	$Page       = new \Think\Page($count,$pagecount);// 实例化分页类 传入总记录数和每页显示的记录数
 	$show       = $Page->show();// 分页显示输出
 	$this->assign('page',$show);// 赋值分页输出
    //$this->ajaxReturn($e);
 	$this->assign('staff_list',$e);// 赋值数据集
 //	dump($e);exit;
		$this->display();
	}
//调用类型:GET
	//调用参数:p,传入数字类型,第几页;默认为1;
	//调用参数:pagecount,传入数字类型，这一页输出多少数据;默认为20;
	//调用参数:key,传入要搜索的值，任意类型;
public function order_search(){
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
if(isset($_GET['key'])){
	$key=$_GET['key'];
}



/*搜索开始*/
 	$a = M('userextend');
 	$b=M('staff');
 	$c=M('order');
 	  $h=M('computer');
 	  $j=M('orderextend');
 	$user_map['name']  = array('like', '%'.$key.'%');
 	$user_map['phone']  = array('like','%'.$key.'%');
 	$user_map['_logic'] = 'or';
 	$r = $a->where($user_map)->select();

foreach ($r as $k => $v) {
	$staff_map1['user_id']=$v['user_id'];
	$d=$b->where($staff_map1)->find();
	if($d){
	$staff_id_map[]=$d['staff_id']; }
}
foreach ($r as $k => $v) {
$user_id_map[]=$v['user_id'];
}
$order_map_all['_logic']='or';
if(!empty($staff_id_map)){
$order_map_all['staff_id']=array('in',$staff_id_map);}
if(!empty($user_id_map)){
$order_map_all['user_id']=array('in',$user_id_map);}
if(!empty($key)){
$order_map_all['number']=array('like','%'.$key.'%');}

$e=$c->where($order_map_all)->page($p.','.$pagecount)->select();

	foreach ($e as $k => $v) {
 	switch ($v['status']) {
 		case '0':
 		$v['status']='系统尚未分配技术员';
 			break;
 		case '1':
 		$v['status']='待技术员确认';
 		     break;
 		case '2':
 		$v['status']='用户已取消';

 			break;
 		case '3':
 		$v['status']='技术员已确认';
 		     break;
 		case '4':
 		$v['status']='已完成';
 			# code...
 			break;
 		case '5':
 		$v['status']='系统重新分配技术员';
 		     break;

 		default:
 		$v['status']='未知状态';
 			break;
 	}

 	$userextend_map['user_id']=$v['user_id'];
    $f=$a->where($userextend_map)->find();
    $g['user_name']=$f['name'];
    $g['user_phone']=$f['phone'];

    $computer_map['computer_id']=$v['computer_id'];

    $i=$h->where($computer_map)->find();

    $orderextend_map['order_id']=$v['order_id'];
    $l=$j->where($orderextend_map)->find();
   //dump($v);
    if($v['staff_id']>0){


    $staff_map['staff_id']=$v['staff_id'];
    $m=$b->where($staff_map)->find();
    $n['staff_email']=$m['email'];
    $n['max']=$m['max'];
     $userextend_map['user_id']=$m['user_id'];
    $o=$a->where($userextend_map)->find();
    $n['staff_name']=$o['name'];
    $n['staff_phone']=$o['phone'];

}else{
	 $n['staff_email']='';
    $n['max']='';
      $n['staff_name']='';
    $n['staff_phone']='';
  //  dump($m);
}
   $q[$k]=array_merge($g,$i,$l,$n,$v);
 	}

 	$count      = $c->where($order_map_all)->count();// 查询满足要求的总记录数
 	$Page       = new \Think\Page($count,$pagecount);// 实例化分页类 传入总记录数和每页显示的记录数
 	$show       = $Page->show();// 分页显示输出
 	$this->assign('page',$show);// 赋值分页输出

    //$this->ajaxReturn($e);
 	$this->assign('order_list',$q);// 赋值数据集
 	$this->display('order');

 }
	//调用类型:GET
	//调用参数:p,传入数字类型,第几页;默认为1;
	//调用参数:pagecount,传入数字类型，这一页输出多少数据;默认为20;
	//调用参数:status,用户类型，有9个选项,包括submitted,distributed,canceled,confirmed,completed,rejected,doing,done,all;默认为all
 //调用参数:staff_id,传入数字类型,技术员id
  //调用参数:user_id,传入数字类型,用户id
	public function order(){
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
 	$a = M('order');
//$order_map['status']=0;
 	if(isset($_GET['status'])){
 		switch ($_GET['status']) {
 			case 'submitted':
 			$order_map['status']=array('in','0,5');
 				break;
 			case 'distributed':
 			$order_map['status']=1;
 				break;
 			case 'canceled':
 			$order_map['status']=2;
 				break;
 			case 'confirmed':
 			$order_map['status']=3;
 				break;
 			case 'completed':
 			$order_map['status']=4;
 				break;
 			case 'rejected':
 			$order_map['status']=5;
 				break;
 			case 'doing':
 			$order_map['status']=array('in','0,1,3,5');
 			break;
 			case 'done':
 			$order_map['status']=array('in','2,4');
 			break;
 			case 'doing_staff':
 			$order_map['status']=array('in','1,3');

 			default:

 				break;
 		}

 	}

 	if(isset($_GET['staff_id'])){
 		$order_map['staff_id']=$_GET['staff_id'];
 	}
 		if(isset($_GET['user_id'])){
 		$order_map['user_id']=$_GET['user_id'];
 	}
 	$b = $a->where($order_map)->order('time desc')->page($p.','.$pagecount)->select();

 	foreach ($b as $r => $v) {
 	switch ($v['status']) {
 		case '0':
 		$v['status']='系统尚未分配技术员';
 			break;
 		case '1':
 		$v['status']='待技术员确认';
 		     break;
 		case '2':
 		$v['status']='用户已取消';

 			break;
 		case '3':
 		$v['status']='技术员已确认';
 		     break;
 		case '4':
 		$v['status']='已完成';
 			# code...
 			break;
 		case '5':
 		$v['status']='系统重新分配技术员';
 		     break;

 		default:
 		$v['status']='未知状态';
 			break;
 	}
 	 $c=M('userextend');
 	 $userextend_map['user_id']=$v['user_id'];
    $d=$c->where($userextend_map)->find();
    $h['user_name']=$d['name'];
    $h['user_phone']=$d['phone'];

    $computer_map['computer_id']=$v['computer_id'];
    $f=M('computer');
    $g=$f->where($computer_map)->find();
    $i=M('orderextend');
    $orderextend_map['order_id']=$v['order_id'];
    $j=$i->where($orderextend_map)->find();
   //dump($v);
    if($v['staff_id']>0){

	 $k=M('staff');
    $staff_map['staff_id']=$v['staff_id'];
    $l=$k->where($staff_map)->find();
    $m['staff_email']=$l['email'];
    $m['max']=$l['max'];
     $userextend_map['user_id']=$l['user_id'];
    $n=$c->where($userextend_map)->find();
    $m['staff_name']=$n['name'];
    $m['staff_phone']=$n['phone'];

}else{
	 $m['staff_email']='';
    $m['max']='';
      $m['staff_name']='';
    $m['staff_phone']='';
  //  dump($m);
}
   $e[$r]=array_merge($h,$g,$j,$m,$v);
 	}
 	$count      = $a->where($order_map)->count();// 查询满足要求的总记录数
 	$Page       = new \Think\Page($count,$pagecount);// 实例化分页类 传入总记录数和每页显示的记录数
 	$show       = $Page->show();// 分页显示输出
 	$this->assign('page',$show);// 赋值分页输出
    $cc['count']=$count;

    //$this->ajaxReturn($e);
 	$this->assign('order_list',$e);// 赋值数据集
 	//dump($e);exit;
		$this->display();
	}
	public function user_add(){
	if(IS_AJAX){


   $c=M('user');
    $user_map['user_id']=$_POST['user_id'];
   $a=$c->where($user_map)->find();
   $e=1;
   if($a['type']==2){
   $b=M('staff');
   $e=$b->where($user_map)->delete();
   }elseif($a['type']==3){
   $b=M('admin');
   $e=$b->where($user_map)->delete();
   }
   $user_data['type']=0;  
   $d=$c->where($user_map)->save($user_data);
if($d && $e){
$data['status']=1;
$data['info']='add user success';
}else{
$data['status']=0;
$data['info']=$d.$e;

}
	$this->ajaxReturn($data);	
	

	}

}
public function vip_add(){
	if(IS_AJAX){


   $c=M('user');
    $user_map['user_id']=$_POST['user_id'];
   $a=$c->where($user_map)->find();
   $e=1;
   if($a['type']==2){
   $b=M('staff');
   $e=$b->where($user_map)->delete();
   }elseif($a['type']==3){
   $b=M('admin');
   $e=$b->where($user_map)->delete();
   }
   $user_data['type']=1;  
   $d=$c->where($user_map)->save($user_data);
if($d && $e){
$data['status']=1;
$data['info']='add vip success';
}else{
$data['status']=0;
$data['info']='add vip error';

}
	$this->ajaxReturn($data);	
	

	}

}

public function staff_add(){
	if(IS_AJAX){

   $a=M('staff');
   $staff_data['email']=$_POST['email'];
   $staff_data['user_id']=$_POST['user_id'];
   $staff_data['max']='2';
   $b=$a->add($staff_data);
   $c=M('user');

    $user_map['user_id']=$_POST['user_id'];
   $e=$c->where($user_map)->find();
   if($e['type']==3){
   $f=M('admin');
   $g=$f->where($user_map)->delete();
   }
   $user_data['type']=2;
   $user_map['user_id']=$_POST['user_id'];
   $d=$c->where($user_map)->save($user_data);
if($b && $d){
$data['status']=1;
	$data['info']='add staff success';
}else{
$data['status']=0;
$data['info']='add staff error';

}
	$this->ajaxReturn($data);	
	

	}else{

	}

}

public function admin_add(){
	if(IS_AJAX){

   $a=M('admin');
   $admin_data['email']=$_POST['email'];
   $admin_data['user_id']=$_POST['user_id'];
   $admin_data['password']=md5($_POST['password']);
   $b=$a->add($admin_data);
   $c=M('user');
    $user_map['user_id']=$_POST['user_id'];
   $f=$c->where($user_map)->find();
   if($f['type']==2){
   $g=M('staff');
   $h=$g->where($user_map)->delete();
   }
   $user_data['type']=3;
   $user_map['user_id']=$_POST['user_id'];
   $d=$c->where($user_map)->save($user_data);

if($b && $d){
$data['status']=1;
	$data['info']='add admin success';
}else{
$data['status']=0;
$data['info']='add admin error';

}
	$this->ajaxReturn($data);	
	

	}else{

	}

}
	//调用类型:GET
	//调用参数:p,传入数字类型,第几页;默认为1;
	//调用参数:pagecount,传入数字类型，这一页输出多少数据;默认为20;
	//调用参数:type,用户类型，有5个选项,包括user,vip,staff,admin,all;默认为all
 public function user(){
 	if(isset($_GET['type'])){
 		switch ($_GET['type']) {
 			case 'user':
 				$user_map['type']=0;
 				break;
 			case 'vip':
 				$user_map['type']=1;
 				break;
 				case 'staff':
 				$user_map['type']=2;
 				break;
 				case 'admin':
 				$user_map['type']=3;
 				break;
 			default:

 				break;
 		}
}
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
 	$a = M('user');
 	$b = $a->where($user_map)->page($p.','.$pagecount)->select();

 	foreach ($b as $k => $v) {
 	 $c=M('userextend');
 	 $userextend_map['user_id']=$v['user_id'];
    $d=$c->where($userextend_map)->find();

   $e[$k]=array_merge($d,$v);
 	}

 	$count      = $a->where($user_map)->count();// 查询满足要求的总记录数
 	$Page       = new \Think\Page($count,$pagecount);// 实例化分页类 传入总记录数和每页显示的记录数
 	$show       = $Page->show();// 分页显示输出
 	$this->assign('page',$show);// 赋值分页输出
    $b['count']=$count;

    //$this->ajaxReturn($e);
 	$this->assign('user_list',$e);// 赋值数据集
 	$this->display();

 }
 //调用类型:GET
	//调用参数:p,传入数字类型,第几页;默认为1;
	//调用参数:pagecount,传入数字类型，这一页输出多少数据;默认为20;
	//调用参数:type,用户类型，有5个选项,包括user,vip,staff,admin,all;默认为all
 public function user_search(){
 	if(isset($_GET['type'])){
 		switch ($_GET['type']) {
 			case 'user':
 				$user_map['type']=0;
 				break;
 			case 'vip':
 				$user_map['type']=1;
 				break;
 				case 'staff':
 				$user_map['type']=2;
 				break;
 				case 'admin':
 				$user_map['type']=3;
 				break;
 			default:

 				break;
 		}
}
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
if(isset($_GET['key'])){
	$key=$_GET['key'];
}

 	$a = M('userextend');
 	$user_map['name']  = array('like', '%'.$key.'%');
 	$user_map['phone']  = array('like','%'.$key.'%');
 	$user_map['_logic'] = 'or';
 	$b = $a->where($user_map)->page($p.','.$pagecount)->select();
$c=M('user');
foreach ($b as $k => $v) {
	$user_map2['user_id']=$v['user_id'];
	$b[$k]['type']=$c->where($user_map2)->getField('type');
}

 	$count      = $a->where($user_map)->count();// 查询满足要求的总记录数
 	$Page       = new \Think\Page($count,$pagecount);// 实例化分页类 传入总记录数和每页显示的记录数
 	$show       = $Page->show();// 分页显示输出
 	$this->assign('page',$show);// 赋值分页输出

    //$this->ajaxReturn($e);
 	$this->assign('user_list',$b);// 赋值数据集
 	$this->display('user');

 }
 //调用类型:GET
	//调用参数:p,传入数字类型,第几页;默认为1;
	//调用参数:pagecount,传入数字类型，这一页输出多少数据;默认为20;
	//调用参数:type,用户类型，有5个选项,包括user,vip,staff,admin,all;默认为all
 public function staff_search(){
 	if(isset($_GET['status'])){
 		switch ($_GET['type']) {
 			case 'yes':
 				$user_map['type']=0;
 				break;
 			case 'no':
 				$user_map['type']=1;
 				break;

 			default:

 				break;
 		}
}
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
if(isset($_GET['key'])){
	$key=$_GET['key'];
}

 	$a = M('userextend');
 	$user_map['name']  = array('like', '%'.$key.'%');
 	$user_map['phone']  = array('like','%'.$key.'%');
 	$user_map['_logic'] = 'or';
 	$b = $a->where($user_map)->select();
    $c=M('staff');
   foreach ($b as $k => $v) {
	$staff_map1['user_id']=$v['user_id'];
	$d=$c->where($staff_map1)->find();
	if($d){
	$staff_id_map[]=$d['staff_id']; }
}
$staff_map['staff_id']=array('in',$staff_id_map);
    $e=$c->where($staff_map)->page($p.','.$pagecount)->select();
     $f=M('order');
   $order_map['status']=array('in','1,3');
 	foreach ($e as $k => $v) {
 	 $userextend_map['user_id']=$v['user_id'];
    $g=$a->where($userextend_map)->find();
     $order_map['staff_id']=$v['staff_id'];
     $h=$f->where($order_map)->count();
   $i['doing_count']=$h;
   $l[$k]=array_merge($i,$g,$v);
 	}
 	$count      = $c->where($staff_map)->count();// 查询满足要求的总记录数
 	$Page       = new \Think\Page($count,$pagecount);// 实例化分页类 传入总记录数和每页显示的记录数
 	$show       = $Page->show();// 分页显示输出
 	$this->assign('page',$show);// 赋值分页输出

    //$this->ajaxReturn($e);
 	$this->assign('staff_list',$l);// 赋值数据集
 	$this->display('staff');

 }
}