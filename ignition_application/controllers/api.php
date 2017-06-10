<?php

class Api extends CI_Controller {
    
	function login()
    {
        // get request from body and clean
        $data = $this->security->xss_clean(json_decode(file_get_contents('php://input'), true));

        $this->load->model('User');
        if(count($data) > 0
            && array_key_exists('username', $data) 
            && array_key_exists('password', $data) 
            && $this->User->login($data['username'], $data['password'])) 
        {
            $this->load->model('Api_Session');
            $token = $this->Api_Session->createSessionToken(1);

            $result['success'] = true;
            $result['token'] = $token;
        }
        else
        {
            $result['success'] = false;
            $result['token'] = "";
        }

        echo json_encode($result);
    }
}