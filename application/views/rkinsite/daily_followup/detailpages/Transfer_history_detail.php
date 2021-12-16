<div class="col-md-12">
    <table class="table table-striped table-responsive-sm" width="100%">
        <tr>
            <th>Sr No.</th>
            <th>Date</th>
            <th>From</th>
            <th>To</th>
            <th>Reason</th>
        </tr>
        <?php foreach($followuptransferdata as $k=>$id){ ?>
        <tr>
            <td><?php echo $k+1; ?></td>
            <td><?php echo $this->general_model->displaydate($id['createddate']);?></td>
            <td><?php echo $id['transferfromemployee']; ?></td>
            <td><?php echo $id['transfertoemployee']; ?></td>
            <td><?php echo $id['reason']; ?></td>
        </tr>
        <?php } ?>
    </table>
</div>