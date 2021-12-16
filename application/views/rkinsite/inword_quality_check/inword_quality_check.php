<div class="page-content">
    <div class="page-heading">
        <?php $this->load->view(ADMINFOLDER . 'includes/menu_header'); ?>
    </div>

    <div class="container-fluid">

        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel mb-md" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
                        <div class="panel-heading filter-panel border-filter-heading">
                            <h2><?= APPLY_FILTER ?></h2>
                            <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                        </div>
                        <div class="panel-body panelcollapse pt-n" style="display: none;">
                            <form action="#" id="categoryform" class="form-horizontal">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="col-sm-12 pl-sm pr-sm">
                                                <label for="vendorid" class="control-label">Vendor</label>
                                                <select id="vendorid" name="vendorid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true">
                                                    <option value="0"> Select Vendor</option>
                                                    <?php foreach ($vendordata as $vd) { ?>
                                                        <option value="<?php echo $vd['id']; ?>"><?php echo $vd['name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="col-sm-12 pl-sm pr-sm">
                                                <label for="grnid" class="control-label">GRN. No.</label>
                                                <select id="grnid" name="grnid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true">
                                                    <option value="0">Select GRN. No.</option>
                                                    <?php foreach ($grndata as $gd) { ?>
                                                        <option value="<?php echo $gd['id']; ?>"><?php echo $gd['grnnumber']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label for="statusid" class="control-label">Status</label>
                                                <select id="statusid" name="statusid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                    <option value="-1">Select Status</option>
                                                    <option value="0">Pending</option>
                                                    <option value="1">Partially</option>
                                                    <option value="2">Complete</option>
                                                    <option value="3">Cancel</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="col-sm-12 pl-sm pr-sm">
                                                <label for="orderid" class="control-label">Order ID</label>
                                                <select id="orderid" name="orderid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true">
                                                    <option value="0">Select Order ID</option>
                                                    <?php foreach ($orderdata as $od) { ?>
                                                        <option value="<?php echo $od['id']; ?>"><?php echo $od['orderid']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="col-sm-12 pl-sm pr-sm">
                                                <label for="startdate" class="control-label">GR Date</label>
                                                <div class="input-daterange input-group" id="datepicker-range">
                                                    <div class="input-group">
                                                        <input type="text" style="text-align: left;" class="input-small form-control text-left" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d", strtotime("-1 month"))); ?>" placeholder="Start Date" title="Start Date" readonly />
                                                        <span class="btn btn-default datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                    </div>
                                                    <span class="input-group-addon">to</span>
                                                    <div class="input-group">
                                                        <input type="text" style="text-align: left;" class="input-small form-control text-left" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly />
                                                        <span class="btn btn-default datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 p-n">
                                        <div class="form-group" style="margin-top: 39px;">
                                            <div class="col-sm-12 ">
                                                <label class="control-label"></label>
                                                <a class="<?= applyfilterbtn_class; ?>" href="javascript:void(0)" onclick="applyFilter()" title=<?= applyfilterbtn_title ?>><?= applyfilterbtn_text; ?></a>
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
                        <div class="panel-heading pr-xs">
                            <div class="col-md-4 p-n">
                                <div class="panel-ctrls panel-tbl"></div>
                            </div>
                            <div class="col-md-8 p-n" style="text-align: right;">
                                <?php
                                if (strpos($submenuvisibility['submenuadd'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) { ?>
                                    <a class="<?= addbtn_class; ?>" href="<?= ADMIN_URL ?>inword-quality-check/add-inword-quality-check" title=<?= addbtn_title ?>><?= addbtn_text; ?></a>
                                <?php
                                } ?>
                                <?php
                                if (strpos($submenuvisibility['submenudelete'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) {
                                ?>
                                    <a class="<?= deletebtn_class; ?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>inword-quality-check/check-inword-qc-use','Inword&nbsp;Q.C.','<?php echo ADMIN_URL; ?>inword-quality-check/delete-mul-inword')" title=<?= deletebtn_title ?>><?= deletebtn_text; ?></a>
                                <?php } ?>
                                <!-- <a class="<?= qrcode_class ?>" href="<?= ADMIN_URL ?>product/qr-code" title="<?= qrcode_title ?>"><?= qrcode_text ?></a> -->
                                <?php if (in_array("import-to-excel", $this->viewData['submenuvisibility']['assignadditionalrights'])) { ?>
                                    <a class="<?= importbtn_class; ?>" href="javascript:void(0)" onclick="importproduct()" title="<?= importbtn_title ?>"><?= importbtn_text; ?></a>
                                    <a class="<?= assignproductbtn_class; ?>" href="javascript:void(0)" onclick="assignproduct()" title="<?= assignproductbtn_title ?>"><?= assignproductbtn_text; ?></a>
                                <?php }
                                if (in_array("upload-image", $this->viewData['submenuvisibility']['assignadditionalrights'])) { ?>
                                    <a class="<?= uploadproductimagebtn_class; ?>" href="javascript:void(0)" onclick="uploadproductfile()" title="<?= uploadproductimagebtn_title ?>"><?= uploadproductimagebtn_text; ?></a>
                                <?php }
                                if (in_array("export-to-excel", $this->viewData['submenuvisibility']['assignadditionalrights'])) { ?>
                                    <a class="<?= exportbtn_class; ?>" href="javascript:void(0)" onclick="exportadminproduct()" title="<?= exportbtn_title ?>"><?= exportbtn_text; ?></a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="panel-body no-padding">
                            <table id="inwordtable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th class="width8">Sr. No.</th>
                                        <th>Vendor Name</th>
                                        <th>Order ID</th>
                                        <th>GRN No.</th>
                                        <th>GRN Date</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <!-- <th class="width8 text-right">Discount (%)</th> -->
                                        <!-- <th class="width8 text-right">Priority</th> -->
                                        <th class="width15">Actions</th>
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