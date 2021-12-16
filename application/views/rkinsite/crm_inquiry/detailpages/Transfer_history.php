<div class="col-md-12">
  <table id="transferhistorytbl" class="table table-striped table-bordered table-responsive-sm" cellspacing="0" width="100%">
    <thead>
      <tr>
        <th>Sr. No.</th>
        <th>Date</th>
        <th>From</th>
        <th>To</th>
        <th>Reason</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($inquirytransferdata as $k=>$id){ ?>
      <tr>
        <td><?php echo $k+1; ?></td>
        <td><?php echo $this->general_model->displaydate($id['createddate']);?></td>
        <td><?php echo ucwords($id['transferfromemployee']); ?></td>
        <td><?php echo ucwords($id['transfertoemployee']); ?></td>
        <td><?php echo ucfirst($id['reason']); ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>