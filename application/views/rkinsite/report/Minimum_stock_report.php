<div class="page-content">
    <div class="page-heading">     
      <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default border-panel">
                    <div class="panel-heading">
                        <div class="col-md-4 p-n">
                            <div class="panel-ctrls panel-tbl"></div>
                        </div>
                        <div class="col-md-2 p-n">
                            <div class="form-group" style="margin-top: -3px;">
                                <div class="col-sm-12 pr-xs pl-n">
                                    <select id="producttype" name="producttype[]" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" data-actions-box="true" title="All Product Type" multiple>
                                        <option value="0">Regular Product</option>
                                        <option value="1">Offer Product</option>
                                        <option value="2">Raw Product</option>
                                        <option value="3">Semi-Finish Product</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 p-n">
                            <div class="form-group" style="margin-top: -3px;">
                                <div class="col-sm-12 pr-xs">
                                    <select id="categoryid" name="categoryid[]" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" data-actions-box="true" title="All Category" multiple>
                                        <?php foreach($categorydata as $category){ ?>
                                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 form-group m-n pr-n" style="text-align: right;">
                            <?php if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportToExcelStockReport()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                            <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exportToPDFStockReport()" title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                            <?php } if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printStockReport()" title="<?=printbtn_title?>"><?=printbtn_text;?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel-body no-padding">
                        <table id="minimumstockreporttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>            
                                    <th class="width8">Sr. No.</th>
                                    <th>Product Name</th>
                                    <th>SKU</th>
                                    <th>Min. Stock Limit</th>
                                    <th>Current Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer"></div>
                    </div>
                </div>
            </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->


