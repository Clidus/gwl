<?php 

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
        $this->db->join('following', 'users.UserID = following.ChildUserID AND following.ParentUserID = ' . $viewedByUserID, 'left');
        $this->db->where('users.UserID', $userID); 
        $query = $this->db->get();

        if($query->num_rows() == 1)
        {
            $user = $query->first_row();
            $user->ProfileImage = $user->ProfileImage == null ? $this->config->item('default_profile_image') : $user->ProfileImage;
            return $user;
        }

        return null;
    }
    
    // add comment
    function addComment($linkID, $commentTypeID, $userID, $comment)
    {
        $data = array(
           'Comment' => $comment,
           'UserID' => $userID,
           'LinkID' => $linkID,
           'CommentTypeID' => $commentTypeID,
           'DateStamp' => date("Y-m-d H:i:s")
        );

        // if a comment for a user event (comment type id = 2) then bump the last updated date stamp of the event
        if($commentTypeID == 2) {
            $this->load->model('Event');
            $this->Event->bumpEvent($linkID);
        }

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
            $comment->ProfileImage = $comment->ProfileImage == null ? $this->config->item('default_profile_image') : $comment->ProfileImage;
        }

        return $comments;
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