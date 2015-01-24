<?php

/*
|--------------------------------------------------------------------------
| Ignition v0.3.1 ignitionpowered.co.uk
|--------------------------------------------------------------------------
|
| This class is a core part of Ignition. It is advised that you extend
| this class rather than modifying it, unless you wish to contribute
| to the project.
|
*/

class IG_Auth extends CI_Controller {
	
	// register user
	function register()
	{
		$this->load->helper(array('form'));
		$this->load->library('form_validation');

		// form validation
		$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|matches[conpassword]');
		$this->form_validation->set_rules('conpassword', 'Confirm Password', 'trim|required');
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '<a class="close" data-dismiss="alert" href="#">&times;</a></div>');

		// page variables 
		$this->load->model('Page');
		$data = $this->Page->create("Register", "Register");
		$data['errorMessage'] = '';

		// validation failed
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('templates/header', $data);
			$this->load->view('register');
			$this->load->view('templates/footer', $data);
		}
		// validation success
		else
		{
			// register user
			$this->load->model('User');
			if($this->User->register($this->input->post('email'), $this->input->post('username'), $this->input->post('password'))) {
				// success, send to homepage
				header("location: " . base_url());
			} else {
				// failed, return error
				$data['errorMessage'] = $this->User->errorMessage;
				$this->load->view('templates/header', $data);
				$this->load->view('register', $data);
				$this->load->view('templates/footer', $data);
			}
		}
	}

	// login user
	function login()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');

		// form validation
		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '<a class="close" data-dismiss="alert" href="#">&times;</a></div>');

		// page variables 
		$this->load->model('Page');
		$data = $this->Page->create("Login", "Login");
		$data['errorMessage'] = '';

		// validation failed
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('templates/header', $data);
			$this->load->view('login');
			$this->load->view('templates/footer', $data);
		}
		// validation success
		else
		{
			// register user
			$this->load->model('User');
			if($this->User->login($this->input->post('username'), $this->input->post('password'))) {
				// success, send to homepage
				header("location: " . base_url());
			} else {
				// failed, return error
				$data['errorMessage'] = "Sorry duder, that seems to be the wrong username or password. Please try again.";
				$this->load->view('templates/header', $data);
				$this->load->view('login', $data);
				$this->load->view('templates/footer', $data);
			}
		}
	}

	// logout user
	function logout()
	{
		// logout user
		$this->load->model('User');
		$this->User->logout();
		header("location: " . base_url());
	}	
}