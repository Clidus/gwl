<?php 

class Platform extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // is platform in database?
    function isPlatformInDB($GBID)
    {
        $query = $this->db->get_where('platforms', array('GBID' => $GBID));

        return $query->num_rows() > 0 ? true : false;
    }

    // get PlatformID from GBID
    function getPlatformByGBID($GBID)
    {
        $query = $this->db->get_where('platforms', array('GBID' => $GBID));

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }

    // returns platform if in db, or adds and returns it if it isn't
    function getOrAddPlatform($gbPlatform)
    {
        // get platform from db
        $platform = $this->getPlatformByGBID($gbPlatform->id);

        // if platform isn't in db
        if($platform == null)
        {
            // add platform to db
            $this->Platform->addPlatform($gbPlatform);

            // get platform from db
            $platform = $this->getPlatformByGBID($gbPlatform->id);
        }

        return $platform;
    }

    // add platform to database
    function addPlatform($platform)
    {
        $data = array(
           'GBID' => $platform->id,
           'Name' => $platform->name,
           'Abbreviation' => $platform->abbreviation,
           'API_Detail' => $platform->api_detail_url
        );

        return $this->db->insert('platforms', $data); 
    }

    // get platform data from Giant Bomb API
    function getPlatform($gbID)
    {
        $url = $this->config->item('gb_api_root') . "/platform/" . $gbID . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";
    
        // make API request
        $result = $this->Utility->getData($url);
        
        if(is_object($result))
        {
            return $result->results;
        } else {
            return null;
        }
    }
}