<?php

class Users extends CI_Controller {
    
    // view user
    function view($userID)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByID($userID);
        $user->ProfileImage = $user->ProfileImage == null ? "gwl_default.jpg" : $user->ProfileImage;

        if($user == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "User");
        $data['user'] = $user;
        $data['events'] = $this->User->getUserEvent($userID, $this->session->userdata('DateTimeFormat'));

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile', $data);
        $this->load->view('templates/footer', $data);
    }

    // edit profile
    function edit()
    {   
        // load form helper
        $this->load->helper(array('form'));

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
        $this->load->helper(array('form'));

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

    function returnError($errorMessage)
    {
        $result['error'] = true; 
        $result['errorMessage'] = $errorMessage; 
        echo json_encode($result);
    }

    // add comment to event
    function comment()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('eventID', 'eventID', 'trim|xss_clean');
        $this->form_validation->set_rules('comment', 'comment', 'trim|xss_clean');

        $eventID = $this->input->post('eventID');
        $comment = $this->input->post('comment');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError("You've been logged out. Please login and try again.");
            return;
        }

        $this->load->model('User');
        $this->User->addComment($eventID, $userID, $comment);

        // return success
        $result['error'] = false;   
        echo json_encode($result);
    }
}
?>