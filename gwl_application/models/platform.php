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
}