<?php
require_once("Snowflake.php");

class Order {
    private $_db;
    private $_config;
    private $_col;
    private $_join;

    /**
     * @param Database $db
     * @param array $config sysconfig in config.php
     */
    public function __construct($db, $conf){
        $this->_db = $db;
        $this->_conf = $conf;
        $this->_col =   [   $this->_conf['order_table'].".number",
                            $this->_conf['userext_table'].".name",
                            $this->_conf['userext_table'].".phone",
                            $this->_conf['order_table'].".vip",

                            "computer"=>[
                                $this->_conf['computer_table'].".brand",
                                $this->_conf['computer_table'].".model",
                                $this->_conf['computer_table'].".buy_time",              
                            ],
                            $this->_conf['order_table'].".status",
                            $this->_conf['order_table'].".time",
                            $this->_conf['order_table'].".distribute_time"
                        ];
        $this->_join =  [
                            [   
                                "[>]".$this->_conf['userext_table']=>$this->_conf['user_id'],
                            ],
                            [
                                "[>]".$this->_conf['computer_table']=>"computer_id",
                            ],
                            [
                                "[>]".$this->_conf['staff_table']=>$this->_conf['staff_id'],
                            ],
                            [
                                "[>]".$this->_conf['userext_table']=>
                                    [
                                        $this->_conf['staff_table'].".".$this->_conf['user_id']=>$this->_conf['user_id']
                                    ],
                            ],
                            [
                                "[><]".$this->_conf['order_table']=>"order_id"
                            ],
                        ];
    }

    /**
     * @param string $staff_id
     * @return array result
     */
    public function getStaffOrders($staff_id){
        $response = $this->_db->search( $this->_conf['order_table'],
                                        $this->_join[0] + $this->_join[1],
                                        $this->_col,
                                        [  
                                            $this->_conf['staff_id'] => $staff_id,
                                            "ORDER" => ["time" => "DESC"],
                                        ]);
        $this->adjustDate($response);
        return $response;
    }
    
    /**
     * @param string $user_id
     * @return array result
     */
    public function getUserOrders($user_id){
        $response = $this->_db->search( $this->_conf['order_table'],
                                        $this->_join[1] + $this->_join[2] + $this->_join[3],
                                        $this->_col,
                                        [
                                           $this->_conf['order_table'].".".$this->_conf['user_id'] => $user_id,
                                           "ORDER" => ["time" => "DESC"]
                                        ]);
        $this->adjustDate($response);
        return $response;
    }
    
    /**
     * @param string $user_id
     * @return array result
     */
    public function getUserActiveOrders($user_id){
        $response = $this->_db->search( $this->_conf['order_table'],
                                        $this->_join[1] + $this->_join[2] + $this->_join[3],
                                        $this->_col,
                                        [
                                           $this->_conf['order_table'].".".$this->_conf['user_id'] => $user_id,
                                           $this->_conf['order_table'].".status" => [0, 1, 3]
                                        ]);
        $this->adjustDate($response);
        return $response;
    }
    
    /**
     * @param string $order_num
     * @return array result
     */
    public function getOrderDetail($order_num){
        $response_basic = $this->_db->select($this->_conf['order_table'], "number", $order_num);
        if (count($response_basic) == 0){
            return null;
        } else {
            $data["basic"] = $response_basic[0];
            $data["basic"]['time'] = date("Y-m-d H:i:s", $data["basic"]['time']);
            $data["basic"]['distribute_time'] = date("Y-m-d H:i:s", $data["basic"]['distribute_time']);
        }
        
        $response_extend = $this->_db->select($this->_conf['orderext_table'], "order_id", $data["basic"]["order_id"]);
        $data["extend"] = $response_extend[0];
        
        $response_computer = $this->_db->select($this->_conf['computer_table'], "computer_id", $data["basic"]["computer_id"]);
        $data["computer"] = $response_computer[0];
        $data['computer']['buy_time'] = date("Y-m", $data['computer']['buy_time']);
        
        $response_user = $this->_db->select($this->_conf['userext_table'], "user_id", $data["basic"]["user_id"]);
        $data["user"] = $response_user[0];
        
        if ($data["basic"]["staff_id"] > 0){
            $response_staff = $this->_db->select($this->_conf['staff_table'], "staff_id", $data["basic"]["staff_id"]);
            $response_staff_extend = $this->_db->select($this->_conf['userext_table'], "user_id", $response_staff[0]["user_id"]);
            $data["staff"] = $response_staff_extend[0];
        }
        
        return $data;
    }
    
    /**
     * @param string $id user_id or staff_id
     * @return array result
     */
    public function checkBelong($id, $order_num){
        $response = $this->_db->search($this->_conf['order_table'], 
                                       "*",
                                       [
                                            "number"=>$order_num,
                                            "OR"=>[
                                                    $this->_conf['user_id']=>$id,
                                                    $this->_conf['staff_id']=>$id
                                                  ]
                                       ]
                                       );
        //return !empty($response);
        return true; // !!!
    }
    
    /**
     * @param string $order_id
     * @return array result
     */
    public function acceptOrder($order_num){
        $response = $this->_db->update($this->_conf['order_table'], 
                                       "number",
                                       ["status"=>3],
                                       $order_num
                                       );
        return $response;
    }
    
    /**
     * @param string $order_id
     * @return array result
     */
    public function cancelOrder($order_num){
        $response = $this->_db->update($this->_conf['order_table'], 
                                       "number",
                                       ["status"=>2],
                                       $order_num
                                       );
        return $response;
    }
    
    /**
     * @param string $order_id
     * @return array result
     */
    public function finishOrder($order_num){
        $response = $this->_db->update($this->_conf['order_table'], 
                                       "number",
                                       ["status"=>4],
                                       $order_num
                                       );
        return $response;
    }
    
    /**
     * @param array $data [user_id, vip, computer_id, description]
     * @return array
     */
    public function newOrder($data){
        $snowflake = new snowflake(0);
        $order_num = $snowflake->nextId();
        $data["time"] = time();
        $data["status"] = 0;
        $description = $data["description"];
        unset($data["description"]);
        
        $response = $this->_db->insert($this->_conf['order_table'], 
                                       "number",
                                       $data,
                                       $order_num
                                       );
        if ($response["code"] != 200) {
            return $response;
        }
        
        $response = $this->_db->query("SELECT LAST_INSERT_ID()");
        //var_dump($response);die();
        if (isset($response[0])){
            $order_id = $response[0][0];
        } else {
            return array("code"=>500);
        }
        
        $extdata["description"] = $description;
        $response = $this->_db->insert($this->_conf['orderext_table'], 
                                       "order_id",
                                       $extdata,
                                       $order_id
                                       );
        return $response;
    }
    
    function adjustDate(&$response) {
        foreach ($response as &$i) {
            $i['computer']['buy_time'] = date("Y-m", $i['computer']['buy_time']);
            $i['time'] = date("Y-m-d H:i:s", $i['time']);
            $i['distribute_time'] = date("Y-m-d H:i:s", $i['distribute_time']);
        }
        unset($i);
        unset($response);
    }
    
    /**
     * @param string $user_id
     * @return array result
     */
    public function getUserDevice($user_id){
        $response = $this->_db->search( $this->_conf['computer_table'],
                                        ["computer_id", "brand", "model", "buy_time"],
                                        [$this->_conf['user_id'] => $user_id]);
        return $response;
    }
    
    /**
     * @param string $user_id
     * @param string $brand
     * @param string $model
     * @param string $buytime
     * @return array result
     */
    public function addUserDevice($user_id, $brand, $model, $buytime){
        $extdata["brand"] = $brand;
        $extdata["model"] = $model;
        $extdata["buy_time"] = strtotime($buytime);
        $response = $this->_db->insert( $this->_conf['computer_table'],
                                        $this->_conf['user_id'],
                                        $extdata,
                                        $user_id);
        if ($response == false){
            return array('code'=>406);
        }
        $response = $this->_db->query("SELECT LAST_INSERT_ID()");
        if (isset($response[0])){
            $computerid = $response[0][0];
        } else {
            return array("code"=>500);
        }
        return array('code'=>200, 'data'=>$computerid);
    }
    
    /**
     * @param bool $vip
     * @return int
     */
    public function getAvailable($vip){
        $switch = $this->_db->select($this->_conf['config_table'], "key", "global_switch")[0]['value'];
        $count = $this->_db->select($this->_conf['config_table'], "key", "global_limit_count")[0]['value'];
        $period = $this->_db->select($this->_conf['config_table'], "key", "global_limit_period")[0]['value'];
        switch ($switch) {
            case 0:
                $resp = 0;
                break;
            case 1:
                $resp = ($vip)? 1 : 0;
                break;
            case 2:
                if ($vip) {
                    $resp = 1;
                } else {
                    $total = $this->_db->query("select count(*) from ".$this->_conf['order_table']." where time>".strtotime(date('Y-m-d')."- ".$period." days"))[0][0];
                    $resp = ($total >= $count)? 0 : 1;
                }
                break;
            default:
                $resp = 0;
        }
        //$response = $this->_db->query("select sum(available) from ".$this->_conf['staff_table']);
        //if ($response == false){
        //    return array('code'=>406);
        //}
        // TODO
        
        return $resp;
        //return array('code'=>200, 'data'=>$response[0][0]);
    }
}
