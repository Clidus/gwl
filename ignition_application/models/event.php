<?php 

class Event extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // record new event
    function addEvent($userID, $gameID, $listID, $statusID, $currentlyPlaying) 
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
    function bumpEvent($eventID)
    {
        $this->db->where('EventID', $eventID); 
        return $this->db->update('userEvents', array('LastUpdated' => date("Y-m-d H:i:s"))); 
    }

    // get list of events by UserID
    function getEvents($userID, $gbID, $feedForUserID, $DateTimeFormat, $offset, $resultsPerPage) 
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
        $this->load->model('Collection');
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
            $event->platforms = $this->Collection->getGamesPlatformsInCollection($event->GBID, $event->UserID);
            
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
            $this->load->model('Comment');
            $event->comments = $this->Comment->getComments($event->EventID, 2, $DateTimeFormat); // 2 = User Event Comment

            // format date stamp
            $event->DateStampFormatted = $this->Time->GetDateTimeInFormat($event->DateStamp, $DateTimeFormat);
        }

        return $events;
    }
}