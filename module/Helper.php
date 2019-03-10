<?php

class Helper {
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
     * @return array
     */
    public function getAnnounce(){
        $response = $this->_db->select($this->_conf['config_table'], 'key', 'wx_announce');
        if ($response == false || $response[0]['value'] == false){
            return array('code'=> '204');
        }
        $response = $this->_db->select($this->_conf['config_table'], 'key', 'wx_announce_data');
        if ($response == false){
            return array('code'=> '500');
        } else {
            return array('code'=> 200, 'data'=>json_decode($response[0]['value'], true));
        }
    }
    
    /**
     * @param $count int count(available)
     * @return string
     */
    public function getLimitTip($count){
        if ($count > 0){
            return '';
        }
        $response = $this->_db->select($this->_conf['config_table'], 'key', 'global_limit_announce');
        if ($response == false){
            return '';
        } else {
            return $response[0]['value'];
        }
    }
    
}
