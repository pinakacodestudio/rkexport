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
                        <form action="#" id="salesanalysisform" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-3 pl-n pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                            <label class="control-label">Date</label>
                                            <div class="input-daterange input-group" id="datepicker-range">
                                                <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php if(!empty($arrSessionDetails["salesanalysisfromdatefilter"])){ echo $arrSessionDetails["salesanalysisfromdatefilter"]; }else{ echo date("d/m/Y",strtotime("-1 month")); } ?>" placeholder="Start Date" title="Start Date" readonly/>
                                                <span class="input-group-addon">to</span>
                                                <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php if(!empty($arrSessionDetails["salesanalysistodatefilter"])){ echo $arrSessionDetails["salesanalysistodatefilter"]; }else{ echo date("d/m/Y"); } ?>" placeholder="End Date" title="End Date" readonly/>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="employee" class="control-label">Employee</label>
                                                <select id="employee" name="employee[]" class="selectpicker form-control" data-select-on-tab="true" data-size="8" data-live-search="true" title="All Employee" multiple data-actions-box="true">
                                                    <?php if(!empty($employeedata)){
                                                        foreach($employeedata as $employee){ 
                                                            $selected = "";
                                                            if(!empty($arrSessionDetails['salesanalysisemployeefilter'])){
                                                                $selected = in_array($employee['id'], explode(",", $arrSessionDetails['salesanalysisemployeefilter']))?"selected":""; 
                                                            }else if(!isset($arrSessionDetails['salesanalysisemployeefilter']) && ($employee['id']==$arrSessionDetails[base_url().'ADMINID'])){
                                                                $selected = "selected";
                                                            }
                                                            ?>
                                                            <option value="<?=$employee['id']?>" <?=$selected?>><?=ucwords($employee['name'])?></option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="product" class="control-label">Product</label>
                                                <select id="product" name="product[]" class="selectpicker form-control" data-select-on-tab="true" data-size="6" data-live-search="true" title="All Products" multiple data-actions-box="true">
                                                    <?php if(!empty($productdata)){
                                                        foreach($productdata as $product){ 
                                                            $selected = "";
                                                            if(!empty($arrSessionDetails['salesanalysisproductfilter'])){
                                                                $selected = in_array($product['id'], explode(",", $arrSessionDetails['salesanalysisproductfilter']))?"selected":""; 
                                                            }
                                                            $productname = str_replace("'","&apos;",$product['name']);
                                                            if(DROPDOWN_PRODUCT_LIST==0){ ?>

                                                                <option value="<?=$product['id']?>" <?=$selected?>><?=$productname?></option>

                                                            <?php }else{

                                                                if($product['image']!="" && file_exists(PRODUCT_PATH.$product['image'])){
                                                                    $img = $product['image'];
                                                                }else{
                                                                    $img = PRODUCTDEFAULTIMAGE;
                                                                }
                                                                ?>

                                                                <option data-content="<?php if(!empty($product['image'])){?><img src='<?=PRODUCT.$img?>' style='width:40px'> <?php } echo $productname; ?> " value="<?php echo $product['id']; ?>" <?=$selected?>><?php echo $productname; ?></option>
                                                            
                                                            <?php } ?>
                                                            
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 pl-sm pr-sm">
                                        <div class="form-group mt-xxl">
                                            <div class="col-md-12">
                                                <label class="control-label"></label>
                                                <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                            </div>
                                        </div>
                                    </div>
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
                            <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exporttoexcelsalesanalysisreport()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                            <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exporttopdfsalesanalysisreport()" title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                            <?php } if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printsalesanalysisreport()" title=<?=printbtn_title?>><?=printbtn_text?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel-body no-padding">
                        <div class="table-responsive">
                            <table id="salesanalysisreporttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
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


