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
                        <?php if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                        <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printorderinvoice(<?=$transactiondata['transactiondetail']['id']?>)" title=<?=printbtn_title?>><?=printbtn_text?></a>
                        <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printorderinvoice(<?=$transactiondata['transactiondetail']['id']?>,1)" title=<?=printbtn_title?>><?=printbtn_text?> with out price</a>
                        <?php } ?>
                        <?php if(!empty($transactiondata['transactiondetail']['previousorderid'])){ ?>
                          <a class="<?=previuos_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')."/view-order/".$transactiondata['transactiondetail']['previousorderid']?>" title="View <?=previuos_title?> Order"><?=previuos_text?></a>
                        <?php }else{ ?>
                          <a class="<?=previuos_class;?>" href="#" title="View <?=previuos_title?> Order" disabled><?=previuos_text?></a>
                        <?php } ?>
                        <?php if(!empty($transactiondata['transactiondetail']['nextorderid'])){ ?>
                          <a class="<?=next_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')."/view-order/".$transactiondata['transactiondetail']['nextorderid']?>" title="View <?=next_title?> Order"><?=next_text?></a>
                        <?php }else{ ?>
                          <a class="<?=next_class;?>" href="#" title="View <?=next_title?> Order" disabled><?=next_text?></a>
                        <?php } ?>
                      </div>
                    </div>
                    <?php $this->load->view(ADMINFOLDER.'order/Orderviewformat');?>
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
                                                <td>
                                                  <?php 
                                                    $channellabel = '';
                                                    $key = array_search($sh['channelid'], array_column($channeldata, 'id'));
                                                    if($sh['channelid']!=0 && $sh['type']==1){
                                                      if(!empty($channeldata) && isset($channeldata[$key])){
                                                          echo '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> '; 
                                                        ?>
                                                          <a href="<?=ADMIN_URL.'member/member-detail/'.$sh['modifiedby']?>" title="<?=ucwords($sh['name'])?>"><?=ucwords($sh['name'])?></a>
                                                        <?php
                                                      }
                                                    }else{
                                                      echo '<span class="label" style="background:#49bf88;">COMPANY</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?=date("d/m/Y h:i A", strtotime($sh['modifieddate']))?></td>
                                                <td class="text-center">
                                                  <?php
                                                      if($sh['status']==0){
                                                        echo "<span class='label label-warning'>Pending</span>";
                                                      }else if($sh['status']==1){
                                                        echo "<span class='label label-success'>Completed</span>";
                                                      }else if($sh['status']==2){
                                                        echo "<span class='label label-danger'>Canceled</span>";
                                                      }else if($sh['status']==3){
                                                        echo "<span class='label label-success'>Partially Completed</span>";
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
                  <?php } 
                  if(!empty($orderfeedback)){ ?>
                    <div class="col-md-12">
                      <div class="panel border-panel">
                        <div class="panel-heading">
                            <h2>Feedback Details</h2>
                          </div>
                          <div class="panel-body no-padding">
                              <div class="">
                                  <table class="table table-hover table-borderaed">
                                      <thead>
                                          <tr>
                                              <th>Question</th>
                                              <th width="35%">Answer</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          <?php
                                          $count=0;
                                          foreach ($orderfeedback as $fk=>$feedback) { ?>
                                            <tr>
                                              <td><?=$feedback['question']?></td>
                                              <td>
                                                <div id="answer<?=($fk+1)?>" data-score="<?=$feedback['answer']?>">
                                                </div>
                                                <script>
                                                  $(document).ready(function(){
                                                    $("#answer<?=($fk+1)?>").raty({
                                                      hints:false,
                                                      halfShow : true,
                                                      readOnly: true,
                                                      score: function() {
                                                        return $(this).attr("data-score");
                                                      }
                                                    });
                                                  });
                                                </script>
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