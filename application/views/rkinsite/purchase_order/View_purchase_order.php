<div class="page-content">
    <div class="page-heading">            
        <h1>View Purchase <?=$this->session->userdata(base_url().'submenuname');?></h1>
        <small>
          	<ol class="breadcrumb">                        
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?php echo $this->session->userdata(base_url().'mainmenuname'); ?></a></li>
            <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?php echo $this->session->userdata(base_url().'submenuname'); ?></a></li>
            <li class="active">View Purchase <?php echo $this->session->userdata(base_url().'submenuname'); ?></li>
          	</ol>
        </small>
    </div>

    <div class="container-fluid">        
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default">
		        <div class="panel-body">
		        	<div class="col-md-12">
                <div class="row">
                  <div class="col-md-12 text-right">
                    <a class="<?=back_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=back_title?>><?=back_text?></a>
                    <?php if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                    <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printorderinvoice(<?=$transactiondata['transactiondetail']['id']?>)" title=<?=printbtn_title?>><?=printbtn_text?></a>
                    <?php } ?>
                  </div>
                </div>
                <?php $this->load->view(ADMINFOLDER.'purchase_order/Purchaseorderviewformat');?>
            </div>
             <?php
              if(count($installment)>0){
              ?>
                 <div class="col-md-12">
                    <div class="panel border-panel mb-xl">
                        <div class="panel-heading">
                          <h2>Installment</h2>
                        </div>
                        <div class="panel-body no-padding">
                            <div class="">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="width8">Sr.No.</th>
                                            <th class="text-right">Amount</th>
                                            <th class="text-right">Percentage</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Payment Date</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                         $count=0;
                                        foreach ($installment as $ins) {
                                        ?>
                                          <tr>
                                            <td><?=++$count?></td>
                                            <td class="text-right"><?=number_format($ins['amount'],2,'.',',')?></td>
                                            <td class="text-right"><?=number_format($ins['percentage'],2,'.','')?></td>
                                            <td class="text-center"><?php  
                                            if($ins['date']!="0000-00-00"){
                                              echo $this->general_model->displaydate($ins['date']);
                                            }else{ echo "-"; }
                                            ?></td>
                                            <td class="text-center"><?php 
                                            if($ins['paymentdate']!="0000-00-00"){ echo $this->general_model->displaydate($ins['paymentdate']); }else{ echo "-"; } ?></td>
                                              <td class="text-center"
                                                <?php
                                                if($ins['status']==1){
                                                  $btncls="btn-success";
                                                  $btntxt="Paid";
                                                  $spancaret="";
                                                }else{
                                                  $btncls="btn-warning";
                                                  $btntxt="Pending";
                                                  $spancaret="<span class='caret'></span>";
                                                }
                                                ?>
                                                <div class="dropdown">
                                                  <button class="btn <?=$btncls?> <?=STATUS_DROPDOWN_BTN?> btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown<?=$ins['id'];?>"><?=$btntxt?><?=$spancaret?></button>
                                                  
                                                  <?php if($ins['status']==0){ ?>
                                                    <ul class="dropdown-menu" role="menu">
                                                      <li id="dropdown-menu">
                                                      <a onclick="changeinstallmentstatus(1,<?=$ins['id']?>)">Paid</a>
                                                      </li>
                                                    </ul>
                                                  <?php } ?>
                                                     
                                                </div>
                                                </td>
                                          </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
              <?php } ?>
              <?php
              if(count($transactionattachment)>0){
              ?>
                 <div class="col-md-12">
                    <div class="panel border-panel mb-xl">
                        <div class="panel-heading">
                          <h2>Order Documents</h2>
                        </div>
                        <div class="panel-body no-padding">
                            <div class="">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="width8">Sr. No.</th>
                                            <th>File</th>
                                            <th>Remarks</th>
                                            <th class="text-center">Entry Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                         $count=0;
                                        foreach ($transactionattachment as $document) {
                                        ?>
                                          <tr>
                                            <td><?=++$count?></td>
                                            <td><a href="<?=TRANSACTION_ATTACHMENT.$document['filename']?>" class="<?=downloadlblbtn_class?>" title="<?=downloadlblbtn_title?>" download><?=downloadlblbtn_text?></a></td>
                                            <td><?=ucfirst($document['remarks'])?></td>
                                            <td class="text-center"><?=date("d/m/Y h:i A", strtotime($document['createddate']))?></td>
                                          </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
              <?php } ?>
              <?php
              if(!empty($orderstatushistory) && count($orderstatushistory)>0){
              ?>
                <div class="col-md-12">
                    <div class="panel border-panel">
                        <div class="panel-heading">
                          <h2>Status History</h2>
                        </div>
                        <div class="panel-body no-padding">
                            <div class="">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="width8">Sr. No.</th>
                                            <th>Modified By</th>
                                            <th>Date</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $count=0;
                                        foreach ($orderstatushistory as $sh) {
                                        ?>
                                          <tr>
                                            <td><?=++$count?></td>
                                            <td><?='<span class="label" style="background:#49bf88;">COMPANY</span>'?></td>
                                            <td><?=date("d/m/Y h:i A", strtotime($sh['modifieddate']))?></td>
                                            <td class="text-center">
                                              <?php
                                                   if($sh['status']==0){
                                                    echo "<span class='label label-warning'>Pending</span>";
                                                  }else if($sh['status']==1){
                                                    echo "<span class='label label-success'>Completed</span>";
                                                  }else if($sh['status']==2){
                                                    echo "<span class='label label-danger'>Canceled</span>";
                                                  }
                                              ?>
                                            </td>
                                          </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
              <?php } ?> 
				    </div>
		      </div>
		    </div>
		  </div>
		</div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->