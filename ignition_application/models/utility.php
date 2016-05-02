<?php 

class Utility extends CI_Model {

    // get API response
    function getData($url, $requestType) {
        $ch = curl_init($url); 
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => array('Content-type: application/json'),
            CURLOPT_USERAGENT => $this->config->item('gwl_user_agent'),
        );                        
        curl_setopt_array($ch, $options);             
        $json = curl_exec($ch);
        curl_close($ch);
        
        $this->logApiRequest($url, $requestType, $json);

        $result = json_decode($json); 
        
        return $result;
    }

    function logApiRequest($url, $requestType, $json) {
        // dont log json result if request is for a single Game
        if($requestType == "Game")
            $json = null;
        
        $data = array(
           'Url' => $url,
           'RequestType' => $requestType,
           'Result' => $json,
           'Processed' => 0,
           'DateStamp' => date('Y-m-d H:i:s')
        );

        return $this->db->insert('apiLog', $data); 
    }
}