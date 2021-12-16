<div class="page-content">
    <div class="page-heading">
        <?php $this->load->view(ADMINFOLDER . 'includes/menu_header'); ?>
    </div>

    <div class="container-fluid">

        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel mb-md" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
                        <div class="panel-heading filter-panel border-filter-heading" display-type="<?php if (isset($panelcollapsed) && $panelcollapsed == 1) { echo "0"; } else { echo "1"; } ?>">
                            <h2><?= APPLY_FILTER ?></h2>
                            <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                        </div>
                        <div class="panel-body panelcollapse pt-n" style="display: none;">
                            <form action="#" id="memberform" class="form-horizontal">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-md-12 pr-sm">
                                                <label for="sellerchannelid" class="control-label">Seller Channel</label>
                                                <select id="sellerchannelid" name="sellerchannelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                    <option value="">All Seller Channel</option>
                                                    <option value="0">Company</option>
                                                    <?php foreach ($channeldata as $cd) {
                                                        $selected = "";
                                                        if (!empty($this->session->userdata(base_url() . 'CHANNEL'))) {
                                                            $arrChannel = explode(",", $this->session->userdata(base_url() . 'CHANNEL'));
                                                            if (in_array($cd['id'], $arrChannel)) {
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label for="sellermemberid" class="control-label">Seller <?= Member_label ?></label>
                                                <select id="sellermemberid" name="sellermemberid[]" multiple data-actions-box="true" title="All Seller <?= Member_label ?>" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label for="buyerchannelid" class="control-label">Buyer Channel</label>
                                                <select id="buyerchannelid" name="buyerchannelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                    <option value="">All Buyer Channel</option>
                                                    <?php foreach ($channeldata as $cd) {
                                                        $selected = "";
                                                        if (!empty($this->session->userdata(base_url() . 'CHANNEL'))) {
                                                            $arrChannel = explode(",", $this->session->userdata(base_url() . 'CHANNEL'));
                                                            if (in_array($cd['id'], $arrChannel)) {
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label for="buyermemberid" class="control-label">Buyer <?= Member_label ?></label>
                                                <select id="buyermemberid" name="buyermemberid[]" multiple data-actions-box="true" title="All Buyer <?= Member_label ?>" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="col-sm-12 pr-sm">
                                                <label for="startdate" class="control-label">Credit Note Date</label>
                                                <div class="input-daterange input-group" id="datepicker-range">
                                                    <div class="input-group">    
                                                        <input type="text" class="input-small form-control" style="text-align: left;" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d", strtotime("-1 month"))); ?>" placeholder="Start Date" title="Start Date" readonly />
                                                        <span class="btn btn-default add-on datepicker_calendar_button"><i class="fa fa-calendar fa-lg"></i></span>
                                                    </div>
                                                    <span class="input-group-addon">to</span>
                                                    <div class="input-group">    
                                                        <input type="text" class="input-small form-control" style="text-align: left;" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly />
                                                        <span class="btn btn-default add-on datepicker_calendar_button"><i class="fa fa-calendar fa-lg"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-sm-12 pl-sm pr-sm">
                                                <label for="status" class="control-label">Select Status</label>
                                                <select id="status" name="status" class="selectpicker form-control" data-select-on-tab="true" data-size="5">
                                                    <option value="-1">All Status</option>
                                                    <option value="0">Pending</option>
                                                    <option value="1">Complete</option>
                                                    <option value="2">Cancel</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mt-xl">
                                            <div class="col-sm-12 pl-xs pr-sm">
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
                        <div class="panel-heading">
                            <div class="col-md-6">
                                <div class="panel-ctrls panel-tbl"></div>
                            </div>
                            <div class="col-md-6 form-group" style="text-align: right;">
                                <?php if (strpos(trim($submenuvisibility['submenuadd']), $this->session->userdata[base_url() . 'ADMINUSERTYPE']) !== false) { ?>
                                    <a class="<?= addbtn_class; ?>" href="<?= ADMIN_URL ?>credit-note/credit-note-add" title=<?= addbtn_title ?>><?= addbtn_text; ?></a>
                                <?php }
                                if (in_array("export-to-excel", $this->viewData['submenuvisibility']['assignadditionalrights'])) { ?>
                                    <a class="<?= exportbtn_class; ?>" href="javascript:void(0)" onclick="" title="<?= exportbtn_title ?>"><?= exportbtn_text; ?></a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="panel-body no-padding">
                            <table id="creditnotettable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th class="width8">Sr. No.</th>
                                        <th>Buyer Name</th>
                                        <th>Seller Name</th>
                                        <th>Invoice No.</th>
                                        <th>Credit Note Type</th>
                                        <th>Credit Note Number</th>
                                        <th>Credit Note Date</th>
                                        <th>Status</th>
                                        <th class="text-right">Amount (<?= CURRENCY_CODE ?>)</th>
                                        <th class="width15">Action</th>
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

        <!-- Modal -->
        <div class="modal fade" id="rejectcreditnoteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 1250000;">
            <div class="modal-dialog" role="document" style="width: 460px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="col-sm-9 p-n">Reason for Cancellation Credit Note</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="pagetitle"></h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" id="resonforrejectionform" name="resonforrejectionform">
                            <input type="hidden" id="rejectioncreditnoteid" name="rejectioncreditnoteid">
                            <input type="hidden" id="rejectionstatus" name="rejectionstatus">
                            <div id="row">
                                <div id="col-md-9">
                                    <div class="form-group" id="resonforrejection_div">
                                        <div class="col-sm-12">
                                            <label for="resonforrejection" class="control-label">Reason for Cancellation <span class="mandatoryfield">*</span></label>
                                            <textarea id="resonforrejection" name="resonforrejection" class="form-control"></textarea>
                                            <p style="color: red;" id="resonalert"></p>
                                        </div>
                                    </div>
                                </div>
                                <div id="col-md-12">
                                    <div class="form-group text-right">
                                        <div class="col-sm-12">
                                            <input type="button" id="submit" onclick="checkvalidationforrejectioncreditnote()" name="submit" value="SUBMIT" class="btn btn-primary btn-raised">

                                            <a class="<?= cancellink_class; ?>" href="<?= ADMIN_URL ?>credit-note" title=<?= cancellink_title ?>><?= cancellink_text ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->