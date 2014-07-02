<?php

class Home extends CI_Controller {
	
	// view static page
	public function view()
	{
		// page variables
		$this->load->model('Page');
		$data = $this->Page->create("Home", "Home"); // Capitalize the first letter

		// get recent user activity
		$this->load->model('User');
		$data['events'] = $this->User->getUserEvents(null, null, $this->session->userdata('DateTimeFormat'), 0, 30);

		// load views
		$this->load->view('templates/header', $data);
		$this->load->view('home', $data);
		$this->load->view('templates/footer', $data);
	}
}