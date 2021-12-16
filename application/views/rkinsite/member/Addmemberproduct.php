<style type="text/css">
.load_variantsdiv {
    /*border:1px solid lightgray;*/
    padding: 5px 25px;
    /*box-shadow: 0px 1px 1px black;*/
    margin-bottom: 10px;
}

.variant_div {
    box-shadow: 0px 2px 9px #333;
    padding: 5px;
}
</style>
<div class="page-content">
    <div class="page-heading">
        <h1>Add <?=$this->session->userdata(base_url().'submenuname')?> Product</h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
                <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
                <li class="active">Add <?=$this->session->userdata(base_url().'submenuname')?> Product</li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">

        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <form class="form-horizontal" id="memberproductform" name="memberproductform">
                                    <input type="hidden" name="memberid" value="<?=$memberid?>" id="memberid">
                                    <div class="form-group row" for="category" id="categoryid_div">
                                        <label class="col-md-2 label-control" for="categoryid">
                                            Category
                                            <span class="mandatoryfield"> * </span></label>
                                        <div class="col-md-8">
                                            <select class="form-control selectpicker" id="categoryid" name="categoryid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                <option value="0" disabled selected>Select Category</option>
                                                <?php foreach($maincategorydata as $row){ ?>
                                                <option value="<?php echo $row['id']; ?>" <?php if(isset($productdata)){ if($productdata['categoryid']== $row['id']){ echo 'selected'; } } ?>><?php echo $row['name']; ?></option>
                                                <?php }?>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row" for="product" id="product_div">
                                        <label class="col-md-2 label-control" for="productid">Product
                                            <span class="mandatoryfield"> * </span></label>
                                        <div class="col-md-8">
                                            <select class="form-control selectpicker" id="productid" name="productid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                <option value="" disabled selected>Select Product</option>
                                            </select>
                                        </div>
                                    </div>

                                  
                                    <div id="load_variants">
                                    </div>
                                    <div class="row">
                                        <label for="focusedinput" class="col-sm-3 control-label"></label>
                                        <div class="col-sm-8">
                                            <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                            <input type="reset" id="resetbtn" name="reset" value="RESET" class="btn btn-info btn-raised">
                                            <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>member" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->