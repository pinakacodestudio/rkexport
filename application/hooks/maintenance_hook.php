<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Check whether the site is offline or not.
 *
 */
class Maintenance_hook
{
  public $viewData = array();
    public function __construct(){
      
    }
    
    public function offline_check(){
      if(MAINTENANCE_MODE==TRUE){
        if (isset( $_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']!=MAINTENANCE_IPS){
          
          include(APPPATH.'views/maintenance.php');
          //$this->load->view('maintenance', $this->viewData);
          exit;
        }
        
      }
    }
}