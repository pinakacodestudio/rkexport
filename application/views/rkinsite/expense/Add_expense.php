<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($expense_data)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Home</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($expense_data)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12">
					<form class="form-horizontal" id="expenseform" name="expenseform">
						<input type="hidden" name="expenseid" value="<?php if(isset($expense_data)){ echo $expense_data['id']; } ?>">
						<div class="col-md-12 p-n">
							<div class="col-md-6 p-n">
								<div class="form-group" id="employeeid_div">
									<label for="name" class="col-md-4 control-label">Employee <span class="mandatoryfield">*</span></label>
									<div class="col-md-8">
										<select class="selectpicker form-control" id="employeeid" name="employeeid"  data-size="5" data-select-on-tab="true" data-live-search="true">
											<option value="0">Select Employee</option>
											<?php foreach($userdata as $_v){?>
												<option value="<?php echo $_v['id']; ?>" <?php if(isset($expense_data)){ if($expense_data['employeeid'] == $_v['id']){ echo 'selected'; } } ?>><?php echo $_v['username']; ?></option>
											<?php  } ?>                                        																		
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6 p-n">
								<div class="form-group" id="expensecategory_div">
									<label class="col-md-4 control-label" for="expensecategoy">Category <span class="mandatoryfield">*</span></label>
									<div class="col-md-8">
										<select class="selectpicker form-control" id="expensecategory" name="expensecategory"  data-size="5" data-select-on-tab="true" data-live-search="true">
										<option value="0">Select Category</option>
										<?php foreach($expensecategory as $ec){?>
											<?php print_r($ec);?>
											<option value="<?php echo $ec['id']; ?>">
											<?php echo $ec['expense_type']; ?></option>
										<?php } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 p-n">
							<div class="col-md-6 p-n">
								<div class="form-group" id="date_div">
									<label for="date" class="col-md-4 control-label">Date <span class="mandatoryfield">*</span></label>
									<div class="col-md-8">
										<input id="date" name="date"  type="text" class="form-control col-sm-6" value="<?php if(isset($expense_data)){ echo $this->general_model->displaydate($expense_data['date']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" readonly>
									</div>
								</div>
							</div>
							<div class="col-md-6 p-n">
								<div class="form-group" id="amount_div">
									<label for="focusedinput" class="col-md-4 control-label">Amount <span class="mandatoryfield">*</span></label>
									<div class="col-md-8">
                                    	<input type="text" id="amount" name="amount" value="<?php if(!empty($expense_data)){ echo $expense_data['amount']; } ?>" onkeypress="return decimal(event,this.id);" class="form-control  " >
									</div>
								</div>
								
							</div>
						</div>
						<div class="col-md-12 p-n">
							<div class="col-md-6 p-n">
								<div class="form-group" id="remarks_div">
									<label for="contactperson" class="col-md-4 control-label">Remark <span class="mandatoryfield">*</span></label>
									<div class="col-md-8">
                                        <textarea class="form-control" id="remarks" name="remarks" rows="3"><?php if(isset($expense_data)){ echo $expense_data['remarks']; } ?></textarea>
									</div>
								</div>
							</div>
							<div class="col-md-6 p-n" >
								<div class="form-group" id="reason_div">
									<label for="mobileno" class="col-md-4 control-label">Reason <span class="mandatoryfield">*</span></label>
									<div class="col-md-8">
                                        <textarea class="form-control" id="reason" name="reason" rows="3" ><?php if(isset($expense_data)){ echo $expense_data['reason']; } ?></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 p-n">
							<div class="col-md-6 p-n">
								<div class="form-group" id="receipt_div">
									<label class="col-md-4 control-label" for="receipt">Receipt</label>
									<div class="col-md-8" >
										<input type="hidden" name="oldreceipt" id="oldreceipt" value="<?php if(isset($expense_data)){ echo $expense_data['receipt'];} ?>">
										<input type="hidden" name="removeoldreceipt" id="removeoldreceipt" value="0">
										<div class="input-group" id="fileupload1">
											<span class="input-group-btn" style="padding: 0 0px 0px 0px;">
												<span class="btn btn-primary btn-raised btn-sm btn-file">Browse...
												<input type="file" name="receipt"  id="receipt" onchange="validreceiptfile($(this),'receipt')" accept=".docx,.pdf,.jpeg,.jpg,.png">
												</span>
											</span>                                        
											<input type="text" id="receipttext" class="form-control" name="receipttext" value="<?php if(isset($expense_data)){ echo $expense_data['receipt'];}?>" readonly >
										</div>
									</div>
								</div>
							
							</div>
							
						</div>	
						
						<div class="col-md-12 p-n" style="text-align: center;">
							<div class="form-group">
								<?php if(!empty($expense_data)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>expense" title=<?=cancellink_title?>><?=cancellink_text?></a>
							</div>
						</div>	
					</form>
				</div>
				</div>
		      </div>
		    </div>
		  </div>
		</div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->

