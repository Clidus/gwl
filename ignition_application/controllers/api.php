<?php

class Api extends CI_Controller {
    
	function login()
    {
        // get request from body and clean
        $data = $this->security->xss_clean(json_decode(file_get_contents('php://input'), true));

        $this->load->model('User');
        if(count($data) > 0
            && array_key_exists('username', $data) 
            && array_key_exists('password', $data)) 
        {
            $userData = $this->User->login($data['username'], $data['password'], false);

            if($userData != null)
            {
                $this->load->model('Api_Session');
                $token = $this->Api_Session->createSessionToken($userData["UserID"]);

                if($token != null)
                {
                    $result['success'] = true;
                    $result['token'] = $token;
                    $result['username'] = $userData["Username"];
                    $result['profileImage'] = $userData["ProfileImage"];

                    echo json_encode($result);
                    return;
                }
            }
        }

        $result['success'] = false;
        $result['token'] = "";

        echo json_encode($result);
        return;
    }
}