<?php

class Games extends CI_Controller {
    
    // add game
	function add()
	{
		// form validation
		$this->load->library('form_validation');
		$this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');
		$this->form_validation->set_rules('apiDetail', 'apiDetail', 'trim|xss_clean');
        $this->form_validation->set_rules('listID', 'listID', 'trim|xss_clean');

		$GBID = $this->input->post('gbID');
		$API_Detail = $this->input->post('apiDetail');
        $listID = $this->input->post('listID');
		$userID = $this->session->userdata('userID');

		$this->load->model('Game');
		// if game isnt in db
    	if(!$this->Game->isGameInDB($GBID))
    	{
    		// add game to db
    		if(!$this->Game->addGame($API_Detail)) 
    		{
    			$result['error'] = true; 
    			$result['errorMessage'] = "Failed to add game to database. Please try again."; 
    			echo json_encode($result);
    			return;
    		}
    	}

        // check if game is in collection
        $collection = $this->Game->isGameIsInCollection($GBID, $userID);
        $currentListID = $collection != null ? $collection->ListID : 0;
       
        // if game isnt in collection
        if($currentListID == 0) 
        {
    		// add game to users collection
    		if(!$this->Game->addToCollection($GBID, $userID, $listID)) 
    		{
    			$result['error'] = true; 
    			$result['errorMessage'] = "Failed to add game to collection. Please try again."; 
    			echo json_encode($result);
    			return;
    		}
        // game is in collection, update list
        } else {
            $this->Game->updateList($GBID, $userID, $listID);
        }

		$result['error'] = false;   
		echo json_encode($result);
	}

    // change played status of game
    function changeStatus()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');
        $this->form_validation->set_rules('statusID', 'statusID', 'trim|xss_clean');

        $GBID = $this->input->post('gbID');
        $statusID = $this->input->post('statusID');
        $userID = $this->session->userdata('userID');

        $this->load->model('Game');
       
        // check if game is in collection
        $collection = $this->Game->isGameIsInCollection($GBID, $userID);
       
        // if game is in collection
        if($collection != null) 
        {
            $this->Game->updateStatus($GBID, $userID, $statusID);
        } else {
            $result['error'] = true; 
            $result['errorMessage'] = "You haven't added this game to your collection. How did you get here?"; 
            echo json_encode($result);
            return;
        }

        $result['error'] = false;   
        echo json_encode($result);
    }

    // view game
    function view($gbID)
    {   
        // lookup game
        $this->load->model('Game');
        $game = $this->Game->getGameByID($gbID, $this->session->userdata('userID'));

        if($game == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($game->name);
        $data['game'] = $game;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('games', $data);
        $this->load->view('templates/footer', $data);
    }
}
?>