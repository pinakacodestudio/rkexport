<script>
    var ChannelRecords = "<?php echo (isset($channelcount)?$channelcount:0) ; ?>";
    var NOOFCHANNEL = "<?php echo NOOFCHANNEL ; ?>";
</script>

<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($channeldata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($channeldata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">   
            <div class="col-sm-12 col-md-12 col-lg-12">
                <form class="form-horizontal" id="channelform" name="channelform">
                    <input type="hidden" name="channelid" value="<?php if(isset($channeldata)){ echo $channeldata['id']; } ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default border-panel">
                                <div class="panel-heading"><h2>Channel Details</h2>
                                </div>
                                <div class="panel-body pt-n">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group" id="channel_div">
                                                <div class="col-sm-12 pr-sm">
                                                    <label for="name" class="control-label">Name <span class="mandatoryfield">*</span></label>
                                                    <input id="name" type="text" name="name" value="<?php if(!empty($channeldata)){ echo $channeldata['name']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row" id="color_div">
                                                <div class="col-md-12 pr-sm pl-sm">
                                                    <label class="control-label" for="color">Color <span class="mandatoryfield">*</span></label>
                                                    <input type="text" id="color" class="form-control demo" name="color" value="<?php if(!empty($channeldata)){ echo $channeldata['color']; }else{ echo '#70c24a'; } ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" id="priority_div">
                                                <div class="col-sm-12 pl-sm pr-sm">
                                                    <label for="priority" class="control-label">Channel Priority <span class="mandatoryfield">*</span></label>
                                                    <input id="priority" type="text" name="priority" value="<?php if(isset($channeldata)){ echo $channeldata['priority']; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="3">
                                                </div>
                                            </div>  
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" id="minimumorderamount_div">
                                                <div class="col-sm-12 pl-sm">
                                                    <label for="minimumorderamount" class="control-label">Min. Order Amount (<?=CURRENCY_CODE?>)</label>
                                                    <input id="minimumorderamount" type="text" name="minimumorderamount" value="<?php if(isset($channeldata)){ echo $channeldata['minimumorderamount']; } ?>" class="form-control" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                                </div>
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group" id="multiplememberchannel_div">
                                                <div class="col-sm-12 pr-sm">
                                                    <label for="multiplememberchannel" class="control-label">Multiple Seller Channel</label>
                                                    <select id="multiplememberchannel" name="multiplememberchannel[]" class="selectpicker form-control" data-select-on-tab="true" data-size="5" title="Select Channel" data-live-search="true" data-actions-box="true" multiple>
                                                        <option value="0" <?php if(!empty($channeldata)){  if(in_array('0', explode(",",$channeldata['multiplememberchannel']))){ echo "selected"; } } ?>>Company</option>
                                                        <?php if(!empty($multiplememberchannel)){ 
                                                            foreach($multiplememberchannel as $cd){ 
                                                            
                                                            $selected = ""; 
                                                            if(!empty($channeldata)){ 
                                                                $arrChannel = explode(",",$channeldata['multiplememberchannel']);
                                                                if(in_array($cd['id'], $arrChannel)){ 
                                                                    $selected = "selected"; 
                                                                } 
                                                            }
                                                            ?>
                                                            <option value="<?php echo $cd['id']; ?>" <?php echo $selected; ?>><?php echo $cd['name']; ?></option>
                                                        <?php }
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group" id="allowedchannelmemberregistration_div">
                                                <div class="col-sm-12 pr-sm pl-sm">
                                                    <label for="allowedchannelmemberregistration" class="control-label">Allowed Channel <?=Member_label?> Registration</label>
                                                    <select id="allowedchannelmemberregistration" name="allowedchannelmemberregistration[]" class="selectpicker form-control" data-select-on-tab="true" data-size="5" title="Select Channel" data-live-search="true" data-actions-box="true" multiple>
                                                        
                                                    <?php if(!empty($allowedchannelregistrationdata)){ 
                                                            foreach($allowedchannelregistrationdata as $cd){ 
                                                            
                                                            $selected = ""; 
                                                            if(!empty($channeldata)){ 
                                                                $arrChannel = explode(",",$channeldata['allowedchannelmemberregistration']);
                                                                if(in_array($cd['id'], $arrChannel)){ 
                                                                    $selected = "selected"; 
                                                                } 
                                                            }
                                                            ?>
                                                            <option value="<?php echo $cd['id']; ?>" <?php echo $selected; ?>><?php echo $cd['name']; ?></option>
                                                        <?php }
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group" id="advancepaymentcod_div">
                                                <div class="col-sm-12 pl-sm">
                                                    <label for="advancepaymentcod" class="control-label">Advance Payment (COD) (%)</label>
                                                    <input id="advancepaymentcod" type="text" name="advancepaymentcod" value="<?php if(isset($channeldata)){ echo $channeldata['advancepaymentcod']; } ?>" class="form-control" onkeypress="return decimal_number_validation(event, this.value, 5)">
                                                </div>
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="form-group text-center">
                                                <label for="focusedinput" class="col-sm-5 col-xs-4 col-md-4 control-label">Priority of Advance Payment</label>
                                                <div class="col-sm-7 col-xs-8">
                                                    <div class="col-sm-6 col-xs-6" style="padding-left: 0px;">
                                                        <div class="radio">
                                                        <input type="radio" name="advancepaymentpriority" id="channelwise" value="0" <?php if(isset($channeldata) && $channeldata['advancepaymentpriority']==0){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                        <label for="channelwise">Channel Wise</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-xs-6">
                                                        <div class="radio">
                                                        <input type="radio" name="advancepaymentpriority" id="memberwise" value="1" <?php if(isset($channeldata) && $channeldata['advancepaymentpriority']==1){ echo 'checked'; }?>>
                                                        <label for="memberwise">Member Wise</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="<?php if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ echo "display:block;"; }else { echo "display:none;"; } ?>">
                        <div class="col-md-12">
                            <div class="panel panel-default border-panel">
                                <div class="panel-heading"><h2>Common Details</h2>
                                </div>
                                <div class="panel-body p-n">
                                    <div class="row mb-sm m-n">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Quotation<br>
                                                </label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="quotation" value="<?php if(isset($channeldata) && $channeldata['quotation']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['quotation']==1){ echo 'checked'; }?>>
                                                    </div>
                                                    <!--  <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                                        <div class="radio">
                                                            <input type="radio" name="quotation" id="quotationyes" value="1" <?php if(isset($channeldata) && $channeldata['quotation']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                            <label for="quotationyes">Yes</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 col-xs-8">
                                                        <div class="radio">
                                                        <input type="radio" name="quotation" id="quotationno" value="0" <?php if(isset($channeldata) && $channeldata['quotation']==0){ echo 'checked'; }?>>
                                                        <label for="quotationno">No</label>
                                                        </div>
                                                    </div> -->
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Partial Payment</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="partialpayment" value="<?php if(isset($channeldata) && $channeldata['partialpayment']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['partialpayment']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Identity Proof</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="identityproof" value="<?php if(isset($channeldata) && $channeldata['identityproof']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['identityproof']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Debit Limit</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="debitlimit" value="<?php if(isset($channeldata) && $channeldata['debitlimit']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['debitlimit']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Multiple <?=member_label?> with same channel</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="multiplememberwithsamechannel" value="<?php if(isset($channeldata) && $channeldata['multiplememberwithsamechannel']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['multiplememberwithsamechannel']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Add Order Without Stock</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="addorderwithoutstock" value="<?php if(isset($channeldata) && $channeldata['addorderwithoutstock']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['addorderwithoutstock']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Product Listing</label>
                                                <div class="col-md-5">
                                                    <div class="listing">
                                                        <input type="checkbox" name="productlisting" value="<?php if(isset($channeldata) && $channeldata['productlisting']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['productlisting']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Automatic Generate Invoice</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="automaticgenerateinvoice" value="<?php if(isset($channeldata) && $channeldata['automaticgenerateinvoice']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['automaticgenerateinvoice']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label"><?=Member_label?> Specific Product</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="memberspecificproduct" value="<?php if(isset($channeldata) && $channeldata['memberspecificproduct']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['memberspecificproduct']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Discount</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="discount" value="<?php if(isset($channeldata) && $channeldata['discount']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['discount']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Web Panel Login</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="website" value="<?php if(isset($channeldata) && $channeldata['website']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['website']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Discount Priority</label>
                                                <div class="col-md-5">
                                                    <div class="generalmember">
                                                        <input type="checkbox" name="discountpriority" value="<?php if(isset($channeldata) && $channeldata['discountpriority']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['discountpriority']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Common Basic Sales Price</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="memberbasicsalesprice" value="<?php if(isset($channeldata) && $channeldata['memberbasicsalesprice']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['memberbasicsalesprice']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Edit Tax Rate</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="edittaxrate" value="<?php if(isset($channeldata) && $channeldata['edittaxrate']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['edittaxrate']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">CRM</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="crm" value="<?php if(isset($channeldata) && $channeldata['crm']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['crm']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Discount Coupon</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="discountcoupon" value="<?php if(isset($channeldata) && $channeldata['discountcoupon']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['discountcoupon']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Rating</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="rating" value="<?php if(isset($channeldata) && $channeldata['rating']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['rating']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Mobile Application</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="mobileapplication" value="<?php if(isset($channeldata) && $channeldata['mobileapplication']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['mobileapplication']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Show Upper Directory</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="showupperdirectory" value="<?php if(isset($channeldata) && $channeldata['showupperdirectory']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['showupperdirectory']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Increment / Decrement Price</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="incrementdecrementprice" value="<?php if(isset($channeldata) && $channeldata['incrementdecrementprice']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['incrementdecrementprice']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Add <?=Member_label?> For APP</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="addmemberforrapp" value="<?php if(isset($channeldata) && $channeldata['addmemberforrapp']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['addmemberforrapp']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-7 control-label">Website Type</label>
                                                <div class="col-md-5">
                                                    <div class="websitetype">
                                                        <input type="checkbox" name="websitetype" value="<?php if(isset($channeldata) && $channeldata['websitetype']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['websitetype']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="<?php if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ echo "display:block;"; }else { echo "display:none;"; } ?>">
                        <div class="col-md-12">
                            <div class="panel panel-default border-panel">
                                <div class="panel-heading"><h2>Offer Module Settings</h2>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="offermodule" class="col-md-8 control-label">Offer Module</label>
                                                <div class="col-md-4 p-n">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="offermodule" value="<?php if(isset($channeldata) && $channeldata['offermodule']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['offermodule']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                    </div>  

                    <?php if(REWARDSPOINTS==1){ ?> 
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default border-panel">
                                <div class="panel-heading"><h2>Points Distribution Settings</h2>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="productwisepoints" class="col-md-8 control-label">Product wise points</label>
                                                <div class="col-md-4 p-n">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="productwisepoints" value="<?php if(isset($channeldata) && $channeldata['productwisepoints']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['productwisepoints']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="productwisepointsmultiplywithqty" class="col-md-8 control-label">Multiply Points with Qty</label>
                                                <div class="col-md-4 p-n">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="productwisepointsmultiplywithqty" value="<?php if(isset($channeldata) && $channeldata['productwisepointsmultiplywithqty']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['productwisepointsmultiplywithqty']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(!isset($firstlevel) || (isset($firstlevel) && isset($channeldata) && $firstlevel!=$channeldata['id'])){ ?>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="productwisepointsforseller" class="col-md-8 control-label">Seller will get Points</label>
                                                <div class="col-md-4 p-n">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="productwisepointsforseller" value="<?php if(isset($channeldata) && $channeldata['productwisepointsforseller']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['productwisepointsforseller']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="productwisepointsforbuyer" class="col-md-8 control-label">Buyer will get Points</label>
                                                <div class="col-md-4 p-n">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="productwisepointsforbuyer" value="<?php if(isset($channeldata) && $channeldata['productwisepointsforbuyer']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['productwisepointsforbuyer']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12"><hr></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="overallproductpoints" class="col-md-7 control-label">Overall Products Points</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="overallproductpoints" value="<?php if(isset($channeldata) && $channeldata['overallproductpoints']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['overallproductpoints']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group" id="rewardforrefferedby_div">
                                                <div class="col-sm-12">
                                                    <label for="sellerpointsforoverallproduct" class="control-label" style="float:left">Give</label>
                                                    <?php if(!isset($firstlevel) || (isset($firstlevel) && isset($channeldata) && $firstlevel!=$channeldata['id'])){ ?>
                                                    <input id="sellerpointsforoverallproduct" type="text" name="sellerpointsforoverallproduct" value="<?php if(!empty($channeldata) && !empty($channeldata['sellerpointsforoverallproduct'])){ echo $channeldata['sellerpointsforoverallproduct']; } ?>" class="form-control text-center mr-sm ml-sm" maxlength="6" onkeypress="return isNumber(event)" style="width:10%;float: left;">
                                                    <label for="sellerpointsforoverallproduct" class="control-label" style="float:left">Points to Seller</label>
                                                    <?php } ?>
                                                    <input id="buyerpointsforoverallproduct" type="text" name="buyerpointsforoverallproduct" value="<?php if(!empty($channeldata) && !empty($channeldata['buyerpointsforoverallproduct'])){ echo $channeldata['buyerpointsforoverallproduct']; } ?>" class="form-control text-center mr-sm ml-sm" maxlength="6" onkeypress="return isNumber(event)" style="width:10%;float: left;">
                                                    <label for="buyerpointsforoverallproduct" class="control-label" style="float:left">Points to Buyer on</label>
                                                    <input id="mimimumorderqtyforoverallproduct" type="text" name="mimimumorderqtyforoverallproduct" value="<?php if(!empty($channeldata) && !empty($channeldata['mimimumorderqtyforoverallproduct'])){ echo $channeldata['mimimumorderqtyforoverallproduct']; } ?>" class="form-control text-center mr-sm ml-sm" maxlength="6" onkeypress="return isNumber(event)" style="width:10%;float: left;">
                                                    <label for="mimimumorderqtyforoverallproduct" class="ml control-label" style="float:left">Minimum Qty</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12"><hr></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="pointsonsalesorder" class="col-md-7 control-label">Points on Sales Order</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="pointsonsalesorder" value="<?php if(isset($channeldata) && $channeldata['pointsonsalesorder']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['pointsonsalesorder']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group" id="rewardforrefferedby_div">
                                                <div class="col-sm-12">
                                                    <label for="sellerpointsforsalesorder" class="control-label" style="float:left">Give</label>
                                                    <?php if(!isset($firstlevel) || (isset($firstlevel) && isset($channeldata) && $firstlevel!=$channeldata['id'])){ ?>
                                                    <input id="sellerpointsforsalesorder" type="text" name="sellerpointsforsalesorder" value="<?php if(!empty($channeldata) && !empty($channeldata['sellerpointsforsalesorder'])){ echo $channeldata['sellerpointsforsalesorder']; } ?>" class="form-control text-center mr-sm ml-sm" maxlength="6" onkeypress="return isNumber(event)" style="width:10%;float: left;">
                                                    <label for="sellerpointsforsalesorder" class="control-label" style="float:left">Points to Seller</label>
                                                    <?php } ?>
                                                    <input id="buyerpointsforsalesorder" type="text" name="buyerpointsforsalesorder" value="<?php if(!empty($channeldata) && !empty($channeldata['buyerpointsforsalesorder'])){ echo $channeldata['buyerpointsforsalesorder']; } ?>" class="form-control text-center mr-sm ml-sm" maxlength="6" onkeypress="return isNumber(event)" style="width:10%;float: left;">
                                                    <label for="buyerpointsforsalesorder" class="control-label" style="float:left">Points to Buyer on</label>
                                                    <input id="mimimumorderamountforsalesorder" type="text" name="mimimumorderamountforsalesorder" value="<?php if(!empty($channeldata) && !empty($channeldata['mimimumorderamountforsalesorder'])){ echo $channeldata['mimimumorderamountforsalesorder']; } ?>" class="form-control text-center mr-sm ml-sm" maxlength="6" onkeypress="return isNumber(event)" style="width:18%;float: left;">
                                                    <label for="mimimumorderamountforsalesorder" class="ml control-label" style="float:left">Minimum Order Amount in Sales Transaction</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12"><hr></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="samechannelreferrermemberpointonoff" class="col-md-7 control-label">Same Channel Referrer <?=Member_label?> Points</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="samechannelreferrermemberpointonoff" value="<?php if(isset($channeldata) && $channeldata['samechannelreferrermemberpointonoff']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['samechannelreferrermemberpointonoff']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group" id="rewardforrefferedby_div">
                                                <div class="col-sm-12">
                                                    <label for="samechannelreferrermemberpoint" class="control-label" style="float:left">Give</label>
                                                    <?php if(!isset($firstlevel) || (isset($firstlevel) && isset($channeldata) && $firstlevel!=$channeldata['id'])){ ?>
                                                    <input id="samechannelreferrermemberpoint" type="text" name="samechannelreferrermemberpoint" value="<?php if(!empty($channeldata) && !empty($channeldata['samechannelreferrermemberpoint'])){ echo $channeldata['samechannelreferrermemberpoint']; } ?>" class="form-control text-center mr-sm ml-sm" maxlength="6" onkeypress="return isNumber(event)" style="width:10%;float: left;">
                                                    <label for="samechannelreferrermemberpoint" class="control-label" style="float:left">Points to Referral <?=Member_label?> If New <?=Member_label?> Purchase</label>
                                                    <input id="mimimumorderamountforsamechannelreferrer" type="text" name="mimimumorderamountforsamechannelreferrer" value="<?php if(!empty($channeldata) && !empty($channeldata['mimimumorderamountforsamechannelreferrer'])){ echo $channeldata['mimimumorderamountforsamechannelreferrer']; } ?>" class="form-control text-center mr-sm ml-sm" maxlength="6" onkeypress="return isNumber(event)" style="width:10%;float: left;">
                                                    <label for="mimimumorderamountforsamechannelreferrer" class="control-label" style="float:left">Min. Order Amount</label>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12"><hr></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="referandearn" class="col-md-7 control-label">Refer & Earn</label>
                                                <div class="col-md-5">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="referandearn" value="<?php if(isset($channeldata) && $channeldata['referandearn']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($channeldata) && $channeldata['referandearn']==1){ echo 'checked'; }?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group" id="rewardforrefferedby_div">
                                                <div class="col-sm-12">
                                                    <label for="rewardforrefferedby" class="mr control-label" style="float:left">Referrer will get</label>
                                                    <input id="rewardforrefferedby" type="text" name="rewardforrefferedby" value="<?php if(!empty($channeldata) && !empty($channeldata['rewardforrefferedby'])){ echo $channeldata['rewardforrefferedby']; } ?>" class="form-control text-center" onkeypress="return isNumber(event)" maxlength="6" style="width:30%;float: left;">
                                                    <label for="rewardforrefferedby" class="ml control-label" style="float:left">Points</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group" id="rewardfornewregister_div">
                                                <div class="col-md-12">
                                                    <label class="mr control-label" for="rewardfornewregister" style="float:left">New <?=member_label?> will get </label>
                                                    <input type="text" id="rewardfornewregister" class="form-control text-center" name="rewardfornewregister" value="<?php if(!empty($channeldata) && !empty($channeldata['rewardfornewregister'])){ echo $channeldata['rewardfornewregister']; } ?>" onkeypress="return isNumber(event)" maxlength="6" style="width:30%;float: left;">
                                                    <label for="rewardfornewregister" class="ml control-label" style="float:left">Points</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default border-panel">
                                <div class="panel-heading"><h2>Points Redeem Settings</h2>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group" id="conversationrate_div">
                                                <div class="col-sm-12">
                                                    <label for="conversationrate" class="mr control-label" style="float:left">1 Point =</label>
                                                    <input id="conversationrate" type="text" name="conversationrate" value="<?php if(isset($channeldata) && !empty($channeldata['conversationrate'])){ echo $channeldata['conversationrate']; } ?>" class="form-control text-center" onkeypress="return isNumber(event)" maxlength="3" style="width:10%;float: left;">
                                                    <label for="conversationrate" class="ml control-label">Rs.</label>
                                                </div>
                                                
                                            </div>
                                            <div class="col-sm-12">
                                                <label for="conversationrate" class="pt-n control-label mandatoryfield">- Ex. 1 Point = {10} Rs.</label>
                                                <hr>
                                            </div>
                                            <div class="form-group" id="conversationrate_div">
                                                <div class="col-sm-12">
                                                    <label for="minimumpointsonredeem" class="mr control-label" style="float:left">Minimum Balance of</label>
                                                    <input id="minimumpointsonredeem" type="text" name="minimumpointsonredeem" value="<?php if(isset($channeldata) && !empty($channeldata['minimumpointsonredeem'])){ echo $channeldata['minimumpointsonredeem']; } ?>" class="form-control text-center" onkeypress="return isNumber(event)" maxlength="3" style="width:10%;float: left;">
                                                    <label for="minimumpointsonredeem" class="ml control-label ">Points Required on Redeem</label>
                                                </div>
                                            </div>  
                                            <div class="col-sm-12">
                                                <label for="minimumpointsonredeem" class="pt-n control-label mandatoryfield">- Ex. If <?=Member_label?> has Minimum Balance of {100} Points then only he can redeem points on purchase</label>
                                                <hr>
                                            </div>
                                            <div class="form-group" id="conversationrate_div">
                                                <div class="col-sm-12">
                                                    <label for="minimumpointsonredeemfororder" class="mr control-label" style="float:left">Minimum</label>
                                                    <input id="minimumpointsonredeemfororder" type="text" name="minimumpointsonredeemfororder" value="<?php if(isset($channeldata) && !empty($channeldata['minimumpointsonredeemfororder'])){ echo $channeldata['minimumpointsonredeemfororder']; } ?>" class="form-control text-center" onkeypress="return isNumber(event)" maxlength="3" style="width:10%;float: left;">
                                                    <label for="minimumpointsonredeemfororder" class="ml control-label ">Points Required on Redeem</label>
                                                </div>
                                            </div>  
                                            <div class="col-sm-12">
                                                <label for="minimumpointsonredeemfororder" class="pt-n control-label mandatoryfield">
                                                - Ex. If Minimum {50} Points Required for Redeem and <?=Member_label?> have 100 Points Balance then, <?=Member_label?> can only redeem {50} or more points at the time of purchase process
                                                </label>
                                                <hr>
                                            </div>
                                            <div class="form-group" id="mimimumpurchaseorderamountforredeem_div">
                                                <div class="col-sm-12">
                                                    <label for="mimimumpurchaseorderamountforredeem" class="mr control-label" style="float:left">Minimum Purchase Order Amount</label>
                                                    <input id="mimimumpurchaseorderamountforredeem" type="text" name="mimimumpurchaseorderamountforredeem" value="<?php if(isset($channeldata) && !empty($channeldata['mimimumpurchaseorderamountforredeem'])){ echo $channeldata['mimimumpurchaseorderamountforredeem']; } ?>" class="form-control text-center" onkeypress="return isNumber(event)" maxlength="6" style="width:10%;float: left;">
                                                    <label for="mimimumpurchaseorderamountforredeem" class="ml control-label ">for Points Redeem</label>
                                                </div>
                                            </div>  
                                            <div class="col-sm-12">
                                                <label for="mimimumpurchaseorderamountforredeem" class="pt-n control-label mandatoryfield" style="text-align:left;">
                                                - Ex. Minimum Purchase Order Amount is {10000}<br>
                                                - If Purchase Order is {9000} then <?=Member_label?> can not redeem any points
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?> 

                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default border-panel">
                                <div class="panel-heading">
                                    <h2>Color Settings</h2>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row" id="themecolor_div">
                                                <label class="col-md-4 control-label" for="themecolor">Theme Color<span
                                                        class="mandatoryfield">*</span>
                                                </label>
                                                <div class="col-md-8">
                                                    <div>	
                                                        <div class="col-sm-11" style="padding: 0px;">
                                                            <input type="text" id="themecolor" class="form-control selectcolor" name="themecolor" data-defaultvalue="#03a9f4" value="<?php if(!empty($channeldata) && $channeldata['themecolor']!=""){ echo $channeldata['themecolor']; }else{ echo '#03a9f4'; } ?>">
                                                        </div>
                                                        <div class="col-sm-1 pr-n pl-n mt-sm">
                                                            <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'themecolor\']').minicolors('value','#03a9f4');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group row" id="fontcolor_div">
                                                <label class="col-md-4 control-label" for="fontcolor">Font Color<span
                                                        class="mandatoryfield">*</span>
                                                </label>
                                                <div class="col-md-8">
                                                    <div>	
                                                        <div class="col-sm-11" style="padding: 0px;">
                                                            <input type="text" id="fontcolor" class="form-control selectcolor" name="fontcolor" value="<?php if(!empty($channeldata) && $channeldata['fontcolor']!=''){ echo $channeldata['fontcolor']; }else{ echo '#ffffff'; } ?>">
                                                        </div>
                                                        <div class="col-sm-1 pr-n pl-n mt-sm">
                                                            <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'fontcolor\']').minicolors('value','#FFF');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group" id="sidebarbgcolor_div">
                                                <label for="sidebarbgcolor" class="col-md-4 control-label">Sidebar BG Color<span
                                                        class="mandatoryfield">*</span>
                                                </label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <div class="col-sm-11" style="padding: 0px;">
                                                            <input type="text" id="sidebarbgcolor" class="form-control selectcolor" name="sidebarbgcolor" value="<?php if(!empty($channeldata) && $channeldata['sidebarbgcolor']!=""){ echo $channeldata['sidebarbgcolor']; }else{ echo '#2196f3'; } ?>">
                                                        </div>
                                                        <div class="col-sm-1 pr-n pl-n mt-sm">
                                                            <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'sidebarbgcolor\']').minicolors('value','#2196f3');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" id="sidebarmenuactivecolor_div">
                                                <label for="sidebarmenuactivecolor" class="col-md-4 control-label">Sidebar Menu Active Color<span
                                                        class="mandatoryfield">*</span>
                                                </label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <div class="col-sm-11" style="padding: 0px;">
                                                            <input type="text" id="sidebarmenuactivecolor" class="form-control selectcolor" name="sidebarmenuactivecolor" value="<?php if(!empty($channeldata) && $channeldata['sidebarmenuactivecolor']!=""){ echo $channeldata['sidebarmenuactivecolor']; }else{ echo '#42a5f5'; } ?>">
                                                        </div>
                                                        <div class="col-sm-1 pr-n pl-n mt-sm">
                                                            <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'sidebarmenuactivecolor\']').minicolors('value','#42a5f5');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group" id="sidebarsubmenubgcolor_div">
                                                <label for="sidebarsubmenubgcolor" class="col-md-4 control-label">Sidebar Submenu BG Color<span
                                                        class="mandatoryfield">*</span>
                                                </label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <div class="col-sm-11" style="padding: 0px;">
                                                            <input type="text" id="sidebarsubmenubgcolor" class="form-control selectcolor" name="sidebarsubmenubgcolor" value="<?php if(!empty($channeldata) && $channeldata['sidebarsubmenubgcolor']!=""){ echo $channeldata['sidebarsubmenubgcolor']; }else{ echo '#1a78c2'; } ?>">
                                                        </div>
                                                        <div class="col-sm-1 pr-n pl-n mt-sm">
                                                            <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'sidebarsubmenubgcolor\']').minicolors('value','#1a78c2');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" id="sidebarsubmenuactivecolor_div">
                                                <label for="sidebarsubmenuactivecolor" class="col-md-4 control-label">Sidebar Submenu Active Color<span
                                                        class="mandatoryfield">*</span></label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <div class="col-sm-11" style="padding: 0px;">
                                                            <input type="text" id="sidebarsubmenuactivecolor" class="form-control selectcolor" name="sidebarsubmenuactivecolor" value="<?php if(!empty($channeldata) && $channeldata['sidebarsubmenuactivecolor']!=""){ echo $channeldata['sidebarsubmenuactivecolor']; }else{ echo '#2196f3'; } ?>">
                                                        </div>
                                                        <div class="col-sm-1 pr-n pl-n mt-sm">
                                                            <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'sidebarsubmenuactivecolor\']').minicolors('value','#2196f3');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group" id="footerbgcolor_div">
                                                <label for="footerbgcolor" class="col-md-4 control-label">Footer BG Color<span
                                                        class="mandatoryfield">*</span>
                                                </label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <div class="col-sm-11" style="padding: 0px;">
                                                            <input type="text" id="footerbgcolor" class="form-control selectcolor" name="footerbgcolor" data-defaultvalue="#03a9f4" value="<?php if(!empty($channeldata) && $channeldata['footerbgcolor']!=""){ echo $channeldata['footerbgcolor']; }else{ echo '#03a9f4'; } ?>">
                                                        </div>
                                                        <div class="col-sm-1 pr-n pl-n mt-sm">
                                                            <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'footerbgcolor\']').minicolors('value','#03a9f4');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" id="linkcolor_div">
                                                <label for="linkcolor" class="col-md-4 control-label">Link Color<span
                                                        class="mandatoryfield">*</span>
                                                </label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <div class="col-sm-11" style="padding: 0px;">
                                                            <input type="text" id="linkcolor" class="form-control selectcolor" name="linkcolor" data-defaultvalue="#03a9f4" value="<?php if(!empty($channeldata) && $channeldata['linkcolor']!=""){ echo $channeldata['linkcolor']; }else{ echo '#03a9f4'; } ?>">
                                                        </div>
                                                        <div class="col-sm-1 pr-n pl-n mt-sm">
                                                            <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'linkcolor\']').minicolors('value','#bf2e2e');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group" id="tableheadercolor_div">
                                                <label for="tableheadercolor" class="col-md-4 control-label">Table Header Color<span
                                                        class="mandatoryfield">*</span>
                                                </label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <div class="col-sm-11" style="padding: 0px;">
                                                            <input type="text" id="tableheadercolor" class="form-control selectcolor" name="tableheadercolor" value="<?php if(!empty($channeldata) && $channeldata['tableheadercolor']!=""){ echo $channeldata['tableheadercolor']; }else{ echo '#000000'; } ?>">
                                                        </div>
                                                        <div class="col-sm-1 pr-n pl-n mt-sm">
                                                            <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Reset" onclick="$('input[name=\'tableheadercolor\']').minicolors('value','#bd9117');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default border-panel">
                                <div class="panel-heading">
                                    <h2>Discount Settings</h2>
                                </div>
                                <div class="panel-body pt-n">
                                    <div class="row">
                                        <div class="form-group col-md-10">
                                            <div class="form-group">
                                                <label for="focusedinput" class="col-sm-4 control-label">Discount On
                                                    Bill </label>
                                                <div class="col-sm-8">
                                                    <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                                        <div class="radio">
                                                            <input type="radio" name="discountonbill" id="discountonbillyes" value="1" <?php if(!empty($channeldata) &&  $channeldata['discountonbill']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                            <label for="discountonbillyes">On</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2 col-xs-6">
                                                        <div class="radio">
                                                            <input type="radio" name="discountonbill" id="discountonbillno" value="0" <?php if(!empty($channeldata) && $channeldata['discountonbill']==0){ echo 'checked'; }?>>
                                                            <label for="discountonbillno">Off</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="discountonbilldiv" class="discountonbilldiv" <?php if(!empty($channeldata) && $channeldata['discountonbill']==0){ echo 'style="display:none;"'; }?>>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">GST on Discount</label>
                                                    <div class="col-sm-8">
                                                        <div class="col-sm-4 col-xs-6" style="padding-left: 0px;">
                                                            <div class="radio">
                                                                <input type="radio" name="gstondiscount"
                                                                    id="withoutgst" value="1" checked
                                                                    <?php if(!empty($channeldata) && $channeldata['gstondiscount']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                                <label for="withoutgst">Without GST</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4 col-xs-6">
                                                            <div class="radio">
                                                                <input type="radio" name="gstondiscount"
                                                                    id="withgst" value="0"
                                                                    <?php if(!empty($channeldata) && $channeldata['gstondiscount']==0){ echo 'checked'; }?>>
                                                                <label for="withgst">With GST</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Discount Type</label>
                                                    <div class="col-sm-8">
                                                        <div class="col-sm-4 col-xs-6" style="padding-left: 0px;">
                                                            <div class="radio">
                                                                <input type="radio" name="discountonbilltype" id="percentage" value="1" checked <?php if(!empty($channeldata) && $channeldata['discountonbilltype']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                                <label for="percentage">Percentage</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4 col-xs-6">
                                                            <div class="radio">
                                                                <input type="radio" name="discountonbilltype" id="amounttype" value="0" <?php if(!empty($channeldata) && $channeldata['discountonbilltype']==0){ echo 'checked'; }?>>
                                                                <label for="amounttype">Amount</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="percentageval_div">
                                                    <label class="col-sm-4 control-label" for="percentageval">Percentage (%) <span class="mandatoryfield">*</span></label>
                                                    <div class="col-sm-4">
                                                        <input id="percentageval" type="text" name="percentageval" class="form-control" onkeypress="return decimal_number_validation(event,this.value)" maxlength="5" value="<?php if(!empty($channeldata) && $channeldata['discountonbilltype']==1){ echo $channeldata['discountonbillvalue']; } ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group" id="amount_div" style="display: none;">
                                                    <label class="col-sm-4 control-label" for="amount">Amount (<?=CURRENCY_CODE?>) <span class="mandatoryfield">*</span></label>
                                                    <div class="col-sm-4">
                                                        <input id="amount" type="text" name="amount" class="form-control" onkeypress="return decimal_number_validation(event,this.value)" maxlength="10" value="<?php if(!empty($channeldata) && $channeldata['discountonbilltype']==0){ echo $channeldata['discountonbillvalue']; } ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group" id="discountonbillminvalue_div">
                                                    <label class="col-sm-4 control-label" for="discountonbillminamount">Minimum Bill
                                                        Amount (<?=CURRENCY_CODE?>) <span
                                                            class="mandatoryfield">*</span></label>
                                                    <div class="col-sm-4">
                                                        <input id="discountonbillminamount" type="text" name="discountonbillminamount" class="form-control" onkeypress="return decimal_number_validation(event,this.value)" maxlength="10" value="<?php if(!empty($channeldata) && $channeldata['discountonbillminamount']!=0){ echo $channeldata['discountonbillminamount']; } ?>">
                                                    </div>
                                                </div>
                                                <div class="input-daterange discountdaterangepicker" id="datepicker-range">
                                                    <div class="form-group row" id="startdate_div">
                                                        <label class="col-sm-4 control-label" for="startdate">Date
                                                        </label>
                                                        <div class="col-sm-3 pr-sm">
                                                            <input id="startdate" type="text" name="startdate" value="<?php if(!empty($channeldata) && $channeldata['discountonbillstartdate']!="0000-00-00"){ echo $this->general_model->displaydate($channeldata['discountonbillstartdate']); } ?>" class="form-control datepicker1" placeholder="Start" readonly>
                                                        </div>
                                                        <div class="col-sm-3 pl-sm">
                                                            <input id="enddate" type="text" name="enddate" value="<?php if(!empty($channeldata)){if($channeldata['discountonbillenddate']!="0000-00-00"){ echo $this->general_model->displaydate($channeldata['discountonbillenddate']); }} ?>" class="form-control datepicker1" placeholder="End" readonly>
                                                        </div>
                                                        <div class="col-sm-2 pl-n pt-sm">
                                                            <button type="button" id="cleardatebtn" class="btn btn-primary btn-xs btn-raised">Clear
                                                        Date</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default border-panel">
                                <div class="panel-heading"><h2>Action</h2>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="form-group text-center">
                                            <label for="focusedinput" class="col-sm-5 col-xs-4 col-md-5 control-label">Activate</label>
                                            <div class="col-sm-6 col-xs-8">
                                                <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                                    <div class="radio">
                                                    <input type="radio" name="status" id="yes" value="1" <?php if(isset($channeldata) && $channeldata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                    <label for="yes">Yes</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2 col-xs-6">
                                                    <div class="radio">
                                                    <input type="radio" name="status" id="no" value="0" <?php if(isset($channeldata) && $channeldata['status']==0){ echo 'checked'; }?>>
                                                    <label for="no">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group text-center">
                                            <?php if(!empty($channeldata)){ ?>
                                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                            <?php }else{ ?>
                                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                            <?php } ?>
                                            <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>channel" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                        </div>
                                    </div>
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