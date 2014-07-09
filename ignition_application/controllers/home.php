<?php

class Home extends CI_Controller {
	
	// view static page
	public function view($page = 1)
	{
		$this->load->model('Page');
		$this->load->model('User');

		// if user is logged in
		if($this->session->userdata('UserID') != null) {
			// page variables
			$data = $this->Page->create("Home", "UserHome");

			// paging
	        $resultsPerPage = 20;
	        $offset = ($page-1) * $resultsPerPage;

			// feed of users you follow
			$data['events'] = $this->User->getUserEvents(null, null, $this->session->userdata('UserID'), $this->session->userdata('DateTimeFormat'), $offset, $resultsPerPage);
			$data['pageNumber'] = $page;
		} else {
			// page variables
			$data = $this->Page->create("Home", "Home");

			// feed of all site activity
			$data['events'] = $this->User->getUserEvents(null, null, null, $this->session->userdata('DateTimeFormat'), 0, 30);
		}

		// load views
		$this->load->view('templates/header', $data);

		// load different homepage if user is logged in
		if($this->session->userdata('UserID') != null) {
			$this->load->view('user/home/header', $data);
			$this->load->view('control/events', $data);
			$this->load->view('user/home/footer', $data);
		} else {
			$this->load->view('home', $data);	
		}

		$this->load->view('templates/footer', $data);
	}
}