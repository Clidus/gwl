<?php

class Pages extends CI_Controller {
	
	// view static page
	public function view($page = 'home')
	{
		if ( ! file_exists('gwl_application/views/pages/'.$page.'.php'))
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