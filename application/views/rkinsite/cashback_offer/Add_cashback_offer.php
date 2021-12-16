<?php 
$PRODUCT_DATA = '';
if(!empty($productdata)){
    foreach($productdata as $product){
        
        $productname = str_replace("'","&apos;",$product['name']);
        if(DROPDOWN_PRODUCT_LIST==0){
            $PRODUCT_DATA .= '<option value="'.$product['id'].'">'.$productname.'</option>';
        }else{
            $content = "";
            if(!empty($product['image']) && file_exists(PRODUCT_PATH.$product['image'])){
                $content .= '<img src=&quot;'.PRODUCT.$product['image'].'&quot; style=&quot;width:40px;&quot;> '.$productname;
            }else{
                $content .= '<img src=&quot;'.PRODUCT.PRODUCTDEFAULTIMAGE.'&quot; style=&quot;width:40px;&quot;> '.$productname;
            }
            
            $PRODUCT_DATA .= '<option data-content="'.$content.'" value="'.$product['id'].'">'.$productname.'</option>';
        }
    } 
}
?>
<script>
    var PRODUCT_DATA = '<?=$PRODUCT_DATA?>';
    var memberidarr = '<?php if(isset($cashbackofferdata)){ echo $cashbackofferdata['memberid']; } ?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($cashbackofferdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($cashbackofferdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
    </small>
    </div>

    <div class="container-fluid">
        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <form class="form-horizontal" id="cashbackofferform" enctype="multipart/form-data">
                        <div class="row">
							<div class="col-md-12">
								<div class="panel panel-default border-panel">
									<div class="panel-heading">
										<h2>Offer Details</h2>
									</div>
									<div class="panel-body pt-n">
                                        <div class="col-sm-12 p-n">
                                            <div class="col-sm-6 p-n">
                                                <input type="hidden" name="cashbackofferid" id="cashbackofferid" value="<?php if(isset($cashbackofferdata)){ echo $cashbackofferdata['id']; } ?>">    
                                                <div class="form-group" id="offername_div">
                                                    <label class="col-md-3 control-label" for="offername">Offer Name <span class="mandatoryfield"> * </span></label>
                                                    <div class="col-md-8">
                                                    <input type="text" id="offername" class="form-control" name="offername" value="<?php if(isset($cashbackofferdata)){ echo $cashbackofferdata['name']; } ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group" id="offerdate_div">
                                                    <label class="col-md-3 control-label">Offer Date</label>
                                                    <div class="col-md-8">
                                                        <div class="input-daterange input-group" id="datepicker-range">
                                                            <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php if(isset($cashbackofferdata) && $cashbackofferdata['startdate']!="0000-00-00"){ echo $this->general_model->displaydate($cashbackofferdata['startdate']); } ?>" placeholder="Start Date" title="Start Date" readonly/>
                                                            <span class="input-group-addon">to</span>
                                                            <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php if(isset($cashbackofferdata) && $cashbackofferdata['enddate']!="0000-00-00"){ echo $this->general_model->displaydate($cashbackofferdata['enddate']); } ?>" placeholder="End Date" title="End Date" readonly/>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <button type="button" id="cleardatebtn" class="col-md-3 btn btn-primary btn-raised btn-xs pull-right" style="margin-right: 80px !important;">Clear Date</button>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if(isset($cashbackofferdata)){
                                                if($cashbackofferdata['channelid']!=0){
                                                    $channelidarr = explode(",",$cashbackofferdata['channelid']);
                                                    $disabled = "";
                                                }else{
                                                    $disabled = "disabled";
                                                    $channelidarr = array(0);
                                                }
                                            } ?>
                                            <div class="col-sm-6 p-n">
                                                <div class="form-group" id="channel_div">
                                                    <label class="col-md-3 control-label" for="channelid">Select Channel</label>
                                                    <input type="hidden" value="<?php if(isset($cashbackofferdata)){ echo $cashbackofferdata['channelid']; } ?>" name="oldchannelid">
                                                    <div class="col-md-8">
                                                        <select class="form-control selectpicker" id="channelid" name="channelid" data-actions-box="true">
                                                            <option value="0">Select Channel</option>
                                                            <?php foreach($channeldata as $row){ ?>
                                                                <option value="<?php echo $row['id']; ?>" <?php if(isset($cashbackofferdata) && $row['id']==$cashbackofferdata['channelid']){  echo 'selected';  } ?>><?php echo $row['name']; ?></option> 
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="member_div">
                                                    <label class="col-md-3 control-label" for="memberid">Select <?=Member_label?></label>
                                                    <input type="hidden" value="<?php if(isset($cashbackofferdata)){ echo $cashbackofferdata['memberid']; } ?>" name="oldmemberid"></label>
                                                    <div class="col-md-8">
                                                        <select id="memberid" name="memberid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" multiple data-actions-box="true" title="Select <?=Member_label?>">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="minbillamount_div">
                                                    <label class="col-md-3 control-label" for="minbillamount">Min. Bill Amount</label>
                                                    <div class="col-md-8">
                                                    <input type="text" id="minbillamount" class="form-control" name="minbillamount" value="<?php if(isset($cashbackofferdata)){ echo $cashbackofferdata['minbillamount']; } ?>" maxlength="10" onkeypress="return decimal_number_validation(event,this.value)">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 pl-xl pr-xl">
                                            <div class="form-group" id="shortdescription_div">
                                                <div class="col-md-12">
                                                    <label class="control-label pl-n" for="shortdescription">Short Description</label>
                                                    <textarea id="shortdescription" class="form-control" name="shortdescription"><?php if(isset($cashbackofferdata)){ echo $cashbackofferdata['shortdescription']; } ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group" id="description_div">
                                                <div id='termscontainer'>
                                                    <label for="focusedinput" class="col-sm-12 control-label mb-sm" for="description" style="text-align: left;">Description</label>
                                                    <div class="col-sm-12">
                                                        <?php $data['controlname']="description";if(isset($cashbackofferdata) && !empty($cashbackofferdata)){$data['controldata']=$cashbackofferdata['description'];} ?>
                                                        <?php $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <hr>
                                            <div class="col-sm-4 pl-sm pr-sm">
                                                <div class="form-group">
                                                    <div class="col-sm-12">
                                                    <label class="control-label">Select Product</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 pl-sm pr-sm">
                                                <div class="form-group">
                                                    <div class="col-sm-12">
                                                        <label class="control-label">Select Variant</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 pl-sm pr-sm">
                                                <div class="form-group">
                                                    <div class="col-sm-12">
                                                        <label class="control-label">Earn Points</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(!empty($cashbackofferdata) && !empty($cashbackofferproductdata)) { ?>
                                            <?php for ($i=0; $i < count($cashbackofferproductdata); $i++) { ?>
                                                <div class="col-sm-12 countproducts" id="countproducts<?=($i+1)?>">
                                                    <input type="hidden" name="cashbackofferproductid[]" value="<?=$cashbackofferproductdata[$i]['id']?>" id="cashbackofferproductid<?=($i+1)?>">
                                                    <input type="hidden" name="uniqueproduct[]" id="uniqueproduct<?=($i+1)?>" value="<?=($cashbackofferproductdata[$i]['productid']."_".$cashbackofferproductdata[$i]['priceid'])?>">
                                                    <div class="col-sm-4 pl-sm pr-sm">
                                                        <div class="form-group" id="product<?=($i+1)?>_div">
                                                            <div class="col-sm-12">
                                                                <select id="productid<?=($i+1)?>" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                                                    <option value="0">Select Product</option>
                                                                    <?=$PRODUCT_DATA?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 pl-sm pr-sm">
                                                        <div class="form-group" id="price<?=($i+1)?>_div">
                                                            <div class="col-md-12">
                                                                <select id="priceid<?=($i+1)?>" name="priceid[]" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                                    <option value="0">Select Variant</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2 pl-sm pr-sm">
                                                        <div class="form-group" id="earnpoints<?=($i+1)?>_div">
                                                            <div class="col-md-12">
                                                                <input type="text" class="form-control earnpoints" id="earnpoints<?=($i+1)?>" name="earnpoints[]" value="<?=$cashbackofferproductdata[$i]['earnpoints']?>" maxlength="4" onkeypress="return isNumber(event);" div-id="<?=($i+1)?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 form-group m-n p-sm pt-md">	
                                                        <?php if($i==0){?>
                                                            <?php if(count($cashbackofferproductdata)>1){ ?>
                                                                <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeproduct(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                            <?php }else { ?>
                                                                <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                            <?php } ?>

                                                        <? }else if($i!=0) { ?>
                                                            <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeproduct(<?=($i+1)?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                        <? } ?>
                                                        <button type="button" class="btn btn-default btn-raised btn-sm remove_btn" onclick="removeproduct(<?=($i+1)?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                                    
                                                        <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> 
                                                    </div>
                                                    <script type="text/javascript">
                                                        $(document).ready(function() {
                                                            $("#productid<?=$i+1?>").val(<?=$cashbackofferproductdata[$i]['productid']?>).selectpicker('refresh');
                                                            getproductprice(<?=$i+1?>);
                                                            $("#priceid<?=$i+1?>").val(<?=$cashbackofferproductdata[$i]['priceid']?>);
                                                            $("#priceid<?=$i+1?>").selectpicker('refresh');
                                                        });
                                                    </script>
                                                </div>
                                            <?php } ?>
                                        <?php }else{ ?>
                                            <div class="col-sm-12 countproducts" id="countproducts1">
                                                <input type="hidden" name="uniqueproduct[]" id="uniqueproduct1">
                                                <div class="col-sm-4 pl-sm pr-sm">
                                                    <div class="form-group" id="product1_div">
                                                        <div class="col-sm-12">
                                                            <select id="productid1" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="1">
                                                                <option value="0">Select Product</option>
                                                                <?=$PRODUCT_DATA?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 pl-sm pr-sm">
                                                    <div class="form-group" id="price1_div">
                                                        <div class="col-md-12">
                                                            <select id="priceid1" name="priceid[]" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                                <option value="0">Select Variant</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2 pl-sm pr-sm">
                                                    <div class="form-group" id="earnpoints1_div">
                                                        <div class="col-md-12">
                                                            <input type="text" class="form-control earnpoints" id="earnpoints1" name="earnpoints[]" value="" maxlength="4" onkeypress="return isNumber(event);" div-id="1">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 form-group m-n p-sm pt-md">	
                                                    <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeproduct(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>
                                                    <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-md-12">
                                            <div class="form-group text-center">
                                                <label for="focusedinput" class="col-sm-5 col-xs-4 control-label">Activate</label>
                                                <div class="col-sm-6 col-xs-8">
                                                <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                                    <div class="radio">
                                                    <input type="radio" name="status" id="yes" value="1" <?php if(isset($cashbackofferdata) && $cashbackofferdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                    <label for="yes">Yes</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2 col-xs-6">
                                                    <div class="radio">
                                                    <input type="radio" name="status" id="no" value="0" <?php if(isset($cashbackofferdata) && $cashbackofferdata['status']==0){ echo 'checked'; }?>>
                                                    <label for="no">No</label>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="form-actions text-center">
                                                <?php if(!empty($cashbackofferdata)){ ?>
                                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                                <?php }else{ ?>
                                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                                <?php } ?>
                                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
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
    </div>
</div>