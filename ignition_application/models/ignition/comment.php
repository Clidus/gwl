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

class IG_Comment extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
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
}