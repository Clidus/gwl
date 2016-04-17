<?php 

class GiantBomb extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // get game from Giant Bomb API
    public function getGame($GBID) 
    {   
        // build API request
        $url = $this->config->item('gb_api_root') . "/game/" . $GBID . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";
        
        // make API request
        $result = $this->Utility->getData($url, "Game");
        
        if(is_object($result))
        {
            return $result;
        } else {
            return null;
        }
    }

    // search Giant Bomb API for games  
    function searchForGame($query, $page, $resultsPerPage, $userID) 
    {  
        // build API request
        $url = $this->config->item('gb_api_root') . "/search/?api_key=" . $this->config->item('gb_api_key') . "&format=json&resources=game&limit=" . $resultsPerPage . "&page=" . $page . "&query=" . urlencode ($query);
      
        // make API request
        $result = $this->Utility->getData($url, "Search");

        if(is_object($result) && $result->error == "OK" && $result->number_of_total_results > 0)
        {                       
			$this->load->model('Collection');                                                                             
            foreach($result->results as $game)
            {    
                $game = $this->Collection->addCollectionInfo($game, $userID);
            }
            return $result;
        } else {
            return null;
        }
    }

    function convertReleaseDate($game)
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
}