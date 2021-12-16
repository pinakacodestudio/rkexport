<style>
    .productvariantdiv {
        box-shadow: 0px 1px 6px #333 !important;
        margin-bottom: 20px;
    }
</style>
<div class="page-content">
    <div class="page-heading">
        <div class="btn-group dropdown dropdown-l dropdown-breadcrumbs">
            <a class="dropdown-toggle dropdown-toggle-style" data-toggle="dropdown" aria-expanded="false"><span>
                <i class="material-icons" style="font-size: 26px;">menu</i>
            </span> </a>
            <ul class="dropdown-menu dropdown-tl" role="menu">
            <label class="mt-sm ml-sm mb-n">Menu</label>
            <?php
                $subid = $this->session->userdata(base_url().'submenuid');
                foreach($subnavtabsmenu as $row){
                if($subid == $row['id']){ ?>
                    
                    <li class="active"><a href="javascript:void(0);"><i class="fa fa-caret-right mr-sm"></i> <?=$row['name']; ?></a></li>
                
                <?php }else{ ?>
                    <li><a href="<?=base_url().ADMINFOLDER.$row['url']; ?>"><i class="fa fa-caret-right mr-sm"></i> <?=$row['name']; ?></a></li>
                <?php } 
                } ?>
            </ul>
        </div> 
        <h1><?=$title?></h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?=base_url(); ?><?=ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
                <li class="active"><?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">

        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);">
                        <div class="panel-heading">
                            <div class="col-md-8 p-n">
                                <h2 style="font-weight:600;">Product Recepie</h2>
                            </div>
                            <div class="col-md-4 pull-right text-right p-n">
                                <a class="<?=back_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=back_title?>><?=back_text?></a>
                                <a id="editBtn" class="<?=editbtn_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl').'/product-recepie-edit/'.$productrecepiedata['id']?>" title=<?=editbtn_title?>><?=editbtn_text?></a>
                            </div>
                        </div>
                        <div class="panel-body p-n">
                            <div class="row m-n">
                                <div class="col-md-8">
                                    <form class="form-horizontal">
                                        <input type="hidden" name="productrecepieid" id="productrecepieid" value="<?php if(isset($productrecepiedata)){ echo $productrecepiedata['id'];}?>">
                                        <div class="form-group">
                                            <label class="col-md-2 pl-n pr-n control-label" for="machineid">Product Name</label>
                                            <div class="col-md-6">
                                                <select id="productid" name="productid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                    <?php foreach($regularproductdata as $product){ 
                                                        
                                                        $productname = str_replace("'","&apos;",$product['name']);
                                                        if(DROPDOWN_PRODUCT_LIST==0){ ?>

                                                            <option data-isuniversal="<?php echo $product['isuniversal']; ?>" data-id="<?php echo $product['productrecepieid']; ?>" value="<?php echo $product['id']; ?>" <?php if(isset($productrecepiedata) && $productrecepiedata['productid'] == $product['id']){ echo "selected"; } ?>><?php echo $productname; ?></option>

                                                        <?php }else{

                                                            if($product['image']!="" && file_exists(PRODUCT_PATH.$product['image'])){
                                                                $img = $product['image'];
                                                            }else{
                                                                $img = PRODUCTDEFAULTIMAGE;
                                                            }
                                                            ?>

                                                            <option data-content="<img src='<?=PRODUCT.$img?>' style='width:40px'> <?php echo $productname; ?>" data-isuniversal="<?php echo $product['isuniversal']; ?>" data-id="<?php echo $product['productrecepieid']; ?>" value="<?php echo $product['id']; ?>" <?php if(isset($productrecepiedata) && $productrecepiedata['productid'] == $product['id']){ echo "selected"; } ?>><?php echo $productname; ?></option>
                                                

                                                        <?php } ?>
                                                        
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4 text-right pt-xs pb-xs">
                                </div>
                                <div class="col-md-12"><hr></div>
                                <div class="col-md-12 panel-heading"><h2 style="font-weight:600;">Common Raw Material</h2></div>
                                <div class="col-md-12">
                                    <div class="panel panel-default productvariantdiv" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);" id="commonpanel">
                                        <div class="panel-heading">
                                            <div class="col-md-12">
                                                <div class="panel-ctrls"></div>
                                            </div>
                                        </div> 
                                        <div class="panel-body no-padding pt-md">
                                            <div class="col-md-12">
                                                <table class="table table-striped table-bordered" id="commonmaterialtable">
                                                    <thead>
                                                        <tr>
                                                            <th class="width8">Sr. No.</th>
                                                            <th>Product Name</th>
                                                            <th>Unit</th>
                                                            <th class="text-right">Quantity</th>
                                                            <th class="width8 text-center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="variantwisepanel" style="display:block;">
                                    <div class="col-md-12 panel-heading"><h2 style="font-weight:600;">Variant Wise Material</h2></div>
                                    <div class="col-md-12" id="variantmaterialdata">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="MaterialModal" tabindex="-1" role="dialog" aria-labelledby="MaterialLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
                            <h4 class="modal-title"></h4>
                            </div>
                            <div class="modal-body" style="float: left;width: 100%;padding:8px 16px;">
                                <div class="col-md-12">
                                    <form class="form-horizontal" id="edit-material-form">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group" id="editproductid_div">
                                                    <label class="col-md-4 control-label" for="editproductid">Product Name <span class="mandatoryfield">*</span></label>
                                                    <div class="col-md-6">
                                                        <select id="editproductid" name="editproductid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                            <option value="0">Select Product</option>
                                                            <?php foreach($rawproductdata as $product){ 
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
        
                                                                    <option data-content="<img src='<?=PRODUCT.$img?>' style='width:40px'> <?php echo $productname; ?>" value="<?php echo $product['id']; ?>"><?php echo $productname; ?></option>
                                                        
        
                                                                <?php } ?>
                                                                
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group" id="editpriceid_div">
                                                    <label class="col-md-4 control-label" for="editpriceid">Variant <span class="mandatoryfield">*</span></label>
                                                    <div class="col-md-6">
                                                        <select id="editpriceid" name="editpriceid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                            <option value="0">Select Variant</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group" id="editunitid_div">
                                                    <label class="col-md-4 control-label" for="editunitid">Unit <span class="mandatoryfield">*</span></label>
                                                    <div class="col-md-6">
                                                        <select id="editunitid" name="editunitid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                            <option value="0">Select Unit</option>
                                                            <?php foreach($unitdata as $unit){ ?>
                                                                <option value="<?php echo $unit['id']; ?>"><?php echo $unit['name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group" id="editvalue_div">
                                                    <label class="col-md-4 control-label" for="editvalue">Value <span class="mandatoryfield">*</span></label>
                                                    <div class="col-md-2">
                                                        <input type="text" id="editvalue" class="form-control" name="editvalue" value="" onkeypress="return decimal_number_validation(event,this.value,8)">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-sm-12 text-center">
                                                    <input type="button" id="submit" onclick="" name="submit" value="UPDATE" class="btn btn-primary btn-raised btnSubmitMaterial">
                                                    <a class="<?=cancellink_class;?>" href="javascript:void(0)" data-dismiss="modal" aria-label="Close" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="modal-footer"></div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-12">
                    <div class="panel panel-default" id="machinedetailpanel">
                        <div class="panel-body no-padding">
                            <div class="tab-container tab-default m-n">
                                <ul class="nav nav-tabs">
                                    <li class="dropdown pull-right tabdrop hide">
                                        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-angle-down"></i> </a><ul class="dropdown-menu"></ul>
                                    </li>
                                    <li class="active">
                                        <a id="firsttab" href="#machinedetailstab" data-toggle="tab" aria-expanded="false">Machine Details<div class="ripple-container"></div></a>
                                    </li>
                                    <li class="">
                                        <a href="#servicedetailstab" data-toggle="tab" aria-expanded="false">Service Details<div class="ripple-container"></div></a>
                                    </li>
                                </ul>
                                <div class="tab-content pb-n">
                                    <input type="hidden" id="machineid" name="machineid" value="">
                                    <div class="tab-pane active" id="machinedetailstab">
                                        <div class="row">
                                            <div class="col-md-12 p-n">
                                                <table class="table table-striped table-bordered">
                                                    <tbody id="machinedetail">
                                                        <tr>
                                                            <th>Company Name</th>
                                                            <td></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="servicedetailstab">
                                        <div class="row">
                                            <div class="panel panel-default mb-n">
                                                <div class="panel-heading">
                                                    <div class="col-md-6 p-n">
                                                        <div class="panel-ctrls panel-tbl"></div>
                                                    </div>
                                                    <div class="col-md-6 form-group" style="text-align: right;">
                                                        <?php if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                                                            <a class="<?=addbtn_class;?>" href="javascript:void(0)" onclick="openservicepopup()" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="panel-body no-padding">
                                                    <table class="table table-striped table-bordered" id="servicestable">
                                                        <thead>
                                                            <tr>
                                                                <th>Sr. No.</th>
                                                                <th>Service By</th>
                                                                <th>Contact Name</th>
                                                                <th>Contact Mobile No.</th>
                                                                <th>Service Date</th>
                                                                <th>Service Due</th>
                                                                <th>Status</th>
                                                                <th>Reviewed By</th>
                                                                <th>Action</th>
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
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->
<script type="text/javascript">
$('.list-group-item').on('click', function() {
    $('.list-group-item').removeClass('active');
    $(this).addClass('active');
});
</script>