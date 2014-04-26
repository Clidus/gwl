<?php

class Users extends CI_Controller {
    
    // view user
    function view($userID, $page = 1)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByID($userID);

        if($user == null)
            show_404();

        $user->ProfileImage = $user->ProfileImage == null ? "gwl_default.jpg" : $user->ProfileImage;

        // paging
        $resultsPerPage = 20;
        $offset = ($page-1) * $resultsPerPage;

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "User");
        $data['user'] = $user;

        // get event feed
        $data['events'] = $this->User->getUserEvents($userID, null, $this->session->userdata('DateTimeFormat'), $offset, $resultsPerPage);
        $data['pageNumber'] = $page;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('control/events', $data);
        $this->load->view('user/profile/footer', $data);
        $this->load->view('templates/footer', $data);
    }

    // user settings page
    function settings()
    {
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

        $this->load->helper(array('form'));
        $this->load->library('form_validation');

        // form validation
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|strip_tags|htmlspecialchars');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|strip_tags|htmlspecialchars');
        $this->form_validation->set_rules('dateFormat', 'Date Format', 'trim|required|xss_clean');
        $this->form_validation->set_rules('bio', 'Date Format', 'trim|xss_clean|strip_tags|htmlspecialchars');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '<a class="close" data-dismiss="alert" href="#">&times;</a></div>');

        // page variables 
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "Settings");
        $data['errorMessage'] = '';

        // validation failed
        if ($this->form_validation->run())
        {
            // update user
            $newUsername = $this->input->post('username');
            $newEmail = $this->input->post('email');
            $newDateFormat = $this->input->post('dateFormat');
            $newBio = $this->input->post('bio');
            if(!$this->User->updateProfile($userID, $newEmail, $newUsername, $newDateFormat, $newBio)) {
                // failed, return error
                $data['errorMessage'] = $this->User->errorMessage;
            } else {
                $user->Username = $newUsername;
                $user->Email = $newEmail;
                $user->DateTimeFormat = $newDateFormat;
                $user->Bio = $newBio;
            }
        }

        $data['user'] = $user;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/settings', $data);
        $this->load->view('templates/footer', $data);
    }

    // user profile image upload page
    function image()
    {
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

        $this->load->helper(array('form'));

        // page variables 
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "ImageUpload");
        $data['errorMessage'] = '';
        $data['user'] = $user;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/image', $data);
        $this->load->view('templates/footer', $data);
    }

    // upload profile image
    function imageUpload()
    {
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

        $this->load->helper(array('form'));

        // page variables 
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "ImageUpload");
        $data['errorMessage'] = '';

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
            $profileImage = $uploadData["file_name"];

            $this->User->updateProfileImage($userID, $profileImage);
            $user->ProfileImage = $profileImage;
        }
        else
        {
            $data['errorMessage'] = $this->upload->display_errors();
        }

        $data['user'] = $user;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/image', $data);
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
        $this->form_validation->set_rules('comment', 'comment', 'trim|xss_clean|strip_tags|htmlspecialchars');
        $this->form_validation->run();

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

        // add new comment (in HTML) to response so it can be added to the current page
        $this->load->library('md');
        $result['comment'] = $this->md->defaultTransform($comment);
        $result['username'] = $this->session->userdata('Username');
        $result['profileImage'] = $this->session->userdata('ProfileImage');
        $result['userID'] = $userID;

        // return success
        $result['error'] = false;   
        echo json_encode($result);
    }
}
?>