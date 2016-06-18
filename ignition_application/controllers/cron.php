<?php

class Cron extends CI_Controller {
	
	// run in cron job to update and add games
	public function update()
	{
		// get offset
		$crawlerName = "Game";
		$crawlerOffset = $this->getCrawlerOffset($crawlerName);
		$gamesPerPage = 100;
		
		// if offset returned
		if($crawlerOffset != null) {
			// get games from Giant Bomb API (returns 100 games at a time, paged with an offset)
			$this->load->model('GiantBomb');
			$result = $this->GiantBomb->getGames($crawlerOffset);

			// if games returned from API
			if(is_object($result))
			{
				// if games found
				if($result->error == "OK" && $result->number_of_page_results > 0)
				{
					// increase offset for next request
					// the games returned will be processed from the API log
					$crawlerOffset = $crawlerOffset + $gamesPerPage;
					
					$this->setCrawlerOffset($crawlerName, $crawlerOffset);
				}
				else
				{
					// nothing returned, reset offset to zero
					$crawlerOffset = 0;
					
					$this->setCrawlerOffset($crawlerName, $crawlerOffset);
				}
					
				echo "Next offset: " . $crawlerOffset;
			}
		}
	}
	
	// run in cron job to process API log
	public function process()
	{
		// get game to update
		$log = $this->getAPILogToProcess();

		// if log was returned
		if($log != null) {
			// decode json
			$result = json_decode($log->Result);
			
			// check json has valid results
			if(is_object($result) && $result->error == "OK" && $result->number_of_page_results > 0)
        	{
				echo "<ul>";
				
				// loop through games
				$this->load->model('Game');
				foreach($result->results as $game)
				{
					// if game is in database
					if($this->Game->isGameInDB($game->id))
					{
						// update game
						$this->Game->updateGame($game);
						echo "<li>Updated " . $game->name . "</li>";
					} else {
						// add game
						$this->Game->addGame($game);
						echo "<li>Added " . $game->name . "</li>";
					}
					
					// destroy game
					$this->Game->destroy();
				}
				
				echo "</ul>";
			} else {
				echo "Nothing to process.";
			}
			
			// process log
			$this->processAPILog($log->LogID);
		} else {
			echo "Nothing to process.";
		}
	}

    // get crawler offset
    function getCrawlerOffset($crawlerName)
    {
        $this->db->select($crawlerName . "CrawlerOffset");
        $this->db->from('settings'); 
        $query = $this->db->get();

        if($query->num_rows() == 1)
        {
			if($crawlerName == "Game")
            	return $query->first_row()->GameCrawlerOffset;
			else if($crawlerName == "Release")
				return $query->first_row()->ReleaseCrawlerOffset;
        }

        return null;
    }
	
	function setCrawlerOffset($crawlerName, $offset)
    {
        $data = array(
           $crawlerName . "CrawlerOffset" => $offset
        );

        $this->db->update('settings', $data); 
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
	
	// get API log to process
    function getAPILogToProcess()
    {
        $this->db->select('LogID, Result');
        $this->db->from('apiLog');
        $this->db->where('Result IS NOT NULL AND Processed = 0');
        $this->db->order_by("DateStamp", "asc");
        $this->db->limit(1, 0);
        $query = $this->db->get();

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }
	
	// failed to get response from GB API, save error in db
    function processAPILog($logID)
    {
        $data = array(
           'Processed' => 1,
           'Result' => null
        );

        $this->db->where('LogID', $logID);
        $this->db->update('apiLog', $data); 
    }
}