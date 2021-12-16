<style>
  .dataTables_length .panel-ctrls-center, .dataTables_filter{
    padding: 0px 8px;
  }
</style>
<script>
    var partyid = '<?php echo !empty($partydata)?$partydata['id']:"0"; ?>';
</script>
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
        <h1>View <?=$this->session->userdata(base_url().'submenuname')?></h1>    
        <small>
          <ol class="breadcrumb">                        
            <li><a href="<?=base_url(); ?><?=ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
            <li><a href="<?=ADMIN_URL?>party"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
            <li class="active">View <?=$this->session->userdata(base_url().'submenuname')?></li>
          </ol>
        </small>                
    </div>
    <div class="container-fluid">
      <div data-widget-group="group1">
        <div class="row">   
            <div class="col-md-12">
                <div class="panel panel-default border-panel">
                    <div class="panel-heading">
                        <div class="col-md-6 col-xs-9 col-sm-6 p-n pt-xs">
                            <h2 style="font-size: 14px;"><b>Party Name : </b><?php echo $partydata['firstname']." ".$partydata['middlename']." ".$partydata['lastname']; ?></h2>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-3 p-n text-right">
                            <a class="<?=editbtn_class;?>" href="<?=ADMIN_URL.'party/edit-party/'.$partydata['id']?>" title=<?=editbtn_title?>><?=editbtn_text?></a>
                        </div>
                    </div>
                </div>
            </div>
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
            
              <div class="panel-body p-n pt-1">
                <div class="tab-container tab-default mb-n">
                  <ul class="nav nav-tabs " id="myTab" > 
                      <li class="dropdown pull-right tabdrop hide active">
                          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-angle-down"></i> </a><ul class="dropdown-menu"></ul>
                      </li>
                      <li class="active">
                          <a href="#personaldetails" data-toggle="tab" aria-expanded="false">Personal Details<div class="ripple-container li-line"></div></a>
                      </li>
                      <li class="">
                          <a href="#documentdetails" data-toggle="tab" aria-expanded="false">Document Details<div class="ripple-container li-line"></div></a>
                      </li> 
                      <li class="">
                          <a href="#assignedsitedetails" data-toggle="tab" aria-expanded="false">Assigned Site<div class="ripple-container li-line"></div></a>
                      </li>
                      <li class="" >
                          <a href="#assignedvehicledetails" data-toggle="tab" aria-expanded="false">Assigned Vehicle <div class="ripple-container li-line"></div></a>
                      </li>
                  </ul>
                  
                  <div class="tab-content">  
                      <div class="tab-pane active" id="personaldetails">
                          <div class="row">
                            <div class="col-md-6 p-n">
                                <?php if(count($partydata)>0) { ?>
                                <table class="table table-striped table-bordered table-responsive-sm" width="100%">
                                    <tr>
                                        <th width="35%">Party Name</th>
                                        <td><?php echo $partydata['firstname']." ".$partydata['middlename']." ".$partydata['lastname']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Party Type</th>
                                        <td><?=$partydata['partytype']?></td>
                                    </tr>
                                    <tr>
                                        <th>Party Code</th>
                                        <td><?=$partydata['partycode']?></td>
                                    </tr>
                                    <tr>
                                        <th>Role</th>
                                        <td><?=($partydata['role']!=""?$partydata['role']:"-")?></td>
                                    </tr>
                                    <tr>
                                        <th>Password</th>
                                        <td><?php echo ($partydata['password']!=""?$this->general_model->decryptIt($partydata['password']):"-"); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td><?=($partydata['gender']==0?"Male":"Female")?></td>
                                    </tr>
                                    <tr>
                                        <th>Birth Date</th>
                                        <td><?=($partydata['birthdate']!="0000-00-00"?$this->general_model->displaydate($partydata['birthdate']):"-")?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Anniversary Date</th>
                                        <td><?=($partydata['anniversarydate']!="0000-00-00"?$this->general_model->displaydate($partydata['anniversarydate']):"-")?></td>
                                    </tr>
                                    <tr>
                                        <th>Education</th>
                                        <td><?=($partydata['education']!=""?$partydata['education']:"-")?></td>
                                    </tr>
                                </table>
                                <?php } ?>
                            </div>
                            <div class="col-md-6 p-n">
                                <?php if(count($partydata)>0) { ?>
                                <table class="table table-striped table-bordered table-responsive-sm" width="100%">
                                    <tr>
                                        <th width="35%">Contact No. 1</th>
                                        <td><?=($partydata['contactno1']!=""?$partydata['contactno1']:"-")?></td>
                                    </tr>
                                    <tr>
                                        <th>Contact No. 2</th>
                                        <td><?=($partydata['contactno2']!=""?$partydata['contactno2']:"-")?></td>
                                    </tr>
                                    <tr>
                                        <th>Email ID</th>
                                        <td><?=($partydata['email']!=""?$partydata['email']:"-")?></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><?=($partydata['address']!=""?$partydata['address']:"-")?></td>
                                    </tr>
                                    <tr>
                                        <th>City</th>
                                        <td><?=($partydata['cityname']!=""?$partydata['cityname']:"-")?></td>
                                    </tr>
                                    <tr>
                                        <th>State</th>
                                        <td><?=($partydata['provincename']!=""?$partydata['provincename']:"-")?></td>
                                    </tr>
                                    <tr>
                                        <th>Country</th>
                                        <td><?=($partydata['countryname']!=""?$partydata['countryname']:"-")?></td>
                                    </tr>
                                </table>
                                <?php } ?>
                            </div>                                                                                           
                          </div>
                      </div>
                      <div class="tab-pane" id="documentdetails">
                          <div class="row">
                            <div class="col-md-12 p-n">
                                <div class="panel panel-default mb-n" style="box-shadow: unset !important;">
                                    <div class="panel-heading">
                                        <div class="col-md-6 p-n">
                                            <div class="panel-ctrls document-tbl"></div>
                                        </div>
                                        <div class="col-md-6 form-group" style="text-align: right;">
                                            <?php if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                                                <a class="<?=addbtn_class;?>" onclick="openDocumentModal(1,<?=$partydata['id']?>)" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                                            <?php }
                                            if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                                                <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>document/check-document-use','Document','<?php echo ADMIN_URL; ?>document/delete-mul-document')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="panel-body no-padding">
                                        <div class="table-responsive">
                                            <table id="documenttable" class="table table-bordered table-striped table-responsive-sm" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="width8">Sr. No.</th>
                                                        <th>Document Type</th>
                                                        <th>Document Number</th>
                                                        <th>Register Date</th>
                                                        <th>Due Date</th>
                                                        <th class="width15">Action</th>
                                                        <th class="width5">
                                                            <div class="checkbox">
                                                                <input id="deletecheckall" onchange="allchecked()" type="checkbox" value="all">
                                                                <label for="deletecheckall"></label>
                                                            </div>
                                                        </th>     
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="panel-footer document-tbl"></div>
                                </div>
                                <?php $this->load->view(ADMINFOLDER.'document/Documentmodal');?>
                            </div>
                          </div>
                      </div>
                      <div class="tab-pane" id="assignedsitedetails">
                          <div class="row">
                            <div class="col-md-12 p-n">
                                <div class="panel panel-default mb-n" style="box-shadow: unset !important;">
                                    <div class="panel-heading">
                                        <div class="col-md-5 p-n">
                                            <div class="panel-ctrls assignedsite-tbl"></div>
                                        </div>
                                        <div class="col-md-7" style="text-align: right;">
                                            <div class="col-sm-4 pl-sm pr-sm">
                                                <div class="form-group" style="margin-top: -7px !important;">
                                                    <select id="sitecityid" name="sitecityid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                    <option value="0">All City</option>
                                                    <?php foreach ($sitecitydata as $city) { ?>
                                                        <option value="<?php echo $city['id']; ?>"><?php echo $city['name']; ?></option>
                                                    <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-8 pl-sm pr-sm">
                                                <div class="form-group" style="margin-top: -3px !important;">
                                                    <div class="input-daterange input-group" id="datepicker-range">
                                                        <input type="text" class="input-small form-control" name="sitestartdate" id="sitestartdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                                        <span class="input-group-addon">to</span>
                                                        <input type="text" class="input-small form-control" name="siteenddate" id="siteenddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-body no-padding">
                                        <table id="assignedsitetable" class="table table-bordered table-striped table-responsive-sm" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Site Name</th>
                                                    <th>Address</th>
                                                    <th>City</th>
                                                    <th>Province</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="panel-footer assignedsite-tbl"></div>
                                </div>
                            </div>                                                                                              
                          </div>
                      </div>    
                      <div class="tab-pane" id="assignedvehicledetails">
                        <div class="row">
                            <div class="col-md-12 p-n">
                                <div class="panel panel-default mb-n" style="box-shadow: unset !important;">
                                    <div class="panel-heading">
                                        <div class="col-md-5 p-n">
                                            <div class="panel-ctrls assignedvehicle-tbl"></div>
                                        </div>
                                        <div class="col-md-7" style="text-align: right;">
                                            <div class="col-sm-8 pl-sm pr-sm pull-right">
                                                <div class="form-group" style="margin-top: -3px !important;">
                                                    <div class="input-daterange input-group" id="datepicker-range">
                                                        <input type="text" class="input-small form-control" name="vehiclestartdate" id="vehiclestartdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                                        <span class="input-group-addon">to</span>
                                                        <input type="text" class="input-small form-control" name="vehicleenddate" id="vehicleenddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-body no-padding">
                                        <table id="assignedvehicletable" class="table table-bordered table-striped table-responsive-sm" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Vehicle Name</th>
                                                    <th>Vehicle Number</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="panel-footer assignedvehicle-tbl"></div>
                                </div>
                            </div>                                                                                              
                          </div>                                        
                      </div> 
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>        
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->