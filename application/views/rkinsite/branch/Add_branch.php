<script>
    var countryid = '<?php if(isset($branch)) { echo $branch['countryid']; }else { echo DEFAULT_COUNTRY_ID; } ?>';
    var provinceid = '<?php if(isset($branch)) { echo $branch['provinceid']; }else { echo '0'; } ?>';
    var cityid = '<?php if(isset($branch)) { echo $branch['cityid']; }else { echo '0'; } ?>';
</script>
<div class="page-content">
    <div class="page-heading">
        <h1><?php if(isset($branch)){ echo 'Edit'; }else{ echo 'Add'; } ?>
            <?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
                <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a>
                </li>
                <li class="active"><?php if(isset($branch)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>
    
    <div class="container-fluid">
        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel">
                        <div class="panel-body">
                            <form class="form-horizontal" id="form-branch">
                                <input id="id" type="hidden" name="id" class="form-control" value="<?php if (isset($branch)) { echo $branch['id']; } ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" id="branchname_div">
                                            <label class="col-md-4 control-label text-right" for="branchname">Branch Name <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-8">
                                                <input type="text" id="branchname" class="form-control" name="branchname" value="<?php if(isset($branch)){ echo $branch['branchname'];}?>">
                                            </div>
                                        </div>
                                        <div class="form-group" id="email_div">
                                            <label class="col-md-4 control-label text-right" for="email">Email <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-8">
                                                <input type="text" id="email" class="form-control" name="email" value="<?php if(isset($branch)){ echo $branch['email'];}?>">
                                            </div>
                                        </div>
                                        <div class="form-group" id="services_div">
                                            <label class="col-md-4 control-label text-right" for="services">Services</label>
                                            <div class="col-md-8">
                                                <textarea class="form-control" id="services" name="services"><?php if(isset($branch)){ echo $branch['services'];}?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" id="address_div">
                                            <label class="col-md-4 control-label text-right" for="address">Address <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-8">
                                                <textarea class="form-control" id="address" name="address"><?php if(isset($branch)){ echo $branch['address'];}?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group" id="countryid_div">
                                            <label class="col-md-4 control-label text-right" for="countryid">Country <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-8">
                                                <select id="countryid" name="countryid" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                                    <option value="0">Select Country</option>
                                                    <?php foreach ($countrydata as $cou) { ?>
                                                        <option value="<?php echo $cou['id']; ?>" <?php if(isset($branch)){ if($branch['countryid']==$cou['id']){ echo 'selected';}}else{ if(DEFAULT_COUNTRY_ID == $cou['id']){ echo "selected"; } } ?>><?php echo $cou['name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" id="provinceid_div">
                                            <label class="col-md-4 control-label text-right" for="provinceid">State <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-8">
                                                <select id="provinceid" name="provinceid" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                                    <option value="0">Select State</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" id="cityid_div">
                                            <label class="col-md-4 control-label text-right" for="cityid">City <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-8">
                                                <select id="cityid" name="cityid" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                                    <option value="0">Select City</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="focusedinput" class="col-md-4 col-sm-4 control-label"></label>
                                    <div class="col-md-8 col-sm-8">
                                        <?php if(!empty($branch)){ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                        <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                        <?php }else{ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                        <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                        <?php } ?>
                                        <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->