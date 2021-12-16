<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Facebook_login extends Member_Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->library('facebook');
    }

    public function index() {

        
    }
}

?>