<!-- 1--><div class="page-content">
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
        <h1><?=$this->session->userdata(base_url().'submenuname')?></h1>    
        <small>
          <ol class="breadcrumb">                        
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
            <li class="active"><?=$this->session->userdata(base_url().'submenuname')?></li>
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
                  <form action="#" id="memberform" class="form-horizontal">
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group" id="channelid_div">
                          <div class="col-sm-12">
                            <label for="channelid" class="control-label">Select Channel</label>
                            <select id="channelid" name="channelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" title="All Channel" data-live-search="true" data-actions-box="true" multiple>
                              <?php foreach($channeldata as $cd){ 
                                    
                                    $selected = ""; 
                                    if(!empty($this->session->userdata(base_url().'CHANNEL'))){ 
                                      $arrChannel = explode(",",$this->session->userdata(base_url().'CHANNEL'));
                                      if(in_array($cd['id'], $arrChannel)){ 
                                        $selected = "selected"; 
                                      } 
                                    }
                                  ?>
                                <option value="<?php echo $cd['id']; ?>" <?php echo $selected; ?>><?php echo $cd['name']; ?></option>
                                <?php } ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group" id="productid_div">
                          <div class="col-sm-12">
                            <label for="productid" class="control-label">Select Product</label>
                            <select id="productid" name="productid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                              <option value="0">All Product</option>
                              <?php foreach($productdata as $product){ ?>

                                   <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>                                  
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="startdate" class="control-label">Date</label>
                            <div class="input-daterange input-group" id="datepicker-range">
                              <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                              <span class="input-group-addon">to</span>
                              <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2 p-n">
                        <div class="form-group" style="margin-top: 39px;">
                          <div class="col-sm-12">
                            <label class="control-label"></label>
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
              </div>
              <div class="panel-body no-padding">
                <table id="carttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr. No.</th>
                      <th>Customer Name</th>                    
                      <th>Product Name</th>
                      <th class="text-right">Quantity</th>
                      <th class="text-right">Price</th>
                      <th class="text-right">Tax</th>
                      <th class="text-right">Disc.(%)</th>
                      <th class="text-right">Net Price</th>
                      <th>Variants</th>
                      <th>Date</th>
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


