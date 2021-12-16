
<style>
.nav-tabs > li {
    margin-bottom: 0px;
}
</style>
<div class="page-content">
	<div class="page-heading">     
        <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">

		    <form class="form-horizontal" id="paymentgatewayform">
			     <div class="tab-container tab-default m-n">
					<ul class="nav nav-tabs">
						<?php foreach($this->Paymentgatewaytype as $key=>$value){
						?>
							<li <?php if($key==1){ echo 'class="active"'; } ?>><a href="#gateway<?=$key?>" data-toggle="tab"><?php echo ucwords($value); ?></a></li>
						<?php
	        			} ?>
					</ul>
					<div class="tab-content">
						<?php if(isset($paymentgatewaydata) && !empty($paymentgatewaydata)){
		        		foreach($paymentgatewaydata as $paymentgateway){ ?>
							<?php 
							if($paymentgateway['paymentgatewayid'] == '1'){ ?>
							<div class="tab-pane active" id="gateway1">
								<div class="row">
								<div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-1 col-md-offset-1">
									<input type="hidden" name="paymentgatewayid" id="paymentgatewayid" value="<?php if(isset($paymentgateway['paymentgatewayid']) && !empty($paymentgateway['paymentgatewayid'])){ echo $paymentgateway['paymentgatewayid']; } ?>">
									<div class="form-group" id="merchantid_div">
										<label for="merchantid" class="col-sm-4 control-label">Merchant ID <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
											<input id="merchantid" type="text" name="merchantid" value="<?php if(isset($paymentgateway) && !empty($paymentgateway)){ echo $paymentgateway['merchantid']; } ?>" class="form-control" onkeypress="return isNumber(event)" >
										</div>
									</div>
									<div class="form-group" id="merchantkey_div">
										<label for="merchantkey" class="col-sm-4 control-label">Merchant Key <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
											<input id="merchantkey" type="text" name="merchantkey" value="<?php if(isset($paymentgateway) && !empty($paymentgateway)){ echo $paymentgateway['merchantkey']; } ?>" class="form-control" onkeypress="return alphanumeric(event)" maxlength="50" >
										</div>
									</div>
									
									<div class="form-group" id="merchantsalt_div">
										<label for="merchantsalt" class="col-sm-4 control-label">Merchant Salt <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
											<input id="merchantsalt" type="text" name="merchantsalt" value="<?php if(isset($paymentgateway) && !empty($paymentgateway)){ echo $paymentgateway['merchantsalt']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)" maxlength="50">
										</div>
									</div>
									
									<div class="form-group" id="authheader_div">
										<label for="authheader" class="col-sm-4 control-label">Auth Header <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
											<input id="authheader" type="text" name="authheader" value="<?php if(isset($paymentgateway) && !empty($paymentgateway)){ echo $paymentgateway['authheader']; } ?>" class="form-control" maxlength="150"  >
										</div>
									</div>

										<div class="form-group">
											<label for="focusedinput" class="col-md-4 control-label">Is Debug</label>
											<div class="col-md-8">
												<div class="col-md-3 col-xs-3" style="padding-left: 0px;">
													<div class="radio">
													<input type="radio" name="isdebug" id="yes" value="1" <?php if(isset($paymentgateway) && $paymentgateway['isdebug']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
													<label for="yes">Yes</label>
													</div>
												</div>
												<div class="col-md-3 col-xs-3">
													<div class="radio">
													<input type="radio" name="isdebug" id="no" value="0" <?php if(isset($paymentgateway) && $paymentgateway['isdebug']==0){ echo 'checked'; }?>>
													<label for="no">No</label>
													</div>
												</div>
											</div>
										</div>


										</div>
									</div>
							</div>
							<?php } 
							if($paymentgateway['paymentgatewayid'] == '2'){ ?>
								<div class="tab-pane" id="gateway2">
									<div class="row">
										<div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-1 col-md-offset-1">
												
												<div class="form-group" id="paytmmerchantid_div">
													<label for="paytmmerchantid" class="col-sm-4 control-label">Merchant ID <span class="mandatoryfield">*</span></label>
													<div class="col-sm-8">
														<input id="paytmmerchantid" type="text" name="paytmmerchantid" value="<?php if(isset($paymentgateway) && !empty($paymentgateway)){ echo $paymentgateway['merchantid']; } ?>" class="form-control" onkeypress="return isNumber(event)" >
													</div>
												</div>

												<div class="form-group" id="paytmmerchantkey_div">
													<label for="paytmmerchantkey" class="col-sm-4 control-label">Merchant Key <span class="mandatoryfield">*</span></label>
													<div class="col-sm-8">
														<input id="paytmmerchantkey" type="text" name="paytmmerchantkey" value="<?php if(isset($paymentgateway) && !empty($paymentgateway)){ echo $paymentgateway['merchantkey']; } ?>" class="form-control" onkeypress="return alphanumeric(event)" maxlength="50" >
													</div>
												</div>
												
												<div class="form-group" id="merchantwebsiteforweb_div">
													<label for="merchantwebsiteforweb" class="col-sm-4 control-label">Merchant Website for Web <span class="mandatoryfield">*</span></label>
													<div class="col-sm-8">
														<input id="merchantwebsiteforweb" type="text" name="merchantwebsiteforweb" value="<?php if(isset($paymentgateway) && !empty($paymentgateway)){ echo $paymentgateway['merchantwebsiteforweb']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)" maxlength="50">
													</div>
												</div>
												
												<div class="form-group" id="merchantwebsiteforapp_div">
													<label for="merchantwebsiteforapp" class="col-sm-4 control-label">Merchant Website for App <span class="mandatoryfield">*</span></label>
													<div class="col-sm-8">
														<input id="merchantwebsiteforapp" type="text" name="merchantwebsiteforapp" value="<?php if(isset($paymentgateway) && !empty($paymentgateway)){ echo $paymentgateway['merchantwebsiteforapp']; } ?>" class="form-control" maxlength="150"  >
													</div>
												</div>
												
												<div class="form-group" id="channelidforweb_div">
													<label for="channelidforweb" class="col-sm-4 control-label">ChannelID for Web <span class="mandatoryfield">*</span></label>
													<div class="col-sm-8">
														<input id="channelidforweb" type="text" name="channelidforweb" value="<?php if(isset($paymentgateway) && !empty($paymentgateway)){ echo $paymentgateway['channelidforweb']; } ?>" class="form-control" maxlength="150"  >
													</div>
												</div>

												<div class="form-group" id="channelidforapp_div">
													<label for="channelidforapp" class="col-sm-4 control-label">ChannelID for App <span class="mandatoryfield">*</span></label>
													<div class="col-sm-8">
														<input id="channelidforapp" type="text" name="channelidforapp" value="<?php if(isset($paymentgateway) && !empty($paymentgateway)){ echo $paymentgateway['channelidforapp']; } ?>" class="form-control" maxlength="150"  >
													</div>
												</div>

												<div class="form-group" id="industrytypeid_div">
													<label for="industrytypeid" class="col-sm-4 control-label">Industry Type ID <span class="mandatoryfield">*</span></label>
													<div class="col-sm-8">
														<input id="industrytypeid" type="text" name="industrytypeid" value="<?php if(isset($paymentgateway) && !empty($paymentgateway)){ echo $paymentgateway['industrytypeid']; } ?>" class="form-control" maxlength="150"  >
													</div>
												</div>

												<div class="form-group">
													<label for="focusedinput" class="col-md-4 control-label">Is Debug</label>
													<div class="col-md-8">
														<div class="col-md-3 col-xs-3" style="padding-left: 0px;">
															<div class="radio">
															<input type="radio" name="paytmisdebug" id="paytmisdebugyes" value="1" <?php if(isset($paymentgateway) && $paymentgateway['isdebug']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
															<label for="paytmisdebugyes">Yes</label>
															</div>
														</div>
														<div class="col-md-3 col-xs-3">
															<div class="radio">
															<input type="radio" name="paytmisdebug" id="paytmisdebugno" value="0" <?php if(isset($paymentgateway) && $paymentgateway['isdebug']==0){ echo 'checked'; }?>>
															<label for="paytmisdebugno">No</label>
															</div>
														</div>
													</div>
												</div>



											</div>
									</div>
								</div>
							<?php }
							if($paymentgateway['paymentgatewayid'] == '3'){ ?>
								<div class="tab-pane" id="gateway3">
									<div class="row">
										<div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-1 col-md-offset-1">
								<div class="form-group" id="payumerchantid_div">
									<label for="payumerchantid" class="col-sm-4 control-label">Merchant ID <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<input id="payumerchantid" type="text" name="payumerchantid" value="<?php if(isset($paymentgateway) && !empty($paymentgateway)){ echo $paymentgateway['merchantid']; } ?>" class="form-control" onkeypress="return isNumber(event)" >
									</div>
								</div>
								
								<div class="form-group" id="payumerchantkey_div">
									<label for="payumerchantkey" class="col-sm-4 control-label">Merchant Key <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<input id="payumerchantkey" type="text" name="payumerchantkey" value="<?php if(isset($paymentgateway) && !empty($paymentgateway)){ echo $paymentgateway['merchantkey']; } ?>" class="form-control" onkeypress="return alphanumeric(event)" maxlength="50" >
									</div>
								</div>
								
								<div class="form-group" id="payumerchantsalt_div">
									<label for="payumerchantsalt" class="col-sm-4 control-label">Merchant Salt <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<input id="payumerchantsalt" type="text" name="payumerchantsalt" value="<?php if(isset($paymentgateway) && !empty($paymentgateway)){ echo $paymentgateway['merchantsalt']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)" maxlength="50">
									</div>
								</div>
								
								<div class="form-group" id="payuauthheader_div">
									<label for="payuauthheader" class="col-sm-4 control-label">Auth Header <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<input id="payuauthheader" type="text" name="payuauthheader" value="<?php if(isset($paymentgateway) && !empty($paymentgateway)){ echo $paymentgateway['authheader']; } ?>" class="form-control" maxlength="150"  >
									</div>
								</div>

								<div class="form-group">
									<label for="focusedinput" class="col-md-4 control-label">Is Debug</label>
									<div class="col-md-8">
										<div class="col-md-3 col-xs-3" style="padding-left: 0px;">
											<div class="radio">
											<input type="radio" name="payuisdebug" id="payuisdebugyes" value="1" <?php if(isset($paymentgateway) && $paymentgateway['isdebug']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
											<label for="payuisdebugyes">Yes</label>
											</div>
										</div>
										<div class="col-md-3 col-xs-3">
											<div class="radio">
											<input type="radio" name="payuisdebug" id="payuisdebugno" value="0" <?php if(isset($paymentgateway) && $paymentgateway['isdebug']==0){ echo 'checked'; }?>>
											<label for="payuisdebugno">No</label>
											</div>
										</div>
									</div>
								</div>


							</div>
							</div>
							</div>
						<?php }
							}
					} ?>
					
				</div>
				</div>	
				<div class="row">
						<div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-1 col-md-offset-1">
						  	<div class="form-group row" for="activeplan" id="activeplan_div">
	                            <label class="col-md-4 label-control" for="activeplan">
	                                  Active Plan
	                             <span class="mandatoryfield"> * </span></label>
	                            <div class="col-md-8">
	                             <select class="form-control selectpicker" id="activeplan" name="activeplan">
                                         <option value="0" selected>Select Plan</option>
                                        <?php foreach($this->Paymentgatewaytype as $key=>$value){ ?>
                                         <option value="<?php echo $key; ?>" <?php if(isset($activeplan)){ if($activeplan['paymentgatewayid']== $key){ echo 'selected'; } } ?>><?php echo ucwords($value); ?></option> 
                                         <?php }?>
                                      
                                    </select>  
	                              </div>
                            </div>
							<div class="form-group row">
								<label for="onlinepaymentyes" class="col-md-4 control-label">Online Payment </label>
								<div class="col-sm-8">
									<div class="col-sm-3 col-xs-3" style="padding-left: 0px;">
										<div class="radio">
										<input type="radio" name="onlinepayment" id="onlinepaymentyes" value="1"  <?php if(isset($systemsetting) && $systemsetting['onlinepayment']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
										<label for="onlinepaymentyes">On</label>
										</div>
									</div>
									<div class="col-sm-3 col-xs-3">
										<div class="radio">
										<input type="radio" name="onlinepayment" id="onlinepaymentno" value="0" <?php if(isset($systemsetting) && $systemsetting['onlinepayment']==0){ echo 'checked'; }?>>
										<label for="onlinepaymentno">Off</label>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group text-center">
								<label for="focusedinput" class="col-sm-4 control-label"></label>
								<div class="col-sm-8">
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
								</div>
							</div>
						</div>
					</div>
			</form>
		    </div>
		  </div>
		</div>
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->