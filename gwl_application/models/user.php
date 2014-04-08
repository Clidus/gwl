<?php 

class User extends CI_Model {

    var $errorMessage = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // hash password
    function hashPassword($password)
    {
        $salt = $this->config->item('password_salt');
        return hash("sha256", $password . $salt);
    }
    
    // register user
    function register($email, $username, $password)
    {
        // hash password
        $hashPassword = $this->hashPassword($password);

        // check is user exists
        $query = $this->db->get_where('users', array('Username' => $username));
        if ($query->num_rows() > 0)
        {
            $this->errorMessage = 'Sorry duder. This username is already taken. Bad luck!';
            return false;
        }

        // add user to db
        $data = array(
           'Username' => $username,
           'Password' => $hashPassword,
           'Email' => $email
        );

        // if added successfully 
        if($this->db->insert('users', $data)) {
            // login user
            if($this->login($username, $hashPassword, true)) {
                // success
                return true;
            } else {
                // error
                $this->errorMessage = 'User created but failed to login. Try logging in I guess?';
                return false;
            }
        } else {
            // error
            $this->errorMessage = 'Something went wrong! Please try again I guess?';
            return false;
        }
    }

    // login user
    function login($username, $password, $passwordIsHashed)
    {
        $hashPassword = $passwordIsHashed ? $password : $this->hashPassword($password);

        // check login is correct
        $query = $this->db->get_where('users', array('Username' => $username, 'Password' => $hashPassword));
        if ($query->num_rows() == 1)
        {
            // create session
            $row = $query->first_row();
            $newdata = array(
                'UserID' => $row->UserID,
                'Username' => $row->Username,
                'Admin' => $row->Admin
            );
            $this->session->set_userdata($newdata);
            
            // success
            return true;
        } else {
            // error, incorrect login details
            return false;
        }
    }

    // logout user
    function logout()
    {
        $this->session->sess_destroy();
    }

    // get user by ID
    function getUserByID($userID)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('UserID', $userID); 
        $query = $this->db->get();

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }

    // update user profile image
    function updateProfileImage($userID, $profileImage)
    {
        $this->db->where('UserID', $userID); 
        $this->db->update('users', array('ProfileImage' => $profileImage)); 
    }

    // record new user event
    function addUserEvent($userID, $gameID, $listID, $statusID, $currentlyPlaying) 
    {
        $data = array(
           'UserID' => $userID,
           'GameID' => $gameID,
           'DateStamp' => date("Y-m-d H:i:s")
        );

        if($listID != null) $data['ListID'] = $listID;
        if($statusID != null) $data['StatusID'] = $statusID;
        if($currentlyPlaying != null) $data['CurrentlyPlaying'] = ($currentlyPlaying === "true");

        $this->db->select('*');
        $this->db->from('userEvents');
        $this->db->where('UserID', $userID); 
        $this->db->where('GameID', $gameID); 
        $this->db->where('DateStamp >', date("Y-m-d H:i:s", strtotime('-24 hour'))); 
        $query = $this->db->get();

        // if event exists for User/Game in last 24 hours, update it rather than adding a new one
        if($query->num_rows() == 1)
        {
            $this->db->where('EventID', $query->first_row()->EventID); 
            return $this->db->update('userEvents', $data); 
        } else {
            return $this->db->insert('userEvents', $data); 
        }
    }

    function getUserEvent($userID) 
    {
        $this->db->select('*');
        $this->db->from('userEvents');
        $this->db->join('games', 'userEvents.GameID = games.GameID');
        $this->db->join('users', 'userEvents.UserID = users.UserID');
        $this->db->join('lists', 'userEvents.ListID = lists.ListID', 'left');
        $this->db->join('gameStatuses', 'userEvents.StatusID = gameStatuses.StatusID', 'left');
        $this->db->where('userEvents.UserID', $userID); 
        $this->db->order_by("DateStamp", "desc"); 
        $events = $this->db->get()->result();

        // loop through events
        $this->load->model('Game');
        foreach ($events as $event)
        {
            // add platforms in collection
            $event->platforms = $this->Game->getGamesPlatformsInCollection($event->GBID, $userID);
        }

        return $events;
    }
}