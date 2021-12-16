<div class="page-content">
    <div class="page-heading">     
        <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
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
                                    <div class="col-md-12">
                                        <div class="col-md-4 pr-sm">
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <label for="startdate" class="control-label">Date</label>
                                                    <div class="input-daterange input-group" id="datepicker-range">
                                                        <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-6 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                                        <span class="input-group-addon">to</span>
                                                        <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 pl-sm pr-sm">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="channelid" class="control-label">Receiver Channel</label>
                                                    <select id="channelid" name="channelid" class="selectpicker form-control" data-select-on-tab="true" data-size="6" data-live-search="true" >
                                                        <option value="0">All Channel</option>
                                                        <?php if(!empty($channeldata)){
                                                            foreach($channeldata as $channel){ ?>
                                                                <option value="<?=$channel['id']?>"><?=$channel['name']?></option>
                                                            <?php }
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 pl-sm pr-sm">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="memberid" class="control-label">Receiver <?=Member_label?></label>
                                                    <select id="memberid" name="memberid" class="selectpicker form-control" data-select-on-tab="true" data-size="6" data-live-search="true" >
                                                        <option value="0">All <?=Member_label?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 pl-sm pr-sm">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label for="offerid" class="control-label">Offer</label>
                                                    <select id="offerid" name="offerid" class="selectpicker form-control" data-select-on-tab="true" data-size="6" data-live-search="true" >
                                                        <option value="">All Offer</option>
                                                        <?php if(!empty($offerdata)){
                                                            foreach($offerdata as $offer){ ?>
                                                                <option value="<?=$offer['id']?>"><?=$offer['name']?></option>
                                                            <?php }
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 pl-sm pr-sm">
                                            <div class="form-group mt-xxl">
                                                <div class="col-md-12">
                                                    <label class="control-label"></label>
                                                    <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                                </div>
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
                            <table id="targetoffertable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th class="width8">Sr. No.</th>
                                        <th>Offer Name</th>
                                        <th>Receiver <?=Member_label?></th>
                                        <th>Provider <?=Member_label?></th>
                                        <th>Target Value</th>
                                        <th>Target Status</th>
                                        <th>Offer Date</th>
                                        <th>Offer Status</th>
                                        <th>Entry Date</th>
                                        <th>Reward</th> 
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


<div class="modal fade" id="GiftModal" tabindex="-1" role="dialog" aria-labelledby="MemberModalLabel">
    <div class="modal-dialog" role="document" style="width: 750px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
                <h4 class="modal-title" id="pagetitle">Offer Reward</h4>
            </div>
            <div class="modal-body" style="max-height: 400px;overflow: auto;">
                <form class="form-horizontal" id="assigngiftform">
                    <input type="hidden" name="giftofferid" id="giftofferid">
                    <input type="hidden" name="giftmemberid" id="giftmemberid">
                    <input type="hidden" name="redeempointsrate" id="redeempointsrate">
                    
                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                            <th>Product Name</th>
                            <th class="text-right" width="20%">Current Stock</th>
                            <th class="text-center width15">Assign Qty.</th>
                            <th class="width5"><div class="checkbox m-n">
                                    <input id="deletecheckall" onchange="allchecked()" type="checkbox" value="all">
                                    <label for="deletecheckall"></label>
                                </div>
                            </th>
                            </tr>
                        </thead>
                        <tbody id="giftproductdata">
                        </tbody>
                    </table>
                    <div class="row m-n">
                        <div class="form-group">
                            <label for="redeempoints" class="col-md-4 control-label" style="text-align: left;"><?=Member_label?> Redeem Points (<span id="memberredeempoint">0</span>)</label>
                            <div class="col-md-2 pl-n">
                                <input id="redeempoints" name="redeempoints" class="form-control text-right" onkeypress="return isNumber(event)" maxlength="4">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="text-align:center;">
                        <a class="<?=addbtn_class;?>" href="#" title="Assign Gift" style="align:center" onclick="assigngift()">Assign Gift</a>
                        <a class="btn btn-danger btn-raised" data-dismiss="modal" title="Cancel" >Cancel</a>
                    </div>
                </form>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>


<div class="modal fade" id="ViewGiftModal" tabindex="-1" role="dialog" aria-labelledby="MemberModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
                <h4 class="modal-title" id="pagetitle">Offer Reward</h4>
            </div>
            <div class="modal-body" style="max-width: 600px;max-height: 400px;overflow: auto;">
                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Assign Qty.</th>
                        </tr>
                    </thead>
                    <tbody id="assigngiftproductdata">
                    </tbody>
                </table> 
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>