<div class="page-content">
    <div class="page-heading">            
        <h1>View <?=$this->session->userdata(base_url().'submenuname');?></h1>
        <small>
          	<ol class="breadcrumb">                        
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?php echo $this->session->userdata(base_url().'mainmenuname'); ?></a></li>
            <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?php echo $this->session->userdata(base_url().'submenuname'); ?></a></li>
            <li class="active">View <?php echo $this->session->userdata(base_url().'submenuname'); ?></li>
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
						    <?php $this->load->view(ADMINFOLDER.'order/Orderviewformat');?>
					   </div>
             <?php
              if(count($installment)>0){
              ?>
                 <div class="col-md-12">
                    <h3>Installment</h3>
                    <div class="panel">
                        <div class="panel-body no-padding">
                            <div class="">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">Percentage</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Payment Date</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($installment as $ins) {
                                        ?>
                                          <tr align="center">
                                            <td><?=$ins['amount']?></td>
                                            <td><?=$ins['percentage']?></td>
                                            <td><?php 
                                            if($ins['date']!="0000-00-00"){
                                              echo $this->general_model->displaydate($ins['date']);
                                            }else{ echo "-"; }
                                            ?></td>
                                            <td><?php 
                                            if($ins['paymentdate']!="0000-00-00"){ echo $this->general_model->displaydate($ins['paymentdate']); }else{ echo "-"; } ?></td>
                                              <td>
                                                <?php
                                                if($ins['status']==1){
                                                  $btncls="btn-success";
                                                  $btntxt="Paid";
                                                }else{
                                                  $btncls="btn-warning";
                                                  $btntxt="Pending";
                                                }
                                                ?>
                                                <div class="dropdown">
                                                  <button class="btn <?=$btncls?> <?=STATUS_DROPDOWN_BTN?> btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown<?=$ins['id'];?>"><?=$btntxt?> <span class="caret"></span></button>
                                                  <ul class="dropdown-menu" role="menu">
                                                    <li id="dropdown-menu">
                                                      <a onclick="changeinstallmentstatus(0,<?=$ins['id']?>)">Pending</a>
                                                    </li>
                                                    <li id="dropdown-menu">
                                                      <a onclick="changeinstallmentstatus(1,<?=$ins['id']?>)">Paid</a>
                                                    </li>
                                                  </ul>
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
				    </div>
		      </div>
		    </div>
		  </div>
		</div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->