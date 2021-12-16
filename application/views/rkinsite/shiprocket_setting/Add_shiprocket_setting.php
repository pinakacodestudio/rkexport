<?php
 //echo "<pre>"; print_r($shiprocketsettingdata);exit;
?>



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
        <h1> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              
            </ol>
		</small>
    </div>

    <div class="container-fluid">
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body pt-n">
                    <form class="form-horizontal" id="shiprocketsettingform" name="shiprocketsettingform">
                        
                       
                     
                        
                             <div class="row">
                                <div class="col-md-12">
                                    <!-- <div class="panel panel-default border-panel">
                                        <div class="panel-heading">
                                            <h2></h2>
                                        </div> -->
                                        <div class="panel-body"> 
                                            <div class="col-md-10 col-md-offset-2 p-n">
                                                <div class="col-md-10 ">
                                                    <div class="form-group row" id="email_div">
                                                        <label class="control-label col-md-3" for="email">User E-mail <span class="mandatoryfield">*</span></label>
                                                        <div class="col-md-6">
                                                            <input id="email" type="text" name="email" value="<?php if(isset($shiprocketsettingdata)){ echo $shiprocketsettingdata['email']; } ?>" class="form-control" tabindex="1">
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                <div class="col-md-10 ">
                                                    <div class="form-group" id="password_div">
                                                        <label for="password" class="control-label col-md-3">Password <span class="mandatoryfield">*</span></label>
                                                        <div class="col-md-6">
                                                            <input type="text" name="password" id="password" class="form-control"  value="<?php if(isset($shiprocketsettingdata)){ echo $this->general_model->decryptIt($shiprocketsettingdata['password']); } ?>" tabindex="2">
                                                            
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-10">
                                                    <div class="form-group">

                                                        <label for="focusedinput" class="col-sm-3 control-label">Activate</label>
                                                        <div class="col-md-8">
                                                            <div class="col-md-2 col-xs-2" style="padding-left: 0px;">
                                                                <div class="radio">
                                                                <input type="radio" name="status" id="yes" value="1" <?php if(isset($shiprocketsettingdata) && $shiprocketsettingdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                                <label for="yes">Yes</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 col-xs-2">
                                                                <div class="radio">
                                                                <input type="radio" name="status" id="no" value="0" <?php if(isset($shiprocketsettingdata) && $shiprocketsettingdata['status']==0){ echo 'checked'; }?>>
                                                                <label for="no">No</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-9 col-md-offset-3 p-n">
                                                    <div class="form-group">
                                                        
                                                            <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                                            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            

                                            
                                           

                                            
                                        <!--  </div> -->
                                    </div>
                                </div>
                                         
                            </div> 
                            

                            

                            <div class="row">
                                <div class="col-md-12">
										<div class="panel-body">
                                            <div class="col-md-6 col-md-offset-5">
												
											</div>
										</div>
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


