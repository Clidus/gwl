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
			$gbResult = $this->Game->getGameByGBID($GBID, null, true);

			// if game returned from API
			if(is_object($gbResult))
			{
				// if game found, update db
				if($gbResult->error == "OK" && $gbResult->number_of_total_results > 0)
					$this->Game->updateGame($gbResult->results);
				else
					$this->Game->saveError($GBID, $gbResult->error);
			}
		}
	}
}