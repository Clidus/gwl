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
        return password_hash($password, PASSWORD_BCRYPT);
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

    // login user
    function login($username, $password)
    {
        // check login is correct
        $query = $this->db->get_where('users', array('Username' => $username));
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
                'ProfileImage' => $user->ProfileImage == null ? "gwl_default.jpg" : $user->ProfileImage
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
        $this->db->where('UserID', $userID); 
        $query = $this->db->get();

        if($query->num_rows() == 1)
        {
            $user = $query->first_row();
            $user->ProfileImage = $user->ProfileImage == null ? "gwl_default.jpg" : $user->ProfileImage;
            return $user;
        }

        return null;
    }

    // update user profile
    function updateProfile($userID, $email, $username, $dateFormat, $bio)
    {
        // check is user exists
        $query = $this->db->get_where('users', array('Username' => $username, 'UserID !=' => $userID));
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

    // record new user event
    function addUserEvent($userID, $gameID, $listID, $statusID, $currentlyPlaying) 
    {
        // dont record event if only change is no longer currently playing
        if($listID == null && $statusID == null && $currentlyPlaying === "false")
            return;

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

    // get list of events by UserID
    function getUserEvents($userID, $gbID, $DateTimeFormat, $offset, $resultsPerPage) 
    {
        $this->db->select('*');
        $this->db->from('userEvents');
        $this->db->join('games', 'userEvents.GameID = games.GameID');
        $this->db->join('users', 'userEvents.UserID = users.UserID');
        $this->db->join('lists', 'userEvents.ListID = lists.ListID', 'left');
        $this->db->join('gameStatuses', 'userEvents.StatusID = gameStatuses.StatusID', 'left');
        if($userID != null) $this->db->where('userEvents.UserID', $userID); 
        if($gbID != null) $this->db->where('games.GBID', $gbID); 
        $this->db->order_by("DateStamp", "desc"); 
        $this->db->limit($resultsPerPage, $offset);
        $events = $this->db->get()->result();

        // loop through events
        $this->load->model('Game');
        $this->load->model('Time');
        $this->load->library('md');
        foreach ($events as $event)
        {  
            // default profile image
            $event->ProfileImage = $event->ProfileImage == null ? "gwl_default.jpg" : $event->ProfileImage;

            // variables for user profile
            if($userID != null)
            {
                $event->Url = '/game/' . $event->GBID;
                $event->Image = $event->ImageSmall;
                $event->Username = $event->Username;
                $event->GameName = '<a href="' . $event->Url . '">' . $event->Name . '</a></b>';
            }
            // variables for game page
            else if($gbID != null)
            {
                $event->Url = '/user/' . $event->UserID;
                $event->Image = '/uploads/' . $event->ProfileImage;
                $event->Username = '<a href="' . $event->Url . '">' . $event->Username . '</a>';
                $event->GameName = $event->Name;
            // variables for homepage feed
            } else {
                $event->Url = '/game/' . $event->GBID;
                $event->Image = $event->ImageSmall;
                $event->Username = '<a href="/user/' . $event->UserID . '">' . $event->Username . '</a>';
                $event->GameName = '<a href="' . $event->Url . '">' . $event->Name . '</a></b>';
            }

            // build event label
            $event->Label = "";

            // currently playing
            if($event->CurrentlyPlaying) {
                $event->Label .= ' is playing';
                if($event->ListID != null && $event->StatusID != null)
                    $event->Label .= ", ";
                else if($event->ListID != null || $event->StatusID != null)
                    $event->Label .= " and ";
            }
            // list
            if($event->ListID != null) {
                $event->Label .= ' <span class="label label-' . $event->ListStyle . '">' . $event->ListThirdPerson . '</span>';
                if($event->StatusID != null)
                    $event->Label .= " and ";
            }
            // status
            if($event->StatusID != null) {
                $event->Label .= ' <span class="label label-' . $event->StatusStyle . '">' . $event->StatusThirdPerson . '</span>';
            }

            // add platforms in collection
            $event->platforms = $this->Game->getGamesPlatformsInCollection($event->GBID, $event->UserID);
            
            // build platforms label
            $event->PlatformsLabel = "";
            if(count($event->platforms) > 0) {
                $event->PlatformsLabel .= " on ";
                $i = 1;
                foreach($event->platforms as $platfrom)
                {
                    $event->PlatformsLabel .= $platfrom->Abbreviation;
                    if($i !== count($event->platforms)) {
                        $event->PlatformsLabel .= ", ";
                    }
                    $i++;
                }
            }

            // get comments
            $event->comments = $this->getComments($event->EventID, 2, $DateTimeFormat); // 2 = User Event Comment

            // format date stamp
            $event->DateStampFormatted = $this->Time->GetDateTimeInFormat($event->DateStamp, $DateTimeFormat);
        }

        return $events;
    }

    // add comment to event
    function addComment($linkID, $commentTypeID, $userID, $comment)
    {
        $data = array(
           'Comment' => $comment,
           'UserID' => $userID,
           'LinkID' => $linkID,
           'CommentTypeID' => $commentTypeID,
           'DateStamp' => date("Y-m-d H:i:s")
        );

        return $this->db->insert('comments', $data); 
    }

    // get comments
    function getComments($eventID, $commentTypeID, $DateTimeFormat) 
    {
        $this->db->select('*');
        $this->db->from('comments');
        $this->db->join('users', 'comments.UserID = users.UserID');
        $this->db->where('comments.LinkID', $eventID); 
        $this->db->where('comments.CommentTypeID', $commentTypeID);
        $this->db->order_by("DateStamp", "asc"); 
        $comments = $this->db->get()->result();

        // loop through events
        $this->load->model('Time');
        foreach ($comments as $comment)
        {
            // transform markdown to HTML
            $comment->Comment = $this->md->defaultTransform($comment->Comment);

            // format date stamp
            $comment->DateStampFormatted = $this->Time->GetDateTimeInFormat($comment->DateStamp, $DateTimeFormat);

            // default profile image
            $comment->ProfileImage = $comment->ProfileImage == null ? "gwl_default.jpg" : $comment->ProfileImage;
        }

        return $comments;
    }
}