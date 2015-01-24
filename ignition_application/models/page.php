<?php 

/*
|--------------------------------------------------------------------------
| Ignition ignitionpowered.co.uk
|--------------------------------------------------------------------------
|
| This class extends the functionality of Ignition. You can add your
| own custom logic here.
|
*/

require_once APPPATH.'/models/ignition/page.php';

class Page extends IG_Page {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
}