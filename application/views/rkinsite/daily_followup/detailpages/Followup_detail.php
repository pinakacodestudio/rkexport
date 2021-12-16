
<div class="col-md-12">
  <div id="followupdiv">
    <table class="table table-responsive-sm" width="100%">
      <tr>
        <th class="text-center">Company Name</th>
        <th class="text-center">Assign To</th>
        <th class="text-center"><?=Followup?> Type</th>
        <th class="text-center">Date</th>
        <th class="text-center">Status</th>
      </tr>
      <?php if(!is_null($followupviewdata)) { ?>
          <tr align="center">
            <td><?=$followupviewdata['companyname']?></td>
            <td><?=ucwords($followupviewdata['employeename'])?></td>
            <td><?=$followupviewdata['followuptypename']?></td>
            <td>
            <?php
              if($followupviewdata['time']!="00:00:00"){
                $time = date('h:i A', strtotime($followupviewdata['time']));
              }else{
                $time = $followupviewdata['time'];
              }
              if($followupviewdata['date']!="0000-00-00"){ echo $this->general_model->displaydate($followupviewdata['date'])." ".$time;  } ?></td>
            <td><?=$followupviewdata['followupstatus']?></td>
          </tr>
          <tr>
            <td colspan="3"><b>Notes : </b><?=$followupviewdata['notes']?></td>
            <td colspan="3"><b>Future Notes : </b><?=$followupviewdata['futurenotes']?></td>
          </tr>
        <?php } ?>
    </table>
  </div>
</div>   