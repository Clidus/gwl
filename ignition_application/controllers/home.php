<?php

class Home extends CI_Controller {
	
	// view static page
	public function view()
	{
		// page variables
		$this->load->model('Page');

		// if user is logged in
		if($this->session->userdata('UserID') != null)
			$data = $this->Page->create("Home", "UserHome");
		else
			$data = $this->Page->create("Home", "Home");

		// get recent user activity
		$this->load->model('User');
		$data['events'] = $this->User->getUserEvents(null, null, $this->session->userdata('UserID'), $this->session->userdata('DateTimeFormat'), 0, 30);

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