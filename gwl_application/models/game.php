<?php 

class Game extends CI_Model {

    var $errorMessage = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    var $APIRoot = "http://www.giantbomb.com/api";    
    var $resultsPerPage = 10;
   
    // search Giant Bomb API for games  
    function searchForGame($query, $page, $userID) {  
        //$url = $this->APIRoot . "/search/?api_key=" . $this->config->item('gb_api_key') . "&format=json&resources=game&limit=" . $this->resultsPerPage . "&page=" . $page . "&query=" . urlencode ($query);
        
        // giant bomb search API is broken. Filter by game resource instead
        $url = $this->APIRoot . "/games/?api_key=" . $this->config->item('gb_api_key') . "&format=json&limit=" . $this->resultsPerPage . "&page=" . $page . "&filter=name:" . urlencode ($query);
    
        $result = $this->getData($url);

        if(is_object($result) && $result->error == "OK" && $result->number_of_total_results > 0)
        {                                                                                                    
            foreach($result->results as $game)
            {    
                $game = $this->addCollectionStatus($game, $userID);
            }
            return $result;
        } else {
            return null;
        }
    }

    // get game from Giant Bomb API by API URL
    function getGame($apiUrl) {  
        $url = $apiUrl . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";

        return $this->getData($url);
    }

    // get game from Giant Bomb API by ID
    public function getGameByID($gbID, $userID) {   
        $url = $this->APIRoot . "/game/" . $gbID . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";
        $result = $this->getData($url);

        if(is_object($result) && $result->error == "OK" && $result->number_of_total_results > 0)
        {
            return $game = $this->addCollectionStatus($result->results, $userID);
        } else {
            return null;
        }
    }

    // add collection status (ownership and played status) to game object
    function addCollectionStatus($game, $userID)
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
        } else {
            // not in collection
            $game->listID = 0; 
            $game->statusID = 0; 
            $game->listLabel = "Add to Collection";
            $game->listStyle = "default";
            $game->statusLabel = "Unplayed";
            $game->statusStyle = "default";
        }

        return $game;
    }

    // get API response
    function getData($url) {

        $ch = curl_init($url); 
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
        );                        
        curl_setopt_array($ch, $options);             
        $json = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($json); 
             
        return $result;
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
            $row = $query->first_row();

            return $row;
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
    function addGame($API_Detail)
    {
        // get game details from Giant Bomb API
        $result = $this->getGame($API_Detail);

        if($result->error == "OK" && $result->number_of_total_results == 1) 
        {
            $data = array(
               'GBID' => $result->results->id,
               'Name' => $result->results->name,
               'API_Detail' => $result->results->api_detail_url,
               'Image' => is_object($result->results->image) ? $result->results->image->small_url : null
            );

            return $this->db->insert('games', $data); 
        } else {
            return false;
        }
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

            return $this->db->insert('collections', $data); 
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
        $query = $this->db->get_where('games', array('GBID' => $GBID));
        if($query->num_rows() == 1)
        {
            $row = $query->first_row();

            $this->db->where('GameID', $row->GameID);
            $this->db->where('UserID', $userID);
            $this->db->delete('collections'); 
        }
    }
}