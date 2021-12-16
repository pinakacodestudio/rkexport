<div class="col-md-12">
<?php if(isset($inquirydata) && $inquirydata) {
  $ij=1;
  foreach ($inquirydata as $id) {
    $amount = (($id['rate'] * $id['qty']) - $id['discount']);
    $taxamount = ($amount * $id['tax'] / 100);
    $netamount = $amount + $taxamount; 
    ?>
    <h4><?=Inquiry?> Product <?=$ij?></h4>
    <div class="row">
      <div class="col-md-6">
        <table class="table table-striped table-responsive-sm" width="100%">
          <tr>
              <th width="30%">Product Name</th>
              <td><?php echo $id['productname']; ?></td>
          </tr>
          <tr>
              <th>Product Category</th>
              <td><?php echo $id['pcname']; ?></td>
          </tr>
          <tr>
              <th>Quantity</th>
              <td><?php echo $id['qty']; ?></td>
          </tr>
          <tr>
              <th>Rate (<?=CURRENCY_CODE?>)</th>
              <td><?php echo numberFormat($id['rate'],2,','); ?></td>
          </tr>
          </table>
      </div>
      <div class="col-md-6">
          <table class="table table-striped table-responsive-sm" width="100%">
          <tr>
              <th width="30%">Discount (<?=CURRENCY_CODE?>)</th>
              <td><?php echo numberFormat($id['discount'],2,','); ?></td>
          </tr>
          <tr>
              <th>Amount (<?=CURRENCY_CODE?>)</th>
              <td><?php echo numberFormat($netamount,2,','); ?></td>
          </tr>
          <tr>
              <th>Tax (<?=CURRENCY_CODE?>)</th>
              <td><?php echo numberFormat($taxamount,2,','); ?></td>
          </tr>
          </table>
      </div>
    </div>
  <?php $ij++;
  }
} ?>
</div>