<div class="panel panel-default offerparticipants">
    <div class="panel-heading">
        <div class="col-md-6">
            <div class="panel-ctrls panel-tbl"></div>
        </div>
        <div class="col-md-6" style="text-align: right;">
            <form action="#" id="offerparticipantsform" class="form-horizontal">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="input-daterange input-group" id="datepicker-range">
                                    <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); ?>" placeholder="Start Date" title="From Date" readonly/>
                                    <span class="input-group-addon">to</span>
                                    <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="To Date" readonly/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <select id="status" name="status" class="selectpicker form-control" data-select-on-tab="true" data-size="5">
                                    <option value="-1">All Status</option>
                                    <option value="0">Pending</option>
                                    <option value="1">Complete</option>
                                    <option value="2">Cancel</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div> 
            </form>
        </div>
    </div>
    <div class="panel-body pt-n pb-n">
        <div class="table-responsive">
            <table id="offerorderstable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th class="width8">Sr. No.</th>
                    <th>Buyer</th>
                    <th>Seller</th>
                    <th>OrderID</th>
                    <th>Order Date</th>
                    <th>Order Status</th>
                    <th>Approved Status</th>
                    <th>Total Amount (&#8377;)</th> 
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>