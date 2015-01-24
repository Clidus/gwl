<?php 

/*
|--------------------------------------------------------------------------
| Ignition v0.3.1 ignitionpowered.co.uk
|--------------------------------------------------------------------------
|
| This class is a core part of Ignition. It is advised that you extend
| this class rather than modifying it, unless you wish to contribute
| to the project.
|
*/

class IG_User extends CI_Model {

    var $errorMessage = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // hash password
    function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    
    // register user
    function register($email, $username, $password)
    {
        // hash password
        $hashPassword = $this->hashPassword($password);

        // check is user exists
        $query = $this->db->get_where('users', array('Username' => $username, 'RegisteredUser' => true));
        if ($query->num_rows() > 0)
        {
            $this->errorMessage = 'Sorry duder. This username is already taken. Bad luck!';
            return false;
        }

        // add user to db
        $data = array(
           'Username' => $username,
           'Password' => $hashPassword,
           'Email' => $email,
           'RegisteredUser' => true
        );

        // if added successfully 
        if($this->db->insert('users', $data)) {
            // login user
            if($this->login($username, $password)) {
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

    // register anonymous user
    function registerAnonymousUser($email, $username)
    {
        // add user to db
        $data = array(
           'Username' => $username,
           'Email' => $email
        );

        // if added successfully 
        if($this->db->insert('users', $data)) {
            return $this->db->insert_id(); // return UserID
        } else {
            return null;
        }
    }

    // login user
    function login($username, $password)
    {
        // check login is correct
        $query = $this->db->get_where('users', array('Username' => $username, 'RegisteredUser' => true));
        if ($query->num_rows() == 1)
        {
            // get user record returned
            $user = $query->first_row();

            // verify password
            if(!password_verify($password , $user->Password))
                // error, incorrect password
                return false;

            // create session
            $newdata = array(
                'UserID' => $user->UserID,
                'Username' => $user->Username,
                'Admin' => $user->Admin,
                'DateTimeFormat' => $user->DateTimeFormat,
                'ProfileImage' => $user->ProfileImage == null ? $this->config->item('default_profile_image') : $user->ProfileImage
            );
            $this->session->set_userdata($newdata);
            
            // success
            return true;
        } else {
            // error, incorrect username
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
        $this->db->where('users.UserID', $userID); 
        $this->db->where('users.RegisteredUser', true); 
        $query = $this->db->get();

        if($query->num_rows() == 1)
        {
            $user = $query->first_row();
            $user->ProfileImage = $user->ProfileImage == null ? $this->config->item('default_profile_image') : $user->ProfileImage;
            return $user;
        }

        return null;
    }
    
    // update user profile
    function updateProfile($userID, $email, $username, $dateFormat, $bio)
    {
        // check is user exists
        $query = $this->db->get_where('users', array('Username' => $username, 'UserID !=' => $userID, 'RegisteredUser' => true));
        if ($query->num_rows() > 0)
        {
            $this->errorMessage = 'Sorry duder. This username is already taken. Bad luck!';
            return false;
        }

        // add user to db
        $data = array(
           'Username' => $username,
           'Email' => $email,
           'DateTimeFormat' => $dateFormat,
           'Bio' => $bio
        );

        $this->db->where('UserID', $userID);
        $this->db->update('users', $data); 

        // update session
        $sessionData = array(
            'Username' => $username,
            'DateTimeFormat' => $dateFormat,
        );
        $this->session->set_userdata($sessionData);

        return true;
    }

    // update user profile image
    function updateProfileImage($userID, $profileImage)
    {
        $newdata = array(
            'ProfileImage' => $profileImage
        );

        // update database
        $this->db->where('UserID', $userID); 
        $this->db->update('users', $newdata); 

        // update session
        $this->session->set_userdata($newdata);
    }

    // change password
    function changePassword($userID, $oldPassword, $newPassword)
    {
        // get user
        $user = $this->getUserByID($userID);
        if ($user != null)
        {
            // verify password
            if(!password_verify($oldPassword , $user->Password))
            {
                // error, incorrect password
                $this->errorMessage = 'Old password is incorrect. Please try again.';
                return false;
            }

            // add user to db
            $data = array(
               'Password' => $this->hashPassword($newPassword)
            );

            $this->db->where('UserID', $userID);
            $this->db->update('users', $data); 

            return true;
        } else {
            $this->errorMessage = 'Something strange happened! Please check you are logged in and try again.';
            return false;
        }
    }
}