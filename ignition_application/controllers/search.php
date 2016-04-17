<?php

class Search extends CI_Controller {
	
	// search page
	function index($query = '', $page = 1)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('query', 'Query', 'trim|xss_clean');

		// page variables
		$this->load->model('Page');
        $data = $this->Page->create("Search", "Search");
		$data['searchQuery'] = $query = $query == '' ? $this->input->post('query') : str_replace("%20", " ", $query);
		$data['searchPage'] = $page;

		// search for game
		if($query != '') {
			$this->load->model('GiantBomb');
			$resultsPerPage = 10;
			$result = $this->GiantBomb->searchForGame($query, $page, $resultsPerPage, $this->session->userdata('UserID'));
			$data['searchResults'] = $result;
		}

		// load views
		$this->load->view('templates/header', $data);
		$this->load->view('search', $data);
		$this->load->view('templates/footer', $data);
	}
}
?>