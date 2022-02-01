<?php    $productdiscount = 0; ?><?php 


   $DOCUMENT_TYPE_DATA = '';
   if(!empty($documenttypedata)){
   foreach($documenttypedata as $documenttype){
       $DOCUMENT_TYPE_DATA .= '<option value="'.$documenttype['id'].' " >'.$documenttype['documenttype'].'</option>';
   } 
   }
   $PRODUCT_CETEGORY_DATA = '';

   if(!empty($categorydorpdowndata)){
      foreach($categorydorpdowndata as $categorydorpdown){
         $PRODUCT_CETEGORY_DATA .= '<option value="'.$categorydorpdown['id'].'">'.$categorydorpdown['name'].'</option>';
      } 
   }
   $PRODUCT_DORPDOWN_DATA = '';
 
   // echo '<pre>';
   // print_r($productdorpdowndata);
   // exit;
   if(!empty($productdorpdowndata)){
      foreach($productdorpdowndata as $productdorpdown){
         $PRODUCT_DORPDOWN_DATA .= '<option value="'.$productdorpdown['id'].'">'.$productdorpdown['name'].'</option>';
      } 
   }
  
   $LICENCE_TYPE_DATA = '';
   if(!empty($this->Licencetype)){
   foreach($this->Licencetype as $k=>$val){
       $LICENCE_TYPE_DATA .= '<option value="'.$k.'">'.$val.'</option>';
   } 
   }
   $cloop=1;
   ?>
<script>
   var DOCUMENT_TYPE_DATA = '<?=$DOCUMENT_TYPE_DATA?>';
   var LICENCE_TYPE_DATA = '<?=$LICENCE_TYPE_DATA?>';
   var PRODUCT_CETEGORY_DATA = '<?=$PRODUCT_CETEGORY_DATA?>';
   var PRODUCT_DORPDOWN_DATA = '<?=$PRODUCT_DORPDOWN_DATA?>';
   
  
</script>
<style>
   .panel-style {
   box-shadow: 0px 1px 6px #333 !important;
   margin-bottom: 20px;
   }
   .tal{
   text-align: left !important;
   };
</style>
<div class="page-content">
<div class="page-heading">
   <h1><?php if (isset($partydata)) { echo 'Edit'; } else { echo 'Add'; } ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></h1>
   <small>
      <ol class="breadcrumb">
         <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
         <li><a href="javascript:void(0)"><?= $this->session->userdata(base_url() . 'mainmenuname') ?></a></li>
         <li><a href="<?php echo ADMIN_URL . $this->session->userdata(base_url() . 'submenuurl') ?>"><?= $this->session->userdata(base_url() . 'submenuname') ?></a>
         </li>
         <li class="active"><?php if (isset($partydata)) { echo 'Edit';} else { echo 'Add'; } ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></li>
      </ol>
   </small>
</div>
<div class="container-fluid">
<div data-widget-group="group1">
   <form class="form-horizontal" id="party-form" enctype="multipart/form-data">
      <input id="sid" type="hidden" name="sid" class="form-control" value="<?php if(isset($salesorderdata)) { echo $salesorderdata['id']; } ?>">
      <input id="base_url" type="hidden" value="<?=base_url()?>">
      <div class="panel panel-default border-panel">
         <div class="panel-body pt-xs">
            <div class="row">
               <div class="col-md-6">
                  <div class="form-group" id="party_div">
                     <label for="party" class="col-md-4 control-label">Party <span class="mandatoryfield">*</span></label>
                     <div class="col-md-7">
                        <select id="party" name="party" class="selectpicker form-control" data-live-search="true" data-size="5">
                           <option value="0">Select Party</option>
                           <?php foreach ($Partydorpdowndata as $Party) { ?>
                           <option value="<?php echo $Party->id; ?>" <?php if (isset($salesorderdata)) {if ($salesorderdata['partyid'] == $Party->id) {echo 'selected';}}?>
                              ><?php echo $Party->name; ?>
                           </option>
                           <?php } ?>
                        </select>
                     </div>
                     <div class="col-md-1 p-n" style="padding-top: 5px !important;">
                        <a href="javascript:void(0)" onclick="addcountry()" class="btn btn-primary btn-raised p-xs"><i class="material-icons" title="Add Unit">add</i></a>
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group" id="pono_div">
                     <label class="col-md-4 col-sm-4 control-label" for="gst">Client PO No<span class="mandatoryfield"></span></label>
                     <div class="col-md-8 col-sm-8">
                        <input type="text" id="pono" class="form-control" name="pono" value="<?php if(isset($salesorderdata)){ echo $salesorderdata['clientpono']; }  ?>">
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group" id="inquiryno_div">
                     <label class="col-md-4 col-sm-4 control-label" for="inquiryno">Inquiry No<span class="mandatoryfield"></span></label>
                     <div class="col-md-8 col-sm-8">
                        <input type="text" id="inquiryno" class="form-control" name="inquiryno" value="<?php if(isset($salesorderdata)){ echo $salesorderdata['inquiryno']; }  ?>">
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group" id="podate_div">
                     <label for="podate" class="col-md-4 col-sm-4 control-label">Po Date<span class="mandatoryfield"></span></label>
                     <div class="col-md-8">
                        <input id="podate" type="text" name="podate" value="<?php if(isset($salesorderdata)){ echo $salesorderdata['podate']; } ?>" class="form-control date" readonly>
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group" id="orderno_div">
                     <label class="col-md-4 col-sm-4 control-label" for="orderno">Order No<span class="mandatoryfield"></span></label>
                     <div class="col-md-8 col-sm-8">
                        <input type="text" id="orderno" class="form-control" name="orderno" value="<?php if(isset($salesorderdata)){ echo $salesorderdata['orderno']; }  ?>">
                     </div>
                  </div>
               </div>
               <div class="clearfix"></div>
            </div>
            <div class="row">
              
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div>
                        <div id="quotationproductdivs">
                        <table id="quotationproducttable" class="table table-hover table-bordered m-n">
                                <thead>
                                    <tr>
                                        <th>Select Category  <span class="mandatoryfield">*</span></th>
                                        <th>Select product <span class="mandatoryfield">*</span></th>
                                        <th class="width12">Price <span class="mandatoryfield">*</span></th>
                                        <th class="width8">Qty <span class="mandatoryfield">*</span></th>
                                        <th class="width8" >Discount</th>
                                        <th class="text-right width8">Amount (<?=CURRENCY_CODE?>)</th>
                                        <th class="width8">Action</th>
                                    </tr>
                                </thead>      
                              
                                <tbody id="productdataforpurchase">
                                    <?php if(!empty($productdata)) { ?>
                                        <input type="hidden" name="removequotationproductid" id="removequotationproductid">
                                       
                                        <?php 
                                  
                                        $count=count($productdata); 
                                        for ($i=0; $i < $count; $i++) { ?>
                                            <tr class="countproducts" id="quotationproductdiv<?=($i+1)?>">
                                                <td>
                                                   
                                                    <input type="hidden" name="referencetype[]" id="referencetype<?=$i+1?>" value="">
                                                   
                                                    <div class="form-group" id="product<?=($i+1)?>_div">
                                                        <div class="col-sm-12">
                                                            <select id="cetegory<?=($i+1)?>" name="cetegory[]" data-width="90%" class="selectpicker form-control cetegory" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                                                <option value="0">Select Cetegory</option>
                                                                  <?php foreach($categorydorpdowndata as $categorydorpdown){ ?>
                                                                  <option value="<?php echo $categorydorpdown['id']; ?>" 
                                                                  <?php if($productdata[$i]->categoryid==$categorydorpdown['id'])
                                                                  { echo "selected"; } ?>><?php echo $categorydorpdown['name'];?>
                                                                  </option>
                                                                  <?php }  ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" id="product<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                            <select id="product<?=($i+1)?>" onchange="getprice($i+1)"name="product<?=($i+1)?>[]" data-width="90%" class="selectpicker form-control priceid productdropdowan" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                                <option value="">Select Product</option>
                                                                <?php foreach($productdorpdowndata as $productdorpdown){ ?>
                                                                  <option value="<?php echo $productdorpdown['id']; ?>" 
                                                                  <?php if($productdata[$i]->productid==$productdorpdown['id'])
                                                                  { echo "selected"; } ?>><?php echo $productdorpdown['name'];?>
                                                                  </option>
                                                                  <?php }  ?>
                                                            </select>
                                                           
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" id="price<?=($i+1)?>_div">
                                                        <div class="col-sm-12">
                                                            <select id="price<?=($i+1)?>" name="price<?=($i+1)?>[]" data-width="150px"  class="selectpicker form-control price" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                                <option value="">Price</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" id="actualprice<?=($i+1)?>_div">
                                                        <div class="col-sm-12">
                                                            <label for="actualprice<?=($i+1)?>" class="control-label">Rate (<?=CURRENCY_CODE?>)</label>
                                                            <input type="text" class="form-control actualprice text-right" id="actualprice<?=($i+1)?>" name="actualprice[]" value="<?php echo $productdata[$i]->price;?>" onkeypress="return decimal_number_validation(event, this.value);" style="display: block;" div-id="<?=($i+1)?>">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" id="qty<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                            <input type="text" class="form-control qty" id="qty<?=($i+1)?>" name="qty<?=($i+1)?>[]" value="<?=$productdata[$i]->qty?>" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="<?=($i+1)?>">
                                                        </div>
                                                    </div>
                                                </td>
                                              
                                                <td>
                                                    <div class="form-group" id="tax<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                            <input type="text" class="form-control text-right tax" id="tax<?=($i+1)?>" name="tax[]" value="<?php //$quotationdata['quotationproduct'][$i]['tax']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)" >	
                                                            <input type="hidden" value="<?php //$quotationdata['quotationproduct'][$i]['tax']?>" id="ordertax<?=$i+1?>">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" id="amount<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                        <input type="text" class="form-control amounttprice" id="amount<?=($i+1)?>" name="amount[]" value="<?=$productdata[$i]->amount?>"  div-id="<?=($i+1)?>">
                                                       
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group pt-sm">
                                                        <div class="col-md-12 pr-n">
                                                            <?php if($i==0){?>
                                                            <?php // if(count($quotationdata['quotationproduct'])>1){ ?>
                                                                <button type="button" class="btn btn-default btn-raised  add_remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                            <?php// }else { ?>
                                                                <button type="button" class="btn btn-default btn-raised  add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                            <?php// } ?>

                                                        <?php }else if($i!=0) { ?>
                                                            <button type="button" class="btn btn-default btn-raised  add_remove_btn_product" onclick="removeproduct(<?=$i+1?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                        <?php } ?>
                                                        <button type="button" class="btn btn-default btn-raised btn-sm add_remove_btn_product" onclick="removeproduct(<?=$i+1?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                                    
                                                      
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <script type="text/javascript">
                                                $(document).ready(function() {
                                           

                                                    $("#qty<?=$i+1?>").TouchSpin(touchspinoptions);
                                                   //  getproduct(<?=$i+1?>);
                                                   //  getproductprice(<?=$i+1?>);
                                                   //  getmultiplepricebypriceid(<?=$i+1?>);
                                                   //  calculatediscount(<?=$i+1?>);
                                                   //  changeproductamount(<?=$i+1?>);
                                                });
                                            </script>
                                        <?php } ?>
                                    <?php }else{ ?>
                                        <tr class="countproducts" id="quotationproductdiv1">
                                            <td>
                                                <input type="hidden" name="producttax[]" id="producttax1">
                                                <input type="hidden" name="productrate[]" id="productrate1">
                                                <input type="hidden" name="originalprice[]" id="originalprice1">
                                                <input type="hidden" name="uniqueproduct[]" id="uniqueproduct1">
                                                <input type="hidden" name="referencetype[]" id="referencetype1">
                                                <div class="form-group" id="cetegory1_div">
                                                    <div class="col-sm-12">
                                                        <select id="cetegory1" name="cetegory[]" data-width="90%" class="selectpicker form-control cetegory" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="1">
                                                            <option value="0">Select Cetegory</option>
                                                            <?=$PRODUCT_CETEGORY_DATA?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="product1_div">
                                                    <div class="col-md-12">
                                                        <select id="product1" name="product[]" data-width="90%" onchange="getprice(1)" class="selectpicker form-control product" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                            <option value="">Select Product</option>
                                                            <?=$PRODUCT_DORPDOWN_DATA?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="price1_div">
                                                    <div class="col-sm-12">
                                                        <select id="price1" name="price[]" data-width="150px" class="selectpicker form-control price1" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                            <option value="">Price</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="actualprice1_div">
                                                    <div class="col-sm-12">
                                                        <label for="actualprice1" class="control-label">Rate (<?=CURRENCY_CODE?>)</label>
                                                        <input type="text" class="form-control actualprice text-right" id="actualprice1" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value)" style="display: block;" div-id="1">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="qty1_div">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control qty" id="qty1" name="qty[]" value="" maxlength="6" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="1">
                                                    </div>
                                                </div>
                                            </td>
                                            <td <?php if($productdiscount==0){ echo "style='display:none;'"; }?>>
                                                <div class="form-group" id="discount1_div">
                                                    <div class="col-md-12">
                                                        <label for="discount1" class="control-label">Dis. (%)</label>
                                                        <input type="text" class="form-control discount" id="discount1" name="discount[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)">
                                                        <input type="hidden" value="" id="orderdiscount1">
                                                    </div>
                                                </div>
                                                <div class="form-group" id="discountinrs1_div">
                                                    <div class="col-md-12">
                                                        <label for="discountinrs1" class="control-label">Dis. (<?=CURRENCY_CODE?>)</label>
                                                        <input type="text" class="form-control discount" id="discount1" name="discount[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)">
                                                        <input type="hidden" value="" id="orderdiscount1">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="discount1_div">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control text-right discount" id="discount1" name="discount[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)"  >	
                                                        <!-- onclick="countamount()" -->
                                                        <input type="hidden" value="" id="orderdiscount1">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="amount1_div">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control amounttprice" id="amount1" name="amount[]" value="" div-id="1">	
                                                        <input type="hidden" class="producttaxamount" id="producttaxamount1" name="producttaxamount[]" value="" div-id="1">		
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group pt-sm">
                                                    <div class="col-md-12 pr-n">
                                                    <button type="button" class="btn btn-default btn-raised  add_remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>		               
                                                <button type="button" class="btn btn-default btn-raised  add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                        </table>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div>
               
            </div>

            <div class="col-sm-12 p-n">
                            <hr>
                            <div class="panel-heading p-n"><h2>Upload Documents</h2></div>
                            <div class="row m-n">
                                <div class="col-md-6 p-n" id="filesheading1"> 
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <div class="col-md-12 pl-n">
                                                <label class="control-label">Select File</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Document Name</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 p-n" id="filesheading2" style="<?php if(!empty($documentdata) && count($documentdata)>=1) { echo "display:block;"; }else{ echo "display:none;"; }?>"> 
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <div class="col-md-12 pl-n">
                                                <label class="control-label">Select File</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Remarks</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if(!empty($documentdata) ) { ?>
                                <input type="hidden" name="removetransactionattachmentid" id="removetransactionattachmentid">
                                <?php for ($i=0; $i < count($documentdata); $i++) { ?>
                                    <div class="col-md-6 p-n countfiles" id="countfiles<?=$i+1?>">
                                        <input type="hidden" name="transactionattachmentid<?=$i+1?>" value="<?=$documentdata[$i]->id?>" id="transactionattachmentid<?=$i+1?>">
                                        <div class="col-md-7">
                                            <div class="form-group" id="file<?=$i+1?>_div">
                                                <div class="col-md-12 pl-n">
                                                    <div class="input-group" id="fileupload<?=$i+1?>">
                                                        <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                            <span class="btn btn-primary btn-raised btn-file"><i
                                                                    class="fa fa-upload"></i>
                                                                <input type="file" name="file<?=$i+1?>"
                                                                    class="file" id="file<?=$i+1?>"
                                                                    accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png,.doc,.docx,.pdf" onchange="validattachmentfile($(this),'file<?=$i+1?>',this)">
                                                            </span>
                                                        </span>
                                                        <input type="text" readonly="" id="Filetext<?=$i+1?>"
                                                            class="form-control" name="Filetext[]" value="<?=$documentdata[$i]->documentfile?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" id="fileremarks<?=$i+1?>_div">
                                                <input type="text" class="form-control" name="fileremarks<?=$i+1?>" id="fileremarks<?=$i+1?>" value="<?=$documentdata[$i]->documentname?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2 pl-sm pr-sm mt-md">
                                            <?php if($i==0){?>
                                                <?php if(count($documentdata)>1){ ?>
                                                    <button type="button" class="btn btn-default btn-raised remove_file_btn" onclick="removeattachfile(1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                <?php }else { ?>
                                                    <button type="button" class="btn btn-default btn-raised add_file_btn" onclick="addattachfile()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                <?php } ?>

                                            <?php }else if($i!=0) { ?>
                                                <button type="button" class="btn btn-default btn-raised remove_file_btn" onclick="removeattachfile(<?=$i+1?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                            <?php } ?>
                                            <button type="button" class="btn btn-default btn-raised btn-sm remove_file_btn" onclick="removeattachfile(<?=$i+1?>)"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                        
                                            <button type="button" class="btn btn-default btn-raised add_file_btn" onclick="addattachfile()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button> 
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php }else{ ?>
                                <div class="col-md-6 p-n countfiles" id="countfiles1"> 
                                    <div class="col-md-7">
                                        <div class="form-group" id="file1_div">
                                            <div class="col-md-12 pl-n">
                                                <div class="input-group" id="fileupload1">
                                                    <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                        <span class="btn btn-primary btn-raised btn-file"><i
                                                                class="fa fa-upload"></i>
                                                            <input type="file" name="file1"
                                                                class="file" id="file1"
                                                                accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png,.doc,.docx,.pdf" onchange="validattachmentfile($(this),'file1',this)">
                                                        </span>
                                                    </span>
                                                    <input type="text" readonly="" id="Filetext1"
                                                        class="form-control" name="Filetext[]" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" id="fileremarks1_div">
                                            <input type="text" class="form-control" name="fileremarks1" id="fileremarks1" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-2 pl-sm pr-sm mt-md">
                                        <button type="button" class="btn btn-default btn-raised remove_file_btn m-n" onclick="removeattachfile(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>

                                        <button type="button" class="btn btn-default btn-raised add_file_btn m-n" onclick="addattachfile()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

            <div class="row">
               <div class="col-md-12 col-xs-12">
                  <div class="panel panel-default border-panel">
                     <div class="panel-heading">
                        <h2>Actions</h2>
                     </div>
                     <div class="panel-body">
                        <div class="row">
                           <div class="form-group text-center">
                              <div class="col-md-12 col-xs-12">
                                 <?php if (!empty($partydata)) { ?>
                                 <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                 <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                                 <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                 <?php } else { ?>
                                 <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                 <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                                 <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                 <?php } ?>
                                 <a class="<?= cancellink_class; ?>" href="<?= ADMIN_URL.$this->session->userdata(base_url() . 'submenuurl')?>" title=<?= cancellink_title ?>><?= cancellink_text ?></a>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <input type="hidden" name="edit_country" id="edit_country" value="<?php if (isset($partydata)) { echo $partydata['countryid']; } ?>">
            <input type="hidden" name="edit_provinceid" id="edit_provinceid" value="<?php if (isset($partydata)) { echo $partydata['provinceid']; } ?>">
            <input type="hidden" name="edit_cityid" id="edit_cityid" value="<?php if (isset($partydata)) { echo $partydata['cityid']; } ?>">
   </form>
   </div>
   </div>
   <!-- model code -->
   <div class="modal addunit" id="addcompanyModal" style="overflow-y: auto;">
      <div class="modal-dialog" role="document" style="width: 600px;">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
               <h4 class="modal-title" id="post_title">Add Company</h4>
            </div>
            <div class="modal-body no-padding"></div>
         </div>
      </div>
   </div>
   <!-- model code -->
   <!-- model code -->
   <div class="modal addunit" id="addpartytypeModal" style="overflow-y: auto;">
      <div class="modal-dialog" role="document" style="width: 600px;">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
               <h4 class="modal-title" id="post_title">Add Party Type</h4>
            </div>
            <div class="modal-body2 no-padding"></div>
         </div>
      </div>
   </div>
   <!-- model code -->
</div>
<script>
   function addcountry() {
      document.getElementById("test_id").onkeyup = function() {
      var input = parseInt(this.value);
      if (input < 0 || input > 100)
         console.log("Value should be between 0 - 100");
      return;
      }
   }
   function addcountry() {
      var uurl = SITE_URL + "Company/addcompanymodal";
      $.ajax({
         url: uurl,
         type: 'POST',
         //async: false,
         beforeSend: function() {
            $('.mask').show();
            $('#loader').show();
         },
         success: function(response) {
            $("#addcompanyModal").modal("show");
            $(".modal-body").html(response);
            include('<?=ADMIN_JS_URL?>pages/Add_company.js', function() {
            });
         },
         error: function(xhr) {
            //alert(xhr.responseText);
         },
         complete: function() {
            $('.mask').hide();
            $('#loader').hide();
         },
   
      });
   }
   function addpartytype() {
      var uurl = SITE_URL + "Party_type/addpartytypemodal";
      $.ajax({
         url: uurl,
         type: 'POST',
         //async: false,
         beforeSend: function() {
            $('.mask').show();
            $('#loader').show();
         },
         success: function(response) {
            $("#addpartytypeModal").modal("show");
            $(".modal-body2").html(response);
            include('<?=ADMIN_JS_URL?>pages/add_party_type.js', function() {
            });
         },
         error: function(xhr) {
            //alert(xhr.responseText);
         },
         complete: function() {
            $('.mask').hide();
            $('#loader').hide();
         },
   
      });
   }


   function getprice(id){
      alert(pid);
      var pid = $("#price" + id).val();
      var uurl = SITE_URL + "Sales-order/Productpricesdorpdowndata/"+pid;
      $.ajax({
         url: uurl,
         type: 'POST',
         dataType: 'json',
         beforeSend: function() {
            // $('.mask').show();
            // $('#loader').show();
         },
         success: function(response) {
            alert(response);
            console.log(response);
            var option = ' <option value="0">Select Price</option>';
            $.each(response, function (index, data) {
               option += '<option value="' + data['id'] + '">' + data['price'] + '</option>';
            });
            $('#price'+id).html(option);
            $(".selectpicker").selectpicker("refresh");
         },
         error: function(xhr) {
            //alert(xhr.responseText);
         },
         complete: function() {
            $('.mask').hide();
            $('#loader').hide();
         },
      });
   }


   $(document).ready(function() {
      $('#checkbox1').on('change', function() {
         var checked = this.checked
         
         if(checked==true){
            var billingaddress = $('#billingaddress').val();
            var shippingaddress = $('#shippingaddress').val(billingaddress);
           
         }else if(checked==false){
            $('#shippingaddress').val('');
         }
         
      });
      $('#checkbox2').on('change', function() {
         var checked = this.checked
         
         if(checked==true){
            var billingaddress = $('#billingaddress').val();
            $('#courieraddress').val(billingaddress);
         }else if(checked==false){
            $('#courieraddress').val('');
         }
         
      });

      $('#checkbox3').on('change', function() {
         var checked = this.checked
         if(checked==true){
            var shippingaddress = $('#shippingaddress').val();
            $('#courieraddress').val(shippingaddress);
         }else if(checked==false){
            $('#courieraddress').val('');
         }
      });
   
      $("#password_div").hide();
      $('#checkbox4').on('change', function() {
         var checked = this.checked
         if(checked==true){
            $("#password_div").show();
         }else if(checked==false){
            $("#password_div").hide();
         }
      });
   
      $('#checkbox3').on('change', function() {
         var checked = this.checked
         if(checked==true){
            $( "#checkbox2" ).prop( "checked", false );
            $( "#checkbox3" ).prop( "checked", true );
         }else if(checked==false){
            $( "#checkbox3" ).prop( "checked", false );
            
         }
      });
      $('#checkbox2').on('change', function() {
         var checked = this.checked
         if(checked==true){
            $( "#checkbox3" ).prop( "checked", false );
         }
      });
   
   
     
   });
   
</script>