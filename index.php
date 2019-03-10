<?php
//register_shutdown_function(function(){ var_dump(error_get_last()); });die();

// import flight framework
require_once 'lib/flight/Flight.php';

// load config
require_once 'config.php';

// database drivers
require_once __DIR__ .'/lib/Medoo.php';
require_once __DIR__ .'/module/MySQL.php';
Flight::register('db', Flight::get('dbconfig')['database_type'], array(Flight::get('dbconfig')));

session_start();

// mobile detect
require_once __DIR__.'/lib/MobileDetect/Mobile_Detect.php';
Flight::register('mdetect', 'Mobile_Detect');

// models
require_once __DIR__ .'/module/Login.php';
Flight::register('login', 'Login', array(Flight::db(), Flight::get('sysconfig')));
require_once __DIR__ .'/module/User.php';
Flight::register('user', 'User', array(Flight::db(), Flight::get('sysconfig')));
require_once __DIR__ .'/module/Order.php';
Flight::register('order', 'Order', array(Flight::db(), Flight::get('sysconfig')));
require_once __DIR__ .'/module/WeChat.php';
Flight::register('wechat', 'WeChat', array(Flight::db(), Flight::get('sysconfig'), Flight::get('apiconfig')));
require_once __DIR__ .'/module/Helper.php';
Flight::register('helper', 'Helper', array(Flight::db(), Flight::get('sysconfig')));
require_once __DIR__ .'/module/Config.php';
Flight::register('config', 'Config', array(Flight::db(), Flight::get('sysconfig')));

// templates
Flight::set('flight.views.path', 'template');

// routes
Flight::route('/api/*', function(){
    //header('Content-type: application/json');
    return true;
});


/*
 *
 * Authentication
 *
 */

Flight::route('/api/getotp', function(){
    $cellnum = Flight::request()->data->cellnum;
    if (empty($cellnum)){
        Flight::json(array("code"=>444));
    } else if (isset($_SESSION['wx_openid'])){
        $req = Flight::login()->getOTP($cellnum);
        if ($req["code"] == 200){
            $_SESSION['last_otp_time'] = time();
        }
        Flight::json($req);
    } else {
        Flight::json(array("code"=>400));
    }
    
    return false;
});

Flight::route('/api/login', function(){
    $uid = Flight::request()->data->uid;
    $token = Flight::request()->data->token;
    if (empty($uid) || empty($token)){
        Flight::json(array("code"=>400));
        return false;
    }

    $login = Flight::login()->handleCallback($uid, $token);
    if ($login['code'] != 200) {
        Flight::json($login);
        return false;
    }

    $_SESSION['uid'] = $uid;
    $_SESSION['profile'] = $login['data'];
    //Flight::user()->updateData($uid, $login['data']);
    $_SESSION['basic'] = Flight::user()->getBasicByUid($uid);
    
    if ($_SESSION['basic'] == null){
        $new = 1;
        if (count($_SESSION['profile']['vip']) > 0){
            $data = array("name"=>$_SESSION['profile']['vip'][0]['name'], "phone"=>$_SESSION['profile']['cell'], "vip"=>1);
            $resp = Flight::user()->addUser($_SESSION['uid'], $data);
            Flight::login()->updateData($_SESSION['uid'], $data);
            $_SESSION['basic'] = Flight::user()->getBasicByUid($_SESSION['uid']);
            $new = 0;
        }

    } else {
        $new = 0;
    }
    
    if (isset($_SESSION['wx_openid'])){
        $checkbind = Flight::wechat()->checkBind($_SESSION['wx_openid']);
        if ($checkbind['code'] == 200){
            if ($checkbind['data']['user_id'] != $uid){
                Flight::wechat()->deleteBind($_SESSION['wx_openid']);
            }
        } else if ($new == 0) {
            Flight::wechat()->addBind($_SESSION['wx_openid'], $uid);
        }
    }
        
    Flight::json(array('code'=>200, 'new'=>$new, 'profile'=>$_SESSION['profile'], 'basic'=>$_SESSION['basic']));
    return false;
});

Flight::route('/api/user/new', function(){
    $name = Flight::request()->data->name;
    if (empty($name)){
        Flight::json(array("code"=>444));
        return false;
    }
    if (isset($_SESSION['uid'])){
        $data = array("name"=>$name, "phone"=>$_SESSION['profile']['cell'], "vip"=>0);
        $resp = Flight::user()->addUser($_SESSION['uid'], $data);
        Flight::login()->updateData($_SESSION['uid'], $data);
        $_SESSION['basic'] = Flight::user()->getBasicByUid($_SESSION['uid']);
        Flight::json($resp);
    } else {
        Flight::json(array('code'=>401));
    }
    return false;
});

Flight::route('/api/wechat/code',function(){
    $code = Flight::request()->data->code;
    if (empty($code)){
        Flight::json(array("code"=>444));
        return false;
    }
    $wx = Flight::wechat()->checkCode($code);
    if ($wx['code'] == 200) {
        $_SESSION['wx_openid'] = $wx['data']->openid;
        $_SESSION['wx_skey'] = $wx['data']->session_key;
        $isbind = Flight::wechat()->checkBind($_SESSION['wx_openid']);
        if ($isbind["code"] == 200){
            $profile = Flight::login()->getProfile($isbind["data"]["user_id"]);
            
            $_SESSION['uid'] = $isbind["data"]["user_id"];
            $_SESSION['profile'] = $profile['data'];
            //Flight::user()->updateData($_SESSION['uid'], $profile['data']);
            $_SESSION['basic'] = Flight::user()->getBasicByUid($_SESSION['uid']);
            Flight::json(array('code'=>200, 'profile'=>$_SESSION['profile'], 'basic'=>$_SESSION['basic']));
        } else {
            Flight::json(array("code"=>204));
        }
    } else {
        Flight::json($wx);
    }
    return false;
});

Flight::route('/api/logout', function(){
    unset($_SESSION['uid']);
    unset($_SESSION['profile']);
    unset($_SESSION['basic']);
    if(isset($_SESSION['wx_openid'])){
        Flight::wechat()->deleteBind($_SESSION['wx_openid']);
    }
    return false;
});

/*
 *
 * Personal infomation
 *
 */

Flight::route('/api/my/*', function(){
    if (!isset($_SESSION['uid'])){
        Flight::json(array('code'=>401));
        return false;
    }
    return true;
});

Flight::route('/api/my/device', function(){
    Flight::json(array('code'=>200,
                   'data'=>Flight::order()->getUserDevice($_SESSION['basic']['user_id'])
    ));
    return false;
});

Flight::route('/api/my/device/add', function(){
    $brand = Flight::request()->data->newDeviceBrand;
    $model = Flight::request()->data->newDeviceModel;
    $date  = Flight::request()->data->newDeviceDate;
    if (empty($brand) || empty($model) || empty($date)){
        Flight::json(array("code"=>444));
    } else {
        Flight::json(Flight::order()->addUserDevice($_SESSION['basic']['user_id'], $brand, $model, $date));
    }
    return false;
});

Flight::route('/api/my/order', function(){
    if ($_SESSION['basic']['staff_id'] > 0){
        Flight::json(array('code'=>200,
                           'data'=>Flight::order()->getStaffOrders($_SESSION['basic']['staff_id'])
            ));
    } else {
        Flight::json(array('code'=>200,
                           'data'=>Flight::order()->getUserOrders($_SESSION['basic']['user_id'])
            ));
    }
    return false;
});

Flight::route('/api/my/order/active', function(){
    Flight::json(array('code'=>200, 'data'=>count(Flight::order()->getUserActiveOrders($_SESSION['basic']['user_id']))));
    return false;
});

Flight::route('/api/my/order/new', function(){
    $name = Flight::request()->data->name;
    $detail = Flight::request()->data->detail;
    $device = Flight::request()->data->selectDevice;
    $brand = Flight::request()->data->newDeviceBrand;
    $model = Flight::request()->data->newDeviceModel;
    $date  = Flight::request()->data->newDeviceDate;
    
    /*if ($name != ""){
        Flight::user()->updateData($_SESSION['uid'], array("name"=>$name));
    }*/
    if (count(Flight::order()->getUserActiveOrders($_SESSION['basic']['user_id'])) > 0){
        Flight::json(array('code'=>403));
    } else {
        if ($device == 0){
            $req = Flight::order()->addUserDevice($_SESSION['basic']['user_id'], $brand, $model, $date);
            if ($req["code"] != 200) {
                Flight::json($req);
                return false;
            } else {
                $device = $req["data"];
            }
        }
        $data["user_id"] = $_SESSION['basic']['user_id'];
        $data["vip"] = (count($_SESSION['profile']['vip']) > 0) ? 1:0;
        $data["computer_id"] = $device;
        $data["description"] = $detail;
        Flight::json(Flight::order()->newOrder($data));
    }
    return false;
});

/*
 *
 * Order Management
 *
 */

Flight::route('/api/order/*', function(){
    if (!isset($_SESSION['uid'])){
        Flight::json(array('code'=>401));
        return false;
    }
    return true;
});

Flight::route('/api/order/staff/@staff_id', function($staff_id){
    Flight::json(Flight::order()->getStaffOrders($staff_id));
    return false;
});

Flight::route('/api/order/user/@user_id', function($user_id){
    Flight::json(Flight::order()->getUserOrders($user_id));
    return false;
});

Flight::route('/api/order/id/@order_num', function($order_num){
    $id = ($_SESSION['basic']['staff_id'] > 0) ? $_SESSION['basic']['staff_id'] : $_SESSION['uid'];
    if (Flight::order()->checkBelong($id, $order_num)){
        Flight::json(array('code'=>200, 'data'=>Flight::order()->getOrderDetail($order_num)));
        return false;
    }
    return false;
});

Flight::route('/api/order/id/@order_num/finish', function($order_num){
    $id = ($_SESSION['basic']['staff_id'] > 0) ? $_SESSION['basic']['staff_id'] : $_SESSION['uid'];
    if (Flight::order()->checkBelong($id, $order_num)){
        Flight::json(array('code'=>200, 'data'=>Flight::order()->finishOrder($order_num)));
        return false;
    }
    return false;
});

Flight::route('/api/order/id/@order_num/cancel', function($order_num){
    if (Flight::order()->checkBelong($_SESSION['uid'], $order_num)){
        Flight::json(array('code'=>200, 'data'=>Flight::order()->finishOrder($order_num)));
        return false;
    }
    return false;
});

/*
 *
 * System Management
 *
 */

Flight::route('/api/system/*', function(){
    if (!isset($_SESSION['uid']) || !in_array("10", $_SESSION['profile']["permissions"])){
        Flight::json(array('code'=>401));
        return false;
    }
    return true;
});

Flight::route('GET /api/system/config/@key', function($key){
    Flight::json(Flight::config()->showConfig($key));
    return false;
});

Flight::route('POST /api/system/config/@key', function($key){
    $value = Flight::request()->data->value;
    Flight::json(Flight::config()->changeConfig($key, $value));
    return false;
});

/*
 *
 * Public Infomation
 *
 */

Flight::route('/api/status/available', function(){
    $vip = false;
    if (isset($_SESSION['profile']['vip']) && count($_SESSION['profile']['vip']) > 0){
        $vip = true;
    }
    $count = Flight::order()->getAvailable($vip);
    Flight::json(array('code'=>200, 'data'=>array('count'=>$count, 'text'=>Flight::helper()->getLimitTip($count))));
    return false;
});

Flight::route('/api/announce', function(){
    Flight::json(Flight::helper()->getAnnounce());
    return false;
});

Flight::start();

?>
