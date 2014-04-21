<?php 

class Page extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // create data for page view
    function create($pageTitle, $pageTemplate)
    {
        $data['baseUrl'] = base_url();
        $data['pagetitle'] = $pageTitle;
        $data['pagetemplate'] = $pageTemplate;
        $data['sessionUserID'] = $this->session->userdata('UserID');
        $data['sessionUsername'] = $this->session->userdata('Username');
        $data['sessionAdmin'] = $this->session->userdata('Admin');
        $data['sessionProfileImage'] = $this->session->userdata('ProfileImage');

        return $data; 
    }
}