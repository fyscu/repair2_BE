<?php
date_default_timezone_set('Asia/Shanghai');

Flight::set('dbconfig', [
	// required
	'database_type' => 'DB_MySQL',
	'database_name' => 'fyscu_repair',
    'server' => 'localhost',
    'port' => 3306,
	'username' => '',
	'password' => '',
	'charset' => 'utf8',

	// driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
	'option' => [
		PDO::ATTR_CASE => PDO::CASE_NATURAL
	]
]);

Flight::set('sysconfig',[
    'uid' => 'ucid',
    'user_id' => 'user_id',
    'staff_id' => 'staff_id',

    'computer_table' => 'fy_computer',
    'order_table' => 'fy_order',
    'orderext_table' => 'fy_orderextend',
    'staff_table' => 'fy_staff',
    'user_table' => 'fy_user',
    'userext_table' => 'fy_userextend',
    'config_table' => 'fy_config',
    'wxuser_table' => 'fy_wxuser',
    'wxpush_table' => 'fy_wxpush',
    
    // lifetime(s)
    'session_life' => 259200,
    
    'sso_api' => 'https://sso.fyscu.com/api/',
    'sso_appid' => "1010",
    'sso_secret' => "",
]);