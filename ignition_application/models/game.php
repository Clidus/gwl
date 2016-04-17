<?php 

class Game extends CI_Model {

    // game data
    var $gameID; 
    var $GBID; 
    var $name; 
    var $image;
    var $imageSmall;
    var $deck;
    var $platforms;

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
    var $dateComplete;
    var $hoursPlayed;

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // get game by GBID
    public function getGame($GBID, $userID)
    {
        // check if game is in database
        if($this->isGameInDB($GBID))
        {
            // get game from database
            if($this->getGameFromDatabase($GBID, $userID))
                // game found
                return true;
        }

        // game was not found, get from Giant Bomb
        $this->load->model('GiantBomb');
        $result = $this->GiantBomb->getGame($GBID);

        // if game was returned
        if($result != null && $result->error == "OK" && $result->number_of_total_results > 0)
        {
            // add game to database
            $this->addGame($result->results);

            // get game from database
            return $this->getGameFromDatabase($GBID, $userID);
        } else {
            // game was not found
            return false;
        }
    }

    // is game in database?
    function isGameInDB($GBID)
    {
        $query = $this->db->get_where('games', array('GBID' => $GBID));

        return $query->num_rows() > 0 ? true : false;
    }

    // get game from database
    public function getGameFromDatabase($GBID, $userID) 
    {   
        // get game from db
        $this->db->select('games.GameID, games.GBID, games.Name, games.Image, games.ImageSmall, games.Deck, lists.ListID, lists.ListName, lists.ListStyle');
        $this->db->select('gameStatuses.StatusID, gameStatuses.StatusName, gameStatuses.StatusStyle, collections.CurrentlyPlaying, collections.DateComplete, collections.HoursPlayed');
        $this->db->from('games');

        if($userID == null) 
            $userID = 0; // prevents joining on UserID causing an error

        $this->db->join('collections', 'collections.GameID = games.GameID AND collections.UserID = ' . $userID, 'left');
        $this->db->join('lists', 'collections.ListID = lists.ListID', 'left');
        $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID', 'left');

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

            // if game is in collection
            if($result->ListID != null)
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

            $this->getPlatforms($userID);         

            return true;
        }
        
        return false;
    }

    // get platforms for game
    function getPlatforms($userID)
    {
        // error if no GameID
        if($this->gameID == null)
            return false;

        // prevents joining on UserID causing an error
        if($userID == null) 
            $userID = 0;

        $this->db->select('platforms.platformID, platforms.GBID, platforms.name, platforms.abbreviation');
        $this->db->select('(CASE WHEN collectionPlatform.CollectionID IS NULL THEN 0 ELSE 1 END) AS inCollection');
        $this->db->from('games');
        $this->db->join('gamePlatforms', 'games.GameID = gamePlatforms.GameID');
        $this->db->join('platforms', 'gamePlatforms.PlatformID = platforms.PlatformID');
        $this->db->join('collections', 'games.GameID = collections.GameID AND collections.UserID = ' . $userID, 'left');
        $this->db->join('collectionPlatform', 'collections.ID = collectionPlatform.CollectionID AND collectionPlatform.PlatformID = platforms.PlatformID','left');
        $this->db->where('games.GameID', $this->gameID); 
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            $this->platforms = $query->result();

            return true;
        }

        return false;
    }

    // get GameID from GBID
    function getGameIDFromGiantBombID($GBID)
    {
        $query = $this->db->get_where('games', array('GBID' => $GBID));

        if($query->num_rows() == 1)
            return $query->first_row()->GameID;
        else
            return null;
    }

    // add game to database
    function addGame($game)
    {
        $this->load->model('GiantBomb');
        $releaseDate = $this->GiantBomb->convertReleaseDate($game);

        $data = array(
           'GBID' => $game->id,
           'Name' => $game->name,
           'Image' => is_object($game->image) ? $game->image->small_url : null,
           'ImageSmall' => is_object($game->image) ? $game->image->icon_url : null,
           'Deck' => $game->deck,
           'ReleaseDate' => $releaseDate,
           'LastUpdated' => date('Y-m-d')
        );

        if($this->db->insert('games', $data))
        {
            $this->gameID = $this->db->insert_id();
            
            // add platforms to game
            $this->addPlatforms($game);

            return true;
        } else {
            return false;
        }
    }

    // update game cache
    function updateGame($game)
    {
        // get GameID
        $this->gameID = $this->getGameIDFromGiantBombID($game->id);

        // if game exists
        if($this->gameID != null) {
            // get release date
            $this->load->model('GiantBomb');
            $releaseDate = $this->GiantBomb->convertReleaseDate($game);

            $data = array(
               'Name' => $game->name,
               'Image' => is_object($game->image) ? $game->image->small_url : null,
               'ImageSmall' => is_object($game->image) ? $game->image->icon_url : null,
               'Deck' => $game->deck,
               'ReleaseDate' => $releaseDate,
               'LastUpdated' => date('Y-m-d')
            );

            // update game data
            $this->db->where('GameID', $this->gameID);
            $this->db->update('games', $data); 

            $this->addPlatforms($game);
        }
    }

    // update game cache
    function addPlatforms($game)
    {
        // add platforms to game
        if(property_exists($game, "platforms") && $game->platforms != null)
        {
            // load platforms model 
            $this->load->model('Platform');

            // get platforms game already has
            $this->getPlatforms(null);

            // loop over platforms returned by GB
            $platformsToAdd = [];
            foreach($game->platforms as $gbPlatform)
            {
                // loop over platforms for game already in db
                $gameHasPlatform = false;
                if($this->platforms != null) {
                    foreach ($this->platforms as $platform)
                    {
                        // if game has platform
                        if($platform->GBID == $gbPlatform->id)
                        {
                            $gameHasPlatform = true;
                            break;
                        }
                    }
                }

                // if game doesnt have platform
                if(!$gameHasPlatform) {
                    // get or add platform to db
                    $platform = $this->Platform->getOrAddPlatform($gbPlatform);

                    // add to list of platforms to add to game
                    array_push($platformsToAdd, array(
                      'GameID' => $this->gameID,
                      'PlatformID' => $platform->PlatformID
                   ));
                }
            }

            // if there are platforms to add to game
            if(count($platformsToAdd) > 0)
                // add to game in db
                $this->db->insert_batch('gamePlatforms', $platformsToAdd); 
        }
    }
}