<script type="text/javascript">
  var followupid=<?php echo $followupid;?>
</script>
<style>
  #map {
    height: 400px;
    width: 100%;  
    margin-left: 0px;
  }
  .rate
  {
      font-size: 20px;
  }
  .rate .rate-hover-layer
  {
      color: orange;
  }
  .rate .rate-select-layer
  {
      color: orange;
  }
</style>
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
            <div class="panel panel-default border-panel">
              <div class="panel-body no-padding pt-1">
                <div class="tab-container tab-default mb-n">
                  <ul class="nav nav-tabs " id="myTab" > 
                      <li class="dropdown pull-right tabdrop hide active">
                          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-angle-down"></i> </a><ul class="dropdown-menu"></ul>
                      </li>
                      <li class="active">
                          <a href="#followupdetails" data-toggle="tab" aria-expanded="false"><?=Follow_Up?> Details<div class="ripple-container li-line"></div></a>
                      </li>
                      <li class="">
                          <a href="#routedetail" data-toggle="tab" aria-expanded="false">Route Detail<div class="ripple-container li-line"></div></a>
                      </li>
                      <li class="">
                          <a href="#memberdetail" data-toggle="tab" aria-expanded="false"><?=Member_label?> Detail<div class="ripple-container li-line"></div></a>
                      </li> 
                      <li class="">
                          <a href="#contactdetail" data-toggle="tab" aria-expanded="false">Contact Detail<div class="ripple-container li-line"></div></a>
                      </li>  
                      <li class="">
                          <a href="#inquirydetail" data-toggle="tab" aria-expanded="false"><?=Inquiry?> Detail<div class="ripple-container li-line"></div></a>
                      </li>
                      <?php if(count($followuptransferdata)>0){ ?>
                      <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#transferhistory" role="tab" aria-controls="transferhistory">Transfer History</a>
                      </li>
                      <?php } ?>
                  </ul>
                  <div class="tab-content">  
                    <div class="tab-pane  active" id="followupdetails">
                        <div class="row">
                          <?php $this->load->view(ADMINFOLDER.'daily_followup/detailpages/Followup_detail.php');?>                     
                        </div>                                         
                    </div>                  
                    <div class="tab-pane" id="routedetail">
                          <?php $this->load->view(ADMINFOLDER.'daily_followup/detailpages/Route_detail.php');?>
                    </div>
                    <div class="tab-pane" id="memberdetail">
                        <div class="row">
                          <?php $this->load->view(ADMINFOLDER.'daily_followup/detailpages/Member_detail.php');?>                                                                                                    
                        </div>
                    </div>
                    <div class="tab-pane" id="contactdetail">
                        <div class="row">
                          <?php $this->load->view(ADMINFOLDER.'daily_followup/detailpages/Contact_detail.php');?>                                                                                        
                        </div>
                    </div>                 
                    <div class="tab-pane" id="inquirydetail">
                      <div class="row">
                        <?php $this->load->view(ADMINFOLDER.'daily_followup/detailpages/Inquiry_detail.php');?>        
                      </div>
                    </div>
                    <?php if(count($followuptransferdata)>0){ ?>
                      <div class="tab-pane" id="transferhistory" role="tabpanel">
                        <div class="row">
                          <?php $this->load->view(ADMINFOLDER.'daily_followup/detailpages/Transfer_history_detail.php');?>  
                        </div>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>        
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->
<script type="text/javascript">
$(document).ready(function() {
  var options = {
                max_value: 5,
                step_size: 0.5,
                initial_value: "<?php if(!empty($followupdata)){ echo $followupdata['rating']; } ?>",
                readonly: true,
            }
  $("#rate").rate(options);
});
</script>