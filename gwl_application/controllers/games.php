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
		$userID = $this->session->userdata('UserID');

        // load game model
        $this->load->model('Game');

        // get game details from Giant Bomb API
        $apiResult = $this->Game->getGame($API_Detail);
        if($apiResult->error == "OK" && $apiResult->number_of_total_results == 1) 
        {
            $game = $apiResult->results;

            // if game isnt in db
            if(!$this->Game->isGameInDB($GBID))
            {
                // add game to db
                $this->Game->addGame($game);
            }

            // check if game is in collection
            $collection = $this->Game->isGameIsInCollection($GBID, $userID);
            $currentListID = $collection != null ? $collection->ListID : 0;
            
            // default value for auto selected platform
            $result['autoSelectPlatform'] = null;

            // if game isnt in collection
            if($currentListID == 0) 
            {
                // add game to users collection
                $collectionID = $this->Game->addToCollection($GBID, $userID, $listID);

                // if game has one platform, automaticly add it
                if(count($game->platforms) == 1)
                {
                    // load platform model
                    $this->load->model('Platform');

                    // if platform isnt in db
                    $platform = $game->platforms[0];
                    if(!$this->Platform->isPlatformInDB($platform->id))
                    {
                        // add platform to db
                        $this->Platform->addPlatform($platform);
                    }

                    // add game to platform in collection
                    $this->Game->addPlatform($collectionID, $platform->id);
                    // tell UI to check platform that was auto-selected
                    $result['autoSelectPlatform'] = $platform->id; 
                }
            // game is in collection, update list
            } else {
                $this->Game->updateList($GBID, $userID, $listID);
            }

            $result['error'] = false;   
            echo json_encode($result);
        } else {
            $result['error'] = true; 
            $result['errorMessage'] = "Failed to add game to database. Please try again."; 
            echo json_encode($result);
            return;
        }
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
        $userID = $this->session->userdata('UserID');

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

    function remove()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');

        $GBID = $this->input->post('gbID');
        $userID = $this->session->userdata('UserID');
       
        $this->load->model('Game');
        $this->Game->removeFromCollection($GBID, $userID);
       
        $result['error'] = false;  
        echo json_encode($result);
    }

    function addPlatform()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('gbID');
        $GBPlatformID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // load game model
        $this->load->model('Game');

        // check if game is in collection
        $collection = $this->Game->isGameIsInCollection($GBID, $userID);

        // if game is in collection
        if($collection != null)
        {
            // if game is not on platform, add it
            if(!$this->Game->isGameOnPlatformInCollection($collection->ID, $GBPlatformID))
            {
                // load platform model
                $this->load->model('Platform');

                // if platform isnt in db
                if(!$this->Platform->isPlatformInDB($GBPlatformID))
                {
                    // get platform data 
                    $platform = $this->Platform->getPlatform($GBPlatformID);

                    // add platform to db
                    $this->Platform->addPlatform($platform);
                }

                // add game to platform in collection
                $this->Game->addPlatform($collection->ID, $GBPlatformID);
            }
            
            $result['error'] = false; 
            echo json_encode($result);
            return;
        } else {
            $result['error'] = true; 
            $result['errorMessage'] = "You haven't added this game to your collection. You probably need to do that first kido."; 
            echo json_encode($result);
            return;
        }
    }

    function removePlatform()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('gbID');
        $GBPlatformID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // load game model
        $this->load->model('Game');

        // check if game is in collection
        $collection = $this->Game->isGameIsInCollection($GBID, $userID);

        // if game is in collection
        if($collection != null)
        {
            // remove platform from game in collection
            $this->Game->removePlatform($collection->ID, $GBPlatformID);
            
            $result['error'] = false; 
            echo json_encode($result);
            return;
        } else {
            $result['error'] = true; 
            $result['errorMessage'] = "You haven't added this game to your collection. You probably need to do that first kido."; 
            echo json_encode($result);
            return;
        }
    }

    // view game
    function view($gbID)
    {   
        // lookup game
        $this->load->model('Game');
        $game = $this->Game->getGameByID($gbID, $this->session->userdata('UserID'));

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