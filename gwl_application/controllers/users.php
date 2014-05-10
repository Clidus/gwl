<?php

class Users extends CI_Controller {

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

        // get games currently playing
        $this->load->model('Game');
        $data['currentlyPlaying'] = $this->Game->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('control/events', $data);
        $this->load->view('user/profile/footer', $data);
        $this->load->view('templates/footer', $data);
    }

    // view user collection
    function collection($userID)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByID($userID);

        if($user == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "Collection");
        $data['user'] = $user;

        // get game collection stats
        $this->load->model('Game');

        // get platforms for filtering
        $data['platforms'] = $this->Game->getPlatformsInCollection($userID);

        // get games currently playing
        $data['currentlyPlaying'] = $this->Game->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/collection', $data);
        $this->load->view('templates/footer', $data);
    }

    function getCollection()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('userID', 'userID', 'trim|xss_clean');
        $this->form_validation->set_rules('page', 'page', 'trim|xss_clean');
        $this->form_validation->set_rules('filters', 'filters', 'xss_clean');
        $this->form_validation->run();

        $userID = $this->input->post('userID');
        $page = $this->input->post('page');
        $filters = json_decode($this->input->post('filters'));

        // check that user is VALID
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_user_invalid_id'),false,false);
            return;
        }

        // paging
        $resultsPerPage = 30;
        $offset = ($page-1) * $resultsPerPage;

        // get collection
        $this->load->model('Game');
        $result['collection'] = $this->Game->getCollection($userID, $filters, $offset, $resultsPerPage);
        $result['stats'] = $this->Game->getCollection($userID, $filters, null, null);

        // return success
        $result['error'] = false;   
        echo json_encode($result);
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

        // get games currently playing
        $this->load->model('Game');
        $data['currentlyPlaying'] = $this->Game->getCurrentlyPlaying($userID);

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

        // get games currently playing
        $this->load->model('Game');
        $data['currentlyPlaying'] = $this->Game->getCurrentlyPlaying($userID);

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

        // get games currently playing
        $this->load->model('Game');
        $data['currentlyPlaying'] = $this->Game->getCurrentlyPlaying($userID);

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

        // get games currently playing
        $this->load->model('Game');
        $data['currentlyPlaying'] = $this->Game->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/password', $data);
        $this->load->view('templates/footer', $data);
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
            $this->returnError($this->lang->line('error_logged_out'),false,false);
            return;
        }

        // check event id is valid
        if($eventID <= 0)
        {
            $this->returnError($this->lang->line('error_event_invalid'),false,false);
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