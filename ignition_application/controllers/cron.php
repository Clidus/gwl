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
			$gbResult = $this->Game->getGameByID($GBID, null, true);

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

	// run in cron job to update platforms cache
	public function updatePlatforms()
	{
		// get game to update
		$this->load->model('Platform');
		$GBID = $this->Platform->getPlatformToUpdate();

		// if platform returned
		if($GBID != null) {
			// get platform details from Giant Bomb API
			$gbResult = $this->Platform->getPlatform($GBID, true);

			// if platform returned from API
			if(is_object($gbResult))
			{
				// if platform found, update db
				if($gbResult->error == "OK" && $gbResult->number_of_total_results > 0)
					$this->Platform->updatePlatform($gbResult->results);
				else
					$this->Platform->saveError($GBID, $gbResult->error);
			}
		}
	}
}