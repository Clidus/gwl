<?php

class Users extends CI_Controller {
    
    // view user
    function view($userID)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByID($userID);

        if($user == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "User");
        $data['user'] = $user;
        $data['events'] = $this->User->getUserEvent($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile', $data);
        $this->load->view('templates/footer', $data);
    }

    // edit profile
    function edit()
    {   
        // load form helper
        $this->load->helper(array('form', 'url'));

        // get logged in user
        $userID = $this->session->userdata('UserID');

        // if not logged in, 404
        if($userID == null)
            show_404();

        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByID($userID);

        if($user == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "User");
        $data['user'] = $user;
        $data['error'] = '';

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/edit', $data);
        $this->load->view('templates/footer', $data);
    }

    // saving edit profile
    function save()
    {   
        // load user model
        $this->load->model('User');

        // get logged in user
        $userID = $this->session->userdata('UserID');

        // if not logged in, 404
        if($userID == null)
            show_404();

        // configure file upload
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '100';
        $config['max_width']  = '1000';
        $config['max_height']  = '1000';
        $this->load->library('upload', $config);

        // upload file
        if ($this->upload->do_upload())
        {
            // if successfull, save image file name
            $uploadData = $this->upload->data();
            $error = '';

            $this->User->updateProfileImage($userID, $uploadData["file_name"]);
        }
        else
        {
            $error = $this->upload->display_errors();
        }

        // load form helper
        $this->load->helper(array('form', 'url'));

        // get user data
        $user = $this->User->getUserByID($userID);

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "User");
        $data['user'] = $user;
        $data['error'] = $error;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/edit', $data);
        $this->load->view('templates/footer', $data);
    }
}
?>