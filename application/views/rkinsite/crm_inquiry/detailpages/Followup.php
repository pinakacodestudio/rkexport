<div class="col-md-12 p-n">
  <div class="col-md-6">
    <table class="table table-responsive-sm" cellspacing="0" width="100%">
      <tbody>
        <tr>
          <th width="26%" style="border-top: unset;">Company Name</th>
          <th width="1%" style="border-top: unset;">:</th>
          <td style="border-top: unset;"><?php echo $memberdata['companyname'];?></td>
        </tr>
        <tr>
          <th width="26%" style="border-top: unset;">Mobile</th>
          <th width="1%" style="border-top: unset;">:</th>
          <td style="border-top: unset;"><?php echo $memberdata['countrycode'].$memberdata['mobileno'];?></td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="col-md-6">
    <table class="table table-responsive-sm" cellspacing="0" width="100%">
      <tbody>
        <tr>
          <th width="27%" style="border-top: unset;"><?=Member_label?> Name</th>
          <th width="1%" style="border-top: unset;">:</th>
          <td style="border-top: unset;"><?php echo $memberdata['name'];?></td>
        </tr>
        <tr>
          <th width="27%" style="border-top: unset;">Email</th>
          <th width="1%" style="border-top: unset;">:</th>
          <td style="border-top: unset;"><a href='mailto:<?php echo $memberdata['email'];?>' class='a-without-link'><?php echo $memberdata['email'];?></a></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<div class="col-md-12"><hr></div>
<input type="hidden" id="inquiryid" value="<?=(!empty($inquirydata[0]['ciid']))?$inquirydata[0]['ciid']:0?>">
<div class="col-md-12">
  <div class="table-responsive">
    <table id="followuptbl" class="table table-striped table-bordered table-responsive-sm" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th width="8%">Sr. No.</th>
          <th>Notes</th>
          <th>Future Notes</th>
          <th>Assign To</th>
          <th>Type</th>
          <th>Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      
      </tbody>
    </table>
  </div>
</div>