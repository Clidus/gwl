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

    // record new user event
    function addUserEvent($userID, $gameID, $listID, $statusID, $currentlyPlaying) 
    {
        // dont record event if only change is no longer currently playing
        if($listID == null && $statusID == null && $currentlyPlaying === "false")
            return;

        $data = array(
           'UserID' => $userID,
           'GameID' => $gameID,
           'LastUpdated' => date("Y-m-d H:i:s")
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
            $data['DateStamp'] = date("Y-m-d H:i:s");   // set datestamp only when event is first added

            return $this->db->insert('userEvents', $data); 
        }
    }

    // bump event to the top of the feed by changing the last updated time
    function bumpUserEvent($eventID)
    {
        $this->db->where('EventID', $eventID); 
        return $this->db->update('userEvents', array('LastUpdated' => date("Y-m-d H:i:s"))); 
    }

    // get list of events by UserID
    function getUserEvents($userID, $gbID, $feedForUserID, $DateTimeFormat, $offset, $resultsPerPage) 
    {
        $this->db->select('*');
        $this->db->from('userEvents');
        $this->db->join('games', 'userEvents.GameID = games.GameID');
        $this->db->join('users', 'userEvents.UserID = users.UserID');
        if($feedForUserID != null) $this->db->join('following', 'userEvents.UserID = following.ChildUserID AND following.ParentUserID = ' . $feedForUserID, 'left');
        $this->db->join('lists', 'userEvents.ListID = lists.ListID', 'left');
        $this->db->join('gameStatuses', 'userEvents.StatusID = gameStatuses.StatusID', 'left');
        if($userID != null) $this->db->where('userEvents.UserID', $userID); // user page
        if($gbID != null) $this->db->where('games.GBID', $gbID); // game page
        if($feedForUserID != null) $this->db->where('userEvents.UserID = ' . $feedForUserID . ' OR following.ID IS NOT NULL'); // user feed (following users and self)
        $this->db->order_by("userEvents.LastUpdated", "desc"); 
        $this->db->limit($resultsPerPage, $offset);
        $events = $this->db->get()->result();

        // loop through events
        $this->load->model('Game');
        $this->load->model('Time');
        $this->load->library('md');
        foreach ($events as $event)
        {  
            // default profile image
            $event->ProfileImage = $event->ProfileImage == null ? $this->config->item('default_profile_image') : $event->ProfileImage;
            $event->GameUrl = '/game/' . $event->GBID;
            $event->GameImage = $event->ImageSmall;
            $event->UserUrl = '/user/' . $event->UserID;
            $event->UserImage = '/uploads/' . $event->ProfileImage;

            // on user page, dont make username a link
            if($userID != null)
                $event->Username = $event->Username;
            else
                $event->Username = '<a href="' . $event->UserUrl . '">' . $event->Username . '</a>';

            // on game page, dont make game name a link
            if($gbID != null)
                $event->GameName = $event->Name;
            else
                $event->GameName = '<a href="' . $event->GameUrl . '">' . $event->Name . '</a></b>';
        

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
        if($commentTypeID == 2) $this->bumpUserEvent($linkID);

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