<script type="text/javascript">
  var followupid=<?php echo $followupid;?>
</script>
<div class="page-content">
    <div class="page-heading">    
      <div class="btn-group dropdown dropdown-l dropdown-breadcrumbs">
        <a class="dropdown-toggle dropdown-toggle-style" data-toggle="dropdown" aria-expanded="false"><span>
            <i class="material-icons" style="font-size: 26px;">menu</i>
          </span> </a>
        <ul class="dropdown-menu dropdown-tl" role="menu">
        <label class="mt-sm ml-sm mb-n">Menu</label>
          <?php
            $subid = $this->session->userdata(base_url().'submenuid');
            foreach($subnavtabsmenu as $row){
              if($subid == $row['id']){ ?>
                
                <li class="active"><a href="javascript:void(0);"><i class="fa fa-caret-right mr-sm"></i> <?=$row['name']; ?></a></li>
              
              <?php }else{ ?>
                <li><a href="<?=base_url().ADMINFOLDER.$row['url']; ?>"><i class="fa fa-caret-right mr-sm"></i> <?=$row['name']; ?></a></li>
              <?php } 
            } ?>
        </ul>
      </div>         
        <h1>View <?=$this->session->userdata(base_url().'submenuname')?> Detail</h1>    
        <small>
          <ol class="breadcrumb">                        
            <li><a href="<?=base_url(); ?><?=ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
            <li><a href="<?=ADMIN_URL?>daily-followup"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
            <li class="active">View <?=$this->session->userdata(base_url().'submenuname')?> Detail</li>
          </ol>
        </small>                
    </div>
    <div class="container-fluid">
      <div data-widget-group="group1">
        <div class="row">   
          
          <div class="col-md-12">
            <div class="tab-container tab-default">
              <ul class="nav nav-tabs " id="myTab" > 
                  <li class="dropdown pull-right tabdrop hide active">
                      <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-angle-down"></i> </a><ul class="dropdown-menu"></ul>
                  </li>
                  <li class="" >
                      <a href="#followupdetails  " data-toggle="tab" aria-expanded="false">Follow up Details<div class="ripple-container li-line"></div></a>
                  </li>
                  <li class="">
                      <a href="#routedetail" data-toggle="tab" aria-expanded="false">Route Detail<div class="ripple-container li-line"></div></a>
                  </li>
                  <li class="">
                      <a href="#customerdetail" data-toggle="tab" aria-expanded="false">Customer Detail<div class="ripple-container li-line"></div></a>
                  </li> 
                  <li class="">
                      <a href="#contactdetail" data-toggle="tab" aria-expanded="false">Contact Detail<div class="ripple-container li-line"></div></a>
                  </li>  
                  <li class="">
                      <a href="#inquierydetail" data-toggle="tab" aria-expanded="false">Inquiery Detail<div class="ripple-container li-line"></div></a>
                  </li>
                  <!--<li class="">
                      <a href="#ebooktab" data-toggle="tab" aria-expanded="false">E-Book<div class="ripple-container"></div></a>
                  </li>   -->           
              </ul>
              <div class="tab-content">  
                  <div class="tab-pane  active" id="followupdetails">
                      <div class="row">
                        <?php $this->load->view(ADMINFOLDER.'daily_followup/detailpages/Followup_detail.php');?>                     
                      </div>                                         
                  </div>                  
                  <div class="tab-pane" id="routedetail">
                      <div class="row">
                        <?php $this->load->view(ADMINFOLDER.'daily_followup/detailpages/Route_detail.php');?>                                                                                                    
                      </div>
                  </div>
                  <div class="tab-pane" id="customerdetail">
                      <div class="row">
                        <?php $this->load->view(ADMINFOLDER.'daily_followup/detailpages/Customer_detail.php');?>                                                                                                    
                      </div>
                  </div>
                  <div class="tab-pane" id="contactdetail">
                      <div class="row">
                        <?php $this->load->view(ADMINFOLDER.'daily_followup/detailpages/Contact_detail.php');?>                                                                                                    
                      </div>
                  </div>                 
                  <div class="tab-pane" id="inquierydetail">
                      <div class="row">
                        <?php $this->load->view(ADMINFOLDER.'daily_followup/detailpages/Inquiery_detail.php');?>                                                                                                    
                      </div>
                  </div>
                  </div>
              </div>
            </div>
          </div>
        </div>        
      </div>
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->