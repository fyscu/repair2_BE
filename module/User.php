<?php

class User {
    private $_db;
    private $_config;

    /**
     * @param Database $db
     * @param array $config sysconfig in config.php
     */
    public function __construct($db, $conf){
        $this->_db = $db;
        $this->_conf = $conf;
    }

    /**
     * @param string $uid
     * @return mix null when not exist, array when found
     */
    public function getBasicByUid($uid){
        $response = $this->_db->select($this->_conf['user_table'], 
                                       $this->_conf['uid'],
                                       $uid);
        if ($response == false){
            return null;
        } else {
            $data[$this->_conf['user_id']] = $response[0][$this->_conf['user_id']];
        }
        
        $response = $this->_db->select($this->_conf['staff_table'], 
                                       $this->_conf['user_id'],
                                       $data[$this->_conf['user_id']]);
        $data[$this->_conf['staff_id']] = ($response == false) ? -1 : $response[0][$this->_conf['staff_id']];
        return $data;
    }
    
    /**
     * @param string $uid
     * @param array result
     * @return array result
     */
    public function addUser($uid, $data){
        $response = $this->_db->insert($this->_conf['user_table'], "ucid", array(), $uid);
        if ($response["code"] != 200) {
            return $response;
        }
        
        $response = $this->_db->query("SELECT LAST_INSERT_ID()");
        if (isset($response[0])){
            $user_id = $response[0][0];
        } else {
            return array("code"=>500);
        }
        
        $response = $this->_db->insert($this->_conf['userext_table'], 
                                       "user_id",
                                       array("name"=>$data["name"], "phone"=>$data["phone"], "vip"=>$data["vip"], "register_time"=>time()),
                                       $user_id
                                       );
        return $response;
        
    }
    
}
