<div class="panel panel-default offerparticipants">
    <div class="panel-heading">
        <div class="col-md-6">
            <div class="panel-ctrls panel-tbl"></div>
        </div>
        <div class="col-md-6" style="text-align: right;">
            <form action="#" id="offerparticipantsform" class="form-horizontal">
                <div class="row">
                    <div class="col-md-4">
                    </div>
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
                    <!-- <div class="col-md-4">
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
                    </div> -->
                </div> 
            </form>
        </div>
    </div>
    <div class="panel-body pt-n pb-n">
        <div class="table-responsive">
            <table id="offerparticipantstable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th class="width8">Sr. No.</th>
                    <th><?=Member_label?> Name</th>
                    <th>Contact Details</th>
                    <th>Admin Notes</th>
                    <th><?=Member_label?> Notes</th>
                    <th>Status</th>
                    <th>Participant Date</th>
                    <th class="width8">Action</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>
<div class="modal fade" id="notesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="pagetitle"></h4>
      </div>
      <div class="modal-body" style="max-width: 600px;max-height: 400px;overflow: auto;">
          <div id="description"></div>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>
<div class="modal fade" id="editnotesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="pagetitle">Edit Offer Notes</h4>
      </div>
      <div class="modal-body" style="max-width: 600px;max-height: 400px;overflow: auto;">
        <form action="#" id="editnotesform" class="form-horizontal">
            <input type="hidden" name="offerparticipantsid" id="offerparticipantsid">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group" id="adminnotes_div">
                        <label class="col-sm-3 control-label" for="adminnotes">Admin Notes</label>
                        <div class="col-md-8">
                            <textarea id="adminnotes" name="adminnotes" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group" id="membernotes_div">
                        <label class="col-sm-3 control-label" for="membernotes"><?=Member_label?> Notes</label>
                        <div class="col-md-8">
                            <textarea id="membernotes" name="membernotes" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 text-center">
                    <div class="form-group">
                        <input type="button" id="submit" onclick="updateoffernotes()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                        <a class="<?=cancellink_class;?>" href="javascript:voi(0)" title=<?=cancellink_title?>  data-dismiss="modal" aria-label="Close"><?=cancellink_text?></a>
                    </div>
                </div>
            </div>
            <div class="row">
                
            </div>
        </form>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>