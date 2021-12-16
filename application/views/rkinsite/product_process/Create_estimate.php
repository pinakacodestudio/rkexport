<?php 
$PRODUCT_DATA = '';
if(!empty($productdata)){
    foreach($productdata as $k=>$product){
        $productname = str_replace("'","&apos;",$product['name']);
        $json = str_replace('"', "&quot;",json_encode($product['variantdata']));
        if(DROPDOWN_PRODUCT_LIST==0){
            $PRODUCT_DATA .= '<option data-variants="'.$json.'" value="'.$product["id"].'">'.addslashes($productname).'</option>';
        }else{
            $content = "";
            if(!empty($product['image']) && file_exists(PRODUCT_PATH.$product['image'])){
                $content .= '<img src=&quot;'.PRODUCT.$product['image'].'&quot; style=&quot;width:40px;&quot;> '.addslashes($productname);
            }else{
                $content .= '<img src=&quot;'.PRODUCT.PRODUCTDEFAULTIMAGE.'&quot; style=&quot;width:40px;&quot;> '.addslashes($productname);
            }
            $PRODUCT_DATA .= '<option data-content="'.$content.'" value="'.$product['id'].'" data-variants="'.$json.'">'.addslashes($productname).'</option>';
        }
    } 
} 
$UNIT_DATA = '';
if(!empty($unitdata)){
  foreach($unitdata as $row){ 
    $UNIT_DATA .= '<option value="'.$row["id"].'">'.$row["name"].'</option>';
  } 
} ?>
<script>
    var PRODUCT_DATA = '<?=$PRODUCT_DATA?>';
    var UNIT_DATA = '<?=$UNIT_DATA?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1>Create <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active">Create <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>
    <div class="container-fluid">
                                     
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <form class="form-horizontal" id="estimateform">
                <div class="panel panel-default border-panel">
                    <div class="panel-body pt-n">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-sm-4 pl-sm pr-sm">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label class="control-label">Select Product</label>
                                            <select id="productid" name="productid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                                <option value="0">Select Product</option>
                                                <?php 
                                                if(!empty($mainproductdata)){
                                                    foreach($mainproductdata as $product){ 
                                                        $productname = str_replace("'","&apos;",$product['name']);
                                                        if(DROPDOWN_PRODUCT_LIST==0){ ?>
                        
                                                            <option value="<?php echo $product['id']; ?>"><?php echo $productname; ?></option>
                        
                                                        <?php }else{
                        
                                                            if($product['image']!="" && file_exists(PRODUCT_PATH.$product['image'])){
                                                                $img = $product['image'];
                                                            }else{
                                                                $img = PRODUCTDEFAULTIMAGE;
                                                            }
                                                            ?>
                        
                                                            <option data-content="<img src='<?=PRODUCT.$img?>' style='width:40px'> <?php echo $productname; ?> "  value="<?php echo $product['id']; ?>"><?php echo $productname; ?></option>
                                                            
                                                        <?php } ?>
                                                <?php } 
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3 pl-sm pr-sm">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label class="control-label">Select Variant</label>
                                            <select id="priceid" name="priceid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                <option value="0">Select Variant</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2 pl-sm pr-sm">
                                    <div class="form-group text-right">
                                        <div class="col-sm-12">
                                            <label class="control-label">Quantity</label>
                                            <input type="text" class="form-control text-right" id="qty" name="qty" value="" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3 pl-sm pr-sm">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label class="control-label">Select Process Group</label>
                                            <select id="processgroupid" name="processgroupid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                <option value="0">Select Process Group</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-2 pl-sm pr-sm">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label class="control-label">Select Price Type</label>
                                            <select id="pricetype" name="pricetype" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                <option value="0">FIFO Base</option>
                                                <option value="1">Avg. Price</option>
                                                <option value="2">Latest Price</option>
                                                <option value="3">Low Price</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="processgroup_maindiv"></div>
                <div class="panel panel-default border-panel" style="display:none;" id="btnpanel">
                    <div class="panel-body pt-n">
                        <div class="col-md-12 p-n">
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <input type="button" id="submit" onclick="generateestimate()" name="submit" value="Bill of Material" class="btn btn-primary btn-raised">
                                </div>
                                <div class="col-sm-6 text-right" id="exportbtn">
                                    <button class="btn btn-info btn-raised" type="button" id="saveestimatebtn" onclick="openestimatepopup()" title="Save"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Save</button>
                                    <?php if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                                    <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exporttopdfestimate()" title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                                    <?php } if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                                    <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printestimate()" title=<?=printbtn_title?>><?=printbtn_text?></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 p-n" id="totalestimate">
                            <table id="outproducttable" class="table table-striped table-bordered mb-md mt-md" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th colspan="7">Total OUT Product</th>  
                                    </tr>
                                    <tr>
                                        <th class="width8 text-center">Sr. No.</th>
                                        <th>Product Name</th> 
                                        <th>Variant Name</th> 
                                        <th>Unit</th> 
                                        <th class="text-right">Price (<?=CURRENCY_CODE?>)</th> 
                                        <th class="text-right">Quantity</th> 
                                        <th class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <table id="inproducttable" class="table table-striped table-bordered mb-sm" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th colspan="7">Total Estimate Product</th>  
                                    </tr>
                                    <tr>
                                        <th class="width8 text-center">Sr. No.</th>
                                        <th>Product Name</th> 
                                        <th>Variant Name</th> 
                                        <th class="text-right">Per Pcs Cost (<?=CURRENCY_CODE?>)</th> 
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Total Amount (<?=CURRENCY_CODE?>)</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                        </div>
                        <textarea id="productjsondata" style="display: none;"></textarea>
                    </div>
                </div>
            </form>
          </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->

<!-- Modal -->
<div class="modal fade" id="saveModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 1250000;">
    <div class="modal-dialog" role="document" style="width: 460px;">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="col-sm-9 p-n">Save Bill of Material With PDF</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></button>
            <h4 class="modal-title" id="pagetitle"></h4>
        </div>
        <div class="modal-body pt-sm pb-sm">
            <form class="form-horizontal" id="saveestimateform" name="saveestimateform">
            <div id="row">
                <div id="col-md-9">
                    <div class="form-group" id="estimatename_div">
                        <div class="col-sm-12">
                            <label for="estimatename" class="control-label">Name of Bill of Material <span class="mandatoryfield">*</span></label>
                            <input type="text" id="estimatename" name="estimatename" class="form-control" value="">
                            <p style="color: red;" id="namealert"></p>
                        </div>
                    </div>
                </div>
                <div id="col-md-12">
                <div class="form-group text-right">
                    <div class="col-sm-12">
                        <input type="button" id="submit" onclick="checkvalidationforsaveestimate()" name="submit" value="SUBMIT" class="btn btn-primary btn-raised">

                        <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger btn-raised">Close</button>
                    </div>
                </div>
                </div>
            </div>
            </form>
        </div>
        <div class="modal-footer"></div>
        </div>
    </div>
    </div>