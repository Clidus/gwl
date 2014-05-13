<?php

class Cron extends CI_Controller {
	
	// run in cron job to update game cache
	public function update()
	{
		// get game to update
		$this->load->model('Game');
        $GBID = $this->Game->getGameToUpdate();
        
        // if game returned
        if($GBID != null) {
        	// get game details from Giant Bomb API
	        $game = $this->Game->getGameByID($GBID, null);

	        // if game returned from API
	        if($game != null) {
		        // update game cache
		        $this->Game->updateGame($game);
			}
        }
	}
}