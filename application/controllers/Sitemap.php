<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Sitemap extends My_controller {


    /**
     * Index Page for this controller.
     *
     */
    public function index()
    {
        $this->load->model("Sitemap_model","Sitemap");
        $this->viewData['sitemapdata'] = $this->Sitemap->getSitemapData();
    
        header("Content-Type: text/xml;charset=iso-8859-1");
        $this->load->view('Sitemap', $this->viewData);
    }
}