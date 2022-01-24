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
		    <!-- <div class="col-md-12">
		      <div class="panel panel-default">
		        <div class="panel-body">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a class="<?=back_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=back_title?>><?=back_text?></a>
                            <?php if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printquotationinvoice(<?=$transactiondata['transactiondetail']['id']?>)" title=<?=printbtn_title?>><?=printbtn_text?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php require_once(APPPATH."views/".ADMINFOLDER.'quotation/Quotationviewformat.php');?>
                </div>
                </div>
		      </div>
		    </div> -->
            <div class="col-md-12">
                <div class="panel panel-default border-panel">
                    <div class="panel-body p-n pt-1">
                        <div class="tab-container tab-default mb-n">
                            <ul class="nav nav-tabs " id="myTab" >
                                <li class="dropdown pull-right tabdrop hide active">
                                    <a class="dropdown-toggle" data-toggle="" href="#"><i class="fa fa-angle-down"></i> </a><ul class="dropdown-menu"></ul>
                                </li>
                                <li class="active">
                                    <a href="#quotationdetails" data-toggle="tab" aria-expanded="false">Quotation Details<div class="ripple-container li-line"></div></a>
                                </li>
                                <li class="">
                                    <a href="#companydetails" data-toggle="tab" aria-expanded="false">Company Details<div class="ripple-container li-line"></div></a>
                                </li>
                                <li class="">
                                    <a href="#contactpersondetails" data-toggle="tab" aria-expanded="false">Contact Person Details<div class="ripple-container li-line"></div></a>
                                </li>
                                <li class="">
                                    <a href="#documents" data-toggle="tab" aria-expanded="false">Documents<div class="ripple-container li-line"></div></a>
                                </li>
                                <!-- <li class="">
                                    <a href="#order" data-toggle="tab" aria-expanded="false">Order<div class="ripple-container li-line"></div></a>
                                </li>
                                <li class="">
                                    <a href="#invoice" data-toggle="tab" aria-expanded="false">Invoice<div class="ripple-container li-line"></div></a>
                                </li> -->
                            </ul>
                            <div class="tab-content">  
                                <div class="tab-pane active" id="quotationdetails">
                                    <div class="row">
                                    <?php
                                        if(count($installment)>0){ ?>
                                        <div class="col-md-12">
                                            <div class="panel border-panel mb-xl">
                                                <div class="panel-body no-padding">
                                                    <div class="">
                                                        <table class="table table-hover table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th class="width8">Sr. No.</th> 
                                                                    <th class="text-left">Category</th>
                                                                    <th class="text-left">Product</th>
                                                                    <th class="text-left">Qty</th>
                                                                    <th class="text-left">Discount</th>
                                                                    <th class="text-left">Price</th>
                                                                    <th class="text-left">Amount</th>
                                                                    <th class="text-left">Delievery</th>
                                                                    <th class="text-left">Notes</th>
                                                                </tr>
                                                            </thead>
                                                            <!-- <tbody>
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
                                                                    if($ins['paymentdate']!="0000-00-00"){
                                                                        echo $this->general_model->displaydate($ins['paymentdate']); }else{ echo "-"; } ?></td>
                                                                        <td class="text-center">
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
                                                                            <button class="btn <?=$btncls?> <?=STATUS_DROPDOWN_BTN?> btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown<?=$ins['id'];?>"><?=$btntxt?> <?=$spancaret?></button>
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
                                                            </tbody> -->
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="tab-pane" id="companydetails">
                                    <?php //foreach($partycontectdata as $contect){ ?>
                                        <div class="row ">
                                            <div class="col-md-6 p-n">
                                                <table class="table table-striped table-bordered table-responsive-sm" width="100%">
                                                    <tr>
                                                        <th width="35%">Company Name</th>
                                                        <td><?php //echo $contect['firstname']." ".$contect['lastname']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>website Name</th>
                                                        <td><?//=($contect['email']!=""? $contect['email']:"-" )?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>GST Number</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th width="35%">Pan Number</th>
                                                        <td><?php //echo $contect['contactno']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Party Code</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th>City</th>
                                                        <td></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6 p-n">
                                                <table class="table table-striped table-bordered table-responsive-sm" width="100%">
                                                    <tr>
                                                        <th width="35%">State</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Country</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Billing Address</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Shipping Address</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Courier Address</th>
                                                        <td></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    <?php //} ?>
                                </div>
                                <div class="tab-pane" id="contactpersondetails">
                                    <?php //foreach($partycontectdata as $contect){ ?>
                                        <div class="row ">
                                            <div class="col-md-6 p-n">
                                                <table class="table table-striped table-bordered table-responsive-sm" width="100%">
                                                    <tr>
                                                        <th width="35%"> Name</th>
                                                        <td><?php //echo $contect['firstname']." ".$contect['lastname']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Email</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Anniversary Date</th>
                                                        <td></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6 p-n">
                                                <table class="table table-striped table-bordered table-responsive-sm" width="100%">
                                                    <tr>
                                                        <th width="35%">Mobile Number</th>
                                                        <td><?php //echo $contect['contactno']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Birth Date</th>
                                                        <td></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    <?php //} ?>
                                </div>
                                <div class="tab-pane" id="documents">
                                    <div class="row">
                                    <?php
                                        if(count($installment)>0){ ?>
                                        <div class="col-md-12">
                                            <div class="panel border-panel mb-xl">
                                                <div class="panel-body no-padding">
                                                    <div class="">
                                                        <table class="table table-hover table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th class="width8">Sr. No.</th> 
                                                                    <th class="text-right">Name</th>
                                                                    <th class="text-right">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <!-- <tbody>
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
                                                                    if($ins['paymentdate']!="0000-00-00"){
                                                                        echo $this->general_model->displaydate($ins['paymentdate']); }else{ echo "-"; } ?></td>
                                                                        <td class="text-center">
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
                                                                            <button class="btn <?=$btncls?> <?=STATUS_DROPDOWN_BTN?> btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown<?=$ins['id'];?>"><?=$btntxt?> <?=$spancaret?></button>
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
                                                            </tbody> -->
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
                </div>
            </div>
		  </div>
		</div>
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->





<!-- Exceptional code -->

                    <!-- <?php
                    if(!empty($quotationstatushistory) && count($quotationstatushistory)>0){
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
                                                foreach ($quotationstatushistory as $qh) {
                                                ?>
                                                <tr>
                                                    <td><?=++$count?></td>
                                                    <td>
                                                    <?php 
                                                        $channellabel = '';
                                                        $key = array_search($qh['channelid'], array_column($channeldata, 'id'));
                                                        if($qh['channelid']!=0 && $qh['type']==1){
                                                        if(!empty($channeldata) && isset($channeldata[$key])){
                                                            echo '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> '; 
                                                            ?>
                                                            <a href="<?=ADMIN_URL.'member/member-detail/'.$qh['modifiedby']?>" title="<?=ucwords($qh['name'])?>"><?=ucwords($qh['name'])?></a>
                                                            <?php
                                                        }
                                                        }else{
                                                            echo '<span class="label" style="background:#49bf88;">COMPANY</span>';
                                                        }
                                                    ?>
                                                    </td>
                                                    <td><?=date("d/m/Y h:i A", strtotime($qh['modifieddate']))?></td>
                                                    <td class="text-center">
                                                    <?php
                                                        if($qh['status']==0){
                                                            echo "<span class='label label-warning'>Pending</span>";
                                                        }else if($qh['status']==1){
                                                            echo "<span class='label label-success'>Approved</span>";
                                                        }else if($qh['status']==2){
                                                            echo "<span class='label label-danger'>Rejected</span>";
                                                        }else if($qh['status']==3){
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
                    <?php } ?> -->