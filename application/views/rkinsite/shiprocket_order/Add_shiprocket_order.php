<?php
 //echo "<pre>"; print_r($orderdata);exit;

?>

<script>

    var CURRENCY_CODE = '<?php echo CURRENCY_CODE; ?>';


</script>

<style>

    .courier_card {
        padding: 10px;
        padding-top: 0px;
        padding-bottom: 5px;
        border: 1px solid #ededed;
        
        margin-bottom: 20px;
        background-color: #60b636;
        color:white;
        /*  position: fixed;  */
        border-bottom-left-radius: 13px;
        position: relative;
    }

    .shadowfaxforwarddiv {
        box-shadow: 0px 1px 6px #333 !important;
        margin-bottom: 20px;
       cursor: pointer;
    }
</style>

<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($orderdata) && !isset($addordertype)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($orderdata) && !isset($addordertype)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body pt-n">
                    <form class="form-horizontal" id="shiprocketorderform" name="shiprocketorderform">
                        <input type="hidden" name="ordersid" id="ordersid" value="<?php if(!empty($orderdata)){ echo $orderdata['orderdetail']['id']; } ?>">
                       
                        <input type=hidden id="couriercompanyid" name="couriercompanyid" >
                        <input type=hidden id="name" name="name" >
                        <input type=hidden id="rtocharges" name="rtocharges" >
                        <input type=hidden id="trackingservice" name="trackingservice" >
                        <input type=hidden id="etd" name="etd" >
                        <input type=hidden id="totalrate" name="totalrate" >
                        <input type=hidden id="freightcharge" name="freightcharge" >
                        <input type=hidden id="codcharges" name="codcharges" >
                        <input type=hidden id="pickupaddress" name="pickupaddress" >
                     
                        
                             <div class="row">
                                <div class="col-md-12">
                                    <!-- <div class="panel panel-default border-panel">
                                        <div class="panel-heading">
                                            <h2></h2>
                                        </div> -->
                                        <div class="panel-body"> 
                                            <div class="col-md-10 col-md-offset-2 p-n">
                                                <div class="col-md-10 ">
                                                    <div class="form-group" id="invoice_div">
                                                        <label for="invoiceid" class="control-label col-md-3">Select Invoice <span class="mandatoryfield">*</span></label>
                                                        <div class="col-md-6">
                                                            <select id="invoiceid" name="invoiceid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                <option value="0">Select Invoice</option>
                                                                <?php if(isset($invoicedata)){ foreach($invoicedata as $ba){ ?>
                                                                <option value="<?php echo $ba['id']; ?>"><?php echo ($ba['invoiceno'])." (".CURRENCY_CODE.$ba['amount'].")"; ?></option>
                                                                <?php }} ?>
                                                            </select>
                                                            
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-10 ">
                                                    <div class="form-group" id="pickuplocation_div">
                                                        <label for="pickuplocation" class="control-label col-md-3">Select Pickup Address <span class="mandatoryfield">*</span></label>
                                                        <div class="col-md-6">
                                                            <select id="pickuplocation" name="pickuplocation" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                <option value="0">Select Pickup Address</option>
                                                                <?php if(isset($pickuplocation)){ foreach($pickuplocation as $ba){ ?>
                                                                <option value="<?php echo $ba->pickup_location; ?>"><?php echo ucwords($ba->address); ?></option>
                                                                <?php }} ?>
                                                            </select>
                                                            
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-offset-4  col-md-8  ">
                                                <div class="col-md-3 ">
                                                    <div class="form-group" id="length_div">
                                                        <div class="col-md-10 ">
                                                            <label for="length" class="control-label ">Length(cm) <span class="mandatoryfield">*</span></label>
                                                            <input type="text" id="length" name ="length" class="form-control text-right" value="" onkeypress="return decimal_number_validation(event,this.value)">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="breath_div">
                                                        <div class="col-md-10">
                                                            <label for="breath" class="control-label ">Breath(cm) <span class="mandatoryfield">*</span></label>
                                                            <input type="text" id="breath" name ="breath" class="form-control text-right" value="" onkeypress="return decimal_number_validation(event,this.value)">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <div class=" col-md-offset-4 col-md-8 ">
                                                <div class="col-md-3 ">
                                                    <div class="form-group" id="height_div">
                                                        <div class="col-md-10">
                                                            <label for="height" class="control-label ">Height(cm) <span class="mandatoryfield">*</span></label>
                                                            <input type="text" id="height" name ="height" class="form-control text-right" value="" onkeypress="return decimal_number_validation(event,this.value)">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="weight_div">
                                                        <div class="col-md-10">
                                                            <label for="weight" class="control-label ">Weight(kg) <span class="mandatoryfield">*</span></label>
                                                            <input type="text" id="weight" name ="weight" class="form-control text-right" value="" onkeypress="return decimal_number_validation(event,this.value,6,3)">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-offset-4 col-md-8"> 
                                                <div class="col-md-10"> 
                                                    <input type="button" id="courier" name="courier"  class="<?=addbtn_class;?>" onclick="searchcourier()" value="Search Courier Company">
                                                    <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                                </div>
                                            </div>
                                        <!--  </div> -->
                                    </div>
                                </div>
                                         
                            </div> 
                            
                            <div id="couriercompany"></div>

                            

                            <div class="row" id="buttonids">
                                <div class="col-md-12">
										<div class="panel-body">
                                            <div class="col-md-12 text-center">
												<div class="form-group">
													<input type="button" name="submit" value="Order Now"  class="<?=resetbtn_class;?>" onclick="checkvalidation()" id="submit">
												</div>
											</div>
										</div>
								</div>
                            </div>

                           
                           
                      
                        
                    </form>
                    </div>
                </div>
		    </div>
		  </div>
		</div>

      
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->


