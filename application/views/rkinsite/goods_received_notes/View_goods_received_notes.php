<div class="page-content">
    <div class="page-heading">            
        <h1>View <?=$this->session->userdata(base_url().'submenuname');?></h1>
        <small>
          	<ol class="breadcrumb">                        
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?php echo $this->session->userdata(base_url().'mainmenuname'); ?></a></li>
            <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?php echo $this->session->userdata(base_url().'submenuname'); ?></a></li>
            <li class="active">View <?php echo $this->session->userdata(base_url().'submenuname'); ?></li>
          	</ol>
        </small>
    </div>

    <div class="container-fluid">        
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-md-12">
                        <div class="row">
                        <div class="col-md-12 text-right">
                            <a class="<?=back_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=back_title?>><?=back_text?></a>
                            <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printGoodsReceivedNotes(<?=$transactiondata['transactiondetail']['id']?>)" title=<?=printbtn_title?>><?=printbtn_text?></a>
                        </div>
                        </div>
                        <?php require_once(APPPATH."views/".ADMINFOLDER.'invoice/Transactionheader.php');?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel border-panel mb-xl">
                                    <div class="panel-body no-padding">
                                        <div class="table-responsive">
                                            <?php require_once(APPPATH."views/".ADMINFOLDER.'invoice/Transactionproductdetails.php');?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <?php require_once(APPPATH."views/".ADMINFOLDER.'invoice/Transactionsummarydetails.php');?>
                                <hr>
                            </div>
                        
                            <?php  if(!empty($transactiondata['transactiondetail']) && $transactiondata['transactiondetail']['remarks']!=''){ ?>
                            <div class="col-md-12">
                                <p><b>Remarks : </b><?=ucfirst($transactiondata['transactiondetail']['remarks']);?></p>
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