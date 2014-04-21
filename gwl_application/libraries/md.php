<?php
 
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
 
set_include_path(APPPATH . 'third_party/' . PATH_SEPARATOR . get_include_path());
 
require_once APPPATH . 'third_party/markdown/MarkdownInterface.php';
require_once APPPATH . 'third_party/markdown/Markdown.php';
 
class md extends Michelf\Markdown {
    function __construct($params = array()) {
        parent::__construct();
    }
}