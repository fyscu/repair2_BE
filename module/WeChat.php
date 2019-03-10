<?php
require_once("Util.php");

class WeChat {
    private $_codeurl = "https://api.weixin.qq.com/sns/jscode2session?appid={appid}&secret={appsecret}&js_code={code}&grant_type=authorization_code";
    
    private $_db;
    private $_user_id;
    
    private $_wxconfig;
    private $_wxuser;
    private $_wxpush;
    
    private $_appid;
    private $_appsecret;

    /**
     * @param Database $db
     * @param array $sysconfig
     * @param array $apiconfig
     */
    public function __construct($db, $sysconfig, $apiconfig){
        $this->_db = $db;
        $this->_user_id = $sysconfig['user_id'];
        
        $this->_wxconfig = $sysconfig['config_table'];
        $this->_wxuser = $sysconfig['wxuser_table'];
        $this->_wxpush = $sysconfig['wxpush_table'];
        
        $this->_appid = $this->_db->select($this->_wxconfig, "key", "wma_appid")[0]['value'];
        $this->_appsecret = $this->_db->select($this->_wxconfig, "key", "wma_appsecret")[0]['value'];
    }

    /**
     * @param string $code callback from app
     * @return array result
     */
    public function checkCode($code){
        $url = str_replace('{appid}', $this->_appid , $this->_codeurl);
        $url = str_replace('{appsecret}', $this->_appsecret , $url);
        $url = str_replace('{code}', $code , $url);
        
        $request = GET($url, array());
        if (empty($request)) return array('code'=>500);
        $res = json_decode($request);
        
        if (isset($res->errcode)){
            return array('code'=>400);
        }
        
        return array('code'=>200, 'data'=>$res);
    }

    /**
     * @param string $openid
     * @return array result
     */
    public function checkBind($openid){
        $response = $this->_db->select($this->_wxuser, 
                                       "openid", 
                                       $openid);
        if ($response == false){
            return array('code'=>406);
        }
        return array('code'=>200, 'data'=>$response[0]);
    }

    /**
     * @param string $openid
     * @param string $uid
     * @return array result
     */
    public function addBind($openid, $uid){
        $response = $this->_db->insert($this->_wxuser, 
                                       $this->_user_id, 
                                       array(
                                        'openid'=>$openid
                                       ), $uid);
        if ($response == false){
            return array('code'=>406);
        }
        return array('code'=>200);
    }
    
    /**
     * @param string $openid
     * @return array result
     */
    public function deleteBind($openid){
        $response = $this->_db->delete($this->_wxuser, 
                                       'openid', 
                                       $openid);
        return $response;
    }

}