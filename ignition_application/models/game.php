<?php 

class Game extends CI_Model {

    // game data
    var $gameID; 
    var $GBID; 
    var $name; 
    var $image;
    var $imageSmall;
    var $deck;
    var $platforms = null;

    // list button
    var $listID = 0; 
    var $listLabel = "Add to Collection";
    var $listStyle = "default";

    // status button
    var $statusID = 0; 
    var $statusLabel = "Unplayed";
    var $statusStyle = "default";

    // played data
    var $currentlyPlaying = false;
    var $dateComplete = null;
    var $hoursPlayed = null;

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    public function getGame($GBID, $userID)
    {
        // get game from db
        $this->db->select('*');
        $this->db->from('games');

        // add collection data if userID provided
        if($userID != null)
        {
            $this->db->join('collections', 'collections.GameID = games.GameID', 'left');
            $this->db->join('lists', 'collections.ListID = lists.ListID', 'left');
            $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID', 'left');
            $this->db->where('collections.UserID', $userID); 
        }

        $this->db->where('games.GBID', $GBID); 
        $query = $this->db->get();

        // if game returned
        if($query->num_rows() == 1)
        {
            $result = $query->first_row();

            $this->gameID = $result->GameID;
            $this->GBID = $result->GBID;
            $this->name = $result->Name;
            $this->image = $result->Image;
            $this->imageSmall = $result->ImageSmall;
            $this->deck = $result->Deck;

            // add collection data
            if($userID != null)
            {
                // list button
                $this->listID = $result->ListID;
                $this->listLabel = $result->ListName;
                $this->listStyle = $result->ListStyle;

                // status button
                $this->statusID = $result->StatusID;
                $this->statusLabel = $result->StatusName;
                $this->statusStyle = $result->StatusStyle;

                // played data
                $this->currentlyPlaying = ($result->CurrentlyPlaying == 1) ? true : false;
                $this->dateComplete = $result->DateComplete;
                $this->hoursPlayed = $result->HoursPlayed;
            }

            return true;
        }
        
        return false;
        //return $this->getGameFromGiantBomb($GBID, $userID, false);
    }
}