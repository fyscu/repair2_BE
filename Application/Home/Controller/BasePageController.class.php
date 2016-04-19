<?php
namespace Home\Controller;
use Think\Controller;


class BasePageController extends Controller
{

    private $_fyuc = null;
    /*------初始化...------*/
    public function _initialize()
    {
        if (C('STOP_REPAIR')) {
            $this->error('您好,由于临近期中考试，飞扬报修系统暂时关闭系统。系统重新开放后，我们将会在四川大学飞扬俱乐部官方微信/微博进行通知，尽请留意！', '', 8);
            exit;
        }

        if ($_GET['token']) {
            // include MODULE_PATH.'Common/fyuc.class.php';
            // $this->_fyuc = new \FYUC(C('APP_ID'),C('APP_KEY'));
            if ($_GET['token'] != $_SESSION['token']) {
                session(null);
                session('token', $_GET['token']);
                redirect('/Home/AccountPage/ucLogin?token=' . $_GET['token'].'&account='.$_GET['account']);
                exit;
            }

            // if (!$this->_fyuc->processCallback()) {
            //     $this->error('登录超时,请重新登录', $this->_fyuc->loginUrl(C('UC_CALLBACK')));
            // }
        } else if ($_SESSION['token']) {
            // $self = __SELF__;
            // redirect(__SELF__ . '?token=' . $_SESSION['token'].'&account='.$_GET['account']);
        } else {


              if(C('DEBUG')){
session('user_id',37);
            session('type',1);

              }else{
                        not_login();

              }
        }
    }
}

?>
