

<div class="page-content">
    <div class="page-heading">   
     <div class="btn-group dropdown dropdown-l dropdown-breadcrumbs">
        <a class="dropdown-toggle dropdown-toggle-style" data-toggle="dropdown" aria-expanded="false"><span>
            <i class="material-icons" style="font-size: 26px;">menu</i>
          </span> </a>
        <ul class="dropdown-menu dropdown-tl" role="menu">
        <label class="mt-sm ml-sm mb-n">Menu</label>
          <?php
            $subid = $this->session->userdata(base_url().'mainmenuid');
            foreach($subnavtabsmenu as $row){
              if($subid == $row['id']){ ?>
                
                <li class="active"><a href="javascript:void(0);"><i class="fa fa-caret-right mr-sm"></i> <?=$row['name']; ?></a></li>
              
              <?php }else{ ?>
                <li><a href="<?=base_url().ADMINFOLDER.$row['url']; ?>"><i class="fa fa-caret-right mr-sm"></i> <?=$row['name']; ?></a></li>
              <?php } 
            } ?>
        </ul>
      </div>
      
        <h1>Action Log</h1>    
        <small>
          <ol class="breadcrumb">                        
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
          
          </ol>
        </small>                
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
                  <form action="#" class="form-horizontal">
                    <div class="row">
                      <div class="col-md-4 pr-sm">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="startdate" class="control-label">Date</label>
                            <div class="input-daterange input-group" id="datepicker-range">
                              <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-2 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                              <span class="input-group-addon">to</span>
                              <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2 pl-sm pr-sm">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="actiontype" class="control-label">Action Type</label>
                            <select id="actiontype" name="actiontype" class="selectpicker form-control" data-select-on-tab="true" data-size="5">
                              <option value="">All Type</option>
                              <option value="1">Add</option>
                              <option value="2">Edit</option>
                              <option value="3">Delete</option>
                              <option value="4">View</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 pl-sm pr-sm">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="module" class="control-label">Select Module</label>
                            <select id="module" name="module[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" title="All Module" data-actions-box="true" multiple>
                              <?php if(!empty($modulelist)){
                                foreach($modulelist as $module){ ?>
                                  <option value="<?=$module['name']?>"><?=$module['name']?></option>
                                <?php }
                              } ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group pt-xxl">
                          <div class="col-sm-12">
                          <?php if(strpos($mainmenuvisibility['menudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                            <a class="btn btn-primary btn-raised btn-label" href="javascript:void(0)" onclick="clearLog('with_filter')">Clear Logs</a>
                            <?php } ?>
                            <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
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
                  <?php if (in_array("export-to-excel",$this->viewData['mainmenuvisibility']['assignadditionalrights'])){ ?>
                    <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportToExcelActionLogs()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                  <?php } if(strpos($mainmenuvisibility['menudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                    <a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('','Action Log','<?php echo ADMIN_URL; ?>action-log/delete-mul-action-log')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                    <a class="btn btn-primary btn-raised btn-label" href="javascript:void(0)" onclick="clearLog('all')">Clear All Logs</a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="actionlogtable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr. No.</th>
                      <th>Date</th>
                      <th>User Name</th>
                      <th>Full Name</th>
                      <th>Action Type</th>
                      <th>Module</th>
                      <th>Message</th>
                      <th>IP Address</th>
                      <th>Browser</th>
                      <th class="width5">
                        <div class="checkbox">
                          <input id="deletecheckall" onchange="allchecked()" type="checkbox" value="all">
                          <label for="deletecheckall"></label>
                        </div>
                      </th>
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
   
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->


