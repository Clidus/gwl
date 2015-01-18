<?php 

/*
|--------------------------------------------------------------------------
| Ignition v0.1 ignitionpowered.co.uk
|--------------------------------------------------------------------------
|
| This class extends the functionality of Ignition. You can add your
| own custom logic here.
|
*/

require_once APPPATH.'/models/ignition/user.php';

class User extends IG_User {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // get user by ID with following status
    function getUserByIdWithFollowingStatus($userID, $viewedByUserID)
    {
        $this->db->select('*');
        $this->db->from('users');
        if($viewedByUserID != null) $this->db->join('following', 'users.UserID = following.ChildUserID AND following.ParentUserID = ' . $viewedByUserID, 'left');
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

    // follow or unfollow user (parent is following child)
    // returns true if user is following, false is not following
    function followUser($parentUserID, $childUserID)
    {
        $data = array(
           'ParentUserID' => $parentUserID,
           'ChildUserID' => $childUserID
        );

        // check if user is already following user
        $query = $this->db->get_where('following', $data);
        if ($query->num_rows() > 0)
        {
            // unfollow user
            $this->db->delete('following', $data); 

            return false;
        } else {
            // follow user
            $this->db->insert('following', $data); 

            return true;
        }
    }
}