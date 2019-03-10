<?php
require_once("Util.php");

class Login {
    private $_db;
    private $_conf;

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
     * @param string $token
     * @return array result
     */
    public function handleCallback($uid, $token){
        $challenge = POST($this->_conf['sso_api']."challenge", array(), array(
            "uid"    =>$uid,
            "token"  =>$token,
            "appid"  =>$this->_conf['sso_appid'],
            "appkey" =>$this->_conf['sso_secret']
        ));
        if (is_null($challenge)){
            return array('code'=>500);
        }
        
        $data = json_decode($challenge, true);
        return $data;
    }

    /**
     * @param string $username
     * @return array result
     */
    public function getOTP($username){
        $challenge = POST($this->_conf['sso_api']."appotp", array(), array(
            "username"=>$username,
            "appid"  =>$this->_conf['sso_appid'],
            "appkey" =>$this->_conf['sso_secret']
        ));
        
        if (is_null($challenge)){
            return array('code'=>500);
        }
        
        $data = json_decode($challenge, true);
        return $data;
    }

    /**
     * @param string $uid
     * @return array result
     */
    public function getProfile($uid){
        $challenge = POST($this->_conf['sso_api']."profile", array(), array(
            "uid"    =>$uid,
            "appid"  =>$this->_conf['sso_appid'],
            "appkey" =>$this->_conf['sso_secret']
        ));
        //var_dump($challenge);die();
        if (is_null($challenge)){
            return array('code'=>500);
        }
        $data = json_decode($challenge, true);
        return $data;
    }
    
    /**
     * @param string $uid
     * @param array $data
     * @return mix null when not exist, array when found
     */
    public function updateData($uid, $data){
        $challenge = POST($this->_conf['sso_api']."updateinfo/app", array(), array_merge(array(
            "uid"    =>$uid,
            "appid"  =>$this->_conf['sso_appid'],
            "appkey" =>$this->_conf['sso_secret']
        ) ,$data));
        if (is_null($challenge)){
            return array('code'=>500);
        }
        $data = json_decode($challenge, true);
        return $data;
    }

}

