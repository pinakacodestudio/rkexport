<div class="col-md-12" style="margin:30px 0px 0px -7px; width:100.3rem" id='addproductitem'>
                           <?php 
                              $cloopdoc = 0;
                              $i=0;
                              
                              if(isset($party_docdata[0]->id ) && !empty($party_docdata[0]->id ))  {
                                  foreach ($party_docdata as $row)
                                 {
                                     $i++;
                                     $cloopdoc = $cloopdoc + 1;
                                     $doc_id = $row->id;
                                     $doc=$row->doc;
                                     $docname = $row->docname;
                                 ?>
                           <!-- <div id="quotationproductdivs">
                              <table id="quotationproducttable" class="table table-hover table-bordered m-n">
                                 <thead>
                                    <tr>
                                       <th>Category<span class="mandatoryfield">*</span></th>
                                       <th class="">Product <span class="mandatoryfield">*</span></th>
                                       <th class="">Qty <span class="mandatoryfield">*</span></th>
                                       <th class="">Delivery Priarity</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php if(!empty($quotationdata) && !empty($quotationdata['quotationproduct'])) { ?>
                                    <input type="hidden" name="removequotationproductid" id="removequotationproductid">
                                    <?php for ($i=0; $i < count($quotationdata['quotationproduct']); $i++) { ?>
                                    <tr class="countproducts" id="quotationproductdiv<?=($i+1)?>">
                                       <td>
                                          <input type="hidden" name="quotationproductsid[]" value="<?=(!isset($isduplicate))?$quotationdata['quotationproduct'][$i]['id']:""?>" id="quotationproductsid<?=$i+1?>">
                                          <input type="hidden" name="producttax[]" value="<?=$quotationdata['quotationproduct'][$i]['tax']?>" id="producttax<?=$i+1?>">
                                          <input type="hidden" name="productrate[]" value="<?=$quotationdata['quotationproduct'][$i]['price']?>" id="productrate<?=$i+1?>">
                                          <input type="hidden" name="originalprice[]" value="<?=$quotationdata['quotationproduct'][$i]['originalprice']?>" id="originalprice<?=$i+1?>">
                                          <input type="hidden" name="uniqueproduct[]" value="<?=$quotationdata['quotationproduct'][$i]['productid']."_".$quotationdata['quotationproduct'][$i]['priceid']?>" id="uniqueproduct<?=$i+1?>">
                                          <input type="hidden" name="referencetype[]" id="referencetype<?=$i+1?>" value="<?=$quotationdata['quotationproduct'][$i]['referencetype']?>">
                                          <div class="form-group" id="product<?=($i+1)?>_div">
                                             <div class="col-sm-12">
                                                <select id="productid<?=($i+1)?>" name="productid[]" data-width="90%" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                                   <option value="0">Select Product</option>
                                                </select>
                                             </div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="form-group" id="price<?=($i+1)?>_div">
                                             <div class="col-md-12">
                                                <select id="priceid<?=($i+1)?>" name="priceid[]" data-width="90%" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                   <option value="">Select Variant</option>
                                                </select>
                                                <div class="form-group m-n p-n" id="applyoldprice<?=($i+1)?>_div">
                                                   <div class="col-sm-12">
                                                      <div class="checkbox pt-n pl-xs text-left">
                                                         <input id="applyoldprice<?=($i+1)?>" type="checkbox" value="0" class="checkradios applyoldprice" checked>
                                                         <?php 
                                 $oldproductrate = $quotationdata['quotationproduct'][$i]['originalprice'];
                                 /* if(PRICE==1){
                                     $oldproductrate = $quotationdata['quotationproduct'][$i]['price'];
                                 }else{
                                     $oldproductrate = $quotationdata['quotationproduct'][$i]['pricewithtax'];
                                 } */?>
                                                         <label for="applyoldprice<?=($i+1)?>" class="control-label p-n">Apply Old Quotation Price : <span id="oldpricewithtax<?=($i+1)?>"><?=$oldproductrate?></span></label>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="form-group" id="comboprice<?=($i+1)?>_div">
                                             <div class="col-sm-12">
                                                <select id="combopriceid<?=($i+1)?>" name="combopriceid[]" data-width="150px" class="selectpicker form-control combopriceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                   <option value="">Price</option>
                                                </select>
                                             </div>
                                          </div>
                                          <div class="form-group" id="actualprice<?=($i+1)?>_div">
                                             <div class="col-sm-12">
                                                <label for="actualprice<?=($i+1)?>" class="control-label">Rate (<?=CURRENCY_CODE?>)</label>
                                                <!-- <input type="text" class="form-control actualprice text-right" id="actualprice<?php //($i+1)?>" name="actualprice[]" value="<?php //$quotationdata['quotationproduct'][$i]['originalprice']?>" onkeypress="return decimal_number_validation(event, this.value, 8);" style="display: block;" div-id="<?php //($i+1)?>"> -->
                        </div>
                     </div>
                     </td>
                     <td>
                        <div class="form-group" id="qty<?=($i+1)?>_div">
                           <div class="col-md-12">
                              <input type="text" class="form-control qty" id="qty<?=($i+1)?>" name="qty[]" value="<?=$quotationdata['quotationproduct'][$i]['quantity']?>" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="<?=($i+1)?>">
                           </div>
                        </div>
                     </td>
                     <td>
                        <div class="form-group" id="discount<?=($i+1)?>_div" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>">
                           <div class="col-sm-12">
                              <input type="text" class="form-control discount" id="discount<?=($i+1)?>" name="discount[]" value="<?=$quotationdata['quotationproduct'][$i]['discount']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)">	
                              <input type="hidden" value="<?=$quotationdata['quotationproduct'][$i]['discount']?>" id="orderdiscount<?=$i+1?>">
                           </div>
                        </div>
                        <div class="form-group" id="discountinrs<?=($i+1)?>_div" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>">
                           <div class="col-sm-12">
                              <input type="text" class="form-control discountinrs" id="discountinrs<?=($i+1)?>" name="discountinrs[]" value="" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)">	
                           </div>
                        </div>
                     </td>
                     <td>
                        <div class="form-group" id="tax<?=($i+1)?>_div">
                           <div class="col-sm-12">
                              <!-- <input type="text" class="form-control text-right tax" id="tax<?=($i+1)?>" name="tax[]" value="<?php //$quotationdata['quotationproduct'][$i]['tax']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)" <?php 
                                 //if($quotationdata['quotationdetail']['memberedittaxrate']==1 && EDITTAXRATE==1){ 
                                 // echo ""; 
                                 //}else{ 
                                 // echo "readonly"; 
                                 // }?>>	
                                 <input type="hidden" value="<?php //$quotationdata['quotationproduct'][$i]['tax']?>" id="ordertax<?php //$i+1?>"> -->
                           </div>
                        </div>
                     </td>
                     <td>
                        <div class="form-group" id="amount<?=($i+1)?>_div">
                           <div class="col-sm-12">
                              <input type="text" class="form-control amounttprice" id="amount<?=($i+1)?>" name="amount[]" value="" readonly="" div-id="<?=($i+1)?>">
                              <input type="hidden" class="producttaxamount" id="producttaxamount<?=($i+1)?>" name="producttaxamount[]" value="" div-id="<?=($i+1)?>">		
                              <span class="material-input"></span>
                           </div>
                        </div>
                     </td>
                     <td>
                        <div class="form-group pt-sm">
                           <div class="col-sm-12 pr-n">
                              <?php if($i==0){?>
                              <?php if(count($quotationdata['quotationproduct'])>1){ ?>
                              <button type="button" class="btn btn-default btn-raised  add_remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                              <?php }else { ?>
                              <button type="button" class="btn btn-default btn-raised  add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                              <?php } ?>
                              <?php }else if($i!=0) { ?>
                              <button type="button" class="btn btn-default btn-raised  add_remove_btn_product" onclick="removeproduct(<?=$i+1?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                              <?php } ?>
                              <button type="button" class="btn btn-default btn-raised btn-sm add_remove_btn_product" onclick="removeproduct(<?=$i+1?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-default btn-raised  add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>  
                           </div>
                        </div>
                     </td>
                     </tr>
                     <script type="text/javascript">
                        $(document).ready(function() {
                            oldproductid.push(<?=$quotationdata['quotationproduct'][$i]['productid']?>);
                            oldpriceid.push(<?=$quotationdata['quotationproduct'][$i]['priceid']?>);
                            oldtax.push(<?=$quotationdata['quotationproduct'][$i]['tax']?>);
                            productdiscount.push(<?=$quotationdata['quotationproduct'][$i]['discount']?>);
                            oldcombopriceid.push(<?=$quotationdata['quotationproduct'][$i]['referenceid']?>);
                            oldprice.push(<?=$quotationdata['quotationproduct'][$i]['originalprice']?>);
                        
                            $("#qty<?=$i+1?>").TouchSpin(touchspinoptions);
                            getproduct(<?=$i+1?>);
                            getproductprice(<?=$i+1?>);
                            getmultiplepricebypriceid(<?=$i+1?>);
                            calculatediscount(<?=$i+1?>);
                            changeproductamount(<?=$i+1?>);
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
                           <div class="form-group" id="product1_div">
                              <div class="col-sm-12">
                                 <select id="productid1" name="productid[]" data-width="90%" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                    <option value="0">Select Product</option>
                                 </select>
                              </div>
                           </div>
                        </td>
                        <!-- <td>
                           <div class="form-group" id="price1_div">
                               <div class="col-md-12">
                                   <select id="priceid1" name="priceid[]" data-width="90%" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                       <option value="">Select Variant</option>
                                   </select>
                               </div>
                           </div>
                           </td> -->
                        <td>
                           <div class="form-group" id="comboprice1_div">
                              <div class="col-sm-12">
                                 <select id="combopriceid1" name="combopriceid[]" data-width="150px" class="selectpicker form-control combopriceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                    <option value="">Price</option>
                                 </select>
                              </div>
                           </div>
                           <!-- <div class="form-group" id="actualprice1_div">
                              <div class="col-sm-12">
                                  <label for="actualprice1" class="control-label">Rate (<?=CURRENCY_CODE?>)</label>
                                  <input type="text" class="form-control actualprice text-right" id="actualprice1" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value, 8)" style="display: block;" div-id="1">
                              </div>
                              </div> -->
                        </td>
                        <!-- <td>
                           <div class="form-group" id="qty1_div">
                               <div class="col-md-12">
                                   <input type="text" class="form-control qty" id="qty1" name="qty[]" value="" onkeypress="<?php //(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="1">
                               </div>
                           </div>
                           </td> -->
                        <!-- <td <?php //if(PRODUCTDISCOUNT==0){ echo "style='display:none;'"; } ?>>
                           <div class="form-group" id="discount1_div">
                               <div class="col-md-12">
                                   <label for="discount1" class="control-label">Dis. (%)</label>
                                   <input type="text" class="form-control discount" id="discount1" name="discount[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)">	
                                   <input type="hidden" value="" id="orderdiscount1">
                               </div>
                           </div>
                           <div class="form-group" id="discountinrs1_div"> 
                               <div class="col-md-12">
                                   <label for="discountinrs1" class="control-label">Dis. (<?php //CURRENCY_CODE?>)</label>
                                   <input type="text" class="form-control discountinrs" id="discountinrs1" name="discountinrs[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)">	
                               </div>
                           </div>
                           </td> -->
                        <!-- <td>
                           <div class="form-group" id="tax1_div"> 
                               <div class="col-md-12">
                                   <input type="text" class="form-control text-right tax" id="tax1" name="tax[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)" readonly>	
                                   <input type="hidden" value="" id="ordertax1">
                               </div>
                           </div>
                           </td> -->
                        <td>
                           <div class="form-group" id="amount1_div">
                              <div class="col-md-12">
                                 <input type="text" class="form-control amounttprice" id="amount1" name="amount[]" value=""  div-id="1">	
                                 <input type="hidden" class="producttaxamount" id="producttaxamount1" name="producttaxamount[]" value="" div-id="1">		
                              </div>
                           </div>
                        </td>
                        <td>
                           <div class="form-group">
                              <div class="col-md-12 pr-n">
                                 <!-- <button type="button" class="btn btn-default btn-raised  add_remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>		                -->
                                 <div class="col-md-12 pr-n">
                                    <div class="form-group" id="deliverypriority_div">
                                       <div class="col-md-9">
                                          <?php
                                             $selectedpriority = 1;
                                             if(!empty($quotationdata['quotationdetail'])){
                                                 $selectedpriority = $quotationdata['quotationdetail']['deliverypriority'];
                                             } 
                                             ?>
                                          <select id="deliverypriority" name="deliverypriority" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                             <option value="1" <?=($selectedpriority==1)?"selected":""?>>Medium</option>
                                             <option value="2" <?=($selectedpriority==2)?"selected":""?>>High</option>
                                             <option value="3" <?=($selectedpriority==3)?"selected":""?>>Low</option>
                                             <option value="4" <?=($selectedpriority==4)?"selected":""?>>Urgent</option>
                                          </select>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </td>
                     </tr>
                     <?php } ?>
                     </tbody>
                     </table>
                  </div>
                  <?php
                     }
                     }else {
                         $count = 1;
                         $cloopdoc = 0;
                         while ($count > $cloopdoc) {
                             $cloopdoc = $cloopdoc + 1;
                     ?>
                  <div id="quotationproductdivs mb-5">
                     <table id="quotationproducttable" class="panel panel-default border-panel table table-hover table-bordered m-n">
                        <thead>
                           <tr>
                              <th>Category<span class="mandatoryfield">*</span></th>
                              <th class="">Product <span class="mandatoryfield">*</span></th>
                              <th class="">Qty <span class="mandatoryfield">*</span></th>
                              <th class="">Discount( % ) <span class="mandatoryfield">*</span></th>
                              <th class="">Amount <span class="mandatoryfield">*</span></th>
                              <th class="">Delivery Priarity</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php if(!empty($quotationdata) && !empty($quotationdata['quotationproduct'])) { ?>
                           <input type="hidden" name="removequotationproductid" id="removequotationproductid">
                           <?php for ($i=0; $i < count($quotationdata['quotationproduct']); $i++) { ?>
                           <tr class="countproducts" id="quotationproductdiv<?=($i+1)?>">
                              <td>
                                 <input type="hidden" name="quotationproductsid[]" value="<?=(!isset($isduplicate))?$quotationdata['quotationproduct'][$i]['id']:""?>" id="quotationproductsid<?=$i+1?>">
                                 <input type="hidden" name="producttax[]" value="<?=$quotationdata['quotationproduct'][$i]['tax']?>" id="producttax<?=$i+1?>">
                                 <input type="hidden" name="productrate[]" value="<?=$quotationdata['quotationproduct'][$i]['price']?>" id="productrate<?=$i+1?>">
                                 <input type="hidden" name="originalprice[]" value="<?=$quotationdata['quotationproduct'][$i]['originalprice']?>" id="originalprice<?=$i+1?>">
                                 <input type="hidden" name="uniqueproduct[]" value="<?=$quotationdata['quotationproduct'][$i]['productid']."_".$quotationdata['quotationproduct'][$i]['priceid']?>" id="uniqueproduct<?=$i+1?>">
                                 <input type="hidden" name="referencetype[]" id="referencetype<?=$i+1?>" value="<?=$quotationdata['quotationproduct'][$i]['referencetype']?>">
                                 <div class="form-group" id="product<?=($i+1)?>_div">
                                    <div class="col-sm-12">
                                       <select id="productid<?=($i+1)?>" name="productid[]" data-width="90%" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                          <option value="0">Select Product</option>
                                       </select>
                                    </div>
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="price<?=($i+1)?>_div">
                                    <div class="col-md-12">
                                       <select id="priceid<?=($i+1)?>" name="priceid[]" data-width="90%" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                          <option value="">Select Variant</option>
                                       </select>
                                       <div class="form-group m-n p-n" id="applyoldprice<?=($i+1)?>_div">
                                          <div class="col-sm-12">
                                             <div class="checkbox pt-n pl-xs text-left">
                                                <input id="applyoldprice<?=($i+1)?>" type="checkbox" value="0" class="checkradios applyoldprice" checked>
                                                <?php 
                                                   $oldproductrate = $quotationdata['quotationproduct'][$i]['originalprice'];
                                                   /* if(PRICE==1){
                                                       $oldproductrate = $quotationdata['quotationproduct'][$i]['price'];
                                                   }else{
                                                       $oldproductrate = $quotationdata['quotationproduct'][$i]['pricewithtax'];
                                                   } */?>
                                                <label for="applyoldprice<?=($i+1)?>" class="control-label p-n">Apply Old Quotation Price : <span id="oldpricewithtax<?=($i+1)?>"><?=$oldproductrate?></span></label>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="comboprice<?=($i+1)?>_div">
                                    <div class="col-sm-12">
                                       <select id="combopriceid<?=($i+1)?>" name="combopriceid[]" data-width="150px" class="selectpicker form-control combopriceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                          <option value="">Price</option>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="form-group" id="actualprice<?=($i+1)?>_div">
                                    <div class="col-sm-12">
                                       <label for="actualprice<?=($i+1)?>" class="control-label">Rate (<?=CURRENCY_CODE?>)</label>
                                       <!-- <input type="text" class="form-control actualprice text-right" id="actualprice<?php //($i+1)?>" name="actualprice[]" value="<?php //$quotationdata['quotationproduct'][$i]['originalprice']?>" onkeypress="return decimal_number_validation(event, this.value, 8);" style="display: block;" div-id="<?php //($i+1)?>"> -->
                                    </div>
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="qty<?=($i+1)?>_div">
                                    <div class="col-md-12">
                                       <input type="text" class="form-control qty" id="qty<?=($i+1)?>" name="qty[]" value="<?=$quotationdata['quotationproduct'][$i]['quantity']?>" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="<?=($i+1)?>">
                                    </div>
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="discount<?=($i+1)?>_div" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>">
                                    <div class="col-sm-12">
                                       <input type="text" class="form-control discount" id="discount<?=($i+1)?>" name="discount[]" value="<?=$quotationdata['quotationproduct'][$i]['discount']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)">	
                                       <input type="hidden" value="<?=$quotationdata['quotationproduct'][$i]['discount']?>" id="orderdiscount<?=$i+1?>">
                                    </div>
                                 </div>
                                 <div class="form-group" id="discountinrs<?=($i+1)?>_div" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>">
                                    <div class="col-sm-12">
                                       <input type="text" class="form-control discountinrs" id="discountinrs<?=($i+1)?>" name="discountinrs[]" value="" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)">	
                                    </div>
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="tax<?=($i+1)?>_div">
                                    <div class="col-sm-12">
                                       <!-- <input type="text" class="form-control text-right tax" id="tax<?=($i+1)?>" name="tax[]" value="<?php //$quotationdata['quotationproduct'][$i]['tax']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)" <?php 
                                          //if($quotationdata['quotationdetail']['memberedittaxrate']==1 && EDITTAXRATE==1){ 
                                          // echo ""; 
                                          //}else{ 
                                          // echo "readonly"; 
                                          // }?>>	
                                          <input type="hidden" value="<?php //$quotationdata['quotationproduct'][$i]['tax']?>" id="ordertax<?php //$i+1?>"> -->
                                    </div>
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="amount<?=($i+1)?>_div">
                                    <div class="col-sm-12">
                                       <input type="text" class="form-control amounttprice" id="amount<?=($i+1)?>" name="amount[]" value="" readonly="" div-id="<?=($i+1)?>">
                                       <input type="hidden" class="producttaxamount" id="producttaxamount<?=($i+1)?>" name="producttaxamount[]" value="" div-id="<?=($i+1)?>">		
                                       <span class="material-input"></span>
                                    </div>
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group pt-sm">
                                    <div class="col-sm-12 pr-n">
                                       <?php if($i==0){?>
                                       <?php if(count($quotationdata['quotationproduct'])>1){ ?>
                                       <button type="button" class="btn btn-default btn-raised  add_remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                       <?php }else { ?>
                                       <button type="button" class="btn btn-default btn-raised  add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                       <?php } ?>
                                       <?php }else if($i!=0) { ?>
                                       <button type="button" class="btn btn-default btn-raised  add_remove_btn_product" onclick="removeproduct(<?=$i+1?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                       <?php } ?>
                                       <button type="button" class="btn btn-default btn-raised btn-sm add_remove_btn_product" onclick="removeproduct(<?=$i+1?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                       <button type="button" class="btn btn-default btn-raised  add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>  
                                    </div>
                                 </div>
                              </td>
                           </tr>
                           <script type="text/javascript">
                              $(document).ready(function() {
                                  oldproductid.push(<?=$quotationdata['quotationproduct'][$i]['productid']?>);
                                  oldpriceid.push(<?=$quotationdata['quotationproduct'][$i]['priceid']?>);
                                  oldtax.push(<?=$quotationdata['quotationproduct'][$i]['tax']?>);
                                  productdiscount.push(<?=$quotationdata['quotationproduct'][$i]['discount']?>);
                                  oldcombopriceid.push(<?=$quotationdata['quotationproduct'][$i]['referenceid']?>);
                                  oldprice.push(<?=$quotationdata['quotationproduct'][$i]['originalprice']?>);
                              
                                  $("#qty<?=$i+1?>").TouchSpin(touchspinoptions);
                                  getproduct(<?=$i+1?>);
                                  getproductprice(<?=$i+1?>);
                                  getmultiplepricebypriceid(<?=$i+1?>);
                                  calculatediscount(<?=$i+1?>);
                                  changeproductamount(<?=$i+1?>);
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
                                 <div class="form-group" id="product1_div">
                                    <div class="col-sm-12">
                                       <select id="productid1" name="productid[]" data-width="90%" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                          <option value="0">Select Product</option>
                                       </select>
                                    </div>
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="comboprice1_div">
                                    <div class="col-sm-12">
                                       <select id="combopriceid1" name="combopriceid[]" data-width="150px" class="selectpicker form-control combopriceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                          <option value="">Price</option>
                                       </select>
                                    </div>
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="amount1_div">
                                    <div class="col-md-12">
                                       <input type="text" class="form-control amounttprice" id="amount1" name="amount[]" value=""  div-id="1">	
                                       <input type="hidden" class="producttaxamount" id="producttaxamount1" name="producttaxamount[]" value="" div-id="1">		
                                    </div>
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="amount1_div">
                                    <div class="col-md-12">
                                       <input type="text" class="form-control amounttprice" id="amount1" name="amount[]" value=""  div-id="1">	
                                       <input type="hidden" class="producttaxamount" id="producttaxamount1" name="producttaxamount[]" value="" div-id="1">		
                                    </div>
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group" id="amount1_div">
                                    <div class="col-md-12">
                                       <input type="text" class="form-control amounttprice" id="amount1" name="amount[]" value=""  div-id="1">	
                                       <input type="hidden" class="producttaxamount" id="producttaxamount1" name="producttaxamount[]" value="" div-id="1">		
                                    </div>
                                 </div>
                              </td>
                              <td>
                                 <div class="form-group">
                                    <div class="col-md-12 pr-n">
                                       <!-- <div class="col-md-12 pr-n"> -->
                                       <div class="form-group" id="deliverypriority_div">
                                          <div class="col-md-9">
                                             <?php
                                                $selectedpriority = 1;
                                                if(!empty($quotationdata['quotationdetail'])){
                                                    $selectedpriority = $quotationdata['quotationdetail']['deliverypriority'];
                                                } 
                                                ?>
                                             <select id="deliverypriority" name="deliverypriority" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                <option value="1" <?=($selectedpriority==1)?"selected":""?>>Medium</option>
                                                <option value="2" <?=($selectedpriority==2)?"selected":""?>>High</option>
                                                <option value="3" <?=($selectedpriority==3)?"selected":""?>>Low</option>
                                                <option value="4" <?=($selectedpriority==4)?"selected":""?>>Urgent</option>
                                             </select>
                                             <!-- <button type="button" style="float:right; width:3rem; height:3.5rem; margin:10px -35px 0px 0px;" class="addnewproductitem btn-primary"><i class="fa fa-plus"></i></button> -->
                                             <!-- <div class="col-md-1 p-n" style="float:right; width:3rem; height:3.5rem; margin:10px -35px 0px 0px;">
                                                <a href="javascript:void(0)" onclick="addcountry()" class="btn btn-primary btn-raised p-xs"><i class="material-icons addnewproductitem" title="Add Unit">add</i></a>
                                                </div> -->
                                          </div>
                                       </div>
                                       <!-- </div> -->
                                    </div>
                                 </div>
                              </td>
                           </tr>
                           <?php } ?>
                        </tbody>
                     </table>
                  </div>
                  <?php
                     }
                     } 
                     ?>
               </div>
               <div class="col-md-1 p-n" style="float:left; width:3rem; height:3.5rem; margin:20px 0px 0px 10px;">
                  <a href="javascript:void(0)" onclick="addcountry()" class="btn btn-primary btn-raised p-xs"><i class="material-icons addnewproductitem" title="Add Unit">add</i></a>
               </div>