<?php 

class Api_Session extends CI_Model {

    // add platform to database
    function createSessionToken($userID)
    {
        $token = $this->generateSessionToken($userID);

        $currentUTCDateTime = new DateTime(null, new DateTimeZone('UTC'));

        $data = array(
            'ApiSessionToken' => $token,
            'UserID' => $userID,
            'IpAddress' => $_SERVER['REMOTE_ADDR'],
            'UserAgent' => $_SERVER['HTTP_USER_AGENT'],
            'Date' => $currentUTCDateTime->format('Y-m-d'),
            'Time' => $currentUTCDateTime->format('H:i:s')
        );

        if($this->db->insert('api_sessions', $data))
        {
            return $token;
        } else {
            return null;
        }
    }

    private function generateSessionToken($userID)
    {
        // include userID within the prefix to make certain 
        // that two different users can't generate the same token
        // more_entropy = true to add add additional entropy to the token
        $token = uniqid('gwl_' . $userID, true);

        // check that there hasnt been a collision 
        if($this->getSession($token) != null)
        {
            // in the unlikely chance of a token collision, fail to login
            return null;
        }

        return $token;
    }

    private function getSession($token)
    {
        $query = $this->db->get_where('api_sessions', array('ApiSessionToken' => $token));

        if($query->num_rows() == 1)
        {
            return $query->first_row();
        }

        return null;
    }
}