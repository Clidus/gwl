<?php


class Games extends CI_Controller {
    
    public function __construct(){ 
      parent::__construct();
    }
    
    // view game
    function view($GBID, $page = 1)
    {   
        $userID = $this->session->userdata('UserID');

        // lookup game
        $this->load->model('Game');
        if(!$this->Game->getGame($GBID, $userID, false))
            show_404();

        // paging
        $resultsPerPage = 20;
        $offset = ($page-1) * $resultsPerPage;

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($this->Game->name, "Game");
        $data['game'] = $this->Game;

        // get event feed
        $this->load->model('Event');
        $data['events'] = $this->Event->getEvents(null, $GBID, null, $this->session->userdata('DateTimeFormat'), $offset, $resultsPerPage);
        $data['pageNumber'] = $page;

        // get users who have game
        $this->load->model('Collection');
        $data['users'] = $this->Collection->getUsersWhoHavePlayedGame($GBID, $userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('games/header', $data);
        $this->load->view('control/events', $data);
        $this->load->view('games/footer', $data);
        $this->load->view('templates/footer', $data);
    }

    function returnError($errorMessage,$errorProgressURL,$errorProgressCTA)
    {
        $result['error'] = true; 
        $result['errorMessage'] = $errorMessage;
        $result['errorProgressURL'] = $errorProgressURL; 
        $result['errorProgressCTA'] = $errorProgressCTA; 
        echo json_encode($result);
    }

    // add game
	function add()
	{
		// form validation
		$this->load->library('form_validation');
		$this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('listID', 'listID', 'trim|xss_clean');

		$GBID = $this->input->post('GBID');
        $listID = $this->input->post('listID');
		$userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game id in database
        $this->load->model('Game');
        if(!$this->Game->getGame($GBID, null))
        {
            // failed to find game or add it to database
            $this->returnError($this->lang->line('error_game_cant_add'),false,false);
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);
        
        // default value for auto selected platform
        $result['autoSelectPlatform'] = null;

        // load event model
        $this->load->model('Event');

        // if game isnt in collection
        if($collection == null) 
        {
            // add game to users collection
            $collectionID = $this->Collection->addToCollection($this->Game->gameID, $userID, $listID);

            // get platforms for game
            if($this->Game->getPlatforms($userID))
            {
                // if game has one platform
                if($this->Game->platforms != null && count($this->Game->platforms) == 1)
                {
                    // add game to platform in collection
                    if($this->Collection->addPlatform($collectionID, $this->Game->platforms[0]->GBID))
                    {
                        // tell UI to check platform that was auto-selected
                        $result['autoSelectPlatform'] = $this->Game->platforms[0]->GBID; 
                    }
                }
            }
            
            // record event
            $this->Event->addEvent($userID, $this->Game->gameID, $listID, null, null);
        // game is in collection, update list
        } else {
            $this->Collection->updateList($GBID, $userID, $listID);

            // record event
            $this->Event->addEvent($userID, $collection->GameID, $listID, null, null);
        }

        // get list name and style
        $listData = $this->Collection->getListDetails($listID);
        $result['listName'] = $listData->ListName;
        $result['listStyle'] = $listData->ListStyle;

        // return success
        $result['error'] = false;   
        echo json_encode($result);
	}

    // change played status of game
    function changeStatus()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('statusID', 'statusID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $statusID = $this->input->post('statusID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);
       
        // if game is in collection
        if($collection != null) 
        {
            // update played status
            $this->Collection->updateStatus($GBID, $userID, $statusID);
        } else {
            // return error
            $this->returnError($this->lang->line('error_game_not_added'), false, false);
            return;
        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, null, $statusID, null);

        // get status name and style
        $statusData = $this->Collection->getStatusDetails($statusID);
        $result['statusName'] = $statusData->StatusName;
        $result['statusStyle'] = $statusData->StatusStyle;

        // return success
        $result['error'] = false;   
        echo json_encode($result);
    }

    // remove game from collection
    function remove()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $userID = $this->session->userdata('UserID');
        
        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // load game model
        $this->load->model('Game');

        // remove game from collection
        $this->Game->removeFromCollection($GBID, $userID);
       
        // return success
        $result['error'] = false;  
        echo json_encode($result);
    }

    function addPlatform()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBPlatformID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError("You haven't added this game to your collection. You probably need to do that first kido.", false, false);
            return;
        }
        
        // if game is not on platform, add it
        if(!$this->Collection->isGameOnPlatformInCollection($collection->ID, $GBPlatformID))
        {
            // load platform model
            $this->load->model('Platform');

            // if platform isnt in db
            if(!$this->Platform->isPlatformInDB($GBPlatformID))
            {
                // get platform data 
                $platform = $this->Platform->getPlatform($GBPlatformID);

                // if API returned nothing
                if($platform == null)
                {
                    $this->returnError($this->lang->line('error_giantbomb_down'), false, false);
                    return;
                }

                // add platform to db
                $this->Platform->addPlatform($platform);
            }

            // add game to platform in collection
            $this->Collection->addPlatform($collection->ID, $GBPlatformID);
        }

        // record event
        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, $collection->ListID, null, null);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

    function removePlatform()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $GBPlatformID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError($this->lang->line('error_game_not_added'), false, false);
            return;
        }
        
        // remove platform from game in collection
        $this->Collection->removePlatform($collection->ID, $GBPlatformID);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

    // change played status of game
    function saveProgression()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('GBID', 'GBID', 'trim|xss_clean');
        $this->form_validation->set_rules('currentlyPlaying', 'currentlyPlaying', 'trim|xss_clean');
        $this->form_validation->set_rules('hoursPlayed', 'hoursPlayed', 'trim|xss_clean');
        $this->form_validation->set_rules('dateCompleted', 'dateCompleted', 'trim|xss_clean');

        $GBID = $this->input->post('GBID');
        $currentlyPlaying = $this->input->post('currentlyPlaying');
        $hoursPlayed = $this->input->post('hoursPlayed');
        $dateCompleted = $this->input->post('dateCompleted');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check if game is in collection
        $this->load->model('Collection');
        $collection = $this->Collection->isGameIsInCollection($GBID, $userID);
       
        // if game is in collection
        if($collection != null) 
        {
            // update played status
            $this->Collection->updateProgression($collection->ID, $currentlyPlaying, $hoursPlayed, $dateCompleted);
        } else {
            // return error
            $this->returnError($this->lang->line('error_game_not_added'),false,false);
            return;
        }

        $this->load->model('Event');
        $this->Event->addEvent($userID, $collection->GameID, null, null, $currentlyPlaying);
       
        // return success
        $result['error'] = false;   
        echo json_encode($result);
    }
}
?>