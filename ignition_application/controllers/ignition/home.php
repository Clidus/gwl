<?php

/*
|--------------------------------------------------------------------------
| Ignition v0.4.0 ignitionpowered.co.uk
|--------------------------------------------------------------------------
|
| This class is a core part of Ignition. It is advised that you extend
| this class rather than modifying it, unless you wish to contribute
| to the project.
|
*/

class IG_Home extends CI_Controller {

	// homepage
	public function view()
	{
		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("Home", "Home");

		// load views
		$this->load->view('templates/header', $data);
		$this->load->view('home', $data);	
		$this->load->view('templates/footer', $data);
	}
}