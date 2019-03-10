<?php

/**
 * @param int $length
 * @param int $type (0: nums, 1:lEtTeRs, 2: nums+lEtTeRs)
 * @param string return
 */
function randomGen($length, $type){
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $range = array(array(52, 61), array(0, 51), array(0, 61));
    $rand = '';  
    for ($i = 0; $i < $length; $i++) {
        $rand .= $chars[ mt_rand($range[$type][0], $range[$type][1]) ]; 
    }
    return $rand;
}

/**
 * @param string $url Destination URL
 * @param string[] $headers additional HTTP Headers
 * @param string[] $data Data sent
 * @return string
 */
function GET($url,$headers = array(),$data = array()){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT,10);
    //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $CURL_HEADERS = array();
    if(is_array($headers)){
        foreach($headers as $k=>$v){
            $CURL_HEADERS[] = $k.':'.$v;
        }
    }
    //curl_setopt($ch, CURLOPT_HTTPHEADER, $CURL_HEADERS);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
/**
 * @param string $url Destination URL
 * @param string[] $headers additional HTTP Headers
 * @param string[] $data Data sent
 * @param bool $json
 * @return string
 */
function POST($url, $headers = array(), $data = array(), $json = false){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT,10);
    curl_setopt($ch, CURLOPT_POST, 1);
    if ($json) $data = json_encode($data);
    else if (is_array($data)){
        $data = http_build_query($data, null, '&');
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $CURL_HEADERS = array();
    if(is_array($headers)){
        foreach($headers as $k=>$v){
            $CURL_HEADERS[] = $k.':'.$v;
        }
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $CURL_HEADERS);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
/**
 * @param string $url Destination URL
 * @param string[] $headers additional HTTP Headers
 * @param string[] $data Data sent
 * @return string
 */
function PUT($url,$headers = array(),$data = array()){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT,10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $CURL_HEADERS = array();
    if(is_array($headers)){
        foreach($headers as $k=>$v){
            $CURL_HEADERS[] = $k.':'.$v;
        }
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $CURL_HEADERS);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
/**
 * @param string $url Destination URL
 * @param string[] $headers additional HTTP Headers
 * @param string[] $data Data sent
 * @return string
 */
function DELETE($url,$headers = array(),$data = array()){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT,10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $CURL_HEADERS = array();
    if(is_array($headers)){
        foreach($headers as $k=>$v){
            $CURL_HEADERS[] = $k.':'.$v;
        }
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $CURL_HEADERS);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}