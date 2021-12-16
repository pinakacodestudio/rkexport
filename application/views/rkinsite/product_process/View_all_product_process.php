<div class="page-content">
    <div class="page-heading">            
        <h1>View All Process Details</h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active">View All Process Details</li>
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
                            <h2><b>Process Group : <?=$productprocessdata['processes'][0]['processgroup']?></b></h2>
                        </div>
                        <div class="col-md-6 p-n text-right">
                            <a class="<?=back_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=back_title?>><?=back_text?></a>
                            <?php if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printallprocessdetail(<?=$productprocessdata['processes'][0]['processgroupid']?>,<?=$productprocessdata['processes'][0]['productprocessid']?>,'all')" title=<?=printbtn_title?>><?=printbtn_text?></a>
                            <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0);" title="<?=exportpdfbtn_title?>" onclick="exporttopdfallprocessdetail(<?=$productprocessdata['processes'][0]['processgroupid']?>,<?=$productprocessdata['processes'][0]['productprocessid']?>,'all')"><?=exportpdfbtn_text;?></a>	
                            <?php } ?>			
                        </div>
                    </div>
                </div>
                <?php $this->load->view(ADMINFOLDER.'product_process/Allproductprocessdetails');?>
            </div>
        </div>
    </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->