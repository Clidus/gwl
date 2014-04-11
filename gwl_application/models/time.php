<?php 

class Time extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // return date and time in format selected by user
    function GetDateTimeInFormat($datestamp, $format)
    {
        switch($format)
        {
            // Swatch Internet Time
            case 1:
                return date_format(date_create($datestamp), 'jS F, Y') . ' @' . date("B", human_to_unix($datestamp)) . ' .beats';
            // Time since
            case 2:
                return timespan(human_to_unix($datestamp), time()) . ' ago';
            // Database
            case 3:
                return $datestamp;
            // English
            case 4:
                return date_format(date_create($datestamp), 'jS F, Y, g:i a');
            // American
            case 5:
                return date_format(date_create($datestamp), 'F jS, Y, g:i a');
            
        }
    }
}