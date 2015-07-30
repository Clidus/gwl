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

class IG_Pages extends CI_Controller {
	
	// view static page
	public function view($page = 'home')
	{
		if (!file_exists('ignition_application/views/pages/'.$page.'.php'))
		{
			// Whoops, we don't have a page for that!
			show_404();
		}

		// page variables
		$this->load->model('Page');
        $data = $this->Page->create(ucfirst($page), "Page"); // Capitalize the first letter
       
		// load views
		$this->load->view('templates/header', $data);
		$this->load->view('pages/'.$page, $data);
		$this->load->view('templates/footer', $data);
	}
}