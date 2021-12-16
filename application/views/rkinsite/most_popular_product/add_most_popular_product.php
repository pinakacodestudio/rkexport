<script type="text/javascript">
var DEFAULT_IMG = '<?=DEFAULT_IMG?>';
var PRODUCT_IMG_WIDTH = '<?=PRODUCT_IMG_WIDTH?>';
var PRODUCT_IMG_HEIGHT = '<?=PRODUCT_IMG_HEIGHT?>';
</script>
<div class="page-content">
    <div class="page-heading">
        <h1><?php if(isset($productdata)){ echo 'Edit'; }else{ echo 'Add'; } ?>
            <?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
                <li><a
                        href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a>
                </li>
                <li class="active"><?php if(isset($productdata)){ echo 'Edit'; }else{ echo 'Add'; } ?>
                    <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">

        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel">
                        <div class="panel-body">
                            <div class="col-sm-12 p-n">
                                <form class="form-horizontal" enctype="multipart/form-data" id="productform">
                                    <input type="hidden" name="productid" id="productid" value="<?php if(isset($productdata)){ echo $productdata['id']; } ?>">
                                    <input type="hidden" name="sendnotification">
                                    <div class="col-md-12 p-n">
                                        <div class="col-md-6 p-n">
                                          <div class="form-group" id="categoryid_div">
                                              <label class="col-sm-3 control-label" for="categoryid">Category <span class="mandatoryfield"> * </span></label>
                                              <div class="col-md-8">
                                                  <select class="form-control selectpicker" id="categoryid" name="categoryid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                      <option value="0">Select Main Category</option>
                                                      <?php foreach($maincategorydata as $row){ ?>
                                                      <option value="<?php echo $row['id']; ?>" <?php if(isset($productdata)){ if($productdata['categoryid']== $row['id']){ echo 'selected'; } } ?>>
                                                          <?php echo $row['name']; ?></option>
                                                      <?php }?>
                                                  </select>
                                              </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6 p-n">
                                          <div class="form-group" id="priority_div">
                                            <label class="col-sm-3 control-label" for="priority">Priority <span class="mandatoryfield"> * </span></label>
                                              <div class="col-md-8">
                                                <input type="text" id="priority" onkeypress="return isNumber(event)" class="form-control" placeholder="Priority" name="priority" value="<?php if(isset($productdata)){ echo $productdata['priority']; } ?>">
                                              </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 p-n">
                                      <div class="col-md-6 p-n">
                                        <div class="form-group" id="productname_div">
                                          <label class="col-sm-3 control-label" for="productname">Product Name <span class="mandatoryfield"> * </span></label>
                                          <div class="col-md-8">
                                            <input type="text" id="productname" class="form-control" placeholder="Product Name" name="productname" value="<?php if(isset($productdata)){ echo $productdata['name']; } ?>">
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-6 p-n">
                                        <div class="form-group" id="discount_div">
                                          <label class="col-sm-3 control-label" for="discount">Discount (%)</label>
                                          <div class="col-sm-8">
                                            <input id="discount" type="text" name="discount" class="form-control" onkeypress="return decimal_number_validation(event,this.value)" maxlength="5" value="<?php if(isset($productdata)){ echo $productdata['discount']; } ?>">
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-12 p-n">
                                      <div class="col-md-6 p-n">
                                        <div class="form-group" id="hsncode_div">
                                          <label class="col-sm-3 control-label" for="hsncode">HSN Code <span class="mandatoryfield"> * </span></label>
                                            <div class="col-sm-8">
                                              <select class="form-control selectpicker" id="hsncodeid" name="hsncodeid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                  <option value="0">Select HSN Code</option>
                                                  <?php foreach($hsncodedata as $hsncode){ ?>
                                                    <option value="<?php echo $hsncode['id']; ?>" <?php if(isset($productdata)){ if($productdata['hsncodeid']== $hsncode['id']){ echo 'selected'; } } ?>><?php echo $hsncode['hsncode']; ?></option> 
                                                  <?php }?>
                                              </select>  
                                            </div>
                                        </div>
                                      </div>
                                      <div class="col-md-6 p-n">
                                        <div class="form-group" id="productsection_div">
                                            <label class="col-sm-3 control-label" for="productsection"> Product Section </label>
                                            <div class="col-sm-8">
                                              <input type="hidden" value="<?php if(isset($productsectionarr)){ echo implode(",",$productsectionarr); } ?>" name="oldproductsection"></label>
                                              <select class="form-control selectpicker" id="productsection" name="productsection[]" title="Select Product Section" multiple data-live-search="true" data-actions-box="true" data-select-on-tab="true" data-size="5">
                                                  <?php foreach($productsection as $row){ ?>
                                                  <option value="<?php echo $row['id']; ?>"
                                                      <?php if(isset($productsectionarr)){ if(in_array($row['id'],$productsectionarr)){ echo 'selected'; } } ?>>
                                                      <?php echo $row['name']; ?></option>
                                                  <?php } ?>
                                              </select>
                                            </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-12 p-n">
                                      <div class="col-md-6 p-n">
                                        <div class="form-group" id="pointsforseller_div" style="<?=(REWARDSPOINTS==1)?"display:block;":"display:none;"?>">
                                          <label class="col-md-3 control-label" for="pointsforseller">Points for Seller</label>
                                          <div class="col-md-8">
                                            <input type="text" id="pointsforseller" class="form-control" placeholder="" name="pointsforseller" value="<?php if(isset($productdata)){ if($productdata['pointsforseller']!=0){ echo $productdata['pointsforseller']; } } ?>" onkeypress="return isNumber(event)">
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-6 p-n">
                                        <div class="form-group" id="pointsforbuyer_div" style="<?=(REWARDSPOINTS==1)?"display:block;":"display:none;"?>">
                                          <label class="col-md-3 control-label" for="pointsforbuyer">Points for Buyer</label>
                                          <div class="col-md-8">
                                            <input type="text" id="pointsforbuyer" class="form-control" placeholder="" name="pointsforbuyer" value="<?php if(isset($productdata)){ if($productdata['pointsforbuyer']!=0){ echo $productdata['pointsforbuyer']; } } ?>" onkeypress="return isNumber(event)">
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-12 p-n">
                                      <div class="col-md-6 p-n">
                                        <div class="form-group" id="brand_div">
                                          <label class="col-md-3 control-label" for="brandid">Select Brand</label>
                                          <div class="col-md-8">
                                            <select class="form-control selectpicker" id="brandid" name="brandid" data-live-search="true" data-actions-box="true" data-select-on-tab="true" data-size="5">
                                              <option value="0">Select Brand</option>
                                                <?php foreach($branddata as $brand){ ?>
                                                  <option value="<?=$brand['id']?>" <?=((isset($productdata) && $productdata['brandid']==$brand['id'])?'selected':"")?>><?=$brand['name']?></option>
                                                <?php } ?>
                                              </select>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-6 p-n">
                                        <div class="form-group" id="producttype_div">
                                          <label class="col-md-3 control-label" for="producttype">Product Type</label>
                                          <div class="col-sm-8 col-xs-8">
                                            <div class="col-sm-4 col-xs-4" style="padding-left: 0px;">
                                                <div class="radio">
                                                    <input type="radio" name="producttype" id="regularproduct" value="0"
                                                        <?php if(isset($productdata) && $productdata['producttype']==0){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                    <label for="regularproduct">Regular</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 col-xs-4">
                                              <div class="radio">
                                                <input type="radio" name="producttype" id="offerproduct" value="1"
                                                    <?php if(isset($productdata) && $productdata['producttype']==1){ echo 'checked'; }?>>
                                                <label for="offerproduct">Offer</label>
                                              </div>
                                            </div>
                                            <div class="col-sm-4 col-xs-4" style="padding-left: 0px;">
                                              <div class="radio">
                                                <input type="radio" name="producttype" id="rawproduct" value="2"
                                                    <?php if(isset($productdata) && $productdata['producttype']==2){ echo 'checked'; }?>>
                                                <label for="rawproduct">Raw</label>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-12 p-n">
                                      <div class="col-md-6 p-n">
                                        <div class="form-group" id="unit_div">
                                          <label class="col-md-3 control-label" for="unitid">Select Unit</label>
                                          <div class="col-md-7">
                                            <select class="form-control selectpicker" id="unitid" name="unitid" data-live-search="true" data-actions-box="true" data-select-on-tab="true" data-size="5">
                                              <option value="0">Select Unit</option>
                                                <?php foreach($unitdata as $unit){ ?>
                                                  <option value="<?=$unit['id']?>" <?=((isset($productdata) && isset($unitid) && $unitid==$unit['id'])?'selected':"")?>><?=$unit['name']?></option>
                                                <?php } ?>
                                              </select>
                                          </div>
                                          <div class="col-md-1 p-n">
                                            <a href="javascript:void(0)" onclick="addunit()" class="btn btn-primary btn-raised p-xs"><i class="material-icons" title="Add Unit">add</i></a>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-12 p-n">
                                      <div class="col-md-6 p-n">
                                        <div class="col-sm-3">
                                          <div class="form-group" id="checkuniversal_div">
                                            <div class="checkbox col-sm-12 col-xs-6 control-label">
                                              <input type="checkbox" name="checkuniversal" id="checkuniversal" value="1" <?php if(isset($productdata) && $productdata['isuniversal']==1){ echo 'checked'; }?>>
                                              <label style="font-size: 14px;" for="checkuniversal">Universal Price </label>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-sm-8 p-n">
                                          <div class="form-group" id="prices_div">
                                            <div class="col-sm-12">
                                                <input id="prices" type="text" name="prices" value="<?php if(isset($productprices)){ echo implode(",",$productprices); } ?>" data-provide="prices" placeholder="Multiple Prices" onkeypress="return decimal(event,this.value)">
                                            </div>
                                          </div>
                                          <div class="form-group" id="price_div">
                                            <div class="col-md-12">
                                              <input type="text" id="price" onkeypress="return decimal(event,this.value)" class="form-control" placeholder="Price" name="price" value="<?php if(isset($productprices)){ echo implode(",",$productprices); } ?>">
                                            </div>
                                          </div>
                                          <?php if(!empty($productdata) && $productdata['isuniversal']==0){ ?>
                                          <div class="form-group">
                                            <div class="col-md-12">
                                              <span class="btn btn-info btn-raised"><?php if(isset($productprices)){ echo "<i class='fa fa-inr'></i> ".min($productprices)." - ".max($productprices); } ?></span>
                                            </div>
                                          </div>
                                          <?php } ?>
                                        </div>
                                      </div>
                                      <div class="col-md-6 p-n">
                                        <div class="form-group" id="stock_div">
                                          <label class="col-md-3 control-label" for="stock">Stock <span class="mandatoryfield"> * </span></label>
                                          <div class="col-md-8">
                                            <input type="text" id="stock" onkeypress="return isNumber(event)" class="form-control" placeholder="Stock" name="stock" value="<?php if(isset($productstock)){ echo implode(',',$productstock); } ?>">
                                          </div>
                                        </div>
                                        <div class="form-group" id="pointspriority_div" style="<?=(REWARDSPOINTS==1)?"display:block;":"display:none;"?>">
                                          <label class="col-md-3 control-label" for="pointspriority">Points Priority</label>
                                          <div class="col-sm-8 col-xs-8">
                                            <div class="col-sm-6 col-xs-6" style="padding-left: 0px;">
                                                <div class="radio">
                                                    <input type="radio" name="pointspriority" id="universalpoint" value="0"
                                                        <?php if(isset($productdata) && $productdata['pointspriority']==0){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                    <label for="universalpoint">Universal Point</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-6">
                                              <div class="radio">
                                                <input type="radio" name="pointspriority" id="variantpoint" value="1"
                                                    <?php if(isset($productdata) && $productdata['pointspriority']==1){ echo 'checked'; }?>>
                                                <label for="variantpoint">Variant Point</label>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                   
                                    <div class="col-md-12">
                                      <div class="form-group row" id="description_div">
                                        <label for="focusedinput" class="col-sm-12" style="text-align: left;">Description <span class="mandatoryfield">*</span></label>
                                        <div id='termscontainer'>
                                          <div class="col-sm-12">
                                              <?php $data['controlname']="description";if(isset($productdata) && !empty($productdata)){$data['controldata']=$productdata['description'];} ?>
                                              <?php $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-12">
                                        <hr>
                                        <div class="form-group row">
                                            <label class="col-sm-12" style="text-align: center;">Upload Product Image</label>
                                        </div>
                                        <?php if(isset($productdata) && !empty($productfile) && isset($productfile)) { ?>
                                        <input type="hidden" name="removeproductfileid" id="removeproductfileid">
                                        <script type="text/javascript">
                                        var productfilecount = '<?=count($productfile) ?>';
                                        </script>
                                        <?php for ($i=0; $i < count($productfile); $i++) { ?>
                                        <div class="col-md-12 p-n" id="productfilecount<?=$i+1?>">
                                            <div class="form-group" id="productfile<?=$i+1?>_div">
                                                <input type="hidden" name="productfileid<?=$i+1?>"
                                                    value="<?=$productfile[$i]['id']?>" id="productfileid<?=$i+1?>">
                                                <div class="col-md-2 text-center">
                                                    <?php 
                                                    if($productfile[$i]['type']==1){
                                                      $image = PRODUCT.$productfile[$i]['filename'];
                                                    }else if($productfile[$i]['type']==2){
                                                      $image = PRODUCT.$productfile[$i]['videothumb'];
                                                    }else if($productfile[$i]['type']==3){
                                                      $image = $this->general_model->getYoutubevideoThumb(urldecode($productfile[$i]['filename']));
                                                    }else{
                                                      $image = DEFAULT_IMG.DEFAULT_IMAGE_PREVIEW;
                                                    }
                                                    ?>
                                                    <img src="<?=$image?>" id="imagepreview<?=$i+1?>"
                                                        class="thumbwidth">
                                                </div>
                                                <div class="col-md-7 p-n">
                                                    <div class="input-group" id="fileupload<?=$i+1?>"
                                                        style="display:<?=($productfile[$i]['type']==1 || $productfile[$i]['type']==2)?'table':'none'?>;">
                                                        <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                            <span class="btn btn-primary btn-raised btn-file"><i
                                                                    class="fa fa-upload"></i>
                                                                <input type="file" name="productfile<?=$i+1?>"
                                                                    class="productfile" id="productfile<?=$i+1?>"
                                                                    accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
                                                            </span>
                                                        </span>
                                                        <video width="400" id="videoelem<?=$i+1?>"
                                                            style="display: none;" controls>
                                                            <source src="" id="video_src<?=$i+1?>">
                                                        </video>
                                                        <input type="text" name="videothumb<?=$i+1?>"
                                                            id="videothumb<?=$i+1?>" value="" style="display: none;" />
                                                        <input type="text" readonly="" id="Filetext<?=$i+1?>"
                                                            class="form-control"
                                                            value="<?=$productfile[$i]['filename']?>">
                                                    </div>
                                                    <div id="youtube<?=$i+1?>"
                                                        style="display:<?=($productfile[$i]['type']==3)?'block':'none'?>;">
                                                        <input type="text" id="youtubeurl<?=$i+1?>" class="form-control"
                                                            name="youtubeurl<?=$i+1?>"
                                                            value="<?=($productfile[$i]['type']==3)?urldecode($productfile[$i]['filename']):''?>"
                                                            onblur="getThumbImage(this.value,'imagepreview<?=$i+1?>')">
                                                    </div>
                                                    <input type="hidden" name="filetype<?=$i+1?>"
                                                        id="imagefile<?=$i+1?>" value="1">
                                                </div>
                                                <div class="col-md-2">
                                                  <?php if($i==0){?>
                                                    <?php if(count($productfile)>1){ ?>
                                                      <button type="button"
                                                          class="btn btn-danger btn-raised add_remove_btn_product"
                                                          onclick="removeproductfile(1)" id=p1
                                                          style="padding: 6px 12px;margin-top: 0px;"><i
                                                              class="fa fa-minus"></i>
                                                          <div class="ripple-container"></div>
                                                      </button>
                                                    <?php } else { ?>
                                                      <button type="button"
                                                          class="btn btn-primary btn-raised add_remove_btn"
                                                          onclick="addnewproductfile()" id=1
                                                          style="padding: 6px 12px;margin-top: 0px;"><i
                                                              class="fa fa-plus"></i>
                                                          <div class="ripple-container"></div>
                                                      </button>
                                                    <?php } ?>
                                                  <? }else if($i!=0) { ?>
                                                    <button type="button"
                                                        class="btn btn-danger btn-raised add_remove_btn_product"
                                                        id="p<?=$i+1?>" onclick="removeproductfile(<?=$i+1?>)"
                                                        style="padding: 6px 12px;margin-top: 0px;"><i
                                                            class="fa fa-minus"></i>
                                                        <div class="ripple-container"></div>
                                                    </button>
                                                  <? } ?>
                                                  <button type="button"
                                                      class="btn btn-danger btn-raised add_remove_btn_product"
                                                      id="p<?=$i+1?>" onclick="removeproductfile(<?=$i+1?>)"
                                                      style="padding: 6px 12px;margin-top: 0px;display:none;"><i
                                                          class="fa fa-minus"></i>
                                                      <div class="ripple-container"></div>
                                                  </button>
                                                  <button type="button"
                                                        class="btn btn-primary btn-raised add_remove_btn"
                                                        onclick="addnewproductfile()" id="<?=$i+1?>"
                                                        style="padding: 6px 12px;margin-top: 0px;"><i
                                                            class="fa fa-plus"></i>
                                                        <div class="ripple-container"></div>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <? } ?>
                                        <? } else { ?>
                                        <script type="text/javascript">
                                        var productfilecount = 1;
                                        </script>
                                        <div class="col-md-12 p-n" id="productfilecount1">
                                            <div class="form-group" id="productfile1_div">
                                                <div class="col-md-2 text-center">
                                                    <img src="<?=DEFAULT_IMG.DEFAULT_IMAGE_PREVIEW?>" id="imagepreview1"
                                                        class="thumbwidth">
                                                </div>
                                                <div class="col-md-7 p-n">
                                                    <div class="input-group" id="fileupload1">
                                                        <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                            <span class="btn btn-primary btn-raised btn-file"><i
                                                                    class="fa fa-upload"></i>
                                                                <input type="file" name="productfile1"
                                                                    class="productfile" id="productfile1"
                                                                    accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
                                                            </span>
                                                        </span>
                                                        <video width="400" id="videoelem1" style="display: none;"
                                                            controls>
                                                            <source src="" id="video_src1">
                                                        </video>
                                                        <input type="text" name="videothumb1" id="videothumb1" value=""
                                                            style="display: none;" />
                                                        <input type="text" readonly="" id="Filetext1"
                                                            class="form-control" name="Filetext[]" value="">
                                                    </div>
                                                    <div id="youtube1" style="display: none;">
                                                        <input type="text" id="youtubeurl1" class="form-control"
                                                            name="youtubeurl1"
                                                            onblur="getThumbImage(this.value,'imagepreview1')">
                                                    </div>
                                                    <input type="hidden" name="filetype1" id="imagefile1" value="1">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button"
                                                        class="btn btn-danger btn-raised add_remove_btn_product" id="p1"
                                                        onclick="removeproductfile(1)"
                                                        style="padding: 6px 12px;display: none;"><i
                                                            class="fa fa-minus"></i>
                                                        <div class="ripple-container"></div>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-primary btn-raised add_remove_btn" id="1"
                                                        onclick="addnewproductfile()"
                                                        style="padding: 6px 12px;margin-top: 0px;"><i
                                                            class="fa fa-plus"></i>
                                                        <div class="ripple-container"></div>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <? } ?>
                                        <div class="col-md-12 p-n" id="productfiledata_div"></div>
                                    </div>
                                    <div class="col-md-12">
                                      <div class="form-group" id="catalogfile_div">
                                        <label class="col-md-3 control-label" for="catalogfile">Product Catalog</label>
                                          <div class="col-md-8" >
                                            <input type="hidden" name="oldcatalogfile" id="oldcatalogfile" value="<?php if(isset($productdata)){ echo $productdata['catalogfile'];} ?>">
                                            <input type="hidden" name="isvalidcatalogfile" id="isvalidcatalogfile" value="<?php if(isset($productdata) && $productdata['catalogfile']!=""){ echo "1"; }else{ echo "0"; } ?>">
                                            <div class="input-group" id="fileupload1">
                                              <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                  <span class="btn btn-primary btn-raised btn-sm btn-file">Browse...
                                                    <input type="file" name="catalogfile"  id="catalogfile" onchange="validimageorpdffile($(this),'catalogfile')" accept=".docx,.pdf,.bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
                                                  </span>
                                              </span>                                        
                                              <input type="text" id="catalogfiletext" class="form-control" name="catalogfiletext" value="<?php  
                                                      if(isset($productdata)){ echo $productdata['catalogfile'];}
                                              ?>" readonly >
                                            </div>                                      
                                          </div>
                                      </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group" id="metatitle_div">
                                            <label class="col-md-3 control-label" for="metatitle">Meta Title</label>
                                            <div class="col-md-8">
                                                <textarea id="metatitle" name="metatitle"
                                                    class="form-control"><?php if(isset($productdata)){ echo $productdata['metatitle']; } ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group" id="metakeyword_div">
                                            <label for="metakeyword" class="col-sm-3 control-label">Meta
                                                Keywords</label>
                                            <div class="col-sm-8">
                                                <input id="metakeyword" type="text" name="metakeyword"
                                                    value="<?php if(isset($productdata)){ echo $productdata['metakeyword']; } ?>"
                                                    data-provide="metakeyword">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group" id="metadescription_div">
                                            <label class="col-md-3 control-label" for="metadescription">Meta
                                                Description</label>
                                            <div class="col-md-8">
                                                <textarea id="metadescription" name="metadescription"
                                                    class="form-control"
                                                    for="metadescription"><?php if(isset($productdata)){ echo $productdata['metadescription']; } ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <input type="hidden" name="commingsoon"
                                            value="<?php if(isset($productdata)){ echo $productdata['commingsoon']; }?>">
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <div class="form-group">
                                            <label for="focusedinput"
                                                class="col-sm-5 col-xs-4 control-label">Activate</label>
                                            <div class="col-sm-6 col-xs-8">
                                                <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                                    <div class="radio">
                                                        <input type="radio" name="status" id="yes" value="1"
                                                            <?php if(isset($productdata) && $productdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                        <label for="yes">Yes</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2 col-xs-6">
                                                    <div class="radio">
                                                        <input type="radio" name="status" id="no" value="0"
                                                            <?php if(isset($productdata) && $productdata['status']==0){ echo 'checked'; }?>>
                                                        <label for="no">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <?php if(!empty($productdata)){ ?>
                                            <input type="button" id="submit" onclick="checkvalidation()" name="submit"
                                                value="UPDATE" class="btn btn-primary btn-raised">
                                            <input type="reset" name="reset" value="RESET"
                                                class="btn btn-info btn-raised">
                                            <?php }else{ ?>
                                            <input type="button" id="submit" onclick="checkvalidation()" name="submit"
                                                value="ADD" class="btn btn-primary btn-raised">
                                            <input type="reset" name="reset" value="RESET"
                                                class="btn btn-info btn-raised">
                                            <?php } ?>
                                            <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>product"
                                                title=<?=cancellink_title?>><?=cancellink_text?></a>
                                        </div>
                                    </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal addunit" id="addunitModal" style="overflow-y: auto;">
      <div class="modal-dialog" role="document" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
                <h4 class="modal-title" id="post_title">Add Product Unit</h4>
            </div>

            <div class="modal-body no-padding">
                
            </div>

        </div>
      </div>
    </div>
</div> <!-- .container-fluid -->


<script type="text/javascript">
$(".add_btn_car_model").hide();
$(".add_btn_car_model:last").show();
/* $(".add_remove_btn:last").attr("onclick","addnewproductfile()");
  $(".add_remove_btn:last").children(":first-child").attr("class","fa fa-plus");
  $(".add_remove_btn:last").children(":first-child").html(" <b>1</b>"); */

$(".add_remove_btn").hide();
$(".add_remove_btn:last").show();

function addnewproductfile() {

    if ($('input[name="Filetext[]"]').length < 10) {
        productfilecount = ++productfilecount;
        $.html = '<div class="col-md-12 p-n" id="productfilecount' + productfilecount +
            '"><div class="form-group" id="productfile' + productfilecount + '_div"> \
              <div class="col-md-2 text-center"> \
              <img src="<?=DEFAULT_IMG.DEFAULT_IMAGE_PREVIEW?>" id="imagepreview' + productfilecount + '" class="thumbwidth"> \
            </div> \
                    <div class="col-md-7 p-n"> \
                      <div class="input-group" id="fileupload' + productfilecount + '"> \
                <span class="input-group-btn" style="padding: 0 0px 0px 0px;"> \
                  <span class="btn btn-primary btn-raised btn-file"><i class="fa fa-upload"></i> \
                    <input type="file" name="productfile' + productfilecount +
            '" class="productfile" id="productfile' + productfilecount + '" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png"> \
                  </span> \
                </span> \
                <video width="400" id="videoelem' + productfilecount + '" style="display: none;" controls> \
                    <source src="" id="video_src' + productfilecount + '"> \
                </video> \
                <input type="text" name="videothumb' + productfilecount + '" id="videothumb' + productfilecount + '" value="" style="display: none;" /> \
                <input type="text" readonly="" id="Filetext' + productfilecount + '" name="Filetext[]" class="form-control" value=""> \
              </div> \
              <div id="youtube' + productfilecount + '" style="display: none;"> \
                <input type="text" id="youtubeurl' + productfilecount + '" class="form-control" name="youtubeurl' +
            productfilecount + '" onblur="getThumbImage(this.value,\'imagepreview' + productfilecount + '\')"> \
              </div> \
              <input type="hidden" name="filetype' + productfilecount + '" id="imagefile' + productfilecount +
            '" value="1" checked onclick="filetype(' + productfilecount + ',1)"> \
                    </div> \
            <div class="col-md-2"> \
              <button type = "button" class = "btn btn-danger btn-raised add_remove_btn_product" id = "p' +
            productfilecount + '" onclick = "removeproductfile(' + productfilecount + ')" style = "padding: 6px 12px;"> <i class = "fa fa-minus"> </i><div class="ripple-container"></div></button> \
              <button type="button" class="btn btn-primary btn-raised add_remove_btn" id="' + productfilecount +
            '" onclick="addnewproductfile(' + productfilecount + ')" style="padding: 6px 12px;margin-top: 0px;"><i class="fa fa-plus"></i><div class="ripple-container"></div></button> \
            </div> \
                </div></div>';


        $(".add_remove_btn_product:first").show();
        $(".add_remove_btn:last").hide();

        $('#productfiledata_div').append($.html);
        /* 
                var last_id=$(".add_remove_btn:last").attr("id");

                $("#"+(parseInt(last_id)-1)).attr("onclick","removeproductfile("+(parseInt(last_id)-1)+")");
                $("#"+(parseInt(last_id)-1)).children(":first-child").attr("class","fa fa-minus");
              $("#"+(parseInt(last_id)-1)).children(":first-child").text(""); */

        // if($(".add_remove_btn:nth-last-child(2)").length)
        // {
        //  alert($(".add_remove_btn:nth-last-child(2)").attr('id'));           
        // }

        /* $(".add_remove_btn:last").attr("onclick","addnewproductfile()");
        $(".add_remove_btn:last").children(":first-child").attr("class","fa fa-plus");
        $(".add_remove_btn:last").children(":first-child").html(" <b>1</b>"); */

        $('.productfile').change(function() {
            validfile($(this), this);
        });
    } else {
        PNotify.removeAll();
        new PNotify({
            title: 'Maximum 10 files allowed !',
            styling: 'fontawesome',
            delay: '3000',
            type: 'error'
        });
    }
}

function removeproductfile(rowid) {
    if (ACTION == 1 && $('#productfileid' + rowid).val() != null) {
        var removeproductfileid = $('#removeproductfileid').val();
        $('#removeproductfileid').val(removeproductfileid + ',' + $('#productfileid' + rowid).val());
    }
    $('#productfilecount' + rowid).remove();
    // $(".add_remove_btn:last").attr("onclick","addnewproductfile()");
    //    $(".add_remove_btn:last").children(":first-child").attr("class","fa fa-plus");
    //    $(".add_remove_btn:last").children(":first-child").text(" 1");
    $(".add_remove_btn:last").show();
    if ($(".add_remove_btn_product:visible").length == 1) {
        $(".add_remove_btn_product:first").hide();
    }
}

function addunit(){
  
  var uurl = SITE_URL+"product-unit/addunitformodal";
  
  $.ajax({
    url: uurl,
    type: 'POST',
    //async: false,
    beforeSend: function(){
      $('.mask').show();
      $('#loader').show();
    },
    success: function(response){
      $("#addunitModal").modal("show");
      $(".modal-body").html(response);

      include('<?=ADMIN_JS_URL?>pages/add_product_unit.js', function() {
          $(document).ready(function() {
            $('.selectpicker').selectpicker('refresh');
          });
      });
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
    complete: function(){
      $('.mask').hide();
      $('#loader').hide();
    },
    
  });
}
</script>