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

    // get platform data from Giant Bomb API
    function getPlatform($gbID, $returnCompleteResponse)
    {
        $url = $this->config->item('gb_api_root') . "/platform/" . $gbID . "?api_key=" . $this->config->item('gb_api_key') . "&format=json";
    
        // make API request
        $result = $this->Utility->getData($url);
        
        if(is_object($result))
        {
            return $returnCompleteResponse ? $result : $result->results;
        } else {
            return null;
        }
    }

    // get platform that need updating
    function getPlatformToUpdate()
    {
        $this->db->select('GBID');
        $this->db->from('platforms'); 
        $this->db->where('(Error IS NULL AND LastUpdated < \'' . Date('Y-m-d', strtotime("-1 days")) . '\')'); 
        $this->db->or_where('LastUpdated', null); 
        $this->db->order_by("LastUpdated", "asc"); 
        $this->db->limit(1, 0);
        $query = $this->db->get();

        if($query->num_rows() == 1)
        {
            return $query->first_row()->GBID;
        }

        return null;
    }

    // update platform cache
    function updatePlatform($platform)
    {
        $data = array(
           'Name' => $platform->name,
           'Abbreviation' => $platform->abbreviation,
           'Image' => is_object($platform->image) ? $platform->image->small_url : null,
           'ImageSmall' => is_object($platform->image) ? $platform->image->icon_url : null,
           'LastUpdated' => date('Y-m-d')
        );

        $this->db->where('GBID', $platform->id);
        $this->db->update('platforms', $data); 
    }

    // failed to get response from GB API, save error in db
    function saveError($GBID, $error)
    {
        $data = array(
           'Error' => $error
        );

        $this->db->where('GBID', $GBID);
        $this->db->update('platforms', $data); 
    }
}