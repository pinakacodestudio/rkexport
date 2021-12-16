<script>
    var GENERATE_QRCODE_SRC = '<?=GENERATE_QRCODE_SRC?>';
    var CURRENCY_CODE = '<?=CURRENCY_CODE?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1>QR Code</h1>            
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active">QR Code</li>
            </ol>
    </small>
    </div>

    <div class="container-fluid">
                                    
        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-9 mb-sm filtersection">
                                    <div class="col-md-5 p-n">
                                        <div class="form-group row pt-n mt-n" id="productid_div">
                                            <select id="productid" name="productid[]" class="selectpicker form-control mt-n" data-select-on-tab="true" data-size="12" data-live-search="true" data-actions-box="true" multiple title="Select Product">
                                                <?php foreach($productlist as $product){ ?>
                                                <option data-content="<?php if(!empty($product['productimage'])){?><img src='<?=PRODUCT.$product['productimage']?>' style='width:40px'> <?php } echo $product['name']; ?> "  value="<?php echo $product['id']; ?>" selected><?php echo $product['name']; ?></option>
                                                
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 pr-n">
                                        <div class="checkbox">
                                            <input id="productname" name="productname" type="checkbox" value="1" checked>
                                            <label for="productname">Product Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-1 pr-n">
                                        <div class="checkbox">
                                            <input id="productprice" name="productprice" type="checkbox" value="1" checked>
                                            <label for="productprice">Price</label>
                                        </div>
                                    </div>
                                    <div class="col-md-1 pr-n">
                                        <div class="checkbox">
                                            <input id="sku" name="sku" type="checkbox" value="1" checked>
                                            <label for="sku">SKU</label>
                                        </div>
                                    </div>
                                    <div class="col-md-1 pr-n">
                                        <div class="checkbox">
                                            <input id="variant" name="variant" type="checkbox" value="1" checked>
                                            <label for="variant">Variant</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-right mb-sm">
                                    <a class="<?=back_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=back_title?>><?=back_text?></a>
                                    <!-- <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printProductDetails()" title=<?=printbtn_title?>><?=printbtn_text?></a> -->
                                    <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exportToPDFQRCode()" title=<?=exportpdfbtn_title?>><?=exportpdfbtn_text?></a>
                                </div>
                                <div class="col-md-12" id="productdetailsdiv">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- .container-fluid -->
