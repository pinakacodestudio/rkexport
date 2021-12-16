<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($documentsdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl'); ?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($documentsdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
                  <div class="col-sm-12 col-md-6 col-lg-6 col-lg-offset-3 col-md-offset-3">
                    <form class="form-horizontal" id="documentsform" name="documentsform" method="post">

                        <input type="hidden" name="id" value="<?php if(isset($documentsdata)){ echo $documentsdata['id']; } ?>">

                        <div class="form-group row" id="name_div">
                            <label class="col-md-3 control-label" for="name">Name <span class="mandatoryfield">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="name" value="<?php if(!empty($documentsdata)){ echo $documentsdata['name']; } ?>" name="name" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row" id="description_div">
                            <label class="col-md-3 control-label" for="description">Description <span class="mandatoryfield">*</span></label>
                            <div class="col-md-8">
                                <textarea id="description" name="description" rows="3" class="form-control"><?php if(isset($documentsdata)){ echo $documentsdata['description']; } ?></textarea>
                            </div>
                        </div>

                        <input type="hidden" name="old_document_file" value="<?php if(isset($documentsdata)){ echo $documentsdata['filename']; } ?>">
                        <input type="hidden" name="remove_document_file" id="remove_document_file" value="0">
                        
                        <?php if(isset($documentsdata)){
                            if($documentsdata['filename']!="") { ?>
                                <div class="form-group row" id="document_file_download_div">
                                    <label class="col-md-3 control-label" for="document_file">File</label>
                                    <div class="col-md-8">
                                        <button type="button" id="remove_old_document_file" class="btn btn-danger btn-raised">Remove</button>
                                        <a href="<?php echo DOCUMENT; ?><?php echo $documentsdata['filename'] ?>" class="btn btn-primary btn-raised" download="<?php echo $documentsdata['filename'] ?>">Download </a>
                                    </div>
                                </div>
                                <div class="form-group row" id="old_document_file_div">
                                    <label class="col-md-3 control-label">File </label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                <span class="btn btn-primary btn-raised btn-file">Browse...
                                                    <input type="file" name="document_file" id="document_file">
                                                </span>
                                            </span>
                                            <input type="text" readonly="" id="Filetext" class="form-control" value="">
                                        </div>
                                    </div>
                                </div>
                            <?php }else { ?>
                                <div class="form-group row" id="document_file_div">
                                    <label class="col-md-3 control-label">File </label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                <span class="btn btn-primary btn-raised btn-file">Browse...
                                                    <input type="file" name="document_file" id="document_file">
                                                </span>
                                            </span>
                                            <input type="text" readonly="" id="Filetext" class="form-control" value="">
                                        </div>
                                    </div>
                                </div>
                            <?php } 
                        } else { ?>
                            <div class="form-group row" id="document_file_div">
                                <label class="col-md-3 control-label">File </label>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                            <span class="btn btn-primary btn-raised btn-file">Browse...
                                                <input type="file" name="document_file" id="document_file">
                                            </span>
                                        </span>
                                        <input type="text" readonly="" id="Filetext" class="form-control" value="">
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="form-group row">
                            <label for="focusedinput" class="col-md-3 control-label">Activate</label>
                            <div class="col-md-8 ml-3">
                                <div class="col-md-3 col-xs-4" style="padding-left: 0px;">
                                    <div class="radio">
                                        <input type="radio" name="status" id="yes" value="1" <?php if(isset($documentsdata) && $documentsdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?> >
                                        <label for="yes">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-xs-4">
                                    <div class="radio">
                                        <input type="radio" name="status" id="no" value="0" <?php if(isset($documentsdata) && $documentsdata['status']==0){ echo 'checked'; }?>>
                                        <label for="no" >No </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="focusedinput" class="col-sm-3 control-label"></label>
                            <div class="col-sm-8 ml-3">
                            <?php if(!empty($documentsdata)){ ?>
                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised text-white" onclick="resetdata()">
                            <?php }else{ ?>
                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised text-white" onclick="resetdata()">
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
		</div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->