<div class="page-content">
    <div class="page-heading">            
        <h1>View Process Details</h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active">View Process Details</li>
            </ol>
        </small>
    </div>
    <div class="container-fluid">
                                    
    <div data-widget-group="group1">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default border-panel">
                    <div class="panel-heading">
                        <div class="col-md-6 p-n">
                            <h2>Product Process Detail</h2>
                        </div>
                        <div class="col-md-6 p-n text-right">
                            <a class="<?=back_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=back_title?>><?=back_text?></a>
                            <?php if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printprocessdetail(<?=$productprocessdata['id']?>)" title=<?=printbtn_title?>><?=printbtn_text?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel-body no-padding">
                        <?php $this->load->view(ADMINFOLDER.'product_process/Productprocessdetails');?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->