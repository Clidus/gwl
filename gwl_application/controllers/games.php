<?php


class Games extends CI_Controller {
    
    public function __construct(){ 
      parent::__construct();
      $this->lang->load('errors');
    }
    
    // view game
    function view($gbID, $page = 1)
    {   
        // lookup game
        $this->load->model('Game');
        $game = $this->Game->getGameByID($gbID, $this->session->userdata('UserID'));

        if($game == null)
            show_404();

        // paging
        $resultsPerPage = 20;
        $offset = ($page-1) * $resultsPerPage;

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($game->name, "Game");
        $data['game'] = $game;

        // get event feed
        $this->load->model('User');
        $data['events'] = $this->User->getUserEvents(null, $gbID, $this->session->userdata('DateTimeFormat'), $offset, $resultsPerPage);
        $data['pageNumber'] = $page;

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
		$this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');
        $this->form_validation->set_rules('listID', 'listID', 'trim|xss_clean');

		$GBID = $this->input->post('gbID');
        $listID = $this->input->post('listID');
		$userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // load game model
        $this->load->model('Game');

        // get game details from Giant Bomb API
        $game = $this->Game->getGameByID($GBID, null);

        // if API returned nothing
        if($game == null)
        {
            $this->returnError($this->lang->line('error_giantbomb_down'),false,false);
            return;
        }

        // if game isnt in db
        if(!$this->Game->isGameInDB($GBID))
        {
            // add game to db
            if(!$this->Game->addGame($game))
            {
                // insert failed
                $this->returnError($this->lang->line('error_game_cant_add'),false,false);
                return;
            }
        }

        // check if game is in collection
        $collection = $this->Game->isGameIsInCollection($GBID, $userID);
        
        // default value for auto selected platform
        $result['autoSelectPlatform'] = null;

        // load user model
        $this->load->model('User');

        // if game isnt in collection
        if($collection == null) 
        {
            // add game to users collection
            $gameID = $this->Game->getGameID($GBID);
            $collectionID = $this->Game->addToCollection($gameID, $userID, $listID);

            // if game has one platform, automaticly add it
            if($collectionID != null && count($game->platforms) == 1)
            {
                // load platform model
                $this->load->model('Platform');

                // get first (and only) platform
                $platform = $game->platforms[0];

                // if platform isnt in db
                if(!$this->Platform->isPlatformInDB($platform->id))
                {
                    // add platform to db
                    $this->Platform->addPlatform($platform);
                }

                // add game to platform in collection
                if($this->Game->addPlatform($collectionID, $platform->id))
                {
                    // tell UI to check platform that was auto-selected
                    $result['autoSelectPlatform'] = $platform->id; 
                }
            }

            // record event
            $this->User->addUserEvent($userID, $gameID, $listID, null, null);
        // game is in collection, update list
        } else {
            $this->Game->updateList($GBID, $userID, $listID);

            // record event
            $this->User->addUserEvent($userID, $collection->GameID, $listID, null, null);
        }

        // get list name and style
        $listData = $this->Game->getListDetails($listID);
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
        $this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');
        $this->form_validation->set_rules('statusID', 'statusID', 'trim|xss_clean');

        $GBID = $this->input->post('gbID');
        $statusID = $this->input->post('statusID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // load game model
        $this->load->model('Game');
       
        // check if game is in collection
        $collection = $this->Game->isGameIsInCollection($GBID, $userID);
       
        // if game is in collection
        if($collection != null) 
        {
            // update played status
            $this->Game->updateStatus($GBID, $userID, $statusID);
        } else {
            // return error
            $this->returnError("You haven't added this game to your collection. How did you get here?", false, false);
            return;
        }

        // record event
        $this->load->model('User');
        $this->User->addUserEvent($userID, $collection->GameID, null, $statusID, null);

        // get status name and style
        $statusData = $this->Game->getStatusDetails($statusID);
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
        $this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');

        $GBID = $this->input->post('gbID');
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
        $this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('gbID');
        $GBPlatformID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // load game model
        $this->load->model('Game');

        // check if game is in collection
        $collection = $this->Game->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError("You haven't added this game to your collection. You probably need to do that first kido.", false, false);
            return;
        }
        
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
            $this->Game->addPlatform($collection->ID, $GBPlatformID);
        }

        // record event
        $this->load->model('User');
        $this->User->addUserEvent($userID, $collection->GameID, $collection->ListID, null, null);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
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

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // load game model
        $this->load->model('Game');

        // check if game is in collection
        $collection = $this->Game->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError($this->lang->line('error_game_not_added'), false, false);
            return;
        }
        
        // remove platform from game in collection
        $this->Game->removePlatform($collection->ID, $GBPlatformID);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

    // change played status of game
    function saveProgression()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');
        $this->form_validation->set_rules('currentlyPlaying', 'currentlyPlaying', 'trim|xss_clean');
        $this->form_validation->set_rules('hoursPlayed', 'hoursPlayed', 'trim|xss_clean');
        $this->form_validation->set_rules('dateCompleted', 'dateCompleted', 'trim|xss_clean');

        $GBID = $this->input->post('gbID');
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

        // load game model
        $this->load->model('Game');
       
        // check if game is in collection
        $collection = $this->Game->isGameIsInCollection($GBID, $userID);
       
        // if game is in collection
        if($collection != null) 
        {
            // update played status
            $this->Game->updateProgression($collection->ID, $currentlyPlaying, $hoursPlayed, $dateCompleted);
        } else {
            // return error
            $this->returnError($this->lang->line('error_game_not_added'),false,false);
            return;
        }

        $this->load->model('User');
        $this->User->addUserEvent($userID, $collection->GameID, null, null, $currentlyPlaying);
       
        // return success
        $result['error'] = false;   
        echo json_encode($result);
    }
}
?>