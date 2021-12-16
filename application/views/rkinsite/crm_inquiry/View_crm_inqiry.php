<style>
  .dataTables_length .panel-ctrls-center, .dataTables_filter{
    padding: 0px 8px;
  }
  .rate{
      font-size: 20px;
  }
  .rate .rate-hover-layer {
      color: orange;
  }
  .rate .rate-select-layer {
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
            <li><a href="<?=ADMIN_URL?>crm-inquiry"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
            <li class="active">View <?=$this->session->userdata(base_url().'submenuname')?> Detail</li>
          </ol>
        </small>                
    </div>
    <div class="container-fluid">
      <div data-widget-group="group1">
        <div class="row">   
          
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-body p-n pt-1">
                <div class="tab-container tab-default mb-n">
                  <ul class="nav nav-tabs " id="myTab" > 
                      <li class="dropdown pull-right tabdrop hide active">
                          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-angle-down"></i> </a><ul class="dropdown-menu"></ul>
                      </li>
                      <li class="active">
                          <a href="#inquirydetail" data-toggle="tab" aria-expanded="false"><?=Inquiry?> Detail<div class="ripple-container li-line"></div></a>
                      </li>
                      <li class="">
                          <a href="#memberdetail" data-toggle="tab" aria-expanded="false"><?=Member_label?> Detail<div class="ripple-container li-line"></div></a>
                      </li> 
                      <li class="">
                          <a href="#contactdetail" data-toggle="tab" aria-expanded="false">Contact Detail<div class="ripple-container li-line"></div></a>
                      </li>
                      <?php if(count($followupdata)>0){ ?>
                      <li class="" >
                          <a href="#followup" data-toggle="tab" aria-expanded="false">Follow Up <div class="ripple-container li-line"></div></a>
                      </li>
                      <?php } ?>
                      <?php if(count($inquirytransferdata)>0){ ?>
                      <li class="" >
                          <a href="#transferhistory" data-toggle="tab" aria-expanded="false">Transfer History <div class="ripple-container li-line"></div></a>
                      </li>  
                      <?php } ?>
                      <li class="" >
                          <a href="#inquiryquotationfile" data-toggle="tab" aria-expanded="false">Quotation Files <div class="ripple-container li-line"></div></a>
                      </li>  
                  </ul>
                  
                  <div class="tab-content">  
                      <div class="tab-pane active" id="inquirydetail">
                          <div class="row">
                            <?php $this->load->view(ADMINFOLDER.'crm_inquiry/detailpages/Inquiry_detail.php');?>                                                                                                    
                          </div>
                      </div>
                      <div class="tab-pane" id="memberdetail">
                          <div class="row">
                            <?php $this->load->view(ADMINFOLDER.'crm_inquiry/detailpages/Member_detail.php');?>                                                                                                    
                          </div>
                      </div>
                      <div class="tab-pane" id="contactdetail">
                          <div class="row">
                            <?php $this->load->view(ADMINFOLDER.'crm_inquiry/detailpages/Contact_detail.php');?>                                                                                                    
                          </div>
                      </div>    
                      <?php if(count($followupdata)>0){ ?>   
                      <div class="tab-pane" id="followup">
                          <div class="row">
                            <?php $this->load->view(ADMINFOLDER.'crm_inquiry/detailpages/Followup.php');?>                     
                          </div>                                         
                      </div> 
                      <?php } ?>
                      <?php if(count($inquirytransferdata)>0) { ?> 
                      <div class="tab-pane" id="transferhistory">
                          <div class="row">
                            <?php $this->load->view(ADMINFOLDER.'crm_inquiry/detailpages/Transfer_history.php');?>
                          </div>                                         
                      </div> 
                      <?php } ?>
                      <div class="tab-pane" id="inquiryquotationfile">
                          <div class="row">
                            <?php $this->load->view(ADMINFOLDER.'crm_inquiry/detailpages/Inquiry_quotation_detail.php');?>
                          </div>
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

<script type="text/javascript">
$(document).ready(function() {
  
  var options = {
      max_value: 5,
      step_size: 0.5,
      initial_value: "<?php if(!empty($memberdata)){ echo $memberdata['rating']; } ?>",
      readonly: true,
  }
  $("#rate").rate(options);
});
</script>