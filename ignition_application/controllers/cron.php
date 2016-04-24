<?php

class Cron extends CI_Controller {
	
	// run in cron job to update game cache
	public function update()
	{
		// get game to update
		$GBID = $this->getGameToUpdate();

		// if game returned
		if($GBID != null) {
			// get game details from Giant Bomb API
			$this->load->model('GiantBomb');
			$result = $this->GiantBomb->getGame($GBID);

			// if game returned from API
			if(is_object($result))
			{
				// if game found, update db
				if($result->error == "OK" && $result->number_of_total_results > 0)
				{
					$this->load->model('Game');
					$this->Game->updateGame($result->results);
				}
				else
				{
					$this->saveError($GBID, $result->error);
				}
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
			if(is_object($result) && $result->error == "OK" && $result->number_of_total_results > 0)
        	{
				// loop through games
				foreach($result->results as $game)
				{
					echo $game->name . "<br />"; // debug
					
					// if game is in database
					$this->load->model('Game');
					if($this->Game->isGameInDB($game->id))
					{
						// update game
						$this->Game->updateGame($game);
					} else {
						// add game
						$this->Game->addGame($game);
					}
				}
			}
		}
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
}