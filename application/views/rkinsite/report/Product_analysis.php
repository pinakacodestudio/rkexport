<?php $arrSessionDetails = $this->session->userdata;?>
<div class="page-content">
    <div class="page-heading">     
        <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default border-panel mb-md" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
                    <div class="panel-heading filter-panel border-filter-heading">
                        <h2><?=APPLY_FILTER?></h2>
                        <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                    </div>
                    <div class="panel-body panelcollapse pt-n" style="display: none;">
                        <form action="#" id="productanalysisform" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-3 pl-n pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="countryid" class="control-label">Country</label>
                                                <select id="countryid" name="countryid[]" class="selectpicker form-control" multiple data-actions-box="true" title="All Country" data-live-search="true" data-size="8">
                                                    <?php foreach($countrydata as $countryrow){ ?>
                                                        <option value="<?php echo $countryrow['coid']; ?>"><?php echo $countryrow['countryname']; ?></option>
                                                    <?php } ?> 
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="provinceid" class="control-label">Province</label>
                                                <select id="provinceid" name="provinceid[]" class="selectpicker form-control" multiple data-actions-box="true" title="All Province" data-live-search="true" data-size="8" >
                                                    <?php foreach($statedata as $staterow){ ?>
                                                        <option value="<?php echo $staterow['sid']; ?>"><?php echo $staterow['statename']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="cityid" class="control-label">City</label>
                                                <select id="cityid" name="cityid[]" class="selectpicker form-control" multiple data-actions-box="true" title="All City" data-live-search="true" data-size="8">
                                                    <?php foreach($citydata as $cityrow){ ?>
                                                        <option value="<?php echo $cityrow['cid']; ?>"><?php echo $cityrow['cityname']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-sm pr-n">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="year" class="control-label">Year</label>
                                                <input type="text" id="year" name="year" class="form-control" value="<?php echo date("Y"); ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3 pl-n pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="month" class="control-label">Month</label>
                                                <select id="month" name="month[]" class="selectpicker form-control" multiple data-actions-box="true" title="All Month" data-size='8' data-live-search="true"> 
                                                    <?php foreach ($this->Monthwise as $monthid => $monthvalue) { ?>
                                                        <option value="<?=$monthid?>"><?=$monthvalue?></option>
                                                    <?php }?>                                   
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="employee" class="control-label">Sales Person</label>
                                                <select id="employee" name="employee[]" class="selectpicker form-control" data-select-on-tab="true" data-size="8" data-live-search="true" title="All Employee" multiple data-actions-box="true">
                                                    <?php if(!empty($employeedata)){
                                                        foreach($employeedata as $employee){ 
                                                            $selected = "";
                                                            if($employee['id']==$arrSessionDetails[base_url().'ADMINID']){
                                                                $selected = "selected";
                                                            } ?>
                                                            <option value="<?=$employee['id']?>" <?=$selected?>><?=ucwords($employee['name'])?></option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="seller" class="control-label">Seller</label>
                                                <select id="seller" name="seller[]" class="selectpicker form-control" data-select-on-tab="true" data-size="8" data-live-search="true" title="All Seller" multiple data-actions-box="true">
                                                    <option value="0">Company</option>
                                                    <?php if(!empty($sellerdata)){
                                                        foreach($sellerdata as $seller){ ?>
                                                            <option value="<?=$seller['id']?>"><?=$seller['name']?></option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-sm pr-n">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="buyer" class="control-label">Buyer</label>
                                                <select id="buyer" name="buyer[]" class="selectpicker form-control" data-select-on-tab="true" data-size="8" data-live-search="true" title="All Buyer" multiple data-actions-box="true">
                                                    <?php if(!empty($buyerdata)){
                                                        foreach($buyerdata as $buyer){ ?>
                                                            <option value="<?=$buyer['id']?>"><?=$buyer['name']?></option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-n pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="product" class="control-label">Product</label>
                                                <select id="product" name="product[]" class="selectpicker form-control" data-select-on-tab="true" data-size="6" data-live-search="true" title="All Products" multiple data-actions-box="true">
                                                    <?php if(!empty($productdata)){
                                                        foreach($productdata as $product){ 
                                                            $productname = str_replace("'","&apos;",$product['name']);
                                                            if(DROPDOWN_PRODUCT_LIST==0){ ?>

                                                                <option value="<?=$product['id']?>"><?=$productname?></option>

                                                            <?php }else{

                                                                if($product['image']!="" && file_exists(PRODUCT_PATH.$product['image'])){
                                                                    $img = $product['image'];
                                                                }else{
                                                                    $img = PRODUCTDEFAULTIMAGE;
                                                                }
                                                                ?>

                                                                <option data-content="<?php if(!empty($product['image'])){?><img src='<?=PRODUCT.$img?>' style='width:40px'> <?php } echo $productname; ?> " value="<?php echo $product['id']; ?>"><?php echo $productname; ?></option>
                                                            
                                                            <?php } ?>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 pl-xs pr-xs">
                                        <div class="form-group pt-xl">
                                            <div class="col-md-12">
                                                <label class="control-label"></label>
                                                <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    
                                </div>
                            </div> 
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-default border-panel">
                    <div class="panel-heading">
                        <div class="col-md-6">
                            <div class="panel-ctrls panel-tbl"></div>
                        </div>
                        <div class="col-md-6 form-group" style="text-align: right;">
                            <?php if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exporttoexcelproductanalysisreport()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                            <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exporttopdfproductanalysisreport()" title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                            <?php } if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printproductanalysisreport()" title=<?=printbtn_title?>><?=printbtn_text?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel-body no-padding">
                        <div class="table-responsive">
                            <table id="productanalysisreporttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            </table>
                        </div>
                    </div>
                    <div class="panel-footer"></div>
                    </div>
                </div>
            </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->


