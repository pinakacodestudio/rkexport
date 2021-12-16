<style>
.dropdown-menu{
    z-index: 10000;
}
</style>
<div class="page-content">
    <div class="page-heading">     
      <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">

        <div class="row">
            <!--<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <div class="panel dashboard-count-panel">
                    <div class="panel-controls dropdown">
                        <button class="btn btn-icon-rounded dropdown-toggle" data-toggle="dropdown"><span class="material-icons inverted text-white">access_time</span></button>
                       <ul class="dropdown-menu">
                                  <li id="customerdd1" class="customerdd active"><a class="dropdown-item" href="#" onclick="getcounts(1,'customer')">1 Month</a></li>
                                  <li id="customerdd2" class="customerdd"><a class="dropdown-item" href="#" onclick="getcounts(2,'customer')">3 Month</a></li>
                                  <li id="customerdd3" class="customerdd"><a class="dropdown-item" href="#" onclick="getcounts(3,'customer')">6 Month</a></li>
                                  <li id="customerdd4" class="customerdd"><a class="dropdown-item" href="#" onclick="getcounts(4,'customer')">1 Year</a></li>
                                  <li id="customerdd5" class="customerdd"><a class="dropdown-item" href="#" onclick="getcounts(5,'customer')">All</a></li>
                        </ul>
                    </div>
                    <div class="panel-heading dashboard-count-heading">
                        
                    </div>
                    <div class="panel-body padding-0 panel-primary panel-indigo">
                        <div class="row">
                            <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2"><span class="material-icons tile-icon">account_circle</span></div>
                            <div class="col-md-10 col-lg-10 col-sm-10 col-xs-10">
                                <div class="count-detail">
                                    <h2 class="fs-17 text-white mb-0"> Customer</h2>
                                    <h2 class="dashboard-count" id="customercount"></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->
           <!--  <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
               <div class="panel dashboard-count-panel">
                    <div class="panel-controls dropdown">
                        <button class="btn btn-icon-rounded dropdown-toggle" data-toggle="dropdown"><span class="material-icons inverted text-white">access_time</span></button>
                       <ul class="dropdown-menu">
                                  <li id="memberdistributerdd1" class="memberdistributerdd active"><a class="dropdown-item" href="#" onclick="getcounts(1,'memberdistributer')">1 Month</a></li>
                                  <li id="memberdistributerdd2" class="memberdistributerdd"><a class="dropdown-item" href="#" onclick="getcounts(2,'memberdistributer')">3 Month</a></li>
                                  <li id="memberdistributerdd3" class="memberdistributerdd"><a class="dropdown-item" href="#" onclick="getcounts(3,'memberdistributer')">6 Month</a></li>
                                  <li id="memberdistributerdd4" class="memberdistributerdd"><a class="dropdown-item" href="#" onclick="getcounts(4,'memberdistributer')">1 Year</a></li>
                                  <li id="memberdistributerdd5" class="memberdistributerdd"><a class="dropdown-item" href="#" onclick="getcounts(5,'memberdistributer')">All</a></li>
                        </ul>
                    </div>
                    <div class="panel-heading dashboard-count-heading">
                        
                    </div>
                    <div class="panel-body padding-0 panel-brown">
                        <div class="row">
                            <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2"><span class="material-icons tile-icon">perm_contact_calendar</span></div>
                            <div class="col-md-10 col-lg-10 col-sm-10 col-xs-10">
                                <div class="count-detail">
                                    <h2 class="fs-17 text-white mb-0"> Member Distributer</h2>
                                    <h2 class="dashboard-count" id="memberdistributercount"></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
               <div class="panel dashboard-count-panel">
                    <div class="panel-controls dropdown">
                        <button class="btn btn-icon-rounded dropdown-toggle" data-toggle="dropdown"><span class="material-icons inverted text-white">access_time</span></button>
                       <ul class="dropdown-menu">
                                  <li id="memberdd1" class="memberdd active"><a class="dropdown-item" href="#" onclick="getcounts(1,'member')">1 Month</a></li>
                                  <li id="memberdd2" class="memberdd"><a class="dropdown-item" href="#" onclick="getcounts(2,'member')">3 Month</a></li>
                                  <li id="memberdd3" class="memberdd"><a class="dropdown-item" href="#" onclick="getcounts(3,'member')">6 Month</a></li>
                                  <li id="memberdd4" class="memberdd"><a class="dropdown-item" href="#" onclick="getcounts(4,'member')">1 Year</a></li>
                                  <li id="memberdd5" class="memberdd"><a class="dropdown-item" href="#" onclick="getcounts(5,'member')">All</a></li>
                        </ul>
                    </div>
                    <div class="panel-heading dashboard-count-heading">
                        
                    </div>
                    <div class="panel-body padding-0 panel-brown">
                        <div class="row">
                            <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2"><span class="material-icons tile-icon">perm_contact_calendar</span></div>
                            <div class="col-md-10 col-lg-10 col-sm-10 col-xs-10">
                                <div class="count-detail">
                                    <h2 class="fs-17 text-white mb-0"> Member</h2>
                                    <h2 class="dashboard-count" id="membercount"></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            
            <?php if (in_array("total-member",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
               <div class="panel dashboard-count-panel">
                    <div class="panel-controls dropdown">
                        <button class="btn btn-icon-rounded dropdown-toggle" data-toggle="dropdown"><span class="material-icons inverted text-white">access_time</span></button>
                       <ul class="dropdown-menu">
                                  <li id="memberdd1" class="memberdd "><a class="dropdown-item" href="#" onclick="getcounts(1,'member')">1 Month</a></li>
                                  <li id="memberdd2" class="memberdd"><a class="dropdown-item" href="#" onclick="getcounts(2,'member')">3 Month</a></li>
                                  <li id="memberdd3" class="memberdd"><a class="dropdown-item" href="#" onclick="getcounts(3,'member')">6 Month</a></li>
                                  <li id="memberdd4" class="memberdd"><a class="dropdown-item" href="#" onclick="getcounts(4,'member')">1 Year</a></li>
                                  <li id="memberdd5" class="memberdd active"><a class="dropdown-item" href="#" onclick="getcounts(5,'member')">All</a></li>
                        </ul>
                    </div>
                    <div class="panel-heading dashboard-count-heading">
                        
                    </div>
                    <div class="panel-body padding-0 panel-indigo">
                        <div class="row">
                            <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2"><span class="material-icons tile-icon">perm_contact_calendar</span></div>
                            <div class="col-md-10 col-lg-10 col-sm-10 col-xs-10">
                                <div class="count-detail">
                                    <h2 class="fs-17 text-white mb-0"> <?=Member_label?></h2>
                                    <h2 class="dashboard-count" id="membercount"><?=nice_number($membercount);?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (in_array("total-inquiry",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
               <div class="panel dashboard-count-panel">
                    <div class="panel-controls dropdown">
                        <button class="btn btn-icon-rounded dropdown-toggle" data-toggle="dropdown"><span class="material-icons inverted text-white">access_time</span></button>
                       <ul class="dropdown-menu">
                                  <li id="inquirydd1" class="inquirydd "><a class="dropdown-item" href="#" onclick="getcounts(1,'inquiry')">1 Month</a></li>
                                  <li id="inquirydd2" class="inquirydd"><a class="dropdown-item" href="#" onclick="getcounts(2,'inquiry')">3 Month</a></li>
                                  <li id="inquirydd3" class="inquirydd"><a class="dropdown-item" href="#" onclick="getcounts(3,'inquiry')">6 Month</a></li>
                                  <li id="inquirydd4" class="inquirydd"><a class="dropdown-item" href="#" onclick="getcounts(4,'inquiry')">1 Year</a></li>
                                  <li id="inquirydd5" class="inquirydd active"><a class="dropdown-item" href="#" onclick="getcounts(5,'inquiry')">All</a></li>
                        </ul>
                    </div>
                    <div class="panel-heading dashboard-count-heading">
                        
                    </div>
                    <div class="panel-body padding-0 panel-blue">
                        <div class="row">
                            <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2"><span class="material-icons tile-icon">reorder</span></div>
                            <div class="col-md-10 col-lg-10 col-sm-10 col-xs-10">
                                <div class="count-detail">
                                    <h2 class="fs-17 text-white mb-0">Inquiry</h2>
                                    <h2 class="dashboard-count" id="inquirycount"><?=nice_number($inquirycount);?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
            
            <?php if (in_array("total-followup",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
               <div class="panel dashboard-count-panel">
                    <div class="panel-controls dropdown">
                        <button class="btn btn-icon-rounded dropdown-toggle" data-toggle="dropdown"><span class="material-icons inverted text-white">access_time</span></button>
                       <ul class="dropdown-menu">
                                  <li id="followupdd1" class="followupdd active"><a class="dropdown-item" href="#" onclick="getcounts(1,'followup')">1 Month</a></li>
                                  <li id="followupdd2" class="followupdd"><a class="dropdown-item" href="#" onclick="getcounts(2,'followup')">3 Month</a></li>
                                  <li id="followupdd3" class="followupdd"><a class="dropdown-item" href="#" onclick="getcounts(3,'followup')">6 Month</a></li>
                                  <li id="followupdd4" class="followupdd"><a class="dropdown-item" href="#" onclick="getcounts(4,'followup')">1 Year</a></li>
                                  <li id="followupdd5" class="followupdd"><a class="dropdown-item" href="#" onclick="getcounts(5,'followup')">All</a></li>
                        </ul>
                    </div>
                    <div class="panel-heading dashboard-count-heading">
                        
                    </div>
                    <div class="panel-body padding-0 panel-success">
                        <div class="row">
                            <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2"><span class="material-icons tile-icon">assessment</span></div>
                            <div class="col-md-10 col-lg-10 col-sm-10 col-xs-10">
                                <div class="count-detail">
                                    <h2 class="fs-17 text-white mb-0"> Followup</h2>
                                    <h2 class="dashboard-count" id="followupcount"><?=nice_number($followupcount);?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (in_array("total-product",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
               <div class="panel dashboard-count-panel">
                    <div class="panel-controls dropdown">
                        <button class="btn btn-icon-rounded dropdown-toggle" data-toggle="dropdown"><span class="material-icons inverted text-white">access_time</span></button>
                       <ul class="dropdown-menu">
                                  <li id="productdd1" class="productdd active"><a class="dropdown-item" href="#" onclick="getcounts(1,'product')">1 Month</a></li>
                                  <li id="productdd2" class="productdd"><a class="dropdown-item" href="#" onclick="getcounts(2,'product')">3 Month</a></li>
                                  <li id="productdd3" class="productdd"><a class="dropdown-item" href="#" onclick="getcounts(3,'product')">6 Month</a></li>
                                  <li id="productdd4" class="productdd"><a class="dropdown-item" href="#" onclick="getcounts(4,'product')">1 Year</a></li>
                                  <li id="productdd5" class="productdd"><a class="dropdown-item" href="#" onclick="getcounts(5,'product')">All</a></li>
                        </ul>
                    </div>
                    <div class="panel-heading dashboard-count-heading">
                        
                    </div>
                    <div class="panel-body padding-0 panel-danger">
                        <div class="row">
                            <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2"><span class="material-icons tile-icon">shopping_cart</span></div>
                            <div class="col-md-10 col-lg-10 col-sm-10 col-xs-10">
                                <div class="count-detail">
                                    <h2 class="fs-17 text-white mb-0">Product</h2>
                                    <h2 class="dashboard-count" id="productcount"><?=nice_number($productcount);?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <!-- <?php if (in_array("total-quotation",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
               <div class="panel dashboard-count-panel">
                    <div class="panel-controls dropdown">
                        <button class="btn btn-icon-rounded dropdown-toggle" data-toggle="dropdown"><span class="material-icons inverted text-white">access_time</span></button>
                       <ul class="dropdown-menu">
                                  <li id="quotationdd1" class="quotationdd active"><a class="dropdown-item" href="#" onclick="getcounts(1,'quotation')">1 Month</a></li>
                                  <li id="quotationdd2" class="quotationdd"><a class="dropdown-item" href="#" onclick="getcounts(2,'quotation')">3 Month</a></li>
                                  <li id="quotationdd3" class="quotationdd"><a class="dropdown-item" href="#" onclick="getcounts(3,'quotation')">6 Month</a></li>
                                  <li id="quotationdd4" class="quotationdd"><a class="dropdown-item" href="#" onclick="getcounts(4,'quotation')">1 Year</a></li>
                                  <li id="quotationdd5" class="quotationdd"><a class="dropdown-item" href="#" onclick="getcounts(5,'quotation')">All</a></li>
                        </ul>
                    </div>
                    <div class="panel-heading dashboard-count-heading">
                        
                    </div>
                    <div class="panel-body padding-0 panel-orange">
                        <div class="row">
                            <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2"><span class="material-icons tile-icon">add_shopping_cart</span></div>
                            <div class="col-md-10 col-lg-10 col-sm-10 col-xs-10">
                                <div class="count-detail">
                                    <h2 class="fs-17 text-white mb-0"> Quotations</h2>
                                    <h2 class="dashboard-count" id="quotationcount"><?=nice_number($quotationcount);?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?> -->

            <!-- <?php if (in_array("total-sales",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
               <div class="panel dashboard-count-panel">
                    <div class="panel-controls dropdown">
                        <button class="btn btn-icon-rounded dropdown-toggle" data-toggle="dropdown"><span class="material-icons inverted text-white">access_time</span></button>
                       <ul class="dropdown-menu">
                                  <li id="totalsalesdd1" class="totalsalesdd active"><a class="dropdown-item" href="#" onclick="getcounts(1,'totalsales')">1 Month</a></li>
                                  <li id="totalsalesdd2" class="totalsalesdd"><a class="dropdown-item" href="#" onclick="getcounts(2,'totalsales')">3 Month</a></li>
                                  <li id="totalsalesdd3" class="totalsalesdd"><a class="dropdown-item" href="#" onclick="getcounts(3,'totalsales')">6 Month</a></li>
                                  <li id="totalsalesdd4" class="totalsalesdd"><a class="dropdown-item" href="#" onclick="getcounts(4,'totalsales')">1 Year</a></li>
                                  <li id="totalsalesdd5" class="totalsalesdd"><a class="dropdown-item" href="#" onclick="getcounts(5,'totalsales')">All</a></li>
                        </ul>
                    </div>
                    <div class="panel-heading dashboard-count-heading">
                        
                    </div>
                    <div class="panel-body padding-0 panel-teal">
                        <div class="row">
                            <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2"><span class="material-icons tile-icon">multiline_chart</span></div>
                            <div class="col-md-10 col-lg-10 col-sm-10 col-xs-10">
                                <div class="count-detail">
                                    <h2 class="fs-17 text-white mb-0"> Total Sales</h2>
                                    <h2 class="dashboard-count" id="totalsalescount"><?=nice_number($totalsalescount);?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?> -->

        </div>         

        <!-- <div class="row">
            <?php if (in_array("total-sales-chart",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
            <div class="col-md-9">
                <div class="row chart-div">
                    <div class="col-md-12 ">
                        <div class="col-md-4">
                            <h4 class="text-center">Total Sales (<?=CURRENCY_CODE?>)</h4>
                        </div>
                        <div class="col-md-6 p-n">
                            <div class="input-daterange input-group" id="datepicker-range">
                                <input type="text" class="input-small form-control datepicker1" name="fromdate" id="fromdate" placeholder="From Date" value="<?=date("d/m/Y",strtotime("-3 month"))?>" style="z-index: 0;" readonly/>
                                <span class="input-group-addon"><p style="margin: 5px;">TO</p></span>
                                <input type="text" class="input-small form-control datepicker1" name="todate" id="todate" placeholder="To Date" value="<?=date("d/m/Y")?>" style="z-index: 0;" readonly/>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="datefilterbtn" class="btn btn-primary btn-raised btn-md" style="margin-top: 20px;">OK</button>
                        </div>
                        <div class="col-md-12 p-n">
                            <div id="container1" style="min-width: 310px; height: 300px; margin: 0 auto"></div>
                        </div> 
                        
                    </div>
                </div>
            </div>
            <?php } if (in_array("total-sales-chart-box",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
            <div class="col-md-3">
                <div class="panel panel-white box-div">
                    <div class="panel-heading p-n table-responsive panel-success">
                        <div class="col-md-12 col-xs-12" >
                          <h4 class="text-white text-center"> Sales (<?=CURRENCY_CODE?>)</h3>
                        </div>
                    </div>
                    <div class="panel-body ">
                       
                        <h4 class="mt-n mb-n pt-xs text-center">Total Sales</h4>
                        <h1 class="text-center box-font" id="salestotalchartbox"><?php if(isset($saleschartboxcount)){ echo $saleschartboxcount['salescount']; }?></h1>
                        <hr color=gray>
                        <h4 class="mt-n mb-n pt-xs text-center">Average Sales</h4>
                        <h1 class="text-center box-font" id="salesaveragechartbox"><?php if(isset($saleschartboxcount)){ echo $saleschartboxcount['salesaverage']; }?></h1>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div> -->
        <br>
        <!-- <div class="row">
            <?php if (in_array("total-orders-chart",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
            <div class="col-md-9">
                <div class="row chart-div">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <h4 class="text-center">Total Orders</h4>
                        </div>
                        <div class="col-md-6 p-n">
                            <div class="input-daterange input-group" id="datepicker-range1">
                                <input type="text" class="input-small form-control datepicker1" name="fromdate1" id="fromdate1" placeholder="From Date" value="<?=date("d/m/Y",strtotime("-3 month"))?>" style="z-index: 0;" readonly/>
                                <span class="input-group-addon"><p style="margin: 5px;">TO</p></span>
                                <input type="text" class="input-small form-control datepicker1" name="todate1" id="todate1" placeholder="To Date" value="<?=date("d/m/Y")?>" style="z-index: 0;" readonly/>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="datefilterbtn1" class="btn btn-primary btn-raised btn-md" style="margin-top: 20px;">OK</button>
                        </div>
                        <div class="col-md-12 p-n">
                            <div id="container" style="min-width: 310px; height: 300px; margin: 0 auto"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } if (in_array("total-orders-chart-box",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
            <div class="col-md-3">
                <div class="panel panel-white box-div">
                    <div class="panel-heading p-n table-responsive panel-teal">
                        <div class="col-md-12 col-xs-12" >
                          <h4 class="text-white text-center">  Orders</h3>
                        </div>
                    </div>
                    <div class="panel-body ">
                        
                        <h4 class="mt-n mb-n pt-xs text-center">Total Orders</h4>
                        <h1 class="text-center box-font" id="orderstotalchartbox"><?php if(isset($orderschartboxcount)){ echo $orderschartboxcount['orderscount']; }?></h1>
                        <hr color=gray>
                        <h4 class="mt-n mb-n pt-xs text-center">Average Orders</h4>
                        <h1 class="text-center box-font" id="ordersaveragechartbox"><?php if(isset($orderschartboxcount)){ echo $orderschartboxcount['ordersaverage']; }?></h1>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div> -->

        <div class="panel dashboard-section">
        <div class="card-body">
            <div class="row">
              <div class="col-md-6 mb-2"><i class="fa fa-bar-chart fa-3x"></i></div>
              <div class="col-md-5">
                <div class="input-daterange input-group" id="datepicker-range">
                  <div class="input-group"> 
                    <input type="text" style="z-index:0;" class="form-control datepicker1" name="fromdate" id="fromdate" placeholder="From Date" value="<?=date("d/m/Y",strtotime("-30 days"))?>" readonly/>
                    <span class="btn btn-default " style="position:absolute; top:14px; right:0px;" title='Date' ><i class="fa fa-calendar fa-lg"></i></span>
                  </div>
                  <span class="input-group-addon"><p style="margin: 5px;">to</p></span>
                  <div class="input-group"> 
                    <input type="text" style="z-index:0;" class="form-control datepicker1" name="todate" id="todate" placeholder="To Date" value="<?=date("d/m/Y")?>" readonly/>
                    <span class="btn btn-default " style="position:absolute; top:14px; right:0px;" title='Date' ><i class="fa fa-calendar fa-lg"></i></span>
                  </div>
                </div>
              </div>
              <div class="col-md-1 mt-md">
                <button type="button" id="datefilterbtn" class="btn btn-primary btn-raised">OK</button>
              </div>
           <!--  </div><br>

            <div class="row"> -->
              <div class="col-md-6 dashboard-chart-div bdr-r">
                <div id="container" style="min-width: 310px; height: 300px; margin: 0 auto"></div>
              </div>
              <div class="col-md-6 dashboard-chart-div">
                <div id="container1" style="min-width: 310px; height: 300px; margin: 0 auto"></div>
              </div>
            </div>
        </div>
    </div>

    <!--<div class="row chart-div" style="box-shadow: 0 8px 17px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <div class="col-md-6 pl-n pr-xs" style="border-right: 1px dashed #ddd;">
                <div class="col-md-4">
                    <h4>Total Sales</h4>
                </div>
                <div class="col-md-6 p-n">
                    <div class="input-daterange input-group" id="datepicker-range">
                        <input type="text" class="input-small form-control datepicker1" name="fromdate" id="fromdate" placeholder="From Date" value="<?=date("d/m/Y",strtotime("-3 month"))?>" style="z-index: 0;" readonly/>
                        <span class="input-group-addon"><p style="margin: 5px;">TO</p></span>
                        <input type="text" class="input-small form-control datepicker1" name="todate" id="todate" placeholder="To Date" value="<?=date("d/m/Y")?>" style="z-index: 0;" readonly/>
                    </div>
                </div>
                <div class="col-md-2 pt-sm text-right">
                    <button type="button" id="datefilterbtn" class="btn btn-primary btn-raised btn-md">OK</button>
                </div>
                <div class="col-md-12 p-n">
                    <div id="container1" style="min-width: 310px; height: 300px; margin: 0 auto"></div>
                </div>
            </div>
            <div class="col-md-6 pr-n pl-xs">
                <div class="col-md-4">
                    <h4>Total Orders</h4>
                </div>
                <div class="col-md-6 p-n">
                    <div class="input-daterange input-group" id="datepicker-range1">
                    <input type="text" class="input-small form-control datepicker1" name="fromdate1" id="fromdate1" placeholder="From Date" value="<?=date("d/m/Y",strtotime("-3 month"))?>" style="z-index: 0;" readonly/>
                    <span class="input-group-addon"><p style="margin: 5px;">TO</p></span>
                    <input type="text" class="input-small form-control datepicker1" name="todate1" id="todate1" placeholder="To Date" value="<?=date("d/m/Y")?>" style="z-index: 0;" readonly/>
                    </div>
                </div>
                <div class="col-md-2 pt-sm text-right">
                    <button type="button" id="datefilterbtn1" class="btn btn-primary btn-raised btn-md">OK</button>
                </div>
                <div class="col-md-12 p-n">
                    <div id="container" style="min-width: 310px; height: 300px; margin: 0 auto"></div>
                </div>
            </div>
        </div> -->
        <br>
        <div class="row">
            <?php if (in_array("today-follow-up",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
            <div class="col-md-6 pr-sm">
                <div class="panel panel-default" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);">
                    <div class="panel-heading panel-midnighblue">
                        <h4 class="text-white ">Recent Followup </h4>
                    </div>
                    <div class="panel-body p-n">
                        <table class="table m-n">
                        <thead>
                            <tr>
                                <!-- <th>Sr. No.</th> -->
                                <th>Company Name</th>
                                <th>Followup Type</th>
                                <th>Asssigned To</th>
                                <th >Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(count($recentfollowup)>0){ 
                                for ($i=0; $i < count($recentfollowup); $i++) { ?>
                                <tr>
                                    
                                    <td><?=$recentfollowup[$i]['companyname']?></td>
                                    <td><?=$recentfollowup[$i]['followuptypename']?></td>
                                    <td><?=$recentfollowup[$i]['employeename']?></td>
                                    <td><?php foreach ($followupstatuses as $fs) {
                                        if ($recentfollowup[$i]['status']==$fs['id']) {
                                            $sts_val=$fs['name'];
                                            $btn_clr=$fs['color'];
                                        }
                                    }
                
                                    $statuses = '<div class="" style="float: left;">
                                                    <button class="btn '.STATUS_DROPDOWN_BTN.' btn-raised "  id="btndropdown'.($i+1).'" style="background:'.$btn_clr.';color: #fff;">'.$sts_val.' </span></button></div>';
                                    
                                    
                                    ?><?=$statuses?></td>
                                </tr>    
                            <? }}else{ ?>
                            <tr>
                                <td colspan="4" class="text-center">No records found</td>
                            </tr>
                            <? } ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            <?php } if (in_array("total-inquiry",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
            <div class="col-md-6 pl-sm">
                <div class="panel panel-default" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);">
                    <div class="panel-heading panel-indigo">
                        <h4 class="text-white ">Recent Inquiry</h4>
                    </div>
                    <div class="panel-body p-n">
                        <table class="table m-n">
                        <thead>
                            <tr>
                                <!-- <th>Sr. No.</th> -->
                                <th>Company Name</th>
                                <th>Mobile</th>
                                <th>Asssigned To</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(count($recentinquiry)>0){ 
                                for ($i=0; $i < count($recentinquiry); $i++) { ?>
                                <tr>
                                    
                                    <td><?=$recentinquiry[$i]['companyname']?></td>
                                    <td><?=$recentinquiry[$i]['mobileno']?></td>
                                    <td ><?=$recentinquiry[$i]['ename']?></td>
                                    <td ><?=$this->general_model->displaydatetime($recentinquiry[$i]['createddate'])?></td>
                                </tr>    
                            <? }}else{ ?>
                            <tr>
                                <td colspan="4" class="text-center">No records found</td>
                            </tr>
                            <? } ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12 pl-n">
                <?php } if (in_array("total-member",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
            
                <div class="col-md-6 pr-n">
                    <div class="panel panel-default" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);">
                        <div class="panel-heading panel-teal">
                            <h4 class="text-white text-center">Most Recent Member</h4>
                        </div>
                        <div class="panel-body pl-md pr-n">
                            <table class="table m-n">
                            <thead>
                                <tr>
                                    <th>Member Name</th>
                                    <th>Assign To</th>
                                    <th>Mobile</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if(count($recentmember)>0){ 
                                    for ($i=0; $i < count($recentmember); $i++) {
                                        $channellabel = ''; 
                                        $key = array_search($recentmember[$i]['channelid'], array_column($channeldata, 'id'));
                                        if(!empty($channeldata) && isset($channeldata[$key])){
                                            $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                                        }
                                        ?>
                                    <tr>
                                    
                                    <!-- '<a href="'.ADMIN_URL.'member/member-detail/'.$Member->id.'" title="'.ucwords($Member->name).'">'.ucwords($Member->name).' ('.$Member->membercode.')'.'</a>'; -->
                                        <td><?=$channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$recentmember[$i]['id'].'" title="'.ucwords($recentmember[$i]['name']).'">'.$recentmember[$i]['name'].' '.($recentmember[$i]['membercode']).'</a>'?></td>
                                        <td><?= empty($recentmember[$i]['name'])?'-':$recentmember[$i]['name']?></td>
                                        <td><?= empty($recentmember[$i]['mobile'])?'-':$recentmember[$i]['mobile']?></td>
                                        <td><?php if($recentmember[$i]['status']== 1){echo "Suspect";}elseif($recentmember[$i]['status'] == 2){echo "Dead Lead";}elseif($recentmember[$i]['status'] == 3){echo "Prospect";}elseif($recentmember[$i]['status'] == 4){echo "Archived";}elseif($recentmember[$i]['status'] == 5){echo "Closed";}else{echo "-";}?></td>
                                        <td><?=$this->general_model->displaydatetime($recentmember[$i]['createddate'])?></td>
                                    </tr>    
                                <? }}else{ ?>
                                <tr>
                                    <td colspan="3" class="text-center">No records found</td>
                                </tr>
                                <? } ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>    
                <?php } ?>
                
                <?php if(in_array("to-do-list",$this->viewData['submenuvisibility']['assignadditionalrights'])){?>
                    <div class="col-md-6 pr-n">
                        <div class="panel panel-info" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="" style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);">
                        <div class="panel-heading ">
                            <h4 class="text-white">To Do List
                            <span class="pull-right pt-n">
                            <button type="button" class="<?=addbtn_class; ?> pull-right " href="javascript:void(0)" onclick="setorder('<?=ADMIN_URL; ?>todo-list/updatepriority','#todo')" id="btntype" style="font-weight:600; background-color:black;" title="SET PRIORITY">Set Priority</button>
                            <button type="button" class="<?=addbtn_class;?> pull-right " title="ADD TO DO LIST" name="add" id="add" data-toggle="modal" data-target="#add_data_Modal" style="font-weight:600; background-color:black;"><?=addbtn_text?></button>
                            </span>
                            </h4>
                        </div>
                        <div class="panel-body pl-md pr-n">
                            <table class="table m-n" id="todo">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>List</th>
                                    <th>Assigned By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $count=1;
                                    foreach ($recenttodolist as $tdl) {                
                                        $btn_cls=$sts_val=$active1=$active2="";
                                        if ($tdl['status']==0) {
                                            $btn_cls="btn-info";
                                            $sts_val="Pending";
                                            $active1="active";
                                        } elseif ($tdl['status']==1) {
                                            $btn_cls="btn-success";
                                            $sts_val="Done";
                                            $active2="active";
                                        } 
                                        $status ='<div class="dropdown">
                                            <button class="btn btn-raised '.$btn_cls.' btn-sm dropdown-toggle" type="button" data-toggle="dropdown" id="liststatusdropdown'.$tdl['id'].'">'.$sts_val.'
                                            <span class="caret"></span></button>
                                            <ul class="dropdown-menu">
                                            <li><a href="javascript:void(0)" class="dropdown-item '.$active2.'" id="done_btn" onclick="changeliststatus('.(1).','.$tdl['id'].')">Done</a></li>
                                            <li><a href="javascript:void(0)" class="dropdown-item '.$active1.'" id="pending_btn" onclick="changeliststatus('.(0).','.$tdl['id'].')">Pending</a></li>
                                            </ul>
                                        </div>';
                                ?>
                                    <tr id="<?=$tdl['id'];?>">
                                        <td><?=$count++;?></td>
                                        <td><?=$tdl['list']?></td>
                                        <td><?=$tdl['assignby']?></td>
                                        <td><?=$status?></td>                
                                        <td><span style="display: none;"><?=$tdl['priority'];?></span>
                                        <a class="<?=edit_class;?> edit_data text-white" id="<?php echo $tdl['id']; ?>" title="<?=edit_title?>" style="margin-right: 3px;"><?=edit_text;?>               
                                        <a class="<?=delete_class?>" href="javascript:void(0)" title="Delete"
                                                                    onclick=deleterow(<?=$tdl['id']?>,"<?=ADMIN_URL?>todo-list/check-todo-list-use","todo-list","<?=ADMIN_URL?>todo-list/delete-mul-todo-list")><?=delete_text?></a>
                                        </td>                
                                    </tr>   
                                <?php }?>
                                
                            </tbody>
                        </table>
                        </div>
                    </div>
                    </div>
                <?php } ?>
            </div>
        </div>  
    </div> <!-- .container-fluid -->
    
</div> <!-- #page-content -->
<div id="add_data_Modal" class="modal fade">  
  <div class="modal-dialog">  
    <div class="modal-content">  
        <div class="modal-header"> 
          <h4 class="modal-title" style="float:left;">TO DO LIST</h4>  
          <button type="button" class="close" data-dismiss="modal">&times;</button> 
        </div>  
        <div class="modal-body pt-n">  
              <form method="post" id="insert_form"> 
                <div class="form-group" id="todolist_div">
                    <label class="control-label" for="todolist">Task List <span class="mandatoryfield">*</span></label>
                    <input id="todolist" class="form-control" type="text" name="todolist" data-url="<?php echo ADMIN_URL.'todo-list/gettodolist';?>" data-provide="todolist" placeholder="To Do List">                   
                </div> 
                <input type="submit" name="insert" id="insert" value="Add" class="btn btn-success btn-raised" />  
              </form>  
        </div>                 
    </div>  
  </div>  
</div> 
<div id="edit_data_Modal" class="modal fade">  
  <div class="modal-dialog">  
    <div class="modal-content">  
        <div class="modal-header"> 
          <h4 class="modal-title">UPDATE TO DO LIST</h4>  
          <button type="button" class="close" data-dismiss="modal">&times;</button> 
        </div>  
        <div class="modal-body pt-n">  
              <form method="post" id="update_form"> 
                <div class="form-group" id="todolist1_div">
                    <label class="control-label" for="todolist1">Task List <span class="mandatoryfield">*</span></label>
                    <input class="form-control" id="todolist1" type="text" name="todolist1" placeholder="To Do List">
                </div>                            
                <input type="hidden" name="tdlid" id="tdlid" />  
                <input type="submit" name="update" id="update" value="Edit" class="btn btn-success btn-raised" />  
              </form>  
        </div>                 
    </div>  
  </div>  
</div> 

<!-- <script>
        var data = '<?php echo ($saleschartdata); ?>';
        var JSONObject = JSON.parse(data);
        var saleschartdata = JSONObject['saleschartdata'];
        var saleschartdrilldata = JSONObject['saleschartdrilldata'];

        var data = '<?php echo ($orderchartdata); ?>';
        var JSONObject = JSON.parse(data);
        var orderchartdata = JSONObject['orderchartdata'];
        var orderchartdrilldata = JSONObject['orderchartdrilldata'];

       
        
</script> -->