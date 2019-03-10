<?php

class Config {
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
     * @param string $key
     * @return array result
     */
    public function showConfig($key){
        $response = $this->_db->select($this->_conf['config_table'], "key", $value);
        if ($response == false){
            return array("code"=>404);
        } else {
            return array("code"=>200, "data"=>$response[0]);
        }
    }
    
    /**
     * @param string $key
     * @param string $value
     * @return array result
     */
    public function changeConfig($key, $value){
       return $this->_db->update($this->_conf['config_table'], "key", array("value"=>$value), $key);
    }
    
}
