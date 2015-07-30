<?php

/*
|--------------------------------------------------------------------------
| Ignition v0.4.0 ignitionpowered.co.uk
|--------------------------------------------------------------------------
|
| This class is a core part of Ignition. It is advised that you extend
| this class rather than modifying it, unless you wish to contribute
| to the project.
|
*/

class IG_Users extends CI_Controller {

	// return error message
    function returnError($errorMessage,$errorProgressURL,$errorProgressCTA)
    {
        $result['error'] = true; 
        $result['errorMessage'] = $errorMessage;
        $result['errorProgressURL'] = $errorProgressURL; 
        $result['errorProgressCTA'] = $errorProgressCTA; 
        echo json_encode($result);
    }

    // view user
    function view($userID, $page = 1)
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

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/profile', $data);
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
        $this->load->view('user/profile/header', $data);
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
        $this->load->view('user/profile/header', $data);
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
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = '2048';
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
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/image', $data);
        $this->load->view('templates/footer', $data);
    }

    // change password page
    function password()
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
        $this->form_validation->set_rules('oldPassword', 'Old Password', 'trim|required');
        $this->form_validation->set_rules('newPassword', 'New Password', 'trim|required|matches[confirmNewPassword]');
        $this->form_validation->set_rules('confirmNewPassword', 'Confirm New Password', 'trim|required');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '<a class="close" data-dismiss="alert" href="#">&times;</a></div>');

        // page variables 
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "Password");
        $data['errorMessage'] = '';
        $data['success'] = false;

        // validation failed
        if ($this->form_validation->run())
        {
            // update user
            $oldPassword = $this->input->post('oldPassword');
            $newPassword = $this->input->post('newPassword');

            if($this->User->changePassword($userID, $oldPassword, $newPassword)) 
                $data['success'] = true;
            else
                // failed, return error
                $data['errorMessage'] = $this->User->errorMessage;
        }

        $data['user'] = $user;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/password', $data);
        $this->load->view('templates/footer', $data);
    }

    // add comment
    function comment()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('linkID', 'linkID', 'trim|xss_clean');
        $this->form_validation->set_rules('commentTypeID', 'commentTypeID', 'trim|xss_clean');
        $this->form_validation->set_rules('comment', 'comment', 'trim|xss_clean|strip_tags|htmlspecialchars');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
        $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
        $this->form_validation->run();

        $linkID = $this->input->post('linkID');
        $commentTypeID = $this->input->post('commentTypeID');
        $comment = $this->input->post('comment');
        $email = $this->input->post('email');
        $name = $this->input->post('name');
        $userID = $this->session->userdata('UserID');
        $registeredUser = true;

        // check that user is logged in or has provided anonymous details
        if($userID <= 0 && $name == null && $email == null)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // if anonymous details have been provided, make all fields required 
        if(($name != null && $email == null) || ($email != null && $name == null)) 
        {
            $this->returnError($this->lang->line('error_comment_missing_details'),false,false);
            return;
        }

        // check event id is valid
        if($linkID <= 0)
        {
            $this->returnError($this->lang->line('error_event_invalid'),false,false);
            return;
        }

        // create anonymous user
        if($userID <= 0 && ($email != null && $name != null)) 
        {
            $registeredUser = false;

            $this->load->model('User');
            $userID = $this->User->registerAnonymousUser($email, $name);

            if($userID == null)
                $this->returnError($this->lang->line('error_comment_failed'),false,false);
        }

        // add comment
        $this->load->model('Comment');
        $this->Comment->addComment($linkID, $commentTypeID, $userID, $comment);

        // add new comment (in HTML) to response so it can be added to the current page
        $this->load->library('md');
        $result['comment'] = $this->md->defaultTransform($comment);
        $result['username'] = $registeredUser ? $this->session->userdata('Username') : $name;
        $result['profileImage'] = $registeredUser ? $this->session->userdata('ProfileImage') : $this->config->item('default_profile_image');
        $result['userID'] = $userID;
        $result['registeredUser'] = $registeredUser;

        // return success
        $result['error'] = false;   
        echo json_encode($result);
    }
}