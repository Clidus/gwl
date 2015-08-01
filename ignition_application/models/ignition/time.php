<?php 

/*
|--------------------------------------------------------------------------
| Ignition v0.4.0 ignitionpowered.co.uk
|--------------------------------------------------------------------------
|
| This class is a core part of Ignition. It is advised that you extend
| this class rather than modifying it, unless you wish to contribute
| to the project.
|
*/

class IG_Time extends CI_Model {

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
            case null:
            case 1:
                return date_format(date_create($datestamp), 'jS F, Y') . ' @' . date("B", human_to_unix($datestamp)) . ' .beats';
            // Unix time
            case 2:
                return human_to_unix($datestamp);
            // Time since
            case 3:
                return timespan(human_to_unix($datestamp), time()) . ' ago';
            // Database
            case 4:
                return $datestamp;
            // English
            case 5:
                return date_format(date_create($datestamp), 'jS F, Y, g:i a');
            // American
            case 6:
                return date_format(date_create($datestamp), 'F jS, Y, g:i a');
            
        }
    }
}