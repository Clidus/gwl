<?php 

class Game extends CI_Model {

    var $errorMessage = '';
    var $resultsPerPage = 10;

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
   
    // search Giant Bomb API for games  
    function searchForGame($query, $page, $userID) 
    {  
        // build API request
        $url = $this->config->item('gb_api_root') . "/search/?api_key=" . $this->config->item('gb_api_key') . "&format=json&resources=game&limit=" . $this->resultsPerPage . "&page=" . $page . "&query=" . urlencode ($query);
      
        // make API request
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
    public function getGameByID($gbID, $userID, $returnCompleteResponse) 
    {   
        // build API request
        $url = $this->config->item('gb_api_root') . "/game/" . $gbID . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";
        
        // make API request
        $result = $this->Utility->getData($url);
        
        if(is_object($result))
        {
            if($result->error == "OK" && $result->number_of_total_results > 0)
            {
                // add collection info to game object
                $result->results = $this->addCollectionInfo($result->results, $userID);
            }

            return $returnCompleteResponse ? $result : $result->results;
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
            // list button
            $game->listID = $collection->ListID;
            $game->listLabel = $collection->ListName;
            $game->listStyle = $collection->ListStyle;

            // status button
            $game->statusID = $collection->StatusID;
            $game->statusLabel = $collection->StatusName;
            $game->statusStyle = $collection->StatusStyle;

            // data
            $game->currentlyPlaying = ($collection->CurrentlyPlaying == 1) ? true : false;
            $game->dateComplete = $collection->DateComplete;
            $game->hoursPlayed = $collection->HoursPlayed;

            // get platforms user has game in collection
            $platforms = $this->getGamesPlatformsInCollection($game->id, $userID);
        // not in collection
        } else {
            // list button
            $game->listID = 0; 
            $game->listLabel = "Add to Collection";
            $game->listStyle = "default";

            // status button
            $game->statusID = 0; 
            $game->statusLabel = "Unplayed";
            $game->statusStyle = "default";

            // data
            $game->currentlyPlaying = false;
            $game->dateComplete = null;
            $game->hoursPlayed = null;

            // get platforms user has game in collection
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
                    foreach ($platforms as $platform)
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
        $this->db->join('lists', 'collections.ListID = lists.ListID');
        $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID');
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

    function getReleaseDate($game)
    {
        // original release date
        if($game->original_release_date != null)
        {
            return $game->original_release_date;
        } else {
            // expected release date
            if($game->expected_release_year != null)
            {
                // year
                $releaseYear = $game->expected_release_year;

                // month (assume end of year if unknown)
                $releaseMonth =  ($game->expected_release_month != null ? $game->expected_release_month : "12");

                // day (assume end of month if unknown)
                if($game->expected_release_day != null) {
                    $releaseDay = $game->expected_release_day;
                } else {
                    switch($releaseMonth) {
                        case "01":
                        case "03":
                        case "05":
                        case "07":
                        case "08":
                        case "10":
                        case "12":
                            $releaseDay = "31";
                            break;
                        case "02":
                            $releaseDay = "28";
                            break;
                        case "04":
                        case "06":
                        case "09":
                        case "11":
                            $releaseDay = "30";
                            break;
                    }
                }

                return $releaseYear . "-" . $releaseMonth . "-" . $releaseDay;
            // unknown release date
            } else {
                // if completly unknwon, set to five years in the future
                return (date("Y")+5) . "-12-31";
            }
        }
    }

    // add game to database
    function addGame($game)
    {
        $releaseDate = $this->getReleaseDate($game);

        $data = array(
           'GBID' => $game->id,
           'Name' => $game->name,
           'Image' => is_object($game->image) ? $game->image->small_url : null,
           'ImageSmall' => is_object($game->image) ? $game->image->icon_url : null,
           'Deck' => $game->deck,
           'ReleaseDate' => $releaseDate,
           'LastUpdated' => date('Y-m-d')
        );

        return $this->db->insert('games', $data); 
    }

    // add game to users collection
    function addToCollection($gameID, $userID, $listID)
    {
        $data = array(
           'UserID' => $userID,
           'GameID' => $gameID,
           'ListID' => $listID,
           'StatusID' => 1 // default to unplayed
        );

        $this->db->insert('collections', $data); 

        return $this->db->insert_id(); // return CollectionID
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

            // delete userEvents records
            $this->db->where('GameID', $row->GameID);
            $this->db->where('UserID', $userID);
            $this->db->delete('userEvents'); 
        }
    }

    // add platform to game in collection
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

    // remove platform to game in collection
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

    // get platforms game is on in collection
    function getGamesPlatformsInCollection($GBID, $userID)
    {
        $this->db->select('platforms.GBID, platforms.Name, platforms.Abbreviation');
        $this->db->from('collections');
        $this->db->join('games', 'collections.GameID = games.GameID');
        $this->db->join('collectionPlatform', 'collections.ID = collectionPlatform.CollectionID');
        $this->db->join('platforms', 'collectionPlatform.PlatformID = platforms.PlatformID');
        $this->db->where('games.GBID', $GBID); 
        $this->db->where('collections.UserID', $userID); 
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            return $query->result();
        }

        return null;
    }

    // check if game is on platform in collection
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

    // update currently playing, hours played and date completed for game
    function updateProgression($collectionID, $currentlyPlaying, $hoursPlayed, $dateCompleted)
    {
        if($hoursPlayed == '') $hoursPlayed = null;
        if($dateCompleted == '') $dateCompleted = null;
        $currentlyPlayingBit = ($currentlyPlaying === "true");

        $this->db->where('ID', $collectionID); 
        $this->db->update('collections', array('CurrentlyPlaying' => $currentlyPlayingBit, 'HoursPlayed' => $hoursPlayed, 'DateComplete' => $dateCompleted)); 
    }

    // get GameID from GBID
    function getGameID($GBID)
    {
        $query = $this->db->get_where('games', array('GBID' => $GBID));

        if($query->num_rows() == 1)
            return $query->first_row()->GameID;
        else
            return null;
    }

    // get list
    function getListDetails($listID)
    {
        $this->db->select('*');
        $this->db->from('lists');
        $this->db->where('lists.ListID', $listID); 
        $query = $this->db->get();

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }

    // get played status
    function getStatusDetails($statusID)
    {
        $this->db->select('*');
        $this->db->from('gameStatuses');
        $this->db->where('gameStatuses.StatusID', $statusID); 
        $query = $this->db->get();

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }

    // get users collection
    function getCollection($userID, $filters, $offset, $resultsPerPage)
    {
        // if offset is provided, get list of games
        if($offset !== null) {
            $this->db->select('ImageSmall, GBID, Name, ListStyle, ListName, StatusStyle, StatusName');
        }
        // if no offset, count number of games in collection
        else {
            // collection: everything not on the want list
            $this->db->select('COUNT(DISTINCT (CASE WHEN collections.ListID != 2 THEN collections.GameID END)) AS Collection');
            // completable collection: everything not uncompletable or on the want list
            $this->db->select('COUNT(DISTINCT (CASE WHEN collections.StatusID != 4 AND collections.ListID != 2 THEN collections.GameID END)) AS CompletableCollection');
            // completed: everything completed and not on the want list
            $this->db->select('COUNT(DISTINCT (CASE WHEN collections.StatusID = 3 AND collections.ListID != 2 THEN collections.GameID END)) AS Completed');
            // backlog: everything unplayed or unfinished and not on the want list
            $this->db->select('COUNT(DISTINCT (CASE WHEN (collections.StatusID = 1 OR collections.StatusID = 2) AND collections.ListID != 2 THEN collections.GameID END)) AS Backlog');
            // want: everything on the want list
            $this->db->select('COUNT(DISTINCT (CASE WHEN collections.ListID = 2 THEN collections.GameID END)) AS Want');   
        }

        $this->db->from('collections');

        if($filters !== null)
        {
            $this->db->join('lists', 'collections.ListID = lists.ListID');
            $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID');
            $this->db->join('collectionPlatform', 'collections.ID = collectionPlatform.CollectionID', 'left');
            $this->db->join('games', 'collections.GameID = games.GameID');
            $this->db->where('collections.UserID', $userID); 
        
            // filter out listID's
            $excludeLists = array();
            foreach($filters->lists as $list)
            {
                if(!$list->Selected)
                    $excludeLists[] = $list->ID;
            }   

            if(count($excludeLists) > 0) 
            {
                $this->db->where_not_in('collections.ListID', $excludeLists);
            }

            // filter out statusID's
            $excludeStatuses = array();
            foreach($filters->statuses as $status)
            {
                if(!$status->Selected)
                    $excludeStatuses[] = $status->ID;
            } 

            if(count($excludeStatuses) > 0) 
            {
                $this->db->where_not_in('collections.StatusID', $excludeStatuses);
            }
            
            // filter out platformID's
            $excludePlatforms = array();
            $includeNoPlatforms = true;
            foreach($filters->platforms as $platform)
            {
                if(!$platform->Selected)
                {
                    if($platform->ID == 0)
                        $includeNoPlatforms = false;
                    
                    $excludePlatforms[] = $platform->ID;
                }
            } 

            if(count($excludePlatforms) > 0) 
            {
                if($includeNoPlatforms)
                {
                    $this->db->where("(`collectionPlatform`.`PlatformID` NOT IN (" . implode(",", $excludePlatforms) . ") OR `collectionPlatform`.`PlatformID` IS NULL)");
                } else {
                    $this->db->where_not_in('collectionPlatform.PlatformID', $excludePlatforms);
                }
            }
        }
        
        // only apply group by, order by and limit if getting list of games
        if($offset !== null) {
            // group by game to remove deuplicates produced by platforms
            $this->db->group_by("collections.GameID");

            // order by
            $currentSort = 0;
            foreach($filters->orderBy as $sort)
            {
                if($sort->Selected)
                {
                    $currentSort = $sort->ID;
                }
            } 

            switch($currentSort)
            {
                case 0:
                    $this->db->order_by("games.ReleaseDate", "asc");
                    break;
                case 1:
                    $this->db->order_by("games.ReleaseDate", "desc");
                    break;
                case 2:
                    $this->db->order_by("games.Name", "asc");
                    break;
                case 3:
                    $this->db->order_by("games.Name", "desc");
                    break;
                case 4:
                    $this->db->order_by("collections.HoursPlayed", "desc");
                    break;
                case 5:
                    $this->db->order_by("collections.HoursPlayed", "asc");
                    break;
            }
            
            // paging
            $this->db->limit($resultsPerPage, $offset);

            // get results
            $games = $this->db->get()->result();
            
            // add platforms to games
            foreach ($games as $game)
            {
                $game->Platforms = $this->getGamesPlatformsInCollection($game->GBID, $userID);
            }

            return $games;
        } else {
            // calculate collection stats
            $stats = $this->db->get()->first_row();
            if($stats->Completed == null) $stats->Completed = 0;
            if($stats->Backlog == null) $stats->Backlog = 0;
            if($stats->Want == null) $stats->Want = 0;
            $stats->PercentComplete = $stats->CompletableCollection == 0 ? 0 : round((($stats->Completed/$stats->CompletableCollection) * 100), 0);
            return $stats;
        }
    }

    // get users collection by platform
    function getCollectionByPlatform($userID) 
    {
        $this->db->select('collectionPlatform.PlatformID');
        $this->db->select('platforms.Name,');
        $this->db->select('platforms.Image,');
        // collection: everything not on the want list
        $this->db->select('COUNT(CASE WHEN collections.ListID != 2 THEN collections.GameID END) AS Collection');
        // completable collection: everything not uncompletable or on the want list
        $this->db->select('COUNT(CASE WHEN collections.StatusID != 4 AND collections.ListID != 2 THEN collections.GameID END) AS CompletableCollection');
        // completed: everything completed and not on the want list
        $this->db->select('COUNT(CASE WHEN collections.StatusID = 3 AND collections.ListID != 2 THEN collections.GameID END) AS Completed');
        // backlog: everything unplayed or unfinished and not on the want list
        $this->db->select('COUNT(CASE WHEN (collections.StatusID = 1 OR collections.StatusID = 2) AND collections.ListID != 2 THEN collections.GameID END) AS Backlog');
        // want: everything on the want list
        $this->db->select('COUNT(CASE WHEN collections.ListID = 2 THEN collections.GameID END) AS Want');   
        // percentage complete: (completed / completable collection) * 100
        $this->db->select('CAST((COUNT(CASE WHEN collections.StatusID = 3 AND collections.ListID != 2 THEN collections.GameID END) / COUNT(CASE WHEN collections.StatusID != 4 AND collections.ListID != 2 THEN collections.GameID END)) * 100 AS UNSIGNED) AS Percentage');   
    
        $this->db->from('collections');
        $this->db->join('lists', 'collections.ListID = lists.ListID');
        $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID');
        $this->db->join('collectionPlatform', 'collections.ID = collectionPlatform.CollectionID', 'left');
        $this->db->join('platforms', 'collectionPlatform.PlatformID = platforms.PlatformID', 'left');
        $this->db->join('games', 'collections.GameID = games.GameID');
        
        $this->db->where('collections.UserID', $userID); 

        $this->db->group_by("collectionPlatform.PlatformID");
        $this->db->order_by("Percentage", "desc"); 

        // get results
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $platforms = $query->result();

            foreach ($platforms as $platform)
            {  
                // default profile image
                $platform->Name = $platform->Name == null ? "No Platform" : $platform->Name;
                $platform->Image = $platform->Image == null ? $this->config->item('default_profile_image') : $platform->Image;
            }

            return $platforms;
        }

        return null;
    }

    // get users collection
    function getCurrentlyPlaying($userID)
    {
        $this->db->select('*');
        $this->db->from('collections');
        $this->db->join('games', 'collections.GameID = games.GameID');
        $this->db->join('gameStatuses', 'gameStatuses.StatusID = collections.StatusID');
        $this->db->where('collections.UserID', $userID); 
        $this->db->where('collections.CurrentlyPlaying', 1); 
        $this->db->order_by("games.Name", "asc");

        // get results
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            return $query->result();
        }

        return null;
    }

    // get platforms in collection
    function getPlatformsInCollection($userID)
    {
        $this->db->select('platforms.PlatformID AS ID, platforms.Abbreviation AS Name, count(*) as Games');
        $this->db->from('collections');
        $this->db->join('collectionPlatform', 'collections.ID = collectionPlatform.CollectionID', 'left');
        $this->db->join('platforms', 'collectionPlatform.PlatformID = platforms.PlatformID', 'left');
        $this->db->where('collections.UserID', $userID); 
        $this->db->group_by("platforms.PlatformID");
        $this->db->order_by("Games", "desc"); 
        
        // get results
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $platforms = $query->result();

            foreach ($platforms as $platform)
            {  
                $platform->ID = $platform->ID == null ? 0 : $platform->ID;
                $platform->Name = $platform->Name == null ? "No Platform" : $platform->Name;
                $platform->Selected = true;
            }

            return $platforms;
        }

        return null;
    }

    // get lists in collection
    function getListsInCollection($userID)
    {
        $this->db->select('lists.ListID AS ID, lists.ListName AS Name, count(*) as Games');
        $this->db->from('collections');
        $this->db->join('lists', 'collections.ListID = lists.ListID');
        $this->db->where('collections.UserID', $userID); 
        $this->db->group_by("lists.ListID");
        $this->db->order_by("Games", "desc"); 

        // get results
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $lists = $query->result();

            foreach ($lists as $list)
            {  
                $list->Selected = true;
            }

            return $lists;
        }

        return null;
    }

    // get statuses in collection
    function getStatusesInCollection($userID)
    {
        $this->db->select('gameStatuses.StatusID AS ID, gameStatuses.StatusName AS Name, count(*) as Games');
        $this->db->from('collections');
        $this->db->join('gameStatuses', 'collections.StatusID = gameStatuses.StatusID');
        $this->db->where('collections.UserID', $userID); 
        $this->db->group_by("gameStatuses.StatusID");
        $this->db->order_by("Games", "desc"); 
        
        // get results
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $statuses = $query->result();

            foreach ($statuses as $status)
            {  
                $status->Selected = true;
            }

            return $statuses;
        }

        return null;
    }

    // get game that need updating
    function getGameToUpdate()
    {
        $this->db->select('GBID');
        $this->db->from('games'); 
        $this->db->where('(Error IS NULL AND LastUpdated < \'' . Date('Y-m-d', strtotime("-1 days")) . '\')'); 
        $this->db->or_where('LastUpdated', null); 
        $this->db->order_by("LastUpdated", "asc"); 
        $this->db->limit(1, 0);
        $query = $this->db->get();

        if($query->num_rows() == 1)
        {
            return $query->first_row()->GBID;
        }

        return null;
    }

    // update game cache
    function updateGame($game)
    {
        $releaseDate = $this->getReleaseDate($game);

        $data = array(
           'Name' => $game->name,
           'Image' => is_object($game->image) ? $game->image->small_url : null,
           'ImageSmall' => is_object($game->image) ? $game->image->icon_url : null,
           'Deck' => $game->deck,
           'ReleaseDate' => $releaseDate,
           'LastUpdated' => date('Y-m-d')
        );

        $this->db->where('GBID', $game->id);
        $this->db->update('games', $data); 
    }

    // failed to get response from GB API, save error in db
    function saveError($GBID, $error)
    {
        $data = array(
           'Error' => $error
        );

        $this->db->where('GBID', $GBID);
        $this->db->update('games', $data); 
    }

    // get users collection by platform
    function getUsersWhoHaveGame($gbID, $userID) 
    {
        $this->db->select('users.UserID');
        $this->db->select('UserName');
        $this->db->select('ProfileImage');
        $this->db->select('StatusNameShort');
        $this->db->select('StatusStyle');

        $this->db->from('games');
        $this->db->join('collections', 'games.GameID = collections.GameID');
        $this->db->join('users', 'collections.UserID = users.UserID');
        $this->db->join('gameStatuses', 'gameStatuses.StatusID = collections.StatusID');

        if($userID != null) 
        {
            $this->db->join('following', 'following.ChildUserID = collections.UserID AND following.ParentUserID = ' . $userID, 'left');
            $this->db->where('users.UserID !=', $userID); // if logged in, exclude yourself
        }

        $this->db->where('games.GBID', $gbID); 

        $this->db->order_by("Ranking", "asc"); 
        
        // if logged in, bump users you follow up the list
        if($userID != null)
            $this->db->order_by('following.ParentUserID', 'desc'); 

        // get results
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $users = $query->result();

            foreach ($users as $user)
            {  
                // default profile image
                $user->ProfileImage = $user->ProfileImage == null ? $this->config->item('default_profile_image') : $user->ProfileImage;
            }

            return $users;
        }

        return null;
    }
}