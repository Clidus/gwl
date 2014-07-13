<?php 

class Utility extends CI_Model {

    // get API response
    function getData($url) {
        $ch = curl_init($url); 
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
        );                        
        curl_setopt_array($ch, $options);             
        $json = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($json); 
             
        return $result;
    }
}