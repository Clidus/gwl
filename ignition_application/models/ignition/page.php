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

class IG_Page extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    // create data for page view
    function create($pageTitle, $pageTemplate)
    {
        $data['pagetitle'] = $pageTitle;
        $data['pagetemplate'] = $pageTemplate;
        $data['sessionUserID'] = $this->session->userdata('UserID');
        $data['sessionUsername'] = $this->session->userdata('Username');
        $data['sessionAdmin'] = $this->session->userdata('Admin');
        $data['sessionProfileImage'] = $this->session->userdata('ProfileImage') == null ? $this->config->item('default_profile_image') : $this->session->userdata('ProfileImage');
        $data['metaTags'] = null;

        return $data; 
    }

    // create meta tags for blog
    function getBlogMetaTags($post)
    {
        return '<meta name="description" content="' . $post->Deck . '" />
        <link rel="author" href="https://plus.google.com/+' . $post->GooglePlus . '"/>
        <link rel="publisher" href="https://plus.google.com/b/116486371787813419091/+Gamingwithlemons"/>
        <meta itemprop="name" content="' . $post->Title . '"> 
        <meta itemprop="description" content="' . $post->Deck . '"> 
        <meta itemprop="image" content="' . $post->Image . '">
        <meta property="og:title" content="' . $post->Title . '" /> 
        <meta property="og:type" content="article" /> 
        <meta property="og:url" content="' . base_url() . 'blog/' . $post->URL . '" />
        <meta property="og:image" content="' . base_url() . $post->Image . '" />
        <meta property="og:description" content="' . $post->Deck . '" /> 
        <meta property="og:site_name" content="Gaming with Lemons" /> ';
    }
}