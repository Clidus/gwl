<?php 

class Game extends CI_Model {

    var $errorMessage = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    var $resultsPerPage = 10;
   
    // search Giant Bomb API for games  
    function searchForGame($query, $page, $userID) {  
        //$url = $this->config->item('gb_api_root') . "/search/?api_key=" . $this->config->item('gb_api_key') . "&format=json&resources=game&limit=" . $this->resultsPerPage . "&page=" . $page . "&query=" . urlencode ($query);
        
        // giant bomb search API is broken. Filter by game resource instead
        $offset = $this->resultsPerPage * ($page-1);
        $url = $this->config->item('gb_api_root') . "/games/?api_key=" . $this->config->item('gb_api_key') . "&format=json&limit=" . $this->resultsPerPage . "&offset=" . $offset . "&filter=name:" . urlencode ($query);

        $result = $this->Utility->getData($url);

        if(is_object($result) && $result->error == "OK" && $result->number_of_total_results > 0)
        {                                                                                                    
            foreach($result->results as $game)
            {    
                $game = $this->addCollectionInfo($game, $userID);
            }
            return $result;
        } else {
            return null;
        }
    }

    // get game from Giant Bomb API by ID
    public function getGameByID($gbID, $userID) {   
        $url = $this->config->item('gb_api_root') . "/game/" . $gbID . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";
        $result = $this->Utility->getData($url);

        if(is_object($result) && $result->error == "OK" && $result->number_of_total_results > 0)
        {
            // add collection info to game object
            return $this->addCollectionInfo($result->results, $userID);
        } else {
            return null;
        }
    }

    // add collection status (ownership and played status) and platforms to game object
    function addCollectionInfo($game, $userID)
    {
        $collection = $this->isGameIsInCollection($game->id, $userID);

        // if in collection
        if($collection != null)
        {
            $game->listID = $collection->ListID;
            $game->statusID = $collection->StatusID;

            // list button
            switch($game->listID)
            {
                case 1:
                    $game->listLabel = "Own";
                    $game->listStyle = "success";
                    break;
                case 2:
                    $game->listLabel = "Want";
                    $game->listStyle = "warning";
                    break;
                case 3:
                    $game->listLabel = "Borrowed";
                    $game->listStyle = "info";
                    break;
                case 4:
                    $game->listLabel = "Lent";
                    $game->listStyle = "danger";
                    break;
                case 5:
                    $game->listLabel = "Played";
                    $game->listStyle = "primary";
                    break;
            }

            // status button
            switch($game->statusID)
            {
                case 1:
                    $game->statusLabel = "Unplayed";
                    $game->statusStyle = "default";
                    break;
                case 2:
                    $game->statusLabel = "Unfinished";
                    $game->statusStyle = "warning";
                    break;
                case 3:
                    $game->statusLabel = "Complete";
                    $game->statusStyle = "success";
                    break;
                case 4:
                    $game->statusLabel = "Uncompletable";
                    $game->statusStyle = "primary";
                    break;
            }

            // get platforms user has game in collection
            $platforms = $this->getGamesPlatformsInCollection($game->id, $userID);
        } else {
            // not in collection
            $game->listID = 0; 
            $game->statusID = 0; 
            $game->listLabel = "Add to Collection";
            $game->listStyle = "default";
            $game->statusLabel = "Unplayed";
            $game->statusStyle = "default";
            $platforms = null;
        }

        // add platforms user has game on in collection (if any)
        // if game has platforms
        if(property_exists($game, "platforms") && $game->platforms != null)
        {
            // loop over platforms game is on
            foreach($game->platforms as $gbPlatform)
            {
                $gbPlatform->inCollection = false;
                if($platforms != null)
                {
                    // loop over platforms user has in collection
                    foreach ($platforms->result() as $platform)
                    {
                        // if platform is on game in collection
                        if($platform->GBID == $gbPlatform->id)
                        {
                            $gbPlatform->inCollection = true;
                            break;
                        }
                    }
                }
            }
        }

        return $game;
    }

    // is game in users collection?
    // returns collection record if in collection, null if not in collection
    function isGameIsInCollection($GBID, $userID)
    {
        $this->db->select('*');
        $this->db->from('collections');
        $this->db->join('games', 'collections.GameID = games.GameID');
        $this->db->where('games.GBID', $GBID); 
        $this->db->where('collections.UserID', $userID); 
        $query = $this->db->get();

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }

    // is game in database?
    function isGameInDB($GBID)
    {
        $query = $this->db->get_where('games', array('GBID' => $GBID));

        return $query->num_rows() > 0 ? true : false;
    }

    // add game to database
    function addGame($game)
    {
        $data = array(
           'GBID' => $game->id,
           'Name' => $game->name,
           'Image' => is_object($game->image) ? $game->image->small_url : null
        );

        return $this->db->insert('games', $data); 
    }

    // add game to users collection
    function addToCollection($GBID, $userID, $listID)
    {
        // get GameID from GBID
        $query = $this->db->get_where('games', array('GBID' => $GBID));
        if($query->num_rows() == 1)
        {
            $row = $query->first_row();

            $data = array(
               'UserID' => $userID,
               'GameID' => $row->GameID,
               'ListID' => $listID,
               'StatusID' => 1 // default to unplayed
            );

            $this->db->insert('collections', $data); 

            return $this->db->insert_id(); // return CollectionID
        }
    }

    // update list game is on in collection
    function updateList($GBID, $userID, $listID)
    {
        // get GameID from GBID
        $query = $this->db->get_where('games', array('GBID' => $GBID));
        if($query->num_rows() == 1)
        {
            $row = $query->first_row();

            $this->db->where('GameID', $row->GameID); 
            $this->db->where('UserID', $userID); 
            $this->db->update('collections', array('ListID' => $listID)); 
        }
    }

    // update played status of game in collection
    function updateStatus($GBID, $userID, $statusID)
    {
        // get GameID from GBID
        $query = $this->db->get_where('games', array('GBID' => $GBID));
        if($query->num_rows() == 1)
        {
            $row = $query->first_row();

            $this->db->where('GameID', $row->GameID); 
            $this->db->where('UserID', $userID); 
            $this->db->update('collections', array('StatusID' => $statusID)); 
        }
    }

    // remove game from users collection
    function removeFromCollection($GBID, $userID)
    {
        // get GameID from GBID

        $this->db->select('*');
        $this->db->from('collections');
        $this->db->join('games', 'collections.GameID = games.GameID');
        $this->db->where('games.GBID', $GBID); 
        $this->db->where('collections.UserID', $userID); 
        $query = $this->db->get();
        if($query->num_rows() == 1)
        {
            $row = $query->first_row();

            // delete collection record
            $this->db->where('GameID', $row->GameID);
            $this->db->where('UserID', $userID);
            $this->db->delete('collections'); 

            // delete collectionPlatform record
            $this->db->where('CollectionID', $row->ID);
            $this->db->delete('collectionPlatform'); 
        }
    }

    function addPlatform($collectionID, $platformGBID)
    {
        // get PlatformID from GBID
        $query = $this->db->get_where('platforms', array('GBID' => $platformGBID));
        if($query->num_rows() == 1)
        {
            $row = $query->first_row();

            $data = array(
               'CollectionID' => $collectionID,
               'PlatformID' => $row->PlatformID
            );

            return $this->db->insert('collectionPlatform', $data); 
        }
    }

    function removePlatform($collectionID, $platformGBID)
    {
        // get PlatformID from GBID
        $query = $this->db->get_where('platforms', array('GBID' => $platformGBID));
        if($query->num_rows() == 1)
        {
            $row = $query->first_row();

            $this->db->where('CollectionID', $collectionID);
            $this->db->where('PlatformID', $row->PlatformID);
            $this->db->delete('collectionPlatform'); 
        }
    }

    function getGamesPlatformsInCollection($GBID, $userID)
    {
        $this->db->select('platforms.GBID');
        $this->db->from('collections');
        $this->db->join('games', 'collections.GameID = games.GameID');
        $this->db->join('collectionPlatform', 'collections.ID = collectionPlatform.CollectionID');
        $this->db->join('platforms', 'collectionPlatform.PlatformID = platforms.PlatformID');
        $this->db->where('games.GBID', $GBID); 
        $this->db->where('collections.UserID', $userID); 
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            return $query;
        }

        return null;
    }

    function isGameOnPlatformInCollection($collectionID, $platformGBID)
    {
        $this->db->select('*');
        $this->db->from('collections');
        $this->db->join('games', 'collections.GameID = games.GameID');
        $this->db->join('collectionPlatform', 'collections.ID = collectionPlatform.CollectionID');
        $this->db->join('platforms', 'collectionPlatform.PlatformID = platforms.PlatformID');
        $this->db->where('collections.ID', $collectionID); 
        $this->db->where('platforms.GBID', $platformGBID); 
        $query = $this->db->get();

        return $query->num_rows() > 0 ? true : false;
    }
}