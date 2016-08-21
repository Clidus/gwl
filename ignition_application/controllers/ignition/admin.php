<?php

/*
|--------------------------------------------------------------------------
| Ignition v0.5.0 ignitionpowered.co.uk
|--------------------------------------------------------------------------
|
| This class is a core part of Ignition. It is advised that you extend
| this class rather than modifying it, unless you wish to contribute
| to the project.
|
*/

class IG_Admin extends CI_Controller {
	
	// admin home
	public function home()
	{
		// restricted page
		if($this->session->userdata('Admin') != 1)
			show_404();

		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("Admin", "Admin");

		// get blog posts
		$this->load->model('Blog');
		$data['posts'] = $this->Blog->getPosts(4, 0, true); // get 100 most recent posts

		// load views
		$this->load->view('templates/header', $data);
		$this->load->view('admin/admin.php', $data);
		$this->load->view('templates/footer', $data);
	}
}